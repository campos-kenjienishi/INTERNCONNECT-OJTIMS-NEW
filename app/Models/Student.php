<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;  // ← Add this line
use Illuminate\Http\Request;


class Student extends Model
{
    use HasFactory;

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_student', 'student_id', 'company_id');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'adviser_name', 'full_name');
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
