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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student_home-responsive.css') }}">
    <script src="{{ url('/assets/js/dark-mode.js') }}"></script>

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
            --surface-3:    #f0f0f0;
            --border:       #f0f0f0;
            --border-2:     rgba(0,0,0,0.04);
            --text-primary: #1a1a1a;
            /* Was #888 on white (~3.5:1) — failed WCAG AA for normal text. Darkened to meet 4.5:1. */
            --text-secondary: #5f5f5f;
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
            --footer-bg:    #121212;
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
            color: var(--toggle-color); font-size: 16px;
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1); flex-shrink: 0;
        }

        .darkmode-toggle:hover {
            background: #fee2e2; color: var(--red); border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        .darkmode-toggle:active { transform: scale(0.95); }

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

        /* Date badge */
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

        body.dark-mode .date-badge:hover {
            background: rgba(220,38,38,0.15);
            border-color: rgba(220,38,38,0.3);
        }

        .date-badge i.fa-calendar-alt { font-size: 11px; }
        .date-badge .pulse-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #16a34a;
            animation: pulse-green 1.8s ease-in-out infinite;
            flex-shrink: 0;
        }
        /* Small affordance so it's clear the badge is interactive, even without hover/tooltip on mobile */
        .date-badge .fa-chevron-down { font-size: 9px; color: var(--text-muted); margin-left: 1px; }

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
        .welcome-video-btn i { font-size: 14px; }
        .welcome-icon    { font-size: 64px; opacity: 0.2; position: relative; z-index: 1; }

        /* =============== STATS =============== */
        /* Was auto-fit(minmax(200px,1fr)), which could produce an uneven last row at
           in-between widths for 4 cards. Explicit column counts per breakpoint instead. */
        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 18px; margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface); border-radius: 16px; padding: 22px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid var(--border-2);
            transition: transform 0.25s, box-shadow 0.25s, background 0.3s;
            text-decoration: none;
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,0.1); text-decoration: none; }

        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.red   { background: #fee2e2; color: var(--red); }
        .stat-icon.blue  { background: #dbeafe; color: #2563eb; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.amber { background: #fef9c3; color: #ca8a04; }

        body.dark-mode .stat-icon.red   { background: rgba(220,38,38,0.2);  color: #ff6b6b; }
        body.dark-mode .stat-icon.blue  { background: rgba(37,99,235,0.2);  color: #4f96ff; }
        body.dark-mode .stat-icon.green { background: rgba(22,163,74,0.2);  color: #22c55e; }
        body.dark-mode .stat-icon.amber { background: rgba(202,138,4,0.2);  color: #facc15; }

        .stat-info .stat-num  { font-size: 26px; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .stat-info .stat-num.stat-num--empty { font-size: 15px; font-weight: 700; color: var(--text-muted); }
        .stat-info .stat-name { font-size: 12.5px; color: var(--text-secondary); margin-top: 4px; }

        /* =============== DASHBOARD GRID =============== */
        .dash-grid {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 22px;
            align-items: start;
        }

        .dash-right-col { display: flex; flex-direction: column; gap: 22px; }

        /* =============== PANEL CARD =============== */
        .panel-card {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid var(--border-2);
            overflow: hidden;
            transition: background 0.3s;
        }

        .panel-card-header {
            display: flex; align-items: center; gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            transition: border-color 0.3s;
        }

        .panel-header-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 14px; flex-shrink: 0;
        }

        body.dark-mode .panel-header-icon { background: rgba(220,38,38,0.2); color: #ff6b6b; }

        .panel-card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-primary); }
        .panel-card-header p  { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }

        /* =============== QUICK ACTIONS =============== */
        .quick-actions-wrap { padding: 8px 0; }

        .qa-item {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            text-decoration: none; color: var(--text-primary);
            transition: all 0.2s;
        }

        .qa-item:hover {
            background: #fff5f5;
            padding-left: 26px;
            text-decoration: none;
            color: var(--text-primary);
        }

        body.dark-mode .qa-item:hover { background: rgba(220,38,38,0.08); }

        .qa-icon-wrap {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0; transition: all 0.2s;
        }

        .qa-icon-wrap.red    { background: #fee2e2; color: var(--red); }
        .qa-icon-wrap.blue   { background: #dbeafe; color: #2563eb; }
        .qa-icon-wrap.green  { background: #dcfce7; color: #16a34a; }
        .qa-icon-wrap.purple { background: #ede9fe; color: #7c3aed; }
        .qa-icon-wrap.amber  { background: #fef9c3; color: #ca8a04; }

        body.dark-mode .qa-icon-wrap.red    { background: rgba(220,38,38,0.2);  color: #ff6b6b; }
        body.dark-mode .qa-icon-wrap.blue   { background: rgba(37,99,235,0.2);  color: #4f96ff; }
        body.dark-mode .qa-icon-wrap.green  { background: rgba(22,163,74,0.2);  color: #22c55e; }
        body.dark-mode .qa-icon-wrap.purple { background: rgba(124,58,237,0.2); color: #a78bfa; }
        body.dark-mode .qa-icon-wrap.amber  { background: rgba(202,138,4,0.2);  color: #facc15; }

        .qa-text { flex: 1; }
        .qa-title { font-size: 13.5px; font-weight: 600; color: var(--text-primary); }
        .qa-desc  { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
        .qa-arrow { color: var(--text-muted); font-size: 11px; transition: all 0.2s; }
        .qa-item:hover .qa-arrow { color: var(--red); transform: translateX(3px); }

        /* =============== MILESTONE / JOURNEY (graph tracker) =============== */
        .milestone-wrap { padding: 24px 16px 16px; }

        .ojt-track { display: flex; align-items: flex-start; position: relative; }

        .ojt-step { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; min-width: 0; }

        .ojt-line {
            position: absolute; top: 23px; left: calc(50% + 26px); right: calc(-50% + 26px);
            height: 3px; background: var(--border); z-index: 0; transition: background 0.3s;
        }

        .ojt-line.filled { background: #16a34a; }

        .ojt-dot {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; position: relative; z-index: 1;
            border: 2.5px solid var(--border); background: var(--surface);
            color: var(--text-muted); flex-shrink: 0; transition: all 0.3s;
        }

        .ojt-dot.done { background: #dcfce7; border-color: #16a34a; color: #16a34a; }

        .ojt-dot.active {
            background: #fee2e2; border-color: var(--red); color: var(--red);
            width: 54px; height: 54px; font-size: 18px;
            animation: ojt-pulse 2s ease-in-out infinite;
        }

        body.dark-mode .ojt-dot.done   { background: rgba(22,163,74,0.2); border-color: #22c55e; color: #22c55e; }
        body.dark-mode .ojt-dot.active { background: rgba(220,38,38,0.2); border-color: var(--red); color: #ff6b6b; }

        @keyframes ojt-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.35); }
            50%       { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .ojt-dot.active { animation: none; }
        }

        .ojt-title { font-size: 13px; font-weight: 600; color: var(--text-primary); text-align: center; margin-top: 12px; }
        .ojt-sub   { font-size: 12px; color: var(--text-secondary); text-align: center; margin-top: 2px; max-width: 130px; }

        .ojt-badge { font-size: 11px; font-weight: 600; margin-top: 8px; padding: 3px 10px; border-radius: 10px; display: inline-block; }
        .ojt-badge.done   { background: #dcfce7; color: #14532d; }
        .ojt-badge.active { background: #fee2e2; color: #7f1d1d; }
        body.dark-mode .ojt-badge.done   { background: rgba(22,163,74,0.2);  color: #86efac; }
        body.dark-mode .ojt-badge.active { background: rgba(220,38,38,0.2); color: #fca5a5; }

        @media (max-width: 520px) {
            .ojt-track { flex-direction: column; align-items: flex-start; gap: 20px; }
            .ojt-line { display: none; }
            .ojt-step { flex-direction: row; align-items: center; gap: 14px; }
            .ojt-title, .ojt-sub { text-align: left; }
            .ojt-sub { max-width: none; }
            .ojt-title { margin-top: 0; }
        }

        /* =============== TIPS =============== */
        .tips-wrap { padding: 8px 20px 16px; }

        .tip-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .tip-item:last-child { border-bottom: none; }

        .tip-icon {
            width: 30px; height: 30px; border-radius: 8px;
            background: #fef9c3; color: #ca8a04;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0; margin-top: 1px;
        }

        body.dark-mode .tip-icon { background: rgba(202,138,4,0.2); color: #facc15; }

        .tip-text { font-size: 12.5px; color: var(--text-secondary); line-height: 1.5; padding-top: 4px; }

        /* =============== ANNOUNCEMENTS =============== */
        .announcement-item {
            display: flex; gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
        }

        .announcement-item:last-child { border-bottom: none; }
        .announcement-item:hover { background: var(--surface-2); }

        .ann-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--red); flex-shrink: 0; margin-top: 5px;
        }

        .ann-title { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .ann-date  { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; }
        .ann-body  { font-size: 12.5px; color: var(--text-secondary); margin-top: 4px; line-height: 1.5; }

        .empty-ann {
            padding: 32px 20px; text-align: center;
            color: var(--text-muted); font-size: 13px;
        }

        .empty-ann i { font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.4; }

        /* =============== FOOTER =============== */
        .dashboard-footer {
            background: var(--footer-bg); border-top: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 18px 28px;
            font-size: 12.5px; margin-top: auto;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center; gap: 6px;
            transition: background 0.3s, border-color 0.3s;
        }

        .dashboard-footer .footer-inner { display: flex; align-items: center; gap: 8px; }
        .dashboard-footer .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .dashboard-footer .footer-copy { font-size: 12.5px; color: var(--text-muted); font-weight: 500; }
        .dashboard-footer .footer-copy span { color: var(--red); font-weight: 600; }
        .dashboard-footer .footer-links { display: flex; align-items: center; gap: 6px; }
        .dashboard-footer a { color: var(--text-secondary); text-decoration: none; font-weight: 500; font-size: 12.5px; transition: color 0.2s; }
        .dashboard-footer a:hover { color: var(--red); }
        .dashboard-footer .divider { color: var(--border); margin: 0 2px; }

        /* =============== DATE-TIME MODAL =============== */
        .dt-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .dt-overlay.open { opacity: 1; pointer-events: all; }

        .dt-modal {
            background: var(--surface);
            border-radius: 22px; width: 360px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.05);
            overflow: hidden;
            transform: scale(0.88) translateY(20px);
            transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
            opacity: 0;
        }

        .dt-overlay.open .dt-modal { transform: scale(1) translateY(0); opacity: 1; }

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

        .dt-header-top { display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1; }
        .dt-header-title { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; }

        .dt-close-btn {
            width: 28px; height: 28px; border-radius: 8px;
            background: rgba(255,255,255,0.15); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 13px; transition: background 0.2s;
        }

        .dt-close-btn:hover { background: rgba(255,255,255,0.25); }

        .dt-clock-display { margin-top: 10px; position: relative; z-index: 1; }

        .dt-time-big {
            font-size: 42px; font-weight: 800; color: #fff;
            letter-spacing: -1px; line-height: 1;
            display: flex; align-items: center; gap: 4px;
        }

        .dt-time-big .colon { animation: blink-colon 1s step-end infinite; display: inline-block; margin: 0 1px; }

        @keyframes blink-colon { 0%, 100% { opacity: 1; } 50% { opacity: 0.15; } }

        .dt-time-ampm { font-size: 14px; font-weight: 700; color: rgba(255,255,255,0.7); margin-left: 6px; align-self: flex-end; margin-bottom: 6px; }
        .dt-date-sub  { font-size: 12.5px; color: rgba(255,255,255,0.65); margin-top: 4px; font-weight: 500; }

        .dt-analog-wrap { display: flex; justify-content: center; padding: 16px 0 8px; }

        .analog-clock {
            width: 110px; height: 110px; border-radius: 50%;
            background: var(--surface-2); border: 3px solid var(--border);
            position: relative;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.08), 0 4px 18px rgba(0,0,0,0.1);
        }

        .clock-mark { position: absolute; width: 2px; border-radius: 2px; background: var(--text-muted); left: 50%; transform-origin: bottom center; }
        .clock-center { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--red); top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; box-shadow: 0 0 0 2px var(--surface); }
        .hand { position: absolute; bottom: 50%; left: 50%; transform-origin: bottom center; border-radius: 4px 4px 0 0; }
        .hour-hand   { width: 3.5px; height: 28px; background: var(--text-primary); margin-left: -1.75px; }
        .minute-hand { width: 2.5px; height: 36px; background: var(--text-primary); margin-left: -1.25px; }
        .second-hand { width: 1.5px; height: 40px; background: var(--red); margin-left: -0.75px; }

        .dt-calendar { padding: 0 18px 18px; }

        .cal-nav { display: flex; align-items: center; justify-content: space-between; padding: 10px 2px; }

        .cal-nav-btn {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--surface-2); border: 1px solid var(--border);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); font-size: 12px; transition: all 0.2s;
        }

        .cal-nav-btn:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }
        .cal-month-label { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
        .cal-day-name { text-align: center; font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; padding: 4px 0 6px; }
        .cal-day { text-align: center; font-size: 12.5px; font-weight: 500; color: var(--text-primary); padding: 7px 4px; border-radius: 8px; cursor: pointer; transition: all 0.15s; line-height: 1; }
        .cal-day:hover:not(.empty):not(.today) { background: var(--surface-2); color: var(--red); }
        .cal-day.empty  { cursor: default; color: transparent; }
        .cal-day.other-month { color: var(--text-muted); }
        .cal-day.today { background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff !important; font-weight: 700; box-shadow: 0 3px 10px rgba(220,38,38,0.35); }
        .cal-day.selected:not(.today) { background: #fee2e2; color: var(--red); font-weight: 700; }

        /* =============== SIDEBAR OVERLAY =============== */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }
        .sidebar-overlay.active { display: block; }

        /* =============== RESPONSIVE =============== */
        @media (max-width: 1100px) {
            .dash-grid { grid-template-columns: 1fr; }
            .dash-right-col { gap: 22px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 900px) {
            .sidebar { width: var(--sidebar-w); transform: translateX(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .dt-modal { width: 92vw; }
            .welcome-icon { display: none; }
            .welcome-banner { flex-direction: column; gap: 12px; }
            .welcome-actions { width: 100%; }
            .welcome-video-btn { width: 100%; justify-content: center; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa fa-user" aria-hidden="true"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-home" aria-hidden="true"></i></span>
            <span class="nav-label">Home</span>
            <span class="tooltip-label">Home</span>
        </a>
        <a href="{{ url('/student/ojtinfo') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-layer-group" aria-hidden="true"></i></span>
            <span class="nav-label">OJT Information</span>
            <span class="tooltip-label">OJT Information</span>
        </a>
        <a href="{{ url('/student/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ url('/student/files') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-download" aria-hidden="true"></i></span>
            <span class="nav-label">Downloadable Files</span>
            <span class="tooltip-label">Downloadable Files</span>
        </a>
        <a href="{{ url('/student/MOA') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-file-alt" aria-hidden="true"></i></span>
            <span class="nav-label">Notarized MOA</span>
            <span class="tooltip-label">Notarized MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt" aria-hidden="true"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
        </a>
        <a href="{{ url('/student/evaluation') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-star-half-alt" aria-hidden="true"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/logout') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-sign-out-alt" aria-hidden="true"></i></span>
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
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar"><i class="fa fa-bars" aria-hidden="true"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode" aria-label="Toggle dark mode">
                <i class="fa fa-moon" aria-hidden="true"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <h1>Home <span>Dashboard</span></h1>
            <div class="date-badge" id="dateBadge" title="Click to view calendar & clock" role="button" tabindex="0" aria-label="Open calendar and clock">
                <span class="pulse-dot" aria-hidden="true"></span>
                <i class="fa fa-calendar-alt" aria-hidden="true"></i>
                <span id="currentDate"></span>
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>
        </div>

        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Welcome to your student dashboard</h2>
                <p>
                    For first-time users, please watch these short how-to videos to fully set up your account
                    and understand the evaluation process.
                </p>
                <div class="welcome-actions">
                    <a href="https://youtu.be/H0ek8it4jKc" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        Student Guide
                    </a>
                    <a href="https://youtu.be/jhLuCIX6yhw" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        Evaluation Guide
                    </a>
                    <a href="https://www.youtube.com/playlist?list=PLyMOKHLwy4fPzwbJ0RsqgHRhvx2Ok1wH3" target="_blank" rel="noopener noreferrer" class="welcome-video-btn">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        How To Videos
                    </a>
                </div>
            </div>
            <div class="welcome-icon">
                <i class="fa fa-user-graduate" aria-hidden="true"></i>
            </div>
        </div>

        <!--
            Stats Row — now driven by real status values instead of repeating each
            card's own label. Controller should pass:
              $fileCount              (int)
              $requirementsSubmitted  (int)
              $requirementsTotal      (int)
              $ojtInfoComplete        (bool)
              $evaluationStatus       (string: 'pending' | 'completed' | null)
        -->
        <div class="stats-grid">
            <a href="{{ url('/student/files') }}" class="stat-card">
                <div class="stat-icon red"><i class="fa fa-cloud-download-alt" aria-hidden="true"></i></div>
                <div class="stat-info">
                    @if($fileCount > 0)
                        <div class="stat-num">{{ $fileCount }}</div>
                    @else
                        <div class="stat-num stat-num--empty">None yet</div>
                    @endif
                    <div class="stat-name">Downloadable Templates</div>
                </div>
            </a>
            <a href="{{ url('/student/requirements') }}" class="stat-card">
                <div class="stat-icon green"><i class="fa fa-tasks" aria-hidden="true"></i></div>
                <div class="stat-info">
                    <div class="stat-num">{{ $requirementsSubmitted ?? 0 }}/{{ $requirementsTotal ?? 0 }}</div>
                    <div class="stat-name">Requirements Submitted</div>
                </div>
            </a>
            <a href="{{ url('/student/ojtinfo') }}" class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
                <div class="stat-info">
                    @if(!empty($ojtInfoComplete))
                        <div class="stat-num">Complete</div>
                    @else
                        <div class="stat-num stat-num--empty">Not set up</div>
                    @endif
                    <div class="stat-name">OJT Information</div>
                </div>
            </a>
            <a href="{{ url('/student/evaluation') }}" class="stat-card">
                <div class="stat-icon red"><i class="fa fa-star-half-alt" aria-hidden="true"></i></div>
                <div class="stat-info">
                    @if(($evaluationStatus ?? null) === 'completed')
                        <div class="stat-num">Completed</div>
                    @else
                        <div class="stat-num stat-num--empty">Pending</div>
                    @endif
                    <div class="stat-name">Supervisor Evaluation</div>
                </div>
            </a>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dash-grid">

            <!-- LEFT: Quick Actions + Announcements -->
            <div style="display:flex; flex-direction:column; gap:22px;">

                <!-- Quick Actions -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-bolt" aria-hidden="true"></i></div>
                        <div>
                            <h2>Quick Actions</h2>
                            <p>Jump to the most important sections</p>
                        </div>
                    </div>
                    <div class="quick-actions-wrap">
                        <a href="{{ url('/student/ojtinfo') }}" class="qa-item">
                            <div class="qa-icon-wrap red"><i class="fa fa-layer-group" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">OJT Information</div>
                                <div class="qa-desc">View your OJT details & status</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/class') }}" class="qa-item">
                            <div class="qa-icon-wrap blue"><i class="fa fa-clipboard" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">My Class</div>
                                <div class="qa-desc">View your class & professor</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/files') }}" class="qa-item">
                            <div class="qa-icon-wrap green"><i class="fa fa-download" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">Downloadable Files</div>
                                <div class="qa-desc">Get templates & forms</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/MOA') }}" class="qa-item">
                            <div class="qa-icon-wrap purple"><i class="fa fa-file-alt" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">MOA</div>
                                <div class="qa-desc">Memorandum of Agreement</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/student/requirements') }}" class="qa-item" style="border-bottom:none;">
                            <div class="qa-icon-wrap amber"><i class="fa fa-cloud-upload-alt" aria-hidden="true"></i></div>
                            <div class="qa-text">
                                <div class="qa-title">Requirements</div>
                                <div class="qa-desc">Submit & track your documents</div>
                            </div>
                            <i class="fa fa-chevron-right qa-arrow" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></div>
                        <div>
                            <h2>Announcements</h2>
                            <p>Latest updates from your coordinator</p>
                        </div>
                    </div>
                    @if(isset($announcements) && count($announcements) > 0)
                        @foreach($announcements as $ann)
                        <div class="announcement-item">
                            <div class="ann-dot" aria-hidden="true"></div>
                            <div>
                                <div class="ann-title">{{ $ann->title }}</div>
                                <div class="ann-date">{{ \Carbon\Carbon::parse($ann->created_at)->format('M d, Y') }}</div>
                                <div class="ann-body">{{ Str::limit($ann->content, 120) }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-ann">
                            <i class="fa fa-bell-slash" aria-hidden="true"></i>
                            No announcements yet. Check back later.
                        </div>
                    @endif
                </div>

            </div>

            <!-- RIGHT: Journey + Tips -->
            <div class="dash-right-col">

                <!-- OJT Journey -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-chart-line" aria-hidden="true"></i></div>
                        <div>
                            <h2>OJT Guidelines</h2>
                            <p>Your internship milestones</p>
                        </div>
                    </div>
                    <!--
                        Graph-style milestone tracker. Drive state from the controller via
                        $ojtStage — one of: 'account_created', 'requirements', 'moa', 'completed'.
                        Each stage is considered "done" once the student has passed it, and
                        "active" if it's their current stage.
                    -->
                    @php
                        $ojtStages = ['account_created', 'requirements', 'moa', 'completed'];
                        $currentIndex = array_search($ojtStage ?? 'account_created', $ojtStages);
                        $currentIndex = $currentIndex === false ? 0 : $currentIndex;
                    @endphp
                    <div class="milestone-wrap" role="list" aria-label="OJT journey progress">
                        <div class="ojt-track">
                            @php
                                $steps = [
                                    ['key' => 'account_created', 'icon' => 'fa-check',          'title' => 'Account Created', 'sub' => "You're registered in the system"],
                                    ['key' => 'requirements',    'icon' => 'fa-circle',          'title' => 'Submit Requirements', 'sub' => 'Upload your required documents'],
                                    ['key' => 'moa',             'icon' => 'fa-file-signature',  'title' => 'MOA Signing', 'sub' => 'Agreement with your company'],
                                    ['key' => 'completed',       'icon' => 'fa-flag',            'title' => 'OJT Completion', 'sub' => 'Finish your internship hours'],
                                ];
                            @endphp
                            @foreach($steps as $i => $step)
                                @php
                                    $isDone   = $i < $currentIndex;
                                    $isActive = $i === $currentIndex;
                                @endphp
                                <div class="ojt-step" role="listitem">
                                    @if($i < count($steps) - 1)
                                        <div class="ojt-line {{ $isDone ? 'filled' : '' }}"></div>
                                    @endif
                                    <div class="ojt-dot {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                                        <i class="fa {{ $isDone ? 'fa-check' : $step['icon'] }}" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <div class="ojt-title">{{ $step['title'] }}</div>
                                        <div class="ojt-sub">{{ $step['sub'] }}</div>
                                        @if($isDone)
                                            <span class="ojt-badge done">Done</span>
                                        @elseif($isActive)
                                            <span class="ojt-badge active">In progress</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Helpful Tips -->
                <div class="panel-card">
                    <div class="panel-card-header">
                        <div class="panel-header-icon"><i class="fa fa-lightbulb" aria-hidden="true"></i></div>
                        <div>
                            <h2>Helpful Tips</h2>
                            <p>Make the most of your OJT</p>
                        </div>
                    </div>
                    <div class="tips-wrap">
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-clock" aria-hidden="true"></i></div>
                            <div class="tip-text">Submit your requirements before the deadline to avoid delays in your OJT approval.</div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-file-alt" aria-hidden="true"></i></div>
                            <div class="tip-text">Download all necessary templates from the Files section early to stay prepared.</div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-user-tie" aria-hidden="true"></i></div>
                            <div class="tip-text">Keep your OJT Information updated with your company details and supervisor.</div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-icon"><i class="fa fa-bell" aria-hidden="true"></i></div>
                            <div class="tip-text">Check the Announcements section regularly for important updates from your coordinator.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-inner">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP logo">
            <span class="footer-copy">
                © 1998–2026 <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div class="footer-links">
            <a href="https://www.pup.edu.ph/" target="_blank">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;" aria-hidden="true"></i>
                PUP Website
            </a>
            <span class="divider">|</span>
            <a href="{{ url('/terms') }}">Terms of Use</a>
            <span class="divider">|</span>
            <a href="{{ url('/privacy') }}">Privacy Statement</a>
        </div>
    </footer>

</div>

<!-- =============== DATE & TIME MODAL =============== -->
<div class="dt-overlay" id="dtOverlay">
    <div class="dt-modal" id="dtModal">
        <div class="dt-modal-header">
            <div class="dt-header-top">
                <span class="dt-header-title"><i class="fa fa-clock" style="margin-right:6px;" aria-hidden="true"></i>Date & Time</span>
                <button class="dt-close-btn" id="dtCloseBtn" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
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
                <div class="hand hour-hand"   id="hourHand"></div>
                <div class="hand minute-hand" id="minuteHand"></div>
                <div class="hand second-hand" id="secondHand"></div>
            </div>
        </div>
        <div class="dt-calendar">
            <div class="cal-nav">
                <button class="cal-nav-btn" id="calPrev" aria-label="Previous month"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                <span class="cal-month-label" id="calMonthLabel"></span>
                <button class="cal-nav-btn" id="calNext" aria-label="Next month"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
            </div>
            <div class="cal-grid" id="calGrid"></div>
        </div>
    </div>
</div>

@if(!session()->has('termsAccepted'))
    @include('students.terms_modal')
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const menuToggle  = document.getElementById('menuToggle');
const overlay     = document.getElementById('sidebarOverlay');

function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.style.display = 'block';   // force override the inline display:none
}

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.style.display = 'none';
}

menuToggle.addEventListener('click', function () {
    const isMobile = window.innerWidth <= 900;
    if (isMobile) {
        if (sidebar.classList.contains('mobile-open')) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
});

overlay.addEventListener('click', closeMobileSidebar);



    /* ── Live date badge ── */
    const dateEl = document.getElementById('currentDate');
    function updateBadgeDate() {
        dateEl.textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'short', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    updateBadgeDate();
    setInterval(updateBadgeDate, 60000);

    /* ── Date & Time Modal ── */
    const dtOverlay  = document.getElementById('dtOverlay');
    const dtCloseBtn = document.getElementById('dtCloseBtn');
    const dateBadge  = document.getElementById('dateBadge');

    function openModal() {
        dtOverlay.classList.add('open');
        startClock();
        renderCalendar(calViewYear, calViewMonth);
    }

    dateBadge.addEventListener('click', openModal);
    dateBadge.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModal(); }
    });

    function closeModal() {
        dtOverlay.classList.remove('open');
        stopClock();
    }

    dtCloseBtn.addEventListener('click', closeModal);
    dtOverlay.addEventListener('click', function (e) { if (e.target === dtOverlay) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    /* ── Digital + Analog Clock ── */
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

    /* ── Analog clock marks ── */
    (function buildMarks() {
        const clock = document.getElementById('analogClock');
        for (let i = 0; i < 12; i++) {
            const mark = document.createElement('div');
            mark.className = 'clock-mark';
            mark.style.cssText = `
                position: absolute;
                width:  ${i % 3 === 0 ? 2.5 : 1.5}px;
                height: ${i % 3 === 0 ? 8 : 5}px;
                background: var(--text-muted);
                border-radius: 2px;
                top: 4px;
                left: calc(50% - ${i % 3 === 0 ? 1.25 : 0.75}px);
                transform-origin: center 51px;
                transform: rotate(${i * 30}deg);
            `;
            clock.appendChild(mark);
        }
    })();

    /* ── Calendar ── */
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
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
        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';
        DAYS.forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-day-name';
            el.textContent = d;
            grid.appendChild(el);
        });
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'cal-day empty';
            grid.appendChild(el);
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const el = document.createElement('div');
            el.className = 'cal-day';
            el.textContent = d;
            const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const isSel   = d === selectedDay && month === calViewMonth && year === calViewYear;
            if (isToday) el.classList.add('today');
            else if (isSel) el.classList.add('selected');
            el.addEventListener('click', function () {
                selectedDay = d; calViewYear = year; calViewMonth = month;
                renderCalendar(year, month);
            });
            grid.appendChild(el);
        }
    }
</script>
<script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
</html>