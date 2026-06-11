<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Analytics</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">

    <style>
        :root {
            --red: #dc2626;
            --bg: #f5f5f5;
            --card: #ffffff;
            --text: #1a1a1a;
            --muted: #6b7280;
            --line: #eceff3;
        }

        * { box-sizing: border-box; }
        html, body { overflow-x: clip; }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .app-layout {
            min-height: 100vh;
            display: flex;
            overflow-x: clip;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(160deg, #1a0000 0%, #4a0000 50%, #7f0000 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 4px 0 24px rgba(0,0,0,0.18);
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            color: #fff;
        }

        .sidebar-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.2));
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            white-space: nowrap;
            overflow: hidden;
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

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            color: #fff;
        }

        .sidebar-user:hover { background: rgba(255,255,255,0.05); }

        .sidebar-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            background: rgba(239,68,68,0.25);
            border: 1.5px solid rgba(239,68,68,0.4);
            display: grid;
            place-items: center;
            color: #fecaca;
            flex-shrink: 0;
        }

        .sidebar-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 10px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            letter-spacing: 1px;
            margin-top: 3px;
        }

        .sidebar-nav {
            padding: 12px 0;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(239,68,68,0.3); border-radius: 10px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            position: relative;
            transition: all 0.25s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(239,68,68,0.15);
            border-left-color: #ef4444;
            color: #fff;
        }

        .nav-item .nav-icon { font-size: 18px; width: 22px; text-align: center; flex-shrink: 0; }
        .nav-item .nav-label { overflow: hidden; }
        .nav-item .tooltip-label {
            position: absolute;
            left: 266px;
            background: #1a0000;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 6px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 20;
            white-space: nowrap;
        }

        /* Analytics page does not use collapsed sidebar; hide tooltips to avoid overflow artifacts. */
        .nav-item .tooltip-label { display: none; }

        .sidebar-footer {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }

        .main-area {
            flex: 1;
            min-width: 0;
            overflow-x: hidden;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #fff;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            width: 38px;
            height: 38px;
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
            width: 38px;
            height: 38px;
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
            background: #fee2e2;
            color: var(--red);
            border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        .darkmode-toggle:active { transform: scale(0.95); }

        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

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
            color: #991b1b;
        }

        .analytics-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid #fecaca;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: #fff;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.16);
            transition: transform .2s, box-shadow .2s, filter .2s;
        }

        .analytics-print-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.03);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.22);
        }

        .analytics-print-btn:focus-visible {
            outline: 3px solid rgba(220,38,38,.18);
            outline-offset: 2px;
        }

        body.dark-mode .topbar { background: #252525; border-bottom: 1px solid #3a3a3a; }
        body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
        body.dark-mode .topbar-title { color: #999; }
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
        body.dark-mode .topbar-badge { background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b; }

        .page {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 28px;
        }

        .heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }

        .heading h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        .heading h1 span { color: var(--red); }
        .heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .updated {
            font-size: 12px;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 999px;
            padding: 8px 12px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 20px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
            font-weight: 600;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 20px;
        }

        .icon-green { background: #dcfce7; color: #166534; }
        .icon-amber { background: #fef3c7; color: #92400e; }
        .icon-blue { background: #dbeafe; color: #1d4ed8; }
        .icon-purple { background: #ede9fe; color: #6d28d9; }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }

        .panel.full { grid-column: 1 / -1; }

        .panel-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--line);
            background: #fafafa;
        }

        .panel-head h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .panel-head p {
            margin: 4px 0 0;
            font-size: 12px;
            color: var(--muted);
        }

        .panel-body {
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .row-metric {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 10px;
        }

        .metric-label {
            font-size: 13px;
            font-weight: 600;
        }

        .metric-meta {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .metric-percent {
            font-size: 13px;
            font-weight: 700;
            color: #991b1b;
        }

        .track {
            width: 100%;
            height: 9px;
            border-radius: 999px;
            background: #eef1f5;
            overflow: hidden;
            margin-top: 7px;
        }

        .fill {
            height: 100%;
            border-radius: 999px;
        }

        .fill-green { background: linear-gradient(90deg, #22c55e, #15803d); }
        .fill-amber { background: linear-gradient(90deg, #f59e0b, #b45309); }
        .fill-red { background: linear-gradient(90deg, #ef4444, #b91c1c); }
        .fill-blue { background: linear-gradient(90deg, #60a5fa, #1d4ed8); }
        .fill-purple { background: linear-gradient(90deg, #a78bfa, #6d28d9); }
        .fill-teal { background: linear-gradient(90deg, #2dd4bf, #0f766e); }

        .month-row {
            display: grid;
            grid-template-columns: 96px 1fr;
            gap: 10px;
            align-items: start;
        }

        .month-label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            padding-top: 2px;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bar-row span {
            width: 55px;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
        }

        .bar-row .track { margin-top: 0; }
        #print-area-wrapper { display: none; }

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

        @media (max-width: 960px) {
            .app-layout {
                display: block;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            .topbar-title { display: none; }
            .analytics-grid { grid-template-columns: 1fr; }
            .panel.full { grid-column: auto; }
            .page { padding: 18px; }
            .dashboard-footer { padding: 18px; }
        }

        @media print {
            body > *:not(#print-area-wrapper) { display: none !important; }
            #print-area-wrapper { display: block !important; }
            #print-area-wrapper, #print-area-wrapper * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body { background: #fff !important; }
        }

        body.dark-mode .dashboard-footer {
            background: #1a1a1a;
            border-top: 1px solid #3a3a3a;
            color: #999;
        }

        body.dark-mode .dashboard-footer a { color: #999; }
        body.dark-mode .dashboard-footer a:hover { color: var(--red); }
        body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
        body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }

        /* Loading spinner for buttons */
        .btn[disabled] { opacity: 0.65; cursor: not-allowed; }
        .ic-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.25);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            vertical-align: middle;
            margin-left: 8px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <a href="{{ url('/dashboard') }}" class="sidebar-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name">Intern<span>Connect</span></span>
                    <span class="sidebar-brand-sub">OJTIMS</span>
                </div>
            </a>

            <a href="{{ url('/accountinfo') }}" class="sidebar-user">
                <div class="sidebar-avatar">
                    @if(isset($data->profile_photo) && $data->profile_photo)
                        <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
                    @else
                        <i class="fa fa-user-tie"></i>
                    @endif
                </div>
                <div>
                    <div class="sidebar-user-name">{{ $data->full_name ?? 'Coordinator' }}</div>
                    <div class="sidebar-user-role">OJT Coordinator</div>
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
                <a href="{{ url('/reports') }}" class="nav-item">
                    <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
                    <span class="nav-label">Reports</span>
                    <span class="tooltip-label">Reports</span>
                </a>
                <a href="{{ url('/analytics') }}" class="nav-item active">
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
        </aside>

        <div class="main-area">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" type="button" aria-label="Toggle sidebar">
                <i class="fa fa-bars"></i>
            </button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode" type="button">
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

    <main class="page">
        <section class="heading">
            <div>
                <h1>Live <span>Analytics</span></h1>
                <p>Operational visibility across students, requirements, and partner companies.</p>
            </div>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <button type="button" id="printBtn" aria-label="Print analytics report" class="analytics-print-btn">
                    <i class="fa fa-print"></i> Print Report
                </button>
                <div class="updated"><i class="fa fa-sync-alt" style="margin-right:6px;"></i> Updated {{ now()->format('M d, Y') }}</div>
            </div>
        </section>

        <section class="stats-grid">
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $approvedStudents }}</div>
                    <div class="stat-label">Approved Students</div>
                </div>
                <div class="stat-icon icon-green"><i class="fa fa-user-check"></i></div>
            </article>
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $pendingStudents }}</div>
                    <div class="stat-label">Pending Students</div>
                </div>
                <div class="stat-icon icon-amber"><i class="fa fa-hourglass-half"></i></div>
            </article>
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $placedStudents }}</div>
                    <div class="stat-label">Placed Students</div>
                </div>
                <div class="stat-icon icon-blue"><i class="fa fa-briefcase"></i></div>
            </article>
            <article class="stat-card">
                <div>
                    <div class="stat-value">{{ $partnerCompanies }}</div>
                    <div class="stat-label">Partner Companies</div>
                </div>
                <div class="stat-icon icon-purple"><i class="fa fa-building"></i></div>
            </article>
        </section>

        @if(!empty($analyticsInsights))
            <section class="panel" style="margin-top:18px; border-left:4px solid #dc2626;">
                <header class="panel-head">
                    <h2>AI Analytics Insight</h2>
                    <p>Summary generated from the current dashboard metrics</p>
                </header>
                <div class="panel-body">
                    <p style="font-size:14px; line-height:1.7; color:#374151; margin-bottom:16px;">{{ $analyticsInsights['summary'] ?? 'No insight available.' }}</p>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No key findings available.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#dc2626; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                            <ul style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['recommendations'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No actions suggested.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="analytics-grid">
            <article class="panel">
                <header class="panel-head">
                    <h2>Student Status Breakdown</h2>
                    <p>Current account state across the portal</p>
                </header>
                <div class="panel-body">
                    @foreach ($studentStatusAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'amber' => 'fill-amber',
                                'red' => 'fill-red',
                                default => 'fill-blue',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} total</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Requirement Status</h2>
                    <p>File review progress across uploads</p>
                </header>
                <div class="panel-body">
                    @foreach ($fileStatusAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'amber' => 'fill-amber',
                                default => 'fill-red',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} files</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Course Distribution</h2>
                    <p>Students grouped by course</p>
                </header>
                <div class="panel-body">
                    @forelse ($courseAnalytics as $course)
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $course['label'] }}</div>
                                    <div class="metric-meta">{{ $course['count'] }} students</div>
                                </div>
                                <div class="metric-percent">{{ $course['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill fill-teal" data-width="{{ $course['percentage'] }}"></div></div>
                        </div>
                    @empty
                        <div class="metric-meta">No course data available yet.</div>
                    @endforelse
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Top Partner Companies</h2>
                    <p>Companies with the most assigned students</p>
                </header>
                <div class="panel-body">
                    @forelse ($topCompanies as $company)
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $company['label'] }}</div>
                                    <div class="metric-meta">{{ $company['count'] }} students placed</div>
                                </div>
                                <div class="metric-percent">{{ $company['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill fill-purple" data-width="{{ $company['percentage'] }}"></div></div>
                        </div>
                    @empty
                        <div class="metric-meta">No company placement data available yet.</div>
                    @endforelse
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>Placement Coverage</h2>
                    <p>Current student placement progress across the portal</p>
                </header>
                <div class="panel-body">
                    @foreach ($placementAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'amber' => 'fill-amber',
                                default => 'fill-blue',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} students</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <header class="panel-head">
                    <h2>MOA Portfolio</h2>
                    <p>Health and assignment coverage of current MOA records</p>
                </header>
                <div class="panel-body">
                    @foreach ($moaStatusAnalytics as $stat)
                        @php
                            $fillClass = match($stat['class']) {
                                'green' => 'fill-green',
                                'red' => 'fill-red',
                                default => 'fill-amber',
                            };
                        @endphp
                        <div>
                            <div class="row-metric">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-meta">{{ $stat['count'] }} records</div>
                                </div>
                                <div class="metric-percent">{{ $stat['percentage'] }}%</div>
                            </div>
                            <div class="track"><div class="fill {{ $fillClass }}" data-width="{{ $stat['percentage'] }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </main>

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
    </div>

    <div id="print-area-wrapper"></div>

    <script>
        const analyticsPrintData = {
            updatedAt: @json(now()->format('M d, Y')),
            approvedStudents: @json($approvedStudents),
            pendingStudents: @json($pendingStudents),
            placedStudents: @json($placedStudents),
            partnerCompanies: @json($partnerCompanies),
            studentStatusAnalytics: @json($studentStatusAnalytics),
            fileStatusAnalytics: @json($fileStatusAnalytics),
            courseAnalytics: @json($courseAnalytics),
            topCompanies: @json($topCompanies),
            placementAnalytics: @json($placementAnalytics),
            moaStatusAnalytics: @json($moaStatusAnalytics),
            analyticsSummary: @json($analyticsInsights['summary'] ?? null),
        };

        const darkToggle = document.getElementById('darkmodeToggle');
        const darkIcon = document.getElementById('darkmodeIcon');
        const darkKey = 'internconnect_darkmode';

        const applyDarkMode = (isDark) => {
            document.body.classList.toggle('dark-mode', isDark);
            if (darkIcon) {
                darkIcon.className = isDark ? 'fa fa-sun' : 'fa fa-moon';
            }
        };

        const savedMode = localStorage.getItem(darkKey);
        applyDarkMode(savedMode === '1');

        if (darkToggle) {
            darkToggle.addEventListener('click', function () {
                const isDark = !document.body.classList.contains('dark-mode');
                applyDarkMode(isDark);
                localStorage.setItem(darkKey, isDark ? '1' : '0');
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildCoordinatorPrintHTML() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            const studentRows = (analyticsPrintData.studentStatusAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const fileRows = (analyticsPrintData.fileStatusAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const courseRows = (analyticsPrintData.courseAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const companyRows = (analyticsPrintData.topCompanies || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const placementRows = (analyticsPrintData.placementAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const moaRows = (analyticsPrintData.moaStatusAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            return `
                <div style="font-family:'Poppins',Arial,sans-serif; background:#fff; color:#111827;">
                    <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); padding:0;">
                        <div style="background:rgba(255,255,255,0.12); height:4px;"></div>
                        <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                            <div style="width:50px; height:50px; background:rgba(255,255,255,0.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,0.25);">
                                <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);" alt="PUP">
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines - OJT Information Management System</div>
                                <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">Coordinator Analytics Report</div>
                                <div style="font-size:8.5px; color:rgba(255,255,255,0.6); margin-top:3px;">Taguig Branch Campus | College of Engineering and Technology</div>
                            </div>
                        </div>
                        <div style="background:rgba(0,0,0,0.15); height:3px;"></div>
                    </div>

                    <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                            <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                                <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                                <span style="color:#6b7280;">Updated:</span>
                                <strong style="color:#111827;">${escapeHtml(analyticsPrintData.updatedAt)}</strong>
                            </div>
                            <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                                <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                                <span style="color:#6b7280;">Generated:</span>
                                <strong style="color:#111827;">${dateStr} ${timeStr}</strong>
                            </div>
                        </div>
                        <div style="font-size:8.5px; color:#9ca3af;">Coordinator dashboard summary</div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Approved Students</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.approvedStudents)}</div>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Pending Students</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.pendingStudents)}</div>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Placed Students</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.placedStudents)}</div>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Partner Companies</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.partnerCompanies)}</div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Student Status Breakdown</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${studentRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No student data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Requirement Status</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${fileRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No file data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Placement Coverage</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${placementRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No placement data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">MOA Portfolio</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${moaRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No MOA data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                            <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Course Distribution</div>
                            <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Course</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                <tbody>${courseRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No course data found.</td></tr>'}</tbody>
                            </table>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Top Partner Companies</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Company</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${companyRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No company data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Analytics Insight</div>
                                <div style="font-size:11px; color:#374151; line-height:1.7;">${escapeHtml(analyticsPrintData.analyticsSummary || 'This printed report focuses on student status, requirement review, placement coverage, MOA health, course distribution, and partner company coverage.')}</div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:18px 22px 12px 22px;">
                        <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                                <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Disclaimer</div>
                                <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</div>
                            </div>
                        </div>
                    </div>

                    <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:0.7; filter:brightness(2);" alt="PUP">
                            <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                        </div>
                        <div style="font-size:8px; color:rgba(255,255,255,0.5);">Ref: COORD-ANA-${now.getFullYear()}</div>
                    </div>
                </div>
            `;
        }

        document.getElementById('printBtn')?.addEventListener('click', () => {
            const wrapper = document.getElementById('print-area-wrapper');
            if (!wrapper) return;
            wrapper.innerHTML = buildCoordinatorPrintHTML();
            window.print();
            setTimeout(() => {
                wrapper.innerHTML = '';
            }, 1000);
        });

    </script>
</body>
</html>
