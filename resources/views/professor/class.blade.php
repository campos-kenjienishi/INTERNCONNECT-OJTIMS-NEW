<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Professor Class</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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
    <link rel="stylesheet" href="{{ vasset('css/professor_class-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/class.css') }}">
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
            <span class="nav-label">Requirement Status</span>
            <span class="tooltip-label">Requirement Status</span>
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
                <h1>My <span>Classes</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Class</span>
                </div>
            </div>
            <div class="page-header-actions">
                <a href="{{ $showArchived ? url('/professor/class') : url('/professor/class?view=archived') }}" class="btn-view-toggle">
                    <i class="fa fa-archive"></i>
                    <span>{{ $showArchived ? 'Active Classes' : 'Archived Classes' }}</span>
                    <span class="count-pill">{{ $showArchived ? ($activeClassCount ?? 0) : ($archivedClassCount ?? 0) }}</span>
                </a>
                <button class="btn-add-room" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fa fa-plus"></i> Add New Class
                </button>
            </div>
        </div>

        <!-- Rooms Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-chalkboard"></i></div>
                    <div>
                        <h2>{{ $showArchived ? 'Archived Classes' : 'Classes' }}</h2>
                        <p>{{ $showArchived ? 'Review archived classes, restore them, or remove them permanently' : 'Manage student enrollment, approve or deny requests, review requirements, upload templates, and post announcements' }}</p>
                    </div>
                </div>
                <div class="room-count-badge">
                    <i class="fa fa-door-open"></i>
                    {{ count($class) }} {{ count($class) == 1 ? 'class' : 'classes' }}
                </div>
            </div>

            <div class="table-card-body">

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Class Name</th>
                            <th>Semester</th>
                            <th>School Year</th>
                            <th>Schedule</th>
                            <th>Needing Approval</th>
                            <th>Students List</th>
                            <th>Templates</th>
                            <th>Announcement</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($class as $room)
                        <tr>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:6px;">
                                    <i class="fa fa-graduation-cap" style="color:var(--red);font-size:12px;"></i>
                                    {{ $room->course }}
                                </span>
                            </td>
                            <td>
                                <div class="room-cell">
                                    <div class="room-icon"><i class="fa fa-chalkboard"></i></div>
                                    <span class="room-name-text">{{ $room->room }}</span>
                                </div>
                            </td>
                            <td>{{ $room->semester ?? 'N/A' }}</td>
                            <td>
                                {{ $room->school_year_start && $room->school_year_end ? $room->school_year_start . ' - ' . $room->school_year_end : 'N/A' }}
                            </td>
                            <td>
                                @if (empty($room->schedule_parsed))
                                    <span style="font-size:12px;color:#888;">No schedule</span>
                                @else
                                    <div style="font-size:12px;line-height:1.5;">
                                        @php
                                            $groupedSchedule = [];
                                            foreach ($room->schedule_parsed as $slot) {
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
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/professor/listStudents', $room->id) }}"
                                    class="btn-action btn-approval">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                            </td>
                            <td>
                                <a href="{{ url('/professor/classList', $room->id) }}"
                                class="btn-action btn-students">
                                    <i class="fa fa-users"></i> View
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn-action btn-template"
                                data-bs-toggle="modal"
                                data-bs-target="#templateModal{{ $loop->index }}">
                                    <i class="fa fa-file-upload"></i>
                                    {{ $room->templateFiles->count() > 0 ? 'Manage (' . $room->templateFiles->count() . ')' : 'Upload' }}
                                </button>

                                <div class="modal fade" id="templateModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-template-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-file-upload"></i>
                                                    Room Templates - {{ $room->room }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form method="POST" action="{{ url('/uploadfile') }}" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="class_id" value="{{ $room->id }}">
                                                <div class="modal-body template-modal-body">
                                                    <label class="modal-field-label"><i class="fa fa-tag"></i> Template Name</label>
                                                    <input class="modal-field-input" type="text" name="name" placeholder="Enter template name" required>

                                                    <label class="modal-field-label"><i class="fa fa-paperclip"></i> File</label>
                                                    <input class="modal-field-input" type="file" name="file" data-max-size-mb="30" accept=".doc,.docx,.pdf" required>
                                                    <div style="margin-top:6px; font-size:12px; color:#777;">
                                                        Accepted: .doc, .docx, .pdf | Max file size: 30 MB
                                                    </div>
                                                    <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>

                                                    <div class="template-list-panel">
                                                        <div class="template-list-header">
                                                            <strong class="template-list-title">Uploaded in this room</strong>
                                                            <div class="template-list-toolbar">
                                                                <label class="template-search-wrap">
                                                                    <i class="fa fa-search"></i>
                                                                    <input
                                                                        type="search"
                                                                        class="template-search-input"
                                                                        placeholder="Search templates"
                                                                        data-template-search>
                                                                </label>
                                                                <span class="template-list-count">
                                                                    <i class="fa fa-folder-open"></i>
                                                                    {{ $room->templateFiles->count() }} template{{ $room->templateFiles->count() === 1 ? '' : 's' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="template-list-scroll">
                                                            @if ($room->templateFiles->isEmpty())
                                                                <p class="template-list-empty">No templates yet.</p>
                                                            @else
                                                                <ul class="template-list" data-template-list>
                                                                    @foreach ($room->templateFiles as $template)
                                                                        <li class="template-list-item" data-template-item data-template-name="{{ strtolower($template->name) }}">
                                                                            <div class="template-list-item-main">
                                                                                <span class="template-list-icon">
                                                                                    <i class="fa fa-file-alt"></i>
                                                                                </span>
                                                                                <span class="template-list-name">{{ $template->name }}</span>
                                                                            </div>
                                                                            <div class="template-list-actions">
                                                                                 @php
                                                                                     $templateExt = strtolower(pathinfo($template->file, PATHINFO_EXTENSION));
                                                                                 @endphp
                                                                                  @if(in_array($templateExt, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'svg']))
                                                                                      <button type="button"
                                                                                              class="btn-template-view btn-preview-file"
                                                                                              style="cursor:pointer;"
                                                                                              data-file-url="{{ url('/view/file', $template->file) }}"
                                                                                              data-file-name="{{ $template->name }}"
                                                                                              data-download-url="{{ url('/download', $template->file) }}">
                                                                                          <i class="fa fa-eye"></i> View
                                                                                      </button>
                                                                                  @endif
                                                                                <a href="{{ url('/download', $template->file) }}" class="btn-template-download">
                                                                                    <i class="fa fa-download"></i> Download
                                                                                </a>
                                                                                <button
                                                                                    type="button"
                                                                                    class="btn-remove-template"
                                                                                    data-action="{{ url('/professor/template/remove', $template->id) }}">
                                                                                    Remove
                                                                                </button>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                                <div class="template-list-empty-state" data-template-empty-state>
                                                                    No matching templates found.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                                                        <i class="fa fa-times me-1"></i> Close
                                                    </button>
                                                    <button type="submit" class="btn-modal-submit">
                                                        <i class="fa fa-upload me-1"></i> Upload Template
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn-action btn-announce"
                                data-bs-toggle="modal"
                                data-bs-target="#announcementModal{{ $loop->index }}">
                                    <i class="fa fa-bullhorn"></i> Add
                                </button>

                                <!-- Announcement Modal -->
                                <div class="modal fade" id="announcementModal{{ $loop->index }}"
                                     tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-bullhorn"></i>
                                                    Announcement — {{ $room->room }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form class="announcementForm" method="POST" action="{{ url('/announcements') }}">
                                                @csrf
                                                <input type="hidden" name="audience" value="class">
                                                <input type="hidden" name="course" value="{{ $room->course }}">
                                                <input type="hidden" name="room"   value="{{ $room->room }}">
                                                <div class="modal-body">
                                                    <label class="modal-field-label">
                                                        <i class="fa fa-tag"></i> Title
                                                    </label>
                                                    <input class="modal-field-input" type="text"
                                                           name="title" placeholder="Announcement title" required>

                                                    <label class="modal-field-label">
                                                        <i class="fa fa-align-left"></i> Content
                                                    </label>
                                                    <textarea class="modal-field-textarea"
                                                              name="content" placeholder="Write your announcement here..." required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                                                        <i class="fa fa-times me-1"></i> Close
                                                    </button>
                                                    <button type="submit" class="btn-modal-submit-blue">
                                                        <i class="fa fa-paper-plane me-1"></i> Post Announcement
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Announcement Modal -->
                            </td>
                            <td>
                                <div class="actions-cell">
                                @if(!$showArchived)
                                    <button class="icon-action-btn btn-edit" title="Edit" aria-label="Edit" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $room->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="icon-action-btn btn-archive btn-archive-room" title="Archive" aria-label="Archive" data-id="{{ $room->id }}">
                                        <i class="fa fa-archive"></i>
                                    </button>
                                    <button class="icon-action-btn btn-remove btn-remove-room" title="Delete" aria-label="Delete" data-id="{{ $room->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @else
                                    <button class="icon-action-btn btn-archive btn-unarchive-room" title="Unarchive" aria-label="Unarchive" data-id="{{ $room->id }}">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                    <button class="icon-action-btn btn-remove btn-remove-room" title="Delete Permanently" aria-label="Delete Permanently" data-id="{{ $room->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endif

                                <div class="modal fade" id="editRoomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-edit"></i> Edit Class
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ url('/roomUpdate', $room->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <label class="modal-field-label"><i class="fa fa-chalkboard"></i> Class Name</label>
                                                    <input class="modal-field-input" type="text" name="room" value="{{ $room->room }}" placeholder="Enter class name" required>

                                                    <label class="modal-field-label"><i class="fa fa-graduation-cap"></i> Course</label>
                                                    <select name="course" class="modal-field-select" required>
                                                        @foreach ($course as $c)
                                                            <option value="{{ $c->course }}" {{ $room->course == $c->course ? 'selected' : '' }}>{{ $c->course }}</option>
                                                        @endforeach
                                                    </select>

                                                    <label class="modal-field-label"><i class="fa fa-calendar-alt"></i> Semester</label>
                                                    <select name="semester" class="modal-field-select" required>
                                                        <option value="1st Sem" {{ ($room->semester ?? '') == '1st Sem' ? 'selected' : '' }}>1st Sem</option>
                                                        <option value="2nd Sem" {{ ($room->semester ?? '') == '2nd Sem' ? 'selected' : '' }}>2nd Sem</option>
                                                        <option value="Summer" {{ ($room->semester ?? '') == 'Summer' ? 'selected' : '' }}>Summer</option>
                                                    </select>

                                                    <label class="modal-field-label"><i class="fa fa-calendar"></i> School Year</label>
                                                    @php
                                                        $currentY = (int) date('Y');
                                                        $syStart = !empty($room->school_year_start) ? (int) $room->school_year_start : $currentY;
                                                        $syEnd = !empty($room->school_year_end) ? (int) $room->school_year_end : ($syStart + 1);
                                                        $selectedSyVal = $syStart . '-' . $syEnd;
                                                        $syOptions = [];
                                                        for ($y = $currentY + 2; $y >= $currentY - 5; $y--) {
                                                            $syOptions[$y . '-' . ($y + 1)] = ['start' => $y, 'end' => $y + 1, 'label' => $y . ' - ' . ($y + 1)];
                                                        }
                                                        if (!empty($room->school_year_start) && !isset($syOptions[$selectedSyVal])) {
                                                            $syOptions[$selectedSyVal] = ['start' => $syStart, 'end' => $syEnd, 'label' => $syStart . ' - ' . $syEnd];
                                                        }
                                                    @endphp
                                                    <select class="modal-field-select school-year-select" required>
                                                        <option value="">Select School Year</option>
                                                        @foreach ($syOptions as $k => $opt)
                                                            <option value="{{ $k }}" data-start="{{ $opt['start'] }}" data-end="{{ $opt['end'] }}" {{ $selectedSyVal === $k ? 'selected' : '' }}>
                                                                {{ $opt['label'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="school_year_start" value="{{ $syStart }}">
                                                    <input type="hidden" name="school_year_end" value="{{ $syEnd }}">

                                                    @php
                                                        $existingSchedule = is_array($room->schedule_parsed ?? null) ? $room->schedule_parsed : [];
                                                        $selectedEditDays = [];
                                                        foreach ($existingSchedule as $slot) {
                                                            if (!empty($slot['day'])) {
                                                                $selectedEditDays[] = $slot['day'];
                                                            }
                                                        }
                                                        $selectedEditDays = array_values(array_unique($selectedEditDays));
                                                        $editSlotCount = !empty($room->schedule_slots) ? (int) $room->schedule_slots : 1;
                                                    @endphp

                                                    <label class="modal-field-label"><i class="fa fa-calendar-week"></i> Schedule Days</label>
                                                    <div style="display:grid;grid-template-columns:repeat(3,minmax(120px,1fr));gap:8px;">
                                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#444;">
                                                                <input type="checkbox" class="edit-schedule-day" data-room-id="{{ $room->id }}" name="schedule_day[]" value="{{ $day }}" {{ in_array($day, $selectedEditDays) ? 'checked' : '' }}>
                                                                {{ $day }}
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                    <label class="modal-field-label" style="margin-top:10px;"><i class="fa fa-list-ol"></i> Number of Time Slots</label>
                                                    <select id="edit_time_slots_{{ $room->id }}" name="time_slots" class="modal-field-select edit-time-slots" data-room-id="{{ $room->id }}">
                                                        <option value="1" {{ $editSlotCount === 1 ? 'selected' : '' }}>1</option>
                                                        <option value="2" {{ $editSlotCount === 2 ? 'selected' : '' }}>2</option>
                                                        <option value="3" {{ $editSlotCount === 3 ? 'selected' : '' }}>3</option>
                                                        <option value="4" {{ $editSlotCount === 4 ? 'selected' : '' }}>4</option>
                                                    </select>

                                                    <div id="editRoomScheduleInputs{{ $room->id }}" data-initial-schedule='@json($existingSchedule)' style="margin-top:10px;"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                                                        <i class="fa fa-times me-1"></i> Close
                                                    </button>
                                                    <button type="submit" class="btn-modal-submit">
                                                        <i class="fa fa-save me-1"></i> Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-bullhorn"></i></div>
                    <div>
                        <h2>My Announcements</h2>
                        <p>Review or delete announcements you posted</p>
                    </div>
                </div>
                <div class="announcement-toolbar">
                    <div class="room-count-badge">
                        <i class="fa fa-list"></i>
                        {{ count($announcements ?? []) }} {{ count($announcements ?? []) == 1 ? 'announcement' : 'announcements' }}
                    </div>
                    <select id="profAnnouncementSort" class="announcement-sort-select" aria-label="Sort announcements by date">
                        <option value="desc" selected>Newest first</option>
                        <option value="asc">Oldest first</option>
                    </select>
                </div>
            </div>

            <div class="table-card-body announcement-table-wrap">
                <table id="profAnnouncementTable" class="announcement-manage-table">
                    <colgroup>
                        <col style="width:42%;">
                        <col style="width:22%;">
                        <col style="width:22%;">
                        <col style="width:14%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Target Class</th>
                            <th>Date Posted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($announcements ?? []) as $announcement)
                            <tr>
                                <td class="announcement-title-cell">
                                    <strong>{{ $announcement->title }}</strong>
                                    <span>{{ Str::limit($announcement->content, 90) }}</span>
                                </td>
                                <td>
                                    {{ $announcement->target_course ?? 'Class' }}
                                    @if(!empty($announcement->target_room))
                                        - {{ $announcement->target_room }}
                                    @endif
                                </td>
                                <td data-order="{{ \Carbon\Carbon::parse($announcement->created_at)->timestamp }}">{{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    <button type="button"
                                            class="btn-edit-announcement"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAnnouncementModal"
                                            data-announcement-id="{{ $announcement->id }}"
                                            data-announcement-title="{{ e($announcement->title) }}"
                                            data-announcement-content="{{ e($announcement->content) }}"
                                            data-announcement-action="{{ route('announcements.update', $announcement->id) }}">
                                        <i class="fa fa-pen"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('announcements.destroy', $announcement->id) }}" class="delete-announcement-form" data-announcement-title="{{ e($announcement->title) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-announcement">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-pen"></i> Edit Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editAnnouncementForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <label class="modal-field-label">
                                <i class="fa fa-heading"></i> Announcement Title
                            </label>
                            <input class="modal-field-input" type="text" name="title" id="editAnnouncementTitle" required>

                            <label class="modal-field-label">
                                <i class="fa fa-align-left"></i> Content
                            </label>
                            <textarea class="modal-field-input" name="content" id="editAnnouncementContent" rows="6" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn-modal-submit">
                                <i class="fa fa-save me-1"></i> Update Announcement
                            </button>
                        </div>
                    </form>
                </div>
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

<!-- =============== ADD ROOM MODAL =============== -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-plus-circle"></i> Add New Class
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRoomForm" action="{{ url('/roomCreate') }}" method="post">
                @csrf
                <div class="modal-body">
                    <label class="modal-field-label">
                        <i class="fa fa-chalkboard"></i> Class Name
                    </label>
                    <input class="modal-field-input" type="text" name="room"
                           placeholder="Enter class name" required>

                    <label class="modal-field-label">
                        <i class="fa fa-graduation-cap"></i> Course
                    </label>
                    <select name="course" class="modal-field-select" required>
                        <option value="">Select a course</option>
                        @foreach ($course as $c)
                            <option value="{{ $c->course }}">{{ $c->course }}</option>
                        @endforeach
                    </select>

                    <label class="modal-field-label">
                        <i class="fa fa-calendar-alt"></i> Semester
                    </label>
                    <select name="semester" class="modal-field-select" required>
                        <option value="">Select semester</option>
                        <option value="1st Sem">1st Sem</option>
                        <option value="2nd Sem">2nd Sem</option>
                        <option value="Summer">Summer</option>
                    </select>

                    <label class="modal-field-label">
                        <i class="fa fa-calendar"></i> School Year
                    </label>
                    @php
                        $currentY = (int) date('Y');
                    @endphp
                    <select class="modal-field-select school-year-select" required>
                        <option value="">Select School Year</option>
                        @for ($y = $currentY + 2; $y >= $currentY - 5; $y--)
                            <option value="{{ $y }}-{{ $y + 1 }}" data-start="{{ $y }}" data-end="{{ $y + 1 }}" {{ $y === $currentY ? 'selected' : '' }}>
                                {{ $y }} - {{ $y + 1 }}
                            </option>
                        @endfor
                    </select>
                    <input type="hidden" name="school_year_start" value="{{ $currentY }}">
                    <input type="hidden" name="school_year_end" value="{{ $currentY + 1 }}">

                    <label class="modal-field-label">
                        <i class="fa fa-calendar-week"></i> Schedule Days
                    </label>
                    <div style="display:grid;grid-template-columns:repeat(3,minmax(120px,1fr));gap:8px;">
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#444;">
                                <input type="checkbox" class="add-schedule-day" name="schedule_day[]" value="{{ $day }}" {{ $day === 'Monday' ? 'checked' : '' }}>
                                {{ $day }}
                            </label>
                        @endforeach
                    </div>

                    <label class="modal-field-label" style="margin-top:10px;">
                        <i class="fa fa-list-ol"></i> Number of Time Slots
                    </label>
                    <select id="add_time_slots" name="time_slots" class="modal-field-select">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>

                    <div id="addRoomScheduleInputs" style="margin-top:10px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-check me-1"></i> Create Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/class.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/upload-size-guard.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>