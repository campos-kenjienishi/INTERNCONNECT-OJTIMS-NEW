<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Reports</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
        body.dark-mode .page-header h1 { color: #fff; }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        body.dark-mode .breadcrumb { color: #999; }
        body.dark-mode .breadcrumb-nav { color: #999; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* =============== STATS =============== */
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border-radius: 14px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .stat-icon.red   { background: #fee2e2; color: var(--red); }
        .stat-icon.blue  { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber { background: #fef9c3; color: #ca8a04; }
        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* =============== FILTER CARD =============== */
        .panel-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 22px; }
        .panel-card-header { display: flex; align-items: center; gap: 12px; padding: 16px 22px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        .panel-header-icon { width: 36px; height: 36px; border-radius: 9px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 14px; flex-shrink: 0; }
        .panel-card-header h2 { font-size: 15px; font-weight: 700; color: #1a1a1a; }
        .panel-card-header p  { font-size: 12px; color: #888; margin-top: 2px; }
        .panel-card-body { padding: 22px; }
        .filter-grid { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 16px; align-items: end; }
        .field-group { display: flex; flex-direction: column; gap: 5px; }
        .field-label { font-size: 12px; font-weight: 600; color: #444; display: flex; align-items: center; gap: 5px; }
        .field-label i { color: var(--red); font-size: 11px; }
        .field-input, .field-select { width: 100%; background: #fafafa; border: 1.5px solid #e8e8e8; border-radius: 10px; color: #1a1a1a; font-family: 'Poppins', sans-serif; font-size: 13px; padding: 10px 13px; outline: none; transition: all 0.25s; }
        .field-input:focus, .field-select:focus { border-color: var(--red); background: #fff; box-shadow: 0 0 0 3px rgba(220,38,38,0.07); }
        .flatpickr-input { font-family: 'Poppins', sans-serif !important; }
        .flatpickr-day.selected { background: var(--red) !important; border-color: var(--red) !important; }
        .flatpickr-day:hover { background: #fee2e2 !important; }
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
        .table-card-body .dataTables_wrapper { padding: 16px 22px; font-family: 'Poppins', sans-serif; font-size: 13px; overflow-x: auto; }
        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; min-width: 1000px; }
        .table-card-body table.dataTable thead th { background: #fafafa; color: #555; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 1px solid #f0f0f0; border-top: none; white-space: nowrap; }
        .table-card-body table.dataTable tbody td { padding: 11px 12px; color: #333; border-bottom: 1px solid #f9f9f9; font-size: 13px; vertical-align: middle; }
        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }
        .dataTables_filter input { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 6px 12px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; outline: none !important; }
        .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important; }
        .dataTables_length select { border: 1px solid #e5e5e5 !important; border-radius: 8px !important; padding: 4px 8px !important; font-family: 'Poppins', sans-serif !important; }
        .dataTables_paginate .paginate_button { border-radius: 6px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; }
        .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        .dataTables_paginate .paginate_button:hover { background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important; }

        /* Cell styles */
        .name-cell { display: flex; align-items: center; gap: 8px; }
        .name-avatar { width: 30px; height: 30px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: var(--red); font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .name-text { font-weight: 600; color: #1a1a1a; }
        .level-badge { display: inline-flex; align-items: center; background: #dbeafe; color: #2563eb; border-radius: 20px; padding: 2px 9px; font-size: 11px; font-weight: 600; }
        .date-badge { display: inline-flex; align-items: center; gap: 4px; background: #f0fdf4; color: #16a34a; border-radius: 20px; padding: 2px 8px; font-size: 11px; font-weight: 600; white-space: nowrap; }

        /* =============== PRINT PREVIEW MODAL =============== */
        .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); font-family: 'Poppins', sans-serif; overflow: hidden; }
        .modal-header { background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%); border-bottom: none; padding: 10px 16px; display: flex; justify-content: flex-end; }
        .modal-title { color: #fff; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }
        .modal-body { padding: 24px; background: #f0f0f0; max-height: 70vh; overflow-y: auto; }
        .modal-footer { background: #fafafa; border-top: 1px solid #f0f0f0; padding: 14px 24px; display: flex; justify-content: flex-end; gap: 10px; }
        .btn-modal-close { padding: 9px 20px; background: #f3f4f6; border: 1px solid #e5e5e5; border-radius: 8px; color: #555; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }
        .btn-modal-print { padding: 9px 24px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); border: none; border-radius: 8px; color: #fff; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.25s; box-shadow: 0 3px 10px rgba(220,38,38,0.2); }
        .btn-modal-print:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(220,38,38,0.3); }

        /* Footer */
        .dashboard-footer {
            background: #fff; border-top: 1px solid #f0f0f0;
            color: #888; padding: 18px 28px; font-size: 12.5px; margin-top: auto;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px;
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
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }

        /* ===== DARK MODE: COMPREHENSIVE STYLING ===== */
        body.dark-mode .topbar { background: #252525 !important; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
        body.dark-mode .page-header h1 { color: #fff; }
        body.dark-mode .breadcrumb { color: #999; }
        body.dark-mode .stat-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        body.dark-mode .stat-num { color: #fff; }
        body.dark-mode .stat-name { color: #999; }
        body.dark-mode .panel-card { background: #1a1a1a !important; border: 1px solid #3a3a3a !important; box-shadow: 0 2px 12px rgba(0,0,0,0.3) !important; }
        body.dark-mode .panel-card-header { background: #1a1a1a !important; border-bottom: 1px solid #3a3a3a !important; }
        body.dark-mode .panel-card-header h2 { color: #fff !important; }
        body.dark-mode .panel-card-header p { color: #999 !important; }
        body.dark-mode .panel-card-body { color: #e8e8e8 !important; background: #1a1a1a !important; }
        body.dark-mode .table-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
        body.dark-mode .table-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .table-card-header h2 { color: #fff; }
        body.dark-mode .table-card-header p { color: #999; }
        body.dark-mode .file-count-badge { background: rgba(220,38,38,0.2); color: #ff6b6b; }
        body.dark-mode .count-badge { background: rgba(220,38,38,0.2) !important; color: #ff6b6b !important; }
        body.dark-mode table.dataTable thead th { background: #2a2a2a; color: #aaa; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode table.dataTable tbody td { color: #e0e0e0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        body.dark-mode table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.1); }
        body.dark-mode .dataTables_filter input { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }
        body.dark-mode .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.2) !important; }
        body.dark-mode .dataTables_length select { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1px solid #3a3a3a !important; }
        body.dark-mode .dataTables_paginate .paginate_button { background: #3a3a3a !important; border-color: #3a3a3a !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_paginate .paginate_button:hover { background: #444 !important; border-color: #444 !important; }
        body.dark-mode .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; }
        body.dark-mode .dataTables_info { color: #999 !important; }
        body.dark-mode .field-input, body.dark-mode .field-select { background: #3a3a3a; color: #e0e0e0; border: 1px solid #3a3a3a; }
        body.dark-mode .field-input::placeholder { color: #888 !important; }
        body.dark-mode .field-input:focus, body.dark-mode .field-select:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(220,38,38,0.2); }
        body.dark-mode .field-label { color: #e0e0e0; }
        body.dark-mode .name-text { color: #fff !important; }
        body.dark-mode .name-avatar { background: rgba(220,38,38,0.3) !important; color: #ff6b6b !important; }
        body.dark-mode .level-badge { background: rgba(37, 99, 235, 0.2) !important; color: #60a5fa !important; }
        body.dark-mode .date-badge { background: rgba(22, 163, 74, 0.2) !important; color: #86efac !important; }
        body.dark-mode .report-time-cell { color: #e0e0e0 !important; }
        body.dark-mode .position-cell { color: #e0e0e0 !important; }
        body.dark-mode .company-address-cell { color: #fff !important; }
        body.dark-mode .btn-download { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        body.dark-mode .btn-remove { border: 1.5px solid rgba(220,38,38,0.3); background: transparent; color: #ff6b6b; }
        body.dark-mode .btn-remove:hover { background: rgba(220,38,38,0.1); }
        body.dark-mode .btn-preview { background: transparent !important; border: 1.5px solid rgba(79, 70, 229, 0.3) !important; color: #818cf8 !important; }
        body.dark-mode .btn-preview:hover { background: rgba(79, 70, 229, 0.1) !important; border-color: rgba(79, 70, 229, 0.5) !important; }
        body.dark-mode .modal-content { background: #1a1a1a !important; box-shadow: 0 20px 60px rgba(0,0,0,0.5) !important; }
        body.dark-mode .modal-body { background: #1a1a1a !important; color: #e8e8e8 !important; }
        body.dark-mode .modal-footer { background: #2a2a2a; border-top: 1px solid #3a3a3a; }
        body.dark-mode .btn-modal-close { background: #3a3a3a; border: 1px solid #3a3a3a; color: #e0e0e0; }
        body.dark-mode .btn-modal-close:hover { background: rgba(220,38,38,0.2); border-color: var(--red); color: #ff6b6b; }
        body.dark-mode .btn-modal-close:hover { background: rgba(220,38,38,0.2); }
        body.dark-mode .file-dropzone { border-color: #3a3a3a; background: #2a2a2a; }
        body.dark-mode .file-dropzone:hover, body.dark-mode .file-dropzone.dragover { border-color: var(--red); background: rgba(220,38,38,0.1); }
        body.dark-mode .file-dropzone-icon { background: rgba(220,38,38,0.2); }
        body.dark-mode .file-dropzone-title { color: #e0e0e0; }
        body.dark-mode .file-dropzone-sub { color: #999; }
        body.dark-mode .dashboard-footer { background: #1a1a1a; border-top: 1px solid #3a3a3a; color: #999; }
        body.dark-mode .dashboard-footer a { color: #999; }
        body.dark-mode .dashboard-footer a:hover { color: var(--red); }
        body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
        body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }
        body.dark-mode .card { background: #2a2a2a; border: 1px solid #3a3a3a; }

        @media (max-width: 900px) {
            .sidebar { width: var(--sidebar-w); transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .filter-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 560px) { .filter-grid { grid-template-columns: 1fr; } }

        .level-wrap{
            display:inline-block;
            max-width:80px;
            white-space:normal;
            word-break:break-word;
            text-align:center;
            line-height:1.1;
        }
        .level-badge{
            max-width:110px;
            white-space:normal;
            line-height:1.2;
            text-align:center;
        }

        /* =============== NATIVE CSS PRINT SOLUTION =============== */
        @media screen {
            #print-area-wrapper { display: none !important; }
        }
        @media print {
            body > *:not(#print-area-wrapper) { display: none !important; }
            #print-area-wrapper { display: block !important; width: 100%; }

            body, body.modal-open {
                overflow: visible !important;
                height: auto !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            @page { size: A4 landscape; margin: 5mm; }
        }
    </style>
</head>

<body>

<div id="print-area-wrapper"></div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

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
            <a href="{{ url('/reports') }}" class="nav-sub-item active">
                <i class="fa fa-user-graduate" style="margin-right:6px; font-size:11px;"></i> Student OJT Info
            </a>
            <a href="{{ url('/reportsExpired') }}" class="nav-sub-item">
                <i class="fa fa-file-contract" style="margin-right:6px; font-size:11px;"></i> MOA
            </a>
        </div>

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

<div class="main-content" id="mainContent">

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-user-shield"></i> OJT Coordinator
            </div>
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <div>
                <h1>Student OJT <span>Reports</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Reports</span>
                    <i class="fa fa-chevron-right"></i>
                    <span>Student OJT Information</span>
                </div>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-user-graduate"></i></div>
                <div>
                    <div class="stat-num">{{ count($studentData) }}</div>
                    <div class="stat-name">Total Records</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ collect($studentData)->pluck('ojt.company_name')->unique()->count() }}</div>
                    <div class="stat-name">Companies</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-chart-bar"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Report Type</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-calendar-alt"></i></div>
                <div>
                    <div class="stat-num">{{ now()->format('Y') }}</div>
                    <div class="stat-name">Current Year</div>
                </div>
            </div>
        </div>

        @if(!empty($reportInsights))
            <div class="panel-card" style="margin-bottom:22px; border-left:4px solid var(--red);">
                <div class="panel-card-header">
                    <div class="panel-header-icon"><i class="fa fa-robot"></i></div>
                    <div>
                        <h2>AI Report Insight</h2>
                        <p>Generated from the current OJT report data</p>
                    </div>
                    <div style="margin-left:auto; display:inline-flex; align-items:center; gap:6px; background:#fff5f5; border:1px solid #fecaca; color:var(--red-dark); border-radius:999px; padding:5px 12px; font-size:12px; font-weight:700;">
                        <i class="fa fa-brain"></i>
                        {{ !empty($reportInsights['used_local_ai']) ? 'Local AI' : 'Internal Insight' }}
                    </div>
                </div>
                <div class="panel-card-body">
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

        <div class="panel-card">
            <div class="panel-card-header">
                <div class="panel-header-icon"><i class="fa fa-filter"></i></div>
                <div>
                    <h2>Generate Report</h2>
                    <p>Filter by year range and course to generate the OJT report</p>
                </div>
            </div>
            <div class="panel-card-body">
                <form id="reportForm" action="{{ route('studentojt.report.generate') }}" method="post">
                    @csrf
                    <div class="filter-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-alt"></i> Start Year</label>
                            <select class="field-select" id="start_year" name="start_year" required>
                                <option value="">Select Start Year</option>
                                @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-check"></i> End Year</label>
                            <select class="field-select" id="end_year" name="end_year" required>
                                <option value="">Select End Year</option>
                            </select>
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

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-user-graduate"></i></div>
                    <div>
                        <h2>Student OJT Information</h2>
                        <p>OJT placement details for all students</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div class="count-badge">
                        <i class="fa fa-users"></i>
                        {{ count($studentData) }} {{ count($studentData) == 1 ? 'record' : 'records' }}
                    </div>
                    <button type="button" class="btn-preview" id="openPreviewBtn">
                        <i class="fa fa-print"></i> Print Preview
                    </button>
                </div>
            </div>

            <div class="table-card-body">
                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Company Name</th>
                            <th>Company Address</th>
                            <th>Nature of Business</th>
                            <th>Nature of Linkages</th>
                            <th>Level</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reporting Time</th>
                            <th>Contact Name</th>
                            <th>Position</th>
                            <th>Contact No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentData as $data)
                            @if(isset($data['ojt']) && $data['ojt'])
                            <tr>
                                <td>
                                    <div class="name-cell">
                                        <div class="name-avatar">{{ strtoupper(substr($data['student']->full_name, 0, 1)) }}</div>
                                        <span class="name-text">{{ $data['student']->full_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:5px;">
                                        <i class="fa fa-building" style="color:var(--red); font-size:11px;"></i>
                                        {{ $data['ojt']->company_name }}
                                    </div>
                                </td>
                                <td class="company-address-cell" style="max-width:160px; word-break:break-word; font-size:12.5px;">{{ $data['ojt']->company_address }}</td>
                                <td>{{ $data['ojt']->nature_of_bus }}</td>
                                <td>{{ $data['ojt']->nature_of_link }}</td>
                                <td>
                                <span class="level-badge">
                                {{ Str::limit($data['ojt']->level, 15) }}
                                </span>
                                </td>
                                <td><span class="date-badge"><i class="fa fa-calendar-alt"></i> {{ $data['ojt']->start_date }}</span></td>
                                <td><span class="date-badge"><i class="fa fa-calendar-check"></i> {{ $data['ojt']->finish_date }}</span></td>
                                <td class="report-time-cell" style="white-space:nowrap;"><i class="fa fa-clock" style="color:var(--red); font-size:11px; margin-right:4px;"></i>{{ $data['ojt']->report_time }}</td>
                                <td style="font-weight:600;">{{ $data['ojt']->contact_name }}</td>
                                <td class="position-cell" style="font-size:12.5px;">{{ $data['ojt']->contact_position }}</td>
                                <td style="white-space:nowrap;"><i class="fa fa-phone" style="color:var(--red); font-size:11px; margin-right:4px;"></i>{{ $data['ojt']->contact_number }}</td>
                            </tr>
                            @endif
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
            <div class="modal-header" style="padding:14px 20px;">
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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

    // Dark mode toggle
    const darkmodeToggle = document.getElementById('darkmodeToggle');
    const isDarkMode = localStorage.getItem('darkMode') === 'enabled';

    if (isDarkMode) {
        document.body.classList.add('dark-mode');
        darkmodeToggle.innerHTML = '<i class="fa fa-sun"></i>';
    }

    darkmodeToggle.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        darkmodeToggle.innerHTML = isDark ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>';
    });

    /* ── DataTable ── */
    $(document).ready(function () {
        $('#fileTable').DataTable({ order: [] });
    });

    /* ── Dynamic end year options ── */
    document.addEventListener('DOMContentLoaded', function () {
        const startYear = document.getElementById('start_year');
        const endYear = document.getElementById('end_year');

        function updateEndYears() {
            const selectedStartYear = parseInt(startYear.value, 10);
            endYear.innerHTML = '<option value="">Select End Year</option>';

            if (!isNaN(selectedStartYear)) {
                for (let year = selectedStartYear; year <= selectedStartYear + 10; year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    endYear.appendChild(option);
                }
            }
        }

        updateEndYears();
        startYear.addEventListener('change', updateEndYears);
    });

    /* ══════════════════════════════════════════════
       BUILD PRINT HTML
    ══════════════════════════════════════════════ */
    function buildPrintHTML() {
        const now      = new Date();
        const dateStr  = now.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
        const timeStr  = now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' });

        const dt = $('#fileTable').DataTable();

        const currentPageNodes = dt.rows({ page: 'current' }).nodes();
        const total            = currentPageNodes.length;
        const pageInfo         = dt.page.info();
        const pageNum          = pageInfo.page + 1;
        const pageCount        = pageInfo.pages;

        const startYear = document.getElementById('start_year').value || '—';
        const endYear   = document.getElementById('end_year').value   || '—';
        const course    = document.getElementById('course').value     || '—';

        let rowsHTML = '';
        for (let i = 0; i < currentPageNodes.length; i++) {
            const tds = currentPageNodes[i].querySelectorAll('td');

            const getName = (idx) => {
                if (!tds[idx]) return '';
                const nt = tds[idx].querySelector('.name-text');
                return nt ? nt.textContent.trim() : tds[idx].textContent.trim();
            };
            const get = (idx) => tds[idx] ? tds[idx].textContent.trim() : '';

            const rowNum = pageInfo.start + i + 1;
            const rowBg  = i % 2 === 0 ? '#ffffff' : '#f9fafb';

            rowsHTML += `
            <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                <td style="padding:7px 6px; font-size:9.5px; font-weight:700; color:#6b7280; vertical-align:top; text-align:center; border-right:1px solid #e5e7eb;">${rowNum}</td>
                <td style="padding:7px 6px; font-size:9.5px; font-weight:700; color:#111827; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${getName(0)}</td>
                <td style="padding:7px 6px; font-size:9px; color:#374151; vertical-align:top; border-right:1px solid #e5e7eb; word-break:break-word;">${get(1)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#4b5563; vertical-align:top; word-break:break-word; border-right:1px solid #e5e7eb;">${get(2)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; word-break:break-word; border-right:1px solid #e5e7eb;">${get(3)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; word-break:break-word; border-right:1px solid #e5e7eb;">${get(4)}</td>
                <td style="padding:7px 6px; vertical-align:top; text-align:center; border-right:1px solid #e5e7eb;">
                <span style="
                display:inline-block;
                background:#dbeafe;
                color:#1d4ed8;
                border-radius:4px;
                padding:2px 4px;
                font-size:8px;
                font-weight:700;
                white-space:normal;
                word-break:break-word;
                text-align:center;
                line-height:1.1;
                max-width:55px;
                ">
                ${get(5)}
                </span>
                </td>
                <td style="padding:7px 6px; font-size:8.5px; color:#059669; font-weight:600; vertical-align:top; white-space:nowrap; border-right:1px solid #e5e7eb;">${get(6)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#059669; font-weight:600; vertical-align:top; white-space:nowrap; border-right:1px solid #e5e7eb;">${get(7)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#4b5563; vertical-align:top; word-break:break-word; border-right:1px solid #e5e7eb;">${get(8)}</td>
                <td style="padding:7px 6px; font-size:9px; font-weight:600; color:#111827; vertical-align:top; word-break:break-word; border-right:1px solid #e5e7eb;">${get(9)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#6b7280; vertical-align:top; word-break:break-word; border-right:1px solid #e5e7eb;">${get(10)}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:top; word-break:break-word;">${get(11)}</td>
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
                        <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">Student OJT Information Report</div>
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
                        <span style="color:#6b7280;">Year Range:</span>
                        <strong style="color:#111827;">${startYear} → ${endYear}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Course:</span>
                        <strong style="color:#111827;">${course}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Showing:</span>
                        <strong style="color:#111827;">${total} student${total !== 1 ? 's' : ''} (Page ${pageNum})</strong>
                    </div>
                </div>
                <div style="font-size:8.5px; color:#9ca3af;">Generated: ${dateStr} at ${timeStr}</div>
            </div>

            <div style="padding:9px 22px 3px 22px;">
                <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">Student Placement Details — Page ${pageNum}</div>
            </div>

            <div style="padding:4px 22px 0 22px;">
                <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-family:'Poppins',Arial,sans-serif; border:1px solid #d1d5db;">
                    <colgroup>
                        <col style="width:3%;">    <col style="width:10%;">   <col style="width:9%;">    <col style="width:10%;">   <col style="width:8%;">    <col style="width:8%;">    <col style="width:6%;">    <col style="width:6%;">    <col style="width:6%;">    <col style="width:10%;">   <col style="width:9%;">    <col style="width:8%;">    <col style="width:7%;">    </colgroup>
                    <thead>
                        <tr style="background:#7f0000;">
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">#</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Student Name</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Company</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Address</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Nat. Business</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Linkages</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Level</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Start</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">End</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Schedule</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Contact Person</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; border-right:1px solid rgba(255,255,255,0.15); overflow:hidden;">Position</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; overflow:hidden;">Contact No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHTML || `<tr><td colspan="13" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No records found for the selected filters.</td></tr>`}
                    </tbody>
                </table>
            </div>

            <div style="page-break-inside: avoid !important; break-inside: avoid !important; display: table; width: 100%;">
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

                <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:0.7; filter:brightness(2);">
                        <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">© 1998–2026 <strong style="color:#fca5a5;">Polytechnic University of the Philippines</strong> — InternConnect OJT IMS</span>
                    </div>
                    <span style="font-size:8px; color:rgba(255,255,255,0.5);">Ref: OJT-RPT-${now.getFullYear()} &nbsp;|&nbsp; Page ${pageNum} of ${pageCount}</span>
                </div>
            </div>

        </div>`;
    }

    /* ══════════════════════════════════════════════
       Single modal trigger
    ══════════════════════════════════════════════ */
    const previewModalEl = document.getElementById('printPreviewModal');
    const previewModal   = new bootstrap.Modal(previewModalEl, { backdrop: 'static', keyboard: true });

    document.getElementById('openPreviewBtn').addEventListener('click', function () {
        document.getElementById('printPreviewContent').innerHTML = buildPrintHTML();
        previewModal.show();
    });

    /* ══════════════════════════════════════════════
       PRINT
       Native Print via hidden #print-area-wrapper
    ══════════════════════════════════════════════ */
    document.getElementById('doPrintBtn').addEventListener('click', function () {
        // 1. Inject the HTML into the print wrapper
        document.getElementById('print-area-wrapper').innerHTML = buildPrintHTML();
        
        // 2. Trigger the native print dialog
        window.print();
        
        // 3. Clean up memory
        setTimeout(function() {
            document.getElementById('print-area-wrapper').innerHTML = '';
        }, 1000);
    });

</script>

<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>