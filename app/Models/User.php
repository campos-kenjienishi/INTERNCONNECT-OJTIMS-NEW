<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;  // ← Add this line
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function studentInfo()
    {
        return $this->hasOne(Student::class, 'user_id', 'id');
    }

    public function professorInfo(): HasOne
    {
        return $this->hasOne(Professor::class, 'user_id', 'id');
    }

    protected function getStudentAttributeFromProfile(string $column, $fallback)
    {
        if ((int) $this->role !== 0) {
            return $fallback;
        }

        $profile = $this->relationLoaded('studentInfo') ? $this->studentInfo : $this->studentInfo()->first();
        if (!$profile) {
            return $fallback;
        }

        return $profile->{$column} ?? $fallback;
    }

    public function getStudentNumAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('studentNum', $value);
    }

    public function getDateOfBirthAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('date_of_birth', $value);
    }

    public function getContactNumberAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('contact_number', $value);
    }

    public function getAddressAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('address', $value);
    }

    public function getYearAndSectionAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('year_and_section', $value);
    }

    public function getCourseAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('course', $value);
    }

    public function getAdviserNameAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('adviser_name', $value);
    }

    public function getClassIdAttribute($value)
    {
        return $this->getStudentAttributeFromProfile('class_id', $value);
    }

    public function uploadPhoto(Request $request, $email)
{
    $request->validate([
        'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // 1. Resolve user then linked student profile
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
