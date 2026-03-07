<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Class</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            flex-shrink: 0;
        }

        .sidebar-brand img {
            width: 36px; height: 36px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.2));
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.25s, width 0.25s;
        }

        .sidebar-brand-name {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            line-height: 1;
        }

        .sidebar-brand-name span { color: #fca5a5; }

        .sidebar-brand-sub {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 3px;
        }

        .sidebar.collapsed .sidebar-brand-text { opacity: 0; width: 0; }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .sidebar-user:hover { background: rgba(255,255,255,0.05); }

        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: rgba(239,68,68,0.25);
            border: 1.5px solid rgba(239,68,68,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fca5a5;
            font-size: 16px;
            flex-shrink: 0;
        }

        .user-info { overflow: hidden; white-space: nowrap; transition: opacity 0.25s, width 0.25s; }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; display: block; text-overflow: ellipsis; overflow: hidden; }
        .user-role { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; }
        .sidebar.collapsed .user-info { opacity: 0; width: 0; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(239,68,68,0.3); border-radius: 10px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.25s;
            position: relative;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(239,68,68,0.15); border-left-color: #ef4444; }
        .nav-item .nav-icon { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }
        .nav-item .nav-label { transition: opacity 0.25s; overflow: hidden; }
        .sidebar.collapsed .nav-label { opacity: 0; width: 0; }

        .nav-item .tooltip-label {
            position: absolute;
            left: calc(var(--sidebar-w-collapsed) + 8px);
            background: #1a0000;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
        }

        .sidebar.collapsed .nav-item:hover .tooltip-label { opacity: 1; }

        .sidebar-footer { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; }

        /* =============== MAIN =============== */
        .main-content {
            margin-left: var(--sidebar-w);
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.expanded { margin-left: var(--sidebar-w-collapsed); }

        .topbar {
            height: var(--topbar-h);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .menu-toggle {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #f5f5f5;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 18px;
            transition: all 0.2s;
        }

        .menu-toggle:hover { background: #fee2e2; color: var(--red); }

        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }

        .topbar-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--red);
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }

        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* =============== CARDS =============== */
        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 22px;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .table-card-header .header-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 15px;
            flex-shrink: 0;
        }

        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p { font-size: 12.5px; color: #888; margin-top: 2px; }

        .table-card-body { padding: 0; overflow-x: auto; }

        /* =============== ROOMS TABLE =============== */
        .rooms-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .rooms-table thead tr {
            background: #fafafa;
            border-bottom: 1px solid #f0f0f0;
        }

        .rooms-table thead th {
            padding: 12px 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            text-align: left;
        }

        .rooms-table tbody tr {
            border-bottom: 1px solid #f9f9f9;
            transition: background 0.2s;
        }

        .rooms-table tbody tr:last-child { border-bottom: none; }
        .rooms-table tbody tr:hover { background: #fff5f5; }

        .rooms-table tbody td {
            padding: 14px 20px;
            color: #333;
            vertical-align: middle;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-approved { background: #dcfce7; color: #16a34a; }
        .status-denied   { background: #fee2e2; color: var(--red); }
        .status-pending  { background: #fef9c3; color: #ca8a04; }
        .status-default  { background: #f3f4f6; color: #6b7280; }

        /* Action buttons */
        .btn-join {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 10px rgba(220,38,38,0.2);
        }

        .btn-join:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(220,38,38,0.3);
        }

        .btn-leave {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: #f3f4f6;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
        }

        .btn-leave:hover {
            background: #fee2e2;
            border-color: #fecaca;
            color: var(--red);
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #fff;
            border: 1.5px solid #e8e8e8;
            border-radius: 8px;
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            margin-left: 6px;
        }

        .btn-view:hover {
            border-color: var(--red);
            color: var(--red);
            background: #fff5f5;
        }

        /* =============== ANNOUNCEMENTS TABLE =============== */
        .table-card-body .dataTables_wrapper {
            padding: 16px 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
        }

        .table-card-body table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        .table-card-body table.dataTable thead th {
            background: #fafafa;
            color: #555;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            border-top: none;
        }

        .table-card-body table.dataTable tbody td {
            padding: 12px 14px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13.5px;
        }

        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }

        .dataTables_filter input {
            border: 1px solid #e5e5e5 !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            outline: none !important;
            transition: border-color 0.2s !important;
        }

        .dataTables_filter input:focus {
            border-color: var(--red) !important;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important;
        }

        .dataTables_length select {
            border: 1px solid #e5e5e5 !important;
            border-radius: 8px !important;
            padding: 4px 8px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            padding: 4px 10px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--red) !important;
            border-color: var(--red) !important;
            color: #fff !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #fee2e2 !important;
            border-color: #fecaca !important;
            color: var(--red) !important;
        }

        /* =============== MODAL =============== */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%);
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-title {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-title i { font-size: 18px; }

        .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .modal-body {
            padding: 24px;
            background: #fff;
        }

        .modal-detail-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .modal-detail-row:last-child { border-bottom: none; }

        .modal-detail-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 13px;
            flex-shrink: 0;
        }

        .modal-detail-label {
            font-size: 11.5px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .modal-detail-value {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 500;
            margin-top: 1px;
        }

        .modal-footer {
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
            padding: 16px 24px;
        }

        .btn-modal-close {
            padding: 9px 20px;
            background: #f3f4f6;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-close:hover {
            background: #fee2e2;
            border-color: #fecaca;
            color: var(--red);
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
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
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
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
                <i class="fa fa-graduation-cap"></i>
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

<<<<<<< HEAD
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>My <span>Class</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Class</span>
                </div>
            </div>
=======
                <li class="active">
                    <a href="{{ url('/student/class') }}">
                        <span class="icon">
                            <ion-icon name="clipboard-outline"></ion-icon>
                        </span>
                        <span class="title">Class</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/student/files') }}">
                        <span class="icon">
                            <ion-icon name="download-outline"></ion-icon>
                        </span>
                        <span class="title">Downloadable Files</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/student/MOA') }}">
                        <span class="icon">
                            <ion-icon name="document-outline"></ion-icon>
                        </span>
                        <span class="title">MOA</span>
                        <span class="icon" style="margin-left: 30%; font-size: 22px;">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span>
                    </a>
                </li>

                <li >
                    <a href="{{ url('/student/requirements') }}">
                        <span class="icon">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                        </span>
                        <span class="title">Requirements</span>
                    </a>
                </li>                  

                <li>
                    <a href="{{ url('/login') }}">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Log Out</span>
                    </a>
                </li>
            </ul>
>>>>>>> aa6a5d91508198ff4cee6146a9d2421213520478
        </div>

        <!-- Rooms Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="header-icon"><i class="fa fa-door-open"></i></div>
                <div>
                    <h2>Available Rooms</h2>
                    <p>Join or view your assigned class room</p>
                </div>
            </div>
            <div class="table-card-body">
                <table class="rooms-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Room</th>
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
                                @if ($data->status == 1)
                                    <span class="status-badge status-approved"><i class="fa fa-check-circle"></i> Approved</span>
                                @elseif ($data->status == 2)
                                    <span class="status-badge status-denied"><i class="fa fa-times-circle"></i> Denied</span>
                                @elseif ($data->status == 3)
                                    <span class="status-badge status-pending"><i class="fa fa-clock"></i> Pending</span>
                                @else
                                    <span class="status-badge status-default"><i class="fa fa-minus-circle"></i> Not Joined</span>
                                @endif
                            </td>
                            <td>
                                @if ($data->status != 1 && $data->status != 3)
                                    <button class="btn-join" onclick="joinStudent('{{ url('/student/join', $data->email) }}')">
                                        <i class="fa fa-sign-in-alt"></i> Join
                                    </button>
                                @else
                                    <button class="btn-leave" onclick="leaveStudent()">
                                        <i class="fa fa-sign-out-alt"></i> Leave
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
            </div>
            <div class="table-card-body">

                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#ATable').DataTable();
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
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}</td>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
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
</script>

</body>
</html>