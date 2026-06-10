<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Analytics Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --red: #dc2626; --red-dark: #7f0000; --line: #e5e7eb; --text: #111827; --muted: #6b7280; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #f3f4f6; color: var(--text); font-family: 'Poppins', sans-serif; }
        body { min-height: 100vh; }
        .sienna-widget,
        .sienna-accessibility,
        [class*="sienna"],
        [id*="sienna"],
        [class*="accessibility"],
        [id*="accessibility"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        a { color: inherit; text-decoration: none; }
        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px;
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
            background: #fff;
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
        .brand-mark img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
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
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            padding: 14px 22px;
            border-bottom: 1px solid var(--line);
            background: #f9fafb;
        }
        .report-meta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            font-size: 11px;
            color: #374151;
            line-height: 1.6;
        }
        .report-meta-group strong { color: #111827; }
        .report-meta-note { font-size: 10px; color: #9ca3af; }
        .report-body {
            padding: 18px 22px 22px;
        }
        .summary-band {
            border: 1px solid var(--line);
            border-left: 4px solid var(--red);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #111827;
            margin-bottom: 10px;
            padding-left: 8px;
            border-left: 3px solid var(--red);
        }
        .mini-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }
        .mini-card {
            background: #fafafa;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px 12px;
        }
        .mini-value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
            color: #111827;
        }
        .mini-label {
            margin-top: 4px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6b7280;
            font-weight: 600;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 14px;
        }
        .report-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px;
            page-break-inside: avoid;
        }
        .report-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .report-card th,
        .report-card td {
            border: 1px solid var(--line);
            padding: 7px 8px;
            vertical-align: top;
        }
        .report-card thead tr { background: #f9fafb; }
        .report-card .table-title {
            margin-bottom: 10px;
        }
        .report-card .table-title .section-label {
            margin-bottom: 0;
        }
        .insight-text {
            font-size: 11px;
            line-height: 1.7;
            color: #374151;
        }
        .insight-list {
            margin: 10px 0 0;
            padding-left: 18px;
            color: #374151;
            line-height: 1.65;
            font-size: 11px;
        }
        .footer-wrap {
            margin-top: 16px;
            border-top: 1px dashed #d1d5db;
            padding-top: 14px;
        }
        .disclaimer {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-left: 4px solid var(--red);
            border-radius: 8px;
            padding: 12px 14px;
        }
        .disclaimer h3 {
            margin: 0 0 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
        }
        .disclaimer p {
            margin: 0;
            font-size: 8.5px;
            line-height: 1.6;
            color: #4b5563;
        }
        .footer-band {
            margin-top: 10px;
            background: #7f0000;
            padding: 8px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .footer-band span {
            font-size: 8px;
            color: rgba(255,255,255,.75);
            font-weight: 500;
        }
        .footer-band small {
            font-size: 8px;
            color: rgba(255,255,255,.5);
        }
        .system-line {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .system-line img {
            width: 13px;
            height: 13px;
            object-fit: contain;
            opacity: .7;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            color: #991b1b;
            background: #fef2f2;
        }
        @media print {
            @page { size: A4 portrait; margin: 14mm; }
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .sienna-widget,
            .sienna-accessibility,
            [class*="sienna"],
            [id*="sienna"],
            [class*="accessibility"],
            [id*="accessibility"] {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
            body > *:not(.page) { display: none !important; }
            .page { padding: 0 !important; max-width: none; }
            .report { box-shadow: none; }
            .report, .report * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .grid-2 { break-inside: auto; }
        }
        @media (max-width: 960px) {
            .topbar { flex-direction: column; align-items: stretch; }
            .report-head-inner, .report-meta, .footer-band { flex-direction: column; align-items: stretch; }
            .mini-cards,
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar no-print">
            <a href="{{ url('/professor/analytics') }}" class="btn-tool">
                <i class="fa fa-arrow-left"></i>
                Back to Analytics
            </a>
            <button type="button" class="btn-tool primary" id="printBtn">
                <i class="fa fa-print"></i>
                Print Report
            </button>
        </div>

        <div class="report" id="report">
            <div class="report-head">
                <div class="report-head-band"></div>
                <div class="report-head-inner">
                    <div class="brand">
                        <div class="brand-mark">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
                        </div>
                        <div class="brand-copy">
                            <div class="eyebrow">Polytechnic University of the Philippines - OJT Information Management System</div>
                            <div class="brand-title">Professor Analytics Report</div>
                            <div class="brand-sub">{{ $data->full_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="report-meta">
                <div class="report-meta-group">
                    <div><span style="color:#6b7280;">Scope:</span> <strong>{{ $data->full_name }}</strong></div>
                    <div><span style="color:#6b7280;">Students:</span> <strong>{{ $totalStudents }}</strong></div>
                    <div><span style="color:#6b7280;">Submitted:</span> <strong>{{ $submittedRequests }}</strong></div>
                    <div><span style="color:#6b7280;">Generated:</span> <strong>{{ now()->format('M d, Y h:i A') }}</strong></div>
                </div>
                <div class="report-meta-note">Analytics snapshot</div>
            </div>

            <div class="report-body">
                <div class="summary-band">
                    <div class="section-label" style="margin-bottom:12px;">Report Summary</div>
                    <div class="mini-cards">
                        <div class="mini-card">
                            <div class="mini-value">{{ $totalStudents }}</div>
                            <div class="mini-label">Total Advisees</div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-value">{{ $classrooms->count() }}</div>
                            <div class="mini-label">Active Classes</div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-value">{{ $submittedRequests }}</div>
                            <div class="mini-label">Submitted Evaluations</div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-value">{{ $templateCount }}</div>
                            <div class="mini-label">File Categories</div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="report-card">
                        <div class="table-title">
                            <div class="section-label">Student Standing</div>
                            <div style="font-size:12px;color:#6b7280;">Breakdown of the current student approval status.</div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Approved students</td><td>{{ $approvedStudents }}</td></tr>
                                <tr><td>Pending students</td><td>{{ $pendingApprovals }}</td></tr>
                                <tr><td>Denied students</td><td>{{ $deniedStudents }}</td></tr>
                                <tr><td>Inactive students</td><td>{{ $inactiveStudents }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="report-card">
                        <div class="table-title">
                            <div class="section-label">Class Overview</div>
                            <div style="font-size:12px;color:#6b7280;">Student load and submitted evaluation activity per class.</div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Students</th>
                                    <th>Submitted</th>
                                    <th>Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classAnalytics as $room)
                                    <tr>
                                        <td>{{ $room['label'] }}</td>
                                        <td>{{ $room['total_students'] }}</td>
                                        <td>{{ $room['submitted'] }}</td>
                                        <td><span class="status-pill">{{ $room['completion'] }}%</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align:center; color:#9ca3af;">No classes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="report-card">
                        <div class="table-title">
                            <div class="section-label">Requirement Review</div>
                            <div style="font-size:12px;color:#6b7280;">Current file requirement status for the professor's advisees.</div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Approved files</td><td>{{ $fileApproved }}</td></tr>
                                <tr><td>Pending files</td><td>{{ $filePending }}</td></tr>
                                <tr><td>Denied files</td><td>{{ $fileDenied }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="report-card">
                        <div class="table-title">
                            <div class="section-label">Analytics Insight</div>
                            <div style="font-size:12px;color:#6b7280;">Quick read of the current advising snapshot.</div>
                        </div>
                        <div class="insight-text">{{ $analyticsInsights['summary'] ?? 'No insight available.' }}</div>
                        <ul class="insight-list">
                            @forelse(($analyticsInsights['key_findings'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @empty
                                <li>No key findings available.</li>
                            @endforelse
                        </ul>
                        <ul class="insight-list">
                            @forelse(($analyticsInsights['watchouts'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @empty
                                <li>No major watchouts detected.</li>
                            @endforelse
                        </ul>
                        <ul class="insight-list">
                            @forelse(($analyticsInsights['recommendations'] ?? []) as $item)
                                <li>{{ $item }}</li>
                            @empty
                                <li>No actions suggested.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="footer-wrap">
                    <div class="disclaimer">
                        <h3>Disclaimer</h3>
                        <p>This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</p>
                    </div>
                    <div class="footer-band">
                        <div class="system-line">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
                            <span>Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                        </div>
                        <small>Ref: ANA-RPT-{{ now()->year }}</small>
                    </div>
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
