<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
use App\Models\Student;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Voucher;
use App\Models\Professor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OJTInformation;
use App\Models\FileRequirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Helpers\AuditLogger;

class CompanyController extends Controller
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

    public function companies(Request $request)
    {
        $user = [];
        $course = Courses::all();
        $selectedCourse = $request->query('course');
        $selectedSchoolYear = $request->query('school_year');
    
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        
    
        // Get the current year
        $currentYear = now()->year;
    
        // Retrieve the selected company or companies
        $companies = Company::with('students')->get(); // Get all companies
        $schoolYears = Company::whereNotNull('school_year')
            ->pluck('school_year')
            ->filter()
            ->unique()
            ->sortByDesc(function ($schoolYear) {
                $normalizedYear = str_replace(' ', '', $schoolYear);
                $parts = explode('-', $normalizedYear);
                return (int) ($parts[0] ?? 0);
            })
            ->values();
    
        $stu = Student::all();
    
        // Filter companies based on the start year of "school_year"
        $companies = $companies->filter(function ($company) use ($currentYear) {
            // Extract the start year from the "school_year" format
            list($startYear, $endYear) = explode('-', $company->school_year);
    
            // Convert them to integers
            $startYear = (int) $startYear;
            $endYear = (int) $endYear;
    
          
            return $currentYear >= $startYear && $currentYear <= $startYear + 3;
        });

        if (!empty($selectedCourse)) {
            $companies = $companies->where('course', $selectedCourse);
        }

        if (!empty($selectedSchoolYear)) {
            $normalizedSelectedYear = str_replace(' ', '', $selectedSchoolYear);
            $companies = $companies->filter(function ($company) use ($normalizedSelectedYear) {
                return str_replace(' ', '', $company->school_year) === $normalizedSelectedYear;
            });
        }

        $companies = $companies->sortByDesc(function ($company) {
            $normalizedYear = str_replace(' ', '', $company->school_year ?? '0-0');
            $parts = explode('-', $normalizedYear);
            return (int) ($parts[0] ?? 0);
        })->values();
    
        $companyNames = $companies->pluck('company_name')->toArray();
    
        // Retrieve students under the specified companies using where and get
        $students = Student::whereHas('companies', function ($query) use ($companyNames) {
            $query->whereIn('company_name', $companyNames);
        })->get();
    
        return view('ojtCoordinator.companies', compact('companies', 'students', 'user', 'stu', 'course', 'selectedCourse', 'schoolYears', 'selectedSchoolYear'));
    }
    



    private function generateVoucherCode($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }


public function companiesup(Request $request)
{
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $user = $sessionCheck;

    // Retrieve the companies where the current user is the uploader
    $companies = Company::where('uploader_name', $user->full_name)->get();
    $stu= Student::all();

    $companyNames = $companies->pluck('company_name')->toArray(); // Get an array of company names

    // Retrieve students under the specified companies using where and get
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames); // Use whereIn to match multiple company names
    })->get();
    $ojt = OJTInformation::where('studentNum', $user->studentNum)->get();

    return view('students.companiesup', compact('companies', 'students', 'user','stu','ojt'));
}


public function companyCreate(Request $request)
{
    $data = [];

    if (Session::has('loginId')) {
        $data = User::where('id', '=', Session::get('loginId'))->first();
    }

    $validator = Validator::make($request->all(), [
                        
        'file' => 'required|mimes:pdf|max:10240', // max:10240 is the maximum file size in kilobytes (10 MB)
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    if ($data->role == 1) {
        $request->validate([
            'course' => 'required|exists:courses,course',
        ]);
    }

    if ($data->role == 0) {
        $request->validate([
            'valid_until' => 'required|date',
        ]);
    }

    $expirationDate = $data->role == 0
        ? $request->input('valid_until')
        : now()->addYears(3);

    // Create or retrieve the company
    $com = new Company();
    $com->company_name = $request->company_name;
    $com->company_address = $request->company_address;
    $com->company_rep = $request->company_rep;
    $com->companyNo = $request->filled('companyNo') ? $request->companyNo : 'N/A';
    $com->company_email = $request->company_email;
    $startYear = $request->input('school_year_start');
    $endYear = $request->input('school_year_end');
    $schoolYear = $startYear . '-' . $endYear; // Combine the start and end years
    $com->school_year = $schoolYear;

    if ($data->role == 0) {
        $studentCourse = $data->course;

        if (empty($studentCourse)) {
            $student = Student::where('studentNum', $data->studentNum)->first();
            $studentCourse = $student->course ?? null;
        }

        $com->course = $studentCourse;
    } else {
        $com->course = $request->input('course');
    }

        $file = $request->file;
    $filename = time() . '.' . $file->getClientOriginalExtension();
    $request->file->move('assets', $filename);
    $com->file = $filename;

    // Set the uploader_name field based on the logged-in user's name
    $com->uploader_name = $data->full_name;
    $com->valid_until = $expirationDate;

    

    // Upload File Requirement
    $fileRequirement = $request->file;
    $fileRequirementFilename = time() . '.' . $fileRequirement->getClientOriginalExtension();
    

    // Create a new instance of FileRequirement model
    $fileup = new FileRequirement();
    $fileup->fileName = "Notarized MOA"; 
    $fileup->file = $fileRequirementFilename;
    $fileup->status = 0;
    $fileup->adviser = $data->adviser_name;
    $fileup->uploadedBy = $data->full_name;

// Save the model instance
$res = $fileup->save();
  


    $selectedStudentNames = $request->input('student_names', []);
    $manualStudentNames = $request->input('manual_student_names', '');

    $parsedManualNames = array_values(array_unique(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) $manualStudentNames)))));
    $studentNames = array_values(array_unique(array_filter(array_map('trim', array_merge($selectedStudentNames, $parsedManualNames)))));

    if ($data->role == 0) {
        $studentNames = [$data->full_name];
    }

    $hasStudentDisplayColumn = Schema::hasColumn('companies', 'student_names_display');

    if ($hasStudentDisplayColumn) {
        $com->student_names_display = implode(', ', $studentNames);
    } elseif ($data->role == 1 && !empty($manualStudentNames)) {
        return back()
            ->withInput()
            ->with('fail', 'Manual student names cannot be saved yet. Please add column companies.student_names_display first.');
    }

    // Resolve selected student names via users table and map to student profiles via user_id.
    $selectedUserIds = User::whereIn('full_name', $studentNames)->pluck('id');
    $existingStudents = Student::whereIn('user_id', $selectedUserIds)->with('user')->get();

    if ($data->role == 1 && !empty($studentNames)) {
        $mismatchedStudents = $existingStudents->filter(function ($student) use ($com) {
            return $student->course !== $com->course;
        });

        if ($mismatchedStudents->isNotEmpty()) {
            $studentList = $mismatchedStudents->map(function ($student) {
                return $student->full_name;
            })->implode(', ');
            return back()
                ->withInput()
                ->with('fail', 'Selected student course does not match the MOA course: ' . $studentList);
        }
    }

    // Save the company to the database
    if ($com->save()) {

        // Attach the existing students to the company
        foreach ($existingStudents as $student) {
            $com->students()->attach($student->id);
        }

        // Generate voucher logic
        $voucherContent = $this->generateVoucherCode(10);

        // Assuming there's a Voucher model, you can save voucher details to the database
        $voucher = new Voucher();
        $voucher->company_id = $com->id;
        $voucher->filename = $voucherContent;
        $voucher->uploader_name = $data->full_name;
        $voucher->save();


    

        return back()->with('success', 'Company and students have been successfully associated.');
    } 
    
    else {
        return back()->with('fail', 'Something went wrong. Company and students could not be created.');
    }
}

public function companyUpdate(Request $request, $id)
{
    $data = [];

    if (Session::has('loginId')) {
        $data = User::where('id', '=', Session::get('loginId'))->first();
    }

    $company = Company::with('students')->find($id);

    if (!$company) {
        return back()->with('fail', 'MOA record not found.');
    }

    $validator = Validator::make($request->all(), [
        'file' => 'nullable|mimes:pdf|max:10240',
        'course' => 'required|exists:courses,course',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $oldValues = [
        'company_name' => $company->company_name,
        'company_address' => $company->company_address,
        'company_rep' => $company->company_rep,
        'companyNo' => $company->companyNo,
        'company_email' => $company->company_email,
        'school_year' => $company->school_year,
        'course' => $company->course,
        'student_names_display' => $company->student_names_display ?? null,
    ];

    $company->company_name = $request->company_name;
    $company->company_address = $request->company_address;
    $company->company_rep = $request->company_rep;
    $company->companyNo = $request->filled('companyNo') ? $request->companyNo : 'N/A';
    $company->company_email = $request->company_email;
    $startYear = $request->input('school_year_start');
    $endYear = $request->input('school_year_end');
    $company->school_year = $startYear . '-' . $endYear;
    $company->course = $request->input('course');

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move('assets', $filename);

        $oldFilePath = public_path('assets/' . $company->file);
        if (!empty($company->file) && file_exists($oldFilePath)) {
            @unlink($oldFilePath);
        }

        $company->file = $filename;
    }

    $selectedStudentNames = $request->input('student_names', []);
    $manualStudentNames = $request->input('manual_student_names', '');
    $parsedManualNames = array_values(array_unique(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) $manualStudentNames)))));
    $studentNames = array_values(array_unique(array_filter(array_map('trim', array_merge($selectedStudentNames, $parsedManualNames)))));

    $hasStudentDisplayColumn = Schema::hasColumn('companies', 'student_names_display');

    if ($hasStudentDisplayColumn) {
        $company->student_names_display = implode(', ', $studentNames);
    } elseif (!empty($manualStudentNames)) {
        return back()
            ->withInput()
            ->with('fail', 'Manual student names cannot be saved yet. Please add column companies.student_names_display first.');
    }

    $selectedUserIds = User::whereIn('full_name', $studentNames)->pluck('id');
    $existingStudents = Student::whereIn('user_id', $selectedUserIds)->with('user')->get();

    if (!empty($studentNames)) {
        $mismatchedStudents = $existingStudents->filter(function ($student) use ($company) {
            return $student->course !== $company->course;
        });

        if ($mismatchedStudents->isNotEmpty()) {
            $studentList = $mismatchedStudents->map(function ($student) {
                return $student->full_name;
            })->implode(', ');

            return back()
                ->withInput()
                ->with('fail', 'Selected student course does not match the MOA course: ' . $studentList);
        }
    }

    if ($company->save()) {
        $company->students()->sync($existingStudents->pluck('id')->all());

        AuditLogger::log(
            'MOA Upload',
            'Update',
            'Updated MOA: ' . $company->company_name,
            Session::get('loginId') ?? null,
            $oldValues,
            [
                'company_name' => $company->company_name,
                'company_address' => $company->company_address,
                'company_rep' => $company->company_rep,
                'companyNo' => $company->companyNo,
                'company_email' => $company->company_email,
                'school_year' => $company->school_year,
                'course' => $company->course,
                'student_names_display' => $company->student_names_display ?? null,
            ]
        );

        return back()->with('success', 'MOA updated successfully.');
    }

    return back()->with('fail', 'Something went wrong. MOA could not be updated.');
}


public function pending()
{
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $user = $sessionCheck;

    $ojt = OJTInformation::where('studentNum', $user->studentNum)
                        ->where(function($query) {
                            $query->where('status', 'Pending')
                                ->orWhere('status', 'With Revision');
                        })
                        ->get();

    return view('students.pending', compact('user', 'ojt'));
}

public function voucher(Company $company){

    $company->vouchers = Voucher::where('company_id', $company->id)->get();
    return view('students.voucher', compact('company'));
}


}
