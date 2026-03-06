<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
Use App\Mail\TemporaryPasswordNotification;
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
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuditLogger; 

class PassDocuController extends Controller
{
    public function maintainFileCategory() {
        $data = [];
        $userName = '';
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name;
        }

        // Fetch all file categories, no filter
        $files = FileCategory::all();

        return view('professor.fileCategory', compact('data', 'userName', 'files'));
    }


     public function fileCategory(Request $request){
        
        
        $files =new FileCategory();
        $files->fileName = $request->fileName;
        $files->uploadedBy = $request->uploadedBy;
        $res = $files->save();
        
        if($res){
            AuditLogger::log(
                'FileCategory',
                'Create',
                'Added new file category: ' . $files->fileName,
                Session::get('loginId') ?? null,
                null,
                ['fileName' => $files->fileName, 'uploadedBy' => $files->uploadedBy]
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
        $user = [];

        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        // Fetch all file categories added by professors
        $fileCategories = FileCategory::all();

        // Student's own uploaded files
        $data = FileRequirement::where('uploadedBy', '=', $user->full_name)->get();

        return view('students.fileReq', compact('user', 'fileCategories', 'data'));
    }

public function fileReqCreate(Request $request){
   

    
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




    public function studentRequirements(Request $request){

        // Retrieve the value from the query parameter
        $value = $request->input('value');
        $data = [];

        $student = User::where('full_name', '=', $value)->first();
        $course=$student->course;
       
        
        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name;
        }
        $files=FileRequirement::where('adviser', '=',$data->full_name)
                                ->where('uploadedBy', '=', $value)
                                ->get();
        
        return view('professor.studentRequire', compact('data','files', 'value','course'));

            
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
                // Validate the request data if needed
        
                // Find the file requirement by ID
                $fileRequirement = FileRequirement::findOrFail($id);
        
                // Update the status based on the request data
                $fileRequirement->status = 2;
                $fileRequirement->save();
                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Denied file: ' . $fileRequirement->fileName,
                    Session::get('loginId') ?? null,
                    ['status' => 0],
                    ['status' => 2]
                );
                return back()->with('success', 'You have updated the information successfully!');
            }

            public function requirementsView(Request $request){

                // Retrieve the value from the query parameter
                $value = $request->input('value');
                $file = $request->input('file');
                $data = [];
               
                
                if (Session::has('loginId')) {
                    $data = User::where('id', '=', Session::get('loginId'))->first();
                    $userName = $data->full_name;
                }
                $files=FileRequirement::where('adviser', '=',$data->full_name)
                                        ->where('uploadedBy', '=', $value)
                                        ->where('fileName', '=', $file)
                                        ->get();
                
                return view('professor.requireView', compact('data','files','value','file'));
        
                    
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
