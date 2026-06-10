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
        :root { --red:#dc2626; --sidebar-w:260px; --sidebar-w-collapsed:70px; --topbar-h:64px; }
        body { font-family:'Poppins',sans-serif; background:#f5f5f5; color:#1a1a1a; min-height:100vh; }
        .sidebar { position:fixed; inset:0 auto 0 0; width:var(--sidebar-w); height:100vh; background:linear-gradient(160deg,#1a0000 0%,#4a0000 50%,#7f0000 100%); z-index:1000; transition:width .3s; overflow:hidden; box-shadow:4px 0 24px rgba(0,0,0,.18); display:flex; flex-direction:column; }
        .sidebar.collapsed { width:var(--sidebar-w-collapsed); }
        .sidebar-brand,.sidebar-user,.nav-item { display:flex; align-items:center; gap:12px; text-decoration:none; }
        .sidebar-brand { padding:22px 18px; border-bottom:1px solid rgba(255,255,255,.08); }
        .sidebar-brand img { width:36px; height:36px; object-fit:contain; }
        .sidebar-brand-name { color:#fff; font-size:16px; font-weight:800; line-height:1; white-space:nowrap; }
        .sidebar-brand-name span { color:#fca5a5; }
        .sidebar-brand-sub { color:rgba(255,255,255,.45); font-size:9px; text-transform:uppercase; letter-spacing:1.4px; margin-top:3px; white-space:nowrap; }
        .sidebar-user { padding:16px 18px; border-bottom:1px solid rgba(255,255,255,.08); }
        .sidebar.collapsed .sidebar-brand-text,.sidebar.collapsed .user-info,.sidebar.collapsed .nav-label { opacity:0; width:0; overflow:hidden; }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:rgba(239,68,68,.25); border:1.5px solid rgba(239,68,68,.4); color:#fca5a5; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
        .user-avatar img { width:100%; height:100%; object-fit:cover; }
        .user-name { color:#fff; font-size:13px; font-weight:600; display:block; white-space:nowrap; }
        .user-role { color:rgba(255,255,255,.4); font-size:10px; text-transform:uppercase; letter-spacing:1px; white-space:nowrap; }
        .sidebar-nav { flex:1; overflow-y:auto; padding:12px 0; }
        .sidebar-nav::-webkit-scrollbar { width:3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background:rgba(239,68,68,.3); border-radius:10px; }
        .nav-item { color:rgba(255,255,255,.55); padding:12px 20px; border-left:3px solid transparent; font-size:14px; font-weight:500; transition:all .25s; position:relative; white-space:nowrap; }
        .nav-item:hover { color:#fff; background:rgba(255,255,255,.06); }
        .nav-item.active { color:#fff; background:rgba(239,68,68,.15); border-left-color:#ef4444; }
        .nav-icon { width:22px; text-align:center; font-size:18px; flex-shrink:0; }
        .nav-label { transition:opacity .25s; overflow:hidden; }
        .tooltip-label { position:absolute; left:calc(var(--sidebar-w-collapsed) + 8px); background:#1a0000; color:#fff; font-size:12px; padding:5px 10px; border-radius:6px; white-space:nowrap; pointer-events:none; opacity:0; transition:opacity .2s; box-shadow:0 4px 12px rgba(0,0,0,.3); z-index:9999; }
        .sidebar.collapsed .nav-item:hover .tooltip-label { opacity:1; }
        .sidebar-footer { padding:12px 0; border-top:1px solid rgba(255,255,255,.07); flex-shrink:0; }
        .main-content { margin-left:var(--sidebar-w); min-height:100vh; transition:margin-left .3s; display:flex; flex-direction:column; }
        .main-content.expanded { margin-left:var(--sidebar-w-collapsed); }
        .topbar { height:var(--topbar-h); background:#fff; border-bottom:1px solid rgba(0,0,0,.05); box-shadow:0 2px 12px rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:100; }
        .topbar-left { display:flex; align-items:center; gap:16px; min-width:0; }
        .topbar-right { display:flex; align-items:center; gap:12px; }
        .menu-toggle,.darkmode-toggle { width:38px; height:38px; border-radius:10px; background:#f5f5f5; border:1px solid #ddd; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#333; font-size:16px; transition:all .25s; flex-shrink:0; }
        .menu-toggle { border:none; font-size:18px; }
        .menu-toggle:hover,.darkmode-toggle:hover { background:#fee2e2; color:var(--red); border-color:#fecaca; transform:translateY(-2px); box-shadow:0 6px 16px rgba(220,38,38,.2); }
        .darkmode-toggle:active { transform:scale(.95); }
        .topbar-title { font-size:13.5px; font-weight:500; color:#888; }
        .topbar-title span { color:var(--red); font-weight:600; }
        .topbar-badge { display:flex; align-items:center; gap:8px; background:#fff5f5; border:1px solid #fecaca; border-radius:20px; padding:6px 14px; font-size:12.5px; font-weight:600; color:#991b1b; white-space:nowrap; }
        .page-content { padding:28px; }
        .page-header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
        .page-header h1 { font-size:26px; font-weight:800; letter-spacing:-.4px; }
        .page-header h1 span { color:var(--red); }
        .page-header p { color:#777; font-size:13px; margin-top:4px; }
        .summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:18px; }
        .summary-card,.class-card { background:#fff; border:1px solid rgba(0,0,0,.04); border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,.05); }
        .summary-card { padding:16px; }
        .summary-num { font-size:24px; font-weight:800; line-height:1; }
        .summary-label { color:#777; font-size:12px; margin-top:5px; }
        .class-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:16px; }
        .class-card { padding:18px; display:grid; gap:14px; }
        .class-title { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; }
        .class-title h2 { font-size:16px; font-weight:800; }
        .class-meta { color:#777; font-size:12px; margin-top:3px; }
        .class-icon { width:42px; height:42px; border-radius:8px; background:#fee2e2; color:var(--red); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .progress-wrap { display:grid; gap:7px; }
        .progress-label { display:flex; justify-content:space-between; font-size:12px; color:#666; }
        .progress-track { height:9px; border-radius:999px; background:#fee2e2; overflow:hidden; }
        .progress-fill { height:100%; border-radius:inherit; background:linear-gradient(135deg,#16a34a,#22c55e); }
        .class-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
        .mini-stat { border:1px solid #eee; border-radius:8px; padding:10px; background:#fafafa; }
        .mini-stat strong { display:block; font-size:18px; line-height:1; }
        .mini-stat span { display:block; color:#777; font-size:10.5px; margin-top:3px; text-transform:uppercase; }
        .btn-report { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 14px; border-radius:8px; background:linear-gradient(135deg,#dc2626,#991b1b); color:#fff; text-decoration:none; font-size:13px; font-weight:700; }
        .empty-state { text-align:center; padding:36px; color:#888; background:#fff; border-radius:8px; border:1px dashed #ddd; }
        body.dark-mode { background:#1a1a1a; color:#e0e0e0; }
        body.dark-mode .topbar,body.dark-mode .summary-card,body.dark-mode .class-card,body.dark-mode .mini-stat { background:#1f1f1f; color:#e5e5e5; border-color:#333; }
        body.dark-mode .topbar { background:#2a2a2a; border-bottom-color:#3a3a3a; }
        body.dark-mode .page-header h1,body.dark-mode .class-title h2,body.dark-mode .summary-num { color:#fff; }
        body.dark-mode .darkmode-toggle { background:#2a2a2a; border-color:#3a3a3a; color:#e8e8e8; }
        body.dark-mode .darkmode-toggle:hover { background:rgba(220,38,38,.2); color:#ff6b6b; border-color:rgba(220,38,38,.3); }
        body.dark-mode .topbar-badge { background:rgba(220,38,38,.15); border-color:rgba(220,38,38,.3); color:#ff6b6b; }
        @media (max-width:900px) { .sidebar { transform:translateX(-100%); } .sidebar.mobile-open { transform:translateX(0); } .main-content,.main-content.expanded { margin-left:0; } .page-content { padding:20px 14px; } }
    </style>
</head>
<body>
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
                <p>Select a class to review submitted, approved, pending, denied, and missing requirements.</p>
            </div>
        </div>
        <div class="summary-grid">
            <div class="summary-card"><div class="summary-num">{{ $classes->count() }}</div><div class="summary-label">Classes</div></div>
            <div class="summary-card"><div class="summary-num">{{ $classes->sum('student_count') }}</div><div class="summary-label">Students</div></div>
            <div class="summary-card"><div class="summary-num">{{ $categoryCount }}</div><div class="summary-label">Required Categories</div></div>
            <div class="summary-card"><div class="summary-num">{{ $classes->sum('complete_count') }}</div><div class="summary-label">Complete Students</div></div>
        </div>

        @if($classes->isEmpty())
            <div class="empty-state">No classes found yet.</div>
        @else
            <div class="class-grid">
                @foreach($classes as $classroom)
                    <article class="class-card">
                        <div class="class-title">
                            <div>
                                <h2>{{ $classroom->room }}</h2>
                                <div class="class-meta">{{ $classroom->course }} | {{ $classroom->school_year_start && $classroom->school_year_end ? $classroom->school_year_start . ' - ' . $classroom->school_year_end : 'No school year' }}</div>
                            </div>
                            <div class="class-icon"><i class="fa fa-chalkboard"></i></div>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-label">
                                <span>Average completion</span>
                                <strong>{{ min($classroom->average_completion, 100) }}%</strong>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ min($classroom->average_completion, 100) }}%;"></div>
                            </div>
                        </div>
                        <div class="class-stats">
                            <div class="mini-stat"><strong>{{ $classroom->student_count }}</strong><span>Students</span></div>
                            <div class="mini-stat"><strong>{{ $classroom->complete_count }}</strong><span>Complete</span></div>
                            <div class="mini-stat"><strong>{{ max($classroom->student_count - $classroom->complete_count, 0) }}</strong><span>Incomplete</span></div>
                        </div>
                        <a href="{{ route('professor.requirementStatus', $classroom->id) }}" class="btn-report">
                            <i class="fa fa-clipboard-list"></i> View Report
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </main>
</div>

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
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
