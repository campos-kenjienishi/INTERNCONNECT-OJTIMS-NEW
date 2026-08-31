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
    if (!$user && session()->has('loginId')) {
        $user = \App\Models\User::find(session('loginId'));
    }

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
            ['url' => url('/professor/home'), 'icon' => 'fa-home', 'label' => 'Dashboard', 'pattern' => 'professor/home*'],
            ['url' => url('/professor/class'), 'icon' => 'fa-clipboard', 'label' => 'Class', 'pattern' => 'professor/class*'],
            ['url' => route('professor.requirementStatus.classes'), 'icon' => 'fa-clipboard-check', 'label' => 'Requirement Status', 'pattern' => 'professor/requirement-status*'],
            ['url' => url('/professor/analytics'), 'icon' => 'fa-chart-line', 'label' => 'Analytics', 'pattern' => 'professor/analytics*'],
            ['url' => url('/reportsExpiredProf'), 'icon' => 'fa-file-contract', 'label' => 'MOA', 'pattern' => 'reportsExpiredProf*'],
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
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if($isAuthenticatedShell)
        <script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
        <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    @endif
    <link rel="stylesheet" href="{{ vasset('css/student_evaluation-responsive.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <script>
        (function(){
            try {
                if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e){}
        })();
    </script>
    <link rel="stylesheet" href="{{ vasset('css/components/evaluation-shell.css') }}">
</head>
<body>
@if($isAuthenticatedShell)
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar{{ $role === 'student' ? ' sidebar-student' : '' }}" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
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
            <span class="user-name">{{ $user ? ($user->full_name ?: trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))) : 'Account' }}</span>
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
                <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
            @else
                <div class="topbar-public-brand">
                    <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
                    <div class="topbar-public-brand-text">
                        <span class="topbar-brand-name">Intern<span class="brand-accent">Connect</span></span>
                        <span class="topbar-brand-sub">OJTIMS</span>
                    </div>
                </div>
                <div class="topbar-divider"></div>
                <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
            @endif
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa {{ $role === 'student' ? 'fa-graduation-cap' : ($role === 'professor' ? 'fa-chalkboard-teacher' : ($role === 'coordinator' ? 'fa-user-tie' : 'fa-clipboard-check')) }}"></i>
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
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
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
        <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
        <span class="divider">|</span>
    </div>
</footer>
</div>

    <script src="{{ vasset('js/components/evaluation-shell.js') }}"></script>
    <script src="{{ vasset('js/sidebar-persist.js') }}"></script>
</body>
</html>