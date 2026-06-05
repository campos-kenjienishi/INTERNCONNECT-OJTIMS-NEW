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
use App\Helpers\AuditLogger; 

class PassDocuController extends Controller
{
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
        $files = $professor ? FileCategory::where('professor_id', $professor->id)->get() : collect();

        return view('professor.fileCategory', compact('data', 'userName', 'files'));
    }


    public function fileCategory(Request $request){
        $files = new FileCategory();
        $files->fileName = $request->fileName;
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
                ['fileName' => $files->fileName, 'uploadedBy' => $files->uploadedBy, 'professor_id' => $files->professor_id]
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


    public function fileReq(Request $request)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        // Fetch only file categories from student's professor
        $student = Student::where('user_id', $user->id)->first();
        $professor = $student ? Professor::where('full_name', $student->adviser_name)->first() : null;
        $fileCategories = $professor
            ? FileCategory::where('professor_id', $professor->id)
                ->get()
                ->sortBy('fileName', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
            : collect();

        // Student's own uploaded files
        $data = FileRequirement::where('uploadedBy', '=', $user->full_name)->get();

        return view('students.fileReq', compact('user', 'fileCategories', 'data'));
    }

public function fileReqCreate(Request $request){
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
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
            ['fileName' => $fileup->fileName, 'file' => $fileup->file, 'uploadedBy' => $fileup->uploadedBy]
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
                $fileRequirement->status = 1;
                $fileRequirement->save();
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


            public function updateDeniedStatus(Request $request, $id)
            {
                $validated = $request->validate([
                    'reason' => 'required|string|max:1000',
                ]);
        
                // Find the file requirement by ID
                $fileRequirement = FileRequirement::findOrFail($id);
        
                // Update the status based on the request data
                $fileRequirement->status = 2;
                if (Schema::hasColumn('file_requirements', 'denial_reason')) {
                    $fileRequirement->denial_reason = $validated['reason'];
                }
                $fileRequirement->save();

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
