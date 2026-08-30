<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ vasset('css/professor_moa-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/reports-expired.css') }}">
</head>


<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div id="print-area-wrapper"></div>

<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($user->profile_photo) && $user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
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
        <a href="{{ url('/professor/analytics') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
        </a>
        <a href="{{ url('/reportsExpiredProf') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/professor/maintain') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cogs"></i></span>
            <span class="nav-label">Maintenance</span>
            <span class="tooltip-label">Maintenance</span>
        </a>
            <a href="{{ url('/professor/evaluation') }}" class="nav-item{{ request()->is('professor/evaluation*') ? ' active' : '' }}">
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
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-chalkboard-teacher"></i> Professor Portal
            </div>
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <div>
                <h1>Memorandum of <span>Agreement</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>MOA</span>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h2>Generate MOA Report</h2>
                    <p>Filter by school year and course to generate a report</p>
                </div>
            </div>
            <form action="{{ route('reports.generate.prof') }}" method="post">
                @csrf
                <div class="filter-card-body">
                    <div class="filter-group">
                        <label class="filter-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                        <select class="filter-select" name="school_year" id="school_year">
                            <option value="" {{ empty($selectedSchoolYear) ? 'selected' : '' }}>All School Years</option>
                            @foreach (($schoolYears ?? collect()) as $year)
                                <option value="{{ $year }}" {{ (string) ($selectedSchoolYear ?? '') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        <div class="year-range-wrap" style="display:none;">
                            <select class="filter-select" name="school_year_start" id="school_year_start">
                                <option value="">Start Year</option>
                                @for ($year = 2018; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}" {{ (string) request('school_year_start') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            <span class="year-separator">—</span>
                            <select class="filter-select" name="school_year_end" id="school_year_end" data-selected="{{ request('school_year_end') }}">
                                <option value="">End Year</option>
                                @for ($year = 2019; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}" {{ (string) request('school_year_end') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <span class="error-hint" id="school_year-error">Please select the school year.</span>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label"><i class="fa fa-graduation-cap"></i> Course</label>
                        <select class="filter-select" name="course" id="courseSelect" required>
                            @foreach ($courseAll as $c)
                                <option value="{{ $c->course }}" {{ (string) ($selectedCourse ?? '') === (string) $c->course ? 'selected' : '' }}>{{ $c->course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label"><i class="fa fa-calendar-check"></i> Semester</label>
                        <select class="filter-select" id="semester" name="semester">
                            <option value="1st Semester" {{ request('semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ request('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer" {{ request('semester') === 'Summer' ? 'selected' : '' }}>Summer</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="button" class="btn-print-preview" id="openPreviewBtn">
                            <i class="fa fa-print"></i> Print Preview
                        </button>
                        <button type="submit" class="btn-generate">
                            <i class="fa fa-chart-bar"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @php
            $totalMOA   = count($companies);
            $activeMOA  = 0;
            $expiredMOA = 0;
            foreach ($companies as $c) {
                [$sy, $ey] = array_pad(explode('-', (string) ($c->school_year ?? '0-0')), 2, '');
                $diff = now()->year - (int)$sy;
                if ($diff > 3) $expiredMOA++; else $activeMOA++;
            }
        @endphp

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">{{ $totalMOA }}</div>
                    <div class="stat-name">Total MOA</div>
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
                <div class="stat-icon red"><i class="fa fa-exclamation-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $expiredMOA }}</div>
                    <div class="stat-name">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalMOA }}</div>
                    <div class="stat-name">Partner Companies</div>
                </div>
            </div>
        </div>

        @if(!empty($reportInsights))
            <div data-ai-insight-card class="filter-card" style="margin-bottom:22px; border-left:4px solid var(--red);">
                <div class="filter-card-header">
                    <div class="filter-header-icon"><i class="fa fa-robot"></i></div>
                    <div>
                        <h2>AI Report Insight</h2>
                        <p>Generated from the current MOA report data</p>
                    </div>
                    <div style="margin-left:auto; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <button type="button" data-ai-insight-button data-ai-context="aiReportContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
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
                            <button type="button" id="askAiBtn" style="justify-self:end; display:inline-flex; align-items:center; gap:8px; border:none; border-radius:10px; background:linear-gradient(135deg,#10b981,#059669); color:#fff; padding:11px 18px; font-size:13px; font-weight:800; white-space:nowrap; box-shadow:0 3px 10px rgba(16,185,129,0.25);">
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

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-contract"></i></div>
                    <div>
                        <h2>MOA Records</h2>
                        <p>Memorandum of Agreement with partner companies</p>
                    </div>
                </div>
                <div class="moa-count-badge">
                    <i class="fa fa-building"></i>
                    {{ $totalMOA }} {{ $totalMOA == 1 ? 'record' : 'records' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="companyTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Representative</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                            <td>{{ $company->id }}</td>
                            <td class="company-name-cell">
                                <div class="company-cell">
                                    <div class="company-icon-box"><i class="fa fa-building"></i></div>
                                    <span class="company-name-text">{{ $company->company_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-map-marker-alt" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->company_address }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-user-tie" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->company_rep }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-phone" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->companyNo }}
                                </div>
                            </td>
                            <td class="email-cell">
                                <div class="email-wrap">
                                    <i class="fa fa-envelope" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->company_email }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-calendar" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $validUntil ? $validUntil->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if($status === 'Active')
                                    <span class="badge-active"><i class="fa fa-circle"></i> Active</span>
                                @else
                                    <span class="badge-expired"><i class="fa fa-times-circle"></i> Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="moa-actions">
                                    @if($company->file && !empty($company->moa_file_ready))
                                        <a href="{{ url('/moa/download/' . $company->file) }}"
                                           class="btn-moa-action btn-moa-download"
                                           target="_blank"
                                           rel="noopener">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                        <button type="button"
                                                class="btn-moa-action btn-moa-print"
                                                onclick="printUploadedMoa('{{ asset('assets/' . $company->file) }}')">
                                            <i class="fa fa-print"></i> Print
                                        </button>
                                    @elseif($company->file)
                                        <span class="btn-moa-action btn-moa-download is-disabled" title="This MOA file is empty or unavailable">
                                            <i class="fa fa-download"></i> Download
                                        </span>
                                        <span class="btn-moa-action btn-moa-print is-disabled" title="This MOA file is empty or unavailable">
                                            <i class="fa fa-print"></i> Print
                                        </span>
                                    @endif
                                </div>
                                @if($company->file && !empty($company->moa_file_empty))
                                    <div class="moa-file-note">Uploaded file is empty or missing. Re-upload the PDF before printing or downloading.</div>
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
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
        <span class="footer-copy">
            © 1998–2026 <span>Polytechnic University of the Philippines</span>
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
            <div class="modal-header">
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
    <input type="hidden" id="courseHidden" name="course" value="{{ $selectedCourse ?? '' }}">
    <input type="hidden" id="schoolYearHidden" name="school_year" value="{{ $selectedSchoolYear ?? '' }}">
    <input type="hidden" id="emailHidden"  name="email"  value="{{ $user->email ?? '' }}">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script>
    window.professorMoaConfig = {
        payload: {
            total_moa: @json($totalMOA ?? 0),
            active_moa: @json($activeMOA ?? 0),
            expired_moa: @json($expiredMOA ?? 0),
            course: @json($selectedCourse ?? (isset($courseAll) && $courseAll->first() ? $courseAll->first()->course : null)),
            school_year: @json($selectedSchoolYear ?? null)
        },
        insight: @json($reportInsights ?? []),
        campusName: @json($campusName ?? config('campus.name', 'PUP Taguig Campus')),
        campusCollege: @json($campusCollege ?? config('campus.college', 'College of Engineering and Technology')),
        coordinatorName: @json($user->full_name ?? 'OJT Coordinator'),
        sendEmailUrl: @json(route('reportsExpired.send.email')),
        aiAskUrl: @json(route('reports.ai.ask')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/professor/reports-expired.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>