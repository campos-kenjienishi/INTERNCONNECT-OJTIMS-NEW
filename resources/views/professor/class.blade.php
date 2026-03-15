<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Professor Class</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

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

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
        }

        /* =============== SIDEBAR =============== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(160deg, #1a0000 0%, #4a0000 50%, #7f0000 100%);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: width 0.35s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0,0,0,0.18);
        }

        .sidebar.collapsed { width: var(--sidebar-w-collapsed); }

        .sidebar-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 22px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none; flex-shrink: 0;
        }

        .sidebar-brand img {
            width: 36px; height: 36px; object-fit: contain; flex-shrink: 0;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.2));
        }

        .sidebar-brand-text {
            display: flex; flex-direction: column;
            white-space: nowrap; overflow: hidden;
            transition: opacity 0.25s, width 0.25s;
        }

        .sidebar-brand-name { font-size: 16px; font-weight: 800; color: #fff; letter-spacing: -0.3px; line-height: 1; }
        .sidebar-brand-name span { color: #fca5a5; }
        .sidebar-brand-sub { font-size: 9px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 3px; }
        .sidebar.collapsed .sidebar-brand-text { opacity: 0; width: 0; }

        .sidebar-user {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none; flex-shrink: 0; transition: background 0.2s;
        }

        .sidebar-user:hover { background: rgba(255,255,255,0.05); }

        .user-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(239,68,68,0.25);
            border: 1.5px solid rgba(239,68,68,0.4);
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
            display: flex; align-items: center; gap: 14px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.55);
            text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all 0.25s; position: relative;
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
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .menu-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 18px; transition: all 0.2s;
        }

        .menu-toggle:hover { background: #fee2e2; color: var(--red); }
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
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }

        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* Add room button */
        .btn-add-room {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-add-room:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            color: #fff;
        }

        .btn-add-room:active { transform: translateY(0); }

        /* Table card */
        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa; flex-wrap: wrap; gap: 12px;
        }

        .table-card-header-left { display: flex; align-items: center; gap: 12px; }

        .header-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 15px; flex-shrink: 0;
        }

        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }

        .room-count-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fee2e2; color: var(--red);
            border-radius: 20px; padding: 5px 14px;
            font-size: 12.5px; font-weight: 700;
        }

        .table-card-body { padding: 0; }

        /* DataTables overrides */
        .table-card-body .dataTables_wrapper {
            padding: 16px 22px;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
        }

        .table-card-body table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        .table-card-body table.dataTable thead th {
            background: #fafafa; color: #555;
            font-size: 11.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0; border-top: none;
        }

        .table-card-body table.dataTable tbody td {
            padding: 14px; color: #333;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13.5px; vertical-align: middle;
        }

        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }

        .dataTables_filter input {
            border: 1px solid #e5e5e5 !important; border-radius: 8px !important;
            padding: 6px 12px !important; font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important; outline: none !important;
        }

        .dataTables_filter input:focus {
            border-color: var(--red) !important;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important;
        }

        .dataTables_length select {
            border: 1px solid #e5e5e5 !important; border-radius: 8px !important;
            padding: 4px 8px !important; font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px !important; font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important; padding: 4px 10px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--red) !important; border-color: var(--red) !important; color: #fff !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important;
        }

        /* Room name cell */
        .room-cell { display: flex; align-items: center; gap: 10px; }

        .room-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 13px; flex-shrink: 0;
        }

        .room-name-text { font-weight: 600; color: #1a1a1a; }

        /* Action buttons */
        .btn-view-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px;
            background: #fff; border: 1.5px solid #e8e8e8;
            border-radius: 8px; color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: all 0.25s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-view-action:hover {
            border-color: var(--red); color: var(--red); background: #fff5f5;
        }

        .btn-announce {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none; border-radius: 8px; color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: all 0.25s;
            box-shadow: 0 3px 10px rgba(37,99,235,0.2);
            white-space: nowrap;
        }

        .btn-announce:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37,99,235,0.3);
        }

        .btn-remove-room {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px;
            background: #fff; border: 1.5px solid #e8e8e8;
            border-radius: 8px; color: #888;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px; font-weight: 600;
            cursor: pointer; transition: all 0.25s;
            white-space: nowrap;
        }

        .btn-remove-room:hover {
            background: #fee2e2; border-color: #fecaca; color: var(--red);
        }

        /* Actions cell */
        .actions-cell { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        /* =============== MODAL =============== */
        .modal-content {
            border-radius: 16px; border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            font-family: 'Poppins', sans-serif; overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%);
            border-bottom: none; padding: 20px 24px;
        }

        .modal-title {
            color: #fff; font-size: 16px; font-weight: 700;
            display: flex; align-items: center; gap: 10px;
        }

        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }

        .modal-body { padding: 24px; background: #fff; }

        .modal-field-label {
            font-size: 13px; font-weight: 600; color: #444;
            margin-bottom: 7px; display: flex; align-items: center; gap: 6px;
            margin-top: 14px;
        }

        .modal-field-label:first-child { margin-top: 0; }
        .modal-field-label i { color: var(--red); font-size: 12px; }

        .modal-field-input,
        .modal-field-select,
        .modal-field-textarea {
            width: 100%; background: #fafafa;
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; padding: 11px 14px; outline: none;
            transition: all 0.25s; appearance: none;
        }

        .modal-field-input:focus,
        .modal-field-select:focus,
        .modal-field-textarea:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .modal-field-textarea { resize: vertical; min-height: 100px; }

        .modal-footer {
            background: #fafafa; border-top: 1px solid #f0f0f0;
            padding: 16px 24px; display: flex; gap: 10px; justify-content: flex-end;
        }

        .btn-modal-close {
            padding: 9px 20px; background: #f3f4f6;
            border: 1px solid #e5e5e5; border-radius: 8px; color: #555;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .btn-modal-submit {
            padding: 9px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 8px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.25s;
            box-shadow: 0 3px 12px rgba(220,38,38,0.2);
        }

        .btn-modal-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(220,38,38,0.3); }

        .btn-modal-submit-blue {
            padding: 9px 24px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none; border-radius: 8px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.25s;
            box-shadow: 0 3px 12px rgba(37,99,235,0.2);
        }

        .btn-modal-submit-blue:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(37,99,235,0.3); }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: var(--sidebar-w);
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .actions-cell { flex-direction: column; align-items: flex-start; }
        }
        /* Dashboard Footer */
.dashboard-footer {
    background: #fff;
    border-top: 1px solid #f0f0f0;
    color: #888;
    text-align: center;
    padding: 18px 28px;
    font-size: 12.5px;
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}

.dashboard-footer .footer-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.dashboard-footer .footer-logo {
    width: 22px;
    height: 22px;
    object-fit: contain;
    opacity: 0.6;
}

.dashboard-footer .footer-copy {
    font-size: 12.5px;
    color: #aaa;
    font-weight: 500;
}

.dashboard-footer .footer-copy span {
    color: var(--red);
    font-weight: 600;
}

.dashboard-footer .footer-links {
    display: flex;
    align-items: center;
    gap: 6px;
}

.dashboard-footer a {
    color: #888;
    text-decoration: none;
    font-weight: 500;
    font-size: 12.5px;
    transition: color 0.2s;
}

.dashboard-footer a:hover {
    color: var(--red);
    text-decoration: none;
}

.dashboard-footer .divider {
    color: #e5e5e5;
    margin: 0 2px;
}
    </style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">

    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
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
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/login') }}" class="nav-item">
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
            <button class="btn-add-room" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="fa fa-plus"></i> Add New Room
            </button>
        </div>

        <!-- Rooms Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-chalkboard"></i></div>
                    <div>
                        <h2>Class Rooms</h2>
                        <p>Manage your rooms, view students, and post announcements</p>
                    </div>
                </div>
                <div class="room-count-badge">
                    <i class="fa fa-door-open"></i>
                    {{ count($class) }} {{ count($class) == 1 ? 'room' : 'rooms' }}
                </div>
            </div>

            <div class="table-card-body">

                <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#fileTable').DataTable();
                    });
                </script>

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Room Name</th>
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
                                   class="btn-view-action">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            </td>
                            <td>
                                <a href="{{ url('/professor/classList', $room->id) }}"
                                   class="btn-view-action">
                                    <i class="fa fa-users"></i> View
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn-view-action"
                                    data-bs-toggle="modal"
                                    data-bs-target="#templateModal{{ $loop->index }}">
                                    <i class="fa fa-file-upload"></i>
                                    {{ $room->templateFiles->count() > 0 ? 'Manage (' . $room->templateFiles->count() . ')' : 'Upload' }}
                                </button>

                                <div class="modal fade" id="templateModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
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
                                                <div class="modal-body">
                                                    <label class="modal-field-label"><i class="fa fa-tag"></i> Template Name</label>
                                                    <input class="modal-field-input" type="text" name="name" placeholder="Enter template name" required>

                                                    <label class="modal-field-label"><i class="fa fa-paperclip"></i> File</label>
                                                    <input class="modal-field-input" type="file" name="file" accept=".doc,.docx,.pdf" required>

                                                    <div style="margin-top:12px;">
                                                        <strong style="font-size:13px; color:#555;">Uploaded in this room:</strong>
                                                        @if ($room->templateFiles->isEmpty())
                                                            <p style="font-size:12px; color:#888; margin-top:6px;">No templates yet.</p>
                                                        @else
                                                            <ul style="margin-top:8px; padding-left:18px; max-height:120px; overflow:auto;">
                                                                @foreach ($room->templateFiles as $template)
                                                                    <li style="font-size:12px; margin-bottom:6px;">
                                                                        {{ $template->name }}
                                                                        <a href="{{ url('/download', $template->file) }}" style="margin-left:8px;">Download</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
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
                                <button type="button" class="btn-announce"
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
                                <button class="btn-view-action" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $room->id }}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <button class="btn-remove-room" data-id="{{ $room->id }}">
                                    <i class="fa fa-trash"></i> Remove
                                </button>

                                <div class="modal fade" id="editRoomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-edit"></i> Edit Room
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ url('/roomUpdate', $room->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <label class="modal-field-label"><i class="fa fa-chalkboard"></i> Room Name</label>
                                                    <input class="modal-field-input" type="text" name="room" value="{{ $room->room }}" required>

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
                                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                                        <input class="modal-field-input" type="number" name="school_year_start" min="2000" max="2100" value="{{ $room->school_year_start }}" required>
                                                        <input class="modal-field-input" type="number" name="school_year_end" min="2001" max="2101" value="{{ $room->school_year_end }}" required>
                                                    </div>

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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>
    <footer class="dashboard-footer">
    <div class="footer-left">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
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
        <a href="{{ url('/terms') }}">Terms of Use</a>
        <span class="divider">|</span>
        <a href="{{ url('/privacy') }}">Privacy Statement</a>
    </div>
</footer>
</div>

<!-- =============== ADD ROOM MODAL =============== -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-plus-circle"></i> Add New Room
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRoomForm" action="{{ url('/roomCreate') }}" method="post">
                @csrf
                <div class="modal-body">
                    <label class="modal-field-label">
                        <i class="fa fa-chalkboard"></i> Room Name
                    </label>
                    <input class="modal-field-input" type="text" name="room"
                           placeholder="Enter room name" required>

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
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <input class="modal-field-input" type="number" name="school_year_start" min="2000" max="2100" placeholder="Start Year" required>
                        <input class="modal-field-input" type="number" name="school_year_end" min="2001" max="2101" placeholder="End Year" required>
                    </div>

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
                        <i class="fa fa-check me-1"></i> Create Room
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', function () {
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    $(document).ready(function () {

        function buildScheduleMapFromArray(scheduleArray) {
            const map = {};
            if (!Array.isArray(scheduleArray)) return map;
            scheduleArray.forEach(item => {
                if (!item || !item.day) return;
                if (!map[item.day]) map[item.day] = [];
                map[item.day].push({
                    start_time: item.start_time || '',
                    end_time: item.end_time || ''
                });
            });
            return map;
        }

        function buildScheduleMapFromForm(form) {
            const map = {};
            if (!form) return map;

            const timeInputs = form.querySelectorAll('input[type="time"][name]');
            timeInputs.forEach(input => {
                const startMatch = input.name.match(/^(.*)_start_time_(\d+)$/);
                const endMatch = input.name.match(/^(.*)_end_time_(\d+)$/);

                if (!startMatch && !endMatch) return;

                const day = (startMatch || endMatch)[1];
                const slotIndex = parseInt((startMatch || endMatch)[2], 10) - 1;
                if (!map[day]) map[day] = [];
                if (!map[day][slotIndex]) {
                    map[day][slotIndex] = { start_time: '', end_time: '' };
                }

                if (startMatch) {
                    map[day][slotIndex].start_time = input.value || '';
                }
                if (endMatch) {
                    map[day][slotIndex].end_time = input.value || '';
                }
            });

            return map;
        }

        function renderScheduleInputs(daySelector, slotSelectId, containerId, fallbackScheduleMap) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const slotSelect = document.getElementById(slotSelectId);
            const slots = parseInt((slotSelect && slotSelect.value) ? slotSelect.value : '1', 10);
            const selectedDays = Array.from(document.querySelectorAll(daySelector)).map(el => el.value);

            const form = container.closest('form');
            const currentMap = buildScheduleMapFromForm(form);
            const scheduleMap = Object.keys(currentMap).length > 0 ? currentMap : (fallbackScheduleMap || {});

            if (selectedDays.length === 0) {
                container.innerHTML = '<p style="font-size:12px;color:#888;margin:0;">Select at least one day to set time slots.</p>';
                return;
            }

            let html = '';
            selectedDays.forEach(day => {
                html += '<div style="border:1px solid #eee;border-radius:10px;padding:10px;margin-bottom:10px;background:#fafafa;">';
                html += '<strong style="font-size:13px;color:#444;">' + day + '</strong>';

                for (let i = 1; i <= slots; i++) {
                    const startName = day + '_start_time_' + i;
                    const endName = day + '_end_time_' + i;
                    const existing = scheduleMap[day] && scheduleMap[day][i - 1] ? scheduleMap[day][i - 1] : { start_time: '', end_time: '' };

                    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">';
                    html += '<input class="modal-field-input" type="time" name="' + startName + '" value="' + (existing.start_time || '') + '" required>';
                    html += '<input class="modal-field-input" type="time" name="' + endName + '" value="' + (existing.end_time || '') + '" required>';
                    html += '</div>';
                }

                html += '</div>';
            });

            container.innerHTML = html;
        }

        function renderAddRoomScheduleInputs() {
            renderScheduleInputs('.add-schedule-day:checked', 'add_time_slots', 'addRoomScheduleInputs', {});
        }

        function renderEditRoomScheduleInputs(roomId) {
            const container = document.getElementById('editRoomScheduleInputs' + roomId);
            if (!container) return;

            let initialSchedule = [];
            try {
                initialSchedule = JSON.parse(container.getAttribute('data-initial-schedule') || '[]');
            } catch (e) {
                initialSchedule = [];
            }

            renderScheduleInputs(
                '.edit-schedule-day[data-room-id="' + roomId + '"]:checked',
                'edit_time_slots_' + roomId,
                'editRoomScheduleInputs' + roomId,
                buildScheduleMapFromArray(initialSchedule)
            );
        }

        $(document).on('change', '.add-schedule-day, #add_time_slots', renderAddRoomScheduleInputs);
        $(document).on('change', '.edit-schedule-day, .edit-time-slots', function () {
            const roomId = $(this).data('room-id');
            if (roomId) {
                renderEditRoomScheduleInputs(roomId);
            }
        });

        $('[id^="editRoomModal"]').on('shown.bs.modal', function () {
            const roomId = this.id.replace('editRoomModal', '');
            renderEditRoomScheduleInputs(roomId);
        });

        renderAddRoomScheduleInputs();

        // Add Room AJAX
        $('#addRoomForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function () {
                    $('#addRoomModal').modal('hide');
                    form[0].reset();
                    Swal.fire({
                        toast: true, icon: 'success',
                        title: 'Room created successfully!',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000, timerProgressBar: true
                    });
                    setTimeout(() => location.reload(), 2000);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Oops!', 'Error creating room.', 'error');
                }
            });
        });

        // Announcement AJAX
        $('.announcementForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var modal = form.closest('.modal');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function () {
                    modal.modal('hide');
                    form[0].reset();
                    Swal.fire({
                        toast: true, icon: 'success',
                        title: 'Announcement posted successfully!',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000, timerProgressBar: true
                    });
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Oops!', 'Error posting announcement.', 'error');
                }
            });
        });

        // Delete Room
        $('.btn-remove-room').on('click', function () {
            let roomId = $(this).data('id');
            Swal.fire({
                title: 'Remove this room?',
                text: 'This will permanently delete the room and all its records.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/roomDelete/' + roomId,
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            Swal.fire({
                                toast: true, icon: 'success',
                                title: 'Room deleted successfully!',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => location.reload(), 2000);
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Oops!', 'Error deleting room.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>

</body>
</html>