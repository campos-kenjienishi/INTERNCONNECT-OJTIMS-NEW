<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Student Dashboard</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/student_home-responsive.css') }}">
    <script src="{{ vasset('assets/js/dark-mode.js') }}"></script>

    <link rel="stylesheet" href="{{ vasset('css/student/home.css') }}">
</head>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa fa-user" aria-hidden="true"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-home" aria-hidden="true"></i></span>
            <span class="nav-label">Home</span>
            <span class="tooltip-label">Home</span>
        </a>
        <a href="{{ url('/student/ojtinfo') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-layer-group" aria-hidden="true"></i></span>
            <span class="nav-label">OJT Information</span>
            <span class="tooltip-label">OJT Information</span>
        </a>
        <a href="{{ url('/student/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ url('/student/files') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-download" aria-hidden="true"></i></span>
            <span class="nav-label">Downloadable Files</span>
            <span class="tooltip-label">Downloadable Files</span>
        </a>
        <a href="{{ url('/student/MOA') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-alt" aria-hidden="true"></i></span>
            <span class="nav-label">Notarized MOA</span>
            <span class="tooltip-label">Notarized MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt" aria-hidden="true"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
        </a>
        <a href="{{ url('/student/evaluation') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-star-half-alt" aria-hidden="true"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/logout') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-sign-out-alt" aria-hidden="true"></i></span>
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
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar"><i class="fa fa-bars" aria-hidden="true"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode" aria-label="Toggle dark mode">
                <i class="fa fa-moon" aria-hidden="true"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <h1>Home <span>Dashboard</span></h1>
            <div class="date-badge" id="dateBadge" title="Click to view calendar & clock" role="button" tabindex="0" aria-label="Open calendar and clock">
                <span class="pulse-dot" aria-hidden="true"></span>
                <i class="fa fa-calendar-alt" aria-hidden="true"></i>
                <span id="currentDate"></span>
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>
        </div>

        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Welcome to your student dashboard</h2>
                <p>
                    For first-time users, please watch these short how-to videos to fully set up your account
                    and understand the evaluation process.
                </p>
                <div class="welcome-actions">
                    <a href="https://youtu.be/H0ek8it4jKc" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        Student Guide
                    </a>
                    <a href="https://youtu.be/jhLuCIX6yhw" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        Evaluation Guide
                    </a>
                    <a href="https://www.youtube.com/playlist?list=PLyMOKHLwy4fPzwbJ0RsqgHRhvx2Ok1wH3" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        How To Videos
                    </a>
                </div>
            </div>
            <div class="welcome-icon">
                <i class="fa fa-user-graduate" aria-hidden="true"></i>
            </div>
        </div>

        <!--
            Stats Row — now driven by real status values instead of repeating each
            card's own label. Controller should pass:
              $fileCount              (int)
              $requirementsSubmitted  (int)
              $requirementsTotal      (int)
              $ojtInfoComplete        (bool)
              $evaluationStatus       (string: 'pending' | 'completed' | null)
        -->
        <div class="stats-grid">
            <a href="{{ url('/student/files') }}" class="stat-card">
                <div class="stat-icon red"><i class="fa fa-cloud-download-alt" aria-hidden="true"></i></div>
                <div class="stat-info">
                    @if($fileCount > 0)
                        <div class="stat-num">{{ $fileCount }}</div>
                    @else
                        <div class="stat-num stat-num--empty">None yet</div>
                    @endif
                    <div class="stat-name">Downloadable Templates</div>
                </div>
            </a>
            <a href="{{ url('/student/requirements') }}" class="stat-card">
                <div class="stat-icon green"><i class="fa fa-tasks" aria-hidden="true"></i></div>
                <div class="stat-info">
                    <div class="stat-num">{{ $requirementsSubmitted ?? 0 }}/{{ $requirementsTotal ?? 0 }}</div>
                    <div class="stat-name">Requirements Submitted</div>
                </div>
            </a>
            <a href="{{ url('/student/ojtinfo') }}" class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
                <div class="stat-info">
                    @if(!empty($ojtInfoComplete))
                        <div class="stat-num">Complete</div>
                    @else
                        <div class="stat-num stat-num--empty">Not set up</div>
                    @endif
                    <div class="stat-name">OJT Information</div>
                </div>
            </a>
            <a href="{{ url('/student/evaluation') }}" class="stat-card">
                <div class="stat-icon red"><i class="fa fa-star-half-alt" aria-hidden="true"></i></div>
                <div class="stat-info">
                    @if(($evaluationStatus ?? null) === 'completed')
                        <div class="stat-num">Completed</div>
                    @else
                        <div class="stat-num stat-num--empty">Pending</div>
                    @endif
                    <div class="stat-name">Supervisor Evaluation</div>
                </div>
            </a>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dash-grid">

            <!-- LEFT: Quick Actions + Announcements -->
            <div style="display:flex; flex-direction:column; gap:22px;">

                <!-- Quick Actions -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-bolt" aria-hidden="true"></i></div>
                        <div>
                            <h2>Quick Actions</h2>
                            <p>Jump to the most important sections</p>
                        </div>
                    </div>
                    <div class="quick-actions-wrap">
                        <a href="{{ url('/student/ojtinfo') }}" class="qa-item">
                            <div class="qa-icon-wrap red"><i class="fa fa-layer-group" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">OJT Information</div>
                                <div class="qa-desc">View your OJT details & status</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/class') }}" class="qa-item">
                            <div class="qa-icon-wrap blue"><i class="fa fa-clipboard" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">My Class</div>
                                <div class="qa-desc">View your class & professor</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/files') }}" class="qa-item">
                            <div class="qa-icon-wrap green"><i class="fa fa-download" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">Downloadable Files</div>
                                <div class="qa-desc">Get templates & forms</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/MOA') }}" class="qa-item">
                            <div class="qa-icon-wrap purple"><i class="fa fa-file-alt" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">MOA</div>
                                <div class="qa-desc">Memorandum of Agreement</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/requirements') }}" class="qa-item" style="border-bottom:none;">
                            <div class="qa-icon-wrap amber"><i class="fa fa-cloud-upload-alt" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">Requirements</div>
                                <div class="qa-desc">Submit & track your documents</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></div>
                        <div>
                            <h2>Announcements</h2>
                            <p>Latest updates from your coordinator</p>
                        </div>
                    </div>
                    @if(isset($announcements) && count($announcements) > 0)
                        @foreach($announcements as $ann)
                        <div class="announcement-item">
                            <div class="ann-dot" aria-hidden="true"></div>
                            <div>
                                <div class="ann-title">{{ $ann->title }}</div>
                                <div class="ann-date">{{ \Carbon\Carbon::parse($ann->created_at)->format('M d, Y') }}</div>
                                <div class="ann-body">{{ Str::limit($ann->content, 120) }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-ann">
                            <i class="fa fa-bell-slash" aria-hidden="true"></i>
                            No announcements yet. Check back later.
                        </div>
                    @endif
                </div>

            </div>

            <!-- RIGHT: Journey + Tips -->
            <div class="dash-right-col">

                <!-- OJT Journey -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-chart-line" aria-hidden="true"></i></div>
                        <div>
                            <h2>OJT Guidelines</h2>
                            <p>Your internship milestones</p>
                        </div>
                    </div>
                    <!--
                        Graph-style milestone tracker. Drive state from the controller via
                        $ojtStage — one of: 'account_created', 'requirements', 'moa', 'completed'.
                        Each stage is considered "done" once the student has passed it, and
                        "active" if it's their current stage.
                    -->
                    @php
                        $ojtStages = ['account_created', 'requirements', 'moa', 'completed'];
                        $currentIndex = array_search($ojtStage ?? 'account_created', $ojtStages);
                        $currentIndex = $currentIndex === false ? 0 : $currentIndex;
                    @endphp
                    <div class="milestone-wrap" role="list" aria-label="OJT journey progress">
                        <div class="ojt-track">
                            @php
                                $steps = [
                                    ['key' => 'account_created', 'icon' => 'fa-check',          'title' => 'Account Created', 'sub' => "You're registered in the system"],
                                    ['key' => 'requirements',    'icon' => 'fa-circle',          'title' => 'Submit Requirements', 'sub' => 'Upload your required documents'],
                                    ['key' => 'moa',             'icon' => 'fa-file-signature',  'title' => 'MOA Signing', 'sub' => 'Agreement with your company'],
                                    ['key' => 'completed',       'icon' => 'fa-flag',            'title' => 'OJT Completion', 'sub' => 'Finish your internship hours'],
                                ];
                            @endphp
                            @foreach($steps as $i => $step)
                                @php
                                    $isDone   = $i < $currentIndex;
                                    $isActive = $i === $currentIndex;
                                @endphp
                                <div class="ojt-step" role="listitem">
                                    @if($i < count($steps) - 1)
                                        <div class="ojt-line {{ $isDone ? 'filled' : '' }}"></div>
                                    @endif
                                    <div class="ojt-dot {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                                        <i class="fa {{ $isDone ? 'fa-check' : $step['icon'] }}" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <div class="ojt-title">{{ $step['title'] }}</div>
                                        <div class="ojt-sub">{{ $step['sub'] }}</div>
                                        @if($isDone)
                                            <span class="ojt-badge done">Done</span>
                                        @elseif($isActive)
                                            <span class="ojt-badge active">In progress</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Helpful Tips -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-lightbulb" aria-hidden="true"></i></div>
                        <div>
                            <h2>Helpful Tips</h2>
                            <p>Make the most of your OJT</p>
                        </div>
                    </div>
                    <div class="tips-wrap">
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-clock" aria-hidden="true"></i></div>
                            <div class="tip-text">Submit your requirements before the deadline to avoid delays in your OJT approval.</div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-file-alt" aria-hidden="true"></i></div>
                            <div class="tip-text">Download all necessary templates from the Files section early to stay prepared.</div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-user-tie" aria-hidden="true"></i></div>
                            <div class="tip-text">Keep your OJT Information updated with your company details and supervisor.</div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-bell" aria-hidden="true"></i></div>
                            <div class="tip-text">Check the Announcements section regularly for important updates from your coordinator.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-inner">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP logo">
            <span class="footer-copy">
                © 1998–2026 <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div class="footer-links">
            <a href="https://www.pup.edu.ph/" target="_blank">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;" aria-hidden="true"></i>
                PUP Website
            </a>
            <span class="divider">|</span>
            <a href="{{ url('/terms') }}">Terms of Use</a>
            <span class="divider">|</span>
            <a href="{{ url('/privacy') }}">Privacy Statement</a>
        </div>
    </footer>

</div>

<!-- =============== DATE & TIME MODAL =============== -->
<div class="dt-overlay" id="dtOverlay">
    <div class="dt-modal" id="dtModal">
        <div class="dt-modal-header">
            <div class="dt-header-top">
                <span class="dt-header-title"><i class="fa fa-clock" style="margin-right:6px;" aria-hidden="true"></i>Date & Time</span>
                <button class="dt-close-btn" id="dtCloseBtn" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="dt-clock-display">
                <div class="dt-time-big">
                    <span id="dtHours">00</span>
                    <span class="colon">:</span>
                    <span id="dtMinutes">00</span>
                    <span class="colon">:</span>
                    <span id="dtSeconds">00</span>
                    <span class="dt-time-ampm" id="dtAmPm">AM</span>
                </div>
                <div class="dt-date-sub" id="dtDateSub"></div>
            </div>
        </div>
        <div class="dt-analog-wrap">
            <div class="analog-clock" id="analogClock">
                <div class="clock-center"></div>
                <div class="hand hour-hand"   id="hourHand"></div>
                <div class="hand minute-hand" id="minuteHand"></div>
                <div class="hand second-hand" id="secondHand"></div>
            </div>
        </div>
        <div class="dt-calendar">
            <div class="cal-nav">
                <button class="cal-nav-btn" id="calPrev" aria-label="Previous month"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                <span class="cal-month-label" id="calMonthLabel"></span>
                <button class="cal-nav-btn" id="calNext" aria-label="Next month"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
            </div>
            <div class="cal-grid" id="calGrid"></div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="{{ vasset('js/student/home.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
    @include('partials.password-setup-modal')
    @include('students.terms_modal')
</body>
</html>