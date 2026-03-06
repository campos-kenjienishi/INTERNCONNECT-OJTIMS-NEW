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
use App\Helpers\AuditLogger;

class CompanyController extends Controller
{
    public function companies(Request $request)
    {
        $user = [];
    
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        
    
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
    
          
            return $currentYear >= $startYear && $currentYear <= $startYear + 3;
        });
    
        $companyNames = $companies->pluck('company_name')->toArray();
    
        // Retrieve students under the specified companies using where and get
        $students = Student::whereHas('companies', function ($query) use ($companyNames) {
            $query->whereIn('company_name', $companyNames);
        })->get();
    
        return view('ojtCoordinator.companies', compact('companies', 'students', 'user', 'stu'));
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
    $user = array();

    if (Session::has('loginId')) {
        $user = User::where('id', '=', Session::get('loginId'))->first();
    }

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

    $expirationDate = now()->addYears(3);

    // Create or retrieve the company
    $com = new Company();
    $com->company_name = $request->company_name;
    $com->company_address = $request->company_address;
    $com->company_rep = $request->company_rep;
    $com->companyNo = $request->companyNo;
    $com->company_email = $request->company_email;
    $startYear = $request->input('school_year_start');
    $endYear = $request->input('school_year_end');
    $schoolYear = $startYear . '-' . $endYear; // Combine the start and end years
    $com->school_year = $schoolYear;
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
  


    // Save the company to the database
    if ($com->save()) {
        // Get student names as an array
        $studentNames = $request->input('student_names', []);

        // Check the role of the user
        if ($data->role == 0) {
            // If the role is 0, set student_names to full_name
            $studentNames = [$data->full_name];
            
        }

        // Find existing students by their names and get their IDs
        $existingStudents = Student::whereIn('full_name', $studentNames)->get();

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


public function pending()
{
    $user = array();

    if (Session::has('loginId')) {
        $user = User::where('id', '=', Session::get('loginId'))->first();
    }

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
