<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
