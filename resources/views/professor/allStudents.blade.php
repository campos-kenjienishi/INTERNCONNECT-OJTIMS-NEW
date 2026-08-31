<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - All Students</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet" href="{{ vasset('css/professor/all-students.css') }}">
</head>


<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">

    <a href="#" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJT IMS</span>
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
        <div class="page-header">
            <div>
                <h1>All <span>Students</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>All Students</span>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ count($studentData) }}</div>
                    <div class="stat-name">Total Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-graduation-cap"></i></div>
                <div>
                    <div class="stat-num">
                        {{ count(collect($studentData)->pluck('student.course')->unique()) }}
                    </div>
                    <div class="stat-name">Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-chalkboard"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Program</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-book"></i></div>
                <div>
                    <div class="stat-num">
                        {{ count(collect($studentData)->pluck('student.year_and_section')->unique()) }}
                    </div>
                    <div class="stat-name">Sections</div>
                </div>
            </div>
        </div>

        <!-- Students Table Card -->
        <div class="table-card">
            <div class="table-card-header" style="flex-wrap: wrap; gap: 16px;">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-users"></i></div>
                    <div>
                        <h2>Student List</h2>
                        <p>All enrolled OJT students and their school years</p>
                    </div>
                </div>
                <form method="GET" action="" style="display: flex; align-items: center; gap: 10px;">
                    <label for="course" style="margin-bottom:0; font-size:13px; font-weight:600;">Filter by Course:</label>
                    <select name="course" id="course" class="form-select" style="width:auto; min-width:160px;" onchange="this.form.submit()">
                        <option value="">All Courses</option>
                        @foreach($course as $c)
                            <option value="{{ $c->course }}" {{ request('course') == $c->course ? 'selected' : '' }}>{{ $c->course }}</option>
                        @endforeach
                    </select>
                </form>
                <div class="student-count-badge">
                    <i class="fa fa-user-graduate"></i>
                    {{ count($studentData) }} {{ count($studentData) == 1 ? 'student' : 'students' }}
                </div>
            </div>

            <div class="table-card-body">

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Year & Section</th>
                            <th>School Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentData as $data)
                        @php
                            $syDisplay = $data['school_year'] ?? '—';
                            if ($syDisplay === '—' && isset($data['student'])) {
                                $stInfo = $data['student']->studentInfo;
                                $sStart = $stInfo->school_year_start ?? $data['student']->school_year_start ?? '';
                                $sEnd = $stInfo->school_year_end ?? $data['student']->school_year_end ?? '';
                                if (!empty($sStart) && !empty($sEnd)) {
                                    $syDisplay = $sStart . ' - ' . $sEnd;
                                } elseif (!empty($sStart)) {
                                    $syDisplay = $sStart;
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($data['student']->first_name, 0, 1)) }}
                                    </div>
                                    <span class="student-name-text">
                                        {{ $data['student']->first_name }} {{ $data['student']->last_name }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="course-badge">
                                    <i class="fa fa-graduation-cap" style="font-size:10px;"></i>
                                    {{ $data['student']->course }}
                                </span>
                            </td>
                            <td>
                                <span class="section-badge">
                                    <i class="fa fa-users" style="font-size:10px;"></i>
                                    {{ $data['student']->year_and_section }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($syDisplay) && $syDisplay !== '—')
                                    <span class="school-year-badge">
                                        <i class="fa fa-calendar-alt" style="font-size:10px;"></i>
                                        {{ $syDisplay }}
                                    </span>
                                @else
                                    <span style="color:#bbb; font-size:13px;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/all-students.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
<script src="{{ vasset('js/mobile-utils.js') }}"></script>
</body>
</html>