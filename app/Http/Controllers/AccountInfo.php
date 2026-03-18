<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Professor;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Helpers\AuditLogger;



class AccountInfo extends Controller
{
    public function accountinfo()
    {
        $data=array();
        if(Session::has('loginId')){

            $data=User::where('id','=', Session::get('loginId'))->first();
                    }

	    return view('ojtCoordinator.accountinfo', compact('data'));

    }

    public function edit(Request $request,$email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }
    
        $professor = Professor::where('email', $email)->first();
    
        if (!$professor) {
            return back()->with('error', 'Professor not found.');
        }
    
        // Update user data
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $user->suffix = $request->suffix;
        $user->email = $request->email;
    
        // Update professor data
        $professor->full_name = $user->full_name;
    
        $professor->save();
        $user->save();
        AuditLogger::log(
            'AccountInfo',
            'Update',
            'Updated account info for: ' . $user->full_name,
            Session::get('loginId') ?? null,
            null,
            [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'suffix' => $user->suffix,
                'email' => $user->email
            ]
        );
        return back()->with('success', 'You have updated the information successfully!');

    }

    public function change_password(Request $request, $id)
{
    $request->validate([
        'current_password' => 'required|min:8|max:12',
        'confirm_password' => 'required|min:8|max:12',
        'new_password' => 'required|min:8|max:12',
    ]);

    $user = User::find($id);

    // Check if the entered current password matches the one in the database
    if (Hash::check($request->current_password, $user->password)) {
        // Passwords match, proceed with updating the password
        $user->password = Hash::make($request->new_password);
        $user->save();
        AuditLogger::log(
            'AccountInfo',
            'Update',
            'Changed password for: ' . $user->full_name,
            Session::get('loginId') ?? null
        );

        // Redirect with a success message
        return back()->with('success', 'You have updated the password successfully!');
    } else {
        // Passwords do not match, show an error message
        return back()->withErrors(['current_password' => 'Current password is incorrect. Please try again.']);
    }
}

    public function profAcc()
    {
        $data=array();
        if(Session::has('loginId')){

            $data=User::where('id','=', Session::get('loginId'))->first();
                    }

	    return view('professor.profAcc', compact('data'));

    }


    public function editojt(Request $request,$email)
    {
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
    
        $student->save();
        $user->save();
        AuditLogger::log(
            'AccountInfo',
            'Update',
            'Updated OJT student info for: ' . $user->full_name,
            Session::get('loginId') ?? null,
            null,
            [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'suffix' => $user->suffix,
                'address' => $student->address,
                'contact_number' => $student->contact_number,
                'date_of_birth' => $student->date_of_birth,
                'course' => $student->course,
                'year_and_section' => $student->year_and_section,
                'studentNum' => $student->studentNum,
                'email' => $user->email
            ]
        );    
        return back()->with('success', 'You have updated the information successfully!');

    }
}