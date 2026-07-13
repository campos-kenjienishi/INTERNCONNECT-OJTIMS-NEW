<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Company;
use App\Models\Courses;
use App\Models\Student;
use App\Models\Professor;
use App\Mail\MOAReportEmail;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\OJTInformation;
use App\Mail\PrintContentsEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Helpers\AuditLogger;
use App\Services\ReportAiInsightService;

class ReportsController extends Controller
{
    private function normalizeCourseSelection($courseInput): array
    {
        if (is_array($courseInput)) {
            $values = $courseInput;
        } else {
            $values = preg_split('/[\r\n,]+/', (string) $courseInput);
        }

        return collect($values)
            ->map(fn ($course) => trim((string) $course))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function companyMatchesCourse(?Company $company, ?string $course): bool
    {
        $course = trim((string) $course);

        if ($course === '') {
            return true;
        }

        return in_array($course, $this->normalizeCourseSelection($company->course ?? null), true);
    }

    private function normalizeSchoolYearRange(?string $startYear, ?string $endYear): ?string
    {
        $startYear = trim((string) $startYear);
        $endYear = trim((string) $endYear);

        if ($startYear === '' || $endYear === '') {
            return null;
        }

        return $startYear . '-' . $endYear;
    }

    public function reports()
    {
        $user = [];

        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }
    
        
    
        $students = User::where('role', 0)
                    ->where('status', 1)
                    ->with('studentInfo')
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

        $schoolYears = $students
            ->map(function ($student) {
                $info = $student->studentInfo;
                if (!$info || empty($info->school_year_start) || empty($info->school_year_end)) {
                    return null;
                }

                return trim($info->school_year_start . '-' . $info->school_year_end);
            })
            ->filter()
            ->unique()
            ->sortByDesc(function ($schoolYear) {
                $parts = explode('-', str_replace(' ', '', $schoolYear));
                return (int) ($parts[0] ?? 0);
            })
            ->values();

        $reportInsights = $this->buildStudentReportInsights($studentData, null);
    
        return view('ojtCoordinator.reportsT', compact('studentData', 'user', 'subjectData','course', 'reportInsights', 'schoolYears'))
            ->with('selectedSchoolYear', null);

    }



    public function generateReport(Request $request)
    {
        $selectedSchoolYear = trim((string) $request->input('school_year'));
        $coursed = $request->input('course');
        $course = Courses::all();

        // Get the current user
        $user = [];
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        $allStudents = User::where('role', 0)
            ->where('status', 1)
            ->with('studentInfo')
            ->get();

        $schoolYears = $allStudents
            ->map(function ($student) {
                $info = $student->studentInfo;
                if (!$info || empty($info->school_year_start) || empty($info->school_year_end)) {
                    return null;
                }

                return trim($info->school_year_start . '-' . $info->school_year_end);
            })
            ->filter()
            ->unique()
            ->sortByDesc(function ($schoolYear) {
                $parts = explode('-', str_replace(' ', '', $schoolYear));
                return (int) ($parts[0] ?? 0);
            })
            ->values();

        [$schoolYearStart, $schoolYearEnd] = array_pad(explode('-', str_replace(' ', '', $selectedSchoolYear)), 2, null);
    
        // Query users based on criteria
        $students = User::where('role', 0)
                        ->where('status', 1)
                        ->whereHas('studentInfo', function ($query) use ($coursed, $schoolYearStart, $schoolYearEnd) {
                            $query->where('course', $coursed);
                            if (!empty($schoolYearStart) && !empty($schoolYearEnd)) {
                                $query->where('school_year_start', $schoolYearStart)
                                    ->where('school_year_end', $schoolYearEnd);
                            }
                        })
                        ->get();


                        $studentData = [];
        
                        // Initialize subject data array outside the loop
                        $subjectData = [];
                        
                    
                    
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

            $reportInsights = $this->buildStudentReportInsights($studentData, $coursed);
            
        AuditLogger::log(
            'Reports',
            'Generate',
            'Generated Student OJT Information report for ' . ($coursed ?: 'all courses') . ' (' . ($selectedSchoolYear ?: 'all school years') . ')',
            Session::get('loginId') ?? null
        );
    
        // Pass the course variable to the view
            return view('ojtCoordinator.reportsT', compact('studentData', 'user', 'subjectData','course', 'reportInsights', 'schoolYears', 'selectedSchoolYear'));
    }


    public function sendEmail(Request $request)
{
    
    $email = $request->input('email');
    $user = User::where('email', $email)->first(); // Retrieve the logged-in user
    $students = User::where('role', 0)
                    ->where('status', 1)
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

    // Send email with print contents
    Mail::to($email)->send(new PrintContentsEmail($studentData));

    AuditLogger::log(
        'Reports',
        'Send Email',
        'Sent Student OJT Information report to ' . $email,
        Session::get('loginId') ?? null
    );

    return back()->with(['message' => 'Email sent successfully']);
}


public function reportsExpired()
    {
        $user = [];
    
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        $course = Courses::all();

        $companies = Company::all();

        $reportInsights = $this->buildMoaReportInsights($companies, $course->first()->course ?? null);

        return view('ojtCoordinator.reportsExpired', compact('companies', 'user', 'course', 'reportInsights'));
    }


    public function generateMOAReport(Request $request)
{
    $validatedData = $request->validate([
        'school_year_start' => 'required',
        'school_year_end' => 'required',
        'course' => 'required',
    ]);

    $startYear = $validatedData['school_year_start'];
    $endYear = $validatedData['school_year_end'];
    $schoolYear = $this->normalizeSchoolYearRange($startYear, $endYear);

    $user = User::find(Session::get('loginId'));
    $course = Courses::all();

    $companyy = Company::whereRaw("REPLACE(COALESCE(school_year, ''), ' ', '') = ?", [$schoolYear])
        ->get();

    $companies = $companyy->filter(function ($company) use ($validatedData) {
        if ($this->companyMatchesCourse($company, $validatedData['course'])) {
            return true;
        }

        return $company->students()->where('course', $validatedData['course'])->exists();
    })->sortByDesc(function ($company) {
        return optional($company->created_at)->timestamp ?? $company->id;
    })->values();
    $companies = $this->annotateMoaFileStatus($companies);
    $companyNames = $companies->pluck('company_name')->toArray();

    // Retrieve students under the specified companies using where and get
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames);
    })
    ->where('course', $validatedData['course'])
    ->get();

    AuditLogger::log(
        'Reports',
        'Generate',
        'Generated MOA report for ' . $validatedData['course'] . ' (' . $schoolYear . ')',
        Session::get('loginId') ?? null
    );

    $reportInsights = $this->buildMoaReportInsights($companies, $validatedData['course']);

    return view('ojtCoordinator.reportsExpired', compact('companies', 'students', 'user','course', 'reportInsights'));
}

public function sendEmailExpired(Request $request)
{
    $email = $request->input('email');
    $course = $request->input('course');

    $companies = Company::all();

    if ($course) {
        $companies = $companies->filter(function ($company) use ($course) {
            return $this->companyMatchesCourse($company, $course)
                || $company->students()->where('course', $course)->exists();
        });
    }

    $companyNames = $companies->pluck('company_name')->toArray();

    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames);
    })
    ->when($course, function ($query) use ($course) {
        $query->where('course', $course);
    })
    ->get();

    $reportInsights = $this->buildMoaReportInsights($companies, $course);

    Mail::to($email)->send(new MOAReportEmail($companies, $students));

    AuditLogger::log(
        'Reports',
        'Send Email',
        'Sent MOA report to ' . $email . ($course ? ' for ' . $course : ''),
        Session::get('loginId') ?? null
    );

    return back()->with(['message' => 'Email sent successfully', 'reportInsights' => $reportInsights]);
}

public function askAi(Request $request)
{
    $validated = $request->validate([
        'question' => 'required|string|max:500',
        'report_type' => 'nullable|string|max:80',
        'metrics' => 'nullable|array',
        'insight' => 'nullable|array',
    ]);

    $answer = app(ReportAiInsightService::class)->answerQuestion(
        $validated['question'],
        $validated['report_type'] ?? 'report',
        $validated['metrics'] ?? [],
        $validated['insight'] ?? []
    );

    return response()->json($answer);
}

public function generateAiInsight(Request $request)
{
    $validated = $request->validate([
        'report_type' => 'nullable|string|max:80',
        'metrics' => 'nullable|array',
        'highlights' => 'nullable|array',
        'watchouts' => 'nullable|array',
        'actions' => 'nullable|array',
    ]);

    $insight = app(ReportAiInsightService::class)->summarize(
        $validated['report_type'] ?? 'report',
        $validated['metrics'] ?? [],
        $validated['highlights'] ?? [],
        $validated['watchouts'] ?? [],
        $validated['actions'] ?? [],
        true
    );

    return response()->json($insight);
}





public function reportsExpiredProf()
    {
        $user = [];
    
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        $courseAll = Courses::all();
    
        // Get the current year
        $currentYear = now()->year;
    
        // Retrieve the selected company or companies
        $companies = Company::all(); // Get all companies
    
        $stu = Student::all();
    
        // Filter companies based on the start year of "school_year"
    $companies = $companies->filter(function ($company) use ($currentYear) {
        // Extract the start year from the "school_year" format
        list($startYear, $endYear) = explode('-', $company->school_year);
    
            // Convert them to integers
            $startYear = (int) $startYear;
            $endYear = (int) $endYear;
    
          
            return ($currentYear - $startYear) ;
        })->sortByDesc(function ($company) {
            return optional($company->created_at)->timestamp ?? $company->id;
        })->values();
    
        $companyNames = $companies->pluck('company_name')->toArray();

        $reportInsights = $this->buildMoaReportInsights($companies, $courseAll->first()->course ?? null);
    
        // Retrieve students under the specified companies using where and get
        // $students = Student::whereHas('companies', function ($query) use ($companyNames, $course) {
        //     $query->whereIn('company_name', $companyNames)
        //         ->where('course', $course); // Add condition to filter by course
        // })->get();
    
        $companies = $this->annotateMoaFileStatus($companies);

        return view('professor.expiredMOAReports', compact('companies', 'user', 'stu','courseAll', 'reportInsights'));
    }


    public function generateMOAReportProf(Request $request)
{
    $validatedData = $request->validate([
        'school_year_start' => 'required',
        'school_year_end' => 'required',
        'course' => 'required',
    ]);

    $startYear = $validatedData['school_year_start'];
    $endYear = $validatedData['school_year_end'];

    $schoolYear = $this->normalizeSchoolYearRange($startYear, $endYear);
    $user = User::find(Session::get('loginId'));
    $courseAll = Courses::all();
    $currentYear = now()->year;

    // Retrieve the selected company or companies
    $companyy = Company::whereRaw("REPLACE(COALESCE(school_year, ''), ' ', '') = ?", [$schoolYear])
        ->get();

    $companies = $companyy->filter(function ($company) use ($currentYear, $validatedData) {
        // Extract the start year from the "school_year" format
        list($startYear, $endYear) = explode('-', $company->school_year);

        // Convert them to integers
        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        
        if ($this->companyMatchesCourse($company, $validatedData['course'])) {
            return true;
        }

        // Backward compatibility for older records that only had student links.
        return $company->students()->where('course', $validatedData['course'])->exists();
    })->sortByDesc(function ($company) {
        return optional($company->created_at)->timestamp ?? $company->id;
    })->values();
    $companies = $this->annotateMoaFileStatus($companies);
    $companyNames = $companies->pluck('company_name')->toArray();

    // Retrieve students under the specified companies using where and get
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames);
    })
    ->where('course', $validatedData['course'])
    ->get();

    $companies = $this->annotateMoaFileStatus($companies);

    $reportInsights = $this->buildMoaReportInsights($companies, $validatedData['course']);

    AuditLogger::log(
        'Reports',
        'Generate',
        'Generated professor MOA report for ' . $validatedData['course'] . ' (' . $schoolYear . ')',
        Session::get('loginId') ?? null
    );

    return view('professor.expiredMOAReports', compact('companies', 'students', 'user','courseAll', 'reportInsights'));
}

    protected function buildStudentReportInsights($studentData, ?string $course = null)
    {
        $records = collect($studentData);
        $recordsWithOjt = $records->filter(function ($row) {
            return !empty($row['ojt']);
        });

        $companyNames = $recordsWithOjt->pluck('ojt.company_name')->filter()->unique()->values();
        $missingOjt = $records->count() - $recordsWithOjt->count();

        $highlights = [
            'Loaded ' . $records->count() . ' student record' . ($records->count() === 1 ? '' : 's') . '.',
            'Found ' . $companyNames->count() . ' unique company placement' . ($companyNames->count() === 1 ? '' : 's') . '.',
        ];

        if ($companyNames->isNotEmpty()) {
            $highlights[] = 'Top company examples: ' . $companyNames->take(3)->implode(', ') . '.';
        }

        $watchouts = [];
        if ($missingOjt > 0) {
            $watchouts[] = $missingOjt . ' student record' . ($missingOjt === 1 ? '' : 's') . ' still need OJT details before export or print.';
        }

        $actions = [
            'Review student placements before generating the final report.',
            'Use the print preview once the OJT information is complete.',
        ];

        return app(ReportAiInsightService::class)->summarize('student_ojt_report', [
            'total_records' => $records->count(),
            'total_companies' => $companyNames->count(),
            'records_with_ojt' => $recordsWithOjt->count(),
            'missing_ojt' => $missingOjt,
            'course' => $course,
        ], $highlights, $watchouts, $actions);
    }

    protected function buildMoaReportInsights($companies, ?string $course = null)
    {
        $items = collect($companies);
        $active = 0;
        $expired = 0;

        foreach ($items as $company) {
            $parts = explode('-', (string) ($company->school_year ?? '0-0'));
            $startYear = (int) ($parts[0] ?? 0);
            $difference = now()->year - $startYear;

            if ($difference > 3) {
                $expired++;
            } else {
                $active++;
            }
        }

        $highlights = [
            'Loaded ' . $items->count() . ' partner company record' . ($items->count() === 1 ? '' : 's') . '.',
            'Active agreements: ' . $active . '.',
            'Expired agreements: ' . $expired . '.',
        ];

        $watchouts = [];
        if ($expired > 0) {
            $watchouts[] = $expired . ' agreement' . ($expired === 1 ? '' : 's') . ' are already outside the preferred validity window.';
        }

        $actions = [
            'Prioritize renewal follow-up for expired MOAs.',
            'Use the report to review active partner coverage by course.',
        ];

        return app(ReportAiInsightService::class)->summarize('moa_report', [
            'total_moa' => $items->count(),
            'active_moa' => $active,
            'expired_moa' => $expired,
            'course' => $course,
        ], $highlights, $watchouts, $actions);
    }

    private function annotateMoaFileStatus($companies)
    {
        return collect($companies)->map(function ($company) {
            $filePath = !empty($company->file) ? public_path('assets/' . $company->file) : null;
            $fileExists = $filePath && file_exists($filePath);
            $fileSize = $fileExists ? filesize($filePath) : 0;

            $company->moa_file_ready = $fileExists && $fileSize > 0;
            $company->moa_file_empty = !empty($company->file) && (!$fileExists || $fileSize === 0);

            return $company;
        })->values();
    }



}
