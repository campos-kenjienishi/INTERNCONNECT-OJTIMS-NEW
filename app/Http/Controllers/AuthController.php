<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use App\Models\FileRequirement;
use App\Models\FileCategory;
use App\Models\OjtEvaluationRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\Announcements;
use App\Models\OJTInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Helpers\AuditLogger;
use App\Services\ReportAiInsightService;

class AuthController extends Controller
{
    public function login(){
        return view("auth.login");
    }

    public function registration(){
        $data=Professor::all();
        $course=Courses::all();
        $schedules = Schedule::with('subject')->get();
        return view('auth.registration', compact('data','course','schedules'));
    }

    public function registerUser(Request $request){
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email|unique:users,email',
            'studentNum'=>'required',
            'password'=>'required|min:8|max:12'
        ]);
        $student = new OJTInformation();
        $user =new User();
        $studentE =new Student();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $student->studentNum =  $request->studentNum;
        $studentE->studentNum =$request->studentNum;
        $studentE->course = $request->course;
        $studentE->year_and_section =$request->year_and_section;
        $studentE->school_year_start = $request->academic_year_start;
        $studentE->school_year_end   = $request->academic_year_end;
        $studentE->adviser_name =$request->adviser_name;

        $res = $user->save();
        $studentE->user_id = $user->id;
        $student->save();
        $studentE->save();

        if($res){
            AuditLogger::log(
                'Student Account',
                'create',
                'Registered new student: ' . $user->full_name,
                $user->id
            );
            return back()->with('success','You have registered successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }

    public function loginUser(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        $user = User::where('email','=',$request->email)->first();

        if($user){
            if(Hash::check($request->password, $user->password)){
                $request->session()->put('loginId',$user->id);
                $request->session()->put('show_terms', true);
                Cache::put(
                    'active_session_id:' . $user->id,
                    $request->session()->getId(),
                    now()->addMinutes((int) config('session.lifetime', 120))
                );

                if ($user->role == 0) {
                    return redirect()->route('student_home');
                } 
                else if ($user->role == 2) {
                    return redirect()->route('professor_home');
                }
                else if($user->role == 1) {
                    return redirect('dashboard');
                }
                else {
                    return redirect('/login');
                }
            }
            else{
                return back()->with('fail','Password does not match.');
            }
        }
        else{
            return back()->with('fail','Email is not registered.');
        }
    }

    public function dashboard(){
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $roleCount = User::where('role', 0)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->count();
        $roleCountP = User::where('role', 2)->count();

        $data=array();
        if(Session::has('loginId')){
            $data=User::where('id','=', Session::get('loginId'))->first();
        }

        $userName = $data->full_name ?? '';
        $fileCount = UploadedFile::where('uploader_name', $userName)->count();
        $announcements = Announcements::where('announcer', $userName)->latest()->get();

        return view('ojtCoordinator.dashboard', compact('data','roleCount','roleCountP','fileCount', 'announcements'));
    }

    public function analytics()
    {
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
        }

        // Optimized: Use grouped query instead of repeated counts
        $studentStats = User::where('role', 0)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalStudents = array_sum($studentStats);
        $approvedStudents = $studentStats[1] ?? 0;
        $pendingStudents = $studentStats[0] ?? 0;
        $deniedStudents = $studentStats[2] ?? 0;
        $inClassStudents = $studentStats[3] ?? 0;

        $studentStatusAnalytics = [
            [
                'label' => 'Approved students',
                'count' => $approvedStudents,
                'percentage' => $totalStudents > 0 ? round(($approvedStudents / $totalStudents) * 100) : 0,
                'class' => 'green',
            ],
            [
                'label' => 'Pending students',
                'count' => $pendingStudents,
                'percentage' => $totalStudents > 0 ? round(($pendingStudents / $totalStudents) * 100) : 0,
                'class' => 'amber',
            ],
            [
                'label' => 'Denied students',
                'count' => $deniedStudents,
                'percentage' => $totalStudents > 0 ? round(($deniedStudents / $totalStudents) * 100) : 0,
                'class' => 'red',
            ],
            [
                'label' => 'Joined rooms',
                'count' => $inClassStudents,
                'percentage' => $totalStudents > 0 ? round(($inClassStudents / $totalStudents) * 100) : 0,
                'class' => 'blue',
            ],
        ];

        // Optimized: Single grouped query for file stats
        $fileStats = FileRequirement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalRequirements = array_sum($fileStats);
        $approvedRequirements = $fileStats[1] ?? 0;
        $pendingRequirements = $fileStats[0] ?? 0;
        $deniedRequirements = $fileStats[2] ?? 0;

        $fileStatusAnalytics = [
            [
                'label' => 'Approved files',
                'count' => $approvedRequirements,
                'percentage' => $totalRequirements > 0 ? round(($approvedRequirements / $totalRequirements) * 100) : 0,
                'class' => 'green',
            ],
            [
                'label' => 'Pending files',
                'count' => $pendingRequirements,
                'percentage' => $totalRequirements > 0 ? round(($pendingRequirements / $totalRequirements) * 100) : 0,
                'class' => 'amber',
            ],
            [
                'label' => 'Denied files',
                'count' => $deniedRequirements,
                'percentage' => $totalRequirements > 0 ? round(($deniedRequirements / $totalRequirements) * 100) : 0,
                'class' => 'red',
            ],
        ];

        $partnerCompanies = Company::count();
        $placedStudents = Student::whereHas('companies')->count();

        $courseAnalytics = Student::select('course', DB::raw('COUNT(*) as total'))
            ->groupBy('course')
            ->orderByDesc('total')
            ->get();

        $courseMax = max(1, (int) $courseAnalytics->max('total'));
        $courseAnalytics = $courseAnalytics->map(function ($course) use ($courseMax) {
            return [
                'label' => $course->course ?: 'Unassigned',
                'count' => (int) $course->total,
                'percentage' => round(((int) $course->total / $courseMax) * 100),
            ];
        })->values();

        $topCompanies = Company::withCount('students')
            ->orderByDesc('students_count')
            ->limit(5)
            ->get();

        $topCompanyMax = max(1, (int) $topCompanies->max('students_count'));
        $topCompanies = $topCompanies->map(function ($company) use ($topCompanyMax) {
            return [
                'label' => $company->company_name,
                'count' => (int) $company->students_count,
                'percentage' => round(((int) $company->students_count / $topCompanyMax) * 100),
            ];
        })->values();

        $monthlyActivity = collect(range(5, 0))->map(function ($offset) {
            $month = Carbon::now()->subMonths($offset);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'label' => $month->format('M Y'),
                'files' => FileRequirement::whereBetween('created_at', [$start, $end])->count(),
                'students' => Student::whereBetween('created_at', [$start, $end])->count(),
            ];
        })->values();

        $maxMonthlyFiles = max(1, (int) $monthlyActivity->max('files'));
        $maxMonthlyStudents = max(1, (int) $monthlyActivity->max('students'));
        $monthlyActivity = $monthlyActivity->map(function ($item) use ($maxMonthlyFiles, $maxMonthlyStudents) {
            return [
                'label' => $item['label'],
                'files' => $item['files'],
                'students' => $item['students'],
                'file_percentage' => round(($item['files'] / $maxMonthlyFiles) * 100),
                'student_percentage' => round(($item['students'] / $maxMonthlyStudents) * 100),
            ];
        })->values();

        $analyticsInsights = $this->buildCoordinatorAnalyticsInsights(
            $studentStatusAnalytics,
            $fileStatusAnalytics,
            $courseAnalytics,
            $topCompanies,
            $totalStudents,
            $partnerCompanies,
            $placedStudents
        );

        return view('ojtCoordinator.analytics', compact(
            'data',
            'totalStudents',
            'approvedStudents',
            'pendingStudents',
            'deniedStudents',
            'inClassStudents',
            'studentStatusAnalytics',
            'totalRequirements',
            'approvedRequirements',
            'pendingRequirements',
            'deniedRequirements',
            'fileStatusAnalytics',
            'partnerCompanies',
            'placedStudents',
            'courseAnalytics',
            'topCompanies',
            'monthlyActivity',
            'analyticsInsights'
        ));
    }

    public function coordinatorAnalyticsData(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $cacheKey = 'coord_analytics_' . md5($start . '|' . $end);

        return response()->json(Cache::remember($cacheKey, 60, function () use ($start, $end) {
            $filesQuery = FileRequirement::query();
        $studentsQuery = Student::query();

        if ($start) {
            $filesQuery->where('created_at', '>=', $start);
            $studentsQuery->where('created_at', '>=', $start);
        }
        if ($end) {
            $filesQuery->where('created_at', '<=', $end);
            $studentsQuery->where('created_at', '<=', $end);
        }

        // Group by YYYY-MM
        $filesData = $filesQuery
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $studentsData = $studentsQuery
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        // Generate all months in range for complete timeline
        $allMonths = [];
        $startDate = $start ? Carbon::createFromFormat('Y-m-d', $start) : Carbon::now()->subMonths(5);
        $endDate = $end ? Carbon::createFromFormat('Y-m-d', $end) : Carbon::now();

        for ($date = $startDate->copy()->startOfMonth(); $date <= $endDate; $date->addMonth()) {
            $monthKey = $date->format('Y-m');
            $allMonths[$monthKey] = [
                'label' => $date->format('M Y'),
                'files' => (int) ($filesData->get($monthKey) ?? 0),
                'students' => (int) ($studentsData->get($monthKey) ?? 0),
            ];
        }

        // Build response with labels and datasets
        $labels = [];
        $filesArray = [];
        $studentsArray = [];

        foreach ($allMonths as $month) {
            $labels[] = $month['label'];
            $filesArray[] = $month['files'];
            $studentsArray[] = $month['students'];
        }

        return [
            'labels' => $labels,
            'files' => $filesArray,
            'students' => $studentsArray,
        ];
        }));
    }

    public function coordinatorAnalyticsDrilldown(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        $type = $request->query('type', 'files');
        $status = $request->query('status');
        $q = trim((string) $request->query('q', ''));
        $page = $request->query('page', 1);
        $perPage = 20;

        if (!$year || !$month) {
            return response()->json(['error' => 'Year and month required'], 400);
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        if ($type === 'files') {
            $items = FileRequirement::whereBetween('created_at', [$start, $end])
                ->select('id', 'file_name', 'status', 'created_at', 'adviser')
                ->when($status !== null && $status !== '', function ($query) use ($status) {
                    $query->where('status', (int) $status);
                })
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('file_name', 'like', '%' . $q . '%')
                            ->orWhere('adviser', 'like', '%' . $q . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $items = Student::whereBetween('created_at', [$start, $end])
                ->select('id', 'first_name', 'last_name', 'course', 'created_at')
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%')
                            ->orWhere('course', 'like', '%' . $q . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }

    public function coordinatorAnalyticsExportCsv(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $filename = 'coordinator-analytics-' . now()->format('Ymd-His') . '.csv';

        $studentStats = User::where('role', 0)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildCoordinatorMonthlySeries($start, $end);

        return response()->streamDownload(function () use ($studentStats, $fileStats, $months) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Section', 'Label', 'Count']);
            fputcsv($handle, ['Students', 'Approved', $studentStats[1] ?? 0]);
            fputcsv($handle, ['Students', 'Pending', $studentStats[0] ?? 0]);
            fputcsv($handle, ['Students', 'Denied', $studentStats[2] ?? 0]);
            fputcsv($handle, ['Students', 'Joined rooms', $studentStats[3] ?? 0]);
            fputcsv($handle, ['Files', 'Approved', $fileStats[1] ?? 0]);
            fputcsv($handle, ['Files', 'Pending', $fileStats[0] ?? 0]);
            fputcsv($handle, ['Files', 'Denied', $fileStats[2] ?? 0]);
            foreach ($months as $month) {
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Files', $month['files']]);
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Students', $month['students']]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function coordinatorAnalyticsExportPdf(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $studentStats = User::where('role', 0)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildCoordinatorMonthlySeries($start, $end);

        $html = view('reports.analytics_export', [
            'title' => 'Coordinator Analytics Report',
            'subtitle' => 'OJT Coordinator overview',
            'summaryRows' => [
                ['label' => 'Approved students', 'value' => $studentStats[1] ?? 0],
                ['label' => 'Pending students', 'value' => $studentStats[0] ?? 0],
                ['label' => 'Denied students', 'value' => $studentStats[2] ?? 0],
                ['label' => 'Joined rooms', 'value' => $studentStats[3] ?? 0],
                ['label' => 'Approved files', 'value' => $fileStats[1] ?? 0],
                ['label' => 'Pending files', 'value' => $fileStats[0] ?? 0],
                ['label' => 'Denied files', 'value' => $fileStats[2] ?? 0],
            ],
            'monthlyRows' => $months,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'coordinator-analytics-' . now()->format('Ymd-His') . '.pdf', ['Content-Type' => 'application/pdf']);
    }

    public function professorAnalyticsExportCsv(Request $request)
    {
        $data = Session::has('loginId') ? User::where('id', Session::get('loginId'))->first() : null;
        if (!$data) {
            return redirect('/login');
        }

        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();
        $filterClass = $request->query('class_id');
        if ($filterClass) {
            $classIds = array_intersect($classIds, [(int) $filterClass]);
        }

        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfMonth() : Carbon::now()->subMonths(5)->startOfMonth();
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfMonth() : Carbon::now()->endOfMonth();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildProfessorMonthlySeries($classIds, $start, $end);

        $filename = 'professor-analytics-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () use ($requestStats, $fileStats, $months) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Section', 'Label', 'Count']);
            fputcsv($handle, ['Requests', 'Sent', $requestStats['sent'] ?? 0]);
            fputcsv($handle, ['Requests', 'Opened', $requestStats['opened'] ?? 0]);
            fputcsv($handle, ['Requests', 'Submitted', $requestStats['submitted'] ?? 0]);
            fputcsv($handle, ['Requests', 'Expired', $requestStats['expired'] ?? 0]);
            fputcsv($handle, ['Requests', 'Cancelled', $requestStats['cancelled'] ?? 0]);
            fputcsv($handle, ['Files', 'Approved', $fileStats[1] ?? 0]);
            fputcsv($handle, ['Files', 'Pending', $fileStats[0] ?? 0]);
            fputcsv($handle, ['Files', 'Denied', $fileStats[2] ?? 0]);
            foreach ($months as $month) {
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Sent', $month['sent']]);
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Submitted', $month['submitted']]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function professorAnalyticsExportPdf(Request $request)
    {
        $data = Session::has('loginId') ? User::where('id', Session::get('loginId'))->first() : null;
        if (!$data) {
            return redirect('/login');
        }

        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();
        $filterClass = $request->query('class_id');
        if ($filterClass) {
            $classIds = array_intersect($classIds, [(int) $filterClass]);
        }

        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfMonth() : Carbon::now()->subMonths(5)->startOfMonth();
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfMonth() : Carbon::now()->endOfMonth();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildProfessorMonthlySeries($classIds, $start, $end);

        $html = view('reports.analytics_export', [
            'title' => 'Professor Analytics Report',
            'subtitle' => $data->full_name,
            'summaryRows' => [
                ['label' => 'Sent requests', 'value' => $requestStats['sent'] ?? 0],
                ['label' => 'Opened requests', 'value' => $requestStats['opened'] ?? 0],
                ['label' => 'Submitted requests', 'value' => $requestStats['submitted'] ?? 0],
                ['label' => 'Expired requests', 'value' => $requestStats['expired'] ?? 0],
                ['label' => 'Cancelled requests', 'value' => $requestStats['cancelled'] ?? 0],
                ['label' => 'Approved files', 'value' => $fileStats[1] ?? 0],
                ['label' => 'Pending files', 'value' => $fileStats[0] ?? 0],
                ['label' => 'Denied files', 'value' => $fileStats[2] ?? 0],
            ],
            'monthlyRows' => $months,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'professor-analytics-' . now()->format('Ymd-His') . '.pdf', ['Content-Type' => 'application/pdf']);
    }

    protected function buildCoordinatorMonthlySeries(?string $start, ?string $end): array
    {
        $startDate = $start ? Carbon::createFromFormat('Y-m-d', $start) : Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = $end ? Carbon::createFromFormat('Y-m-d', $end) : Carbon::now()->endOfMonth();

        $fileTotals = FileRequirement::query()
            ->when($start, fn ($query) => $query->where('created_at', '>=', $start))
            ->when($end, fn ($query) => $query->where('created_at', '<=', $end))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $studentTotals = Student::query()
            ->when($start, fn ($query) => $query->where('created_at', '>=', $start))
            ->when($end, fn ($query) => $query->where('created_at', '<=', $end))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $months = [];
        for ($date = $startDate->copy()->startOfMonth(); $date <= $endDate; $date->addMonth()) {
            $key = $date->format('Y-m');
            $months[] = [
                'label' => $date->format('M Y'),
                'files' => (int) ($fileTotals[$key] ?? 0),
                'students' => (int) ($studentTotals[$key] ?? 0),
            ];
        }

        return $months;
    }

    protected function buildProfessorMonthlySeries(array $classIds, Carbon $start, Carbon $end): array
    {
        $sentTotals = OjtEvaluationRequest::query()
            ->whereIn('class_id', $classIds)
            ->whereNotNull('emailed_at')
            ->whereBetween('emailed_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(emailed_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(emailed_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $submittedTotals = OjtEvaluationRequest::query()
            ->whereIn('class_id', $classIds)
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(submitted_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(submitted_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $months = [];
        for ($date = $start->copy()->startOfMonth(); $date <= $end->copy()->endOfMonth(); $date->addMonth()) {
            $key = $date->format('Y-m');
            $months[] = [
                'label' => $date->format('M Y'),
                'sent' => (int) ($sentTotals[$key] ?? 0),
                'submitted' => (int) ($submittedTotals[$key] ?? 0),
            ];
        }

        return $months;
    }

    public function logout(){
        if(Session::has('loginId')){
            $id = Session::get('loginId');
            Session::pull('loginId');
            Session::forget('termsAccepted');
            Cache::forget('active_session_id:' . $id);
            return redirect('login');
        }
    }

    public function professorTab()
    {
        $user = [];
        if (Session::has('loginId')) {
            $user = User::where('id', Session::get('loginId'))->first();
        }
        $course= Courses::all();
        $data = Professor::with('subjects')->get();
        $usersP = User::whereIn('email', $data->pluck('email'))->get();

        // Transform the subjects data
        $subjectData = $data->flatMap(function ($professor) {
            return $professor->subjects->map(function ($subject) {
                return [
                    'subject_code' => $subject->code,
                    'subject_description' => $subject->description,
                ];
            });
        })->toArray();

        return view('ojtCoordinator.professorTab', compact('data', 'user', 'subjectData','usersP','course'));
    }

    public function professorCreate(Request $request){
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'subject_code' => 'required|string|max:255',
            'subject_description' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = new User();
        $professor = new Professor();
        $subject = Subject::firstOrCreate([
            'subject_code' => $request->subject_code,
            'subject_description' => $request->subject_description,
        ]);

        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $user->password = Hash::make($request->password);
        $user->role = 2;

        $professor->email = $request->email;
        $professor->full_name = $user->full_name;

        $res = $user->save();

        if ($res) {
            $professor->user_id = $user->id;
            $professor->save();
        }

        if($res){
            $professor->subjects()->syncWithoutDetaching([$subject->id]);
            AuditLogger::log(
                'Professor',
                'create',
                'Created professor account: ' . $professor->full_name . ' with subject: ' . $subject->subject_code,
                $user->id
            );
            return back()->with('success','You have registered successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }

    public function student_home()
    {
        if (Session::has('loginId')) {
            $user = User::where('id', Session::get('loginId'))->first();
            if (Schema::hasColumn('uploaded_files', 'class_id')) {
                $fileCount = UploadedFile::where(function ($query) {
                        $query->whereNull('class_id')
                              ->orWhere('class_id', 0);
                    })
                    ->count();
            } else {
                $fileCount = UploadedFile::count();
            }

            // TERMS MODAL LOGIC
            $showTerms = false;
            $lastAccepted = Session::get('termsAcceptedTime'); // timestamp of last acceptance

            if (!$lastAccepted || now()->diffInHours($lastAccepted) >= 24) { // 24 hours = 1 day
                $showTerms = true;
            }
            return view('students.student_home', compact('user', 'fileCount', 'showTerms'));
        }
        return redirect()->route('login');
    }

    public function professor_home(){
        $data = [];
        $userName = '';
        $loginId = Session::get('loginId');
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $class = [];
        if ($loginId) {
            $data = User::where('id', '=', $loginId)->first();
            $userName = $data->full_name;
            // Get all rooms (classes) where this professor is adviser
            $class = Classes::where('adviser_name', $userName)->get();
            // For each room, preload students
            foreach ($class as $room) {
                $room->students = User::where('status', 1)
                    ->whereHas('studentInfo', function ($query) use ($room, $data) {
                        $query->where('class_id', $room->id);
                        if (empty($room->school_year_start) || empty($room->school_year_end)) {
                            $query->orWhere(function ($legacy) use ($room, $data) {
                                $legacy->whereNull('class_id')
                                    ->where('course', $room->course)
                                    ->where('adviser_name', $data->full_name);
                            });
                        }
                    })
                    ->get();
            }
        }
        $roleCount = User::where('role', 0)
            ->where(function ($query) use ($userName, $loginId) {
                $query->whereHas('studentInfo', function ($studentQuery) use ($userName) {
                    $studentQuery->where('adviser_name', $userName);
                });
                if (!empty($loginId)) {
                    $query->orWhere('id', $loginId);
                }
            })
            ->where('created_at', '>=', $sixMonthsAgo)
            ->count();
        $fileCount = UploadedFile::all()->count();
        $currentYear = now()->year;
        $companies = Company::all();
        $stu = Student::all();
        $companies = $companies->filter(function ($company) use ($currentYear) {
            list($startYear, $endYear) = explode('-', $company->school_year);
            $startYear = (int) $startYear;
            $endYear = (int) $endYear;
            return $currentYear >= $startYear && $currentYear <= $startYear + 3;
        });
        $companyNames = $companies->pluck('company_name')->toArray();
        return view('professor.home', compact('companies','data', 'roleCount', 'fileCount', 'class'));
    }

    public function professorAnalytics()
    {
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        $students = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds, $data) {
                $query->whereIn('class_id', $classIds)
                      ->orWhere(function ($legacy) use ($data) {
                          $legacy->whereNull('class_id')
                              ->where('adviser_name', $data->full_name);
                      });
            })
            ->get();

        $totalStudents = $students->count();
        $approvedStudents = $students->where('status', 1)->count();
        $pendingApprovals = $students->where('status', 3)->count();
        $deniedStudents = $students->where('status', 2)->count();
        $inactiveStudents = $students->where('status', 0)->count();

        $classAnalytics = $classrooms->map(function ($room) use ($students) {
            $roomStudents = $students->filter(function ($student) use ($room) {
                return (string) optional($student->studentInfo)->class_id === (string) $room->id;
            });

            $requestTotal = OjtEvaluationRequest::where('class_id', $room->id)->count();
            $submitted = OjtEvaluationRequest::where('class_id', $room->id)->where('status', 'submitted')->count();

            return [
                'label' => $room->room,
                'total_students' => $roomStudents->count(),
                'submitted' => $submitted,
                'requests' => $requestTotal,
                'completion' => $requestTotal > 0 ? round(($submitted / $requestTotal) * 100) : 0,
            ];
        })->values();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $requestTotal = array_sum($requestStats);
        $sentRequests = $requestStats['sent'] ?? 0;
        $openedRequests = $requestStats['opened'] ?? 0;
        $submittedRequests = $requestStats['submitted'] ?? 0;
        $expiredRequests = $requestStats['expired'] ?? 0;
        $cancelledRequests = $requestStats['cancelled'] ?? 0;

        $requestAnalytics = [
            ['label' => 'Sent', 'count' => $sentRequests, 'class' => 'blue'],
            ['label' => 'Opened', 'count' => $openedRequests, 'class' => 'amber'],
            ['label' => 'Submitted', 'count' => $submittedRequests, 'class' => 'green'],
            ['label' => 'Expired', 'count' => $expiredRequests, 'class' => 'red'],
            ['label' => 'Cancelled', 'count' => $cancelledRequests, 'class' => 'purple'],
        ];

        $templateCount = FileCategory::when($professor, function ($query) use ($professor) {
            $query->where('professor_id', $professor->id);
        })->count();

        $profFileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $filePending = $profFileStats[0] ?? 0;
        $fileApproved = $profFileStats[1] ?? 0;
        $fileDenied = $profFileStats[2] ?? 0;

        $monthlyActivity = collect(range(5, 0))->map(function ($offset) use ($classIds) {
            $month = Carbon::now()->subMonths($offset);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'label' => $month->format('M Y'),
                'submitted' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('submitted_at', [$start, $end])->count(),
                'sent' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('emailed_at', [$start, $end])->count(),
            ];
        })->values();

        $maxSubmitted = max(1, (int) $monthlyActivity->max('submitted'));
        $maxSent = max(1, (int) $monthlyActivity->max('sent'));

        $monthlyActivity = $monthlyActivity->map(function ($item) use ($maxSubmitted, $maxSent) {
            return [
                'label' => $item['label'],
                'submitted' => $item['submitted'],
                'sent' => $item['sent'],
                'submitted_percentage' => round(($item['submitted'] / $maxSubmitted) * 100),
                'sent_percentage' => round(($item['sent'] / $maxSent) * 100),
            ];
        })->values();

        $analyticsInsights = $this->buildProfessorAnalyticsInsights(
            $classAnalytics,
            $requestAnalytics,
            $totalStudents,
            $approvedStudents,
            $pendingApprovals,
            $submittedRequests,
            $requestTotal,
            $filePending,
            $fileApproved,
            $fileDenied,
            $monthlyActivity
        );

        return view('professor.analytics', compact(
            'data',
            'classrooms',
            'classAnalytics',
            'requestAnalytics',
            'totalStudents',
            'approvedStudents',
            'pendingApprovals',
            'deniedStudents',
            'inactiveStudents',
            'requestTotal',
            'submittedRequests',
            'templateCount',
            'filePending',
            'fileApproved',
            'fileDenied',
            'monthlyActivity',
            'analyticsInsights'
        ));
    }

    public function professorAnalyticsPrint()
    {
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        $students = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds, $data) {
                $query->whereIn('class_id', $classIds)
                    ->orWhere(function ($legacy) use ($data) {
                        $legacy->whereNull('class_id')
                            ->where('adviser_name', $data->full_name);
                    });
            })
            ->get();

        $totalStudents = $students->count();
        $approvedStudents = $students->where('status', 1)->count();
        $pendingApprovals = $students->where('status', 3)->count();
        $deniedStudents = $students->where('status', 2)->count();
        $inactiveStudents = $students->where('status', 0)->count();

        $classAnalytics = $classrooms->map(function ($room) use ($students) {
            $roomStudents = $students->filter(function ($student) use ($room) {
                return (string) optional($student->studentInfo)->class_id === (string) $room->id;
            });

            $requestTotal = OjtEvaluationRequest::where('class_id', $room->id)->count();
            $submitted = OjtEvaluationRequest::where('class_id', $room->id)->where('status', 'submitted')->count();

            return [
                'label' => $room->room,
                'total_students' => $roomStudents->count(),
                'submitted' => $submitted,
                'requests' => $requestTotal,
                'completion' => $requestTotal > 0 ? round(($submitted / $requestTotal) * 100) : 0,
            ];
        })->values();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $requestTotal = array_sum($requestStats);
        $sentRequests = $requestStats['sent'] ?? 0;
        $openedRequests = $requestStats['opened'] ?? 0;
        $submittedRequests = $requestStats['submitted'] ?? 0;
        $expiredRequests = $requestStats['expired'] ?? 0;
        $cancelledRequests = $requestStats['cancelled'] ?? 0;

        $requestAnalytics = [
            ['label' => 'Sent', 'count' => $sentRequests, 'class' => 'blue'],
            ['label' => 'Opened', 'count' => $openedRequests, 'class' => 'amber'],
            ['label' => 'Submitted', 'count' => $submittedRequests, 'class' => 'green'],
            ['label' => 'Expired', 'count' => $expiredRequests, 'class' => 'red'],
            ['label' => 'Cancelled', 'count' => $cancelledRequests, 'class' => 'purple'],
        ];

        $templateCount = FileCategory::when($professor, function ($query) use ($professor) {
            $query->where('professor_id', $professor->id);
        })->count();

        $profFileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $filePending = $profFileStats[0] ?? 0;
        $fileApproved = $profFileStats[1] ?? 0;
        $fileDenied = $profFileStats[2] ?? 0;

        $monthlyActivity = collect(range(5, 0))->map(function ($offset) use ($classIds) {
            $month = Carbon::now()->subMonths($offset);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'label' => $month->format('M Y'),
                'submitted' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('submitted_at', [$start, $end])->count(),
                'sent' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('emailed_at', [$start, $end])->count(),
            ];
        })->values();

        $maxSubmitted = max(1, (int) $monthlyActivity->max('submitted'));
        $maxSent = max(1, (int) $monthlyActivity->max('sent'));

        $monthlyActivity = $monthlyActivity->map(function ($item) use ($maxSubmitted, $maxSent) {
            return [
                'label' => $item['label'],
                'submitted' => $item['submitted'],
                'sent' => $item['sent'],
                'submitted_percentage' => round(($item['submitted'] / $maxSubmitted) * 100),
                'sent_percentage' => round(($item['sent'] / $maxSent) * 100),
            ];
        })->values();

        $analyticsInsights = $this->buildProfessorAnalyticsInsights(
            $classAnalytics,
            $requestAnalytics,
            $totalStudents,
            $approvedStudents,
            $pendingApprovals,
            $submittedRequests,
            $requestTotal,
            $filePending,
            $fileApproved,
            $fileDenied,
            $monthlyActivity
        );

        return view('professor.analytics_print', compact(
            'data',
            'classrooms',
            'classAnalytics',
            'requestAnalytics',
            'totalStudents',
            'approvedStudents',
            'pendingApprovals',
            'deniedStudents',
            'inactiveStudents',
            'requestTotal',
            'submittedRequests',
            'templateCount',
            'filePending',
            'fileApproved',
            'fileDenied',
            'monthlyActivity',
            'analyticsInsights'
        ));
    }

    // JSON endpoint for AJAX-driven charting and filters
    public function professorAnalyticsData(Request $request)
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        // optional class filter
        $filterClass = $request->query('class_id');
        if ($filterClass) {
            $classIds = array_intersect($classIds, [(int)$filterClass]);
        }

        // date range: default last 6 months
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfMonth() : Carbon::now()->endOfMonth();
        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfMonth() : Carbon::now()->subMonths(5)->startOfMonth();

        // Build cache key from parameters
        $cacheKey = 'prof_analytics_' . $data->id . '_' . md5(implode(',', $classIds) . $start->format('Y-m-d') . $end->format('Y-m-d'));

        $chartData = Cache::remember($cacheKey, 60, function () use ($classIds, $start, $end) {
            // aggregated counts grouped by year-month for emailed_at (sent) and submitted_at (submitted)
            $sentRows = OjtEvaluationRequest::select(
                DB::raw("YEAR(emailed_at) as y"),
                DB::raw("MONTH(emailed_at) as m"),
                DB::raw('COUNT(*) as total')
            )->whereIn('class_id', $classIds)
             ->whereNotNull('emailed_at')
             ->whereBetween('emailed_at', [$start, $end])
             ->groupBy('y','m')
             ->get()
             ->keyBy(function($r){ return $r->y.'-'.str_pad($r->m,2,'0',STR_PAD_LEFT); });

            $submittedRows = OjtEvaluationRequest::select(
                DB::raw("YEAR(submitted_at) as y"),
                DB::raw("MONTH(submitted_at) as m"),
                DB::raw('COUNT(*) as total')
            )->whereIn('class_id', $classIds)
             ->whereNotNull('submitted_at')
             ->whereBetween('submitted_at', [$start, $end])
             ->groupBy('y','m')
             ->get()
             ->keyBy(function($r){ return $r->y.'-'.str_pad($r->m,2,'0',STR_PAD_LEFT); });

            $period = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $period[] = $cursor->format('Y-m');
                $cursor->addMonth();
            }

            $labels = [];
            $sent = [];
            $submitted = [];

            foreach ($period as $p) {
                [$y,$m] = explode('-', $p);
                $labels[] = Carbon::createFromDate((int)$y,(int)$m,1)->format('M Y');
                $sent[] = isset($sentRows[$p]) ? (int)$sentRows[$p]->total : 0;
                $submitted[] = isset($submittedRows[$p]) ? (int)$submittedRows[$p]->total : 0;
            }

            return ["labels" => $labels, "sent" => $sent, "submitted" => $submitted];
        });

        return response()->json($chartData);
    }

    public function professorAnalyticsDrilldown(Request $request)
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $year = $request->query('year');
        $month = $request->query('month');
        $class_id = $request->query('class_id');
        $status = $request->query('status');
        $q = trim((string) $request->query('q', ''));
        $page = $request->query('page', 1);
        $perPage = 20;

        if (!$year || !$month) {
            return response()->json(['error' => 'Year and month required'], 400);
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        if ($class_id) {
            $classIds = array_intersect($classIds, [(int)$class_id]);
        }

        if (empty($classIds)) {
            return response()->json(['data' => [], 'total' => 0, 'per_page' => $perPage, 'current_page' => $page]);
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $items = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->whereBetween('submitted_at', [$start, $end])
            ->select('id', 'student_id', 'company', 'status', 'submitted_at', 'created_at')
            ->with('student:id,first_name,last_name')
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->whereHas('student', function ($studentQuery) use ($q) {
                        $studentQuery->where('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%');
                    })->orWhere('company', 'like', '%' . $q . '%');
                });
            })
            ->orderByDesc('submitted_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }

    public function pending(){
        $data=array();
        if(Session::has('loginId')){
            $data=User::where('id','=', Session::get('loginId'))->first();
        }
        return view('students.pending', compact('data'));
    }

    protected function buildCoordinatorAnalyticsInsights($studentStatusAnalytics, $fileStatusAnalytics, $courseAnalytics, $topCompanies, int $totalStudents, int $partnerCompanies, int $placedStudents)
    {
        $studentStats = collect($studentStatusAnalytics);
        $fileStats = collect($fileStatusAnalytics);
        $courseStats = collect($courseAnalytics);
        $companyStats = collect($topCompanies);

        $approved = (int) ($studentStats->firstWhere('label', 'Approved students')['count'] ?? 0);
        $pending = (int) ($studentStats->firstWhere('label', 'Pending students')['count'] ?? 0);
        $pendingFiles = (int) ($fileStats->firstWhere('label', 'Pending files')['count'] ?? 0);
        $topCourse = $courseStats->sortByDesc('count')->first();
        $topCompany = $companyStats->sortByDesc('count')->first();

        $highlights = [
            'Student coverage shows ' . $totalStudents . ' records with ' . $approved . ' approved students.',
            'Partner coverage includes ' . $partnerCompanies . ' companies and ' . $placedStudents . ' placed students.',
        ];

        if (!empty($topCourse['label'])) {
            $highlights[] = 'Largest course group: ' . $topCourse['label'] . ' (' . $topCourse['count'] . ').';
        }

        if (!empty($topCompany['label'])) {
            $highlights[] = 'Top partner company: ' . $topCompany['label'] . ' (' . $topCompany['count'] . ' placements).';
        }

        $watchouts = [];
        if ($pending > 0) {
            $watchouts[] = $pending . ' student account' . ($pending === 1 ? '' : 's') . ' still need approval or placement review.';
        }
        if ($pendingFiles > 0) {
            $watchouts[] = $pendingFiles . ' requirement file' . ($pendingFiles === 1 ? '' : 's') . ' are still pending review.';
        }

        $actions = [
            'Review pending student approvals before the next intake cycle.',
            'Check placement balance across top partner companies.',
            'Use the analytics chart filters to find weak course coverage.'
        ];

        return app(ReportAiInsightService::class)->summarize('coordinator_analytics', [
            'total_records' => $totalStudents,
            'total_companies' => $partnerCompanies,
            'records_with_ojt' => $placedStudents,
            'course' => $topCourse['label'] ?? null,
        ], $highlights, $watchouts, $actions);
    }

    protected function buildProfessorAnalyticsInsights($classAnalytics, $requestAnalytics, int $totalStudents, int $approvedStudents, int $pendingApprovals, int $submittedRequests, int $requestTotal, int $filePending, int $fileApproved, int $fileDenied, $monthlyActivity)
    {
        $classStats = collect($classAnalytics);
        $requestStats = collect($requestAnalytics);
        $activityStats = collect($monthlyActivity);

        $topClass = $classStats->sortByDesc('completion')->first();
        $sent = (int) ($requestStats->firstWhere('label', 'Sent')['count'] ?? 0);
        $opened = (int) ($requestStats->firstWhere('label', 'Opened')['count'] ?? 0);
        $pendingFiles = (int) $filePending;
        $latestMonth = $activityStats->last();

        $highlights = [
            'Advisee coverage shows ' . $totalStudents . ' students, ' . $approvedStudents . ' approved and ' . $pendingApprovals . ' pending.',
            'Evaluation flow shows ' . $submittedRequests . ' submitted evaluations out of ' . $requestTotal . ' requests.',
        ];

        if (!empty($topClass['label'])) {
            $highlights[] = 'Best completion class: ' . $topClass['label'] . ' at ' . $topClass['completion'] . '%.';
        }

        if ($latestMonth) {
            $highlights[] = 'Latest month activity: ' . $latestMonth['sent'] . ' sent and ' . $latestMonth['submitted'] . ' submitted.';
        }

        $watchouts = [];
        if ($pendingApprovals > 0) {
            $watchouts[] = $pendingApprovals . ' student account' . ($pendingApprovals === 1 ? '' : 's') . ' still need approval.';
        }
        if ($pendingFiles > 0) {
            $watchouts[] = $pendingFiles . ' file requirement' . ($pendingFiles === 1 ? '' : 's') . ' are still pending.';
        }
        if ($sent > $opened && $opened > 0) {
            $watchouts[] = 'Some evaluation links were sent but not opened yet, so follow-up may be needed.';
        }

        $actions = [
            'Follow up with classes that have low evaluation completion.',
            'Review pending files alongside student approval status.',
            'Use monthly activity to time reminders before deadlines.'
        ];

        return app(ReportAiInsightService::class)->summarize('professor_analytics', [
            'total_records' => $totalStudents,
            'records_with_ojt' => $submittedRequests,
            'missing_ojt' => max(0, $requestTotal - $submittedRequests),
            'course' => 'Professor advisories',
        ], $highlights, $watchouts, $actions);
    }
}
