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

class ReportsController extends Controller
{
    public function reports()
    {
        $user = [];

        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }
    
        
    
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
    
        return view('ojtCoordinator.reportsT', compact('studentData', 'user', 'subjectData','course'));

    }



    public function generateReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $coursed = $request->input('course');
        $course = Courses::all();
        // Get the current user
        $user = [];
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }
    
        // Query users based on criteria
        $students = User::where('role', 0)
                        ->where('status', 1)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->where('course', $coursed)
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
                        
    
        // Pass the course variable to the view
        return view('ojtCoordinator.reportsT', compact('studentData', 'user', 'subjectData','course'));
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

    return back()->with(['message' => 'Email sent successfully']);
}


public function reportsExpired()
    {
        $user = [];
    
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        $course = Courses::all();
    
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
    
          
            return ($currentYear - $startYear) > 3;
        });
    
        $companyNames = $companies->pluck('company_name')->toArray();
    
        // Retrieve students under the specified companies using where and get
        $students = Student::whereHas('companies', function ($query) use ($companyNames) {
            $query->whereIn('company_name', $companyNames);
        })->get();
    
        return view('ojtCoordinator.reportsExpired', compact('companies', 'students', 'user', 'stu','course'));
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

    $schoolYear = $startYear . '-' . $endYear;
    $user = User::find(Session::get('loginId'));
    $course = Courses::all();
    $currentYear = now()->year;

    // Retrieve the selected company or companies
    $companyy = Company::where(function ($query) use ($startYear, $endYear) {
        $query->where('school_year', '>=', $startYear)
            ->where('school_year', '<=', $endYear);
    })->get();

    $companies = $companyy->filter(function ($company) use ($currentYear, $validatedData) {
        // Extract the start year from the "school_year" format
        list($startYear, $endYear) = explode('-', $company->school_year);

        // Convert them to integers
        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        // Check if the difference between current year and start year is greater than 3
        $isExpired = ($currentYear - $startYear) > 3;

        // Check if the company has students associated with the specified course
        $hasStudentsWithCourse = $company->students()->where('course', $validatedData['course'])->exists();

        return $isExpired && $hasStudentsWithCourse;
    });
    $companyNames = $companies->pluck('company_name')->toArray();

    // Retrieve students under the specified companies using where and get
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames);
    })
    ->where('course', $validatedData['course'])
    ->get();

    return view('ojtCoordinator.reportsExpired', compact('companies', 'students', 'user','course'));
}

public function sendEmailExpired(Request $request)
{
    $email = $request->input('email');
    $user = User::where('email', $email)->first(); // Retrieve the logged-in user
    $currentYear = now()->year;

    // Retrieve the selected companies
    $companies = Company::all()->filter(function ($company) use ($currentYear) {
        // Extract the start year from the "school_year" format
        list($startYear, $endYear) = explode('-', $company->school_year);

        // Convert them to integers
        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        // Check if the difference between current year and start year is greater than 3
        $isExpired = ($currentYear - $startYear) > 3;

        return $isExpired;
    });

    $companyNames = $companies->pluck('company_name')->toArray();

    // Retrieve students under the specified companies
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames);
    })->get();

    // Send email with the data
    Mail::to($email)->send(new MOAReportEmail($companies, $students));

    return back()->with(['message' => 'Email sent successfully']);
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
        });
    
        $companyNames = $companies->pluck('company_name')->toArray();
    
        // Retrieve students under the specified companies using where and get
        // $students = Student::whereHas('companies', function ($query) use ($companyNames, $course) {
        //     $query->whereIn('company_name', $companyNames)
        //         ->where('course', $course); // Add condition to filter by course
        // })->get();
    
        return view('professor.expiredMOAReports', compact('companies', 'user', 'stu','courseAll'));
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

    $schoolYear = $startYear . '-' . $endYear;
    $user = User::find(Session::get('loginId'));
    $courseAll = Courses::all();
    $currentYear = now()->year;

    // Retrieve the selected company or companies
    $companyy = Company::where(function ($query) use ($startYear, $endYear) {
        $query->where('school_year', '>=', $startYear)
            ->where('school_year', '<=', $endYear);
    })->get();

    $companies = $companyy->filter(function ($company) use ($currentYear, $validatedData) {
        // Extract the start year from the "school_year" format
        list($startYear, $endYear) = explode('-', $company->school_year);

        // Convert them to integers
        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        
        // Check if the company has students associated with the specified course
        $hasStudentsWithCourse = $company->students()->where('course', $validatedData['course'])->exists();

        return  $hasStudentsWithCourse;
    });
    $companyNames = $companies->pluck('company_name')->toArray();

    // Retrieve students under the specified companies using where and get
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames);
    })
    ->where('course', $validatedData['course'])
    ->get();

    return view('professor.expiredMOAReports', compact('companies', 'students', 'user','courseAll'));
}



}
