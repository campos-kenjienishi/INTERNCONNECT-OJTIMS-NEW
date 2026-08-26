<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoaUnlockRequest extends Model
{
    use HasFactory;

    protected $table = 'moa_unlock_requests';

    protected $fillable = [
        'student_id',
        'company_id',
        'request_type',
        'reason',
        'status',
        'admin_notes',
        'processed_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
