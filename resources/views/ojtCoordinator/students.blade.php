<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Students</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }

        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        /* Report filter card */
        .filter-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden; margin-bottom: 22px;
        }

        .filter-card-header {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 22px; background: #fafafa;
            border-bottom: 1px solid #f0f0f0;
        }

        .filter-header-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 14px; flex-shrink: 0;
        }

        .filter-card-header h3 { font-size: 14px; font-weight: 700; color: #1a1a1a; }
        .filter-card-header p  { font-size: 12px; color: #888; margin-top: 1px; }

        .filter-card-body {
            padding: 20px 22px;
            display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
        }

        .filter-field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px; }

        .filter-label {
            font-size: 12px; font-weight: 600; color: #444;
            display: flex; align-items: center; gap: 5px;
        }

        .filter-label i { color: var(--red); font-size: 10px; }

        .filter-select {
            background: #fafafa; border: 1.5px solid #e8e8e8;
            border-radius: 10px; color: #1a1a1a;
            font-family: 'Poppins', sans-serif; font-size: 13px;
            padding: 10px 14px; outline: none; transition: all 0.25s;
        }

        .filter-select:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .btn-generate {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 14px rgba(220,38,38,0.22);
            white-space: nowrap;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220,38,38,0.32);
        }

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

        .student-count-badge {
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
            padding: 12px; color: #333;
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

        /* Student cell */
        .student-cell { display: flex; align-items: center; gap: 10px; }

        .student-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 12px; font-weight: 700; flex-shrink: 0;
        }

        .student-name-text { font-weight: 600; color: #1a1a1a; font-size: 13px; }

        /* Badges */
        .course-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #ede9fe; color: #7c3aed;
            border-radius: 20px; padding: 3px 10px;
            font-size: 11.5px; font-weight: 600;
        }

        .section-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #dbeafe; color: #2563eb;
            border-radius: 20px; padding: 3px 10px;
            font-size: 11.5px; font-weight: 600;
        }

        .subject-badge {
            display: inline-flex; align-items: center;
            background: #dcfce7; color: #16a34a;
            border-radius: 20px; padding: 2px 9px;
            font-size: 11px; font-weight: 600; margin: 2px 2px 0 0;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            border-radius: 20px; padding: 4px 10px;
            font-size: 11.5px; font-weight: 600;
        }

        .status-badge.pending  { background: #fef9c3; color: #ca8a04; }
        .status-badge.approved { background: #dcfce7; color: #16a34a; }
        .status-badge.revision { background: #fee2e2; color: var(--red); }

        /* Action buttons */
        .btn-action {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px; border-radius: 7px;
            font-family: 'Poppins', sans-serif; font-size: 12px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
            border: 1.5px solid transparent; white-space: nowrap;
        }

        .btn-action.view-personal {
            background: #fff; border-color: #e0e7ff; color: #4f46e5;
        }

        .btn-action.view-personal:hover { background: #e0e7ff; }

        .btn-action.view-ojt {
            background: #fff; border-color: #d1fae5; color: #059669;
        }

        .btn-action.view-ojt:hover { background: #d1fae5; }

        .btn-action.status {
            background: #fff; border-color: #fef3c7; color: #d97706;
        }

        .btn-action.status:hover { background: #fef3c7; }

        .btn-action.notify {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: #fff; border: none;
            box-shadow: 0 2px 8px rgba(220,38,38,0.2);
        }

        .btn-action.notify:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220,38,38,0.3);
        }

        .actions-wrap { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

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

        .modal-student-banner {
            display: flex; align-items: center; gap: 12px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        }

        .modal-student-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 16px; font-weight: 800; flex-shrink: 0;
        }

        .modal-student-name { font-size: 14px; font-weight: 700; color: #1a1a1a; }
        .modal-student-sub  { font-size: 12px; color: #888; margin-top: 2px; }

        .info-row {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 10px 0; border-bottom: 1px solid #f5f5f5;
        }

        .info-row:last-child { border-bottom: none; }

        .info-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 12px; flex-shrink: 0; margin-top: 1px;
        }

        .info-label { font-size: 11px; font-weight: 600; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 13.5px; font-weight: 500; color: #1a1a1a; margin-top: 2px; }

        .modal-footer {
            background: #fafafa; border-top: 1px solid #f0f0f0;
            padding: 14px 24px; display: flex; justify-content: flex-end;
        }

        .btn-modal-close {
            padding: 9px 22px; background: #f3f4f6;
            border: 1px solid #e5e5e5; border-radius: 8px; color: #555;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .btn-modal-update {
            padding: 9px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 8px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.25s;
            box-shadow: 0 3px 10px rgba(220,38,38,0.2);
        }

        .btn-modal-update:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(220,38,38,0.3); }

        .status-select {
            width: 100%; background: #fafafa;
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; padding: 11px 14px; outline: none; transition: all 0.25s;
        }

        .status-select:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

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
        }
        .btn-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px;
    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
    border: none; border-radius: 10px; color: #fff;
    font-family: 'Poppins', sans-serif; font-size: 14px;
    font-weight: 600; cursor: pointer; transition: all 0.3s;
    box-shadow: 0 4px 16px rgba(220,38,38,0.25);
    text-decoration: none;
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(220,38,38,0.35);
    color: #fff; text-decoration: none;
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
        <h1>Active <span>Students</span></h1>
        <div class="breadcrumb">
            <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
            <i class="fa fa-chevron-right"></i>
            <span>Students</span>
        </div>
    </div>
    <button class="btn-back" onclick="window.location.href='{{ url('/dashboard') }}'">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </button>
</div>

        <!-- Stats Row -->
        @php $totalStudents = count($studentData); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ $totalStudents }}</div>
                    <div class="stat-name">Total Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa fa-graduation-cap"></i></div>
                <div>
                    <div class="stat-num">{{ count(collect($studentData)->pluck('student')->pluck('course')->unique()) }}</div>
                    <div class="stat-name">Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-layer-group"></i></div>
                <div>
                    <div class="stat-num">{{ count(collect($studentData)->pluck('student')->pluck('year_and_section')->unique()) }}</div>
                    <div class="stat-name">Sections</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-chalkboard-teacher"></i></div>
                <div>
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Active Program</div>
                </div>
            </div>
        </div>

        <!-- Report Generator -->
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-header-icon"><i class="fa fa-file-chart-line"></i></div>
                <div>
                    <h3>Generate Report</h3>
                    <p>Filter by course and generate an OJT student report</p>
                </div>
            </div>
            <div class="filter-card-body">
                <form id="reportForm" action="{{ route('ojt.report.generate') }}" method="post" target="_blank" style="display:flex; align-items:flex-end; gap:14px; flex-wrap:wrap; width:100%;">
                    @csrf
                    <div class="filter-field">
                        <label class="filter-label" for="course">
                            <i class="fa fa-graduation-cap"></i> Filter by Course
                        </label>
                        <select class="filter-select" id="course" name="course" required>
                            @foreach ($course as $c)
                                <option value="{{ $c->course }}">{{ $c->course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn-generate" type="button" onclick="generateReportPreview()">
                        <i class="fa fa-file-alt"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-users"></i></div>
                    <div>
                        <h2>Active Student List</h2>
                        <p>All currently enrolled OJT students</p>
                    </div>
                </div>
                <div class="student-count-badge">
                    <i class="fa fa-users"></i>
                    {{ $totalStudents }} {{ $totalStudents == 1 ? 'student' : 'students' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Year &amp; Section</th>
                            <th>Professor</th>
                            <th>Subject Code(s)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentData as $data)
                        <tr>
                            <!-- Name -->
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($data['student']->first_name, 0, 1)) }}
                                    </div>
                                    <span class="student-name-text">
                                        {{ $data['student']->first_name }} {{ $data['student']->last_name }}
                                    </span>
                                </div>
                            </td>

                            <!-- Course -->
                            <td>
                                <span class="course-badge">
                                    <i class="fa fa-graduation-cap" style="font-size:10px;"></i>
                                    {{ $data['student']->course }}
                                </span>
                            </td>

                            <!-- Section -->
                            <td>
                                <span class="section-badge">{{ $data['student']->year_and_section }}</span>
                            </td>

                            <!-- Professor -->
                            <td style="font-size:13px; color:#555;">
                                {{ $data['student']->adviser_name ?? '—' }}
                            </td>

                            <!-- Subjects -->
                            <td>
                                @if(isset($data['subjects']) && count($data['subjects']) > 0)
                                    @foreach($data['subjects'] as $subject)
                                        <span class="subject-badge">{{ $subject['subject_code'] ?? '--' }}</span>
                                    @endforeach
                                @else
                                    <span style="color:#aaa; font-size:13px;">—</span>
                                @endif
                            </td>

                            <!-- Actions -->
                           <td>
    <div class="actions-wrap" style="flex-wrap:nowrap;">

        <!-- Personal Info -->
        <button class="btn-action view-personal btn-view-personal"
            data-bs-toggle="modal" data-bs-target="#personalModal"
            data-full-name="{{ $data['student']->full_name }}"
            data-contact-number="{{ $data['student']->contact_number }}"
            data-email="{{ $data['student']->email }}"
            data-address="{{ $data['student']->address }}"
            data-date-of-birth="{{ $data['student']->date_of_birth }}"
            data-student-num="{{ $data['ojt']->studentNum ?? '' }}">
            <i class="fa fa-user"></i> Personal
        </button>

        <!-- OJT Info -->
        <button class="btn-action view-ojt btn-view-ojt"
            data-bs-toggle="modal" data-bs-target="#ojtModal"
            data-full-name="{{ $data['student']->full_name }}"
            data-company-name="{{ $data['ojt']->company_name ?? '' }}"
            data-company-address="{{ $data['ojt']->company_address ?? '' }}"
            data-nature-of-business="{{ $data['ojt']->nature_of_bus ?? '' }}"
            data-nature-of-linkages="{{ $data['ojt']->nature_of_link ?? '' }}"
            data-level="{{ $data['ojt']->level ?? '' }}"
            data-start-date="{{ $data['ojt']->start_date ?? '' }}"
            data-finish-date="{{ $data['ojt']->finish_date ?? '' }}"
            data-report-time="{{ $data['ojt']->report_time ?? '' }}"
            data-contact-name="{{ $data['ojt']->contact_name ?? '' }}"
            data-contact-position="{{ $data['ojt']->contact_position ?? '' }}"
            data-contact-number="{{ $data['ojt']->contact_number ?? '' }}">
            <i class="fa fa-briefcase"></i> OJT Info
        </button>

        <!-- Status + Notify side by side -->
        <button class="btn-action status btn-status"
            data-bs-toggle="modal" data-bs-target="#statusModal"
            data-student="{{ $data['ojt']->studentNum ?? '' }}"
            data-status="{{ $data['ojt']->status ?? '' }}"
            data-name="{{ $data['student']->full_name }}">
            <i class="fa fa-info-circle"></i> Status
        </button>

        <form class="notifyForm d-inline"
              action="{{ url('/notify', $data['ojt']->studentNum ?? 0) }}"
              method="POST">
            @csrf
            <button type="submit" class="btn-action notify">
                <i class="fa fa-bell"></i> Notify
            </button>
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

<!-- =============== PERSONAL INFO MODAL =============== -->
<div class="modal fade" id="personalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user"></i> Student Personal Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-student-banner">
                    <div class="modal-student-avatar" id="pi-avatar"></div>
                    <div>
                        <div class="modal-student-name" id="pi-full-name"></div>
                        <div class="modal-student-sub">Student Personal Details</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-id-card"></i></div>
                    <div><div class="info-label">Student Number</div><div class="info-value" id="pi-student-num"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-phone"></i></div>
                    <div><div class="info-label">Contact Number</div><div class="info-value" id="pi-contact-number"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-envelope"></i></div>
                    <div><div class="info-label">Email Address</div><div class="info-value" id="pi-email"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div><div class="info-label">Address</div><div class="info-value" id="pi-address"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-birthday-cake"></i></div>
                    <div><div class="info-label">Date of Birth</div><div class="info-value" id="pi-date-of-birth"></div></div>
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

<!-- =============== OJT INFO MODAL =============== -->
<div class="modal fade" id="ojtModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-briefcase"></i> Student OJT Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-student-banner">
                    <div class="modal-student-avatar" id="ojt-avatar"></div>
                    <div>
                        <div class="modal-student-name" id="ojt-full-name"></div>
                        <div class="modal-student-sub">OJT Details</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-building"></i></div>
                    <div><div class="info-label">Company Name</div><div class="info-value" id="ojt-company-name"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div><div class="info-label">Company Address</div><div class="info-value" id="ojt-company-address"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-industry"></i></div>
                    <div><div class="info-label">Nature of Business</div><div class="info-value" id="ojt-nature-of-business"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-network-wired"></i></div>
                    <div><div class="info-label">Nature of Linkages</div><div class="info-value" id="ojt-nature-of-linkages"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-layer-group"></i></div>
                    <div><div class="info-label">Level</div><div class="info-value" id="ojt-level"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-calendar-alt"></i></div>
                    <div><div class="info-label">Start Date</div><div class="info-value" id="ojt-start-date"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-calendar-check"></i></div>
                    <div><div class="info-label">End Date</div><div class="info-value" id="ojt-finish-date"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-clock"></i></div>
                    <div><div class="info-label">Reporting Time</div><div class="info-value" id="ojt-report-time"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-user-tie"></i></div>
                    <div><div class="info-label">Contact Person</div><div class="info-value" id="ojt-contact-name"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-id-badge"></i></div>
                    <div><div class="info-label">Position</div><div class="info-value" id="ojt-contact-position"></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon"><i class="fa fa-phone"></i></div>
                    <div><div class="info-label">Contact Number</div><div class="info-value" id="ojt-contact-number"></div></div>
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

<!-- =============== STATUS MODAL =============== -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-info-circle"></i> Update Student Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" action="{{ url('/status') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="modal-student-banner">
                        <div class="modal-student-avatar" id="st-avatar"></div>
                        <div>
                            <div class="modal-student-name" id="st-name"></div>
                            <div class="modal-student-sub">Update MOA Status</div>
                        </div>
                    </div>
                    <div style="margin-bottom:6px;">
                        <label style="font-size:12.5px; font-weight:600; color:#444; display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                            <i class="fa fa-tag" style="color:var(--red); font-size:11px;"></i> Select Status
                        </label>
                        <select class="status-select" name="status" id="status-select">
                            <option value="" disabled selected>Select status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved and For Notary">Approved and For Notary</option>
                            <option value="With Revision">With Revision</option>
                        </select>
                    </div>
                    <input type="hidden" id="status-student" name="student" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-update">
                        <i class="fa fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Print Preview Modal -->
<div id="printPreviewModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-print"></i> Report Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="printPreviewContent"></div>
            <div class="modal-footer">
                <button class="btn-modal-close" type="button" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="printReport()" class="btn-modal-update">
                    <i class="fa fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

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
        $('#fileTable').DataTable({ order: [] });

        // Personal Info Modal
        $(document).on('click', '.btn-view-personal', function () {
            const name = $(this).data('full-name');
            $('#pi-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#pi-full-name').text(name || '—');
            $('#pi-contact-number').text($(this).data('contact-number') || '—');
            $('#pi-email').text($(this).data('email') || '—');
            $('#pi-address').text($(this).data('address') || '—');
            $('#pi-date-of-birth').text($(this).data('date-of-birth') || '—');
            $('#pi-student-num').text($(this).data('student-num') || '—');
        });

        // OJT Info Modal
        $(document).on('click', '.btn-view-ojt', function () {
            const name = $(this).data('full-name');
            $('#ojt-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#ojt-full-name').text(name || '—');
            $('#ojt-company-name').text($(this).data('company-name') || '—');
            $('#ojt-company-address').text($(this).data('company-address') || '—');
            $('#ojt-nature-of-business').text($(this).data('nature-of-business') || '—');
            $('#ojt-nature-of-linkages').text($(this).data('nature-of-linkages') || '—');
            $('#ojt-level').text($(this).data('level') || '—');
            $('#ojt-start-date').text($(this).data('start-date') || '—');
            $('#ojt-finish-date').text($(this).data('finish-date') || '—');
            $('#ojt-report-time').text($(this).data('report-time') || '—');
            $('#ojt-contact-name').text($(this).data('contact-name') || '—');
            $('#ojt-contact-position').text($(this).data('contact-position') || '—');
            $('#ojt-contact-number').text($(this).data('contact-number') || '—');
        });

        // Status Modal
        $(document).on('click', '.btn-status', function () {
            const name = $(this).data('name');
            const studentNum = $(this).data('student');
            const status = $(this).data('status');
            $('#st-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#st-name').text(name || '—');
            $('#status-student').val(studentNum);
            $('#status-select').val(status);
            $('#statusUpdateForm').attr('action', '/status/' + studentNum);
        });

        // Status form AJAX
        $('#statusUpdateForm').on('submit', function (e) {
            e.preventDefault();
            const studentNum = $('#status-student').val();
            const newStatus  = $('#status-select').val();
            $.ajax({
                type: 'POST',
                url: '/status/' + studentNum,
                data: { _token: "{{ csrf_token() }}", status: newStatus },
                success: function () { location.reload(); },
                error:   function () { alert('An error occurred.'); }
            });
        });

        // Notify form
        $(document).on('submit', '.notifyForm', function (e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: { _token: "{{ csrf_token() }}" },
                success: function () { location.reload(); },
                error:   function () { console.error('Notify error.'); }
            });
        });

    });

    // Report generation
    function generateReportPreview() {
        var formData = new FormData(document.getElementById('reportForm'));
        $.ajax({
            url: "{{ route('ojt.report.generate') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                $("#printPreviewContent").html(data);
                $("#printPreviewModal").modal('show');
            },
            error: function () {
                alert('An error occurred while fetching the report content.');
            }
        });
    }

    function printReport() {
        var printContents = document.getElementById("printPreviewContent").innerHTML;
        var printFrame = document.getElementById("printFrame").contentWindow;
        printFrame.document.open();
        printFrame.document.write('<html><head><title>Report</title></head><body>' + printContents + '</body></html>');
        printFrame.document.close();
        printFrame.focus();
        printFrame.print();
    }

    flatpickr('.datepicker', { dateFormat: 'Y-m-d' });
</script>

</body>
</html>