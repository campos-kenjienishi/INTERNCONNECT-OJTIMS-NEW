<!DOCTYPE html>
<html lang="en">
<head>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InternConnect - Professor Dashboard</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/professor_home-responsive.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/professor/home.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
    <script src="{{ vasset('js/darkmode.js') }}"></script>
    <script>
        (function(){
            try {
                if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e){}
        })();
    </script>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
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
        <a href="{{ url('/professor/home') }}" class="nav-item active">
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
                <i class="fa fa-chalkboard-teacher"></i>
                Professor Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <h1>Home <span>Dashboard</span></h1>
            @if(isset($data) && (int) ($data->role ?? 0) === 1)
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <a href="{{ url('/dashboard') }}" class="btn-switch-view" style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; background:#ffffff; border:1.5px solid #fecaca; border-radius:12px; color:#dc2626; font-weight:700; font-size:13px; text-decoration:none; box-shadow:0 3px 12px rgba(220,38,38,0.1); transition:all 0.25s;">
                        <i class="fa fa-user-shield" style="color:var(--red);"></i>
                        Switch to Coordinator View
                    </a>
                </div>
            @endif
        </div>

        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Welcome to your professor dashboard</h2>
                <p>
                    For first-time users, please watch these short how-to videos to fully set up your account
                    and get familiar with the evaluation workflow.
                </p>
                <div class="welcome-actions">
                    <a href="https://youtu.be/DMKokFwPbDM" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube"></i>
                        Professor Setup Guide
                    </a>
                    <a href="https://youtu.be/2txVame31n0" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube"></i>
                        Student Evaluation Guide
                    </a>
                    <a href="https://www.youtube.com/playlist?list=PLyMOKHLwy4fOSbAPTzUQBSlEcMHoLQvcQ" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube"></i>
                        How To Videos
                    </a>
                </div>
            </div>
            <div class="welcome-icon">
                <i class="fa fa-chalkboard-teacher"></i>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <a href="{{ url('/allStudents') }}" class="stat-card">
                <div class="stat-icon red">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ $roleCount }}</div>
                    <div class="stat-name">Total Students</div>
                </div>
            </a>

            <a href="{{ url('/professor/class') }}" class="stat-card">
                <div class="stat-icon green">
                    <i class="fa fa-chalkboard"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ isset($class) ? count($class) : (isset($classrooms) ? count($classrooms) : 0) }}</div>
                    <div class="stat-name">Active Classes</div>
                </div>
            </a>

            <a href="{{ url('/professor/evaluation') }}" class="stat-card">
                <div class="stat-icon blue">
                    <i class="fa fa-star-half-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-num">View</div>
                    <div class="stat-name">Evaluations</div>
                </div>
            </a>
        </div>

        <!-- Live Academic Chrono & Calendar Hub -->
        <div class="chrono-calendar-hub">
            <!-- Left Pane: Live Time & Date Station -->
            <div class="chrono-station-pane">
                <div class="chrono-pane-top">
                    <div class="chrono-tag-pill">
                        <i class="fa fa-clock"></i>
                        <span>Philippine Standard Time</span>
                        <span class="chrono-utc-tag">UTC+8</span>
                    </div>
                    <div class="chrono-live-chip">
                        <span class="chrono-pulse-core"></span>
                        <span>LIVE</span>
                    </div>
                </div>

                <div class="chrono-digit-display">
                    <!-- Left: Moving Clock Design -->
                    <div class="chrono-moving-clock-wrap">
                        <div class="chrono-analog-clock" id="chronoMovingClock">
                            <div class="clock-dial-marks" id="chronoDialMarks"></div>
                            <div class="chrono-hand chrono-hour-hand" id="chronoHourHand"></div>
                            <div class="chrono-hand chrono-min-hand" id="chronoMinHand"></div>
                            <div class="chrono-hand chrono-sec-hand" id="chronoSecHand"></div>
                            <div class="chrono-clock-pivot"></div>
                        </div>
                    </div>

                    <!-- Right: Hours, Minutes, Seconds Digits -->
                    <div class="chrono-digits-pod-group">
                        <div class="chrono-digit-pod">
                            <span class="pod-val" id="dashHours">--</span>
                            <span class="pod-lbl">Hours</span>
                        </div>
                        <span class="chrono-digit-sep">:</span>
                        <div class="chrono-digit-pod">
                            <span class="pod-val" id="dashMins">--</span>
                            <span class="pod-lbl">Minutes</span>
                        </div>
                        <span class="chrono-digit-sep">:</span>
                        <div class="chrono-digit-pod chrono-sec-pod">
                            <span class="pod-val" id="dashSecs">--</span>
                            <span class="pod-lbl">Seconds</span>
                        </div>
                        <div class="chrono-ampm-badge" id="dashAmPm">--</div>
                    </div>
                </div>

                <div class="chrono-date-strip">
                    <div class="chrono-date-icon-wrap">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                    <div class="chrono-date-meta">
                        <span class="chrono-day-name" id="dashDayName">Loading...</span>
                        <span class="chrono-full-date" id="dashFullDate">Loading date...</span>
                    </div>
                </div>

                <div class="chrono-pane-footer">
                    <div class="chrono-greeting-wrap">
                        <span class="chrono-greeting-icon" id="dashGreetingIcon"><i class="fa fa-sun"></i></span>
                        <span class="chrono-greeting-msg" id="dashGreetingMsg">Good day</span>
                        @if(!empty($data->full_name))
                            <span class="chrono-greeting-user">, {{ $data->full_name }}</span>
                        @endif
                    </div>
                    <div class="chrono-sync-pill">
                        <i class="fa fa-shield-alt"></i> Official Academic Clock
                    </div>
                </div>
            </div>

            <!-- Vertical Divider -->
            <div class="chrono-hub-divider"></div>

            <!-- Right Pane: Interactive Academic Calendar -->
            <div class="chrono-calendar-pane">
                <div class="cal-pane-header">
                    <div class="cal-pane-title-group">
                        <div class="cal-header-icon-box">
                            <i class="fa fa-calendar-alt"></i>
                        </div>
                        <div>
                            <span class="cal-kicker-label">Academic Calendar</span>
                            <h3 class="cal-current-month" id="dashCalMonthLabel">Loading...</h3>
                        </div>
                    </div>
                    <div class="cal-nav-group">
                        <button type="button" class="cal-nav-arrow-btn" id="dashCalPrev" title="Previous Month" aria-label="Previous Month">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button type="button" class="cal-nav-today-btn" id="dashCalToday" title="Jump to Today">
                            Today
                        </button>
                        <button type="button" class="cal-nav-arrow-btn" id="dashCalNext" title="Next Month" aria-label="Next Month">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="cal-pane-grid-wrap">
                    <div class="cal-grid-weekdays">
                        <span>Sun</span>
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                    </div>
                    <div class="cal-grid-days" id="dashCalGrid">
                        <!-- Populated dynamically by JS -->
                    </div>
                </div>

                <div class="cal-pane-footer">
                    <div class="cal-legend-item">
                        <span class="legend-circle legend-today"></span> Today
                    </div>
                    <div class="cal-legend-item">
                        <span class="legend-circle legend-selected"></span> Selected: <strong id="dashCalSelectedLabel">Loading...</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid (2 columns: Students by Class on Left, Bud AI Hero on Right) -->
        <div class="dashboard-grid" style="margin-top:28px; margin-bottom:32px;">
            <!-- Left: Students by Class -->
            <div class="table-card" style="margin-top:0;">
                <div class="table-card-header">
                    <h2>
                        <div class="header-icon"><i class="fa fa-users"></i></div>
                        Students by Class
                    </h2>
                    <div class="table-card-filters">
                        <div class="prof-select-wrap">
                            <i class="fa fa-graduation-cap"></i>
                            <select id="courseFilter" class="prof-filter-select">
                                <option value="">All Courses</option>
                                @if(isset($class))
                                    @foreach($class->pluck('course')->unique() as $course)
                                        <option value="{{ $course }}">{{ $course }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="prof-select-wrap">
                            <i class="fa fa-chalkboard"></i>
                            <select id="classFilter" class="prof-filter-select">
                                <option value="">All Classes</option>
                                @if(isset($class))
                                    @foreach($class as $room)
                                        <option value="{{ $room->room }}" data-course="{{ $room->course }}">{{ $room->room }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-card-body" style="padding: 0;">
                    <table id="studentsTable" class="display" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($class) && count($class) > 0)
                                @foreach($class as $room)
                                    @if(isset($room->students) && count($room->students) > 0)
                                        @foreach($room->students as $student)
                                            <tr data-course="{{ $room->course }}" data-class="{{ $room->room }}">
                                                <td>
                                                    <div class="prof-student-cell">
                                                        <div class="prof-student-avatar">{{ strtoupper(substr($student->full_name, 0, 1)) }}</div>
                                                        <div class="prof-student-name">{{ $student->full_name }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="prof-student-email">{{ $student->email }}</span>
                                                </td>
                                                <td>
                                                    <span class="prof-course-pill">{{ $room->course }}</span>
                                                </td>
                                                <td>
                                                    <span class="prof-class-badge">{{ $room->room }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if(!(isset($class) && count($class) > 0))
                        <div style="color:#888; font-size:13px; padding:18px; text-align:center;">No classes found.</div>
                    @endif
                </div>
            </div>

            <!-- Right: InternConnect AI Assistant -->
            <x-dashboard-ai-hero :role="'professor'" />
        </div>
        <!-- Overall Overview Summary & Visual Trends -->
        <div class="analytics-section-title" style="margin-top:32px;">
            <i class="fa fa-chart-area"></i> Overall Overview Summary & Trends
        </div>

        <div class="analytics-chart-section">
            <div class="panel analytics-chart-card">
                <div class="panel-head analytics-chart-header">
                    <div class="analytics-chart-header-left">
                        <div class="chart-badge-icon">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <div>
                            <h2>Evaluation & Submission Activity</h2>
                            <p>6-Month activity tracking for evaluation requests sent and student responses submitted</p>
                        </div>
                    </div>
                    <div class="analytics-chart-legend">
                        <span class="legend-badge legend-sent">
                            <span class="legend-dot"></span> Requests Sent
                        </span>
                        <span class="legend-badge legend-submitted">
                            <span class="legend-dot"></span> Submitted
                        </span>
                    </div>
                </div>
                <div class="panel-body analytics-chart-body">
                    <div class="chart-canvas-wrapper">
                        <canvas id="monthlyActivityChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="panel analytics-donut-card">
                <div class="panel-head">
                    <h2>Advisee Standing</h2>
                    <p>Status breakdown of all enrolled students</p>
                </div>
                <div class="panel-body analytics-donut-body">
                    <div class="donut-canvas-wrapper">
                        <canvas id="overviewDonutChart"></canvas>
                    </div>
                    <div class="donut-stat-grid">
                        <div class="donut-stat-item item-approved">
                            <span class="stat-lbl"><i class="fa fa-circle"></i> Approved</span>
                            <strong class="stat-val">{{ $approvedStudents ?? 0 }}</strong>
                        </div>
                        <div class="donut-stat-item item-pending">
                            <span class="stat-lbl"><i class="fa fa-circle"></i> Pending</span>
                            <strong class="stat-val">{{ $pendingApprovals ?? 0 }}</strong>
                        </div>
                        <div class="donut-stat-item item-denied">
                            <span class="stat-lbl"><i class="fa fa-circle"></i> Denied</span>
                            <strong class="stat-val">{{ $deniedStudents ?? 0 }}</strong>
                        </div>
                        <div class="donut-stat-item item-inactive">
                            <span class="stat-lbl"><i class="fa fa-circle"></i> Inactive</span>
                            <strong class="stat-val">{{ $inactiveStudents ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Footer -->
    <footer class="dashboard-footer">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
            <span class="footer-copy">
                &copy; 1998–{{ date('Y') }} <span>Polytechnic University of the Philippines</span>
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

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.professorDashboardConfig = {
        approvedStudents: @json($approvedStudents ?? 0),
        pendingApprovals: @json($pendingApprovals ?? 0),
        deniedStudents: @json($deniedStudents ?? 0),
        inactiveStudents: @json($inactiveStudents ?? 0),
        monthlyActivity: @json($monthlyActivity ?? [])
    };
</script>
<script src="{{ vasset('js/professor/home.js') }}?v={{ time() }}" defer></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
@include('partials.password-setup-modal')
@include('students.terms_modal')
<script src="{{ vasset('js/mobile-utils.js') }}"></script>
</body>
</html>