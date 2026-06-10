<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Evaluation Report</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --red: #dc2626;
            --red-dark: #991b1b;
            --bg: #f5f5f5;
            --surface: #fff;
            --surface-2: #fafafa;
            --border: #e5e7eb;
            --text: #1a1a1a;
            --muted: #6b7280;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 22px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }
        .btn-tool {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #444;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-tool.primary {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            border-color: #991b1b;
        }
        .report {
            background: var(--surface);
            border: 1px solid rgba(0,0,0,.05);
            border-radius: 10px;
            box-shadow: 0 2px 14px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .report-head {
            background: linear-gradient(135deg, #7f0000 0%, #991b1b 55%, #dc2626 100%);
            color: #fff;
        }
        .report-head-band {
            height: 4px;
            background: rgba(255,255,255,.16);
        }
        .report-head-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 22px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .brand-mark {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brand-mark img { width: 36px; height: 36px; object-fit: contain; }
        .brand-copy { min-width: 0; }
        .eyebrow {
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,.6);
            margin-bottom: 3px;
        }
        .brand-title {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.15;
        }
        .brand-sub {
            font-size: 8.5px;
            color: rgba(255,255,255,.68);
            margin-top: 3px;
        }
        .report-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 10px 22px;
            background: #f8f9fa;
            border-bottom: 1px solid var(--border);
        }
        .meta-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .meta-item {
            font-size: 9.5px;
            color: #374151;
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .meta-item span { color: #6b7280; }
        .meta-generated { font-size: 8.5px; color: #9ca3af; }
        .section-label {
            padding: 8px 22px 3px;
        }
        .section-label div {
            font-size: 8px;
            font-weight: 700;
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-left: 3px solid var(--red);
            padding-left: 6px;
        }
        .table-wrap { padding: 4px 22px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #d1d5db;
            font-family: 'Poppins', sans-serif;
        }
        th, td {
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 6px;
            vertical-align: top;
        }
        th {
            background: #7f0000;
            color: #fff;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: .4px;
            text-align: left;
        }
        td { font-size: 8.5px; color: #374151; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .student-name { font-size: 9px; font-weight: 800; color: #111827; }
        .student-meta { font-size: 7.5px; color: #6b7280; margin-top: 1px; }
        .badge {
            display: inline-block;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: 700;
            line-height: 1.1;
        }
        .badge.success { background: #dcfce7; color: #15803d; }
        .badge.warning { background: #fef9c3; color: #a16207; }
        .badge.secondary { background: #e5e7eb; color: #4b5563; }
        .badge.dark { background: #e5e7eb; color: #111827; }
        .footer-block {
            margin-top: 0;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .disclaimer {
            margin: 18px 22px 12px;
            border-top: 1px dashed #d1d5db;
            padding-top: 16px;
        }
        .disclaimer-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-left: 4px solid var(--red);
            border-radius: 8px;
            padding: 12px 14px;
        }
        .disclaimer-box h3 {
            font-size: 9px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 4px;
        }
        .disclaimer-box p {
            font-size: 8.5px;
            color: #4b5563;
            line-height: 1.6;
        }
        .footer-band {
            background: #7f0000;
            padding: 8px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .footer-band .left {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .footer-band img { width: 13px; height: 13px; object-fit: contain; opacity: .7; }
        .footer-band .left span,
        .footer-band .right {
            font-size: 8px;
            color: rgba(255,255,255,.75);
            font-weight: 500;
        }
        .footer-band .right { color: rgba(255,255,255,.5); }
        .no-print { margin-bottom: 18px; }
        .sienna-widget,
        .sienna-accessibility,
        [class*="sienna"],
        [id*="sienna"],
        .sienna-widget * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { background: #fff; }
            .no-print { display: none !important; }
            .sienna-widget,
            .sienna-accessibility,
            [class*="sienna"],
            [id*="sienna"],
            .sienna-widget * {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }
            body > *:not(.page) { display: none !important; }
            .page { padding: 0; max-width: none; }
            .report { box-shadow: none; border-color: #ddd; }
            .summary-card, .disclaimer-box { break-inside: avoid; page-break-inside: avoid; }
            table { break-inside: auto; page-break-inside: auto; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        @media (max-width: 900px) {
            .report-head-inner, .report-meta, .footer-band { flex-direction: column; align-items: stretch; }
            .meta-row { gap: 8px; }
        }
    </style>
</head>
<body>
@php
    $selectedClass = !empty($selectedClassId) ? $classrooms->firstWhere('id', $selectedClassId) : null;
    $totalStudents = $students->count();
    $statusRows = [];

    foreach ($students as $student) {
        $latest = ($requestsByStudent[$student->id] ?? collect())->first();
        $studentClass = $classrooms->firstWhere('id', optional($student->studentInfo)->class_id);
        $status = optional($latest)->status ?? 'not sent';
        $badge = $status === 'submitted' ? 'success' : ($status === 'expired' ? 'secondary' : ($status === 'cancelled' ? 'dark' : 'warning'));

        $statusRows[] = [
            'student' => $student,
            'class' => $studentClass,
            'latest' => $latest,
            'status' => $status,
            'badge' => $badge,
        ];
    }
    $submittedCount = collect($statusRows)->where('status', 'submitted')->count();
    $notSentCount = $totalStudents - $submittedCount;
@endphp
<div class="page">
    <div class="topbar no-print">
        <a href="{{ route('professor.evaluation') }}" class="btn-tool">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <button type="button" class="btn-tool primary" id="printBtn">
            <i class="fa fa-print"></i> Print Report
        </button>
    </div>

    <div class="report" id="printReport">
        <div class="report-head">
            <div class="report-head-band"></div>
            <div class="report-head-inner">
                <div class="brand">
                    <div class="brand-mark">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
                    </div>
                    <div class="brand-copy">
                        <div class="eyebrow">Polytechnic University of the Philippines - OJT Information Management System</div>
                        <div class="brand-title">Evaluation Monitoring Report</div>
                        <div class="brand-sub">
                            {{ $selectedClass ? $selectedClass->room : 'Selected Class' }} | College of Engineering and Technology
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="report-meta">
            <div class="meta-row">
                <div class="meta-item"><span>Scope:</span> <strong>{{ $selectedClass ? $selectedClass->room : 'Selected class' }}</strong></div>
                <div class="meta-item"><span>Students:</span> <strong>{{ $totalStudents }}</strong></div>
                <div class="meta-item"><span>Submitted:</span> <strong>{{ $submittedCount }}</strong></div>
                <div class="meta-item"><span>Not Sent:</span> <strong>{{ $notSentCount }}</strong></div>
            </div>
            <div class="meta-generated">Generated: {{ $printedAt->format('M d, Y h:i A') }}</div>
        </div>

        <div class="section-label">
            <div>Student Evaluation Details</div>
        </div>

        <div class="table-wrap">
            <table>
                <colgroup>
                    <col style="width:10%;">
                    <col style="width:22%;">
                    <col style="width:18%;">
                    <col style="width:16%;">
                    <col style="width:18%;">
                    <col style="width:16%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Student No.</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Supervisor Email</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statusRows as $row)
                        <tr>
                            <td>{{ optional($row['student']->studentInfo)->studentNum ?: '-' }}</td>
                            <td>
                                <div class="student-name">{{ $row['student']->full_name }}</div>
                            </td>
                            <td>{{ $row['class'] ? $row['class']->room : '-' }}</td>
                            <td><span class="badge {{ $row['badge'] }}">{{ strtoupper($row['status']) }}</span></td>
                            <td>{{ optional($row['latest'])->supervisor_email ?: '-' }}</td>
                            <td>{{ optional(optional($row['latest'])->submitted_at)->format('M d, Y h:i A') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No students found for the selected class.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer-block">
            <div class="disclaimer">
                <div class="disclaimer-box">
                    <h3>Disclaimer</h3>
                    <p>This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</p>
                </div>
            </div>

            <div class="footer-band">
                <div class="left">
                    <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
                    <span>Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                </div>
                <div class="right">Ref: EVL-RPT-{{ now()->year }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('printBtn')?.addEventListener('click', function () {
        window.print();
    });
</script>
</body>
</html>
