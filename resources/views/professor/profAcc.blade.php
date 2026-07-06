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
        .field-group.has-bubble { overflow: visible; }

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

        .field-input-wrap { position: relative; }

        .field-bubble {
            position: absolute;
            left: 0;
            right: 0;
            bottom: calc(100% + 10px);
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #fecaca;
            background: #fff7f7;
            color: var(--red-dark);
            font-size: 11.5px;
            line-height: 1.45;
            box-shadow: 0 12px 24px rgba(127, 0, 0, 0.12);
            visibility: hidden;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
            pointer-events: none;
            z-index: 5;
        }

        .field-bubble.active {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .field-bubble::after {
            content: '';
            position: absolute;
            left: 22px;
            top: 100%;
            width: 14px;
            height: 14px;
            background: #fff7f7;
            border-right: 1px solid #fecaca;
            border-bottom: 1px solid #fecaca;
            transform: rotate(45deg) translateY(-7px);
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

        .pw-input-wrap.has-bubble { overflow: visible; }

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

        .pw-bubble {
            position: absolute;
            left: 0;
            right: 0;
            bottom: calc(100% + 10px);
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #fecaca;
            background: #fff7f7;
            color: var(--red-dark);
            font-size: 11.5px;
            line-height: 1.45;
            box-shadow: 0 12px 24px rgba(127, 0, 0, 0.12);
            visibility: hidden;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
            pointer-events: none;
            z-index: 5;
        }

        .pw-bubble.active {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .pw-bubble::after {
            content: '';
            position: absolute;
            left: 22px;
            top: 100%;
            width: 14px;
            height: 14px;
            background: #fff7f7;
            border-right: 1px solid #fecaca;
            border-bottom: 1px solid #fecaca;
            transform: rotate(45deg) translateY(-7px);
        }

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

        .btn-modal-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

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
/* ===== DARK MODE ===== */
body.dark-mode { background: #000000; color: #e0e0e0; }
body.dark-mode .main-content { background: #000000; }
body.dark-mode .sidebar { box-shadow: 4px 0 24px rgba(0,0,0,0.4); }
body.dark-mode .topbar { background: #252525 !important; border-bottom: 1px solid #3a3a3a; }
body.dark-mode .menu-toggle { background: #3a3a3a; color: #e0e0e0; }
body.dark-mode .topbar-title { color: #999; }
body.dark-mode .topbar-badge { background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); color: #ff6b6b; }
body.dark-mode .page-content { background: #000000; }
body.dark-mode .page-header h1 { color: #fff; }
body.dark-mode .breadcrumb { color: #999; }

/* Profile Card */
body.dark-mode .profile-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
body.dark-mode .profile-name { color: #fff; }
body.dark-mode .profile-info-label { color: #888; }
body.dark-mode .profile-info-value { color: #fff; }
body.dark-mode .profile-divider { background: rgba(255,255,255,0.06); }
body.dark-mode .profile-info-row { border-bottom-color: rgba(255,255,255,0.05); }
body.dark-mode .btn-change-password { background: #3a3a3a; border-color: #4a4a4a; color: #e0e0e0; }
body.dark-mode .btn-change-password:hover { background: rgba(220,38,38,0.15); border-color: rgba(220,38,38,0.3); color: #ff6b6b; }

/* Form Card */
body.dark-mode .form-card { background: #2a2a2a; border: 1px solid #3a3a3a; box-shadow: 0 2px 12px rgba(0,0,0,0.3); }
body.dark-mode .form-card-header { background: #2a2a2a; border-bottom: 1px solid #3a3a3a; }
body.dark-mode .form-card-header h2 { color: #fff; }
body.dark-mode .form-card-header p { color: #999; }
body.dark-mode .form-section-title { color: #ff6b6b; }
body.dark-mode .form-section-title::after { background: rgba(220,38,38,0.2); }

/* Form Elements */
body.dark-mode .field-label { color: #e0e0e0; }
body.dark-mode .field-input { background: #3a3a3a; color: #e0e0e0; border: 1.5px solid #4a4a4a; }
body.dark-mode .field-input::placeholder { color: #777; }
body.dark-mode .field-input:focus { border-color: var(--red); background: #3a3a3a; box-shadow: 0 0 0 3px rgba(220,38,38,0.2); }

/* Form Card Footer */
body.dark-mode .form-card-footer { background: #2a2a2a !important; border-top: 1px solid #3a3a3a; }

/* Modal */
body.dark-mode .modal-content { background: #1a1a1a; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
body.dark-mode .modal-body { background: #1a1a1a !important; color: #e0e0e0; }
body.dark-mode .modal-footer { background: #2a2a2a !important; border-top: 1px solid #3a3a3a; }
body.dark-mode .modal-field-label { color: #e0e0e0; }
body.dark-mode .pw-input-wrap .form-control { background: #3a3a3a; color: #e0e0e0; border: 1.5px solid #4a4a4a; }
body.dark-mode .pw-input-wrap .form-control:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(220,38,38,0.2); background: #3a3a3a; }
body.dark-mode .pw-toggle { color: #aaa; }
body.dark-mode .pw-toggle:hover { color: var(--red); }
body.dark-mode .btn-modal-close { background: #3a3a3a; color: #e0e0e0; border: 1px solid #3a3a3a; }
body.dark-mode .btn-modal-close:hover { background: rgba(220,38,38,0.2); border-color: var(--red); color: #ff6b6b; }

/* Footer */
body.dark-mode .dashboard-footer { background: #1a1a1a !important; border-top: 1px solid #3a3a3a; color: #999; }
body.dark-mode .dashboard-footer a { color: #999; }
body.dark-mode .dashboard-footer a:hover { color: var(--red); }
body.dark-mode .dashboard-footer .divider { color: #3a3a3a; }
body.dark-mode .dashboard-footer .footer-copy { color: #999; }
body.dark-mode .dashboard-footer .footer-copy span { color: var(--red); }
body.dark-mode .dashboard-footer .footer-logo { opacity: 0.4; }
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
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard-check"></i></span>
            <span class="nav-label">Req. Status</span>
            <span class="tooltip-label">Req. Status</span>
        </a>
        <a href="{{ url('/professor/analytics') }}" class="nav-item">
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
            <a href="{{ url('/professor/evaluation') }}" class="nav-item{{ request()->is('professor/evaluation*') ? ' active' : '' }}">
            <span class="nav-icon"><i class="fa fa-star-half-alt"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/logout') }}" class="nav-item">
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
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
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

                <form action="{{ url('/professor/edit', $data->email) }}" method="post" id="professorProfileForm" data-email-check-url="{{ route('check-email-availability') }}" data-current-user-id="{{ $data->id }}">
                    @csrf
                    @method('PUT')

                    <div class="form-card-body">

                        <!-- Name Section -->
                        <div class="form-section-title">
                            <i class="fa fa-user"></i> Name
                        </div>
                        <div class="form-grid">
                            <div class="field-group has-bubble">
                                <label class="field-label" for="first_name">
                                    <i class="fa fa-id-card"></i> First Name
                                </label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="firstNameBubble"></div>
                                    <input class="field-input" type="text" id="first_name"
                                           name="first_name" value="{{ $data->first_name }}" autocapitalize="words" spellcheck="false">
                                </div>
                            </div>
                            <div class="field-group has-bubble">
                                <label class="field-label" for="middle_name">
                                    <i class="fa fa-id-card"></i> Middle Name
                                </label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="middleNameBubble"></div>
                                    <input class="field-input" type="text" id="middle_name"
                                           name="middle_name" value="{{ $data->middle_name }}" autocapitalize="words" spellcheck="false">
                                </div>
                            </div>
                            <div class="field-group has-bubble">
                                <label class="field-label" for="last_name">
                                    <i class="fa fa-id-card"></i> Last Name
                                </label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="lastNameBubble"></div>
                                    <input class="field-input" type="text" id="last_name"
                                           name="last_name" value="{{ $data->last_name }}" autocapitalize="words" spellcheck="false">
                                </div>
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
                            <div class="field-group has-bubble">
                                <label class="field-label" for="email">
                                    <i class="fa fa-envelope"></i> Email Address
                                </label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="emailBubble"></div>
                                    <input class="field-input" type="email" id="email"
                                           name="email" value="{{ $data->email }}">
                                </div>
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

            <form action="{{ url('/change_password', $data->id) }}" method="post" data-verify-current-url="{{ url('/change_password/verify-current', $data->id) }}">
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
                    <div class="pw-input-wrap has-bubble">
                        <div class="pw-bubble" id="currentPasswordBubble"></div>
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
                    <div class="pw-input-wrap has-bubble">
                        <div class="pw-bubble" id="newPasswordBubble"></div>
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
                    <div class="pw-input-wrap has-bubble">
                        <div class="pw-bubble" id="confirmPasswordBubble"></div>
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
                    <button type="submit" class="btn-modal-submit" id="updatePasswordButton" disabled>
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

        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const updatePasswordButton = document.getElementById('updatePasswordButton');
        const passwordForm = document.querySelector('#changePasswordModal form');
        const currentPasswordBubble = document.getElementById('currentPasswordBubble');
        const newPasswordBubble = document.getElementById('newPasswordBubble');
        const confirmPasswordBubble = document.getElementById('confirmPasswordBubble');
        const verifyCurrentUrl = passwordForm ? passwordForm.dataset.verifyCurrentUrl : '';
        let currentPasswordState = 'idle';
        let verifyCurrentPasswordTimer = null;
        let verifyCurrentPasswordSequence = 0;

        function isNewPasswordValid(value) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/.test(value);
        }

        function getNewPasswordUnmetRules(value) {
            const unmetRules = [];

            if (value.length < 8) {
                unmetRules.push('Use at least 8 characters.');
            }
            if (!/[A-Z]/.test(value)) {
                unmetRules.push('Add an uppercase letter.');
            }
            if (!/[a-z]/.test(value)) {
                unmetRules.push('Add a lowercase letter.');
            }
            if (!/\d/.test(value)) {
                unmetRules.push('Add a number.');
            }
            if (!/[!@#$%^&*]/.test(value)) {
                unmetRules.push('Add one symbol: ! @ # $ % ^ & *.');
            }
            if (/[^A-Za-z\d!@#$%^&*]/.test(value)) {
                unmetRules.push('Use only these symbols: ! @ # $ % ^ & *.');
            }

            return unmetRules;
        }

        function showBubble(bubble, messages) {
            if (!bubble) {
                return;
            }

            if (!messages.length) {
                bubble.innerHTML = '';
                bubble.classList.remove('active');
                return;
            }

            bubble.innerHTML = messages.map(function (message) {
                return '<div>' + message + '</div>';
            }).join('');
            bubble.classList.add('active');
        }

        function verifyCurrentPassword() {
            if (!currentPasswordInput || !verifyCurrentUrl) {
                return;
            }

            const currentPassword = currentPasswordInput.value.trim();

            if (!currentPassword.length) {
                currentPasswordState = 'idle';
                showBubble(currentPasswordBubble, []);
                syncPasswordModalState();
                return;
            }

            currentPasswordState = 'checking';
            syncPasswordModalState();

            const requestSequence = ++verifyCurrentPasswordSequence;

            fetch(verifyCurrentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                }),
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Verification failed.');
                }

                return response.json();
            })
            .then(function (data) {
                if (requestSequence !== verifyCurrentPasswordSequence) {
                    return;
                }

                currentPasswordState = data.valid ? 'valid' : 'invalid';
                showBubble(currentPasswordBubble, data.valid ? [] : [data.message || 'Current password is incorrect.']);
                syncPasswordModalState();
            })
            .catch(function () {
                if (requestSequence !== verifyCurrentPasswordSequence) {
                    return;
                }

                currentPasswordState = 'idle';
                showBubble(currentPasswordBubble, ['We could not verify the current password right now.']);
                syncPasswordModalState();
            });
        }

        function queueCurrentPasswordVerification() {
            if (verifyCurrentPasswordTimer) {
                clearTimeout(verifyCurrentPasswordTimer);
            }

            verifyCurrentPasswordTimer = setTimeout(verifyCurrentPassword, 350);
        }

        function syncPasswordModalState() {
            if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput || !updatePasswordButton) {
                return;
            }

            const currentPassword = currentPasswordInput.value.trim();
            const newPassword = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            const isCurrentPasswordPresent = currentPassword.length > 0;
            const isCurrentPasswordValid = currentPasswordState === 'valid';
            const unmetRules = getNewPasswordUnmetRules(newPassword);
            const hasValidNewPassword = newPassword.length > 0 && unmetRules.length === 0 && isNewPasswordValid(newPassword);
            const isConfirmPasswordMatching = newPassword === confirmPassword && confirmPassword.length > 0;
            const canSubmit = isCurrentPasswordValid && hasValidNewPassword && isConfirmPasswordMatching;

            updatePasswordButton.disabled = !canSubmit;

            if (!isCurrentPasswordPresent) {
                showBubble(currentPasswordBubble, []);
            } else if (currentPasswordState === 'checking') {
                showBubble(currentPasswordBubble, ['Checking current password...']);
            }

            showBubble(newPasswordBubble, newPassword.length > 0 && !hasValidNewPassword ? unmetRules : []);
            showBubble(confirmPasswordBubble, confirmPassword.length > 0 && !isConfirmPasswordMatching ? ['Confirmation password must match the new password.'] : []);
        }

        [currentPasswordInput, newPasswordInput, confirmPasswordInput].forEach(function (input) {
            if (!input) return;
            input.addEventListener('input', syncPasswordModalState);
        });

        if (currentPasswordInput) {
            currentPasswordInput.addEventListener('input', function () {
                currentPasswordState = currentPasswordInput.value.trim().length ? 'checking' : 'idle';
                queueCurrentPasswordVerification();
            });
            currentPasswordInput.addEventListener('blur', verifyCurrentPassword);
        }

        syncPasswordModalState();

        // Auto-open password modal if there are validation errors
        @if ($errors->has('current_password') || $errors->has('new_password') || $errors->has('confirm_password'))
            var modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            modal.show();
        @endif
    });

    document.addEventListener('DOMContentLoaded', function () {
        const profileForm = document.getElementById('professorProfileForm');
        if (!profileForm) {
            return;
        }

        const emailCheckUrl = profileForm.dataset.emailCheckUrl || '';
        const currentUserId = profileForm.dataset.currentUserId || '';
        const nameFieldConfig = [
            { id: 'first_name', bubbleId: 'firstNameBubble', optional: false },
            { id: 'middle_name', bubbleId: 'middleNameBubble', optional: true },
            { id: 'last_name', bubbleId: 'lastNameBubble', optional: false },
        ];
        const emailInput = document.getElementById('email');
        let emailCheckTimer = null;
        let emailRequestCounter = 0;

        function setInputErrorState(input, hasError) {
            if (!input) return;
            input.style.borderColor = hasError ? '#dc2626' : '';
            input.style.boxShadow = hasError ? '0 0 0 3px rgba(220,38,38,0.1)' : '';
        }

        function showFieldBubble(bubbleId, message) {
            const bubble = document.getElementById(bubbleId);
            if (!bubble) return;

            if (!message) {
                bubble.innerHTML = '';
                bubble.classList.remove('active');
                return;
            }

            const messages = Array.isArray(message) ? message : [message];
            bubble.innerHTML = messages.map(function (item) {
                return '<div>' + item + '</div>';
            }).join('');
            bubble.classList.add('active');
        }

        function sanitizeNameValue(value) {
            let sanitized = (value || '').replace(/[^\p{L}\s'\-]/gu, '');
            sanitized = sanitized.replace(/\s+/g, ' ');
            sanitized = sanitized.replace(/\s*-\s*/g, '-');
            sanitized = sanitized.replace(/\s*'\s*/g, "'");
            sanitized = sanitized.replace(/-{2,}/g, '-');
            sanitized = sanitized.replace(/'{2,}/g, "'");
            sanitized = sanitized.trim();

            return sanitized.replace(/(^|[\s'-])(\p{L})/gu, function (_, separator, character) {
                return separator + character.toUpperCase();
            });
        }

        function getNameValidationError(value, isOptional) {
            const trimmed = (value || '').trim();
            if (!trimmed) {
                return isOptional ? '' : 'This field is required.';
            }

            if (!/^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u.test(trimmed)) {
                return 'Use letters only. Apostrophes and hyphens are allowed.';
            }

            if (!/^[\p{Lu}]/u.test(trimmed)) {
                return 'Name must start with a capital letter.';
            }

            return '';
        }

        async function checkEmailAvailability(email) {
            const trimmedEmail = (email || '').trim();

            if (!trimmedEmail) {
                return { available: false, message: 'Email is required.' };
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
                return { available: false, message: 'Please enter a valid email address.' };
            }

            const requestId = ++emailRequestCounter;

            try {
                const response = await fetch(emailCheckUrl + '?email=' + encodeURIComponent(trimmedEmail) + '&ignore_id=' + encodeURIComponent(currentUserId), {
                    headers: { 'Accept': 'application/json' }
                });
                const payload = await response.json();

                if (requestId !== emailRequestCounter) {
                    return { available: false, message: 'Checking email availability...' };
                }

                return {
                    available: Boolean(payload.available),
                    message: payload.message || (payload.available ? 'Email is available.' : 'This email is already in use.')
                };
            } catch (error) {
                return { available: false, message: 'Unable to verify email right now. Please try again.' };
            }
        }

        nameFieldConfig.forEach(function (field) {
            const input = document.getElementById(field.id);
            if (!input) return;

            function syncNameField(showBubble) {
                input.value = sanitizeNameValue(input.value);
                const validationError = getNameValidationError(input.value, field.optional);
                input.setCustomValidity(validationError);
                setInputErrorState(input, Boolean(validationError));
                showFieldBubble(field.bubbleId, showBubble ? validationError : '');
            }

            input.addEventListener('input', function () {
                syncNameField(false);
            });

            input.addEventListener('blur', function () {
                syncNameField(Boolean(input.value.trim()));
            });
        });

        if (emailInput) {
            emailInput.addEventListener('input', function () {
                const value = emailInput.value.trim();

                if (emailCheckTimer) {
                    clearTimeout(emailCheckTimer);
                }

                if (!value) {
                    emailInput.setCustomValidity('Email is required.');
                    setInputErrorState(emailInput, false);
                    showFieldBubble('emailBubble', '');
                    return;
                }

                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    emailInput.setCustomValidity('Please enter a valid email address.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('emailBubble', 'Please enter a valid email address.');
                    return;
                }

                emailInput.setCustomValidity('');
                setInputErrorState(emailInput, false);
                showFieldBubble('emailBubble', '');

                emailCheckTimer = setTimeout(async function () {
                    const result = await checkEmailAvailability(value);
                    if (emailInput.value.trim() !== value) {
                        return;
                    }

                    if (!result.available) {
                        emailInput.setCustomValidity(result.message);
                        setInputErrorState(emailInput, true);
                        showFieldBubble('emailBubble', result.message);
                    } else {
                        emailInput.setCustomValidity('');
                        setInputErrorState(emailInput, false);
                        showFieldBubble('emailBubble', '');
                    }
                }, 350);
            });
        }

        profileForm.addEventListener('submit', async function (event) {
            if (profileForm.dataset.submitting === 'true') {
                return;
            }

            event.preventDefault();
            let hasError = false;

            nameFieldConfig.forEach(function (field) {
                const input = document.getElementById(field.id);
                if (!input) return;

                input.value = sanitizeNameValue(input.value);
                const validationError = getNameValidationError(input.value, field.optional);
                input.setCustomValidity(validationError);
                setInputErrorState(input, Boolean(validationError));
                showFieldBubble(field.bubbleId, validationError);
                if (validationError) {
                    hasError = true;
                }
            });

            if (emailInput) {
                const value = emailInput.value.trim();

                if (!value) {
                    emailInput.setCustomValidity('Email is required.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('emailBubble', 'Email is required.');
                    hasError = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    emailInput.setCustomValidity('Please enter a valid email address.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('emailBubble', 'Please enter a valid email address.');
                    hasError = true;
                } else {
                    const result = await checkEmailAvailability(value);
                    if (!result.available) {
                        emailInput.setCustomValidity(result.message);
                        setInputErrorState(emailInput, true);
                        showFieldBubble('emailBubble', result.message);
                        hasError = true;
                    } else {
                        emailInput.setCustomValidity('');
                        setInputErrorState(emailInput, false);
                        showFieldBubble('emailBubble', '');
                    }
                }
            }

            if (hasError) {
                return;
            }

            profileForm.dataset.submitting = 'true';
            profileForm.submit();
        });
    });
    // Dark mode is handled globally by dark-mode.js
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
