<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjtEvaluationTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'section',
        'label',
        'input_type',
        'display_order',
        'is_required',
    ];

    public function template()
    {
        return $this->belongsTo(OjtEvaluationTemplate::class, 'template_id');
    }
}
