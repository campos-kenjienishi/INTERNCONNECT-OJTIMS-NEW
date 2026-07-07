<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
Use App\Mail\TemporaryPasswordNotification;
use App\Mail\RequirementDenied;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use Illuminate\Support\Str;
use App\Models\FileCategory;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\Announcements;
use App\Models\OJTInformation;
use App\Models\FileRequirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLogger; 

class PassDocuController extends Controller
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

    private function normalizeRequirementPhase(?string $phase): string
    {
        return $phase === 'basic' ? 'basic' : 'other';
    }

    private function sortCategoriesByPhaseAndName($categories)
    {
        return $categories
            ->sortBy(function ($category) {
                $phaseOrder = $this->normalizeRequirementPhase($category->phase ?? null) === 'basic' ? 0 : 1;
                return sprintf('%d-%s', $phaseOrder, mb_strtolower(trim((string) $category->fileName)));
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    private function buildRequirementPhaseState(User $user, ?Student $student, ?Professor $professor): array
    {
        $fileCategories = $professor
            ? $this->sortCategoriesByPhaseAndName(
                FileCategory::where('professor_id', $professor->id)->get()
            )
            : collect();

        $basicCategories = $fileCategories
            ->filter(fn ($category) => $this->normalizeRequirementPhase($category->phase ?? null) === 'basic')
            ->values();
        $otherCategories = $fileCategories
            ->filter(fn ($category) => $this->normalizeRequirementPhase($category->phase ?? null) !== 'basic')
            ->values();

        $submittedRequirements = FileRequirement::where('uploadedBy', $user->full_name)->get();
        $submittedRequirementNames = $submittedRequirements
            ->pluck('fileName')
            ->filter()
            ->map(fn ($name) => mb_strtolower(trim((string) $name)))
            ->unique()
            ->values();
        $validSubmittedNames = $submittedRequirements
            ->filter(fn ($requirement) => (int) ($requirement->status ?? 0) !== 2)
            ->pluck('fileName')
            ->filter()
            ->map(fn ($name) => mb_strtolower(trim((string) $name)))
            ->unique()
            ->values();

        $submittedBasicNames = $basicCategories
            ->filter(fn ($category) => $validSubmittedNames->contains(mb_strtolower(trim((string) $category->fileName))))
            ->pluck('fileName')
            ->values();
        $missingBasicCategories = $basicCategories
            ->reject(fn ($category) => $validSubmittedNames->contains(mb_strtolower(trim((string) $category->fileName))))
            ->values();

        $hasSubmittedNotarizedMoa = $validSubmittedNames->contains(mb_strtolower('Notarized MOA'));
        $otherRequirementsUnlocked = $missingBasicCategories->isEmpty() && $hasSubmittedNotarizedMoa;

        return [
            'fileCategories' => $fileCategories,
            'basicCategories' => $basicCategories,
            'otherCategories' => $otherCategories,
            'submittedRequirements' => $submittedRequirements,
            'submittedRequirementNames' => $submittedRequirementNames,
            'submittedBasicNames' => $submittedBasicNames,
            'missingBasicCategories' => $missingBasicCategories,
            'hasSubmittedNotarizedMoa' => $hasSubmittedNotarizedMoa,
            'otherRequirementsUnlocked' => $otherRequirementsUnlocked,
        ];
    }

    private function updateCompanyStudentDisplay(Company $company, ?string $removedName = null): void
    {
        if (!Schema::hasColumn('companies', 'student_names_display')) {
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

        if (Schema::hasColumn('file_requirements', 'denial_reason')) {
            $requirement->denial_reason = $sourceRequirement->denial_reason ?? null;
        }

        if (Schema::hasColumn('file_requirements', 'professor_id') && isset($sourceRequirement->professor_id)) {
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

    private function requireStudentSession()
    {
        if (!Session::has('loginId')) {
            return redirect('/login');
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || (string) $user->role !== '0') {
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect('/login');
        }

        return $user;
    }

    private function propagateSharedNotarizedMoaStatus(FileRequirement $fileRequirement, int $status, ?string $reason = null): void
    {
        if ($fileRequirement->fileName !== 'Notarized MOA' || empty($fileRequirement->file)) {
            $fileRequirement->status = $status;
            if (Schema::hasColumn('file_requirements', 'denial_reason')) {
                $fileRequirement->denial_reason = $status === 2 ? $reason : null;
            }
            $fileRequirement->save();
            return;
        }

        $company = Company::with('students')
            ->where('file', $fileRequirement->file)
            ->first();

        $uploadedByNames = collect([$fileRequirement->uploadedBy]);

        if ($company) {
            $uploadedByNames = $company->students->pluck('full_name')
                ->filter()
                ->push($company->uploader_name)
                ->unique()
                ->values();
        }

        $query = FileRequirement::where('fileName', 'Notarized MOA')
            ->where('file', $fileRequirement->file)
            ->whereIn('uploadedBy', $uploadedByNames);

        $updates = ['status' => $status];

        if (Schema::hasColumn('file_requirements', 'denial_reason')) {
            $updates['denial_reason'] = $status === 2 ? $reason : null;
        }

        $query->update($updates);
    }

    public function maintainFileCategory() {
        $data = [];
        $userName = '';
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name;
        }

        // Fetch only file categories created by this professor
        $professor = Professor::where('user_id', $data->id)->first();
        $files = $professor
            ? $this->sortCategoriesByPhaseAndName(
                FileCategory::where('professor_id', $professor->id)->get()
            )
            : collect();

        return view('professor.fileCategory', compact('data', 'userName', 'files'));
    }


    public function fileCategory(Request $request){
        $request->validate([
            'fileName' => 'required|string|max:255',
            'phase' => 'required|in:basic,other',
        ]);

        $files = new FileCategory();
        $files->fileName = $request->fileName;
        $files->phase = $this->normalizeRequirementPhase($request->phase);
        $files->uploadedBy = $request->uploadedBy;
        // Attach professor_id
        $user = User::where('id', Session::get('loginId'))->first();
        $professor = Professor::where('user_id', $user->id)->first();
        $files->professor_id = $professor ? $professor->id : null;
        $res = $files->save();

        if($res){
            AuditLogger::log(
                'FileCategory',
                'Create',
                'Added new file category: ' . $files->fileName,
                Session::get('loginId') ?? null,
                null,
                ['fileName' => $files->fileName, 'phase' => $files->phase, 'uploadedBy' => $files->uploadedBy, 'professor_id' => $files->professor_id]
            );
            return back()->with('success','You have added the course successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }

    public function removeCategory($id)
    {

        $data = FileCategory::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if (!empty($data->file)) {
            $filePath = public_path('assets/' . $data->file);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    
        $data->delete();
        AuditLogger::log(
            'FileCategory',
            'Delete',
            'Deleted file category: ' . $data->fileName,
            Session::get('loginId') ?? null,
            ['id' => $data->id, 'fileName' => $data->fileName, 'uploadedBy' => $data->uploadedBy],
            null
        );
        return redirect()->back();
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'fileName' => 'required|string|max:255',
            'phase' => 'required|in:basic,other',
        ]);

        $category = FileCategory::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'File category not found.');
        }

        $category->fileName = $request->fileName;
        $category->phase = $this->normalizeRequirementPhase($request->phase);
        $category->save();

        AuditLogger::log(
            'FileCategory',
            'Update',
            'Updated file category: ' . $category->fileName,
            Session::get('loginId') ?? null
        );

        return redirect()->back()->with('success', 'File category updated successfully.');
    }


    public function fileReq(Request $request)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        $student = Student::where('user_id', $user->id)->first();
        $professor = $student ? Professor::where('full_name', $student->adviser_name)->first() : null;
        $phaseState = $this->buildRequirementPhaseState($user, $student, $professor);
        $data = $phaseState['submittedRequirements'];

        return view('students.fileReq', array_merge($phaseState, [
            'user' => $user,
            'data' => $data,
        ]));
    }

public function fileReqCreate(Request $request){
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $request->validate([
        'fileName' => 'required|string',
        'phase' => 'required|in:basic,other',
        'file' => 'required|file|mimes:pdf|max:2048',
        'uploadedBy' => 'required|string',
        'adviser' => 'required|string',
    ], [
        'file.mimes' => 'Only PDF files are accepted for requirement uploads.',
    ]);

    $user = $sessionCheck;
    $student = Student::where('user_id', $user->id)->first();
    $professor = $student ? Professor::where('full_name', $student->adviser_name)->first() : null;
    $phaseState = $this->buildRequirementPhaseState($user, $student, $professor);

    $category = $phaseState['fileCategories']->first(function ($item) use ($request) {
        return trim((string) $item->fileName) === trim((string) $request->fileName);
    });

    if (!$category) {
        return back()->with('fail', 'Selected requirement category is not assigned to your professor.');
    }

    $categoryPhase = $this->normalizeRequirementPhase($category->phase ?? null);

    if ($request->phase !== $categoryPhase) {
        return back()->with('fail', 'Selected requirement category does not belong to the chosen phase.');
    }

    if ($categoryPhase === 'other' && !$phaseState['otherRequirementsUnlocked']) {
        return back()->with('fail', 'Submit all basic requirements and your Notarized MOA first before uploading other requirements.');
    }

    $normalizedCategoryName = mb_strtolower(trim((string) $request->fileName));
    $alreadySubmitted = $phaseState['submittedRequirementNames']
        ->contains($normalizedCategoryName);

    if ($alreadySubmitted) {
        return back()->withInput()->with('fail', 'This requirement is already submitted. Remove the existing submission first before uploading another file for it.');
    }
    
    // Create a new instance of FileRequirement model
    $fileup = new FileRequirement();
    $fileup->fileName = $request->fileName; 
    $file=$request->file;
    $filename=time().'.'.$file->getClientOriginalExtension();
    $request->file->move('assets',$filename);
    $fileup->file=$filename;
    $fileup->status = 0;
    $fileup->adviser = $request->adviser;
    $fileup->uploadedBy = $request->uploadedBy;
    
    // Save the model instance
    $res = $fileup->save();

    if($res){
        AuditLogger::log(
            'PassDocu',
            'Upload',
            'Uploaded file: ' . $fileup->fileName,
            Session::get('loginId') ?? null,
            null,
            ['fileName' => $fileup->fileName, 'phase' => $categoryPhase, 'file' => $fileup->file, 'uploadedBy' => $fileup->uploadedBy]
        );
        return back()->with('success', 'File uploaded successfully!');
    } else {
        // If saving fails, delete the uploaded file
        Storage::delete('assets/' . $filename);
        return back()->with('fail', 'Failed to upload file.');
    }
}

public function removeFile($id)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $data = FileRequirement::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if ($data->uploadedBy !== $sessionCheck->full_name) {
            return redirect()->back()->with('error', 'You do not have permission to remove this file.');
        }

        if ($data->fileName === 'Notarized MOA' && !empty($data->file)) {
            $student = Student::where('user_id', $sessionCheck->id)->first();
            $company = Company::with('students')
                ->where('file', $data->file)
                ->where(function ($query) use ($data, $student) {
                    $query->where('uploader_name', $data->uploadedBy);

                    if ($student) {
                        $query->orWhereHas('students', function ($studentQuery) use ($student) {
                            $studentQuery->where('students.id', $student->id);
                        });
                    }
                })
                ->first();

            if ($company) {
                $isOwner = $company->uploader_name === $sessionCheck->full_name;
                $ownerRequirement = $isOwner ? clone $data : null;

                if ($student && $company->students->contains('id', $student->id)) {
                    $company->students()->detach($student->id);
                    $company = $company->fresh('students');
                    $this->updateCompanyStudentDisplay($company, $sessionCheck->full_name);
                }

                $this->reconcileStudentNotarizedRequirements($sessionCheck);

                if ($company->students->isEmpty()) {
                    $ownerRequirement = FileRequirement::where('uploadedBy', $company->uploader_name)
                        ->where('fileName', 'Notarized MOA')
                        ->where('file', $company->file);

                    if ($data->uploadedBy !== $company->uploader_name) {
                        $ownerRequirement->delete();
                    }

                    $filePath = public_path('assets/' . $company->file);
                    if (!empty($company->file) && file_exists($filePath)) {
                        @unlink($filePath);
                    }

                    $company->delete();
                } elseif ($isOwner) {
                    $newOwnerName = $this->transferCompanyOwnership($company, $ownerRequirement);
                    if ($newOwnerName) {
                        AuditLogger::log(
                            'PassDocu',
                            'Transfer Ownership',
                            'Transferred MOA ownership for ' . $company->company_name . ' to ' . $newOwnerName,
                            Session::get('loginId') ?? null,
                            ['previous_owner' => $sessionCheck->full_name],
                            ['new_owner' => $newOwnerName]
                        );
                    }
                }
            }
        }
    
        $data->delete();
        AuditLogger::log(
            'PassDocu',
            'Delete',
            'Deleted file: ' . $data->fileName,
            Session::get('loginId') ?? null,
            ['id' => $data->id, 'fileName' => $data->fileName, 'file' => $data->file],
            null
        );
        return redirect()->back();
    }

    public function viewFile($id)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        $fileRequirement = FileRequirement::where('id', $id)
            ->where('uploadedBy', $user->full_name)
            ->firstOrFail();

        $filePath = public_path('assets/' . $fileRequirement->file);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->file($filePath);
    }

    public function downloadStudent($id)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        $fileRequirement = FileRequirement::where('id', $id)
            ->where('uploadedBy', $user->full_name)
            ->firstOrFail();

        $filePath = public_path('assets/' . $fileRequirement->file);

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileRequirement->file);
        }

        return back()->with(['error' => 'File not found.'], 404);
    }

    public function studentRequirements(Request $request){
        // Retrieve the value from the query parameter
        $value = $request->input('value');
        $data = [];

        $student = User::where('full_name', '=', $value)->first();
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $course = $student->course;
        $roomId = $request->input('roomId');
        if (empty($roomId) && isset($student->class_id)) {
            $roomId = $student->class_id;
        }
       
        
        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name;
        }
        $files=FileRequirement::where('adviser', '=',$data->full_name)
                                ->where('uploadedBy', '=', $value)
                                ->get();
        
        return view('professor.studentRequire', compact('data','files', 'value','course', 'roomId'));

            
            }


            public function updateApproveStatus(Request $request, $id)
            {
                // Validate the request data if needed
        
                // Find the file requirement by ID
                $fileRequirement = FileRequirement::findOrFail($id);
        
                // Update the status based on the request data
                $this->propagateSharedNotarizedMoaStatus($fileRequirement, 1);
                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Approved file: ' . $fileRequirement->fileName,
                    Session::get('loginId') ?? null,
                    ['status' => 0],
                    ['status' => 1]
                );
                return back()->with('success', 'You have updated the information successfully!');
            }

            public function updateApproveStatusBulk(Request $request)
            {
                $request->validate([
                    'student_name' => 'required|string',
                    'roomId' => 'nullable',
                ]);

                $data = null;

                if (Session::has('loginId')) {
                    $data = User::where('id', Session::get('loginId'))->first();
                }

                if (!$data) {
                    return back()->with('error', 'Professor account not found.');
                }

                $files = FileRequirement::where('adviser', $data->full_name)
                    ->where('uploadedBy', $request->student_name)
                    ->whereNotIn('status', [1, 2])
                    ->get();

                if ($files->isEmpty()) {
                    return back()->with('info', 'There are no pending files to approve.');
                }

                foreach ($files as $fileRequirement) {
                    $this->propagateSharedNotarizedMoaStatus($fileRequirement, 1);
                }

                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Approved all pending files for student: ' . $request->student_name . ' (' . $files->count() . ' files)',
                    Session::get('loginId') ?? null
                );

                return back()->with('success', $files->count() . ' file(s) approved successfully.');
            }


            public function updateDeniedStatus(Request $request, $id)
            {
                $validated = $request->validate([
                    'reason' => 'required|string|max:1000',
                ]);
        
                // Find the file requirement by ID
                $fileRequirement = FileRequirement::findOrFail($id);
        
                // Update the status based on the request data
                $this->propagateSharedNotarizedMoaStatus($fileRequirement, 2, $validated['reason']);

                $student = User::where('role', 0)
                    ->where('full_name', $fileRequirement->uploadedBy)
                    ->first();

                if ($student && !empty($student->email)) {
                    Mail::to($student->email)->send(new RequirementDenied($fileRequirement, $validated['reason']));
                }

                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Denied file: ' . $fileRequirement->fileName . '. Reason: ' . $validated['reason'],
                    Session::get('loginId') ?? null,
                    ['status' => 0],
                    ['status' => 2, 'denial_reason' => $validated['reason']]
                );
                return back()->with('success', 'You have updated the information successfully!');
            }

            public function requirementsView(Request $request){

                // Retrieve the value from the query parameter
                $value = $request->input('value');
                $file = $request->input('file');
                $roomId = $request->input('roomId');
                $data = [];
               
                
                if (Session::has('loginId')) {
                    $data = User::where('id', '=', Session::get('loginId'))->first();
                    $userName = $data->full_name;
                }
                $files=FileRequirement::where('adviser', '=',$data->full_name)
                                        ->where('uploadedBy', '=', $value)
                                        ->where('fileName', '=', $file)
                                        ->get();
                
                return view('professor.requireView', compact('data','files','value','file','roomId'));
        
                    
                    }

     public function download($id)
    {
        // Find the FileRequirement by ID
        $fileRequirement = FileRequirement::findOrFail($id);

        // Get the file path
        $filePath = public_path('assets/' . $fileRequirement->file);

        // Check if the file exists
        if (file_exists($filePath)) {
            // Return the file as a download response
            return response()->download($filePath, $fileRequirement->file);
        } else {
            // File not found
            return back()->with(['error' => 'File not found.'], 404);
        }
    }

}
