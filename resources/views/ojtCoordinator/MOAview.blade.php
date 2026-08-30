<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Company Information</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/coordinator/moa-view.css') }}?v={{ time() }}">
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
        <a href="{{ url('/MOA') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <div class="nav-group-reports">
            <a href="{{ url('/reports') }}" class="nav-item nav-item-reports">
                <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
                <span class="nav-label">Reports</span>
                <span class="tooltip-label">Reports</span>
            </a>
            <div class="nav-sub">
                <a href="{{ url('/reports') }}" class="nav-sub-item">
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
        <li>
    <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
        </a>
</li>
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
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">
                On-the-Job Training <span>Information Management System</span>
            </span>
        </div>
        <div class="topbar-badge">
            <i class="fa fa-user-shield"></i>
            OJT Coordinator
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Company <span>Information</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/MOA') }}">MOA</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Company Info</span>
                </div>
            </div>
            <a href="{{ url('/MOA') }}" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back to MOA
            </a>
        </div>

        <!-- Company Info Card -->
        <div class="panel-card">
            <div class="panel-card-header">
                <div class="panel-header-icon">
                    <i class="fa fa-building"></i>
                </div>
                <div>
                    <h2>{{ $company->company_name }}</h2>
                    <p>Partner company details for this MOA agreement</p>
                </div>
            </div>
            <div class="panel-card-body">
                <div class="company-info-grid">

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-building"></i></div>
                        <div>
                            <div class="info-item-label">Company Name</div>
                            <div class="info-item-value">{{ $company->company_name }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div>
                            <div class="info-item-label">Company Address</div>
                            <div class="info-item-value">{{ $company->company_address }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-user-tie"></i></div>
                        <div>
                            <div class="info-item-label">Representative</div>
                            <div class="info-item-value">{{ $company->company_rep }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <div class="info-item-label">Contact Number</div>
                            <div class="info-item-value">{{ $company->companyNo }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-envelope"></i></div>
                        <div>
                            <div class="info-item-label">Email Address</div>
                            <div class="info-item-value">{{ $company->company_email }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-calendar-alt"></i></div>
                        <div>
                            <div class="info-item-label">School Year</div>
                            <div class="info-item-value">{{ $company->school_year }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-calendar-check"></i></div>
                        <div>
                            <div class="info-item-label">Date Notarized</div>
                            <div class="info-item-value">
                                @if(!empty($company->date_notarized))
                                    {{ \Carbon\Carbon::parse($company->date_notarized)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-hourglass-end"></i></div>
                        <div>
                            <div class="info-item-label">Validity Period</div>
                            <div class="info-item-value">
                                @if(!empty($company->valid_until))
                                    {{ \Carbon\Carbon::parse($company->valid_until)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Two-column: Students + MOA Viewer -->
        <div class="detail-grid">

            <!-- ===== LEFT: Student List ===== -->
            <div class="panel-card" style="margin-bottom:0;">
                <div class="panel-card-header">
                    <div class="panel-header-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        @php
                            $displayStudents = collect(array_filter(array_map('trim', explode(',', (string) ($company->student_names_display ?? '')))))->values();
                            $linkedStudents = $company->students;
                            $studentCount = $displayStudents->isNotEmpty() ? $displayStudents->count() : $linkedStudents->count();
                        @endphp
                        <h2>Student List</h2>
                        <p>
                            {{ $studentCount }}
                            {{ $studentCount == 1 ? 'student' : 'students' }}
                            assigned to this company
                        </p>
                    </div>
                </div>
                <div class="panel-card-body">
                    <div class="student-search-wrap">
                        <input
                            type="text"
                            id="studentSearchInput"
                            class="student-search-input"
                            placeholder="Search assigned students by name"
                        >
                    </div>
                    <div class="student-list">
                        @if ($displayStudents->isNotEmpty())
                            @foreach ($displayStudents as $displayStudent)
                                @php $matchedStudent = $linkedStudents->firstWhere('full_name', $displayStudent); @endphp
                                @if ($matchedStudent)
                                <div class="student-card">
                                    <div class="student-card-header">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($matchedStudent->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $matchedStudent->full_name }}</div>
                                            <span class="student-course">{{ $matchedStudent->course }}</span>
                                        </div>
                                    </div>

                                    <div class="student-detail-row">
                                        <i class="fa fa-id-card"></i>
                                        <span><strong>Student No:</strong> {{ $matchedStudent->studentNum }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-envelope"></i>
                                        <span>{{ $matchedStudent->email }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-birthday-cake"></i>
                                        <span><strong>DOB:</strong> {{ $matchedStudent->date_of_birth }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-phone"></i>
                                        <span>{{ $matchedStudent->contact_number }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-map-marker-alt"></i>
                                        <span>{{ $matchedStudent->address }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-layer-group"></i>
                                        <span>{{ $matchedStudent->year_and_section }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-chalkboard-teacher"></i>
                                        <span><strong>Adviser:</strong> {{ $matchedStudent->adviser_name }}</span>
                                    </div>
                                </div>
                                @else
                                <div class="student-card">
                                    <div class="student-card-header">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($displayStudent, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $displayStudent }}</div>
                                            <span class="student-course">{{ $company->course ?: 'Manual entry' }}</span>
                                        </div>
                                    </div>

                                    <div class="student-detail-row">
                                        <i class="fa fa-keyboard"></i>
                                        <span>This student was entered manually for this MOA.</span>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            @forelse ($linkedStudents as $student)
                            <div class="student-card">
                                <div class="student-card-header">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="student-name">{{ $student->full_name }}</div>
                                        <span class="student-course">{{ $student->course }}</span>
                                    </div>
                                </div>

                                <div class="student-detail-row">
                                    <i class="fa fa-id-card"></i>
                                    <span><strong>Student No:</strong> {{ $student->studentNum }}</span>
                                </div>
                                <div class="student-detail-row">
                                    <i class="fa fa-envelope"></i>
                                    <span>{{ $student->email }}</span>
                                </div>
                                <div class="student-detail-row">
                                    <i class="fa fa-birthday-cake"></i>
                                    <span><strong>DOB:</strong> {{ $student->date_of_birth }}</span>
                                </div>
                                <div class="student-detail-row">
                                    <i class="fa fa-phone"></i>
                                    <span>{{ $student->contact_number }}</span>
                                </div>
                                <div class="student-detail-row">
                                    <i class="fa fa-map-marker-alt"></i>
                                    <span>{{ $student->address }}</span>
                                </div>
                                <div class="student-detail-row">
                                    <i class="fa fa-layer-group"></i>
                                    <span>{{ $student->year_and_section }}</span>
                                </div>
                                <div class="student-detail-row">
                                    <i class="fa fa-chalkboard-teacher"></i>
                                    <span><strong>Adviser:</strong> {{ $student->adviser_name }}</span>
                                </div>
                            </div>
                            @empty
                            <div style="text-align:center; padding:40px 20px; color:#aaa;">
                                <i class="fa fa-users" style="font-size:40px; margin-bottom:12px; display:block; color:#fecaca;"></i>
                                <div style="font-size:14px; font-weight:600; color:#888;">No students assigned</div>
                                <div style="font-size:12.5px; margin-top:4px;">No students are linked to this company yet.</div>
                            </div>
                            @endforelse
                        @endif
                        <div id="studentNoMatch" class="student-no-match">
                            No assigned student matched your search.
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT: MOA Viewer ===== -->
            <div class="moa-viewer-card">
                <div class="moa-viewer-header">
                    <div class="panel-header-icon">
                        <i class="fa fa-file-contract"></i>
                    </div>
                    <div>
                        <h3>Memorandum of Agreement</h3>
                        <p>Official MOA document for {{ $company->company_name }}</p>
                    </div>
                </div>
                <div class="moa-iframe-wrap">
                    <iframe src="/assets/{{ $company->file }}"
                            title="MOA Document">
                    </iframe>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script src="{{ vasset('js/coordinator/moa-view.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
