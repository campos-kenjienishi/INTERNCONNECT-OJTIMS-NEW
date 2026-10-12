<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoaRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'previous_date_notarized',
        'previous_valid_until',
        'previous_file',
        'new_date_notarized',
        'new_valid_until',
        'new_file',
        'renewed_by',
        'renewed_at',
        'notes',
    ];

    protected $casts = [
        'previous_date_notarized' => 'date',
        'previous_valid_until' => 'date',
        'new_date_notarized' => 'date',
        'new_valid_until' => 'date',
        'renewed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function renewedBy()
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }
}
