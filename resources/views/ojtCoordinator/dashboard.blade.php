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
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

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
                display: none; position: fixed; inset: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
            }

            @media (max-width: 1100px) {
                .dashboard-grid { grid-template-columns: 1fr; }
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
                .topbar-date { display: none; }
            }

            @media (max-width: 480px) {
                .stats-row { grid-template-columns: 1fr; }
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

        .welcome-banner h2 {
            font-size: 22px; font-weight: 800; color: #fff;
            margin-bottom: 6px; letter-spacing: -0.3px;
            position: relative; z-index: 1;
        }

        .welcome-banner p {
            font-size: 13.5px; color: rgba(255,255,255,0.75);
            line-height: 1.5; position: relative; z-index: 1;
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
            <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
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
            <!-- Page Header: title left, date right -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <h1 style="font-size:26px; font-weight:800; color:#1a1a1a; letter-spacing:-0.5px;">
        Home <span style="color:var(--red);">Dashboard</span>
    </h1>
    <div id="bannerDate"
         style="background:#fff; border:1px solid #e5e5e5; border-radius:8px;
                padding:7px 16px; font-size:13px; font-weight:600; color:#555;">
    </div>
</div>

<!-- Red welcome banner, no date inside -->
<div class="welcome-banner">
    <div class="welcome-left">
        <h2>Welcome back, {{ explode(' ', $data->full_name)[0] }}! 👋</h2>
        <p>Here's what's happening in your OJT portal today.</p>
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
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
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

        // Live date in topbar
        function updateDate() {
            const now = new Date();
            const short = now.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('bannerDate').textContent = short;
        }
        updateDate();
        setInterval(updateDate, 60000);
    </script>

    </body>
    </html>