<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Requirement Status</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/css/dark-mode.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --red: #dc2626;
            --red-dark: #991b1b;
            --sidebar-w: 260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h: 64px;
        }
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #1a1a1a; min-height: 100vh; }
        .sidebar { position: fixed; inset: 0 auto 0 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(160deg, #1a0000 0%, #4a0000 50%, #7f0000 100%); z-index: 1000; transition: width .3s; overflow: hidden; box-shadow: 4px 0 24px rgba(0,0,0,.18); display: flex; flex-direction: column; }
        .sidebar.collapsed { width: var(--sidebar-w-collapsed); }
        .sidebar-brand, .sidebar-user, .nav-item { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .sidebar-brand { padding: 22px 18px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-brand img { width: 36px; height: 36px; object-fit: contain; }
        .sidebar-brand-name { color: #fff; font-size: 16px; font-weight: 800; line-height: 1; white-space: nowrap; }
        .sidebar-brand-name span { color: #fca5a5; }
        .sidebar-brand-sub { color: rgba(255,255,255,.45); font-size: 9px; text-transform: uppercase; letter-spacing: 1.4px; margin-top: 3px; white-space: nowrap; }
        .sidebar.collapsed .sidebar-brand-text, .sidebar.collapsed .user-info, .sidebar.collapsed .nav-label { opacity: 0; width: 0; overflow: hidden; }
        .sidebar-user { padding: 16px 18px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .user-avatar { width: 38px; height: 38px; border-radius: 50%; background: rgba(239,68,68,.25); border: 1.5px solid rgba(239,68,68,.4); color: #fca5a5; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-name { color: #fff; font-size: 13px; font-weight: 600; display: block; white-space: nowrap; }
        .user-role { color: rgba(255,255,255,.4); font-size: 10px; text-transform: uppercase; letter-spacing: 1px; white-space: nowrap; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(239,68,68,.3); border-radius: 10px; }
        .nav-item { color: rgba(255,255,255,.55); padding: 12px 20px; border-left: 3px solid transparent; font-size: 14px; font-weight: 500; transition: all .25s; position: relative; white-space: nowrap; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,.06); }
        .nav-item.active { color: #fff; background: rgba(239,68,68,.15); border-left-color: #ef4444; }
        .nav-icon { width: 22px; text-align: center; font-size: 18px; flex-shrink: 0; }
        .nav-label { transition: opacity .25s; overflow: hidden; }
        .tooltip-label { position: absolute; left: calc(var(--sidebar-w-collapsed) + 8px); background: #1a0000; color: #fff; font-size: 12px; padding: 5px 10px; border-radius: 6px; white-space: nowrap; pointer-events: none; opacity: 0; transition: opacity .2s; box-shadow: 0 4px 12px rgba(0,0,0,.3); z-index: 9999; }
        .sidebar.collapsed .nav-item:hover .tooltip-label { opacity: 1; }
        .sidebar-footer { padding: 12px 0; border-top: 1px solid rgba(255,255,255,.07); flex-shrink: 0; }
        .main-content { margin-left: var(--sidebar-w); min-height: 100vh; transition: margin-left .3s; display: flex; flex-direction: column; }
        .main-content.expanded { margin-left: var(--sidebar-w-collapsed); }
        .topbar { height: var(--topbar-h); background: #fff; border-bottom: 1px solid rgba(0,0,0,.05); box-shadow: 0 2px 12px rgba(0,0,0,.06); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 100; }
        .topbar-left { display: flex; align-items: center; gap: 16px; min-width: 0; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .menu-toggle, .darkmode-toggle { width: 38px; height: 38px; border-radius: 10px; background: #f5f5f5; border: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #333; font-size: 16px; transition: all .25s; flex-shrink: 0; }
        .menu-toggle { border: none; font-size: 18px; }
        .menu-toggle:hover, .darkmode-toggle:hover { background: #fee2e2; color: var(--red); border-color: #fecaca; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(220,38,38,.2); }
        .darkmode-toggle:active { transform: scale(.95); }
        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }
        .topbar-badge { display: flex; align-items: center; gap: 8px; background: #fff5f5; border: 1px solid #fecaca; border-radius: 20px; padding: 6px 14px; font-size: 12.5px; font-weight: 600; color: var(--red-dark); white-space: nowrap; }
        .page-content { padding: 28px; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .page-header h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.4px; }
        .page-header h1 span { color: var(--red); }
        .page-header p { color: #777; font-size: 13px; margin-top: 4px; }
        .matrix-pagination { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 16px 22px 20px; border-top: 1px solid #f0f0f0; background: #fafafa; }
        .pagination-meta { color: #777; font-size: 12px; font-weight: 600; }
        .pagination-nav { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .page-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 12px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; color: #444; text-decoration: none; font-size: 13px; font-weight: 700; transition: all .2s; }
        .page-btn:hover { background: #fee2e2; color: var(--red); border-color: #fecaca; }
        .page-btn.active { background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff; border-color: #991b1b; }
        .page-btn.disabled { opacity: .45; pointer-events: none; }
        .toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .entries-form {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
        }
        .entries-form label {
            font-size: 12px;
            font-weight: 700;
            color: #555;
            white-space: nowrap;
        }
        .entries-form select {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            color: #333;
            background: #fff;
            outline: none;
        }
        .entries-form select:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(220,38,38,.08); }
        .btn-tool { display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; border-radius: 8px; border: 1.5px solid #e5e7eb; background: #fff; color: #444; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .btn-tool.primary { border-color: #fecaca; background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff; }
        .view-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
        .view-tab { display: inline-flex; align-items: center; gap: 7px; padding: 9px 13px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #555; text-decoration: none; font-size: 12.5px; font-weight: 700; }
        .view-tab.active { background: #fee2e2; border-color: #fecaca; color: var(--red); }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 18px; }
        .summary-card { background: #fff; border: 1px solid rgba(0,0,0,.04); border-radius: 8px; padding: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
        .summary-num { font-size: 24px; font-weight: 800; line-height: 1; }
        .summary-label { color: #777; font-size: 12px; margin-top: 5px; }
        .report-card { background: #fff; border-radius: 8px; border: 1px solid rgba(0,0,0,.05); box-shadow: 0 2px 12px rgba(0,0,0,.05); overflow: hidden; }
        .report-head { padding: 18px 22px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: #fafafa; }
        .report-head h2 { font-size: 16px; font-weight: 700; }
        .report-head p { font-size: 12px; color: #777; margin-top: 2px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 920px; table-layout: fixed; }
        th { background: #fff; color: #555; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; padding: 12px 14px; border-bottom: 1px solid #eee; }
        td { padding: 14px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; font-size: 12.5px; color: #333; }
        .student-name { font-weight: 700; color: #1a1a1a; overflow-wrap: anywhere; }
        .student-meta { color: #888; font-size: 11.5px; margin-top: 3px; }
        .progress-wrap { display: grid; gap: 7px; min-width: 140px; }
        .progress-label { display: flex; justify-content: space-between; gap: 8px; font-size: 12px; color: #666; }
        .progress-track { flex: 1; height: 8px; border-radius: 999px; background: #fee2e2; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(135deg, #16a34a, #22c55e); }
        .metric-stack { display: flex; gap: 8px; flex-wrap: wrap; }
        .metric-pill { min-width: 68px; border-radius: 8px; padding: 8px 10px; background: #fafafa; border: 1px solid #eee; }
        .metric-pill strong { display: block; font-size: 18px; line-height: 1; }
        .metric-pill span { display: block; color: #777; font-size: 10.5px; margin-top: 3px; text-transform: uppercase; letter-spacing: .3px; }
        .metric-pill.good strong { color: #16a34a; }
        .metric-pill.warn strong { color: #dc2626; }
        .requirement-menu-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .requirement-menu-toggle {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }
        .requirement-menu-toggle.submitted { border-color: #bbf7d0; color: #15803d; background: #f0fdf4; }
        .requirement-menu-toggle.missing { border-color: #fecaca; color: #b91c1c; background: #fff5f5; }
        .requirement-menu-toggle.pending { border-color: #fde68a; color: #a16207; background: #fffbeb; }
        .requirement-menu-toggle.denied { border-color: #fecaca; color: #b91c1c; background: #fef2f2; }
        .requirement-menu-toggle:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(0,0,0,.08); }
        .requirement-menu-toggle:focus-visible { outline: 3px solid rgba(220,38,38,.2); outline-offset: 2px; }
        .requirement-menu-toggle .count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            background: rgba(255,255,255,.8);
            font-size: 11px;
            font-weight: 800;
        }
        .print-requirement-lists { display: none; }
        .requirement-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 7px; }
        .requirement-item {
            display: flex;
            align-items: center;
            gap: 7px;
            min-width: 0;
            padding: 8px 9px;
            border-radius: 8px;
            border: 1px solid #eee;
            background: #fff;
            color: #374151;
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.25;
        }
        .requirement-item i { flex-shrink: 0; font-size: 11px; }
        .requirement-item span { min-width: 0; overflow-wrap: anywhere; }
        .requirement-item.passed { border-color: #bbf7d0; background: #f0fdf4; color: #15803d; }
        .requirement-item.missing { border-color: #fecaca; background: #fff5f5; color: #b91c1c; }
        .requirement-item.pending { border-color: #fde68a; background: #fffbeb; color: #a16207; }
        .requirement-item.denied { border-color: #fecaca; background: #fef2f2; color: #b91c1c; }
        .requirement-item.extra { border-color: #bae6fd; background: #f0f9ff; color: #0369a1; }
        .status-counts { display: flex; gap: 6px; flex-wrap: wrap; color: #666; }
        .status-badge { border-radius: 999px; padding: 5px 8px; font-size: 11px; font-weight: 700; background: #f3f4f6; color: #4b5563; }
        .status-badge.approved { background: #dcfce7; color: #15803d; }
        .status-badge.pending { background: #fef9c3; color: #a16207; }
        .status-badge.denied { background: #fee2e2; color: #b91c1c; }
        .details-section h3 { font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: #777; margin-bottom: 7px; }
        .focus-note { color: #777; font-size: 12px; margin-top: 2px; }
        .empty-note { color: #999; font-style: italic; }
        .footer-note { padding: 14px 22px; color: #888; font-size: 12px; border-top: 1px solid #f0f0f0; background: #fafafa; }
        .requirement-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(17,24,39,.55);
        }
        .requirement-modal-overlay.open { display: flex; }
        .requirement-modal {
            width: min(720px, 100%);
            max-height: 80vh;
            overflow: hidden;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 24px 60px rgba(0,0,0,.24);
            display: flex;
            flex-direction: column;
        }
        .requirement-modal-header {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid #eee;
            background: #fafafa;
        }
        .requirement-modal-header h2 { font-size: 17px; font-weight: 800; color: #1a1a1a; }
        .requirement-modal-header p { font-size: 12px; color: #777; margin-top: 3px; }
        .requirement-modal-close {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            color: #555;
            cursor: pointer;
        }
        .requirement-modal-body { padding: 18px 20px; overflow: auto; }
        body.modal-open { overflow: hidden; }
        #print-area-wrapper { display: none; }
        body.dark-mode .topbar, body.dark-mode .summary-card, body.dark-mode .report-card, body.dark-mode .btn-tool { background: #1f1f1f; color: #e5e5e5; border-color: #333; }
        body.dark-mode .report-head, body.dark-mode .footer-note, body.dark-mode th, body.dark-mode .metric-pill, body.dark-mode .requirement-item, body.dark-mode .requirement-modal, body.dark-mode .requirement-modal-header { background: #2a2a2a; color: #ddd; border-color: #333; }
        body.dark-mode td { color: #ddd; border-color: #2c2c2c; }
        body.dark-mode .student-name { color: #fff; }
        body.dark-mode .requirement-modal-header h2 { color: #fff; }
        body.dark-mode .requirement-modal-close { background: #1f1f1f; color: #ddd; border-color: #333; }
        body.dark-mode { background: #1a1a1a; color: #e0e0e0; }
        body.dark-mode .topbar { background: #2a2a2a; border-bottom-color: #3a3a3a; }
        body.dark-mode .page-header h1, body.dark-mode .report-head h2, body.dark-mode .summary-num { color: #fff; }
        body.dark-mode .matrix-pagination { background: #1f1f1f; border-top-color: #333; }
        body.dark-mode .pagination-meta { color: #aaa; }
        body.dark-mode .page-btn { background: #2a2a2a; border-color: #3a3a3a; color: #e5e5e5; }
        body.dark-mode .page-btn:hover { background: rgba(220,38,38,.2); color: #ff6b6b; border-color: rgba(220,38,38,.3); }
        body.dark-mode .page-btn.active { background: linear-gradient(135deg, #dc2626, #991b1b); color: #fff; border-color: #991b1b; }
        body.dark-mode .entries-form { background: #1f1f1f; border-color: #333; }
        body.dark-mode .entries-form label { color: #ddd; }
        body.dark-mode .entries-form select { background: #2a2a2a; color: #e5e5e5; border-color: #3a3a3a; }
        body.dark-mode .darkmode-toggle { background: #2a2a2a; border-color: #3a3a3a; color: #e8e8e8; }
        body.dark-mode .darkmode-toggle:hover { background: rgba(220,38,38,.2); color: #ff6b6b; border-color: rgba(220,38,38,.3); }
        body.dark-mode .topbar-badge { background: rgba(220,38,38,.15); border-color: rgba(220,38,38,.3); color: #ff6b6b; }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content, .main-content.expanded { margin-left: 0; }
            .page-content { padding: 20px 14px; }
        }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body > :not(#print-area-wrapper) { display: none !important; }
            #print-area-wrapper { display: block !important; }
            #print-area-wrapper, #print-area-wrapper * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .sidebar, .topbar, .toolbar, .btn-tool, .view-tabs { display: none !important; }
            body { background: #fff; color: #111; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 0; }
            .report-card, .summary-card { box-shadow: none; border-color: #ddd; }
            table { min-width: 0; font-size: 10px; }
            th, td { padding: 8px; }
            .page-header { margin-bottom: 12px; }
            .requirement-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 5px; }
            .requirement-item { border-color: #ddd !important; background: #fff !important; color: #111 !important; padding: 5px 6px; }
            .requirement-menu-row, .requirement-modal-overlay { display: none !important; }
            .print-requirement-lists { display: grid; gap: 8px; }
        }
    </style>
</head>
<body>
@php
    $totalStudents = $allStudentStatuses->count();
    $completeStudents = $allStudentStatuses->where('missingCount', 0)->count();
    $categoryCount = $categories->count();
    $averageCompletion = $totalStudents > 0 ? round($allStudentStatuses->avg('completion')) : 0;
    $printStatuses = ($activeView === 'overview'
        ? $allStudentStatuses
        : $allStudentStatuses->filter(fn ($status) => $status[$activeView]->count() > 0)
    )->values()->map(function ($status) {
        return [
            'studentName' => $status['student']->full_name,
            'studentNumber' => $status['student']->studentNum ?? 'No student no.',
            'completion' => $status['completion'],
            'submittedCount' => $status['submittedCount'],
            'missingCount' => $status['missingCount'],
            'approvedCount' => $status['approvedCount'],
            'pendingCount' => $status['pendingCount'],
            'deniedCount' => $status['deniedCount'],
            'passed' => $status['passed']->values()->all(),
            'approved' => $status['approved']->values()->all(),
            'pending' => $status['pending']->values()->all(),
            'denied' => $status['denied']->values()->all(),
            'missing' => $status['missing']->values()->all(),
        ];
    });
@endphp
<div class="sidebar" id="sidebar">
    <a href="{{ url('/professor/home') }}" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP">
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">Intern<span>Connect</span></div>
            <div class="sidebar-brand-sub">OJTIMS</div>
        </div>
    </a>
    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(!empty($data->profile_photo))
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name ?? 'Professor' }}</span>
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
        <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item active">
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
        <a href="{{ url('/professor/evaluation') }}" class="nav-item">
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

<div class="main-content" id="mainContent">
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

    <main class="page-content">
        <div class="page-header">
            <div>
                <h1>Requirement <span>Status</span></h1>
                <p>{{ $course->course }} | {{ $course->room }} | {{ $course->school_year_start && $course->school_year_end ? $course->school_year_start . ' - ' . $course->school_year_end : 'School year not set' }}</p>
            </div>
            <div class="toolbar">
                <form method="get" action="{{ route('professor.requirementStatus', $course->id) }}" class="entries-form">
                    @if($activeView !== 'overview')
                        <input type="hidden" name="view" value="{{ $activeView }}">
                    @endif
                    <label for="perPageSelect">Show</label>
                    <select id="perPageSelect" name="per_page" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                    <label for="perPageSelect">entries</label>
                </form>
                <a href="{{ route('professor.requirementStatus.classes') }}" class="btn-tool"><i class="fa fa-arrow-left"></i> Classes</a>
                <button type="button" class="btn-tool primary" id="printReportBtn"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>

        <div class="view-tabs">
            <a href="{{ route('professor.requirementStatus', $course->id) }}" class="view-tab {{ $activeView === 'overview' ? 'active' : '' }}"><i class="fa fa-table"></i> Overview</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'approved']) }}" class="view-tab {{ $activeView === 'approved' ? 'active' : '' }}"><i class="fa fa-check-circle"></i> Approved</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'pending']) }}" class="view-tab {{ $activeView === 'pending' ? 'active' : '' }}"><i class="fa fa-clock"></i> Pending</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'denied']) }}" class="view-tab {{ $activeView === 'denied' ? 'active' : '' }}"><i class="fa fa-times-circle"></i> Denied</a>
            <a href="{{ route('professor.requirementStatus', ['roomId' => $course->id, 'view' => 'missing']) }}" class="view-tab {{ $activeView === 'missing' ? 'active' : '' }}"><i class="fa fa-exclamation-circle"></i> Missing</a>
        </div>

        <div class="summary-grid">
            <div class="summary-card"><div class="summary-num">{{ $totalStudents }}</div><div class="summary-label">Students</div></div>
            <div class="summary-card"><div class="summary-num">{{ $categoryCount }}</div><div class="summary-label">Required Categories</div></div>
            <div class="summary-card"><div class="summary-num">{{ $completeStudents }}</div><div class="summary-label">Complete Students</div></div>
            <div class="summary-card"><div class="summary-num">{{ $averageCompletion }}%</div><div class="summary-label">Average Completion</div></div>
        </div>

        <section class="report-card">
            <div class="report-head">
                <div>
                    <h2>Student Requirement Matrix</h2>
                    <p>Submitted and missing requirements are based on the current professor file categories.</p>
                </div>
                <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
            </div>
            <div class="table-wrap">
                <table>
                    <colgroup>
                        <col style="width:24%;">
                        <col style="width:18%;">
                        <col style="width:30%;">
                        <col style="width:28%;">
                    </colgroup>
                    @if($activeView === 'overview')
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Progress</th>
                                <th>Requirements</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentStatuses as $status)
                            <tr>
                                <td>
                                    <div class="student-name">{{ $status['student']->full_name }}</div>
                                    <div class="student-meta">{{ $status['student']->studentNum ?? 'No student no.' }}</div>
                                </td>
                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress-label">
                                            <span>Completion</span>
                                            <strong>{{ $status['completion'] }}%</strong>
                                        </div>
                                        <div class="progress-track">
                                            <div class="progress-fill" style="width: {{ $status['completion'] }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="requirement-menu-row">
                                        <button type="button"
                                            class="requirement-menu-toggle submitted requirement-modal-trigger"
                                            data-modal-title="Submitted Requirements"
                                            data-modal-type="passed"
                                            data-empty-text="No submitted requirements yet."
                                            data-student-name="{{ e($status['student']->full_name) }}"
                                            data-requirements='@json($status["passed"]->values()->all())'>
                                            <i class="fa fa-eye"></i> View Submitted <span class="count">{{ $status['submittedCount'] }}</span>
                                        </button>
                                        <button type="button"
                                            class="requirement-menu-toggle missing requirement-modal-trigger"
                                            data-modal-title="Missing Requirements"
                                            data-modal-type="missing"
                                            data-empty-text="Complete"
                                            data-student-name="{{ e($status['student']->full_name) }}"
                                            data-requirements='@json($status["missing"]->values()->all())'>
                                            <i class="fa fa-eye"></i> View Missing <span class="count">{{ $status['missingCount'] }}</span>
                                        </button>
                                    </div>
                                    <div class="print-requirement-lists">
                                        <div class="details-section">
                                            <h3>Submitted</h3>
                                            <div class="requirement-grid">
                                                @forelse($status['passed'] as $item)
                                                    <div class="requirement-item passed">
                                                        <i class="fa fa-check"></i>
                                                        <span>{{ $item }}</span>
                                                    </div>
                                                @empty
                                                    <span class="empty-note">No submitted requirements yet.</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="details-section">
                                            <h3>Missing</h3>
                                            <div class="requirement-grid">
                                                @forelse($status['missing'] as $item)
                                                    <div class="requirement-item missing">
                                                        <i class="fa fa-times"></i>
                                                        <span>{{ $item }}</span>
                                                    </div>
                                                @empty
                                                    <div class="requirement-item passed">
                                                        <i class="fa fa-check-circle"></i>
                                                        <span>Complete</span>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-counts">
                                        <span class="status-badge approved">Approved {{ $status['approvedCount'] }}</span>
                                        <span class="status-badge pending">Pending {{ $status['pendingCount'] }}</span>
                                        <span class="status-badge denied">Denied {{ $status['deniedCount'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#999;">No students found for this class.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    @else
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>{{ ucfirst($activeView) }} Requirements</th>
                                <th>Count</th>
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentStatuses as $status)
                                <tr>
                                    <td>
                                        <div class="student-name">{{ $status['student']->full_name }}</div>
                                        <div class="student-meta">{{ $status['student']->studentNum ?? 'No student no.' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $focusedType = $activeView === 'approved' ? 'passed' : $activeView;
                                            $focusedIcon = $activeView === 'approved' ? 'check' : ($activeView === 'pending' ? 'clock' : 'times');
                                            $focusedButtonClass = $activeView === 'approved' ? 'submitted' : $activeView;
                                        @endphp
                                        <button type="button"
                                            class="requirement-menu-toggle {{ $focusedButtonClass }} requirement-modal-trigger"
                                            data-modal-title="{{ ucfirst($activeView) }} Requirements"
                                            data-modal-type="{{ $focusedType }}"
                                            data-empty-text="No {{ $activeView }} requirements found."
                                            data-student-name="{{ e($status['student']->full_name) }}"
                                            data-requirements='@json($status[$activeView]->values()->all())'>
                                            <i class="fa fa-{{ $focusedIcon }}"></i> View {{ ucfirst($activeView) }} <span class="count">{{ $status[$activeView]->count() }}</span>
                                        </button>
                                        <div class="print-requirement-lists">
                                            <div class="requirement-grid">
                                                @foreach($status[$activeView] as $item)
                                                    <div class="requirement-item {{ $activeView === 'missing' ? 'missing' : ($activeView === 'approved' ? 'passed' : ($activeView === 'denied' ? 'denied' : 'pending')) }}">
                                                        @if($activeView === 'approved')
                                                            <i class="fa fa-check"></i>
                                                        @elseif($activeView === 'missing' || $activeView === 'denied')
                                                            <i class="fa fa-times"></i>
                                                        @else
                                                            <i class="fa fa-clock"></i>
                                                        @endif
                                                        <span>{{ $item }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="metric-pill {{ $activeView === 'missing' || $activeView === 'denied' ? 'warn' : 'good' }}">
                                            <strong>{{ $status[$activeView]->count() }}</strong>
                                            <span>{{ $activeView }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress-wrap">
                                            <div class="progress-label">
                                                <span>Completion</span>
                                                <strong>{{ $status['completion'] }}%</strong>
                                            </div>
                                            <div class="progress-track">
                                                <div class="progress-fill" style="width: {{ $status['completion'] }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:30px; color:#999;">
                                        No {{ $activeView }} requirements found for this class.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
            </div>
            <div class="footer-note">
                Missing requirements are computed from the professor's current file categories. Submitted requirements with old or renamed categories appear under Extra Submitted.
            </div>
            @if($studentStatuses->hasPages())
                <div class="matrix-pagination">
                    <div class="pagination-meta">
                        Showing {{ $studentStatuses->firstItem() }} to {{ $studentStatuses->lastItem() }} of {{ $studentStatuses->total() }} students
                    </div>
                    <div class="pagination-nav">
                        <a href="{{ $studentStatuses->previousPageUrl() ?: '#' }}" class="page-btn {{ $studentStatuses->onFirstPage() ? 'disabled' : '' }}">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        @for($page = 1; $page <= $studentStatuses->lastPage(); $page++)
                            @if($page === $studentStatuses->currentPage())
                                <span class="page-btn active">{{ $page }}</span>
                            @else
                                <a href="{{ $studentStatuses->url($page) }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endfor
                        <a href="{{ $studentStatuses->nextPageUrl() ?: '#' }}" class="page-btn {{ $studentStatuses->hasMorePages() ? '' : 'disabled' }}">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </main>
</div>

<div class="requirement-modal-overlay" id="requirementListModal" aria-hidden="true">
    <div class="requirement-modal" role="dialog" aria-modal="true" aria-labelledby="requirementListTitle">
        <div class="requirement-modal-header">
            <div>
                <h2 id="requirementListTitle">Requirements</h2>
                <p id="requirementListStudent"></p>
            </div>
            <button type="button" class="requirement-modal-close" id="requirementModalClose" aria-label="Close requirements list">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="requirement-modal-body">
            <div class="requirement-grid" id="requirementModalList"></div>
        </div>
    </div>
</div>

<div id="print-area-wrapper"></div>

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    document.getElementById('menuToggle').addEventListener('click', function () {
        if (window.innerWidth <= 900) {
            sidebar.classList.toggle('mobile-open');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    const requirementReportData = {
        activeView: @json($activeView),
        course: @json($course->course),
        room: @json($course->room),
        schoolYear: @json($course->school_year_start && $course->school_year_end ? $course->school_year_start . ' - ' . $course->school_year_end : 'School year not set'),
        totalStudents: @json($totalStudents),
        categoryCount: @json($categoryCount),
        completeStudents: @json($completeStudents),
        averageCompletion: @json($averageCompletion),
        generatedAt: @json(now()->format('F d, Y h:i A')),
        professor: @json($data->full_name ?? 'Professor'),
        rows: @json($printStatuses),
    };

    function escapeReportHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderRequirementList(items, emptyText) {
        if (!items || items.length === 0) {
            return `<span style="color:#9ca3af; font-style:italic;">${escapeReportHtml(emptyText)}</span>`;
        }

        return items.map(function (item) {
            return `<span style="display:inline-block; margin:1px 3px 3px 0; padding:2px 5px; border-radius:4px; background:#f3f4f6; color:#374151; font-size:7.5px; line-height:1.25;">${escapeReportHtml(item)}</span>`;
        }).join('');
    }

    function statusBadge(label, count, bg, color) {
        return `<span style="display:inline-block; margin:1px 2px 3px 0; padding:2px 6px; border-radius:999px; background:${bg}; color:${color}; font-size:7.5px; font-weight:700;">${label} ${count}</span>`;
    }

    function buildRequirementPrintHTML() {
        const report = requirementReportData;
        const title = report.activeView === 'overview'
            ? 'Student Requirement Status Report'
            : `${report.activeView.charAt(0).toUpperCase() + report.activeView.slice(1)} Requirement Report`;
        const sectionLabel = report.activeView === 'overview'
            ? 'Student Requirement Matrix'
            : `${report.activeView.charAt(0).toUpperCase() + report.activeView.slice(1)} Requirements`;

        let rowsHTML = '';

        report.rows.forEach(function (row, index) {
            const rowBg = index % 2 === 0 ? '#ffffff' : '#f9fafb';

            if (report.activeView === 'overview') {
                rowsHTML += `
                    <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                        <td style="padding:7px 6px; font-size:8px; font-weight:700; color:#6b7280; text-align:center; border-right:1px solid #e5e7eb;">${index + 1}</td>
                        <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">
                            <div style="font-size:9px; font-weight:800; color:#111827;">${escapeReportHtml(row.studentName)}</div>
                            <div style="font-size:7.5px; color:#6b7280; margin-top:1px;">${escapeReportHtml(row.studentNumber)}</div>
                        </td>
                        <td style="padding:7px 6px; font-size:9px; font-weight:800; color:#15803d; text-align:center; border-right:1px solid #e5e7eb;">${row.completion}%</td>
                        <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">${renderRequirementList(row.passed, 'No submitted requirements yet.')}</td>
                        <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">${renderRequirementList(row.missing, 'Complete')}</td>
                        <td style="padding:7px 6px;">
                            ${statusBadge('Approved', row.approvedCount, '#dcfce7', '#15803d')}
                            ${statusBadge('Pending', row.pendingCount, '#fef9c3', '#a16207')}
                            ${statusBadge('Denied', row.deniedCount, '#fee2e2', '#b91c1c')}
                        </td>
                    </tr>`;
                return;
            }

            const focusedItems = row[report.activeView] || [];
            rowsHTML += `
                <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                    <td style="padding:7px 6px; font-size:8px; font-weight:700; color:#6b7280; text-align:center; border-right:1px solid #e5e7eb;">${index + 1}</td>
                    <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">
                        <div style="font-size:9px; font-weight:800; color:#111827;">${escapeReportHtml(row.studentName)}</div>
                        <div style="font-size:7.5px; color:#6b7280; margin-top:1px;">${escapeReportHtml(row.studentNumber)}</div>
                    </td>
                    <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">${renderRequirementList(focusedItems, `No ${report.activeView} requirements found.`)}</td>
                    <td style="padding:7px 6px; font-size:9px; font-weight:800; color:#111827; text-align:center; border-right:1px solid #e5e7eb;">${focusedItems.length}</td>
                    <td style="padding:7px 6px; font-size:9px; font-weight:800; color:#15803d; text-align:center;">${row.completion}%</td>
                </tr>`;
        });

        const tableHead = report.activeView === 'overview'
            ? `
                <tr style="background:#7f0000;">
                    <th style="width:4%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">#</th>
                    <th style="width:17%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Student</th>
                    <th style="width:8%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">Completion</th>
                    <th style="width:29%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Submitted Requirements</th>
                    <th style="width:27%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Missing Requirements</th>
                    <th style="width:15%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left;">Approval Status</th>
                </tr>`
            : `
                <tr style="background:#7f0000;">
                    <th style="width:4%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">#</th>
                    <th style="width:24%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Student</th>
                    <th style="width:52%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">${escapeReportHtml(sectionLabel)}</th>
                    <th style="width:8%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">Count</th>
                    <th style="width:12%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center;">Completion</th>
                </tr>`;

        return `
            <div style="font-family:'Poppins',Arial,sans-serif; background:#fff;">
                <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%);">
                    <div style="background:rgba(255,255,255,.12); height:4px;"></div>
                    <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                        <div style="width:50px; height:50px; background:rgba(255,255,255,.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,.25);">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);">
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines - OJT Information Management System</div>
                            <div style="font-size:15px; font-weight:800; color:#fff; line-height:1.15;">${escapeReportHtml(title)}</div>
                            <div style="font-size:8.5px; color:rgba(255,255,255,.6); margin-top:3px;">Taguig Branch Campus | College of Engineering and Technology</div>
                        </div>
                        <div style="text-align:right; flex-shrink:0;">
                            <div style="display:inline-block; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:6px; padding:5px 12px; text-align:center;">
                                <div style="font-size:18px; font-weight:800; color:#fff; line-height:1;">${report.rows.length}</div>
                                <div style="font-size:7.5px; color:rgba(255,255,255,.7); text-transform:uppercase; letter-spacing:1px; margin-top:1px;">Report Rows</div>
                            </div>
                        </div>
                    </div>
                    <div style="background:rgba(0,0,0,.15); height:3px;"></div>
                </div>

                <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                        <div style="font-size:9.5px; color:#374151;"><span style="color:#6b7280;">Class:</span> <strong>${escapeReportHtml(report.course)} ${escapeReportHtml(report.room)}</strong></div>
                        <div style="font-size:9.5px; color:#374151;"><span style="color:#6b7280;">School Year:</span> <strong>${escapeReportHtml(report.schoolYear)}</strong></div>
                        <div style="font-size:9.5px; color:#374151;"><span style="color:#6b7280;">View:</span> <strong>${escapeReportHtml(sectionLabel)}</strong></div>
                    </div>
                    <div style="font-size:8.5px; color:#9ca3af;">Generated: ${escapeReportHtml(report.generatedAt)}</div>
                </div>

                <div style="padding:10px 22px 4px 22px; display:grid; grid-template-columns:repeat(4, 1fr); gap:8px;">
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.totalStudents}</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Students</div></div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.categoryCount}</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Categories</div></div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #16a34a; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.completeStudents}</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Complete Students</div></div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #16a34a; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.averageCompletion}%</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Average Completion</div></div>
                </div>

                <div style="padding:8px 22px 3px 22px;">
                    <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">${escapeReportHtml(sectionLabel)}</div>
                </div>

                <div style="padding:4px 22px 0 22px;">
                    <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-family:'Poppins',Arial,sans-serif; border:1px solid #d1d5db;">
                        <thead>${tableHead}</thead>
                        <tbody>
                            ${rowsHTML || `<tr><td colspan="${report.activeView === 'overview' ? 6 : 5}" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No records found for this report view.</td></tr>`}
                        </tbody>
                    </table>
                </div>

                <div style="page-break-inside:avoid; break-inside:avoid; display:table; width:100%;">
                    <div style="padding:18px 22px 12px 22px;">
                        <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                                <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Disclaimer</div>
                                <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">
                                    This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:.7; filter:brightness(2);">
                            <span style="font-size:8px; color:rgba(255,255,255,.75); font-weight:500;">Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                        </div>
                        <span style="font-size:8px; color:rgba(255,255,255,.5);">Ref: REQ-STATUS-${new Date().getFullYear()}</span>
                    </div>
                </div>
            </div>`;
    }

    document.getElementById('printReportBtn').addEventListener('click', function () {
        document.getElementById('print-area-wrapper').innerHTML = buildRequirementPrintHTML();
        window.print();
        setTimeout(function () {
            document.getElementById('print-area-wrapper').innerHTML = '';
        }, 1000);
    });

    const requirementModal = document.getElementById('requirementListModal');
    const requirementModalTitle = document.getElementById('requirementListTitle');
    const requirementModalStudent = document.getElementById('requirementListStudent');
    const requirementModalList = document.getElementById('requirementModalList');
    const requirementModalClose = document.getElementById('requirementModalClose');

    function requirementIcon(type, empty) {
        if (empty && type === 'missing') {
            return 'fa-check-circle';
        }
        if (type === 'pending') {
            return 'fa-clock';
        }
        if (type === 'missing') {
            return 'fa-times';
        }
        if (type === 'denied') {
            return 'fa-times';
        }
        return 'fa-check';
    }

    function closeRequirementModal() {
        requirementModal.classList.remove('open');
        requirementModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    function openRequirementModal(button) {
        const type = button.dataset.modalType || 'passed';
        const emptyText = button.dataset.emptyText || 'No requirements found.';
        let requirements = [];

        try {
            requirements = JSON.parse(button.dataset.requirements || '[]');
        } catch (error) {
            requirements = [];
        }

        requirementModalTitle.textContent = button.dataset.modalTitle || 'Requirements';
        requirementModalStudent.textContent = button.dataset.studentName || '';
        requirementModalList.innerHTML = '';

        if (requirements.length === 0) {
            requirements = [emptyText];
        }

        requirements.forEach(function (item) {
            const isEmptyComplete = item === 'Complete';
            const row = document.createElement('div');
            row.className = 'requirement-item ' + (isEmptyComplete ? 'passed' : type);

            const icon = document.createElement('i');
            icon.className = 'fa ' + requirementIcon(type, isEmptyComplete);

            const label = document.createElement('span');
            label.textContent = item;

            row.appendChild(icon);
            row.appendChild(label);
            requirementModalList.appendChild(row);
        });

        requirementModal.classList.add('open');
        requirementModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        requirementModalClose.focus();
    }

    document.querySelectorAll('.requirement-modal-trigger').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            openRequirementModal(button);
        });
    });

    requirementModalClose.addEventListener('click', closeRequirementModal);

    requirementModal.addEventListener('click', function (event) {
        if (event.target === requirementModal) {
            closeRequirementModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && requirementModal.classList.contains('open')) {
            closeRequirementModal();
        }
    });
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
