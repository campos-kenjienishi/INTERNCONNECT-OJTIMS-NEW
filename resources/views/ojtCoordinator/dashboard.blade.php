<!DOCTYPE html>
    <html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InternConnect - Dashboard</title>
        <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
        <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">

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

            .topbar-right { display: flex; align-items: center; gap: 12px; }

            .topbar-date {
                font-size: 12.5px; color: #888; font-weight: 500;
            }

            .topbar-badge {
                display: flex; align-items: center; gap: 8px;
                background: #fff5f5; border: 1px solid #fecaca;
                border-radius: 20px; padding: 6px 14px;
                font-size: 12.5px; font-weight: 600; color: var(--red-dark);
            }

            /* =============== PAGE =============== */
            .page-content { padding: 28px; flex: 1; }

            .page-header { margin-bottom: 24px; }
            .page-header h1 { font-size: 26px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
            .page-header h1 span { color: var(--red); }
            .page-header p { font-size: 13.5px; color: #888; margin-top: 4px; }

            /* =============== STATS ROW =============== */
            .stats-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 18px;
                margin-bottom: 28px;
            }

            .stat-card {
                background: #fff; border-radius: 16px;
                padding: 22px 24px;
                display: flex; align-items: center; justify-content: space-between;
                box-shadow: 0 2px 12px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                text-decoration: none; color: inherit;
                transition: all 0.25s; cursor: pointer;
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 24px rgba(0,0,0,0.1);
                text-decoration: none; color: inherit;
            }

            .stat-card-left { flex: 1; }
            .stat-num  { font-size: 28px; font-weight: 800; color: #1a1a1a; line-height: 1; }
            .stat-name { font-size: 13px; color: #888; margin-top: 4px; font-weight: 500; }

            /* =============== ANALYTICS =============== */
            .analytics-section { margin-bottom: 26px; }
            .analytics-header {
                display: flex; align-items: flex-end; justify-content: space-between;
                gap: 12px; margin-bottom: 14px; flex-wrap: wrap;
            }
            .analytics-header h2 { font-size: 18px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.3px; }
            .analytics-header p { font-size: 13px; color: #888; margin-top: 4px; }
            .analytics-updated {
                font-size: 12px; color: #666; background: #fff;
                border: 1px solid #e5e7eb; border-radius: 999px;
                padding: 8px 12px; white-space: nowrap;
            }
            .analytics-grid {
                display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px; margin-bottom: 24px;
            }
            .analytics-card.full-width { grid-column: 1 / -1; }
            .analytics-list { display: flex; flex-direction: column; gap: 14px; }
            .analytics-item { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
            .analytics-item-title { font-size: 13px; font-weight: 600; color: #333; }
            .analytics-item-meta { font-size: 12px; color: #777; margin-top: 2px; }
            .analytics-track {
                width: 100%; height: 10px; border-radius: 999px; background: #edf0f5;
                overflow: hidden; margin-top: 8px;
            }
            .analytics-fill { height: 100%; border-radius: 999px; transition: width 0.3s ease; }
            .analytics-fill.green { background: linear-gradient(90deg, #22c55e, #16a34a); }
            .analytics-fill.red { background: linear-gradient(90deg, #ef4444, #dc2626); }
            .analytics-fill.blue { background: linear-gradient(90deg, #60a5fa, #2563eb); }
            .analytics-fill.amber { background: linear-gradient(90deg, #f59e0b, #d97706); }
            .analytics-fill.purple { background: linear-gradient(90deg, #a78bfa, #7c3aed); }
            .analytics-fill.teal { background: linear-gradient(90deg, #2dd4bf, #0f766e); }
            .analytics-month-row { display: grid; grid-template-columns: 96px 1fr; gap: 14px; align-items: start; }
            .analytics-month-label { font-size: 12px; font-weight: 700; color: #555; padding-top: 3px; }
            .analytics-bars { display: flex; flex-direction: column; gap: 10px; }
            .analytics-bar-group { display: flex; align-items: center; gap: 10px; }
            .analytics-bar-group span { width: 52px; font-size: 11px; color: #777; font-weight: 600; }
            .analytics-bar { flex: 1; }
            .analytics-bar .analytics-fill { height: 8px; }

            .stat-change {
                display: inline-flex; align-items: center; gap: 4px;
                font-size: 11.5px; font-weight: 600; margin-top: 6px;
                padding: 3px 8px; border-radius: 20px;
            }

            .stat-change.up   { background: #dcfce7; color: #16a34a; }
            .stat-change.blue { background: #dbeafe; color: #2563eb; }
            .stat-change.amber{ background: #fef9c3; color: #ca8a04; }

            .stat-icon-box {
                width: 52px; height: 52px; border-radius: 14px;
                display: flex; align-items: center; justify-content: center;
                font-size: 22px; flex-shrink: 0;
            }

            .stat-icon-box.red    { background: #fee2e2; color: var(--red); }
            .stat-icon-box.blue   { background: #dbeafe; color: #2563eb; }
            .stat-icon-box.green  { background: #dcfce7; color: #16a34a; }
            .stat-icon-box.purple { background: #ede9fe; color: #7c3aed; }

            /* =============== TWO-COLUMN LAYOUT =============== */
            .dashboard-grid {
                display: grid;
                grid-template-columns: 1fr 380px;
                gap: 22px;
                align-items: start;
            }

            /* =============== ANNOUNCEMENT CARD =============== */
            .panel-card {
                background: #fff; border-radius: 16px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: hidden;
            }

            .panel-card-header {
                display: flex; align-items: center; gap: 12px;
                padding: 18px 24px;
                border-bottom: 1px solid #f0f0f0;
                background: #fafafa;
            }

            .panel-header-icon {
                width: 38px; height: 38px; border-radius: 10px;
                background: #fee2e2; display: flex;
                align-items: center; justify-content: center;
                color: var(--red); font-size: 15px; flex-shrink: 0;
            }

            .panel-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
            .panel-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }

            .panel-card-body { padding: 24px; }

            .announcement-table-wrap { overflow-x: auto; }
            .announcement-manage-table {
                width: 100%;
                min-width: 720px;
                border-collapse: collapse;
                table-layout: fixed;
            }
            .announcement-manage-table th {
                background: #fafafa;
                color: #555;
                font-size: 11.5px;
                font-weight: 700;
                text-transform: uppercase;
                padding: 12px 16px;
                text-align: left;
                border-bottom: 1px solid #f0f0f0;
            }
            .announcement-manage-table td {
                padding: 14px 16px;
                color: #333;
                border-bottom: 1px solid #f5f5f5;
                vertical-align: middle;
            }
            .announcement-manage-table tbody tr:hover td { background: #fff5f5; }
            .announcement-title-cell strong {
                display: block;
                color: #1a1a1a;
                overflow-wrap: anywhere;
            }
            .announcement-title-cell span {
                display: block;
                color: #777;
                font-size: 12px;
                line-height: 1.45;
                margin-top: 4px;
                overflow-wrap: anywhere;
            }
            .btn-delete-announcement {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 12px;
                border: 1.5px solid #fecaca;
                border-radius: 8px;
                background: #fff;
                color: #dc2626;
                font-family: 'Poppins', sans-serif;
                font-size: 12.5px;
                font-weight: 600;
                cursor: pointer;
            }
            .btn-edit-announcement {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 12px;
                border: 1.5px solid #bfdbfe;
                border-radius: 8px;
                background: #fff;
                color: #2563eb;
                font-family: 'Poppins', sans-serif;
                font-size: 12.5px;
                font-weight: 600;
                cursor: pointer;
                margin-right: 8px;
            }
            .announcement-toolbar {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-left: auto;
            }
            .announcement-sort-select {
                min-width: 150px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 7px 10px;
                background: #fff;
                color: #374151;
                font-family: 'Poppins', sans-serif;
                font-size: 12.5px;
                outline: none;
            }
            .announcement-sort-select:focus {
                border-color: var(--red);
                box-shadow: 0 0 0 3px rgba(220,38,38,.12);
            }
            .announcement-table-wrap .dataTables_wrapper {
                padding: 18px 24px;
                font-family: 'Poppins', sans-serif;
                font-size: 13px;
            }
            .announcement-table-wrap .dataTables_length,
            .announcement-table-wrap .dataTables_filter {
                margin-bottom: 14px;
            }
            .announcement-table-wrap .dataTables_length label,
            .announcement-table-wrap .dataTables_filter label,
            .announcement-table-wrap .dataTables_info {
                color: #4b5563 !important;
                font-size: 12.5px;
                font-weight: 600;
            }
            .announcement-table-wrap .dataTables_length select,
            .announcement-table-wrap .dataTables_filter input {
                border: 1px solid #e5e7eb !important;
                border-radius: 8px !important;
                background: #fff !important;
                color: #1f2937 !important;
                font-family: 'Poppins', sans-serif !important;
                font-size: 12.5px !important;
                padding: 6px 10px !important;
            }
            .announcement-table-wrap .dataTables_filter input:focus {
                border-color: var(--red) !important;
                box-shadow: 0 0 0 3px rgba(220,38,38,.12) !important;
            }
            .announcement-table-wrap .dataTables_paginate .paginate_button {
                border-radius: 8px !important;
                border: 1px solid #e5e7eb !important;
                background: #fff !important;
                color: #374151 !important;
                font-family: 'Poppins', sans-serif !important;
                font-size: 12px !important;
                font-weight: 700 !important;
            }
            .announcement-table-wrap .dataTables_paginate .paginate_button.current {
                background: linear-gradient(135deg, #dc2626, #991b1b) !important;
                border-color: #991b1b !important;
                color: #fff !important;
            }
            .announcement-table-wrap .dataTables_paginate .paginate_button:hover {
                background: #fee2e2 !important;
                border-color: #fecaca !important;
                color: var(--red) !important;
            }
            .announcement-table-wrap .dataTables_empty {
                color: #9ca3af;
                padding: 28px 12px !important;
                text-align: center;
            }

            /* Form fields */
            .field-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
            .field-group:last-of-type { margin-bottom: 0; }

            .field-label {
                font-size: 12.5px; font-weight: 600; color: #444;
                display: flex; align-items: center; gap: 6px;
            }

            .field-label i { color: var(--red); font-size: 11px; }

            .field-input {
                width: 100%; background: #fafafa;
                border: 1.5px solid #e8e8e8; border-radius: 10px;
                color: #1a1a1a; font-family: 'Poppins', sans-serif;
                font-size: 13.5px; padding: 11px 14px; outline: none;
                transition: all 0.25s;
            }

            .field-input:focus {
                border-color: var(--red); background: #fff;
                box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
            }

            textarea.field-input { resize: vertical; min-height: 120px; }

            .panel-card-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
                background: #fafafa;
                display: flex; justify-content: flex-end;
            }

            .btn-submit {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 11px 28px;
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
                border: none; border-radius: 10px; color: #fff;
                font-family: 'Poppins', sans-serif; font-size: 14px;
                font-weight: 600; cursor: pointer; transition: all 0.3s;
                box-shadow: 0 4px 16px rgba(220,38,38,0.25);
            }

            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            }

            /* =============== QUICK LINKS PANEL =============== */
            .quick-links-grid {
                display: grid; grid-template-columns: 1fr 1fr;
                gap: 12px; padding: 20px;
            }

            .quick-link-item {
                display: flex; flex-direction: column; align-items: center;
                gap: 10px; padding: 18px 12px;
                background: #fafafa; border: 1.5px solid #f0f0f0;
                border-radius: 12px; text-decoration: none; color: #333;
                transition: all 0.25s; text-align: center;
            }

            .quick-link-item:hover {
                border-color: #fecaca; background: #fff5f5;
                color: var(--red); text-decoration: none;
                transform: translateY(-2px);
                box-shadow: 0 4px 14px rgba(220,38,38,0.1);
            }

            .quick-link-icon {
                width: 42px; height: 42px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
                font-size: 17px; transition: all 0.25s;
            }

            .quick-link-icon.red    { background: #fee2e2; color: var(--red); }
            .quick-link-icon.blue   { background: #dbeafe; color: #2563eb; }
            .quick-link-icon.green  { background: #dcfce7; color: #16a34a; }
            .quick-link-icon.purple { background: #ede9fe; color: #7c3aed; }
            .quick-link-icon.amber  { background: #fef9c3; color: #ca8a04; }
            .quick-link-icon.teal   { background: #ccfbf1; color: #0d9488; }

            .quick-link-item:hover .quick-link-icon {
                background: #fee2e2; color: var(--red);
            }

            .quick-link-label { font-size: 12px; font-weight: 600; line-height: 1.3; }

            /* Mobile overlay */
            .sidebar-overlay {
                display: none !important; position: fixed; inset: 0;
                background: rgba(0,0,0,0.55) !important; z-index: 1999 !important;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
            }

            .sidebar-overlay.active {
                display: block !important;
                opacity: 1 !important;
                pointer-events: auto !important;
            }

            @media (max-width: 1100px) {
                .dashboard-grid { grid-template-columns: 1fr; }
            }

            @media (max-width: 900px) {
                .sidebar {
                    width: var(--sidebar-w);
                    z-index: 2000;
                    transform: translateX(-100%);
                    transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
                }
                .sidebar.mobile-open { transform: translateX(0); }
                .main-content { margin-left: 0 !important; }
                .page-content { padding: 18px; }
                .topbar-title { display: none; }
                .stats-row { grid-template-columns: 1fr 1fr; }
                .topbar-date { display: none; }
                .welcome-banner { flex-direction: column; gap: 12px; align-items: flex-start; }
                .welcome-actions { width: 100%; }
                .welcome-video-btn { width: 100%; justify-content: center; }
                .welcome-icon { align-self: flex-end; margin-top: -46px; }
            }

            @media (max-width: 480px) {
                .stats-row { grid-template-columns: 1fr; }
            }

            /* =============== DATE BADGE & TIME MODAL =============== */
            .date-badge {
                font-size: 12.5px;
                color: #888;
                background: #fff;
                border: 1px solid #e5e5e5;
                border-radius: 8px;
                padding: 6px 14px;
                cursor: pointer;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                gap: 7px;
                user-select: none;
            }

            body.dark-mode .date-badge {
                background: #2a2a2a;
                border: 1px solid #3a3a3a;
                color: #e0e0e0;
            }

            .date-badge:hover {
                border-color: #d0d0d0;
                color: #666;
            }

            body.dark-mode .date-badge:hover {
                border-color: #444;
                color: #fff;
            }

            .date-badge i { font-size: 11px; }
            .date-badge .pulse-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: #16a34a;
                animation: pulse-green 1.8s ease-in-out infinite;
                flex-shrink: 0;
            }

            @keyframes pulse-green {
                0%, 100% { transform: scale(1); opacity: 1; }
                50%       { transform: scale(1.5); opacity: 0.5; }
            }

            /* =============================================
               DATE & TIME MODAL
            ============================================= */
            .dt-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(4px);
                z-index: 2000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .dt-overlay.open {
                opacity: 1;
                pointer-events: all;
            }

            .dt-modal {
                background: #fff;
                border-radius: 22px;
                width: 360px;
                box-shadow: 0 32px 80px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.05);
                overflow: hidden;
                transform: scale(0.88) translateY(20px);
                transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
                opacity: 0;
            }

            body.dark-mode .dt-modal {
                background: #1a1a1a;
                box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.08);
            }

            .dt-overlay.open .dt-modal {
                transform: scale(1) translateY(0);
                opacity: 1;
            }

            /* Modal gradient header */
            .dt-modal-header {
                background: linear-gradient(135deg, #7f0000 0%, #b91c1c 45%, #dc2626 100%);
                padding: 20px 22px 16px;
                position: relative;
                overflow: hidden;
            }

            .dt-modal-header::before {
                content: '';
                position: absolute;
                top: -40px;
                right: -40px;
                width: 160px;
                height: 160px;
                border-radius: 50%;
                background: rgba(255,255,255,0.06);
                pointer-events: none;
            }

            .dt-modal-header::after {
                content: '';
                position: absolute;
                bottom: -30px;
                left: 20px;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: rgba(255,255,255,0.04);
                pointer-events: none;
            }

            .dt-header-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                z-index: 1;
            }

            .dt-header-title {
                font-size: 13px;
                font-weight: 600;
                color: rgba(255,255,255,0.8);
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .dt-close-btn {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                background: rgba(255,255,255,0.15);
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 13px;
                transition: background 0.2s;
            }

            .dt-close-btn:hover { background: rgba(255,255,255,0.25); }

            /* Live clock inside modal header */
            .dt-clock-display {
                margin-top: 10px;
                position: relative;
                z-index: 1;
            }

            .dt-time-big {
                font-size: 42px;
                font-weight: 800;
                color: #fff;
                letter-spacing: -1px;
                line-height: 1;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .dt-time-big .colon {
                animation: blink-colon 1s step-end infinite;
                display: inline-block;
                margin: 0 1px;
            }

            @keyframes blink-colon {
                0%, 100% { opacity: 1; }
                50%       { opacity: 0.15; }
            }

            .dt-time-ampm {
                font-size: 14px;
                font-weight: 700;
                color: rgba(255,255,255,0.7);
                margin-left: 6px;
                align-self: flex-end;
                margin-bottom: 6px;
            }

            .dt-date-sub {
                font-size: 12.5px;
                color: rgba(255,255,255,0.65);
                margin-top: 4px;
                font-weight: 500;
                letter-spacing: 0.3px;
            }

            /* Analog clock */
            .dt-analog-wrap {
                display: flex;
                justify-content: center;
                padding: 16px 0 8px;
            }

            .analog-clock {
                width: 110px;
                height: 110px;
                border-radius: 50%;
                background: #fafafa;
                border: 3px solid #e5e5e5;
                position: relative;
                box-shadow: inset 0 2px 8px rgba(0,0,0,0.08), 0 4px 18px rgba(0,0,0,0.1);
            }

            body.dark-mode .analog-clock {
                background: #2a2a2a;
                border-color: #3a3a3a;
                box-shadow: inset 0 2px 8px rgba(0,0,0,0.3), 0 4px 18px rgba(0,0,0,0.3);
            }

            /* Hour markers */
            .clock-mark {
                position: absolute;
                width: 2px;
                border-radius: 2px;
                background: #888;
            }

            body.dark-mode .clock-mark {
                background: #666;
            }

            /* Center dot */
            .clock-center {
                position: absolute;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: var(--red);
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 10;
                box-shadow: 0 0 0 2px #fff;
            }

            body.dark-mode .clock-center {
                box-shadow: 0 0 0 2px #2a2a2a;
            }

            /* Hands */
            .hand {
                position: absolute;
                bottom: 50%;
                left: 50%;
                transform-origin: bottom center;
                border-radius: 4px 4px 0 0;
            }

            .hour-hand   { width: 3.5px; height: 28px; background: #333; margin-left: -1.75px; }
            .minute-hand { width: 2.5px; height: 36px; background: #333; margin-left: -1.25px; }
            .second-hand { width: 1.5px; height: 40px; background: var(--red); margin-left: -0.75px; }

            body.dark-mode .hour-hand   { background: #e0e0e0; }
            body.dark-mode .minute-hand { background: #e0e0e0; }

            /* Calendar */
            .dt-calendar { padding: 0 18px 18px; }

            .cal-nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 2px;
            }

            .cal-nav-btn {
                width: 30px;
                height: 30px;
                border-radius: 8px;
                background: #fafafa;
                border: 1px solid #e5e5e5;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #888;
                font-size: 12px;
                transition: all 0.2s;
            }

            .cal-nav-btn:hover {
                background: #fee2e2;
                border-color: #fecaca;
                color: var(--red);
            }

            body.dark-mode .cal-nav-btn {
                background: #3a3a3a;
                border-color: #555;
                color: #999;
            }

            body.dark-mode .cal-nav-btn:hover {
                background: rgba(220,38,38,0.2);
                border-color: var(--red);
                color: #ff6b6b;
            }

            .cal-month-label {
                font-size: 14px;
                font-weight: 700;
                color: #1a1a1a;
            }

            body.dark-mode .cal-month-label { color: #fff; }

            .cal-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 2px;
            }

            .cal-day-name {
                text-align: center;
                font-size: 10px;
                font-weight: 700;
                color: #888;
                text-transform: uppercase;
                padding: 4px 0 6px;
            }

            body.dark-mode .cal-day-name { color: #999; }

            .cal-day {
                text-align: center;
                font-size: 12.5px;
                font-weight: 500;
                color: #1a1a1a;
                padding: 7px 4px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.15s;
                line-height: 1;
            }

            body.dark-mode .cal-day { color: #e0e0e0; }

            .cal-day:hover:not(.empty):not(.today) {
                background: #fafafa;
                color: var(--red);
            }

            body.dark-mode .cal-day:hover:not(.empty):not(.today) {
                background: #3a3a3a;
            }

            .cal-day.empty  { cursor: default; color: transparent; }
            .cal-day.other-month { color: #aaa; }
            body.dark-mode .cal-day.other-month { color: #666; }

            .cal-day.today {
                background: linear-gradient(135deg, #dc2626, #991b1b);
                color: #fff !important;
                font-weight: 700;
                box-shadow: 0 3px 10px rgba(220,38,38,0.35);
            }

            .cal-day.selected:not(.today) {
                background: #fee2e2;
                color: var(--red);
                font-weight: 700;
            }

            body.dark-mode .cal-day.selected:not(.today) {
                background: rgba(220,38,38,0.2);
            }
            /* =============== WELCOME BANNER =============== */
        .welcome-banner {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
            border-radius: 16px;
            padding: 28px 32px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(185,28,28,0.25);
        }

        .welcome-banner::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .welcome-banner::after {
            content: '';
            position: absolute; bottom: -40px; right: 80px;
            width: 140px; height: 140px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .welcome-text { position: relative; z-index: 1; }
        .welcome-text h2 {
            font-size: 22px; font-weight: 800; color: #fff;
            margin-bottom: 6px; letter-spacing: -0.3px;
        }

        .welcome-text p {
            font-size: 13.5px; color: rgba(255,255,255,0.7);
            line-height: 1.5;
        }

        .welcome-actions {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .welcome-video-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.25s ease;
            position: relative;
            z-index: 1;
        }

        .welcome-video-btn:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.28);
            box-shadow: 0 8px 18px rgba(0,0,0,0.12);
        }

        .welcome-video-btn i {
            font-size: 14px;
        }

        .welcome-icon {
            font-size: 64px;
            opacity: 0.2;
            position: relative;
            z-index: 1;
            color: #fff;
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
/* ===== DARK MODE ===== */
body.dark-mode { background: #000000; color: #e0e0e0; }
body.dark-mode .main-content { background: #000000; }
body.dark-mode .sidebar { box-shadow: 4px 0 24px rgba(0,0,0,0.4); }
body.dark-mode .topbar { background: #252525 !important; border-bottom: 1px solid #3a3a3a; }
body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
body.dark-mode .topbar-title { color: #999; }
body.dark-mode .topbar-badge { background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b; }
body.dark-mode .page-content { background: #000000; }
body.dark-mode h1 { color: #fff !important; }

/* Stat cards */
body.dark-mode .stat-card { background: #2a2a2a !important; border: 1px solid #3a3a3a; box-shadow: 0 2px 10px rgba(0,0,0,0.3); color: #e0e0e0; }
body.dark-mode .stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.4) !important; }
body.dark-mode .stat-num { color: #fff; }
body.dark-mode .stat-name { color: #999; }

/* Panel cards */
body.dark-mode .panel-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
body.dark-mode .panel-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
body.dark-mode .panel-card-header h2 { color: #fff; }
body.dark-mode .panel-card-header p { color: #999; }
body.dark-mode .panel-card-footer { background: #2a2a2a !important; border-top: 1px solid #3a3a3a; }
body.dark-mode .announcement-manage-table th { background: #2a2a2a; color: #aaa; border-bottom-color: #3a3a3a; }
body.dark-mode .announcement-manage-table td { color: #e0e0e0; border-bottom-color: rgba(255,255,255,0.05); }
            body.dark-mode .announcement-manage-table tbody tr:hover td { background: rgba(220,38,38,0.1); }
body.dark-mode .announcement-title-cell strong { color: #fff; }
body.dark-mode .announcement-title-cell span { color: #aaa; }
body.dark-mode .btn-edit-announcement { background: rgba(37,99,235,0.12); border-color: rgba(96,165,250,0.3); color: #93c5fd; }
body.dark-mode .btn-edit-announcement:hover { background: rgba(37,99,235,0.22); color: #bfdbfe; }
body.dark-mode .announcement-sort-select { background: #2a2a2a; color: #e5e7eb; border-color: #3a3a3a; }
body.dark-mode .announcement-table-wrap .dataTables_length label,
body.dark-mode .announcement-table-wrap .dataTables_filter label,
body.dark-mode .announcement-table-wrap .dataTables_info { color: #d1d5db !important; }
body.dark-mode .announcement-table-wrap .dataTables_length select,
body.dark-mode .announcement-table-wrap .dataTables_filter input {
    background: #2a2a2a !important;
    color: #e5e7eb !important;
    border-color: #3a3a3a !important;
}
body.dark-mode .announcement-table-wrap .dataTables_paginate .paginate_button {
    background: #2a2a2a !important;
    color: #e5e7eb !important;
    border-color: #3a3a3a !important;
}
body.dark-mode .announcement-table-wrap .dataTables_paginate .paginate_button:hover {
    background: rgba(220,38,38,0.2) !important;
    color: #ff6b6b !important;
    border-color: rgba(220,38,38,0.3) !important;
}
body.dark-mode .announcement-table-wrap .dataTables_empty { color: #9ca3af; }
body.dark-mode .analytics-header h2 { color: #fff; }
body.dark-mode .analytics-header p { color: #aaa; }
body.dark-mode .analytics-updated { background: #2a2a2a; border: 1px solid #3a3a3a; color: #e0e0e0; }
body.dark-mode .analytics-item-title { color: #f3f4f6; }
body.dark-mode .analytics-item-meta { color: #9ca3af; }
body.dark-mode .analytics-track { background: #3a3a3a; }
body.dark-mode .analytics-month-label { color: #d1d5db; }
body.dark-mode .analytics-bar-group span { color: #9ca3af; }

/* Form fields */
body.dark-mode .field-label { color: #e0e0e0; }
body.dark-mode .field-input { background: #3a3a3a !important; color: #e0e0e0 !important; border: 1.5px solid #4a4a4a !important; }
body.dark-mode .field-input::placeholder { color: #777 !important; }
body.dark-mode .field-input:focus { border-color: var(--red) !important; background: #3a3a3a !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.2); }
body.dark-mode textarea.field-input { background: #3a3a3a !important; color: #e0e0e0 !important; }

/* Quick links — gray in dark mode */
body.dark-mode .quick-link-item { background: #2a2a2a; border-color: #3a3a3a; color: #e0e0e0; }
body.dark-mode .quick-link-item:hover { background: #333; border-color: #555; color: #fff; box-shadow: 0 4px 14px rgba(0,0,0,0.3); }
body.dark-mode .quick-link-item:hover .quick-link-icon { background: #4a4a4a; color: #e0e0e0; }
body.dark-mode .quick-link-icon.red    { background: #3a2a2a; color: #ff8080; }
body.dark-mode .quick-link-icon.blue   { background: #1e2a3a; color: #7eb3f5; }
body.dark-mode .quick-link-icon.green  { background: #1e3a2a; color: #6ee7b7; }
body.dark-mode .quick-link-icon.purple { background: #2a1e3a; color: #c4b5fd; }
body.dark-mode .quick-link-icon.amber  { background: #3a2e1e; color: #fcd34d; }
body.dark-mode .quick-link-icon.teal   { background: #1e3a38; color: #5eead4; }
body.dark-mode .quick-link-label { color: #e0e0e0; }

/* Footer */
body.dark-mode .dashboard-footer { background: #1a1a1a !important; border-top: 1px solid #3a3a3a; color: #999; }
body.dark-mode .dashboard-footer a { color: #999; }
body.dark-mode .dashboard-footer a:hover { color: var(--red); }
body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
body.dark-mode .dashboard-footer .footer-copy { color: #999; }
body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }
body.dark-mode .dashboard-footer .footer-logo { opacity: 0.4; }
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
                @if(isset($data->profile_photo) && $data->profile_photo)
                    <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
                @else
                    <i class="fa fa-user-tie"></i>
                @endif
            </div>
            <div class="user-info">
                <span class="user-name">{{ $data->full_name }}</span>
                <span class="user-role">OJT Coordinator</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ url('/dashboard') }}" class="nav-item active">
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
                    <i class="fa fa-user-shield"></i>
                    OJT Coordinator
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">

            <!-- Page Header -->
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h1 style="font-size:26px; font-weight:800; color:#1a1a1a; letter-spacing:-0.5px;">
                    Home <span style="color:var(--red);">Dashboard</span>
                </h1>
                <div class="date-badge" id="dateBadge" title="Click to view calendar & clock">
                    <span class="pulse-dot"></span>
                    <i class="fa fa-calendar-alt"></i>
                    <span id="currentDate"></span>
                </div>
            </div>

<!-- Red welcome banner, no date inside -->
<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Welcome to your coordinator dashboard</h2>
        <p>
            For first-time users, please watch these how-to videos to get familiar with the coordinator workflow.
        </p>
        <div class="welcome-actions">
            <a href="https://www.youtube.com/playlist?list=PLyMOKHLwy4fOPcBDkY_buk3Ol98a91zWW" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                <i class="fab fa-youtube"></i>
                How To Videos
            </a>
        </div>
    </div>
    <div class="welcome-icon">
        <i class="fa fa-user-shield"></i>
    </div>
</div>

            <!-- Stats Row -->
            <div class="stats-row">
                <a href="{{ url('/studentLists') }}" class="stat-card">
                    <div class="stat-card-left">
                        <div class="stat-num">{{ $roleCount }}</div>
                        <div class="stat-name">Total Students</div>
                        <div class="stat-change up">
                            <i class="fa fa-users" style="font-size:10px;"></i> Enrolled
                        </div>
                    </div>
                    <div class="stat-icon-box red">
                        <i class="fa fa-users"></i>
                    </div>
                </a>

                <a href="{{ url('/professorTab') }}" class="stat-card">
                    <div class="stat-card-left">
                        <div class="stat-num">{{ $roleCountP }}</div>
                        <div class="stat-name">Total Professors</div>
                        <div class="stat-change blue">
                            <i class="fa fa-chalkboard-teacher" style="font-size:10px;"></i> Active
                        </div>
                    </div>
                    <div class="stat-icon-box blue">
                        <i class="fa fa-chalkboard-teacher"></i>
                    </div>
                </a>

                <a href="{{ url('/uploadpage') }}" class="stat-card">
                    <div class="stat-card-left">
                        <div class="stat-num">{{ $fileCount }}</div>
                        <div class="stat-name">Uploaded Templates</div>
                        <div class="stat-change amber">
                            <i class="fa fa-file" style="font-size:10px;"></i> Available
                        </div>
                    </div>
                    <div class="stat-icon-box green">
                        <i class="fa fa-file-upload"></i>
                    </div>
                </a>
            </div>

            @if(!empty($dashboardInsights))
                <section data-ai-insight-card style="background:#fff; border:1px solid #f1f1f1; border-left:5px solid var(--red); border-radius:14px; box-shadow:0 8px 28px rgba(15,23,42,0.06); margin-bottom:22px; overflow:hidden;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px 20px; border-bottom:1px solid #f3f4f6; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center;">
                                <i class="fa fa-robot"></i>
                            </div>
                            <div>
                                <h2 style="font-size:18px; font-weight:800; color:#111827; margin:0;">Today&apos;s AI Brief</h2>
                                <p style="font-size:13px; color:#777; margin:4px 0 0;">Generated from current dashboard activity</p>
                            </div>
                        </div>
                        @php
                            $dashboardAiSource = $dashboardInsights['source'] ?? 'fallback';
                            $dashboardAiLabel = $dashboardAiSource === 'gemini'
                                ? 'Gemini AI'
                                : ($dashboardAiSource === 'openai' ? 'OpenAI' : 'Internal insight');
                        @endphp
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <button type="button" data-ai-insight-button data-ai-context="dashboardAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:#dc2626; color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer;">
                                <i class="fa fa-magic"></i> Generate AI Insight
                            </button>
                            <span style="display:inline-flex; align-items:center; gap:7px; border:1px solid #fecaca; background:#fff5f5; color:#b91c1c; border-radius:999px; padding:8px 13px; font-size:12px; font-weight:800;">
                                <i class="fa fa-brain"></i> <span data-ai-badge>{{ $dashboardAiLabel }}</span>
                            </span>
                        </div>
                    </div>
                    <div data-ai-result-panel style="display:none; padding:20px;">
                        @if(($dashboardInsights['source'] ?? '') === 'fallback')
                            <div data-ai-notice style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;">
                                <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                                <div><strong>Gemini is temporarily unavailable.</strong> <span data-ai-notice-text>{{ $dashboardInsights['availability']['message'] ?? 'Internal insight is shown for now. Try again in a few minutes, or later if the daily free-tier quota was reached.' }}</span></div>
                            </div>
                        @endif
                        <div data-ai-status style="display:none; margin-bottom:12px; font-size:12px; color:#777;"></div>
                        <p data-ai-summary style="font-size:15px; line-height:1.7; color:#1f2937; margin:0 0 18px;">{{ $dashboardInsights['summary'] ?? 'No dashboard insight available.' }}</p>

                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                            <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                                <ul data-ai-findings style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                    @forelse(($dashboardInsights['key_findings'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <li>No key findings available.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                                <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                    @forelse(($dashboardInsights['watchouts'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <li>No major watchouts detected.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                                <ul data-ai-actions style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                    @forelse(($dashboardInsights['recommendations'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <li>No actions suggested.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        @php
                            $dashboardPromptSuggestions = [
                                ['label' => 'Priorities', 'question' => 'What should we prioritize today based on this dashboard?'],
                                ['label' => 'Risk', 'question' => 'What risks does this dashboard show and why?'],
                                ['label' => 'Action plan', 'question' => 'Create a short coordinator action plan from this dashboard.'],
                            ];

                            if (($pendingStudents ?? 0) > 0) {
                                $dashboardPromptSuggestions[] = ['label' => 'Pending students', 'question' => 'How should we handle the pending student accounts shown here?'];
                            }

                            if (($pendingRequirements ?? 0) > 0) {
                                $dashboardPromptSuggestions[] = ['label' => 'Pending files', 'question' => 'What should we do about the pending requirement files?'];
                            }

                            if (($expiredMoaCount ?? 0) > 0) {
                                $dashboardPromptSuggestions[] = ['label' => 'MOA renewals', 'question' => 'What should be done about the expired MOA records?'];
                            }
                        @endphp

                        <div style="margin-top:18px; border-top:1px solid #f0f0f0; padding-top:16px;">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                                <div>
                                    <div style="font-size:13px; font-weight:800; color:#1f2937;">Ask AI about this dashboard</div>
                                    <div style="font-size:12px; color:#888; margin-top:2px;">Click a suggested prompt to ask instantly, or type your own question and press Ask.</div>
                                </div>
                                <div style="display:grid; gap:7px;">
                                    <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase; letter-spacing:.6px;">Suggested prompts</div>
                                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                        @foreach($dashboardPromptSuggestions as $suggestion)
                                            <button type="button" class="dashboard-ai-quick-question" data-question="{{ $suggestion['question'] }}" style="display:inline-flex; align-items:center; gap:7px; border:1.5px solid #fecaca; background:#fff; color:#991b1b; border-radius:9px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(220,38,38,0.08);"><i class="fa fa-bolt" style="width:18px; height:18px; border-radius:6px; background:#fee2e2; display:inline-flex; align-items:center; justify-content:center; font-size:10px; color:#dc2626;"></i>{{ $suggestion['label'] }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:10px; align-items:start;">
                                <textarea id="dashboardAiQuestionInput" rows="3" placeholder="Ask about pending approvals, files, placements, MOAs, or next actions..." style="width:100%; min-height:82px; border:1px solid #e5e7eb; border-radius:12px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; resize:vertical;"></textarea>
                                <button type="button" id="dashboardAskAiBtn" style="height:44px; border:none; border-radius:12px; padding:0 18px; background:#dc2626; color:#fff; font-family:'Poppins',sans-serif; font-size:13px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px;"><i class="fa fa-paper-plane"></i> Ask</button>
                            </div>
                            <div id="dashboardAiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#777;"></div>
                            <div id="dashboardAiAnswer" style="display:none; margin-top:12px; background:#fff7f7; border:1px solid #fecaca; border-radius:12px; padding:14px;">
                                <div style="font-size:12px; font-weight:800; color:#b91c1c; margin-bottom:8px;">AI Answer</div>
                                <p id="dashboardAiAnswerText" style="margin:0; color:#374151; line-height:1.7; font-size:13px;"></p>
                                <div id="dashboardAiNextStepsWrap" style="display:none; margin-top:10px;">
                                    <div style="font-size:12px; font-weight:800; color:#b91c1c; margin-bottom:6px;">Next Steps</div>
                                    <ul id="dashboardAiNextSteps" style="margin:0; padding-left:18px; color:#374151; line-height:1.6; font-size:13px;"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">

                <!-- ===== LEFT: Create Announcement ===== -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon">
                            <i class="fa fa-bullhorn"></i>
                        </div>
                        <div>
                            <h2>Create Announcement</h2>
                            <p>Broadcast a message to all students and professors</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ url('/announcements') }}">
                        @csrf
                        <input type="hidden" name="audience" value="all_students">
                        <div class="panel-card-body">

                            <div class="field-group">
                                <label class="field-label" for="title">
                                    <i class="fa fa-heading"></i> Announcement Title
                                </label>
                                <input class="field-input" type="text" id="title" name="title"
                                    placeholder="e.g. OJT Orientation Schedule"
                                    required>
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="content">
                                    <i class="fa fa-align-left"></i> Content
                                </label>
                                <textarea class="field-input" id="content" name="content"
                                        rows="5"
                                        placeholder="Write your announcement message here..."
                                        required></textarea>
                            </div>

                        </div>

                        <div class="panel-card-footer">
                            <button type="submit" class="btn-submit">
                                <i class="fa fa-paper-plane"></i> Post Announcement
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ===== RIGHT: Quick Links ===== -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <div>
                            <h2>Quick Links</h2>
                            <p>Jump to any section of the portal</p>
                        </div>
                    </div>

                    <div class="quick-links-grid">
                        <a href="{{ url('/studentLists') }}" class="quick-link-item">
                            <div class="quick-link-icon red">
                                <i class="fa fa-users"></i>
                            </div>
                            <span class="quick-link-label">Students</span>
                        </a>
                        <a href="{{ url('/professorTab') }}" class="quick-link-item">
                            <div class="quick-link-icon blue">
                                <i class="fa fa-chalkboard-teacher"></i>
                            </div>
                            <span class="quick-link-label">Professors</span>
                        </a>
                        <a href="{{ url('/uploadpage') }}" class="quick-link-item">
                            <div class="quick-link-icon green">
                                <i class="fa fa-file-upload"></i>
                            </div>
                            <span class="quick-link-label">Upload Templates</span>
                        </a>
                        <a href="{{ url('/MOA') }}" class="quick-link-item">
                            <div class="quick-link-icon purple">
                                <i class="fa fa-file-contract"></i>
                            </div>
                            <span class="quick-link-label">MOA</span>
                        </a>
                        <a href="{{ url('/maintenance') }}" class="quick-link-item">
                            <div class="quick-link-icon amber">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <span class="quick-link-label">Maintenance</span>
                        </a>
                        <a href="{{ url('/reports') }}" class="quick-link-item">
                            <div class="quick-link-icon teal">
                                <i class="fa fa-chart-bar"></i>
                            </div>
                            <span class="quick-link-label">Reports</span>
                        </a>
                        <a href="{{ url('/analytics') }}" class="quick-link-item">
                            <div class="quick-link-icon blue">
                                <i class="fa fa-chart-line"></i>
                            </div>
                            <span class="quick-link-label">Analytics</span>
                        </a>
                    </div>
                </div>

            </div>

            <div class="panel-card" style="margin-top:22px;">
                <div class="panel-card-header">
                    <div class="panel-header-icon">
                        <i class="fa fa-list"></i>
                    </div>
                    <div>
                        <h2>My Announcements</h2>
                        <p>Review or delete announcements you posted</p>
                    </div>
                    <div class="announcement-toolbar">
                        <select id="coordinatorAnnouncementSort" class="announcement-sort-select" aria-label="Sort announcements by date">
                            <option value="desc" selected>Newest first</option>
                            <option value="asc">Oldest first</option>
                        </select>
                    </div>
                </div>
                <div class="panel-card-body announcement-table-wrap" style="padding:0;">
                    <table id="coordinatorAnnouncementTable" class="announcement-manage-table">
                        <colgroup>
                            <col style="width:42%;">
                            <col style="width:18%;">
                            <col style="width:24%;">
                            <col style="width:16%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Audience</th>
                                <th>Posted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($announcements ?? []) as $announcement)
                                <tr>
                                    <td class="announcement-title-cell">
                                        <strong>{{ $announcement->title }}</strong>
                                        <span>{{ Str::limit($announcement->content, 90) }}</span>
                                    </td>
                                    <td>All students</td>
                                    <td data-order="{{ \Carbon\Carbon::parse($announcement->created_at)->timestamp }}">
                                        {{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td>
                                        <button type="button"
                                                class="btn-edit-announcement"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editAnnouncementModal"
                                                data-announcement-id="{{ $announcement->id }}"
                                                data-announcement-title="{{ e($announcement->title) }}"
                                                data-announcement-content="{{ e($announcement->content) }}"
                                                data-announcement-action="{{ route('announcements.update', $announcement->id) }}">
                                            <i class="fa fa-pen"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('announcements.destroy', $announcement->id) }}" class="delete-announcement-form" data-announcement-title="{{ e($announcement->title) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-announcement">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-pen"></i> Edit Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editAnnouncementForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <label class="field-label">
                                <i class="fa fa-heading"></i> Announcement Title
                            </label>
                            <input class="field-input" type="text" name="title" id="editAnnouncementTitle" required>

                            <label class="field-label">
                                <i class="fa fa-align-left"></i> Content
                            </label>
                            <textarea class="field-input" name="content" id="editAnnouncementContent" rows="6" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn-submit">
                                <i class="fa fa-save"></i> Update Announcement
                            </button>
                        </div>
                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        $(document).ready(function () {
            const coordinatorAnnouncementTable = $('#coordinatorAnnouncementTable').DataTable({
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                order: [[2, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 3 }
                ],
                language: {
                    emptyTable: 'No announcements posted yet.'
                }
            });

            $('#coordinatorAnnouncementSort').on('change', function () {
                coordinatorAnnouncementTable.order([2, this.value]).draw();
            });
        });

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

        document.querySelectorAll('.delete-announcement-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const title = form.dataset.announcementTitle || 'this announcement';
                const proceed = function () { form.submit(); };

                if (typeof Swal === 'undefined') {
                    if (window.confirm('Delete "' + title + '"? This cannot be undone.')) {
                        proceed();
                    }
                    return;
                }

                Swal.fire({
                    title: 'Delete announcement?',
                    html: 'This will permanently delete <strong>' + title + '</strong>.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceed();
                    }
                });
            });
        });

        /* ══════════════════════════════════════════════
           DATE & TIME MODAL
        ══════════════════════════════════════════════ */

        const dateEl = document.getElementById('currentDate');
        if (dateEl) {
            dateEl.textContent = new Date().toLocaleDateString('en-US', {
                weekday: 'short', year: 'numeric',
                month: 'long', day: 'numeric'
            });
        }

        const dtOverlay  = document.getElementById('dtOverlay') || createDTModal();
        const dtCloseBtn = document.getElementById('dtCloseBtn');
        const dateBadge  = document.getElementById('dateBadge');

        function createDTModal() {
            const html = `
            <div class="dt-overlay" id="dtOverlay">
                <div class="dt-modal" id="dtModal">
                    <div class="dt-modal-header">
                        <div class="dt-header-top">
                            <span class="dt-header-title"><i class="fa fa-clock" style="margin-right:6px;"></i>Date & Time</span>
                            <button class="dt-close-btn" id="dtCloseBtn"><i class="fa fa-times"></i></button>
                        </div>
                        <div class="dt-clock-display">
                            <div class="dt-time-big">
                                <span id="dtHours">00</span>
                                <span class="colon">:</span>
                                <span id="dtMinutes">00</span>
                                <span class="colon">:</span>
                                <span id="dtSeconds">00</span>
                                <span class="dt-time-ampm" id="dtAmPm">AM</span>
                            </div>
                            <div class="dt-date-sub" id="dtDateSub"></div>
                        </div>
                    </div>
                    <div class="dt-analog-wrap">
                        <div class="analog-clock" id="analogClock">
                            <div class="clock-center"></div>
                            <div class="hand hour-hand" id="hourHand"></div>
                            <div class="hand minute-hand" id="minuteHand"></div>
                            <div class="hand second-hand" id="secondHand"></div>
                        </div>
                    </div>
                    <div class="dt-calendar">
                        <div class="cal-nav">
                            <button class="cal-nav-btn" id="calPrev"><i class="fa fa-chevron-left"></i></button>
                            <span class="cal-month-label" id="calMonthLabel"></span>
                            <button class="cal-nav-btn" id="calNext"><i class="fa fa-chevron-right"></i></button>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                    </div>
                </div>
            </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
            return document.getElementById('dtOverlay');
        }

        /* Open / Close */
        if (dateBadge) {
            dateBadge.addEventListener('click', function () {
                dtOverlay.classList.add('open');
                startClock();
                renderCalendar(calViewYear, calViewMonth);
            });
        }

        function closeModal() {
            dtOverlay.classList.remove('open');
            stopClock();
        }

        if (dtCloseBtn) {
            dtCloseBtn.addEventListener('click', closeModal);
        }
        dtOverlay.addEventListener('click', function (e) {
            if (e.target === dtOverlay) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        /* ── Digital Clock ── */
        let clockRAF = null;

        function startClock() {
            function tick() {
                const now  = new Date();
                let   h    = now.getHours();
                const m    = now.getMinutes();
                const s    = now.getSeconds();
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;

                document.getElementById('dtHours').textContent   = String(h).padStart(2,'0');
                document.getElementById('dtMinutes').textContent = String(m).padStart(2,'0');
                document.getElementById('dtSeconds').textContent = String(s).padStart(2,'0');
                document.getElementById('dtAmPm').textContent    = ampm;
                document.getElementById('dtDateSub').textContent =
                    now.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

                /* ── Analog hands ── */
                const secDeg  = s * 6;
                const minDeg  = m * 6 + s * 0.1;
                const hourDeg = (h % 12) * 30 + m * 0.5;

                document.getElementById('secondHand').style.transform = `rotate(${secDeg}deg)`;
                document.getElementById('minuteHand').style.transform = `rotate(${minDeg}deg)`;
                document.getElementById('hourHand').style.transform   = `rotate(${hourDeg}deg)`;

                clockRAF = requestAnimationFrame(tick);
            }
            tick();
        }

        function stopClock() {
            if (clockRAF) { cancelAnimationFrame(clockRAF); clockRAF = null; }
        }

        /* ── Build hour tick marks ── */
        (function buildMarks() {
            const clock = document.getElementById('analogClock');
            if (!clock) return;
            for (let i = 0; i < 12; i++) {
                const mark = document.createElement('div');
                mark.className = 'clock-mark';
                const angle  = i * 30;
                mark.style.cssText = `
                    position: absolute;
                    width:  ${i % 3 === 0 ? 2.5 : 1.5}px;
                    height: ${i % 3 === 0 ? 8 : 5}px;
                    background: currentColor;
                    border-radius: 2px;
                    top: 4px;
                    left: calc(50% - ${i % 3 === 0 ? 1.25 : 0.75}px);
                    transform-origin: center 53px;
                    transform: rotate(${angle}deg);
                `;
                clock.appendChild(mark);
            }
        })();

        /* ── Calendar ── */
        const MONTHS = ['January','February','March','April','May','June',
                        'July','August','September','October','November','December'];
        const DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

        const today       = new Date();
        let calViewYear   = today.getFullYear();
        let calViewMonth  = today.getMonth();
        let selectedDay   = today.getDate();

        const calPrev = document.getElementById('calPrev');
        const calNext = document.getElementById('calNext');

        if (calPrev) {
            calPrev.addEventListener('click', function () {
                calViewMonth--;
                if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
                renderCalendar(calViewYear, calViewMonth);
            });
        }

        if (calNext) {
            calNext.addEventListener('click', function () {
                calViewMonth++;
                if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
                renderCalendar(calViewYear, calViewMonth);
            });
        }

        function renderCalendar(year, month) {
            document.getElementById('calMonthLabel').textContent = `${MONTHS[month]} ${year}`;

            const grid      = document.getElementById('calGrid');
            grid.innerHTML  = '';

            /* Day-name headers */
            DAYS.forEach(d => {
                const el = document.createElement('div');
                el.className   = 'cal-day-name';
                el.textContent = d;
                grid.appendChild(el);
            });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            /* Empty leading cells */
            for (let i = 0; i < firstDay; i++) {
                const el = document.createElement('div');
                el.className = 'cal-day empty';
                grid.appendChild(el);
            }

            /* Day cells */
            for (let d = 1; d <= daysInMonth; d++) {
                const el = document.createElement('div');
                el.className   = 'cal-day';
                el.textContent = d;

                const isToday  = d === today.getDate() &&
                                 month === today.getMonth() &&
                                 year  === today.getFullYear();
                const isSel    = d === selectedDay &&
                                 month === calViewMonth &&
                                 year  === calViewYear;

                if (isToday) el.classList.add('today');
                else if (isSel) el.classList.add('selected');

                el.addEventListener('click', function () {
                    selectedDay  = d;
                    calViewYear  = year;
                    calViewMonth = month;
                    renderCalendar(year, month);
                });

                grid.appendChild(el);
            }
        }

        window.dashboardAiContext = {
            report_type: 'coordinator_dashboard',
            metrics: {
                total_records: @json($roleCount ?? 0),
                professors: @json($roleCountP ?? 0),
                uploaded_templates: @json($fileCount ?? 0),
                announcements: @json(isset($announcements) ? $announcements->count() : 0),
                approved_students: @json($approvedStudents ?? 0),
                pending_students: @json($pendingStudents ?? 0),
                pending_files: @json($pendingRequirements ?? 0),
                total_companies: @json($partnerCompanies ?? 0),
                records_with_ojt: @json($placedStudents ?? 0),
                missing_ojt: @json($unplacedStudents ?? 0),
                expired_moa: @json($expiredMoaCount ?? 0)
            },
            insight: @json($dashboardInsights ?? null)
        };

        const dashboardAiContext = window.dashboardAiContext;

        function renderDashboardAiAnswer(data) {
            const answerBox = document.getElementById('dashboardAiAnswer');
            const answerText = document.getElementById('dashboardAiAnswerText');
            const nextStepsWrap = document.getElementById('dashboardAiNextStepsWrap');
            const nextStepsList = document.getElementById('dashboardAiNextSteps');

            if (!answerBox || !answerText) return;

            answerText.textContent = data.answer || 'No answer was returned.';
            answerBox.style.display = 'block';

            if (nextStepsList) nextStepsList.innerHTML = '';
            if (Array.isArray(data.next_steps) && data.next_steps.length && nextStepsWrap && nextStepsList) {
                data.next_steps.forEach(function (step) {
                    const li = document.createElement('li');
                    li.textContent = step;
                    nextStepsList.appendChild(li);
                });
                nextStepsWrap.style.display = 'block';
            } else if (nextStepsWrap) {
                nextStepsWrap.style.display = 'none';
            }
        }

        function askDashboardAi(question) {
            const status = document.getElementById('dashboardAiAskStatus');
            const button = document.getElementById('dashboardAskAiBtn');

            if (!question.trim()) {
                if (status) {
                    status.textContent = 'Type a question first.';
                    status.style.display = 'block';
                }
                return;
            }

            if (button) button.disabled = true;
            if (status) {
                status.textContent = 'Asking AI...';
                status.style.display = 'block';
            }

            fetch(@json(route('reports.ai.ask')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify({
                    question: question,
                    report_type: dashboardAiContext.report_type,
                    metrics: dashboardAiContext.metrics,
                    insight: dashboardAiContext.insight
                })
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('AI request failed.');
                    return response.json();
                })
                .then(function (data) {
                    renderDashboardAiAnswer(data);
                    if (status) {
                        status.textContent = data.source === 'fallback'
                            ? ((data.availability && data.availability.message) ? data.availability.message + ' Internal answer shown.' : 'Gemini is unavailable or rate-limited. Internal answer shown; try again in a few minutes, or later if daily quota was reached.')
                            : 'Answer generated.';
                    }
                })
                .catch(function () {
                    if (status) {
                        status.textContent = 'AI could not answer right now. Please try again later.';
                        status.style.display = 'block';
                    }
                })
                .finally(function () {
                    if (button) button.disabled = false;
                });
        }

        document.querySelectorAll('.dashboard-ai-quick-question').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = document.getElementById('dashboardAiQuestionInput');
                const question = btn.getAttribute('data-question') || '';
                if (input) input.value = question;
                askDashboardAi(question);
            });
        });

        const dashboardAskAiBtn = document.getElementById('dashboardAskAiBtn');
        if (dashboardAskAiBtn) {
            dashboardAskAiBtn.addEventListener('click', function () {
                const input = document.getElementById('dashboardAiQuestionInput');
                askDashboardAi(input ? input.value : '');
            });
        }

        $('.btn-edit-announcement').on('click', function () {
            $('#editAnnouncementForm').attr('action', $(this).data('announcement-action'));
            $('#editAnnouncementTitle').val($(this).data('announcement-title'));
            $('#editAnnouncementContent').val($(this).data('announcement-content'));
        });
    </script>
    <script src="{{ asset('js/ai-insight-controls.js') }}"></script>
    <script src="{{ url('/assets/js/dark-mode.js') }}"></script>
    <script src="{{ asset('assets/js/voice-input.js') }}"></script>
    <script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
    </html>
