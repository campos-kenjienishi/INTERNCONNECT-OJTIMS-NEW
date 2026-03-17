<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_code',
        'course',
        'academic_year',
        'semester',
        'schedule_day',
        'schedule_time',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'subject_code');
    }
    public function classes()
    {
        return $this->belongsTo(Classes::class, 'course', 'course');
    }
}
 