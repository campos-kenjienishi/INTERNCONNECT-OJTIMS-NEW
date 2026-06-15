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
        $this->deleteLinkedNotarizedRequirement($company);

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

    $company = Company::where('id', $id)
        ->where('uploader_name', $user->full_name)
        ->first();

    if (!$company) {
        return redirect()->back()->with('error', 'MOA not found or you do not have permission to remove it.');
    }

    $this->deleteLinkedNotarizedRequirement($company);

    DB::table('company_student')
        ->where('company_id', $company->id)
        ->delete();

    if (!empty($company->file)) {
        $filePath = public_path('assets/' . $company->file);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    $company->delete();

    AuditLogger::log(
        'MOA Upload',
        'Delete',
        'Student deleted own MOA: ' . $company->company_name,
        Session::get('loginId') ?? null,
        ['company_id' => $company->id],
        null
    );

    return redirect()->back()->with('success', 'MOA removed successfully.');
}




}
