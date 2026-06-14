<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
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
        .menu-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 18px; transition: all 0.2s;
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

        .btn-add {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220,38,38,0.35); }

        /* Stats */
        .stats-row {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px; margin-bottom: 22px;
        }
        .stat-card {
            background: #fff; border-radius: 14px; padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04);
        }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }
        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* Table card */
        .table-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04); overflow: hidden;
        }
        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0; background: #fafafa; flex-wrap: wrap;
        }
        .table-card-header-left { display: flex; align-items: center; gap: 12px; }
        .header-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: #fee2e2; display: flex; align-items: center;
            justify-content: center; color: var(--red); font-size: 15px; flex-shrink: 0;
        }
        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }
        .count-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fee2e2; color: var(--red);
            border-radius: 20px; padding: 5px 14px;
            font-size: 12.5px; font-weight: 700;
        }

        /* DataTables */
        .table-card-body .dataTables_wrapper { padding: 16px 22px; font-family: 'Poppins', sans-serif; font-size: 13px; }
        
        /* Horizontal scroll container for mobile */
        .table-card-body {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }
        .table-card-body table.dataTable thead th {
            background: #fafafa; color: #555; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0; border-top: none;
        }
        
        /* Sticky first column header */
        .table-card-body table.dataTable thead th:first-child {
            position: sticky;
            left: 0;
            z-index: 10;
        }
        .table-card-body table.dataTable tbody td {
            padding: 13px 12px; color: #333;
            border-bottom: 1px solid #f9f9f9; font-size: 13px; vertical-align: middle;
        }
        
        /* Sticky first column body */
        .table-card-body table.dataTable tbody td:first-child {
            position: sticky;
            left: 0;
            z-index: 9;
            background: #fff;
        }
        
        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:hover td:first-child { background: #fff5f5; }
        
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }
        .dataTables_filter label { display: inline-flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 500; color: #555; }
        .dataTables_filter input {
            border: 1px solid #e5e5e5 !important; border-radius: 8px !important;
            padding: 6px 12px !important; font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important; outline: none !important;
        }
        .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important; }
        .dataTables_length select { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 4px 8px !important; font-family: 'Poppins', sans-serif !important; }
        .dataTables_paginate .paginate_button { border-radius: 6px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; }
        .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        .dataTables_paginate .paginate_button:hover { background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important; }

        /* Company cell */
        .company-cell { display: flex; align-items: center; gap: 10px; }
        .company-icon-box {
            width: 34px; height: 34px; border-radius: 9px;
            background: #fee2e2; display: flex; align-items: center;
            justify-content: center; color: var(--red); font-size: 13px; flex-shrink: 0;
        }
        .company-name-text { font-weight: 600; color: #1a1a1a; font-size: 13px; }

        /* Student list pills */
        .student-pill {
            display: inline-flex; align-items: center;
            background: #f0fdf4; color: #16a34a;
            border-radius: 20px; padding: 2px 9px;
            font-size: 11px; font-weight: 600;
            margin: 2px 2px 0 0;
        }

        /* Status badge */
        .status-active {
            display: inline-flex; align-items: center; gap: 5px;
            background: #dcfce7; color: #16a34a;
            border-radius: 20px; padding: 4px 10px;
            font-size: 11.5px; font-weight: 600;
        }

        /* Action buttons */
        .actions-wrap {
            display: grid;
            grid-template-columns: repeat(3, 34px);
            gap: 6px;
            justify-content: start;
            min-width: 114px;
        }

        .btn-action-icon {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 9px; border: 1.5px solid transparent;
            font-size: 13px; cursor: pointer; transition: all 0.2s;
            text-decoration: none;
            background: #fff;
        }

        .btn-action-icon:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .btn-view {
            border-color: #e0e7ff; color: #4f46e5; background: #fff;
        }
        .btn-view:hover { background: #eef2ff; color: #4f46e5; }

        .btn-edit {
            border-color: #dbeafe; color: #2563eb; background: #fff;
        }
        .btn-edit:hover { background: #eff6ff; color: #2563eb; }

        .btn-download {
            border-color: #bfdbfe;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 2px 6px rgba(37,99,235,0.2);
        }
        .btn-download:hover { box-shadow: 0 4px 10px rgba(37,99,235,0.3); color: #fff; }

        .btn-send {
            border-color: #d1fae5; color: #059669; background: #fff;
        }
        .btn-send:hover { background: #ecfdf5; color: #059669; }

        .btn-print {
            border-color: #fef3c7; color: #d97706; background: #fff;
        }
        .btn-print:hover { background: #fffbeb; color: #d97706; }

        .btn-remove {
            border-color: #fecaca; color: #dc2626; background: #fff;
        }
        .btn-remove:hover { background: #fef2f2; color: #dc2626; }

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
        .modal-title { color: #fff; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }
        .modal-body { padding: 24px; background: #fff; max-height: 520px; overflow-y: auto; }
        .modal-body::-webkit-scrollbar { width: 4px; }
        .modal-body::-webkit-scrollbar-thumb { background: #fecaca; border-radius: 10px; }

        .field-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
        .field-group:last-child { margin-bottom: 0; }
        .field-label { font-size: 12px; font-weight: 600; color: #444; display: flex; align-items: center; gap: 5px; }
        .field-label i { color: var(--red); font-size: 11px; }
        .field-input, .field-select {
            width: 100%; background: #fafafa; border: 1.5px solid #e8e8e8;
            border-radius: 10px; color: #1a1a1a;
            font-family: 'Poppins', sans-serif; font-size: 13px;
            padding: 10px 13px; outline: none; transition: all 0.25s;
        }
        .field-input:focus, .field-select:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .year-row { display: flex; align-items: center; gap: 10px; }
        .year-row span { font-weight: 700; color: #aaa; flex-shrink: 0; }
        .year-row .field-input { flex: 1; }

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
            padding: 14px 24px; display: flex; justify-content: flex-end; gap: 10px;
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

        /* Print modal iframe */
        .scrollable-modal-body { height: 520px; padding: 0; overflow: hidden; }
        .scrollable-modal-body iframe { width: 100%; height: 100%; border: none; }

        /* Mobile overlay */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }

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
        body.dark-mode .file-count-badge { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        /* DataTables */
        body.dark-mode table.dataTable thead th { background: #2a2a2a; color: #aaa; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode table.dataTable thead th:first-child { background: #2a2a2a; }
        body.dark-mode table.dataTable tbody td { color: #e0e0e0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        body.dark-mode table.dataTable tbody td:first-child { background: #2a2a2a; }
        body.dark-mode table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.1); }
        body.dark-mode table.dataTable tbody tr:hover td:first-child { background: rgba(220,38,38,0.1); }

        /* DataTables controls */
        body.dark-mode .dataTables_filter label { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_filter input { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }
        body.dark-mode .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.2) !important; }
        body.dark-mode .dataTables_length select { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }
        body.dark-mode .dataTables_paginate .paginate_button { background: #3a3a3a !important; border-color: #3a3a3a !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_paginate .paginate_button:hover { background: #444 !important; border-color: #444 !important; }
        body.dark-mode .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; }

        /* Form elements */
        body.dark-mode .field-input, body.dark-mode .field-select { background: #3a3a3a; color: #e0e0e0; border: 1px solid #3a3a3a; }
        body.dark-mode .field-input:focus, body.dark-mode .field-select:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(220,38,38,0.2); }
        body.dark-mode .field-label { color: #e0e0e0; }

        /* Buttons */
        body.dark-mode .btn-view { background: #252525; border-color: rgba(99,102,241,0.3); color: #c7d2fe; }
        body.dark-mode .btn-view:hover { background: rgba(79,70,229,0.2); }
        body.dark-mode .btn-edit { background: #252525; border-color: rgba(59,130,246,0.3); color: #93c5fd; }
        body.dark-mode .btn-edit:hover { background: rgba(37,99,235,0.2); }
        body.dark-mode .btn-download { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        body.dark-mode .btn-send { background: #252525; border-color: rgba(16,185,129,0.3); color: #6ee7b7; }
        body.dark-mode .btn-send:hover { background: rgba(16,185,129,0.16); }
        body.dark-mode .btn-print { background: #252525; border-color: rgba(245,158,11,0.3); color: #fbbf24; }
        body.dark-mode .btn-print:hover { background: rgba(245,158,11,0.16); }
        body.dark-mode .btn-remove { border: 1.5px solid rgba(220,38,38,0.3); background: #252525; color: #ff6b6b; }
        body.dark-mode .btn-remove:hover { background: rgba(220,38,38,0.1); }

        /* Modal */
        body.dark-mode .modal-body { background: #1a1a1a; }
        body.dark-mode .modal-footer { background: #2a2a2a; border-top: 1px solid #3a3a3a; }
        body.dark-mode .btn-modal-close { background: #3a3a3a; border: 1px solid #3a3a3a; color: #e0e0e0; }
        body.dark-mode .btn-modal-close:hover { background: rgba(220,38,38,0.2); }

        /* File dropzone */
        body.dark-mode .file-dropzone { border-color: #3a3a3a; background: #2a2a2a; }
        body.dark-mode .file-dropzone:hover, body.dark-mode .file-dropzone.dragover { border-color: var(--red); background: rgba(220,38,38,0.1); }
        body.dark-mode .file-dropzone-icon { background: rgba(220,38,38,0.2); }
        body.dark-mode .file-dropzone-title { color: #e0e0e0; }
        body.dark-mode .file-dropzone-sub { color: #999; }

        /* Footer */
        body.dark-mode .dashboard-footer { background: #1a1a1a; border-top: 1px solid #3a3a3a; color: #999; }
        body.dark-mode .dashboard-footer a { color: #999; }
        body.dark-mode .dashboard-footer a:hover { color: var(--red); }
        body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
        body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }
        /* Dark mode toggle button response */
body.dark-mode .topbar { background: #252525 !important; border-bottom: 1px solid #3a3a3a; }
body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }

/* Company name & table text */
body.dark-mode .company-name-text { color: #fff !important; }
body.dark-mode table.dataTable tbody td { color: #e0e0e0 !important; }

/* Footer dark mode */
body.dark-mode .dashboard-footer { background: #1a1a1a !important; border-top: 1px solid #3a3a3a; color: #999; }
body.dark-mode .dashboard-footer a { color: #999; }
body.dark-mode .dashboard-footer a:hover { color: var(--red); }
body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
body.dark-mode .dashboard-footer .footer-copy { color: #999; }
body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }
body.dark-mode .dashboard-footer .footer-logo { opacity: 0.4; }

/* Action buttons in dark mode */
body.dark-mode .btn-view { background: transparent; border-color: rgba(99,102,241,0.4); color: #a5b4fc; }
body.dark-mode .btn-view:hover { background: rgba(99,102,241,0.15); }
body.dark-mode .btn-send { background: transparent; border-color: rgba(5,150,105,0.4); color: #6ee7b7; }
body.dark-mode .btn-send:hover { background: rgba(5,150,105,0.15); }
body.dark-mode .btn-print { background: transparent; border-color: rgba(217,119,6,0.4); color: #fcd34d; }
body.dark-mode .btn-print:hover { background: rgba(217,119,6,0.15); }

/* Modal dark mode */
body.dark-mode .modal-content { background: #2a2a2a; }
body.dark-mode .modal-body { background: #1a1a1a !important; color: #e0e0e0; }
body.dark-mode .modal-footer { background: #2a2a2a !important; border-top: 1px solid #3a3a3a; }
body.dark-mode .modal-section { color: #777; border-bottom-color: #3a3a3a; }
body.dark-mode .field-label { color: #e0e0e0 !important; }
body.dark-mode .field-input, body.dark-mode .field-select { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1.5px solid #4a4a4a !important; }
body.dark-mode .field-input::placeholder { color: #777 !important; }
body.dark-mode #send-company-banner { background: rgba(220,38,38,0.1) !important; border-color: rgba(220,38,38,0.3) !important; }
body.dark-mode #send-company-name { color: #fff !important; }

/* Count badge */
body.dark-mode .count-badge { background: rgba(220,38,38,0.2); color: #ff6b6b; }

/* Student pills */
body.dark-mode .student-pill { background: rgba(22,163,74,0.15); color: #6ee7b7; }

/* Status badge */
body.dark-mode .status-active { background: rgba(22,163,74,0.15); color: #6ee7b7; }

        /* Cards */
        body.dark-mode .card { background: #2a2a2a; border: 1px solid #3a3a3a; }

        @media (max-width: 900px) {
            .sidebar { width: var(--sidebar-w); transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .field-row { grid-template-columns: 1fr; }

            /* Mobile table scrolling */
            .table-card-body {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                min-width: 0;
            }

            .table-card-body table.dataTable {
                min-width: 700px;
            }

            .table-card-body .dataTables_wrapper {
                padding: 12px 16px;
            }
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
            <span class="sidebar-brand-sub">OJTIMS</span>
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
        <li>
    <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
        </a>
</li>
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

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Memorandum of <span>Agreement</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>MOA</span>
                </div>
            </div>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                <i class="fa fa-plus"></i> Add New Company
            </button>
        </div>

        <!-- Stats Row -->
        @php $totalCompanies = count($companies); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalCompanies }}</div>
                    <div class="stat-name">Partner Companies</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $totalCompanies }}</div>
                    <div class="stat-name">Active MOAs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ collect($companies)->sum(fn($c) => $c->students->count()) }}</div>
                    <div class="stat-name">Assigned Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">MOA</div>
                    <div class="stat-name">Document Type</div>
                </div>
            </div>
        </div>

        <!-- Companies Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-contract"></i></div>
                    <div>
                        <h2>Companies</h2>
                        <p>All partner companies with MOA agreements</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <form action="{{ url('/MOA') }}" method="GET" style="display:flex; align-items:center; gap:8px;">
                        <select name="school_year" class="field-select" style="min-width:170px; height:36px; font-size:12px;">
                            <option value="">All School Years</option>
                            @foreach ($schoolYears as $schoolYear)
                                <option value="{{ $schoolYear }}" {{ ($selectedSchoolYear ?? '') === $schoolYear ? 'selected' : '' }}>
                                    {{ $schoolYear }}
                                </option>
                            @endforeach
                        </select>
                        <select name="course" class="field-select" style="min-width:220px; height:36px; font-size:12px;">
                            <option value="">All Courses</option>
                            @foreach ($course as $courseItem)
                                <option value="{{ $courseItem->course }}" {{ ($selectedCourse ?? '') === $courseItem->course ? 'selected' : '' }}>
                                    {{ $courseItem->course }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-modal-submit" style="height:36px; padding:0 14px; font-size:12px;">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        @if (!empty($selectedCourse) || !empty($selectedSchoolYear))
                            <a href="{{ url('/MOA') }}" class="btn-modal-close" style="height:36px; padding:0 14px; font-size:12px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                                <i class="fa fa-times"></i> Clear
                            </a>
                        @endif
                    </form>
                    <div class="count-badge">
                        <i class="fa fa-building"></i>
                        {{ $totalCompanies }} {{ $totalCompanies == 1 ? 'company' : 'companies' }}
                    </div>
                </div>
            </div>

            <div class="table-card-body">
                <table id="companyTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="display:none;">ID</th>
                            <th>Company Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>School Year</th>
                            <th>Course</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        @php
                            $displayStudents = collect(array_filter(array_map('trim', explode(',', (string) ($company->student_names_display ?? '')))));
                            $linkedStudentNames = $company->students->pluck('full_name')->filter()->values();
                            $manualStudentNames = $displayStudents->diff($linkedStudentNames)->values();
                            [$schoolYearStart, $schoolYearEnd] = array_pad(explode('-', (string) ($company->school_year ?? '')), 2, '');
                        @endphp
                        <tr>
                            <td style="display:none;">{{ $company->id }}</td>

                            <!-- Company Name -->
                            <td>
                                <div class="company-cell">
                                    <div class="company-icon-box">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <span class="company-name-text">{{ $company->company_name }}</span>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-phone" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->companyNo ?: '—' }}
                                </div>
                            </td>

                            <!-- Email -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-envelope" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->company_email ?: '—' }}
                                </div>
                            </td>

                            <!-- School Year -->
                            <td>
                                <span style="font-size:13px; color:#555;">{{ $company->school_year }}</span>
                            </td>

                            <!-- Course -->
                            <td>
                                <span style="font-size:13px; color:#555;">{{ $company->course ?: '—' }}</span>
                            </td>

                            <!-- Students -->
                            <td>
                                @if ($displayStudents->isNotEmpty())
                                    @foreach ($displayStudents as $displayStudent)
                                        <span class="student-pill">{{ $displayStudent }}</span>
                                    @endforeach
                                @else
                                    @forelse ($company->students as $student)
                                        <span class="student-pill">{{ $student->full_name }}</span>
                                    @empty
                                        <span style="color:#aaa; font-size:12px;">—</span>
                                    @endforelse
                                @endif
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="status-active">
                                    <i class="fa fa-circle" style="font-size:7px;"></i> Active
                                </span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">

                                    <!-- View -->
                                    <a class="btn-action-icon btn-view"
                                       href="{{ route('moa.view', ['companyId' => $company->id]) }}"
                                       title="View MOA"
                                       aria-label="View MOA">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                    <button type="button"
                                        class="btn-action-icon btn-edit btn-open-edit"
                                        title="Edit MOA"
                                        aria-label="Edit MOA"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCompanyModal"
                                        onclick="openEditCompanyModal({{ $company->id }})">
                                        <i class="fa fa-pen"></i>
                                    </button>

                                    <!-- Download -->
                                    <a class="btn-action-icon btn-download"
                                       href="{{ url('/moa/download', $company->file) }}"
                                       title="Download MOA"
                                       aria-label="Download MOA">
                                        <i class="fa fa-download"></i>
                                    </a>

                                    <!-- Send -->
                                    <button class="btn-action-icon btn-send btn-open-send"
                                        data-file-id="{{ $company->id }}"
                                        data-company-name="{{ $company->company_name }}"
                                        title="Send MOA"
                                        aria-label="Send MOA"
                                        data-bs-toggle="modal"
                                        data-bs-target="#sendModal">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>

                                    <!-- Print -->
                                    @if($company->file)
                                        <button class="btn-action-icon btn-print"
                                            title="Print MOA"
                                            aria-label="Print MOA"
                                            onclick="printUploadedMoa('{{ asset('assets/' . $company->file) }}')">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    @else
                                        <button class="btn-action-icon btn-print" disabled title="No MOA file available">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    @endif

                                    <!-- Remove -->
                                    <button type="button" class="btn-action-icon btn-remove"
                                        title="Remove MOA"
                                        aria-label="Remove MOA"
                                        onclick="confirmRemove({{ $company->id }}, '{{ addslashes($company->company_name) }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                    <form id="remove-form-{{ $company->id }}" action="{{ url('/moa/remove/' . $company->id) }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>

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

<!-- =============== ADD COMPANY MODAL =============== -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-building"></i> Add New Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/companyCreate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="modal-section"><i class="fa fa-building"></i> Company Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-building"></i> Company Name <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="text" name="company_name" placeholder="e.g. Acme Corporation" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-map-marker-alt"></i> Company Address <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="text" name="company_address" placeholder="Full address" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-user-tie"></i> Company Representative <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="text" name="company_rep" placeholder="Representative name" required>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-phone"></i> Contact Number <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input class="field-input" type="text" name="companyNo" placeholder="e.g. 09XX-XXX-XXXX">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-envelope"></i> Email Address <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input class="field-input" type="email" name="company_email" placeholder="company@email.com">
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-calendar-alt"></i> Academic Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year <span style="color:var(--red);">*</span></label>
                        <div class="year-row">
                            <input class="field-input" type="text" name="school_year_start" placeholder="Start Year" required>
                            <span>–</span>
                            <input class="field-input" type="text" name="school_year_end" placeholder="End Year" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-graduation-cap"></i> Course <span style="color:var(--red);">*</span></label>
                        <select name="course" id="moaCourseSelect" class="field-select" required>
                            <option value="" disabled selected>Select course</option>
                            @foreach ($course as $courseItem)
                                <option value="{{ $courseItem->course }}">{{ $courseItem->course }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-section"><i class="fa fa-paperclip"></i> MOA Document</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-file-pdf"></i> Attach MOA File <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="file" name="file" data-max-size-mb="2" required style="padding:8px 13px;">
                        <div style="margin-top:6px; font-size:12px; color:#777;">
                            PDF only | Max file size: 2 MB
                        </div>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                    <div class="modal-section"><i class="fa fa-users"></i> Assign Students</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-user-graduate"></i> Student Names <span style="color:#aaa; font-weight:400;">(Optional, hold Ctrl to select multiple)</span></label>
                        <input
                            id="moaStudentSearch"
                            class="field-input"
                            type="text"
                            placeholder="Search student name"
                            style="margin-bottom:8px;"
                            disabled
                        >
                        <select name="student_names[]" id="moaStudentSelect" class="field-select" multiple style="min-height:100px;" disabled>
                            @foreach ($stu as $student)
                                <option value="{{ $student->full_name }}" data-course="{{ strtolower(trim($student->course ?? '')) }}">{{ $student->full_name }}</option>
                            @endforeach
                        </select>
                        <div id="moaStudentHint" style="margin-top:6px; font-size:12px; color:#888;">Select a course first to show matching students.</div>
                    </div>

                    <div class="field-group" style="margin-top:10px;">
                        <label class="field-label"><i class="fa fa-keyboard"></i> Manual Student Input <span style="color:#aaa; font-weight:400;">(Optional, comma or new line separated)</span></label>
                        <textarea
                            id="manualStudentInput"
                            name="manual_student_names"
                            class="field-input"
                            rows="3"
                            placeholder="Type student names separated by comma or new line"
                            style="resize:vertical; min-height:88px;"
                        ></textarea>
                        <div style="font-size:12px; color:#888; margin-top:8px;">Use this for students without accounts. Input is also shown in the MOA list.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $companyEditPayload = $companies->mapWithKeys(function ($companyItem) {
        $displayStudents = collect(array_filter(array_map('trim', explode(',', (string) ($companyItem->student_names_display ?? '')))));
        $linkedStudentNames = $companyItem->students->pluck('full_name')->filter()->values();
        $manualStudentNames = $displayStudents->diff($linkedStudentNames)->values();
        [$schoolYearStart, $schoolYearEnd] = array_pad(explode('-', (string) ($companyItem->school_year ?? '')), 2, '');

        return [
            $companyItem->id => [
                'id' => $companyItem->id,
                'company_name' => $companyItem->company_name,
                'company_address' => $companyItem->company_address,
                'company_rep' => $companyItem->company_rep,
                'company_no' => $companyItem->companyNo,
                'company_email' => $companyItem->company_email,
                'school_year_start' => trim((string) $schoolYearStart),
                'school_year_end' => trim((string) $schoolYearEnd),
                'course' => $companyItem->course,
                'selected_students' => $linkedStudentNames->all(),
                'manual_students' => $manualStudentNames->all(),
            ],
        ];
    });
@endphp

<!-- =============== EDIT COMPANY MODAL =============== -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-pen"></i> Edit MOA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCompanyForm" action="" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="modal-section"><i class="fa fa-building"></i> Company Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-building"></i> Company Name <span style="color:var(--red);">*</span></label>
                        <input id="edit_company_name" class="field-input" type="text" name="company_name" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-map-marker-alt"></i> Company Address <span style="color:var(--red);">*</span></label>
                        <input id="edit_company_address" class="field-input" type="text" name="company_address" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-user-tie"></i> Company Representative <span style="color:var(--red);">*</span></label>
                        <input id="edit_company_rep" class="field-input" type="text" name="company_rep" required>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-phone"></i> Contact Number <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input id="edit_company_no" class="field-input" type="text" name="companyNo">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-envelope"></i> Email Address <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input id="edit_company_email" class="field-input" type="email" name="company_email">
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-calendar-alt"></i> Academic Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year <span style="color:var(--red);">*</span></label>
                        <div class="year-row">
                            <input id="edit_school_year_start" class="field-input" type="text" name="school_year_start" placeholder="Start Year" required>
                            <span>–</span>
                            <input id="edit_school_year_end" class="field-input" type="text" name="school_year_end" placeholder="End Year" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-graduation-cap"></i> Course <span style="color:var(--red);">*</span></label>
                        <select name="course" id="editMoaCourseSelect" class="field-select" required>
                            <option value="" disabled>Select course</option>
                            @foreach ($course as $courseItem)
                                <option value="{{ $courseItem->course }}">{{ $courseItem->course }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-section"><i class="fa fa-paperclip"></i> MOA Document</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-file-pdf"></i> Replace MOA File <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                        <input class="field-input" type="file" name="file" data-max-size-mb="2" accept="application/pdf" style="padding:8px 13px;">
                        <div style="font-size:12px; color:#888; margin-top:8px;">Leave this blank to keep the current PDF.</div>
                        <div style="margin-top:6px; font-size:12px; color:#777;">PDF only | Max file size: 2 MB</div>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                    <div class="modal-section"><i class="fa fa-users"></i> Assign Students</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-user-graduate"></i> Student Names <span style="color:#aaa; font-weight:400;">(Optional, hold Ctrl to select multiple)</span></label>
                        <input
                            id="editMoaStudentSearch"
                            class="field-input"
                            type="text"
                            placeholder="Search student name"
                            style="margin-bottom:8px;"
                            disabled
                        >
                        <select name="student_names[]" id="editMoaStudentSelect" class="field-select" multiple style="min-height:100px;" disabled>
                            @foreach ($stu as $student)
                                <option value="{{ $student->full_name }}" data-course="{{ strtolower(trim($student->course ?? '')) }}">{{ $student->full_name }}</option>
                            @endforeach
                        </select>
                        <div id="editMoaStudentHint" style="margin-top:6px; font-size:12px; color:#888;">Select a course first to show matching students.</div>
                    </div>

                    <div class="field-group" style="margin-top:10px;">
                        <label class="field-label"><i class="fa fa-keyboard"></i> Manual Student Input <span style="color:#aaa; font-weight:400;">(Optional, comma or new line separated)</span></label>
                        <textarea
                            id="editManualStudentInput"
                            name="manual_student_names"
                            class="field-input"
                            rows="3"
                            placeholder="Type student names separated by comma or new line"
                            style="resize:vertical; min-height:88px;"
                        ></textarea>
                        <div style="font-size:12px; color:#888; margin-top:8px;">Use this for students without accounts. Input is also shown in the MOA list.</div>
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
</div>

<!-- =============== SEND MODAL =============== -->
<div class="modal fade" id="sendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-paper-plane"></i> Send MOA File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/sendFile') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="send-company-banner"
                         style="display:flex; align-items:center; gap:10px; background:#fff5f5; border:1px solid #fecaca; border-radius:12px; padding:12px 14px; margin-bottom:18px;">
                        <div style="width:36px; height:36px; border-radius:9px; background:#fee2e2; display:flex; align-items:center; justify-content:center; color:var(--red); font-size:14px; flex-shrink:0;">
                            <i class="fa fa-building"></i>
                        </div>
                        <div>
                            <div id="send-company-name" style="font-size:13.5px; font-weight:700; color:#1a1a1a;"></div>
                            <div style="font-size:12px; color:#888; margin-top:2px;">Send MOA document via email</div>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-envelope"></i> Recipient Email Address</label>
                        <input class="field-input" type="email" name="email" placeholder="Enter email address" required>
                    </div>

                    <input type="hidden" id="send-file-id" name="file_id" value="">
                    <input type="hidden" id="send-company-name-input" name="company_name" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-paper-plane me-1"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== PRINT PREVIEW MODAL =============== -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-print"></i> Print Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scrollable-modal-body">
                <iframe id="viewIframe" style="width:100%; height:520px; border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
                <button type="button" onclick="printRegularPreview()" class="btn-modal-submit">
                    <i class="fa fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    const companyEditData = @json($companyEditPayload);

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

        // DataTable - keep server-side ordering by school year
        $('#companyTable').DataTable({
            order: [],
            columnDefs: [{ targets: 0, visible: false }]
        });

        // Send modal populate
        $(document).on('click', '.btn-open-send', function () {
            const fileId      = $(this).data('file-id');
            const companyName = $(this).data('company-name');
            $('#send-file-id').val(fileId);
            $('#send-company-name-input').val(companyName);
            $('#send-company-name').text(companyName);
        });

        function setupStudentFilter(courseSelector, searchSelector, selectSelector, hintSelector) {
            const $course = $(courseSelector);
            const $search = $(searchSelector);
            const studentSelect = document.querySelector(selectSelector);
            const studentHint = document.querySelector(hintSelector);

            function runFilter() {
                if (!studentSelect) {
                    return;
                }

                const selectedCourse = (($course.val() || '').trim()).toLowerCase();
                const searchQuery = (($search.val() || '').trim()).toLowerCase();
                const hasSelectedCourse = selectedCourse.length > 0;

                studentSelect.disabled = !hasSelectedCourse;
                if ($search.length) {
                    $search.prop('disabled', !hasSelectedCourse);
                }

                if (studentHint) {
                    studentHint.textContent = hasSelectedCourse
                        ? 'Only students from the selected course are shown.'
                        : 'Select a course first to show matching students.';
                }

                Array.from(studentSelect.options).forEach(function (option) {
                    const studentCourse = (option.getAttribute('data-course') || '').trim().toLowerCase();
                    const matchesCourse = !selectedCourse || studentCourse === selectedCourse;
                    const matchesSearch = !searchQuery || option.value.toLowerCase().includes(searchQuery);
                    const isMatch = matchesCourse && matchesSearch;

                    option.hidden = !isMatch;
                    option.disabled = !isMatch;

                    if (!isMatch) {
                        option.selected = false;
                    }
                });
            }

            $course.on('change', function () {
                $search.val('');
                runFilter();
            });
            $search.on('input', runFilter);

            runFilter();
            return runFilter;
        }

        const filterAddStudents = setupStudentFilter('#moaCourseSelect', '#moaStudentSearch', '#moaStudentSelect', '#moaStudentHint');
        const filterEditStudents = setupStudentFilter('#editMoaCourseSelect', '#editMoaStudentSearch', '#editMoaStudentSelect', '#editMoaStudentHint');

        // File validation error
        @if ($errors->has('file'))
            Swal.fire({
                icon: 'error',
                title: 'Upload Error',
                text: "{{ $errors->first('file') }}",
                confirmButtonColor: '#dc2626',
            });
        @endif

        $('form[action$="/companyCreate"], #editCompanyForm').on('submit', function (e) {
            if (this.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }

            this.dataset.submitting = 'true';

            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = this.id === 'editCompanyForm'
                    ? '<i class="fa fa-spinner fa-spin me-1"></i> Saving...'
                    : '<i class="fa fa-spinner fa-spin me-1"></i> Uploading...';
            }
        });

    });

    function confirmRemove(companyId, companyName) {
        Swal.fire({
            title: 'Remove MOA?',
            html: 'This will permanently delete <strong>' + companyName + '</strong> and all associated data.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('remove-form-' + companyId).submit();
            }
        });
    }

    function openEditCompanyModal(companyId) {
        const company = companyEditData[String(companyId)] || companyEditData[companyId];

        if (!company) {
            return;
        }

        $('#editCompanyForm').attr('action', '/company/' + company.id);
        $('#edit_company_name').val(company.company_name || '');
        $('#edit_company_address').val(company.company_address || '');
        $('#edit_company_rep').val(company.company_rep || '');
        $('#edit_company_no').val(company.company_no || '');
        $('#edit_company_email').val(company.company_email || '');
        $('#edit_school_year_start').val(company.school_year_start || '');
        $('#edit_school_year_end').val(company.school_year_end || '');
        $('#editMoaCourseSelect').val(company.course || '');
        $('#editMoaStudentSearch').val('');
        $('#editManualStudentInput').val(Array.isArray(company.manual_students) ? company.manual_students.join(', ') : '');

        const courseSelect = document.getElementById('editMoaCourseSelect');
        if (courseSelect) {
            courseSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const selectedSet = new Set(Array.isArray(company.selected_students) ? company.selected_students : []);
        const studentSelect = document.getElementById('editMoaStudentSelect');
        if (studentSelect) {
            Array.from(studentSelect.options).forEach(function (option) {
                option.selected = selectedSet.has(option.value) && !option.disabled;
            });
        }
    }

    // Print functions
    function openViewModal(routeUrl) {
        document.getElementById('viewIframe').src = routeUrl;
        $('#viewModal').modal('show');
    }

    function printUploadedMoa(fileUrl) {
        if (!fileUrl) return;

        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.src = fileUrl;
        document.body.appendChild(iframe);

        iframe.onload = function () {
            try {
                const pdfWindow = iframe.contentWindow;
                pdfWindow.focus();
                pdfWindow.print();
            } catch (error) {
                window.open(fileUrl, '_blank');
            } finally {
                setTimeout(function () {
                    if (iframe.parentNode) {
                        iframe.parentNode.removeChild(iframe);
                    }
                }, 1000);
            }
        };
    }

    function printRegularPreview() {
        const iframe = document.getElementById('viewIframe');
        if (iframe.contentDocument.readyState === 'complete') {
            iframe.contentWindow.print();
        } else {
            iframe.onload = function () { iframe.contentWindow.print(); };
        }
    }
        // Dark mode toggle
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/upload-size-guard.js') }}"></script>

<script src="{{ asset('assets/js/voice-input.js') }}"></script>
<script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
</html>
