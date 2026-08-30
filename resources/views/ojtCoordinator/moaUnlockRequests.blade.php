<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA Unlock Requests</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <script>
        (function(){
            try {
                if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e){}
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ vasset('css/coordinator/moa-unlock-requests.css') }}?v={{ time() }}">
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">
    <a href="{{ url('/dashboard') }}" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="PUP Logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJT IMS</span>
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

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-badge">
            <i class="fa fa-user-shield"></i> OJT Coordinator
        </div>
    </div>

    <div class="page-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px; font-size:14px; margin-bottom:20px;">
                <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px; font-size:14px; margin-bottom:20px;">
                <i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>MOA <span>Unlock Requests</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/MOA') }}">MOA</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Unlock Requests</span>
                </div>
            </div>
            <a href="{{ url('/MOA') }}" class="btn-add" style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                <i class="fa fa-arrow-left"></i> Back to MOA Management
            </a>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="header-icon"><i class="fa fa-key"></i></div>
                    <div>
                        <h2 style="font-size:16px; font-weight:700;">Student Unlock Requests Queue</h2>
                        <p style="font-size:12.5px; color:#888; margin-top:2px;">Review student requests to unlock their company MOA selection</p>
                    </div>
                </div>
            </div>

            <div style="padding: 20px;">
                <table id="requestsTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Current Company</th>
                            <th>Request Purpose</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:#1a1a1a;" class="company-name-text">{{ $req->student->full_name ?? 'N/A' }}</div>
                                    <div style="font-size:12px; color:#888;">{{ $req->student->studentNum ?? '' }} &bull; {{ $req->student->course ?? '' }}</div>
                                </td>
                                <td>
                                    @if(!empty($req->company))
                                        <div style="font-weight:600;">{{ $req->company->company_name }}</div>
                                    @elseif(($req->request_type ?? '') === 'switch_external')
                                        <div style="font-weight:600; color:#047857;"><i class="fa fa-university me-1"></i> School In-House OJT</div>
                                    @else
                                        <div style="font-weight:600; color:#888;">N/A</div>
                                    @endif
                                </td>
                                <td>
                                    @if(($req->request_type ?? 'unlink') === 'edit')
                                        <span style="display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-weight:700; padding:4px 10px; border-radius:999px; font-size:11.5px;">
                                            <i class="fa fa-edit"></i> Edit Details/File
                                        </span>
                                    @elseif(($req->request_type ?? 'unlink') === 'switch_external')
                                        <span style="display:inline-flex; align-items:center; gap:5px; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-weight:700; padding:4px 10px; border-radius:999px; font-size:11.5px;">
                                            <i class="fa fa-exchange-alt"></i> Switch to External MOA
                                        </span>
                                    @elseif(($req->request_type ?? 'unlink') === 'switch_inhouse')
                                        <span style="display:inline-flex; align-items:center; gap:5px; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-weight:700; padding:4px 10px; border-radius:999px; font-size:11.5px;">
                                            <i class="fa fa-university"></i> Switch to In-House OJT
                                        </span>
                                    @else
                                        <span style="display:inline-flex; align-items:center; gap:5px; background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; font-weight:700; padding:4px 10px; border-radius:999px; font-size:11.5px;">
                                            <i class="fa fa-unlink"></i> Remove / Unlink
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" 
                                        class="btn-view-reason"
                                        data-student-name="{{ e($req->student->full_name ?? 'Student') }}"
                                        data-student-info="{{ e(($req->student->studentNum ?? '') . ' &bull; ' . ($req->student->course ?? '')) }}"
                                        data-company-name="{{ e($req->company->company_name ?? 'N/A') }}"
                                        data-reason="{{ e($req->reason) }}"
                                        data-date="{{ $req->created_at ? $req->created_at->format('M d, Y g:i A') : '' }}"
                                        onclick="openViewReasonModal(this)">
                                        <i class="fa fa-eye"></i> View Reason
                                    </button>
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge-pending"><i class="fa fa-clock me-1"></i> Pending</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge-approved"><i class="fa fa-check-circle me-1"></i> Approved</span>
                                    @else
                                        <span class="badge-denied"><i class="fa fa-times-circle me-1"></i> Denied</span>
                                    @endif
                                </td>
                                <td style="font-size:12.5px; color:#888;">
                                    {{ $req->created_at ? $req->created_at->format('M d, Y g:i A') : '-' }}
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                        <div style="display:flex; gap:8px;">
                                            <form action="{{ route('coordinator.moa.approveUnlock', $req->id) }}" method="POST" onsubmit="return confirmApproveUnlock(this, '{{ addslashes($req->student->full_name ?? 'this student') }}')">
                                                @csrf
                                                <button type="submit" class="btn-approve"><i class="fa fa-check me-1"></i> Approve</button>
                                            </form>
                                            <form action="{{ route('coordinator.moa.denyUnlock', $req->id) }}" method="POST" onsubmit="return confirmDenyUnlock(this, '{{ addslashes($req->student->full_name ?? 'this student') }}')">
                                                @csrf
                                                <button type="submit" class="btn-deny"><i class="fa fa-times me-1"></i> Deny</button>
                                            </form>
                                        </div>
                                    @else
                                        <span style="font-size:12px; color:#888;">Processed by {{ $req->processed_by ?? 'Admin' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" style="width:22px; height:22px; object-fit:contain; opacity:0.6;" alt="PUP">
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

<!-- =============== VIEW REASON MODAL =============== -->
<div class="modal fade" id="viewReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 18px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%); padding: 20px 24px; border: none;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; flex-shrink: 0;">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="color: #fff; font-weight: 800; font-size: 17px; margin: 0; letter-spacing: -0.3px;">
                            Unlock Request Details
                        </h5>
                        <p style="font-size: 12.5px; color: rgba(255,255,255,0.8); margin: 2px 0 0;">
                            Submitted reason and student profile information
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Student & Company Card -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;" class="reason-modal-info-card">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 46px; height: 46px; border-radius: 50%; background: #fee2e2; border: 1.5px solid #fecaca; display: flex; align-items: center; justify-content: center; color: var(--red); font-weight: 800; font-size: 18px; flex-shrink: 0;" id="modalReasonAvatar">
                                S
                            </div>
                            <div>
                                <div id="modalReasonStudentName" style="font-size: 16px; font-weight: 800; color: #0f172a;"></div>
                                <div id="modalReasonStudentInfo" style="font-size: 12.5px; color: #64748b; margin-top: 2px;"></div>
                            </div>
                        </div>
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 16px; text-align: right;" class="reason-modal-company-box">
                            <div style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Current Company</div>
                            <div id="modalReasonCompanyName" style="font-size: 14px; font-weight: 800; color: #1e293b; margin-top: 2px;"></div>
                        </div>
                    </div>

                    <!-- Stated Reason Section -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <label style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin: 0; display: flex; align-items: center; gap: 6px;">
                                <i class="fa fa-quote-left" style="color: var(--red);"></i> Reason Provided by Student:
                            </label>
                        </div>
                        <div id="modalReasonContent" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px; font-size: 14px; color: #1e293b; line-height: 1.65; white-space: pre-wrap; word-break: break-word; min-height: 100px; max-height: 320px; overflow-y: auto; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" class="reason-modal-text-box"></div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px; font-size: 12.5px; color: #94a3b8;">
                        <i class="fa fa-clock"></i> <span id="modalReasonDate"></span>
                    </div>

                </div>
            </div>
            <div class="modal-footer" style="background: #fafafa; border-top: 1px solid #f1f5f9; padding: 16px 24px;">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ vasset('js/coordinator/moa-unlock-requests.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
</body>
</html>
