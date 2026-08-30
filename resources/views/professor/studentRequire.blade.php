<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Requirements</title>
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
    <link rel="stylesheet" href="{{ vasset('css/professor_studentRequire-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/student-require.css') }}">
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
        @php
            $backToClassListUrl = !empty($roomId) ? url('/professor/classList', $roomId) : url('/professor/class');
        @endphp

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Student <span>Requirements</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ $backToClassListUrl }}">Class List</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Requirements</span>
                </div>
            </div>
            <button class="btn-back"
                onclick="window.location.href='{{ $backToClassListUrl }}'">
                <i class="fa fa-arrow-left"></i> Back to Class List
            </button>
        </div>

        <!-- Stats Row -->
        @php
            $totalReq    = count($files);
            $approvedReq = $files->where('status', 1)->count();
            $deniedReq   = $files->where('status', 2)->count();
            $pendingReq  = $files->whereNotIn('status', [1, 2])->count();
        @endphp

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $totalReq }}</div>
                    <div class="stat-name">Total Files</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-clock"></i></div>
                <div>
                    <div class="stat-num">{{ $pendingReq }}</div>
                    <div class="stat-name">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $approvedReq }}</div>
                    <div class="stat-name">Approved</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-times-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $deniedReq }}</div>
                    <div class="stat-name">Denied</div>
                </div>
            </div>
        </div>

        <!-- Requirements Table Card -->
        <div class="table-card">
                <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-tasks"></i></div>
                    <div>
                        <h2>
                            Submitted Requirements
                            @if(!empty($value))
                                : <span style="font-weight:700;color:#444;font-size:16px;">{{ $value }}</span>
                            @endif
                        </h2>
                        <p>Review, approve or deny student requirement files</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="display:flex;gap:8px;align-items:center;">
                        <label style="font-size:13px;color:#666;margin-right:6px;">Date</label>
                        <select id="dateSort" class="form-select" style="padding:6px 10px;border-radius:8px;border:1px solid #e5e5e5;font-size:13px;width:220px;min-width:180px;">
                            <option value="none">None</option>
                            <option value="newest" selected>Newest first</option>
                            <option value="oldest">Oldest first</option>
                        </select>
                    </div>

                    <div style="display:flex;gap:8px;align-items:center;">
                        <label style="font-size:13px;color:#666;margin-right:6px;">Category</label>
                        <select id="nameSort" class="form-select" style="padding:6px 10px;border-radius:8px;border:1px solid #e5e5e5;font-size:13px;width:220px;min-width:180px;">
                            <option value="none" selected>None</option>
                            <option value="az">A → Z</option>
                            <option value="za">Z → A</option>
                        </select>
                    </div>

                    <div>
                        <button id="applySort" class="btn-view btn-apply" style="padding:8px 12px;">Apply</button>
                    </div>

                    @if($pendingReq > 0)
                        <form id="approveAllFilesForm" method="POST" action="{{ url('/update/approve/status/bulk') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="student_name" value="{{ $value }}">
                            <input type="hidden" name="roomId" value="{{ $roomId }}">
                            <button type="submit" class="btn-approve-all">
                                <i class="fa fa-check-double"></i> Approve All
                            </button>
                        </form>
                    @endif

                    <div class="req-count-badge">
                        <i class="fa fa-file-alt"></i>
                        {{ $totalReq }} {{ $totalReq == 1 ? 'file' : 'files' }}
                    </div>
                </div>
            </div>

            <div class="table-card-body">

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>File</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                        <tr>
                            <!-- Category -->
                            @php
                                $categorySortKey = preg_replace_callback(
                                    '/\d+/',
                                    fn ($match) => str_pad($match[0], 12, '0', STR_PAD_LEFT),
                                    strtolower($file->fileName ?? '')
                                );
                            @endphp
                            <td data-order="{{ $categorySortKey }}">
                                <div class="cat-cell">
                                    <div class="cat-icon-box">
                                        <i class="fa fa-folder"></i>
                                    </div>
                                    <span class="cat-name-text">
                                        {{ $file->fileName }}
                                    </span>
                                </div>
                            </td>

                            <!-- File -->
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-paperclip" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    <span class="file-name-text">{{ $file->file }}</span>
                                </div>
                            </td>

                            <!-- Date -->
                            <td data-order="{{ $file->created_at }}">
                                <div class="date-main">
                                    {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y') }}
                                </div>
                                <div class="date-sub">
                                    {{ \Carbon\Carbon::parse($file->created_at)->format('h:i A') }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td>
                                @if($file->status == 1)
                                    <span class="badge-approved">
                                        <i class="fa fa-check-circle"></i> Approved
                                    </span>
                                @elseif($file->status == 2)
                                    <span class="badge-denied">
                                        <i class="fa fa-times-circle"></i> Denied
                                    </span>
                                    @if(!empty($file->denial_reason))
                                        <div class="denial-reason-note">
                                            <i class="fa fa-comment-alt"></i>
                                            {{ $file->denial_reason }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge-pending">
                                        <i class="fa fa-clock"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">

                                    @if($file->status != 1 && $file->status != 2)
                                        <!-- Approve -->
                                        <form method="POST"
                                              action="/update/approve/status/{{ $file->id }}"
                                              style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-approve">
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <!-- Deny -->
                                        <button type="button"
                                                class="btn-deny open-deny-modal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#denyRequirementModal"
                                                data-action="{{ url('/update/denied/status/' . $file->id) }}"
                                                data-category="{{ $file->fileName }}"
                                                data-file="{{ $file->file }}">
                                            <i class="fa fa-times"></i> Deny
                                        </button>
                                    @endif

                                    <!-- View -->
                                    <a href="/requireview?file={{ $file->fileName }}&value={{ urlencode($value) }}@if(!empty($roomId))&roomId={{ $roomId }}@endif"
                                       class="btn-view">
                                        <i class="fa fa-eye"></i> View
                                    </a>

                                    <!-- Download -->
                                    <a href="{{ url('/download/req', $file->id) }}"
                                       class="btn-download">
                                        <i class="fa fa-download"></i> Download
                                    </a>

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

<!-- =============== DENY REQUIREMENT MODAL =============== -->
<div class="modal fade" id="denyRequirementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-times-circle"></i> Reason to Deny
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="denyRequirementForm" method="POST" action="">
                @csrf

                <div class="modal-body">
                    <div class="deny-file-banner">
                        <div class="deny-file-avatar" id="denyRequirementAvatar">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <div>
                            <div class="deny-file-name" id="denyRequirementCategory"></div>
                            <div class="deny-file-sub" id="denyRequirementFile"></div>
                        </div>
                    </div>

                    <label class="reason-label">
                        <i class="fa fa-comment-alt"></i> Reason for Denial
                    </label>
                    <textarea class="reason-textarea"
                              id="denyRequirementReason"
                              name="reason"
                              rows="4"
                              placeholder="Explain why this requirement document is being denied..."
                              required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-deny">
                        <i class="fa fa-ban me-1"></i> Deny Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/student-require.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>