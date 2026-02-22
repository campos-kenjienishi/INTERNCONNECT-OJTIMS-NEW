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
use Illuminate\Support\Facades\Stroage;


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
            'confirm_password' => 'required',
        ]);
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return back()->with('fail', 'Email not found');
        }
        
        $user->password = Hash::make($request->input('confirm_password'));
        $res = $user->save();
        
        if ($res) {
            return back()->with('success', 'Password reset successfully! Proceed to login.');
        } else {
            return back()->with('fail', ' Something went wrong.');
        }
    }
    



 
}


