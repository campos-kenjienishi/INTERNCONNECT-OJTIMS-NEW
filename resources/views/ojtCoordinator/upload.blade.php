<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Upload Templates</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ vasset('css/coordinator/upload.css') }}?v={{ time() }}">
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
        <a href="{{ url('/uploadpage') }}" class="nav-item active">
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
                <h1>Upload <span>Templates</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Upload Templates</span>
                </div>
            </div>
            <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fa fa-cloud-upload-alt"></i> Upload New Template
            </button>
        </div>

        <!-- Stats Row -->
        @php $totalFiles = count($data); @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $totalFiles }}</div>
                    <div class="stat-name">Total Templates</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-file-alt"></i></div>
                <div>
                    <div class="stat-num" style="font-size: 16px;">DOC, PDF, XLS</div>
                    <div class="stat-name">Accepted Formats</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-cloud-upload-alt"></i></div>
                <div>
                    <div class="stat-num">Active</div>
                    <div class="stat-name">Upload Status</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-download"></i></div>
                <div>
                    <div class="stat-num">{{ $totalFiles }}</div>
                    <div class="stat-name">Available Downloads</div>
                </div>
            </div>
        </div>

        <!-- Templates Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-upload"></i></div>
                    <div>
                        <h2>Uploaded Templates</h2>
                        <p>Manage the OJT document templates that you uploaded</p>
                    </div>
                </div>
                <div class="file-count-badge">
                    <i class="fa fa-paperclip"></i>
                    {{ $totalFiles }} {{ $totalFiles == 1 ? 'file' : 'files' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="fileTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>File</th>
                            <th>Date Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $file)
                        <tr>
                            <!-- Template Name -->
                            <td>
                                <div class="file-cell">
                                    <div class="file-icon-box">
                                        @php
                                            $ext = strtolower(pathinfo($file->file, PATHINFO_EXTENSION));
                                            $iconClass = match($ext) {
                                                'pdf' => 'fa-file-pdf',
                                                'xls', 'xlsx' => 'fa-file-excel',
                                                default => 'fa-file-word',
                                            };
                                        @endphp
                                        <i class="fa {{ $iconClass }}"></i>
                                    </div>
                                    <div>
                                        <div class="file-name-text">{{ $file->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Raw filename -->
                            <td>
                                <span style="font-size:12.5px; color:#888; font-family:monospace;">
                                    {{ $file->file }}
                                </span>
                            </td>

                            <!-- Date -->
                            <td>
                                <div class="date-cell">
                                    <i class="fa fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y  h:i A') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">
                                    @if(in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'svg']))
                                        <button type="button"
                                                class="btn-view btn-preview-file"
                                                data-file-url="{{ url('/view/file', $file->file) }}"
                                                data-file-name="{{ $file->name }}"
                                                data-download-url="{{ url('/download', $file->file) }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                    @endif

                                    <a class="btn-download btn-dl-item"
                                       href="{{ url('/download', $file->file) }}">
                                        <i class="fa fa-download"></i> Download
                                    </a>

                                    <button class="btn-remove remove-button"
                                        data-file-id="{{ $file->id }}">
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

<!-- =============== UPLOAD MODAL =============== -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-cloud-upload-alt"></i> Upload New Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" action="{{ url('/uploadfile') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-tag"></i> Template Name
                        </label>
                        <input class="field-input" type="text" name="name"
                               placeholder="e.g. OJT Endorsement Letter" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-file-alt"></i> Choose File
                        </label>
                        <div class="file-dropzone" id="dropzone">
                            <input type="file" name="file" id="fileInput" data-max-size-mb="30"
                                   accept=".doc,.docx,.pdf,.xls,.xlsx" required>
                            <div class="file-dropzone-icon">
                                <i class="fa fa-cloud-upload-alt"></i>
                            </div>
                            <div class="file-dropzone-title">Click or drag to upload</div>
                            <div class="file-dropzone-sub">Accepted: .doc, .docx, .pdf, .xls, .xlsx | Max file size: 30 MB</div>
                            <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                            <div class="file-dropzone-name" id="selectedFileName"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-cloud-upload-alt me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:16px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%); color:#fff; padding:16px 20px;">
                <h5 class="modal-title" style="font-size:16px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px; margin:0;">
                    <i class="fa fa-file-alt"></i> <span id="filePreviewTitle">Document Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1); opacity:0.8;"></button>
            </div>
            <div class="modal-body" style="padding:0; background:#f8fafc;">
                <div style="padding:12px 18px; border-bottom:1px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:8px; min-width:0;">
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; font-size:12px; font-weight:600; flex-shrink:0;">
                            <i class="fa fa-eye"></i> Preview
                        </span>
                        <span id="filePreviewSubTitle" style="font-size:13px; font-weight:600; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                    </div>
                    <a id="filePreviewDownloadBtn" href="#" class="btn-download" style="padding:6px 14px; font-size:12px; text-decoration:none;">
                        <i class="fa fa-download"></i> Download File
                    </a>
                </div>
                <iframe id="filePreviewFrame" title="File Preview" style="width:100%; height:75vh; min-height:400px; border:0; background:#fff;"></iframe>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script>
    window.coordinatorConfig = {
        fileError: @json($errors->first('file')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/coordinator/upload.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/upload-size-guard.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
