<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjtEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'template_id',
        'supervisor_name',
        'responses_json',
        'comments',
        'supervisor_confirmation',
        'submitted_at',
        'released_to_student_at',
    ];

    protected $casts = [
        'supervisor_confirmation' => 'boolean',
        'submitted_at' => 'datetime',
        'released_to_student_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(OjtEvaluationRequest::class, 'request_id');
    }

    public function template()
    {
        return $this->belongsTo(OjtEvaluationTemplate::class, 'template_id');
    }

    public function getResponsesAttribute()
    {
        return json_decode($this->responses_json ?? '{}', true) ?: [];
    }
}
