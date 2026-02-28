<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Courses;
use App\Models\Student;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use Illuminate\Support\Str;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\Announcements;
use App\Models\OJTInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentNotificationMail;
use Illuminate\Support\Facades\Session;

class StudentController extends Controller
{
    public function student_acc()
    {
       
        $course=Courses::all();
        $data=array();
        if(Session::has('loginId')){

            $data=User::where('id','=', Session::get('loginId'))->first();
                    }

	    return view('students.student_account', compact('data','course'));

    }
    public function edit(Request $request, $email)
    {
        // Check if the user exists
    $user = User::where('email', $email)->first();
 


                // Update user data
                $user->first_name = $request->first_name;
                $user->middle_name = $request->middle_name;
                $user->last_name = $request->last_name;
                $user->full_name = $user->first_name . ' ' . $user->last_name;
                $user->suffix = $request->suffix;
                $user->address = $request->address;
                $user->contact_number = $request->contact_number;
                $user->date_of_birth = $request->date_of_birth;
                $user->course = $request->course;
                $user->year_and_section = $request->year_and_section;
                $user->studentNum = $request->studentNum;
                $user->email = $request->email;


    
            // Check if the student exists and update its data
            $student = Student::where('email', $email)->first();
     
                
                $student->first_name = $user->first_name;
                $student->middle_name = $user->middle_name;
                $student->last_name = $user->last_name;
                $student->full_name = $user->full_name;
                $student->suffix = $user->suffix ;
                $student->address = $user->address;
                $student->contact_number = $user->contact_number;
                $student->date_of_birth = $user->date_of_birth;
                $student->course = $user->course;
                $student->year_and_section = $user->year_and_section;
                $student->studentNum = $user->studentNum;
                $student->email = $user->email;
                $student->save();
                $user->save();
    
            return back()->with('success', 'You have updated the information successfully!');
      
    }

    public function class()
    {   

        $sched = []; 
        $class = [];
        $course=Courses::all();
       
        
        $announce=[];
        $data=array();
            if(Session::has('loginId')){

                $data=User::where('id','=', Session::get('loginId'))->first();
            }
                         
            $class = [];

            // Check if $data is not empty before accessing the property
            if (!empty($data) && isset($data->adviser_name)) {
                $class = Classes::where('adviser_name', $data->adviser_name)
                               ->where('course', $data->course)
                               ->get();

                $professor = Professor::where('full_name', $data->adviser_name)->with('subjects')->get();

            // Iterate over each professor to retrieve subjects and related schedules
            foreach ($professor as $prof) {
                $subjectCodes = $prof->subjects->pluck('subject_code')->all();

                $sched = array_merge($sched, Schedule::whereIn('subject_code', $subjectCodes)
                    ->whereIn('course', $class->pluck('course')->all())
                    ->get()->all());
            }
            }
       
        //    $class = Classes::where('adviser_name', $data->adviser_name)->get();


        if (!empty($data) && isset($data->status) && $data->status == 1) {
            // User's status is 1, allow access to announcements
            $announce = Announcements::where(function ($query) use ($data) {
                $query->where('announcer', 'Gina Dela Cruz')
                      ->orWhere('announcer', $data->adviser_name);
            })->get();
        } else {
            $announce = [];
        }
        

    // Pass the $professor and $students variables to the view
    return view('students.student_class', compact('data', 'course', 'class', 'announce', 'sched'));


}


public function join(Request $request,$email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }
    
    
        // Update user data

        $user->status = 3;
    
        $user->save();
    
        return back()->with('success', 'You have updated the information successfully!');

    }

    public function leave(Request $request)
    {
        if (Session::has('loginId')) {

            $user = User::where('id', Session::get('loginId'))->first();

            if (!$user) {
                return response()->json(['error' => true], 404);
            }

            // Just change the status to "not joined"
            $user->status = 0;

            $user->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => true], 403);
    }
    public function fileSee()
    {   
        $data=array();
            if(Session::has('loginId')){

                $user=User::where('id','=', Session::get('loginId'))->first();
                        }
           $class = Classes::where('adviser_name', $user->adviser_name)->get();
            // Fetch and display the templates where Uploader_name matches adviser_name
    $upload = UploadedFile::where(function ($query) use ($user) {
        $query->where('uploader_name', 'Gina Dela Cruz')
              ->orWhere('uploader_name', $user->adviser_name);
    })
    ->get();

    // Pass the $professor and $students variables to the view
    return view('students.student_file', compact('data','upload','class','user'));

}

public function StuList()
{
    $user = [];

    if (Session::has('loginId')) {
        $user = User::where('id', '=', Session::get('loginId'))->first();
    }

    // Get the current date and subtract 6 months
    $sixMonthsAgo = Carbon::now()->subMonths(6);

    $students = User::where('role', 0)
                ->where('status', 1)
                ->where('created_at', '>=', $sixMonthsAgo) // Add condition for created_at
                ->get();
    $studentData = [];
    
    // Initialize subject data array outside the loop
    $subjectData = [];
    $course = Courses::all();


    foreach ($students as $student) {
        $ojt = OJTInformation::where('studentNum', $student->studentNum)->first();
    
        // Find the professor associated with the logged-in user's adviser_name
        $professor = Professor::where('full_name', $student->adviser_name)->first();
    
        // Clear subject data array for each student
        $subjectData = [];
    
        if ($professor) {
            // Get the subjects associated with the professor through the relationship
            $subjects = $professor->subjects;
    
            foreach ($subjects as $subject) {
                // Add the subject data to the array
                $subjectData[] = [
                    'subject_code' => $subject->subject_code,
                    'subject_description' => $subject->subject_description,
                ];
            }
        }
    
        // Add the student and associated OJT and subject data to the data array
        $studentData[] = [
            'student' => $student,
            'ojt' => $ojt,
            'subjects' => $subjectData, // Include subject data here
        ];
    }

    return view('ojtCoordinator.students', compact('studentData', 'user', 'subjectData','course'));
}



public function ojtInformation()
{
    $course=Courses::all();
    $data=array();
    if(Session::has('loginId')){

        $data=User::where('id','=', Session::get('loginId'))->first();
                }
                $user = OJTInformation::where('studentNum', $data->studentNum)->first();

    return view('students.ojtinfo', compact('data','course','user'));

}
public function ojt_edit(Request $request,$studentNum)
    {

        $data=array();
        if(Session::has('loginId')){
    
            $data=User::where('id','=', Session::get('loginId'))->first();
                    }
    
        $user = OJTInformation::where('studentNum', $data->studentNum)->first();


    
    
        // Update user data
        $user->company_name = $request->company_name;
        $user->company_address = $request->company_address;
        $user->nature_of_bus = $request->nature_of_bus;
        $user->nature_of_link = $request->nature_of_link;
        $user->level = $request->level;
        $user->start_date = $request->start_date;
        $user->finish_date = $request->finish_date;
        $user->contact_name = $request->contact_name;
        $user->report_time = $request->report_time;
        $user->contact_position = $request->contact_position;
        $user->contact_number = $request->contact_number;
    
        $user->save();
    
        return back()->with('success', 'You have updated the information successfully!');

    }

    public function update(Request $request, $studentNum)
    {
        // Update OJTInformation model if necessary
        $ojtInformation = OJTInformation::where('studentNum', $studentNum)->first();
    
        if (!$ojtInformation) {
            return back()->with('error', 'OJT Information not found for the student.');
        }
    
        // Update the status of the OJTInformation model
        $ojtInformation->status = $request->status;
        $ojtInformation->save();
    
        return back()->with('success', 'You have updated the information successfully!');
    }


  

    public function notify(Request $request, $studentNum)
    {
        // Find the student's email
        $student = User::where('studentNum', $studentNum)->first();
    
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }
    
        // Find the OJTInformation for the student
        $ojtInformation = OJTInformation::where('studentNum', $studentNum)->first();
    
        if (!$ojtInformation) {
            return back()->with('error', 'OJT Information not found for the student.');
        }
    
        // Get the status from OJTInformation
        $status = $ojtInformation->status;
    
        // Send the notification email with the status
        $notificationMail = new StudentNotificationMail($student, $status); // Pass $status to the email
        Mail::to($student->email)->send($notificationMail);
    
        return back()->with('success', 'Notification sent.');
    }
    
    
    public function acceptTerms(Request $request)
    {
        Session::put('termsAccepted', true); // lasts for this session only
        return response()->json(['success' => true]);
    }

}
