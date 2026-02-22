<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function professors()
{
    return $this->belongsToMany(Professor::class, 'professor_subject', 'subject_id', 'professor_id');
    
}


public function schedules()
    {
        return $this->hasMany(Schedule::class, 'subject_code', 'subject_code');
    }
}
