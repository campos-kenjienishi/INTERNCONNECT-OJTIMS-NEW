<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Student Requirements Matrix</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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

    <link rel="stylesheet" href="{{ vasset('css/coordinator/student-requirements.css') }}?v={{ time() }}">
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
            @if(is_object($user) && !empty($user->profile_photo) && file_exists(public_path('storage/' . $user->profile_photo)))
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo">
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
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
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
                <h1>Student <span>Requirements Matrix</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/studentLists') }}">Students</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Requirements Matrix</span>
                </div>
            </div>
            <a href="{{ url('/studentLists') }}" class="btn-generate">
                <i class="fa fa-arrow-left"></i> Back to Active Students
            </a>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ $totalStudentsTracked }}</div>
                    <div class="stat-name">Total Students Tracked</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $fullySubmittedCount }}</div>
                    <div class="stat-name">100% Fully Uploaded</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-exclamation-triangle"></i></div>
                <div>
                    <div class="stat-num">{{ $incompleteCount }}</div>
                    <div class="stat-name">Incomplete Submissions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $totalFilesSubmitted }}</div>
                    <div class="stat-name">Submitted Requirements</div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h3>Filter & Search Requirements</h3>
                    <p>Filter student submission records by course, section, professor or submission status</p>
                </div>
            </div>
            <form action="{{ route('coordinator.studentRequirements') }}" method="GET">
                <div class="filter-card-body">
                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-graduation-cap"></i> Course</label>
                        <select name="course" class="filter-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $c)
                                <option value="{{ $c }}" {{ $selectedCourse === $c ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                        <select name="school_year" class="filter-select">
                            <option value="">All School Years</option>
                            @foreach($schoolYears as $sy)
                                <option value="{{ $sy }}" {{ $selectedSchoolYear === $sy ? 'selected' : '' }}>
                                    {{ $sy }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-user-tie"></i> Adviser / Professor</label>
                        <select name="professor_id" class="filter-select">
                            <option value="">All Professors</option>
                            @foreach($professors as $prof)
                                <option value="{{ $prof->id }}" {{ (string)$selectedProfessorId === (string)$prof->id ? 'selected' : '' }}>
                                    {{ $prof->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-tasks"></i> Submission Status</label>
                        <select name="status" class="filter-select">
                            <option value="">All Submission Statuses</option>
                            <option value="complete" {{ $selectedStatus === 'complete' || $selectedStatus === 'completed' ? 'selected' : '' }}>100% Complete (All Uploaded)</option>
                            <option value="incomplete" {{ $selectedStatus === 'incomplete' ? 'selected' : '' }}>Incomplete (Missing Files)</option>
                        </select>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn-generate"><i class="fa fa-filter"></i> Apply Filter</button>
                        <a href="{{ route('coordinator.studentRequirements') }}" class="btn-generate" style="background:#6b7280; box-shadow:none;"><i class="fa fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Requirements Matrix Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                    <div>
                        <h2>Student Requirements Compliance Matrix</h2>
                        <p>View student requirement submission statuses and detailed file repository</p>
                    </div>
                </div>
                <div class="student-count-badge">
                    <i class="fa fa-users"></i> {{ $totalStudentsTracked }} Students Tracked
                </div>
            </div>

            <div style="padding: 20px; overflow-x: auto;">
                <table id="requirementsMatrixTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>STUDENT DETAILS</th>
                            <th>COURSE</th>
                            <th>SECTION</th>
                            <th>SCHOOL YEAR</th>
                            <th>PROFESSOR</th>
                            <th>STATUS SUMMARY</th>
                            <th>PROGRESS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentMatrix as $item)
                            @php
                                $st = $item['student'];
                                $usr = $item['user'];
                                $categories = $item['categories'];
                                $totalCats = $item['total_categories'] ?? 0;
                                $pct = $totalCats > 0 ? round(($item['submitted_count'] / $totalCats) * 100) : 0;
                                $studentName = $st->full_name ?? ($usr->full_name ?? 'N/A');
                                $initial = strtoupper(substr($studentName, 0, 1));
                                $syText = (!empty($st->school_year_start) && !empty($st->school_year_end))
                                    ? ($st->school_year_start . '-' . $st->school_year_end)
                                    : '—';
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="student-avatar-initial">{{ $initial }}</div>
                                        <div>
                                            <div style="font-weight:700; color:#1a1a1a;">{{ $studentName }}</div>
                                            <div style="font-size:12px; color:#888;">{{ $st->studentNum ?? '' }}</div>
                                            @if(!empty($st->is_inhouse_ojt))
                                                <span class="req-badge" style="background:#d1fae5; color:#047857; border:1px solid #a7f3d0; margin-top:3px; display:inline-flex; align-items:center; gap:4px;" title="Student is in School In-House OJT mode. External MOA requirement is waived.">
                                                    <i class="fa fa-university"></i> In-House OJT
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="course-badge">{{ $st->course ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="section-badge">{{ $st->year_and_section ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="req-badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">
                                        <i class="fa fa-calendar-alt me-1"></i> {{ $syText }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight:600; font-size:13px; color:#444;">
                                        <i class="fa fa-user-tie me-1" style="color:var(--red);"></i>
                                        {{ $st->adviser_name ?? 'Not Assigned' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-wrap:wrap; gap:6px; max-width:280px;">
                                        @if($item['is_fully_submitted'])
                                            <span class="req-badge req-approved">
                                                <i class="fa fa-check-circle"></i> {{ $item['submitted_count'] }}/{{ $totalCats }} Uploaded
                                            </span>
                                        @else
                                            <span class="req-badge req-approved">
                                                <i class="fa fa-file-alt"></i> {{ $item['submitted_count'] }} Uploaded
                                            </span>
                                            @if($item['missing_count'] > 0)
                                                <span class="req-badge req-missing">
                                                    <i class="fa fa-minus-circle"></i> {{ $item['missing_count'] }} Missing
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="flex:1; background:#e2e8f0; height:8px; border-radius:4px; overflow:hidden;">
                                            <div style="width:{{ $pct }}%; background: linear-gradient(90deg, #dc2626 0%, #16a34a 100%); height:100%;"></div>
                                        </div>
                                        <span style="font-size:12px; font-weight:700; color:#334155;">{{ $item['submitted_count'] }}/{{ $totalCats }} ({{ $pct }}%)</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:nowrap;">
                                        <button type="button"
                                                class="btn-action-view"
                                                onclick='openStudentFolderModal({
                                                    student_name: "{{ addslashes($studentName) }}",
                                                    student_num: "{{ addslashes($st->studentNum ?? '') }}",
                                                    course: "{{ addslashes($st->course ?? 'N/A') }}",
                                                    adviser: "{{ addslashes($st->adviser_name ?? 'Not Assigned') }}",
                                                    categories: @json($categories)
                                                })'>
                                            <i class="fa fa-folder-open"></i> View Folder
                                        </button>
                                        <form id="inhouse-form-{{ $st->id }}" action="{{ route('coordinator.student.toggleInhouse', $st->id) }}" method="POST" style="display:inline; margin:0;">
                                            @csrf
                                            @if(!empty($st->is_inhouse_ojt))
                                                <button type="button" class="btn-action-inhouse active" title="Revoke School In-House OJT Waiver" onclick="confirmToggleInhouse({{ $st->id }}, '{{ addslashes($studentName) }}', true)">
                                                    <i class="fa fa-undo"></i> Revoke Waiver
                                                </button>
                                            @else
                                                <button type="button" class="btn-action-inhouse" title="Grant School In-House OJT Waiver" onclick="confirmToggleInhouse({{ $st->id }}, '{{ addslashes($studentName) }}', false)">
                                                    <i class="fa fa-university"></i> In-House OJT
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
            <span class="footer-copy">
                &copy; 1998&ndash;2026 <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div style="display:flex; align-items:center; gap:6px; font-size:12.5px;">
            <a href="https://www.pup.edu.ph/" target="_blank" style="color:#888; text-decoration:none; font-weight:500;">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                PUP Website
            </a>
            <span style="color:#e5e5e5; margin:0 2px;">|</span>
            <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer" style="color:#888; text-decoration:none; font-weight:500;">Terms of Use</a>
            <span style="color:#ddd; margin:0 8px;">|</span>
            <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer" style="color:#888; text-decoration:none; font-weight:500;">Privacy Statement</a>
        </div>
    </footer>

</div>

<!-- =============== STUDENT FOLDER MODAL =============== -->
<div class="modal fade" id="studentFilesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1a0000 0%, #7f0000 100%); color: white; padding: 20px 24px; border: none;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fca5a5;">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" id="modalFolderName" style="font-weight: 800; font-size: 18px; margin: 0; color: #ffffff;">
                            Student Requirements
                        </h5>
                        <div id="modalFolderSub" style="font-size: 12.5px; color: rgba(255,255,255,0.75); margin-top: 2px;">
                            Detailed repository of submitted basic requirements
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div id="modalFilesList" style="display: flex; flex-direction: column; gap: 12px;"></div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =============== PDF PREVIEW MODAL =============== -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90vw;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px rgba(0,0,0,0.3); overflow: hidden; height: 88vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f0000 0%, #991b1b 100%); color: white; padding: 16px 24px; border: none; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fa fa-file-pdf" style="font-size: 22px; color: #fca5a5;"></i>
                    <h5 class="modal-title" id="pdfPreviewTitle" style="font-weight: 700; font-size: 16px; margin: 0; color: #ffffff;">
                        Document Preview
                    </h5>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a id="pdfDownloadLink" href="#" class="btn-action view-personal" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; text-decoration: none;">
                        <i class="fa fa-download"></i> Download
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" style="padding: 0; flex: 1; background: #525659;">
                <iframe id="pdfPreviewIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ vasset('js/coordinator/student-requirements.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
</body>
</html>
