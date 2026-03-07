<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Company Information</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

<<<<<<< HEAD
        :root {
            --red:        #dc2626;
            --red-dark:   #991b1b;
            --red-deeper: #7f0000;
            --sidebar-w:  260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h:   64px;
        }
=======
            <script src="js/jquery.printPage.js"></script>
            <style>
                /* Style the content container */
                .content-container {
                    display: flex; /* Use flexbox layout */
                    flex-wrap: wrap; /* Allow columns to wrap to the next row on smaller screens */
                    gap: 20px; /* Spacing between columns */
                    justify-content: space-between; /* Distribute columns evenly horizontally */
                }
            
                /* Style each column */
                .column {
                    flex-basis: calc(50% - 10px); /* Set column width to 50% minus spacing */
                    box-sizing: border-box; /* Include padding and border in column width */
                    padding: 10px; /* Add padding to the columns */
                    background-color: #f0f0f0; /* Background color for columns (adjust as needed) */
                   
                }
            
                /* Center the iframe within the second column */
                .column iframe {
                    display: block; /* Make the iframe a block-level element */
                    margin: 0 auto; /* Center horizontally within the column */
                }
            
                /* Add additional styling for the content within columns */
                .student-info {
                    margin-bottom: 10px; /* Add spacing between student info items */
                }
    
    
                /* Add this CSS to your stylesheet */
                .scrollable-container {
                    max-height: 80vh; /* Adjust the maximum height as needed */
                    overflow-y: auto;
                    padding: 20px; /* Optional: Add padding for spacing */
                    /* Add any other styles you need for the container */
                }
                 /* Add this CSS to your stylesheet */
                .scrollable-container {
             
                    max-height: 600px;
                    overflow-y: auto;
                    padding: 20px; /* Optional: Add padding for spacing */
                    /* Add any other styles you need for the container */
                }
    
    
            </style>
        
            
        </head>
        
        <body>
            <!-- =============== Navigation ================ -->
            <div class="container">
                <div class="navigation">
                    <ul>
                        <li>
                            <a href="#">
                                <img src="/images/final-puptg_logo-ojtims_nbg.png">
                                <span class="toptitle">InternConnect</span>
                            </a>
        
        
                        </li>
        
                        <a href="{{ url('/accountinfo') }}" style="text-decoration: none;">
                            <span class="iconname">
                                <ion-icon name="person-circle-outline"></ion-icon>
                            </span>
                            <span class="name"> {{ $user->full_name }} </span>
                            <span class="name2">OJT COORDINATOR </span>
        
                        </a>
        
                        <a href="{{ url('/accountinfo') }}" style="text-decoration: none;">
                            <span class="hidden-on-big">{{ $user->full_name }}</span>
                            <!-- <div class="toggle" id="toggle2">
                                <ion-icon name="menu-outline"></ion-icon>
                            </div> -->
                        </a>
        
        
                        <li>
                            <a href="{{ url('/dashboard') }}">
                                <span class="icon">
                                    <ion-icon name="home-outline"></ion-icon>
                                </span>
                                <span class="title" >Dashboard</span>
                            </a>
                        </li>
        
                        <li>
                            <a href="{{ url('/studentLists') }}">
                                <span class="icon">
                                    <ion-icon name="people-outline"></ion-icon>
                                </span>
                                <span class="title">Students</span>
                            </a>
                        </li>
        
                        <li>
                            <a href="{{ url('/professorTab') }}">
                                <span class="icon">
                                    <ion-icon name="people-circle-outline"></ion-icon>
                                </span>
                                <span class="title">Professors</span>
                            </a>
                        </li>
        
                        <li>
                            <a href="{{ url('/uploadpage') }}">
                                <span class="icon">
                                    <ion-icon name="document-outline"></ion-icon>
                                </span>
                                <span class="title">Upload Templates</span>
                            </a>
                        </li>
        
                        <li>
                            <a href="{{ url('/maintenance') }}">
                                <span class="icon">
                                    <ion-icon name="code-working-outline"></ion-icon>
                                </span>
                                <span class="title">Maintenance</span>
                            </a>
                        </li>
        
                        <li class="active">
                            <a href="{{ url('/MOA') }}">
                                <span class="icon">
                                    <ion-icon name="folder-outline"></ion-icon>
                                </span>
                                <span class="title">MOA</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/reports') }}">
                                <span class="icon">
                                    <ion-icon name="cellular-outline"></ion-icon>
                                </span>
                                <span class="title">Reports</span>
                                <span class="icon" style="margin-left: 30%; font-size: 22px;">
                                    <ion-icon name="chevron-down-outline"></ion-icon>
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/auditlog') }}">
                                <span class="icon">
                                    <ion-icon name="clipboard-outline"></ion-icon>
                                </span>
                                <span class="title">Audit Log</span>
                            </a>
                        </li> 
                               
                        <li>
                            <a href="{{ url('/login') }}">
                                <span class="icon">
                                    <ion-icon name="log-out-outline"></ion-icon>
                                </span>
                                <span class="title">Log Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
        
                <!-- ========================= Main ==================== -->
                <div class="main">
        
                    <div class="topbar">
        
                        <div class="toggle">
                            <ion-icon name="menu-outline"></ion-icon>
                        </div>
        
                        <span class="subtitle">On-the-Job Training Information Management System </span>
        
                    </div>
>>>>>>> aa6a5d91508198ff4cee6146a9d2421213520478

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

        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red-dark);
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }

        .page-header {
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #888; margin-top: 6px;
        }

        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
            text-decoration: none;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            color: #fff; text-decoration: none;
        }

        /* =============== COMPANY INFO CARD =============== */
        .panel-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden; margin-bottom: 22px;
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

        /* Company info grid */
        .company-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .info-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 16px;
            background: #fafafa; border: 1px solid #f0f0f0;
            border-radius: 12px;
        }

        .info-item-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 13px; flex-shrink: 0;
        }

        .info-item-label {
            font-size: 11px; font-weight: 600; color: #aaa;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        .info-item-value {
            font-size: 13.5px; font-weight: 600; color: #1a1a1a;
            margin-top: 3px; word-break: break-word;
        }

        /* =============== TWO-COLUMN LAYOUT =============== */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            align-items: start;
        }

        /* =============== STUDENT LIST =============== */
        .student-list {
            max-height: 600px; overflow-y: auto;
            padding-right: 4px;
        }

        .student-list::-webkit-scrollbar { width: 4px; }
        .student-list::-webkit-scrollbar-thumb { background: #fecaca; border-radius: 10px; }

        .student-card {
            background: #fafafa; border: 1px solid #f0f0f0;
            border-radius: 12px; padding: 16px;
            margin-bottom: 12px; transition: all 0.2s;
        }

        .student-card:last-child { margin-bottom: 0; }
        .student-card:hover { border-color: #fecaca; background: #fff5f5; }

        .student-card-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 12px; padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .student-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 14px; font-weight: 700;
            flex-shrink: 0;
        }

        .student-name { font-size: 14px; font-weight: 700; color: #1a1a1a; }
        .student-course {
            display: inline-flex; align-items: center;
            background: #ede9fe; color: #7c3aed;
            border-radius: 20px; padding: 2px 9px;
            font-size: 11px; font-weight: 600; margin-top: 2px;
        }

        .student-detail-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 12.5px; color: #555; margin-bottom: 5px;
        }

        .student-detail-row:last-child { margin-bottom: 0; }

        .student-detail-row i {
            color: var(--red); font-size: 11px; width: 14px;
            text-align: center; flex-shrink: 0;
        }

        /* =============== MOA VIEWER =============== */
        .moa-viewer-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .moa-viewer-header {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .moa-viewer-header h3 { font-size: 15px; font-weight: 700; color: #1a1a1a; }
        .moa-viewer-header p  { font-size: 12px; color: #888; margin-top: 2px; }

        .moa-iframe-wrap { padding: 16px; }

        .moa-iframe-wrap iframe {
            width: 100%; height: 560px;
            border: 1.5px solid #f0f0f0;
            border-radius: 10px; display: block;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
        }

        @media (max-width: 1100px) {
            .detail-grid { grid-template-columns: 1fr; }
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
            .company-info-grid { grid-template-columns: 1fr 1fr; }
            .moa-iframe-wrap iframe { height: 400px; }
        }

        @media (max-width: 480px) {
            .company-info-grid { grid-template-columns: 1fr; }
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
            <span class="sidebar-brand-sub">OJT IMS</span>
        </div>
    </a>

    <a href="{{ url('/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($user->profile_photo) && $user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
            <span class="user-role">OJT Coordinator</span>
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
        <a href="{{ url('/MOA') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/reports') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
            <span class="nav-label">Reports</span>
            <span class="tooltip-label">Reports</span>
        </a>
        <li>
    <a href="{{ url('/auditlog') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
        </a>
</li>
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
        <div class="topbar-badge">
            <i class="fa fa-user-shield"></i>
            OJT Coordinator
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Company <span>Information</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ url('/MOA') }}">MOA</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Company Info</span>
                </div>
            </div>
            <a href="{{ url('/MOA') }}" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back to MOA
            </a>
        </div>

        <!-- Company Info Card -->
        <div class="panel-card">
            <div class="panel-card-header">
                <div class="panel-header-icon">
                    <i class="fa fa-building"></i>
                </div>
                <div>
                    <h2>{{ $company->company_name }}</h2>
                    <p>Partner company details for this MOA agreement</p>
                </div>
            </div>
            <div class="panel-card-body">
                <div class="company-info-grid">

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-building"></i></div>
                        <div>
                            <div class="info-item-label">Company Name</div>
                            <div class="info-item-value">{{ $company->company_name }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div>
                            <div class="info-item-label">Company Address</div>
                            <div class="info-item-value">{{ $company->company_address }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-user-tie"></i></div>
                        <div>
                            <div class="info-item-label">Representative</div>
                            <div class="info-item-value">{{ $company->company_rep }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <div class="info-item-label">Contact Number</div>
                            <div class="info-item-value">{{ $company->companyNo }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-envelope"></i></div>
                        <div>
                            <div class="info-item-label">Email Address</div>
                            <div class="info-item-value">{{ $company->company_email }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-item-icon"><i class="fa fa-calendar-alt"></i></div>
                        <div>
                            <div class="info-item-label">School Year</div>
                            <div class="info-item-value">{{ $company->school_year }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Two-column: Students + MOA Viewer -->
        <div class="detail-grid">

            <!-- ===== LEFT: Student List ===== -->
            <div class="panel-card" style="margin-bottom:0;">
                <div class="panel-card-header">
                    <div class="panel-header-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h2>Student List</h2>
                        <p>
                            {{ count($company->students) }}
                            {{ count($company->students) == 1 ? 'student' : 'students' }}
                            assigned to this company
                        </p>
                    </div>
                </div>
                <div class="panel-card-body">
                    <div class="student-list">
                        @forelse ($company->students as $student)
                        <div class="student-card">
                            <div class="student-card-header">
                                <div class="student-avatar">
                                    {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="student-name">{{ $student->full_name }}</div>
                                    <span class="student-course">{{ $student->course }}</span>
                                </div>
                            </div>

                            <div class="student-detail-row">
                                <i class="fa fa-id-card"></i>
                                <span><strong>Student No:</strong> {{ $student->studentNum }}</span>
                            </div>
                            <div class="student-detail-row">
                                <i class="fa fa-envelope"></i>
                                <span>{{ $student->email }}</span>
                            </div>
                            <div class="student-detail-row">
                                <i class="fa fa-birthday-cake"></i>
                                <span><strong>DOB:</strong> {{ $student->date_of_birth }}</span>
                            </div>
                            <div class="student-detail-row">
                                <i class="fa fa-phone"></i>
                                <span>{{ $student->contact_number }}</span>
                            </div>
                            <div class="student-detail-row">
                                <i class="fa fa-map-marker-alt"></i>
                                <span>{{ $student->address }}</span>
                            </div>
                            <div class="student-detail-row">
                                <i class="fa fa-layer-group"></i>
                                <span>{{ $student->year_and_section }}</span>
                            </div>
                            <div class="student-detail-row">
                                <i class="fa fa-chalkboard-teacher"></i>
                                <span><strong>Adviser:</strong> {{ $student->adviser_name }}</span>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center; padding:40px 20px; color:#aaa;">
                            <i class="fa fa-users" style="font-size:40px; margin-bottom:12px; display:block; color:#fecaca;"></i>
                            <div style="font-size:14px; font-weight:600; color:#888;">No students assigned</div>
                            <div style="font-size:12.5px; margin-top:4px;">No students are linked to this company yet.</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT: MOA Viewer ===== -->
            <div class="moa-viewer-card">
                <div class="moa-viewer-header">
                    <div class="panel-header-icon">
                        <i class="fa fa-file-contract"></i>
                    </div>
                    <div>
                        <h3>Memorandum of Agreement</h3>
                        <p>Official MOA document for {{ $company->company_name }}</p>
                    </div>
                </div>
                <div class="moa-iframe-wrap">
                    <iframe src="/assets/{{ $company->file }}"
                            title="MOA Document">
                    </iframe>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
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
</script>

</body>
</html>