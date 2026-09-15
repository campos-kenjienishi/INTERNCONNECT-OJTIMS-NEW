<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
public function students()
{
    return $this->belongsToMany(Student::class, 'company_student', 'company_id', 'student_id');
}

public function vouchers()
{
    return $this->hasMany(Voucher::class, 'company_id');
}

public function latestVoucher()
{
    return $this->hasOne(Voucher::class, 'company_id')->latestOfMany();
}

public function scopeFiltered($query, $schoolYear, $course)
    {
        return $query->where('school_year', $schoolYear)
                     ->where('course', $course);
    }
}
