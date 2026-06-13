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
use App\Models\Announcements;
use App\Models\FileCategory;
use App\Models\FileRequirement;
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
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\AuditLogger;
use App\Services\ReportAiInsightService;


class ProfessorController extends Controller
{
    
public function class()
{
    $data = null;
    $classrooms = [];
    $courses = Courses::all();
    $announcements = collect();

    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
        $hasUploadClassId = Schema::hasColumn('uploaded_files', 'class_id');
        $hasScheduleClassId = Schema::hasTable('schedules') && Schema::hasColumn('schedules', 'class_id');
        $hasClassScheduleDay = Schema::hasColumn('classes', 'schedule_day');
        $hasClassScheduleTime = Schema::hasColumn('classes', 'schedule_time');

        // Get all rooms (classes) where this professor is adviser
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $announcements = Announcements::where('announcer', $data->full_name)->latest()->get();

        // For each room, preload students needing approval and all students
        foreach ($classrooms as $room) {
            $room->needingApproval = User::where('status', 3)
                ->whereHas('studentInfo', function ($query) use ($room, $data) {
                    $query->where('class_id', $room->id);

                    // Legacy students without class_id are only shown for legacy rooms (no SY set).
                    if (empty($room->school_year_start) || empty($room->school_year_end)) {
                        $query->orWhere(function ($legacy) use ($room, $data) {
                            $legacy->whereNull('class_id')
                                ->where('course', $room->course)
                                ->where('adviser_name', $data->full_name);
                        });
                    }
                })
                ->get();

            $room->students = User::where('status', 1)
                ->whereHas('studentInfo', function ($query) use ($room, $data) {
                    $query->where('class_id', $room->id);

                    // Legacy students without class_id are only shown for legacy rooms (no SY set).
                    if (empty($room->school_year_start) || empty($room->school_year_end)) {
                        $query->orWhere(function ($legacy) use ($room, $data) {
                            $legacy->whereNull('class_id')
                                ->where('course', $room->course)
                                ->where('adviser_name', $data->full_name);
                        });
                    }
                })
                ->get();

            if ($hasUploadClassId) {
                $room->templateFiles = UploadedFile::where('class_id', $room->id)
                    ->latest()
                    ->get();
            } else {
                // Legacy fallback when uploaded_files.class_id does not exist yet
                $room->templateFiles = UploadedFile::where('uploader_name', $data->full_name)
                    ->latest()
                    ->get();
            }

            // Room schedule source priority: schedules.class_id -> classes.schedule_* fallback.
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
    }

    return view('professor.class', [
        'data' => $data,
        'course' => $courses,
        'class' => $classrooms,
        'announcements' => $announcements
    ]);
}
 
    
public function show($roomId)
{
    $data = array();
    
    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }
    
    if($data->status == 0){
    $course = Classes::find($roomId);
    
    if (!$course) {
        return redirect()->back()->with('error', 'Room not found.');
    }
    
    $students = User::where('status', 3)
        ->whereHas('studentInfo', function ($query) use ($roomId, $course, $data) {
            $query->where('class_id', $roomId);

            // Legacy students without class_id are only shown for legacy rooms (no SY set).
            if (empty($course->school_year_start) || empty($course->school_year_end)) {
                $query->orWhere(function ($legacy) use ($course, $data) {
                    $legacy->whereNull('class_id')
                        ->where('course', $course->course)
                        ->where('adviser_name', $data->full_name);
                });
            }
        })
        ->get();

    // Pass the $course and $students variables to the view
    return view('professor.listStudents', compact('course', 'students', 'data'));
}
    

}
public function roomCreate(Request $request){

    $request->validate([
        'room' => 'required|string|max:255',
        'course' => 'required|string|max:255',
        'semester' => 'required|string|max:50',
        'school_year_start' => 'required|integer',
        'school_year_end' => 'required|integer|gt:school_year_start',
        'schedule_day' => 'nullable|array',
        'schedule_day.*' => 'string',
        'time_slots' => 'nullable|integer|min:1|max:4',
    ]);
    
    $data=array();
            if(Session::has('loginId')){

                $data=User::where('id','=', Session::get('loginId'))->first();
                        }

$room =new Classes();
$room->room = $request->room;
$room->course = $request->course;
$room->semester = $request->semester;
$room->school_year_start = $request->school_year_start;
$room->school_year_end = $request->school_year_end;
$room->adviser_name = $data->full_name;

$scheduleDays = $request->input('schedule_day', []);
$timeSlots = (int) $request->input('time_slots', 1);
$scheduleData = [];

foreach ($scheduleDays as $day) {
    for ($i = 1; $i <= $timeSlots; $i++) {
        $startTime = $request->input($day . '_start_time_' . $i);
        $endTime = $request->input($day . '_end_time_' . $i);

        if (!empty($startTime) && !empty($endTime)) {
            $scheduleData[] = [
                'day' => $day,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        }
    }
}

if (Schema::hasColumn('classes', 'schedule_day')) {
    $room->schedule_day = !empty($scheduleData) ? json_encode($scheduleData) : null;
}
if (Schema::hasColumn('classes', 'schedule_time')) {
    $room->schedule_time = (string) $timeSlots;
}




$res = $room->save();

if ($res) {
    $this->syncRoomSchedule($room, $scheduleData, $timeSlots);
}

if ($res) {
    AuditLogger::log(
        'Class Room',           // module
        'create',               // action
        'Created room: ' . $room->room . ' for course: ' . $room->course, // description
        $data->id ?? null       // affected user ID (the acting professor)
    );
    return back()->with('success','You have registered successfully!');
}
else{
    return back()->with('fail','Oh no! Something went wrong.');
}
}

public function roomUpdate(Request $request, $id)
{
    $request->validate([
        'room' => 'required|string|max:255',
        'course' => 'required|string|max:255',
        'semester' => 'required|string|max:50',
        'school_year_start' => 'required|integer',
        'school_year_end' => 'required|integer|gt:school_year_start',
        'schedule_day' => 'nullable|array',
        'schedule_day.*' => 'string',
        'time_slots' => 'nullable|integer|min:1|max:4',
    ]);

    $data = null;
    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }

    $room = Classes::find($id);
    if (!$room) {
        return back()->with('fail', 'Room not found.');
    }

    $room->room = $request->room;
    $room->course = $request->course;
    $room->semester = $request->semester;
    $room->school_year_start = $request->school_year_start;
    $room->school_year_end = $request->school_year_end;

    $scheduleData = [];
    $timeSlots = null;
    $scheduleDays = $request->input('schedule_day', []);
    $hasScheduleInput = !empty($scheduleDays);

    if ($hasScheduleInput) {
        $timeSlots = (int) $request->input('time_slots', 1);

        foreach ($scheduleDays as $day) {
            for ($i = 1; $i <= $timeSlots; $i++) {
                $startTime = $request->input($day . '_start_time_' . $i);
                $endTime = $request->input($day . '_end_time_' . $i);

                if (!empty($startTime) && !empty($endTime)) {
                    $scheduleData[] = [
                        'day' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ];
                }
            }
        }

        if (Schema::hasColumn('classes', 'schedule_day')) {
            $room->schedule_day = !empty($scheduleData) ? json_encode($scheduleData) : null;
        }
        if (Schema::hasColumn('classes', 'schedule_time')) {
            $room->schedule_time = (string) $timeSlots;
        }
    }

    $room->save();

    if ($hasScheduleInput && $timeSlots !== null) {
        $this->syncRoomSchedule($room, $scheduleData, $timeSlots);
    }

    AuditLogger::log(
        'Class Room',
        'update',
        'Updated room: ' . $room->room . ' for course: ' . $room->course,
        $data->id ?? null
    );

    return back()->with('success', 'Room updated successfully!');
}

public function roomDelete($id)
{
    $data = null;
    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }

    $room = Classes::find($id);

    if (!$room) {
        return response()->json(['error' => 'Room not found'], 404);
    }
    if ($room) {
        $roomName = $room->room;
        $courseName = $room->course;

        // Unassign students from this room before deleting it.
        if (Schema::hasColumn('students', 'class_id')) {
            Student::where('class_id', $room->id)->update(['class_id' => null]);
        }

        if (Schema::hasTable('schedules') && Schema::hasColumn('schedules', 'class_id')) {
            Schedule::where('class_id', $room->id)->delete();
        }

        $room->delete();
        AuditLogger::log(
            'Class Room',
            'delete',
            'Deleted room: ' . $roomName . ' from course: ' . $courseName,
            $data->id ?? null
        );
    }
    return response()->json(['success' => true]);
}

private function syncRoomSchedule($room, array $scheduleData, int $timeSlots): void
{
    if (!Schema::hasTable('schedules') || !Schema::hasColumn('schedules', 'class_id')) {
        return;
    }

    $academicYear = null;
    if (!empty($room->school_year_start) && !empty($room->school_year_end)) {
        $academicYear = $room->school_year_start . '-' . $room->school_year_end;
    }

    Schedule::updateOrCreate(
        ['class_id' => $room->id],
        [
            'subject_code' => null,
            'course' => $room->course,
            'academic_year' => $academicYear,
            'semester' => $room->semester,
            'schedule_day' => !empty($scheduleData) ? json_encode($scheduleData) : null,
            'schedule_time' => (string) $timeSlots,
        ]
    );
}

public function show_list($roomId)
{
    $data = array();

    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }

    if ($data->status == 0) {
        $course = Classes::find($roomId);

        if (!$course) {
            return redirect()->back()->with('error', 'Room not found.');
        }

        $studentsQuery = User::with('studentInfo')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->where('users.status', 1)
            ->orderBy('students.school_year_start', 'desc')
            ->orderBy('students.school_year_end', 'desc')
            ->select('users.*');

        $studentsQuery->where(function ($query) use ($roomId, $course, $data) {
            $query->where('students.class_id', $roomId);

            // Legacy students without class_id are only shown for legacy rooms (no SY set).
            if (empty($course->school_year_start) || empty($course->school_year_end)) {
                $query->orWhere(function ($legacy) use ($course, $data) {
                    $legacy->whereNull('students.class_id')
                        ->where('students.course', $course->course)
                        ->where('students.adviser_name', $data->full_name);
                });
            }
        });

        $students = $studentsQuery->get();

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

public function requirementStatusClasses()
{
    if (!Session::has('loginId')) {
        return redirect('/login');
    }

    $data = User::where('id', Session::get('loginId'))->first();
    $professor = Professor::where('full_name', $data->full_name)->first();
    $categoryCount = $professor
        ? FileCategory::where('professor_id', $professor->id)->count()
        : 0;

    $allClasses = Classes::where('adviser_name', $data->full_name)
        ->orderBy('course')
        ->orderBy('room')
        ->get();

    $classStats = [];
    $totalStudents = 0;
    $totalCompleteStudents = 0;

    foreach ($allClasses as $classroom) {
        $studentQuery = User::join('students', 'users.id', '=', 'students.user_id')
            ->where('users.status', 1)
            ->select('users.full_name');

        $studentQuery->where(function ($query) use ($classroom, $data) {
            $query->where('students.class_id', $classroom->id);

            if (empty($classroom->school_year_start) || empty($classroom->school_year_end)) {
                $query->orWhere(function ($legacy) use ($classroom, $data) {
                    $legacy->whereNull('students.class_id')
                        ->where('students.course', $classroom->course)
                        ->where('students.adviser_name', $data->full_name);
                });
            }
        });

        $studentNames = $studentQuery->pluck('users.full_name')->filter()->values();
        $studentCount = $studentNames->count();
        $totalStudents += $studentCount;

        $submittedPairs = FileRequirement::where('adviser', $data->full_name)
            ->whereIn('uploadedBy', $studentNames)
            ->select('uploadedBy', 'fileName')
            ->get()
            ->groupBy('uploadedBy');

        $completeCount = 0;
        $submittedCategoryTotal = 0;
        foreach ($studentNames as $studentName) {
            $submittedCount = $submittedPairs->get($studentName, collect())
                ->pluck('fileName')
                ->map(fn ($name) => mb_strtolower(trim((string) $name)))
                ->unique()
                ->count();

            $submittedCategoryTotal += min($submittedCount, $categoryCount);

            if ($categoryCount > 0 && $submittedCount >= $categoryCount) {
                $completeCount++;
            }
        }

        $totalCompleteStudents += $completeCount;

        $classStats[$classroom->id] = [
            'student_count' => $studentCount,
            'complete_count' => $completeCount,
            'average_completion' => $studentCount > 0 && $categoryCount > 0
                ? round(($submittedCategoryTotal / ($studentCount * $categoryCount)) * 100)
                : 0,
        ];
    }

    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $perPage = 6;
    $currentItems = $allClasses->forPage($currentPage, $perPage)->values();

    foreach ($currentItems as $classroom) {
        if (isset($classStats[$classroom->id])) {
            foreach ($classStats[$classroom->id] as $key => $value) {
                $classroom->{$key} = $value;
            }
        }
    }

    $classes = new LengthAwarePaginator(
        $currentItems,
        $allClasses->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );

    return view('professor.requirementStatusClasses', compact(
        'data',
        'classes',
        'categoryCount',
        'allClasses',
        'totalStudents',
        'totalCompleteStudents'
    ));
}

public function requirementStatus(Request $request, $roomId)
{
    if (!Session::has('loginId')) {
        return redirect('/login');
    }

    $data = User::where('id', Session::get('loginId'))->first();
    $course = Classes::where('id', $roomId)
        ->where('adviser_name', $data->full_name)
        ->first();

    if (!$course) {
        return redirect()->back()->with('error', 'Room not found.');
    }

    $professor = Professor::where('full_name', $data->full_name)->first();
    $categories = $professor
        ? FileCategory::where('professor_id', $professor->id)->get()
        : collect();

    $categories = $categories
        ->sortBy('fileName', SORT_NATURAL | SORT_FLAG_CASE)
        ->values();

    $studentsQuery = User::with('studentInfo')
        ->join('students', 'users.id', '=', 'students.user_id')
        ->where('users.status', 1)
        ->orderBy('users.full_name')
        ->select('users.*');

    $studentsQuery->where(function ($query) use ($roomId, $course, $data) {
        $query->where('students.class_id', $roomId);

        if (empty($course->school_year_start) || empty($course->school_year_end)) {
            $query->orWhere(function ($legacy) use ($course, $data) {
                $legacy->whereNull('students.class_id')
                    ->where('students.course', $course->course)
                    ->where('students.adviser_name', $data->full_name);
            });
        }
    });

    $students = $studentsQuery->get();
    $categoryNames = $categories->pluck('fileName')->values();
    $categoryLookup = $categoryNames->mapWithKeys(function ($name) {
        return [mb_strtolower(trim((string) $name)) => $name];
    });

    $requirements = FileRequirement::where('adviser', $data->full_name)
        ->whereIn('uploadedBy', $students->pluck('full_name')->filter()->values())
        ->get()
        ->groupBy('uploadedBy');

    $allStudentStatuses = $students->map(function ($student) use ($requirements, $categoryNames, $categoryLookup) {
        $submittedFiles = $requirements->get($student->full_name, collect());
        $submittedByCategory = $submittedFiles->groupBy(function ($file) {
            return mb_strtolower(trim((string) $file->fileName));
        });

        $passed = collect();
        $missing = collect();
        $approved = collect();
        $pending = collect();
        $denied = collect();

        foreach ($categoryNames as $categoryName) {
            $key = mb_strtolower(trim((string) $categoryName));
            if ($submittedByCategory->has($key)) {
                $passed->push($categoryName);

                $categoryFiles = $submittedByCategory->get($key);
                if ($categoryFiles->where('status', 1)->isNotEmpty()) {
                    $approved->push($categoryName);
                } elseif ($categoryFiles->where('status', 2)->isNotEmpty()) {
                    $denied->push($categoryName);
                } else {
                    $pending->push($categoryName);
                }
            } else {
                $missing->push($categoryName);
            }
        }

        $extraSubmitted = $submittedByCategory->keys()
            ->filter(fn ($key) => !$categoryLookup->has($key))
            ->map(fn ($key) => optional($submittedByCategory->get($key)->first())->fileName)
            ->filter()
            ->values();

        return [
            'student' => $student,
            'passed' => $passed,
            'missing' => $missing,
            'approved' => $approved,
            'pending' => $pending,
            'denied' => $denied,
            'extraSubmitted' => $extraSubmitted,
            'submittedCount' => $passed->count(),
            'missingCount' => $missing->count(),
            'approvedCount' => $submittedFiles->where('status', 1)->count(),
            'pendingCount' => $submittedFiles->whereNotIn('status', [1, 2])->count(),
            'deniedCount' => $submittedFiles->where('status', 2)->count(),
            'completion' => $categoryNames->count() > 0
                ? round(($passed->count() / $categoryNames->count()) * 100)
            : 0,
        ];
    });

    $activeView = $request->query('view', 'overview');
    if (!in_array($activeView, ['overview', 'approved', 'pending', 'denied', 'missing'], true)) {
        $activeView = 'overview';
    }

    $perPage = (int) $request->query('per_page', 10);
    if (!in_array($perPage, [10, 25, 50], true)) {
        $perPage = 10;
    }

    $displayStatuses = $activeView === 'overview'
        ? $allStudentStatuses
        : $allStudentStatuses->filter(fn ($status) => $status[$activeView]->count() > 0)->values();

    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $displayStatuses->forPage($currentPage, $perPage)->values();

    $studentStatuses = new LengthAwarePaginator(
        $currentItems,
        $displayStatuses->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );

    $requirementInsights = $this->buildRequirementStatusInsights($course, $categories, $allStudentStatuses);

    return view('professor.requirementStatus', compact(
        'data',
        'course',
        'categories',
        'studentStatuses',
        'allStudentStatuses',
        'activeView',
        'requirementInsights'
    ));
}

protected function buildRequirementStatusInsights($course, $categories, $allStudentStatuses): array
{
    $totalStudents = $allStudentStatuses->count();
    $categoryCount = $categories->count();
    $completeStudents = $allStudentStatuses->where('missingCount', 0)->count();
    $studentsWithMissing = $allStudentStatuses->filter(fn ($status) => $status['missingCount'] > 0)->count();
    $studentsWithPending = $allStudentStatuses->filter(fn ($status) => $status['pendingCount'] > 0)->count();
    $averageCompletion = $totalStudents > 0 ? round($allStudentStatuses->avg('completion')) : 0;
    $submittedRequirements = (int) $allStudentStatuses->sum('submittedCount');
    $missingRequirements = (int) $allStudentStatuses->sum('missingCount');
    $approvedRequirements = (int) $allStudentStatuses->sum('approvedCount');
    $pendingRequirements = (int) $allStudentStatuses->sum('pendingCount');
    $deniedRequirements = (int) $allStudentStatuses->sum('deniedCount');

    $topMissing = $allStudentStatuses
        ->flatMap(fn ($status) => $status['missing']->values())
        ->countBy()
        ->sortDesc()
        ->take(5)
        ->map(fn ($count, $label) => ['label' => $label, 'count' => $count])
        ->values();

    $classLabel = trim(($course->course ?? '') . ' ' . ($course->room ?? ''));
    $schoolYear = ($course->school_year_start && $course->school_year_end)
        ? $course->school_year_start . '-' . $course->school_year_end
        : 'Not set';

    $highlights = [
        'Class coverage includes ' . $totalStudents . ' student' . ($totalStudents === 1 ? '' : 's') . ' and ' . $categoryCount . ' required categor' . ($categoryCount === 1 ? 'y' : 'ies') . '.',
        $completeStudents . ' student' . ($completeStudents === 1 ? '' : 's') . ' have completed all tracked requirement categories.',
        'Average requirement completion is ' . $averageCompletion . '%.',
    ];

    if ($topMissing->isNotEmpty()) {
        $firstMissing = $topMissing->first();
        $highlights[] = 'Most common missing requirement: ' . $firstMissing['label'] . ' (' . $firstMissing['count'] . ' student' . ($firstMissing['count'] === 1 ? '' : 's') . ').';
    }

    $watchouts = [];
    if ($studentsWithMissing > 0) {
        $watchouts[] = $studentsWithMissing . ' student' . ($studentsWithMissing === 1 ? '' : 's') . ' still have missing requirement categories.';
    }
    if ($pendingRequirements > 0) {
        $watchouts[] = $pendingRequirements . ' submitted requirement' . ($pendingRequirements === 1 ? '' : 's') . ' still need review.';
    }
    if ($deniedRequirements > 0) {
        $watchouts[] = $deniedRequirements . ' requirement' . ($deniedRequirements === 1 ? '' : 's') . ' have been denied and may need resubmission.';
    }

    $actions = [
        'Prioritize students with missing or denied requirements.',
        'Review pending submitted files before exporting the report.',
        'Use the requirement tabs to isolate missing, pending, approved, and denied items.',
    ];

    return app(ReportAiInsightService::class)->summarize('requirement_status', [
        'class' => $classLabel,
        'school_year' => $schoolYear,
        'total_students' => $totalStudents,
        'required_categories' => $categoryCount,
        'complete_students' => $completeStudents,
        'students_with_missing' => $studentsWithMissing,
        'students_with_pending' => $studentsWithPending,
        'average_completion' => $averageCompletion,
        'submitted_requirements' => $submittedRequirements,
        'missing_requirements' => $missingRequirements,
        'approved_requirements' => $approvedRequirements,
        'pending_requirements' => $pendingRequirements,
        'denied_requirements' => $deniedRequirements,
        'top_missing_requirements' => $topMissing->all(),
    ], $highlights, $watchouts, $actions);
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
    AuditLogger::log(
        'Student Account',
        'approve',
        'Approved student: ' . $user->full_name,
        $user->id
    );
    // Send approval email
    Mail::to($user->email)->send(new UserApproved($user));

    return back()->with('success', 'You have updated the information successfully!');
}

public function approveAll(Request $request, $roomId)
{
    $data = null;

    if (Session::has('loginId')) {
        $data = User::where('id', Session::get('loginId'))->first();
    }

    $course = Classes::find($roomId);

    if (!$data || !$course || $course->adviser_name !== $data->full_name) {
        return back()->with('error', 'Unable to approve students for this class.');
    }

    $students = User::where('status', 3)
        ->whereHas('studentInfo', function ($query) use ($roomId, $course, $data) {
            $query->where('class_id', $roomId);

            if (empty($course->school_year_start) || empty($course->school_year_end)) {
                $query->orWhere(function ($legacy) use ($course, $data) {
                    $legacy->whereNull('class_id')
                        ->where('course', $course->course)
                        ->where('adviser_name', $data->full_name);
                });
            }
        })
        ->get();

    if ($students->isEmpty()) {
        return back()->with('info', 'There are no pending students to approve.');
    }

    foreach ($students as $student) {
        $student->status = 1;
        $student->save();

        Mail::to($student->email)->send(new UserApproved($student));
    }

    AuditLogger::log(
        'Student Account',
        'approve',
        'Approved all pending students in room: ' . $course->room . ' (' . $students->count() . ' students)',
        $data->id ?? null
    );

    return back()->with('success', $students->count() . ' student request(s) approved successfully.');
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
    AuditLogger::log(
        'Student Account',
        'deny',
        'Denied student: ' . $user->full_name . '. Reason: ' . $request->input('reason'),
        $user->id
    );

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
AuditLogger::log(
    'Professor',
    'update',
    'Updated professor: ' . $professor->full_name . '. Email changed: ' . $initialProfessorEmail . ' → ' . $professor->email,
    $user->id ?? null
);


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

    $selectedCourse = request('course');
    $students = User::where('role', 0)
        ->whereHas('studentInfo', function ($query) use ($user, $selectedCourse) {
            $query->where('adviser_name', $user->full_name);
            if ($selectedCourse) {
                $query->where('course', $selectedCourse);
            }
        })
        ->where('created_at', '>=', $sixMonthsAgo)
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

        // Remove classes/rooms created by this professor
        $rooms = Classes::where('adviser_name', $professor->full_name)->get();
        foreach ($rooms as $room) {
            $roomName = $room->room;
            $courseName = $room->course;
            $room->delete();
            AuditLogger::log(
                'Class Room',
                'delete',
                'Deleted room: ' . $roomName . ' from course: ' . $courseName,
                $user ? $user->id : null
            );
        }

        // Delete the professor
        $professor->delete();
        AuditLogger::log(
            'Professor',
            'delete',
            'Deleted professor: ' . $professor->full_name . ' and all associated data (students, subjects, schedules, rooms)',
            $user ? $user->id : null
        );

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
