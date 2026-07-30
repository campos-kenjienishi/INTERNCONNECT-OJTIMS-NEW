<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Student Requirements Matrix</title>
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

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
        }

        body.dark-mode {
            background: #000000;
            color: #e0e0e0;
        }

        body.dark-mode .sidebar {
            box-shadow: 4px 0 24px rgba(0,0,0,0.4);
        }

        /* =============== SIDEBAR =============== */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(160deg, #1a0000 0%, #4a0000 50%, #7f0000 100%);
            display: flex; flex-direction: column;
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
            padding: 12px 20px; color: rgba(255,255,255,0.55);
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

        body.dark-mode .main-content { background: #000000; }
        .main-content.expanded { margin-left: var(--sidebar-w-collapsed); }

        .topbar {
            height: var(--topbar-h); background: #fff;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        body.dark-mode .topbar {
            background: #252525;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
            border-bottom: 1px solid #3a3a3a;
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }
        body.dark-mode .topbar-left { color: #e0e0e0; }

        .menu-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 18px; transition: all 0.2s;
        }

        body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
        .menu-toggle:hover { background: #fee2e2; color: var(--red); }
        body.dark-mode .menu-toggle:hover { background: rgba(220,38,38,0.2); }

        .darkmode-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: 1px solid #ddd; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 16px; transition: all 0.3s; padding: 0;
        }

        .darkmode-toggle:hover {
            background: #fee2e2; color: var(--red); border-color: #fecaca;
            transform: translateY(-2px); box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        body.dark-mode .darkmode-toggle { background: #2a2a2a; border-color: #3a3a3a; color: #e8e8e8; }
        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2); color: #ff6b6b;
            border-color: rgba(220,38,38,0.3); box-shadow: 0 6px 16px rgba(220,38,38,0.3);
        }

        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }
        body.dark-mode .topbar-title { color: #999; }

        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red-dark);
        }

        body.dark-mode .topbar-badge {
            background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b;
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }
        body.dark-mode .page-content { background: #000000; }

        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
        body.dark-mode .page-header h1 { color: #fff; }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #888; margin-top: 6px;
        }
        body.dark-mode .breadcrumb { color: #999; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* Stats row */
        .stats-row {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px; margin-bottom: 22px;
        }

        .stat-card {
            background: #fff; border-radius: 14px; padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04);
        }

        body.dark-mode .stat-card {
            background: #2a2a2a; box-shadow: 0 2px 10px rgba(0,0,0,0.3); border: 1px solid #3a3a3a;
        }

        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }

        body.dark-mode .stat-icon.red    { background: rgba(220,38,38,0.2); color: #ff6b6b; }
        body.dark-mode .stat-icon.blue   { background: rgba(37,99,235,0.2); color: #93c5fd; }
        body.dark-mode .stat-icon.green  { background: rgba(22,163,74,0.2); color: #6ee7b7; }
        body.dark-mode .stat-icon.amber  { background: rgba(202,138,4,0.2); color: #fcd34d; }

        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        body.dark-mode .stat-num { color: #fff; }
        body.dark-mode .stat-name { color: #999; }

        /* Report filter card */
        .filter-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden; margin-bottom: 22px;
        }

        body.dark-mode .filter-card {
            background: #2a2a2a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); border: 1px solid #3a3a3a;
        }

        .filter-card-header {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 22px; background: #fafafa; border-bottom: 1px solid #f0f0f0;
        }

        body.dark-mode .filter-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }

        .filter-header-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: #fee2e2; display: flex; align-items: center; justify-content: center;
            color: var(--red); font-size: 14px; flex-shrink: 0;
        }

        body.dark-mode .filter-header-icon { background: rgba(220,38,38,0.15); color: #ff6b6b; }
        .filter-card-header h3 { font-size: 14px; font-weight: 700; color: #1a1a1a; }
        .filter-card-header p  { font-size: 12px; color: #888; margin-top: 1px; }

        body.dark-mode .filter-card-header h3 { color: #fff; }
        body.dark-mode .filter-card-header p { color: #999; }

        .filter-card-body {
            padding: 20px 22px; display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
        }
        body.dark-mode .filter-card-body { background: #2a2a2a; }
        .filter-field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 180px; }

        .filter-label {
            font-size: 12px; font-weight: 600; color: #444; display: flex; align-items: center; gap: 5px;
        }
        body.dark-mode .filter-label { color: #ccc; }
        .filter-label i { color: var(--red); font-size: 10px; }

        .filter-select {
            background: #fafafa; border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif; font-size: 13px;
            padding: 10px 14px; outline: none; transition: all 0.25s;
        }

        body.dark-mode .filter-select { background: #3a3a3a; border: 1.5px solid #3a3a3a; color: #e0e0e0; }

        .btn-generate {
            display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 14px rgba(220,38,38,0.22); white-space: nowrap; text-decoration: none;
        }
        .btn-generate:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(220,38,38,0.32); color: #fff; text-decoration: none; }

        /* Table Card */
        .table-card {
            background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04); overflow: hidden;
        }

        body.dark-mode .table-card { background: #2a2a2a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); border: 1px solid #3a3a3a; }

        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 18px 24px; border-bottom: 1px solid #f0f0f0; background: #fafafa; flex-wrap: wrap;
        }

        body.dark-mode .table-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
        .table-card-header-left { display: flex; align-items: center; gap: 12px; }

        .header-icon {
            width: 38px; height: 38px; border-radius: 10px; background: #fee2e2;
            display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 15px; flex-shrink: 0;
        }

        body.dark-mode .header-icon { background: rgba(220,38,38,0.15); color: #ff6b6b; }

        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }
        body.dark-mode .table-card-header h2 { color: #fff; }
        body.dark-mode .table-card-header p { color: #999; }

        .student-count-badge {
            display: inline-flex; align-items: center; gap: 6px; background: #fee2e2;
            color: var(--red); border-radius: 20px; padding: 5px 14px; font-size: 12.5px; font-weight: 700;
        }

        body.dark-mode .student-count-badge { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        /* Badges */
        .student-avatar-initial {
            width: 34px; height: 34px; border-radius: 50%; background: #fee2e2;
            color: var(--red); display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; flex-shrink: 0;
        }
        body.dark-mode .student-avatar-initial { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        .course-badge {
            background: #f3e8ff; color: #7c3aed; padding: 4px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 600; display: inline-block;
        }
        body.dark-mode .course-badge { background: rgba(124,58,237,0.2); color: #d8b4fe; }

        .section-badge {
            background: #dbeafe; color: #2563eb; padding: 4px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 700; display: inline-block;
        }
        body.dark-mode .section-badge { background: rgba(37,99,235,0.2); color: #93c5fd; }

        .req-badge {
            display: inline-flex; align-items: center; justify-content: center; gap: 4px;
            padding: 4px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 600;
            text-decoration: none; white-space: nowrap; cursor: pointer;
        }
        .req-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .req-pending  { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .req-denied   { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .req-missing  { background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1; }

        body.dark-mode .req-approved { background: rgba(22,163,74,0.2); color: #6ee7b7; border-color: rgba(22,163,74,0.3); }
        body.dark-mode .req-pending  { background: rgba(202,138,4,0.2); color: #fcd34d; border-color: rgba(202,138,4,0.3); }
        body.dark-mode .req-denied   { background: rgba(220,38,38,0.2); color: #ff6b6b; border-color: rgba(220,38,38,0.3); }
        body.dark-mode .req-missing  { background: #3a3a3a; color: #888; border-color: #555; }

        .btn-action.view-personal {
            display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
            border-radius: 8px; background: #fff; border: 1.5px solid #e0e7ff; color: #4f46e5;
            font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-action.view-personal:hover { background: #e0e7ff; transform: translateY(-1px); }

        body.dark-mode .btn-action.view-personal {
            background: rgba(79,70,229,0.15); border-color: rgba(79,70,229,0.3); color: #93c5fd;
        }
        body.dark-mode .btn-action.view-personal:hover { background: rgba(79,70,229,0.25); }

        .progress-bar-container {
            width: 100%; height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%; background: linear-gradient(90deg, #dc2626, #16a34a); border-radius: 10px;
        }

        /* Modal styling matching students.blade.php */
        .modal-content { border-radius: 18px; border: none; overflow: hidden; }
        .modal-header {
            background: linear-gradient(135deg, #1a0000 0%, #4a0000 50%, #7f0000 100%);
            padding: 20px 24px; border: none; color: #fff;
        }
        .modal-body { padding: 24px; background: #fff; }
        body.dark-mode .modal-body { background: #1a1a1a; }
        .modal-footer { background: #fafafa; border-top: 1px solid #f0f0f0; padding: 14px 24px; }
        body.dark-mode .modal-footer { background: #2a2a2a; border-top: 1px solid #3a3a3a; }

        .btn-modal-close {
            padding: 9px 22px; background: #f3f4f6; border: 1px solid #e5e5e5;
            border-radius: 8px; color: #555; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        /* Dashboard Footer */
        .dashboard-footer {
            background: #fff; border-top: 1px solid #f0f0f0; color: #888;
            text-align: center; padding: 18px 28px; font-size: 12.5px;
            margin-top: auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }
        body.dark-mode .dashboard-footer { background: #252525; border-top: 1px solid #3a3a3a; color: #999; }
        .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .footer-copy { font-size: 12.5px; color: #aaa; font-weight: 500; }
        .footer-copy span { color: var(--red); font-weight: 600; }
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
            <span class="sidebar-brand-sub">OJTIMS</span>
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
        <a href="{{ url('/studentLists') }}" class="nav-item active">
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
                <h1>Student <span>Requirements Matrix</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/studentLists') }}">Students</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Requirements Matrix</span>
                </div>
            </div>
            <a href="{{ url('/studentLists') }}" class="btn-generate">
                <i class="fa fa-arrow-left"></i> Back to Active Students
            </a>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ $totalStudentsTracked }}</div>
                    <div class="stat-name">Total Students Tracked</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $fullySubmittedCount }}</div>
                    <div class="stat-name">100% Fully Uploaded</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-exclamation-triangle"></i></div>
                <div>
                    <div class="stat-num">{{ $incompleteCount }}</div>
                    <div class="stat-name">Incomplete Submissions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $totalFilesSubmitted }}</div>
                    <div class="stat-name">Submitted Requirements</div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h3>Filter & Search Requirements</h3>
                    <p>Filter student submission records by course, section, professor or submission status</p>
                </div>
            </div>
            <form action="{{ route('coordinator.studentRequirements') }}" method="GET">
                <div class="filter-card-body">
                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-graduation-cap"></i> Course</label>
                        <select name="course" class="filter-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $c)
                                <option value="{{ $c }}" {{ $selectedCourse === $c ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                        <select name="school_year" class="filter-select">
                            <option value="">All School Years</option>
                            @foreach($schoolYears as $sy)
                                <option value="{{ $sy }}" {{ $selectedSchoolYear === $sy ? 'selected' : '' }}>
                                    {{ $sy }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-user-tie"></i> Adviser / Professor</label>
                        <select name="professor_id" class="filter-select">
                            <option value="">All Professors</option>
                            @foreach($professors as $prof)
                                <option value="{{ $prof->id }}" {{ (string)$selectedProfessorId === (string)$prof->id ? 'selected' : '' }}>
                                    {{ $prof->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="filter-label"><i class="fa fa-tasks"></i> Submission Status</label>
                        <select name="status" class="filter-select">
                            <option value="">All Submission Statuses</option>
                            <option value="complete" {{ $selectedStatus === 'complete' || $selectedStatus === 'completed' ? 'selected' : '' }}>100% Complete (All Uploaded)</option>
                            <option value="incomplete" {{ $selectedStatus === 'incomplete' ? 'selected' : '' }}>Incomplete (Missing Files)</option>
                        </select>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn-generate"><i class="fa fa-filter"></i> Apply Filter</button>
                        <a href="{{ route('coordinator.studentRequirements') }}" class="btn-generate" style="background:#6b7280; box-shadow:none;"><i class="fa fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Requirements Matrix Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                    <div>
                        <h2>Student Requirements Compliance Matrix</h2>
                        <p>View student requirement submission statuses and detailed file repository</p>
                    </div>
                </div>
                <div class="student-count-badge">
                    <i class="fa fa-users"></i> {{ $totalStudentsTracked }} Students Tracked
                </div>
            </div>

            <div style="padding: 20px; overflow-x: auto;">
                <table id="requirementsMatrixTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>STUDENT DETAILS</th>
                            <th>COURSE</th>
                            <th>SECTION</th>
                            <th>SCHOOL YEAR</th>
                            <th>PROFESSOR</th>
                            <th>STATUS SUMMARY</th>
                            <th>PROGRESS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentMatrix as $item)
                            @php
                                $st = $item['student'];
                                $usr = $item['user'];
                                $categories = $item['categories'];
                                $totalCats = $item['total_categories'] ?? 0;
                                $pct = $totalCats > 0 ? round(($item['submitted_count'] / $totalCats) * 100) : 0;
                                $studentName = $st->full_name ?? ($usr->full_name ?? 'N/A');
                                $initial = strtoupper(substr($studentName, 0, 1));
                                $syText = (!empty($st->school_year_start) && !empty($st->school_year_end))
                                    ? ($st->school_year_start . '-' . $st->school_year_end)
                                    : '—';
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="student-avatar-initial">{{ $initial }}</div>
                                        <div>
                                            <div style="font-weight:700; color:#1a1a1a;">{{ $studentName }}</div>
                                            <div style="font-size:12px; color:#888;">{{ $st->studentNum ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="course-badge">{{ $st->course ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="section-badge">{{ $st->year_and_section ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="req-badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">
                                        <i class="fa fa-calendar-alt me-1"></i> {{ $syText }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight:600; font-size:13px; color:#444;">
                                        <i class="fa fa-user-tie me-1" style="color:var(--red);"></i>
                                        {{ $st->adviser_name ?? 'Not Assigned' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-wrap:wrap; gap:6px; max-width:280px;">
                                        @if($item['is_fully_submitted'])
                                            <span class="req-badge req-approved">
                                                <i class="fa fa-check-circle"></i> {{ $item['submitted_count'] }}/{{ $totalCats }} Uploaded
                                            </span>
                                        @else
                                            <span class="req-badge req-approved">
                                                <i class="fa fa-file-alt"></i> {{ $item['submitted_count'] }} Uploaded
                                            </span>
                                            @if($item['missing_count'] > 0)
                                                <span class="req-badge req-missing">
                                                    <i class="fa fa-minus-circle"></i> {{ $item['missing_count'] }} Missing
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="flex:1; background:#e2e8f0; height:8px; border-radius:4px; overflow:hidden;">
                                            <div style="width:{{ $pct }}%; background: linear-gradient(90deg, #dc2626 0%, #16a34a 100%); height:100%;"></div>
                                        </div>
                                        <span style="font-size:12px; font-weight:700; color:#334155;">{{ $item['submitted_count'] }}/{{ $totalCats }} ({{ $pct }}%)</span>
                                    </div>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn-action view-personal"
                                            onclick='openStudentFolderModal({
                                                student_name: "{{ addslashes($studentName) }}",
                                                student_num: "{{ addslashes($st->studentNum ?? '') }}",
                                                course: "{{ addslashes($st->course ?? 'N/A') }}",
                                                adviser: "{{ addslashes($st->adviser_name ?? 'Not Assigned') }}",
                                                categories: @json($categories)
                                            })'>
                                        <i class="fa fa-folder"></i> View Folder
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
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

<!-- =============== STUDENT FOLDER MODAL =============== -->
<div class="modal fade" id="studentFilesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1a0000 0%, #7f0000 100%); color: white; padding: 20px 24px; border: none;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fca5a5;">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" id="modalFolderName" style="font-weight: 800; font-size: 18px; margin: 0; color: #ffffff;">
                            Student Requirements
                        </h5>
                        <div id="modalFolderSub" style="font-size: 12.5px; color: rgba(255,255,255,0.75); margin-top: 2px;">
                            Detailed repository of submitted basic requirements
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                <div id="modalFilesList" style="display: flex; flex-direction: column; gap: 12px;"></div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =============== PDF PREVIEW MODAL =============== -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 90vw;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px rgba(0,0,0,0.3); overflow: hidden; height: 88vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f0000 0%, #991b1b 100%); color: white; padding: 16px 24px; border: none; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fa fa-file-pdf" style="font-size: 22px; color: #fca5a5;"></i>
                    <h5 class="modal-title" id="pdfPreviewTitle" style="font-weight: 700; font-size: 16px; margin: 0; color: #ffffff;">
                        Document Preview
                    </h5>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a id="pdfDownloadLink" href="#" class="btn-action view-personal" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; text-decoration: none;">
                        <i class="fa fa-download"></i> Download
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" style="padding: 0; flex: 1; background: #525659;">
                <iframe id="pdfPreviewIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showDenialReason(catName, reason) {
        Swal.fire({
            title: `Denied Requirement: ${catName}`,
            text: reason || 'No denial reason provided.',
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    }

    function openPdfPreviewModal(url, title, downloadUrl) {
        document.getElementById('pdfPreviewTitle').innerText = title || 'Document Preview';
        document.getElementById('pdfPreviewIframe').src = url;
        document.getElementById('pdfDownloadLink').href = downloadUrl || url;

        const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
        modal.show();
    }

    function openStudentFolderModal(data) {
        document.getElementById('modalFolderName').innerText = data.student_name + "'s Requirements";
        document.getElementById('modalFolderSub').innerText = (data.student_num ? data.student_num + ' • ' : '') + data.course + ' (Adviser: ' + data.adviser + ')';

        const listContainer = document.getElementById('modalFilesList');
        listContainer.innerHTML = '';

        const categories = data.categories || {};
        const catKeys = Object.keys(categories);

        if (catKeys.length === 0) {
            listContainer.innerHTML = `
                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                    <i class="fa fa-folder-open" style="font-size: 36px; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p style="font-size: 14px; font-weight: 600; margin: 0;">No basic requirement categories found.</p>
                </div>
            `;
        } else {
            catKeys.forEach(catName => {
                const info = categories[catName];
                let badgeHtml = '';
                let actionHtml = '';

                if (info.submitted || info.file_id) {
                    badgeHtml = '<span class="req-badge req-approved"><i class="fa fa-check-circle me-1"></i> Uploaded</span>';
                    actionHtml = `
                        <button type="button" onclick="openPdfPreviewModal('/coordinator/requirements/view/${info.file_id}', '${catName.replace(/'/g, "\\'")}', '/coordinator/requirements/download/${info.file_id}')" class="btn-action view-personal">
                            <i class="fa fa-eye"></i> View PDF
                        </button>
                        <a href="/coordinator/requirements/download/${info.file_id}" class="btn-action view-personal" style="background:#f1f5f9; border-color:#cbd5e1; color:#475569; text-decoration:none;">
                            <i class="fa fa-download"></i> Download
                        </a>
                    `;
                } else {
                    badgeHtml = '<span class="req-badge req-missing"><i class="fa fa-minus-circle me-1"></i> Missing</span>';
                    actionHtml = '<span style="font-size:12px; color:#94a3b8; font-style:italic;">Not Uploaded</span>';
                }

                listContainer.innerHTML += `
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa fa-file-pdf" style="color: #dc2626; font-size: 16px;"></i> ${catName}
                            </div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                ${info.file_name ? info.file_name : 'No file attached'}
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            ${badgeHtml}
                            ${actionHtml}
                        </div>
                    </div>
                `;
            });
        }

        const modal = new bootstrap.Modal(document.getElementById('studentFilesModal'));
        modal.show();
    }

    $(document).ready(function() {
        $('#requirementsMatrixTable').DataTable({
            scrollX: true,
            order: []
        });

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
