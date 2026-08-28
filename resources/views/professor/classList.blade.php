<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Class List</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/professor_classList-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/class-list.css') }}">
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
        <a href="{{ url('/professor/class') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-check"></i></span>
            <span class="nav-label">Req. Status</span>
            <span class="tooltip-label">Req. Status</span>
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
                <h1>Class <span>List</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/professor/class') }}">Class</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Student List</span>
                </div>
            </div>
            <a class="btn-back"
                href="{{ url('/professor/class') }}">
                <i class="fa fa-arrow-left"></i> Back to Class
            </a>
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
                <div class="stat-icon purple"><i class="fa fa-graduation-cap"></i></div>
                <div>
                    <div class="stat-num">
                        {{ count(collect($studentData)->pluck('student.course')->unique()) }}
                    </div>
                    <div class="stat-name">Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-layer-group"></i></div>
                <div>
                    <div class="stat-num">
                        {{ count(collect($studentData)->pluck('student.year_and_section')->unique()) }}
                    </div>
                    <div class="stat-name">Sections</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-briefcase"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Program</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-users"></i></div>
                    <div>
                        <h2>Student List</h2>
                        <p>View personal info, OJT details and submitted requirements</p>
                    </div>
                </div>
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
                            <th>Year &amp; Section</th>
                            <th>School Year</th>
                            <th>Personal Info</th>
                            <th>OJT Info</th>
                            <th>Requirements</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentData as $item)
                        <tr>
                            <!-- Student Name -->
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($item['student']->full_name, 0, 1)) }}
                                    </div>
                                    <span class="student-name-text">{{ $item['student']->full_name }}</span>
                                </div>
                            </td>

                            <!-- Course -->
                            <td>
                                <span class="course-badge">
                                    <i class="fa fa-graduation-cap" style="font-size:10px;"></i>
                                    {{ $item['student']->course }}
                                </span>
                            </td>

                            <!-- Year & Section -->
                            <td>
                                <span class="section-badge">
                                    <i class="fa fa-users" style="font-size:10px;"></i>
                                    {{ $item['student']->year_and_section }}
                                </span>
                            </td>

                            <!-- School Year -->
                            <td>
                                <span class="sy-badge">
                                    <i class="fa fa-calendar" style="font-size:10px;"></i>
                                    {{ $item['student']->studentInfo->school_year_start ?? '-' }}
                                    –
                                    {{ $item['student']->studentInfo->school_year_end ?? '-' }}
                                </span>
                            </td>

                            <!-- Personal Info -->
                            <td>
                                <button class="btn-view-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#personalInfoModal"
                                    data-full-name="{{ $item['student']->full_name }}"
                                    data-contact-number="{{ $item['student']->contact_number }}"
                                    data-email="{{ $item['student']->email }}"
                                    data-address="{{ $item['student']->address }}"
                                    data-date-of-birth="{{ $item['student']->date_of_birth }}"
                                    data-student-num="{{ $item['ojt']->studentNum ?? '—' }}">
                                    <i class="fa fa-user"></i> View
                                </button>
                            </td>

                            <!-- OJT Info -->
                            <td>
                                <button class="btn-view-ojt"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ojtInfoModal"
                                    data-full-name="{{ $item['student']->full_name }}"
                                    data-company-name="{{ $item['ojt']->company_name ?? '—' }}"
                                    data-company-address="{{ $item['ojt']->company_address ?? '—' }}"
                                    data-nature-of-business="{{ $item['ojt']->nature_of_bus ?? '—' }}"
                                    data-nature-of-linkages="{{ $item['ojt']->nature_of_link ?? '—' }}"
                                    data-level="{{ $item['ojt']->level ?? '—' }}"
                                    data-start-date="{{ $item['ojt']->start_date ?? '—' }}"
                                    data-finish-date="{{ $item['ojt']->finish_date ?? '—' }}"
                                    data-report-time="{{ $item['ojt']->report_time ?? '—' }}"
                                    data-contact-name="{{ $item['ojt']->contact_name ?? '—' }}"
                                    data-contact-position="{{ $item['ojt']->contact_position ?? '—' }}"
                                    data-contact-number="{{ $item['ojt']->contact_number ?? '—' }}">
                                    <i class="fa fa-briefcase"></i> View
                                </button>
                            </td>

                            <!-- Requirements -->
                            <td>
                                <a class="btn-view-req"
                                    href="{{ url('/studentrequire') }}?value={{ urlencode($item['student']->full_name) }}&roomId={{ $course->id }}">
                                    <i class="fa fa-file-alt"></i> Requirements
                                </a>
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

<!-- =============== PERSONAL INFO MODAL =============== -->
<div class="modal fade" id="personalInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user"></i> Personal Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-student-banner">
                    <div class="modal-student-avatar" id="pi-avatar"></div>
                    <div>
                        <div class="modal-student-name" id="pi-full-name"></div>
                        <div class="modal-student-sub">Student Personal Details</div>
                    </div>
                </div>

                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-id-card"></i></div>
                    <div>
                        <div class="modal-info-label">Student Number</div>
                        <div class="modal-info-value" id="pi-student-num"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-phone"></i></div>
                    <div>
                        <div class="modal-info-label">Contact Number</div>
                        <div class="modal-info-value" id="pi-contact-number"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-envelope"></i></div>
                    <div>
                        <div class="modal-info-label">Email Address</div>
                        <div class="modal-info-value" id="pi-email"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div>
                        <div class="modal-info-label">Address</div>
                        <div class="modal-info-value" id="pi-address"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-birthday-cake"></i></div>
                    <div>
                        <div class="modal-info-label">Date of Birth</div>
                        <div class="modal-info-value" id="pi-dob"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== OJT INFO MODAL =============== -->
<div class="modal fade" id="ojtInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-briefcase"></i> OJT Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-student-banner">
                    <div class="modal-student-avatar" id="ojt-avatar"></div>
                    <div>
                        <div class="modal-student-name" id="ojt-full-name"></div>
                        <div class="modal-student-sub">OJT Details</div>
                    </div>
                </div>

                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-building"></i></div>
                    <div>
                        <div class="modal-info-label">Company Name</div>
                        <div class="modal-info-value" id="ojt-company-name"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div>
                        <div class="modal-info-label">Company Address</div>
                        <div class="modal-info-value" id="ojt-company-address"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-industry"></i></div>
                    <div>
                        <div class="modal-info-label">Nature of Business</div>
                        <div class="modal-info-value" id="ojt-nature-business"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-link"></i></div>
                    <div>
                        <div class="modal-info-label">Nature of Linkages</div>
                        <div class="modal-info-value" id="ojt-nature-link"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-layer-group"></i></div>
                    <div>
                        <div class="modal-info-label">Level</div>
                        <div class="modal-info-value" id="ojt-level"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-calendar-alt"></i></div>
                    <div>
                        <div class="modal-info-label">Start Date</div>
                        <div class="modal-info-value" id="ojt-start-date"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-calendar-check"></i></div>
                    <div>
                        <div class="modal-info-label">End Date</div>
                        <div class="modal-info-value" id="ojt-finish-date"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-clock"></i></div>
                    <div>
                        <div class="modal-info-label">Reporting Time</div>
                        <div class="modal-info-value" id="ojt-report-time"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-user-tie"></i></div>
                    <div>
                        <div class="modal-info-label">Contact Person</div>
                        <div class="modal-info-value" id="ojt-contact-name"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-id-badge"></i></div>
                    <div>
                        <div class="modal-info-label">Position</div>
                        <div class="modal-info-value" id="ojt-contact-position"></div>
                    </div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa fa-phone"></i></div>
                    <div>
                        <div class="modal-info-label">Contact Number</div>
                        <div class="modal-info-value" id="ojt-contact-number"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/class-list.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>