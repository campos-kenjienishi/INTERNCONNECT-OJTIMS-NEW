<?php
// Temporary test script to exercise ReportAiInsightService
// Run: php scripts/ai_test.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var \App\Services\ReportAiInsightService $service */
$service = $app->make(\App\Services\ReportAiInsightService::class);

$metrics = [
    'total_moa' => 10,
    'active_moa' => 8,
    'expired_moa' => 2,
    'total_records' => 50,
    'total_companies' => 5,
    'records_with_ojt' => 40,
    'missing_ojt' => 10,
    'course' => 'BSIT'
];

$result = $service->summarize('moa', $metrics, ['Strong company engagement'], ['2 expired MOAs'], ['Follow up with partners']);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
