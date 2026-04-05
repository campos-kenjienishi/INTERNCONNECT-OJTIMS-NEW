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
        'signature_path',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
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
