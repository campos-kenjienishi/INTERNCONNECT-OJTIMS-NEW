<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Student Requests</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/professor_listStudents-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/list-students.css') }}">
</head>


<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">

    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJT IMS</span>
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
                <h1>Student <span>Requests</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/professor/class') }}">Class</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Student Requests</span>
                </div>
            </div>
            <button class="btn-back"
                onclick="window.location.href='{{ url('/professor/class') }}'">
                <i class="fa fa-arrow-left"></i> Back to Class
            </button>
        </div>

        <!-- Stats Row -->
        @php $totalRequests = count($students); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-user-clock"></i></div>
                <div>
                    <div class="stat-num">{{ $totalRequests }}</div>
                    <div class="stat-name">Pending Requests</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num"><i class="fa fa-check" style="font-size:18px;"></i></div>
                    <div class="stat-name">Approve to Enroll</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-times-circle"></i></div>
                <div>
                    <div class="stat-num"><i class="fa fa-times" style="font-size:18px;"></i></div>
                    <div class="stat-name">Deny with Reason</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-clipboard-list"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Enrollment Review</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-inbox"></i></div>
                    <div>
                        <h2>Student Requests</h2>
                        <p>Approve or deny student OJT enrollment requests</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    @if($totalRequests > 0)
                        <form id="approveAllStudentsForm"
                              method="POST"
                              action="{{ url('/professor/approve-all/' . $course->id) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-approve-all">
                                <i class="fa fa-check-double"></i> Approve All
                            </button>
                        </form>
                    @endif
                    <div class="request-count-badge">
                        <i class="fa fa-user-clock"></i>
                        {{ $totalRequests }} {{ $totalRequests == 1 ? 'request' : 'requests' }}
                    </div>
                </div>
            </div>

            <div class="table-card-body">
                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Year &amp; Section</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                        <tr>
                            <!-- Student Name -->
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                    </div>
                                    <span class="student-name-text">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </span>
                                </div>
                            </td>

                            <!-- Course -->
                            <td>
                                <span class="course-badge">
                                    <i class="fa fa-graduation-cap" style="font-size:10px;"></i>
                                    {{ $student->course }}
                                </span>
                            </td>

                            <!-- Year & Section -->
                            <td>
                                <span class="section-badge">
                                    <i class="fa fa-users" style="font-size:10px;"></i>
                                    {{ $student->year_and_section }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">

                                    <!-- Approve Form -->
                                    <form class="approveForm" method="POST"
                                          action="{{ url('professor/approve', $student->email) }}"
                                          style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-approve">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                    </form>

                                    <!-- Deny — triggers modal with student data -->
                                    <button class="btn-deny open-deny-modal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#denyModal"
                                        data-name="{{ $student->first_name }} {{ $student->last_name }}"
                                        data-course="{{ $student->course }}"
                                        data-email="{{ $student->email }}">
                                        <i class="fa fa-times"></i> Deny
                                    </button>

                                </div>
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
        <a href="{{ url('/terms') }}">Terms of Use</a>
        <span class="divider">|</span>
        <a href="{{ url('/privacy') }}">Privacy Statement</a>
    </div>
</footer>
</div>

<!-- =============== DENY MODAL (single, reused) =============== -->
<div class="modal fade" id="denyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-times-circle"></i> Reason to Deny
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="denyForm" method="POST" action="" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">

                    <!-- Student banner populated by JS -->
                    <div class="deny-student-banner">
                        <div class="deny-student-avatar" id="denyAvatar"></div>
                        <div>
                            <div class="deny-student-name" id="denyStudentName"></div>
                            <div class="deny-student-sub" id="denyStudentCourse"></div>
                        </div>
                    </div>

                    <label class="reason-label">
                        <i class="fa fa-comment-alt"></i> Reason for Denial
                    </label>
                    <textarea class="reason-textarea" id="reason" name="reason"
                              rows="4" placeholder="Explain why this student's request is being denied..."
                              required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-deny">
                        <i class="fa fa-ban me-1"></i> Deny Request
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/list-students.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>