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
            color: #fca5a5; font-size: 16px; flex-shrink: 0;
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
            display: flex; align-items: center; gap: 14px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.55);
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
            font-size: 12.5px; font-weight: 600; color: var(--red);
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

        /* =============== PROFILE LAYOUT =============== */
        .account-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 22px;
            align-items: start;
        }

        /* Profile card */
        .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
            position: sticky;
            top: calc(var(--topbar-h) + 28px);
        }

        .profile-card-top {
            background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%);
            padding: 32px 24px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .profile-card-top::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        /* Profile picture */
        .profile-pic-wrap {
            position: relative;
            margin-bottom: 14px;
        }

        .profile-pic {
            width: 100px; height: 100px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.4);
            object-fit: cover;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.6);
            font-size: 40px;
            overflow: hidden;
        }

        .profile-pic img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-pic-edit {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--red);
            font-size: 12px;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .profile-pic-edit:hover {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
        }

        .profile-pic-input { display: none; }

        .profile-name {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            text-align: center;
            line-height: 1.3;
        }

        .profile-role {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }

        .profile-card-body { padding: 20px; }

        .profile-info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .profile-info-row:last-child { border-bottom: none; }

        .profile-info-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
            color: var(--red); font-size: 12px; flex-shrink: 0;
        }

        .profile-info-label { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px; }
        .profile-info-value { font-size: 13px; font-weight: 600; color: #1a1a1a; margin-top: 1px; }

        /* Change password button */
        .btn-change-pass {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px;
            background: #f5f5f5;
            border: 1.5px solid #e8e8e8;
            border-radius: 10px;
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            margin-top: 16px;
        }

        .btn-change-pass:hover {
            background: #fee2e2;
            border-color: #fecaca;
            color: var(--red);
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
            display: flex; align-items: center; gap: 14px;
            padding: 20px 28px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .form-card-header .header-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: #fee2e2; display: flex;
            align-items: center; justify-content: center;
            color: var(--red); font-size: 18px; flex-shrink: 0;
        }

        .form-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .form-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }

        .form-card-body { padding: 28px; }

        .section-title {
            font-size: 11px; font-weight: 700; color: var(--red);
            text-transform: uppercase; letter-spacing: 2px;
            margin-bottom: 18px; margin-top: 28px;
            display: flex; align-items: center; gap: 10px;
        }

        .section-title:first-child { margin-top: 0; }
        .section-title i { font-size: 13px; }

        .section-title::after {
            content: ''; flex: 1; height: 1px; background: #f0f0f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-grid .full-width { grid-column: 1 / -1; }

        .field-group { display: flex; flex-direction: column; }

        .field-label {
            font-size: 13px; font-weight: 600; color: #444;
            margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
        }

        .field-label i { color: var(--red); font-size: 12px; }

        .field-input, .field-select {
            width: 100%; background: #fafafa;
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; padding: 11px 14px; outline: none;
            transition: all 0.25s; appearance: none;
        }

        .field-input::placeholder { color: #bbb; }

        .field-input:focus, .field-select:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .field-input:hover:not(:focus),
        .field-select:hover:not(:focus) { border-color: #ccc; background: #fff; }

        input[type="date"].field-input { color: #1a1a1a; }

        /* Alerts */
        .alert {
            border-radius: 10px; font-size: 13px; padding: 12px 16px;
            margin-bottom: 20px; border: none;
            display: flex; align-items: flex-start; gap: 10px;
        }

        .alert-success { background: rgba(34,197,94,0.1); color: #16a34a; }
        .alert-danger  { background: rgba(220,38,38,0.08); color: var(--red); }

        /* Form footer */
        .form-footer {
            margin-top: 28px; padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-update {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
        }

        .btn-update:active { transform: translateY(0); }

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
        }

        .modal-field-label i { color: var(--red); font-size: 12px; }

        .pw-input-wrap { position: relative; margin-bottom: 16px; }

        .pw-input-wrap input {
            width: 100%; background: #fafafa;
            border: 1.5px solid #e8e8e8; border-radius: 10px;
            color: #1a1a1a; font-family: 'Poppins', sans-serif;
            font-size: 13.5px; padding: 11px 44px 11px 14px;
            outline: none; transition: all 0.25s;
        }

        .pw-input-wrap input:focus {
            border-color: var(--red); background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .pw-toggle {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--red); cursor: pointer; font-size: 14px;
            transition: color 0.2s;
        }

        .pw-toggle:hover { color: var(--red-dark); }

        .text-error { font-size: 12px; color: var(--red); margin-top: -10px; margin-bottom: 10px; display: block; }

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

        @media (max-width: 1024px) {
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
            <span class="sidebar-brand-sub">OJT IMS</span>
        </div>
    </a>

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if($data->profile_photo)
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user"></i>
            @endif
        </div>
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
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
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

<<<<<<< HEAD
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Account <span>Information</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Account Information</span>
                </div>
            </div>
=======
                <li >
                    <a href="{{ url('/student/requirements') }}">
                        <span class="icon">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                        </span>
                        <span class="title">Requirements</span>
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
>>>>>>> aa6a5d91508198ff4cee6146a9d2421213520478
        </div>

        <div class="account-layout">

            <!-- ===== LEFT: Profile Card ===== -->
            <div class="profile-card">
                <div class="profile-card-top">
                    <div class="profile-pic-wrap">
                        <!-- Profile picture display -->
                        <div class="profile-pic" id="profilePicDisplay">
                            @if($data->profile_photo)
                                <img src="{{ asset('storage/' . $data->profile_photo) }}"
                                     alt="Profile Photo" id="profilePicImg">
                            @else
                                <i class="fa fa-user" id="profilePicIcon"></i>
                            @endif
                        </div>

                        <!-- Edit button triggers hidden file input -->
                        <label class="profile-pic-edit" for="profilePhotoInput" title="Change photo">
                            <i class="fa fa-camera"></i>
                        </label>
                    </div>

                    <div class="profile-name">{{ $data->full_name }}</div>
                    <div class="profile-role">Student</div>
                </div>

                <div class="profile-card-body">
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-id-card"></i></div>
                        <div>
                            <div class="profile-info-label">Student No.</div>
                            <div class="profile-info-value">{{ $data->studentNum ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-graduation-cap"></i></div>
                        <div>
                            <div class="profile-info-label">Course</div>
                            <div class="profile-info-value">{{ $data->course ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-users"></i></div>
                        <div>
                            <div class="profile-info-label">Year & Section</div>
                            <div class="profile-info-value">{{ $data->year_and_section ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-envelope"></i></div>
                        <div>
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value" style="word-break:break-all;">{{ $data->email ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <div class="profile-info-label">Contact No.</div>
                            <div class="profile-info-value">{{ $data->contact_number ?? '—' }}</div>
                        </div>
                    </div>

                    <button class="btn-change-pass" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fa fa-lock"></i> Change Password
                    </button>
                </div>
            </div>

            <!-- ===== RIGHT: Edit Form ===== -->
            <div>

                <!-- Profile Photo Upload Form -->
                <form action="{{ url('/student/uploadPhoto', $data->email) }}"
                      method="post" enctype="multipart/form-data" id="photoUploadForm">
                    @csrf
                    @method('PUT')
                    <input type="file" name="profile_photo" id="profilePhotoInput"
                           class="profile-pic-input" accept="image/*">
                </form>

                <!-- Account Details Form -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="header-icon"><i class="fa fa-user-edit"></i></div>
                        <div>
                            <h2>Personal Details</h2>
                            <p>Update your personal and academic information below</p>
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

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i>
                                <ul style="margin:0; padding-left:16px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ url('/student/edit', $data->email) }}" method="post">
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="section-title"><i class="fa fa-user"></i> Name</div>
                            <div class="form-grid">
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-user"></i> First Name</label>
                                    <input class="field-input" type="text" name="first_name" value="{{ $data->first_name }}" placeholder="First name">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-user"></i> Middle Name</label>
                                    <input class="field-input" type="text" name="middle_name" value="{{ $data->middle_name }}" placeholder="Middle name">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-user"></i> Last Name</label>
                                    <input class="field-input" type="text" name="last_name" value="{{ $data->last_name }}" placeholder="Last name">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-user-tag"></i> Suffix</label>
                                    <input class="field-input" type="text" name="suffix" value="{{ $data->suffix }}" placeholder="e.g. Jr., III">
                                </div>
                            </div>

                            <!-- Contact -->
                            <div class="section-title"><i class="fa fa-address-card"></i> Contact & Identity</div>
                            <div class="form-grid">
                                <div class="field-group full-width">
                                    <label class="field-label"><i class="fa fa-map-marker-alt"></i> Address</label>
                                    <input class="field-input" type="text" name="address" value="{{ $data->address }}" placeholder="Complete address">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-phone"></i> Contact No.</label>
                                    <input class="field-input" type="text" name="contact_number" value="{{ $data->contact_number }}" placeholder="Contact number">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-birthday-cake"></i> Date of Birth</label>
                                    <input class="field-input" type="date" name="date_of_birth" value="{{ $data->date_of_birth }}">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-envelope"></i> E-mail</label>
                                    <input class="field-input" type="email" name="email" value="{{ $data->email }}" placeholder="Email address">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-id-card"></i> Student No.</label>
                                    <input class="field-input" type="text" name="studentNum" value="{{ $data->studentNum }}" placeholder="Student number">
                                </div>
                            </div>

                            <!-- Academic -->
                            <div class="section-title"><i class="fa fa-graduation-cap"></i> Academic Information</div>
                            <div class="form-grid">
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-university"></i> Course</label>
                                    <select name="course" class="field-select">
                                        @foreach ($course as $courseI)
                                            <option value="{{ $courseI->course }}"
                                                {{ $courseI->course == $data->course ? 'selected' : '' }}>
                                                {{ $courseI->course }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-users"></i> Year and Section</label>
                                    <input class="field-input" type="text" name="year_and_section" value="{{ $data->year_and_section }}" placeholder="e.g. 4-A">
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn-update">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
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
                        <div class="alert alert-danger" style="border-radius:10px; font-size:13px; padding:12px 16px; background:rgba(220,38,38,0.08); color:#dc2626; border:none;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <label class="modal-field-label"><i class="fa fa-lock"></i> Current Password</label>
                    <div class="pw-input-wrap">
                        <input type="password" id="current_password" name="current_password" placeholder="Enter current password">
                        <i class="fa fa-eye pw-toggle" id="toggleCurrent"></i>
                    </div>
                    <span class="text-error">@error('current_password') {{ $message }} @enderror</span>

                    <label class="modal-field-label"><i class="fa fa-key"></i> New Password</label>
                    <div class="pw-input-wrap">
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                        <i class="fa fa-eye pw-toggle" id="toggleNew"></i>
                    </div>
                    <span class="text-error">@error('new_password') {{ $message }} @enderror</span>

                    <label class="modal-field-label"><i class="fa fa-check-circle"></i> Confirm New Password</label>
                    <div class="pw-input-wrap">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                        <i class="fa fa-eye pw-toggle" id="toggleConfirm"></i>
                    </div>
                    <span class="text-error">@error('confirm_password') {{ $message }} @enderror</span>

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

    // Profile photo preview + auto-submit
    document.getElementById('profilePhotoInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const display = document.getElementById('profilePicDisplay');

            // Replace icon with image preview
            display.innerHTML = `<img src="${e.target.result}" alt="Profile Preview"
                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;

            // Also update sidebar avatar
            const sidebarAvatar = document.querySelector('.sidebar-user .user-avatar');
            if (sidebarAvatar) {
                sidebarAvatar.innerHTML = `<img src="${e.target.result}" alt="Profile"
                    style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
            }
        };

        reader.readAsDataURL(file);

        // Auto-submit the photo upload form
        document.getElementById('photoUploadForm').submit();
    });

    // Password toggles
    function setupToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input  = document.getElementById(inputId);
        if (!toggle || !input) return;

        toggle.addEventListener('click', function () {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupToggle('toggleCurrent', 'current_password');
        setupToggle('toggleNew',     'new_password');
        setupToggle('toggleConfirm', 'confirm_password');
    });
</script>

</body>
</html>