<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Audit Log</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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
    <link rel="stylesheet" href="{{ vasset('css/coor_audit-responsive.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/coordinator/audit.css') }}?v={{ time() }}">
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">

    <a href="#" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ url('/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            <i class="fa fa-user-tie"></i>
        </div>
        <div class="user-info">
            <span class="user-name">{{ $data->full_name }}</span>
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
        <a href="{{ url('/MOA') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-folder-open"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <div class="nav-group-reports">
            <a href="{{ url('/reports') }}" class="nav-item nav-item-reports">
                <span class="nav-icon"><i class="fa fa-chart-bar"></i></span>
                <span class="nav-label">Reports</span>
                <span class="tooltip-label">Reports</span>
            </a>
            <div class="nav-sub">
                <a href="{{ url('/reports') }}" class="nav-sub-item">
                    <i class="fa fa-user-graduate"></i> Student OJT Info
                </a>
                <a href="{{ url('/reportsExpired') }}" class="nav-sub-item">
                    <i class="fa fa-file-contract"></i> MOA
                </a>
            </div>
        </div>
        <a href="{{ url('/analytics') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-chart-line"></i></span>
            <span class="nav-label">Analytics</span>
            <span class="tooltip-label">Analytics</span>
        </a>
        <a href="{{ url('/auditlog') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-clipboard-list"></i></span>
            <span class="nav-label">Audit Log</span>
            <span class="tooltip-label">Audit Log</span>
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
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">
                On-the-Job Training <span>Information Management System</span>
            </span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-user-shield"></i>
                OJT Coordinator
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Audit <span>Log</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Audit Log</span>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-clipboard-list"></i></div>
                <div>
                    <div class="stat-num">{{ count($logs) }}</div>
                    <div class="stat-name">Total Logs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-plus-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $logs->where('action', 'create')->count() }}</div>
                    <div class="stat-name">Create Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-edit"></i></div>
                <div>
                    <div class="stat-num">{{ $logs->where('action', 'update')->count() }}</div>
                    <div class="stat-name">Update Actions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-trash"></i></div>
                <div>
                    <div class="stat-num">{{ $logs->where('action', 'delete')->count() }}</div>
                    <div class="stat-name">Delete Actions</div>
                </div>
            </div>
        </div>

        <!-- Audit Log Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-clipboard-list"></i></div>
                    <div>
                        <h2>System Activity Logs</h2>
                        <p>Track all actions performed within the InternConnect system</p>
                    </div>
                </div>
                <div class="table-card-header-right" id="auditTableHeaderSearch"></div>
            </div>

            <div class="table-card-body">
                @if($logs->isEmpty())
                    <div class="empty-state">
                        <i class="fa fa-clipboard-list"></i>
                        <p>No audit logs recorded yet.</p>
                    </div>
                @else
                <div class="audit-filters" id="auditFilters">
                    <div class="audit-filter-group">
                        <label for="actionFilter" class="audit-filter-label">Action</label>
                        <select id="actionFilter" class="audit-filter-select">
                            <option value="">All Actions</option>
                            @foreach($logs->pluck('action')->filter()->map(fn ($action) => strtolower(trim($action)))->unique()->sort()->values() as $actionOption)
                                <option value="{{ $actionOption }}">{{ ucfirst($actionOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="audit-filter-group">
                        <label for="roleFilter" class="audit-filter-label">Role</label>
                        <select id="roleFilter" class="audit-filter-select">
                            <option value="">All Roles</option>
                            @foreach($logs->pluck('user_role')->filter()->map(fn ($role) => strtolower(trim($role)))->unique()->sort()->values() as $roleOption)
                                <option value="{{ $roleOption }}">{{ ucfirst($roleOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="audit-filter-group">
                        <label for="monthFilter" class="audit-filter-label">Month</label>
                        <select id="monthFilter" class="audit-filter-date">
                            <option value="">All Months</option>
                            @foreach([
                                '01' => 'January',
                                '02' => 'February',
                                '03' => 'March',
                                '04' => 'April',
                                '05' => 'May',
                                '06' => 'June',
                                '07' => 'July',
                                '08' => 'August',
                                '09' => 'September',
                                '10' => 'October',
                                '11' => 'November',
                                '12' => 'December',
                            ] as $monthValue => $monthLabel)
                                <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="audit-filter-group">
                        <label for="yearFilter" class="audit-filter-label">Year</label>
                        <select id="yearFilter" class="audit-filter-date">
                            <option value="">All Years</option>
                            @foreach($logs->pluck('created_at')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('Y'))->unique()->sortDesc()->values() as $yearOption)
                                <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="audit-table-wrapper" style="position: relative; min-height: 280px;">
                    <!-- Loading Skeleton State -->
                    <div id="auditTableLoading" class="audit-table-loader">
                        <div class="audit-skeleton-header">
                            <div class="skeleton-pill w-title"></div>
                            <div class="skeleton-pill w-filter"></div>
                        </div>
                        <div class="audit-skeleton-row">
                            <div class="skeleton-pill w-date"></div>
                            <div class="skeleton-pill w-badge"></div>
                            <div class="skeleton-pill w-name"></div>
                            <div class="skeleton-pill w-role"></div>
                            <div class="skeleton-pill w-module"></div>
                            <div class="skeleton-pill w-btn"></div>
                        </div>
                        <div class="audit-skeleton-row">
                            <div class="skeleton-pill w-date"></div>
                            <div class="skeleton-pill w-badge"></div>
                            <div class="skeleton-pill w-name"></div>
                            <div class="skeleton-pill w-role"></div>
                            <div class="skeleton-pill w-module"></div>
                            <div class="skeleton-pill w-btn"></div>
                        </div>
                        <div class="audit-skeleton-row">
                            <div class="skeleton-pill w-date"></div>
                            <div class="skeleton-pill w-badge"></div>
                            <div class="skeleton-pill w-name"></div>
                            <div class="skeleton-pill w-role"></div>
                            <div class="skeleton-pill w-module"></div>
                            <div class="skeleton-pill w-btn"></div>
                        </div>
                        <div class="audit-skeleton-row">
                            <div class="skeleton-pill w-date"></div>
                            <div class="skeleton-pill w-badge"></div>
                            <div class="skeleton-pill w-name"></div>
                            <div class="skeleton-pill w-role"></div>
                            <div class="skeleton-pill w-module"></div>
                            <div class="skeleton-pill w-btn"></div>
                        </div>
                    </div>

                    <table id="auditTable" class="display" style="width:100%; opacity: 0; transition: opacity 0.3s ease;">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>Action</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Module</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr
                            data-action="{{ strtolower(trim((string) $log->action)) }}"
                            data-role="{{ strtolower(trim((string) $log->user_role)) }}"
                            data-month="{{ \Carbon\Carbon::parse($log->created_at)->format('m') }}"
                            data-year="{{ \Carbon\Carbon::parse($log->created_at)->format('Y') }}"
                        >
                            <td class="audit-date-cell" data-order="{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}">
                                <div class="date-main">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}
                                </div>
                                <div class="date-sub">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $action = strtolower($log->action);
                                    $actionClass = match($action) {
                                        'create'  => 'action-create',
                                        'update'  => 'action-update',
                                        'delete'  => 'action-delete',
                                        'approve' => 'action-approve',
                                        'deny'    => 'action-deny',
                                        default   => 'action-default',
                                    };
                                    $actionIcon = match($action) {
                                        'create'  => 'fa-plus-circle',
                                        'update'  => 'fa-edit',
                                        'delete'  => 'fa-trash',
                                        'approve' => 'fa-check-circle',
                                        'deny'    => 'fa-times-circle',
                                        default   => 'fa-circle',
                                    };
                                @endphp
                                <span class="action-badge {{ $actionClass }}">
                                    <i class="fa {{ $actionIcon }}"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="audit-name-cell">
                                <div class="name-cell">
                                    <div class="name-avatar">
                                        {{ strtoupper(substr($log->user_name ?? 'N', 0, 1)) }}
                                    </div>
                                    <span class="name-text">{{ $log->user_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $role = strtolower(trim((string) ($log->user_role ?? 'unknown')));
                                    $roleClass = match($role) {
                                        'student' => 'role-student',
                                        'ojt coordinator', 'coordinator' => 'role-coordinator',
                                        'professor' => 'role-professor',
                                        default => '',
                                    };
                                @endphp
                                <span class="role-badge {{ $roleClass }}">
                                    <i class="fa fa-user-shield"></i>
                                    {{ $log->user_role ?: 'Unknown' }}
                                </span>
                            </td>
                            <td>
                                <span class="module-badge">
                                    <i class="fa fa-tag"></i>
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="audit-desc-cell">
                                <button
                                    type="button"
                                    class="btn-view-desc"
                                    data-action="{{ ucfirst($log->action) }}"
                                    data-role="{{ $log->user_role ?: 'Unknown' }}"
                                    data-module="{{ $log->module }}"
                                    data-datetime="{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}"
                                    data-description="{{ $log->description }}"
                                >
                                    <i class="fa fa-eye"></i>
                                    View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                @endif

            </div>
        </div>

    </div>

    <div class="desc-modal" id="descModal">
        <div class="desc-modal-card">
            <div class="desc-modal-header">
                <div class="desc-modal-title">Audit Log Description</div>
                <button type="button" class="desc-modal-close" id="descModalClose">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="desc-modal-body">
                <div class="desc-modal-meta">
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Action</div>
                        <div class="desc-modal-meta-value" id="descModalAction">N/A</div>
                    </div>
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Role</div>
                        <div class="desc-modal-meta-value" id="descModalRole">Unknown</div>
                    </div>
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Module</div>
                        <div class="desc-modal-meta-value" id="descModalModule">N/A</div>
                    </div>
                    <div class="desc-modal-meta-item">
                        <div class="desc-modal-meta-label">Date / Time</div>
                        <div class="desc-modal-meta-value" id="descModalDate">N/A</div>
                    </div>
                </div>
                <div class="desc-modal-text" id="descModalText"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
        <span class="footer-copy">
            &copy; 1998&ndash;2026 <span>Polytechnic University of the Philippines</span>
        </span>
    </div>
    <div class="footer-links">
        <a href="https://www.pup.edu.ph/" target="_blank">
            <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
            PUP Website
        </a>
        <span class="divider">|</span>
        <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
        <span class="divider">|</span>
        <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
    </div>
</footer>

</div>

<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/coordinator/audit.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
