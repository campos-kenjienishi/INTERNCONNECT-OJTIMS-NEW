<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Smaller font for MOA table and action buttons */
        .table-card-body table {
            font-size: 13px;
        }
        .table-card-body table th,
        .table-card-body table td {
            font-size: 13px;
            vertical-align: middle;
        }
        .btn-action, .btn-modal-print, .btn-modal-close {
            font-size: 12px !important;
            padding: 7px 14px !important;
        }
        .btn-action i, .btn-modal-print i, .btn-modal-close i {
            font-size: 13px !important;
        }
        .modal-field-label, .modal-field-input, .modal-field-select, .modal-field-textarea {
            font-size: 13px !important;
        }
        .company-name-text {
            font-size: 13px !important;
        }

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

        /* =============== FILTER CARD =============== */
        .filter-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden; margin-bottom: 22px;
        }
        .filter-card-header {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 24px; border-bottom: 1px solid #f0f0f0; background: #fafafa;
        }
        .filter-header-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: #fee2e2; display: flex; align-items: center; justify-content: center;
            color: var(--red); font-size: 14px; flex-shrink: 0;
        }
        .filter-card-header h2 { font-size: 15px; font-weight: 700; color: #1a1a1a; }
        .filter-card-header p  { font-size: 12px; color: #888; margin-top: 1px; }
        .filter-card-body {
            padding: 22px 24px; display: flex;
            align-items: flex-end; gap: 20px; flex-wrap: wrap;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-label { font-size: 12.5px; font-weight: 600; color: #444; display: flex; align-items: center; gap: 6px; }
        .filter-label i { color: var(--red); font-size: 11px; }
        .filter-select {
            background: #fafafa; border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; padding: 10px 14px; outline: none;
            transition: all 0.25s; min-width: 160px;
        }
        .filter-select:focus { border-color: var(--red); background: #fff; box-shadow: 0 0 0 3px rgba(220,38,38,0.07); }
        .year-range-wrap { display: flex; align-items: center; gap: 10px; }
        .year-separator { font-size: 14px; font-weight: 600; color: #888; }
        .error-hint {
            font-size: 11.5px; color: var(--red);
            display: none; margin-top: 4px;
        }
        .filter-actions { display: flex; gap: 10px; align-items: flex-end; margin-left: auto; }
        .btn-generate {
            display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13.5px; font-weight: 600;
            cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }
        .btn-generate:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220,38,38,0.35); }
        .btn-print-preview {
            display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px;
            background: #fff; border: 1.5px solid #e0e7ff;
            border-radius: 10px; color: #4f46e5;
            font-family: 'Poppins', sans-serif; font-size: 13.5px; font-weight: 600;
            cursor: pointer; transition: all 0.25s;
        }
        .btn-print-preview:hover { background: #e0e7ff; }

        /* =============== STATS =============== */
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border-radius: 14px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }
        .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* =============== TABLE CARD =============== */
        .table-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
        .table-card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 24px; border-bottom: 1px solid #f0f0f0; background: #fafafa; flex-wrap: wrap; }
        .table-card-header-left { display: flex; align-items: center; gap: 12px; }
        .header-icon { width: 38px; height: 38px; border-radius: 10px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 15px; flex-shrink: 0; }
        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }
        .moa-count-badge { display: inline-flex; align-items: center; gap: 6px; background: #fee2e2; color: var(--red); border-radius: 20px; padding: 5px 14px; font-size: 12.5px; font-weight: 700; }

        /* DataTables */
        .table-card-body .dataTables_wrapper { padding: 16px 22px; font-family: 'Poppins', sans-serif; font-size: 13.5px; }
        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }
        .table-card-body table.dataTable thead th { background: #fafafa; color: #555; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; border-top: none; }
        .table-card-body table.dataTable tbody td { padding: 14px; color: #333; border-bottom: 1px solid #f9f9f9; font-size: 13.5px; vertical-align: middle; }
        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }
        .dataTables_wrapper { color: #333 !important; }
        .dataTables_length { color: #333 !important; background: #f5f5f5; padding: 8px 12px; border-radius: 6px; display: inline-block; margin-bottom: 10px; }
        .dataTables_length label { color: #333 !important; font-weight: 500; }
        .dataTables_filter { color: #333 !important; background: #f5f5f5; padding: 8px 12px; border-radius: 6px; margin-bottom: 10px; float: right; }
        .dataTables_filter label { color: #333 !important; font-weight: 500; }
        .dataTables_filter input { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 6px 12px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; outline: none !important; color: #333 !important; background: #fff !important; }
        .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important; }
        .dataTables_length select { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 4px 8px !important; font-family: 'Poppins', sans-serif !important; color: #333 !important; background: #fff !important; }
        .dataTables_info { color: #555 !important; font-weight: 500 !important; background: #f5f5f5; padding: 8px 12px; border-radius: 6px; display: inline-block; margin-top: 10px; }
        .dataTables_paginate { color: #333 !important; }
        .dataTables_paginate .paginate_button { border-radius: 6px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; color: #333 !important; background: #f5f5f5 !important; border-color: #ddd !important; }
        .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        .dataTables_paginate .paginate_button:hover { background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important; }

        /* Company cell */
        .company-cell { display: flex; align-items: center; gap: 10px; }
        .company-icon-box { width: 34px; height: 34px; border-radius: 9px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 13px; flex-shrink: 0; }
        .company-name-text { font-weight: 600; color: #1a1a1a; }
        .company-name-cell { color: #1a1a1a !important; }

        /* Status badges */
        .badge-active { display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #16a34a; border-radius: 20px; padding: 4px 12px; font-size: 12px; font-weight: 700; }
        .badge-expired { display: inline-flex; align-items: center; gap: 5px; background: #fee2e2; color: var(--red); border-radius: 20px; padding: 4px 12px; font-size: 12px; font-weight: 700; }
        .badge-active i, .badge-expired i { font-size: 10px; }
        .moa-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .btn-moa-action {
            display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
            padding: 7px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-moa-download {
            background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;
        }
        .btn-moa-download:hover { background: #bae6fd; color: #075985; }
        .btn-moa-print {
            background: #fff; color: #7f0000; border: 1px solid #e5e7eb;
        }
        .btn-moa-print:hover { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

        /* Dark mode status badges */
        body.dark-mode .badge-active { background: rgba(22,163,74,0.2); color: #4ade80; }
        body.dark-mode .badge-expired { background: rgba(220,38,38,0.2); color: #ff6b6b; }
        body.dark-mode .btn-moa-download { background: rgba(37,99,235,0.18); color: #93c5fd; border-color: rgba(96,165,250,0.3); }
        body.dark-mode .btn-moa-download:hover { background: rgba(37,99,235,0.28); color: #bfdbfe; }
        body.dark-mode .btn-moa-print { background: rgba(255,255,255,0.04); color: #fecaca; border-color: rgba(255,255,255,0.12); }
        body.dark-mode .btn-moa-print:hover { background: rgba(220,38,38,0.14); color: #fff1f2; border-color: rgba(248,113,113,0.3); }
        body.dark-mode .table-card-body table.dataTable thead th { background: #3a3a3a; color: #e0e0e0; border-bottom: 1px solid #555; }

        /* =============== PRINT PREVIEW MODAL =============== */
        .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); font-family: 'Poppins', sans-serif; overflow: hidden; }
        /* Slim header — no title text, just close button */
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
            .filter-card-body { flex-direction: column; align-items: stretch; }
            .filter-actions { margin-left: 0; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .year-range-wrap { flex-wrap: wrap; }
        }

        .darkmode-toggle {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            flex-shrink: 0;
            padding: 0;
        }

        .darkmode-toggle:hover {
            background: #fee2e2; color: var(--red); border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        .darkmode-toggle:active { transform: scale(0.95); }

        body.dark-mode .darkmode-toggle {
            background: #2a2a2a;
            border-color: #3a3a3a;
            color: #e8e8e8;
        }

        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2);
            color: #ff6b6b;
            border-color: rgba(220,38,38,0.3);
            box-shadow: 0 6px 16px rgba(220,38,38,0.3);
            transform: translateY(-2px);
        }

        /* Dark mode DataTables */
        body.dark-mode .dataTables_wrapper { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_length { background: #3a3a3a !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_length label { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_length select { background: #2a2a2a !important; color: #e0e0e0 !important; border-color: #555 !important; }
        body.dark-mode .dataTables_filter { background: #3a3a3a !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_filter label { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_filter input { background: #2a2a2a !important; color: #e0e0e0 !important; border-color: #555 !important; }
        body.dark-mode .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.2) !important; }
        body.dark-mode .dataTables_info { background: #3a3a3a !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_paginate .paginate_button { background: #3a3a3a !important; border-color: #555 !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_paginate .paginate_button:hover { background: rgba(220,38,38,0.2) !important; border-color: var(--red) !important; color: #ff6b6b !important; }
        body.dark-mode .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        body.dark-mode .dataTables_paginate .paginate_button.disabled { color: #777 !important; }
        body.dark-mode .company-name-cell { color: #fff !important; }
        body.dark-mode .table-card-body table.dataTable tbody td.company-name-cell { color: #fff !important; }
        body.dark-mode .moa-count-badge { background: rgba(220,38,38,0.2) !important; color: #ff6b6b !important; }
        body.dark-mode .table-card-body table.dataTable tbody td { color: #e0e0e0; border-bottom: 1px solid #2a2a2a; }
        body.dark-mode .table-card-body table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.1); }

        /* =============== NATIVE CSS PRINT SOLUTION =============== */
        @media screen {
            #print-area-wrapper { display: none !important; }
        }
        @media print {
            /* Hide everything on the page except the print wrapper */
            body > *:not(#print-area-wrapper) { display: none !important; }
            
            /* Show the print wrapper natively */
            #print-area-wrapper { 
                display: block !important; 
                width: 100%; 
            }

            /* Fix Bootstrap modal bug that cuts off multiple pages */
            body, body.modal-open {
                overflow: visible !important;
                height: auto !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                /* Add padding here so the content doesn't touch the paper edge */
                padding: 10mm !important; 
            }

            /* REMOVE DEFAULT BROWSER HEADERS AND FOOTERS */
            @page { 
                margin: 0; 
            }
        }
        /* Custom Status Filter Dropdown */
        .custom-status-filter {
            background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; 
            padding: 4px 8px; font-family: 'Poppins', sans-serif; 
            outline: none; color: #333; margin-left: 5px;
        }
        body.dark-mode .custom-status-filter {
            background: #2a2a2a; color: #e0e0e0; border-color: #555;
        }
    </style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div id="print-area-wrapper"></div>

<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($user->profile_photo) && $user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
            <span class="user-role">Professor</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/professor/home') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Dashboard</span>
            <span class="tooltip-label">Dashboard</span>
        </a>
        <a href="{{ url('/professor/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ url('/professor/analytics') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
        </a>
        <a href="{{ url('/reportsExpiredProf') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/professor/maintain') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cogs"></i></span>
            <span class="nav-label">Maintenance</span>
            <span class="tooltip-label">Maintenance</span>
        </a>
            <a href="{{ url('/professor/evaluation') }}" class="nav-item{{ request()->is('professor/evaluation*') ? ' active' : '' }}">
            <span class="nav-icon"><i class="fa fa-star-half-alt"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
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

<div class="main-content" id="mainContent">

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-chalkboard-teacher"></i> Professor Portal
            </div>
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <div>
                <h1>Memorandum of <span>Agreement</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>MOA</span>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h2>Generate MOA Report</h2>
                    <p>Filter by school year and course to generate a report</p>
                </div>
            </div>
            <form action="{{ route('reports.generate.prof') }}" method="post">
                @csrf
                <div class="filter-card-body">
                    <div class="filter-group">
                        <label class="filter-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                        <div class="year-range-wrap">
                            <select class="filter-select" name="school_year_start" id="school_year_start" required>
                                <option value="">Start Year</option>
                                @for ($year = 2018; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}" {{ (string) request('school_year_start') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            <span class="year-separator">—</span>
                            <select class="filter-select" name="school_year_end" id="school_year_end" data-selected="{{ request('school_year_end') }}" required>
                                <option value="">End Year</option>
                                @for ($year = 2019; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}" {{ (string) request('school_year_end') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <span class="error-hint" id="school_year-error">Please select the school year.</span>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label"><i class="fa fa-graduation-cap"></i> Course</label>
                        <select class="filter-select" name="course" id="courseSelect" required style="min-width:220px;">
                            @foreach ($courseAll as $c)
                                <option value="{{ $c->course }}" {{ request('course') === $c->course ? 'selected' : '' }}>{{ $c->course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="button" class="btn-print-preview" id="openPreviewBtn">
                            <i class="fa fa-print"></i> Print Preview
                        </button>
                        <button type="submit" class="btn-generate">
                            <i class="fa fa-chart-bar"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @php
            $totalMOA   = count($companies);
            $activeMOA  = 0;
            $expiredMOA = 0;
            foreach ($companies as $c) {
                [$sy, $ey] = explode('-', $c->school_year);
                $diff = now()->year - (int)$sy;
                if ($diff > 3) $expiredMOA++; else $activeMOA++;
            }
        @endphp

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">{{ $totalMOA }}</div>
                    <div class="stat-name">Total MOA</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $activeMOA }}</div>
                    <div class="stat-name">Active</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-exclamation-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $expiredMOA }}</div>
                    <div class="stat-name">Expired</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalMOA }}</div>
                    <div class="stat-name">Partner Companies</div>
                </div>
            </div>
        </div>

        @if(!empty($reportInsights))
            <div class="filter-card" style="margin-bottom:22px; border-left:4px solid var(--red);">
                <div class="filter-card-header">
                    <div class="filter-header-icon"><i class="fa fa-robot"></i></div>
                    <div>
                        <h2>AI Report Insight</h2>
                        <p>Generated from the current MOA report data</p>
                    </div>
                    <div style="margin-left:auto; display:inline-flex; align-items:center; gap:6px; background:#fff5f5; border:1px solid #fecaca; color:var(--red-dark); border-radius:999px; padding:5px 12px; font-size:12px; font-weight:700;">
                        <i class="fa fa-brain"></i>
                        {{ !empty($reportInsights['used_local_ai']) ? 'Local AI' : 'Internal Insight' }}
                    </div>
                </div>
                <div class="filter-card-body" style="display:block;">
                    <p style="font-size:14px; line-height:1.7; color:#333; margin-bottom:16px;">{{ $reportInsights['summary'] ?? 'No AI insight available.' }}</p>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul style="margin:0; padding-left:18px; color:#444; line-height:1.65;">
                                @foreach(($reportInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul style="margin:0; padding-left:18px; color:#444; line-height:1.65;">
                                @forelse(($reportInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:var(--red); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                            <ul style="margin:0; padding-left:18px; color:#444; line-height:1.65;">
                                @foreach(($reportInsights['recommendations'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-contract"></i></div>
                    <div>
                        <h2>MOA Records</h2>
                        <p>Memorandum of Agreement with partner companies</p>
                    </div>
                </div>
                <div class="moa-count-badge">
                    <i class="fa fa-building"></i>
                    {{ $totalMOA }} {{ $totalMOA == 1 ? 'record' : 'records' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="companyTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Representative</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        @php
                            $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;
                            $status = ($validUntil && now()->lte($validUntil)) ? 'Active' : 'Expired';
                        @endphp
                        <tr>
                            <td>{{ $company->id }}</td>
                            <td class="company-name-cell">
                                <div class="company-cell">
                                    <div class="company-icon-box"><i class="fa fa-building"></i></div>
                                    <span class="company-name-text">{{ $company->company_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-map-marker-alt" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->company_address }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-user-tie" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->company_rep }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-phone" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->companyNo }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-envelope" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $company->company_email }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-calendar" style="color:var(--red);font-size:12px;flex-shrink:0;"></i>
                                    {{ $validUntil ? $validUntil->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if($status === 'Active')
                                    <span class="badge-active"><i class="fa fa-circle"></i> Active</span>
                                @else
                                    <span class="badge-expired"><i class="fa fa-times-circle"></i> Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="moa-actions">
                                    @if($company->file)
                                        <a href="{{ url('/moa/download/' . $company->file) }}"
                                           class="btn-moa-action btn-moa-download"
                                           target="_blank"
                                           rel="noopener">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    @endif
                                    <a href="{{ url('/moa/view/' . $company->id) }}"
                                       class="btn-moa-action btn-moa-print"
                                       target="_blank"
                                       rel="noopener">
                                        <i class="fa fa-print"></i> Print
                                    </a>
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

<div id="printPreviewModal" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="visibility:hidden; font-size:0;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="printPreviewContent" style="background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,0.12); overflow:hidden;">
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

<form id="sendEmailForm" action="{{ url('/reportsExpired/send-email') }}" method="post" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="hidden" id="courseHidden" name="course" value="{{ $course ?? '' }}">
    <input type="hidden" id="emailHidden"  name="email"  value="{{ $user->email ?? '' }}">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
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
        var table = $('#companyTable').DataTable({
            // Sort by validity (column 6) from latest to oldest
            order: [[6, 'desc']], 
            columnDefs: [
                { targets: 0, visible: false }, // Hide ID column
                // Disable the sorting dropdown arrows on all visible columns
                { targets: [1, 2, 3, 4, 5, 6, 7, 8], orderable: false } 
            ]
        });

        // Add the custom Status Filter right next to "Show entries"
        $('.dataTables_length').append(
            '<label style="margin-left: 20px; font-weight: 500;">Filter Status: ' +
            '<select id="statusFilter" class="custom-status-filter">' +
            '<option value="">All MOAs</option>' +
            '<option value="Active">Active</option>' +
            '<option value="Expired">Expired</option>' +
            '</select></label>'
        );

        // When the user picks "Active" or "Expired", filter the table
        $('#statusFilter').on('change', function() {
            table.column(7).search(this.value).draw();
        });
    });

    /* ── Dynamic end-year options ── */
    document.addEventListener('DOMContentLoaded', function () {
        const startSel = document.getElementById('school_year_start');
        const endSel   = document.getElementById('school_year_end');
        const selectedEndYear = endSel.dataset.selected || '';

        function updateEndYears() {
            const sy = parseInt(startSel.value);
            endSel.innerHTML = '<option value="">End Year</option>';
            if (!isNaN(sy)) {
                for (let y = sy + 1; y <= sy + 10; y++) {
                    const opt = document.createElement('option');
                    opt.value = y; opt.textContent = y;
                    if (String(y) === selectedEndYear) {
                        opt.selected = true;
                    }
                    endSel.appendChild(opt);
                }
            }
        }
        updateEndYears();
        startSel.addEventListener('change', updateEndYears);
    });

    /* ══════════════════════════════════════════════
       BUILD PRINT HTML  — branded MOA layout
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

        const syStart  = document.getElementById('school_year_start').value || '—';
        const syEnd    = document.getElementById('school_year_end').value   || '—';
        const course   = document.getElementById('courseSelect').value      || '—';
        const syLabel  = (syStart !== '—' && syEnd !== '—') ? syStart + ' – ' + syEnd : '—';

        let rowsHTML = '';
        for (let i = 0; i < currentPageNodes.length; i++) {
            const tds  = currentPageNodes[i].querySelectorAll('td');
            const getCompany = () => {
                const cn = Array.from(tds).find(td => td.querySelector('.company-name-text'));
                return cn ? cn.querySelector('.company-name-text').textContent.trim() : '';
            };
            const getAddress = () => {
                const addr = Array.from(tds).find(td => td.querySelector('[class*="fa-map-marker"]'));
                if (!addr) return '';
                let text = '';
                addr.childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) text += node.textContent;
                    else if (node.nodeName !== 'I') text += node.textContent;
                });
                return text.trim();
            };
            const getRep = () => {
                const rep = Array.from(tds).find(td => td.querySelector('[class*="fa-user-tie"]'));
                if (!rep) return '';
                let text = '';
                rep.childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) text += node.textContent;
                    else if (node.nodeName !== 'I') text += node.textContent;
                });
                return text.trim();
            };
            const getContact = () => {
                const contact = Array.from(tds).find(td => td.querySelector('[class*="fa-phone"]'));
                if (!contact) return '';
                let text = '';
                contact.childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) text += node.textContent;
                    else if (node.nodeName !== 'I') text += node.textContent;
                });
                return text.trim();
            };
            const getEmail = () => {
                const email = Array.from(tds).find(td => td.querySelector('[class*="fa-envelope"]'));
                if (!email) return '';
                let text = '';
                email.childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) text += node.textContent;
                    else if (node.nodeName !== 'I') text += node.textContent;
                });
                return text.trim();
            };
            const getSY = () => {
                const sy = Array.from(tds).find(td => td.querySelector('[class*="fa-calendar"]'));
                if (!sy) return '';
                let text = '';
                sy.childNodes.forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) text += node.textContent;
                    else if (node.nodeName !== 'I') text += node.textContent;
                });
                return text.trim();
            };
            const getStatus = () => {
                const status = Array.from(tds).find(td => td.querySelector('.badge-active, .badge-expired'));
                return status ? status.querySelector('.badge-active, .badge-expired').textContent.trim() : '';
            };
            const statusTd = Array.from(tds).find(td => td.querySelector('.badge-active, .badge-expired'));
            const isActive = statusTd && statusTd.querySelector('.badge-active');
            const statusBadge = isActive
                ? `<span style="display:inline-block;background:#dcfce7;color:#16a34a;border-radius:4px;padding:1px 7px;font-size:8px;font-weight:700;">Active</span>`
                : `<span style="display:inline-block;background:#fee2e2;color:#dc2626;border-radius:4px;padding:1px 7px;font-size:8px;font-weight:700;">Expired</span>`;

            const rowNum = pageInfo.start + i + 1;
            const rowBg  = i % 2 === 0 ? '#ffffff' : '#f9fafb';

            rowsHTML += `
            <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                <td style="padding:7px 6px; font-size:9px; font-weight:700; color:#6b7280; text-align:center; vertical-align:top; border-right:1px solid #e5e7eb;">${rowNum}</td>
                <td style="padding:7px 6px; font-size:9.5px; font-weight:700; color:#111827; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${getCompany()}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#4b5563; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${getAddress()}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${getRep()}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${getContact()}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${getEmail()}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; white-space:nowrap;">${getSY()}</td>
                <td style="padding:7px 6px; vertical-align:top; text-align:center;">${statusBadge}</td>
            </tr>`;
        }

        return `
        <div style="font-family:'Poppins',Arial,sans-serif; background:#fff;">
            <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); padding:0;">
                <div style="background:rgba(255,255,255,0.12); height:4px;"></div>
                <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                    <div style="width:50px; height:50px; background:rgba(255,255,255,0.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,0.25);">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);">
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines — OJT Information Management System</div>
                        <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">MOA Report — Partner Companies</div>
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
                <div style="font-size:8.5px; color:#9ca3af;">Generated: ${dateStr}</div>
            </div>
            <div style="padding:9px 22px 3px 22px;">
                <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">Partner Company Details — Page ${pageNum}</div>
            </div>
            <div style="padding:4px 22px 0 22px;">
                <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-family:'Poppins',Arial,sans-serif; border:1px solid #d1d5db;">
                    <colgroup>
                        <col style="width:3%;">
                        <col style="width:18%;">
                        <col style="width:18%;">
                        <col style="width:14%;">
                        <col style="width:12%;">
                        <col style="width:18%;">
                        <col style="width:9%;">
                        <col style="width:8%;">
                    </colgroup>
                    <thead>
                        <tr style="background:#7f0000;">
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,0.15);">#</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Company Name</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Address</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Representative</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Contact No.</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Email</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,0.15);">Validity</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHTML || `<tr><td colspan="8" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No records found for the selected filters.</td></tr>`}
                    </tbody>
                </table>
            </div>

            <div class="keep-together">
                <div style="padding:18px 22px 12px 22px;">
                    <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                        <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                            <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Disclaimer</div>
                            <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">
                                This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.
                            </div>
                        </div>
                    </div>
                </div>
                <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:0.7; filter:brightness(2);">
                        <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">© 1998–2026 <strong style="color:#fca5a5;">Polytechnic University of the Philippines</strong> — InternConnect OJT IMS</span>
                    </div>
                    <span style="font-size:8px; color:rgba(255,255,255,0.5);">Ref: MOA-RPT-${now.getFullYear()}</span>
                </div>
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

    /* ── Print ── */
    document.getElementById('doPrintBtn').addEventListener('click', function () {
        // 1. Put the generated HTML into the hidden print wrapper on the main page
        document.getElementById('print-area-wrapper').innerHTML = buildPrintHTML();
        
        // 2. Trigger the native browser print dialog
        window.print();
        
        // 3. Clear the wrapper after printing to free memory
        setTimeout(function() {
            document.getElementById('print-area-wrapper').innerHTML = '';
        }, 1000);
    });

    /* ── Send email (preserved from original) ── */
    function sendEmail(course, userEmail) {
        document.getElementById('sendEmailBtn') &&
            (document.getElementById('sendEmailBtn').disabled = true);
        $.ajax({
            url: "{{ route('reportsExpired.send.email') }}",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', email: userEmail, course: course },
            success:  function ()    { alert('Email sent successfully!'); },
            error:    function (xhr) { console.error(xhr.responseText); },
            complete: function ()    {
                document.getElementById('sendEmailBtn') &&
                    (document.getElementById('sendEmailBtn').disabled = false);
            }
        });
    }
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
