<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$count = (int) ($argv[2] ?? 35);
$entries = collect(range(1, max($count, 1)))->take($count);
$html = view($argv[1], [
    'classrooms' => collect([(object) ['id' => 1, 'room' => 'Class A', 'course' => 'BSIT']]),
    'students' => $entries->map(fn ($number) => (object) [
        'id' => $number, 'full_name' => 'Fixture Student '.$number,
        'studentInfo' => (object) ['studentNum' => 'TEST-'.$number, 'class_id' => 1],
    ]),
    'selectedClassId' => null, 'requestsByStudent' => collect(), 'printedAt' => now(),
    'data' => (object) ['full_name' => 'Fixture Professor'],
    'totalStudents' => $count, 'submittedRequests' => 3, 'templateCount' => 4,
    'approvedStudents' => 2, 'pendingApprovals' => 1, 'deniedStudents' => 0, 'inactiveStudents' => 0,
    'fileApproved' => 5, 'filePending' => 2, 'fileDenied' => 0,
    'classAnalytics' => $entries->map(fn ($number) => [
        'label' => 'Fixture Class '.$number, 'total_students' => 5, 'submitted' => 3, 'completion' => 60,
    ])->all(),
    'analyticsInsights' => ['summary' => 'Fixture insight', 'key_findings' => ['Fixture finding']],
    'title' => 'Fixture Analytics', 'subtitle' => 'Fixture scope',
    'summaryRows' => array_fill(0, 7, ['label' => 'Summary', 'value' => 5]),
    'monthlyRows' => $entries->map(fn ($number) => ['label' => 'Month '.$number, 'files' => 5, 'students' => 3])->all(),
    'company' => (object) ['vouchers' => collect([(object) ['filename' => 'FIXTURE123', 'created_at' => now()]])],
])->render();
if (($argv[3] ?? '') === 'pdf') {
    $pdf = new Dompdf\Dompdf();
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();
    echo $pdf->output();
} else {
    echo $html;
}
