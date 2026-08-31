<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Reports</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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

    <link rel="stylesheet" href="{{ vasset('css/coordinator/reports-table.css') }}?v={{ time() }}">
</head>

<body>

<div id="print-area-wrapper"></div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJT IMS</span>
        </div>
    </a>

    <a href="{{ url('/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($user->profile_photo) && $user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
            <span class="user-role">OJT Coordinator</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/dashboard') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Dashboard</span>
            <span class="tooltip-label">Dashboard</span>
        </a>
        <a href="{{ url('/studentLists') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-users"></i></span>
            <span class="nav-label">Students</span>
            <span class="tooltip-label">Students</span>
        </a>
        <a href="{{ url('/professorTab') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chalkboard-teacher"></i></span>
            <span class="nav-label">Professors</span>
            <span class="tooltip-label">Professors</span>
        </a>
        <a href="{{ url('/uploadpage') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-upload"></i></span>
            <span class="nav-label">Upload Templates</span>
            <span class="tooltip-label">Upload Templates</span>
        </a>
        <a href="{{ url('/maintenance') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cogs"></i></span>
            <span class="nav-label">Maintenance</span>
            <span class="tooltip-label">Maintenance</span>
        </a>
        <a href="{{ url('/MOA') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>

        <div class="nav-group-reports active">
            <a href="{{ url('/reports') }}" class="nav-item nav-item-reports active" style="cursor:pointer;">
                <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
                <span class="nav-label">Reports</span>
                <span class="tooltip-label">Reports</span>
            </a>
            <div class="nav-sub">
                <a href="{{ url('/reports') }}" class="nav-sub-item active">
                    <i class="fa fa-user-graduate"></i> Student OJT Info
                </a>
                <a href="{{ url('/reportsExpired') }}" class="nav-sub-item">
                    <i class="fa fa-file-contract"></i> MOA
                </a>
            </div>
        </div>

        <a href="{{ url('/analytics') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
        </a>

        <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
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
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-user-shield"></i> OJT Coordinator
            </div>
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <div>
                <h1>Student OJT <span>Reports</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Reports</span>
                    <i class="fa fa-chevron-right"></i>
                    <span>Student OJT Information</span>
                </div>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-user-graduate"></i></div>
                <div>
                    <div class="stat-num">{{ count($studentData) }}</div>
                    <div class="stat-name">Total Records</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ collect($studentData)->pluck('ojt.company_name')->unique()->count() }}</div>
                    <div class="stat-name">Companies</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-chart-bar"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Report Type</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-calendar-alt"></i></div>
                <div>
                    <div class="stat-num">{{ now()->format('Y') }}</div>
                    <div class="stat-name">Current Year</div>
                </div>
            </div>
        </div>

        @if(!empty($reportInsights))
            <div data-ai-insight-card class="panel-card" style="margin-bottom:22px; border-left:4px solid var(--red);">
                <div class="panel-card-header">
                    <div class="panel-header-icon"><i class="fa fa-robot"></i></div>
                    <div>
                        <h2>AI Report Insight</h2>
                        <p>Generated from the current OJT report data</p>
                    </div>
                    <div style="margin-left:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <button type="button" data-ai-insight-button data-ai-context="studentAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
                            <i class="fa fa-magic"></i> Generate AI Insight
                        </button>
                        <div style="display:inline-flex; align-items:center; gap:6px; background:#fff5f5; border:1px solid #fecaca; color:var(--red-dark); border-radius:999px; padding:5px 12px; font-size:12px; font-weight:700;">
                            <i class="fa fa-brain"></i>
                            <span data-ai-badge>{{ ($reportInsights['source'] ?? '') === 'openai' ? 'OpenAI' : (($reportInsights['source'] ?? '') === 'gemini' ? 'Gemini AI' : (!empty($reportInsights['used_local_ai']) ? 'Local AI' : 'Internal Insight')) }}</span>
                        </div>
                    </div>
                </div>
                <div data-ai-result-panel class="filter-card-body" style="display:none;">
                    @if(($reportInsights['source'] ?? '') === 'fallback')
                        <div data-ai-notice style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;">
                            <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                            <div>
                                <strong>Gemini is temporarily unavailable.</strong>
                                <span data-ai-notice-text>{{ $reportInsights['availability']['message'] ?? 'Internal insight is shown for now. Try again in a few minutes if this is a short rate limit; if the daily free-tier limit was reached, AI answers may resume after the quota resets.' }}</span>
                            </div>
                        </div>
                    @endif
                    <div data-ai-status style="display:none; margin-bottom:12px; font-size:12px; color:#888;"></div>
                    <p data-ai-summary style="font-size:14px; line-height:1.7; color:#333; margin-bottom:16px;">{{ $reportInsights['summary'] ?? 'No AI insight available.' }}</p>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul data-ai-findings style="margin:0; padding-left:18px; color:#444; line-height:1.65;">
                                @foreach(($reportInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#444; line-height:1.65;">
                                @forelse(($reportInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                            <ul data-ai-actions style="margin:0; padding-left:18px; color:#444; line-height:1.65;">
                                @foreach(($reportInsights['recommendations'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @php
                        $studentReportRecords = collect($studentData);
                        $studentRecordsWithOjt = $studentReportRecords->filter(fn ($row) => !empty($row['ojt']));
                        $studentCompanyCount = $studentRecordsWithOjt->pluck('ojt.company_name')->filter()->unique()->count();
                        $studentMissingOjt = $studentReportRecords->count() - $studentRecordsWithOjt->count();
                        $studentPromptSuggestions = [
                            ['label' => 'Priorities', 'question' => 'What should we prioritize first based on this student OJT report?'],
                            ['label' => 'Risk', 'question' => 'What risk level does this report show and which student groups need attention?'],
                            ['label' => 'Executive summary', 'question' => 'Write a short executive summary for this student report.'],
                        ];

                        if (($completedStudents ?? 0) < ($totalStudents ?? 0)) {
                            $studentPromptSuggestions[] = ['label' => 'Completion plan', 'question' => 'How can we help the remaining students finish their required hours faster?'];
                        }

                        if (($activeStudents ?? 0) > 0) {
                            $studentPromptSuggestions[] = ['label' => 'Active monitoring', 'question' => 'What should the coordinator monitor this week for active interns?'];
                        }
                    @endphp
                    <div style="margin-top:18px; border-top:1px solid #f0f0f0; padding-top:16px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                            <div>
                                <div style="font-size:13px; font-weight:800; color:#1f2937;">Ask AI about this report</div>
                                <div style="font-size:12px; color:#888; margin-top:2px;">Click a suggested prompt to ask instantly, or type your own question and press Ask.</div>
                            </div>
                            <div style="display:grid; gap:7px;">
                                <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:.6px;">Suggested prompts</div>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    @foreach($studentPromptSuggestions as $suggestion)
                                        <button type="button" class="student-ai-quick-question" data-question="{{ $suggestion['question'] }}" style="display:inline-flex; align-items:center; gap:7px; border:1.5px solid #fecaca; background:#fff; color:#991b1b; border-radius:9px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(220,38,38,0.08);"><i class="fa fa-bolt" style="width:18px; height:18px; border-radius:6px; background:#fee2e2; display:inline-flex; align-items:center; justify-content:center; font-size:10px; color:#dc2626;"></i>{{ $suggestion['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div style="display:grid; gap:10px;">
                            <textarea id="studentAiQuestionInput" rows="3" maxlength="500" placeholder="Ask a question about this student OJT report..." style="width:100%; resize:vertical; min-height:86px; border:1.5px solid #e5e7eb; border-radius:10px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; outline:none; line-height:1.6;"></textarea>
                            <button type="button" id="studentAskAiBtn" style="justify-self:end; display:inline-flex; align-items:center; gap:8px; border:none; border-radius:10px; background:linear-gradient(135deg,#10b981,#059669); color:#fff; padding:11px 18px; font-size:13px; font-weight:800; white-space:nowrap; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
                                <i class="fa fa-paper-plane"></i> Ask
                            </button>
                        </div>
                        <div id="studentAiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#888;"></div>
                        <div id="studentAiAnswerBox" style="display:none; margin-top:12px; background:#fff; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; margin-bottom:8px;">
                                <div style="font-size:12px; font-weight:800; color:var(--red); text-transform:uppercase; letter-spacing:.4px;">AI Answer</div>
                                <div id="studentAiAnswerSource" style="font-size:11px; font-weight:700; color:#888;"></div>
                            </div>
                            <div id="studentAiAnswerText" style="font-size:13px; color:#333; line-height:1.7;"></div>
                            <ul id="studentAiAnswerSteps" style="margin:10px 0 0; padding-left:18px; color:#444; font-size:13px; line-height:1.65;"></ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="panel-card">
            <div class="panel-card-header">
                <div class="panel-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h2>Generate Report</h2>
                    <p>Filter by school year and course to generate the OJT report</p>
                </div>
            </div>
            <div class="panel-card-body">
                <form id="reportForm" action="{{ route('studentojt.report.generate') }}" method="post">
                    @csrf
                    <div class="filter-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                            <select class="field-select" id="school_year" name="school_year" required>
                                <option value="">Select School Year</option>
                                @foreach (($schoolYears ?? collect()) as $schoolYear)
                                    <option value="{{ $schoolYear }}" {{ ($selectedSchoolYear ?? '') === $schoolYear ? 'selected' : '' }}>
                                        {{ $schoolYear }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-graduation-cap"></i> Course</label>
                            <select class="field-select" id="course" name="course" required>
                                @foreach ($course as $c)
                                <option value="{{ $c->course }}" {{ request('course') === $c->course ? 'selected' : '' }}>{{ $c->course }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-check"></i> Semester</label>
                            <select class="field-select" id="semester" name="semester">
                                <option value="1st Semester" {{ ($selectedSemester ?? request('semester') ?? '1st Semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd Semester" {{ ($selectedSemester ?? request('semester')) === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="Summer Term" {{ ($selectedSemester ?? request('semester')) === 'Summer Term' ? 'selected' : '' }}>Summer Term</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label" style="opacity:0;">Action</label>
                            <button type="submit" class="btn-generate">
                                <i class="fa fa-file-alt"></i> Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-user-graduate"></i></div>
                    <div>
                        <h2>Student OJT Information</h2>
                        <p>OJT placement details for all students</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div class="count-badge">
                        <i class="fa fa-users"></i>
                        {{ count($studentData) }} {{ count($studentData) == 1 ? 'record' : 'records' }}
                    </div>
                    <button type="button" class="btn-preview" id="openPreviewBtn">
                        <i class="fa fa-print"></i> Print Preview
                    </button>
                </div>
            </div>

            <div class="table-card-body">
                <div class="table-scroll">
                    <table id="fileTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="white-space:nowrap; min-width:140px;">Student Name</th>
                                <th style="white-space:nowrap; min-width:130px;">Course / Major</th>
                                <th style="white-space:nowrap; min-width:70px;">Section</th>
                                <th style="white-space:nowrap; min-width:160px;">Company Name</th>
                                <th style="white-space:nowrap; min-width:120px;">Assigned Dept.</th>
                                <th style="white-space:nowrap; min-width:110px;">Role</th>
                                <th style="white-space:nowrap; min-width:100px;">Start Date</th>
                                <th style="white-space:nowrap; min-width:100px;">End Date</th>
                                <th style="white-space:nowrap; min-width:150px;">Reporting Time</th>
                                <th style="white-space:nowrap; min-width:120px;">Contact Name</th>
                                <th style="white-space:nowrap; min-width:110px;">Contact No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentData as $data)
                                @if(isset($data['ojt']) && $data['ojt'])
                                <tr data-course="{{ $data['student']->course ?? '' }}"
                                    data-section="{{ $data['student']->year_and_section ?? '' }}"
                                    data-department="{{ $data['ojt']->assigned_department ?? '' }}"
                                    data-role="{{ $data['ojt']->student_role ?? '' }}">
                                    <td>
                                        <div class="name-cell">
                                            <div class="name-avatar">{{ strtoupper(substr($data['student']->full_name, 0, 1)) }}</div>
                                            <span class="name-text">{{ $data['student']->full_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $data['student']->course ?? '—' }}</td>
                                    <td>{{ $data['student']->year_and_section ?? '—' }}</td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:5px;">
                                            <i class="fa fa-building" style="color:var(--red); font-size:11px;"></i>
                                            {{ $data['ojt']->company_name }}
                                        </div>
                                    </td>
                                    <td>{{ $data['ojt']->assigned_department ?? '' }}</td>
                                    <td>{{ $data['ojt']->student_role ?? '' }}</td>
                                    <td><span class="date-badge"><i class="fa fa-calendar-alt"></i> {{ $data['ojt']->start_date }}</span></td>
                                    <td><span class="date-badge"><i class="fa fa-calendar-check"></i> {{ $data['ojt']->finish_date }}</span></td>
                                    <td class="report-time-cell" style="white-space:nowrap;"><i class="fa fa-clock" style="color:var(--red); font-size:11px; margin-right:4px;"></i>{{ $data['ojt']->report_time }}</td>
                                    <td style="font-weight:600;">{{ $data['ojt']->contact_name }}</td>
                                    <td style="white-space:nowrap;"><i class="fa fa-phone" style="color:var(--red); font-size:11px; margin-right:4px;"></i>{{ $data['ojt']->contact_number }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
            <span class="footer-copy">
                &copy; 1998&ndash;2026 <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div class="footer-links">
            <a href="https://www.pup.edu.ph/" target="_blank">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                PUP Website
            </a>
            <span class="divider">|</span>
            <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
            <span class="divider">|</span>
            <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
        </div>
    </footer>
</div>

<div id="printPreviewModal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="padding:14px 20px;">
                <h5 class="modal-title" style="visibility:hidden; font-size:0;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="printPreviewContent" style="background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,0.12); overflow:hidden;">
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                    <i class="fa fa-times" style="margin-right:5px;"></i> Close
                </button>
                <button type="button" id="doPrintBtn" class="btn-modal-print">
                    <i class="fa fa-print" style="margin-right:5px;"></i> Print / Save as PDF
                </button>
            </div>
        </div>
    </div>
</div>

<form id="sendEmailForm" action="{{ url('/reportsExpired/send-email') }}" method="post" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="hidden" id="courseHidden" name="course" value="{{ $course ?? '' }}">
    <input type="hidden" id="emailHidden"  name="email"  value="{{ $user->email ?? '' }}">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    window.studentAiContext = {
        report_type: 'student_ojt_report',
        metrics: {
            total_records: @json(count($studentData)),
            total_companies: @json(collect($studentData)->pluck('ojt.company_name')->filter()->unique()->count()),
            records_with_ojt: @json(collect($studentData)->filter(fn ($row) => !empty($row['ojt']))->count()),
            missing_ojt: @json(count($studentData) - collect($studentData)->filter(fn ($row) => !empty($row['ojt']))->count()),
            course: @json(request('course') ?: null),
            school_year: @json($selectedSchoolYear ?? null)
        },
        insight: @json($reportInsights ?? []),
        askUrl: @json(route('reports.ai.ask')),
        csrfToken: @json(csrf_token())
    };
@php
    $coordinatorFullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    if (!$coordinatorFullName) {
        $coordinatorFullName = $user->full_name ?? (auth()->user() ? trim(auth()->user()->first_name . ' ' . auth()->user()->last_name) : 'OJT Coordinator');
    }
@endphp
    window.reportAiContext = window.studentAiContext;
    window.__reportsConfig = {
        campusName: @json($campusName ?? config('campus.name', 'PUP Taguig Campus')),
        coordinatorName: @json($coordinatorFullName ?: 'OJT Coordinator'),
        schoolYear: @json($selectedSchoolYear ?? request('school_year') ?? ''),
        course: @json(request('course') ?? ''),
        semester: @json($selectedSemester ?? request('semester') ?? '1st Semester')
    };
    window.reportConfig = window.__reportsConfig;
</script>
<script src="{{ vasset('js/coordinator/reports-table.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
