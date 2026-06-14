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
use Illuminate\Support\Facades\Schema;
use App\Helpers\AuditLogger;
use App\Models\Company; // ADD THIS at the top

class StudentController extends Controller
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

private function visibleAnnouncementsForStudent($data, $classRoomNames = [], $includeClassAnnouncements = false)
{
    $coordinatorNames = User::where('role', 1)->pluck('full_name')->filter()->values()->all();
    $hasAudienceColumn = Schema::hasColumn('announcements', 'audience');
    $hasTargetCourseColumn = Schema::hasColumn('announcements', 'target_course');
    $hasTargetRoomColumn = Schema::hasColumn('announcements', 'target_room');

    if (!$hasAudienceColumn) {
        return Announcements::where(function ($query) use ($data, $coordinatorNames, $includeClassAnnouncements) {
            $query->whereIn('announcer', $coordinatorNames);

            if ($includeClassAnnouncements && !empty($data->adviser_name)) {
                $query->orWhere('announcer', $data->adviser_name);
            }
        })->latest()->get();
    }

    return Announcements::where(function ($query) use ($data, $classRoomNames, $coordinatorNames, $includeClassAnnouncements, $hasTargetCourseColumn, $hasTargetRoomColumn) {
        $query->where(function ($coordinatorQuery) use ($coordinatorNames) {
            $coordinatorQuery->where('audience', 'all_students')
                ->whereIn('announcer', $coordinatorNames);
        })
        ->orWhere(function ($legacyCoordinatorQuery) use ($coordinatorNames) {
            $legacyCoordinatorQuery->whereNull('audience')
                ->whereIn('announcer', $coordinatorNames);
        });

        if ($includeClassAnnouncements) {
            $query->orWhere(function ($classQuery) use ($data, $classRoomNames, $hasTargetCourseColumn, $hasTargetRoomColumn) {
                $classQuery->where('audience', 'class')
                    ->where('announcer', $data->adviser_name);

                if ($hasTargetCourseColumn) {
                    $classQuery->where('target_course', $data->course);
                }

                if ($hasTargetRoomColumn) {
                    $classQuery->whereIn('target_room', $classRoomNames);
                }
            });
        }
    })->latest()->get();
}

public function home()
{
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $data = $sessionCheck;

    // GET COMPANIES
    $companies = Company::all();

    // GET FILE COUNT
    $fileCount = UploadedFile::count();

    $announcements = $this->visibleAnnouncementsForStudent($data)->take(5);

    return view('students.student_home', [
        'user' => $data,
        'companies' => $companies,
        'fileCount' => $fileCount,
        'announcements' => $announcements
    ]);
}
    public function student_acc()
    {
       $sessionCheck = $this->requireStudentSession();

       if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
           return $sessionCheck;
       }

       $data = $sessionCheck;
       
        $course=Courses::all();
            if ($data) {
                $studentProfile = Student::where('user_id', $data->id)->first();

                if ($studentProfile) {
                    // Ensure student profile values are always shown from students table.
                    $data->studentNum = $studentProfile->studentNum;
                    $data->course = $studentProfile->course;
                    $data->year_and_section = $studentProfile->year_and_section;
                    $data->adviser_name = $studentProfile->adviser_name;
                    $data->address = $studentProfile->address;
                    $data->contact_number = $studentProfile->contact_number;
                    $data->date_of_birth = $studentProfile->date_of_birth;
                }
            }

	    return view('students.student_account', compact('data','course'));

    }
    public function edit(Request $request, $email)
    {
        // Check if the user exists
    $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }
 


                // Update user data
                $user->first_name = $request->first_name;
                $user->middle_name = $request->middle_name;
                $user->last_name = $request->last_name;
                $user->full_name = $user->first_name . ' ' . $user->last_name;
                $user->suffix = $request->suffix;
                $user->email = $request->email;


    
            // Check if the student exists and update its data
            $student = Student::where('user_id', $user->id)->first();

            if (!$student) {
                $student = new Student();
                $student->user_id = $user->id;
            }
     
                
                $student->address = $request->address;
                $student->contact_number = $request->contact_number;
                $student->date_of_birth = $request->date_of_birth;
                $student->course = $request->course;
                $student->year_and_section = $request->year_and_section;
                $student->studentNum = $request->studentNum;
                $student->adviser_name = $request->adviser_name ?: $student->adviser_name;
                $student->user_id = $user->id;
                $student->save();
                $user->save();
                AuditLogger::log(
                    'Student Account',       // module
                    'update',                // action
                    'Updated personal information',  // description
                    $user->id                // affected user ID
                );
    
            return back()->with('success', 'You have updated the information successfully!');
      
    }

    public function class()
    {   
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $data = $sessionCheck;

        $sched = []; 
        $class = [];
        $roomTemplates = collect();
        $course=Courses::all();
       
        
        $announce=[];
                         
            $class = [];

            // Check if $data is not empty before accessing the property
            if (!empty($data) && isset($data->adviser_name)) {
                $class = Classes::where('adviser_name', $data->adviser_name)
                               ->where('course', $data->course)
                               ->get();

                $hasScheduleClassId = Schema::hasTable('schedules') && Schema::hasColumn('schedules', 'class_id');
                $hasClassScheduleDay = Schema::hasColumn('classes', 'schedule_day');
                $hasClassScheduleTime = Schema::hasColumn('classes', 'schedule_time');

                foreach ($class as $room) {
                    if ($hasScheduleClassId) {
                        $roomSchedule = Schedule::where('class_id', $room->id)->latest('id')->first();
                        if ($roomSchedule) {
                            $room->schedule_day = $roomSchedule->schedule_day;
                            $room->schedule_time = $roomSchedule->schedule_time;
                        }
                    }

                    if (!$hasScheduleClassId) {
                        if (!$hasClassScheduleDay) {
                            $room->schedule_day = null;
                        }
                        if (!$hasClassScheduleTime) {
                            $room->schedule_time = null;
                        }
                    }

                    $room->schedule_slots = !empty($room->schedule_time) ? (int) $room->schedule_time : 1;
                    $room->schedule_parsed = [];
                    if (!empty($room->schedule_day)) {
                        $decodedSchedule = json_decode($room->schedule_day, true);
                        if (is_array($decodedSchedule)) {
                            $room->schedule_parsed = $decodedSchedule;
                        }
                    }
                }

                if (Schema::hasColumn('uploaded_files', 'class_id') && Schema::hasColumn('students', 'class_id') && !empty($data->class_id)) {
                    $roomTemplates = UploadedFile::where('class_id', $data->class_id)
                        ->latest()
                        ->get();
                }

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


        $classRoomNames = $class->pluck('room')->filter()->values()->all();
        $announce = $this->visibleAnnouncementsForStudent(
            $data,
            $classRoomNames,
            !empty($data) && isset($data->status) && $data->status == 1
        );
        

    // Pass the $professor and $students variables to the view
    return view('students.student_class', compact('data', 'course', 'class', 'announce', 'sched', 'roomTemplates'));


}


public function join(Request $request, $email, $classId)
    {
        $user = User::where('email', $email)->first();
        $class = Classes::find($classId);

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if (!$class) {
            return back()->with('error', 'Class not found.');
        }
    
    
        // Update user data

        $user->status = 3;
        if (Schema::hasColumn('students', 'class_id')) {
            Student::where('user_id', $user->id)->update(['class_id' => $classId]);
        }
    
        $user->save();
        AuditLogger::log(
            'Student Account',
            'join',
            'Student joined class: ' . $class->room . ' (' . $class->course . ')',
            $user->id
        );   
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
            $studentProfile = Student::where('user_id', $user->id)->first();
            $class = null;

            if ($studentProfile && !empty($studentProfile->class_id)) {
                $class = Classes::find($studentProfile->class_id);
            }

            $user->status = 0;
            if (Schema::hasColumn('students', 'class_id')) {
                Student::where('user_id', $user->id)->update(['class_id' => null]);
            }

            $user->save();
            AuditLogger::log(
                'Student Account',
                'leave',
                $class
                    ? ('Student left class: ' . $class->room . ' (' . $class->course . ')')
                    : 'Student left class',
                $user->id
            );
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
            // Student download page shows global templates (not room-specific).
        if (Schema::hasColumn('uploaded_files', 'class_id')) {
            $upload = UploadedFile::where(function ($query) {
                    $query->whereNull('class_id')
                          ->orWhere('class_id', 0);
                })
                ->latest()
                ->get();
        } else {
            $upload = UploadedFile::latest()->get();
        }

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
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $course=Courses::all();
    $data = $sessionCheck;
                $studentNum = $data->studentNum;

                if (empty($studentNum) && !empty($data->id)) {
                    $studentProfile = Student::where('user_id', $data->id)->first();
                    $studentNum = $studentProfile->studentNum ?? null;
                }

                $user = !empty($studentNum)
                    ? OJTInformation::firstOrCreate(['studentNum' => $studentNum])
                    : new OJTInformation();

    return view('students.ojtinfo', compact('data','course','user', 'studentNum'));

}
public function ojt_edit(Request $request,$studentNum)
    {

        $data=array();
        if(Session::has('loginId')){
    
            $data=User::where('id','=', Session::get('loginId'))->first();
                    }
    
        $resolvedStudentNum = $data->studentNum;

        if (empty($resolvedStudentNum) && !empty($data->id)) {
            $studentProfile = Student::where('user_id', $data->id)->first();
            $resolvedStudentNum = $studentProfile->studentNum ?? null;
        }

        if (empty($resolvedStudentNum)) {
            return back()->with('fail', 'Student number is missing for this account.');
        }

        $user = OJTInformation::firstOrCreate(['studentNum' => $resolvedStudentNum]);

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
        AuditLogger::log(
            'OJT Information',
            'update',
            'Edited OJT information',
            $data->id
        );
    
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
        AuditLogger::log(
            'OJT Status',
            'update',
            'Changed OJT status',
            $data->id ?? null
        );
    
        return back()->with('success', 'You have updated the information successfully!');
    }

    public function notify(Request $request, $studentNum)
    {
        // Find the student's profile and linked user record
        $studentProfile = Student::with('user')->where('studentNum', $studentNum)->first();
    
        if (!$studentProfile || !$studentProfile->user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Student not found.'], 422);
            }

            return back()->with('error', 'Student not found.');
        }

        $student = $studentProfile->user;

        if (empty($student->email)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This student does not have an email address saved yet.'], 422);
            }

            return back()->with('error', 'This student does not have an email address saved yet.');
        }

        // Find the OJTInformation for the student
        $ojtInformation = OJTInformation::where('studentNum', $studentNum)->first();
    
        if (!$ojtInformation) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No OJT information is saved yet for this student, so the notification cannot be sent.'], 422);
            }

            return back()->with('error', 'OJT Information not found for the student.');
        }
    
        try {
            // Get the status from OJTInformation
            $status = $ojtInformation->status;
            $studentName = $student->full_name ?? 'the student';
            $successMessage = 'Notification email sent to ' . $studentName . ' for OJT status: ' . $status . '.';

            // Send the notification email with the status
            $notificationMail = new StudentNotificationMail($student, $status);
            Mail::to($student->email)->send($notificationMail);

            if ($request->expectsJson()) {
                return response()->json(['message' => $successMessage]);
            }

            return back()->with('success', $successMessage);
        } catch (\Throwable $e) {
            \Log::error('Student notification failed', [
                'studentNum' => $studentNum,
                'student_email' => $student->email ?? null,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Notification could not be sent: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Notification could not be sent.');
        }
    }
    
    
    public function acceptTerms(Request $request)
    {
        Session::put('termsAccepted', true); // lasts for this session only
        return response()->json(['success' => true]);
    }

}
