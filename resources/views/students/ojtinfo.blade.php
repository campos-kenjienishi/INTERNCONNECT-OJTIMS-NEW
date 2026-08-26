<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect - OJT Information</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/ojtinfo-responsive.css') }}">

    <link rel="stylesheet" href="{{ vasset('css/student/ojt-info.css') }}">
</head>
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
            <span class="user-name">{{ $data->full_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Home</span>
            <span class="tooltip-label">Home</span>
        </a>

        <a href="{{ url('/student/ojtinfo') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-layer-group"></i></span>
            <span class="nav-label">OJT Information</span>
            <span class="tooltip-label">OJT Information</span>
        </a>

        <a href="{{ url('/student/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>

        <a href="{{ url('/student/files') }}" class="nav-item">
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
                <h1>OJT <span>Information</span></h1>
                <div class="breadcrumb" style="margin-top: 6px;">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>OJT Information</span>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="fa fa-building"></i></div>
                <div>
                    <h2>Company & OJT Details</h2>
                    <p>Fill in and update your on-the-job training information below</p>
                </div>
            </div>

            <div class="form-card-body">

                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('fail'))
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i> {{ Session::get('fail') }}
                    </div>
                @endif

                <form action="{{ url('/student/ojtEdit', $studentNum ?? ($data->studentNum ?? '')) }}" method="post">
                    @csrf
                    @method('PUT')

                    <!-- Company Information -->
                    <div class="section-title"><i class="fa fa-building"></i> Company Information</div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-building"></i> Company Name</label>
                            <input class="field-input" type="text" name="company_name"
                                value="{{ old('company_name', optional($user)->company_name) }}" placeholder="Enter company name">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-map-marker-alt"></i> Company Address</label>
                            <input class="field-input" type="text" name="company_address"
                                value="{{ old('company_address', optional($user)->company_address) }}" placeholder="Enter company address">
                        </div>

                        <div class="field-group full-width">
                            <label class="field-label"><i class="fa fa-briefcase"></i> Nature of Business</label>
                            <span class="field-hint">e.g. Educational Institution, Government Agency, Telecommunication, Travel Agency, Hotel and Hospitality Service, Food Service, BPOs, NGOs, POS, etc.</span>
                            <input class="field-input" type="text" name="nature_of_bus"
                                value="{{ old('nature_of_bus', optional($user)->nature_of_bus) }}" placeholder="Enter nature of business">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-sitemap"></i> Assigned Department</label>
                            <input class="field-input" type="text" name="assigned_department"
                                value="{{ old('assigned_department', optional($user)->assigned_department) }}" placeholder="e.g. IT Department, QA Division, HR">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-user-tag"></i> Internship Role / Position</label>
                            <input class="field-input" type="text" name="student_role"
                                value="{{ old('student_role', optional($user)->student_role) }}" placeholder="e.g. Web Developer Intern, Data Analyst Intern">
                        </div>
                    </div>

                    <!-- Schedule Information -->
                    <div class="section-title"><i class="fa fa-calendar-alt"></i> Schedule & Duration</div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-check"></i> Start Date</label>
                            <input class="field-input" type="date" name="start_date" value="{{ old('start_date', optional($user)->start_date) }}">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-calendar-times"></i> End Date</label>
                            <input class="field-input" type="date" name="finish_date" value="{{ old('finish_date', optional($user)->finish_date) }}">
                        </div>

                        <div class="field-group full-width">
                            <label class="field-label"><i class="fa fa-clock"></i> Reporting Time</label>
                            <input class="field-input" type="text" name="report_time" id="report_time"
                                value="{{ old('report_time', optional($user)->report_time) }}" placeholder="e.g. 9:00 am - 6:00 pm (Monday - Friday)">
                        </div>
                    </div>

                    <!-- Contact Person -->
                    <div class="section-title"><i class="fa fa-address-card"></i> Contact Person</div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-user-tie"></i> Contact Name</label>
                            <input class="field-input" type="text" name="contact_name"
                                value="{{ old('contact_name', optional($user)->contact_name) }}" placeholder="Enter contact name">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-id-badge"></i> Position of Contact</label>
                            <input class="field-input" type="text" name="contact_position"
                                value="{{ old('contact_position', optional($user)->contact_position) }}" placeholder="Enter contact's position">
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-phone"></i> Contact Number</label>
                            <input class="field-input" type="text" name="contact_number"
                                value="{{ old('contact_number', optional($user)->contact_number) }}" placeholder="Enter contact number">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                        <button type="submit" class="btn-submit">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>

                </form>
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

    <script src="{{ vasset('js/student/ojt-info.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>