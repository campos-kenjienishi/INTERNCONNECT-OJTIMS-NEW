<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Classes;
use App\Models\Courses;
use App\Models\Student;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use App\Mail\DenialReason;
use App\Mail\UserApproved;
use Illuminate\Support\Str;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\OJTInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class ProfessorController extends Controller
{
    
public function class()
{
    $data = null;
    $classrooms = [];
    $courses = Courses::all();

    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();

        // Get all rooms (classes) where this professor is adviser
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();

        // For each room, preload students needing approval and all students
        foreach ($classrooms as $room) {
            // Students needing approval (status = 0 or 3)
            $room->needingApproval = User::where('course', $room->course)
                ->where('status', 0)
                ->where('adviser_name', $data->full_name)
                ->get();

            // All students in this class/room (status = 1 = approved)
            $room->students = User::where('course', $room->course)
                ->where('status', 1)
                ->where('adviser_name', $data->full_name)
                ->get();
        }
    }

    return view('professor.class', [
        'data' => $data,
        'course' => $courses,
        'class' => $classrooms
    ]);
}
 
    
public function show($courseName)
{
    $data = array();
    
    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }
    
    if($data->status == 0){
    // Assuming you have logic to retrieve the professor and students data
    $course = Classes::where('course', $courseName)->first();
    
    
    
    $students = User::where('course', $course->course)->where('status', 3)->where('adviser_name', $data->full_name)->get();;


    // Pass the $course and $students variables to the view
    return view('professor.listStudents', compact('course', 'students', 'data'));
}
    

}
public function roomCreate(Request $request){

    
    $data=array();
            if(Session::has('loginId')){

                $data=User::where('id','=', Session::get('loginId'))->first();
                        }

$room =new Classes();
$room->room = $request->room;
$room->course = $request->course;
$room->adviser_name = $data->full_name;




$res = $room->save();

if($res){
    return back()->with('success','You have registered successfully!');
}
else{
    return back()->with('fail','Oh no! Something went wrong.');
}
}

public function roomDelete($id)
{
    $room = Classes::find($id);

    if (!$room) {
        return response()->json(['error' => 'Room not found'], 404);
    }

    // Delete the room immediately
    $room->delete();

    return response()->json(['success' => true]);
}

public function show_list($courseName)
{
    $data = array();

    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }

    if ($data->status == 0) {
        // Assuming you have logic to retrieve the professor and students data
        $course = Classes::where('course', $courseName)->first();

        if (!$course) {
            // Handle the case where the course doesn't exist
            return redirect()->back()->with('error', 'Course not found.');
        }

        $students = User::with('studentInfo')
            ->join('students', 'users.studentNum', '=', 'students.studentNum')
            ->where('users.course', $course->course)
            ->where('users.status', 1)
            ->where('users.adviser_name', $data->full_name)
            ->orderBy('students.school_year_start', 'desc')
            ->orderBy('students.school_year_end', 'desc')
            ->select('users.*')
            ->get();

        $studentData = [];

        foreach ($students as $student) {
            $ojt = OJTInformation::where('studentNum', $student->studentNum)->first();
            
            // Add the student and associated OJT information to the data array
            $studentData[] = [
                'student' => $student,
                'ojt' => $ojt,
            ];
        }

        // Pass the $course and $students variables to the view
        return view('professor.classList', compact('course', 'studentData', 'data'));
    }
}

    



public function approve(Request $request, $email)
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    // Update user data
    $user->status = 1;
    $user->save();

    // Send approval email
    Mail::to($user->email)->send(new UserApproved($user));

    return back()->with('success', 'You have updated the information successfully!');
}

   

    public function deny(Request $request, $email)
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    // Update user data
    $user->status = 2;
    $user->save();

    // Get the reason for denial from the form
    $reason = $request->input('reason');

    // Send denial email with reason
    Mail::to($user->email)->send(new DenialReason($reason));

    return back()->with('success', 'You have updated the information successfully!');
}


public function uploadP()
{   
           // Get the currently logged-in user's name
           $user=array();
           if(Session::has('loginId')){
   
               $user=User::where('id','=', Session::get('loginId'))->first();
                       }
   
    $userName=$user->full_name;
// Fetch data from the database where the uploader_name matches the currently logged-in user's name
$data = UploadedFile::all();

return view('professor.uploadt', compact('data','user'));

}


public function update(Request $request)
{
    // Validate the form data
    $validatedData = $request->validate([
        'professor_id' => 'required|exists:professors,id',
        'email' => 'required|email',
        
    ]);

    // Find the professor
$professor = Professor::find($validatedData['professor_id']);

if (!$professor) {
    return back()->with('error', 'Professor not found.');
}

// Store the initial professor email
$initialProfessorEmail = $professor->email;

// Update the professor's email
$professor->email = $validatedData['email'];

Student::where('adviser_name', $professor->full_name)->update(['adviser_name' => $professor->full_name]);
// Save the updated professor
$professor->save();

// Retrieve the associated subjects and update them
$professor->subjects()->update([
    'subject_code' => $request->input('subject_code'),
    'subject_description' => $request->input('subject_description'),
]);

// Find the user with the initial professor email
$user = User::where('email', $initialProfessorEmail)->first();

if ($user) {
    // Update the user email
    $user->email = $professor->email;
    $user->save();

   
} else {
    // Handle the case where the user with the initial professor email doesn't exist
    return back()->with('error', 'User not found.');
}

// Redirect back with a success message
return back()->with('success', 'Professor details and associated subjects updated successfully.');

}


public function allStudents()
{
    $user = [];

    if (Session::has('loginId')) {
        $user = User::where('id', '=', Session::get('loginId'))->first();
    }

    // Get the current date and subtract 6 months
    $sixMonthsAgo = Carbon::now()->subMonths(6);

    $students = User::where('role', 0)
                ->where('adviser_name', $user->full_name)
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

    return view('professor.allStudents', compact('studentData', 'user', 'subjectData','course'));
}

public function removeProfessor($id)
{
    DB::beginTransaction();

    try {
        // Find the professor
        $professor = Professor::find($id);

        if (!$professor) {
            return response()->json([
                'success' => false,
                'message' => 'Professor not found'
            ], 404);
        }

        // Find the corresponding user by email
        $user = User::where('email', $professor->email)->first();

        // Get all subject codes associated with this professor
        $subjectCodes = $professor->subjects->pluck('subject_code');

        // Delete schedules linked to these subjects
        Schedule::whereIn('subject_code', $subjectCodes)->delete();

        // Remove associated subjects
        $professor->subjects()->delete();

        // Update students who had this professor as adviser
        Student::where('adviser_name', $professor->full_name)
            ->update(['adviser_name' => null]);

        // Delete the professor
        $professor->delete();

        // Delete the user account if exists
        if ($user) {
            $user->delete();
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Professor and all associated data removed successfully'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove professor: ' . $e->getMessage()
        ], 500);
    }
}

public function fetchProfessors(Request $request, $semester,$startYear,$endYear)
{
    $schoolYear = $startYear . '-' . $endYear;
   // Retrieve schedules with associated subjects for the given semester
$schedules = Schedule::with(['subject.professors']) // Include professors relationship
->where('semester', $semester)
->where('academic_year', $schoolYear)
->get();

// Initialize an empty array to store professor names
$professorNames = [];

// Loop through each schedule to retrieve the associated subject and professor
foreach ($schedules as $schedule) {
// Check if the schedule has a subject
if ($schedule->subject) {
    // Get the associated professors for the subject
    $professors = $schedule->subject->professors; // Use professors() relationship
    // Extract professor names and add them to the array
    foreach ($professors as $professor) {
        $professorNames[] = $professor->full_name;
    }
}
}

// Remove duplicates from the professor names array
$uniqueProfessorNames = array_unique($professorNames);

// Return the unique professor names as a JSON response
return response()->json($uniqueProfessorNames);
}    
}