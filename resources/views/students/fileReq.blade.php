<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Requirements</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student_filereq-responsive.css') }}">
    

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

        /* Upload button */
        .btn-upload {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
            text-decoration: none;
        }

        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            color: #fff;
        }

        .btn-upload:active { transform: translateY(0); }

        /* Stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);  /* always 4 columns on desktop */
            gap: 16px;
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
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
            min-width: 0;
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

        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }
        .stat-icon.gray   { background: #f3f4f6; color: #6b7280; }

        .stat-num  { font-size: 22px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 12px; color: #888; margin-top: 3px; }

        .stat-card > div:last-child {
            min-width: 0;
        }

        .stat-name {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

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

        .table-card-body { padding: 0; }

        /* DataTables overrides */
        .table-card-body .dataTables_wrapper {
            padding: 16px 22px;
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

        /* Category cell */
        .category-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .category-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 13px;
            flex-shrink: 0;
        }

        .category-name {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 13.5px;
            line-height: 1.45;
        }

        /* File cell */
        .file-cell {
            font-size: 12.5px;
            color: #777;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .file-cell i { color: var(--red); font-size: 12px; }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 96px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-approved { background: #dcfce7; color: #16a34a; }
        .status-denied   { background: #fee2e2; color: var(--red); }
        .status-pending  { background: #fef9c3; color: #ca8a04; }
        .status-default  { background: #f3f4f6; color: #6b7280; }

        .phase-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .phase-panel {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #fff;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .phase-panel.basic {
            background: linear-gradient(180deg, #fff8f6 0%, #ffffff 100%);
        }

        .phase-panel-header {
            display: grid;
            margin-bottom: 14px;
        }

        .phase-panel-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .phase-panel-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        .phase-panel-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.5;
        }

        .phase-header-badge {
            flex-shrink: 0;
            font-size: 10.5px;
            padding: 5px 10px;
            white-space: nowrap;
        }

        .phase-summary-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .phase-summary-stat {
            padding: 11px 12px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
        }

        .phase-summary-stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 4px;
        }

        .phase-summary-stat-value {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        .phase-summary-actions {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .phase-view-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #fecaca;
            background: #fff;
            color: var(--red);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s ease;
            cursor: pointer;
        }

        .phase-view-btn:hover {
            background: #fff5f5;
            border-color: #fca5a5;
            color: var(--red-dark);
        }

        .phase-progress-list {
            display: grid;
            gap: 10px;
            margin-bottom: 14px;
        }

        .phase-progress-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            font-size: 13px;
            color: #374151;
        }

        .phase-note {
            border-radius: 12px;
            padding: 14px;
            font-size: 13px;
            line-height: 1.6;
        }

        .phase-note.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .phase-note.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .phase-note-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 8px;
        }

        .phase-note-list {
            display: grid;
            gap: 6px;
        }

        .phase-modal-list {
            display: grid;
            gap: 10px;
        }

        .phase-modal-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
        }

        .phase-modal-item-name {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
        }

        .phase-modal-item-name span:last-child {
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .upload-modal-layout {
                grid-template-columns: 1fr;
            }
        }

        .status-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-status-reason {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 9px;
            color: #7f1d1d;
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 999px;
            font-family: 'Poppins', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-status-reason:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        /* Date cell */
        .date-main { font-size: 13px; color: #444; }
        .date-sub  { font-size: 11.5px; color: #aaa; margin-top: 2px; }

        /* Remove button */
        .btn-remove {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 10px rgba(220,38,38,0.25);
        }

        .btn-remove:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(220,38,38,0.35);
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #fff;
            border: 1.5px solid #e8e8e8;
            border-radius: 8px;
            color: #0f766e;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
        }

        .btn-view:hover {
            background: #ecfeff;
            border-color: #a5f3fc;
            color: #0f766e;
        }

        .file-preview-badge.no-preview {
            background: #fee2e2;
            color: #991b1b;
        }

        .unsupported-file-message {
            padding: 44px 24px;
            text-align: center;
            background: #fff;
            border-top: 1px solid #f0f0f0;
        }

        .unsupported-file-message .unsupported-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 16px;
        }

        .unsupported-file-message h3 {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .unsupported-file-message p {
            font-size: 13.5px;
            color: #777;
            margin-bottom: 18px;
        }

        .btn-download-file {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(37,99,235,0.2);
        }

        .btn-download-file:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37,99,235,0.3);
            color: #fff;
            text-decoration: none;
        }

        /* =============== MODAL =============== */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            font-family: 'Poppins', sans-serif;
            overflow: visible;
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

        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }

        .modal-body {
            padding: 24px;
            background: #fff;
            overflow: visible;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .modal-field-label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-field-label i { color: var(--red); font-size: 12px; }

        .modal-field-input,
        .modal-field-select {
            width: 100%;
            background: #fafafa;
            border: 1.5px solid #e8e8e8;
            border-radius: 10px;
            color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            padding: 11px 14px;
            outline: none;
            transition: all 0.25s;
            margin-bottom: 18px;
            appearance: none;
        }

        .modal-field-select {
            background-image:
                linear-gradient(45deg, transparent 50%, #b91c1c 50%),
                linear-gradient(135deg, #b91c1c 50%, transparent 50%);
            background-position:
                calc(100% - 18px) calc(50% - 3px),
                calc(100% - 12px) calc(50% - 3px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            padding-right: 40px;
        }

        .modal-field-input:focus,
        .modal-field-select:focus {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .upload-modal-form {
            background:
                radial-gradient(circle at top right, rgba(254, 226, 226, 0.8), transparent 26%),
                linear-gradient(180deg, #ffffff 0%, #fff9f9 100%);
        }

        .upload-modal-dialog {
            max-width: 980px;
        }

        .upload-modal-body {
            display: grid;
            gap: 14px;
            overflow: visible;
        }

        .upload-modal-layout {
            display: grid;
            grid-template-columns: minmax(300px, 0.9fr) minmax(460px, 1.3fr);
            gap: 18px;
            align-items: stretch;
            overflow: visible;
        }

        .upload-modal-left {
            display: grid;
            gap: 14px;
            align-content: start;
            overflow: visible;
        }

        .upload-modal-right {
            min-width: 0;
        }

        .upload-modal-right .upload-modal-section.file {
            height: 100%;
        }

        .upload-modal-section {
            background: rgba(255,255,255,0.94);
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: visible;
        }

        .upload-modal-section.phase {
            background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%);
            border-color: #fee2e2;
        }

        .upload-modal-section.file {
            background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%);
            border-color: #ffedd5;
        }

        .upload-modal-section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .upload-modal-section-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fee2e2;
            color: var(--red);
            font-size: 14px;
            flex-shrink: 0;
        }

        .upload-modal-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .upload-modal-section-subtitle {
            font-size: 11.5px;
            color: #6b7280;
            margin-top: 3px;
            line-height: 1.5;
        }

        .upload-modal-help-row {
            margin-top: -4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .upload-modal-help {
            margin-top: 0;
            flex: 1;
            padding: 8px 10px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #fee2e2;
            font-size: 11.5px;
            color: #6b7280;
            line-height: 1.6;
        }

        .phase-lock-hint {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            flex-shrink: 0;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
            font-size: 12px;
            cursor: help;
        }

        .phase-lock-tooltip {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 230px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #7f1d1d;
            color: #fff;
            font-size: 11.5px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 16px 30px rgba(127, 29, 29, 0.28);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: all 0.2s ease;
            z-index: 20;
            pointer-events: none;
        }

        .phase-lock-tooltip::before {
            content: "";
            position: absolute;
            right: 10px;
            bottom: 100%;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #7f1d1d transparent;
        }

        .phase-lock-hint:hover .phase-lock-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .phase-dropdown {
            position: relative;
            margin-bottom: 18px;
        }

        .phase-dropdown-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #fafafa;
            border: 1.5px solid #e8e8e8;
            border-radius: 10px;
            color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            padding: 11px 14px;
            transition: all 0.25s;
            cursor: pointer;
        }

        .phase-dropdown-trigger:hover,
        .phase-dropdown.open .phase-dropdown-trigger {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .phase-dropdown-trigger-text {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }

        .phase-dropdown-trigger-icon {
            color: #b91c1c;
            font-size: 12px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .phase-dropdown.open .phase-dropdown-trigger-icon {
            transform: rotate(180deg);
        }

        .phase-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            width: 100%;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid #f1d3d3;
            border-radius: 14px;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.14);
            padding: 8px;
            display: grid;
            gap: 6px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: all 0.2s ease;
            z-index: 40;
        }

        .category-dropdown-menu-scroll {
            max-height: 176px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
        }

        .category-dropdown-menu-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .category-dropdown-menu-scroll::-webkit-scrollbar-thumb {
            background: #f3c7c7;
            border-radius: 999px;
        }

        .category-dropdown-menu-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .phase-dropdown.open .phase-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .phase-dropdown-option {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
            padding: 11px 12px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: #fff;
            color: #1f2937;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .phase-dropdown-option:hover {
            background: #fff5f5;
            border-color: #fecaca;
            color: var(--red-dark);
        }

        .phase-dropdown-option.active {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #fff;
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.18);
        }

        .phase-dropdown-option.active .phase-dropdown-option-meta {
            color: rgba(255,255,255,0.85);
        }

        .phase-dropdown-option.locked {
            background: #fffaf0;
            border-color: #fde68a;
            color: #92400e;
        }

        .phase-dropdown-option-tooltip {
            position: absolute;
            left: 12px;
            right: 12px;
            top: calc(100% + 8px);
            width: auto;
            max-width: none;
            padding: 10px 12px;
            border-radius: 12px;
            background: #7f1d1d;
            color: #fff;
            font-size: 11.5px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 16px 30px rgba(127, 29, 29, 0.28);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 51;
        }

        .phase-dropdown-option-tooltip::before {
            content: "";
            position: absolute;
            left: 18px;
            bottom: 100%;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #7f1d1d transparent;
        }

        .phase-dropdown-option.locked:hover .phase-dropdown-option-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .phase-dropdown-option-label {
            display: grid;
            gap: 2px;
            min-width: 0;
        }

        .phase-dropdown-option-title {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .phase-dropdown-option-meta {
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.4;
        }

        .phase-dropdown-option-status {
            flex-shrink: 0;
            margin-left: auto;
            font-size: 11px;
            font-weight: 700;
            border-radius: 999px;
            padding: 5px 8px;
            background: #fef3c7;
            color: #b45309;
        }

        .category-hover-bubble {
            position: fixed;
            z-index: 30001;
            max-width: min(320px, calc(100vw - 32px));
            padding: 10px 12px;
            border-radius: 12px;
            background: #7f1d1d;
            color: #fff;
            font-size: 11.5px;
            font-weight: 500;
            line-height: 1.5;
            box-shadow: 0 16px 30px rgba(127, 29, 29, 0.28);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
            pointer-events: none;
        }

        .category-hover-bubble.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .category-hover-bubble::before {
            content: "";
            position: absolute;
            left: 18px;
            bottom: 100%;
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #7f1d1d transparent;
        }

        .category-dropdown-option.empty {
            cursor: default;
            color: #9ca3af;
            background: #f8fafc;
            border-color: #e5e7eb;
        }

        .category-dropdown-option.empty:hover {
            color: #9ca3af;
            background: #f8fafc;
            border-color: #e5e7eb;
        }

        /* File upload zone */
        .file-upload-zone {
            border: 2px dashed #f3c7c7;
            border-radius: 16px;
            padding: 24px 20px;
            min-height: 250px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            margin-bottom: 0;
            position: relative;
            background:
                radial-gradient(circle at top, rgba(254, 242, 242, 0.9), transparent 65%),
                #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .file-upload-zone:hover {
            border-color: var(--red);
            background:
                radial-gradient(circle at top, rgba(254, 226, 226, 0.95), transparent 68%),
                #fffafa;
            transform: translateY(-1px);
        }

        .file-upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-upload-zone .upload-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 20px;
            color: #f87171;
            background: #fff1f2;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.12);
            transition: all 0.25s;
        }

        .file-upload-zone:hover .upload-icon {
            color: var(--red);
            background: #fee2e2;
        }

        .file-upload-zone p {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin: 0;
        }

        .file-upload-zone span {
            display: block;
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 6px;
        }

        .modal-footer {
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
            padding: 16px 24px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            background-clip: padding-box;
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

        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .btn-modal-submit {
            padding: 9px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 12px rgba(220,38,38,0.2);
        }

        .btn-modal-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(220,38,38,0.3);
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
            .stats-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            html, body {
                max-width: 100%;
                overflow-x: hidden;
            }

            .page-content {
                padding: 12px;
                overflow-x: hidden;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-upload {
                width: 100%;
                justify-content: center;
                white-space: normal;
            }

            .stats-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .stat-card {
                padding: 12px 14px;
                gap: 10px;
            }

            .table-card {
                overflow: hidden;
            }

            .table-card-body {
                overflow: visible;
            }

            .table-card-body .dataTables_wrapper {
                overflow: visible;
            }

            .table-card-body .dataTables_scroll {
                width: 100%;
            }

            .table-card-body .dataTables_scrollBody {
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .stat-card {
                padding: 10px 12px;
            }

            .stat-icon {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }

            .stat-num {
                font-size: 16px;
            }

            .stat-name {
                font-size: 10.5px;
            }
        }
        /* Dashboard Footer */
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

.dashboard-footer .footer-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.dashboard-footer .footer-logo {
    width: 22px; height: 22px;
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

.dashboard-footer a:hover { color: var(--red); }

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

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa fa-user"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
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
        <a href="{{ url('/student/class') }}" class="nav-item">
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
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">Notarized MOA</span>
            <span class="tooltip-label">Notarized MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
        </a>
            <a href="{{ url('/student/evaluation') }}" class="nav-item{{ request()->is('student/evaluation*') ? ' active' : '' }}">
            <span class="nav-icon"><i class="fa fa-star-half-alt"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
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
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-graduation-cap"></i>
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>File <span>Requirements</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Requirements</span>
                </div>
            </div>
            <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fa fa-cloud-upload-alt"></i> Upload Document
            </button>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ count($data) }}</div>
                    <div class="stat-name">Total Submitted</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $data->where('status', 1)->count() }}</div>
                    <div class="stat-name">Approved</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-clock"></i></div>
                <div>
                    <div class="stat-num">{{ $data->where('status', 0)->count() }}</div>
                    <div class="stat-name">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gray"><i class="fa fa-times-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $data->where('status', 2)->count() }}</div>
                    <div class="stat-name">Denied</div>
                </div>
            </div>
        </div>

        <div class="stats-row" style="margin-top:-6px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-unlock-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $submittedBasicNames->count() }}/{{ $basicCategories->count() }}</div>
                    <div class="stat-name">Basic Requirements Submitted</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon {{ $hasSubmittedNotarizedMoa ? 'green' : 'amber' }}"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">{{ $hasSubmittedNotarizedMoa ? 'Yes' : 'No' }}</div>
                    <div class="stat-name">Notarized MOA Submitted</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon {{ $otherRequirementsUnlocked ? 'green' : 'gray' }}"><i class="fa fa-layer-group"></i></div>
                <div>
                    <div class="stat-num">{{ $otherRequirementsUnlocked ? 'Unlocked' : 'Locked' }}</div>
                    <div class="stat-name">Other Requirements</div>
                </div>
            </div>
        </div>

        <div class="table-card" style="margin-bottom:24px;">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-stream"></i></div>
                    <div>
                        <h2>Requirement Phases</h2>
                        <p>Finish the basic set and submit your Notarized MOA to unlock the rest</p>
                    </div>
                </div>
            </div>
            <div class="table-card-body" style="padding:20px;">
                <div style="margin-bottom:16px;padding:14px 16px;border-radius:14px;background:#fff8f1;border:1px solid #fed7aa;font-size:13px;color:#9a3412;line-height:1.6;">
                    Upload the basic requirements first, then submit your Notarized MOA to unlock the during-OJT and after-OJT files.
                </div>

                <div class="phase-grid">
                    <div class="phase-panel basic">
                        <div class="phase-panel-header">
                            <div class="phase-panel-title-row">
                                <div class="phase-panel-title">Basic Requirements</div>
                                <span class="status-badge status-approved phase-header-badge"><i class="fa fa-unlock"></i> Always Open</span>
                            </div>
                            <div class="phase-panel-subtitle">Required before you start your OJT.</div>
                        </div>

                        <div class="phase-summary-stats">
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Submitted</div>
                                <div class="phase-summary-stat-value">{{ $submittedBasicNames->count() }}/{{ $basicCategories->count() }}</div>
                            </div>
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Status</div>
                                <div class="phase-summary-stat-value">{{ $missingBasicCategories->isEmpty() ? 'Completed' : 'In Progress' }}</div>
                            </div>
                        </div>

                        <div class="phase-summary-actions">
                            <button type="button" class="phase-view-btn" data-bs-toggle="modal" data-bs-target="#basicRequirementsModal">
                                <i class="fa fa-eye"></i> View Requirements
                            </button>
                        </div>
                    </div>

                    <div class="phase-panel">
                        <div class="phase-panel-header">
                            <div class="phase-panel-title-row">
                                <div class="phase-panel-title">Other Requirements</div>
                                <span class="status-badge {{ $otherRequirementsUnlocked ? 'status-approved' : 'status-pending' }} phase-header-badge">
                                    <i class="fa {{ $otherRequirementsUnlocked ? 'fa-unlock' : 'fa-lock' }}"></i>
                                    {{ $otherRequirementsUnlocked ? 'Unlocked' : 'Locked' }}
                                </span>
                            </div>
                            <div class="phase-panel-subtitle">These open after the basics and Notarized MOA are done.</div>
                        </div>

                        <div class="phase-summary-stats">
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Requirements</div>
                                <div class="phase-summary-stat-value">{{ $otherCategories->count() }}</div>
                            </div>
                            <div class="phase-summary-stat">
                                <div class="phase-summary-stat-label">Access</div>
                                <div class="phase-summary-stat-value">{{ $otherRequirementsUnlocked ? 'Available' : 'Locked' }}</div>
                            </div>
                        </div>

                        <div class="phase-summary-actions">
                            <button type="button" class="phase-view-btn" data-bs-toggle="modal" data-bs-target="#otherRequirementsModal">
                                <i class="fa fa-eye"></i> View Requirements
                            </button>
                        </div>
                    </div>

                    <div class="phase-panel">
                        <div class="phase-panel-header" style="margin-bottom:12px;">
                            <div>
                                <div class="phase-panel-title">Unlock Progress</div>
                                <div class="phase-panel-subtitle">Track what is still needed before the next phase opens.</div>
                            </div>
                        </div>

                        <div class="phase-progress-list">
                            <div class="phase-progress-item">
                                <span>Basic requirements completed</span>
                                <span class="status-badge {{ $missingBasicCategories->isEmpty() ? 'status-approved' : 'status-pending' }}">
                                    {{ $submittedBasicNames->count() }}/{{ $basicCategories->count() ?: 0 }}
                                </span>
                            </div>
                            <div class="phase-progress-item">
                                <span>Notarized MOA submitted</span>
                                <span class="status-badge {{ $hasSubmittedNotarizedMoa ? 'status-approved' : 'status-pending' }}">
                                    {{ $hasSubmittedNotarizedMoa ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            <div class="phase-progress-item">
                                <span>Other requirements access</span>
                                <span class="status-badge {{ $otherRequirementsUnlocked ? 'status-approved' : 'status-pending' }}">
                                    {{ $otherRequirementsUnlocked ? 'Unlocked' : 'Locked' }}
                                </span>
                            </div>
                        </div>

                        @if (!$otherRequirementsUnlocked)
                            <div class="phase-summary-actions" style="margin-top:2px;">
                                <button type="button" class="phase-view-btn" data-bs-toggle="modal" data-bs-target="#unlockRequirementsModal">
                                    <i class="fa fa-clipboard-list"></i> What Still Needs To Be Submitted
                                </button>
                            </div>
                        @else
                            <div class="phase-note success">
                                Other requirements are unlocked. You can now submit the during-OJT and after-OJT files.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Requirements Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                    <div>
                        <h2>Submitted Requirements</h2>
                        <p>Manage and track all your uploaded OJT requirement files</p>
                    </div>
                </div>
                
            </div>

            <div class="table-card-body">

                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        const fileTable = $('#fileTable').DataTable({
                            "order": [[3, 'desc']],
                            "scrollX": true,
                            "autoWidth": false,
                            "columnDefs": [
                                { "width": "32%", "targets": 0 },
                                { "width": "18%", "targets": 2 },
                                { "width": "14%", "targets": 3 },
                                { "width": "18%", "targets": 4 }
                            ]
                        });

                        $('#fileTable tbody').on('click', '.remove-button', function (e) {
                            e.preventDefault();
                            var fileId = $(this).data('file-id');
                            showRemoveConfirmation(fileId);
                        });

                        $('#fileTable tbody').on('click', '.view-button', function (e) {
                            e.preventDefault();
                            var fileUrl = $(this).data('file-url');
                            var fileName = $(this).data('file-name');
                            var downloadUrl = $(this).data('download-url');
                            var fileExt = (fileName.split('.').pop() || '').toLowerCase();
                            var previewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt', 'html', 'htm'].indexOf(fileExt) !== -1;

                            $('#previewFileName').text(fileName);
                            $('#previewDownloadBtn').attr('href', downloadUrl);
                            $('#previewDownloadBtnBottom').attr('href', downloadUrl);
                            if (previewable) {
                                $('#previewFrame').show().attr('src', fileUrl);
                                $('#previewFallback').hide();
                                $('#previewBadge').removeClass('no-preview').html('<i class="fa fa-eye"></i> Preview available');
                            } else {
                                $('#previewFrame').hide().attr('src', 'about:blank');
                                $('#previewFallback').show();
                                $('#previewBadge').addClass('no-preview').html('<i class="fa fa-file-download"></i> No preview available');
                            }
                            var previewModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('previewModal'));
                            previewModal.show();
                        });

                        document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
                            $('#previewFrame').attr('src', 'about:blank');
                            $('#previewFrame').show();
                            $('#previewFallback').hide();
                            $('#previewBadge').removeClass('no-preview').html('<i class="fa fa-eye"></i> Preview available');
                            $('#previewDownloadBtn').attr('href', '#');
                            $('#previewDownloadBtnBottom').attr('href', '#');
                        });
                    });
                </script>

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $files)
                        <tr>
                            <td>
                                <div class="category-cell">
                                    <div class="category-icon"><i class="fa fa-file-alt"></i></div>
                                    <span class="category-name">{{ $files->fileName }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="file-cell">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $files->file }}
                                </div>
                            </td>
                            <td>
                                <div class="status-cell">
                                    @if ($files->status == 1)
                                        <span class="status-badge status-approved">
                                            <i class="fa fa-check-circle"></i> Approved
                                        </span>
                                    @elseif ($files->status == 2)
                                        <span class="status-badge status-denied">
                                            <i class="fa fa-times-circle"></i> Denied
                                        </span>
                                        @if(!empty($files->denial_reason))
                                            <button type="button"
                                                class="btn-status-reason"
                                                onclick="showDenialReason({{ Illuminate\Support\Js::from($files->fileName) }}, {{ Illuminate\Support\Js::from($files->denial_reason) }})">
                                                <i class="fa fa-comment-alt"></i> View Reason
                                            </button>
                                        @endif
                                    @elseif ($files->status == 0)
                                        <span class="status-badge status-pending">
                                            <i class="fa fa-clock"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="date-main">{{ \Carbon\Carbon::parse($files->created_at)->format('M d, Y') }}</div>
                                <div class="date-sub">{{ \Carbon\Carbon::parse($files->created_at)->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <button type="button" class="btn-view view-button" data-file-url="{{ url('/student/requirements/view/' . $files->id) }}" data-download-url="{{ url('/student/requirements/download/' . $files->id) }}" data-file-name="{{ $files->file }}">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <button class="btn-remove remove-button" data-file-id="{{ $files->id }}">
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
    <div class="modal-dialog modal-dialog-centered upload-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-cloud-upload-alt"></i> Submit Requirement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/uploadReq') }}" method="post" enctype="multipart/form-data" class="upload-modal-form">
                @csrf

                <div class="modal-body upload-modal-body">

                    <div class="upload-modal-layout">
                        <div class="upload-modal-left">
                            <div class="upload-modal-section phase">
                                <div class="upload-modal-section-head">
                                    <div class="upload-modal-section-icon"><i class="fa fa-stream"></i></div>
                                    <div>
                                        <div class="upload-modal-section-title">Requirement Phase</div>
                                        <div class="upload-modal-section-subtitle">Choose which phase this requirement belongs to.</div>
                                    </div>
                                </div>
                                <input type="hidden" name="phase" id="requirementPhaseSelect" value="basic">
                                <div class="phase-dropdown" id="phaseDropdown">
                                    <button type="button" class="phase-dropdown-trigger" id="phaseDropdownTrigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="phase-dropdown-trigger-text" id="phaseDropdownTriggerText">Basic Requirements</span>
                                        <i class="fa fa-chevron-down phase-dropdown-trigger-icon"></i>
                                    </button>
                                    <div class="phase-dropdown-menu" id="phaseDropdownMenu" role="listbox" aria-label="Requirement Phase">
                                        <button type="button" class="phase-dropdown-option active" data-value="basic">
                                            <span class="phase-dropdown-option-label">
                                                <span class="phase-dropdown-option-title">Basic Requirements</span>
                                                <span class="phase-dropdown-option-meta">Upload these before starting OJT.</span>
                                            </span>
                                        </button>
                                        <button
                                            type="button"
                                            class="phase-dropdown-option {{ $otherRequirementsUnlocked ? '' : 'locked' }}"
                                            data-value="other"
                                            {{ $otherRequirementsUnlocked ? '' : 'data-locked=true' }}
                                        >
                                            <span class="phase-dropdown-option-label">
                                                <span class="phase-dropdown-option-title">Other Requirements</span>
                                                <span class="phase-dropdown-option-meta">During-OJT and after-OJT uploads.</span>
                                            </span>
                                            @if (!$otherRequirementsUnlocked)
                                                <span class="phase-dropdown-option-status">Locked</span>
                                                <div class="phase-dropdown-option-tooltip">
                                                    Upload all basic requirements first before the other requirements phase becomes available.
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                </div>
                                <div class="upload-modal-help-row">
                                    <div id="phaseHelpText" class="upload-modal-help">
                                        Upload your basic requirements first.
                                    </div>
                                </div>
                            </div>

                            <div class="upload-modal-section">
                                <div class="upload-modal-section-head">
                                    <div class="upload-modal-section-icon"><i class="fa fa-tag"></i></div>
                                    <div>
                                        <div class="upload-modal-section-title">Category</div>
                                        <div class="upload-modal-section-subtitle">Select the exact requirement you are uploading.</div>
                                    </div>
                                </div>
                                <input type="hidden" name="fileName" id="requirementCategorySelect" value="">
                                <div class="phase-dropdown" id="categoryDropdown">
                                    <button type="button" class="phase-dropdown-trigger" id="categoryDropdownTrigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="phase-dropdown-trigger-text" id="categoryDropdownTriggerText">Select a category</span>
                                        <i class="fa fa-chevron-down phase-dropdown-trigger-icon"></i>
                                    </button>
                                    <div class="phase-dropdown-menu category-dropdown-menu-scroll" id="categoryDropdownMenu" role="listbox" aria-label="Requirement Category">
                                        <div class="phase-dropdown-option category-dropdown-option empty">
                                            <span class="phase-dropdown-option-label">
                                                <span class="phase-dropdown-option-title">Select a category</span>
                                                <span class="phase-dropdown-option-meta">Choose a phase first to load the available categories.</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="upload-modal-right">
                            <div class="upload-modal-section file">
                                <div class="upload-modal-section-head">
                                    <div class="upload-modal-section-icon"><i class="fa fa-paperclip"></i></div>
                                    <div>
                                        <div class="upload-modal-section-title">Choose File</div>
                                        <div class="upload-modal-section-subtitle">Upload a clear PDF copy of your requirement.</div>
                                    </div>
                                </div>
                                <div class="file-upload-zone" id="dropZone">
                                    <input type="file" name="file" required id="fileInput" data-max-size-mb="2" accept="application/pdf,.pdf">
                                    <div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
                                    <p id="fileLabel">Click or drag a file here to upload</p>
                                    <span>Accepts PDF files only | Max file size: 2 MB</span>
                                    <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="uploadedBy" value="{{ $user->full_name }}">
                    <input type="hidden" name="adviser" value="{{ $user->adviser_name }}">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-paper-plane me-1"></i> Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="basicRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-list-ul"></i> Basic Requirements
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px;">
                    These are the requirements you need to complete before starting your OJT.
                </div>
                <div class="phase-modal-list">
                    @forelse ($basicCategories as $category)
                        @php
                            $isSubmittedBasic = $submittedBasicNames->contains($category->fileName);
                        @endphp
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa {{ $isSubmittedBasic ? 'fa-check-circle' : 'fa-file-alt' }}" style="color:{{ $isSubmittedBasic ? '#16a34a' : '#64748b' }};"></i>
                                <span>{{ $category->fileName }}</span>
                            </div>
                            <span class="status-badge {{ $isSubmittedBasic ? 'status-approved' : 'status-default' }}">
                                {{ $isSubmittedBasic ? 'Submitted' : 'Pending' }}
                            </span>
                        </div>
                    @empty
                        <div style="font-size:13px;color:#6b7280;">No basic requirements configured yet.</div>
                    @endforelse
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

<div class="modal fade" id="otherRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-layer-group"></i> Other Requirements
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px;">
                    These will be available after all basic requirements are submitted and your Notarized MOA is completed.
                </div>
                <div class="phase-modal-list">
                    @forelse ($otherCategories as $category)
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa fa-file-alt" style="color:#64748b;"></i>
                                <span>{{ $category->fileName }}</span>
                            </div>
                            <span class="status-badge {{ $otherRequirementsUnlocked ? 'status-approved' : 'status-default' }}">
                                {{ $otherRequirementsUnlocked ? 'Available' : 'Locked' }}
                            </span>
                        </div>
                    @empty
                        <div style="font-size:13px;color:#6b7280;">No other requirements configured yet.</div>
                    @endforelse
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

<div class="modal fade" id="unlockRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-clipboard-list"></i> What Still Needs To Be Submitted
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px;">
                    Complete these items to unlock the other requirements phase.
                </div>
                <div class="phase-modal-list">
                    @if ($missingBasicCategories->isNotEmpty())
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa fa-file-alt" style="color:#64748b;"></i>
                                <span>Basic requirements left: {{ $missingBasicCategories->pluck('fileName')->implode(', ') }}</span>
                            </div>
                            <span class="status-badge status-pending">Pending</span>
                        </div>
                    @endif
                    @if (!$hasSubmittedNotarizedMoa)
                        <div class="phase-modal-item">
                            <div class="phase-modal-item-name">
                                <i class="fa fa-file-contract" style="color:#64748b;"></i>
                                <span>Submit your Notarized MOA from the MOA page.</span>
                            </div>
                            <span class="status-badge status-pending">Pending</span>
                        </div>
                    @endif
                    @if ($missingBasicCategories->isEmpty() && $hasSubmittedNotarizedMoa)
                        <div style="font-size:13px;color:#16a34a;">All required items are completed.</div>
                    @endif
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

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-eye"></i> View Requirement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0; background:#f8fafc;">
                <div style="padding:14px 18px; border-bottom:1px solid #e5e7eb; background:#fff; color:#475569; font-size:13px; font-weight:600; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                    <span id="previewBadge" class="file-preview-badge"><i class="fa fa-eye"></i> Preview available</span>
                    <a id="previewDownloadBtn" href="#" class="btn-download-file"><i class="fa fa-download"></i> Download</a>
                </div>
                <iframe id="previewFrame" title="Requirement Preview" style="width:100%; height:75vh; border:0; background:#fff;"></iframe>
                <div id="previewFallback" class="unsupported-file-message" style="display:none;">
                    <div class="unsupported-icon">
                        <i class="fa fa-file-alt"></i>
                    </div>
                    <h3>This type of file cannot be previewed</h3>
                    <p>Please download the file to view its contents.</p>
                    <a id="previewDownloadBtnBottom" href="#" class="btn-download-file"><i class="fa fa-download"></i> Download to View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // File upload zone label update
    document.getElementById('fileInput').addEventListener('change', function () {
        const label = document.getElementById('fileLabel');
        label.textContent = this.files.length > 0
            ? this.files[0].name
            : 'Click or drag a file here to upload';
    });

    const requirementPhaseSelect = document.getElementById('requirementPhaseSelect');
    const phaseDropdown = document.getElementById('phaseDropdown');
    const phaseDropdownTrigger = document.getElementById('phaseDropdownTrigger');
    const phaseDropdownTriggerText = document.getElementById('phaseDropdownTriggerText');
    const phaseDropdownOptions = phaseDropdown ? Array.from(phaseDropdown.querySelectorAll('.phase-dropdown-option')) : [];
    const requirementCategorySelect = document.getElementById('requirementCategorySelect');
    const categoryDropdown = document.getElementById('categoryDropdown');
    const categoryDropdownTrigger = document.getElementById('categoryDropdownTrigger');
    const categoryDropdownTriggerText = document.getElementById('categoryDropdownTriggerText');
    const categoryDropdownMenu = document.getElementById('categoryDropdownMenu');
    const phaseHelpText = document.getElementById('phaseHelpText');
    const requirementCategoriesByPhase = {
        basic: @json($basicCategories->map(fn ($category) => ['fileName' => $category->fileName])->values()),
        other: @json($otherCategories->map(fn ($category) => ['fileName' => $category->fileName])->values()),
    };
    const otherRequirementsUnlocked = @json($otherRequirementsUnlocked);
    const missingBasicRequirementNames = @json($missingBasicCategories->pluck('fileName')->values());
    const hasSubmittedNotarizedMoa = @json($hasSubmittedNotarizedMoa);
    const submittedRequirementNames = new Set(
        @json($submittedRequirementNames->values()).map(function (name) {
            return String(name || '').trim().toLowerCase();
        })
    );
    let categoryHoverBubble = null;

    function normalizeRequirementName(value) {
        return String(value || '').trim().toLowerCase();
    }

    function ensureCategoryHoverBubble() {
        if (categoryHoverBubble) {
            return categoryHoverBubble;
        }

        categoryHoverBubble = document.createElement('div');
        categoryHoverBubble.className = 'category-hover-bubble';
        document.body.appendChild(categoryHoverBubble);
        return categoryHoverBubble;
    }

    function hideCategoryHoverBubble() {
        if (!categoryHoverBubble) {
            return;
        }

        categoryHoverBubble.classList.remove('visible');
    }

    function showCategoryHoverBubble(target, message) {
        const bubble = ensureCategoryHoverBubble();
        bubble.textContent = message;
        bubble.classList.add('visible');

        const rect = target.getBoundingClientRect();
        const bubbleWidth = Math.min(320, window.innerWidth - 32);
        const bubbleHeight = bubble.offsetHeight || 56;

        let left = rect.left;
        if (left + bubbleWidth > window.innerWidth - 16) {
            left = window.innerWidth - bubbleWidth - 16;
        }
        left = Math.max(16, left);

        let top = rect.bottom + 10;
        if (top + bubbleHeight > window.innerHeight - 16) {
            top = rect.top - bubbleHeight - 10;
        }
        top = Math.max(16, top);

        bubble.style.left = left + 'px';
        bubble.style.top = top + 'px';
        bubble.style.width = bubbleWidth + 'px';
    }

    function setPhaseDropdownValue(value) {
        if (!requirementPhaseSelect || !phaseDropdownOptions.length) {
            return;
        }

        requirementPhaseSelect.value = value;

        phaseDropdownOptions.forEach(function (option) {
            const isActive = option.dataset.value === value;
            option.classList.toggle('active', isActive);
            if (isActive && phaseDropdownTriggerText) {
                const title = option.querySelector('.phase-dropdown-option-title');
                phaseDropdownTriggerText.textContent = title ? title.textContent.trim() : option.textContent.trim();
            }
        });
    }

    function closePhaseDropdown() {
        if (!phaseDropdown || !phaseDropdownTrigger) {
            return;
        }

        phaseDropdown.classList.remove('open');
        phaseDropdownTrigger.setAttribute('aria-expanded', 'false');
    }

    function openPhaseDropdown() {
        if (!phaseDropdown || !phaseDropdownTrigger) {
            return;
        }

        phaseDropdown.classList.add('open');
        phaseDropdownTrigger.setAttribute('aria-expanded', 'true');
    }

    function setCategoryDropdownValue(value, label) {
        if (!requirementCategorySelect || !categoryDropdownTriggerText) {
            return;
        }

        requirementCategorySelect.value = value || '';
        categoryDropdownTriggerText.textContent = label || 'Select a category';

        if (!categoryDropdownMenu) {
            return;
        }

        Array.from(categoryDropdownMenu.querySelectorAll('.phase-dropdown-option[data-value]')).forEach(function (option) {
            option.classList.toggle('active', option.dataset.value === (value || ''));
        });
    }

    function closeCategoryDropdown() {
        if (!categoryDropdown || !categoryDropdownTrigger) {
            return;
        }

        categoryDropdown.classList.remove('open');
        categoryDropdownTrigger.setAttribute('aria-expanded', 'false');
        hideCategoryHoverBubble();
    }

    function openCategoryDropdown() {
        if (!categoryDropdown || !categoryDropdownTrigger) {
            return;
        }

        categoryDropdown.classList.add('open');
        categoryDropdownTrigger.setAttribute('aria-expanded', 'true');
    }

    function updateRequirementCategoryOptions() {
        if (!requirementPhaseSelect || !requirementCategorySelect || !categoryDropdownMenu) {
            return;
        }

        const selectedPhase = requirementPhaseSelect.value || 'basic';
        const categories = requirementCategoriesByPhase[selectedPhase] || [];

        categoryDropdownMenu.innerHTML = '';

        if (!categories.length) {
            categoryDropdownMenu.innerHTML = ''
                + '<div class="phase-dropdown-option category-dropdown-option empty">'
                + '  <span class="phase-dropdown-option-label">'
                + '    <span class="phase-dropdown-option-title">No categories available</span>'
                + '    <span class="phase-dropdown-option-meta">Ask your professor to set up requirement categories for this phase.</span>'
                + '  </span>'
                + '</div>';
            setCategoryDropdownValue('', 'Select a category');
        } else {
            categories.forEach(function (category, index) {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'phase-dropdown-option category-dropdown-option';
                option.dataset.value = category.fileName;
                const isAlreadySubmitted = submittedRequirementNames.has(normalizeRequirementName(category.fileName));

                option.innerHTML = ''
                    + '<span class="phase-dropdown-option-label">'
                    + '  <span class="phase-dropdown-option-title">' + category.fileName + '</span>'
                    + '</span>';

                if (isAlreadySubmitted) {
                    option.classList.add('locked');
                    option.setAttribute('aria-disabled', 'true');
                    option.addEventListener('mouseenter', function () {
                        showCategoryHoverBubble(
                            option,
                            'This requirement is already submitted. Remove the existing submission first before uploading another file for it.'
                        );
                    });
                    option.addEventListener('mouseleave', hideCategoryHoverBubble);
                    option.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    });
                    option.innerHTML += ''
                        + '<span class="phase-dropdown-option-status">Submitted</span>';
                } else {
                    option.addEventListener('click', function () {
                        setCategoryDropdownValue(category.fileName, category.fileName);
                        closeCategoryDropdown();
                    });
                }

                categoryDropdownMenu.appendChild(option);

                if (index === 0) {
                    setCategoryDropdownValue('', 'Select a category');
                }
            });
        }

        if (!phaseHelpText) {
            return;
        }

        if (selectedPhase === 'other') {
            if (otherRequirementsUnlocked) {
                phaseHelpText.textContent = 'Other requirements are now unlocked. You can upload them here.';
            } else {
                const missingParts = [];
                if (missingBasicRequirementNames.length) {
                    missingParts.push('Basic requirements remaining: ' + missingBasicRequirementNames.join(', '));
                }
                if (!hasSubmittedNotarizedMoa) {
                    missingParts.push('Submit your Notarized MOA from the MOA page first.');
                }
                phaseHelpText.textContent = missingParts.join(' ');
            }
        } else {
            phaseHelpText.textContent = 'Upload your basic requirements first.';
        }
    }

    if (phaseDropdownTrigger && phaseDropdown) {
        phaseDropdownTrigger.addEventListener('click', function () {
            if (phaseDropdown.classList.contains('open')) {
                closePhaseDropdown();
            } else {
                openPhaseDropdown();
            }
        });

        phaseDropdownOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                const value = option.dataset.value;
                const isLocked = option.dataset.locked === 'true';

                if (isLocked) {
                    return;
                }

                setPhaseDropdownValue(value);
                closePhaseDropdown();
                updateRequirementCategoryOptions();
            });
        });

        document.addEventListener('click', function (event) {
            if (!phaseDropdown.contains(event.target)) {
                closePhaseDropdown();
            }
        });
    }

    if (categoryDropdownTrigger && categoryDropdown) {
        categoryDropdownTrigger.addEventListener('click', function () {
            if (categoryDropdown.classList.contains('open')) {
                closeCategoryDropdown();
            } else {
                openCategoryDropdown();
            }
        });

        document.addEventListener('click', function (event) {
            if (!categoryDropdown.contains(event.target)) {
                closeCategoryDropdown();
                hideCategoryHoverBubble();
            }
        });
    }

    if (requirementPhaseSelect) {
        setPhaseDropdownValue(requirementPhaseSelect.value || 'basic');
        updateRequirementCategoryOptions();
    }

    // SweetAlert remove confirmation
    function showDenialReason(fileName, denialReason) {
        Swal.fire({
            title: 'Denial Reason',
            html: '<div style="text-align:left; font-size:13px; line-height:1.6;">'
                + '<div style="font-weight:700; margin-bottom:8px; color:#1f2937;">' + fileName + '</div>'
                + '<div style="color:#4b5563;">' + denialReason + '</div>'
                + '</div>',
            icon: 'info',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Close'
        });
    }

    function showRemoveConfirmation(fileId) {
        Swal.fire({
            title: 'Remove this requirement?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa fa-trash"></i> Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                removeFileCategory(fileId);
            }
        });
    }

    function removeFileCategory(fileId) {
        $.ajax({
            type: 'POST',
            url: '/remove/filesReq/' + fileId,
            data: { _token: "{{ csrf_token() }}" },
            success: function () {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Requirement removed',
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
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/upload-size-guard.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
