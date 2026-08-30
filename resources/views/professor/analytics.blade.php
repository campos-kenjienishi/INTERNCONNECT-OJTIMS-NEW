<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Professor Analytics</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <script>
        (function(){
            try {
                if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e){}
        })();
    </script>

    <link rel="stylesheet" href="{{ vasset('css/professor/analytics.css') }}">
</head>

<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div> 
<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($data->profile_photo) && $data->profile_photo)
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name }}</span>
            <span class="user-role">Professor</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/professor/home') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Dashboard</span>
            <span class="tooltip-label">Dashboard</span>
        </a>
        <a href="{{ url('/professor/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-check"></i></span>
            <span class="nav-label">Requirement Status</span>
            <span class="tooltip-label">Requirement Status</span>
        </a>
        <a href="{{ url('/professor/analytics') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
        </a>
        <a href="{{ url('/reportsExpiredProf') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/professor/maintain') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cogs"></i></span>
            <span class="nav-label">Maintenance</span>
            <span class="tooltip-label">Maintenance</span>
        </a>
        <a href="{{ url('/professor/evaluation') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-star-half-alt"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/logout') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-sign-out-alt"></i></span>
            <span class="nav-label">Log Out</span>
            <span class="tooltip-label">Log Out</span>
        </a>
    </div>
</div>

<div class="main-content" id="mainContent">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode"><i class="fa fa-moon" id="darkmodeIcon"></i></button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge"><i class="fa fa-chalkboard-teacher"></i> Professor Portal</div>
        </div>
    </div>

    <div class="page-content">
        <div class="page-header">
            <div>
                <h1>Professor <span>Analytics</span></h1>
                <p>Advising load, student standing, and file requirement trends for your advisees.</p>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right" style="font-size:10px;"></i>
                    <span>Analytics</span>
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:6px;">
                <button type="button" id="printBtn" aria-label="Print analytics report" class="analytics-print-btn">
                    <i class="fa fa-print"></i>
                    <span>Print Report</span>
                </button>
                <div class="topbar-badge">
                    <i class="fa fa-sync-alt"></i> Updated {{ now()->format('M d, Y') }}
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $totalStudents }}</div>
                    <div class="stat-name">Total Advisees</div>
                </div>
                <div class="stat-icon-box red"><i class="fa fa-users"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $classrooms->count() }}</div>
                    <div class="stat-name">Active Classes</div>
                </div>
                <div class="stat-icon-box blue"><i class="fa fa-clipboard"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $approvedStudents }}</div>
                    <div class="stat-name">Approved Students</div>
                </div>
                <div class="stat-icon-box green"><i class="fa fa-user-check"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $pendingApprovals }}</div>
                    <div class="stat-name">Pending Students</div>
                </div>
                <div class="stat-icon-box amber"><i class="fa fa-hourglass-half"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $deniedStudents }}</div>
                    <div class="stat-name">Denied Students</div>
                </div>
                <div class="stat-icon-box red"><i class="fa fa-user-times"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $templateCount }}</div>
                    <div class="stat-name">File Categories</div>
                </div>
                <div class="stat-icon-box purple"><i class="fa fa-folder-open"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $fileApproved }}</div>
                    <div class="stat-name">Approved Files</div>
                </div>
                <div class="stat-icon-box green"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $filePending }}</div>
                    <div class="stat-name">Pending Files</div>
                </div>
                <div class="stat-icon-box amber"><i class="fa fa-file-alt"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $fileDenied }}</div>
                    <div class="stat-name">Denied Files</div>
                </div>
                <div class="stat-icon-box red"><i class="fa fa-times-circle"></i></div>
            </div>
        </div>

        <div class="analytics-grid">
            <div class="panel">
                <div class="panel-head">
                    <h2>Student Standing</h2>
                    <p>Adviser-side breakdown of student approval status</p>
                </div>
                <div class="panel-body">
                    <div class="metric-list">
                        @php
                            $studentMetrics = [
                                ['label' => 'Approved students', 'count' => $approvedStudents, 'class' => 'green'],
                                ['label' => 'Pending students', 'count' => $pendingApprovals, 'class' => 'amber'],
                                ['label' => 'Denied students', 'count' => $deniedStudents, 'class' => 'red'],
                                ['label' => 'Inactive students', 'count' => $inactiveStudents, 'class' => 'blue'],
                            ];
                            $studentTotal = max(1, $approvedStudents + $pendingApprovals + $deniedStudents + $inactiveStudents);
                        @endphp
                        @foreach($studentMetrics as $metric)
                            <div>
                                <div class="metric-row">
                                    <div>
                                        <div class="metric-title">{{ $metric['label'] }}</div>
                                        <div class="metric-meta">{{ $metric['count'] }} students</div>
                                    </div>
                                    <div class="metric-percent">{{ round(($metric['count'] / $studentTotal) * 100) }}%</div>
                                </div>
                                <div class="track"><div class="fill {{ $metric['class'] }}" data-width="{{ round(($metric['count'] / $studentTotal) * 100) }}"></div></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h2>Class Overview</h2>
                    <p>Student load and submitted evaluation activity per class</p>
                </div>
                <div class="panel-body">
                    <div class="metric-list">
                        @forelse($classAnalytics as $room)
                            <div>
                                <div class="metric-row">
                                    <div>
                                        <div class="metric-title">{{ $room['label'] }}</div>
                                        <div class="metric-meta">{{ $room['total_students'] }} students | {{ $room['submitted'] }} submitted</div>
                                    </div>
                                    <div class="metric-percent">{{ $room['completion'] }}%</div>
                                </div>
                                <div class="track"><div class="fill green" data-width="{{ $room['completion'] }}"></div></div>
                            </div>
                        @empty
                            <div class="metric-meta">No classes found for your account.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h2>Requirement Review</h2>
                    <p>File requirement statuses for your advisees</p>
                </div>
                <div class="panel-body">
                    <div class="metric-list">
                        @php
                            $fileMetrics = [
                                ['label' => 'Approved files', 'count' => $fileApproved, 'class' => 'green'],
                                ['label' => 'Pending files', 'count' => $filePending, 'class' => 'amber'],
                                ['label' => 'Denied files', 'count' => $fileDenied, 'class' => 'red'],
                            ];
                            $fileTotal = max(1, $fileApproved + $filePending + $fileDenied);
                        @endphp
                        @foreach($fileMetrics as $metric)
                            <div>
                                <div class="metric-row">
                                    <div>
                                        <div class="metric-title">{{ $metric['label'] }}</div>
                                        <div class="metric-meta">{{ $metric['count'] }} files</div>
                                    </div>
                                    <div class="metric-percent">{{ round(($metric['count'] / $fileTotal) * 100) }}%</div>
                                </div>
                                <div class="track"><div class="fill {{ $metric['class'] }}" data-width="{{ round(($metric['count'] / $fileTotal) * 100) }}"></div></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div data-ai-insight-card class="panel full">
                <div class="panel-head" style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa fa-robot"></i>
                        </div>
                        <div>
                            <h2>AI Analytics Insight</h2>
                            <p>Summary generated from the current dashboard metrics</p>
                        </div>
                    </div>
                    @php
                        $analyticsAiSource = $analyticsInsights['source'] ?? 'fallback';
                        $analyticsAiLabel = $analyticsAiSource === 'gemini'
                            ? 'Gemini AI'
                            : ($analyticsAiSource === 'openai' ? 'OpenAI' : 'Internal Insight');
                    @endphp
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-left:auto;">
                        <button type="button" data-ai-insight-button data-ai-context="analyticsAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
                            <i class="fa fa-magic"></i> Generate AI Insight
                        </button>
                        <span style="display:inline-flex; align-items:center; gap:7px; border:1px solid #fecaca; background:#fff5f5; color:#b91c1c; border-radius:999px; padding:8px 13px; font-size:12px; font-weight:800;">
                            <i class="fa fa-brain"></i>
                            <span data-ai-badge>{{ $analyticsAiLabel }}</span>
                        </span>
                    </div>
                </div>
                <div data-ai-result-panel class="panel-body" style="display:none;">
                    @if(($analyticsInsights['source'] ?? '') === 'fallback')
                        <div data-ai-notice style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;">
                            <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                            <div><strong>Gemini is temporarily unavailable.</strong> <span data-ai-notice-text>{{ $analyticsInsights['availability']['message'] ?? 'Internal insight is shown for now. Try again in a few minutes, or later if the daily free-tier quota was reached.' }}</span></div>
                        </div>
                    @endif
                    <div data-ai-status style="display:none; margin-bottom:12px; font-size:12px; color:#888;"></div>
                    <p data-ai-summary style="font-size:14px; line-height:1.7; color:#374151; margin-bottom:16px;">{{ $analyticsInsights['summary'] ?? 'No insight available.' }}</p>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#ef4444; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul data-ai-findings style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No key findings available.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#ef4444; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#ef4444; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                            <ul data-ai-actions style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['recommendations'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No actions suggested.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    @php
                        $analyticsPromptSuggestions = [
                            ['label' => 'Priorities', 'question' => 'What should I prioritize first based on this professor analytics dashboard?'],
                            ['label' => 'Risk', 'question' => 'What risk level does this advising dashboard suggest and why?'],
                            ['label' => 'Action plan', 'question' => 'Create a short action plan based on the current professor analytics metrics.'],
                        ];

                        if (($pendingApprovals ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Pending approvals', 'question' => 'How should I handle the pending student approvals shown here?'];
                        }

                        if (($filePending ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Pending files', 'question' => 'What should I do about pending file requirements?'];
                        }

                        if (($requestTotal ?? 0) > 0 && ($submittedRequests ?? 0) < ($requestTotal ?? 0)) {
                            $analyticsPromptSuggestions[] = ['label' => 'Evaluations', 'question' => 'What does the evaluation completion data suggest I should follow up on?'];
                        }
                    @endphp
                    <div style="margin-top:18px; border-top:1px solid #f0f0f0; padding-top:16px;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                            <div>
                                <div style="font-size:13px; font-weight:800; color:#1f2937;">Ask AI about this dashboard</div>
                                <div style="font-size:12px; color:#888; margin-top:2px;">Click a suggested prompt to ask instantly, or type your own question and press Ask.</div>
                            </div>
                            <div style="display:grid; gap:7px;">
                                <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:.6px;">Suggested prompts</div>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    @foreach($analyticsPromptSuggestions as $suggestion)
                                        <button type="button" class="analytics-ai-quick-question" data-question="{{ $suggestion['question'] }}" style="display:inline-flex; align-items:center; gap:7px; border:1.5px solid #fecaca; background:#fff; color:#991b1b; border-radius:9px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(220,38,38,0.08);"><i class="fa fa-bolt" style="width:18px; height:18px; border-radius:6px; background:#fee2e2; display:inline-flex; align-items:center; justify-content:center; font-size:10px; color:#dc2626;"></i>{{ $suggestion['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div style="display:grid; gap:10px;">
                            <textarea id="analyticsAiQuestionInput" rows="3" maxlength="500" placeholder="Ask a question about this analytics dashboard..." style="width:100%; resize:vertical; min-height:86px; border:1.5px solid #e5e7eb; border-radius:10px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; outline:none; line-height:1.6;"></textarea>
                            <button type="button" id="analyticsAskAiBtn" style="justify-self:end; display:inline-flex; align-items:center; gap:8px; border:none; border-radius:10px; background:linear-gradient(135deg,#10b981,#059669); color:#fff; padding:11px 18px; font-size:13px; font-weight:800; white-space:nowrap; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
                                <i class="fa fa-paper-plane"></i> Ask
                            </button>
                        </div>
                        <div id="analyticsAiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#888;"></div>
                        <div id="analyticsAiAnswerBox" style="display:none; margin-top:12px; background:#fff; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; margin-bottom:8px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; text-transform:uppercase; letter-spacing:.4px;">AI Answer</div>
                                <div id="analyticsAiAnswerSource" style="font-size:11px; font-weight:700; color:#888;"></div>
                            </div>
                            <div id="analyticsAiAnswerText" style="font-size:13px; color:#333; line-height:1.7;"></div>
                            <ul id="analyticsAiAnswerSteps" style="margin:10px 0 0; padding-left:18px; color:#444; font-size:13px; line-height:1.65;"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $analyticsPrintStudentMetrics = [
        ['label' => 'Approved students', 'count' => $approvedStudents, 'class' => 'green'],
        ['label' => 'Pending students', 'count' => $pendingApprovals, 'class' => 'amber'],
        ['label' => 'Denied students', 'count' => $deniedStudents, 'class' => 'red'],
        ['label' => 'Inactive students', 'count' => $inactiveStudents, 'class' => 'blue'],
    ];
    $analyticsPrintFileMetrics = [
        ['label' => 'Approved files', 'count' => $fileApproved, 'class' => 'green'],
        ['label' => 'Pending files', 'count' => $filePending, 'class' => 'amber'],
        ['label' => 'Denied files', 'count' => $fileDenied, 'class' => 'red'],
    ];
@endphp

<div id="print-area-wrapper">
    <div class="analytics-print" style="font-family:'Poppins',Arial,sans-serif; background:#fff; color:#111827;">
        <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); padding:0;">
            <div style="background:rgba(255,255,255,0.12); height:4px;"></div>
            <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                <div style="width:50px; height:50px; background:rgba(255,255,255,0.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,0.25);">
                    <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);" alt="PUP">
                </div>
                <div style="flex:1;">
                    <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines - Taguig Campus</div>
                    <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">Professor Analytics Report</div>
                    <div style="font-size:8.5px; color:rgba(255,255,255,0.6); margin-top:3px;">PUP Taguig Campus</div>
                </div>
            </div>
            <div style="background:rgba(0,0,0,0.15); height:3px;"></div>
        </div>

        <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
            <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                    <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                    <span style="color:#6b7280;">Professor:</span>
                    <strong style="color:#111827;">{{ $data->full_name }}</strong>
                </div>
                <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                    <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                    <span style="color:#6b7280;">Students:</span>
                    <strong style="color:#111827;">{{ $totalStudents }}</strong>
                </div>
                <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                    <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                    <span style="color:#6b7280;">Submitted:</span>
                    <strong style="color:#111827;">{{ $submittedRequests }}</strong>
                </div>
                <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                    <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                    <span style="color:#6b7280;">Generated:</span>
                    <strong style="color:#111827;">{{ now()->format('M d, Y h:i A') }}</strong>
                </div>
            </div>
            <div style="font-size:8.5px; color:#9ca3af;">Analytics snapshot</div>
        </div>

        <div style="padding:14px 22px 0 22px;">
            <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:12px;">
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                    <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Total Advisees</div>
                    <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">{{ $totalStudents }}</div>
                </div>
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                    <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Active Classes</div>
                    <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">{{ $classrooms->count() }}</div>
                </div>
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                    <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Submitted Evaluations</div>
                    <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">{{ $submittedRequests }}</div>
                </div>
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                    <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">File Categories</div>
                    <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">{{ $templateCount }}</div>
                </div>
            </div>
        </div>

        <div style="padding:14px 22px 0 22px;">
            <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px;">
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                    <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Student Standing</div>
                    <table style="width:100%; border-collapse:collapse; font-size:10px;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th>
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyticsPrintStudentMetrics as $index => $metric)
                                <tr style="background:{{ $index % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#111827;">{{ $metric['label'] }}</td>
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">{{ $metric['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                    <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Class Overview</div>
                    <table style="width:100%; border-collapse:collapse; font-size:10px;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Class</th>
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th>
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Submitted</th>
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classAnalytics as $index => $room)
                                <tr style="background:{{ $index % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#111827;">{{ $room['label'] }}</td>
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">{{ $room['total_students'] }}</td>
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">{{ $room['submitted'] }}</td>
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#991b1b; font-weight:700;">{{ $room['completion'] }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="padding:8px; border:1px solid #e5e7eb; text-align:center;">No classes found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="padding:14px 22px 0 22px;">
            <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px;">
                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                    <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Requirement Review</div>
                    <table style="width:100%; border-collapse:collapse; font-size:10px;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th>
                                <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyticsPrintFileMetrics as $index => $metric)
                                <tr style="background:{{ $index % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#111827;">{{ $metric['label'] }}</td>
                                    <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">{{ $metric['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                    <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Analytics Insight</div>
                    <div style="font-size:11px; color:#374151; line-height:1.7;">{{ $analyticsInsights['summary'] ?? 'This printed report focuses on the current adviser snapshot, student standing, class overview, and file requirements.' }}</div>
                </div>
            </div>
        </div>

        <div style="padding:18px 22px 12px 22px;">
            <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                    <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Disclaimer</div>
                    <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</div>
                </div>
            </div>
        </div>

        <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:6px;">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP" style="width:13px; height:13px; object-fit:contain; opacity:.7; filter:brightness(2);">
                <span style="font-size:8px; color:rgba(255,255,255,.75); font-weight:500;">© 1998–{{ now()->year }} <strong style="color:#fca5a5;">Polytechnic University of the Philippines</strong> — InternConnect OJT IMS</span>
            </div>
            <div style="font-size:8px; color:rgba(255,255,255,.5);">Ref: PROF-ANA-{{ now()->year }}</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.professorAnalyticsConfig = {
        subtitle: @json($data->full_name),
        summary: @json($analyticsInsights['summary'] ?? 'No insight available.'),
        generatedAt: @json(now()->format('M d, Y h:i A')),
        totalStudents: @json($totalStudents),
        classCount: @json($classrooms->count()),
        submittedRequests: @json($submittedRequests),
        templateCount: @json($templateCount),
        classAnalytics: @json($classAnalytics),
        requestAnalytics: @json($requestAnalytics),
        fileMetrics: @json($analyticsPrintFileMetrics),
        approvedStudents: @json($approvedStudents),
        pendingApprovals: @json($pendingApprovals),
        deniedStudents: @json($deniedStudents),
        inactiveStudents: @json($inactiveStudents),
        payload: {
            total_students: @json($totalStudents),
            approved_students: @json($approvedStudents),
            pending_approvals: @json($pendingApprovals),
            denied_students: @json($deniedStudents),
            inactive_students: @json($inactiveStudents),
            request_total: @json($requestTotal),
            submitted_requests: @json($submittedRequests),
            template_count: @json($templateCount),
            file_pending: @json($filePending),
            file_approved: @json($fileApproved),
            file_denied: @json($fileDenied),
            class_analytics: @json($classAnalytics),
            request_analytics: @json($requestAnalytics),
            monthly_activity: @json($monthlyActivity)
        },
        insight: @json($analyticsInsights ?? []),
        dataUrl: @json(route('professor.analytics.data')),
        drilldownUrl: @json(route('professor.analytics.drilldown')),
        printUrl: @json(route('professor.analytics.print')),
        aiAskUrl: @json(route('reports.ai.ask')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/professor/analytics.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>