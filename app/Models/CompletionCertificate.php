<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletionCertificate extends Model
{
    use HasFactory;

    public const PENDING_PROFESSOR = 'pending_professor';
    public const PENDING_COORDINATOR = 'pending_coordinator';
    public const APPROVED = 'approved';
    public const PROFESSOR_REJECTED = 'professor_rejected';
    public const COORDINATOR_REJECTED = 'coordinator_rejected';

    protected $fillable = [
        'student_id',
        'student_user_id',
        'professor_id',
        'file',
        'original_name',
        'storage_directory',
        'version',
        'status',
        'professor_remarks',
        'professor_reviewed_by',
        'professor_reviewed_at',
        'coordinator_remarks',
        'coordinator_reviewed_by',
        'coordinator_reviewed_at',
        'superseded_at',
        'legacy_file_requirement_id',
    ];

    protected $casts = [
        'professor_reviewed_at' => 'datetime',
        'coordinator_reviewed_at' => 'datetime',
        'superseded_at' => 'datetime',
        'version' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentUser()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function professorReviewer()
    {
        return $this->belongsTo(User::class, 'professor_reviewed_by');
    }

    public function coordinatorReviewer()
    {
        return $this->belongsTo(User::class, 'coordinator_reviewed_by');
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('superseded_at');
    }

    public function canBeResubmitted(): bool
    {
        return in_array($this->status, [self::PROFESSOR_REJECTED, self::COORDINATOR_REJECTED], true);
    }

    public function hasProfessorValidation(): bool
    {
        return $this->professor_reviewed_at !== null && $this->status !== self::PROFESSOR_REJECTED;
    }

    public function professorValidationWasSkipped(): bool
    {
        return $this->status === self::APPROVED && ! $this->hasProfessorValidation();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::PENDING_COORDINATOR => 'Pending Coordinator Review',
            self::APPROVED => 'Fully Approved',
            self::PROFESSOR_REJECTED => 'Professor Rejected',
            self::COORDINATOR_REJECTED => 'Coordinator Rejected',
            default => 'Professor Review Pending (Optional)',
        };
    }
}
