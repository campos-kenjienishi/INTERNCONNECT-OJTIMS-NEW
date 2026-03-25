<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Student Dashboard</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red:        #dc2626;
            --red-dark:   #991b1b;
            --red-deeper: #7f0000;
            --sidebar-w:  260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h:   64px;
            --bg:           #f5f5f5;
            --surface:      #fff;
            --surface-2:    #fafafa;
            --border:       #f0f0f0;
            --border-2:     rgba(0,0,0,0.04);
            --text-primary: #1a1a1a;
            --text-secondary: #888;
            --text-muted:   #aaa;
            --topbar-bg:    #fff;
            --input-border: #e5e5e5;
            --row-hover:    #fff5f5;
            --toggle-bg:    #f5f5f5;
            --toggle-color: #333;
            --footer-bg:    #fff;
        }

        body.dark-mode {
            --bg:           #0a0a0a;
            --surface:      #1a1a1a;
            --surface-2:    #252525;
            --surface-3:    #303030;
            --border:       #2a2a2a;
            --border-2:     rgba(255,255,255,0.05);
            --text-primary: #e8e8e8;
            --text-secondary: #a0a0a0;
            --text-muted:   #707070;
            --topbar-bg:    #1a1a1a;
            --input-border: #3a3a3a;
            --row-hover:    rgba(220,38,38,0.1);
            --toggle-bg:    #2a2a2a;
            --toggle-color: #e8e8e8;
            --footer-bg:    #1a1a1a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
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

        .sidebar-brand-text { display: flex; flex-direction: column; white-space: nowrap; overflow: hidden; transition: opacity 0.25s, width 0.25s; }
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
            background: rgba(239,68,68,0.25); border: 1.5px solid rgba(239,68,68,0.4);
            display: flex; align-items: center; justify-content: center;
            color: #fca5a5; font-size: 16px; flex-shrink: 0;
        }

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
            height: var(--topbar-h); background: var(--topbar-bg);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid var(--border);
            transition: background 0.3s, border-color 0.3s;
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .menu-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--toggle-bg); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--toggle-color); font-size: 18px; transition: all 0.2s;
        }

        .menu-toggle:hover { background: #fee2e2; color: var(--red); }
        .topbar-title { font-size: 13.5px; font-weight: 500; color: var(--text-secondary); }
        .topbar-title span { color: var(--red); font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red);
            transition: all 0.3s ease;
        }

        body.dark-mode .topbar-badge {
            background: rgba(220,38,38,0.15);
            border-color: rgba(220,38,38,0.3);
            color: #ff6b6b;
        }

        .darkmode-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--toggle-bg); border: 1px solid var(--border);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--toggle-color); font-size: 16px; transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1); flex-shrink: 0;
            transform-origin: center;
        }

        .darkmode-toggle:hover {
            background: #fee2e2; color: var(--red); border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        .darkmode-toggle:active {
            transform: scale(0.95);
        }

        body.dark-mode .darkmode-toggle {
            background: var(--toggle-bg);
            border-color: var(--border);
            color: var(--toggle-color);
        }

        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2);
            color: #ff6b6b;
            border-color: rgba(220,38,38,0.3);
            box-shadow: 0 6px 16px rgba(220,38,38,0.3);
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }

        .page-header {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 24px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }

        /* ── CLICKABLE DATE BADGE ── */
        .date-badge {
            font-size: 12.5px; color: var(--text-secondary);
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 8px; padding: 6px 14px;
            cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 7px;
            user-select: none;
        }

        .date-badge:hover {
            background: #fee2e2; border-color: #fecaca;
            color: var(--red); transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220,38,38,0.12);
        }

        .date-badge i { font-size: 11px; }
        .date-badge .pulse-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #16a34a;
            animation: pulse-green 1.8s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes pulse-green {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.5); opacity: 0.5; }
        }

        /* =============== WELCOME BANNER =============== */
        .welcome-banner {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
            border-radius: 16px; padding: 28px 32px; margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between;
            position: relative; overflow: hidden;
            box-shadow: 0 8px 28px rgba(185,28,28,0.25);
        }

        .welcome-banner::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .welcome-banner::after {
            content: ''; position: absolute; bottom: -40px; right: 80px;
            width: 140px; height: 140px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .welcome-text { position: relative; z-index: 1; }
        .welcome-text h2 { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 6px; letter-spacing: -0.3px; }
        .welcome-text p  { font-size: 13.5px; color: rgba(255,255,255,0.7); line-height: 1.5; }
        .welcome-icon    { font-size: 64px; opacity: 0.2; position: relative; z-index: 1; }

        /* =============== STATS =============== */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px; margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface); border-radius: 16px; padding: 22px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid var(--border-2);
            transition: transform 0.25s, box-shadow 0.25s, background 0.3s;
            text-decoration: none;
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,0.1); }

        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.red   { background: #fee2e2; color: var(--red); }
        .stat-icon.blue  { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber { background: #fef9c3; color: #ca8a04; }

        body.dark-mode .stat-icon.red   { background: rgba(220,38,38,0.2); color: #ff6b6b; }
        body.dark-mode .stat-icon.blue  { background: rgba(37,99,235,0.2); color: #4f96ff; }
        body.dark-mode .stat-icon.green { background: rgba(22,163,74,0.2); color: #22c55e; }
        body.dark-mode .stat-icon.amber { background: rgba(202,138,4,0.2); color: #facc15; }

        body.dark-mode .qa-icon.red { background: rgba(220,38,38,0.2); color: #ff6b6b; }
        body.dark-mode .qa-icon.blue { background: rgba(37,99,235,0.2); color: #4f96ff; }
        body.dark-mode .qa-icon.green { background: rgba(22,163,74,0.2); color: #22c55e; }
        body.dark-mode .qa-icon.amber { background: rgba(202,138,4,0.2); color: #facc15; }

        .stat-info .stat-num  { font-size: 26px; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .stat-info .stat-name { font-size: 12.5px; color: var(--text-secondary); margin-top: 4px; }

        /* =============== TABLE CARD =============== */
        .content-grid { display: grid; grid-template-columns: 1fr; gap: 22px; }

        .table-card {
            background: var(--surface); border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid var(--border-2);
            overflow: hidden; transition: background 0.3s;
        }

        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px; border-bottom: 1px solid var(--border);
            transition: border-color 0.3s;
        }

        .table-card-header h2 { font-size: 16px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }

        .table-card-header h2 .header-icon {
            width: 32px; height: 32px; border-radius: 8px; background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
            color: var(--red); font-size: 14px;
        }

        body.dark-mode .table-card-header h2 .header-icon { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        .table-card-body { padding: 0; overflow-x: auto; }

        .table-card-body .dataTables_wrapper { padding: 16px 20px; font-family: 'Poppins', sans-serif; font-size: 13.5px; color: var(--text-primary); }
        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }

        .table-card-body table.dataTable thead th { background: var(--surface-2); color: var(--text-secondary); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 14px; border-bottom: 1px solid var(--border); border-top: none; transition: background 0.3s; }
        .table-card-body table.dataTable tbody td { padding: 12px 14px; color: var(--text-primary); border-bottom: 1px solid var(--border); font-size: 13.5px; transition: color 0.3s; }
        .table-card-body table.dataTable tbody tr:hover td { background: var(--row-hover); }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }

        .dataTables_filter input { border: 1px solid var(--input-border) !important; border-radius: 8px !important; padding: 6px 12px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; outline: none !important; transition: border-color 0.2s !important; background: var(--surface) !important; color: var(--text-primary) !important; }
        .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important; }
        .dataTables_length select { border: 1px solid var(--input-border) !important; border-radius: 8px !important; padding: 4px 8px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; background: var(--surface) !important; color: var(--text-primary) !important; }
        .dataTables_info { color: var(--text-secondary) !important; }
        .dataTables_paginate .paginate_button { border-radius: 6px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; padding: 4px 10px !important; color: var(--text-primary) !important; }
        .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        .dataTables_paginate .paginate_button:hover { background: #fee2e2 !important; border-color: #fecaca !important; color: var(--red) !important; }

        /* =============== FOOTER =============== */
        .dashboard-footer {
            background: var(--footer-bg); border-top: 1px solid var(--border);
            color: var(--text-secondary); text-align: center; padding: 18px 28px;
            font-size: 12.5px; margin-top: auto;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px; transition: background 0.3s, border-color 0.3s;
        }

        .dashboard-footer .footer-left { display: flex; align-items: center; gap: 8px; }
        .dashboard-footer .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .dashboard-footer .footer-copy { font-size: 12.5px; color: var(--text-muted); font-weight: 500; }
        .dashboard-footer .footer-copy span { color: var(--red); font-weight: 600; }
        .dashboard-footer .footer-links { display: flex; align-items: center; gap: 6px; }
        .dashboard-footer a { color: var(--text-secondary); text-decoration: none; font-weight: 500; font-size: 12.5px; transition: color 0.2s; }
        .dashboard-footer a:hover { color: var(--red); }
        .dashboard-footer .divider { color: var(--border); margin: 0 2px; }

        /* =============================================
           DATE & TIME MODAL
        ============================================= */
        .dt-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .dt-overlay.open {
            opacity: 1; pointer-events: all;
        }

        .dt-modal {
            background: var(--surface);
            border-radius: 22px;
            width: 360px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.05);
            overflow: hidden;
            transform: scale(0.88) translateY(20px);
            transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
            opacity: 0;
        }

        .dt-overlay.open .dt-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Modal gradient header */
        .dt-modal-header {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 45%, #dc2626 100%);
            padding: 20px 22px 16px;
            position: relative; overflow: hidden;
        }

        .dt-modal-header::before {
            content: ''; position: absolute; top: -40px; right: -40px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,0.06); pointer-events: none;
        }

        .dt-modal-header::after {
            content: ''; position: absolute; bottom: -30px; left: 20px;
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(255,255,255,0.04); pointer-events: none;
        }

        .dt-header-top {
            display: flex; align-items: center; justify-content: space-between;
            position: relative; z-index: 1;
        }

        .dt-header-title {
            font-size: 13px; font-weight: 600;
            color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px;
        }

        .dt-close-btn {
            width: 28px; height: 28px; border-radius: 8px;
            background: rgba(255,255,255,0.15); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 13px; transition: background 0.2s;
        }

        .dt-close-btn:hover { background: rgba(255,255,255,0.25); }

        /* Live clock inside modal header */
        .dt-clock-display {
            margin-top: 10px; position: relative; z-index: 1;
        }

        .dt-time-big {
            font-size: 42px; font-weight: 800; color: #fff;
            letter-spacing: -1px; line-height: 1;
            display: flex; align-items: center; gap: 4px;
        }

        .dt-time-big .colon {
            animation: blink-colon 1s step-end infinite;
            display: inline-block; margin: 0 1px;
        }

        @keyframes blink-colon {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.15; }
        }

        .dt-time-ampm {
            font-size: 14px; font-weight: 700;
            color: rgba(255,255,255,0.7); margin-left: 6px;
            align-self: flex-end; margin-bottom: 6px;
        }

        .dt-date-sub {
            font-size: 12.5px; color: rgba(255,255,255,0.65);
            margin-top: 4px; font-weight: 500; letter-spacing: 0.3px;
        }

        /* Analog clock */
        .dt-analog-wrap {
            display: flex; justify-content: center;
            padding: 16px 0 8px;
        }

        .analog-clock {
            width: 110px; height: 110px; border-radius: 50%;
            background: var(--surface-2);
            border: 3px solid var(--border);
            position: relative;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.08), 0 4px 18px rgba(0,0,0,0.1);
        }

        body.dark-mode .analog-clock {
            border-color: #333;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.3), 0 4px 18px rgba(0,0,0,0.3);
        }

        /* Hour markers */
        .clock-mark {
            position: absolute; width: 2px; border-radius: 2px;
            background: var(--text-muted);
            left: 50%; transform-origin: bottom center;
        }

        /* Center dot */
        .clock-center {
            position: absolute; width: 10px; height: 10px; border-radius: 50%;
            background: var(--red);
            top: 50%; left: 50%; transform: translate(-50%, -50%);
            z-index: 10;
            box-shadow: 0 0 0 2px var(--surface);
        }

        /* Hands */
        .hand {
            position: absolute; bottom: 50%; left: 50%;
            transform-origin: bottom center;
            border-radius: 4px 4px 0 0;
        }

        .hour-hand   { width: 3.5px; height: 28px; background: var(--text-primary); margin-left: -1.75px; }
        .minute-hand { width: 2.5px; height: 36px; background: var(--text-primary); margin-left: -1.25px; }
        .second-hand { width: 1.5px; height: 40px; background: var(--red); margin-left: -0.75px; }

        /* Calendar */
        .dt-calendar { padding: 0 18px 18px; }

        .cal-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 2px;
        }

        .cal-nav-btn {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--surface-2); border: 1px solid var(--border);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); font-size: 12px; transition: all 0.2s;
        }

        .cal-nav-btn:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .cal-month-label {
            font-size: 14px; font-weight: 700; color: var(--text-primary);
        }

        .cal-grid {
            display: grid; grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .cal-day-name {
            text-align: center; font-size: 10px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            padding: 4px 0 6px;
        }

        .cal-day {
            text-align: center; font-size: 12.5px; font-weight: 500;
            color: var(--text-primary); padding: 7px 4px;
            border-radius: 8px; cursor: pointer; transition: all 0.15s;
            line-height: 1;
        }

        .cal-day:hover:not(.empty):not(.today) { background: var(--surface-2); color: var(--red); }
        .cal-day.empty  { cursor: default; color: transparent; }
        .cal-day.other-month { color: var(--text-muted); }

        .cal-day.today {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff !important; font-weight: 700;
            box-shadow: 0 3px 10px rgba(220,38,38,0.35);
        }

        .cal-day.selected:not(.today) {
            background: #fee2e2; color: var(--red); font-weight: 700;
        }

        /* Sidebar overlay */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
        }

        @media (max-width: 900px) {
            .sidebar { width: var(--sidebar-w); transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .hero-section { flex-direction: column; gap: 24px; }
            .hero-left { width: 100%; }
            .hero-stats { width: 100%; grid-template-columns: repeat(2, 1fr); }
            .quick-actions-grid { grid-template-columns: repeat(2, 1fr); }
            .stats-grid { grid-template-columns: 1fr; }
            .dt-modal { width: 92vw; }
            .topbar { left: 0; }
        }

        /* ─────────────────────────────────── 
           UNIVERSAL DARK MODE ENHANCEMENTS
        ─────────────────────────────────── */
        
        /* Dark mode hero section */
        body.dark-mode .hero-section {
            box-shadow: 0 16px 48px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05) inset;
        }

        body.dark-mode .hero-left h2 { color: #fff; }
        body.dark-mode .hero-left p { color: rgba(255,255,255,0.9); }
        body.dark-mode .hero-stat-item {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.1);
        }

        body.dark-mode .hero-stat-icon { background: rgba(255,255,255,0.12); }
        body.dark-mode .hero-stat-num { color: #fff; }
        body.dark-mode .hero-stat-label { color: rgba(255,255,255,0.8); }

        /* Dark mode quick actions */
        body.dark-mode .quick-action-card {
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        body.dark-mode .quick-action-card::before {
            background: rgba(220,38,38,0.1);
        }

        body.dark-mode .qa-content .qa-title { color: var(--text-primary); }
        body.dark-mode .qa-content .qa-desc { color: var(--text-secondary); }
        body.dark-mode .quick-action-card:hover .qa-arrow { color: var(--red); }

        /* Dark mode stats cards */
        body.dark-mode .stat-card {
            box-shadow: 0 4px 20px rgba(0,0,0,0.3), 0 0 0 1px rgba(220,38,38,0.08) inset;
        }

        body.dark-mode .stat-card::before {
            background: rgba(220,38,38,0.08);
        }

        body.dark-mode .stat-info .stat-num { color: var(--text-primary); }
        body.dark-mode .stat-info .stat-name { color: var(--text-primary); }
        body.dark-mode .stat-info .stat-desc { color: var(--text-secondary); }

        /* Dark mode table */
        body.dark-mode .table-card {
            box-shadow: 0 4px 20px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05) inset;
        }

        body.dark-mode .table-card-body .dataTables_wrapper { color: var(--text-primary); }
        body.dark-mode .table-card-body table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.08); }

        /* Dark mode dataTables pagination */
        body.dark-mode .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #fee2e2 !important;
            color: var(--red) !important;
        }

        /* Dark mode date badge */
        body.dark-mode .date-badge {
            background: linear-gradient(135deg, var(--surface), var(--surface-2));
            border-color: var(--border);
            color: var(--text-primary);
        }

        body.dark-mode .date-badge:hover {
            background: linear-gradient(135deg, rgba(220,38,38,0.15), rgba(220,38,38,0.1));
            border-color: rgba(220,38,38,0.3);
        }

        /* Dark mode modal */
        body.dark-mode .dt-modal {
            box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.05);
        }

        body.dark-mode .cal-nav-btn {
            background: var(--surface-3);
            border-color: var(--border);
        }

        /* Dark mode footer */
        body.dark-mode .dashboard-footer {
            background: #121212;
            border-top-color: #2a2a2a;
        }

        /* Dark mode topbar */
        body.dark-mode .topbar {
            border-bottom-color: var(--border);
        }

        body.dark-mode .topbar-badge {
            background: var(--surface-2);
            color: var(--text-primary);
        }

        /* Dark mode page header */
        body.dark-mode .page-header {
            border-bottom-color: var(--border);
        }

        body.dark-mode .section-title { color: var(--text-primary); }

        /* Dark mode buttons and inputs */
        body.dark-mode .dataTables_filter input {
            border-color: var(--input-border) !important;
            background: var(--surface) !important;
            color: var(--text-primary) !important;
        }

        body.dark-mode .dataTables_filter input::placeholder {
            color: var(--text-muted) !important;
        }

        body.dark-mode .dataTables_length select {
            border-color: var(--input-border) !important;
            background: var(--surface) !important;
            color: var(--text-primary) !important;
        }

        /* Dark mode page content and general elements */
        body.dark-mode {
            scrollbar-color: var(--toggle-bg) transparent;
        }

        body.dark-mode ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        body.dark-mode ::-webkit-scrollbar-track {
            background: transparent;
        }

        body.dark-mode ::-webkit-scrollbar-thumb {
            background: var(--toggle-bg);
            border-radius: 4px;
        }

        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: var(--border);
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
            <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item active">
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
            <span class="nav-icon"><i class="fa fa-file-alt"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
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

<!-- =============== MAIN CONTENT =============== -->
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
                <i class="fa fa-graduation-cap"></i> Student Portal
            </div>
        </div>
    </div>

    <div class="page-content">

        <div class="page-header">
            <h1>Home <span>Dashboard</span></h1>
            <!-- Clickable date badge -->
            <div class="date-badge" id="dateBadge" title="Click to view calendar & clock">
                <span class="pulse-dot"></span>
                <i class="fa fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </div>

        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Welcome back, {{ $user->first_name }}! 👋</h2>
                <p>Track your OJT progress, submit requirements, and manage your internship journey all in one place.</p>
            </div>
            <div class="welcome-icon">🎓</div>
        </div>

        <div class="stats-grid">
            <a href="{{ url('/student/files') }}" class="stat-card">
                <div class="stat-icon red"><i class="fa fa-cloud-download-alt"></i></div>
                <div class="stat-info">
                    <div class="stat-num">{{ $fileCount }}</div>
                    <div class="stat-name">Downloadable Templates</div>
                </div>
            </a>
            <a href="{{ url('/student/requirements') }}" class="stat-card">
                <div class="stat-icon green"><i class="fa fa-tasks"></i></div>
                <div class="stat-info">
                    <div class="stat-num">OJT</div>
                    <div class="stat-name">Requirements</div>
                </div>
            </a>
            <a href="{{ url('/student/ojtinfo') }}" class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-info-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-num">Info</div>
                    <div class="stat-name">OJT Information</div>
                </div>
            </a>
        </div>

        <!-- Partner Companies section removed for students -->

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

<!-- =============================================
     DATE & TIME MODAL
============================================= -->
<div class="dt-overlay" id="dtOverlay">
    <div class="dt-modal" id="dtModal">

        <!-- Gradient header with digital clock -->
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

        <!-- Analog clock -->
        <div class="dt-analog-wrap">
            <div class="analog-clock" id="analogClock">
                <!-- Hour markers injected by JS -->
                <div class="clock-center"></div>
                <div class="hand hour-hand"   id="hourHand"></div>
                <div class="hand minute-hand" id="minuteHand"></div>
                <div class="hand second-hand" id="secondHand"></div>
            </div>
        </div>

        <!-- Calendar -->
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

@if(!session()->has('termsAccepted'))
    @include('students.terms_modal')
@endif


<script src="{{ url('/assets/js/main.js') }}"></script>
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

/* ───────────────────────────────────────
   UNIVERSAL DARK MODE IMPLEMENTATION
   ─────────────────────────────────────── */

/* ── Live date in badge ── */
const dateEl = document.getElementById('currentDate');
function updateBadgeDate() {
    const now = new Date();
    dateEl.textContent = now.toLocaleDateString('en-US', {
        weekday: 'short', year: 'numeric', month: 'long', day: 'numeric'
    });
}
updateBadgeDate();
setInterval(updateBadgeDate, 60000);

/* ══════════════════════════════════════════════
   DATE & TIME MODAL
══════════════════════════════════════════════ */

const dtOverlay  = document.getElementById('dtOverlay');
const dtCloseBtn = document.getElementById('dtCloseBtn');
const dateBadge  = document.getElementById('dateBadge');

/* Open / Close */
dateBadge.addEventListener('click', function () {
    dtOverlay.classList.add('open');
    startClock();
    renderCalendar(calViewYear, calViewMonth);
});

function closeModal() {
    dtOverlay.classList.remove('open');
    stopClock();
}

dtCloseBtn.addEventListener('click', closeModal);
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
    const r = 55; // half of 110px
    for (let i = 0; i < 12; i++) {
        const mark = document.createElement('div');
        mark.className = 'clock-mark';
        const angle  = i * 30;
        const isHour = true;
        mark.style.cssText = `
            height: ${i % 3 === 0 ? 8 : 5}px;
            width:  ${i % 3 === 0 ? 2 : 1.5}px;
            top:    ${r - (i % 3 === 0 ? 8 : 5)}px;
            left:   calc(50% - ${i % 3 === 0 ? 1 : 0.75}px);
            transform-origin: bottom center;
            transform: rotate(${angle}deg) translateY(${-(r - (i % 3 === 0 ? 8 : 5))}px) rotate(0deg);
            position: absolute;
        `;
        // Simpler approach: position via rotation
        mark.style.cssText = `
            position: absolute;
            width:  ${i % 3 === 0 ? 2.5 : 1.5}px;
            height: ${i % 3 === 0 ? 8 : 5}px;
            background: var(--text-muted);
            border-radius: 2px;
            top: 4px;
            left: calc(50% - ${i % 3 === 0 ? 1.25 : 0.75}px);
            transform-origin: bottom center;
            transform: rotate(${angle}deg) translateY(0);
        `;
        /* Fix: use translate to push from center */
        mark.style.top        = `${r - r + 4}px`;
        mark.style.left       = `calc(50% - ${i % 3 === 0 ? 1.25 : 0.75}px)`;
        mark.style.transformOrigin = `center ${r - 4}px`;
        mark.style.transform  = `rotate(${angle}deg)`;
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

document.getElementById('calPrev').addEventListener('click', function () {
    calViewMonth--;
    if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
    renderCalendar(calViewYear, calViewMonth);
});

document.getElementById('calNext').addEventListener('click', function () {
    calViewMonth++;
    if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
    renderCalendar(calViewYear, calViewMonth);
});

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
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>

<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>