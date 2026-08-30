<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Professors</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ vasset('css/coordinator/professors.css') }}?v={{ time() }}">
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
        <a href="{{ url('/professorTab') }}" class="nav-item active">
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
        <a href="{{ route('auditlog') }}" class="nav-item">
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
                <h1>OJT <span>Professors</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Professors</span>
                </div>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <button type="button" data-bs-toggle="modal" data-bs-target="#transferCoordinatorModal" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);">
                    <i class="fa fa-user-shield"></i>
                    <span>Transfer Coordinator Role</span>
                </button>
                <button type="button" id="btnSyncFlss" class="btn-sync-flss" style="background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                    <i class="fa fa-sync-alt" id="flssSyncIcon"></i>
                    <span>Sync Faculty from FLSS</span>
                </button>
                <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
                    <i class="fa fa-plus"></i> Add New Professor
                </button>
            </div>
        </div>

        <!-- Stats Row -->
        @php 
            $totalProfs = count($data); 
            $syncedCount = 0;
            foreach ($data as $prof) {
                $uRec = $usersP->where('email', $prof->email)->first();
                $eLower = strtolower(trim($prof->email ?? ''));
                $nLower = strtolower(trim($prof->full_name ?? ''));
                if (($uRec && !empty($uRec->idp_user_id)) || str_ends_with($eLower, '@pup.edu.ph') || in_array($eLower, $flssEmails ?? []) || in_array($nLower, $flssNames ?? [])) {
                    $syncedCount++;
                }
            }
        @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-chalkboard-teacher"></i></div>
                <div>
                    <div class="stat-num">{{ $totalProfs }}</div>
                    <div class="stat-name">Total Professors</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa fa-sync-alt"></i></div>
                <div>
                    <div class="stat-num">{{ $syncedCount }}</div>
                    <div class="stat-name">FLSS Synced</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-user-tag"></i></div>
                <div>
                    <div class="stat-num">{{ $totalProfs - $syncedCount }}</div>
                    <div class="stat-name">Manually Added</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-user-check"></i></div>
                <div>
                    <div class="stat-num">Active</div>
                    <div class="stat-name">Status</div>
                </div>
            </div>
        </div>

        <!-- Professors Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                    <div>
                        <h2>Professor List</h2>
                        <p>Manage all OJT professors and faculty accounts</p>
                    </div>
                </div>
                <div class="prof-count-badge">
                    <i class="fa fa-chalkboard-teacher"></i>
                    {{ $totalProfs }} {{ $totalProfs == 1 ? 'professor' : 'professors' }}
                </div>
            </div>

            <div class="table-card-body">
                <table id="profTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Professor Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Account Source</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $professor)
                        @php
                            $userRec = $usersP->where('email', $professor->email)->first();
                            $isCoordinator = ($userRec && (int)$userRec->role === 1);
                            $roleLabel = $isCoordinator ? 'OJT Coordinator' : 'Professor';
                            $roleStyle = $isCoordinator 
                                ? 'background: #6b21a8 !important; color: #ffffff !important; font-weight: 600 !important; font-size: 11.5px; padding: 5px 10px; border-radius: 6px; display: inline-block;' 
                                : 'background: #0284c7 !important; color: #ffffff !important; font-weight: 600 !important; font-size: 11.5px; padding: 5px 10px; border-radius: 6px; display: inline-block;';

                            $emailLower = strtolower(trim($professor->email ?? ''));
                            $nameLower = strtolower(trim($professor->full_name ?? ''));

                            $isFlssSynced = ($userRec && !empty($userRec->idp_user_id))
                                || str_ends_with($emailLower, '@pup.edu.ph')
                                || in_array($emailLower, $flssEmails ?? [])
                                || in_array($nameLower, $flssNames ?? []);

                            $sourceLabel = $isFlssSynced ? 'FLSS / IdP Synced' : 'Manually Added';
                            $sourceStyle = $isFlssSynced 
                                ? 'background: #475569 !important; color: #ffffff !important; font-weight: 600 !important; font-size: 11.5px; padding: 5px 10px; border-radius: 6px; display: inline-block;' 
                                : 'background: #2563eb !important; color: #ffffff !important; font-weight: 600 !important; font-size: 11.5px; padding: 5px 10px; border-radius: 6px; display: inline-block;';
                        @endphp
                        <tr>
                            <!-- Name -->
                            <td>
                                <div class="prof-cell">
                                    <div class="prof-avatar">
                                        {{ strtoupper(substr($professor->full_name, 0, 1)) }}
                                    </div>
                                    <span class="prof-name-text">{{ $professor->full_name }}</span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <i class="fa fa-envelope" style="color:var(--red); font-size:11px;"></i>
                                    {{ $professor->email }}
                                </div>
                            </td>

                            <!-- Role -->
                            <td>
                                <span class="badge" style="{{ $roleStyle }}">{{ $roleLabel }}</span>
                            </td>

                            <!-- Source -->
                            <td>
                                <span class="badge" style="{{ $sourceStyle }}">{{ $sourceLabel }}</span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">
                                    <button class="btn-edit btnView1"
                                        data-professor-id="{{ $professor->id }}"
                                        data-email="{{ $professor->email }}"
                                        data-first-name="{{ $userRec->first_name ?? '' }}"
                                        data-middle-name="{{ $userRec->middle_name ?? '' }}"
                                        data-last-name="{{ $userRec->last_name ?? '' }}"
                                        data-full-name="{{ $professor->full_name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editProfessorModal">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>

                                    <button class="btn-remove btnRemove"
                                        data-professor-id="{{ $professor->id }}">
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

<!-- =============== ADD PROFESSOR MODAL =============== -->
<div class="modal fade" id="addProfessorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-plus"></i> Add New Professor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/professorCreate') }}" method="post" enctype="multipart/form-data" id="addProfessorForm" data-email-check-url="{{ route('check-email-availability') }}">
                @csrf
                <div class="modal-body">
                    @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('subject_code') || $errors->has('subject_description') || $errors->has('password') || $errors->has('password_confirmation'))
                        <div class="modal-alert-errors">
                            <ul>
                                @foreach (['first_name', 'last_name', 'email', 'subject_code', 'subject_description', 'password', 'password_confirmation'] as $field)
                                    @error($field)
                                        <li>{{ $message }}</li>
                                    @enderror
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="modal-section"><i class="fa fa-user"></i> Basic Information</div>

                    <div class="field-row">
                        <div class="field-group has-bubble">
                            <label class="field-label"><i class="fa fa-user"></i> First Name</label>
                            <div class="field-input-wrap">
                                <div class="field-bubble" id="profFirstNameBubble"></div>
                                <input class="field-input" type="text" id="prof_first_name" name="first_name" placeholder="First name" value="{{ old('first_name') }}" required autocapitalize="words" spellcheck="false">
                            </div>
                        </div>
                        <div class="field-group has-bubble">
                            <label class="field-label"><i class="fa fa-user"></i> Last Name</label>
                            <div class="field-input-wrap">
                                <div class="field-bubble" id="profLastNameBubble"></div>
                                <input class="field-input" type="text" id="prof_last_name" name="last_name" placeholder="Last name" value="{{ old('last_name') }}" required autocapitalize="words" spellcheck="false">
                            </div>
                        </div>
                    </div>

                    <div class="field-group has-bubble">
                        <label class="field-label"><i class="fa fa-envelope"></i> Email Address</label>
                        <div class="field-input-wrap">
                            <div class="field-bubble" id="profEmailBubble"></div>
                            <input class="field-input" type="email" id="prof_email" name="email" placeholder="Enter email address" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-lock"></i> Set Password</div>
                    <div class="field-group has-bubble">
                        <label class="field-label"><i class="fa fa-lock"></i> Password</label>
                            <div class="field-input-wrap has-toggle">
                                <div class="field-bubble" id="profPasswordBubble"></div>
                                <input class="field-input" type="password" id="prof_password" name="password" placeholder="Enter password" required minlength="8">
                                <button type="button" class="field-toggle" id="toggleProfPassword" aria-label="Show password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    <div class="field-group has-bubble">
                        <label class="field-label"><i class="fa fa-check-circle"></i> Confirm Password</label>
                        <div class="field-input-wrap has-toggle">
                            <div class="field-bubble" id="profConfirmPasswordBubble"></div>
                            <input class="field-input" type="password" id="prof_password_confirmation" name="password_confirmation" placeholder="Re-enter password" required minlength="8">
                            <button type="button" class="field-toggle" id="toggleProfPasswordConfirmation" aria-label="Show confirm password">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field-group">
                        <small style="color:#666;display:block;margin-top:4px;">
                            The coordinator sets the password. Please share it securely with the professor. Use at least 8 characters with uppercase, lowercase, a number, and one symbol: ! @ # $ % ^ & *.
                        </small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Add Professor
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<!-- =============== EDIT PROFESSOR MODAL =============== -->
<div class="modal fade" id="editProfessorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-edit"></i> Edit Professor Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/updateProfessor') }}" method="post" id="editProfessorForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editProfessorId" name="professor_id">

                <div class="modal-body">
                    <!-- Personal Info Section -->
                    <div class="modal-section"><i class="fa fa-user"></i> Personal Information</div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label"><i class="fa fa-user"></i> First Name <span style="color:var(--red);">*</span></label>
                                <input class="field-input" type="text" name="first_name" id="editFirstName" placeholder="e.g. Juan" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label"><i class="fa fa-user"></i> Middle Name</label>
                                <input class="field-input" type="text" name="middle_name" id="editMiddleName" placeholder="e.g. Santos (optional)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="field-group">
                                <label class="field-label"><i class="fa fa-user"></i> Last Name <span style="color:var(--red);">*</span></label>
                                <input class="field-input" type="text" name="last_name" id="editLastName" placeholder="e.g. Dela Cruz" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details Section -->
                    <div class="modal-section mt-3"><i class="fa fa-envelope"></i> Contact Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-envelope"></i> Email Address <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="email" name="email" id="editEmail" placeholder="Enter email address" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
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
    <footer class="dashboard-footer">
    <div class="footer-left">
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

<!-- Scripts -->
<!-- Transfer Coordinator Designation Modal -->
<div class="modal fade" id="transferCoordinatorModal" tabindex="-1" aria-labelledby="transferCoordinatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border-top-left-radius: 14px; border-top-right-radius: 14px; padding: 18px 24px;">
                <h5 class="modal-title font-weight-bold" id="transferCoordinatorModalLabel" style="font-size: 16px;">
                    <i class="fa fa-user-shield me-2"></i> Transfer OJT Coordinator Designation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="font-size: 13.5px; color: #555; line-height: 1.5; margin-bottom: 18px;">
                    Select an active professor from the faculty list below to transfer the <strong>OJT Coordinator designation</strong> to them.
                </p>
                <div class="alert alert-warning d-flex align-items-center" style="font-size: 12.5px; border-radius: 8px;">
                    <i class="fa fa-exclamation-triangle me-2" style="font-size: 16px; color: #d97706;"></i>
                    <div>
                        <strong>Important:</strong> Upon transfer, your account will revert to a standard Professor account, and the selected faculty member will become the active OJT Coordinator.
                    </div>
                </div>
                <form id="transferCoordinatorForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-semibold" style="font-size: 13px;">Select New Coordinator</label>
                        <select name="target_user_id" id="targetUserId" class="form-select" required style="border-radius: 8px; font-size: 13.5px;">
                            <option value="">-- Choose Active Faculty Member --</option>
                            @foreach($usersP as $uProf)
                                @if($uProf->id !== session('loginId'))
                                    <option value="{{ $uProf->id }}">{{ $uProf->full_name }} ({{ $uProf->email }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">Cancel</button>
                        <button type="submit" class="btn btn-purple" id="btnConfirmTransfer" style="background: #7c3aed; color: white; border-radius: 8px; font-weight: 600;">
                            Confirm Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Prune Missing Faculty Confirmation Modal -->
<div class="modal fade" id="pruneMissingFacultyModal" tabindex="-1" aria-labelledby="pruneMissingFacultyModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.3); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 18px 24px;">
                <h5 class="modal-title font-weight-bold" id="pruneMissingFacultyModalLabel" style="font-size: 17px; margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-minus"></i> Review Missing Faculty Accounts
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #ffffff;">
                <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" style="font-size: 13.5px; border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle mt-1 text-warning" style="font-size: 18px;"></i>
                    <div>
                        <strong>Notice:</strong> The following faculty member(s) exist in InternConnect but were <strong>not found in the latest FLSS data</strong>. Select which accounts should be removed or kept.
                    </div>
                </div>

                <!-- Search Bar & Controls -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                    <div class="input-group" style="max-width: 380px;">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="pruneFacultySearchInput" class="form-control border-start-0 bg-light" placeholder="Search missing faculty name or email..." style="font-size: 13.5px;">
                    </div>
                    <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                        <input class="form-check-input ms-0" type="checkbox" id="selectAllPruneAccounts" checked style="width: 38px; height: 20px; cursor: pointer;">
                        <label class="form-check-input-label font-weight-bold" for="selectAllPruneAccounts" style="font-size: 13px; cursor: pointer; user-select: none;">
                            Select / Deselect All
                        </label>
                    </div>
                </div>

                <!-- Missing Accounts Table -->
                <div class="table-responsive" style="max-height: 340px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <table class="table table-hover align-middle mb-0" id="pruneFacultyTable">
                        <thead class="table-light sticky-top" style="z-index: 5;">
                            <tr style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                                <th style="width: 44px;" class="text-center">Select</th>
                                <th>Faculty Name & Email</th>
                                <th>Role</th>
                                <th>Account Source</th>
                                <th>Active Assignments</th>
                            </tr>
                        </thead>
                        <tbody id="pruneFacultyTableBody" style="font-size: 13.5px;">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
                <div class="text-muted mt-2 d-flex justify-content-between" style="font-size: 12px;">
                    <span id="pruneSelectionCountText">Selected 0 of 0 accounts for removal</span>
                    <span class="text-secondary"><i class="fas fa-info-circle me-1"></i> Unchecked accounts will stay in InternConnect</span>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">
                    Skip Removal (Keep All)
                </button>
                <button type="button" class="btn btn-danger btn-sm px-4 font-weight-bold" id="btnConfirmPruneFaculty">
                    <i class="fas fa-trash-alt me-1"></i> Confirm & Remove Selected (<span id="pruneSelectedCountBadge">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.professorsConfig = {
        hasModalErrors: @json($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('subject_code') || $errors->has('subject_description') || $errors->has('password') || $errors->has('password_confirmation')),
        syncFacultyUrl: @json(route('coordinator.syncFaculty')),
        transferRoleUrl: @json(route('coordinator.transferRole')),
        professorHomeUrl: @json(route('professor_home')),
        pruneMissingFacultyUrl: @json(route('coordinator.pruneMissingFaculty')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/coordinator/professors.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
