<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InternConnect - Requirements</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/student_filereq-responsive.css') }}">
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
    <link rel="stylesheet" href="{{ vasset('css/student/file-req.css') }}">
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
            <span class="user-name">{{ $user->full_name }}</span>
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
        <a href="{{ url('/student/class') }}" class="nav-item">
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
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">Notarized MOA</span>
            <span class="tooltip-label">Notarized MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item active">
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
                <h1>File <span>Requirements</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Requirements</span>
                </div>
                @if(!empty($isInhouseOjt))
                    <div style="display:inline-flex; align-items:center; gap:6px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; margin-top:8px;">
                        <i class="fa fa-university"></i> School In-House OJT Mode (External MOA Waived)
                    </div>
                @endif
            </div>
            <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fa fa-cloud-upload-alt"></i> Upload Document
            </button>
        </div>

        @if(session('fail'))
            <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:12px; font-size:13.5px; margin-bottom:18px; font-weight:500;">
                <i class="fa fa-exclamation-circle me-1"></i> {{ session('fail') }}
            </div>
        @endif
        @if(session('success'))
            <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:12px; font-size:13.5px; margin-bottom:18px; font-weight:500;">
                <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @php
            $currentAdviser = trim((string)($user->adviser_name ?? ''));
        @endphp
        @if(empty($currentAdviser) || $currentAdviser === 'Not Assigned' || $currentAdviser === 'Not Yet Listed')
            <div style="background:#fffbeb; border:1px solid #fde68a; color:#92400e; padding:14px 18px; border-radius:14px; font-size:13.5px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fa fa-exclamation-triangle" style="font-size:18px; color:#d97706;"></i>
                    <span>
                        <strong>No Professor Assigned:</strong> You currently do not have an assigned professor linked to your account. Please go to <a href="{{ url('/student/class') }}" style="color:#b45309; text-decoration:underline; font-weight:600;">Class Settings</a> to select your professor.
                    </span>
                </div>
            </div>
        @endif

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ count($data) }}</div>
                    <div class="stat-name">Total Submitted</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $data->where('status', 1)->count() }}</div>
                    <div class="stat-name">Approved</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-clock"></i></div>
                <div>
                    <div class="stat-num">{{ $data->where('status', 0)->count() }}</div>
                    <div class="stat-name">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gray"><i class="fa fa-times-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $data->where('status', 2)->count() }}</div>
                    <div class="stat-name">Denied</div>
                </div>
            </div>
        </div>

        @php
            $submittedBasicCount = $submittedBasicNames->count();
            $totalBasicCount = $basicCategories->count();
            $allBasicDone = $totalBasicCount > 0 && $missingBasicCategories->isEmpty();

            if ($allBasicDone) {
                $basicIconColor = 'green';
                $basicBadgeClass = 'status-approved';
                $basicStatusText = 'Completed';
            } elseif ($submittedBasicCount === 0) {
                $basicIconColor = 'red';
                $basicBadgeClass = 'status-denied';
                $basicStatusText = 'Not Started';
            } else {
                $basicIconColor = 'amber';
                $basicBadgeClass = 'status-pending';
                $basicStatusText = 'In Progress';
            }
        @endphp

        <div class="stats-row" style="margin-top:-6px;">
            <div class="stat-card">
                <div class="stat-icon {{ $basicIconColor }}"><i class="fa fa-unlock-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $submittedBasicCount }}/{{ $totalBasicCount }}</div>
                    <div class="stat-name">Basic Requirements Submitted</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon {{ $hasSubmittedNotarizedMoa ? 'green' : 'red' }}"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">{{ $hasSubmittedNotarizedMoa ? 'Yes' : 'No' }}</div>
                    <div class="stat-name">Notarized MOA Submitted</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon {{ $otherRequirementsUnlocked ? 'green' : 'red' }}"><i class="fa fa-layer-group"></i></div>
                <div>
                    <div class="stat-num">{{ $otherRequirementsUnlocked ? 'Unlocked' : 'Locked' }}</div>
                    <div class="stat-name">Other Requirements</div>
                </div>
            </div>
        </div>

        <div class="table-card" style="margin-bottom:24px;">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-stream"></i></div>
                    <div>
                        <h2>Requirement Phases</h2>
                        <p>Finish the basic set and submit your Notarized MOA to unlock the rest</p>
                    </div>
                </div>
            </div>
            <div class="table-card-body" style="padding:20px;">
                <div style="margin-bottom:16px;padding:14px 16px;border-radius:14px;background:#fff8f1;border:1px solid #fed7aa;font-size:13px;color:#9a3412;line-height:1.6;">
                    Upload the basic requirements first, then submit your Notarized MOA to unlock the during-OJT and after-OJT files.
                </div>

                <div class="phase-grid">
                    <div class="phase-panel basic">
                        <div class="phase-panel-header">
                            <div class="phase-panel-title-row">
                                <div class="phase-panel-title">Basic Requirements</div>
                                <span class="status-badge status-approved phase-header-badge"><i class="fa fa-unlock"></i> Always Open</span>
                            </div>
                            <div class="phase-panel-subtitle">Required before you start your OJT.</div>
                        </div>

                        <div class="phase-summary-stats">
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Submitted</div>
                                <div class="phase-summary-stat-value">{{ $submittedBasicCount }}/{{ $totalBasicCount }}</div>
                            </div>
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Status</div>
                                <div class="phase-summary-stat-value">{{ $basicStatusText }}</div>
                            </div>
                        </div>

                        <div class="phase-summary-actions">
                            <button type="button" class="phase-view-btn" data-bs-toggle="modal" data-bs-target="#basicRequirementsModal">
                                <i class="fa fa-eye"></i> View Requirements
                            </button>
                        </div>
                    </div>

                    <div class="phase-panel">
                        <div class="phase-panel-header">
                            <div class="phase-panel-title-row">
                                <div class="phase-panel-title">Other Requirements</div>
                                <span class="status-badge {{ $otherRequirementsUnlocked ? 'status-approved' : 'status-denied' }} phase-header-badge">
                                    <i class="fa {{ $otherRequirementsUnlocked ? 'fa-unlock' : 'fa-lock' }}"></i>
                                    {{ $otherRequirementsUnlocked ? 'Unlocked' : 'Locked' }}
                                </span>
                            </div>
                            <div class="phase-panel-subtitle">These open after the basics and Notarized MOA are done.</div>
                        </div>

                        <div class="phase-summary-stats">
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Requirements</div>
                                <div class="phase-summary-stat-value">{{ $otherCategories->count() }}</div>
                            </div>
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Access</div>
                                <div class="phase-summary-stat-value">{{ $otherRequirementsUnlocked ? 'Available' : 'Locked' }}</div>
                            </div>
                        </div>

                        <div class="phase-summary-actions">
                            <button type="button" class="phase-view-btn" data-bs-toggle="modal" data-bs-target="#otherRequirementsModal">
                                <i class="fa fa-eye"></i> View Requirements
                            </button>
                        </div>
                    </div>

                    <div class="phase-panel">
                        <div class="phase-panel-header" style="margin-bottom:12px;">
                            <div>
                                <div class="phase-panel-title">Unlock Progress</div>
                                <div class="phase-panel-subtitle">Track what is still needed before the next phase opens.</div>
                            </div>
                        </div>

                        <div class="phase-progress-list">
                            <div class="phase-progress-item">
                                <span>Basic Requirements Completed</span>
                                <span class="status-badge {{ $basicBadgeClass }}">
                                    {{ $submittedBasicCount }}/{{ $totalBasicCount ?: 0 }}
                                </span>
                            </div>
                            <div class="phase-progress-item">
                                <span>Notarized MOA Submitted</span>
                                <span class="status-badge {{ $hasSubmittedNotarizedMoa ? 'status-approved' : 'status-denied' }}">
                                    {{ $hasSubmittedNotarizedMoa ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            <div class="phase-progress-item">
                                <span>Other Requirements Access</span>
                                <span class="status-badge {{ $otherRequirementsUnlocked ? 'status-approved' : 'status-denied' }}">
                                    {{ $otherRequirementsUnlocked ? 'Unlocked' : 'Locked' }}
                                </span>
                            </div>
                        </div>

                        @if (!$otherRequirementsUnlocked)
                            <div class="phase-summary-actions" style="margin-top:2px;">
                                <button type="button" class="phase-view-btn" data-bs-toggle="modal" data-bs-target="#unlockRequirementsModal">
                                    <i class="fa fa-clipboard-list"></i> What Still Needs To Be Submitted
                                </button>
                            </div>
                        @else
                            <div class="phase-note success">
                                Other requirements are unlocked. You can now submit the during-OJT and after-OJT files.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Requirements Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                    <div>
                        <h2>Submitted Requirements</h2>
                        <p>Manage and track all your uploaded OJT requirement files</p>
                    </div>
                </div>
                
            </div>

            <div class="table-card-body">

                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        const fileTable = $('#fileTable').DataTable({
                            "order": [[3, 'desc']],
                            "scrollX": true,
                            "autoWidth": false,
                            "columnDefs": [
                                { "width": "32%", "targets": 0 },
                                { "width": "18%", "targets": 2 },
                                { "width": "14%", "targets": 3 },
                                { "width": "18%", "targets": 4 }
                            ]
                        });

                        $('#fileTable tbody').on('click', '.remove-button', function (e) {
                            e.preventDefault();
                            var fileId = $(this).data('file-id');
                            showRemoveConfirmation(fileId);
                        });

                        $('#fileTable tbody').on('click', '.view-button', function (e) {
                            e.preventDefault();
                            var fileUrl = $(this).data('file-url');
                            var fileName = $(this).data('file-name');
                            var downloadUrl = $(this).data('download-url');
                            var fileExt = (fileName.split('.').pop() || '').toLowerCase();
                            var previewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt', 'html', 'htm'].indexOf(fileExt) !== -1;

                            $('#previewFileName').text(fileName);
                            $('#previewDownloadBtn').attr('href', downloadUrl);
                            $('#previewDownloadBtnBottom').attr('href', downloadUrl);
                            if (previewable) {
                                $('#previewFrame').show().attr('src', fileUrl);
                                $('#previewFallback').hide();
                                $('#previewBadge').removeClass('no-preview').html('<i class="fa fa-eye"></i> Preview available');
                            } else {
                                $('#previewFrame').hide().attr('src', 'about:blank');
                                $('#previewFallback').show();
                                $('#previewBadge').addClass('no-preview').html('<i class="fa fa-file-download"></i> No preview available');
                            }
                             var modalEl = document.getElementById('previewModal');
                             if (modalEl && modalEl.parentNode !== document.body) {
                                 document.body.appendChild(modalEl);
                             }
                             var previewModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                             previewModal.show();
                        });

                        document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
                            $('#previewFrame').attr('src', 'about:blank');
                            $('#previewFrame').show();
                            $('#previewFallback').hide();
                            $('#previewBadge').removeClass('no-preview').html('<i class="fa fa-eye"></i> Preview available');
                            $('#previewDownloadBtn').attr('href', '#');
                            $('#previewDownloadBtnBottom').attr('href', '#');
                        });
                    });
                </script>

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $files)
                        <tr>
                            <td>
                                <div class="category-cell">
                                    <div class="category-icon"><i class="fa fa-file-alt"></i></div>
                                    <span class="category-name">{{ $files->fileName }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="file-cell">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $files->file }}
                                </div>
                            </td>
                            <td>
                                <div class="status-cell">
                                    @if ($files->status == 1)
                                        <span class="status-badge status-approved">
                                            <i class="fa fa-check-circle"></i> Approved
                                        </span>
                                    @elseif ($files->status == 2)
                                        <span class="status-badge status-denied">
                                            <i class="fa fa-times-circle"></i> Denied
                                        </span>
                                        @if(!empty($files->denial_reason))
                                            <button type="button"
                                                class="btn-status-reason"
                                                onclick="showDenialReason({{ Illuminate\Support\Js::from($files->fileName) }}, {{ Illuminate\Support\Js::from($files->denial_reason) }})">
                                                <i class="fa fa-comment-alt"></i> View Reason
                                            </button>
                                        @endif
                                    @elseif ($files->status == 0)
                                        <span class="status-badge status-pending">
                                            <i class="fa fa-clock"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="date-main">{{ \Carbon\Carbon::parse($files->created_at)->format('M d, Y') }}</div>
                                <div class="date-sub">{{ \Carbon\Carbon::parse($files->created_at)->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <button type="button" class="btn-view view-button" data-file-url="{{ url('/student/requirements/view/' . $files->id) }}" data-download-url="{{ url('/student/requirements/download/' . $files->id) }}" data-file-name="{{ $files->file }}">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <button class="btn-remove remove-button" data-file-id="{{ $files->id }}">
                                        <i class="fa fa-trash"></i> Remove
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

<!-- =============== UPLOAD MODAL =============== -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered upload-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-cloud-upload-alt"></i> Submit Requirement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/uploadReq') }}" method="post" enctype="multipart/form-data" class="upload-modal-form">
                @csrf

                <div class="modal-body upload-modal-body">

                    <div class="upload-modal-layout">
                        <div class="upload-modal-left">
                            <div class="upload-modal-section phase">
                                <div class="upload-modal-section-head">
                                    <div class="upload-modal-section-icon"><i class="fa fa-stream"></i></div>
                                    <div>
                                        <div class="upload-modal-section-title">Requirement Phase</div>
                                        <div class="upload-modal-section-subtitle">Choose which phase this requirement belongs to.</div>
                                    </div>
                                </div>
                                <input type="hidden" name="phase" id="requirementPhaseSelect" value="basic">
                                <div class="phase-dropdown" id="phaseDropdown">
                                    <button type="button" class="phase-dropdown-trigger" id="phaseDropdownTrigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="phase-dropdown-trigger-text" id="phaseDropdownTriggerText">Basic Requirements</span>
                                        <i class="fa fa-chevron-down phase-dropdown-trigger-icon"></i>
                                    </button>
                                    <div class="phase-dropdown-menu" id="phaseDropdownMenu" role="listbox" aria-label="Requirement Phase">
                                        <button type="button" class="phase-dropdown-option active" data-value="basic">
                                            <span class="phase-dropdown-option-label">
                                                <span class="phase-dropdown-option-title">Basic Requirements</span>
                                                <span class="phase-dropdown-option-meta">Upload these before starting OJT.</span>
                                            </span>
                                        </button>
                                        <button
                                            type="button"
                                            class="phase-dropdown-option {{ $otherRequirementsUnlocked ? '' : 'locked' }}"
                                            data-value="other"
                                            {{ $otherRequirementsUnlocked ? '' : 'data-locked=true' }}
                                        >
                                            <span class="phase-dropdown-option-label">
                                                <span class="phase-dropdown-option-title">Other Requirements</span>
                                                <span class="phase-dropdown-option-meta">During-OJT and after-OJT uploads.</span>
                                            </span>
                                            @if (!$otherRequirementsUnlocked)
                                                <span class="phase-dropdown-option-status">Locked</span>
                                                <div class="phase-dropdown-option-tooltip">
                                                    Upload all basic requirements first before the other requirements phase becomes available.
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                </div>
                                <div class="upload-modal-help-row">
                                    <div id="phaseHelpText" class="upload-modal-help">
                                        Upload your basic requirements first.
                                    </div>
                                </div>
                            </div>

                            <div class="upload-modal-section">
                                <div class="upload-modal-section-head">
                                    <div class="upload-modal-section-icon"><i class="fa fa-tag"></i></div>
                                    <div>
                                        <div class="upload-modal-section-title">Category</div>
                                        <div class="upload-modal-section-subtitle">Select the exact requirement you are uploading.</div>
                                    </div>
                                </div>
                                <input type="hidden" name="fileName" id="requirementCategorySelect" value="">
                                <div class="phase-dropdown" id="categoryDropdown">
                                    <button type="button" class="phase-dropdown-trigger" id="categoryDropdownTrigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="phase-dropdown-trigger-text" id="categoryDropdownTriggerText">Select a category</span>
                                        <i class="fa fa-chevron-down phase-dropdown-trigger-icon"></i>
                                    </button>
                                    <div class="phase-dropdown-menu category-dropdown-menu-scroll" id="categoryDropdownMenu" role="listbox" aria-label="Requirement Category">
                                        <div class="phase-dropdown-option category-dropdown-option empty">
                                            <span class="phase-dropdown-option-label">
                                                <span class="phase-dropdown-option-title">Select a category</span>
                                                <span class="phase-dropdown-option-meta">Choose a phase first to load the available categories.</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="upload-modal-right">
                            <div class="upload-modal-section file">
                                <div class="upload-modal-section-head">
                                    <div class="upload-modal-section-icon"><i class="fa fa-paperclip"></i></div>
                                    <div>
                                        <div class="upload-modal-section-title">Choose File</div>
                                        <div class="upload-modal-section-subtitle">Upload a clear PDF copy of your requirement.</div>
                                    </div>
                                </div>
                                <div class="file-upload-zone" id="dropZone">
                                    <input type="file" name="file" required id="fileInput" data-max-size-mb="30" accept="application/pdf,.pdf">
                                    <div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
                                    <p id="fileLabel">Click or drag a file here to upload</p>
                                    <span>Accepts PDF files only | Max file size: 30 MB</span>
                                    <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="uploadedBy" value="{{ $user->full_name }}">
                    <input type="hidden" name="adviser" value="{{ $user->adviser_name }}">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-paper-plane me-1"></i> Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="basicRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-list-ul"></i> Basic Requirements
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px;">
                    These are the requirements you need to complete before starting your OJT.
                </div>
                <div class="phase-modal-list">
                    @forelse ($basicCategories as $category)
                        @php
                            $isSubmittedBasic = $submittedBasicNames->contains($category->fileName);
                        @endphp
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa {{ $isSubmittedBasic ? 'fa-check-circle' : 'fa-file-alt' }}" style="color:{{ $isSubmittedBasic ? '#16a34a' : '#64748b' }};"></i>
                                <span>{{ $category->fileName }}</span>
                            </div>
                            <span class="status-badge {{ $isSubmittedBasic ? 'status-approved' : 'status-pending' }}">
                                {{ $isSubmittedBasic ? 'Submitted' : 'Pending' }}
                            </span>
                        </div>
                    @empty
                        <div style="font-size:13px;color:#6b7280;">No basic requirements configured yet.</div>
                    @endforelse
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

<div class="modal fade" id="otherRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-layer-group"></i> Other Requirements
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px;">
                    These will be available after all basic requirements are submitted and your Notarized MOA is completed.
                </div>
                <div class="phase-modal-list">
                    @forelse ($otherCategories as $category)
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa fa-file-alt" style="color:#64748b;"></i>
                                <span>{{ $category->fileName }}</span>
                            </div>
                            <span class="status-badge {{ $otherRequirementsUnlocked ? 'status-approved' : 'status-denied' }}">
                                {{ $otherRequirementsUnlocked ? 'Available' : 'Locked' }}
                            </span>
                        </div>
                    @empty
                        <div style="font-size:13px;color:#6b7280;">No other requirements configured yet.</div>
                    @endforelse
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

<div class="modal fade" id="unlockRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-clipboard-list"></i> What Still Needs To Be Submitted
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px;">
                    Complete these items to unlock the other requirements phase.
                </div>
                <div class="phase-modal-list">
                    @if ($missingBasicCategories->isNotEmpty())
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa fa-file-alt" style="color:#64748b;"></i>
                                <span>Basic requirements left: {{ $missingBasicCategories->pluck('fileName')->implode(', ') }}</span>
                            </div>
                            <span class="status-badge status-pending">Pending</span>
                        </div>
                    @endif
                    @if (!$hasSubmittedNotarizedMoa)
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa fa-file-contract" style="color:#64748b;"></i>
                                <span>Submit your Notarized MOA from the MOA page.</span>
                            </div>
                            <span class="status-badge status-pending">Pending</span>
                        </div>
                    @endif
                    @if ($missingBasicCategories->isEmpty() && $hasSubmittedNotarizedMoa)
                        <div style="font-size:13px;color:#16a34a;">All required items are completed.</div>
                    @endif
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

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-eye"></i> View Requirement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0; background:#f8fafc;">
                <div style="padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#fff; color:#475569; font-size:13px; font-weight:600; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                    <span id="previewBadge" class="file-preview-badge"><i class="fa fa-eye"></i> Preview available</span>
                    <a id="previewDownloadBtn" href="#" class="btn-download-file"><i class="fa fa-download"></i> Download</a>
                </div>
                <iframe id="previewFrame" title="Requirement Preview" style="width:100%; height:75vh; border:0; background:#fff;"></iframe>
                <div id="previewFallback" class="unsupported-file-message" style="display:none;">
                    <div class="unsupported-icon">
                        <i class="fa fa-file-alt"></i>
                    </div>
                    <h3>This type of file cannot be previewed</h3>
                    <p>Please download the file to view its contents.</p>
                    <a id="previewDownloadBtnBottom" href="#" class="btn-download-file"><i class="fa fa-download"></i> Download to View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
    window.studentFileReqConfig = {
        basicCategories: @json($basicCategories->map(fn ($category) => ['fileName' => $category->fileName])->values()),
        otherCategories: @json($otherCategories->map(fn ($category) => ['fileName' => $category->fileName])->values()),
        otherRequirementsUnlocked: @json($otherRequirementsUnlocked),
        missingBasicCategories: @json($missingBasicCategories->pluck('fileName')->values()),
        hasSubmittedNotarizedMoa: @json($hasSubmittedNotarizedMoa),
        submittedRequirementNames: @json($submittedRequirementNames->values())
    };
</script>
    <script src="{{ vasset('js/student/file-req.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>