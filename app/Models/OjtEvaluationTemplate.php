<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjtEvaluationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'version',
        'previous_template_id',
        'is_active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function items()
    {
        return $this->hasMany(OjtEvaluationTemplateItem::class, 'template_id')->orderBy('display_order');
    }

    public function previousTemplate()
    {
        return $this->belongsTo(self::class, 'previous_template_id');
    }
}
