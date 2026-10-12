<?php

$megabyte = 1024 * 1024;

return [
    'disk' => env('UPLOAD_DISK', 'secure_uploads'),
    'storage_directory' => '',
    'quarantine_directory' => '.quarantine',
    'legacy_directory' => public_path('assets'),
    'max_bytes' => (int) env('UPLOAD_MAX_SIZE_MB', 30) * $megabyte,
    'max_expanded_bytes' => (int) env('UPLOAD_MAX_EXPANDED_SIZE_MB', 100) * $megabyte,
    'download_rate_per_minute' => (int) env('UPLOAD_DOWNLOAD_RATE_PER_MINUTE', 60),
    'preview_rate_per_minute' => (int) env('UPLOAD_PREVIEW_RATE_PER_MINUTE', 120),
    'evidence_enabled' => (bool) env('UPLOAD_SECURITY_EVIDENCE_ENABLED', true),
    'hosting_scanner' => env('UPLOAD_HOSTING_SCANNER', 'monarx'),
    'integrity_check_enabled' => (bool) env('UPLOAD_INTEGRITY_CHECK_ENABLED', true),

    'malware_scan' => [
        'enabled' => (bool) env('UPLOAD_MALWARE_SCAN_ENABLED', false),
        'required' => (bool) env('UPLOAD_MALWARE_SCAN_REQUIRED', env('APP_ENV', 'production') === 'production'),
        'host' => env('CLAMAV_HOST', '127.0.0.1'),
        'port' => (int) env('CLAMAV_PORT', 3310),
        'timeout' => (float) env('CLAMAV_TIMEOUT', 30),
    ],

    'profiles' => [
        'pdf' => [
            'max_bytes' => (int) env('UPLOAD_MAX_SIZE_MB', 30) * $megabyte,
            'types' => [
                'pdf' => [
                    'mime_types' => ['application/pdf', 'application/x-pdf'],
                ],
            ],
        ],

        'template' => [
            'max_bytes' => (int) env('UPLOAD_MAX_SIZE_MB', 30) * $megabyte,
            'max_expanded_bytes' => (int) env('UPLOAD_MAX_EXPANDED_SIZE_MB', 100) * $megabyte,
            'types' => [
                'pdf' => [
                    'mime_types' => ['application/pdf', 'application/x-pdf'],
                ],
                'doc' => [
                    'mime_types' => ['application/msword', 'application/cdfv2', 'application/x-ole-storage'],
                ],
                'docx' => [
                    'mime_types' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
                ],
                'xls' => [
                    'mime_types' => ['application/vnd.ms-excel', 'application/cdfv2', 'application/x-ole-storage'],
                ],
                'xlsx' => [
                    'mime_types' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
                ],
            ],
        ],
    ],
];
