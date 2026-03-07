<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;  // ← Add this line
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // In User.php
    public function studentInfo()
    {
        return $this->hasOne(Student::class, 'studentNum', 'studentNum');
    }
    public function uploadPhoto(Request $request, $email)
{
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // 1. Update the Student model
    $student = Student::where('email', $email)->firstOrFail();
    
    // 2. Find the corresponding User model
    $user = User::where('email', $email)->first();

    // Delete old photo if it exists
    if ($student->profile_photo && Storage::disk('public')->exists($student->profile_photo)) {
        Storage::disk('public')->delete($student->profile_photo);
    }

    // Store new photo
    $path = $request->file('profile_photo')->store('profile_photos', 'public');

    // Update both tables
    $student->profile_photo = $path;
    $student->save();

    if ($user) {
        $user->profile_photo = $path; // Ensure 'profile_photo' column exists in users table too!
        $user->save();
    }

    return redirect()->back()->with('success', 'Profile photo updated successfully!');
}
}
