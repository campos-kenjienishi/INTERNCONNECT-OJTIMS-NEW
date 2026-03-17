<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
Use App\Mail\TemporaryPasswordNotification;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use Illuminate\Support\Str;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\Announcements;
use App\Models\OJTInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use App\Helpers\AuditLogger;


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
        $user->studentNum = $request->studentNum;
        $user->course= $request->course;
        $user->year_and_section= $request->year_and_section;
        $user->adviser_name = $request->adviser_name;
        $user->password = Hash::make($request->password);
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $student->studentNum =  $user->studentNum;
        $studentE->first_name =$user->first_name;
        $studentE->last_name =$user->last_name;
        $studentE->email =$user->email;
        $studentE->studentNum =$user->studentNum;
        $studentE->course = $user->course;
        $studentE->year_and_section =$user->year_and_section;
        $studentE->school_year_start = $request->academic_year_start;
        $studentE->school_year_end   = $request->academic_year_end;
        $studentE->adviser_name =$user->adviser_name;
        $studentE->full_name = $user->full_name;

        $res = $user->save();
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

                        $userName=$data->full_name;
                        $fileCount = UploadedFile::where('uploader_name', $userName)->count();
    
        return view('ojtCoordinator.dashboard', compact('data','roleCount','roleCountP','fileCount'));
    
    }

    public function logout(){
        if(Session::has('loginId')){
            Session::pull('loginId');
            Session::forget('termsAccepted');
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
    
        return view('ojtCoordinator.professorTab', compact('data', 'user', 'subjectData','usersP',"course"));
    }

    public function professorCreate(Request $request){

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'subject_code' => 'required|string|max:255',
            'subject_description' => 'required|string|max:255',
        ]);

        $temporaryPassword = Str::random(8);

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
        $user->password = Hash::make($temporaryPassword);
        $user->role = 2;

        $professor->email = $request->email;
        $professor->full_name = $user->full_name;

        Mail::to($user->email)->send(new TemporaryPasswordNotification($temporaryPassword));

        $professor->save();
        $res = $user->save();

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
        $userName = ''; // Initialize with an empty string
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        
        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name;
        }
    
        $roleCount = User::where('role', 0)
        ->where(function ($query) use ($userName) {
            $query->where('adviser_name', $userName)
                ->orWhere('id', Session::get('loginId')); // Include the logged-in professor in the count
        })
        ->where('created_at', '>=', $sixMonthsAgo)
        ->count();
    
        $fileCount = UploadedFile::all()->count();


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
    
        return view('professor.home', compact('companies','data', 'roleCount', 'fileCount'));

            
            }



public function pending(){

    $data=array();
        if(Session::has('loginId')){

            $data=User::where('id','=', Session::get('loginId'))->first();
                    }
    return view('students.pending', compact('data'));
}

     
}