<?php

return [
    'disposal_enabled' => filter_var(env('RETENTION_DISPOSAL_ENABLED', false), FILTER_VALIDATE_BOOL),
    'requirements_years' => (int) env('RETENTION_REQUIREMENTS_YEARS', 1),
    'completion_records_years' => (int) env('RETENTION_COMPLETION_RECORDS_YEARS', 2),
    'profiles_years' => (int) env('RETENTION_PROFILES_YEARS', 2),
    'moa_years' => (int) env('RETENTION_MOA_YEARS', 2),
    'inactive_review_days' => (int) env('RETENTION_INACTIVE_REVIEW_DAYS', 365),
];
