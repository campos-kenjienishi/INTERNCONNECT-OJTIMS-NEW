<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjtEvaluationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'template_id',
        'student_num',
        'student_name',
        'supervisor_name',
        'supervisor_email',
        'token',
        'token_expires_at',
        'emailed_at',
        'opened_at',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'emailed_at' => 'datetime',
        'opened_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function template()
    {
        return $this->belongsTo(OjtEvaluationTemplate::class, 'template_id');
    }

    public function evaluation()
    {
        return $this->hasOne(OjtEvaluation::class, 'request_id');
    }
}
