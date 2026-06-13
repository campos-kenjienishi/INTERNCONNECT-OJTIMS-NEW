<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Professor Analytics</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red: #dc2626;
            --red-dark: #991b1b;
            --red-deeper: #7f0000;
            --sidebar-w: 260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h: 64px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
        }

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
            padding: 12px 20px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all 0.25s; position: relative;
            white-space: nowrap; border-left: 3px solid transparent;
        }

        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(239,68,68,0.15); border-left-color: #ef4444; }
        .nav-item .nav-icon { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }
        .nav-item .nav-label { transition: opacity 0.25s; overflow: hidden; }
        .sidebar.collapsed .nav-label { opacity: 0; width: 0; }
        .sidebar.collapsed { width: var(--sidebar-w-collapsed); }

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
        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red-dark);
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
        body.dark-mode .topbar-badge { background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b; }

        .page-content { padding: 28px; flex: 1; }
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 { font-size: 26px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
            .page-header p { font-size: 13.5px; color: #888; margin-top: 4px; }
        .analytics-print {
            background: #fff;
            border: 1px solid rgba(0,0,0,.05);
            border-radius: 10px;
            box-shadow: 0 2px 14px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        .breadcrumb a { color: var(--red); text-decoration: none; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px 22px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            text-decoration: none; color: inherit;
        }
        .stat-card-left { flex: 1; }
        .stat-num { font-size: 28px; font-weight: 800; color: #1a1a1a; line-height: 1; }
        .stat-name { font-size: 13px; color: #888; margin-top: 4px; font-weight: 500; }
        .stat-icon-box {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .stat-icon-box.red { background: #fee2e2; color: var(--red); }
        .stat-icon-box.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon-box.green { background: #dcfce7; color: #16a34a; }
        .stat-icon-box.amber { background: #fef9c3; color: #ca8a04; }
        .stat-icon-box.purple { background: #ede9fe; color: #7c3aed; }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .panel {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .panel.full { grid-column: 1 / -1; }
        .panel-head {
            padding: 18px 22px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }
        .panel-head h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .panel-head p { font-size: 12.5px; color: #888; margin-top: 3px; }
        .panel-body { padding: 20px 22px; }
        .metric-list { display: flex; flex-direction: column; gap: 14px; }
        .metric-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .metric-title { font-size: 13px; font-weight: 600; color: #333; }
        .metric-meta { font-size: 12px; color: #777; margin-top: 2px; }
        .metric-percent { font-size: 13px; font-weight: 700; color: #991b1b; }
        .track {
            width: 100%; height: 10px; border-radius: 999px; background: #eef1f5;
            overflow: hidden; margin-top: 8px;
        }
        .fill { height: 100%; border-radius: 999px; }
        .fill.green { background: linear-gradient(90deg, #22c55e, #15803d); }
        .fill.red { background: linear-gradient(90deg, #ef4444, #b91c1c); }
        .fill.blue { background: linear-gradient(90deg, #60a5fa, #1d4ed8); }
        .fill.amber { background: linear-gradient(90deg, #f59e0b, #b45309); }
        .fill.purple { background: linear-gradient(90deg, #a78bfa, #6d28d9); }

        #print-area-wrapper { display: none; }

        .month-row {
            display: grid;
            grid-template-columns: 96px 1fr;
            gap: 12px;
            align-items: start;
        }
        .month-label { font-size: 12px; font-weight: 700; color: #374151; padding-top: 2px; }
        .bar-row { display: flex; align-items: center; gap: 10px; }
        .bar-row span { width: 62px; font-size: 11px; font-weight: 600; color: #777; }
        .bar-row .track { margin-top: 0; }

        @media print {
            @page { size: A4 portrait; margin: 14mm; }
            body > *:not(#print-area-wrapper) { display: none !important; }
            #print-area-wrapper { display: block !important; }
            #print-area-wrapper, #print-area-wrapper * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body { background: #fff !important; }
            .analytics-print { color: #111827; font-family: 'Poppins', sans-serif; }
            .analytics-print * { box-sizing: border-box; }
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
        .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        pointer-events: none;
    }
    .sidebar-overlay.active {
        display: block;
        pointer-events: auto;
    }
    </style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div> 
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
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-check"></i></span>
            <span class="nav-label">Req. Status</span>
            <span class="tooltip-label">Req. Status</span>
        </a>
        <a href="{{ url('/professor/analytics') }}" class="nav-item active">
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

<div class="main-content" id="mainContent">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode"><i class="fa fa-moon" id="darkmodeIcon"></i></button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge"><i class="fa fa-chalkboard-teacher"></i> Professor Portal</div>
        </div>
    </div>

    <div class="page-content">
        <div class="page-header">
            <div>
                <h1>Professor <span>Analytics</span></h1>
                <p>Advising load, student standing, and file requirement trends for your advisees.</p>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right" style="font-size:10px;"></i>
                    <span>Analytics</span>
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:6px;">
                <button type="button" id="printBtn" aria-label="Print analytics report" class="analytics-print-btn">
                    <i class="fa fa-print"></i>
                    <span>Print Report</span>
                </button>
                <div class="topbar-badge">
                    <i class="fa fa-sync-alt"></i> Updated {{ now()->format('M d, Y') }}
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $totalStudents }}</div>
                    <div class="stat-name">Total Advisees</div>
                </div>
                <div class="stat-icon-box red"><i class="fa fa-users"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $classrooms->count() }}</div>
                    <div class="stat-name">Active Classes</div>
                </div>
                <div class="stat-icon-box blue"><i class="fa fa-clipboard"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $approvedStudents }}</div>
                    <div class="stat-name">Approved Students</div>
                </div>
                <div class="stat-icon-box green"><i class="fa fa-user-check"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $pendingApprovals }}</div>
                    <div class="stat-name">Pending Students</div>
                </div>
                <div class="stat-icon-box amber"><i class="fa fa-hourglass-half"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $deniedStudents }}</div>
                    <div class="stat-name">Denied Students</div>
                </div>
                <div class="stat-icon-box red"><i class="fa fa-user-times"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $templateCount }}</div>
                    <div class="stat-name">File Categories</div>
                </div>
                <div class="stat-icon-box purple"><i class="fa fa-folder-open"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $fileApproved }}</div>
                    <div class="stat-name">Approved Files</div>
                </div>
                <div class="stat-icon-box green"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $filePending }}</div>
                    <div class="stat-name">Pending Files</div>
                </div>
                <div class="stat-icon-box amber"><i class="fa fa-file-alt"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-left">
                    <div class="stat-num">{{ $fileDenied }}</div>
                    <div class="stat-name">Denied Files</div>
                </div>
                <div class="stat-icon-box red"><i class="fa fa-times-circle"></i></div>
            </div>
        </div>

        <div class="analytics-grid">
            <div class="panel">
                <div class="panel-head">
                    <h2>Student Standing</h2>
                    <p>Adviser-side breakdown of student approval status</p>
                </div>
                <div class="panel-body">
                    <div class="metric-list">
                        @php
                            $studentMetrics = [
                                ['label' => 'Approved students', 'count' => $approvedStudents, 'class' => 'green'],
                                ['label' => 'Pending students', 'count' => $pendingApprovals, 'class' => 'amber'],
                                ['label' => 'Denied students', 'count' => $deniedStudents, 'class' => 'red'],
                                ['label' => 'Inactive students', 'count' => $inactiveStudents, 'class' => 'blue'],
                            ];
                            $studentTotal = max(1, $approvedStudents + $pendingApprovals + $deniedStudents + $inactiveStudents);
                        @endphp
                        @foreach($studentMetrics as $metric)
                            <div>
                                <div class="metric-row">
                                    <div>
                                        <div class="metric-title">{{ $metric['label'] }}</div>
                                        <div class="metric-meta">{{ $metric['count'] }} students</div>
                                    </div>
                                    <div class="metric-percent">{{ round(($metric['count'] / $studentTotal) * 100) }}%</div>
                                </div>
                                <div class="track"><div class="fill {{ $metric['class'] }}" data-width="{{ round(($metric['count'] / $studentTotal) * 100) }}"></div></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h2>Class Overview</h2>
                    <p>Student load and submitted evaluation activity per class</p>
                </div>
                <div class="panel-body">
                    <div class="metric-list">
                        @forelse($classAnalytics as $room)
                            <div>
                                <div class="metric-row">
                                    <div>
                                        <div class="metric-title">{{ $room['label'] }}</div>
                                        <div class="metric-meta">{{ $room['total_students'] }} students | {{ $room['submitted'] }} submitted</div>
                                    </div>
                                    <div class="metric-percent">{{ $room['completion'] }}%</div>
                                </div>
                                <div class="track"><div class="fill green" data-width="{{ $room['completion'] }}"></div></div>
                            </div>
                        @empty
                            <div class="metric-meta">No classes found for your account.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h2>Requirement Review</h2>
                    <p>File requirement statuses for your advisees</p>
                </div>
                <div class="panel-body">
                    <div class="metric-list">
                        @php
                            $fileMetrics = [
                                ['label' => 'Approved files', 'count' => $fileApproved, 'class' => 'green'],
                                ['label' => 'Pending files', 'count' => $filePending, 'class' => 'amber'],
                                ['label' => 'Denied files', 'count' => $fileDenied, 'class' => 'red'],
                            ];
                            $fileTotal = max(1, $fileApproved + $filePending + $fileDenied);
                        @endphp
                        @foreach($fileMetrics as $metric)
                            <div>
                                <div class="metric-row">
                                    <div>
                                        <div class="metric-title">{{ $metric['label'] }}</div>
                                        <div class="metric-meta">{{ $metric['count'] }} files</div>
                                    </div>
                                    <div class="metric-percent">{{ round(($metric['count'] / $fileTotal) * 100) }}%</div>
                                </div>
                                <div class="track"><div class="fill {{ $metric['class'] }}" data-width="{{ round(($metric['count'] / $fileTotal) * 100) }}"></div></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div data-ai-insight-card class="panel full">
                <div class="panel-head" style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa fa-robot"></i>
                        </div>
                        <div>
                            <h2>AI Analytics Insight</h2>
                            <p>Summary generated from the current dashboard metrics</p>
                        </div>
                    </div>
                    @php
                        $analyticsAiSource = $analyticsInsights['source'] ?? 'fallback';
                        $analyticsAiLabel = $analyticsAiSource === 'gemini'
                            ? 'Gemini AI'
                            : ($analyticsAiSource === 'openai' ? 'OpenAI' : 'Internal Insight');
                    @endphp
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-left:auto;">
                        <button type="button" data-ai-insight-button data-ai-context="analyticsAiContext" data-ai-endpoint="{{ route('reports.ai.insight') }}" data-ai-token="{{ csrf_token() }}" style="display:inline-flex; align-items:center; gap:7px; border:none; background:#dc2626; color:#fff; border-radius:10px; padding:9px 13px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer;">
                            <i class="fa fa-magic"></i> Generate AI Insight
                        </button>
                        <span style="display:inline-flex; align-items:center; gap:7px; border:1px solid #fecaca; background:#fff5f5; color:#b91c1c; border-radius:999px; padding:8px 13px; font-size:12px; font-weight:800;">
                            <i class="fa fa-brain"></i>
                            <span data-ai-badge>{{ $analyticsAiLabel }}</span>
                        </span>
                    </div>
                </div>
                <div data-ai-result-panel class="panel-body" style="display:none;">
                    @if(($analyticsInsights['source'] ?? '') === 'fallback')
                        <div data-ai-notice style="display:flex; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;">
                            <i class="fa fa-exclamation-triangle" style="margin-top:2px;"></i>
                            <div><strong>Gemini is temporarily unavailable.</strong> <span data-ai-notice-text>{{ $analyticsInsights['availability']['message'] ?? 'Internal insight is shown for now. Try again in a few minutes, or later if the daily free-tier quota was reached.' }}</span></div>
                        </div>
                    @endif
                    <div data-ai-status style="display:none; margin-bottom:12px; font-size:12px; color:#888;"></div>
                    <p data-ai-summary style="font-size:14px; line-height:1.7; color:#374151; margin-bottom:16px;">{{ $analyticsInsights['summary'] ?? 'No insight available.' }}</p>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#ef4444; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                            <ul data-ai-findings style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['key_findings'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No key findings available.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#ef4444; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                            <ul data-ai-watchouts style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['watchouts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No major watchouts detected.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:14px;">
                            <div style="font-size:12px; font-weight:700; color:#ef4444; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                            <ul data-ai-actions style="margin:0; padding-left:18px; color:#374151; line-height:1.65;">
                                @forelse(($analyticsInsights['recommendations'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>No actions suggested.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    @php
                        $analyticsPromptSuggestions = [
                            ['label' => 'Priorities', 'question' => 'What should I prioritize first based on this professor analytics dashboard?'],
                            ['label' => 'Risk', 'question' => 'What risk level does this advising dashboard suggest and why?'],
                            ['label' => 'Action plan', 'question' => 'Create a short action plan based on the current professor analytics metrics.'],
                        ];

                        if (($pendingApprovals ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Pending approvals', 'question' => 'How should I handle the pending student approvals shown here?'];
                        }

                        if (($filePending ?? 0) > 0) {
                            $analyticsPromptSuggestions[] = ['label' => 'Pending files', 'question' => 'What should I do about pending file requirements?'];
                        }

                        if (($requestTotal ?? 0) > 0 && ($submittedRequests ?? 0) < ($requestTotal ?? 0)) {
                            $analyticsPromptSuggestions[] = ['label' => 'Evaluations', 'question' => 'What does the evaluation completion data suggest I should follow up on?'];
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
                                    @foreach($analyticsPromptSuggestions as $suggestion)
                                        <button type="button" class="analytics-ai-quick-question" data-question="{{ $suggestion['question'] }}" style="display:inline-flex; align-items:center; gap:7px; border:1.5px solid #fecaca; background:#fff; color:#991b1b; border-radius:9px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:800; cursor:pointer; box-shadow:0 2px 8px rgba(220,38,38,0.08);"><i class="fa fa-bolt" style="width:18px; height:18px; border-radius:6px; background:#fee2e2; display:inline-flex; align-items:center; justify-content:center; font-size:10px; color:#dc2626;"></i>{{ $suggestion['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div style="display:grid; gap:10px;">
                            <textarea id="analyticsAiQuestionInput" rows="3" maxlength="500" placeholder="Ask a question about this analytics dashboard..." style="width:100%; resize:vertical; min-height:86px; border:1.5px solid #e5e7eb; border-radius:10px; padding:12px 14px; font-family:'Poppins',sans-serif; font-size:13px; outline:none; line-height:1.6;"></textarea>
                            <button type="button" id="analyticsAskAiBtn" style="justify-self:end; display:inline-flex; align-items:center; gap:8px; border:none; border-radius:10px; background:linear-gradient(135deg,#dc2626,#991b1b); color:#fff; padding:11px 18px; font-size:13px; font-weight:800; white-space:nowrap;">
                                <i class="fa fa-paper-plane"></i> Ask
                            </button>
                        </div>
                        <div id="analyticsAiAskStatus" style="display:none; margin-top:10px; font-size:12px; color:#888;"></div>
                        <div id="analyticsAiAnswerBox" style="display:none; margin-top:12px; background:#fff; border:1px solid #eee; border-radius:12px; padding:14px;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; margin-bottom:8px;">
                                <div style="font-size:12px; font-weight:800; color:#dc2626; text-transform:uppercase; letter-spacing:.4px;">AI Answer</div>
                                <div id="analyticsAiAnswerSource" style="font-size:11px; font-weight:700; color:#888;"></div>
                            </div>
                            <div id="analyticsAiAnswerText" style="font-size:13px; color:#333; line-height:1.7;"></div>
                            <ul id="analyticsAiAnswerSteps" style="margin:10px 0 0; padding-left:18px; color:#444; font-size:13px; line-height:1.65;"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $analyticsPrintStudentMetrics = [
        ['label' => 'Approved students', 'count' => $approvedStudents, 'class' => 'green'],
        ['label' => 'Pending students', 'count' => $pendingApprovals, 'class' => 'amber'],
        ['label' => 'Denied students', 'count' => $deniedStudents, 'class' => 'red'],
        ['label' => 'Inactive students', 'count' => $inactiveStudents, 'class' => 'blue'],
    ];
    $analyticsPrintFileMetrics = [
        ['label' => 'Approved files', 'count' => $fileApproved, 'class' => 'green'],
        ['label' => 'Pending files', 'count' => $filePending, 'class' => 'amber'],
        ['label' => 'Denied files', 'count' => $fileDenied, 'class' => 'red'],
    ];
@endphp

<div id="print-area-wrapper">
    <div class="analytics-print">
        <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); color:#fff;">
            <div style="height:4px; background:rgba(255,255,255,.16);"></div>
            <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px 22px; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; gap:14px; min-width:0;">
                    <div style="width:50px; height:50px; border-radius:10px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP" style="width:36px; height:36px; object-fit:contain;">
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:rgba(255,255,255,.6); margin-bottom:3px;">Polytechnic University of the Philippines - OJT Information Management System</div>
                        <div style="font-size:15px; font-weight:800; line-height:1.15;">Professor Analytics Report</div>
                        <div style="font-size:8.5px; color:rgba(255,255,255,.68); margin-top:3px;">{{ $data->full_name }} | Professor Portal</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding:10px 22px; border-bottom:1px solid #e5e7eb; background:#f8f9fa; display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; flex-wrap:wrap; gap:14px; font-size:9.5px; color:#374151;">
                <div><span style="color:#6b7280;">Classes:</span> <strong>{{ $classrooms->count() }}</strong></div>
                <div><span style="color:#6b7280;">Students:</span> <strong>{{ $totalStudents }}</strong></div>
                <div><span style="color:#6b7280;">Approved:</span> <strong>{{ $approvedStudents }}</strong></div>
                <div><span style="color:#6b7280;">File Categories:</span> <strong>{{ $templateCount }}</strong></div>
            </div>
            <div style="font-size:8.5px; color:#9ca3af;">Generated: {{ now()->format('M d, Y h:i A') }}</div>
        </div>

        <div style="padding:8px 22px 4px;">
            <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">Professor Snapshot</div>
        </div>

        <div style="padding:14px 22px 22px;">
            <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; margin-bottom:16px;">
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:10px; padding:10px 12px;">
                        <div style="font-size:16px; font-weight:800; color:#111827;">{{ $totalStudents }}</div>
                        <div style="font-size:8px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Total Advisees</div>
                    </div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #2563eb; border-radius:10px; padding:10px 12px;">
                        <div style="font-size:16px; font-weight:800; color:#111827;">{{ $classrooms->count() }}</div>
                        <div style="font-size:8px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Active Classes</div>
                    </div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #16a34a; border-radius:10px; padding:10px 12px;">
                        <div style="font-size:16px; font-weight:800; color:#111827;">{{ $approvedStudents }}</div>
                        <div style="font-size:8px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Approved Students</div>
                    </div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #7c3aed; border-radius:10px; padding:10px 12px;">
                        <div style="font-size:16px; font-weight:800; color:#111827;">{{ $templateCount }}</div>
                        <div style="font-size:8px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">File Categories</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px; margin-bottom:14px;">
                    <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px;">
                        <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px;">Student Standing</div>
                        <table style="width:100%; border-collapse:collapse; font-size:10px;">
                            <thead>
                                <tr style="background:#f9fafb;">
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th>
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyticsPrintStudentMetrics as $metric)
                                    <tr>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $metric['label'] }}</td>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $metric['count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px;">
                        <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px;">Class Overview</div>
                        <table style="width:100%; border-collapse:collapse; font-size:10px;">
                            <thead>
                                <tr style="background:#f9fafb;">
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Class</th>
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th>
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Submitted</th>
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classAnalytics as $room)
                                    <tr>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $room['label'] }}</td>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $room['total_students'] }}</td>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $room['submitted'] }}</td>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $room['completion'] }}%</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" style="padding:8px; border:1px solid #e5e7eb; text-align:center;">No classes found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px;">
                    <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px;">
                        <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px;">Requirement Review</div>
                        <table style="width:100%; border-collapse:collapse; font-size:10px;">
                            <thead>
                                <tr style="background:#f9fafb;">
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th>
                                    <th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyticsPrintFileMetrics as $metric)
                                    <tr>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $metric['label'] }}</td>
                                        <td style="padding:7px 8px; border:1px solid #e5e7eb;">{{ $metric['count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px;">
                        <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px;">Analytics Insight</div>
                        <p style="font-size:11px; line-height:1.7; color:#374151; margin-bottom:10px;">{{ $analyticsInsights['summary'] ?? 'No insight available.' }}</p>
                        <div style="font-size:10px; color:#6b7280; line-height:1.6;">This report focuses on the current adviser snapshot, student standing, class overview, and file requirements.</div>
                    </div>
                </div>

                <div style="margin-top:16px; border-top:1px dashed #d1d5db; padding-top:14px;">
                    <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                        <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Disclaimer</div>
                        <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</div>
                    </div>
                    <div style="margin-top:10px; background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP" style="width:13px; height:13px; object-fit:contain; opacity:.7;">
                            <span style="font-size:8px; color:rgba(255,255,255,.75); font-weight:500;">Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                        </div>
                        <div style="font-size:8px; color:rgba(255,255,255,.5);">Ref: ANA-RPT-{{ now()->year }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
   // Replace your existing sidebar toggle script
(function () {
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    if (!sidebar || !menuToggle) return;

    menuToggle.addEventListener('click', function () {
        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            if (overlay) overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
        }
    });

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    /* Close sidebar when resizing back to desktop */
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('mobile-open');
            if (overlay) overlay.classList.remove('active');
        }
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        // Prepare data from Blade collection
        // Chart instance
        let monthlyChart = null;

        function createOrUpdateChart(labels, sentData, submittedData) {
            const ctx = document.getElementById('monthlyActivityChart');
            if (!ctx) return;

            const datasets = [
                {
                    label: 'Evaluation Requests Sent',
                    data: sentData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.08)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                },
                {
                    label: 'Evaluation Responses Submitted',
                    data: submittedData,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(16,163,74,0.08)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                }
            ];

            if (monthlyChart) {
                monthlyChart.data.labels = labels;
                monthlyChart.data.datasets[0].data = sentData;
                monthlyChart.data.datasets[1].data = submittedData;
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
                            openDrilldownModal(label, year, monthNum);
                        }
                    }
                }
            });
        }

        async function fetchMonthlyData(params = {}, opts = { showLoading: true }) {
            const applyBtn = document.getElementById('applyFilters');
            const spinner = applyBtn?.querySelector('.ic-spinner');
            if (opts.showLoading && applyBtn) { applyBtn.disabled = true; if (spinner) spinner.style.display = 'inline-block'; }
            const url = new URL("{{ route('professor.analytics.data') }}", window.location.origin);
            Object.keys(params).forEach(k => { if (params[k] !== undefined && params[k] !== null && params[k] !== '') url.searchParams.set(k, params[k]); });
            try {
                const res = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Failed to fetch');
                const json = await res.json();
                createOrUpdateChart(json.labels || [], json.sent || [], json.submitted || []);
            } catch (e) {
                console.error('Monthly data load error', e);
            } finally {
                if (opts.showLoading && applyBtn) { applyBtn.disabled = false; if (spinner) spinner.style.display = 'none'; }
            }
        }

        // controls
        (function setupFilters() {
            const classFilter = document.getElementById('classFilter');
            const startInput = document.getElementById('startMonth');
            const endInput = document.getElementById('endMonth');
            const applyBtn = document.getElementById('applyFilters');

            // restore from localStorage
            try {
                const stored = JSON.parse(localStorage.getItem('prof_analytics_filters') || 'null');
                if (stored) {
                    if (stored.classId && classFilter) classFilter.value = stored.classId;
                    if (stored.start && startInput) startInput.value = stored.start;
                    if (stored.end && endInput) endInput.value = stored.end;
                }
            } catch (e) { }

            applyBtn?.addEventListener('click', function () {
                const classId = classFilter?.value || '';
                const startMonth = startInput?.value || '';
                const endMonth = endInput?.value || '';
                try { localStorage.setItem('prof_analytics_filters', JSON.stringify({ classId: classId, start: startMonth, end: endMonth })); } catch (e) {}
                const start = startMonth ? startMonth + '-01' : '';
                const end = endMonth ? endMonth + '-01' : '';
                fetchMonthlyData({ class_id: classId, start: start ? start : undefined, end: end ? end : undefined }, { showLoading: true });
            });
        })();

        // load initial data (use restored filters if any)
        (function initLoad() {
            try {
                const stored = JSON.parse(localStorage.getItem('prof_analytics_filters') || 'null') || {};
                const params = {};
                if (stored.classId) params.class_id = stored.classId;
                if (stored.start) params.start = stored.start + '-01';
                if (stored.end) params.end = stored.end + '-01';
                fetchMonthlyData(params, { showLoading: true });
            } catch (e) {
                fetchMonthlyData({}, { showLoading: true });
            }
        });

        function escapeHtml(text) {
            return String(text ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        const analyticsPrintData = {
            title: 'Professor Analytics Report',
            subtitle: @json($data->full_name),
            summary: @json($analyticsInsights['summary'] ?? 'No insight available.'),
            generatedAt: @json(now()->format('M d, Y h:i A')),
            totalStudents: @json($totalStudents),
            classCount: @json($classrooms->count()),
            submittedRequests: @json($submittedRequests),
            templateCount: @json($templateCount),
            classAnalytics: @json($classAnalytics),
            requestAnalytics: @json($requestAnalytics),
            fileMetrics: @json($analyticsPrintFileMetrics),
        };

        window.analyticsAiContext = {
            report_type: 'professor_analytics',
            metrics: {
                total_students: @json($totalStudents),
                approved_students: @json($approvedStudents),
                pending_approvals: @json($pendingApprovals),
                denied_students: @json($deniedStudents),
                inactive_students: @json($inactiveStudents),
                request_total: @json($requestTotal),
                submitted_requests: @json($submittedRequests),
                template_count: @json($templateCount),
                file_pending: @json($filePending),
                file_approved: @json($fileApproved),
                file_denied: @json($fileDenied),
                class_analytics: @json($classAnalytics),
                request_analytics: @json($requestAnalytics),
                monthly_activity: @json($monthlyActivity)
            },
            insight: @json($analyticsInsights ?? [])
        };

        const analyticsAiContext = window.analyticsAiContext;

        function renderAnalyticsAiAnswer(data) {
            const answerBox = document.getElementById('analyticsAiAnswerBox');
            const answerText = document.getElementById('analyticsAiAnswerText');
            const answerSteps = document.getElementById('analyticsAiAnswerSteps');
            const answerSource = document.getElementById('analyticsAiAnswerSource');

            if (!answerBox || !answerText || !answerSteps || !answerSource) return;

            answerText.textContent = data.answer || 'No answer was generated.';
            answerSource.textContent = data.source === 'gemini' ? 'Gemini AI' : (data.source === 'openai' ? 'OpenAI' : 'Internal Insight');
            answerSteps.innerHTML = '';
            (data.next_steps || []).forEach(function (step) {
                const li = document.createElement('li');
                li.textContent = step;
                answerSteps.appendChild(li);
            });
            answerBox.style.display = 'block';
        }

        function askAnalyticsAi(question) {
            const status = document.getElementById('analyticsAiAskStatus');
            const button = document.getElementById('analyticsAskAiBtn');

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
                    report_type: analyticsAiContext.report_type,
                    metrics: analyticsAiContext.metrics,
                    insight: analyticsAiContext.insight
                })
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('AI request failed.');
                    return response.json();
                })
                .then(function (data) {
                    renderAnalyticsAiAnswer(data);
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

        document.querySelectorAll('.analytics-ai-quick-question').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = document.getElementById('analyticsAiQuestionInput');
                const question = btn.getAttribute('data-question') || '';
                if (input) input.value = question;
                askAnalyticsAi(question);
            });
        });

        const analyticsAskAiBtn = document.getElementById('analyticsAskAiBtn');
        if (analyticsAskAiBtn) {
            analyticsAskAiBtn.addEventListener('click', function () {
                const input = document.getElementById('analyticsAiQuestionInput');
                askAnalyticsAi(input ? input.value : '');
            });
        }

        const analyticsAiCard = document.querySelector('[data-ai-insight-card]');
        const analyticsPageHeader = document.querySelector('.page-header');
        if (analyticsAiCard && analyticsPageHeader) {
            analyticsAiCard.style.marginTop = '0';
            analyticsAiCard.style.marginBottom = '24px';
            analyticsPageHeader.insertAdjacentElement('afterend', analyticsAiCard);
        }

        function buildAnalyticsPrintHTML() {
            const classRows = analyticsPrintData.classAnalytics.map(room => `
                <tr>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${escapeHtml(room.label)}</td>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${room.total_students ?? 0}</td>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${room.submitted ?? 0}</td>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${room.completion ?? 0}%</td>
                </tr>
            `).join('');

            const requestRows = analyticsPrintData.requestAnalytics.map(metric => `
                <tr>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${escapeHtml(metric.label)}</td>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${metric.count ?? 0}</td>
                </tr>
            `).join('');

            const fileRows = analyticsPrintData.fileMetrics.map(metric => `
                <tr>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${escapeHtml(metric.label)}</td>
                    <td style="padding:7px 8px; border:1px solid #e5e7eb;">${metric.count ?? 0}</td>
                </tr>
            `).join('');

            return `
                <div class="analytics-print" style="font-family:Poppins,Arial,sans-serif;color:#111827;">
                    <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#fff;">
                        <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%);color:#fff;padding:18px 22px;">
                            <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:rgba(255,255,255,.7);font-weight:700;">Polytechnic University of the Philippines - OJT Information Management System</div>
                            <div style="font-size:22px;font-weight:800;line-height:1.15;margin-top:4px;">${escapeHtml(analyticsPrintData.title)}</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.82);margin-top:4px;">${escapeHtml(analyticsPrintData.subtitle)}</div>
                        </div>
                        <div style="padding:14px 22px;border-bottom:1px solid #e5e7eb;background:#f9fafb;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;align-items:flex-start;">
                            <div style="display:flex;flex-wrap:wrap;gap:14px;font-size:11px;color:#374151;line-height:1.6;">
                                <div><span style="color:#6b7280;">Scope:</span> <strong>${escapeHtml(analyticsPrintData.subtitle)}</strong></div>
                                <div><span style="color:#6b7280;">Students:</span> <strong>${analyticsPrintData.totalStudents}</strong></div>
                                <div><span style="color:#6b7280;">Submitted:</span> <strong>${analyticsPrintData.submittedRequests}</strong></div>
                                <div><span style="color:#6b7280;">Generated:</span> <strong>${escapeHtml(analyticsPrintData.generatedAt)}</strong></div>
                            </div>
                            <div style="font-size:10px;color:#9ca3af;">Analytics snapshot</div>
                        </div>
                        <div style="padding:18px 22px 22px;">
                            <div style="border:1px solid #e5e7eb;border-left:4px solid #dc2626;border-radius:10px;padding:14px 16px;margin-bottom:14px;">
                                <div style="font-size:12px;font-weight:700;color:#111827;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Report Summary</div>
                                <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;">
                                    <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;"><div style="font-size:16px;font-weight:800;color:#111827;">${analyticsPrintData.totalStudents}</div><div style="font-size:8px;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">Total Advisees</div></div>
                                    <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;"><div style="font-size:16px;font-weight:800;color:#111827;">${analyticsPrintData.classCount}</div><div style="font-size:8px;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">Active Classes</div></div>
                                    <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;"><div style="font-size:16px;font-weight:800;color:#111827;">${analyticsPrintData.submittedRequests}</div><div style="font-size:8px;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">Submitted Evaluations</div></div>
                                    <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;"><div style="font-size:16px;font-weight:800;color:#111827;">${analyticsPrintData.templateCount}</div><div style="font-size:8px;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">File Categories</div></div>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:14px;">
                                <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;page-break-inside:avoid;">
                                    <div style="font-size:12px;font-weight:700;color:#111827;margin-bottom:10px;border-left:3px solid #dc2626;padding-left:8px;">Student Standing</div>
                                    <table style="width:100%;border-collapse:collapse;font-size:10px;">
                                        <thead><tr style="background:#f9fafb;"><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Status</th><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Count</th></tr></thead>
                                        <tbody>
                                            <tr><td style="padding:7px 8px;border:1px solid #e5e7eb;">Approved students</td><td style="padding:7px 8px;border:1px solid #e5e7eb;">{{ $approvedStudents }}</td></tr>
                                            <tr><td style="padding:7px 8px;border:1px solid #e5e7eb;">Pending students</td><td style="padding:7px 8px;border:1px solid #e5e7eb;">{{ $pendingApprovals }}</td></tr>
                                            <tr><td style="padding:7px 8px;border:1px solid #e5e7eb;">Denied students</td><td style="padding:7px 8px;border:1px solid #e5e7eb;">{{ $deniedStudents }}</td></tr>
                                            <tr><td style="padding:7px 8px;border:1px solid #e5e7eb;">Inactive students</td><td style="padding:7px 8px;border:1px solid #e5e7eb;">{{ $inactiveStudents }}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;page-break-inside:avoid;">
                                    <div style="font-size:12px;font-weight:700;color:#111827;margin-bottom:10px;border-left:3px solid #dc2626;padding-left:8px;">Class Overview</div>
                                    <table style="width:100%;border-collapse:collapse;font-size:10px;">
                                        <thead><tr style="background:#f9fafb;"><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Class</th><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Students</th><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Submitted</th><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Completion</th></tr></thead>
                                        <tbody>${classRows || '<tr><td colspan="4" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No classes found.</td></tr>'}</tbody>
                                    </table>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;">
                                <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;page-break-inside:avoid;">
                                    <div style="font-size:12px;font-weight:700;color:#111827;margin-bottom:10px;border-left:3px solid #dc2626;padding-left:8px;">Requirement Review</div>
                                    <table style="width:100%;border-collapse:collapse;font-size:10px;">
                                        <thead><tr style="background:#f9fafb;"><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Status</th><th style="text-align:left;padding:7px 8px;border:1px solid #e5e7eb;">Count</th></tr></thead>
                                        <tbody>${fileRows}</tbody>
                                    </table>
                                </div>
                                <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;page-break-inside:avoid;">
                                    <div style="font-size:12px;font-weight:700;color:#111827;margin-bottom:10px;border-left:3px solid #dc2626;padding-left:8px;">Analytics Insight</div>
                                    <div style="font-size:11px;line-height:1.7;color:#374151;margin-bottom:10px;">${escapeHtml(analyticsPrintData.summary)}</div>
                                    <div style="font-size:10px;color:#6b7280;line-height:1.6;">This printed report focuses on the current adviser snapshot, student standing, class overview, and file requirements.</div>
                                </div>
                            </div>
                            <div style="margin-top:16px;border-top:1px dashed #d1d5db;padding-top:14px;">
                                <div style="background:#f8fafc;border:1px solid #e5e7eb;border-left:4px solid #dc2626;border-radius:8px;padding:12px 14px;">
                                    <div style="font-size:9px;font-weight:700;color:#111827;text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;">Disclaimer</div>
                                    <div style="font-size:8.5px;color:#4b5563;line-height:1.6;">This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</div>
                                </div>
                                <div style="margin-top:10px;background:#7f0000;padding:8px 22px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP" style="width:13px;height:13px;object-fit:contain;opacity:.7;">
                                        <span style="font-size:8px;color:rgba(255,255,255,.75);font-weight:500;">Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                                    </div>
                                    <div style="font-size:8px;color:rgba(255,255,255,.5);">Ref: ANA-RPT-${new Date().getFullYear()}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        const analyticsPrintUrl = @json(route('professor.analytics.print'));

        document.getElementById('printBtn')?.addEventListener('click', function () {
            const frame = document.createElement('iframe');
            frame.style.position = 'fixed';
            frame.style.right = '0';
            frame.style.bottom = '0';
            frame.style.width = '0';
            frame.style.height = '0';
            frame.style.border = '0';
            frame.style.opacity = '0';
            frame.setAttribute('aria-hidden', 'true');
            frame.src = analyticsPrintUrl;

            let cleanedUp = false;
            const cleanup = function () {
                if (cleanedUp) {
                    return;
                }
                cleanedUp = true;
                window.removeEventListener('afterprint', cleanup);
                if (frame.parentNode) {
                    frame.parentNode.removeChild(frame);
                }
            };

            frame.onload = function () {
                setTimeout(function () {
                    if (frame.contentWindow) {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                        window.addEventListener('afterprint', cleanup, { once: true });
                        setTimeout(cleanup, 1500);
                    } else {
                        cleanup();
                    }
                }, 150);
            };

            document.body.appendChild(frame);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && drilldownModal && drilldownModal.style.display === 'flex') {
                drilldownModal.style.display = 'none';
            }
        });

        // Drilldown modal functions
        const drilldownModal = document.getElementById('drilldownModal');
        let currentPage = 1;

        function openDrilldownModal(label, year, month) {
            document.getElementById('drilldownTitle').textContent = 'Requests Submitted - ' + label;
            currentPage = 1;
            fetchDrilldownData(year, month, 1);
            drilldownModal.style.display = 'flex';
        }

        async function fetchDrilldownData(year, month, page) {
            const classId = document.getElementById('classFilter')?.value || '';
            const status = document.getElementById('drilldownStatusFilter')?.value || '';
            const queryText = document.getElementById('drilldownSearch')?.value?.trim() || '';
            const url = new URL("{{ route('professor.analytics.drilldown') }}", window.location.origin);
            url.searchParams.set('year', year);
            url.searchParams.set('month', month);
            if (classId) url.searchParams.set('class_id', classId);
            if (status) url.searchParams.set('status', status);
            if (queryText) url.searchParams.set('q', queryText);
            url.searchParams.set('page', page);

            try {
                const res = await fetch(url.toString(), { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Failed to fetch');
                const json = await res.json();
                renderDrilldownTable(json);
            } catch (e) {
                console.error('Drilldown fetch error', e);
            }
        }

        function renderDrilldownTable(data) {
            const tbody = document.getElementById('drilldownTableBody');
            tbody.innerHTML = '';

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:16px;">No records found</td></tr>';
                return;
            }

            data.data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding:8px;">${item.student?.first_name} ${item.student?.last_name || ''}</td>
                    <td style="padding:8px;">${item.company || '-'}</td>
                    <td style="padding:8px;"><span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:4px;font-size:11px;">${item.status}</span></td>
                    <td style="padding:8px;font-size:12px;color:#666;">${new Date(item.submitted_at).toLocaleDateString()}</td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('drilldownPaginationInfo').textContent = `Page ${data.current_page} of ${Math.ceil(data.total / data.per_page)}`;
            document.getElementById('drilldownPrevBtn').disabled = data.current_page === 1;
            document.getElementById('drilldownNextBtn').disabled = data.current_page >= Math.ceil(data.total / data.per_page);
            currentPage = data.current_page;
        }

        document.getElementById('drilldownCloseBtn')?.addEventListener('click', () => {
            drilldownModal.style.display = 'none';
        });

        document.getElementById('drilldownPrevBtn')?.addEventListener('click', () => {
            if (currentPage > 1) fetchDrilldownData(window.drilldownYear, window.drilldownMonth, currentPage - 1);
        });

        document.getElementById('drilldownNextBtn')?.addEventListener('click', () => {
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, currentPage + 1);
        });

        document.getElementById('drilldownSearchBtn')?.addEventListener('click', () => {
            currentPage = 1;
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1);
        });

        document.getElementById('drilldownSearch')?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                currentPage = 1;
                fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1);
            }
        });

        document.getElementById('drilldownStatusFilter')?.addEventListener('change', () => {
            currentPage = 1;
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1);
        });

        window.addEventListener('click', (e) => {
            if (e.target === drilldownModal) drilldownModal.style.display = 'none';
        });

        // load initial data
        fetchMonthlyData();
    })();
</script>
<script src="{{ asset('js/ai-insight-controls.js') }}"></script>

<!-- Drilldown Modal -->
<div id="drilldownModal" role="dialog" aria-modal="true" aria-labelledby="drilldownTitle" tabindex="-1" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;z-index:9999;">
    <div style="background:#fff;border-radius:12px;width:90%;max-width:700px;max-height:80vh;overflow:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px;border-bottom:1px solid #e5e7eb;">
            <h3 id="drilldownTitle" style="margin:0;font-size:16px;font-weight:600;">Details</h3>
            <button id="drilldownCloseBtn" type="button" aria-label="Close details dialog" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;">&times;</button>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;padding:12px 16px;border-bottom:1px solid #e5e7eb;align-items:center;">
            <input id="drilldownSearch" type="text" aria-label="Search drilldown records" placeholder="Search student or company" style="flex:1;min-width:220px;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;">
            <select id="drilldownStatusFilter" aria-label="Filter drilldown status" style="padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;min-width:160px;">
                <option value="">All statuses</option>
                <option value="sent">Sent</option>
                <option value="opened">Opened</option>
                <option value="submitted">Submitted</option>
                <option value="expired">Expired</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button id="drilldownSearchBtn" type="button" aria-label="Search drilldown records" style="background:#ef4444;color:#fff;border:none;border-radius:6px;padding:8px 12px;cursor:pointer;">Search</button>
        </div>
        <div style="padding:16px;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f5f5f5;">
                        <th style="padding:8px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb;">Student</th>
                        <th style="padding:8px;text-align:left;font-weight:600;border-bottom:1px solid #e5e7eb;">Company</th>
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
