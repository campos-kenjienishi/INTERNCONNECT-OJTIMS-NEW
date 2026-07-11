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

private function studentProfileForUser(User $user): ?Student
{
    return Student::where('user_id', $user->id)->first();
}

private function updateCompanyStudentDisplay(Company $company, ?string $removedName = null): void
{
    if (!Schema::hasColumn('companies', 'student_names_display')) {
        return;
    }

    $existingNames = collect(explode(',', (string) ($company->student_names_display ?? '')))
        ->map(fn ($name) => trim((string) $name))
        ->filter();

    $linkedNames = $company->students()->with('user')->get()
        ->pluck('full_name')
        ->map(fn ($name) => trim((string) $name))
        ->filter();

    $manualNames = $existingNames->reject(function ($name) use ($linkedNames, $removedName) {
        return $linkedNames->contains($name) || (!empty($removedName) && $name === trim($removedName));
    });

    $company->student_names_display = $manualNames
        ->merge($linkedNames)
        ->filter()
        ->unique()
        ->implode(', ');

    $company->save();
}

private function syncLinkedNotarizedRequirement(Company $company, User $user): void
{
    $requirement = FileRequirement::where('uploadedBy', $user->full_name)
        ->where('fileName', 'Notarized MOA')
        ->where('file', $company->file)
        ->first();

    $sourceRequirement = FileRequirement::where('uploadedBy', $company->uploader_name)
        ->where('fileName', 'Notarized MOA')
        ->where('file', $company->file)
        ->latest('id')
        ->first();

    $requirement = $requirement ?: new FileRequirement();
    $requirement->fileName = 'Notarized MOA';
    $requirement->file = $company->file;
    $requirement->status = $sourceRequirement->status ?? 0;
    $requirement->adviser = $user->adviser_name;
    $requirement->uploadedBy = $user->full_name;

    if (Schema::hasColumn('file_requirements', 'denial_reason')) {
        $requirement->denial_reason = $sourceRequirement->denial_reason ?? null;
    }

    if (Schema::hasColumn('file_requirements', 'professor_id') && isset($sourceRequirement->professor_id)) {
        $requirement->professor_id = $sourceRequirement->professor_id;
    }

    $requirement->save();
}

private function ensureCompanyVoucher(Company $company): void
{
    if (Voucher::where('company_id', $company->id)->exists()) {
        return;
    }

    $voucher = new Voucher();
    $voucher->company_id = $company->id;
    $voucher->filename = $this->generateVoucherCode(10);
    $voucher->uploader_name = $company->uploader_name;
    $voucher->save();
}

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
    
        $studentSchoolYears = Student::whereNotNull('school_year_start')
            ->whereNotNull('school_year_end')
            ->get()
            ->map(function ($student) {
                return trim((string) $student->school_year_start . '-' . (string) $student->school_year_end);
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();
    
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
            $companies = $companies->filter(function ($company) use ($selectedCourse) {
                return $this->companyMatchesCourse($company, $selectedCourse);
            });
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
    
    return view('ojtCoordinator.companies', compact('companies', 'students', 'user', 'course', 'selectedCourse', 'schoolYears', 'selectedSchoolYear', 'studentSchoolYears'));
    }

    public function assignableStudents(Request $request)
    {
        $course = trim((string) $request->query('course', ''));
        $schoolYear = trim((string) $request->query('school_year', ''));
        $search = trim((string) $request->query('search', ''));

        $query = Student::with('user');

        if ($course !== '') {
            $query->where('course', $course);
        }

        if ($schoolYear !== '') {
            $query->whereRaw(
                "CONCAT(COALESCE(school_year_start, ''), '-', COALESCE(school_year_end, '')) = ?",
                [$schoolYear]
            );
        }

        if ($search !== '') {
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery->where('studentNum', 'like', '%' . $search . '%')
                    ->orWhere('year_and_section', 'like', '%' . $search . '%')
                    ->orWhere('course', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('full_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $students = $query->orderBy('course')
            ->orderBy('school_year_start')
            ->orderBy('year_and_section')
            ->get()
            ->map(function (Student $student) {
                return [
                    'id' => $student->id,
                    'full_name' => trim((string) $student->full_name),
                    'course' => trim((string) $student->course),
                    'year_and_section' => trim((string) $student->year_and_section),
                    'school_year' => trim((string) $student->school_year_start . '-' . (string) $student->school_year_end),
                    'student_num' => trim((string) $student->studentNum),
                ];
            })
            ->values();

        return response()->json([
            'students' => $students,
        ]);
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

    $studentProfile = $this->studentProfileForUser($user);

    $companies = Company::with('students')
        ->where(function ($query) use ($user, $studentProfile) {
            if ($studentProfile) {
                $query->whereHas('students', function ($studentQuery) use ($studentProfile) {
                    $studentQuery->where('students.id', $studentProfile->id);
                })->orWhere(function ($ownerQuery) use ($user) {
                    $ownerQuery->where('uploader_name', $user->full_name)
                        ->whereDoesntHave('students');
                });
            } else {
                $query->where('uploader_name', $user->full_name);
            }
        })
        ->orderByDesc('created_at')
        ->get()
        ->unique('id')
        ->values();

    $linkedCompanyIds = $companies->pluck('id');

    $availableLinkableCompanies = Company::with('students')
        ->whereNotIn('id', $linkedCompanyIds)
        ->orderBy('company_name')
        ->get()
        ->filter(function ($company) use ($studentProfile) {
            if (empty($company->file)) {
                return false;
            }

            if (empty($studentProfile?->course)) {
                return true;
            }

            return $this->companyMatchesCourse($company, $studentProfile->course);
        })
        ->values();
    $stu= Student::all();

    $companyNames = $companies->pluck('company_name')->toArray(); // Get an array of company names

    // Retrieve students under the specified companies using where and get
    $students = Student::whereHas('companies', function ($query) use ($companyNames) {
        $query->whereIn('company_name', $companyNames); // Use whereIn to match multiple company names
    })->get();
    $ojt = OJTInformation::where('studentNum', $user->studentNum)->get();

    return view('students.companiesup', compact('companies', 'students', 'user','stu','ojt', 'availableLinkableCompanies'));
}

public function linkExistingMoa(Request $request)
{
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $user = $sessionCheck;
    $studentProfile = $this->studentProfileForUser($user);

    if (!$studentProfile) {
        return back()->with('fail', 'Student profile not found.');
    }

    $validated = $request->validate([
        'company_id' => 'required|exists:companies,id',
    ]);

    $company = Company::with('students')->find($validated['company_id']);

    if (!$company || empty($company->file)) {
        return back()->with('fail', 'Selected MOA is unavailable.');
    }

    if (!empty($company->course) && !empty($studentProfile->course) && !$this->companyMatchesCourse($company, $studentProfile->course)) {
        return back()->with('fail', 'The selected MOA does not match your course.');
    }

    if ($company->students->contains('id', $studentProfile->id)) {
        return back()->with('fail', 'This MOA is already linked to your account.');
    }

    $company->students()->attach($studentProfile->id);
    $this->updateCompanyStudentDisplay($company->fresh(), null);
    $this->syncLinkedNotarizedRequirement($company, $user);
    $this->ensureCompanyVoucher($company);

    AuditLogger::log(
        'MOA Upload',
        'Link',
        'Linked existing MOA: ' . $company->company_name . ' to student ' . $user->full_name,
        Session::get('loginId') ?? null,
        ['company_id' => $company->id],
        ['student_id' => $studentProfile->id]
    );

    return back()
        ->with('success', 'Existing MOA linked successfully.')
        ->with('showVoucherModal', route('voucher', $company->id));
}


public function companyCreate(Request $request)
{
    $data = [];

    if (Session::has('loginId')) {
        $data = User::where('id', '=', Session::get('loginId'))->first();
    }

    $validator = Validator::make($request->all(), [
        'school_year_start' => 'required|integer|digits:4',
        'school_year_end' => 'required|integer|digits:4',
        'file' => 'required|mimes:pdf|max:2048', // max:2048 is the maximum file size in kilobytes (2 MB)
    ]);

    $validator->after(function ($validator) use ($request) {
        $startYear = (int) $request->input('school_year_start');
        $endYear = (int) $request->input('school_year_end');

        if ($startYear && $endYear && $endYear !== $startYear + 1) {
            $validator->errors()->add('school_year_end', 'School year must be two continuous years.');
        }
    });

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    if ($data->role == 1) {
        $request->validate([
            'course' => 'required|array|min:1',
            'course.*' => 'required|exists:courses,course',
            'valid_until' => 'required|date',
        ]);
    }

    if ($data->role == 0) {
        $request->validate([
            'valid_until' => 'required|date',
        ]);
    }

    $expirationDate = $request->input('valid_until');

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
        $com->course = implode(', ', $this->normalizeCourseSelection($request->input('course')));
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
    $selectedCourses = $data->role == 1 ? $this->normalizeCourseSelection($request->input('course')) : $this->normalizeCourseSelection($com->course);

    if ($data->role == 1 && !empty($studentNames)) {
        $mismatchedStudents = $existingStudents->filter(function ($student) use ($selectedCourses) {
            return !in_array($student->course, $selectedCourses, true);
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

        AuditLogger::log(
            'MOA Upload',
            'Create',
            'Created MOA: ' . $com->company_name . ' (' . $com->school_year . ')',
            Session::get('loginId') ?? null
        );

    

        return back()
            ->with('success', 'Company and students have been successfully associated.')
            ->with('showVoucherModal', route('voucher', $com->id));
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

    if ($data->role == 0 && $company->uploader_name !== $data->full_name) {
        return back()->with('fail', 'You do not have permission to update this MOA.');
    }

    $rules = [
        'company_name' => 'required|string|max:255',
        'company_address' => 'required|string|max:255',
        'company_rep' => 'required|string|max:255',
        'companyNo' => 'nullable|string|max:255',
        'company_email' => 'required|email|max:255',
        'school_year_start' => 'required|integer|digits:4',
        'school_year_end' => 'required|integer|digits:4',
        'file' => 'nullable|mimes:pdf|max:2048',
    ];

    if ($data->role == 0) {
        $rules['valid_until'] = 'required|date';
    } else {
        $rules['course'] = 'nullable|array';
        $rules['course.*'] = 'required|exists:courses,course';
        $rules['valid_until'] = 'required|date';
    }

    $validator = Validator::make($request->all(), $rules);

    $validator->after(function ($validator) use ($request) {
        $startYear = (int) $request->input('school_year_start');
        $endYear = (int) $request->input('school_year_end');

        if ($startYear && $endYear && $endYear !== $startYear + 1) {
            $validator->errors()->add('school_year_end', 'School year must be two continuous years.');
        }
    });

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
    $company->valid_until = $request->input('valid_until');

    if ($data->role != 0) {
        $selectedCourses = $this->normalizeCourseSelection($request->input('course'));

        if (empty($selectedCourses)) {
            $selectedCourses = $this->normalizeCourseSelection($company->course);
        }

        $company->course = implode(', ', $selectedCourses);
    }

    $oldFileName = $company->file;
    $newFileName = $oldFileName;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move('assets', $filename);

        $oldFilePath = public_path('assets/' . $company->file);
        if (!empty($company->file) && file_exists($oldFilePath)) {
            @unlink($oldFilePath);
        }

        $company->file = $filename;
        $newFileName = $filename;
    }

    $selectedStudentNames = $request->input('student_names', []);
    $manualStudentNames = $request->input('manual_student_names', '');
    $parsedManualNames = array_values(array_unique(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) $manualStudentNames)))));
    $studentNames = array_values(array_unique(array_filter(array_map('trim', array_merge($selectedStudentNames, $parsedManualNames)))));

    if ($data->role == 0) {
        $studentNames = [$data->full_name];
    }

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
    $selectedCourses = $data->role == 0 ? $this->normalizeCourseSelection($company->course) : $this->normalizeCourseSelection($request->input('course'));

    if ($data->role != 0 && !empty($studentNames)) {
        $mismatchedStudents = $existingStudents->filter(function ($student) use ($selectedCourses) {
            return !in_array($student->course, $selectedCourses, true);
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
        if ($data->role != 0) {
            $company->students()->sync($existingStudents->pluck('id')->all());
        }

        if (!empty($newFileName) && $oldFileName !== $newFileName) {
            $linkedStudentNames = $company->students()->with('user')->get()
                ->pluck('full_name')
                ->filter()
                ->push($company->uploader_name)
                ->unique()
                ->values();

            FileRequirement::whereIn('uploadedBy', $linkedStudentNames)
                ->where('fileName', 'Notarized MOA')
                ->update(['file' => $newFileName]);
        }

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
