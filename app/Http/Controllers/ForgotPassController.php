<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Courses;
use App\Models\MOAUpload;
Use App\Mail\TemporaryPasswordNotification;
Use App\Mail\ForgotPassNotif;
Use App\Mail\SendFile;
use App\Models\Professor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuditLogger;


class ForgotPassController extends Controller
{
    public function resetP(){
        return view("auth.reset");
    }

    public function forgotP(){
        return view("auth.forgot");
    }


    public function forgotPass(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);
    
        $userEmail = $request->email;
    
        // Pass the recipient's email to the ForgotPassNotif constructor
        Mail::to($userEmail)->send(new ForgotPassNotif($userEmail));
    
        return back()->with('success', 'Email sent successfully!');
    }

    public function resetPass(Request $request) {
        $email = $request->query('email');
        
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'max:12',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,12}$/',
            ],
            'confirm_password' => 'required|same:password',
        ], [
            'password.min' => 'Password must be 8 to 12 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
            'password.max' => 'Password must be 8 to 12 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
            'password.regex' => 'Password must be 8 to 12 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
            'confirm_password.same' => 'Password confirmation does not match.',
        ]);
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return back()->with('fail', 'Email not found');
        }
        
        $user->password = Hash::make($request->input('password'));
        $res = $user->save();
        
        if ($res) {
            return back()->with('success', 'Password reset successfully! Proceed to login.');
        } else {
            return back()->with('fail', ' Something went wrong.');
        }
    }
    



 
}
