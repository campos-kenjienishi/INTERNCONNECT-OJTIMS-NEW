@props([
    'role' => 'public',
    'title' => 'InternConnect',
    'pageTitle' => '',
    'pageTitleHtml' => null,
    'pageSubtitle' => '',
    'pageSubtitleHtml' => null,
    'backUrl' => null,
    'backLabel' => 'Back',
    'headerBreadcrumbs' => null,
    'headerActionUrl' => null,
    'headerActionLabel' => 'Back',
    'headerActionIcon' => 'fa-arrow-left',
])

@php
    $user = auth()->user();
    $isAuthenticatedShell = in_array($role, ['student', 'professor', 'coordinator'], true);

    $sidebarLinks = [];
    if ($role === 'student') {
        $sidebarLinks = [
            ['url' => url('/student/home'), 'icon' => 'fa-home', 'label' => 'Home', 'pattern' => 'student/home*'],
            ['url' => url('/student/ojtinfo'), 'icon' => 'fa-layer-group', 'label' => 'OJT Information', 'pattern' => 'student/ojtinfo*'],
            ['url' => url('/student/class'), 'icon' => 'fa-clipboard', 'label' => 'Class', 'pattern' => 'student/class*'],
            ['url' => url('/student/files'), 'icon' => 'fa-download', 'label' => 'Downloadable Files', 'pattern' => 'student/files*'],
            ['url' => url('/student/MOA'), 'icon' => 'fa-file-alt', 'label' => 'MOA', 'pattern' => 'student/MOA*'],
            ['url' => url('/student/requirements'), 'icon' => 'fa-cloud-upload-alt', 'label' => 'Requirements', 'pattern' => 'student/requirements*'],
            ['url' => url('/student/evaluation'), 'icon' => 'fa-star-half-alt', 'label' => 'Evaluation', 'pattern' => 'student/evaluation*'],
        ];
    } elseif ($role === 'professor') {
        $sidebarLinks = [
            ['url' => url('/professor/home'), 'icon' => 'fa-home', 'label' => 'Home', 'pattern' => 'professor/home*'],
            ['url' => url('/professor/class'), 'icon' => 'fa-clipboard', 'label' => 'Class', 'pattern' => 'professor/class*'],
            ['url' => url('/professor/analytics'), 'icon' => 'fa-chart-line', 'label' => 'Analytics', 'pattern' => 'professor/analytics*'],
            ['url' => url('/professor/maintain'), 'icon' => 'fa-cogs', 'label' => 'Maintenance', 'pattern' => 'professor/maintain*'],
            ['url' => url('/professor/evaluation'), 'icon' => 'fa-star-half-alt', 'label' => 'Evaluation', 'pattern' => 'professor/evaluation*'],
        ];
    } elseif ($role === 'coordinator') {
        $sidebarLinks = [
            ['url' => url('/dashboard'), 'icon' => 'fa-home', 'label' => 'Dashboard', 'pattern' => 'dashboard*'],
            ['url' => url('/studentLists'), 'icon' => 'fa-users', 'label' => 'Students', 'pattern' => 'studentLists*'],
            ['url' => url('/professorTab'), 'icon' => 'fa-chalkboard-teacher', 'label' => 'Professors', 'pattern' => 'professorTab*'],
            ['url' => url('/uploadpage'), 'icon' => 'fa-file-upload', 'label' => 'Upload Templates', 'pattern' => 'uploadpage*'],
            ['url' => url('/maintenance'), 'icon' => 'fa-cogs', 'label' => 'Maintenance', 'pattern' => 'maintenance*'],
            ['url' => url('/MOA'), 'icon' => 'fa-file-contract', 'label' => 'MOA', 'pattern' => 'MOA*'],
            ['url' => url('/reports'), 'icon' => 'fa-chart-bar', 'label' => 'Reports', 'pattern' => 'reports*'],
            ['url' => url('/auditlog'), 'icon' => 'fa-clipboard-list', 'label' => 'Audit Log', 'pattern' => 'auditlog*'],
        ];
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="{{ url('/assets/js/dark-mode.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/student_evaluation-responsive.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red: #dc2626;
            --red-dark: #991b1b;
            --red-deeper: #7f0000;
            --sidebar-w: 260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h: 64px;
            --bg: #f5f5f5;
            --surface: #fff;
            --surface-2: #fafafa;
            --surface-3: #f3f4f6;
            --border: #e5e7eb;
            --border-2: rgba(0,0,0,0.04);
            --text-primary: #1a1a1a;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --topbar-bg: #fff;
            --input-border: #e5e5e5;
            --row-hover: #fff5f5;
            --toggle-bg: #f5f5f5;
            --toggle-color: #333;
            --footer-bg: #fff;
        }

        body.dark-mode {
            --bg: #0a0a0a;
            --surface: #1a1a1a;
            --surface-2: #252525;
            --surface-3: #303030;
            --border: #2a2a2a;
            --border-2: rgba(255,255,255,0.05);
            --text-primary: #e8e8e8;
            --text-secondary: #a0a0a0;
            --text-muted: #707070;
            --topbar-bg: #1a1a1a;
            --input-border: #3a3a3a;
            --row-hover: rgba(220,38,38,0.1);
            --toggle-bg: #2a2a2a;
            --toggle-color: #e8e8e8;
            --footer-bg: #1a1a1a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        a { color: inherit; }

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

        .sidebar-brand,
        .sidebar-user {
            display: flex; align-items: center; gap: 12px;
            padding: 22px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            flex-shrink: 0;
        }

        .sidebar-user {
            padding: 16px 18px;
            transition: background 0.2s;
        }

        .sidebar-user:hover { background: rgba(255,255,255,0.05); }

        .sidebar-brand img,
        .footer-logo {
            object-fit: contain;
        }

        .sidebar-brand img {
            width: 36px; height: 36px;
            flex-shrink: 0;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.2));
        }

        .sidebar-brand-text,
        .user-info {
            display: flex; flex-direction: column;
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

        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .nav-label {
            opacity: 0; width: 0;
        }

        .user-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(239,68,68,0.25);
            border: 1.5px solid rgba(239,68,68,0.4);
            display: flex; align-items: center; justify-content: center;
            color: #fca5a5; font-size: 16px; flex-shrink: 0; overflow: hidden;
        }

        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .user-name { font-size: 13px; font-weight: 600; color: #fff; display: block; text-overflow: ellipsis; overflow: hidden; }
        .user-role { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
        }

        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(239,68,68,0.3); border-radius: 10px; }

        .nav-item {
            display: flex; align-items: center; gap: 14px;
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

        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(239,68,68,0.15); border-left-color: #ef4444; }
        .nav-item .nav-icon { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }
        .nav-item .nav-label { transition: opacity 0.25s; overflow: hidden; }
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

        .main-content {
            margin-left: var(--sidebar-w);
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.public-mode {
            margin-left: 0;
        }

        .main-content.expanded { margin-left: var(--sidebar-w-collapsed); }

        .topbar {
            height: var(--topbar-h);
            background: var(--topbar-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid var(--border);
            transition: background 0.3s, border-color 0.3s;
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .menu-toggle,
        .darkmode-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--toggle-bg);
            border: 1px solid var(--border);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--toggle-color);
            font-size: 18px;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .darkmode-toggle { font-size: 16px; }

        .menu-toggle:hover,
        .darkmode-toggle:hover {
            background: #fee2e2;
            color: var(--red);
            border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        body.dark-mode .menu-toggle:hover,
        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2);
            color: #ff6b6b;
            border-color: rgba(220,38,38,0.3);
            box-shadow: 0 6px 16px rgba(220,38,38,0.3);
        }

        .topbar-title { font-size: 13.5px; font-weight: 500; color: var(--text-secondary); }
        .topbar-title span { color: var(--red); font-weight: 600; }

        .topbar-badge {
            display: flex; align-items: center; gap: 8px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 20px; padding: 6px 14px;
            font-size: 12.5px; font-weight: 600; color: var(--red);
        }

        body.dark-mode .topbar-badge {
            background: rgba(220,38,38,0.15);
            border-color: rgba(220,38,38,0.3);
            color: #ff6b6b;
        }

        .page-content {
            padding: 28px;
            flex: 1;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .page-header h1 span { color: var(--red); }

        .page-header .subtext {
            margin-top: 6px;
            color: var(--text-secondary);
            font-size: 12.5px;
            font-weight: 500;
        }

        .page-header .subtext a {
            color: var(--red);
            text-decoration: none;
            font-weight: 600;
        }

        .page-header .subtext a:hover { text-decoration: underline; }

        .page-header .subtext .crumb-sep {
            color: var(--text-secondary);
            margin: 0 6px;
            font-size: 10px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
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
            text-decoration: none;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            color: #fff;
            text-decoration: none;
        }

        .btn-back i { font-size: 12px; }

        .card-shell {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid var(--border-2);
            overflow: hidden;
        }

        .card-shell .card-header-shell {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card-shell .card-header-shell h2 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-shell .card-header-shell h2 .header-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 14px;
            flex-shrink: 0;
        }

        body.dark-mode .card-shell .card-header-shell h2 .header-icon {
            background: rgba(220,38,38,0.2);
            color: #ff6b6b;
        }

        .card-shell .card-body-shell { padding: 24px; }
        .card-shell .card-body-shell.tight { padding: 0; }

        .panel-note {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #7f1d1d;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .flash-alert {
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-size: 13px;
            border: 1px solid transparent;
        }

        .flash-alert.success {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .flash-alert.error {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .flash-alert.info {
            background: #dbeafe;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        body.dark-mode .flash-alert.success {
            background: rgba(22,163,74,0.16);
            border-color: rgba(34,197,94,0.28);
            color: #86efac;
        }

        body.dark-mode .flash-alert.error {
            background: rgba(220,38,38,0.16);
            border-color: rgba(248,113,113,0.28);
            color: #fca5a5;
        }

        body.dark-mode .flash-alert.info {
            background: rgba(37,99,235,0.16);
            border-color: rgba(96,165,250,0.28);
            color: #93c5fd;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .form-group { display: flex; flex-direction: column; gap: 8px; }

        .form-label-shell {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control-shell,
        .form-select-shell,
        .form-textarea-shell {
            width: 100%;
            border: 1px solid var(--input-border);
            border-radius: 12px;
            background: var(--surface);
            color: var(--text-primary);
            padding: 11px 14px;
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .form-textarea-shell { min-height: 120px; resize: vertical; }

        .form-control-shell:focus,
        .form-select-shell:focus,
        .form-textarea-shell:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }

        .form-hint {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.45;
        }

        .stacked-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .section-gap { margin-top: 20px; }

        .muted-text {
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.55;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .text-center-shell { text-align: center; }

        .badge-like {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-like.success { background: #dcfce7; color: #166534; }
        .badge-like.warning { background: #fef3c7; color: #92400e; }
        .badge-like.secondary { background: #e5e7eb; color: #374151; }
        .badge-like.dark { background: #1f2937; color: #fff; }
        .badge-like.primary { background: #dbeafe; color: #1d4ed8; }

        body.dark-mode .badge-like.success { background: rgba(22,163,74,0.18); color: #86efac; }
        body.dark-mode .badge-like.warning { background: rgba(245,158,11,0.18); color: #fbbf24; }
        body.dark-mode .badge-like.secondary { background: rgba(107,114,128,0.22); color: #e5e7eb; }
        body.dark-mode .badge-like.dark { background: rgba(17,24,39,0.9); color: #fff; }
        body.dark-mode .badge-like.primary { background: rgba(37,99,235,0.18); color: #93c5fd; }

        .rating-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .rating-row {
            display: grid;
            grid-template-columns: 1.2fr 2fr 120px;
            gap: 12px;
            align-items: center;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
        }

        .rating-row .rating-score {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            font-weight: 700;
            color: var(--red);
        }

        .rating-row .rating-section {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .rating-row .rating-label {
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .rating-row .rating-select {
            width: 100%;
        }

        .evaluation-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .summary-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 16px;
        }

        .summary-card .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            margin-bottom: 6px;
            font-weight: 700;
        }

        .summary-card .value {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        body.dark-mode .panel-note {
            background: rgba(220,38,38,0.12);
            border-color: rgba(220,38,38,0.28);
            color: #fca5a5;
        }

        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .btn-eval {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-eval-primary {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: #fff;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-eval-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            color: #fff;
        }

        .btn-eval-outline {
            background: var(--surface);
            color: var(--text-primary);
            border-color: var(--border);
        }

        .btn-eval-outline:hover {
            border-color: #fecaca;
            color: var(--red);
            background: #fff5f5;
        }

        .btn-eval-success {
            background: #16a34a;
            color: #fff;
            box-shadow: 0 4px 16px rgba(22,163,74,0.2);
        }

        .btn-eval-success:hover { color: #fff; }

        .btn-eval-danger {
            background: #dc2626;
            color: #fff;
            box-shadow: 0 4px 16px rgba(220,38,38,0.2);
        }

        .btn-eval-muted {
            background: #6b7280;
            color: #fff;
        }

        .table-shell {
            width: 100%;
            border-collapse: collapse;
        }

        .table-shell thead th {
            background: var(--surface-2);
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .table-shell tbody td {
            padding: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 14px;
        }

        .table-shell tbody tr:hover td { background: var(--row-hover); }
        .table-shell tbody tr:last-child td { border-bottom: none; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .status-badge.success { background: #dcfce7; color: #166534; }
        .status-badge.warning { background: #fef3c7; color: #92400e; }
        .status-badge.secondary { background: #e5e7eb; color: #374151; }
        .status-badge.dark { background: #1f2937; color: #fff; }
        .status-badge.primary { background: #dbeafe; color: #1d4ed8; }

        body.dark-mode .status-badge.success { background: rgba(22,163,74,0.18); color: #86efac; }
        body.dark-mode .status-badge.warning { background: rgba(245,158,11,0.18); color: #fbbf24; }
        body.dark-mode .status-badge.secondary { background: rgba(107,114,128,0.22); color: #e5e7eb; }
        body.dark-mode .status-badge.dark { background: rgba(17,24,39,0.9); color: #fff; }
        body.dark-mode .status-badge.primary { background: rgba(37,99,235,0.18); color: #93c5fd; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .dashboard-footer {
            background: var(--footer-bg);
            border-top: 1px solid var(--border);
            color: var(--text-secondary);
            text-align: center;
            padding: 18px 28px;
            font-size: 12.5px;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            transition: background 0.3s, border-color 0.3s;
        }

        .dashboard-footer .footer-left { display: flex; align-items: center; gap: 8px; }
        .dashboard-footer .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .dashboard-footer .footer-copy { font-size: 12.5px; color: var(--text-muted); font-weight: 500; }
        .dashboard-footer .footer-copy span { color: var(--red); font-weight: 600; }
        .dashboard-footer .footer-links { display: flex; align-items: center; gap: 6px; }
        .dashboard-footer a { color: var(--text-secondary); text-decoration: none; font-weight: 500; font-size: 12.5px; transition: color 0.2s; }
        .dashboard-footer a:hover { color: var(--red); }
        .dashboard-footer .divider { color: var(--border); margin: 0 2px; }

        .dashboard-footer.public-footer {
            justify-content: center;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
        }

        @media print {
            .sidebar, .sidebar-overlay, .topbar, .dashboard-footer, .no-print {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .page-content {
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }
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
        }

        @media (max-width: 640px) {
            .topbar-title { display: none; }
            .page-header h1 { font-size: 21px; }
            .card-shell .card-body-shell { padding: 18px; }
            .card-shell .card-header-shell { padding: 16px 18px; }
            .dashboard-footer { justify-content: center; text-align: center; }
        }
    </style>
</head>
<body>
@if($isAuthenticatedShell)
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ $role === 'student' ? url('/student/accountinfo') : ($role === 'professor' ? url('/professor/accountinfo') : url('/accountinfo')) }}" class="sidebar-user">
        <div class="user-avatar">
            @if($user && !empty($user->profile_photo))
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile">
            @else
                <i class="fa {{ $role === 'coordinator' ? 'fa-user-tie' : 'fa-user' }}"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $user ? trim($user->first_name . ' ' . $user->last_name) : 'Account' }}</span>
            <span class="user-role">
                {{ $role === 'student' ? 'Student' : ($role === 'professor' ? 'Professor' : 'OJT Coordinator') }}
            </span>
        </div>
    </a>

    <nav class="sidebar-nav">
        @foreach($sidebarLinks as $link)
            <a href="{{ $link['url'] }}" class="nav-item{{ request()->is($link['pattern']) ? ' active' : '' }}">
                <span class="nav-icon"><i class="fa {{ $link['icon'] }}"></i></span>
                <span class="nav-label">{{ $link['label'] }}</span>
                <span class="tooltip-label">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/logout') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-sign-out-alt"></i></span>
            <span class="nav-label">Log Out</span>
            <span class="tooltip-label">Log Out</span>
        </a>
    </div>
</div>
@endif

<div class="main-content{{ $isAuthenticatedShell ? '' : ' public-mode' }}" id="mainContent">
    <div class="topbar">
        <div class="topbar-left">
            @if($isAuthenticatedShell)
                <button class="menu-toggle" id="menuToggle" type="button">
                    <i class="fa fa-bars"></i>
                </button>
                <button class="darkmode-toggle" id="darkmodeToggle" type="button" title="Toggle Dark Mode">
                    <i class="fa fa-moon" id="darkmodeIcon"></i>
                </button>
            @endif
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa {{ $role === 'student' ? 'fa-graduation-cap' : ($role === 'professor' ? 'fa-chalkboard-teacher' : 'fa-star-half-alt') }}"></i>
                {{ $role === 'student' ? 'Student Portal' : ($role === 'professor' ? 'Professor Portal' : ($role === 'coordinator' ? 'Coordinator Portal' : 'Evaluation Portal')) }}
            </div>
        </div>
    </div>

    <div class="page-content">
        @if($pageTitle || $pageTitleHtml || $pageSubtitle || $pageSubtitleHtml || $backUrl || !empty($headerBreadcrumbs) || $headerActionUrl)
            <div class="page-header">
                <div>
                    @if($pageTitleHtml)
                        <h1>{!! $pageTitleHtml !!}</h1>
                    @elseif($pageTitle)
                        <h1>{{ $pageTitle }}</h1>
                    @endif
                    @if($pageSubtitleHtml)
                        <div class="subtext">{!! $pageSubtitleHtml !!}</div>
                    @elseif($pageSubtitle)
                        <div class="subtext">{{ $pageSubtitle }}</div>
                    @endif
                    @if(!empty($headerBreadcrumbs))
                        <div class="breadcrumb">
                            @foreach($headerBreadcrumbs as $index => $crumb)
                                @if(!empty($crumb['url']))
                                    <a href="{{ $crumb['url'] }}">
                                        @if(!empty($crumb['icon']))
                                            <i class="fa {{ $crumb['icon'] }}"></i>
                                        @endif
                                        {{ $crumb['label'] }}
                                    </a>
                                @else
                                    <span>{{ $crumb['label'] }}</span>
                                @endif
                                @if($index < count($headerBreadcrumbs) - 1)
                                    <i class="fa fa-chevron-right"></i>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                @if($headerActionUrl)
                    <a href="{{ $headerActionUrl }}" class="btn-back">
                        <i class="fa {{ $headerActionIcon }}"></i>
                        <span>{{ $headerActionLabel }}</span>
                    </a>
                @endif
            </div>
        @endif

        {{ $slot }}
    </div>

    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
        <span class="footer-copy">
            © 1998–2026 <span>Polytechnic University of the Philippines</span>
        </span>
    </div>
    <div class="footer-links">
        <a href="https://www.pup.edu.ph/" target="_blank" rel="noreferrer">
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

<script>
    (function () {
        // ── Sidebar toggle only ──
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (menuToggle && sidebar && mainContent) {
            menuToggle.addEventListener('click', () => {
                if (window.innerWidth <= 900) {
                    sidebar.classList.toggle('mobile-open');
                    if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });
        }

        if (sidebarOverlay && sidebar) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });
        }
    })();
</script>
</body>
</html>
