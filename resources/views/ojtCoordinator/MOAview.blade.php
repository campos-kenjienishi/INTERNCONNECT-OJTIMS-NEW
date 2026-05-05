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
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">

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

        body.dark-mode { background: #000000; color: #e0e0e0; }
        body.dark-mode .main-content { background: #000000; }
        body.dark-mode .sidebar { box-shadow: 4px 0 24px rgba(0,0,0,0.4); }

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

        body.dark-mode .topbar {
            background: #1a1a1a !important;
            border-bottom: 1px solid #3a3a3a !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3) !important;
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .menu-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #333; font-size: 18px; transition: all 0.2s;
        }

        .menu-toggle:hover { background: #fee2e2; color: var(--red); }

        body.dark-mode .menu-toggle {
            background: #2a2a2a !important;
            color: #e8e8e8 !important;
        }

        body.dark-mode .menu-toggle:hover {
            background: rgba(220,38,38,0.2) !important;
            color: #ff6b6b !important;
        }

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
            background: #fee2e2;
            color: var(--red);
            border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        .darkmode-toggle:active { transform: scale(0.95); }

        body.dark-mode .darkmode-toggle {
            background: #2a2a2a !important;
            border-color: #3a3a3a !important;
            color: #e8e8e8 !important;
        }

        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2) !important;
            color: #ff6b6b !important;
            border-color: rgba(220,38,38,0.3) !important;
            box-shadow: 0 6px 16px rgba(220,38,38,0.3) !important;
            transform: translateY(-2px) !important;
        }

        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }

        body.dark-mode .topbar-title {
            color: #999 !important;
        }

        body.dark-mode .topbar-title span {
            color: var(--red) !important;
        }

        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red-dark);
        }

        body.dark-mode .topbar-badge {
            background: rgba(220,38,38,0.15) !important;
            border: 1px solid rgba(220,38,38,0.3) !important;
            color: #ff6b6b !important;
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }
        body.dark-mode .page-content { background: #000000; }

        .page-header {
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }
        body.dark-mode .page-header h1 { color: #fff; }
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #888; margin-top: 6px;
        }

        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }
        body.dark-mode .breadcrumb { color: #999; }
        body.dark-mode .breadcrumb-nav { color: #999; }

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

        .student-search-wrap { margin-bottom: 12px; }
        .student-search-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.2s;
        }
        .student-search-input:focus {
            border-color: #fca5a5;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }
        .student-no-match {
            display: none;
            text-align: center;
            padding: 22px 16px;
            color: #888;
            font-size: 12.5px;
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

/* ===== DARK MODE: COMPREHENSIVE STYLING ===== */
body.dark-mode {
    background: #000000 !important;
    color: #e8e8e8 !important;
}

body.dark-mode .main-content {
    background: #000000 !important;
}

body.dark-mode .page-content {
    background: #000000 !important;
}

body.dark-mode .topbar {
    background: #1a1a1a !important;
    border-bottom: 1px solid #3a3a3a !important;
    box-shadow: 0 2px 12px rgba(0,0,0,0.3) !important;
}

body.dark-mode .menu-toggle {
    background: #2a2a2a !important;
    color: #e8e8e8 !important;
}

body.dark-mode .menu-toggle:hover {
    background: rgba(220,38,38,0.2) !important;
    color: #ff6b6b !important;
}

body.dark-mode .page-header h1 {
    color: #fff !important;
}

body.dark-mode .breadcrumb {
    color: #999 !important;
}

body.dark-mode .breadcrumb a {
    color: #ff6b6b !important;
}

body.dark-mode .breadcrumb-nav {
    color: #999 !important;
}

/* Panel Cards */
body.dark-mode .panel-card {
    background: #1a1a1a !important;
    border: 1px solid #3a3a3a !important;
}

body.dark-mode .panel-card-header {
    background: #1a1a1a !important;
    border-bottom: 1px solid #3a3a3a !important;
}

body.dark-mode .panel-card-header h2 {
    color: #fff !important;
}

body.dark-mode .panel-card-header p {
    color: #999 !important;
}

body.dark-mode .panel-card-body {
    color: #e8e8e8 !important;
    background: #1a1a1a !important;
}

body.dark-mode .panel-header-icon {
    background: rgba(220,38,38,0.2) !important;
    color: #ff6b6b !important;
}

/* Info Items */
body.dark-mode .info-item {
    background: #2a2a2a !important;
    border: 1px solid #3a3a3a !important;
    color: #e8e8e8 !important;
}

body.dark-mode .info-item-icon {
    background: rgba(220,38,38,0.2) !important;
    color: #ff6b6b !important;
}

body.dark-mode .info-item-label {
    color: #999 !important;
}

body.dark-mode .info-item-value {
    color: #e8e8e8 !important;
}

/* Student Cards */
body.dark-mode .student-card {
    background: #2a2a2a !important;
    border: 1px solid #3a3a3a !important;
    color: #e8e8e8 !important;
}

body.dark-mode .student-card:hover {
    border-color: rgba(220,38,38,0.5) !important;
    background: rgba(220,38,38,0.1) !important;
}

body.dark-mode .student-card-header {
    border-bottom: 1px solid rgba(255,255,255,0.1) !important;
}

body.dark-mode .student-avatar {
    background: rgba(220,38,38,0.3) !important;
    color: #ff6b6b !important;
}

body.dark-mode .student-name {
    color: #fff !important;
}

body.dark-mode .student-course {
    background: rgba(124,58,237,0.2) !important;
    color: #c084fc !important;
}

body.dark-mode .student-detail-row {
    color: #999 !important;
}

body.dark-mode .student-detail-row i {
    color: #ff6b6b !important;
}

/* Search Input */
body.dark-mode .student-search-input {
    background: #2a2a2a !important;
    color: #e8e8e8 !important;
    border: 1.5px solid #3a3a3a !important;
}

body.dark-mode .student-search-input::placeholder {
    color: #666 !important;
}

body.dark-mode .student-search-input:focus {
    border-color: var(--red) !important;
    box-shadow: 0 0 0 3px rgba(220,38,38,0.2) !important;
}

body.dark-mode .student-list::-webkit-scrollbar-thumb {
    background: rgba(220,38,38,0.3) !important;
}

body.dark-mode .student-no-match {
    color: #999 !important;
}

/* MOA Viewer */
body.dark-mode .moa-viewer-card {
    background: #1a1a1a !important;
    border: 1px solid #3a3a3a !important;
}

body.dark-mode .moa-viewer-card iframe {
    border-color: #3a3a3a !important;
    background: #2a2a2a !important;
}

body.dark-mode .moa-viewer-header {
    background: #1a1a1a !important;
    border-bottom: 1px solid #3a3a3a !important;
}

body.dark-mode .moa-viewer-header h3 {
    color: #fff !important;
}

body.dark-mode .moa-viewer-header p {
    color: #999 !important;
}

/* Footer */
body.dark-mode .dashboard-footer {
    background: #1a1a1a !important;
    border-top: 1px solid #3a3a3a !important;
    color: #999 !important;
}

body.dark-mode .dashboard-footer a {
    color: #999 !important;
}

body.dark-mode .dashboard-footer a:hover {
    color: var(--red) !important;
}

body.dark-mode .dashboard-footer .divider {
    color: #3a3a3a !important;
}

body.dark-mode .dashboard-footer .footer-copy span {
    color: var(--red) !important;
}

/* General Elements */
body.dark-mode .card {
    background: #1a1a1a !important;
    border: 1px solid #3a3a3a !important;
}

body.dark-mode .topbar-title {
    color: #999 !important;
}

body.dark-mode .topbar-title span {
    color: var(--red) !important;
}

body.dark-mode .topbar-badge {
    background: rgba(220,38,38,0.15) !important;
    border: 1px solid rgba(220,38,38,0.3) !important;
    color: #ff6b6b !important;
}

body.dark-mode .darkmode-toggle {
    background: #2a2a2a !important;
    color: #e8e8e8 !important;
    border-color: #3a3a3a !important;
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
        <a href="{{ url('/analytics') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
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
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
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
                        @php
                            $displayStudents = collect(array_filter(array_map('trim', explode(',', (string) ($company->student_names_display ?? '')))))->values();
                            $linkedStudents = $company->students;
                            $studentCount = $displayStudents->isNotEmpty() ? $displayStudents->count() : $linkedStudents->count();
                        @endphp
                        <h2>Student List</h2>
                        <p>
                            {{ $studentCount }}
                            {{ $studentCount == 1 ? 'student' : 'students' }}
                            assigned to this company
                        </p>
                    </div>
                </div>
                <div class="panel-card-body">
                    <div class="student-search-wrap">
                        <input
                            type="text"
                            id="studentSearchInput"
                            class="student-search-input"
                            placeholder="Search assigned students by name"
                        >
                    </div>
                    <div class="student-list">
                        @if ($displayStudents->isNotEmpty())
                            @foreach ($displayStudents as $displayStudent)
                                @php $matchedStudent = $linkedStudents->firstWhere('full_name', $displayStudent); @endphp
                                @if ($matchedStudent)
                                <div class="student-card">
                                    <div class="student-card-header">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($matchedStudent->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $matchedStudent->full_name }}</div>
                                            <span class="student-course">{{ $matchedStudent->course }}</span>
                                        </div>
                                    </div>

                                    <div class="student-detail-row">
                                        <i class="fa fa-id-card"></i>
                                        <span><strong>Student No:</strong> {{ $matchedStudent->studentNum }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-envelope"></i>
                                        <span>{{ $matchedStudent->email }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-birthday-cake"></i>
                                        <span><strong>DOB:</strong> {{ $matchedStudent->date_of_birth }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-phone"></i>
                                        <span>{{ $matchedStudent->contact_number }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-map-marker-alt"></i>
                                        <span>{{ $matchedStudent->address }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-layer-group"></i>
                                        <span>{{ $matchedStudent->year_and_section }}</span>
                                    </div>
                                    <div class="student-detail-row">
                                        <i class="fa fa-chalkboard-teacher"></i>
                                        <span><strong>Adviser:</strong> {{ $matchedStudent->adviser_name }}</span>
                                    </div>
                                </div>
                                @else
                                <div class="student-card">
                                    <div class="student-card-header">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($displayStudent, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="student-name">{{ $displayStudent }}</div>
                                            <span class="student-course">{{ $company->course ?: 'Manual entry' }}</span>
                                        </div>
                                    </div>

                                    <div class="student-detail-row">
                                        <i class="fa fa-keyboard"></i>
                                        <span>This student was entered manually for this MOA.</span>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            @forelse ($linkedStudents as $student)
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
                        @endif
                        <div id="studentNoMatch" class="student-no-match">
                            No assigned student matched your search.
                        </div>
                        </div>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    // ====== DARK MODE TOGGLE ======
    // ====== SIDEBAR TOGGLE ======
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle  = document.getElementById('menuToggle');
        const overlay     = document.getElementById('sidebarOverlay');

        if (menuToggle && sidebar && mainContent) {
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
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }
    });

    // ====== STUDENT SEARCH ======
    document.addEventListener('DOMContentLoaded', function() {
        const studentSearchInput = document.getElementById('studentSearchInput');
        if (studentSearchInput) {
            studentSearchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const cards = Array.from(document.querySelectorAll('.student-list .student-card'));
                let visibleCount = 0;

                cards.forEach(function (card) {
                    const nameEl = card.querySelector('.student-name');
                    const studentName = (nameEl ? nameEl.textContent : '').toLowerCase();
                    const isVisible = !query || studentName.includes(query);
                    card.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        visibleCount++;
                    }
                });

                const noMatchEl = document.getElementById('studentNoMatch');
                if (noMatchEl) {
                    noMatchEl.style.display = (cards.length > 0 && visibleCount === 0) ? 'block' : 'none';
                }
            });
        }
    });
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>

<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>