<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Upload Templates</title>
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

        body.dark-mode { background: #000000; color: #e0e0e0; }
        body.dark-mode .main-content { background: #000000; }
        body.dark-mode .sidebar { box-shadow: 4px 0 24px rgba(0,0,0,0.4); }

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
        .darkmode-toggle { width: 38px; height: 38px; border-radius: 10px; background: #f5f5f5; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #333; font-size: 18px; transition: all 0.2s; }
        .darkmode-toggle:hover { background: #fee2e2; color: var(--red); }
        .darkmode-toggle:active { transform: scale(0.95); }
        body.dark-mode .darkmode-toggle { background: #3a3a3a; border-color: #555; color: #e8e8e8; }
        body.dark-mode .darkmode-toggle:hover { background: rgba(220,38,38,0.2); color: #ff6b6b; border-color: rgba(220,38,38,0.3); box-shadow: 0 6px 16px rgba(220,38,38,0.3); transform: translateY(-2px); }
        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }
        body.dark-mode .topbar-title { color: #999; }
        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red-dark);
        }
        body.dark-mode .topbar-badge { background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b; }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }
        body.dark-mode .page-content { background: #000000; }

        .page-header {
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
        body.dark-mode .page-header h1 { color: #fff; }
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #888; margin-top: 6px;
        }

        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }
        body.dark-mode .breadcrumb { color: #999; }
        body.dark-mode .breadcrumb-nav { color: #999; }

        .btn-upload {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-upload:hover {
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
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }

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

        .file-count-badge {
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

        /* Horizontal scroll container for mobile */
        .table-card-body {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }

        .table-card-body table.dataTable thead th {
            background: #fafafa; color: #555;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 10px 12px;
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
            border-bottom: 1px solid #f9f9f9;
            font-size: 13px; vertical-align: middle;
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

        /* File cell */
        .file-cell { display: flex; align-items: center; gap: 10px; }

        .file-icon-box {
            width: 36px; height: 36px; border-radius: 9px;
            background: #dbeafe; display: flex;
            align-items: center; justify-content: center;
            color: #2563eb; font-size: 14px; flex-shrink: 0;
        }

        .file-name-text { font-weight: 600; color: #1a1a1a; font-size: 13px; }
        .file-raw-name  { font-size: 11.5px; color: #aaa; margin-top: 1px; }

        /* Date cell */
        .date-cell { display: flex; align-items: center; gap: 6px; color: #666; font-size: 13px; }
        .date-cell i { color: var(--red); font-size: 11px; }

        /* Action buttons */
        .btn-download {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(37,99,235,0.2);
            text-decoration: none;
        }

        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
            color: #fff; text-decoration: none;
        }

        .btn-remove {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            background: #fff; border: 1.5px solid #fecaca; color: var(--red);
            font-family: 'Poppins', sans-serif; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-remove:hover { background: #fee2e2; }

        .actions-wrap { display: flex; align-items: center; gap: 8px; }

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

        .field-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
        .field-group:last-child { margin-bottom: 0; }

        .field-label {
            font-size: 12px; font-weight: 600; color: #444;
            display: flex; align-items: center; gap: 5px;
        }

        .field-label i { color: var(--red); font-size: 11px; }

        .field-input {
            width: 100%; background: #fafafa;
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13px; padding: 10px 13px; outline: none;
            transition: all 0.25s;
        }

        .field-input:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        /* File drop zone */
        .file-dropzone {
            border: 2px dashed #e8e8e8; border-radius: 12px;
            padding: 28px 20px; text-align: center;
            background: #fafafa; transition: all 0.25s; cursor: pointer;
            position: relative;
        }

        .file-dropzone:hover, .file-dropzone.dragover {
            border-color: var(--red); background: #fff5f5;
        }

        .file-dropzone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%;
        }

        .file-dropzone-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 20px;
            margin: 0 auto 12px;
        }

        .file-dropzone-title { font-size: 13.5px; font-weight: 600; color: #333; }
        .file-dropzone-sub   { font-size: 12px; color: #aaa; margin-top: 4px; }
        .file-dropzone-name  { font-size: 12.5px; color: var(--red); font-weight: 600; margin-top: 8px; display: none; }

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

        /* Mobile overlay */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
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
body.dark-mode .btn-download { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
body.dark-mode .btn-remove { border: 1.5px solid rgba(220,38,38,0.3); background: transparent; color: #ff6b6b; }
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

/* Cards */
body.dark-mode .card { background: #2a2a2a; border: 1px solid #3a3a3a; }

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
        <a href="{{ url('/uploadpage') }}" class="nav-item active">
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
                <h1>Upload <span>Templates</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Upload Templates</span>
                </div>
            </div>
            <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fa fa-cloud-upload-alt"></i> Upload New Template
            </button>
        </div>

        <!-- Stats Row -->
        @php $totalFiles = count($data); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $totalFiles }}</div>
                    <div class="stat-name">Total Templates</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-word"></i></div>
                <div>
                    <div class="stat-num">DOCX</div>
                    <div class="stat-name">Accepted Format</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-cloud-upload-alt"></i></div>
                <div>
                    <div class="stat-num">Active</div>
                    <div class="stat-name">Upload Status</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-download"></i></div>
                <div>
                    <div class="stat-num">{{ $totalFiles }}</div>
                    <div class="stat-name">Available Downloads</div>
                </div>
            </div>
        </div>

        <!-- Templates Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-upload"></i></div>
                    <div>
                        <h2>Uploaded Templates</h2>
                        <p>Manage the OJT document templates that you uploaded</p>
                    </div>
                </div>
                <div class="file-count-badge">
                    <i class="fa fa-paperclip"></i>
                    {{ $totalFiles }} {{ $totalFiles == 1 ? 'file' : 'files' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>File</th>
                            <th>Date Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $file)
                        <tr>
                            <!-- Template Name -->
                            <td>
                                <div class="file-cell">
                                    <div class="file-icon-box">
                                        <i class="fa fa-file-word"></i>
                                    </div>
                                    <div>
                                        <div class="file-name-text">{{ $file->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Raw filename -->
                            <td>
                                <span style="font-size:12.5px; color:#888; font-family:monospace;">
                                    {{ $file->file }}
                                </span>
                            </td>

                            <!-- Date -->
                            <td>
                                <div class="date-cell">
                                    <i class="fa fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y  h:i A') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">
                                    <a class="btn-download btn-dl-item"
                                       href="{{ url('/download', $file->file) }}">
                                        <i class="fa fa-download"></i> Download
                                    </a>

                                    <button class="btn-remove remove-button"
                                        data-file-id="{{ $file->id }}">
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

<!-- =============== UPLOAD MODAL =============== -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-cloud-upload-alt"></i> Upload New Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" action="{{ url('/uploadfile') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-tag"></i> Template Name
                        </label>
                        <input class="field-input" type="text" name="name"
                               placeholder="e.g. OJT Endorsement Letter" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-file-word"></i> Choose File
                        </label>
                        <div class="file-dropzone" id="dropzone">
                            <input type="file" name="file" id="fileInput" data-max-size-mb="2"
                                   accept=".doc,.docx" required>
                            <div class="file-dropzone-icon">
                                <i class="fa fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-dropzone-title">Click or drag to upload</div>
                            <div class="file-dropzone-sub">Accepted: .doc, .docx files only | Max file size: 2 MB</div>
                            <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                            <div class="file-dropzone-name" id="selectedFileName"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-cloud-upload-alt me-1"></i> Upload
                    </button>
                </div>
            </form>
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

    // Dark mode toggle
    $(document).ready(function () {

        // DataTable
        $('#fileTable').DataTable({
            order: [],
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false
        });

        // File input: show selected filename in dropzone
        $('#fileInput').on('change', function () {
            const fileName = this.files[0] ? this.files[0].name : '';
            if (fileName) {
                $('#selectedFileName').text(fileName).show();
                $('#dropzone').css('border-color', 'var(--red)');
            }
        });

        // Drag and drop visual feedback
        const dz = document.getElementById('dropzone');
        dz.addEventListener('dragover',  () => dz.classList.add('dragover'));
        dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
        dz.addEventListener('drop',      () => dz.classList.remove('dragover'));

        // Download button with SweetAlert toast
        $(document).on('click', '.btn-dl-item', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');

            const Toast = Swal.mixin({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 3000,
                timerProgressBar: true,
            });
            Toast.fire({ icon: 'info', title: 'File download initiated' });

            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', '');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Remove button with SweetAlert confirmation
        $(document).on('click', '.remove-button', function () {
            const fileId = $(this).data('file-id');
            Swal.fire({
                title: 'Remove Template?',
                text: 'This will permanently delete the template.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/remove/' + fileId,
                        data: { _token: "{{ csrf_token() }}" },
                        success: function () { location.reload(); },
                        error:   function () { console.error('Remove failed.'); }
                    });
                }
            });
        });

        // File validation error alert
        @if ($errors->has('file'))
            Swal.fire({
                icon: 'error',
                title: 'Upload Error',
                text: "{{ $errors->first('file') }}",
                confirmButtonColor: '#dc2626',
            });
        @endif

    });
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/upload-size-guard.js') }}"></script>

<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
