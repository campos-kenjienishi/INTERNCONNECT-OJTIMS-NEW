<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Analytics</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/coordinator/analytics.css') }}?v={{ time() }}">
</head>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-layout">
        <aside class="sidebar" id="sidebar">
            <a href="{{ url('/dashboard') }}" class="sidebar-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name">Intern<span>Connect</span></span>
                    <span class="sidebar-brand-sub">OJTIMS</span>
                </div>
            </a>

            <a href="{{ url('/accountinfo') }}" class="sidebar-user">
                <div class="sidebar-avatar">
                    @if(isset($data->profile_photo) && $data->profile_photo)
                        <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
                    @else
                        <i class="fa fa-user-tie"></i>
                    @endif
                </div>
                <div>
                    <div class="sidebar-user-name">{{ $data->full_name ?? 'Coordinator' }}</div>
                    <div class="sidebar-user-role">OJT Coordinator</div>
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
                <a href="{{ url('/reports') }}" class="nav-item">
                    <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
                    <span class="nav-label">Reports</span>
                    <span class="tooltip-label">Reports</span>
                </a>
                <a href="{{ url('/analytics') }}" class="nav-item active">
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
        </aside>

        <div class="main-area">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Toggle sidebar">
                <i class="fa fa-bars"></i>
            </button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode" type="button">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
            </button>
            <span class="topbar-title">
                On-the-Job Training <span>Information Management System</span>
            </span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-user-shield"></i>
                OJT Coordinator
            </div>
        </div>
    </div>

    <main class="page">
        <section class="heading">
            <div>
                <h1>Live <span>Analytics</span></h1>
                <p>Operational visibility across students, requirements, and partner companies.</p>
            </div>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <button type="button" id="printBtn" aria-label="Print analytics report" class="analytics-print-btn">
                    <i class="fa fa-print"></i> Print Report
                </button>
                <div class="updated"><i class="fa fa-sync-alt" style="margin-right:6px;"></i> Updated {{ now()->format('M d, Y') }}</div>
            </div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $approvedStudents }}</div>
                    <div class="stat-label">Approved Students</div>
                </div>
                <div class="stat-icon icon-green"><i class="fa fa-user-check"></i></div>
            </article>
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $pendingStudents }}</div>
                    <div class="stat-label">Pending Students</div>
                </div>
                <div class="stat-icon icon-amber"><i class="fa fa-hourglass-half"></i></div>
            </article>
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $placedStudents }}</div>
                    <div class="stat-label">Placed Students</div>
                </div>
                <div class="stat-icon icon-blue"><i class="fa fa-briefcase"></i></div>
            </article>
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $partnerCompanies }}</div>
                    <div class="stat-label">Partner Companies</div>
                </div>
                <div class="stat-icon icon-purple"><i class="fa fa-building"></i></div>
            </article>
        </section>

        @if(!empty($analyticsInsights))
            <section data-ai-insight-card class="panel" style="margin-top:18px; border-left:4px solid #dc2626;">
                <header class="panel-head" style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;">
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
                        <button type="button" data-ai-insight-button data-ai-context="analyticsAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:#dc2626; color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer;">
                            <i class="fa fa-magic"></i> Generate AI Insight
                        </button>
                        <span style="display:inline-flex; align-items:center; gap:7px; border:1px solid #fecaca; background:#fff5f5; color:#b91c1c; border-radius:999px; padding:8px 13px; font-size:12px; font-weight:800;">
                            <i class="fa fa-brain"></i>
                            <span data-ai-badge>{{ $analyticsAiLabel }}</span>
                        </span>
                    </div>
                </header>
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
                            <div style="font-size:12px; font-weight:700; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul data-ai-findings style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No key findings available.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
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
                            ['label' => 'Priorities', 'question' => 'What should we prioritize first based on this coordinator analytics dashboard?'],
                            ['label' => 'Risk', 'question' => 'What risk level does this analytics dashboard suggest and why?'],
                            ['label' => 'Action plan', 'question' => 'Create a short action plan based on the current analytics metrics.'],
                        ];

                        if (($pendingStudents ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Pending students', 'question' => 'How should we handle the pending student approvals shown in this dashboard?'];
                        }

                        if (($pendingRequirements ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Pending files', 'question' => 'What should we do about the pending requirement files?'];
                        }

                        if (($unplacedStudents ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Placement gap', 'question' => 'What does the placement gap suggest and what follow-up should we do?'];
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
                            <button type="button" id="analyticsAskAiBtn" style="justify-self:end; display:inline-flex; align-items:center; gap:8px; border:none; border-radius:10px; background:linear-gradient(135deg,#dc2626,#991b1b); color:#fff; padding:11px 18px; font-size:13px; font-weight:800; white-space:nowrap;">
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
            </section>
        @endif

        <section class="analytics-grid">
            <article class="panel">
                <header class="panel-head">
                    <h2>Student Status Breakdown</h2>
                    <p>Current account state across the portal</p>
                </header>
                <div class="panel-body">
                    @foreach ($studentStatusAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'amber' => 'fill-amber',
                                'red' => 'fill-red',
                                default => 'fill-blue',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} total</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Requirement Status</h2>
                    <p>File review progress across uploads</p>
                </header>
                <div class="panel-body">
                    @foreach ($fileStatusAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'amber' => 'fill-amber',
                                default => 'fill-red',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} files</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Course Distribution</h2>
                    <p>Students grouped by course</p>
                </header>
                <div class="panel-body">
                    @forelse ($courseAnalytics as $course)
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $course['label'] }}</div>
                                    <div class="metric-meta">{{ $course['count'] }} students</div>
                                </div>
                                <div class="metric-percent">{{ $course['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill fill-teal" data-width="{{ $course['percentage'] }}"></div></div>
                        </div>
                    @empty
                        <div class="metric-meta">No course data available yet.</div>
                    @endforelse
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Top Partner Companies</h2>
                    <p>Companies with the most assigned students</p>
                </header>
                <div class="panel-body">
                    @forelse ($topCompanies as $company)
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $company['label'] }}</div>
                                    <div class="metric-meta">{{ $company['count'] }} students placed</div>
                                </div>
                                <div class="metric-percent">{{ $company['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill fill-purple" data-width="{{ $company['percentage'] }}"></div></div>
                        </div>
                    @empty
                        <div class="metric-meta">No company placement data available yet.</div>
                    @endforelse
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Placement Coverage</h2>
                    <p>Current student placement progress across the portal</p>
                </header>
                <div class="panel-body">
                    @foreach ($placementAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'amber' => 'fill-amber',
                                default => 'fill-blue',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} students</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>MOA Portfolio</h2>
                    <p>Health and assignment coverage of current MOA records</p>
                </header>
                <div class="panel-body">
                    @foreach ($moaStatusAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'red' => 'fill-red',
                                default => 'fill-amber',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} records</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </main>

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
            <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
            <span class="divider">|</span>
            <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
        </div>
    </footer>

        </div>
    </div>

    <div id="print-area-wrapper"></div>

<script>
    window.analyticsPrintData = {
        updatedAt: @json(now()->format('M d, Y')),
        approvedStudents: @json($approvedStudents),
        pendingStudents: @json($pendingStudents),
        placedStudents: @json($placedStudents),
        partnerCompanies: @json($partnerCompanies),
        studentStatusAnalytics: @json($studentStatusAnalytics),
        fileStatusAnalytics: @json($fileStatusAnalytics),
        courseAnalytics: @json($courseAnalytics),
        topCompanies: @json($topCompanies),
        placementAnalytics: @json($placementAnalytics),
        moaStatusAnalytics: @json($moaStatusAnalytics),
        analyticsSummary: @json($analyticsInsights['summary'] ?? null)
    };

    window.analyticsAiContext = {
        report_type: 'coordinator_analytics',
        metrics: {
            total_students: @json($totalStudents),
            approved_students: @json($approvedStudents),
            pending_students: @json($pendingStudents),
            denied_students: @json($deniedStudents),
            in_class_students: @json($inClassStudents),
            total_requirements: @json($totalRequirements),
            approved_requirements: @json($approvedRequirements),
            pending_requirements: @json($pendingRequirements),
            denied_requirements: @json($deniedRequirements),
            partner_companies: @json($partnerCompanies),
            placed_students: @json($placedStudents),
            unplaced_students: @json($unplacedStudents),
            student_status: @json($studentStatusAnalytics),
            file_status: @json($fileStatusAnalytics),
            placement: @json($placementAnalytics),
            moa_status: @json($moaStatusAnalytics),
            course_distribution: @json($courseAnalytics),
            top_companies: @json($topCompanies)
        },
        insight: @json($analyticsInsights ?? []),
        askUrl: @json(route('reports.ai.ask')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/coordinator/analytics.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
</body>
</html>
