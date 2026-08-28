<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Notarized MOA</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/student/companies.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/student_moa-responsive.css') }}">
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
        <a href="{{ url('/student/files') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-download"></i></span>
            <span class="nav-label">Downloadable Files</span>
            <span class="tooltip-label">Downloadable Files</span>
        </a>
        <a href="{{ url('/student/MOA') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
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

        @php
            $studentProfile = \App\Models\Student::where('user_id', $user->id)->first();
            $isInhouseOjt = (bool) ($studentProfile?->is_inhouse_ojt ?? false);
        @endphp

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Notarized <span>MOA</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Notarized MOA</span>
                </div>
            </div>
            @if(empty($isLocked) && !$isInhouseOjt)
                <button class="btn-add-moa" data-bs-toggle="modal" data-bs-target="#addMoaModal">
                    <i class="fa fa-plus-circle"></i> Add Notarized MOA
                </button>
            @else
                @if(isset($unlockRequest) && $unlockRequest->status === 'pending')
                    <button class="btn-add-moa" style="background: linear-gradient(135deg, #ca8a04 0%, #854d0e 100%); cursor: default;" disabled>
                        <i class="fa fa-clock"></i> Unlock Request Pending
                    </button>
                @else
                    <button class="btn-add-moa" style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);" onclick="openUnlockRequestModal('switch_external', true)">
                        <i class="fa fa-key"></i> Request MOA Unlock
                    </button>
                @endif
            @endif
        </div>

        @if($isInhouseOjt)
            <div style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); border-radius: 16px; padding: 22px 26px; margin-bottom: 24px; color: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 4px 20px rgba(4,120,87,0.2);">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; flex-shrink: 0;">
                        <i class="fa fa-university"></i>
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 700; color: #ffffff;">School In-House OJT Mode Active</div>
                        <div style="font-size: 13px; color: #d1fae5; margin-top: 2px;">
                            You are registered for internal campus OJT. External notarized MOA requirement is <strong>waived</strong>, and all requirement submission slots are unlocked!
                        </div>
                    </div>
                </div>
                @if(!empty($unlockRequest) && $unlockRequest->status === 'pending')
                    <button type="button" class="btn" disabled style="background: rgba(253, 230, 138, 0.2); color: #fef08a; border: 1px solid rgba(253, 230, 138, 0.4); padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: not-allowed;">
                        <i class="fa fa-clock me-1"></i> Switch Request Pending
                    </button>
                @else
                    <button type="button" class="btn" onclick="openUnlockRequestModal('switch_external', true)" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-paper-plane me-1"></i> Request Switch to External MOA
                    </button>
                @endif
            </div>
        @elseif(empty($isLocked) && count($companies) === 0)
            <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px; padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                        <i class="fa fa-university"></i>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #0f172a;">Doing OJT inside the School / Campus?</div>
                        <div style="font-size: 12.5px; color: #64748b;">If your OJT is internal within PUP / School, no external MOA is required.</div>
                    </div>
                </div>
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#confirmInhouseLockModal" style="background: #0284c7; color: #fff; padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; box-shadow: 0 2px 8px rgba(2,132,199,0.25);">
                    <i class="fa fa-check-circle me-1"></i> Declare School In-House OJT
                </button>
            </div>
        @endif

        @if(!empty($isLocked))
            <!-- Lock Alert Banner -->
            <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; color: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 20px; flex-shrink: 0;">
                        <i class="fa fa-lock"></i>
                    </div>
                    <div>
                        <div style="font-size: 15px; font-weight: 700; color: #f8fafc;">MOA Selection Locked</div>
                        <div style="font-size: 13px; color: #94a3b8; margin-top: 2px;">
                            Your account is locked to your selected company MOA. Browsing or linking to other companies is disabled.
                        </div>
                    </div>
                </div>
                <div>
                    @if(isset($unlockRequest) && $unlockRequest->status === 'pending')
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.3); color: #facc15; font-size: 13px; font-weight: 600;">
                            <i class="fa fa-clock"></i> Request Pending Review
                        </span>
                    @else
                        <button type="button" class="btn" style="background: #ef4444; color: #fff; font-size: 13px; font-weight: 600; padding: 9px 18px; border-radius: 10px; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;" data-bs-toggle="modal" data-bs-target="#requestUnlockModal">
                            <i class="fa fa-key"></i> Request MOA Reset / Change
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Info Banner -->
        <div class="info-banner">
            <div class="info-banner-icon">
                <i class="fa fa-file-contract"></i>
            </div>
            <div class="info-banner-text">
                <h3>Notarized MOA Submission</h3>
                <p>Upload your company's Memorandum of Agreement here. Ensure the document is properly notarized before submission. You may download or print your MOA details anytime.</p>
            </div>
        </div>

        <!-- MOA Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-building"></i></div>
                    <div>
                        <h2>Notarized MOA Records</h2>
                        <p>All submitted Memoranda of Agreement with partner companies</p>
                    </div>
                </div>
            </div>

            <div class="table-card-body moa-table-wrap">

                @if($companies->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fa fa-file-contract"></i>
                        </div>
                        <h3>No MOA Submitted Yet</h3>
                        <p>Click "Add Notarized MOA" to submit your first company MOA.</p>
                    </div>
                @else
                <table id="moaTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>School Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        @php $isOwner = $company->uploader_name === $user->full_name; @endphp
                        <tr>
                            <td>
                                <div class="company-cell">
                                    <div class="company-avatar">
                                        {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="company-name-text">{{ $company->company_name }}</div>
                                        <div class="company-sub">
                                            {{ $company->company_address }}
                                            @if(!$isOwner)
                                                <span style="display:inline-flex; align-items:center; gap:5px; margin-left:8px; padding:3px 8px; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:11px; font-weight:700;">
                                                    <i class="fa fa-link"></i> Linked
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-phone" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->companyNo }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-envelope" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->company_email }}
                                </div>
                            </td>
                            <td>
                                <span class="year-badge">
                                    <i class="fa fa-calendar-alt"></i>
                                    {{ $company->school_year }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('/moa/download', $company->file) }}" class="btn-action btn-download">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <button type="button"
                                        class="btn-action btn-voucher"
                                        onclick="openVoucherModal('{{ route('voucher', $company->id) }}')">
                                        <i class="fa fa-ticket-alt"></i> Voucher
                                    </button>
                                    <button class="btn-action btn-print"
                                        onclick="openPdfPreview('{{ asset('assets/' . $company->file) }}')">
                                        <i class="fa fa-print"></i> Print PDF
                                    </button>
                                     @php $companyHasEditUnlock = is_callable($hasApprovedEditUnlock) ? $hasApprovedEditUnlock($company->id) : !empty($hasApprovedEditUnlock); @endphp
                                     @if($isOwner && (empty($isLocked) || $companyHasEditUnlock))
                                        <button type="button"
                                            class="btn-action"
                                            style="background:#eff6ff; border-color:#bfdbfe; color:#2563eb;"
                                            data-update-url="{{ route('student.moa.update', $company->id) }}"
                                            data-company-name="{{ e($company->company_name) }}"
                                            data-company-address="{{ e($company->company_address) }}"
                                            data-company-rep="{{ e($company->company_rep) }}"
                                            data-company-no="{{ e($company->companyNo) }}"
                                            data-company-email="{{ e($company->company_email) }}"
                                            data-school-year="{{ e($company->school_year) }}"
                                            data-date-notarized="{{ $company->date_notarized ? \Carbon\Carbon::parse($company->date_notarized)->format('Y-m-d') : '' }}"
                                            data-valid-until="{{ $company->valid_until ? \Carbon\Carbon::parse($company->valid_until)->format('Y-m-d') : '' }}"
                                            data-file-name="{{ e($company->file) }}"
                                            onclick="openEditMoaModal(this)">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                    @endif
                                    @if(!empty($isLocked))
                                        @if(!empty($unlockRequest) && $unlockRequest->status === 'pending')
                                            <button type="button" class="btn-action" disabled style="border:1.5px solid #fcd34d; color:#b45309; background:#fffbeb; cursor:not-allowed; opacity:0.9;" title="Your unlock request is pending coordinator approval.">
                                                <i class="fa fa-clock me-1"></i> Unlock Request Pending
                                            </button>
                                        @else
                                            <button type="button" class="btn-action" style="border:1.5px solid #fecaca; color:#dc2626; background:#fff;"
                                                onclick="openUnlockRequestModal('{{ $isOwner ? 'edit' : 'unlink' }}', {{ $isOwner ? 'true' : 'false' }})">
                                                <i class="fa fa-key me-1"></i> {{ $isOwner ? 'Request Edit / Remove' : 'Request Unlink' }}
                                            </button>
                                             <script>
                                                 function openUnlockRequestModal(type, isOwner) {
                                                     const select = document.getElementById('modalRequestType');
                                                     if (select) {
                                                         const editOpt = select.querySelector('option[value="edit"]');
                                                         const unlinkOpt = select.querySelector('option[value="unlink"]');
                                                         const switchOpt = select.querySelector('option[value="switch_external"]');

                                                         if (type === 'switch_external') {
                                                             if (editOpt) editOpt.style.display = 'none';
                                                             if (unlinkOpt) unlinkOpt.style.display = 'none';
                                                             if (switchOpt) switchOpt.style.display = 'block';
                                                             select.value = 'switch_external';
                                                         } else if (isOwner === false) {
                                                             if (editOpt) editOpt.style.display = 'none';
                                                             if (unlinkOpt) unlinkOpt.style.display = 'block';
                                                             if (switchOpt) switchOpt.style.display = 'none';
                                                             select.value = 'unlink';
                                                         } else {
                                                             if (editOpt) editOpt.style.display = 'block';
                                                             if (unlinkOpt) unlinkOpt.style.display = 'block';
                                                             if (switchOpt) switchOpt.style.display = 'none';
                                                             select.value = type || 'edit';
                                                         }
                                                     }
                                                     const modal = new bootstrap.Modal(document.getElementById('requestUnlockModal'));
                                                     modal.show();
                                                 }
                                             </script>
                                        @endif
                                    @else
                                        <button type="button" class="btn-action" style="border:1.5px solid #fecaca; color:#dc2626; background:#fff;"
                                            onclick="confirmStudentRemove({{ $company->id }}, '{{ addslashes($company->company_name) }}', {{ $isOwner ? 'true' : 'false' }})">
                                            <i class="fa fa-trash"></i> {{ $isOwner ? 'Remove' : 'Unlink' }}
                                        </button>
                                        <form id="student-remove-form-{{ $company->id }}" action="{{ route('student.moa.remove', $company->id) }}" method="POST" style="display:none;">
                                            @csrf
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#moaTable').DataTable({
                            scrollX: true,
                            autoWidth: false,
                            order: [[0, 'asc']]
                        });
                    });
                </script>
                @endif

            </div>
        </div>

    </div>

    <!-- Footer -->
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

@php
    $schoolYearBase = now()->year;
    $schoolYearOptions = range($schoolYearBase - 5, $schoolYearBase + 5);
    $selectedCreateStartYear = old('school_year_start', $schoolYearBase);
    $selectedCreateEndYear = old('school_year_end', $selectedCreateStartYear + 1);
@endphp

<!-- =============== ADD MOA MODAL =============== -->
<div class="modal fade" id="addMoaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-file-contract"></i> Submit Notarized MOA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="linkExistingMoaForm" action="{{ route('student.moa.link') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="company_id" id="linkExistingMoaCompanyId">
            </form>

            <form id="studentMoaForm" action="{{ url('/companyCreate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div style="margin-bottom: 20px; padding: 16px; border: 1px solid #fde2e2; border-radius: 14px; background: linear-gradient(180deg, #fffefe 0%, #fff7f7 100%);">
                        <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom: 12px;">
                            <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:var(--red); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa fa-link"></i>
                            </div>
                            <div>
                                <div style="font-size:16px; font-weight:800; color:#111827;">Use Existing MOA First</div>
                                <div style="font-size:12.5px; color:#6b7280; line-height:1.6;">
                                    Search the company name below. If the notarized MOA is already in the system, you can link it to your account instead of uploading a duplicate file.
                                </div>
                            </div>
                        </div>

                        <input type="text" id="existingMoaSearch" class="modal-field-input" placeholder="Search company name...">

                        <div id="existingMoaList" style="margin-top: 12px; max-height: 220px; overflow-y: auto; display: grid; gap: 10px;">
                            @forelse ($availableLinkableCompanies as $linkableCompany)
                                <div class="existing-moa-item" data-company-name="{{ strtolower($linkableCompany->company_name) }}">
                                    <div style="display:flex; justify-content:space-between; gap:14px; align-items:center; padding:14px; border:1px solid #f1d5d5; border-radius:12px; background:#fff;">
                                        <div style="min-width:0;">
                                            <div style="font-size:14px; font-weight:800; color:#111827;">{{ $linkableCompany->company_name }}</div>
                                            <div style="font-size:12px; color:#6b7280; margin-top:4px;">{{ $linkableCompany->company_address }}</div>
                                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;">
                                                <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:999px; background:#fef3c7; color:#92400e; font-size:11px; font-weight:700;">
                                                    <i class="fa fa-calendar-alt"></i> {{ $linkableCompany->school_year }}
                                                </span>
                                                @if(!empty($linkableCompany->course))
                                                    <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:700;">
                                                        <i class="fa fa-graduation-cap"></i> {{ $linkableCompany->course }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="display:flex; flex-direction:column; gap:8px; align-items:stretch; min-width: 150px;">
                                            <button type="button"
                                                class="btn-modal-close view-btn"
                                                data-url="{{ asset('assets/' . $linkableCompany->file) }}"
                                                style="justify-content:center; padding-inline: 16px; white-space: nowrap;">
                                                <i class="fa fa-eye me-1"></i> View MOA
                                            </button>
                                            <button type="button"
                                                class="btn-modal-submit existing-moa-link-btn"
                                                data-company-id="{{ $linkableCompany->id }}"
                                                style="justify-content:center; padding-inline: 16px; white-space: nowrap;">
                                                <i class="fa fa-link me-1"></i> Use This MOA
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div id="existingMoaEmptyState" style="padding:14px; border:1px dashed #f3b3b3; border-radius:12px; color:#6b7280; font-size:12.5px; background:#fff;">
                                    No existing MOA is available to link right now. You can continue with a new upload below.
                                </div>
                            @endforelse
                        </div>

                        @if ($availableLinkableCompanies->isNotEmpty())
                            <div id="existingMoaNoResults" style="display:none; margin-top:12px; padding:14px; border:1px dashed #f3b3b3; border-radius:12px; color:#6b7280; font-size:12.5px; background:#fff;">
                                No matching company found. You can continue with a new upload below.
                            </div>
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; gap:10px; margin: 0 0 18px;">
                        <div style="flex:1; height:1px; background:#ececec;"></div>
                        <span style="font-size:11px; font-weight:800; color:#9ca3af; letter-spacing:0.12em;">OR UPLOAD A NEW MOA</span>
                        <div style="flex:1; height:1px; background:#ececec;"></div>
                    </div>

                    <!-- Two-column grid -->
                    <div class="moa-form-grid">

                        <!-- LEFT COLUMN (4 fields) -->
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-building"></i> Company Name
                            </label>
                            <input class="modal-field-input" type="text" name="company_name"
                                placeholder="e.g. Acme Corporation" required>

                            <label class="modal-field-label">
                                <i class="fa fa-map-marker-alt"></i> Company Address
                            </label>
                            <input class="modal-field-input" type="text" name="company_address"
                                placeholder="e.g. 123 Main St, Manila" required>

                            <label class="modal-field-label">
                                <i class="fa fa-user-tie"></i> Company Representative
                            </label>
                            <input class="modal-field-input" type="text" name="company_rep"
                                placeholder="e.g. Juan dela Cruz" required>

                            <label class="modal-field-label">
                                <i class="fa fa-phone"></i> Company Number
                            </label>
                            <input class="modal-field-input" type="text" name="companyNo"
                                placeholder="e.g. 09XX-XXX-XXXX or N/A">
                        </div>

                        <!-- RIGHT COLUMN (3 fields) -->
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-envelope"></i> Company Email
                            </label>
                            <input class="modal-field-input" type="text" name="company_email"
                                placeholder="e.g. info@company.com" required>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                                <span><i class="fa fa-calendar-alt"></i> School Year</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the current school year, example: <strong>2025-2026</strong>.
                                </span>
                            </label>
                            <div class="school-year-row">
                                <select name="school_year_start" id="schoolYearStart" class="modal-field-input" required>
                                    @foreach ($schoolYearOptions as $year)
                                        <option value="{{ $year }}" {{ (string) $selectedCreateStartYear === (string) $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="sep">–</span>
                                <select name="school_year_end" id="schoolYearEnd" class="modal-field-input" required>
                                    <option value="{{ $selectedCreateEndYear }}" selected>{{ $selectedCreateEndYear }}</option>
                                </select>
                            </div>

                             <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; margin-top: 14px;">
                                <span><i class="fa fa-calendar-check"></i> Date Notarized</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the date when the MOA was notarized.
                                </span>
                            </label>
                            <input class="modal-field-input" type="date" name="date_notarized">

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; margin-top: 14px;">
                                <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the MOA expiry date.
                                </span>
                            </label>
                            <input class="modal-field-input" type="date" name="valid_until" required>

                            <!-- Info notice card -->
                            <div style="
                                background: #fff5f5;
                                border: 1px solid #fecaca;
                                border-left: 3px solid var(--red);
                                border-radius: 10px;
                                padding: 12px 14px;
                                margin-top: 6px;
                            ">
                                <div style="font-size: 12px; font-weight: 700; color: var(--red); margin-bottom: 5px;">
                                    <i class="fa fa-info-circle"></i> Reminder
                                </div>
                                <div style="font-size: 11.5px; color: #777; line-height: 1.6;">
                                    Ensure your MOA is properly <strong>notarized</strong> before submitting.
                                    Accepted format: <strong>PDF only</strong>.
                                    Max file size: <strong>30 MB</strong>.
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- DIVIDER -->
                    <div style="height: 1px; background: #f0f0f0; margin: 20px 0;"></div>

                    <!-- MOA DOCUMENT — full width below -->
                    <label class="modal-field-label">
                        <i class="fa fa-paperclip"></i> MOA Document
                    </label>
                    <div class="file-upload-zone" id="moaDropZone">
                        <input type="file" name="file" id="moaFileInput" data-max-size-mb="30" accept=".pdf,application/pdf" required>
                        <i class="fa fa-cloud-upload-alt upload-icon"></i>
                        <p id="moaFileLabel">Click or drag your notarized MOA file here</p>
                        <span>Supported: PDF only | Max file size: 30 MB</span>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-paper-plane me-1"></i> Submit MOA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== EDIT MOA MODAL =============== -->
<div class="modal fade" id="editMoaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-edit"></i> Edit Notarized MOA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editMoaForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="moa-form-grid">
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-building"></i> Company Name
                            </label>
                            <input class="modal-field-input" type="text" name="company_name" id="editCompanyName" required>

                            <label class="modal-field-label">
                                <i class="fa fa-map-marker-alt"></i> Company Address
                            </label>
                            <input class="modal-field-input" type="text" name="company_address" id="editCompanyAddress" required>

                            <label class="modal-field-label">
                                <i class="fa fa-user-tie"></i> Company Representative
                            </label>
                            <input class="modal-field-input" type="text" name="company_rep" id="editCompanyRep" required>

                            <label class="modal-field-label">
                                <i class="fa fa-phone"></i> Company Number
                            </label>
                            <input class="modal-field-input" type="text" name="companyNo" id="editCompanyNo" placeholder="e.g. 09XX-XXX-XXXX or N/A">
                        </div>

                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-envelope"></i> Company Email
                            </label>
                            <input class="modal-field-input" type="text" name="company_email" id="editCompanyEmail" required>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                                <span><i class="fa fa-calendar-alt"></i> School Year</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the current school year, example: <strong>2025-2026</strong>.
                                </span>
                            </label>
                            <div class="school-year-row">
                                <select name="school_year_start" id="editSchoolYearStart" class="modal-field-input" required>
                                    @foreach ($schoolYearOptions as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <span class="sep">-</span>
                                <select name="school_year_end" id="editSchoolYearEnd" class="modal-field-input" required></select>
                            </div>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; margin-top: 14px;">
                                <span><i class="fa fa-calendar-check"></i> Date Notarized</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the date when the MOA was notarized.
                                </span>
                            </label>
                            <input class="modal-field-input" type="date" name="date_notarized" id="editDateNotarized">

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; margin-top: 14px;">
                                <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the MOA expiry date.
                                </span>
                            </label>
                            <input class="modal-field-input" type="date" name="valid_until" id="editValidUntil" required>

                            <div style="
                                background: #eff6ff;
                                border: 1px solid #bfdbfe;
                                border-left: 3px solid #2563eb;
                                border-radius: 10px;
                                padding: 12px 14px;
                                margin-top: 6px;
                            ">
                                <div style="font-size: 12px; font-weight: 700; color: #2563eb; margin-bottom: 5px;">
                                    <i class="fa fa-info-circle"></i> Optional PDF Replacement
                                </div>
                                <div style="font-size: 11.5px; color: #555; line-height: 1.6;" id="editMoaCurrentFile">
                                    Leave the file empty if you only need to update the company details.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="height: 1px; background: #f0f0f0; margin: 20px 0;"></div>

                    <label class="modal-field-label">
                        <i class="fa fa-paperclip"></i> Replace MOA Document
                    </label>
                    <div class="file-upload-zone" id="editMoaDropZone">
                        <input type="file" name="file" id="editMoaFileInput" data-max-size-mb="30" accept=".pdf,application/pdf">
                        <i class="fa fa-cloud-upload-alt upload-icon"></i>
                        <p id="editMoaFileLabel">Leave empty to keep the current notarized MOA PDF</p>
                        <span>Supported: PDF only | Max file size: 30 MB</span>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== VIEW / PRINT MODAL =============== -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-eye"></i> Document Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 0; height: 75vh;">
                <iframe id="viewIframe" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn-modal-submit" onclick="printRegularPreview()">
                    <i class="fa fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== VOUCHER MODAL =============== -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%); color:#fff; padding:14px 18px; border-bottom:none;">
                <h5 class="modal-title" style="font-size:15px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px; margin:0;">
                    <i class="fa fa-ticket-alt"></i> Voucher Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:brightness(0) invert(1); opacity:0.85;"></button>
            </div>
            <div class="modal-body" style="padding: 0; height: 75vh; min-height: 400px; background:#f8fafc;">
                <iframe id="voucherIframe" style="width:100%; height:100%; border:none; display:block;"></iframe>
            </div>
            <div class="modal-footer" style="padding:10px 16px; background:#fff; border-top:1px solid #f0f0f0;">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    const SIDEBAR_COLLAPSED_KEY = 'internconnect_sidebar_collapsed';
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    // Restore persisted desktop sidebar state
    if (localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === 'true' && window.innerWidth > 900) {
        if (sidebar) sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
        document.documentElement.classList.add('sidebar-is-collapsed');
    }

    function closeMobileSidebar() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('active');
    }

    function openMobileSidebar() {
        if (sidebar) sidebar.classList.add('mobile-open');
        if (overlay) overlay.classList.add('active');
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function (event) {
            const isMobile = window.innerWidth <= 900;

            if (isMobile) {
                event.stopPropagation();

                if (sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem(SIDEBAR_COLLAPSED_KEY, isCollapsed ? 'true' : 'false');
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                } else {
                    document.documentElement.classList.remove('sidebar-is-collapsed');
                }
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    document.addEventListener('click', function (event) {
        if (window.innerWidth > 900 || !sidebar.classList.contains('mobile-open')) {
            return;
        }

        if (sidebar.contains(event.target) || menuToggle.contains(event.target)) {
            return;
        }

        closeMobileSidebar();
    });

// ✅ ADD THIS (IMPORTANT FIX)
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileSidebar();
        }
    });

    function confirmStudentRemove(companyId, companyName, isOwner) {
        const title = isOwner ? 'Remove MOA?' : 'Unlink MOA?';
        const html = isOwner
            ? 'This will remove your notarized MOA record for <strong>' + companyName + '</strong>.'
            : 'This will unlink <strong>' + companyName + '</strong> from your account.';
        const confirmText = isOwner ? 'Yes, remove it' : 'Yes, unlink it';

        Swal.fire({
            title: title,
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('student-remove-form-' + companyId).submit();
            }
        });
    }

    // PDF preview / print modal
    function openPdfPreview(url) {
        document.getElementById('viewIframe').src = url;
        new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    function openVoucherModal(url) {
        document.getElementById('voucherIframe').src = url;
        new bootstrap.Modal(document.getElementById('voucherModal')).show();
    }

    function syncSchoolYearEnd(startId, endId, selectedEndYear = null) {
        const startSelect = document.getElementById(startId);
        const endSelect = document.getElementById(endId);

        if (!startSelect || !endSelect || !startSelect.value) {
            return;
        }

        const startYear = parseInt(startSelect.value, 10);

        if (Number.isNaN(startYear)) {
            return;
        }

        const endYear = selectedEndYear ? parseInt(selectedEndYear, 10) : startYear + 1;
        endSelect.innerHTML = '';

        const option = document.createElement('option');
        option.value = String(endYear);
        option.textContent = String(endYear);
        option.selected = true;
        endSelect.appendChild(option);
        endSelect.value = String(endYear);
    }

    function openEditMoaModal(button) {
        const form = document.getElementById('editMoaForm');
        const schoolYear = (button.dataset.schoolYear || '').split('-');
        const currentFile = button.dataset.fileName || '';

        form.action = button.dataset.updateUrl;
        document.getElementById('editCompanyName').value = button.dataset.companyName || '';
        document.getElementById('editCompanyAddress').value = button.dataset.companyAddress || '';
        document.getElementById('editCompanyRep').value = button.dataset.companyRep || '';
        document.getElementById('editCompanyNo').value = button.dataset.companyNo || '';
        document.getElementById('editCompanyEmail').value = button.dataset.companyEmail || '';
        document.getElementById('editSchoolYearStart').value = schoolYear[0] || '';
        syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd', schoolYear[1] || '');
        document.getElementById('editDateNotarized').value = button.dataset.dateNotarized || '';
        document.getElementById('editValidUntil').value = button.dataset.validUntil || '';
        document.getElementById('editMoaFileInput').value = '';
        document.getElementById('editMoaFileLabel').textContent = 'Leave empty to keep the current notarized MOA PDF';
        document.getElementById('editMoaCurrentFile').textContent = currentFile
            ? 'Current file: ' + currentFile + '. Leave the file empty if you only need to update the company details.'
            : 'Leave the file empty if you only need to update the company details.';

        new bootstrap.Modal(document.getElementById('editMoaModal')).show();
    }

    function printRegularPreview() {
        document.getElementById('viewIframe').contentWindow.print();
    }

    function bindPdfInputValidation(inputId, labelId, emptyLabel) {
        const input = document.getElementById(inputId);
        if (!input) {
            return;
        }

        input.addEventListener('change', function () {
            const label = document.getElementById(labelId);
            const file = this.files.length > 0 ? this.files[0] : null;

            if (file && !file.name.toLowerCase().endsWith('.pdf')) {
                this.value = '';
                label.textContent = emptyLabel;
                Swal.fire({
                    icon: 'error',
                    title: 'PDF only',
                    text: 'Please upload the notarized MOA as a PDF file.',
                    confirmButtonColor: '#d32f2f',
                });
                return;
            }

            label.textContent = file ? file.name : emptyLabel;
        });
    }

    // Form validation
    $(document).ready(function () {
        function validateForm($form) {
            let valid = true;
            $form.find('input[required]').each(function () {
                const errorId = $(this).attr('name') + '-error';
                if ($(this).val() === '') {
                    valid = false;
                    $('#' + errorId).show();
                } else {
                    $('#' + errorId).hide();
                }
            });
            return valid;
        }

        ['#studentMoaForm', '#editMoaForm'].forEach(function (selector) {
            $(selector).on('submit', function (e) {
                if (!validateForm($(this))) {
                    e.preventDefault();
                    return;
                }

                if (this.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }

                this.dataset.submitting = 'true';

                const submitButton = this.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
                }
            });
        });

        bindPdfInputValidation('moaFileInput', 'moaFileLabel', 'Click or drag your notarized MOA file here');
        bindPdfInputValidation('editMoaFileInput', 'editMoaFileLabel', 'Leave empty to keep the current notarized MOA PDF');

        syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd', @json($selectedCreateEndYear));
        syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd');

        $('#schoolYearStart').on('change', function () {
            syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd');
        });

        $('#editSchoolYearStart').on('change', function () {
            syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd');
        });

        const existingMoaSearch = document.getElementById('existingMoaSearch');
        if (existingMoaSearch) {
            existingMoaSearch.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const items = Array.from(document.querySelectorAll('.existing-moa-item'));
                let visibleCount = 0;

                items.forEach(function (item) {
                    const companyName = item.dataset.companyName || '';
                    const matches = companyName.includes(query);
                    item.style.display = matches ? '' : 'none';
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                const noResults = document.getElementById('existingMoaNoResults');
                if (noResults) {
                    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        }

        let pendingFormType = null;
        let pendingCompanyId = null;

        document.querySelectorAll('.existing-moa-link-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const companyIdInput = document.getElementById('linkExistingMoaCompanyId');
                const linkForm = document.getElementById('linkExistingMoaForm');
                if (!companyIdInput || !linkForm) return;

                pendingCompanyId = this.dataset.companyId || '';
                companyIdInput.value = pendingCompanyId;

                const item = this.closest('.existing-moa-item');
                const companyName = item ? (item.querySelector('[style*="font-weight:800"]')?.innerText || 'this company') : 'this company';

                document.getElementById('confirmCompanyNameText').innerText = companyName;
                pendingFormType = 'link';

                // Close addMoaModal if open
                const addModalEl = document.getElementById('addMoaModal');
                if (addModalEl) {
                    const addModalInst = bootstrap.Modal.getInstance(addModalEl);
                    if (addModalInst) addModalInst.hide();
                }

                const confirmModal = new bootstrap.Modal(document.getElementById('confirmMoaLockModal'));
                confirmModal.show();
            });
        });

        const studentMoaForm = document.getElementById('studentMoaForm');
        if (studentMoaForm) {
            studentMoaForm.addEventListener('submit', function (e) {
                if (window.moaLockConfirmed) return;

                e.preventDefault();
                const companyNameInput = this.querySelector('input[name="company_name"]');
                const compName = companyNameInput ? companyNameInput.value.trim() : 'this company';

                document.getElementById('confirmCompanyNameText').innerText = compName || 'this company';
                pendingFormType = 'create';

                const addModalEl = document.getElementById('addMoaModal');
                if (addModalEl) {
                    const addModalInst = bootstrap.Modal.getInstance(addModalEl);
                    if (addModalInst) addModalInst.hide();
                }

                const confirmModal = new bootstrap.Modal(document.getElementById('confirmMoaLockModal'));
                confirmModal.show();
            });
        }

        const btnConfirmLockSubmit = document.getElementById('btnConfirmLockSubmit');
        if (btnConfirmLockSubmit) {
            btnConfirmLockSubmit.addEventListener('click', function () {
                this.disabled = true;
                this.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Processing...';

                if (pendingFormType === 'link') {
                    const linkForm = document.getElementById('linkExistingMoaForm');
                    if (linkForm) linkForm.submit();
                } else if (pendingFormType === 'create') {
                    window.moaLockConfirmed = true;
                    if (studentMoaForm) studentMoaForm.submit();
                }
            });
        }
    });

    document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-btn');
    if (btn) {
        const url = btn.getAttribute('data-url');
        openPdfPreview(url);
    }
});

@if(session('showVoucherModal'))
    window.addEventListener('load', function () {
        openVoucherModal(@json(session('showVoucherModal')));
    });
@endif
</script>

<!-- =============== CONFIRM MOA LOCK MODAL =============== -->
<div class="modal fade" id="confirmMoaLockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #fff5f5; border-bottom: 1px solid #fee2e2;">
                <h5 class="modal-title" style="color: var(--red); font-weight: 700; display: flex; align-items: center; gap: 8px; font-size: 16px;">
                    <i class="fa fa-exclamation-triangle"></i> Confirm MOA Lock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 14px;">
                    Are you sure you want to proceed with <strong id="confirmCompanyNameText" style="color:#111827;">this company</strong>?
                </p>
                <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 14px; font-size: 13px; color: #9f1239; line-height: 1.5;">
                    <i class="fa fa-lock me-1"></i> <strong>Important:</strong> Once confirmed, your selection will be <strong>locked</strong>. You will not be able to browse or select other company MOAs without prior approval from your Internship Coordinator.
                </div>
            </div>
            <div class="modal-footer" style="background: #fafafa;">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-modal-submit" id="btnConfirmLockSubmit">
                    <i class="fa fa-lock me-1"></i> Yes, Confirm & Lock Selection
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== REQUEST UNLOCK MODAL =============== -->
<div class="modal fade" id="requestUnlockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 12px 36px rgba(0,0,0,0.18);">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 18px 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        <i class="fa fa-key"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" style="color: #0f172a; font-weight: 700; font-size: 16px; margin: 0;">
                            Request MOA Unlock
                        </h5>
                        <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">Coordinator approval required</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.moa.requestUnlock') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding: 24px;">
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 18px; line-height: 1.5; background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                        <i class="fa fa-info-circle me-1" style="color:#2563eb;"></i> Select your purpose and provide a clear explanation for your OJT Coordinator.
                    </p>

                    <label class="modal-field-label">
                        <i class="fa fa-tasks"></i> Request Purpose <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="request_type" id="modalRequestType" class="modal-field-input" required>
                        <option value="edit">Edit MOA Details / Replace File</option>
                        <option value="unlink">Remove / Unlink MOA</option>
                        <option value="switch_external">Switch from In-House OJT to External MOA</option>
                    </select>

                    <label class="modal-field-label">
                        <i class="fa fa-comment-alt"></i> Reason for Request <span style="color:#ef4444;">*</span>
                    </label>
                    <textarea name="reason" rows="4" class="modal-field-input" style="width:100%; font-family: inherit; resize: vertical;" placeholder="e.g. Need to update company address / replacement PDF file..." required minlength="5"></textarea>
                </div>
                <div class="modal-footer" style="background: #fafafa; padding: 14px 24px;">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                        <i class="fa fa-paper-plane me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== CONFIRM IN-HOUSE OJT LOCK MODAL =============== -->
<div class="modal fade" id="confirmInhouseLockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #e0f2fe; border-bottom: 1px solid #bae6fd;">
                <h5 class="modal-title" style="color: #0369a1; font-weight: 700; display: flex; align-items: center; gap: 8px; font-size: 16px;">
                    <i class="fa fa-university"></i> Confirm School In-House OJT
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.moa.toggleInhouse') }}" method="POST">
                @csrf
                <input type="hidden" name="is_inhouse" value="1">
                <div class="modal-body" style="padding: 24px;">
                    <p style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 14px;">
                        Are you sure you want to register for <strong>School In-House OJT</strong>?
                    </p>
                    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 14px; font-size: 13px; color: #9f1239; line-height: 1.5;">
                        <i class="fa fa-lock me-1"></i> <strong>Important Disclaimer:</strong> Once confirmed, your selection will be <strong>locked</strong> to School In-House OJT mode. You will not be able to upload or link to an external company MOA without prior unlock approval from your Internship Coordinator.
                    </div>
                </div>
                <div class="modal-footer" style="background: #fafafa;">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <i class="fa fa-lock me-1"></i> Yes, Confirm & Lock Selection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/upload-size-guard.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>

    <script src="{{ vasset('js/student/companies.js') }}"></script>
</body>
</html>