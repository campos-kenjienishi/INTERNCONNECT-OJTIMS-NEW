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
        }

        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .app-layout { display: block !important; }
            .page { padding: 0 !important; }
            .panel { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
            body { background: #fff !important; }
        }

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                <form action="{{ url('/logout') }}" method="post" style="margin:0;">
                    @csrf
                    <button type="submit" class="nav-item" style="width:100%; background:none; border:none; text-align:left; padding:0;">
                        <span class="nav-icon"><i class="fa fa-sign-out-alt"></i></span>
                        <span class="nav-label">Log Out</span>
                        <span class="tooltip-label">Log Out</span>
                    </button>
                </form>
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
            <div class="updated"><i class="fa fa-sync-alt" style="margin-right:6px;"></i> Updated {{ now()->format('M d, Y') }}</div>
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

            <article class="panel full">
                <header class="panel-head">
                    <h2>Monthly Activity</h2>
                    <p>Recent student registrations and file submissions</p>
                </header>
                <div class="panel-body">
                    <div class="no-print" style="width:100%; margin-bottom: 12px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                        <label style="font-size:13px;color:#666;">
                            From
                            <input type="month" id="startMonth" aria-label="Start month filter" style="margin-left:8px;padding:6px;border-radius:6px;border:1px solid #e5e7eb;">
                        </label>
                        <label style="font-size:13px;color:#666;">
                            To
                            <input type="month" id="endMonth" aria-label="End month filter" style="margin-left:8px;padding:6px;border-radius:6px;border:1px solid #e5e7eb;">
                        </label>
                        <button id="applyFilters" type="button" aria-label="Apply analytics filters" class="btn" style="background:#dc2626;color:#fff;border-radius:8px;padding:8px 12px;border:none;display:inline-flex;align-items:center;">
                            <span class="btn-label">Apply</span>
                            <span class="ic-spinner" style="display:none;"></span>
                        </button>
                        <button type="button" id="exportCsvBtn" aria-label="Export analytics as CSV" class="btn" style="background:#0f766e;color:#fff;border-radius:8px;padding:8px 12px;border:none;">CSV</button>
                        <button type="button" id="exportPdfBtn" aria-label="Export analytics as PDF" class="btn" style="background:#1d4ed8;color:#fff;border-radius:8px;padding:8px 12px;border:none;">PDF</button>
                        <button type="button" id="printBtn" aria-label="Print analytics report" class="btn" style="background:#6b7280;color:#fff;border-radius:8px;padding:8px 12px;border:none;">Print</button>
                    </div>
                    <div style="width:100%;">
                        <canvas id="monthlyActivityChart" role="img" aria-label="Monthly activity chart showing student registrations and file submissions" style="width:100%;height:260px;"></canvas>
                    </div>
                </div>
            </article>
        </section>
    </main>

        </div>
    </div>

    <script>
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

        /* Monthly Activity Chart */
        let monthlyChart = null;

        function createOrUpdateChart(labels, filesArray, studentsArray) {
            const ctx = document.getElementById('monthlyActivityChart');
            if (!ctx) return;

            const datasets = [
                {
                    label: 'Files Submitted',
                    data: filesArray,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Students Registered',
                    data: studentsArray,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }
            ];

            if (monthlyChart) {
                monthlyChart.data.labels = labels;
                monthlyChart.data.datasets = datasets;
                monthlyChart.update();
                return;
            }

            monthlyChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: { labels: labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } },
                    onClick: (evt, elems) => {
                        if (elems.length > 0) {
                            const idx = elems[0].index;
                            const label = labels[idx];
                            const [month, year] = label.split(' ');
                            const monthNum = new Date(Date.parse(month + ' 1')).getMonth() + 1;
                            window.drilldownYear = year;
                            window.drilldownMonth = monthNum;
                            openDrilldownModal(label, year, monthNum, 'files');
                        }
                    }
                }
            });
        }

        async function fetchMonthlyData(params = {}, opts = { showLoading: true }) {
            const applyBtn = document.getElementById('applyFilters');
            const spinner = applyBtn?.querySelector('.ic-spinner');
            if (opts.showLoading && applyBtn) { applyBtn.disabled = true; if (spinner) spinner.style.display = 'inline-block'; }
            const url = new URL("{{ route('coordinator.analytics.data') }}", window.location.origin);
            Object.keys(params).forEach(k => { if (params[k] !== undefined && params[k] !== null && params[k] !== '') url.searchParams.set(k, params[k]); });
            try {
                const res = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Failed to fetch');
                const json = await res.json();
                createOrUpdateChart(json.labels || [], json.files || [], json.students || []);
            } catch (e) {
                console.error('Monthly data load error', e);
            } finally {
                if (opts.showLoading && applyBtn) { applyBtn.disabled = false; if (spinner) spinner.style.display = 'none'; }
            }
        }

        // controls
        (function setupFilters() {
            const startInput = document.getElementById('startMonth');
            const endInput = document.getElementById('endMonth');
            const applyBtn = document.getElementById('applyFilters');

            // restore from localStorage
            try {
                const stored = JSON.parse(localStorage.getItem('coord_analytics_filters') || 'null');
                if (stored) {
                    if (stored.start && startInput) startInput.value = stored.start;
                    if (stored.end && endInput) endInput.value = stored.end;
                }
            } catch (e) { }

            applyBtn?.addEventListener('click', function () {
                const startMonth = startInput?.value || '';
                const endMonth = endInput?.value || '';
                try { localStorage.setItem('coord_analytics_filters', JSON.stringify({ start: startMonth, end: endMonth })); } catch (e) {}
                const start = startMonth ? startMonth + '-01' : '';
                const end = endMonth ? endMonth + '-01' : '';
                fetchMonthlyData({ start: start ? start : undefined, end: end ? end : undefined }, { showLoading: true });
            });
        })();

        // load initial data (use restored filters if any)
        (function initLoad() {
            try {
                const stored = JSON.parse(localStorage.getItem('coord_analytics_filters') || 'null') || {};
                const params = {};
                if (stored.start) params.start = stored.start + '-01';
                if (stored.end) params.end = stored.end + '-01';
                fetchMonthlyData(params, { showLoading: true });
            } catch (e) {
                fetchMonthlyData({}, { showLoading: true });
            }
        })();

        function buildCoordinatorExportUrl(type) {
            const startMonth = document.getElementById('startMonth')?.value || '';
            const endMonth = document.getElementById('endMonth')?.value || '';
            const routeBase = type === 'pdf' ? "{{ route('coordinator.analytics.export.pdf') }}" : "{{ route('coordinator.analytics.export.csv') }}";
            const url = new URL(routeBase, window.location.origin);
            if (startMonth) url.searchParams.set('start', startMonth + '-01');
            if (endMonth) url.searchParams.set('end', endMonth + '-01');
            return url.toString();
        }

        document.getElementById('exportCsvBtn')?.addEventListener('click', () => {
            window.location.href = buildCoordinatorExportUrl('csv');
        });

        document.getElementById('exportPdfBtn')?.addEventListener('click', () => {
            window.location.href = buildCoordinatorExportUrl('pdf');
        });

        document.getElementById('printBtn')?.addEventListener('click', () => window.print());

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && drilldownModal && drilldownModal.style.display === 'flex') {
                drilldownModal.style.display = 'none';
            }
        });

        // Drilldown modal functions
        const drilldownModal = document.getElementById('drilldownModal');
        let currentPage = 1;

        function setDrilldownControls(type) {
            const statusFilter = document.getElementById('drilldownStatusFilter');
            if (!statusFilter) return;

            if (type === 'files') {
                statusFilter.innerHTML = `
                    <option value="">All statuses</option>
                    <option value="1">Approved</option>
                    <option value="0">Pending</option>
                    <option value="2">Denied</option>
                `;
                statusFilter.disabled = false;
                statusFilter.style.display = 'inline-block';
            } else {
                statusFilter.innerHTML = '<option value="">All statuses</option>';
                statusFilter.disabled = true;
                statusFilter.style.display = 'none';
            }
        }

        function openDrilldownModal(label, year, month, type) {
            document.getElementById('drilldownTitle').textContent = (type === 'files' ? 'Files Submitted' : 'Students Registered') + ' - ' + label;
            currentPage = 1;
            setDrilldownControls(type);
            fetchDrilldownData(year, month, 1, type);
            drilldownModal.style.display = 'flex';
        }

        async function fetchDrilldownData(year, month, page, type) {
            const url = new URL("{{ route('coordinator.analytics.drilldown') }}", window.location.origin);
            const status = document.getElementById('drilldownStatusFilter')?.value || '';
            const queryText = document.getElementById('drilldownSearch')?.value?.trim() || '';
            url.searchParams.set('year', year);
            url.searchParams.set('month', month);
            url.searchParams.set('type', type);
            if (status) url.searchParams.set('status', status);
            if (queryText) url.searchParams.set('q', queryText);
            url.searchParams.set('page', page);

            try {
                const res = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Failed to fetch');
                const json = await res.json();
                renderDrilldownTable(json, type);
            } catch (e) {
                console.error('Drilldown fetch error', e);
            }
        }

        function renderDrilldownTable(data, type) {
            const tbody = document.getElementById('drilldownTableBody');
            tbody.innerHTML = '';

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:16px;">No records found</td></tr>';
                return;
            }

            data.data.forEach(item => {
                const row = document.createElement('tr');
                if (type === 'files') {
                    row.innerHTML = `
                        <td style="padding:8px;">${item.file_name}</td>
                        <td style="padding:8px;">${item.adviser || '-'}</td>
                        <td style="padding:8px;"><span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:4px;font-size:11px;">${item.status === 1 ? 'Approved' : item.status === 0 ? 'Pending' : 'Denied'}</span></td>
                        <td style="padding:8px;font-size:12px;color:#666;">${new Date(item.created_at).toLocaleDateString()}</td>
                    `;
                } else {
                    row.innerHTML = `
                        <td style="padding:8px;">${item.first_name} ${item.last_name}</td>
                        <td style="padding:8px;">${item.course || '-'}</td>
                        <td style="padding:8px;"><span style="background:#dbeafe;color:#2563eb;padding:2px 8px;border-radius:4px;font-size:11px;">Registered</span></td>
                        <td style="padding:8px;font-size:12px;color:#666;">${new Date(item.created_at).toLocaleDateString()}</td>
                    `;
                }
                tbody.appendChild(row);
            });

            document.getElementById('drilldownPaginationInfo').textContent = `Page ${data.current_page} of ${Math.ceil(data.total / data.per_page)}`;
            document.getElementById('drilldownPrevBtn').disabled = data.current_page === 1;
            document.getElementById('drilldownNextBtn').disabled = data.current_page >= Math.ceil(data.total / data.per_page);
            currentPage = data.current_page;
            window.lastDrilldownType = type;
        }

        document.getElementById('drilldownCloseBtn')?.addEventListener('click', () => {
            drilldownModal.style.display = 'none';
        });

        document.getElementById('drilldownPrevBtn')?.addEventListener('click', () => {
            if (currentPage > 1) fetchDrilldownData(window.drilldownYear, window.drilldownMonth, currentPage - 1, window.lastDrilldownType);
        });

        document.getElementById('drilldownNextBtn')?.addEventListener('click', () => {
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, currentPage + 1, window.lastDrilldownType);
        });

        document.getElementById('drilldownSearchBtn')?.addEventListener('click', () => {
            currentPage = 1;
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1, window.lastDrilldownType);
        });

        document.getElementById('drilldownSearch')?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                currentPage = 1;
                fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1, window.lastDrilldownType);
            }
        });

        document.getElementById('drilldownStatusFilter')?.addEventListener('change', () => {
            currentPage = 1;
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1, window.lastDrilldownType);
        });

        window.addEventListener('click', (e) => {
            if (e.target === drilldownModal) drilldownModal.style.display = 'none';
        });
    </script>

<!-- Drilldown Modal -->
<div id="drilldownModal" role="dialog" aria-modal="true" aria-labelledby="drilldownTitle" tabindex="-1" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;z-index:9999;">
    <div style="background:#fff;border-radius:12px;width:90%;max-width:700px;max-height:80vh;overflow:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px;border-bottom:1px solid #e5e7eb;">
            <h3 id="drilldownTitle" style="margin:0;font-size:16px;font-weight:600;">Details</h3>
            <button id="drilldownCloseBtn" type="button" aria-label="Close details dialog" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;">&times;</button>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;padding:12px 16px;border-bottom:1px solid #e5e7eb;align-items:center;">
            <input id="drilldownSearch" type="text" aria-label="Search drilldown records" placeholder="Search file, adviser, or student" style="flex:1;min-width:220px;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;">
            <select id="drilldownStatusFilter" aria-label="Filter drilldown status" style="padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;min-width:160px;">
                <option value="">All statuses</option>
                <option value="1">Approved</option>
                <option value="0">Pending</option>
                <option value="2">Denied</option>
            </select>
            <button id="drilldownSearchBtn" type="button" aria-label="Search drilldown records" style="background:#dc2626;color:#fff;border:none;border-radius:6px;padding:8px 12px;cursor:pointer;">Search</button>
        </div>
        <div style="padding:16px;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f5f5f5;">
                        <th style="padding:8px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb;">Name</th>
                        <th style="padding:8px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb;">Company/Course</th>
                        <th style="padding:8px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb;">Status</th>
                        <th style="padding:8px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb;">Date</th>
                    </tr>
                </thead>
                <tbody id="drilldownTableBody"></tbody>
            </table>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid #e5e7eb;">
            <span id="drilldownPaginationInfo" style="font-size:12px;color:#666;"></span>
            <div style="display:flex;gap:8px;">
                <button id="drilldownPrevBtn" type="button" aria-label="Previous page" style="background:#f5f5f5;border:1px solid #ddd;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;">← Prev</button>
                <button id="drilldownNextBtn" type="button" aria-label="Next page" style="background:#f5f5f5;border:1px solid #ddd;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;">Next →</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
