<?php

return [
    'retention_days' => (int) env('AUDIT_LOG_RETENTION_DAYS', 730),
    'prune_enabled' => filter_var(env('AUDIT_LOG_PRUNE_ENABLED', true), FILTER_VALIDATE_BOOL),
];
