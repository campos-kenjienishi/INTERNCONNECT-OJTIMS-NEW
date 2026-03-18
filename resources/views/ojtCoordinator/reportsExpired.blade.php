<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Expired MOA Report</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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
        .nav-sub { padding: 4px 0 4px 52px; border-left: 3px solid transparent; }
        .nav-sub-item {
            display: block; padding: 7px 16px;
            color: rgba(255,255,255,0.45); text-decoration: none;
            font-size: 12.5px; font-weight: 500;
            border-left: 2px solid transparent;
            transition: all 0.2s; border-radius: 0 6px 6px 0; white-space: nowrap;
        }
        .nav-sub-item:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .nav-sub-item.active { color: #fca5a5; border-left-color: #fca5a5; background: rgba(239,68,68,0.08); }
        .sidebar.collapsed .nav-sub { display: none; }
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
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* =============== STATS =============== */
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border-radius: 14px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }
        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* =============== FILTER CARD =============== */
        .panel-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 22px; }
        .panel-card-header { display: flex; align-items: center; gap: 12px; padding: 16px 22px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        .panel-header-icon { width: 36px; height: 36px; border-radius: 9px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 14px; flex-shrink: 0; }
        .panel-card-header h2 { font-size: 15px; font-weight: 700; color: #1a1a1a; }
        .panel-card-header p  { font-size: 12px; color: #888; margin-top: 2px; }
        .panel-card-body { padding: 22px; }
        .filter-grid { display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: end; }
        .field-group { display: flex; flex-direction: column; gap: 5px; }
        .field-label { font-size: 12px; font-weight: 600; color: #444; display: flex; align-items: center; gap: 5px; }
        .field-label i { color: var(--red); font-size: 11px; }
        .field-input, .field-select { width: 100%; background: #fafafa; border: 1.5px solid #e8e8e8; border-radius: 10px; color: #1a1a1a; font-family: 'Poppins', sans-serif; font-size: 13px; padding: 10px 13px; outline: none; transition: all 0.25s; }
        .field-input:focus, .field-select:focus { border-color: var(--red); background: #fff; box-shadow: 0 0 0 3px rgba(220,38,38,0.07); }
        .year-row { display: flex; align-items: center; gap: 8px; }
        .year-row span { font-weight: 700; color: #aaa; flex-shrink: 0; font-size: 14px; }
        .year-row .field-select { flex: 1; }
        .btn-generate { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); border: none; border-radius: 10px; color: #fff; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 16px rgba(220,38,38,0.25); white-space: nowrap; }
        .btn-generate:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220,38,38,0.35); }
        .btn-preview { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; background: #fff; border: 1.5px solid #e0e7ff; color: #4f46e5; font-family: 'Poppins', sans-serif; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-preview:hover { background: #e0e7ff; }

        /* =============== TABLE CARD =============== */
        .table-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
        .table-card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 24px; border-bottom: 1px solid #f0f0f0; background: #fafafa; flex-wrap: wrap; }
        .table-card-header-left { display: flex; align-items: center; gap: 12px; }
        .header-icon { width: 38px; height: 38px; border-radius: 10px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 15px; flex-shrink: 0; }
        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }
        .count-badge { display: inline-flex; align-items: center; gap: 6px; background: #fee2e2; color: var(--red); border-radius: 20px; padding: 5px 14px; font-size: 12.5px; font-weight: 700; }

        /* DataTables */
        .table-card-body .dataTables_wrapper { padding: 16px 22px; font-family: 'Poppins', sans-serif; font-size: 13px; }
        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }
        .table-card-body table.dataTable thead th { background: #fafafa; color: #555; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 1px solid #f0f0f0; border-top: none; white-space: nowrap; }
        .table-card-body table.dataTable tbody td { padding: 12px 12px; color: #333; border-bottom: 1px solid #f9f9f9; font-size: 13px; vertical-align: middle; }
        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }
        .dataTables_filter input { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 6px 12px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; outline: none !important; }
        .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important; }
        .dataTables_length select { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 4px 8px !important; font-family: 'Poppins', sans-serif !important; }
        .dataTables_paginate .paginate_button { border-radius: 6px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; }
        .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        .dataTables_paginate .paginate_button:hover { background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important; }

        /* Cell styles */
        .company-cell { display: flex; align-items: center; gap: 9px; }
        .company-avatar { width: 32px; height: 32px; border-radius: 9px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 13px; flex-shrink: 0; }
        .company-name-text { font-weight: 600; color: #1a1a1a; font-size: 13px; }
        .expired-badge { display: inline-flex; align-items: center; gap: 5px; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
        .school-year-badge { display: inline-flex; align-items: center; gap: 5px; background: #fef9c3; color: #ca8a04; border-radius: 20px; padding: 3px 10px; font-size: 11.5px; font-weight: 600; }

        /* =============== PRINT PREVIEW MODAL =============== */
        .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); font-family: 'Poppins', sans-serif; overflow: hidden; }
        /* Slim header — no title, just close button */
        .modal-header { background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%); border-bottom: none; padding: 10px 16px; display: flex; justify-content: flex-end; }
        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }
        .modal-body { padding: 24px; background: #f0f0f0; max-height: 70vh; overflow-y: auto; }
        .modal-footer { background: #fafafa; border-top: 1px solid #f0f0f0; padding: 14px 24px; display: flex; justify-content: flex-end; gap: 10px; }
        .btn-modal-close { padding: 9px 20px; background: #f3f4f6; border: 1px solid #e5e5e5; border-radius: 8px; color: #555; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }
        .btn-modal-print { padding: 9px 24px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); border: none; border-radius: 8px; color: #fff; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.25s; box-shadow: 0 3px 10px rgba(220,38,38,0.2); }
        .btn-modal-print:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(220,38,38,0.3); }

        /* Footer */
        .dashboard-footer { background: #fff; border-top: 1px solid #f0f0f0; color: #888; padding: 18px 28px; font-size: 12.5px; margin-top: auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .dashboard-footer .footer-left { display: flex; align-items: center; gap: 8px; }
        .dashboard-footer .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .dashboard-footer .footer-copy { font-size: 12.5px; color: #aaa; font-weight: 500; }
        .dashboard-footer .footer-copy span { color: var(--red); font-weight: 600; }
        .dashboard-footer .footer-links { display: flex; align-items: center; gap: 6px; }
        .dashboard-footer a { color: #888; text-decoration: none; font-weight: 500; font-size: 12.5px; transition: color 0.2s; }
        .dashboard-footer a:hover { color: var(--red); }
        .dashboard-footer .divider { color: #e5e5e5; margin: 0 2px; }

        /* Mobile overlay */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }

        @media (max-width: 900px) {
            .sidebar { width: var(--sidebar-w); transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .filter-grid { grid-template-columns: 1fr; }
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

    <a href="{{ url('/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($user->profile_photo) && $user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
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

        <div class="nav-item" style="cursor:default; pointer-events:none;">
            <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
            <span class="nav-label">Reports</span>
        </div>
        <div class="nav-sub">
            <a href="{{ url('/reports') }}" class="nav-sub-item">
                <i class="fa fa-user-graduate" style="margin-right:6px; font-size:11px;"></i> Student OJT Info
            </a>
            <a href="{{ url('/reportsExpired') }}" class="nav-sub-item active">
                <i class="fa fa-calendar-times" style="margin-right:6px; font-size:11px;"></i> Expired MOA
            </a>
        </div>

        <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
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

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-badge">
            <i class="fa fa-user-shield"></i> OJT Coordinator
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <div>
                <h1>Expired <span>MOA Report</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/reports') }}">Reports</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Expired MOA</span>
                </div>
            </div>
        </div>

        <!-- Stats -->
        @php $totalExpired = count($companies); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-calendar-times"></i></div>
                <div>
                    <div class="stat-num">{{ $totalExpired }}</div>
                    <div class="stat-name">Expired MOAs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalExpired }}</div>
                    <div class="stat-name">Companies Affected</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">MOA</div>
                    <div class="stat-name">Report Type</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-calendar-alt"></i></div>
                <div>
                    <div class="stat-num">{{ now()->format('Y') }}</div>
                    <div class="stat-name">Current Year</div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="panel-card">
            <div class="panel-card-header">
                <div class="panel-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h2>Generate Report</h2>
                    <p>Filter expired MOAs by school year and course</p>
                </div>
            </div>
            <div class="panel-card-body">
                <form action="{{ route('reports.generate') }}" method="post">
                    @csrf
                    <div class="filter-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                            <div class="year-row">
                                <select class="field-select" name="school_year_start" id="school_year_start" required>
                                    <option value="">Start Year</option>
                                    @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                <span>–</span>
                                <select class="field-select" name="school_year_end" id="school_year_end" required>
                                    <option value="">End Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-graduation-cap"></i> Course</label>
                            <select class="field-select" id="course" name="course" required>
                                @foreach ($course as $c)
                                <option value="{{ $c->course }}">{{ $c->course }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label" style="opacity:0;">Action</label>
                            <button type="submit" class="btn-generate">
                                <i class="fa fa-file-alt"></i> Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-calendar-times"></i></div>
                    <div>
                        <h2>Expired Memorandum of Agreement</h2>
                        <p>Companies with expired MOA agreements</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div class="count-badge">
                        <i class="fa fa-building"></i>
                        {{ $totalExpired }} {{ $totalExpired == 1 ? 'company' : 'companies' }}
                    </div>
                    <button type="button" class="btn-preview" id="openPreviewBtn">
                        <i class="fa fa-print"></i> Print Preview
                    </button>
                </div>
            </div>

            <div class="table-card-body">
                <table id="companyTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="display:none;">ID</th>
                            <th>Company Name</th>
                            <th>Company Address</th>
                            <th>Representative</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>School Year</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        <tr>
                            <td style="display:none;">{{ $company->id }}</td>
                            <td>
                                <div class="company-cell">
                                    <div class="company-avatar"><i class="fa fa-building"></i></div>
                                    <span class="company-name-text">{{ $company->company_name }}</span>
                                </div>
                            </td>
                            <td style="max-width:160px; word-break:break-word; font-size:12.5px; color:#555;">
                                <i class="fa fa-map-marker-alt" style="color:var(--red); font-size:10px; margin-right:4px;"></i>
                                {{ $company->company_address }}
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <i class="fa fa-user-tie" style="color:var(--red); font-size:11px;"></i>
                                    <span style="font-weight:600;">{{ $company->company_rep }}</span>
                                </div>
                            </td>
                            <td style="white-space:nowrap;">
                                <i class="fa fa-phone" style="color:var(--red); font-size:10px; margin-right:4px;"></i>
                                {{ $company->companyNo ?: '—' }}
                            </td>
                            <td style="font-size:12.5px;">
                                <i class="fa fa-envelope" style="color:var(--red); font-size:10px; margin-right:4px;"></i>
                                {{ $company->company_email ?: '—' }}
                            </td>
                            <td>
                                <span class="school-year-badge">
                                    <i class="fa fa-calendar-alt" style="font-size:10px;"></i>
                                    {{ $company->school_year }}
                                </span>
                            </td>
                            <td>
                                <span class="expired-badge">
                                    <i class="fa fa-times-circle" style="font-size:10px;"></i> Expired
                                </span>
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
            <span class="footer-copy">© 1998–2026 <span>Polytechnic University of the Philippines</span></span>
        </div>
        <div class="footer-links">
            <a href="https://www.pup.edu.ph/" target="_blank"><i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i> PUP Website</a>
            <span class="divider">|</span>
            <a href="{{ url('/terms') }}">Terms of Use</a>
            <span class="divider">|</span>
            <a href="{{ url('/privacy') }}">Privacy Statement</a>
        </div>
    </footer>
</div>

<!-- =============== PRINT PREVIEW MODAL =============== -->
<div id="printPreviewModal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="visibility:hidden; font-size:0;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="printPreviewContent" style="background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,0.12); overflow:hidden;">
                    <!-- Injected by JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                    <i class="fa fa-times" style="margin-right:5px;"></i> Close
                </button>
                <button type="button" id="doPrintBtn" class="btn-modal-print">
                    <i class="fa fa-print" style="margin-right:5px;"></i> Print / Save as PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden print iframe -->
<iframe id="printFrame" style="display:none; position:absolute; left:-9999px; top:-9999px;"></iframe>

<!-- Hidden email form (preserved) -->
<form id="sendEmailForm" action="{{ url('/reportsExpired/send-email') }}" method="post" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="hidden" id="courseInput" name="course" value="{{ $course }}">
    <input type="hidden" id="emailInput"  name="email"  value="{{ $user->email }}">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    /* ── Sidebar toggle ── */
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

    /* ── DataTable ── */
    $(document).ready(function () {
        $('#companyTable').DataTable({
            order: [[0, 'desc']],
            columnDefs: [{ targets: 0, visible: false }]
        });
    });

    /* ── Dynamic end year ── */
    document.addEventListener('DOMContentLoaded', function () {
        const startSel = document.getElementById('school_year_start');
        const endSel   = document.getElementById('school_year_end');

        function updateEndYears() {
            const sy = parseInt(startSel.value);
            endSel.innerHTML = '<option value="">End Year</option>';
            if (!isNaN(sy)) {
                for (let y = sy + 1; y <= sy + 10; y++) {
                    const opt = document.createElement('option');
                    opt.value = y; opt.textContent = y;
                    endSel.appendChild(opt);
                }
            }
        }
        updateEndYears();
        startSel.addEventListener('change', updateEndYears);
    });

    /* ══════════════════════════════════════════════
       BUILD PRINT HTML — branded Expired MOA layout
       • Matches Student OJT Info print style exactly
       • Reads CURRENT DataTable page rows only
       • Icon-safe get() strips Font Awesome glyphs
       • @page margin:0 removes browser date/URL
    ══════════════════════════════════════════════ */
    function buildPrintHTML() {
        const now      = new Date();
        const dateStr  = now.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
        const timeStr  = now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' });

        const dt               = $('#companyTable').DataTable();
        const currentPageNodes = dt.rows({ page: 'current' }).nodes();
        const total            = currentPageNodes.length;
        const pageInfo         = dt.page.info();
        const pageNum          = pageInfo.page + 1;
        const pageCount        = pageInfo.pages;

        const syStart = document.getElementById('school_year_start').value || '—';
        const syEnd   = document.getElementById('school_year_end').value   || '—';
        const course  = document.getElementById('course').value            || '—';
        const syLabel = (syStart !== '—' && syEnd !== '—') ? syStart + ' – ' + syEnd : '—';

        /* ── Icon-safe text extractor ──
           Skips <i> Font Awesome nodes so glyphs don't
           bleed into the printed cell content.         */
        const getClean = (td) => {
            if (!td) return '';
            let text = '';
            td.childNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    text += node.textContent;
                } else if (node.nodeName !== 'I') {
                    node.childNodes.forEach(inner => {
                        if (inner.nodeType === Node.TEXT_NODE) text += inner.textContent;
                        else if (inner.nodeName !== 'I') text += inner.textContent;
                    });
                }
            });
            return text.trim();
        };

        /* Column map (td[0]=id hidden, td[1..7] = visible columns):
           td[0] id  td[1] Company  td[2] Address  td[3] Rep
           td[4] Contact  td[5] Email  td[6] School Year  td[7] Status */
        let rowsHTML = '';
        for (let i = 0; i < currentPageNodes.length; i++) {
            const tds    = currentPageNodes[i].querySelectorAll('td');
            const rowNum = pageInfo.start + i + 1;
            const rowBg  = i % 2 === 0 ? '#ffffff' : '#f9fafb';

            const company   = tds[1] ? (tds[1].querySelector('.company-name-text')?.textContent.trim() || getClean(tds[1])) : '';
            const address   = getClean(tds[2]);
            const rep       = getClean(tds[3]);
            const contact   = getClean(tds[4]);
            const email     = getClean(tds[5]);
            const schoolYr  = getClean(tds[6]);

            rowsHTML += `
            <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                <td style="padding:7px 6px; font-size:9px; font-weight:700; color:#6b7280; text-align:center; vertical-align:top; border-right:1px solid #e5e7eb;">${rowNum}</td>
                <td style="padding:7px 6px; font-size:9.5px; font-weight:700; color:#111827; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${company}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#4b5563; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${address}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; font-weight:600; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${rep}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; white-space:nowrap;">${contact}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${email}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#ca8a04; font-weight:600; vertical-align:top; border-right:1px solid #e5e7eb; white-space:nowrap;">${schoolYr}</td>
                <td style="padding:7px 6px; vertical-align:top; text-align:center;">
                    <span style="display:inline-block; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:4px; padding:1px 7px; font-size:8px; font-weight:700;">Expired</span>
                </td>
            </tr>`;
        }

        return `
        <div style="font-family:'Poppins',Arial,sans-serif; background:#fff;">

            <!-- HEADER -->
            <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); padding:0;">
                <div style="background:rgba(255,255,255,0.12); height:4px;"></div>
                <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                    <div style="width:50px; height:50px; background:rgba(255,255,255,0.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,0.25);">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);">
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines — OJT Information Management System</div>
                        <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">Expired MOA Report — Partner Companies</div>
                        <div style="font-size:8.5px; color:rgba(255,255,255,0.6); margin-top:3px;">Taguig Branch Campus &nbsp;|&nbsp; College of Engineering and Technology</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="display:inline-block; background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); border-radius:6px; padding:5px 12px; text-align:center;">
                            <div style="font-size:18px; font-weight:800; color:#fff; line-height:1;">${total}</div>
                            <div style="font-size:7.5px; color:rgba(255,255,255,0.7); text-transform:uppercase; letter-spacing:1px; margin-top:1px;">Page Records</div>
                        </div>
                        <div style="font-size:8.5px; color:rgba(255,255,255,0.55); margin-top:4px; text-align:center;">Page ${pageNum} of ${pageCount}</div>
                    </div>
                </div>
                <div style="background:rgba(0,0,0,0.15); height:3px;"></div>
            </div>

            <!-- META ROW -->
            <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">School Year:</span>
                        <strong style="color:#111827;">${syLabel}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Course:</span>
                        <strong style="color:#111827;">${course}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Showing:</span>
                        <strong style="color:#111827;">${total} compan${total !== 1 ? 'ies' : 'y'} (Page ${pageNum})</strong>
                    </div>
                </div>
                <div style="font-size:8.5px; color:#9ca3af;">Generated: ${dateStr} at ${timeStr}</div>
            </div>

            <!-- SECTION LABEL -->
            <div style="padding:9px 22px 3px 22px;">
                <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">Expired MOA Details — Page ${pageNum}</div>
            </div>

            <!-- DATA TABLE — table-layout:fixed + % widths = no side scroll -->
            <div style="padding:4px 22px 0 22px;">
                <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-family:'Poppins',Arial,sans-serif; border:1px solid #d1d5db;">
                    <colgroup>
                        <col style="width:3%;">   <!-- # -->
                        <col style="width:18%;">  <!-- Company -->
                        <col style="width:18%;">  <!-- Address -->
                        <col style="width:13%;">  <!-- Rep -->
                        <col style="width:11%;">  <!-- Contact -->
                        <col style="width:18%;">  <!-- Email -->
                        <col style="width:10%;">  <!-- School Year -->
                        <col style="width:9%;">   <!-- Status -->
                    </colgroup>
                    <thead>
                        <tr style="background:#7f0000;">
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; border-right:1px solid rgba(255,255,255,0.15);">#</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Company Name</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Address</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Representative</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Contact No.</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Email</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">School Year</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHTML || `<tr><td colspan="8" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No records found for the selected filters.</td></tr>`}
                    </tbody>
                </table>
            </div>

            <!-- SIGNATURE BLOCK -->
            <div style="padding:18px 22px 12px 22px;">
                <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                    <div style="display:flex; justify-content:space-between; gap:24px;">
                        <div style="flex:1; text-align:center; border-top:1.5px solid #374151; padding-top:6px; margin-top:32px;">
                            <div style="font-size:9.5px; font-weight:700; color:#111827; letter-spacing:0.3px;">OJT COORDINATOR</div>
                            <div style="font-size:8px; color:#6b7280; margin-top:2px;">Signature over Printed Name</div>
                        </div>
                        <div style="flex:1; text-align:center; border-top:1.5px solid #374151; padding-top:6px; margin-top:32px;">
                            <div style="font-size:9.5px; font-weight:700; color:#111827; letter-spacing:0.3px;">DEPARTMENT CHAIR / HEAD</div>
                            <div style="font-size:8px; color:#6b7280; margin-top:2px;">Signature over Printed Name</div>
                        </div>
                        <div style="flex:1; text-align:center; border-top:1.5px solid #374151; padding-top:6px; margin-top:32px;">
                            <div style="font-size:9.5px; font-weight:700; color:#111827; letter-spacing:0.3px;">DATE</div>
                            <div style="font-size:8px; color:#6b7280; margin-top:2px;">Date Approved / Noted</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DOCUMENT FOOTER -->
            <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:0.7; filter:brightness(2);">
                    <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">© 1998–2026 <strong style="color:#fca5a5;">Polytechnic University of the Philippines</strong> — InternConnect OJT IMS</span>
                </div>
                <span style="font-size:8px; color:rgba(255,255,255,0.5);">Ref: MOA-EXP-${now.getFullYear()} &nbsp;|&nbsp; Page ${pageNum} of ${pageCount}</span>
            </div>

        </div>`;
    }

    /* ── Modal (single instance, no double-trigger) ── */
    const previewModalEl = document.getElementById('printPreviewModal');
    const previewModal   = new bootstrap.Modal(previewModalEl, { backdrop: 'static', keyboard: true });

    document.getElementById('openPreviewBtn').addEventListener('click', function () {
        document.getElementById('printPreviewContent').innerHTML = buildPrintHTML();
        previewModal.show();
    });

    /* ── Print
       @page { margin: 0 } removes browser date/title/URL.
       Body padding restores the visual spacing.          ── */
    document.getElementById('doPrintBtn').addEventListener('click', function () {
        const html = buildPrintHTML();

        const printDoc = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Expired MOA Report — InternConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html, body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            font-size: 10px; color: #1a1a1a; background: #fff;
            padding: 10mm 12mm;
        }
        table { border-collapse: collapse; width: 100%; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr    { page-break-inside: avoid; }
    </style>
</head>
<body>${html}</body>
</html>`;

        const frame = document.getElementById('printFrame');
        const fw    = frame.contentWindow;
        fw.document.open();
        fw.document.write(printDoc);
        fw.document.close();
        fw.focus();
        setTimeout(function () { fw.print(); }, 900);
    });
</script>

</body>
</html>