<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Requirement Status</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
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
    <link rel="stylesheet" href="{{ vasset('css/professor/requirement-status.css') }}">
</head>

<body>
@php
    $totalStudents = $allStudentStatuses->count();
    $completeStudents = $allStudentStatuses->where('missingCount', 0)->count();
    $categoryCount = isset($categoryNames) ? $categoryNames->count() : ($categories->count() + 1);
    $averageCompletion = $totalStudents > 0 ? round($allStudentStatuses->avg('completion')) : 0;
    $studentsWithMissing = $allStudentStatuses->filter(fn ($status) => $status['missingCount'] > 0)->count();
    $studentsWithPending = $allStudentStatuses->filter(fn ($status) => $status['pendingCount'] > 0)->count();
    $submittedRequirements = (int) $allStudentStatuses->sum('submittedCount');
    $missingRequirements = (int) $allStudentStatuses->sum('missingCount');
    $approvedRequirements = (int) $allStudentStatuses->sum('approvedCount');
    $pendingRequirements = (int) $allStudentStatuses->sum('pendingCount');
    $deniedRequirements = (int) $allStudentStatuses->sum('deniedCount');
    $topMissingRequirements = $allStudentStatuses
        ->flatMap(fn ($status) => $status['missing']->values())
        ->countBy()
        ->sortDesc()
        ->take(5)
        ->map(fn ($count, $label) => ['label' => $label, 'count' => $count])
        ->values();
    $printStatuses = ($activeView === 'overview'
        ? $allStudentStatuses
        : $allStudentStatuses->filter(fn ($status) => $status[$activeView]->count() > 0)
    )->values()->map(function ($status) {
        return [
            'studentName' => $status['student']->full_name,
            'studentNumber' => $status['student']->studentNum ?? 'No student no.',
            'completion' => $status['completion'],
            'submittedCount' => $status['submittedCount'],
            'missingCount' => $status['missingCount'],
            'approvedCount' => $status['approvedCount'],
            'pendingCount' => $status['pendingCount'],
            'deniedCount' => $status['deniedCount'],
            'passed' => $status['passed']->values()->all(),
            'approved' => $status['approved']->values()->all(),
            'pending' => $status['pending']->values()->all(),
            'denied' => $status['denied']->values()->all(),
            'missing' => $status['missing']->values()->all(),
        ];
    });
@endphp
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar" id="sidebar">
    <a href="{{ url('/professor/home') }}" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">Intern<span>Connect</span></div>
            <div class="sidebar-brand-sub">OJTIMS</div>
        </div>
    </a>
    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(!empty($data->profile_photo))
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name ?? 'Professor' }}</span>
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
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-clipboard-check"></i></span>
            <span class="nav-label">Requirement Status</span>
            <span class="tooltip-label">Requirement Status</span>
        </a>
        <a href="{{ url('/professor/analytics') }}" class="nav-item">
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
            <button class="menu-toggle" id="menuToggle">
                <i class="fa fa-bars"></i>
            </button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
            </button>
            <span class="topbar-title">
                On-the-Job Training <span>Information Management System</span>
            </span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-chalkboard-teacher"></i>
                Professor Portal
            </div>
        </div>
    </div>

    <main class="page-content">
        <div class="page-header">
            <div>
                <h1>Requirement <span>Status</span></h1>
                <p>{{ $course->course }} | {{ $course->room }} | {{ $course->school_year_start && $course->school_year_end ? $course->school_year_start . ' - ' . $course->school_year_end : 'School year not set' }}</p>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ route('professor.requirementStatus.classes') }}">Classes</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Requirement Status</span>
                </div>
            </div>
            <div class="toolbar">
                <a href="{{ route('professor.requirementStatus.classes') }}" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Classes
                </a>
                <button type="button" class="btn-tool primary" id="printReportBtn"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>

        <div class="view-tabs">
            <a href="{{ route('professor.requirementStatus', $course->id) }}" class="view-tab tab-overview {{ $activeView === 'overview' ? 'active' : '' }}"><i class="fa fa-table"></i> Overview</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'approved']) }}" class="view-tab tab-approved {{ $activeView === 'approved' ? 'active' : '' }}"><i class="fa fa-check-circle"></i> Approved</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'pending']) }}" class="view-tab tab-pending {{ $activeView === 'pending' ? 'active' : '' }}"><i class="fa fa-clock"></i> Pending</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'denied']) }}" class="view-tab tab-denied {{ $activeView === 'denied' ? 'active' : '' }}"><i class="fa fa-times-circle"></i> Denied</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'missing']) }}" class="view-tab tab-missing {{ $activeView === 'missing' ? 'active' : '' }}"><i class="fa fa-exclamation-circle"></i> Missing</a>
        </div>

        <div class="summary-grid">
            <div class="summary-card"><div class="summary-num color-blue">{{ $totalStudents }}</div><div class="summary-label">Students</div></div>
            <div class="summary-card"><div class="summary-num color-purple">{{ $categoryCount }}</div><div class="summary-label">Required Categories</div></div>
            <div class="summary-card"><div class="summary-num color-green">{{ $completeStudents }}</div><div class="summary-label">Complete Students</div></div>
            <div class="summary-card"><div class="summary-num color-amber">{{ $averageCompletion }}%</div><div class="summary-label">Average Completion</div></div>
        </div>

        @if(!empty($requirementInsights))
            <section data-ai-insight-card class="report-card" style="margin-bottom:18px; border-left:4px solid var(--red);">
                <div class="report-head" style="align-items:center;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:var(--red); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa fa-robot"></i>
                        </div>
                        <div class="report-head-left">
                            <h2>AI Requirement Insight</h2>
                            <p>Generated from the current class requirement status</p>
                        </div>
                    </div>
                    @php
                        $requirementAiSource = $requirementInsights['source'] ?? 'fallback';
                        $requirementAiLabel = $requirementAiSource === 'gemini'
                            ? 'Gemini AI'
                            : ($requirementAiSource === 'openai' ? 'OpenAI' : 'Internal Insight');
                    @endphp
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <button type="button" data-ai-insight-button data-ai-context="requirementAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
                            <i class="fa fa-magic"></i> Generate AI Insight
                        </button>
                        <div style="display:inline-flex; align-items:center; gap:6px; background:#fff5f5; border:1px solid #fecaca; color:var(--red-dark); border-radius:999px; padding:7px 12px; font-size:12px; font-weight:800;">
                            <i class="fa fa-brain"></i> <span data-ai-badge>{{ $requirementAiLabel }}</span>
                        </div>
                    </div>
                </div>
                <div data-ai-result-panel style="display:none; padding:0 20px 20px;">
                    @if(($requirementInsights['source'] ?? '') === 'fallback')
                        <div data-ai-notice style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;">
                            <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                            <div><strong>Gemini is temporarily unavailable.</strong> <span data-ai-notice-text>{{ $requirementInsights['availability']['message'] ?? 'Internal insight is shown for now. Try again in a few minutes, or later if the daily free-tier quota was reached.' }}</span></div>
                        </div>
                    @endif

                    <div data-ai-status style="display:none; margin-bottom:12px; font-size:12px; color:#777;"></div>
                    <p data-ai-summary style="font-size:14px; line-height:1.7; color:#374151; margin:0 0 16px;">{{ $requirementInsights['summary'] ?? 'No requirement insight available.' }}</p>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                        <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:800; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul data-ai-findings style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($requirementInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No key findings available.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:800; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($requirementInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:800; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                            <ul data-ai-actions style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($requirementInsights['recommendations'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No actions suggested.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    @php
                        $requirementPromptSuggestions = [
                            ['label' => 'Priorities', 'question' => 'What should I prioritize first in this requirement status report?'],
                            ['label' => 'Risk', 'question' => 'What risks does this requirement status report show?'],
                            ['label' => 'Action plan', 'question' => 'Create a short action plan for this class requirement status.'],
                        ];

                        if (($missingRequirements ?? 0) > 0) {
                            $requirementPromptSuggestions[] = ['label' => 'Missing files', 'question' => 'Which missing requirement issues should I focus on first?'];
                        }

                        if (($pendingRequirements ?? 0) > 0) {
                            $requirementPromptSuggestions[] = ['label' => 'Pending review', 'question' => 'How should I handle the pending requirement submissions?'];
                        }

                        if (($deniedRequirements ?? 0) > 0) {
                            $requirementPromptSuggestions[] = ['label' => 'Denied files', 'question' => 'What should I do about denied requirement submissions?'];
                        }
                    @endphp

                    <div style="margin-top:18px; border-top:1px solid #f0f0f0; padding-top:16px;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                            <div>
                                <div style="font-size:13px; font-weight:800; color:#1f2937;">Ask AI about this requirement status</div>
                                <div style="font-size:12px; color:#888; margin-top:2px;">Click a suggested prompt to ask instantly, or type your own question and press Ask.</div>
                            </div>
                            <div style="display:grid; gap:7px;">
                                <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:.6px;">Suggested prompts</div>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    @foreach($requirementPromptSuggestions as $suggestion)
                                        <button type="button" class="requirement-ai-quick-question" data-question="{{ $suggestion['question'] }}" style="display:inline-flex; align-items:center; gap:7px; border:1.5px solid #fecaca; background:#fff; color:#991b1b; border-radius:9px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(220,38,38,0.08);"><i class="fa fa-bolt" style="width:18px; height:18px; border-radius:6px; background:#fee2e2; display:inline-flex; align-items:center; justify-content:center; font-size:10px; color:#dc2626;"></i>{{ $suggestion['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div style="display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:10px; align-items:start;">
                            <textarea id="requirementAiQuestionInput" rows="3" placeholder="Ask about missing requirements, pending reviews, denied files, completion, or next actions..." style="width:100%; min-height:82px; border:1px solid #e5e7eb; border-radius:12px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; resize:vertical;"></textarea>
                            <button type="button" id="requirementAskAiBtn" style="height:44px; border:none; border-radius:12px; padding:0 18px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; font-family:'Poppins',sans-serif; font-size:13px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 3px 10px rgba(16,185,129,0.25);"><i class="fa fa-paper-plane"></i> Ask</button>
                        </div>
                        <div id="requirementAiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#777;"></div>
                        <div id="requirementAiAnswer" style="display:none; margin-top:12px; background:#fff7f7; border:1px solid #fecaca; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:800; color:#b91c1c; margin-bottom:8px;">AI Answer</div>
                            <p id="requirementAiAnswerText" style="margin:0; color:#374151; line-height:1.7; font-size:13px;"></p>
                            <div id="requirementAiNextStepsWrap" style="display:none; margin-top:10px;">
                                <div style="font-size:12px; font-weight:800; color:#b91c1c; margin-bottom:6px;">Next Steps</div>
                                <ul id="requirementAiNextSteps" style="margin:0; padding-left:18px; color:#374151; line-height:1.6; font-size:13px;"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="report-card">
            <div class="report-head">
                <div class="report-head-left">
                    <h2>Student Requirement Matrix</h2>
                    <p>Submitted and missing requirements are based on professor file categories and Notarized MOA.</p>
                </div>
                <div class="report-head-meta">
                    <p class="report-generated">Generated: {{ now()->format('M d, Y h:i A') }}</p>
                    <div class="report-filter-row">
                        <form method="get" action="{{ route('professor.requirementStatus', $course->id) }}" class="entries-form">
                            @if($activeView !== 'overview')
                                <input type="hidden" name="view" value="{{ $activeView }}">
                            @endif
                            <label for="perPageSelect">Show</label>
                            <select id="perPageSelect" name="per_page" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                            <label for="perPageSelect">entries</label>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-wrap">
                <div class="table-loading-overlay" id="tableLoadingOverlay">
                    <div class="table-spinner-wrap">
                        <div class="table-spinner"></div>
                        <span>Loading requirement data...</span>
                    </div>
                </div>
                <table>
                    <colgroup>
                        <col style="width:24%;">
                        <col style="width:18%;">
                        <col style="width:30%;">
                        <col style="width:28%;">
                    </colgroup>
                    @if($activeView === 'overview')
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Progress</th>
                                <th>Requirements</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentStatuses as $status)
                            <tr>
                                <td>
                                    <div class="student-name">{{ $status['student']->full_name }}</div>
                                    <div class="student-meta">{{ $status['student']->studentNum ?? 'No student no.' }}</div>
                                </td>
                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress-label">
                                            <span>Completion</span>
                                            <strong>{{ $status['completion'] }}%</strong>
                                        </div>
                                        <div class="progress-track">
                                            <div class="progress-fill" style="width: {{ $status['completion'] }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="requirement-menu-row">
                                        <button type="button"
                                            class="requirement-menu-toggle submitted requirement-modal-trigger"
                                            data-modal-title="Submitted Requirements"
                                            data-modal-type="passed"
                                            data-empty-text="No submitted requirements yet."
                                            data-student-name="{{ e($status['student']->full_name) }}"
                                            data-requirements='@json($status["passed"]->values()->all())'>
                                            <i class="fa fa-eye"></i> <span class="label">View Submitted</span> <span class="count">{{ $status['submittedCount'] }}</span>
                                        </button>
                                        <button type="button"
                                            class="requirement-menu-toggle missing requirement-modal-trigger"
                                            data-modal-title="Missing Requirements"
                                            data-modal-type="missing"
                                            data-empty-text="Complete"
                                            data-student-name="{{ e($status['student']->full_name) }}"
                                            data-requirements='@json($status["missing"]->values()->all())'>
                                            <i class="fa fa-eye"></i> <span class="label">View Missing</span> <span class="count">{{ $status['missingCount'] }}</span>
                                        </button>
                                    </div>
                                    <div class="print-requirement-lists">
                                        <div class="details-section">
                                            <h3>Submitted</h3>
                                            <div class="requirement-grid">
                                                @forelse($status['passed'] as $item)
                                                    <div class="requirement-item passed">
                                                        <i class="fa fa-check"></i>
                                                        <span>{{ $item }}</span>
                                                    </div>
                                                @empty
                                                    <span class="empty-note">No submitted requirements yet.</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="details-section">
                                            <h3>Missing</h3>
                                            <div class="requirement-grid">
                                                @forelse($status['missing'] as $item)
                                                    <div class="requirement-item missing">
                                                        <i class="fa fa-exclamation-circle"></i>
                                                        <span>{{ $item }}</span>
                                                    </div>
                                                @empty
                                                    <div class="requirement-item passed">
                                                        <i class="fa fa-check-circle"></i>
                                                        <span>Complete</span>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-counts">
                                        <span class="status-badge approved">Approved {{ $status['approvedCount'] }}</span>
                                        <span class="status-badge pending">Pending {{ $status['pendingCount'] }}</span>
                                        <span class="status-badge denied">Denied {{ $status['deniedCount'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#999;">No students found for this class.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    @else
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>{{ ucfirst($activeView) }} Requirements</th>
                                <th>Count</th>
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentStatuses as $status)
                                <tr>
                                    <td>
                                        <div class="student-name">{{ $status['student']->full_name }}</div>
                                        <div class="student-meta">{{ $status['student']->studentNum ?? 'No student no.' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $focusedType = $activeView === 'approved' ? 'passed' : $activeView;
                                            $focusedIcon = $activeView === 'approved' ? 'check' : ($activeView === 'pending' ? 'clock' : ($activeView === 'missing' ? 'exclamation-circle' : 'times'));
                                            $focusedButtonClass = $activeView === 'approved' ? 'submitted' : $activeView;
                                        @endphp
                                        <button type="button"
                                            class="requirement-menu-toggle {{ $focusedButtonClass }} requirement-modal-trigger"
                                            data-modal-title="{{ ucfirst($activeView) }} Requirements"
                                            data-modal-type="{{ $focusedType }}"
                                            data-empty-text="No {{ $activeView }} requirements found."
                                            data-student-name="{{ e($status['student']->full_name) }}"
                                            data-requirements='@json($status[$activeView]->values()->all())'>
                                            <i class="fa fa-{{ $focusedIcon }}"></i> <span class="label">View {{ ucfirst($activeView) }}</span> <span class="count">{{ $status[$activeView]->count() }}</span>
                                        </button>
                                        <div class="print-requirement-lists">
                                            <div class="requirement-grid">
                                                @foreach($status[$activeView] as $item)
                                                    <div class="requirement-item {{ $activeView === 'missing' ? 'missing' : ($activeView === 'approved' ? 'passed' : ($activeView === 'denied' ? 'denied' : 'pending')) }}">
                                                        @if($activeView === 'approved')
                                                            <i class="fa fa-check"></i>
                                                        @elseif($activeView === 'missing')
                                                            <i class="fa fa-exclamation-circle"></i>
                                                        @elseif($activeView === 'denied')
                                                            <i class="fa fa-times"></i>
                                                        @else
                                                            <i class="fa fa-clock"></i>
                                                        @endif
                                                        <span>{{ $item }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="metric-pill {{ $activeView }}">
                                            <strong>{{ $status[$activeView]->count() }}</strong>
                                            <span>{{ $activeView }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress-wrap">
                                            <div class="progress-label">
                                                <span>Completion</span>
                                                <strong>{{ $status['completion'] }}%</strong>
                                            </div>
                                            <div class="progress-track">
                                                <div class="progress-fill" style="width: {{ $status['completion'] }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#999;">
                                        No {{ $activeView }} requirements found for this class.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
            </div>
            <div class="footer-note">
                Missing requirements are computed from the professor's current file categories. Submitted requirements with old or renamed categories appear under Extra Submitted.
            </div>
            @if($studentStatuses->hasPages())
                <div class="matrix-pagination">
                    <div class="pagination-meta">
                        Showing {{ $studentStatuses->firstItem() }} to {{ $studentStatuses->lastItem() }} of {{ $studentStatuses->total() }} students
                    </div>
                    <div class="pagination-nav">
                        <a href="{{ $studentStatuses->previousPageUrl() ?: '#' }}" class="page-btn {{ $studentStatuses->onFirstPage() ? 'disabled' : '' }}">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        @for($page = 1; $page <= $studentStatuses->lastPage(); $page++)
                            @if($page === $studentStatuses->currentPage())
                                <span class="page-btn active">{{ $page }}</span>
                            @else
                                <a href="{{ $studentStatuses->url($page) }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endfor
                        <a href="{{ $studentStatuses->nextPageUrl() ?: '#' }}" class="page-btn {{ $studentStatuses->hasMorePages() ? '' : 'disabled' }}">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </main>
</div>

<div class="requirement-modal-overlay" id="requirementListModal" aria-hidden="true">
    <div class="requirement-modal" role="dialog" aria-modal="true" aria-labelledby="requirementListTitle">
        <div class="requirement-modal-header">
            <div>
                <h2 id="requirementListTitle">Requirements</h2>
                <p id="requirementListStudent"></p>
            </div>
            <button type="button" class="requirement-modal-close" id="requirementModalClose" aria-label="Close requirements list">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="requirement-modal-body">
            <div class="requirement-grid" id="requirementModalList"></div>
        </div>
    </div>
</div>

<div id="print-area-wrapper"></div>

<script>
    window.requirementReportData = {
        activeView: @json($activeView),
        course: @json($course->course),
        room: @json($course->room),
        schoolYear: @json($course->school_year_start && $course->school_year_end ? $course->school_year_start . ' - ' . $course->school_year_end : 'School year not set'),
        totalStudents: @json($totalStudents),
        categoryCount: @json($categoryCount),
        completeStudents: @json($completeStudents),
        averageCompletion: @json($averageCompletion),
        generatedAt: @json(now()->format('F d, Y h:i A')),
        professor: @json($data->full_name ?? 'Professor'),
        rows: @json($printStatuses),
    };

    window.requirementAiContext = {
        report_type: 'requirement_status',
        metrics: {
            class: @json(trim(($course->course ?? '') . ' ' . ($course->room ?? ''))),
            school_year: @json($course->school_year_start && $course->school_year_end ? $course->school_year_start . '-' . $course->school_year_end : 'Not set'),
            total_students: @json($totalStudents),
            required_categories: @json($categoryCount),
            complete_students: @json($completeStudents),
            students_with_missing: @json($studentsWithMissing),
            students_with_pending: @json($studentsWithPending),
            average_completion: @json($averageCompletion),
            submitted_requirements: @json($submittedRequirements),
            missing_requirements: @json($missingRequirements),
            approved_requirements: @json($approvedRequirements),
            pending_requirements: @json($pendingRequirements),
            denied_requirements: @json($deniedRequirements),
            top_missing_requirements: @json($topMissingRequirements),
            current_view: @json($activeView)
        },
        insight: @json($requirementInsights ?? null)
    };

    window.requirementAiAskUrl = @json(route('reports.ai.ask'));
    window.csrfToken = @json(csrf_token());
</script>
<script src="{{ vasset('js/professor/requirement-status.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>