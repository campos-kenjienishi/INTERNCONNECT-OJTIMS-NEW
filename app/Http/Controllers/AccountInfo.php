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
use Illuminate\Validation\Rule;



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

        $request->validate([
            'first_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'middle_name' => ['nullable', 'regex:' . $this->nameValidationPattern()],
            'last_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ], array_merge(
            $this->nameValidationMessages(),
            [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already in use.',
            ]
        ));
    
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
        'current_password' => 'required',
        'confirm_password' => 'required|same:new_password',
        'new_password' => [
            'required',
            'string',
            'min:8',
            'max:12',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,12}$/',
        ],
    ], [
        'new_password.min' => 'New password must be 8 to 12 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
        'new_password.max' => 'New password must be 8 to 12 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
        'new_password.regex' => 'New password must be 8 to 12 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
        'confirm_password.same' => 'Password confirmation does not match.',
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

    public function verifyCurrentPassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        if ((int) Session::get('loginId') !== (int) $id) {
            abort(403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'Unable to verify the current password.',
            ], 404);
        }

        $isValid = Hash::check($request->current_password, $user->password);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? '' : 'Current password is incorrect.',
        ]);
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

        $request->validate([
            'first_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'middle_name' => ['nullable', 'regex:' . $this->nameValidationPattern()],
            'last_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ], array_merge(
            $this->nameValidationMessages(),
            [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already in use.',
            ]
        ));
    
      
    
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

    protected function nameValidationPattern(): string
    {
        return "/^[\\p{L}]+(?:[ '\\-][\\p{L}]+)*$/u";
    }

    protected function nameValidationMessages(): array
    {
        return [
            'first_name.regex' => "First name may only contain letters, spaces, apostrophes, and hyphens.",
            'middle_name.regex' => "Middle name may only contain letters, spaces, apostrophes, and hyphens.",
            'last_name.regex' => "Last name may only contain letters, spaces, apostrophes, and hyphens.",
        ];
    }
}
