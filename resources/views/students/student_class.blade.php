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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
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
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fffaf9;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">Course</div>
                                <div style="margin-top:8px;font-size:16px;font-weight:700;color:#1a1a1a;">{{ $currentClass->course }}</div>
                            </div>
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fffaf9;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">Room</div>
                                <div style="margin-top:8px;font-size:16px;font-weight:700;color:#1a1a1a;">{{ $currentClass->room }}</div>
                            </div>
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fffaf9;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">School Year</div>
                                <div style="margin-top:8px;font-size:16px;font-weight:700;color:#1a1a1a;">{{ $currentClass->school_year_start && $currentClass->school_year_end ? $currentClass->school_year_start . ' - ' . $currentClass->school_year_end : 'N/A' }}</div>
                            </div>
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fffaf9;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">Status</div>
                                <div style="margin-top:8px;">
                                    @if ($data->status == 1)
                                        <span class="status-badge status-approved"><i class="fa fa-check-circle"></i> Approved</span>
                                    @elseif ($data->status == 2)
                                        <span class="status-badge status-denied"><i class="fa fa-times-circle"></i> Denied</span>
                                    @elseif ($data->status == 3)
                                        <span class="status-badge status-pending"><i class="fa fa-clock"></i> Pending</span>
                                    @else
                                        <span class="status-badge status-default"><i class="fa fa-minus-circle"></i> Not Joined</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-top:16px;">
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fff;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">Professor</div>
                                <div style="margin-top:8px;font-size:15px;font-weight:600;color:#1a1a1a;">{{ $currentClass->adviser_name }}</div>
                            </div>
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fff;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">Semester</div>
                                <div style="margin-top:8px;font-size:15px;font-weight:600;color:#1a1a1a;">{{ $currentClass->semester ?? 'N/A' }}</div>
                            </div>
                            <div style="border:1px solid #f0f0f0;border-radius:14px;padding:18px;background:#fff;grid-column:1 / -1;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#888;">Schedule</div>
                                <div style="margin-top:8px;font-size:14px;color:#333;">
                                    @if (empty($currentClass->schedule_parsed))
                                        <span style="color:#888;">No schedule available</span>
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
                                            <div><strong>{{ $day }}:</strong> {{ implode(', ', array_filter($times)) }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:18px;display:flex;justify-content:flex-end;">
                            <button class="btn-leave" onclick="leaveStudent()">
                                <i class="fa fa-sign-out-alt"></i> Leave
                            </button>
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
                        <span class="empty-hint">Once your professor creates the matching class for your course and school year, it can appear here automatically.</span>
                    </div>
                @else
                <table id="roomsTable" class="display rooms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Course</th>
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
                                                        <div class="modal-detail-label">Course</div>
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

        <!-- Room Templates Table Card -->
        <div class="table-card">
    <div class="table-card-header">
        <div class="header-icon"><i class="fa fa-file-download"></i></div>
        <div>
            <h2>Room Templates</h2>
            <p>Templates uploaded by your professor for your current room</p>
        </div>
        <div class="header-sort-control" style="margin-left:auto;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <label for="templateDateSort" style="font-size:13px;color:#666;margin-bottom:0;">Date</label>
            <select id="templateDateSort" class="form-select" style="padding:6px 10px;border-radius:8px;border:1px solid #e5e5e5;font-size:13px;width:160px;">
                <option value="newest" selected>Newest first</option>
                <option value="oldest">Oldest first</option>
            </select>
        </div>
    </div>
    <div class="table-card-body">
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
            <table id="templateTable" class="display rooms-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>File</th>
                        <th>Date Uploaded</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roomTemplates as $template)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:8px;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:var(--red);font-size:13px;flex-shrink:0;">
                                        <i class="fa fa-file-alt"></i>
                                    </div>
                                    <span style="font-weight:600;">{{ $template->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="template-file-badge">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $template->file }}
                                </span>
                            </td>
                            <td data-order="{{ \Carbon\Carbon::parse($template->created_at)->timestamp }}" style="color:#888; font-size:13px;">
                                {{ \Carbon\Carbon::parse($template->created_at)->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                @php
                                    $templateExt = strtolower(pathinfo($template->file, PATHINFO_EXTENSION));
                                @endphp
                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                    @if(in_array($templateExt, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'svg']))
                                        <button type="button"
                                                class="btn-view-green btn-preview-file"
                                                data-file-url="{{ url('/view/file', $template->file) }}"
                                                data-file-name="{{ $template->name }}"
                                                data-download-url="{{ url('/download', $template->file) }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                    @endif
                                    <a href="{{ url('/download', $template->file) }}" class="btn-view-action">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

        <!-- Announcements Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="header-icon"><i class="fa fa-bullhorn"></i></div>
                <div>
                    <h2>Announcements</h2>
                    <p>Latest announcements from your class adviser</p>
                </div>
                <div class="header-sort-control" style="margin-left:auto;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <label for="announcementDateSort" style="font-size:13px;color:#666;margin-bottom:0;">Date</label>
                    <select id="announcementDateSort" class="form-select" style="padding:6px 10px;border-radius:8px;border:1px solid #e5e5e5;font-size:13px;width:160px;">
                        <option value="newest" selected>Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                </div>
            </div>
            <div class="table-card-body">

                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        var roomsTable = null;
                        var templateTable = null;

                        if ($('#roomsTable').length) {
                            roomsTable = $('#roomsTable').DataTable({
                                order: [[2, 'desc'], [1, 'desc']],
                                pageLength: 5,
                                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                                scrollX: true,
                                autoWidth: false,
                                columnDefs: [
                                    { targets: [4], orderable: false, searchable: false }
                                ]
                            });

                            $('#roomsTable_filter input')
                                .attr('placeholder', 'Search rooms, course, professor, school year')
                                .css({
                                    padding: '6px 12px',
                                    borderRadius: '8px',
                                    border: '1px solid #e5e5e5',
                                    fontSize: '13px',
                                    fontFamily: 'Poppins, sans-serif'
                                });
                        }

                        if ($('#templateTable').length) {
                            var templateEmptyMessage = @json(
                                empty($data->class_id)
                                    ? "You haven't joined a room yet."
                                    : ($roomTemplates->isEmpty()
                                        ? "No room templates uploaded yet."
                                        : "No room templates found.")
                            );

                            templateTable = $('#templateTable').DataTable({
                                order: [[2, 'desc']],
                                pageLength: 5,
                                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                                scrollX: true,
                                autoWidth: false,
                                language: {
                                    emptyTable: templateEmptyMessage
                                },
                                columnDefs: [
                                    { targets: [2], type: 'num' },
                                    { targets: [3], orderable: false, searchable: false }
                                ]
                            });

                            $('#templateDateSort').on('change', function () {
                                templateTable
                                    .order([[2, this.value === 'oldest' ? 'asc' : 'desc']])
                                    .draw();
                            });
                        }

                        var announcementTable = $('#ATable').DataTable({
                            order: [[2, 'desc']],
                            scrollX: true,
                            autoWidth: false,
                            columnDefs: [
                                { targets: [2], type: 'num' }
                            ]
                        });

                        $('#announcementDateSort').on('change', function () {
                            announcementTable
                                .order([[2, this.value === 'oldest' ? 'asc' : 'desc']])
                                .draw();
                        });
                    });
                </script>

                <table id="ATable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Comments</th>
                            <th>Date</th>
                            <th>Announced By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($announce as $item)
                        <tr>
                            <td><strong>{{ $item->title }}</strong></td>
                            <td>{{ $item->content }}</td>
                            <td data-order="{{ \Carbon\Carbon::parse($item->created_at)->timestamp }}">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:28px;height:28px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:var(--red);font-size:11px;">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    {{ $item->announcer }}
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    // Sidebar toggle
    const SIDEBAR_COLLAPSED_KEY = 'internconnect_sidebar_collapsed';
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    // Restore persisted desktop sidebar state
    if (localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === 'true' && window.innerWidth > 900) {
        if (sidebar) sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
        document.documentElement.classList.add('sidebar-is-collapsed');
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                if (sidebar) sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem(SIDEBAR_COLLAPSED_KEY, isCollapsed ? 'true' : 'false');
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-is-collapsed');
                }
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    // Join student
    function joinStudent(url) {
        Swal.fire({
            title: 'Join this room?',
            text: 'You will be added to this class.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa fa-sign-in-alt"></i> Yes, Join',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal-poppins'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'Successfully joined the room!',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1800
                        });
                        setTimeout(() => location.reload(), 1800);
                    },
                    error: function () {
                        Swal.fire('Oops!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }

    // Leave student
    function leaveStudent() {
        Swal.fire({
            title: 'Leave this room?',
            text: 'You will be removed from this class.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa fa-sign-out-alt"></i> Yes, Leave',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url("/student/leave") }}',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'You left the room',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1800
                        });
                        setTimeout(() => location.reload(), 1800);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Oops!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }

    // File preview modal handler
    $(document).on('click', '.btn-preview-file', function (e) {
        e.preventDefault();
        var fileUrl = $(this).data('file-url');
        var fileName = $(this).data('file-name');
        var downloadUrl = $(this).data('download-url');

        $('#filePreviewTitle').text(fileName || 'Document Preview');
        $('#filePreviewSubTitle').text(fileName || '');
        $('#filePreviewDownloadBtn').attr('href', downloadUrl);
        $('#filePreviewFrame').attr('src', fileUrl);

        var modalEl = document.getElementById('filePreviewModal');
        if (modalEl && modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    });

    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('filePreviewModal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                var frame = document.getElementById('filePreviewFrame');
                if (frame) frame.src = 'about:blank';
            });
        }
    });
</script>

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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ vasset('js/student/class.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>