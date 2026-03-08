<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - OJT Information</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
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
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
        }

        /* =============== */
        /* SIDEBAR         */
        /* =============== */

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

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .user-role {
            font-size: 10px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar.collapsed .user-info { opacity: 0; width: 0; }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
        }

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

        .nav-item:hover {
            color: #fff;
            background: rgba(255,255,255,0.06);
        }

        .nav-item.active {
            color: #fff;
            background: rgba(239,68,68,0.15);
            border-left-color: #ef4444;
        }

        .nav-item .nav-icon {
            font-size: 18px;
            flex-shrink: 0;
            width: 22px;
            text-align: center;
        }

        .nav-item .nav-label {
            transition: opacity 0.25s;
            overflow: hidden;
        }

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

        .sidebar-footer {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }

        /* =============== */
        /* MAIN CONTENT    */
        /* =============== */

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

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

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

        .topbar-title {
            font-size: 13.5px;
            font-weight: 500;
            color: #888;
        }

        .topbar-title span { color: var(--red); font-weight: 600; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

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

        /* Page content */
        .page-content { padding: 28px; flex: 1; }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }

        .page-header h1 span { color: var(--red); }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #888;
        }

        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* Form card */
        .form-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 22px;
        }

        .form-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 28px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .form-card-header .header-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 16px;
            flex-shrink: 0;
        }

        .form-card-header h2 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .form-card-header p {
            font-size: 12.5px;
            color: #888;
            margin-top: 2px;
        }

        .form-card-body { padding: 28px; }

        /* Section label */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 18px;
            margin-top: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title:first-child { margin-top: 0; }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }

        /* Form grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-grid .full-width { grid-column: 1 / -1; }

        /* Field group */
        .field-group { display: flex; flex-direction: column; }

        .field-label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-label i {
            color: var(--red);
            font-size: 12px;
        }

        .field-hint {
            font-size: 11.5px;
            color: #999;
            margin-bottom: 8px;
            line-height: 1.5;
            font-style: italic;
        }

        .field-input {
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
        }

        .field-input::placeholder { color: #bbb; }

        .field-input:focus {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .field-input:hover:not(:focus) {
            border-color: #ccc;
            background: #fff;
        }

        /* Date input icon fix */
        input[type="date"].field-input {
            color: #1a1a1a;
        }

        /* Submit button */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
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
            margin-top: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(220,38,38,0.2);
        }

        /* Alert */
        .alert {
            border-radius: 10px;
            font-size: 13px;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: rgba(34,197,94,0.1); color: #16a34a; }
        .alert-danger  { background: rgba(220,38,38,0.08); color: var(--red); }

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
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .full-width { grid-column: 1; }
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

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa fa-user"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Home</span>
            <span class="tooltip-label">Home</span>
        </a>

        <a href="{{ url('/student/ojtinfo') }}" class="nav-item active">
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
                <h1>OJT <span>Information</span></h1>
                <div class="breadcrumb" style="margin-top: 6px;">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>OJT Information</span>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="fa fa-building"></i></div>
                <div>
                    <h2>Company & OJT Details</h2>
                    <p>Fill in and update your on-the-job training information below</p>
                </div>
            </div>

            <div class="form-card-body">

                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('fail'))
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i> {{ Session::get('fail') }}
                    </div>
                @endif

                <form action="{{ url('/student/ojtEdit', $data->studentNum) }}" method="post">
                    @csrf
                    @method('PUT')

                    <!-- Company Information -->
                    <div class="section-title"><i class="fa fa-building"></i> Company Information</div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-building"></i> Company Name</label>
                            <input class="field-input" type="text" name="company_name"
                                value="{{ $user->company_name }}" placeholder="Enter company name">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-map-marker-alt"></i> Company Address</label>
                            <input class="field-input" type="text" name="company_address"
                                value="{{ $user->company_address }}" placeholder="Enter company address">
                        </div>

                        <div class="field-group full-width">
                            <label class="field-label"><i class="fa fa-briefcase"></i> Nature of Business</label>
                            <span class="field-hint">e.g. Educational Institution, Government Agency, Telecommunication, Travel Agency, Hotel and Hospitality Service, Food Service, BPOs, NGOs, POS, etc.</span>
                            <input class="field-input" type="text" name="nature_of_bus"
                                value="{{ $user->nature_of_bus }}" placeholder="Enter nature of business">
                        </div>

                        <div class="field-group full-width">
                            <label class="field-label"><i class="fa fa-network-wired"></i> Nature of Networking or Linkages</label>
                            <span class="field-hint">Please indicate if: Academic Linkages, Benefactors, Research and Extension Linkage, Educational and Cultural Exchange, Government Agencies Partners, National/Institutional Membership, Non-Government Organizations Partners, OJT/Training Stations, etc.</span>
                            <input class="field-input" type="text" name="nature_of_link"
                                value="{{ $user->nature_of_link }}" placeholder="Enter nature of networking or linkages">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-globe"></i> Level</label>
                            <input class="field-input" type="text" name="level"
                                value="{{ $user->level }}" placeholder="e.g. International, National, Regional, Local">
                        </div>
                    </div>

                    <!-- Schedule Information -->
                    <div class="section-title"><i class="fa fa-calendar-alt"></i> Schedule & Duration</div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-check"></i> Start Date</label>
                            <input class="field-input" type="date" name="start_date" value="{{ $user->start_date }}">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-times"></i> End Date</label>
                            <input class="field-input" type="date" name="finish_date" value="{{ $user->finish_date }}">
                        </div>

                        <div class="field-group full-width">
                            <label class="field-label"><i class="fa fa-clock"></i> Reporting Time</label>
                            <input class="field-input" type="text" name="report_time" id="report_time"
                                value="{{ $user->report_time }}" placeholder="e.g. 9:00 am - 6:00 pm (Monday - Friday)">
                        </div>
                    </div>

                    <!-- Contact Person -->
                    <div class="section-title"><i class="fa fa-address-card"></i> Contact Person</div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-user-tie"></i> Contact Name</label>
                            <input class="field-input" type="text" name="contact_name"
                                value="{{ $user->contact_name }}" placeholder="Enter contact name">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-id-badge"></i> Position of Contact</label>
                            <input class="field-input" type="text" name="contact_position"
                                value="{{ $user->contact_position }}" placeholder="Enter contact's position">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-phone"></i> Contact Number</label>
                            <input class="field-input" type="text" name="contact_number"
                                value="{{ $user->contact_number }}" placeholder="Enter contact number">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                        <button type="submit" class="btn-submit">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>

                </form>
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

    // Report time validation
    document.getElementById("report_time").addEventListener("blur", function () {
        const input = this.value.trim();
        const pattern = /^(\d{1,2}:\d{2} [APap][Mm] - \d{1,2}:\d{2} [APap][Mm])\s+\((Monday|Tuesday|Wednesday|Thursday|Friday)\s*-\s*(Monday|Tuesday|Wednesday|Thursday|Friday)\)$/;

        if (input !== '' && !input.match(pattern)) {
            this.style.borderColor = '#dc2626';
            this.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            alert("Invalid format. Please use: 9:00 am - 6:00 pm (Monday - Friday)");
        } else {
            this.style.borderColor = '';
            this.style.boxShadow = '';
        }
    });
</script>

</body>
</html>