<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - Requirements Viewer</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/professor/require-view.css') }}">
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
        <a href="{{ url('/professor/class') }}" class="nav-item active">
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
        @php
            $backToRequirementsUrl = url('/studentrequire') . '?value=' . urlencode($value) . (!empty($roomId) ? '&roomId=' . $roomId : '');
        @endphp

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Requirement <span>Viewer</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/professor/home') }}">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                    <i class="fa fa-chevron-right"></i>
                    <a href="{{ $backToRequirementsUrl }}">Requirements</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>{{ $file }}</span>
                </div>
            </div>
            <button class="btn-back"
                onclick="window.location.href='{{ $backToRequirementsUrl }}'">
                <i class="fa fa-arrow-left"></i> Back to Requirements
            </button>
        </div>

        <!-- Viewer Card -->
        @php $fileCount = count($files); @endphp
        <div class="viewer-card">
            <div class="viewer-card-header">
                <div class="viewer-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-alt"></i></div>
                    <div>
                        <h2>{{ $file }}</h2>
                        <p>Viewing submitted requirement file(s) for this category</p>
                    </div>
                </div>
                <div class="file-count-badge">
                    <i class="fa fa-paperclip"></i>
                    {{ $fileCount }} {{ $fileCount == 1 ? 'file' : 'files' }}
                </div>
            </div>

            <div class="viewer-card-body">

                @forelse($files as $fileItem)
                @php
                    $ext = strtolower(pathinfo($fileItem->file, PATHINFO_EXTENSION));
                    $canPreview = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt', 'html', 'htm']);
                @endphp
                <div class="file-viewer-wrap">
                    <div class="file-viewer-toolbar">
                        <div class="file-viewer-name">
                            <div class="file-icon-box">
                                @if(in_array($ext, ['pdf']))
                                    <i class="fa fa-file-pdf"></i>
                                @elseif(in_array($ext, ['doc','docx']))
                                    <i class="fa fa-file-word"></i>
                                @elseif(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                    <i class="fa fa-file-image"></i>
                                @elseif(in_array($ext, ['xls','xlsx']))
                                    <i class="fa fa-file-excel"></i>
                                @else
                                    <i class="fa fa-file-alt"></i>
                                @endif
                            </div>
                            {{ $fileItem->file }}
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span class="file-preview-badge {{ $canPreview ? '' : 'no-preview' }}">
                                <i class="fa {{ $canPreview ? 'fa-eye' : 'fa-file-download' }}"></i>
                                {{ $canPreview ? 'Preview available' : 'No preview available' }}
                            </span>
                            <a href="{{ url('/download/req/' . $fileItem->id) }}" class="btn-download-file">
                                <i class="fa fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    @if($canPreview)
                        <div class="file-iframe-wrap">
                            <iframe src="/assets/{{ $fileItem->file }}"
                                    title="{{ $fileItem->file }}">
                            </iframe>
                        </div>
                    @else
                        <div class="unsupported-file-message">
                            <div class="unsupported-icon">
                                <i class="fa fa-file-excel"></i>
                            </div>
                            <h3>This type of file cannot be previewed</h3>
                            <p>Please download the file to view its contents.</p>
                            <a href="{{ url('/download/req/' . $fileItem->id) }}" class="btn-download-file">
                                <i class="fa fa-download"></i> Download to View
                            </a>
                        </div>
                    @endif
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <h3>No Files Found</h3>
                    <p>No requirement files have been submitted for this category yet.</p>
                </div>
                @endforelse

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
        <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
        <span class="divider">|</span>
        <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
    </div>
</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ vasset('js/professor/require-view.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>