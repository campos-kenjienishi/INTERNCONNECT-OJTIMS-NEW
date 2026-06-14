<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Professor Dashboard</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/professor_home-responsive.css') }}">

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
            display: flex;
            flex-direction: column;
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
            overflow: hidden;
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

        .topbar-right { display: flex; align-items: center; gap: 10px; }

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
            color: var(--red-dark);
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }

        /* ── CLICKABLE DATE BADGE ── */
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

        /* Welcome banner */
        .welcome-banner {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
            border-radius: 16px;
            padding: 28px 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(185,28,28,0.25);
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -40px; right: 80px;
            width: 140px; height: 140px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        

        .welcome-text { position: relative; z-index: 1; }
        .welcome-text h2 { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 6px; letter-spacing: -0.3px; }
        .welcome-text p  { font-size: 13.5px; color: rgba(255,255,255,0.7); line-height: 1.5; }

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

        .welcome-icon { font-size: 64px; opacity: 0.2; position: relative; z-index: 1; }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.25s, box-shadow 0.25s;
            text-decoration: none;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.red    { background: #fee2e2; color: var(--red); }
        .stat-icon.blue   { background: #dbeafe; color: #2563eb; }
        .stat-icon.green  { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber  { background: #fef9c3; color: #ca8a04; }
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }

        .stat-info .stat-num  { font-size: 26px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-info .stat-name { font-size: 12.5px; color: #888; margin-top: 4px; }

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
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-card-header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-card-header h2 .header-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 14px;
        }

        .company-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fee2e2;
            color: var(--red);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12.5px;
            font-weight: 700;
        }

        .table-card-body { padding: 0; overflow-x: auto; }

        /* DataTables overrides */
        .table-card-body .dataTables_wrapper {
            padding: 16px 20px;
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
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            border-top: none;
        }

        .table-card-body table.dataTable tbody td {
            padding: 12px 14px;
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
            transition: border-color 0.2s !important;
            color: #333 !important; background: #fff !important;
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
            color: #333 !important; background: #fff !important;
        }

        .dataTables_wrapper { color: #333 !important; }
        .dataTables_length { color: #333 !important; background: #f5f5f5; padding: 8px 12px; border-radius: 6px; display: inline-block; margin-bottom: 10px; }
        .dataTables_length label { color: #333 !important; font-weight: 500; }
        .dataTables_filter { color: #333 !important; background: #f5f5f5; padding: 8px 12px; border-radius: 6px; display: inline-block; margin-bottom: 10px; float: right; }
        .dataTables_filter label { color: #333 !important; font-weight: 500; }
        .dataTables_info { color: #555 !important; font-weight: 500 !important; background: #f5f5f5; padding: 8px 12px; border-radius: 6px; display: inline-block; margin-top: 10px; }
        .dataTables_paginate { color: #333 !important; }
        .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            padding: 4px 10px !important;
            color: #333 !important; background: #f5f5f5 !important; border-color: #ddd !important;
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

        /* Company cell */
        .company-cell { display: flex; align-items: center; gap: 10px; }

        .company-icon {
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

        .company-name-text { font-weight: 600; color: #1a1a1a; }

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
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .welcome-banner { flex-direction: column; gap: 12px; }
            .welcome-icon { display: none; }
            .welcome-actions { width: 100%; }
            .welcome-video-btn { width: 100%; justify-content: center; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
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

        /* =============== DARK MODE =============== */
        body.dark-mode { background: #1a1a1a; color: #e0e0e0; }
        body.dark-mode .topbar { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .page-header h1 { color: #fff; }
        body.dark-mode .table-card { background: #2a2a2a; border: 1px solid #3a3a3a; }
        body.dark-mode .table-card-header { background: #3a3a3a; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .table-card-header h2 { color: #fff; }
        body.dark-mode .table-card-header p { color: #999; }
        body.dark-mode .company-count-badge { background: rgba(220,38,38,0.2) !important; color: #ff6b6b !important; }
        body.dark-mode .table-card-body table.dataTable thead th { background: #3a3a3a; color: #e0e0e0; border-bottom: 1px solid #555; }
        body.dark-mode .table-card-body table.dataTable tbody td { color: #e0e0e0; border-bottom: 1px solid #2a2a2a; }
        body.dark-mode .table-card-body table.dataTable tbody tr:hover td { background: rgba(220,38,38,0.1); }
        body.dark-mode .dataTables_wrapper { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_length { background: #3a3a3a !important; color: #e0e0e0 !important; padding: 8px 12px; border-radius: 6px; display: inline-block; }
        body.dark-mode .dataTables_length label { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_length select { background: #2a2a2a !important; color: #e0e0e0 !important; border-color: #555 !important; }
        body.dark-mode .dataTables_filter { background: #3a3a3a !important; color: #e0e0e0 !important; padding: 8px 12px; border-radius: 6px; display: inline-block; float: right; }
        body.dark-mode .dataTables_filter label { color: #e0e0e0 !important; }
        body.dark-mode .dataTables_filter input { background: #2a2a2a !important; color: #e0e0e0 !important; border-color: #555 !important; }
        body.dark-mode .dataTables_filter input:focus { border-color: var(--red) !important; box-shadow: 0 0 0 3px rgba(220,38,38,0.2) !important; }
        body.dark-mode .dataTables_info { background: #3a3a3a !important; color: #e0e0e0 !important; padding: 8px 12px; border-radius: 6px; display: inline-block; }
        body.dark-mode .dataTables_paginate .paginate_button { background: #3a3a3a !important; border-color: #555 !important; color: #e0e0e0 !important; }
        body.dark-mode .dataTables_paginate .paginate_button:hover { background: rgba(220,38,38,0.2) !important; border-color: var(--red) !important; color: #ff6b6b !important; }
        body.dark-mode .dataTables_paginate .paginate_button.current { background: var(--red) !important; border-color: var(--red) !important; color: #fff !important; }
        body.dark-mode .dataTables_paginate .paginate_button.disabled { color: #777 !important; }
        body.dark-mode .dashboard-footer { background: #2a2a2a; border-top: 1px solid #3a3a3a; color: #999; }
        body.dark-mode .dashboard-footer a { color: #999; }
        body.dark-mode .dashboard-footer a:hover { color: var(--red); }
        body.dark-mode .dashboard-footer .divider { color: #555; }
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

    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($data->profile_photo) && $data->profile_photo)
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name }}</span>
            <span class="user-role">Professor</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/professor/home') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Dashboard</span>
            <span class="tooltip-label">Dashboard</span>
        </a>
        <a href="{{ url('/professor/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-check"></i></span>
            <span class="nav-label">Req. Status</span>
            <span class="tooltip-label">Req. Status</span>
        </a>
        <a href="{{ url('/professor/analytics') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
        </a>
        <a href="{{ url('/reportsExpiredProf') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/professor/maintain') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cogs"></i></span>
            <span class="nav-label">Maintenance</span>
            <span class="tooltip-label">Maintenance</span>
        </a>
        <a href="{{ url('/professor/evaluation') }}" class="nav-item">
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
                <i class="fa fa-chalkboard-teacher"></i>
                Professor Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content" style="flex:1 0 auto;">

        <!-- Page Header -->
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
                <h2>Welcome to your professor dashboard</h2>
                <p>
                    For first-time users, please watch these short how-to videos to fully set up your account
                    and get familiar with the evaluation workflow.
                </p>
                <div class="welcome-actions">
                    <a href="https://youtu.be/ikfInULZYJs" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube"></i>
                        Professor Setup Guide
                    </a>
                    <a href="https://youtu.be/2txVame31n0" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube"></i>
                        Student Evaluation Guide
                    </a>
                    <a href="https://www.youtube.com/playlist?list=PLyMOKHLwy4fOSbAPTzUQBSlEcMHoLQvcQ" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube"></i>
                        How To Videos
                    </a>
                </div>
            </div>
            <div class="welcome-icon">
                <i class="fa fa-chalkboard-teacher"></i>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <a href="{{ url('/allStudents') }}" class="stat-card">
                <div class="stat-icon red">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ $roleCount }}</div>
                    <div class="stat-name">Total Students</div>
                </div>
            </a>

            <a href="{{ url('/professor/class') }}" class="stat-card">
                <div class="stat-icon green">
                    <i class="fa fa-chalkboard"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-num">{{ isset($class) ? count($class) : (isset($classrooms) ? count($classrooms) : 0) }}</div>
                    <div class="stat-name">Active Classes</div>
                </div>
            </a>

            <a href="{{ url('/professor/evaluation') }}" class="stat-card">
                <div class="stat-icon red">
                    <i class="fa fa-star-half-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-num">View</div>
                    <div class="stat-name">Evaluations</div>
                </div>
            </a>
        </div>

        <!-- Students by Class/Section -->
        <div class="table-card" style="margin-top:32px;">
            <div class="table-card-header">
                <h2>
                    <div class="header-icon"><i class="fa fa-users"></i></div>
                    Students by Class/Section
                </h2>
                <div style="display:flex; gap:12px; align-items:center;">
                    <select id="courseFilter" style="border-radius:8px;padding:6px 12px;font-size:13px;">
                        <option value="">All Courses</option>
                        @if(isset($class))
                            @foreach($class->pluck('course')->unique() as $course)
                                <option value="{{ $course }}">{{ $course }}</option>
                            @endforeach
                        @endif
                    </select>
                    <select id="classFilter" style="border-radius:8px;padding:6px 12px;font-size:13px;">
                        <option value="">All Classes</option>
                        @if(isset($class))
                            @foreach($class as $room)
                                <option value="{{ $room->room }}">{{ $room->room }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="table-card-body" style="padding: 0;">
                <div style="overflow-x:auto;">
                <table id="studentsTable" class="display" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Class/Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($class) && count($class) > 0)
                            @foreach($class as $room)
                                @if(isset($room->students) && count($room->students) > 0)
                                    @foreach($room->students as $student)
                                        <tr data-course="{{ $room->course }}" data-class="{{ $room->room }}">
                                            <td>{{ $student->full_name }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $room->course }}</td>
                                            <td>{{ $room->room }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
                </div>
                @if(!(isset($class) && count($class) > 0))
                    <div style="color:#888; font-size:13px; padding:18px;">No classes found.</div>
                @endif
            </div>
        </div>


<!-- Dashboard Footer (restored, only at the bottom) -->
<footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
        <span class="footer-copy">
            &copy; 1998–{{ date('Y') }} <span>Polytechnic University of the Philippines</span>
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

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#studentsTable').DataTable({
        "paging": true,
        "info": false,
        "lengthChange": false,
        "pageLength": 8,
        "order": [[0, 'asc']],
        "language": {
            "emptyTable": "No students to display"
        }
    });

    $('#courseFilter').on('change', function() {
        var val = $(this).val();
        table.column(2).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
        $('#classFilter').val('');
    });
    $('#classFilter').on('change', function() {
        var val = $(this).val();
        table.column(3).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
        $('#courseFilter').val('');
    });
});
</script>

<script>
    // Current date
    const dateEl = document.getElementById('currentDate');
    if (dateEl) {
        dateEl.textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'short', year: 'numeric',
            month: 'long', day: 'numeric'
        });
    }

    // Sidebar toggle - wrapped in a safe initialization function
    function initSidebarToggle() {
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle  = document.getElementById('menuToggle');
        const overlay     = document.getElementById('sidebarOverlay');

        // Ensure all elements exist
        if (!sidebar) {
            console.error('Sidebar element not found');
            return;
        }
        if (!mainContent) {
            console.error('Main content element not found');
            return;
        }
        if (!menuToggle) {
            console.error('Menu toggle element not found');
            return;
        }
        if (!overlay) {
            console.error('Sidebar overlay element not found');
            return;
        }

        // Click handler for menu toggle
        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // Click handler for overlay
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            } else {
                if (!sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                }
            }
        });
    }

    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarToggle);
    } else {
        // DOM is already loaded
        initSidebarToggle();
    }
</script>
</script>

<script>
    /* ══════════════════════════════════════════════
       DATE & TIME MODAL
    ══════════════════════════════════════════════ */

    let dtOverlay = document.getElementById('dtOverlay');
    const dateBadge = document.getElementById('dateBadge');

    // Create modal if it doesn't exist
    if (!dtOverlay) {
        const modalHTML = `
        <div class="dt-overlay" id="dtOverlay">
            <div class="dt-modal">
                <div class="dt-modal-header">
                    <div class="dt-header-top">
                        <span class="dt-header-title">Current Date & Time</span>
                        <button class="dt-close-btn" id="dtCloseBtn">
                            <i class="fa fa-times"></i>
                        </button>
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
                        <div class="dt-date-sub" id="dtDateSub">Loading...</div>
                    </div>
                </div>
                <div class="dt-analog-wrap">
                    <div class="analog-clock" id="analogClock">
                        <div class="hand hour-hand" id="hourHand"></div>
                        <div class="hand minute-hand" id="minuteHand"></div>
                        <div class="hand second-hand" id="secondHand"></div>
                        <div class="clock-center"></div>
                    </div>
                </div>
                <div class="dt-calendar">
                    <div class="cal-nav">
                        <button class="cal-nav-btn" id="calPrev"><i class="fa fa-chevron-left"></i></button>
                        <div class="cal-month-label" id="calMonthLabel">January 2024</div>
                        <button class="cal-nav-btn" id="calNext"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <div class="cal-grid" id="calGrid"></div>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        dtOverlay = document.getElementById('dtOverlay');
    }

    function openModal() {
        if (dtOverlay) {
            dtOverlay.classList.add('open');
            startClock();
        }
    }

    function closeModal() {
        if (dtOverlay) {
            dtOverlay.classList.remove('open');
            stopClock();
        }
    }

    // Event listeners for modal
    const dtCloseBtn = document.getElementById('dtCloseBtn');
    if (dtCloseBtn) {
        dtCloseBtn.addEventListener('click', closeModal);
    }

    if (dtOverlay) {
        dtOverlay.addEventListener('click', function (e) {
            if (e.target === dtOverlay) closeModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    // Open modal when date badge is clicked
    if (dateBadge) {
        dateBadge.addEventListener('click', openModal);
    }

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

            document.getElementById('secondHand').style.transform = 'rotate(' + secDeg + 'deg)';
            document.getElementById('minuteHand').style.transform = 'rotate(' + minDeg + 'deg)';
            document.getElementById('hourHand').style.transform   = 'rotate(' + hourDeg + 'deg)';

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
                width:  ' + (i % 3 === 0 ? 2.5 : 1.5) + 'px;
                height: ' + (i % 3 === 0 ? 8 : 5) + 'px;
                background: currentColor;
                border-radius: 2px;
                top: 4px;
                left: calc(50% - ' + (i % 3 === 0 ? 1.25 : 0.75) + 'px);
                transform-origin: center 53px;
                transform: rotate(' + angle + 'deg);';
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
        document.getElementById('calMonthLabel').textContent = MONTHS[month] + ' ' + year;

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
    <script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
</html>
