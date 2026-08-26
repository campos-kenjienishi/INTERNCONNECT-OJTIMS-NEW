<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - File Categories</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/professor_fileReq-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/file-category.css') }}">
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

    <a href="{{ url('/professor/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar">
            @if(isset($data->profile_photo) && $data->profile_photo)
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
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
        <a href="{{ url('/professor/maintain') }}" class="nav-item active">
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
                <h1>File <span>Categories</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Maintenance</span>
                    <i class="fa fa-chevron-right"></i>
                    <span>File Categories</span>
                </div>
            </div>
            <button class="btn-add-category"
                data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fa fa-plus"></i> Add File Category
            </button>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-folder"></i></div>
                <div>
                    <div class="stat-num">{{ count($files) }}</div>
                    <div class="stat-name">Total Categories</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-tags"></i></div>
                <div>
                    <div class="stat-num">{{ $files->where('phase', 'basic')->count() }}</div>
                    <div class="stat-name">Basic Requirements</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $files->where('phase', '!=', 'basic')->count() }}</div>
                    <div class="stat-name">Other Requirements</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                    <div>
                        <h2>File Categories</h2>
                        <p>Manage requirement file categories for student submissions</p>
                    </div>
                </div>
                <div class="cat-count-badge">
                    <i class="fa fa-folder"></i>
                    {{ count($files) }} {{ count($files) == 1 ? 'category' : 'categories' }}
                </div>
            </div>

            <div class="table-card-body">

                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Phase</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                        <tr>
                            <td>
                                <div class="cat-cell">
                                    <div class="cat-icon-box">
                                        <i class="fa fa-folder"></i>
                                    </div>
                                    <span class="cat-name-text">{{ $file->fileName }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ ($file->phase ?? 'other') === 'basic' ? 'status-pending' : 'status-approved' }}">
                                    <i class="fa {{ ($file->phase ?? 'other') === 'basic' ? 'fa-unlock-alt' : 'fa-layer-group' }}"></i>
                                    {{ ($file->phase ?? 'other') === 'basic' ? 'Basic Requirements' : 'Other Requirements' }}
                                </span>
                            </td>
                            <td>
                                <div class="uploader-cell">
                                    <div class="uploader-avatar">
                                        <i class="fa fa-user-tie"></i>
                                    </div>
                                    {{ $file->uploadedBy }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button
                                        type="button"
                                        class="btn-edit edit-button"
                                        data-file-id="{{ $file->id }}"
                                        data-file-name="{{ e($file->fileName) }}"
                                        data-file-phase="{{ $file->phase ?? 'other' }}"
                                    >
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-remove remove-button" data-file-id="{{ $file->id }}">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

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

<!-- =============== EDIT CATEGORY MODAL =============== -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-edit"></i> Edit File Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <label class="modal-field-label">
                        <i class="fa fa-folder"></i> Category Name
                    </label>
                    <input class="modal-field-input" type="text" name="fileName" id="editCategoryName"
                           placeholder="e.g. Endorsement Letter, Daily Time Record..."
                           required>
                    <label class="modal-field-label" style="margin-top:14px;">
                        <i class="fa fa-sitemap"></i> Requirement Phase
                        <span class="modal-field-label-note">Basic = before OJT, Other = during/after OJT</span>
                    </label>
                    <select class="modal-field-select" name="phase" id="editCategoryPhase" required>
                        <option value="basic">Basic Requirements</option>
                        <option value="other">Other Requirements</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== ADD CATEGORY MODAL =============== -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-folder-plus"></i> Add File Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/fileCategory') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <label class="modal-field-label">
                        <i class="fa fa-folder"></i> Category Name
                    </label>
                    <input class="modal-field-input" type="text" name="fileName"
                           placeholder="e.g. Endorsement Letter, Daily Time Record..."
                           required>
                    <label class="modal-field-label" style="margin-top:14px;">
                        <i class="fa fa-sitemap"></i> Requirement Phase
                        <span class="modal-field-label-note">Basic = before OJT, Other = during/after OJT</span>
                    </label>
                    <select class="modal-field-select" name="phase" required>
                        <option value="basic" selected>Basic Requirements</option>
                        <option value="other">Other Requirements</option>
                    </select>
                    <input type="hidden" name="uploadedBy" value="{{ $userName }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-check me-1"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/file-category.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>