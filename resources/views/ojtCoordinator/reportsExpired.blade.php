<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA Report</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ vasset('css/coordinator/reports-expired.css') }}?v={{ time() }}">
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div id="print-area-wrapper"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
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

        <div class="nav-item" style="cursor:default; pointer-events:none;">
            <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
            <span class="nav-label">Reports</span>
        </div>
        <div class="nav-sub">
            <a href="{{ url('/reports') }}" class="nav-sub-item">
                <i class="fa fa-user-graduate" style="margin-right:6px; font-size:11px;"></i> Student OJT Info
            </a>
            <a href="{{ url('/reportsExpired') }}" class="nav-sub-item active">
                <i class="fa fa-file-contract" style="margin-right:6px; font-size:11px;"></i> MOA
            </a>
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

<!-- =============== MAIN CONTENT =============== -->
<div class="main-content" id="mainContent">

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-badge">
            <i class="fa fa-user-shield"></i> OJT Coordinator
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <div>
                <h1>Memorandum of <span>Agreement</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/reports') }}">Reports</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>MOA</span>
                </div>
            </div>
        </div>

        <!-- Stats -->
        @php
            $totalMOA = count($companies);
            $activeMOA = 0;
            $expiredMOA = 0;

            foreach ($companies as $company) {
                $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;

                if (!$validUntil || now()->gt($validUntil)) {
                    $expiredMOA++;
                } else {
                    $activeMOA++;
                }
            }
        @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">{{ $totalMOA }}</div>
                    <div class="stat-name">Total MOAs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $activeMOA }}</div>
                    <div class="stat-name">Active</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-exclamation-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $expiredMOA }}</div>
                    <div class="stat-name">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalMOA }}</div>
                    <div class="stat-name">Partner Companies</div>
                </div>
            </div>
        </div>

        @if(!empty($reportInsights))
            <div data-ai-insight-card class="panel-card" style="margin-bottom:22px; border-left:4px solid var(--red);">
                <div class="panel-card-header">
                    <div class="panel-header-icon"><i class="fa fa-robot"></i></div>
                    <div>
                        <h2>AI Report Insight</h2>
                        <p>Generated from the current MOA report data</p>
                    </div>
                    <div style="margin-left:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <button type="button" data-ai-insight-button data-ai-context="aiReportContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:var(--red); color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer;">
                            <i class="fa fa-magic"></i> Generate AI Insight
                        </button>
                        <div style="display:inline-flex; align-items:center; gap:6px; background:#fff5f5; border:1px solid #fecaca; color:var(--red-dark); border-radius:999px; padding:5px 12px; font-size:12px; font-weight:700;">
                            <i class="fa fa-brain"></i>
                            <span data-ai-badge>{{ ($reportInsights['source'] ?? '') === 'openai' ? 'OpenAI' : (($reportInsights['source'] ?? '') === 'gemini' ? 'Gemini AI' : (!empty($reportInsights['used_local_ai']) ? 'Local AI' : 'Internal Insight')) }}</span>
                        </div>
                    </div>
                </div>
                <div data-ai-result-panel class="panel-card-body" style="display:none;">
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
                    <div style="margin-top:18px; border-top:1px solid #f0f0f0; padding-top:16px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                            <div>
                                <div style="font-size:13px; font-weight:800; color:#1f2937;">Ask AI about this report</div>
                                <div style="font-size:12px; color:#888; margin-top:2px;">Click a suggested prompt to ask instantly, or type your own question and press Ask.</div>
                            </div>
                            @php
                                $aiPromptSuggestions = [
                                    ['label' => 'Priorities', 'question' => 'What should we prioritize first in this MOA report?'],
                                    ['label' => 'Risk', 'question' => 'What risk level does this MOA report suggest and why?'],
                                    ['label' => 'Executive summary', 'question' => 'Write a short executive summary for this MOA report.'],
                                ];

                                if (($expiredMOA ?? 0) > 0) {
                                    $aiPromptSuggestions[] = ['label' => 'Renewal plan', 'question' => 'Create a practical renewal follow-up plan for the expired MOAs in this report.'];
                                }

                                if (($expiredMOA ?? 0) > ($activeMOA ?? 0)) {
                                    $aiPromptSuggestions[] = ['label' => 'Urgent concern', 'question' => 'Explain the most urgent concern in this report and what should be done this week.'];
                                } else {
                                    $aiPromptSuggestions[] = ['label' => 'Coverage', 'question' => 'How strong is the active partner coverage in this report?'];
                                }
                            @endphp
                            <div style="display:grid; gap:7px;">
                                <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:.6px;">Suggested prompts</div>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    @foreach($aiPromptSuggestions as $suggestion)
                                        <button type="button" class="ai-quick-question" data-question="{{ $suggestion['question'] }}"><i class="fa fa-bolt"></i>{{ $suggestion['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div style="display:grid; gap:10px;">
                            <textarea id="aiQuestionInput" rows="3" maxlength="500" placeholder="Ask a question about this report..." style="width:100%; resize:vertical; min-height:86px; border:1.5px solid #e5e7eb; border-radius:10px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; outline:none; line-height:1.6;"></textarea>
                            <button type="button" id="askAiBtn" style="justify-self:end; display:inline-flex; align-items:center; gap:8px; border:none; border-radius:10px; background:linear-gradient(135deg,#dc2626,#991b1b); color:#fff; padding:11px 18px; font-size:13px; font-weight:800; white-space:nowrap;">
                                <i class="fa fa-paper-plane"></i> Ask
                            </button>
                        </div>
                        <div id="aiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#888;"></div>
                        <div id="aiAnswerBox" style="display:none; margin-top:12px; background:#fff; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; margin-bottom:8px;">
                                <div style="font-size:12px; font-weight:800; color:var(--red); text-transform:uppercase; letter-spacing:.4px;">AI Answer</div>
                                <div id="aiAnswerSource" style="font-size:11px; font-weight:700; color:#888;"></div>
                            </div>
                            <div id="aiAnswerText" style="font-size:13px; color:#333; line-height:1.7;"></div>
                            <ul id="aiAnswerSteps" style="margin:10px 0 0; padding-left:18px; color:#444; font-size:13px; line-height:1.65;"></ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="panel-card">
            <div class="panel-card-header">
                <div class="panel-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h2>Generate MOA Report</h2>
                    <p>Filter MOAs by school year and course</p>
                </div>
            </div>
            <div class="panel-card-body">
                <form action="{{ route('reports.generate') }}" method="post">
                    @csrf
                    <div class="filter-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                            <select class="field-select" id="school_year" name="school_year">
                                <option value="">Select School Year</option>
                                @foreach (($schoolYears ?? collect()) as $sy)
                                    <option value="{{ $sy }}" {{ ($selectedSchoolYear ?? '') === $sy ? 'selected' : '' }}>
                                        {{ $sy }}
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
                                <option value="1st Semester" {{ request('semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd Semester" {{ request('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="Summer" {{ request('semester') === 'Summer' ? 'selected' : '' }}>Summer</option>
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

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-calendar-times"></i></div>
                    <div>
                        <h2>MOA Records</h2>
                        <p>Partner companies with active and expired MOAs</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div class="count-badge">
                        <i class="fa fa-building"></i>
                        {{ $totalMOA }} {{ $totalMOA == 1 ? 'record' : 'records' }}
                    </div>
                    <button type="button" class="btn-preview" id="openPreviewBtn">
                        <i class="fa fa-print"></i> Print Preview
                    </button>
                </div>
            </div>

            <div class="table-card-body">
                <table id="companyTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="display:none;">ID</th>
                            <th>Company Name</th>
                            <th>Company Address</th>
                            <th>Representative</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Validity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        @php
                            $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;
                            $status = ($validUntil && now()->lte($validUntil)) ? 'Active' : 'Expired';
                            $dateNotarizedFormatted = $company->date_notarized ? \Carbon\Carbon::parse($company->date_notarized)->format('M d, Y') : '';
                        @endphp
                        <tr data-nature-of-bus="{{ $company->nature_of_bus ?? '' }}" data-date-notarized="{{ $dateNotarizedFormatted }}">
                            <td style="display:none;">{{ $company->id }}</td>
                            <td>
                                <div class="company-cell">
                                    <div class="company-avatar"><i class="fa fa-building"></i></div>
                                    <span class="company-name-text">{{ $company->company_name }}</span>
                                </div>
                            </td>
                            <td class="company-address-cell">
                                <i class="fa fa-map-marker-alt" style="color:var(--red); font-size:10px; margin-right:4px;"></i>
                                {{ $company->company_address }}
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <i class="fa fa-user-tie" style="color:var(--red); font-size:11px;"></i>
                                    <span style="font-weight:600;">{{ $company->company_rep }}</span>
                                </div>
                            </td>
                            <td style="white-space:nowrap;">
                                <i class="fa fa-phone" style="color:var(--red); font-size:10px; margin-right:4px;"></i>
                                {{ $company->companyNo ?: '—' }}
                            </td>
                            <td style="font-size:12.5px;">
                                <i class="fa fa-envelope" style="color:var(--red); font-size:10px; margin-right:4px;"></i>
                                {{ $company->company_email ?: '—' }}
                            </td>
                            <td>
                                <span class="school-year-badge">
                                    <i class="fa fa-calendar-alt" style="font-size:10px;"></i>
                                    {{ $validUntil ? $validUntil->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($status === 'Active')
                                    <span class="badge-active"><i class="fa fa-circle"></i> Active</span>
                                @else
                                    <span class="badge-expired"><i class="fa fa-times-circle"></i> Expired</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
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
        <a href="{{ url('/terms') }}">Terms of Use</a>
        <span class="divider">|</span>
        <a href="{{ url('/privacy') }}">Privacy Statement</a>
    </div>
</footer>
</div>

<!-- =============== PRINT PREVIEW MODAL =============== -->
<div id="printPreviewModal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="visibility:hidden; font-size:0;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="printPreviewContent" style="background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,0.12); overflow:hidden;">
                    <!-- Injected by JS -->
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

<!-- Hidden email form (preserved) -->
<form id="sendEmailForm" action="{{ url('/reportsExpired/send-email') }}" method="post" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="hidden" id="courseInput" name="course" value="">
    <input type="hidden" id="emailInput"  name="email"  value="{{ $user->email }}">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    window.aiReportContext = {
        report_type: 'moa_report',
        metrics: {
            total_moa: @json($totalMOA ?? 0),
            active_moa: @json($activeMOA ?? 0),
            expired_moa: @json($expiredMOA ?? 0),
            course: @json(request('course') ?: (isset($course) && $course->first() ? $course->first()->course : null))
        },
        insight: @json($reportInsights ?? []),
        askUrl: @json(route('reports.ai.ask')),
        csrfToken: @json(csrf_token())
    };
    window.reportAiContext = window.aiReportContext;
    window.reportConfig = {
        campusName: @json($campusName ?? config('campus.name', 'PUP Taguig Branch')),
        campusCollege: @json($campusCollege ?? config('campus.college', 'College of Engineering and Technology')),
        coordinatorName: @json($user->full_name ?? 'OJT Coordinator')
    };
</script>
<script src="{{ vasset('js/coordinator/reports-expired.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
