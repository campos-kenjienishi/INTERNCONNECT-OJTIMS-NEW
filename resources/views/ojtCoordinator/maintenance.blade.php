<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Maintenance</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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

    <link rel="stylesheet" href="{{ vasset('css/coordinator/maintenance.css') }}?v={{ time() }}">
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
            <i class="fa fa-user-tie"></i>
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
        <a href="{{ url('/maintenance') }}" class="nav-item active">
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
                <h1>System <span>Maintenance</span></h1>
                <nav class="breadcrumb-nav">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Maintenance</span>
        
    </nav>
            </div>
            <button class="btn-add-course" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="fa fa-plus"></i> Add Course
            </button>
        </div>

        <!-- Stats Row -->
        @php $totalCourses = count($data); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-graduation-cap"></i></div>
                <div>
                    <div class="stat-num">{{ $totalCourses }}</div>
                    <div class="stat-name">Total Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-university"></i></div>
                <div>
                    <div class="stat-num">Active</div>
                    <div class="stat-name">System Status</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $totalCourses }}</div>
                    <div class="stat-name">Available Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-cogs"></i></div>
                <div>
                    <div class="stat-num">Config</div>
                    <div class="stat-name">Maintenance Mode</div>
                </div>
            </div>
        </div>

        <!-- Courses Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-graduation-cap"></i></div>
                    <div>
                        <h2>Courses</h2>
                        <p>Manage all available courses in the system</p>
                    </div>
                </div>
                <div class="count-badge">
                    <i class="fa fa-list"></i>
                    {{ $totalCourses }} {{ $totalCourses == 1 ? 'course' : 'courses' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="courseTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Acronym</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $data)
                        <tr>
                            <td>
                                <div class="course-cell">
                                    <div class="course-icon-box">
                                        <i class="fa fa-book"></i>
                                    </div>
                                    <div>
                                        <div class="course-name-text">{{ $data->course }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="acronym-badge">{{ $data->acronym }}</span>
                            </td>
                            <td>
                                <div class="course-actions">
                                    <button type="button"
                                        class="btn-edit edit-button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCourseModal"
                                        data-course-id="{{ $data->id }}"
                                        data-course-name="{{ $data->course }}"
                                        data-course-acronym="{{ $data->acronym }}">
                                        <i class="fa fa-pen"></i> Edit
                                    </button>
                                    <button class="btn-remove remove-button" data-course-id="{{ $data->id }}">
                                        <i class="fa fa-trash-alt"></i> Remove
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

<!-- =============== ADD COURSE MODAL =============== -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-graduation-cap"></i> Add New Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/courses') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-book"></i> Course Name
                        </label>
                        <input class="field-input" type="text" name="course"
                               placeholder="e.g. Bachelor of Science in Information Technology" required>
                    </div>
                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-tag"></i> Acronym
                        </label>
                        <input class="field-input" type="text" name="acronym"
                               placeholder="e.g. BSIT" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-plus"></i> Add Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-pen"></i> Edit Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourseForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-book"></i> Course Name
                        </label>
                        <input id="edit-course-name" class="field-input" type="text" name="course"
                               placeholder="e.g. Bachelor of Science in Information Technology" required>
                    </div>
                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-tag"></i> Acronym
                        </label>
                        <input id="edit-course-acronym" class="field-input" type="text" name="acronym"
                               placeholder="e.g. BSIT" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save"></i> Save Changes
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
    window.coordinatorConfig = {
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/coordinator/maintenance.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
