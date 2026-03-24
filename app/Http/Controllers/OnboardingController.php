<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OJTInformation;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuditLogger;

class OnboardingController extends Controller
{
    public function showForm(Request $request)
    {
        $idp = Session::get('onboarding_idp', []);
        // Optionally, fetch courses, professors, etc. for dropdowns
        $courses = \App\Models\Courses::all();
        $professors = \App\Models\Professor::all();
        return view('onboarding', [
            'idp' => $idp,
            'courses' => $courses,
            'professors' => $professors,
        ]);
    }

    public function complete(Request $request)
    {
        $idp = Session::get('onboarding_idp', []);
        if (empty($idp['email'])) {
            return redirect('/login')->with('error', 'Session expired. Please login again.');
        }
        $request->validate([
            'studentNum' => 'required',
            'semester' => 'required',
            'academic_year_start' => 'required',
            'academic_year_end' => 'required',
            'adviser_name' => 'required',
            'course' => 'required',
            'year_and_section' => 'required',
        ]);
        // Create user
        $user = new User();
        $user->first_name = $idp['first_name'] ?? '';
        $user->middle_name = $idp['middle_name'] ?? '';
        $user->last_name = $idp['last_name'] ?? '';
        $user->email = $idp['email'];
        $user->password = Hash::make(uniqid('idp_', true)); // random password
        $user->full_name = trim(($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name));
        $user->role = '0'; // default to student
        $user->save();
        // OJTInformation
        $ojt = new OJTInformation();
        $ojt->studentNum = $request->studentNum;
        $ojt->save();
        // Student
        $student = new Student();
        $student->studentNum = $request->studentNum;
        $student->course = $request->course;
        $student->year_and_section = $request->year_and_section;
        $student->school_year_start = $request->academic_year_start;
        $student->school_year_end = $request->academic_year_end;
        $student->adviser_name = $request->adviser_name;
        $student->user_id = $user->id;
        $student->save();
        AuditLogger::log('Student Account', 'create', 'Onboarded new student: ' . $user->full_name, $user->id);
        Session::forget('onboarding_idp');
        Session::put('loginId', $user->id);
        Log::info('Onboarding complete, user created and logged in', ['user_id' => $user->id]);
        return redirect('/student/home');
    }
    
    // Alias for POST route compatibility
    public function store(Request $request)
    {
        return $this->complete($request);
    }
}
