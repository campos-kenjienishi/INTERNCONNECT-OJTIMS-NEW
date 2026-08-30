<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Account Information</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

    <link rel="stylesheet" href="{{ vasset('css/coordinator/accountinfo.css') }}?v={{ time() }}">
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
            @if(isset($data->profile_photo) && $data->profile_photo)
                <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
            @else
                <i class="fa fa-user-tie"></i>
            @endif
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
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
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
        <a href="{{ url('/auditlog') }}" class="nav-item">
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

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle">
                <i class="fa fa-moon"></i>
            </button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-badge">
            <i class="fa fa-user-shield"></i> OJT Coordinator
        </div>
    </div>

    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Account <span>Information</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Account Info</span>
                </div>
            </div>
        </div>

        <!-- Account Grid -->
        <div class="account-grid">

            <!-- ===== LEFT: Profile Card ===== -->
            <div class="profile-card">
                <div class="profile-card-banner">
                    <div class="profile-big-avatar">
                        @if(isset($data->profile_photo) && $data->profile_photo)
                            <img src="{{ asset('storage/' . $data->profile_photo) }}" alt="Profile">
                        @else
                            {{ strtoupper(substr($data->first_name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="profile-full-name">{{ $data->full_name }}</div>
                    <div class="profile-role-badge">
                        <i class="fa fa-user-shield" style="font-size:10px;"></i>
                        OJT Coordinator
                    </div>
                </div>

                <div class="profile-card-body">

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-envelope"></i></div>
                        <div>
                            <div class="profile-info-label">Email Address</div>
                            <div class="profile-info-value">{{ $data->email }}</div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <div class="profile-info-label">Contact Number</div>
                            <div class="profile-info-value">{{ $data->contact_number ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-birthday-cake"></i></div>
                        <div>
                            <div class="profile-info-label">Date of Birth</div>
                            <div class="profile-info-value">
                                {{ $data->date_of_birth ? \Carbon\Carbon::parse($data->date_of_birth)->format('F d, Y') : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div>
                            <div class="profile-info-label">Address</div>
                            <div class="profile-info-value">{{ $data->address ?: '—' }}</div>
                        </div>
                    </div>

                    <button class="btn-change-password"
                            data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fa fa-lock"></i> Change Password
                    </button>

                </div>
            </div>

            <!-- ===== RIGHT: Edit Form ===== -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-header-icon"><i class="fa fa-user-edit"></i></div>
                    <div>
                        <h2>Edit Personal Details</h2>
                        <p>Update your account information below</p>
                    </div>
                </div>

                <form action="{{ url('/edit', $data->email) }}" method="post" id="coordinatorProfileForm" data-email-check-url="{{ route('check-email-availability') }}" data-current-user-id="{{ $data->id }}">
                    @csrf
                    @method('PUT')

                    <div class="form-card-body">

                        <div class="form-section">
                            <i class="fa fa-id-card"></i> Name Details
                        </div>

                        <div class="fields-grid-4">
                            <div class="field-group has-bubble">
                                <label class="field-label"><i class="fa fa-user"></i> First Name</label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="firstNameBubble"></div>
                                    <input class="field-input" type="text" id="first_name" name="first_name"
                                           value="{{ $data->first_name }}" placeholder="First name" autocapitalize="words" spellcheck="false">
                                </div>
                            </div>
                            <div class="field-group has-bubble">
                                <label class="field-label"><i class="fa fa-user"></i> Middle Name</label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="middleNameBubble"></div>
                                    <input class="field-input" type="text" id="middle_name" name="middle_name"
                                           value="{{ $data->middle_name }}" placeholder="Middle name" autocapitalize="words" spellcheck="false">
                                </div>
                            </div>
                            <div class="field-group has-bubble">
                                <label class="field-label"><i class="fa fa-user"></i> Last Name</label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="lastNameBubble"></div>
                                    <input class="field-input" type="text" id="last_name" name="last_name"
                                           value="{{ $data->last_name }}" placeholder="Last name" autocapitalize="words" spellcheck="false">
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="field-label"><i class="fa fa-tag"></i> Suffix</label>
                                <input class="field-input" type="text" name="suffix"
                                       value="{{ $data->suffix }}" placeholder="e.g. Jr., III">
                            </div>
                        </div>

                        <div class="form-section" style="margin-top:22px;">
                            <i class="fa fa-address-card"></i> Contact & Personal Details
                        </div>

                        <div class="fields-grid-3">
                            <div class="field-group">
                                <label class="field-label"><i class="fa fa-phone"></i> Contact Number</label>
                                <input class="field-input" type="text" name="contact_number"
                                       value="{{ $data->contact_number }}" placeholder="09XX-XXX-XXXX">
                            </div>
                            <div class="field-group">
                                <label class="field-label"><i class="fa fa-birthday-cake"></i> Date of Birth</label>
                                <input class="field-input" type="date" name="date_of_birth"
                                       value="{{ $data->date_of_birth }}">
                            </div>
                            <div class="field-group has-bubble">
                                <label class="field-label"><i class="fa fa-envelope"></i> Email Address</label>
                                <div class="field-input-wrap">
                                    <div class="field-bubble" id="emailBubble"></div>
                                    <input class="field-input" type="email" id="email" name="email"
                                           value="{{ $data->email }}" placeholder="email@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="form-section" style="margin-top:22px;">
                            <i class="fa fa-map-marker-alt"></i> Address
                        </div>

                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-home"></i> Full Address</label>
                            <input class="field-input" type="text" name="address"
                                   value="{{ $data->address }}" placeholder="Street, Barangay, City, Province">
                        </div>

                    </div>

                    <div class="form-card-footer">
                        <button type="submit" class="btn-update">
                            <i class="fa fa-save"></i> Update Information
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
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

<!-- =============== CHANGE PASSWORD MODAL =============== -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-lock"></i> Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/change_password', $data->id) }}" method="post" data-verify-current-url="{{ url('/change_password/verify-current', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    @if ($errors->any())
                    <div class="alert-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Current Password -->
                    <div class="pw-field-group">
                        <label class="pw-field-label"><i class="fa fa-lock"></i> Current Password</label>
                        <div class="pw-input-wrap has-bubble">
                            <div class="pw-bubble" id="currentPasswordBubble"></div>
                            <input type="password" class="pw-input" id="current_password"
                                   name="current_password" placeholder="Enter current password">
                            <button type="button" class="pw-toggle" id="toggleCurrent">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="pw-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="pw-field-group">
                        <label class="pw-field-label"><i class="fa fa-key"></i> New Password</label>
                        <div class="pw-input-wrap has-bubble">
                            <div class="pw-bubble" id="newPasswordBubble"></div>
                            <input type="password" class="pw-input" id="new_password"
                                   name="new_password" placeholder="Enter new password">
                            <button type="button" class="pw-toggle" id="toggleNew">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <div class="pw-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="pw-field-group">
                        <label class="pw-field-label"><i class="fa fa-check-circle"></i> Confirm New Password</label>
                        <div class="pw-input-wrap has-bubble">
                            <div class="pw-bubble" id="confirmPasswordBubble"></div>
                            <input type="password" class="pw-input" id="confirm_password"
                                   name="confirm_password" placeholder="Re-enter new password">
                            <button type="button" class="pw-toggle" id="toggleConfirm">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        @error('confirm_password')
                            <div class="pw-error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    window.accountInfoConfig = {
        hasPasswordErrors: @json($errors->has('current_password') || $errors->has('new_password') || $errors->has('confirm_password')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/coordinator/accountinfo.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
