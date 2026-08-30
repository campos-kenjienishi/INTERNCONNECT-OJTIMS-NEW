<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InternConnect - Account Information</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
    <script>
        (function(){
            try {
                if (localStorage.getItem('internconnect_sidebar_collapsed') === 'true' && window.innerWidth > 900) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e){}
        })();
    </script>
    <link rel="stylesheet" href="{{ vasset('css/student/account.css') }}">
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
        <div class="user-avatar">
            <i class="fa fa-user"></i>
        </div>
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
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1>Account <span>Information</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Account Information</span>
                </div>
            </div>
            <div>
                <button type="button" id="btnSyncGuidance" class="btn-sync-guidance" style="background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                    <i class="fa fa-sync-alt" id="syncIcon"></i>
                    <span>Sync from Guidance System (GuiSIS)</span>
                </button>
            </div>
        </div>

        <div class="account-layout">

            <!-- ===== LEFT: Profile Card ===== -->
            <div class="profile-card">
                <div class="profile-card-top">
                    <div class="profile-pic-wrap">
                        <!-- Profile picture display -->
                        <div class="profile-pic" id="profilePicDisplay">
                            <i class="fa fa-user" id="profilePicIcon"></i>
                        </div>
                    </div>

                    <div class="profile-name">{{ $data->full_name }}</div>
                    <div class="profile-role">Student</div>
                </div>

                <div class="profile-card-body">
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-id-card"></i></div>
                        <div>
                            <div class="profile-info-label">Student No.</div>
                            <div class="profile-info-value">{{ $data->studentNum ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-graduation-cap"></i></div>
                        <div>
                            <div class="profile-info-label">Course</div>
                            <div class="profile-info-value">{{ $data->course ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-users"></i></div>
                        <div>
                            <div class="profile-info-label">Year & Section</div>
                            <div class="profile-info-value">{{ $data->year_and_section ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-calendar-alt"></i></div>
                        <div>
                            <div class="profile-info-label">School Year</div>
                            <div class="profile-info-value">{{ !empty($data->school_year_start) && !empty($data->school_year_end) ? $data->school_year_start . ' - ' . $data->school_year_end : '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-envelope"></i></div>
                        <div>
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value" style="word-break:break-all;">{{ $data->email ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <div class="profile-info-label">Contact No.</div>
                            <div class="profile-info-value">{{ $data->contact_number ?? '—' }}</div>
                        </div>
                    </div>

                    <button class="btn-change-pass" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fa fa-lock"></i> Change Password
                    </button>
                </div>
            </div>

            <!-- ===== RIGHT: Edit Form ===== -->
            <div>

                <!-- Account Details Form -->
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="header-icon"><i class="fa fa-user-edit"></i></div>
                        <div>
                            <h2>Personal Details</h2>
                            <p>Update your personal and academic information below</p>
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

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i>
                                <ul style="margin:0; padding-left:16px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ url('/student/edit', $data->email) }}" method="post" id="studentProfileForm" data-email-check-url="{{ route('check-email-availability') }}" data-current-user-id="{{ $data->id }}">
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="section-title"><i class="fa fa-user"></i> Name</div>
                            <div class="form-grid">
                                <div class="field-group has-bubble">
                                    <label class="field-label"><i class="fa fa-user"></i> First Name</label>
                                    <div class="field-input-wrap">
                                        <div class="field-bubble" id="firstNameBubble"></div>
                                        <input class="field-input" type="text" id="first_name" name="first_name" value="{{ $data->first_name }}" placeholder="First name" autocapitalize="words" spellcheck="false">
                                    </div>
                                </div>
                                <div class="field-group has-bubble">
                                    <label class="field-label"><i class="fa fa-user"></i> Middle Name</label>
                                    <div class="field-input-wrap">
                                        <div class="field-bubble" id="middleNameBubble"></div>
                                        <input class="field-input" type="text" id="middle_name" name="middle_name" value="{{ $data->middle_name }}" placeholder="Middle name" autocapitalize="words" spellcheck="false">
                                    </div>
                                </div>
                                <div class="field-group has-bubble">
                                    <label class="field-label"><i class="fa fa-user"></i> Last Name</label>
                                    <div class="field-input-wrap">
                                        <div class="field-bubble" id="lastNameBubble"></div>
                                        <input class="field-input" type="text" id="last_name" name="last_name" value="{{ $data->last_name }}" placeholder="Last name" autocapitalize="words" spellcheck="false">
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-user-tag"></i> Suffix</label>
                                    <input class="field-input" type="text" name="suffix" value="{{ $data->suffix }}" placeholder="e.g. Jr., III">
                                </div>
                            </div>

                            <!-- Contact -->
                            <div class="section-title"><i class="fa fa-address-card"></i> Contact & Identity</div>
                            <div class="form-grid">
                                <div class="field-group full-width">
                                    <label class="field-label"><i class="fa fa-map-marker-alt"></i> Address</label>
                                    <input class="field-input" type="text" name="address" value="{{ $data->address }}" placeholder="Complete address">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-phone"></i> Contact No.</label>
                                    <input class="field-input" type="text" name="contact_number" value="{{ $data->contact_number }}" placeholder="Contact number">
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-birthday-cake"></i> Date of Birth</label>
                                    <input class="field-input" type="date" name="date_of_birth" value="{{ $data->date_of_birth }}">
                                </div>
                                <div class="field-group has-bubble">
                                    <label class="field-label"><i class="fa fa-envelope"></i> E-mail</label>
                                    <div class="field-input-wrap">
                                        <div class="field-bubble" id="emailBubble"></div>
                                        <input class="field-input" type="email" id="email" name="email" value="{{ $data->email }}" placeholder="Email address">
                                    </div>
                                </div>
                                <div class="field-group has-bubble">
                                    <label class="field-label"><i class="fa fa-id-card"></i> Student No.</label>
                                    <div class="field-input-wrap">
                                        <div class="field-bubble" id="studentNumBubble"></div>
                                        <input class="field-input" type="text" id="studentNum" name="studentNum" value="{{ $data->studentNum }}" placeholder="Enter student number" maxlength="16" spellcheck="false">
                                    </div>
                                </div>
                            </div>

                            <!-- Academic -->
                            <div class="section-title"><i class="fa fa-graduation-cap"></i> Academic Information</div>
                            <div class="form-grid">
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-university"></i> Course</label>
                                    <select name="course" class="field-select">
                                        @foreach ($course as $courseI)
                                            <option value="{{ $courseI->course }}"
                                                {{ $courseI->course == $data->course ? 'selected' : '' }}>
                                                {{ $courseI->course }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field-group has-bubble">
                                    <label class="field-label"><i class="fa fa-users"></i> Year and Section</label>
                                    <div class="field-input-wrap">
                                        <div class="field-bubble" id="yearSectionBubble"></div>
                                        <input class="field-input" type="text" id="year_and_section" name="year_and_section" value="{{ $data->year_and_section }}" placeholder="eg. 4-1" maxlength="10" spellcheck="false">
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                                    <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:center;">
                                        <select name="academic_year_start" id="academic_year_start" class="field-select" required>
                                            <option value="">Start Year</option>
                                            @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                                <option value="{{ $year }}" {{ (string) ($data->school_year_start ?? '') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                        <span style="color:#888;font-weight:700;">-</span>
                                        <select name="academic_year_end" id="academic_year_end" class="field-select" required>
                                            <option value="">End Year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn-update">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

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

<!-- =============== CHANGE PASSWORD MODAL =============== -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-lock"></i> Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/change_password', $data->id) }}" method="post" data-verify-current-url="{{ url('/change_password/verify-current', $data->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    @if ($errors->any())
                        <div class="alert alert-danger" style="border-radius:10px; font-size:13px; padding:12px 16px; background:rgba(220,38,38,0.08); color:#dc2626; border:none;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <label class="modal-field-label"><i class="fa fa-lock"></i> Current Password</label>
                    <div class="pw-input-wrap has-bubble">
                        <div class="pw-bubble" id="currentPasswordBubble"></div>
                        <input type="password" id="current_password" name="current_password" placeholder="Enter current password">
                        <i class="fa fa-eye pw-toggle" id="toggleCurrent"></i>
                    </div>
                    <span class="text-error">@error('current_password') {{ $message }} @enderror</span>

                    <label class="modal-field-label"><i class="fa fa-key"></i> New Password</label>
                    <div class="pw-input-wrap has-bubble">
                        <div class="pw-bubble" id="newPasswordBubble"></div>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                        <i class="fa fa-eye pw-toggle" id="toggleNew"></i>
                    </div>
                    <span class="text-error">@error('new_password') {{ $message }} @enderror</span>

                    <label class="modal-field-label"><i class="fa fa-check-circle"></i> Confirm New Password</label>
                    <div class="pw-input-wrap has-bubble">
                        <div class="pw-bubble" id="confirmPasswordBubble"></div>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                        <i class="fa fa-eye pw-toggle" id="toggleConfirm"></i>
                    </div>
                    <span class="text-error">@error('confirm_password') {{ $message }} @enderror</span>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit" id="updatePasswordButton" disabled>
                        <i class="fa fa-save me-1"></i> Update Password
                    </button>
                </div>

            </form>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
    window.studentAccountConfig = {
        initialEndYear: @json((string) ($data->school_year_end ?? '')),
        syncGuidanceUrl: @json(route('student.syncGuidance'))
    };
</script>
    <script src="{{ vasset('js/student/account.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>