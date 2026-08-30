<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Students</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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

    <link rel="stylesheet" href="{{ vasset('css/coordinator/students.css') }}?v={{ time() }}">
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

    <a href="{{ url('/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($user->profile_photo) && $user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ is_object($user) ? ($user->full_name ?? 'OJT Coordinator') : 'OJT Coordinator' }}</span>
            <span class="user-role">OJT Coordinator</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/dashboard') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Dashboard</span>
            <span class="tooltip-label">Dashboard</span>
        </a>
        <a href="{{ url('/studentLists') }}" class="nav-item active">
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
                <h1>Active <span>Students</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Students</span>
                </div>
            </div>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap: wrap;">
                <button type="button" id="btnSyncIdp" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #fff; border: none; border-radius: 10px; padding: 10px 16px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(79,70,229,0.3);">
                    <i class="fa fa-id-badge" id="idpSyncIcon"></i>
                    <span>Sync IDP UUIDs</span>
                </button>
                <button type="button" id="btnSyncGuisis" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); color: #fff; border: none; border-radius: 10px; padding: 10px 16px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(13,148,136,0.3);">
                    <i class="fa fa-graduation-cap" id="guisisSyncIcon"></i>
                    <span>Sync GuiSIS</span>
                </button>
                <a href="{{ route('coordinator.studentRequirements') }}" class="btn" style="background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%); color: #ffffff !important; border: none; border-radius: 10px; padding: 10px 20px; font-size: 13px; font-weight: 600; text-decoration: none !important; box-shadow: 0 4px 14px rgba(127,0,0,0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa fa-folder-open"></i> Student Requirements Matrix
                </a>
                <button class="btn-back" onclick="window.location.href='{{ url('/dashboard') }}'">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </button>
            </div>
        </div>

        <!-- Stats Row -->
        @php 
            $totalStudents = count($studentData); 
            $idpLinkedTotal = \App\Models\User::whereNotNull('idp_user_id')->where('idp_user_id', '!=', '')->count();
        @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ $totalStudents }}</div>
                    <div class="stat-name">Active Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa fa-graduation-cap"></i></div>
                <div>
                    <div class="stat-num">{{ count(collect($studentData)->pluck('student')->pluck('course')->unique()) }}</div>
                    <div class="stat-name">Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-layer-group"></i></div>
                <div>
                    <div class="stat-num">{{ count(collect($studentData)->pluck('student')->pluck('year_and_section')->unique()) }}</div>
                    <div class="stat-name">Sections</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-chalkboard-teacher"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Active Program</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(79,70,229,0.12); color: #7c3aed;"><i class="fa fa-link"></i></div>
                <div>
                    <div class="stat-num">{{ $idpLinkedTotal }}</div>
                    <div class="stat-name">IDP Linked</div>
                </div>
            </div>
        </div>

        @php
            $courseOptions = collect($studentData)
                ->pluck('student')
                ->pluck('course')
                ->filter()
                ->unique()
                ->sort()
                ->values();
            $schoolYearOptions = collect($studentData)
                ->pluck('school_year_label')
                ->filter(fn ($value) => !empty($value) && $value !== '—')
                ->unique()
                ->sortDesc()
                ->values();
        @endphp

        <!-- Students Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-users"></i></div>
                    <div>
                        <h2>Active Student List</h2>
                        <p>All currently active enrolled OJT students</p>
                    </div>
                </div>
                <div class="table-card-header-right">
                    <div class="table-inline-filter">
                        <label for="courseFilter" class="table-inline-filter-label">Filter by course</label>
                        <select id="courseFilter" class="table-inline-filter-select">
                            <option value="">All Courses</option>
                            @foreach ($courseOptions as $courseOption)
                                <option value="{{ $courseOption }}">{{ $courseOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-inline-filter">
                        <label for="schoolYearFilter" class="table-inline-filter-label">Filter by school year</label>
                        <select id="schoolYearFilter" class="table-inline-filter-select">
                            <option value="">All School Years</option>
                            @foreach ($schoolYearOptions as $schoolYearOption)
                                <option value="{{ $schoolYearOption }}">{{ $schoolYearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="student-count-badge">
                        <i class="fa fa-users"></i>
                        {{ $totalStudents }} {{ $totalStudents == 1 ? 'student' : 'students' }}
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
                            <th>Professor</th>
                            <th>School Year</th>
                            <th>Notarized MOA</th>
                            <th style="display:none;">Legacy Subject</th>
                            <th style="display:none;">Filter School Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentData as $data)
                        <tr>
                            <!-- Name -->
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

                            <!-- Course -->
                            <td>
                                <span class="course-badge">
                                    <i class="fa fa-graduation-cap" style="font-size:10px;"></i>
                                    {{ $data['student']->course }}
                                </span>
                            </td>

                            <!-- Section -->
                            <td>
                                <span class="section-badge">{{ $data['student']->year_and_section }}</span>
                            </td>

                            <!-- Professor -->
                            <td style="font-size:13px; color:#555;">
                                {{ $data['student']->adviser_name ?? '—' }}
                            </td>

                            <!-- School Year -->
                            <td>
                                <span class="section-badge">{{ $data['school_year_label'] ?? '—' }}</span>
                            </td>

                            <!-- Notarized MOA -->
                            <td>
                                @php
                                    $moaRequirement = $data['moa_requirement'] ?? null;
                                    $moaCompany = $data['moa_company'] ?? null;
                                    $moaStatus = 'Not Uploaded';
                                    $moaStatusClass = 'empty';
                                    $moaLinkUrl = null;

                                    if ($moaRequirement) {
                                        if ((int) $moaRequirement->status === 1) {
                                            $moaStatus = 'Approved';
                                            $moaStatusClass = 'approved';
                                        } elseif ((int) $moaRequirement->status === 2) {
                                            $moaStatus = 'Denied';
                                            $moaStatusClass = 'denied';
                                        } else {
                                            $moaStatus = 'Uploaded';
                                            $moaStatusClass = 'uploaded';
                                        }
                                    } elseif ($moaCompany) {
                                        $moaStatus = 'Uploaded';
                                        $moaStatusClass = 'uploaded';
                                    }

                                    if ($moaCompany) {
                                        $moaLinkUrl = route('moa.view', ['companyId' => $moaCompany->id]);
                                    } elseif ($moaRequirement && !empty($moaRequirement->file)) {
                                        $moaLinkUrl = url('/moa/download', $moaRequirement->file);
                                    }
                                @endphp

                                <div class="moa-cell">
                                    <span class="moa-pill {{ $moaStatusClass }}">
                                        <i class="fa fa-file-signature" style="font-size:10px;"></i>
                                        {{ $moaStatus }}
                                    </span>

                                    @if($moaLinkUrl)
                                        <a href="{{ $moaLinkUrl }}" class="moa-link-btn">
                                            <i class="fa fa-eye"></i> Open MOA
                                        </a>
                                    @else
                                        <span class="moa-meta">No proof yet</span>
                                    @endif
                                </div>
                            </td>

                            <td style="display:none;">
                                @if(isset($data['subjects']) && count($data['subjects']) > 0)
                                    @foreach($data['subjects'] as $subject)
                                        <span class="subject-badge">{{ $subject['subject_code'] ?? '--' }}</span>
                                    @endforeach
                                @else
                                    <span style="color:#aaa; font-size:13px;">&mdash;</span>
                                @endif
                            </td>

                            <td style="display:none;">{{ $data['school_year_label'] ?? '—' }}</td>

                            <!-- Actions -->
                           <td>
    <div class="actions-wrap">

        <!-- Personal Info -->
        <button class="btn-action action-icon-btn view-personal btn-view-personal"
            data-bs-toggle="modal" data-bs-target="#personalModal"
            data-full-name="{{ $data['student']->full_name }}"
            data-contact-number="{{ $data['student']->contact_number }}"
            data-email="{{ $data['student']->email }}"
            data-address="{{ $data['student']->address }}"
            data-date-of-birth="{{ $data['student']->date_of_birth }}"
            data-student-num="{{ $data['ojt']->studentNum ?? '' }}"
            title="Personal Info"
            aria-label="Personal Info">
            <i class="fa fa-user"></i>
            <span class="action-label">Personal</span>
        </button>

        <!-- OJT Info -->
        <button class="btn-action action-icon-btn view-ojt btn-view-ojt"
            data-bs-toggle="modal" data-bs-target="#ojtModal"
            data-full-name="{{ $data['student']->full_name }}"
            data-company-name="{{ $data['ojt']->company_name ?? '' }}"
            data-company-address="{{ $data['ojt']->company_address ?? '' }}"
            data-nature-of-business="{{ $data['ojt']->nature_of_bus ?? '' }}"
            data-nature-of-linkages="{{ $data['ojt']->nature_of_link ?? '' }}"
            data-level="{{ $data['ojt']->level ?? '' }}"
            data-start-date="{{ $data['ojt']->start_date ?? '' }}"
            data-finish-date="{{ $data['ojt']->finish_date ?? '' }}"
            data-report-time="{{ $data['ojt']->report_time ?? '' }}"
            data-contact-name="{{ $data['ojt']->contact_name ?? '' }}"
            data-contact-position="{{ $data['ojt']->contact_position ?? '' }}"
            data-contact-number="{{ $data['ojt']->contact_number ?? '' }}"
            title="OJT Info"
            aria-label="OJT Info">
            <i class="fa fa-briefcase"></i>
            <span class="action-label">OJT Info</span>
        </button>

        <!-- Status + Notify side by side -->
        <button class="btn-action action-icon-btn status btn-status"
            data-bs-toggle="modal" data-bs-target="#statusModal"
            data-student="{{ $data['ojt']->studentNum ?? '' }}"
            data-status="{{ $data['ojt']->status ?? '' }}"
            data-name="{{ $data['student']->full_name }}"
            title="Status"
            aria-label="Status">
            <i class="fa fa-info-circle"></i>
            <span class="action-label">Status</span>
        </button>

        @php
            $canNotify = !empty($data['ojt']) && !empty($data['ojt']->studentNum);
        @endphp

        @if($canNotify)
            <form class="notifyForm d-inline"
                  action="{{ url('/notify', $data['ojt']->studentNum) }}"
                  method="POST">
                @csrf
                <button type="submit" class="btn-action action-icon-btn notify" title="Notify Student" aria-label="Notify Student">
                    <i class="fa fa-bell"></i>
                    <span class="action-label">Notify</span>
                </button>
            </form>
        @else
            <button type="button" class="btn-action action-icon-btn notify" disabled
                    aria-label="Notify unavailable"
                    title="No OJT information is saved yet for this student.">
                <i class="fa fa-bell"></i>
                <span class="action-label">Notify</span>
            </button>
        @endif

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

<!-- =============== PERSONAL INFO MODAL =============== -->
<div class="modal fade" id="personalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user"></i> Student Personal Information
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
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-id-card"></i></div>
                    <div><div class="info-label">Student Number</div><div class="info-value" id="pi-student-num"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-phone"></i></div>
                    <div><div class="info-label">Contact Number</div><div class="info-value" id="pi-contact-number"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-envelope"></i></div>
                    <div><div class="info-label">Email Address</div><div class="info-value" id="pi-email"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div><div class="info-label">Address</div><div class="info-value" id="pi-address"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-birthday-cake"></i></div>
                    <div><div class="info-label">Date of Birth</div><div class="info-value" id="pi-date-of-birth"></div></div>
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
<div class="modal fade" id="ojtModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-briefcase"></i> Student OJT Information
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
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-building"></i></div>
                    <div><div class="info-label">Company Name</div><div class="info-value" id="ojt-company-name"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div><div class="info-label">Company Address</div><div class="info-value" id="ojt-company-address"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-industry"></i></div>
                    <div><div class="info-label">Nature of Business</div><div class="info-value" id="ojt-nature-of-business"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-network-wired"></i></div>
                    <div><div class="info-label">Nature of Linkages</div><div class="info-value" id="ojt-nature-of-linkages"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-layer-group"></i></div>
                    <div><div class="info-label">Level</div><div class="info-value" id="ojt-level"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-calendar-alt"></i></div>
                    <div><div class="info-label">Start Date</div><div class="info-value" id="ojt-start-date"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-calendar-check"></i></div>
                    <div><div class="info-label">End Date</div><div class="info-value" id="ojt-finish-date"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-clock"></i></div>
                    <div><div class="info-label">Reporting Time</div><div class="info-value" id="ojt-report-time"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-user-tie"></i></div>
                    <div><div class="info-label">Contact Person</div><div class="info-value" id="ojt-contact-name"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-id-badge"></i></div>
                    <div><div class="info-label">Position</div><div class="info-value" id="ojt-contact-position"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-phone"></i></div>
                    <div><div class="info-label">Contact Number</div><div class="info-value" id="ojt-contact-number"></div></div>
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

<!-- =============== STATUS MODAL =============== -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-info-circle"></i> Update Student Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" action="{{ url('/status') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="modal-student-banner">
                        <div class="modal-student-avatar" id="st-avatar"></div>
                        <div>
                            <div class="modal-student-name" id="st-name"></div>
                            <div class="modal-student-sub">Update MOA Status</div>
                        </div>
                    </div>
                    <div style="margin-bottom:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#444; display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                            <i class="fa fa-tag" style="color:var(--red); font-size:11px;"></i> Select Status
                        </label>
                        <select class="status-select" name="status" id="status-select">
                            <option value="" disabled selected>Select status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved and For Notary">Approved and For Notary</option>
                            <option value="With Revision">With Revision</option>
                        </select>
                    </div>
                    <input type="hidden" id="status-student" name="student" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-update">
                        <i class="fa fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    window.studentsConfig = {
        syncUsersIdpUrl: @json(route('coordinator.syncUsersIdp')),
        syncUsersGuisisUrl: @json(route('coordinator.syncUsersGuisis')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/coordinator/students.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
