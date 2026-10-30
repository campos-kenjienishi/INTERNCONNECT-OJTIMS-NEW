<?php

return [
    'headers_enabled' => env('SECURITY_HEADERS_ENABLED', true),

    'login_attempts_per_minute' => env('SECURITY_LOGIN_ATTEMPTS_PER_MINUTE', 5),
    'login_ip_attempts_per_minute' => env('SECURITY_LOGIN_IP_ATTEMPTS_PER_MINUTE', 30),

    'hsts_enabled' => env('SECURITY_HSTS_ENABLED', true),
    'hsts_max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000),

    'csp_mode' => env('SECURITY_CSP_MODE', 'report-only'),
    'csp_policy' => env('SECURITY_CSP_POLICY', implode('; ', [
        "default-src 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "form-action 'self'",
        "frame-ancestors 'self'",
        "script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://cdn.datatables.net https://unicons.iconscout.com",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.datatables.net https://unicons.iconscout.com",
        "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://unicons.iconscout.com",
        "img-src 'self' data: blob: https:",
        "connect-src 'self' https://identity-provider.isaxbsit2027.com https://puptas.undraftedbsit2027.com",
        "frame-src 'self' blob:",
        "worker-src 'self' blob:",
        "media-src 'self' blob:",
        "manifest-src 'self'",
        'upgrade-insecure-requests',
    ])),
];
