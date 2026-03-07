<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Professors</title>
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

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #888; margin-top: 6px;
        }

        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* Add Professor button */
        .btn-add {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
        }

        /* Stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px; margin-bottom: 22px;
        }

        .stat-card {
            background: #fff; border-radius: 14px;
            padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }

        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* Table card */
        .table-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa; flex-wrap: wrap;
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

        .prof-count-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fee2e2; color: var(--red);
            border-radius: 20px; padding: 5px 14px;
            font-size: 12.5px; font-weight: 700;
        }

        /* DataTables */
        .table-card-body .dataTables_wrapper {
            padding: 16px 22px;
            font-family: 'Poppins', sans-serif; font-size: 13px;
        }

        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }

        .table-card-body table.dataTable thead th {
            background: #fafafa; color: #555;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0; border-top: none;
        }

        .table-card-body table.dataTable tbody td {
            padding: 13px 12px; color: #333;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13px; vertical-align: middle;
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
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px !important; font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--red) !important; border-color: var(--red) !important; color: #fff !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important;
        }

        /* Professor cell */
        .prof-cell { display: flex; align-items: center; gap: 10px; }

        .prof-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 13px; font-weight: 700; flex-shrink: 0;
        }

        .prof-name-text { font-weight: 600; color: #1a1a1a; }

        /* Subject badges */
        .subject-code-badge {
            display: inline-flex; align-items: center;
            background: #ede9fe; color: #7c3aed;
            border-radius: 20px; padding: 3px 10px;
            font-size: 11.5px; font-weight: 600;
            margin: 2px 2px 0 0;
        }

        /* Action buttons */
        .btn-edit {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            background: #fff; border: 1.5px solid #e0e7ff; color: #4f46e5;
            font-family: 'Poppins', sans-serif; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-edit:hover { background: #e0e7ff; }

        .btn-remove {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            background: #fff; border: 1.5px solid #fecaca; color: var(--red);
            font-family: 'Poppins', sans-serif; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-remove:hover { background: #fee2e2; }

        .actions-wrap { display: flex; align-items: center; gap: 6px; }

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

        .modal-body {
            padding: 24px; background: #fff;
            max-height: 520px; overflow-y: auto;
        }

        .modal-body::-webkit-scrollbar { width: 4px; }
        .modal-body::-webkit-scrollbar-thumb { background: #fecaca; border-radius: 10px; }

        /* Modal form fields */
        .field-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }

        .field-label {
            font-size: 12px; font-weight: 600; color: #444;
            display: flex; align-items: center; gap: 5px;
        }

        .field-label i { color: var(--red); font-size: 11px; }

        .field-input, .field-select {
            width: 100%; background: #fafafa;
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13px; padding: 10px 13px; outline: none;
            transition: all 0.25s;
        }

        .field-input:focus, .field-select:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* Checkboxes */
        .checkbox-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 8px; margin-top: 4px;
        }

        .checkbox-item {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 10px; border-radius: 8px;
            border: 1.5px solid #e8e8e8; background: #fafafa;
            cursor: pointer; transition: all 0.2s; font-size: 12.5px;
        }

        .checkbox-item input[type="checkbox"] { accent-color: var(--red); width: 14px; height: 14px; }
        .checkbox-item:has(input:checked) { border-color: #fecaca; background: #fff5f5; color: var(--red); }

        /* Section divider */
        .modal-section {
            font-size: 11px; font-weight: 700; color: #aaa;
            text-transform: uppercase; letter-spacing: 1px;
            margin: 18px 0 10px; padding-bottom: 6px;
            border-bottom: 1px solid #f0f0f0;
            display: flex; align-items: center; gap: 8px;
        }

        .modal-section i { color: var(--red); font-size: 12px; }

        .modal-footer {
            background: #fafafa; border-top: 1px solid #f0f0f0;
            padding: 14px 24px; display: flex;
            justify-content: flex-end; gap: 10px;
        }

        .btn-modal-close {
            padding: 9px 20px; background: #f3f4f6;
            border: 1px solid #e5e5e5; border-radius: 8px; color: #555;
            font-family: 'Poppins', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .btn-modal-submit {
            padding: 9px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 8px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all 0.25s;
            box-shadow: 0 3px 10px rgba(220,38,38,0.2);
        }

        .btn-modal-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(220,38,38,0.3); }

        /* Year select row */
        .year-row { display: flex; align-items: center; gap: 10px; }
        .year-row span { font-weight: 700; color: #aaa; font-size: 14px; flex-shrink: 0; }
        .year-row .field-select { flex: 1; }

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
            .stats-row { grid-template-columns: 1fr 1fr; }
            .field-row { grid-template-columns: 1fr; }
            .checkbox-grid { grid-template-columns: repeat(2, 1fr); }
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

<<<<<<< HEAD
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
=======
                <a href="{{ url('/accountinfo') }}" style="text-decoration: none;">
                    <span class="iconname">
                        <ion-icon name="person-circle-outline"></ion-icon>
                    </span>
                    <span class="name"> {{ $user->full_name }} </span>
                    <span class="name2">OJT COORDINATOR </span>
                </a>

                <a href="{{ url('/accountinfo') }}" style="text-decoration: none;">
                    <span class="hidden-on-big">{{ $user->full_name }}</span>
                    <!-- <div class="toggle" id="toggle2">
                        <ion-icon name="menu-outline"></ion-icon>
                    </div> -->
                </a>


                <li>
                    <a href="{{ url('/dashboard') }}">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>



                <li>
                    <a href="{{ url('/studentLists') }}">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Students</span>
                    </a>
                </li>

                <li class="active">
                    <a href="{{ url('/professorTab') }}">
                        <span class="icon">
                            <ion-icon name="people-circle-outline"></ion-icon>
                        </span>
                        <span class="title">Professors</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/uploadpage') }}">
                        <span class="icon">
                            <ion-icon name="document-outline"></ion-icon>
                        </span>
                        <span class="title">Upload Templates</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/maintenance') }}">
                        <span class="icon">
                            <ion-icon name="code-working-outline"></ion-icon>
                        </span>
                        <span class="title">Maintenance</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/MOA') }}">
                        <span class="icon">
                            <ion-icon name="folder-outline"></ion-icon>
                        </span>
                        <span class="title">MOA</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/reports') }}">
                        <span class="icon">
                            <ion-icon name="cellular-outline"></ion-icon>
                        </span>
                        <span class="title">Reports</span>
                        <span class="icon" style="margin-left: 30%; font-size: 22px;">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/auditlog') }}">
                        <span class="icon">
                            <ion-icon name="clipboard-outline"></ion-icon>
                        </span>
                        <span class="title">Audit Log</span>
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
        <a href="{{ url('/professorTab') }}" class="nav-item active">
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
        <li>
    <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
        </a>
</li>
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
                <h1>OJT <span>Professors</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Professors</span>
                </div>
            </div>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
                <i class="fa fa-plus"></i> Add New Professor
            </button>
        </div>

        <!-- Stats Row -->
        @php $totalProfs = count($data); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-chalkboard-teacher"></i></div>
                <div>
                    <div class="stat-num">{{ $totalProfs }}</div>
                    <div class="stat-name">Total Professors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa fa-book"></i></div>
                <div>
                    <div class="stat-num">
                        {{ collect($data)->sum(fn($p) => $p->subjects->count()) }}
                    </div>
                    <div class="stat-name">Total Subjects</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-graduation-cap"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Active Program</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-user-check"></i></div>
                <div>
                    <div class="stat-num">Active</div>
                    <div class="stat-name">Status</div>
                </div>
            </div>
        </div>

        <!-- Professors Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                    <div>
                        <h2>Professor List</h2>
                        <p>Manage all OJT professors and their subject assignments</p>
                    </div>
                </div>
                <div class="prof-count-badge">
                    <i class="fa fa-chalkboard-teacher"></i>
                    {{ $totalProfs }} {{ $totalProfs == 1 ? 'professor' : 'professors' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="profTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Professor Name</th>
                            <th>Email</th>
                            <th>Subject Code</th>
                            <th>Subject Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $professor)
                        @php $subject = $professor->subjects->first(); @endphp
                        <tr>
                            <!-- Name -->
                            <td>
                                <div class="prof-cell">
                                    <div class="prof-avatar">
                                        {{ strtoupper(substr($professor->full_name, 0, 1)) }}
                                    </div>
                                    <span class="prof-name-text">{{ $professor->full_name }}</span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <i class="fa fa-envelope" style="color:var(--red); font-size:11px;"></i>
                                    {{ $professor->email }}
                                </div>
                            </td>

                            <!-- Subject Code -->
                            <td>
                                @foreach ($professor->subjects as $s)
                                    <span class="subject-code-badge">{{ $s->subject_code }}</span>
                                @endforeach
                            </td>

                            <!-- Subject Description -->
                            <td style="font-size:13px; color:#555;">
                                @foreach ($professor->subjects as $s)
                                    {{ $s->subject_description }}@if(!$loop->last), @endif
                                @endforeach
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">
                                    <button class="btn-edit btnView1"
                                        data-professor-id="{{ $professor->id }}"
                                        data-email="{{ $professor->email }}"
                                        data-subject-code="{{ $subject->subject_code ?? '' }}"
                                        data-subject-description="{{ $subject->subject_description ?? '' }}"
                                        data-first-name="{{ $usersP->where('email', $professor->email)->first()->first_name ?? '' }}"
                                        data-last-name="{{ $usersP->where('email', $professor->email)->first()->last_name ?? '' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editProfessorModal">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>

                                    <button class="btn-remove btnRemove"
                                        data-professor-id="{{ $professor->id }}">
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

<!-- =============== ADD PROFESSOR MODAL =============== -->
<div class="modal fade" id="addProfessorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-plus"></i> Add New Professor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/professorCreate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="modal-section"><i class="fa fa-user"></i> Basic Information</div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-user"></i> First Name</label>
                            <input class="field-input" type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-user"></i> Last Name</label>
                            <input class="field-input" type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-envelope"></i> Email Address</label>
                        <input class="field-input" type="email" name="email" placeholder="Enter email address" required>
                    </div>

                    <div class="modal-section"><i class="fa fa-book"></i> Subject Details</div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-tag"></i> Subject Code</label>
                            <input class="field-input" type="text" name="subject_code" placeholder="e.g. OJT101">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-align-left"></i> Subject Description</label>
                            <input class="field-input" type="text" name="subject_description" placeholder="Subject description">
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-alt"></i> Semester</label>
                            <select class="field-select" name="semester">
                                <option value="1st Sem">1st Sem</option>
                                <option value="2nd Sem">2nd Sem</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-graduation-cap"></i> Course</label>
                            <select name="course" class="field-select">
                                @foreach ($course as $c)
                                    <option value="{{ $c->course }}">{{ $c->course }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar"></i> Academic Year</label>
                        <div class="year-row">
                            <select class="field-select" name="academic_year_start" id="academic_year_start" required>
                                <option value="">Start Year</option>
                                @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <span>–</span>
                            <select class="field-select" name="academic_year_end" id="academic_year_end" required>
                                <option value="">End Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-clock"></i> Schedule</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar-week"></i> Schedule Days</label>
                        <div class="checkbox-grid">
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <label class="checkbox-item">
                                <input type="checkbox" name="schedule_day[]" value="{{ $day }}">
                                {{ $day }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-list-ol"></i> Number of Time Slots</label>
                            <select class="field-select" id="time_slots" name="time_slots">
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </div>

                    <div id="timeInputs"></div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Add Professor
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<!-- =============== EDIT PROFESSOR MODAL =============== -->
<div class="modal fade" id="editProfessorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-edit"></i> Edit Professor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/updateProfessor') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editProfessorId" name="professor_id">

                <div class="modal-body">
                    <div class="modal-section"><i class="fa fa-envelope"></i> Contact Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-envelope"></i> Email Address</label>
                        <input class="field-input" type="text" name="email" id="editEmail" placeholder="Enter email address">
                    </div>

                    <div class="modal-section"><i class="fa fa-book"></i> Subject Details</div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-tag"></i> Subject Code</label>
                            <input class="field-input" type="text" name="subject_code" id="editSubjectCode" placeholder="Subject code">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-align-left"></i> Subject Description</label>
                            <input class="field-input" type="text" name="subject_description" id="editSubjectDescription" placeholder="Subject description">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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

        // DataTable
        $('#profTable').DataTable();

        // Academic year end options
        const startYearSelect = document.getElementById('academic_year_start');
        const endYearSelect   = document.getElementById('academic_year_end');

        function updateEndYearOptions() {
            const selectedStart = parseInt(startYearSelect.value);
            endYearSelect.innerHTML = '<option value="">End Year</option>';
            for (let y = selectedStart + 1; y <= selectedStart + 10; y++) {
                const opt = document.createElement('option');
                opt.value = y; opt.textContent = y;
                endYearSelect.appendChild(opt);
            }
        }

        if (startYearSelect) {
            updateEndYearOptions();
            startYearSelect.addEventListener('change', updateEndYearOptions);
        }

        // Time slots dynamic inputs
        $('#time_slots').change(function () {
            const numSlots = parseInt($(this).val());
            $('#timeInputs').empty();
            if (numSlots > 0) {
                $('input[name="schedule_day[]"]:checked').each(function () {
                    const day = $(this).val();
                    for (let i = 1; i <= numSlots; i++) {
                        $('#timeInputs').append(`
                            <div style="margin-bottom:12px;">
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                    <div class="field-group">
                                        <label class="field-label"><i class="fa fa-clock"></i> Start Time ${i} (${day})</label>
                                        <input class="field-input" type="time" name="${day}_start_time_${i}">
                                    </div>
                                    <div class="field-group">
                                        <label class="field-label"><i class="fa fa-clock"></i> End Time ${i} (${day})</label>
                                        <input class="field-input" type="time" name="${day}_end_time_${i}">
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                });
            }
        });

        // Edit professor modal populate
        $(document).on('click', '.btnView1', function () {
            $('#editProfessorId').val($(this).data('professor-id'));
            $('#editEmail').val($(this).data('email'));
            $('#editSubjectCode').val($(this).data('subject-code'));
            $('#editSubjectDescription').val($(this).data('subject-description'));
        });

        // Remove professor with SweetAlert
        $(document).on('click', '.btnRemove', function () {
            const professorId = $(this).data('professor-id');
            Swal.fire({
                title: 'Remove Professor?',
                text: 'This will permanently remove the professor.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove!',
                cancelButtonText: 'Cancel',
                borderRadius: '16px',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/removeProfessor/' + professorId,
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            Swal.fire({
                                title: 'Removed!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#dc2626'
                            }).then(() => location.reload());
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });

    });
</script>

</body>
</html>