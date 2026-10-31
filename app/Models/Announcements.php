<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    use HasFactory;

    protected $casts = ['targeting' => 'array', 'email_payload' => 'array'];

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->targeting['audience'] ?? $this->audience) {
            'students' => 'Students',
            'professors' => 'OJT Professors',
            'both' => 'Students & OJT Professors',
            'class' => 'Class',
            default => 'All students',
        };
    }
}
