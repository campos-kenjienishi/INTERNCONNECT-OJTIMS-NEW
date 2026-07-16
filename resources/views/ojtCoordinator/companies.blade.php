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

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            z-index: 1998;
        }

        body.mobile-sidebar-open::before {
            opacity: 1;
            pointer-events: auto;
        }

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

        .course-pill {
            display: inline-flex; align-items: center; justify-content: center;
            background: #eef2ff; color: #4338ca;
            border-radius: 999px; padding: 2px 9px;
            font-size: 10.5px; font-weight: 800;
            letter-spacing: 0.6px; margin: 2px 4px 0 0;
            white-space: nowrap;
        }

        .course-picker-shell {
            border: 1.5px solid #e8e8e8;
            border-radius: 14px;
            background: #fafafa;
            padding: 12px;
        }

        .course-picker-search {
            width: 100%;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 9px 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            margin-bottom: 10px;
            outline: none;
            background: #fff;
        }

        .course-picker-search:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }

        .course-picker-scroll {
            max-height: 220px;
            overflow-y: auto;
            padding-right: 4px;
            display: grid;
            gap: 8px;
        }

        .course-picker-scroll::-webkit-scrollbar { width: 5px; }
        .course-picker-scroll::-webkit-scrollbar-thumb { background: rgba(220,38,38,0.25); border-radius: 999px; }

        .course-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 11px;
            border: 1px solid #ececec;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .course-option:hover {
            border-color: #fecaca;
            box-shadow: 0 4px 14px rgba(220,38,38,0.06);
        }

        .course-option input {
            margin-top: 3px;
            width: 16px;
            height: 16px;
            accent-color: var(--red);
            flex-shrink: 0;
        }

        .course-option-content {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .course-option-acronym {
            font-size: 12px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 0.4px;
        }

        .course-option-name {
            font-size: 11.5px;
            color: #6b7280;
            line-height: 1.3;
            word-break: break-word;
        }

        /* Status badge */
        .status-active {
            display: inline-flex; align-items: center; gap: 5px;
            background: #dcfce7; color: #16a34a;
            border-radius: 20px; padding: 4px 10px;
            font-size: 11.5px; font-weight: 600;
        }

        .status-expired {
            display: inline-flex; align-items: center; gap: 5px;
            background: #fee2e2; color: #dc2626;
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
        .sidebar-overlay {
            display: none !important;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55) !important;
            z-index: 1999 !important;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .sidebar-overlay.active {
            display: block !important;
            opacity: 1 !important;
            pointer-events: auto !important;
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
body.dark-mode .status-expired { background: rgba(220,38,38,0.2); color: #fca5a5; }

        /* Cards */
        body.dark-mode .card { background: #2a2a2a; border: 1px solid #3a3a3a; }

        @media (max-width: 900px) {
            .sidebar { width: var(--sidebar-w); z-index: 2000; transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .field-row { grid-template-columns: 1fr; }

            /* Mobile table scrolling */
            .table-card-body {
                overflow-x: hidden;
                min-width: 0;
            }

            .table-card-body .dataTables_wrapper {
                padding: 12px 16px;
                overflow: visible !important;
            }

            .table-card-body .dataTables_scroll,
            .table-card-body .dataTables_scrollHead,
            .table-card-body .dataTables_scrollHeadInner,
            .table-card-body .dataTables_scrollBody {
                width: 100% !important;
                max-width: 100% !important;
            }

            .table-card-body .dataTables_scrollBody {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .table-card-body table.dataTable,
            .table-card-body .dataTables_scrollHeadInner table {
                min-width: 700px;
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
        @php
            $totalCompanies = count($companies);
            $activeMoaCount = collect($companies)->filter(function ($company) {
                try {
                    $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;
                } catch (\Throwable $e) {
                    $validUntil = null;
                }

                return $validUntil && now()->lte($validUntil);
            })->count();
        @endphp
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
                    <div class="stat-num">{{ $activeMoaCount }}</div>
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
                            $schoolYearStart = trim((string) $schoolYearStart);
                            $schoolYearEnd = trim((string) $schoolYearEnd);
                            if ($schoolYearStart !== '' && $schoolYearEnd !== '' && (int) $schoolYearEnd < (int) $schoolYearStart) {
                                [$schoolYearStart, $schoolYearEnd] = [$schoolYearEnd, $schoolYearStart];
                            }
                            $companyCourses = collect(preg_split('/[\r\n,;|\/]+/', trim((string) $company->course), -1, PREG_SPLIT_NO_EMPTY))
                                ->map(fn ($course) => trim((string) $course))
                                ->filter()
                                ->values();

                            try {
                                $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;
                            } catch (\Throwable $e) {
                                $validUntil = null;
                            }

                            $companyEditPayload = [
                                'company_name' => $company->company_name,
                                'company_address' => $company->company_address,
                                'company_rep' => $company->company_rep,
                                'company_no' => $company->companyNo,
                                'company_email' => $company->company_email,
                                'school_year_start' => $schoolYearStart,
                                'school_year_end' => $schoolYearEnd,
                                'school_year' => trim((string) $schoolYearStart) && trim((string) $schoolYearEnd)
                                    ? $schoolYearStart . '-' . $schoolYearEnd
                                    : ($company->school_year ?? ''),
                                'valid_until' => $validUntil ? $validUntil->format('Y-m-d') : '',
                                'course_values' => $companyCourses->values(),
                                'selected_students' => $linkedStudentNames->values(),
                                'manual_students' => $manualStudentNames->values(),
                            ];

                            $isActive = $validUntil && now()->lte($validUntil);
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
                                <span style="font-size:13px; color:#555;">
                                    {{ trim((string) $schoolYearStart) && trim((string) $schoolYearEnd) ? $schoolYearStart . '-' . $schoolYearEnd : ($company->school_year ?? '—') }}
                                </span>
                            </td>

                            <!-- Course -->
                            <td>
                                @php
                                    $companyCourses = collect(preg_split('/[\r\n,;|\/]+/', trim((string) $company->course), -1, PREG_SPLIT_NO_EMPTY))
                                        ->map(fn ($course) => trim((string) $course))
                                        ->filter()
                                        ->values();
                                    $courseAcronymLookup = collect($course)->mapWithKeys(function ($courseItem) {
                                        return [trim((string) $courseItem->course) => trim((string) ($courseItem->acronym ?? ''))];
                                    });
                                @endphp

                                @if ($companyCourses->isNotEmpty())
                                    <div style="display:flex; flex-wrap:wrap; gap:4px; max-width:180px;" title="{{ $companyCourses->implode(', ') }}">
                                        @foreach ($companyCourses as $companyCourse)
                                            @php
                                                $courseAcronym = trim((string) ($courseAcronymLookup[$companyCourse] ?? ''));
                                                if ($courseAcronym === '') {
                                                    $courseAcronym = collect(preg_split('/\s+/', trim($companyCourse), -1, PREG_SPLIT_NO_EMPTY))
                                                        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                                                        ->implode('');
                                                }
                                            @endphp
                                            <span class="course-pill" title="{{ $companyCourse }}">{{ $courseAcronym ?: $companyCourse }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size:13px; color:#555;">—</span>
                                @endif
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
                                @if ($isActive)
                                    <span class="status-active">
                                        <i class="fa fa-circle" style="font-size:7px;"></i> Active
                                    </span>
                                @else
                                    <span class="status-expired">
                                        <i class="fa fa-times-circle" style="font-size:11px;"></i> Expired
                                    </span>
                                @endif
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
                                        data-company-id="{{ $company->id }}"
                                        data-company-name="{{ $company->company_name }}"
                                        data-company-address="{{ $company->company_address }}"
                                        data-company-rep="{{ $company->company_rep }}"
                                        data-company-no="{{ $company->companyNo }}"
                                        data-company-email="{{ $company->company_email }}"
                                        data-school-year="{{ trim((string) $schoolYearStart) && trim((string) $schoolYearEnd) ? $schoolYearStart . '-' . $schoolYearEnd : ($company->school_year ?? '') }}"
                                        data-school-year-raw="{{ e($company->school_year ?? '') }}"
                                        data-school-year-start="{{ $schoolYearStart }}"
                                        data-school-year-end="{{ $schoolYearEnd }}"
                                        data-school-year-normalized="{{ trim((string) $schoolYearStart) && trim((string) $schoolYearEnd) ? $schoolYearStart . '-' . $schoolYearEnd : ($company->school_year ?? '') }}"
                                        data-valid-until="{{ $validUntil ? $validUntil->format('Y-m-d') : '' }}"
                                        data-course-raw="{{ e($company->course ?? '') }}"
                                        data-course-values='@json($companyCourses->values())'
                                        data-selected-students-raw="{{ e($linkedStudentNames->implode(', ')) }}"
                                        data-selected-students='@json($linkedStudentNames->values())'
                                        data-manual-students-raw="{{ e($manualStudentNames->implode(', ')) }}"
                                        data-manual-students='@json($manualStudentNames->values())'
                                        data-edit-payload='@json($companyEditPayload)'
                                        onclick="openEditCompanyModal(this)">
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

@php
    $selectedCreateStudentNames = collect(old('student_names', []))->filter()->values();
    $schoolYearBase = now()->year;
    $schoolYearOptions = range($schoolYearBase - 5, $schoolYearBase + 5);
    $selectedCreateStartYear = old('school_year_start', $schoolYearBase);
    $selectedCreateEndYear = old('school_year_end', $selectedCreateStartYear + 1);
@endphp

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
                            <select class="field-input" name="school_year_start" id="schoolYearStart" required>
                                @foreach ($schoolYearOptions as $year)
                                    <option value="{{ $year }}" {{ (string) $selectedCreateStartYear === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                            <span>–</span>
                            <select class="field-input" name="school_year_end" id="schoolYearEnd" required>
                                <option value="{{ $selectedCreateEndYear }}" selected>{{ $selectedCreateEndYear }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                            <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                            <span style="font-size: 11.5px; color: #777; font-weight: 400;">Select the MOA expiry date.</span>
                        </label>
                        <input class="field-input" type="date" name="valid_until" required>
                    </div>

                   <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-graduation-cap"></i> Course
                            <span style="color:var(--red);">*</span>
                        </label>

                        <div class="course-picker-shell">
                            <input
                                type="text"
                                id="moaCourseSearch"
                                class="course-picker-search"
                                placeholder="Search course acronym or name..."
                            >

                            <div id="moaCourseSelect" class="course-picker-scroll course-checkbox-group">
                                @foreach ($course as $courseItem)
                                    <label class="course-option" data-course-name="{{ strtolower($courseItem->course) }}" data-course-acronym="{{ strtolower($courseItem->acronym ?? '') }}">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="course[]"
                                            id="course{{ $courseItem->id }}"
                                            value="{{ $courseItem->course }}">
                                        <span class="course-option-content">
                                            <span class="course-option-acronym">{{ $courseItem->acronym ?: $courseItem->course }}</span>
                                            <span class="course-option-name">{{ $courseItem->course }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div style="font-size:11.5px; color:#777;">
                            Select one or more courses.
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-paperclip"></i> MOA Document</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-file-pdf"></i> Attach MOA File <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="file" name="file" data-max-size-mb="30" required style="padding:8px 13px;">
                        <div style="margin-top:6px; font-size:12px; color:#777;">
                            PDF only | Max file size: 30 MB
                        </div>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                    <div class="modal-section"><i class="fa fa-users"></i> Assign Students</div>

                    <div class="field-group" style="margin-top:10px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                            <div>
                                <label class="field-label" style="margin-bottom:4px;"><i class="fa fa-user-graduate"></i> Assigned Students</label>
                                <div id="moaAssignedStudentsSummary" style="font-size:12px; color:#666;">{{ $selectedCreateStudentNames->isEmpty() ? 'No students assigned yet.' : $selectedCreateStudentNames->count() . ' students selected.' }}</div>
                            </div>
                            <button type="button" class="btn-modal-submit" id="openAssignStudentsModal" style="padding:10px 14px; white-space:nowrap;">
                                <i class="fa fa-user-plus me-1"></i> Assign
                            </button>
                        </div>
                        <div id="moaAssignedStudentInputs" style="display:none;">
                            @foreach ($selectedCreateStudentNames as $studentName)
                                <input type="hidden" name="student_names[]" value="{{ $studentName }}">
                            @endforeach
                        </div>
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
                            <input id="edit_company_no" class="field-input" type="text" name="companyNo" placeholder="Leave blank if none">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-envelope"></i> Email Address <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input id="edit_company_email" class="field-input" type="email" name="company_email" placeholder="Leave blank if none">
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-calendar-alt"></i> Academic Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year <span style="color:var(--red);">*</span></label>
                        <div class="year-row">
                            <select id="edit_school_year_start" class="field-input" name="school_year_start" required>
                                @foreach ($schoolYearOptions as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <span>–</span>
                            <select id="edit_school_year_end" class="field-input" name="school_year_end" required></select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                            <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                            <span style="font-size: 11.5px; color: #777; font-weight: 400;">Select the MOA expiry date.</span>
                        </label>
                        <input id="editValidUntil" class="field-input" type="date" name="valid_until" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-graduation-cap"></i> Course <span style="color:var(--red);">*</span></label>
                        <div class="course-picker-shell">
                            <input
                                type="text"
                                id="editMoaCourseSearch"
                                class="course-picker-search"
                                placeholder="Search course acronym or name..."
                            >

                            <div id="editMoaCourseSelect" class="course-picker-scroll course-checkbox-group">
                                @foreach ($course as $courseItem)
                                    <label class="course-option" data-course-name="{{ strtolower($courseItem->course) }}" data-course-acronym="{{ strtolower($courseItem->acronym ?? '') }}">
                                        <input
                                            class="form-check-input edit-course-checkbox"
                                            type="checkbox"
                                            name="course[]"
                                            id="editCourse{{ $courseItem->id }}"
                                            value="{{ $courseItem->course }}">
                                        <span class="course-option-content">
                                            <span class="course-option-acronym">{{ $courseItem->acronym ?: $courseItem->course }}</span>
                                            <span class="course-option-name">{{ $courseItem->course }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div style="font-size:11.5px; color:#777;">Select one or more courses.</div>
                    </div>

                    <div class="modal-section"><i class="fa fa-paperclip"></i> MOA Document</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-file-pdf"></i> Replace MOA File <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                        <input class="field-input" type="file" name="file" data-max-size-mb="30" accept="application/pdf" style="padding:8px 13px;">
                        <div style="font-size:12px; color:#888; margin-top:8px;">Leave this blank to keep the current PDF.</div>
                        <div style="margin-top:6px; font-size:12px; color:#777;">PDF only | Max file size: 30 MB</div>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                    <div class="modal-section"><i class="fa fa-users"></i> Assign Students</div>

                    <div class="field-group" style="margin-top:10px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                            <div>
                                <label class="field-label" style="margin-bottom:4px;"><i class="fa fa-user-graduate"></i> Assigned Students</label>
                                <div id="editMoaAssignedStudentsSummary" style="font-size:12px; color:#666;">No students assigned yet.</div>
                            </div>
                            <button type="button" class="btn-modal-submit" id="openEditAssignStudentsModal" style="padding:10px 14px; white-space:nowrap;">
                                <i class="fa fa-user-plus me-1"></i> Assign
                            </button>
                        </div>
                        <div id="editMoaAssignedStudentInputs" style="display:none;"></div>
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

<!-- =============== ASSIGN STUDENTS MODAL =============== -->
<div class="modal fade" id="assignStudentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-user-plus"></i> Assign Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#fff; max-height:78vh;">
                <div style="display:grid; grid-template-columns: 1.1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div class="field-group" style="margin:0;">
                        <label class="field-label"><i class="fa fa-graduation-cap"></i> Course</label>
                        <select id="assignStudentsCourse" class="field-select">
                            <option value="">All Courses</option>
                            @foreach ($course as $courseItem)
                                <option value="{{ $courseItem->course }}">{{ $courseItem->course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group" style="margin:0;">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                        <select id="assignStudentsSchoolYear" class="field-select">
                            <option value="">All School Years</option>
                            @foreach ($studentSchoolYears as $studentSchoolYear)
                                <option value="{{ $studentSchoolYear }}">{{ $studentSchoolYear }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field-group" style="margin:0 0 14px;">
                    <label class="field-label"><i class="fa fa-search"></i> Search Students</label>
                    <input id="assignStudentsSearch" class="field-input" type="text" placeholder="Search by name, student number, or section">
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                    <div id="assignStudentsSelectedInfo" style="font-size:12.5px; color:#6b7280;">No students selected yet.</div>
                    <button type="button" class="btn-modal-close" id="assignStudentsClear" style="padding:9px 14px;">
                        <i class="fa fa-eraser me-1"></i> Clear Selection
                    </button>
                </div>

                <div id="assignStudentsSelectedChips" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px;"></div>

                <div id="assignStudentsStatus" style="display:none; padding:14px; border:1px dashed #f3b3b3; border-radius:12px; color:#6b7280; font-size:12.5px; background:#fff7f7; margin-bottom:12px;"></div>

                <div style="max-height: 52vh; overflow-y: auto; padding-right: 4px;">
                    <div id="assignStudentsList" style="display:grid; gap:10px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="margin-right:auto; font-size:12px; color:#6b7280;">Choose one or more students, then click Apply.</div>
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn-modal-submit" id="assignStudentsApply">
                    <i class="fa fa-check me-1"></i> Apply Assignments
                </button>
            </div>
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
    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.classList.remove('mobile-sidebar-open');
            } else {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('active');
                document.body.classList.add('mobile-sidebar-open');
            }
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.classList.remove('mobile-sidebar-open');
    });

    const closeMobileSidebar = function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.classList.remove('mobile-sidebar-open');
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

    function setCheckedCourseValues(containerSelector, selectedCourses) {
        const normalizedCourses = new Set((selectedCourses || []).map(function (course) {
            return String(course || '').trim().toLowerCase();
        }).filter(Boolean));

        document.querySelectorAll(containerSelector + ' input[name="course[]"]').forEach(function (checkbox) {
            const option = checkbox.closest('.course-option');
            const checkboxValue = (checkbox.value || '').trim().toLowerCase();
            const optionName = (option && option.getAttribute('data-course-name') || '').trim().toLowerCase();
            const optionAcronym = (option && option.getAttribute('data-course-acronym') || '').trim().toLowerCase();
            const labelText = option ? option.textContent.replace(/\s+/g, ' ').trim().toLowerCase() : '';

            checkbox.checked = normalizedCourses.has(checkboxValue)
                || normalizedCourses.has(optionName)
                || normalizedCourses.has(optionAcronym)
                || (labelText ? normalizedCourses.has(labelText) : false);
        });
    }

    function filterCourseOptions(searchInputId, containerId) {
        const searchInput = document.getElementById(searchInputId);
        const container = document.getElementById(containerId);

        if (!searchInput || !container) {
            return;
        }

        const searchTerm = searchInput.value.trim().toLowerCase();

        container.querySelectorAll('.course-option').forEach(function (option) {
            const courseName = (option.getAttribute('data-course-name') || '').toLowerCase();
            const courseAcronym = (option.getAttribute('data-course-acronym') || '').toLowerCase();
            const visible = !searchTerm || courseName.includes(searchTerm) || courseAcronym.includes(searchTerm);
            option.style.display = visible ? '' : 'none';
        });
    }

    function setEditSchoolYearFields(startYear, endYear) {
        const startSelect = document.getElementById('edit_school_year_start');
        const endSelect = document.getElementById('edit_school_year_end');

        if (!startSelect || !endSelect) {
            return;
        }

        if (startYear) {
            startSelect.value = String(startYear);
        }

        const resolvedStartYear = parseInt(startSelect.value, 10);
        let resolvedEndYear = parseInt(endYear, 10);

        if (Number.isNaN(resolvedStartYear)) {
            return;
        }

        if (Number.isNaN(resolvedEndYear) || resolvedEndYear <= resolvedStartYear) {
            resolvedEndYear = resolvedStartYear + 1;
        }

        endSelect.innerHTML = '';

        const option = document.createElement('option');
        option.value = String(resolvedEndYear);
        option.textContent = String(resolvedEndYear);
        option.selected = true;
        endSelect.appendChild(option);
        endSelect.value = String(resolvedEndYear);
    }

    $(document).ready(function () {

        // DataTable - keep server-side ordering by school year
        $('#companyTable').DataTable({
            order: [],
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
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

        const assignStudentsUrl = @json(route('moa.assignableStudents'));
        const assignableStudentState = {
            mode: null,
            selectedNames: new Set(),
            students: [],
            loading: false,
            searchTimer: null
        };

        const assignTargets = {
            add: {
                courseField: '#moaCourseSelect',
                schoolYearStartField: '#schoolYearStart',
                schoolYearEndField: '#schoolYearEnd',
                summaryField: '#moaAssignedStudentsSummary',
                inputsField: '#moaAssignedStudentInputs'
            },
            edit: {
                courseField: '#editMoaCourseSelect',
                schoolYearStartField: '#edit_school_year_start',
                schoolYearEndField: '#edit_school_year_end',
                summaryField: '#editMoaAssignedStudentsSummary',
                inputsField: '#editMoaAssignedStudentInputs'
            }
        };

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getCheckedCourseValues(containerSelector) {
            return Array.from(document.querySelectorAll(containerSelector + ' input[name="course[]"]:checked'))
                .map(function (input) {
                    return (input.value || '').trim();
                })
                .filter(Boolean);
        }

        function setCheckedCourseValues(containerSelector, selectedCourses) {
            const normalizedCourses = new Set((selectedCourses || []).map(function (course) {
                return String(course || '').trim().toLowerCase();
            }).filter(Boolean));

            document.querySelectorAll(containerSelector + ' input[name="course[]"]').forEach(function (checkbox) {
                const option = checkbox.closest('.course-option');
                const checkboxValue = (checkbox.value || '').trim().toLowerCase();
                const optionName = (option && option.getAttribute('data-course-name') || '').trim().toLowerCase();
                const optionAcronym = (option && option.getAttribute('data-course-acronym') || '').trim().toLowerCase();
                const labelText = option ? option.textContent.replace(/\s+/g, ' ').trim().toLowerCase() : '';

                checkbox.checked = normalizedCourses.has(checkboxValue)
                    || normalizedCourses.has(optionName)
                    || normalizedCourses.has(optionAcronym)
                    || (labelText ? normalizedCourses.has(labelText) : false);
            });
        }

        function filterCourseOptions(searchInputId, containerId) {
            const searchInput = document.getElementById(searchInputId);
            const container = document.getElementById(containerId);

            if (!searchInput || !container) {
                return;
            }

            const searchTerm = searchInput.value.trim().toLowerCase();

            container.querySelectorAll('.course-option').forEach(function (option) {
                const courseName = (option.getAttribute('data-course-name') || '').toLowerCase();
                const courseAcronym = (option.getAttribute('data-course-acronym') || '').toLowerCase();
                const visible = !searchTerm || courseName.includes(searchTerm) || courseAcronym.includes(searchTerm);
                option.style.display = visible ? '' : 'none';
            });
        }

    function syncSchoolYearEnd(startId, endId, selectedEndYear = null) {
        const startSelect = document.getElementById(startId);
        const endSelect = document.getElementById(endId);

            if (!startSelect || !endSelect || !startSelect.value) {
                return;
            }

            const startYear = parseInt(startSelect.value, 10);

            if (Number.isNaN(startYear)) {
                return;
            }

            const parsedEndYear = selectedEndYear ? parseInt(selectedEndYear, 10) : NaN;
            const endYear = Number.isNaN(parsedEndYear) || parsedEndYear <= startYear
                ? startYear + 1
                : parsedEndYear;
            endSelect.innerHTML = '';

            const option = document.createElement('option');
            option.value = String(endYear);
            option.textContent = String(endYear);
            option.selected = true;
            endSelect.appendChild(option);
            endSelect.value = String(endYear);
        }

        function setEditSchoolYearFields(startYear, endYear) {
            const startSelect = document.getElementById('edit_school_year_start');
            const endSelect = document.getElementById('edit_school_year_end');

            if (!startSelect || !endSelect) {
                return;
            }

            if (startYear) {
                startSelect.value = String(startYear);
            }

            const resolvedStartYear = parseInt(startSelect.value, 10);
            let resolvedEndYear = parseInt(endYear, 10);

            if (Number.isNaN(resolvedStartYear)) {
                return;
            }

            if (Number.isNaN(resolvedEndYear) || resolvedEndYear <= resolvedStartYear) {
                resolvedEndYear = resolvedStartYear + 1;
            }

            endSelect.innerHTML = '';

            const option = document.createElement('option');
            option.value = String(resolvedEndYear);
            option.textContent = String(resolvedEndYear);
            option.selected = true;
            endSelect.appendChild(option);
            endSelect.value = String(resolvedEndYear);
        }

        function getTargetConfig(mode) {
            return assignTargets[mode] || null;
        }

        function getTargetValues(mode) {
            const target = getTargetConfig(mode);
            if (!target) {
                return { course: '', schoolYear: '' };
            }

            const courseValues = getCheckedCourseValues(target.courseField);
            const startYear = ($(target.schoolYearStartField).val() || '').trim();
            const endYear = ($(target.schoolYearEndField).val() || '').trim();

            return {
                course: courseValues[0] || '',
                schoolYear: startYear && endYear ? startYear + '-' + endYear : ''
            };
        }

        function syncTargetFields(mode, course, schoolYear) {
            const target = getTargetConfig(mode);
            if (!target) return;

            if ($(target.schoolYearStartField).length && $(target.schoolYearEndField).length) {
                const parts = String(schoolYear || '').split('-');
                $(target.schoolYearStartField).val(parts[0] || '');
                $(target.schoolYearEndField).val(parts[1] || '');
            }
        }

        function renderAssignedSummary(mode, selectedNames) {
            const target = getTargetConfig(mode);
            if (!target) return;

            const summary = document.querySelector(target.summaryField);
            const inputs = document.querySelector(target.inputsField);
            const names = Array.from(selectedNames || []).filter(Boolean);

            if (summary) {
                summary.textContent = names.length
                    ? names.length + ' student' + (names.length === 1 ? '' : 's') + ' selected.'
                    : 'No students assigned yet.';
            }

            if (inputs) {
                inputs.innerHTML = names.map(function (name) {
                    return '<input type="hidden" name="student_names[]" value="' + escapeHtml(name) + '">';
                }).join('');
            }
        }

        function renderAssignableSelectedChips() {
            const chips = document.getElementById('assignStudentsSelectedChips');
            const info = document.getElementById('assignStudentsSelectedInfo');
            if (!chips || !info) return;

            const names = Array.from(assignableStudentState.selectedNames);
            info.textContent = names.length
                ? names.length + ' student' + (names.length === 1 ? '' : 's') + ' selected.'
                : 'No students selected yet.';

            chips.innerHTML = names.length
                ? names.map(function (name) {
                    return '<button type="button" class="student-pill" data-selected-name="' + escapeHtml(name) + '" style="border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">'
                        + '<i class="fa fa-times" style="font-size:10px;"></i>'
                        + escapeHtml(name)
                        + '</button>';
                }).join('')
                : '<span style="font-size:12px; color:#6b7280;">Selected students will appear here.</span>';
        }

        function getActiveAssignFilters() {
            return {
                course: ($('#assignStudentsCourse').val() || '').trim(),
                schoolYear: ($('#assignStudentsSchoolYear').val() || '').trim(),
                search: ($('#assignStudentsSearch').val() || '').trim()
            };
        }

        function setAssignStatus(message, isError) {
            const status = document.getElementById('assignStudentsStatus');
            if (!status) return;

            if (!message) {
                status.style.display = 'none';
                status.textContent = '';
                return;
            }

            status.style.display = 'block';
            status.textContent = message;
            status.style.borderColor = isError ? '#fca5a5' : '#f3b3b3';
            status.style.background = isError ? '#fef2f2' : '#fff7f7';
        }

        function renderAssignableStudentsList() {
            const list = document.getElementById('assignStudentsList');
            const { course } = getActiveAssignFilters();

            if (!list) return;

            if (assignableStudentState.loading) {
                list.innerHTML = '<div style="padding:18px; text-align:center; color:#6b7280; border:1px dashed #f3b3b3; border-radius:12px; background:#fff;">Loading students...</div>';
                return;
            }

            if (!course) {
                list.innerHTML = '<div style="padding:18px; text-align:center; color:#6b7280; border:1px dashed #f3b3b3; border-radius:12px; background:#fff;">Choose a course to load matching students.</div>';
                setAssignStatus('', false);
                return;
            }

            if (!assignableStudentState.students.length) {
                list.innerHTML = '<div style="padding:18px; text-align:center; color:#6b7280; border:1px dashed #f3b3b3; border-radius:12px; background:#fff;">No students found for the selected filters.</div>';
                setAssignStatus('No students matched the current filters.', false);
                return;
            }

            setAssignStatus('Loaded ' + assignableStudentState.students.length + ' student' + (assignableStudentState.students.length === 1 ? '' : 's') + ' for the current filters.', false);

            list.innerHTML = assignableStudentState.students.map(function (student) {
                const checked = assignableStudentState.selectedNames.has(student.full_name) ? 'checked' : '';
                return '<label style="display:flex; align-items:flex-start; gap:12px; padding:14px 15px; border:1px solid #f1d5d5; border-radius:14px; background:#fff; cursor:pointer; transition:all .15s ease;">'
                    + '<input type="checkbox" class="assign-student-checkbox" data-student-name="' + escapeHtml(student.full_name) + '" ' + checked + ' style="margin-top:4px; width:18px; height:18px;">'
                    + '<div style="flex:1; min-width:0;">'
                    + '<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">'
                    + '<span style="font-size:14px; font-weight:700; color:#111827;">' + escapeHtml(student.full_name) + '</span>'
                    + '<span style="font-size:11px; padding:3px 8px; border-radius:999px; background:#eff6ff; color:#2563eb; font-weight:700;">' + escapeHtml(student.student_num || 'No ID') + '</span>'
                    + '</div>'
                    + '<div style="font-size:12px; color:#6b7280; margin-top:4px;">'
                    + escapeHtml(student.course || 'No course') + ' • '
                    + escapeHtml(student.year_and_section || 'No section') + ' • '
                    + escapeHtml(student.school_year || 'No school year')
                    + '</div>'
                    + '</div>'
                    + '</label>';
            }).join('');

            renderAssignableSelectedChips();
        }

        function fetchAssignableStudents() {
            const { course, schoolYear, search } = getActiveAssignFilters();

            assignableStudentState.loading = true;
            renderAssignableStudentsList();

            $.getJSON(assignStudentsUrl, {
                course: course,
                school_year: schoolYear,
                search: search
            }).done(function (response) {
                assignableStudentState.students = Array.isArray(response.students) ? response.students : [];
            }).fail(function () {
                assignableStudentState.students = [];
                setAssignStatus('Unable to load students right now. Please try again.', true);
            }).always(function () {
                assignableStudentState.loading = false;
                renderAssignableStudentsList();
            });
        }

        function resetAssignableSelection() {
            assignableStudentState.selectedNames.clear();
            renderAssignableSelectedChips();
            renderAssignableStudentsList();
        }

        function openAssignStudentsModal(mode) {
            assignableStudentState.mode = mode;

            const target = getTargetConfig(mode);
            if (!target) return;

            const values = getTargetValues(mode);
            $('#assignStudentsCourse').val(values.course);
            $('#assignStudentsSchoolYear').val(values.schoolYear);
            $('#assignStudentsSearch').val('');

            const existingNames = Array.from(document.querySelectorAll(target.inputsField + ' input[name="student_names[]"]'))
                .map(function (input) { return (input.value || '').trim(); })
                .filter(Boolean);
            assignableStudentState.selectedNames = new Set(existingNames);

            renderAssignedSummary(mode, assignableStudentState.selectedNames);
            renderAssignableSelectedChips();
            setAssignStatus('', false);

            $('#assignStudentsModal').modal('show');
            fetchAssignableStudents();
        }

        function applyAssignableStudents() {
            if (!assignableStudentState.mode) {
                return;
            }

            const values = getActiveAssignFilters();
            syncTargetFields(assignableStudentState.mode, values.course, values.schoolYear);
            renderAssignedSummary(assignableStudentState.mode, assignableStudentState.selectedNames);

            $('#assignStudentsModal').modal('hide');
        }

        $(document).on('click', '#openAssignStudentsModal', function () {
            openAssignStudentsModal('add');
        });

        $(document).on('click', '#openEditAssignStudentsModal', function () {
            openAssignStudentsModal('edit');
        });

        $(document).on('change', '#assignStudentsCourse, #assignStudentsSchoolYear', function () {
            if (!assignableStudentState.mode) {
                return;
            }

            syncTargetFields(assignableStudentState.mode, $('#assignStudentsCourse').val(), $('#assignStudentsSchoolYear').val());
            resetAssignableSelection();
            fetchAssignableStudents();
        });

        $(document).on('input', '#assignStudentsSearch', function () {
            clearTimeout(assignableStudentState.searchTimer);
            assignableStudentState.searchTimer = setTimeout(function () {
                fetchAssignableStudents();
            }, 250);
        });

        $(document).on('change', '.assign-student-checkbox', function () {
            const studentName = ($(this).data('student-name') || '').trim();
            if (!studentName) {
                return;
            }

            if (this.checked) {
                assignableStudentState.selectedNames.add(studentName);
            } else {
                assignableStudentState.selectedNames.delete(studentName);
            }

            renderAssignableSelectedChips();
        });

        $(document).on('click', '#assignStudentsApply', function () {
            applyAssignableStudents();
        });

        $(document).on('input', '#moaCourseSearch', function () {
            filterCourseOptions('moaCourseSearch', 'moaCourseSelect');
        });

        $(document).on('input', '#editMoaCourseSearch', function () {
            filterCourseOptions('editMoaCourseSearch', 'editMoaCourseSelect');
        });

        syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd', @json($selectedCreateEndYear));
        syncSchoolYearEnd('edit_school_year_start', 'edit_school_year_end');

        $('#schoolYearStart').on('change', function () {
            syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd');
        });

        $('#edit_school_year_start').on('change', function () {
            syncSchoolYearEnd('edit_school_year_start', 'edit_school_year_end');
        });

        $(document).on('click', '#assignStudentsClear', function () {
            resetAssignableSelection();
        });

        $(document).on('click', '#assignStudentsSelectedChips [data-selected-name]', function () {
            const studentName = ($(this).data('selected-name') || '').trim();
            if (!studentName) return;

            assignableStudentState.selectedNames.delete(studentName);
            renderAssignableSelectedChips();

            document.querySelectorAll('.assign-student-checkbox').forEach(function (checkbox) {
                if ((checkbox.getAttribute('data-student-name') || '').trim() === studentName) {
                    checkbox.checked = false;
                }
            });
        });

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

    function parseSchoolYearParts(value) {
        const matches = String(value || '').match(/\d{4}/g) || [];

        if (matches.length < 2) {
            return ['', ''];
        }

        let startYear = parseInt(matches[0], 10);
        let endYear = parseInt(matches[1], 10);

        if (!Number.isNaN(startYear) && !Number.isNaN(endYear) && endYear < startYear) {
            const swap = startYear;
            startYear = endYear;
            endYear = swap;
        }

        return [
            Number.isNaN(startYear) ? '' : String(startYear),
            Number.isNaN(endYear) ? '' : String(endYear)
        ];
    }

    function parseJsonDataset(value, fallback) {
        if (!value) {
            return fallback;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            return fallback;
        }
    }

    function getEditPayload(dataset) {
        const payload = parseJsonDataset(dataset.editPayload, null);

        if (!payload || typeof payload !== 'object' || Array.isArray(payload)) {
            return {};
        }

        return payload;
    }

    function normalizeCourseList(value) {
        return String(value || '')
            .split(/[\r\n,;|\/]+/)
            .map(function (course) {
                return course.trim();
            })
            .filter(function (course) {
                return course.length > 0;
            });
    }

    function normalizeNameList(value) {
        return String(value || '')
            .split(/[\r\n,;]+/)
            .map(function (name) {
                return name.trim();
            })
            .filter(function (name) {
                return name.length > 0;
            });
    }

    function normalizeDateInput(value) {
        if (!value) {
            return '';
        }

        const raw = String(value).trim();
        const isoMatch = raw.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (isoMatch) {
            return isoMatch[1] + '-' + isoMatch[2] + '-' + isoMatch[3];
        }

        const slashMatch = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
        if (slashMatch) {
            return slashMatch[3] + '-' + slashMatch[2] + '-' + slashMatch[1];
        }

        const parsed = new Date(raw);
        if (!Number.isNaN(parsed.getTime())) {
            const year = String(parsed.getFullYear());
            const month = String(parsed.getMonth() + 1).padStart(2, '0');
            const day = String(parsed.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        return '';
    }

    function openEditCompanyModal(button) {
        const dataset = button && button.dataset ? button.dataset : {};
        const payload = getEditPayload(dataset);
        const form = document.getElementById('editCompanyForm');
        const currentFile = dataset.fileName || '';
        const row = button && button.closest ? button.closest('tr') : null;
        const cells = row ? row.querySelectorAll('td') : [];

        const rowSchoolYearText = cells[4] ? cells[4].textContent.trim() : '';
        const rowCourseValues = cells[5]
            ? Array.from(cells[5].querySelectorAll('.course-pill'))
                .map(function (pill) {
                    return (pill.textContent || '').trim();
                })
                .filter(Boolean)
            : [];
        const rowStudentValues = cells[6]
            ? Array.from(cells[6].querySelectorAll('.student-pill'))
                .map(function (pill) {
                    return (pill.textContent || '').trim();
                })
                .filter(Boolean)
            : [];

        if (!form) {
            return;
        }

        form.action = '/company/' + (dataset.companyId || '');
        $('#edit_company_name').val(payload.company_name || dataset.companyName || '');
        $('#edit_company_address').val(payload.company_address || dataset.companyAddress || '');
        $('#edit_company_rep').val(payload.company_rep || dataset.companyRep || '');
        $('#edit_company_no').val(payload.company_no || dataset.companyNo || '');
        $('#edit_company_email').val(payload.company_email || dataset.companyEmail || '');

        const schoolYearParts = parseSchoolYearParts(
            payload.school_year
                || dataset.schoolYearRaw
                || dataset.schoolYearNormalized
                || dataset.schoolYear
                || rowSchoolYearText
                || ''
        );
        const schoolYearStart = payload.school_year_start || schoolYearParts[0] || dataset.schoolYearStart || '';
        const schoolYearEnd = payload.school_year_end || schoolYearParts[1] || dataset.schoolYearEnd || '';

        setEditSchoolYearFields(schoolYearStart, schoolYearEnd);
        $('#editValidUntil').val(normalizeDateInput(payload.valid_until || dataset.validUntil || ''));

        const selectedCourses = normalizeCourseList(payload.course_values && payload.course_values.length ? payload.course_values.join(', ') : (dataset.courseRaw || ''));
        const selectedCoursesFallback = rowCourseValues.length
            ? rowCourseValues
            : parseJsonDataset(dataset.courseValues, []);
        setCheckedCourseValues('#editMoaCourseSelect', selectedCourses.length ? selectedCourses : selectedCoursesFallback);
        const manualStudents = normalizeNameList((payload.manual_students && payload.manual_students.length ? payload.manual_students.join(', ') : (dataset.manualStudentsRaw || '')));
        const manualStudentsFallback = parseJsonDataset(dataset.manualStudents, []);
        $('#editManualStudentInput').val((manualStudents.length ? manualStudents : (manualStudentsFallback || [])).join(', '));

        const courseSearch = document.getElementById('editMoaCourseSearch');
        if (courseSearch) {
            courseSearch.value = '';
        }

        filterCourseOptions('editMoaCourseSearch', 'editMoaCourseSelect');

        const selectedStudents = normalizeNameList((payload.selected_students && payload.selected_students.length ? payload.selected_students.join(', ') : (dataset.selectedStudentsRaw || '')));
        const selectedStudentsFallback = rowStudentValues.length
            ? rowStudentValues
            : parseJsonDataset(dataset.selectedStudents, []);
        const resolvedSelectedStudents = selectedStudents.length ? selectedStudents : (selectedStudentsFallback || []);
        const selectedSet = new Set(resolvedSelectedStudents);
        const editAssignedInputs = document.getElementById('editMoaAssignedStudentInputs');
        const editAssignedSummary = document.getElementById('editMoaAssignedStudentsSummary');

        if (editAssignedInputs) {
            editAssignedInputs.innerHTML = '';
            resolvedSelectedStudents.forEach(function (studentName) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_names[]';
                input.value = studentName;
                editAssignedInputs.appendChild(input);
            });
        }

        if (editAssignedSummary) {
            editAssignedSummary.textContent = selectedSet.size
                ? selectedSet.size + ' student' + (selectedSet.size === 1 ? '' : 's') + ' selected.'
                : 'No students assigned yet.';
        }

        const currentFileNode = document.getElementById('editMoaCurrentFile');
        if (currentFileNode) {
            currentFileNode.textContent = currentFile
                ? 'Current file: ' + currentFile + '. Leave the file empty if you only need to update the company details.'
                : 'Leave the file empty if you only need to update the company details.';
        }

        const fileInput = document.getElementById('editMoaFileInput');
        const fileLabel = document.getElementById('editMoaFileLabel');
        if (fileInput) {
            fileInput.value = '';
        }
        if (fileLabel) {
            fileLabel.textContent = 'Leave empty to keep the current notarized MOA PDF';
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
