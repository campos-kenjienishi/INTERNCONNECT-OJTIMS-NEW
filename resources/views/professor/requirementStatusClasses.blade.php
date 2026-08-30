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
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
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
    <link rel="stylesheet" href="{{ vasset('css/professor/requirement-status-classes.css') }}">
</head>

<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
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
            <span class="nav-label">Requirement Status</span>
            <span class="tooltip-label">Requirement Status</span>
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
            <div class="summary-card"><div class="summary-num">{{ $classes->total() }}</div><div class="summary-label">Classes</div></div>
            <div class="summary-card"><div class="summary-num">{{ $totalStudents }}</div><div class="summary-label">Students</div></div>
            <div class="summary-card"><div class="summary-num">{{ $categoryCount }}</div><div class="summary-label">Required Categories</div></div>
            <div class="summary-card"><div class="summary-num">{{ $totalCompleteStudents }}</div><div class="summary-label">Complete Students</div></div>
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
            @if($classes->hasPages())
                <div class="pagination-wrap">
                    <div class="pagination-meta">
                        Showing {{ $classes->firstItem() }} to {{ $classes->lastItem() }} of {{ $classes->total() }} classes
                    </div>
                    <div class="pagination-nav">
                        <a href="{{ $classes->previousPageUrl() ?: '#' }}" class="page-btn {{ $classes->onFirstPage() ? 'disabled' : '' }}">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        @for($page = 1; $page <= $classes->lastPage(); $page++)
                            @if($page === $classes->currentPage())
                                <span class="page-btn active">{{ $page }}</span>
                            @else
                                <a href="{{ $classes->url($page) }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endfor
                        <a href="{{ $classes->nextPageUrl() ?: '#' }}" class="page-btn {{ $classes->hasMorePages() ? '' : 'disabled' }}">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        @endif
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ vasset('js/professor/requirement-status-classes.js') }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>