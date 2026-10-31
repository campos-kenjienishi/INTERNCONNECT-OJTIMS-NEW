<?php

return [
    'enabled' => env('ANNOUNCEMENT_EMAIL_ENABLED', false),
    'mailer' => env('ANNOUNCEMENT_EMAIL_MAILER', env('MAIL_MAILER', 'smtp')),
    'batch_size' => (int) env('ANNOUNCEMENT_EMAIL_BATCH_SIZE', 10),
    'interval_ms' => (int) env('ANNOUNCEMENT_EMAIL_INTERVAL_MS', 1000),
];
