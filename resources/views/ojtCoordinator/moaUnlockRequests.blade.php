<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA Unlock Requests</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red:        #dc2626;
            --red-dark:   #991b1b;
            --red-deeper: #7f0000;
            --sidebar-w:  260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h:   64px;
        }

        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #1a1a1a; min-height: 100vh; }

        body.dark-mode { background: #000000; color: #e0e0e0; }
        body.dark-mode .main-content { background: #000000; }
        body.dark-mode .sidebar { box-shadow: 4px 0 24px rgba(0,0,0,0.4); }

        /* =============== SIDEBAR =============== */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(160deg, #1a0000 0%, #4a0000 50%, #7f0000 100%);
            display: flex; flex-direction: column; z-index: 1000;
            transition: width 0.35s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden; box-shadow: 4px 0 24px rgba(0,0,0,0.18);
        }
        .sidebar.collapsed { width: var(--sidebar-w-collapsed); }
        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 22px 18px; border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none; flex-shrink: 0;
        }
        .sidebar-brand img { width: 36px; height: 36px; object-fit: contain; flex-shrink: 0; filter: drop-shadow(0 0 8px rgba(255,255,255,0.2)); }
        .sidebar-brand-text { display: flex; flex-direction: column; white-space: nowrap; overflow: hidden; transition: opacity 0.25s, width 0.25s; }
        .sidebar-brand-name { font-size: 16px; font-weight: 800; color: #fff; letter-spacing: -0.3px; line-height: 1; }
        .sidebar-brand-name span { color: #fca5a5; }
        .sidebar-brand-sub { font-size: 9px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px; }
        .sidebar.collapsed .sidebar-brand-text { opacity: 0; width: 0; }
        .sidebar-user {
            display: flex; align-items: center; gap: 12px; padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none; flex-shrink: 0; transition: background 0.2s;
        }
        .sidebar-user:hover { background: rgba(255,255,255,0.05); }
        .user-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(239,68,68,0.25); border: 1.5px solid rgba(239,68,68,0.4);
            display: flex; align-items: center; justify-content: center;
            color: #fca5a5; font-size: 16px; flex-shrink: 0; overflow: hidden;
        }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-info { overflow: hidden; white-space: nowrap; transition: opacity 0.25s, width 0.25s; }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; display: block; text-overflow: ellipsis; overflow: hidden; }
        .user-role { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; }
        .sidebar.collapsed .user-info { opacity: 0; width: 0; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(239,68,68,0.3); border-radius: 10px; }
        .nav-item {
            display: flex; align-items: center; gap: 14px; padding: 12px 20px;
            color: rgba(255,255,255,0.55); text-decoration: none; font-size: 14px;
            font-weight: 500; transition: all 0.25s; position: relative;
            white-space: nowrap; border-left: 3px solid transparent;
        }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(239,68,68,0.15); border-left-color: #ef4444; }
        .nav-item .nav-icon { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }
        .nav-item .nav-label { transition: opacity 0.25s; overflow: hidden; }
        .sidebar.collapsed .nav-label { opacity: 0; width: 0; }
        .nav-item .tooltip-label {
            position: absolute; left: calc(var(--sidebar-w-collapsed) + 8px);
            background: #1a0000; color: #fff; font-size: 12px;
            padding: 5px 10px; border-radius: 6px; white-space: nowrap;
            pointer-events: none; opacity: 0; transition: opacity 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999;
        }
        .sidebar.collapsed .nav-item:hover .tooltip-label { opacity: 1; }
        .sidebar-footer { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; }

        /* =============== MAIN =============== */
        .main-content {
            margin-left: var(--sidebar-w);
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
            min-height: 100vh; display: flex; flex-direction: column;
        }
        .main-content.expanded { margin-left: var(--sidebar-w-collapsed); }
        .topbar {
            height: var(--topbar-h); background: #fff;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .menu-toggle, .darkmode-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 18px; transition: all 0.2s;
        }
        .menu-toggle:hover, .darkmode-toggle:hover { background: #fee2e2; color: var(--red); }
        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }
        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red-dark);
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #888; margin-top: 6px;
        }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        .btn-add {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
            text-decoration: none;
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220,38,38,0.35); color: #fff; }

        /* Table Card */
        .table-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04); overflow: hidden;
        }
        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 24px; border-bottom: 1px solid #f0f0f0; background: #fafafa;
        }
        .header-icon {
            width: 38px; height: 38px; border-radius: 10px; background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
            color: var(--red); font-size: 15px; flex-shrink: 0;
        }

        .badge-pending { background: #fef3c7; color: #92400e; font-weight: 700; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }
        .badge-approved { background: #dcfce7; color: #166534; font-weight: 700; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }
        .badge-denied { background: #fee2e2; color: #991b1b; font-weight: 700; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }

        .btn-approve { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #fff; border: none; border-radius: 8px; padding: 7px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-approve:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(22,163,74,0.3); }
        .btn-deny { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #fff; border: none; border-radius: 8px; padding: 7px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-deny:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); }

        .btn-view-reason {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; background: #eff6ff; border: 1px solid #bfdbfe;
            border-radius: 8px; color: #2563eb; font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-view-reason:hover { background: #dbeafe; color: #1d4ed8; transform: translateY(-1px); }

        .btn-modal-close {
            padding: 9px 22px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            color: #475569;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: translateY(-1px);
        }

        /* Dark Mode Overrides */
        body.dark-mode .topbar { background: #252525 !important; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .menu-toggle, body.dark-mode .darkmode-toggle { background: #3a3a3a; color: #e0e0e0; }
        body.dark-mode .table-card { background: #2a2a2a; border: 1px solid #3a3a3a; }
        body.dark-mode .table-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .table-card-header h2 { color: #fff; }
        body.dark-mode table.dataTable thead th { background: #2a2a2a; color: #aaa; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode table.dataTable tbody td { color: #e0e0e0; border-bottom: 1px solid rgba(255,255,255,0.05); }

        body.dark-mode .modal-content { background: #1e293b !important; border: 1px solid #334155 !important; }
        body.dark-mode .modal-body { background: #0f172a !important; }
        body.dark-mode .modal-footer { background: #1e293b !important; border-top: 1px solid #334155 !important; }
        body.dark-mode .reason-modal-info-card { background: #1e293b !important; border-color: #334155 !important; }
        body.dark-mode .reason-modal-company-box { background: #0f172a !important; border-color: #334155 !important; }
        body.dark-mode #modalReasonStudentName { color: #f8fafc !important; }
        body.dark-mode #modalReasonStudentInfo { color: #94a3b8 !important; }
        body.dark-mode #modalReasonCompanyName { color: #f8fafc !important; }
        body.dark-mode .reason-modal-text-box { background: #1e293b !important; border-color: #334155 !important; color: #f1f5f9 !important; }
        body.dark-mode .btn-modal-close { background: #334155 !important; border-color: #475569 !important; color: #e2e8f0 !important; }
        body.dark-mode .btn-modal-close:hover { background: #475569 !important; color: #fff !important; }

        /* Footer */
        .dashboard-footer {
            background: #fff; border-top: 1px solid #f0f0f0;
            color: #888; padding: 18px 28px; font-size: 12.5px;
            margin-top: auto; display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }
        body.dark-mode .dashboard-footer { background: #1a1a1a; border-top: 1px solid #3a3a3a; color: #999; }
        body.dark-mode .dashboard-footer a { color: #999; }
    </style>
</head>
<body>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">
    <a href="{{ url('/dashboard') }}" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP Logo">
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
        <a href="{{ url('/reports') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
            <span class="nav-label">Reports</span>
            <span class="tooltip-label">Reports</span>
        </a>
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
                                    <div style="font-size:12px; color:#888;">{{ $req->student->studentNum ?? '' }} • {{ $req->student->course ?? '' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:600;">{{ $req->company->company_name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    @if(($req->request_type ?? 'unlink') === 'edit')
                                        <span style="display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-weight:700; padding:4px 10px; border-radius:999px; font-size:11.5px;">
                                            <i class="fa fa-edit"></i> Edit Details/File
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
                                        data-student-info="{{ e(($req->student->studentNum ?? '') . ' • ' . ($req->student->course ?? '')) }}"
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
            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:22px; height:22px; object-fit:contain; opacity:0.6;" alt="PUP">
            <span class="footer-copy">
                © 1998–2026 <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div style="display:flex; align-items:center; gap:6px; font-size:12.5px;">
            <a href="https://www.pup.edu.ph/" target="_blank" style="color:#888; text-decoration:none; font-weight:500;">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                PUP Website
            </a>
            <span style="color:#e5e5e5; margin:0 2px;">|</span>
            <a href="{{ url('/terms') }}" style="color:#888; text-decoration:none; font-weight:500;">Terms of Use</a>
            <span style="color:#e5e5e5; margin:0 2px;">|</span>
            <a href="{{ url('/privacy') }}" style="color:#888; text-decoration:none; font-weight:500;">Privacy Statement</a>
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
<script>
    function openViewReasonModal(button) {
        const studentName = button.getAttribute('data-student-name') || 'Student';
        const studentInfo = button.getAttribute('data-student-info') || '';
        const companyName = button.getAttribute('data-company-name') || 'N/A';
        const reason = button.getAttribute('data-reason') || 'No reason provided.';
        const date = button.getAttribute('data-date') || '';

        document.getElementById('modalReasonAvatar').innerText = studentName.charAt(0).toUpperCase();
        document.getElementById('modalReasonStudentName').innerText = studentName;
        document.getElementById('modalReasonStudentInfo').innerText = studentInfo;
        document.getElementById('modalReasonCompanyName').innerText = companyName;
        document.getElementById('modalReasonContent').innerText = reason;
        document.getElementById('modalReasonDate').innerText = date ? ('Submitted on: ' + date) : '';

        const modal = new bootstrap.Modal(document.getElementById('viewReasonModal'));
        modal.show();
    }

    function confirmApproveUnlock(form, studentName) {
        if (form.dataset.confirmed === 'true') {
            return true;
        }

        Swal.fire({
            title: 'Approve Unlock Request?',
            html: `Are you sure you want to approve <strong>${studentName}</strong>'s unlock request?<br><br><span style="font-size:13px; color:#64748b;">The student will be unlinked from their company and allowed to select a new MOA.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-check me-1"></i> Yes, Approve',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });

        return false;
    }

    function confirmDenyUnlock(form, studentName) {
        if (form.dataset.confirmed === 'true') {
            return true;
        }

        Swal.fire({
            title: 'Deny Unlock Request?',
            html: `Are you sure you want to deny <strong>${studentName}</strong>'s unlock request?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-times me-1"></i> Yes, Deny',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });

        return false;
    }

    $(document).ready(function() {
        $('#requestsTable').DataTable({
            scrollX: true,
            order: [[3, 'asc'], [4, 'desc']]
        });

        // Sidebar toggle logic
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');

        if (menuToggle && sidebar && mainContent) {
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
    });
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
</body>
</html>
