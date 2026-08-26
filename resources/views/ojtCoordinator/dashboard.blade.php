<!DOCTYPE html>
    <html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InternConnect - Dashboard</title>
        <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
        <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/coordinator/dashboard.css') }}?v={{ time() }}">
    </head>

    <body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- =============== SIDEBAR =============== -->
    <div class="sidebar" id="sidebar">

        <a href="#" class="sidebar-brand">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">Intern<span>Connect</span></span>
                <span class="sidebar-brand-sub">OJTIMS</span>
            </div>
        </a>

        <a href="{{ url('/accountinfo') }}" class="sidebar-user">
            <div class="user-avatar">
                @if(isset($data->profile_photo) && $data->profile_photo)
                    <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
                @else
                    <i class="fa fa-user-tie"></i>
                @endif
            </div>
            <div class="user-info">
                <span class="user-name">{{ $data->full_name }}</span>
                <span class="user-role">OJT Coordinator</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ url('/dashboard') }}" class="nav-item active">
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

        <!-- Topbar -->
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
                    <i class="fa fa-user-shield"></i>
                    OJT Coordinator
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">

            <!-- Page Header -->
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h1 style="font-size:26px; font-weight:800; color:#1a1a1a; letter-spacing:-0.5px;">
                    Home <span style="color:var(--red);">Dashboard</span>
                </h1>
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <a href="{{ route('professor_home') }}" class="btn-switch-view" style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; background:#ffffff; border:1.5px solid #fecaca; border-radius:12px; color:#dc2626; font-weight:700; font-size:13px; text-decoration:none; box-shadow:0 3px 12px rgba(220,38,38,0.1); transition:all 0.25s;">
                        <i class="fa fa-exchange-alt"></i>
                        Switch to Professor View
                    </a>
                    <div class="date-badge" id="dateBadge" title="Click to view calendar & clock">
                        <span class="pulse-dot"></span>
                        <i class="fa fa-calendar-alt"></i>
                        <span id="currentDate"></span>
                    </div>
                </div>
            </div>

<!-- Red welcome banner, no date inside -->
<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Welcome to your coordinator dashboard</h2>
        <p>
            For first-time users, please watch these how-to videos to get familiar with the coordinator workflow.
        </p>
        <div class="welcome-actions">
            <a href="https://www.youtube.com/playlist?list=PLyMOKHLwy4fOPcBDkY_buk3Ol98a91zWW" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                <i class="fab fa-youtube"></i>
                How To Videos
            </a>
        </div>
    </div>
    <div class="welcome-icon">
        <i class="fa fa-user-shield"></i>
    </div>
</div>

            <!-- Stats Row -->
            <div class="stats-row">
                <a href="{{ url('/studentLists') }}" class="stat-card">
                    <div class="stat-card-left">
                        <div class="stat-num">{{ $roleCount }}</div>
                        <div class="stat-name">Total Students</div>
                        <div class="stat-change up">
                            <i class="fa fa-users" style="font-size:10px;"></i> Enrolled
                        </div>
                    </div>
                    <div class="stat-icon-box red">
                        <i class="fa fa-users"></i>
                    </div>
                </a>

                <a href="{{ url('/professorTab') }}" class="stat-card">
                    <div class="stat-card-left">
                        <div class="stat-num">{{ $roleCountP }}</div>
                        <div class="stat-name">Total Professors</div>
                        <div class="stat-change blue">
                            <i class="fa fa-chalkboard-teacher" style="font-size:10px;"></i> Active
                        </div>
                    </div>
                    <div class="stat-icon-box blue">
                        <i class="fa fa-chalkboard-teacher"></i>
                    </div>
                </a>

                <a href="{{ url('/uploadpage') }}" class="stat-card">
                    <div class="stat-card-left">
                        <div class="stat-num">{{ $fileCount }}</div>
                        <div class="stat-name">Uploaded Templates</div>
                        <div class="stat-change amber">
                            <i class="fa fa-file" style="font-size:10px;"></i> Available
                        </div>
                    </div>
                    <div class="stat-icon-box green">
                        <i class="fa fa-file-upload"></i>
                    </div>
                </a>
            </div>

            @if(!empty($dashboardInsights))
                <section data-ai-insight-card style="background:#fff; border:1px solid #f1f1f1; border-left:5px solid var(--red); border-radius:14px; box-shadow:0 8px 28px rgba(15,23,42,0.06); margin-bottom:22px; overflow:hidden;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px 20px; border-bottom:1px solid #f3f4f6; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center;">
                                <i class="fa fa-robot"></i>
                            </div>
                            <div>
                                <h2 style="font-size:18px; font-weight:800; color:#111827; margin:0;">Today&apos;s AI Brief</h2>
                                <p style="font-size:13px; color:#777; margin:4px 0 0;">Generated from current dashboard activity</p>
                            </div>
                        </div>
                        @php
                            $dashboardAiSource = $dashboardInsights['source'] ?? 'fallback';
                            $dashboardAiLabel = $dashboardAiSource === 'gemini'
                                ? 'Gemini AI'
                                : ($dashboardAiSource === 'openai' ? 'OpenAI' : 'Internal insight');
                        @endphp
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <button type="button" data-ai-insight-button data-ai-context="dashboardAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:#dc2626; color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer;">
                                <i class="fa fa-magic"></i> Generate AI Insight
                            </button>
                            <span style="display:inline-flex; align-items:center; gap:7px; border:1px solid #fecaca; background:#fff5f5; color:#b91c1c; border-radius:999px; padding:8px 13px; font-size:12px; font-weight:800;">
                                <i class="fa fa-brain"></i> <span data-ai-badge>{{ $dashboardAiLabel }}</span>
                            </span>
                        </div>
                    </div>
                    <div data-ai-result-panel style="display:none; padding:20px;">
                        @if(($dashboardInsights['source'] ?? '') === 'fallback')
                            <div data-ai-notice style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;">
                                <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                                <div><strong>Gemini is temporarily unavailable.</strong> <span data-ai-notice-text>{{ $dashboardInsights['availability']['message'] ?? 'Internal insight is shown for now. Try again in a few minutes, or later if the daily free-tier quota was reached.' }}</span></div>
                            </div>
                        @endif
                        <div data-ai-status style="display:none; margin-bottom:12px; font-size:12px; color:#777;"></div>
                        <p data-ai-summary style="font-size:15px; line-height:1.7; color:#1f2937; margin:0 0 18px;">{{ $dashboardInsights['summary'] ?? 'No dashboard insight available.' }}</p>

                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                            <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                                <ul data-ai-findings style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                    @forelse(($dashboardInsights['key_findings'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <li>No key findings available.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                                <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                    @forelse(($dashboardInsights['watchouts'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <li>No major watchouts detected.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                                <ul data-ai-actions style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                    @forelse(($dashboardInsights['recommendations'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <li>No actions suggested.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        @php
                            $dashboardPromptSuggestions = [
                                ['label' => 'Priorities', 'question' => 'What should we prioritize today based on this dashboard?'],
                                ['label' => 'Risk', 'question' => 'What risks does this dashboard show and why?'],
                                ['label' => 'Action plan', 'question' => 'Create a short coordinator action plan from this dashboard.'],
                            ];

                            if (($pendingStudents ?? 0) > 0) {
                                $dashboardPromptSuggestions[] = ['label' => 'Pending students', 'question' => 'How should we handle the pending student accounts shown here?'];
                            }

                            if (($pendingRequirements ?? 0) > 0) {
                                $dashboardPromptSuggestions[] = ['label' => 'Pending files', 'question' => 'What should we do about the pending requirement files?'];
                            }

                            if (($expiredMoaCount ?? 0) > 0) {
                                $dashboardPromptSuggestions[] = ['label' => 'MOA renewals', 'question' => 'What should be done about the expired MOA records?'];
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
                                        @foreach($dashboardPromptSuggestions as $suggestion)
                                            <button type="button" class="dashboard-ai-quick-question" data-question="{{ $suggestion['question'] }}" style="display:inline-flex; align-items:center; gap:7px; border:1.5px solid #fecaca; background:#fff; color:#991b1b; border-radius:9px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(220,38,38,0.08);"><i class="fa fa-bolt" style="width:18px; height:18px; border-radius:6px; background:#fee2e2; display:inline-flex; align-items:center; justify-content:center; font-size:10px; color:#dc2626;"></i>{{ $suggestion['label'] }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:10px; align-items:start;">
                                <textarea id="dashboardAiQuestionInput" rows="3" placeholder="Ask about pending approvals, files, placements, MOAs, or next actions..." style="width:100%; min-height:82px; border:1px solid #e5e7eb; border-radius:12px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; resize:vertical;"></textarea>
                                <button type="button" id="dashboardAskAiBtn" style="height:44px; border:none; border-radius:12px; padding:0 18px; background:#dc2626; color:#fff; font-family:'Poppins',sans-serif; font-size:13px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px;"><i class="fa fa-paper-plane"></i> Ask</button>
                            </div>
                            <div id="dashboardAiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#777;"></div>
                            <div id="dashboardAiAnswer" style="display:none; margin-top:12px; background:#fff7f7; border:1px solid #fecaca; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#b91c1c; margin-bottom:8px;">AI Answer</div>
                                <p id="dashboardAiAnswerText" style="margin:0; color:#374151; line-height:1.7; font-size:13px;"></p>
                                <div id="dashboardAiNextStepsWrap" style="display:none; margin-top:10px;">
                                    <div style="font-size:12px; font-weight:800; color:#b91c1c; margin-bottom:6px;">Next Steps</div>
                                    <ul id="dashboardAiNextSteps" style="margin:0; padding-left:18px; color:#374151; line-height:1.6; font-size:13px;"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">

                <!-- ===== LEFT: Create Announcement ===== -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon">
                            <i class="fa fa-bullhorn"></i>
                        </div>
                        <div>
                            <h2>Create Announcement</h2>
                            <p>Broadcast a message to all students and professors</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ url('/announcements') }}">
                        @csrf
                        <input type="hidden" name="audience" value="all_students">
                        <div class="panel-card-body">

                            <div class="field-group">
                                <label class="field-label" for="title">
                                    <i class="fa fa-heading"></i> Announcement Title
                                </label>
                                <input class="field-input" type="text" id="title" name="title"
                                    placeholder="e.g. OJT Orientation Schedule"
                                    required>
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="content">
                                    <i class="fa fa-align-left"></i> Content
                                </label>
                                <textarea class="field-input" id="content" name="content"
                                        rows="5"
                                        placeholder="Write your announcement message here..."
                                        required></textarea>
                            </div>

                        </div>

                        <div class="panel-card-footer">
                            <button type="submit" class="btn-submit">
                                <i class="fa fa-paper-plane"></i> Post Announcement
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ===== RIGHT: Quick Links ===== -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <div>
                            <h2>Quick Links</h2>
                            <p>Jump to any section of the portal</p>
                        </div>
                    </div>

                    <div class="quick-links-grid">
                        <a href="{{ url('/studentLists') }}" class="quick-link-item">
                            <div class="quick-link-icon red">
                                <i class="fa fa-users"></i>
                            </div>
                            <span class="quick-link-label">Students</span>
                        </a>
                        <a href="{{ url('/professorTab') }}" class="quick-link-item">
                            <div class="quick-link-icon blue">
                                <i class="fa fa-chalkboard-teacher"></i>
                            </div>
                            <span class="quick-link-label">Professors</span>
                        </a>
                        <a href="{{ url('/uploadpage') }}" class="quick-link-item">
                            <div class="quick-link-icon green">
                                <i class="fa fa-file-upload"></i>
                            </div>
                            <span class="quick-link-label">Upload Templates</span>
                        </a>
                        <a href="{{ url('/MOA') }}" class="quick-link-item">
                            <div class="quick-link-icon purple">
                                <i class="fa fa-file-contract"></i>
                            </div>
                            <span class="quick-link-label">MOA</span>
                        </a>
                        <a href="{{ url('/maintenance') }}" class="quick-link-item">
                            <div class="quick-link-icon amber">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <span class="quick-link-label">Maintenance</span>
                        </a>
                        <a href="{{ url('/reports') }}" class="quick-link-item">
                            <div class="quick-link-icon teal">
                                <i class="fa fa-chart-bar"></i>
                            </div>
                            <span class="quick-link-label">Reports</span>
                        </a>
                        <a href="{{ url('/analytics') }}" class="quick-link-item">
                            <div class="quick-link-icon blue">
                                <i class="fa fa-chart-line"></i>
                            </div>
                            <span class="quick-link-label">Analytics</span>
                        </a>
                    </div>
                </div>

            </div>

            <div class="panel-card" style="margin-top:22px;">
                <div class="panel-card-header">
                    <div class="panel-header-icon">
                        <i class="fa fa-list"></i>
                    </div>
                    <div>
                        <h2>My Announcements</h2>
                        <p>Review or delete announcements you posted</p>
                    </div>
                    <div class="announcement-toolbar">
                        <select id="coordinatorAnnouncementSort" class="announcement-sort-select" aria-label="Sort announcements by date">
                            <option value="desc" selected>Newest first</option>
                            <option value="asc">Oldest first</option>
                        </select>
                    </div>
                </div>
                <div class="panel-card-body announcement-table-wrap" style="padding:0;">
                    <table id="coordinatorAnnouncementTable" class="announcement-manage-table">
                        <colgroup>
                            <col style="width:42%;">
                            <col style="width:18%;">
                            <col style="width:24%;">
                            <col style="width:16%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Audience</th>
                                <th>Posted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($announcements ?? []) as $announcement)
                                <tr>
                                    <td class="announcement-title-cell">
                                        <strong>{{ $announcement->title }}</strong>
                                        <span>{{ Str::limit($announcement->content, 90) }}</span>
                                    </td>
                                    <td>All students</td>
                                    <td data-order="{{ \Carbon\Carbon::parse($announcement->created_at)->timestamp }}">
                                        {{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td>
                                        <button type="button"
                                                class="btn-edit-announcement"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editAnnouncementModal"
                                                data-announcement-id="{{ $announcement->id }}"
                                                data-announcement-title="{{ e($announcement->title) }}"
                                                data-announcement-content="{{ e($announcement->content) }}"
                                                data-announcement-action="{{ route('announcements.update', $announcement->id) }}">
                                            <i class="fa fa-pen"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('announcements.destroy', $announcement->id) }}" class="delete-announcement-form" data-announcement-title="{{ e($announcement->title) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-announcement">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-pen"></i> Edit Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editAnnouncementForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <label class="field-label">
                                <i class="fa fa-heading"></i> Announcement Title
                            </label>
                            <input class="field-input" type="text" name="title" id="editAnnouncementTitle" required>

                            <label class="field-label">
                                <i class="fa fa-align-left"></i> Content
                            </label>
                            <textarea class="field-input" name="content" id="editAnnouncementContent" rows="6" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn-submit">
                                <i class="fa fa-save"></i> Update Announcement
                            </button>
                        </div>
                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        window.dashboardAiContext = {
            report_type: 'coordinator_dashboard',
            metrics: {
                total_records: @json($roleCount ?? 0),
                professors: @json($roleCountP ?? 0),
                uploaded_templates: @json($fileCount ?? 0),
                announcements: @json(isset($announcements) ? $announcements->count() : 0),
                approved_students: @json($approvedStudents ?? 0),
                pending_students: @json($pendingStudents ?? 0),
                pending_files: @json($pendingRequirements ?? 0),
                total_companies: @json($partnerCompanies ?? 0),
                records_with_ojt: @json($placedStudents ?? 0),
                missing_ojt: @json($unplacedStudents ?? 0),
                expired_moa: @json($expiredMoaCount ?? 0)
            },
            insight: @json($dashboardInsights ?? null),
            askUrl: @json(route('reports.ai.ask')),
            csrfToken: @json(csrf_token())
        };
    </script>
    <script src="{{ vasset('js/coordinator/dashboard.js') }}?v={{ time() }}"></script>
    <script src="{{ vasset('js/ai-insight-controls.js') }}"></script>
    <script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
    @include('partials.password-setup-modal')
    @include('students.terms_modal')
    <script src="{{ vasset('js/mobile-utils.js') }}"></script>
</body>
    </html>
