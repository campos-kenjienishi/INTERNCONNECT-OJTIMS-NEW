<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Class</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/student_class-responsive.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
    <script>
        (function(){
            try {
                if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e){}
        })();
    </script>
    <link rel="stylesheet" href="{{ vasset('css/student/class.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
    <script src="{{ vasset('js/darkmode.js') }}"></script>
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

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa fa-user"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Home</span>
            <span class="tooltip-label">Home</span>
        </a>
        <a href="{{ url('/student/ojtinfo') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-layer-group"></i></span>
            <span class="nav-label">OJT Information</span>
            <span class="tooltip-label">OJT Information</span>
        </a>
        <a href="{{ url('/student/class') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ url('/student/files') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-download"></i></span>
            <span class="nav-label">Downloadable Files</span>
            <span class="tooltip-label">Downloadable Files</span>
        </a>
        <a href="{{ url('/student/MOA') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-alt"></i></span>
            <span class="nav-label">Notarized MOA</span>
            <span class="tooltip-label">Notarized MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
        </a>
            <a href="{{ url('/student/evaluation') }}" class="nav-item{{ request()->is('student/evaluation*') ? ' active' : '' }}">
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
                <i class="fa fa-graduation-cap"></i>
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>My <span>Class</span></h1>
                <div class="breadcrumb" style="margin-top: 6px;">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Class</span>
                </div>
            </div>
        </div>
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px; margin-bottom:20px;">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('fail') || session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px; margin-bottom:20px;">
                <i class="fa fa-exclamation-circle me-2"></i> {{ session('fail') ?? session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Rooms Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="header-icon"><i class="fa fa-door-open"></i></div>
                <div>
                    <h2>{{ $currentClass ? 'Current Class' : 'Available Rooms' }}</h2>
                    <p>{{ $currentClass ? 'View your assigned class information or leave this room' : 'Join or view your assigned class room' }}</p>
                </div>
            </div>
            <div class="table-card-body">
                @if ($currentClass)
                    <div style="padding:24px;">
                        <div class="current-class-hero-card">
                            <!-- Hero Banner -->
                            <div class="current-class-banner">
                                <div class="class-banner-left">
                                    <div class="class-room-badge">
                                        <i class="fa fa-chalkboard"></i>
                                    </div>
                                    <div>
                                        <div class="class-room-name">{{ $currentClass->room }}</div>
                                        <div class="class-course-name">{{ $currentClass->course }}</div>
                                    </div>
                                </div>
                                <div class="class-banner-right">
                                    @if ($data->status == 1)
                                        <span class="status-badge status-approved"><i class="fa fa-check-circle"></i> Approved</span>
                                    @elseif ($data->status == 2)
                                        <span class="status-badge status-denied"><i class="fa fa-times-circle"></i> Denied</span>
                                    @elseif ($data->status == 3)
                                        <span class="status-badge status-pending"><i class="fa fa-clock"></i> Pending</span>
                                    @else
                                        <span class="status-badge status-default"><i class="fa fa-minus-circle"></i> Not Joined</span>
                                    @endif

                                    <button class="btn-leave-class" onclick="leaveStudent()">
                                        <i class="fa fa-sign-out-alt"></i> Leave Room
                                    </button>
                                </div>
                            </div>

                            <!-- Balanced Metric Grid -->
                            <div class="current-class-grid">
                                <div class="class-metric-tile">
                                    <div class="metric-icon"><i class="fa fa-user-tie"></i></div>
                                    <div class="metric-content">
                                        <span class="metric-label">Professor</span>
                                        <span class="metric-value">{{ $currentClass->adviser_name }}</span>
                                    </div>
                                </div>

                                <div class="class-metric-tile">
                                    <div class="metric-icon"><i class="fa fa-calendar-alt"></i></div>
                                    <div class="metric-content">
                                        <span class="metric-label">School Year</span>
                                        <span class="metric-value">{{ $currentClass->school_year_start && $currentClass->school_year_end ? $currentClass->school_year_start . ' - ' . $currentClass->school_year_end : 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="class-metric-tile">
                                    <div class="metric-icon"><i class="fa fa-graduation-cap"></i></div>
                                    <div class="metric-content">
                                        <span class="metric-label">Semester</span>
                                        <span class="metric-value">{{ $currentClass->semester ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="class-metric-tile class-metric-tile-schedule">
                                    <div class="metric-icon"><i class="fa fa-clock"></i></div>
                                    <div class="metric-content">
                                        <span class="metric-label">Class Schedule</span>
                                        <div class="metric-schedule-list">
                                            @if (empty($currentClass->schedule_parsed))
                                                <span style="color:#888; font-size:13px;">No schedule available</span>
                                            @else
                                                @php
                                                    $groupedSchedule = [];
                                                    foreach ($currentClass->schedule_parsed as $slot) {
                                                        if (!empty($slot['day'])) {
                                                            $startRaw = $slot['start_time'] ?? '';
                                                            $endRaw = $slot['end_time'] ?? '';
                                                            $startFormatted = !empty($startRaw) ? date('g:i A', strtotime($startRaw)) : '';
                                                            $endFormatted = !empty($endRaw) ? date('g:i A', strtotime($endRaw)) : '';
                                                            $groupedSchedule[$slot['day']][] = trim($startFormatted . ' - ' . $endFormatted);
                                                        }
                                                    }
                                                @endphp
                                                @foreach ($groupedSchedule as $day => $times)
                                                    <span class="schedule-pill"><strong>{{ $day }}:</strong> {{ implode(', ', array_filter($times)) }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif (empty($data->adviser_name) || $data->adviser_name === 'Not Yet Listed')
                    <div class="empty-state" style="padding: 36px 20px;">
                        <div class="empty-icon-wrap" style="background:#fff3ed; color:#e65100;">
                            <i class="fa fa-user-clock"></i>
                        </div>
                        <h3 style="font-size:18px; font-weight:700; color:#1a1a1a; margin-top:12px;">Choose a Professor First</h3>
                        <p style="color:#666; max-width:500px; margin:8px auto 20px; line-height:1.5;">
                            You selected <strong>Not Yet Listed</strong> (or haven't assigned a professor yet). You cannot access or join a class without an assigned professor.
                        </p>
                        <form action="{{ route('student.updateProfessor') }}" method="POST" style="max-width:480px; margin:0 auto; display:flex; gap:10px; flex-wrap:wrap; justify-content:center; align-items:center;">
                            @csrf
                            @method('PUT')
                            <div style="flex:1; min-width:240px; text-align:left;">
                                <select name="adviser_name" class="form-select" required>
                                    <option value="">Select your Professor</option>
                                    @foreach($professors as $prof)
                                        <option value="{{ $prof->full_name }}">{{ $prof->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger" style="background:var(--red); border:none; padding:10px 24px; border-radius:10px; font-weight:600; height:44px;">
                                <i class="fa fa-save"></i> Save Professor
                            </button>
                        </form>
                    </div>
                @elseif ($class->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon-wrap">
                            <i class="fa fa-door-closed"></i>
                        </div>
                        <p>No class matched your academic details yet.</p>
                        <span class="empty-hint">Once your professor creates the matching class for your program and school year, it can appear here automatically.</span>
                    </div>
                @else
                <table id="roomsTable" class="display rooms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Room</th>
                            <th>School Year</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($class as $classItem)
                        <tr>
                            <td>{{ $classItem->course }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:32px;height:32px;border-radius:8px;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:var(--red);font-size:13px;">
                                        <i class="fa fa-chalkboard"></i>
                                    </div>
                                    <span style="font-weight:600;">{{ $classItem->room }}</span>
                                </div>
                            </td>
                            <td>
                                {{ $classItem->school_year_start && $classItem->school_year_end ? $classItem->school_year_start . ' - ' . $classItem->school_year_end : 'N/A' }}
                            </td>
                            <td>
                                @if ($data->class_id == $classItem->id && $data->status == 1)
                                    <span class="status-badge status-approved"><i class="fa fa-check-circle"></i> Approved</span>
                                @elseif ($data->class_id == $classItem->id && $data->status == 2)
                                    <span class="status-badge status-denied"><i class="fa fa-times-circle"></i> Denied</span>
                                @elseif ($data->class_id == $classItem->id && $data->status == 3)
                                    <span class="status-badge status-pending"><i class="fa fa-clock"></i> Pending</span>
                                @else
                                    <span class="status-badge status-default"><i class="fa fa-minus-circle"></i> Not Joined</span>
                                @endif
                            </td>
                            <td>
                                @if ($data->class_id == $classItem->id && ($data->status == 1 || $data->status == 3))
                                    <button class="btn-leave" onclick="leaveStudent()">
                                        <i class="fa fa-sign-out-alt"></i> Leave
                                    </button>
                                @elseif (empty($data->class_id))
                                    <button class="btn-join" onclick="joinStudent('{{ url('/student/join/' . $data->email . '/' . $classItem->id) }}')">
                                        <i class="fa fa-sign-in-alt"></i> Join
                                    </button>
                                @elseif ($data->status != 1 && $data->status != 3)
                                    <button class="btn-join" onclick="joinStudent('{{ url('/student/join/' . $data->email . '/' . $classItem->id) }}')">
                                        <i class="fa fa-sign-in-alt"></i> Join
                                    </button>
                                @endif

                                <button class="btn-view" data-bs-toggle="modal" data-bs-target="#modal{{ $loop->iteration }}">
                                    <i class="fa fa-eye"></i> View
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="modal{{ $loop->iteration }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                             <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-door-open"></i> Room Details
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="modal-detail-row">
                                                    <div class="modal-detail-icon"><i class="fa fa-chalkboard"></i></div>
                                                    <div>
                                                        <div class="modal-detail-label">Room Name</div>
                                                        <div class="modal-detail-value">{{ $classItem->room }}</div>
                                                    </div>
                                                </div>
                                                <div class="modal-detail-row">
                                                    <div class="modal-detail-icon"><i class="fa fa-graduation-cap"></i></div>
                                                    <div>
                                                        <div class="modal-detail-label">Program</div>
                                                        <div class="modal-detail-value">{{ $classItem->course }}</div>
                                                    </div>
                                                </div>
                                                <div class="modal-detail-row">
                                                    <div class="modal-detail-icon"><i class="fa fa-info-circle"></i></div>
                                                    <div>
                                                        <div class="modal-detail-label">Status</div>
                                                        <div class="modal-detail-value">
                                                            @if ($data->class_id == $classItem->id && $data->status == 1)
                                                                <span class="status-badge status-approved"><i class="fa fa-check-circle"></i> Approved</span>
                                                            @elseif ($data->class_id == $classItem->id && $data->status == 2)
                                                                <span class="status-badge status-denied"><i class="fa fa-times-circle"></i> Denied</span>
                                                            @elseif ($data->class_id == $classItem->id && $data->status == 3)
                                                                <span class="status-badge status-pending"><i class="fa fa-clock"></i> Pending</span>
                                                            @else
                                                                <span class="status-badge status-default"><i class="fa fa-minus-circle"></i> Not Joined</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-detail-row">
                                                    <div class="modal-detail-icon"><i class="fa fa-calendar-alt"></i></div>
                                                    <div>
                                                        <div class="modal-detail-label">Semester</div>
                                                        <div class="modal-detail-value">{{ $classItem->semester ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                                <div class="modal-detail-row">
                                                    <div class="modal-detail-icon"><i class="fa fa-clock"></i></div>
                                                    <div>
                                                        <div class="modal-detail-label">Schedule</div>
                                                        <div class="modal-detail-value">
                                                            @if (empty($classItem->schedule_parsed))
                                                                <span style="color:#888;">No schedule available</span>
                                                            @else
                                                                @php
                                                                    $groupedSchedule = [];
                                                                    foreach ($classItem->schedule_parsed as $slot) {
                                                                        if (!empty($slot['day'])) {
                                                                            $startRaw = $slot['start_time'] ?? '';
                                                                            $endRaw = $slot['end_time'] ?? '';
                                                                            $startFormatted = !empty($startRaw) ? date('g:i A', strtotime($startRaw)) : '';
                                                                            $endFormatted = !empty($endRaw) ? date('g:i A', strtotime($endRaw)) : '';
                                                                            $groupedSchedule[$slot['day']][] = trim($startFormatted . ' - ' . $endFormatted);
                                                                        }
                                                                    }
                                                                @endphp
                                                                @foreach ($groupedSchedule as $day => $times)
                                                                    <div><strong>{{ $day }}:</strong> {{ implode(', ', array_filter($times)) }}</div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-detail-row">
                                                    <div class="modal-detail-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                                                    <div>
                                                        <div class="modal-detail-label">Adviser</div>
                                                        <div class="modal-detail-value">{{ $classItem->adviser_name }}</div>
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
                                <!-- End Modal -->

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <!-- Room Templates Section (Option 1: Document Gallery Grid) -->
        <div class="table-card" style="margin-bottom: 24px;">
            <div class="table-card-header">
                <div class="table-card-header-left" style="display:flex;align-items:center;gap:12px;">
                    <div class="header-icon"><i class="fa fa-file-download"></i></div>
                    <div>
                        <h2>Room Templates</h2>
                        <p>Templates uploaded by your professor for your current room</p>
                    </div>
                </div>
            </div>
            <div class="table-card-body" style="padding: 24px;">
                @if (empty($data->class_id))
                    <div class="empty-state">
                        <div class="empty-icon-wrap">
                            <i class="fa fa-door-closed"></i>
                        </div>
                        <p>You haven't joined a room yet.</p>
                        <span class="empty-hint">Join a room above to access room-specific templates.</span>
                    </div>
                @elseif ($roomTemplates->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon-wrap">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <p>No room templates uploaded yet.</p>
                        <span class="empty-hint">Check back later — your adviser hasn't uploaded any templates.</span>
                    </div>
                @else
                    <!-- Search & Filter Bar -->
                    <div class="section-filter-bar">
                        <div class="search-input-pill">
                            <i class="fa fa-search"></i>
                            <input type="text" id="templateSearchInput" placeholder="Search templates by name or file..." autocomplete="off">
                        </div>
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <div class="header-sort-pill" style="margin-left:0;">
                                <i class="fa fa-sort-amount-down sort-icon"></i>
                                <label for="templateSortSelect">Sort:</label>
                                <select id="templateSortSelect" class="sort-select">
                                    <option value="newest" selected>Newest first</option>
                                    <option value="oldest">Oldest first</option>
                                    <option value="az">Name (A-Z)</option>
                                    <option value="za">Name (Z-A)</option>
                                </select>
                            </div>
                            <div style="font-size:12.5px; color:#64748b; font-weight:500;">
                                Showing <span id="templateCount" style="font-weight:700; color:var(--red);">{{ count($roomTemplates) }}</span> template{{ count($roomTemplates) != 1 ? 's' : '' }}
                            </div>
                        </div>
                    </div>

                    <!-- Template Grid -->
                    <div class="template-grid-container" id="templateGridContainer">
                        @foreach ($roomTemplates as $template)
                            @php
                                $templateExt = strtolower(pathinfo($template->file, PATHINFO_EXTENSION));
                                $iconData = match($templateExt) {
                                    'pdf'  => ['icon' => 'fa-file-pdf', 'class' => 'icon-pdf'],
                                    'doc', 'docx' => ['icon' => 'fa-file-word', 'class' => 'icon-word'],
                                    'xls', 'xlsx' => ['icon' => 'fa-file-excel', 'class' => 'icon-excel'],
                                    'ppt', 'pptx' => ['icon' => 'fa-file-powerpoint', 'class' => 'icon-word'],
                                    'jpg', 'jpeg', 'png', 'gif' => ['icon' => 'fa-file-image', 'class' => 'icon-image'],
                                    default => ['icon' => 'fa-file-alt', 'class' => 'icon-file']
                                };
                            @endphp
                            <div class="doc-template-card"
                                 data-name="{{ strtolower($template->name) }}"
                                 data-file="{{ strtolower($template->file) }}"
                                 data-timestamp="{{ \Carbon\Carbon::parse($template->created_at)->timestamp }}">
                                <div class="doc-card-top">
                                    <div class="doc-card-icon {{ $iconData['class'] }}">
                                        <i class="fa {{ $iconData['icon'] }}"></i>
                                    </div>
                                    <span class="doc-format-badge">{{ strtoupper($templateExt) }}</span>
                                </div>

                                <div class="doc-card-body">
                                    <h3 class="doc-title">{{ $template->name }}</h3>
                                    <div class="doc-filename-badge" title="{{ $template->file }}">
                                        <i class="fa fa-paperclip"></i>
                                        <span>{{ $template->file }}</span>
                                    </div>
                                    <div class="doc-meta-row">
                                        <span class="doc-meta-item">
                                            <i class="fa fa-calendar-alt" style="color:#94a3b8;"></i>
                                            {{ \Carbon\Carbon::parse($template->created_at)->format('M d, Y') }}
                                        </span>
                                        <span class="doc-meta-item">
                                            <i class="fa fa-clock" style="color:#94a3b8;"></i>
                                            {{ \Carbon\Carbon::parse($template->created_at)->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="doc-card-actions">
                                    @if(in_array($templateExt, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'svg']))
                                        <button type="button"
                                                class="doc-btn-view btn-preview-file"
                                                data-file-url="{{ url('/view/file', $template->file) }}"
                                                data-file-name="{{ $template->name }}"
                                                data-download-url="{{ url('/download', $template->file) }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                    @endif
                                    <a href="{{ url('/download', $template->file) }}" class="doc-btn-download">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Empty Search State for Room Templates -->
                    <div id="templateEmptySearch" class="empty-state" style="display: none; padding: 36px 20px;">
                        <div class="empty-icon-wrap" style="background:#fee2e2; color:var(--red); width:50px; height:50px; font-size:20px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin: 0 auto 12px;">
                            <i class="fa fa-search"></i>
                        </div>
                        <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 15px;">No room templates found</p>
                        <span class="empty-hint" style="display:block; margin-top: 4px; color: #64748b; font-size: 12.5px;">Try adjusting your search query</span>
                    </div>

                    <!-- Template Pagination Wrapper (Max 3 items per page) -->
                    <div class="template-pagination-wrapper" id="templatePaginationWrapper" style="display: none;">
                        <div class="template-pagination-info" id="templatePaginationInfo">
                            Showing <span id="templatePageRange">1–3</span> of <span id="templateTotalCount">{{ count($roomTemplates) }}</span> templates
                        </div>
                        <div class="template-pagination-controls" id="templatePaginationControls">
                            <!-- Page buttons rendered by JavaScript -->
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Class Announcements Section (Option 1: Bulletin Feed) -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left" style="display:flex;align-items:center;gap:12px;">
                    <div class="header-icon"><i class="fa fa-bullhorn"></i></div>
                    <div>
                        <h2>Class Announcements</h2>
                        <p>Latest updates and notices from your class adviser</p>
                    </div>
                </div>
            </div>

            <div class="table-card-body" style="padding: 24px;">
                @if($announce->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon-wrap">
                            <i class="fa fa-bullhorn"></i>
                        </div>
                        <p>No class announcements posted yet.</p>
                        <span class="empty-hint">Your adviser has not published any announcements for this room.</span>
                    </div>
                @else
                    <!-- Search & Filter Bar -->
                    <div class="section-filter-bar">
                        <div class="search-input-pill">
                            <i class="fa fa-search"></i>
                            <input type="text" id="announcementSearchInput" placeholder="Search announcements..." autocomplete="off">
                        </div>
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <div class="header-sort-pill" style="margin-left:0;">
                                <i class="fa fa-sort-amount-down sort-icon"></i>
                                <label for="announcementSortSelect">Sort:</label>
                                <select id="announcementSortSelect" class="sort-select">
                                    <option value="newest" selected>Newest first</option>
                                    <option value="oldest">Oldest first</option>
                                </select>
                            </div>
                            <div style="font-size:12.5px; color:#64748b; font-weight:500;">
                                Showing <span id="announcementCount" style="font-weight:700; color:var(--red);">{{ count($announce) }}</span> announcement{{ count($announce) != 1 ? 's' : '' }}
                            </div>
                        </div>
                    </div>

                    <!-- Announcement Feed -->
                    <div class="announcement-feed-stream" id="announcementFeedStream">
                        @foreach($announce as $item)
                            <div class="announcement-feed-card"
                                 data-title="{{ strtolower($item->title) }}"
                                 data-content="{{ strtolower($item->content) }}"
                                 data-announcer="{{ strtolower($item->announcer) }}"
                                 data-timestamp="{{ \Carbon\Carbon::parse($item->created_at)->timestamp }}">
                                <div class="announcement-card-head">
                                    <div class="announcement-author-info">
                                        <div class="announcement-avatar">
                                            <i class="fa fa-user-tie"></i>
                                        </div>
                                        <div class="announcement-author-details">
                                            <span class="announcement-author-name">{{ $item->announcer }}</span>
                                            <span class="announcement-author-role">Class Adviser</span>
                                        </div>
                                    </div>
                                    <span class="announcement-time-badge">
                                        <i class="fa fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y • h:i A') }}
                                    </span>
                                </div>

                                <div class="announcement-body-section">
                                    <h3 class="announcement-headline">
                                        <i class="fa fa-bullhorn"></i>
                                        {{ $item->title }}
                                    </h3>
                                    <div class="announcement-message-text">{{ $item->content }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Empty Search State for Announcements -->
                    <div id="announcementEmptySearch" class="empty-state" style="display: none; padding: 36px 20px;">
                        <div class="empty-icon-wrap" style="background:#fee2e2; color:var(--red); width:50px; height:50px; font-size:20px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin: 0 auto 12px;">
                            <i class="fa fa-search"></i>
                        </div>
                        <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 15px;">No announcements found</p>
                        <span class="empty-hint" style="display:block; margin-top: 4px; color: #64748b; font-size: 12.5px;">Try adjusting your search query</span>
                    </div>
                @endif
            </div>
        </div>


    </div>
    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
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

<!-- =============== FILE PREVIEW MODAL =============== -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:16px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%); color:#fff; padding:16px 20px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px; margin:0;">
                    <i class="fa fa-file-alt"></i> <span id="filePreviewTitle">Document Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1); opacity:0.8;"></button>
            </div>
            <div class="modal-body" style="padding:0; background:#f8fafc;">
                <div style="padding:12px 18px; border-bottom:1px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:8px; min-width:0;">
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; font-size:12px; font-weight:600; flex-shrink:0;">
                            <i class="fa fa-eye"></i> Preview
                        </span>
                        <span id="filePreviewSubTitle" style="font-size:13px; font-weight:600; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                    </div>
                    <a id="filePreviewDownloadBtn" href="#" class="btn-view-action" style="padding:6px 14px; font-size:12px; text-decoration:none;">
                        <i class="fa fa-download"></i> Download File
                    </a>
                </div>
                <iframe id="filePreviewFrame" title="File Preview" style="width:100%; height:75vh; min-height:400px; border:0; background:#fff;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.studentClassConfig = {
        csrfToken: @json(csrf_token()),
        leaveUrl: @json(url('/student/leave'))
    };
</script>
<script src="{{ vasset('js/student/class.js') }}?v={{ time() }}" defer></script>
<script src="{{ vasset('assets/js/voice-input.js') }}?v={{ time() }}" defer></script>
</body>
</html>