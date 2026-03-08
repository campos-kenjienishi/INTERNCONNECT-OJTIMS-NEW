<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Professor;
use App\Models\Courses;
use Illuminate\Support\Str;
Use App\Mail\TemporaryPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Helpers\AuditLogger;

class MaintenanceController extends Controller
{

    public function maintenance(){

        $user=array();
        if(Session::has('loginId')){
    
            $user=User::where('id','=', Session::get('loginId'))->first();
                    }

        $data=Courses::all();

    return view('ojtCoordinator.maintenance', compact('data','user'));
        
    }
    public function courses(Request $request){
        
        
        $courses =new Courses();
        $courses->course = $request->course;
        $courses->acronym = $request->acronym;
        $res = $courses->save();
        
        if($res){
            AuditLogger::log(
                'Maintenance',
                'Create',
                'Added course: ' . $courses->course . ' (' . $courses->acronym . ')',
                Session::get('loginId') ?? null,
                null,
                ['course' => $courses->course, 'acronym' => $courses->acronym]
            );
            return back()->with('success','You have added the course successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }




    public function remove($id)
    {

        $data = Courses::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }
    
        $data->delete();
        if($data){
            AuditLogger::log(
                'Maintenance',
                'Delete',
                'Deleted course: ' . $data->course . ' (' . $data->acronym . ')',
                Session::get('loginId') ?? null,
                ['course_id' => $data->id, 'course' => $data->course, 'acronym' => $data->acronym],
                null
            );
        }
        return redirect()->back();
    }
    
    public function auditTrail()
    {
        // Get the logged-in coordinator info
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        // Fetch all audit logs for display in coordinator tab, latest first
        $logs = \App\Models\AuditLog::orderBy('created_at', 'desc')->get();

        // Return the coordinator audit log view
        return view('ojtCoordinator.audit', compact('logs', 'data'));
    }
    
}