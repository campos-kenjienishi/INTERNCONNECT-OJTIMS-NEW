<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'professor_subject', 'professor_id', 'subject_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'adviser_name', 'full_name');
    }
    
}
