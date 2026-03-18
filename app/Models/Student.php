<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;  // ← Add this line
use Illuminate\Http\Request;


class Student extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_student', 'student_id', 'company_id');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class, 'adviser_name', 'full_name');
    }

    protected function getNameAttributeFromUser(string $column, $fallback = null)
    {
        $user = $this->relationLoaded('user') ? $this->user : $this->user()->first();
        if (!$user) {
            return $fallback;
        }

        return $user->{$column} ?? $fallback;
    }

    public function getFirstNameAttribute($value)
    {
        return $this->getNameAttributeFromUser('first_name', $value);
    }

    public function getMiddleNameAttribute($value)
    {
        return $this->getNameAttributeFromUser('middle_name', $value);
    }

    public function getLastNameAttribute($value)
    {
        return $this->getNameAttributeFromUser('last_name', $value);
    }

    public function getSuffixAttribute($value)
    {
        return $this->getNameAttributeFromUser('suffix', $value);
    }

    public function getFullNameAttribute($value)
    {
        return $this->getNameAttributeFromUser('full_name', $value);
    }

    public function getEmailAttribute($value)
    {
        return $this->getNameAttributeFromUser('email', $value);
    }

    public function uploadPhoto(Request $request, $email)
{
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // 1. Update the Student model
    $user = User::where('email', $email)->firstOrFail();
    $student = Student::where('user_id', $user->id)->firstOrFail();

    // Delete old photo if it exists
    if ($student->profile_photo && Storage::disk('public')->exists($student->profile_photo)) {
        Storage::disk('public')->delete($student->profile_photo);
    }

    // Store new photo
    $path = $request->file('profile_photo')->store('profile_photos', 'public');

    // Update both tables
    $student->profile_photo = $path;
    $student->save();

    $user->profile_photo = $path; // Ensure 'profile_photo' column exists in users table too!
    $user->save();

    return redirect()->back()->with('success', 'Profile photo updated successfully!');
}
    
}
