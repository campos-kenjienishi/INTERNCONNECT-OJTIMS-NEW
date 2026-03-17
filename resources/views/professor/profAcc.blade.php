<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Account Information</title>
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

        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* =============== TWO-COLUMN LAYOUT =============== */
        .account-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            align-items: start;
        }

        /* =============== PROFILE CARD =============== */
        .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
            position: sticky;
            top: calc(var(--topbar-h) + 20px);
        }

        .profile-card-banner {
            background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%);
            height: 80px; position: relative;
        }

        .profile-card-banner::after {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='30'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .profile-photo-wrap {
            display: flex; justify-content: center;
            margin-top: -44px; position: relative; z-index: 1;
        }

        .profile-photo-ring {
            width: 88px; height: 88px; border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            position: relative; overflow: hidden;
            background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
        }

        .profile-photo-ring img {
            width: 100%; height: 100%; object-fit: cover;
        }

        .profile-photo-ring .photo-placeholder {
            font-size: 32px; color: var(--red); font-weight: 800;
        }

        .profile-photo-btn {
            position: absolute; bottom: 0; right: 0;
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--red); border: 2px solid #fff;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 11px; cursor: pointer;
            transition: background 0.2s;
        }

        .profile-photo-btn:hover { background: var(--red-dark); }

        .profile-card-body { padding: 16px 20px 24px; text-align: center; }

        .profile-name {
            font-size: 16px; font-weight: 800; color: #1a1a1a;
            margin-top: 10px; line-height: 1.2;
        }

        .profile-role-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #fee2e2; color: var(--red);
            border-radius: 20px; padding: 4px 12px;
            font-size: 11.5px; font-weight: 700;
            margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px;
        }

        .profile-divider {
            height: 1px; background: #f0f0f0;
            margin: 18px 0;
        }

        .profile-info-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0; text-align: left;
        }

        .profile-info-icon {
            width: 30px; height: 30px; border-radius: 8px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 12px; flex-shrink: 0;
        }

        .profile-info-label { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; }
        .profile-info-value { font-size: 13px; font-weight: 600; color: #333; margin-top: 1px; word-break: break-all; }

        .btn-change-password {
            width: 100%; margin-top: 18px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px;
            background: #fff; border: 1.5px solid #e8e8e8;
            border-radius: 10px; color: #555;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.25s;
        }

        .btn-change-password:hover {
            border-color: var(--red); color: var(--red); background: #fff5f5;
        }

        /* =============== FORM CARD =============== */
        .form-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .form-card-header {
            display: flex; align-items: center; gap: 12px;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .form-header-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 15px; flex-shrink: 0;
        }

        .form-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .form-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }

        .form-card-body { padding: 26px 28px; }

        /* Section divider */
        .form-section-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 12px; font-weight: 700; color: var(--red);
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 18px; margin-top: 26px;
        }

        .form-section-title:first-child { margin-top: 0; }

        .form-section-title::after {
            content: ''; flex: 1; height: 1px; background: #fee2e2;
        }

        /* Form grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 4px;
        }

        .form-grid-full { grid-template-columns: 1fr; }

        .field-group { display: flex; flex-direction: column; gap: 6px; }

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

        /* Form footer */
        .form-card-footer {
            padding: 18px 28px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
            display: flex; justify-content: flex-end; gap: 10px;
        }

        .btn-save {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 28px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
        }

        /* =============== MODAL =============== */
        .modal-content {
            border-radius: 16px; border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            font-family: 'Poppins', sans-serif; overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%);
            border-bottom: none; padding: 20px 24px;
        }

        .modal-title {
            color: #fff; font-size: 16px; font-weight: 700;
            display: flex; align-items: center; gap: 10px;
        }

        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }
        .modal-body { padding: 24px; background: #fff; }

        .modal-field-label {
            font-size: 13px; font-weight: 600; color: #444;
            margin-bottom: 7px; display: flex; align-items: center; gap: 6px;
            margin-top: 16px;
        }

        .modal-field-label:first-child { margin-top: 0; }
        .modal-field-label i { color: var(--red); font-size: 12px; }

        .pw-input-wrap {
            position: relative; display: flex; align-items: center;
        }

        .pw-input-wrap .form-control {
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            padding: 11px 44px 11px 14px; outline: none;
            transition: all 0.25s; background: #fafafa;
        }

        .pw-input-wrap .form-control:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .pw-toggle {
            position: absolute; right: 12px;
            background: none; border: none; color: #aaa;
            cursor: pointer; font-size: 14px; transition: color 0.2s;
            padding: 0;
        }

        .pw-toggle:hover { color: var(--red); }

        .modal-footer {
            background: #fafafa; border-top: 1px solid #f0f0f0;
            padding: 16px 24px; display: flex; gap: 10px; justify-content: flex-end;
        }

        .btn-modal-close {
            padding: 9px 20px; background: #f3f4f6;
            border: 1px solid #e5e5e5; border-radius: 8px; color: #555;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }

        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .btn-modal-submit {
            padding: 9px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 8px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 13.5px;
            font-weight: 600; cursor: pointer; transition: all 0.25s;
            box-shadow: 0 3px 12px rgba(220,38,38,0.2);
        }

        .btn-modal-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(220,38,38,0.3); }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
        }

        @media (max-width: 1100px) {
            .account-layout { grid-template-columns: 1fr; }
            .profile-card { position: static; }
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
            .form-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
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

    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
                <i class="fa fa-user-tie"></i>
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
                <i class="fa fa-chalkboard-teacher"></i>
                Professor Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Account <span>Information</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Account Information</span>
                </div>
            </div>
        </div>

        <!-- Two-column layout -->
        <div class="account-layout">

            <!-- ===== LEFT: Profile Card ===== -->
            <div class="profile-card">
                <div class="profile-card-banner"></div>

                <div class="profile-photo-wrap">
                    <div class="profile-photo-ring" id="profilePhotoRing">
                        <span class="photo-placeholder" id="profilePhotoPreview">
                            {{ strtoupper(substr($data->first_name, 0, 1)) }}
                        </span>
                    </div>
                </div>

                <div class="profile-card-body">
                    <div class="profile-name">{{ $data->full_name }}</div>
                    <div class="profile-role-badge">
                        <i class="fa fa-chalkboard-teacher" style="font-size:10px;"></i>
                        Professor
                    </div>

                    <div class="profile-divider"></div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-envelope"></i></div>
                        <div>
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value">{{ $data->email }}</div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <div class="profile-info-label">Contact No.</div>
                            <div class="profile-info-value">{{ $data->contact_number ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-birthday-cake"></i></div>
                        <div>
                            <div class="profile-info-label">Date of Birth</div>
                            <div class="profile-info-value">
                                {{ $data->date_of_birth ? \Carbon\Carbon::parse($data->date_of_birth)->format('M d, Y') : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div>
                            <div class="profile-info-label">Address</div>
                            <div class="profile-info-value">{{ $data->address ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="profile-divider"></div>

                    <button class="btn-change-password"
                        data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fa fa-lock"></i> Change Password
                    </button>
                </div>
            </div>

            <!-- ===== RIGHT: Edit Form ===== -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-header-icon"><i class="fa fa-user-edit"></i></div>
                    <div>
                        <h2>Edit Personal Details</h2>
                        <p>Update your personal information below</p>
                    </div>
                </div>

                <form action="{{ url('/professor/edit', $data->email) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-card-body">

                        <!-- Name Section -->
                        <div class="form-section-title">
                            <i class="fa fa-user"></i> Name
                        </div>
                        <div class="form-grid">
                            <div class="field-group">
                                <label class="field-label" for="first_name">
                                    <i class="fa fa-id-card"></i> First Name
                                </label>
                                <input class="field-input" type="text" id="first_name"
                                       name="first_name" value="{{ $data->first_name }}">
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="middle_name">
                                    <i class="fa fa-id-card"></i> Middle Name
                                </label>
                                <input class="field-input" type="text" id="middle_name"
                                       name="middle_name" value="{{ $data->middle_name }}">
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="last_name">
                                    <i class="fa fa-id-card"></i> Last Name
                                </label>
                                <input class="field-input" type="text" id="last_name"
                                       name="last_name" value="{{ $data->last_name }}">
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="suffix">
                                    <i class="fa fa-pen"></i> Suffix
                                </label>
                                <input class="field-input" type="text" id="suffix"
                                       name="suffix" value="{{ $data->suffix }}"
                                       placeholder="Jr., Sr., III...">
                            </div>
                        </div>

                        <!-- Contact & Identity Section -->
                        <div class="form-section-title">
                            <i class="fa fa-address-book"></i> Contact &amp; Identity
                        </div>
                        <div class="form-grid">
                            <div class="field-group">
                                <label class="field-label" for="contact_number">
                                    <i class="fa fa-phone"></i> Contact No.
                                </label>
                                <input class="field-input" type="text" id="contact_number"
                                       name="contact_number" value="{{ $data->contact_number }}">
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="date_of_birth">
                                    <i class="fa fa-birthday-cake"></i> Date of Birth
                                </label>
                                <input class="field-input" type="date" id="date_of_birth"
                                       name="date_of_birth" value="{{ $data->date_of_birth }}">
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="email">
                                    <i class="fa fa-envelope"></i> Email Address
                                </label>
                                <input class="field-input" type="email" id="email"
                                       name="email" value="{{ $data->email }}">
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="form-section-title">
                            <i class="fa fa-map-marker-alt"></i> Address
                        </div>
                        <div class="form-grid form-grid-full">
                            <div class="field-group">
                                <label class="field-label" for="address">
                                    <i class="fa fa-home"></i> Full Address
                                </label>
                                <input class="field-input" type="text" id="address"
                                       name="address" value="{{ $data->address }}"
                                       placeholder="Street, Barangay, City, Province">
                            </div>
                        </div>

                    </div>

                    <div class="form-card-footer">
                        <button type="submit" class="btn-save">
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

<!-- =============== CHANGE PASSWORD MODAL =============== -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-lock"></i> Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/change_password', $data->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    @if ($errors->any())
                        <div class="alert alert-danger" style="border-radius:10px; font-size:13px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <label class="modal-field-label">
                        <i class="fa fa-key"></i> Current Password
                    </label>
                    <div class="pw-input-wrap">
                        <input type="password" class="form-control"
                               id="current_password" name="current_password">
                        <button type="button" class="pw-toggle" id="toggleCurrent">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                    <label class="modal-field-label">
                        <i class="fa fa-lock"></i> New Password
                    </label>
                    <div class="pw-input-wrap">
                        <input type="password" class="form-control"
                               id="new_password" name="new_password">
                        <button type="button" class="pw-toggle" id="toggleNew">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('new_password')
                        <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                    <label class="modal-field-label">
                        <i class="fa fa-check-circle"></i> Confirm New Password
                    </label>
                    <div class="pw-input-wrap">
                        <input type="password" class="form-control"
                               id="confirm_password" name="confirm_password">
                        <button type="button" class="pw-toggle" id="toggleConfirm">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('confirm_password')
                        <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
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

    // Password toggle
    document.addEventListener("DOMContentLoaded", function () {
        function setupToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input  = document.getElementById(inputId);
            if (!toggle || !input) return;
            toggle.addEventListener('click', function () {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        setupToggle('toggleCurrent', 'current_password');
        setupToggle('toggleNew',     'new_password');
        setupToggle('toggleConfirm', 'confirm_password');

        // Auto-open password modal if there are validation errors
        @if ($errors->any())
            var modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            modal.show();
        @endif
    });
</script>

</body>
</html>
