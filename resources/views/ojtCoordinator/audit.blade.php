<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Audit Log</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/coor_audit-responsive.css') }}">

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

        body.dark-mode { background: #000000; color: #e0e0e0; }
        body.dark-mode .main-content { background: #000000; }
        body.dark-mode .dashboard-footer { 
        background: #1a1a1a !important; 
        border-top: 1px solid #3a3a3a; 
        color: #999; 
        }
        body.dark-mode .dashboard-footer a { color: #999; }
        body.dark-mode .dashboard-footer a:hover { color: var(--red); }
        body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
        body.dark-mode .dashboard-footer .footer-copy { color: #999; }
        body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }
        body.dark-mode .dashboard-footer .footer-logo { opacity: 0.4; }

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

        /* ===== DARK MODE: COMPREHENSIVE STYLING ===== */
        body.dark-mode .topbar { background: #252525 !important; box-shadow: 0 2px 12px rgba(0,0,0,0.3) !important; }
        body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
        body.dark-mode .menu-toggle:hover { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        body.dark-mode .table-card { background: #2a2a2a; border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
        body.dark-mode .table-card-header { background: #333; border-bottom: 1px solid rgba(255,255,255,0.08); }
        body.dark-mode .table-card-header h2 { color: #fff; }
        body.dark-mode .table-card-header p { color: #999; }

        body.dark-mode table.dataTable thead th { background: #333; color: #aaa; border-bottom: 1px solid rgba(255,255,255,0.08); }
        body.dark-mode table.dataTable tbody td { color: #e0e0e0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        body.dark-mode table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.08); }

        body.dark-mode .field-input, body.dark-mode .field-select { background: #3a3a3a; color: #e0e0e0; border: 1.5px solid rgba(255,255,255,0.08); }
        body.dark-mode .field-input::placeholder { color: #777; }
        body.dark-mode .field-input:focus, body.dark-mode .field-select:focus { border-color: #ff6b6b; box-shadow: 0 0 0 3px rgba(220,38,38,0.15); }
        body.dark-mode .field-label { color: #e0e0e0; }

        body.dark-mode .modal-content { background: #2a2a2a; box-shadow: 0 20px 60px rgba(0,0,0,0.45); }
        body.dark-mode .modal-body { background: #2a2a2a; color: #e0e0e0; }
        body.dark-mode .modal-footer { background: #333; border-top: 1px solid rgba(255,255,255,0.08); }

        body.dark-mode .btn-primary { box-shadow: 0 4px 16px rgba(220,38,38,0.3); }
        body.dark-mode .btn-secondary { background: #3a3a3a; color: #e0e0e0; border: 1px solid rgba(255,255,255,0.08); }

        body.dark-mode .section-card { background: #2a2a2a; border: 1px solid rgba(255,255,255,0.08); }
        body.dark-mode .card { background: #2a2a2a; border: 1px solid rgba(255,255,255,0.08); color: #e0e0e0; }

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
        .darkmode-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 18px; transition: all 0.2s;
        }
        .darkmode-toggle:hover { background: #fee2e2; color: var(--red); }
        .darkmode-toggle:active { transform: scale(0.95); }
        body.dark-mode .darkmode-toggle {
            background: #3a3a3a; border-color: #555; color: #e8e8e8;
        }
        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2); color: #ff6b6b;
            border-color: rgba(220,38,38,0.3); box-shadow: 0 6px 16px rgba(220,38,38,0.3);
            transform: translateY(-2px);
        }
        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }
        body.dark-mode .topbar-title { color: #999; }
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

        body.dark-mode .topbar-badge {
            background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b;
        }

        body.dark-mode .topbar-badge {
            background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b;
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }
        body.dark-mode .page-content { background: #000000; }

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
        body.dark-mode .page-header h1 { color: #fff; }

        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        body.dark-mode .breadcrumb { color: #999; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* Stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .stat-icon.red   { background: #fee2e2; color: var(--red); }
        .stat-icon.blue  { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber { background: #fef9c3; color: #ca8a04; }

        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* Table card */
        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
            flex-wrap: wrap;
        }

        .table-card-header-left { display: flex; align-items: center; gap: 12px; }
        .table-card-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .header-icon {
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
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }
        .table-card-header .dataTables_filter { margin: 0; }
        .table-card-header .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #666;
            font-weight: 600;
        }

        .table-card-body { padding: 0; }

        /* DataTables overrides */
        .table-card-body .dataTables_wrapper {
            padding: 16px 22px;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
        }

        .audit-filters {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .audit-filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .audit-filter-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #666;
        }

        .audit-filter-select {
            min-width: 150px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 7px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            color: #333;
            background: #fff;
            outline: none;
        }

        .audit-filter-select:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }

        .audit-filter-date {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 7px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            color: #333;
            background: #fff;
            outline: none;
        }

        .audit-filter-date:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }

        .audit-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .audit-toolbar .dataTables_length {
            margin: 0;
        }

        .table-card-body table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        .table-card-body table.dataTable thead th {
            background: #fafafa;
            color: #555;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            border-top: none;
        }

        .table-card-body table.dataTable tbody td {
            padding: 14px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13.5px;
            vertical-align: middle;
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

        /* Action badges */
        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .action-create  { background: #dcfce7; color: #16a34a; }
        .action-update  { background: #dbeafe; color: #2563eb; }
        .action-delete  { background: #fee2e2; color: var(--red); }
        .action-approve { background: #dcfce7; color: #16a34a; }
        .action-deny    { background: #fee2e2; color: var(--red); }
        .action-default { background: #f3f4f6; color: #6b7280; }

        /* Module badge */
        .module-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 600;
            background: #fff5f5;
            color: var(--red);
            border: 1px solid #fecaca;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            text-transform: capitalize;
            background: #f3f4f6;
            color: #4b5563;
        }

        .role-student {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .role-coordinator {
            background: #fee2e2;
            color: var(--red);
        }

        .role-professor {
            background: #ede9fe;
            color: #7c3aed;
        }

        /* Name cell */
        .name-cell {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .name-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .name-text { font-size: 13px; font-weight: 600; color: #000; }

        .btn-view-desc {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #fecaca;
            background: #fff5f5;
            color: var(--red);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-view-desc:hover {
            background: #fee2e2;
            transform: translateY(-1px);
        }

        .desc-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 1200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .desc-modal.show {
            display: flex;
        }

        .desc-modal-card {
            width: min(640px, 100%);
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(0,0,0,0.25);
            overflow: hidden;
            border: 1px solid rgba(220,38,38,0.12);
        }

        .desc-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            background: linear-gradient(135deg, #991b1b 0%, #dc2626 58%, #ef4444 100%);
        }

        .desc-modal-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .desc-modal-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,0.16);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.14);
        }

        .desc-modal-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            margin-bottom: 3px;
        }

        .desc-modal-title {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
        }

        .desc-modal-close {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 10px;
            background: rgba(255,255,255,0.14);
            color: #fff;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .desc-modal-close:hover {
            background: rgba(255,255,255,0.22);
            transform: translateY(-1px);
        }

        .desc-modal-body {
            padding: 22px;
            background:
                linear-gradient(180deg, rgba(254,242,242,0.78) 0%, rgba(255,255,255,1) 34%);
        }

        .desc-modal-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .desc-modal-meta-item {
            background: linear-gradient(180deg, #fff 0%, #fff8f8 100%);
            border: 1px solid #f3d2d2;
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 8px 18px rgba(220,38,38,0.06);
        }

        .desc-modal-meta-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #b91c1c;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .desc-modal-meta-value {
            font-size: 13px;
            font-weight: 600;
            color: #222;
        }

        .desc-modal-content {
            background: #fff;
            border: 1px solid #f0d7d7;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(0,0,0,0.05);
        }

        .desc-modal-content-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            background: linear-gradient(90deg, #fff1f2 0%, #fff 100%);
            border-bottom: 1px solid #f3d2d2;
            color: #b91c1c;
            font-size: 13px;
            font-weight: 700;
        }

        .desc-modal-text {
            padding: 18px;
            line-height: 1.7;
            color: #333;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Date cell */
       .date-main { font-size: 13px; color: #000; font-weight: 500; }
.date-sub  { font-size: 11px; color: #000; margin-top: 2px; }

        /* Description cell */
       .desc-text {
    font-size: 13px;
    color: #000;
    max-width: 280px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

        /* Dark Mode: Date/Time, Name, Description */
        body.dark-mode .date-main { color: #fff !important; }
        body.dark-mode .date-sub { color: #fff !important; }
        body.dark-mode .name-text { color: #fff !important; }
        body.dark-mode .desc-text { color: #fff !important; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: #e5e5e5;
            margin-bottom: 16px;
            display: block;
        }

        .empty-state p {
            font-size: 14px;
            color: #aaa;
            font-style: italic;
        }

        /* Footer */
        .dashboard-footer {
            background: #fff;
            border-top: 1px solid #f0f0f0;
            color: #888;
            padding: 18px 28px;
            font-size: 12.5px;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .dashboard-footer .footer-left { display: flex; align-items: center; gap: 8px; }
        .dashboard-footer .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .dashboard-footer .footer-copy { font-size: 12.5px; color: #aaa; font-weight: 500; }
        .dashboard-footer .footer-copy span { color: var(--red); font-weight: 600; }
        .dashboard-footer .footer-links { display: flex; align-items: center; gap: 6px; }
        .dashboard-footer a { color: #888; text-decoration: none; font-weight: 500; font-size: 12.5px; transition: color 0.2s; }
        .dashboard-footer a:hover { color: var(--red); }
        .dashboard-footer .divider { color: #e5e5e5; margin: 0 2px; }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
            pointer-events: auto;
        }
        /* ===== DARK MODE: COMPREHENSIVE STYLING ===== */
        body.dark-mode .topbar { background: #252525 !important; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
        body.dark-mode .page-header h1 { color: #fff; }
        body.dark-mode .breadcrumb { color: #999; }

        /* Stat cards */
        body.dark-mode .stat-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        body.dark-mode .stat-num { color: #fff; }
        body.dark-mode .stat-name { color: #999; }

        /* Table cards */
        body.dark-mode .table-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
        body.dark-mode .table-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .table-card-header h2 { color: #fff; }
        body.dark-mode .table-card-header p { color: #999; }
        body.dark-mode .table-card-header .dataTables_filter label { color: #cfcfcf; }
        body.dark-mode .file-count-badge { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        /* DataTables */
        body.dark-mode table.dataTable thead th { background: #2a2a2a; color: #aaa; border-bottom: 1px solid #3a3a3a; }
body.dark-mode table.dataTable tbody td { color: #fff !important; border-bottom: 1px solid rgba(255,255,255,0.05); }
        body.dark-mode table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.1); }

        /* DataTables controls */
        body.dark-mode .dataTables_filter input { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }
        body.dark-mode .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.2) !important; }
        body.dark-mode .dataTables_length select { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }
        body.dark-mode .dataTables_paginate .paginate_button { background: #3a3a3a !important; border-color: #3a3a3a !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_paginate .paginate_button:hover { background: #444 !important; border-color: #444 !important; }
        body.dark-mode .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; }
        body.dark-mode .audit-filter-label { color: #cfcfcf; }
        body.dark-mode .audit-filter-select { background: #3a3a3a; color: #e0e0e0; border-color: #4a4a4a; }
        body.dark-mode .audit-filter-date { background: #3a3a3a; color: #e0e0e0; border-color: #4a4a4a; }

        /* Audit Log Table Cells */
        body.dark-mode .audit-date-cell { color: #fff !important; }
        body.dark-mode .dataTables_length select { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }

        /* Form elements */
        body.dark-mode .field-input, body.dark-mode .field-select { background: #3a3a3a; color: #e0e0e0; border: 1px solid #3a3a3a; }
        body.dark-mode .field-input:focus, body.dark-mode .field-select:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(220,38,38,0.2); }
        body.dark-mode .field-label { color: #e0e0e0; }

        /* Buttons */
        body.dark-mode .btn-download { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        body.dark-mode .btn-remove { border: 1.5px solid rgba(220,38,38,0.3); background: transparent; color: #ff6b6b; }
        body.dark-mode .btn-remove:hover { background: rgba(220,38,38,0.1); }

        /* Modal */
        body.dark-mode .modal-body { background: #1a1a1a; }
        body.dark-mode .modal-footer { background: #2a2a2a; border-top: 1px solid #3a3a3a; }
        body.dark-mode .btn-modal-close { background: #3a3a3a; border: 1px solid #3a3a3a; color: #e0e0e0; }
        body.dark-mode .btn-modal-close:hover { background: rgba(220,38,38,0.2); }
        body.dark-mode .btn-view-desc { background: rgba(220,38,38,0.15); border-color: rgba(220,38,38,0.25); color: #ff8a8a; }
        body.dark-mode .btn-view-desc:hover { background: rgba(220,38,38,0.24); }
        body.dark-mode .desc-modal-card { background: #2a2a2a; border-color: rgba(239,68,68,0.18); }
        body.dark-mode .desc-modal-header { background: linear-gradient(135deg, #5f0f0f 0%, #991b1b 55%, #b91c1c 100%); border-bottom: 1px solid rgba(255,255,255,0.08); }
        body.dark-mode .desc-modal-title { color: #fff; }
        body.dark-mode .desc-modal-close { background: rgba(255,255,255,0.12); color: #e0e0e0; }
        body.dark-mode .desc-modal-body { background: linear-gradient(180deg, rgba(127,29,29,0.28) 0%, #2a2a2a 32%); }
        body.dark-mode .desc-modal-meta-item { background: linear-gradient(180deg, #333 0%, #2d2d2d 100%); border-color: #454545; box-shadow: none; }
        body.dark-mode .desc-modal-meta-label { color: #fca5a5; }
        body.dark-mode .desc-modal-meta-value,
        body.dark-mode .desc-modal-text { color: #e0e0e0; }
        body.dark-mode .desc-modal-content { background: #2f2f2f; border-color: #454545; box-shadow: none; }
        body.dark-mode .desc-modal-content-head { background: linear-gradient(90deg, rgba(127,29,29,0.38) 0%, #2f2f2f 100%); border-bottom: 1px solid #454545; color: #fca5a5; }

        /* File dropzone */
        body.dark-mode .file-dropzone { border-color: #3a3a3a; background: #2a2a2a; }
        body.dark-mode .file-dropzone:hover, body.dark-mode .file-dropzone.dragover { border-color: var(--red); background: rgba(220,38,38,0.1); }
        body.dark-mode .file-dropzone-icon { background: rgba(220,38,38,0.2); }
        body.dark-mode .file-dropzone-title { color: #e0e0e0; }
        body.dark-mode .file-dropzone-sub { color: #999; }

        /* Footer */
        /* Footer - Dark Mode */
body.dark-mode .dashboard-footer {
    background: #1a1a1a;
    border-top: 1px solid #3a3a3a;
    color: #999;
}

body.dark-mode .dashboard-footer a {
    color: #999;
}

body.dark-mode .dashboard-footer a:hover {
    color: var(--red);
}

body.dark-mode .dashboard-footer .divider {
    color: #3a3a3a;
}

body.dark-mode .dashboard-footer .footer-copy span {
    color: var(--red);
}

body.dark-mode .dashboard-footer .footer-logo {
    opacity: 0.4;
}

        /* Cards */
        body.dark-mode .card { background: #2a2a2a; border: 1px solid #3a3a3a; }

        @media (max-width: 900px) {
            .sidebar {
                width: var(--sidebar-w);
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .desc-text { max-width: 160px; }
            .desc-modal-meta { grid-template-columns: 1fr 1fr; }
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
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            <i class="fa fa-user-tie"></i>
        </div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name }}</span>
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
            <span class="nav-icon"><i class="fa fa-folder-open"></i></span>
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
        <a href="{{ url('/auditlog') }}" class="nav-item active">
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
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">
                On-the-Job Training <span>Information Management System</span>
            </span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-user-shield"></i>
                OJT Coordinator
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Audit <span>Log</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Audit Log</span>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-clipboard-list"></i></div>
                <div>
                    <div class="stat-num">{{ count($logs) }}</div>
                    <div class="stat-name">Total Logs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-plus-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $logs->where('action', 'create')->count() }}</div>
                    <div class="stat-name">Create Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-edit"></i></div>
                <div>
                    <div class="stat-num">{{ $logs->where('action', 'update')->count() }}</div>
                    <div class="stat-name">Update Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-trash"></i></div>
                <div>
                    <div class="stat-num">{{ $logs->where('action', 'delete')->count() }}</div>
                    <div class="stat-name">Delete Actions</div>
                </div>
            </div>
        </div>

        <!-- Audit Log Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-clipboard-list"></i></div>
                    <div>
                        <h2>System Activity Logs</h2>
                        <p>Track all actions performed within the InternConnect system</p>
                    </div>
                </div>
                <div class="table-card-header-right" id="auditTableHeaderSearch"></div>
            </div>

            <div class="table-card-body">

                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                            if (settings.nTable.id !== 'auditTable') {
                                return true;
                            }

                            const rowNode = settings.aoData[dataIndex]?.nTr;
                            if (!rowNode) {
                                return true;
                            }

                            const selectedAction = ($('#actionFilter').val() || '').toLowerCase();
                            const selectedRole = ($('#roleFilter').val() || '').toLowerCase();
                            const selectedMonth = $('#monthFilter').val();
                            const selectedYear = $('#yearFilter').val();
                            const rowAction = (rowNode.getAttribute('data-action') || '').toLowerCase();
                            const rowRole = (rowNode.getAttribute('data-role') || '').toLowerCase();
                            const rowMonth = rowNode.getAttribute('data-month') || '';
                            const rowYear = rowNode.getAttribute('data-year') || '';

                            if (selectedAction && rowAction !== selectedAction) {
                                return false;
                            }

                            if (selectedRole && rowRole !== selectedRole) {
                                return false;
                            }

                            if (selectedMonth && rowMonth !== selectedMonth) {
                                return false;
                            }

                            if (selectedYear && rowYear !== selectedYear) {
                                return false;
                            }

                            return true;
                        });

                        const table = $('#auditTable').DataTable({
                            "order": [[0, 'desc']],
                            "pageLength": 10,
                            "scrollX": true,
                            "autoWidth": false,
                            "dom": '<"audit-toolbar"lf>rtip',
                        });

                        const $toolbar = $('#auditTable_wrapper .audit-toolbar');
                        const $length = $toolbar.find('.dataTables_length');
                        const $filter = $toolbar.find('.dataTables_filter');
                        const $customFilters = $('#auditFilters');
                        const $headerSearch = $('#auditTableHeaderSearch');

                        $customFilters.insertAfter($length);
                        $headerSearch.append($filter);

                        $('#actionFilter, #roleFilter, #monthFilter, #yearFilter').on('change', function () {
                            table.draw();
                        });

                        $(document).on('click', '.btn-view-desc', function () {
                            const modal = document.getElementById('descModal');
                            document.getElementById('descModalAction').textContent = $(this).data('action') || 'N/A';
                            document.getElementById('descModalRole').textContent = $(this).data('role') || 'Unknown';
                            document.getElementById('descModalModule').textContent = $(this).data('module') || 'N/A';
                            document.getElementById('descModalDate').textContent = $(this).data('datetime') || 'N/A';
                            document.getElementById('descModalText').textContent = $(this).data('description') || 'No description available.';
                            modal.classList.add('show');
                            document.body.style.overflow = 'hidden';
                        });

                        function closeDescModal() {
                            $('#descModal').removeClass('show');
                            document.body.style.overflow = '';
                        }

                        $('#descModalClose').on('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            closeDescModal();
                        });

                        $('#descModal').on('click', function (event) {
                            if (event.target === this) {
                                closeDescModal();
                            }
                        });

                        $(document).on('keydown', function (event) {
                            if (event.key === 'Escape' && $('#descModal').hasClass('show')) {
                                closeDescModal();
                            }
                        });
                    });
                </script>

                @if($logs->isEmpty())
                    <div class="empty-state">
                        <i class="fa fa-clipboard-list"></i>
                        <p>No audit logs recorded yet.</p>
                    </div>
                @else
                <div class="audit-filters" id="auditFilters">
                    <div class="audit-filter-group">
                        <label for="actionFilter" class="audit-filter-label">Action</label>
                        <select id="actionFilter" class="audit-filter-select">
                            <option value="">All Actions</option>
                            @foreach($logs->pluck('action')->filter()->map(fn ($action) => strtolower(trim($action)))->unique()->sort()->values() as $actionOption)
                                <option value="{{ $actionOption }}">{{ ucfirst($actionOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="audit-filter-group">
                        <label for="roleFilter" class="audit-filter-label">Role</label>
                        <select id="roleFilter" class="audit-filter-select">
                            <option value="">All Roles</option>
                            @foreach($logs->pluck('user_role')->filter()->map(fn ($role) => strtolower(trim($role)))->unique()->sort()->values() as $roleOption)
                                <option value="{{ $roleOption }}">{{ ucfirst($roleOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="audit-filter-group">
                        <label for="monthFilter" class="audit-filter-label">Month</label>
                        <select id="monthFilter" class="audit-filter-date">
                            <option value="">All Months</option>
                            @foreach([
                                '01' => 'January',
                                '02' => 'February',
                                '03' => 'March',
                                '04' => 'April',
                                '05' => 'May',
                                '06' => 'June',
                                '07' => 'July',
                                '08' => 'August',
                                '09' => 'September',
                                '10' => 'October',
                                '11' => 'November',
                                '12' => 'December',
                            ] as $monthValue => $monthLabel)
                                <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="audit-filter-group">
                        <label for="yearFilter" class="audit-filter-label">Year</label>
                        <select id="yearFilter" class="audit-filter-date">
                            <option value="">All Years</option>
                            @foreach($logs->pluck('created_at')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('Y'))->unique()->sortDesc()->values() as $yearOption)
                                <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <table id="auditTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>Action</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Module</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr
                            data-action="{{ strtolower(trim((string) $log->action)) }}"
                            data-role="{{ strtolower(trim((string) $log->user_role)) }}"
                            data-month="{{ \Carbon\Carbon::parse($log->created_at)->format('m') }}"
                            data-year="{{ \Carbon\Carbon::parse($log->created_at)->format('Y') }}"
                        >
                            <td class="audit-date-cell" data-order="{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}">
                                <div class="date-main">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}
                                </div>
                                <div class="date-sub">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $action = strtolower($log->action);
                                    $actionClass = match($action) {
                                        'create'  => 'action-create',
                                        'update'  => 'action-update',
                                        'delete'  => 'action-delete',
                                        'approve' => 'action-approve',
                                        'deny'    => 'action-deny',
                                        default   => 'action-default',
                                    };
                                    $actionIcon = match($action) {
                                        'create'  => 'fa-plus-circle',
                                        'update'  => 'fa-edit',
                                        'delete'  => 'fa-trash',
                                        'approve' => 'fa-check-circle',
                                        'deny'    => 'fa-times-circle',
                                        default   => 'fa-circle',
                                    };
                                @endphp
                                <span class="action-badge {{ $actionClass }}">
                                    <i class="fa {{ $actionIcon }}"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="audit-name-cell">
                                <div class="name-cell">
                                    <div class="name-avatar">
                                        {{ strtoupper(substr($log->user_name ?? 'N', 0, 1)) }}
                                    </div>
                                    <span class="name-text">{{ $log->user_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $role = strtolower(trim((string) ($log->user_role ?? 'unknown')));
                                    $roleClass = match($role) {
                                        'student' => 'role-student',
                                        'ojt coordinator', 'coordinator' => 'role-coordinator',
                                        'professor' => 'role-professor',
                                        default => '',
                                    };
                                @endphp
                                <span class="role-badge {{ $roleClass }}">
                                    <i class="fa fa-user-shield"></i>
                                    {{ $log->user_role ?: 'Unknown' }}
                                </span>
                            </td>
                            <td>
                                <span class="module-badge">
                                    <i class="fa fa-tag"></i>
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="audit-desc-cell">
                                <button
                                    type="button"
                                    class="btn-view-desc"
                                    data-action="{{ ucfirst($log->action) }}"
                                    data-role="{{ $log->user_role ?: 'Unknown' }}"
                                    data-module="{{ $log->module }}"
                                    data-datetime="{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}"
                                    data-description="{{ $log->description }}"
                                >
                                    <i class="fa fa-eye"></i>
                                    View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

            </div>
        </div>

    </div>

    <div class="desc-modal" id="descModal">
        <div class="desc-modal-card">
            <div class="desc-modal-header">
                <div class="desc-modal-title">Audit Log Description</div>
                <button type="button" class="desc-modal-close" id="descModalClose">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="desc-modal-body">
                <div class="desc-modal-meta">
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Action</div>
                        <div class="desc-modal-meta-value" id="descModalAction">N/A</div>
                    </div>
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Role</div>
                        <div class="desc-modal-meta-value" id="descModalRole">Unknown</div>
                    </div>
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Module</div>
                        <div class="desc-modal-meta-value" id="descModalModule">N/A</div>
                    </div>
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Date / Time</div>
                        <div class="desc-modal-meta-value" id="descModalDate">N/A</div>
                    </div>
                </div>
                <div class="desc-modal-text" id="descModalText"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
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

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            const shouldOpen = !sidebar.classList.contains('mobile-open');
            sidebar.classList.toggle('mobile-open', shouldOpen);
            overlay.classList.toggle('active', shouldOpen);
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    const closeMobileSidebar = function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    };

    ['click', 'touchstart'].forEach(function (eventName) {
        document.addEventListener(eventName, function (event) {
            if (window.innerWidth > 900 || !sidebar.classList.contains('mobile-open')) {
                return;
            }

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedMenuToggle = menuToggle.contains(event.target);

            if (!clickedInsideSidebar && !clickedMenuToggle) {
                closeMobileSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileSidebar();
        }
    });
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>

<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
