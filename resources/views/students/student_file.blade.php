    <!DOCTYPE html>
    <html lang="en">
    <head>
    
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>InternConnect - Downloadable Files</title>
        <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
        <link rel="stylesheet" href="{{ vasset('css/student_downloadablefile-responsive.css') }}">
        <script>
            (function(){
                try {
                    if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                        document.documentElement.classList.add('sidebar-is-collapsed');
                    }
                } catch(e){}
            })();
        </script>
        <link rel="stylesheet" href="{{ vasset('css/student/file.css') }}">
        <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
        <script src="{{ vasset('js/darkmode.js') }}"></script>
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
            <a href="{{ url('/student/evaluation') }}" class="nav-item">
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
                    <span>Student Portal</span>
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
                    <div class="stat-icon red"><i class="fa fa-university"></i></div>
                    <div>
                        <div class="stat-num">{{ count($upload) }}</div>
                        <div class="stat-name">General Files</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fa fa-graduation-cap"></i></div>
                    <div>
                        <div class="stat-num">{{ count($roomTemplates) }}</div>
                        <div class="stat-name">Class Templates</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa fa-chalkboard"></i></div>
                    <div>
                        <div class="stat-num" style="font-size:16px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">
                            {{ $currentClass ? $currentClass->room : 'No Class' }}
                        </div>
                        <div class="stat-name">{{ $currentClass ? 'Enrolled Room' : 'Not Joined' }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher Navigation -->
            <div class="file-tabs-nav">
                <button type="button" class="file-tab-btn active" id="tabBtnGeneral" data-tab="generalTabPane">
                    <i class="fa fa-university"></i>
                    <span>General OJT Files</span>
                    <span class="file-tab-count">{{ count($upload) }}</span>
                </button>
                <button type="button" class="file-tab-btn" id="tabBtnClass" data-tab="classTabPane">
                    <i class="fa fa-graduation-cap"></i>
                    <span>Class Templates</span>
                    <span class="file-tab-count purple">{{ count($roomTemplates) }}</span>
                </button>
            </div>

            <!-- TAB 1: General OJT Files (Coordinator) -->
            <div class="tab-pane active" id="generalTabPane">
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="table-card-header-left">
                            <div class="header-icon"><i class="fa fa-folder-open"></i></div>
                            <div>
                                <h2>General Downloadable Files</h2>
                                <p>Official university-wide OJT forms, guidelines, and manuals from the Coordinator</p>
                            </div>
                        </div>
                        <div class="file-count-badge">
                            <i class="fa fa-file"></i>
                            {{ count($upload) }} file{{ count($upload) != 1 ? 's' : '' }} available
                        </div>
                    </div>

                    <div class="table-card-body">
                        <table id="fileTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>FILE NAME</th>
                                    <th>FILE</th>
                                    <th>DATE UPLOADED</th>
                                    <th>UPLOADED BY</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upload as $file)
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
                                <tr>
                                    <td>
                                        <div class="file-name-cell">
                                            <div class="file-icon-wrap">
                                                <i class="fa {{ $icon }}"></i>
                                            </div>
                                            <div>
                                                <div class="file-name-text">{{ $file->name }}</div>
                                                <div class="file-ext">{{ strtoupper($ext) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#666; font-size:13px;">{{ $file->file }}</td>
                                    <td data-order="{{ \Carbon\Carbon::parse($file->created_at)->timestamp }}">
                                        {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y') }}
                                    </td>
                                    <td>
                                        {{ $file->uploader_name ?: 'Coordinator' }}
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <button type="button"
                                                    class="icon-action-btn btn-view btn-preview-file"
                                                    title="Preview File"
                                                    aria-label="Preview File"
                                                    data-file-url="{{ url('/view/file', $file->file) }}"
                                                    data-file-name="{{ $file->name }}"
                                                    data-file-ext="{{ $ext }}"
                                                    data-download-url="{{ url('/download', $file->file) }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="{{ url('/download', $file->file) }}" class="icon-action-btn btn-download" title="Download File" aria-label="Download File">
                                                <i class="fa fa-download"></i>
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

            <!-- TAB 2: Class Templates (Professor) -->
            <div class="tab-pane" id="classTabPane" style="display: none;">
                @if (!$currentClass)
                    <div class="table-card">
                        <div class="class-empty-container">
                            <div class="class-empty-icon">
                                <i class="fa fa-door-closed"></i>
                            </div>
                            <h3 class="class-empty-title">You Haven't Joined a Class Room Yet</h3>
                            <p class="class-empty-desc">
                                Class templates and instructional rubrics are uploaded specifically by professors for enrolled students.
                                Join your designated class room under the Class module to access your adviser's materials.
                            </p>
                            <a href="{{ url('/student/class') }}" class="btn-class-join-link">
                                <i class="fa fa-clipboard"></i> Go to Class Module
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Active Room Info Banner -->
                    <div class="class-room-banner">
                        <div class="class-room-banner-left">
                            <div class="class-room-icon">
                                <i class="fa fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <div class="class-room-title">
                                    <span>{{ $currentClass->room }}</span>
                                    <span class="class-room-badge">{{ $currentClass->course }}</span>
                                </div>
                                <div class="class-room-sub">
                                    <span><i class="fa fa-user-tie"></i> Adviser: <strong>{{ $currentClass->adviser_name }}</strong></span>
                                    @if(!empty($currentClass->semester))
                                        <span class="separator-dot">•</span>
                                        <span><i class="fa fa-calendar-alt"></i> {{ $currentClass->semester }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="class-room-banner-right">
                            <span class="class-templates-count-badge">
                                <i class="fa fa-file-download"></i> {{ count($roomTemplates) }} Class Template{{ count($roomTemplates) != 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>

                    <div class="table-card">
                        <div class="table-card-header">
                            <div class="table-card-header-left">
                                <div class="header-icon purple"><i class="fa fa-file-signature"></i></div>
                                <div>
                                    <h2>Class & Room Templates</h2>
                                    <p>Materials and templates uploaded by <strong>{{ $currentClass->adviser_name }}</strong> for <strong>{{ $currentClass->room }}</strong></p>
                                </div>
                            </div>
                            <div class="file-count-badge purple">
                                <i class="fa fa-file"></i>
                                {{ count($roomTemplates) }} file{{ count($roomTemplates) != 1 ? 's' : '' }} available
                            </div>
                        </div>

                        <div class="table-card-body">
                            @if ($roomTemplates->isEmpty())
                                <div class="class-empty-container" style="padding: 42px 20px;">
                                    <div class="class-empty-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                                        <i class="fa fa-folder-open"></i>
                                    </div>
                                    <h4 class="class-empty-title" style="font-size: 17px;">No Class Templates Uploaded Yet</h4>
                                    <p class="class-empty-desc">
                                        Your adviser (<strong>{{ $currentClass->adviser_name }}</strong>) has not uploaded any room-specific templates for <strong>{{ $currentClass->room }}</strong> yet.
                                    </p>
                                </div>
                            @else
                                <table id="classFileTable" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>FILE NAME</th>
                                            <th>FILE</th>
                                            <th>DATE UPLOADED</th>
                                            <th>UPLOADED BY</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roomTemplates as $file)
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
                                        <tr>
                                            <td>
                                                <div class="file-name-cell">
                                                    <div class="file-icon-wrap purple">
                                                        <i class="fa {{ $icon }}"></i>
                                                    </div>
                                                    <div>
                                                        <div class="file-name-text">{{ $file->name }}</div>
                                                        <div class="file-ext">{{ strtoupper($ext) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="color:#666; font-size:13px;">{{ $file->file }}</td>
                                            <td data-order="{{ \Carbon\Carbon::parse($file->created_at)->timestamp }}">
                                                {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y') }}
                                            </td>
                                            <td>
                                                {{ $file->uploader_name ?: $currentClass->adviser_name }}
                                            </td>
                                            <td>
                                                <div class="actions-cell">
                                                    <button type="button"
                                                            class="icon-action-btn btn-view btn-preview-file"
                                                            title="Preview File"
                                                            aria-label="Preview File"
                                                            data-file-url="{{ url('/view/file', $file->file) }}"
                                                            data-file-name="{{ $file->name }}"
                                                            data-file-ext="{{ $ext }}"
                                                            data-download-url="{{ url('/download', $file->file) }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <a href="{{ url('/download', $file->file) }}" class="icon-action-btn btn-download" title="Download File" aria-label="Download File">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>
    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
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
                            <span id="filePreviewBadge" style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; font-size:12px; font-weight:600; flex-shrink:0;">
                                <i class="fa fa-eye"></i> Preview
                            </span>
                            <span id="filePreviewSubTitle" style="font-size:13px; font-weight:600; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                        </div>
                        <a id="filePreviewDownloadBtn" href="#" class="btn-download" style="padding:6px 14px; font-size:12px; text-decoration:none;">
                            <i class="fa fa-download"></i> Download File
                        </a>
                    </div>
                    
                    <!-- In-browser preview iframe for PDF, Images, Text -->
                    <iframe id="filePreviewFrame" title="File Preview" style="width:100%; height:75vh; min-height:400px; border:0; background:#fff; display:none;"></iframe>

                    <!-- Clean Notice Box for Word / Unsupported documents -->
                    <div id="filePreviewNotice" style="display:none; padding:60px 24px; text-align:center; background:#fff; min-height:400px;">
                        <div style="width:80px; height:80px; margin:0 auto 20px auto; border-radius:20px; background:#eff6ff; border:1px solid #bfdbfe; display:flex; align-items:center; justify-content:center; color:#2563eb; font-size:36px;">
                            <i class="fa fa-file-word" id="fileNoticeIcon"></i>
                        </div>
                        <h4 style="font-size:18px; font-weight:700; color:#1e293b; margin-bottom:8px;" id="fileNoticeHeading">
                            Preview Not Supported for Word Documents
                        </h4>
                        <p style="font-size:14px; color:#64748b; max-width:480px; margin:0 auto 24px auto; line-height:1.6;" id="fileNoticeText">
                            In-browser preview is not supported for Word (.docx / .doc) documents. Instead of viewing in browser, please download the file to open and view it directly on your device.
                        </p>
                        <a id="fileNoticeDownloadBtn" href="#" class="btn-download" style="padding:10px 24px; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px;">
                            <i class="fa fa-download"></i> Download File Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ vasset('js/student/file.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
