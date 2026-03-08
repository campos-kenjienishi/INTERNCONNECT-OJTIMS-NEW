<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_name',        
        'user_role',
        'affected_user_id',
        'affected_name',     
        'action',
        'module',
        'description',
        'ip_address'
    ];
}