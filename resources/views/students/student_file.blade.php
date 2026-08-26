    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>InternConnect - Downloadable Files</title>
        <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
        <link rel="stylesheet" href="{{ vasset('css/student_downloadablefile-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/student/file.css') }}">
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

        <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
            <div class="user-avatar"><i class="fa fa-user"></i></div>
            <div class="user-info">
                <span class="user-name">{{ $user->full_name }}</span>
                <span class="user-role">Student</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ url('/student/home') }}" class="nav-item">
                <span class="nav-icon"><i class="fa fa-home"></i></span>
                <span class="nav-label">Home</span>
                <span class="tooltip-label">Home</span>
            </a>
            <a href="{{ url('/student/ojtinfo') }}" class="nav-item">
                <span class="nav-icon"><i class="fa fa-layer-group"></i></span>
                <span class="nav-label">OJT Information</span>
                <span class="tooltip-label">OJT Information</span>
            </a>
            <a href="{{ url('/student/class') }}" class="nav-item">
                <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
                <span class="nav-label">Class</span>
                <span class="tooltip-label">Class</span>
            </a>
            <a href="{{ url('/student/files') }}" class="nav-item active">
                <span class="nav-icon"><i class="fa fa-download"></i></span>
                <span class="nav-label">Downloadable Files</span>
                <span class="tooltip-label">Downloadable Files</span>
            </a>
            <a href="{{ url('/student/MOA') }}" class="nav-item">
                <span class="nav-icon"><i class="fa fa-file-alt"></i></span>
                <span class="nav-label">Notarized MOA</span>
                <span class="tooltip-label">Notarized MOA</span>
            </a>
            <a href="{{ url('/student/requirements') }}" class="nav-item">
                <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
                <span class="nav-label">Requirements</span>
                <span class="tooltip-label">Requirements</span>
            </a>
                <a href="{{ url('/student/evaluation') }}" class="nav-item{{ request()->is('student/evaluation*') ? ' active' : '' }}">
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
                    <i class="fa fa-graduation-cap"></i>
                    Student Portal
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1>Downloadable <span>Files</span></h1>
                    <div class="breadcrumb">
                        <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                        <i class="fa fa-chevron-right"></i>
                        <span>Downloadable Files</span>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fa fa-file-alt"></i></div>
                    <div>
                        <div class="stat-num">{{ count($upload) }}</div>
                        <div class="stat-name">Total Files</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa fa-download"></i></div>
                    <div>
                        <div class="stat-num">Free</div>
                        <div class="stat-name">All Downloads</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa fa-folder-open"></i></div>
                    <div>
                        <div class="stat-num">OJT</div>
                        <div class="stat-name">Templates & Forms</div>
                    </div>
                </div>
            </div>

            <!-- Files Table Card -->
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-header-left">
                        <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                        <div>
                            <h2>Downloadable Files</h2>
                            <p>Click the download button to save any file to your device</p>
                        </div>
                    </div>
                    <div class="file-count-badge">
                        <i class="fa fa-file"></i>
                        {{ count($upload) }} file{{ count($upload) != 1 ? 's' : '' }} available
                    </div>
                </div>

                <div class="table-card-body">

                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    <script>
                        $(document).ready(function () {
                        $('#fileTable').DataTable({
                            "order": [[2, 'desc']],
                            "autoWidth": false
                        });
                    });
                    </script>
                    <div style="overflow-x: auto; width: 100%;">
                      <table id="fileTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>File</th>
                                <th>Date Uploaded</th>
                                <th>Uploaded By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upload as $file)
                            <tr>
                                <td>
                                    <div class="file-name-cell">
                                        <div class="file-icon-wrap">
                                            @php
                                                $ext = strtolower(pathinfo($file->file, PATHINFO_EXTENSION));
                                                $icon = match($ext) {
                                                    'pdf'  => 'fa-file-pdf',
                                                    'doc', 'docx' => 'fa-file-word',
                                                    'xls', 'xlsx' => 'fa-file-excel',
                                                    'ppt', 'pptx' => 'fa-file-powerpoint',
                                                    'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                                    'zip', 'rar' => 'fa-file-archive',
                                                    default => 'fa-file-alt'
                                                };
                                            @endphp
                                            <i class="fa {{ $icon }}"></i>
                                        </div>
                                        <div>
                                            <div class="file-name-text">{{ $file->name }}</div>
                                            <div class="file-ext">{{ strtoupper($ext) }} file</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color:#888; font-size:12.5px;">{{ $file->file }}</td>
                                <td>
                                    <div class="date-cell">
                                        {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y') }}
                                        <div class="date-time">{{ \Carbon\Carbon::parse($file->created_at)->format('h:i A') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="uploader-cell">
                                        <div class="uploader-avatar"><i class="fa fa-user"></i></div>
                                        {{ $file->uploader_name }}
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        @if(in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'svg']))
                                            <button type="button"
                                                    class="btn-view btn-preview-file"
                                                    data-file-url="{{ url('/view/file', $file->file) }}"
                                                    data-file-name="{{ $file->name }}"
                                                    data-download-url="{{ url('/download', $file->file) }}">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                        @endif
                                        <a href="{{ url('/download', $file->file) }}" class="btn-download">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

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

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- =============== FILE PREVIEW MODAL =============== -->
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


    <script src="{{ vasset('js/student/file.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
