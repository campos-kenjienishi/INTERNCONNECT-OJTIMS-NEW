<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Classes;
use App\Mail\SendFileNotif;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Company;
use App\Models\Courses;
use App\Models\Student;
use App\Models\Professor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\FileRequirement;
use App\Helpers\AuditLogger;

class MOAUploadController extends Controller
{
    private function resolveUserFromStudent(Student $student): ?User
    {
        $student->loadMissing('user');

        if ($student->user) {
            return $student->user;
        }

        $fullName = trim((string) ($student->full_name ?? ''));
        if ($fullName === '') {
            return null;
        }

        return User::where('full_name', $fullName)->first();
    }

    private function updateCompanyStudentDisplay(Company $company, ?string $removedName = null): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('companies', 'student_names_display')) {
            return;
        }

        $existingNames = collect(explode(',', (string) ($company->student_names_display ?? '')))
            ->map(fn ($name) => trim((string) $name))
            ->filter();

        $linkedNames = $company->students()->with('user')->get()
            ->pluck('full_name')
            ->map(fn ($name) => trim((string) $name))
            ->filter();

        $manualNames = $existingNames->reject(function ($name) use ($linkedNames, $removedName) {
            return $linkedNames->contains($name) || (!empty($removedName) && $name === trim($removedName));
        });

        $company->student_names_display = $manualNames
            ->merge($linkedNames)
            ->filter()
            ->unique()
            ->implode(', ');

        $company->save();
    }

    private function deleteStudentNotarizedRequirement(User $user, Company $company): void
    {
        FileRequirement::where('uploadedBy', $user->full_name)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->delete();
    }

    private function syncStudentNotarizedRequirement(Company $company, Student $student, ?FileRequirement $sourceRequirement = null): void
    {
        $user = $this->resolveUserFromStudent($student);

        if (!$user) {
            return;
        }

        $sourceRequirement = $sourceRequirement ?: FileRequirement::where('uploadedBy', $company->uploader_name)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->latest('id')
            ->first();

        $requirement = FileRequirement::where('uploadedBy', $user->full_name)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->first();

        $requirement = $requirement ?: new FileRequirement();
        $requirement->fileName = 'Notarized MOA';
        $requirement->file = $company->file;
        $requirement->status = $sourceRequirement->status ?? 0;
        $requirement->adviser = $user->adviser_name;
        $requirement->uploadedBy = $user->full_name;

        if (\Illuminate\Support\Facades\Schema::hasColumn('file_requirements', 'denial_reason')) {
            $requirement->denial_reason = $sourceRequirement->denial_reason ?? null;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('file_requirements', 'professor_id') && isset($sourceRequirement->professor_id)) {
            $requirement->professor_id = $sourceRequirement->professor_id;
        }

        $requirement->save();
    }

    private function reconcileStudentNotarizedRequirements(User $user): void
    {
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            FileRequirement::where('uploadedBy', $user->full_name)
                ->where('fileName', 'Notarized MOA')
                ->delete();
            return;
        }

        $linkedCompanies = $student->companies()->get()->filter(fn ($company) => !empty($company->file))->values();
        $validFiles = $linkedCompanies->pluck('file')->filter()->unique()->values();

        $query = FileRequirement::where('uploadedBy', $user->full_name)
            ->where('fileName', 'Notarized MOA');

        if ($validFiles->isEmpty()) {
            $query->delete();
            return;
        }

        $query->whereNotIn('file', $validFiles)->delete();

        foreach ($linkedCompanies as $linkedCompany) {
            $this->syncStudentNotarizedRequirement($linkedCompany, $student);
        }
    }

    private function transferCompanyOwnership(Company $company, ?FileRequirement $sourceRequirement = null): ?string
    {
        $company->loadMissing('students.user');

        $newOwner = $company->students->first(function ($student) {
            return !empty(trim((string) ($student->full_name ?? ''))) || $this->resolveUserFromStudent($student);
        });

        if (!$newOwner) {
            return null;
        }

        $newOwnerUser = $this->resolveUserFromStudent($newOwner);
        $newOwnerName = trim((string) ($newOwnerUser->full_name ?? $newOwner->full_name ?? ''));

        if ($newOwnerName === '') {
            return null;
        }

        $company->uploader_name = $newOwnerName;
        $company->save();

        if (class_exists(\App\Models\Voucher::class)) {
            \App\Models\Voucher::where('company_id', $company->id)
                ->update(['uploader_name' => $newOwnerName]);
        }

        $this->syncStudentNotarizedRequirement($company, $newOwner, $sourceRequirement);

        if ($newOwnerUser) {
            $this->reconcileStudentNotarizedRequirements($newOwnerUser);
        }

        return $newOwnerName;
    }

    private function deleteCompanyAssets(Company $company): void
    {
        $this->deleteLinkedNotarizedRequirement($company);

        if (!empty($company->file)) {
            $filePath = public_path('assets/' . $company->file);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        DB::table('company_student')
            ->where('company_id', $company->id)
            ->delete();

        $company->delete();
    }

    private function deleteLinkedNotarizedRequirement(?Company $company): void
    {
        if (!$company || empty($company->file) || empty($company->uploader_name)) {
            return;
        }

        FileRequirement::where('uploadedBy', $company->uploader_name)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->delete();
    }

    private function deleteAllLinkedNotarizedRequirements(?Company $company): void
    {
        if (!$company || empty($company->file)) {
            return;
        }

        $company->loadMissing('students.user');

        $linkedNames = $company->students
            ->map(function ($student) {
                $user = $this->resolveUserFromStudent($student);

                return trim((string) ($user->full_name ?? $student->full_name ?? ''));
            })
            ->push(trim((string) $company->uploader_name))
            ->filter()
            ->unique()
            ->values();

        if ($linkedNames->isEmpty()) {
            return;
        }

        FileRequirement::whereIn('uploadedBy', $linkedNames->all())
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->delete();
    }

    private function isReadableMoaFile(?Company $company): bool
    {
        if (!$company || empty($company->file)) {
            return false;
        }

        $filePath = public_path('assets/' . $company->file);

        return file_exists($filePath) && filesize($filePath) > 0;
    }

    public function download(Request $request, $file)
    {   
	    $fileRecord = Company::where('file', $file)->first();

    if ($fileRecord) {
        // Check if the file is still valid
        if ($fileRecord->valid_until && now()->gt($fileRecord->valid_until)) {
            // File has expired, return a response indicating that
            return response()->json(['message' => 'File has expired'], 403);
        }

        if (!$this->isReadableMoaFile($fileRecord)) {
            return response()->json(['message' => 'The uploaded MOA file is empty or unavailable.'], 422);
        }

        // File is valid, allow download
        return response()->download(public_path('assets/' . $file));
    }

    // File not found, return a response indicating that
    return response()->json(['message' => 'File not found'], 404);

    }

    public function remove($id)
{
    $company = Company::find($id);

    if ($company) {
        $this->deleteAllLinkedNotarizedRequirements($company);

        if (!empty($company->file)) {
            $filePath = public_path('assets/' . $company->file);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Get the associated students
        $students = $company->students;

        // Detach the students from the company in the pivot table (companystudent)
        DB::table('company_student')
            ->where('company_id', $company->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->delete();

       

        // Delete the company
        $company->delete();
        AuditLogger::log(
            'MOA Upload',
            'Delete',
            'Deleted company and associated students: ' . $company->company_name,
            Session::get('loginId') ?? null,
            ['company_id' => $company->id, 'students' => $students->pluck('id')->toArray()],
            null
        );
        return redirect()->back()->with('success', 'Company and associated students deleted successfully.');
    }

    return redirect()->back()->with('error', 'Company not found.');
}


public function view($id)
{
    $user = [];

    if (Session::has('loginId')) {
        $user = User::where('id', '=', Session::get('loginId'))->first();
    }

    // Find the company by its ID along with its associated students
    $company = Company::with('students')->find($id);

    if (!$company) {
        abort(404); // Handle the case where the company is not found
    }


    return view('ojtCoordinator.MOAview', compact('company', 'user'));
}


    public function sendFiles(Request $request){
       
        $request->validate([
            'email' => 'required|email',
            'file_id', // Make sure the file exists in the 'moa_uploads' table
        ]);
    
        $fileId = $request->input('file_id');
        $file = Company::find($fileId);
    
        if (!$file) {
            return back()->with('error', 'File not found.');
        }
        
        $validUntilFromDatabase=$file->valid_until;
        $createdAt = Carbon::parse($validUntilFromDatabase);
        $attachmentPath = $createdAt->format('F j, Y \a\t g:i A'); 
    
        try {
            Mail::to($request->email)->send(new SendFileNotif($attachmentPath, $file->file));

            AuditLogger::log(
                'MOA Upload',
                'Send',
                'Sent MOA file for company: ' . $file->company_name . ' to ' . $request->email,
                Session::get('loginId') ?? null
            );
    
            return back()->with('success', 'Email sent with file attachment.');
        } catch (\Exception $e) {
            \Log::error('Email sending error: ' . $e->getMessage());
            return back()->with('error', 'Email sending failed.');
        }
    
    }

    public function downloadFile($file)
{
    $fileRecord = Company::where('file', $file)->first();

    if ($fileRecord) {
        // Check if the file is still valid
        if ($fileRecord->valid_until && now()->gt($fileRecord->valid_until)) {
            // File has expired, return a response indicating that
            return response()->json(['message' => 'File has expired'], 403);
        }

        if (!$this->isReadableMoaFile($fileRecord)) {
            return response()->json(['message' => 'The uploaded MOA file is empty or unavailable.'], 422);
        }

        // File is valid, allow download
        $filePath = public_path('assets/' . $file);
        $headers = [
            'Content-Type' => 'application/pdf', // Adjust the content type as needed
        ];

        return response()->download(public_path('assets/' . $file));
    }

    // File not found, return a response indicating that
    return response()->json(['message' => 'File not found'], 404);
}

public function printData(Company $company)
{
    // Load the company's data along with its students
    $company->load('students');
    

    // Return the print preview view with the data
    return view('ojtCoordinator.print-data', compact('company'));
}

public function studentRemove($id)
{
    $user = User::where('id', Session::get('loginId'))->first();

    if (!$user) {
        return redirect()->back()->with('error', 'Unauthorized.');
    }

    $student = Student::where('user_id', $user->id)->first();
    $company = Company::with('students')->find($id);

    if (!$company || !$student || !$company->students->contains('id', $student->id)) {
        return redirect()->back()->with('error', 'MOA not found or you do not have permission to remove it.');
    }

    $isOwner = $company->uploader_name === $user->full_name;
    $ownerRequirement = $isOwner
        ? FileRequirement::where('uploadedBy', $user->full_name)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->latest('id')
            ->first()
        : null;

    $this->deleteStudentNotarizedRequirement($user, $company);
    $company->students()->detach($student->id);
    $company = $company->fresh('students');
    $this->updateCompanyStudentDisplay($company, $user->full_name);
    $this->reconcileStudentNotarizedRequirements($user);

    if ($company->students->isEmpty()) {
        $this->deleteCompanyAssets($company);
    } elseif ($isOwner) {
        $newOwnerName = $this->transferCompanyOwnership($company, $ownerRequirement);
        if ($newOwnerName) {
            AuditLogger::log(
                'MOA Upload',
                'Transfer Ownership',
                'Transferred MOA ownership for ' . $company->company_name . ' to ' . $newOwnerName,
                Session::get('loginId') ?? null,
                ['previous_owner' => $user->full_name],
                ['new_owner' => $newOwnerName]
            );
        }
    }

    AuditLogger::log(
        'MOA Upload',
        'Delete',
        ($isOwner ? 'Student removed own MOA: ' : 'Student unlinked shared MOA: ') . $company->company_name,
        Session::get('loginId') ?? null,
        ['company_id' => $company->id],
        null
    );

    return redirect()->back()->with('success', $isOwner ? 'MOA removed successfully.' : 'MOA unlinked successfully.');
}




}
