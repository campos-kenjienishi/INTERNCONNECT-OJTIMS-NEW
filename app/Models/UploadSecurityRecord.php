<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadSecurityRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'storage_disk',
        'storage_path',
        'filename',
        'directory',
        'profile',
        'extension',
        'mime_type',
        'size_bytes',
        'sha256',
        'validation_status',
        'scanner_driver',
        'scan_status',
        'scanned_at',
        'hosting_scanner',
        'hosting_scan_status',
        'source_type',
        'source_id',
        'uploaded_by_user_id',
        'origin',
        'lifecycle_status',
        'last_verified_at',
        'retired_at',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'scanned_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'retired_at' => 'datetime',
    ];
}
