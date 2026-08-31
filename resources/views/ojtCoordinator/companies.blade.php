<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ vasset('css/coordinator/companies.css') }}?v={{ time() }}">
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
        <a href="{{ url('/MOA') }}" class="nav-item active">
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
                <h1>Memorandum of <span>Agreement</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>MOA</span>
                </div>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('coordinator.moa.unlockRequests') }}" class="btn-add btn-unlock-requests" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff !important; text-decoration: none; box-shadow: 0 4px 16px rgba(245, 158, 11, 0.28);">
                    <i class="fa fa-key"></i> Unlock Requests
                </a>
                <button class="btn-add btn-add-company" data-bs-toggle="modal" data-bs-target="#addCompanyModal" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #fff !important; box-shadow: 0 4px 16px rgba(22, 163, 74, 0.28);">
                    <i class="fa fa-plus"></i> Add New Company
                </button>
            </div>
        </div>

        <!-- Stats Row -->
        @php
            $totalCompanies = count($companies);
            $activeMoaCount = collect($companies)->filter(function ($company) {
                try {
                    $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;
                } catch (\Throwable $e) {
                    $validUntil = null;
                }

                return $validUntil && now()->lte($validUntil);
            })->count();
        @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red"><i class="fa fa-building"></i></div>
                <div>
                    <div class="stat-num">{{ $totalCompanies }}</div>
                    <div class="stat-name">Partner Companies</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <div>
                    <div class="stat-num">{{ $activeMoaCount }}</div>
                    <div class="stat-name">Active MOAs</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-users"></i></div>
                <div>
                    <div class="stat-num">{{ collect($companies)->sum(fn($c) => $c->students->count()) }}</div>
                    <div class="stat-name">Assigned Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa fa-file-contract"></i></div>
                <div>
                    <div class="stat-num">MOA</div>
                    <div class="stat-name">Document Type</div>
                </div>
            </div>
        </div>

        <!-- Companies Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-file-contract"></i></div>
                    <div>
                        <h2>Companies</h2>
                        <p>All partner companies with MOA agreements</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <form action="{{ url('/MOA') }}" method="GET" style="display:flex; align-items:center; gap:8px;">
                        <select name="school_year" class="field-select" style="min-width:170px; height:36px; font-size:12px;">
                            <option value="">All School Years</option>
                            @foreach ($schoolYears as $schoolYear)
                                <option value="{{ $schoolYear }}" {{ ($selectedSchoolYear ?? '') === $schoolYear ? 'selected' : '' }}>
                                    {{ $schoolYear }}
                                </option>
                            @endforeach
                        </select>
                        <select name="course" class="field-select" style="min-width:220px; height:36px; font-size:12px;">
                            <option value="">All Courses</option>
                            @foreach ($course as $courseItem)
                                <option value="{{ $courseItem->course }}" {{ ($selectedCourse ?? '') === $courseItem->course ? 'selected' : '' }}>
                                    {{ $courseItem->course }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-modal-submit" style="height:36px; padding:0 14px; font-size:12px;">
                            Filter
                        </button>
                        @if (!empty($selectedCourse) || !empty($selectedSchoolYear))
                            <a href="{{ url('/MOA') }}" class="btn-modal-close" style="height:36px; padding:0 14px; font-size:12px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                                Clear
                            </a>
                        @endif
                    </form>
                    <div class="count-badge">
                        <i class="fa fa-building"></i>
                        {{ $totalCompanies }} {{ $totalCompanies == 1 ? 'company' : 'companies' }}
                    </div>
                </div>
            </div>

            <div class="table-card-body">
                <table id="companyTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="display:none;">ID</th>
                            <th>Company Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>School Year</th>
                            <th>Course</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        @php
                            $displayStudents = collect(array_filter(array_map('trim', explode(',', (string) ($company->student_names_display ?? '')))));
                            $linkedStudentNames = $company->students->pluck('full_name')->filter()->values();
                            $manualStudentNames = $displayStudents->diff($linkedStudentNames)->values();
                            [$schoolYearStart, $schoolYearEnd] = array_pad(explode('-', (string) ($company->school_year ?? '')), 2, '');
                            $schoolYearStart = trim((string) $schoolYearStart);
                            $schoolYearEnd = trim((string) $schoolYearEnd);
                            if ($schoolYearStart !== '' && $schoolYearEnd !== '' && (int) $schoolYearEnd < (int) $schoolYearStart) {
                                [$schoolYearStart, $schoolYearEnd] = [$schoolYearEnd, $schoolYearStart];
                            }
                            $companyCourses = collect(preg_split('/[\r\n,;|\/]+/', trim((string) $company->course), -1, PREG_SPLIT_NO_EMPTY))
                                ->map(fn ($course) => trim((string) $course))
                                ->filter()
                                ->values();

                            try {
                                $dateNotarized = $company->date_notarized ? \Carbon\Carbon::parse($company->date_notarized) : null;
                            } catch (\Throwable $e) {
                                $dateNotarized = null;
                            }

                            try {
                                $validUntil = $company->valid_until ? \Carbon\Carbon::parse($company->valid_until) : null;
                            } catch (\Throwable $e) {
                                $validUntil = null;
                            }

                            $companyEditPayload = [
                                'company_name' => $company->company_name,
                                'company_address' => $company->company_address,
                                'company_rep' => $company->company_rep,
                                'company_no' => $company->companyNo,
                                'company_email' => $company->company_email,
                                'school_year_start' => $schoolYearStart,
                                'school_year_end' => $schoolYearEnd,
                                'school_year' => trim((string) $schoolYearStart) && trim((string) $schoolYearEnd)
                                    ? $schoolYearStart . '-' . $schoolYearEnd
                                    : ($company->school_year ?? ''),
                                'date_notarized' => $dateNotarized ? $dateNotarized->format('Y-m-d') : '',
                                'valid_until' => $validUntil ? $validUntil->format('Y-m-d') : '',
                                'course_values' => $companyCourses->values(),
                                'selected_students' => $linkedStudentNames->values(),
                                'manual_students' => $manualStudentNames->values(),
                            ];

                            $isActive = $validUntil && now()->lte($validUntil);
                        @endphp
                        <tr>
                            <td style="display:none;">{{ $company->id }}</td>

                            <!-- Company Name -->
                            <td>
                                <div class="company-cell">
                                    <div class="company-icon-box">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <span class="company-name-text">{{ $company->company_name }}</span>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-phone" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->companyNo ?: '—' }}
                                </div>
                            </td>

                            <!-- Email -->
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-envelope" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->company_email ?: '—' }}
                                </div>
                            </td>

                            <!-- School Year -->
                            <td>
                                <span style="font-size:13px; color:#555;">
                                    {{ trim((string) $schoolYearStart) && trim((string) $schoolYearEnd) ? $schoolYearStart . '-' . $schoolYearEnd : ($company->school_year ?? '—') }}
                                </span>
                            </td>

                            <!-- Course -->
                            <td>
                                @php
                                    $companyCourses = collect(preg_split('/[\r\n,;|\/]+/', trim((string) $company->course), -1, PREG_SPLIT_NO_EMPTY))
                                        ->map(fn ($course) => trim((string) $course))
                                        ->filter()
                                        ->values();
                                    $courseAcronymLookup = collect($course)->mapWithKeys(function ($courseItem) {
                                        return [trim((string) $courseItem->course) => trim((string) ($courseItem->acronym ?? ''))];
                                    });
                                @endphp

                                @if ($companyCourses->isNotEmpty())
                                    <div style="display:flex; flex-wrap:wrap; gap:4px; max-width:180px;" title="{{ $companyCourses->implode(', ') }}">
                                        @foreach ($companyCourses as $companyCourse)
                                            @php
                                                $courseAcronym = trim((string) ($courseAcronymLookup[$companyCourse] ?? ''));
                                                if ($courseAcronym === '') {
                                                    $courseAcronym = collect(preg_split('/\s+/', trim($companyCourse), -1, PREG_SPLIT_NO_EMPTY))
                                                        ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                                                        ->implode('');
                                                }
                                            @endphp
                                            <span class="course-pill" title="{{ $companyCourse }}">{{ $courseAcronym ?: $companyCourse }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size:13px; color:#555;">&mdash;</span>
                                @endif
                            </td>

                            <!-- Students -->
                            <td>
                                @if ($displayStudents->isNotEmpty())
                                    @foreach ($displayStudents as $displayStudent)
                                        <span class="student-pill">{{ $displayStudent }}</span>
                                    @endforeach
                                @else
                                    @forelse ($company->students as $student)
                                        <span class="student-pill">{{ $student->full_name }}</span>
                                    @empty
                                        <span style="color:#aaa; font-size:12px;">&mdash;</span>
                                    @endforelse
                                @endif
                            </td>

                            <!-- Status -->
                            <td>
                                @if ($isActive)
                                    <span class="status-active">
                                        <i class="fa fa-circle" style="font-size:7px;"></i> Active
                                    </span>
                                @else
                                    <span class="status-expired">
                                        <i class="fa fa-times-circle" style="font-size:11px;"></i> Expired
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="actions-wrap">

                                    <!-- View -->
                                    <a class="btn-action-icon btn-view"
                                       href="{{ route('moa.view', ['companyId' => $company->id]) }}"
                                       title="View MOA"
                                       aria-label="View MOA">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                    <button type="button"
                                        class="btn-action-icon btn-edit btn-open-edit"
                                        title="Edit MOA"
                                        aria-label="Edit MOA"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCompanyModal"
                                        data-company-id="{{ $company->id }}"
                                        data-company-name="{{ $company->company_name }}"
                                        data-company-address="{{ $company->company_address }}"
                                        data-company-rep="{{ $company->company_rep }}"
                                        data-company-no="{{ $company->companyNo }}"
                                        data-company-email="{{ $company->company_email }}"
                                        data-school-year="{{ trim((string) $schoolYearStart) && trim((string) $schoolYearEnd) ? $schoolYearStart . '-' . $schoolYearEnd : ($company->school_year ?? '') }}"
                                        data-school-year-raw="{{ e($company->school_year ?? '') }}"
                                        data-school-year-start="{{ $schoolYearStart }}"
                                        data-school-year-end="{{ $schoolYearEnd }}"
                                        data-school-year-normalized="{{ trim((string) $schoolYearStart) && trim((string) $schoolYearEnd) ? $schoolYearStart . '-' . $schoolYearEnd : ($company->school_year ?? '') }}"
                                        data-date-notarized="{{ $dateNotarized ? $dateNotarized->format('Y-m-d') : '' }}"
                                        data-valid-until="{{ $validUntil ? $validUntil->format('Y-m-d') : '' }}"
                                        data-course-raw="{{ e($company->course ?? '') }}"
                                        data-course-values='@json($companyCourses->values())'
                                        data-selected-students-raw="{{ e($linkedStudentNames->implode(', ')) }}"
                                        data-selected-students='@json($linkedStudentNames->values())'
                                        data-manual-students-raw="{{ e($manualStudentNames->implode(', ')) }}"
                                        data-manual-students='@json($manualStudentNames->values())'
                                        data-edit-payload='@json($companyEditPayload)'
                                        onclick="openEditCompanyModal(this)">
                                        <i class="fa fa-pen"></i>
                                    </button>

                                    <!-- Download -->
                                    <a class="btn-action-icon btn-download"
                                       href="{{ url('/moa/download', $company->file) }}"
                                       title="Download MOA"
                                       aria-label="Download MOA">
                                        <i class="fa fa-download"></i>
                                    </a>

                                    <!-- Send -->
                                    <button class="btn-action-icon btn-send btn-open-send"
                                        data-file-id="{{ $company->id }}"
                                        data-company-name="{{ $company->company_name }}"
                                        title="Send MOA"
                                        aria-label="Send MOA"
                                        data-bs-toggle="modal"
                                        data-bs-target="#sendModal">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>

                                    <!-- Print -->
                                    @if($company->file)
                                        <button class="btn-action-icon btn-print"
                                            title="Print MOA"
                                            aria-label="Print MOA"
                                            onclick="printUploadedMoa('{{ asset('assets/' . $company->file) }}')">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    @else
                                        <button class="btn-action-icon btn-print" disabled title="No MOA file available">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    @endif

                                    <!-- Remove -->
                                    <button type="button" class="btn-action-icon btn-remove"
                                        title="Remove MOA"
                                        aria-label="Remove MOA"
                                        onclick="confirmRemove({{ $company->id }}, '{{ addslashes($company->company_name) }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                    <form id="remove-form-{{ $company->id }}" action="{{ url('/moa/remove/' . $company->id) }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>

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

@php
    $selectedCreateStudentNames = collect(old('student_names', []))->filter()->values();
    $schoolYearBase = now()->year;
    $schoolYearOptions = range($schoolYearBase - 5, $schoolYearBase + 5);
    $selectedCreateStartYear = old('school_year_start', $schoolYearBase);
    $selectedCreateEndYear = old('school_year_end', $selectedCreateStartYear + 1);
@endphp

<!-- =============== ADD COMPANY MODAL =============== -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-building"></i> Add New Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/companyCreate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="modal-section"><i class="fa fa-building"></i> Company Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-building"></i> Company Name <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="text" name="company_name" placeholder="e.g. Acme Corporation" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-map-marker-alt"></i> Company Address <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="text" name="company_address" placeholder="Full address" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-user-tie"></i> Company Representative <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="text" name="company_rep" placeholder="Representative name" required>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-phone"></i> Contact Number <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input class="field-input" type="text" name="companyNo" placeholder="e.g. 09XX-XXX-XXXX">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-envelope"></i> Email Address <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input class="field-input" type="email" name="company_email" placeholder="company@email.com">
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-calendar-alt"></i> Academic Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year <span style="color:var(--red);">*</span></label>
                        <div class="year-row">
                            <select class="field-input" name="school_year_start" id="schoolYearStart" required>
                                @foreach ($schoolYearOptions as $year)
                                    <option value="{{ $year }}" {{ (string) $selectedCreateStartYear === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                            <span>&ndash;</span>
                            <select class="field-input" name="school_year_end" id="schoolYearEnd" required>
                                <option value="{{ $selectedCreateEndYear }}" selected>{{ $selectedCreateEndYear }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                            <span><i class="fa fa-calendar-check"></i> Date Notarized</span>
                            <span style="font-size: 11.5px; color: #777; font-weight: 400;">Select the date when the MOA was notarized.</span>
                        </label>
                        <input class="field-input" type="date" name="date_notarized">
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                            <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                            <span style="font-size: 11.5px; color: #777; font-weight: 400;">Select the MOA expiry date.</span>
                        </label>
                        <input class="field-input" type="date" name="valid_until" required>
                    </div>

                   <div class="field-group">
                        <label class="field-label">
                            <i class="fa fa-graduation-cap"></i> Course
                            <span style="color:var(--red);">*</span>
                        </label>

                        <div class="course-picker-shell">
                            <input
                                type="text"
                                id="moaCourseSearch"
                                class="course-picker-search"
                                placeholder="Search course acronym or name..."
                            >

                            <div id="moaCourseSelect" class="course-picker-scroll course-checkbox-group">
                                @foreach ($course as $courseItem)
                                    <label class="course-option" data-course-name="{{ strtolower($courseItem->course) }}" data-course-acronym="{{ strtolower($courseItem->acronym ?? '') }}">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="course[]"
                                            id="course{{ $courseItem->id }}"
                                            value="{{ $courseItem->course }}">
                                        <span class="course-option-content">
                                            <span class="course-option-acronym">{{ $courseItem->acronym ?: $courseItem->course }}</span>
                                            <span class="course-option-name">{{ $courseItem->course }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div style="font-size:11.5px; color:#777;">
                            Select one or more courses.
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-paperclip"></i> MOA Document</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-file-pdf"></i> Attach MOA File <span style="color:var(--red);">*</span></label>
                        <input class="field-input" type="file" name="file" data-max-size-mb="30" required style="padding:8px 13px;">
                        <div style="margin-top:6px; font-size:12px; color:#777;">
                            PDF only | Max file size: 30 MB
                        </div>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                    <div class="modal-section"><i class="fa fa-users"></i> Assign Students</div>

                    <div class="field-group" style="margin-top:10px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                            <div>
                                <label class="field-label" style="margin-bottom:4px;"><i class="fa fa-user-graduate"></i> Assigned Students</label>
                                <div id="moaAssignedStudentsSummary" style="font-size:12px; color:#666;">{{ $selectedCreateStudentNames->isEmpty() ? 'No students assigned yet.' : $selectedCreateStudentNames->count() . ' students selected.' }}</div>
                            </div>
                            <button type="button" class="btn-modal-submit" id="openAssignStudentsModal" style="padding:10px 14px; white-space:nowrap;">
                                <i class="fa fa-user-plus me-1"></i> Assign
                            </button>
                        </div>
                        <div id="moaAssignedStudentInputs" style="display:none;">
                            @foreach ($selectedCreateStudentNames as $studentName)
                                <input type="hidden" name="student_names[]" value="{{ $studentName }}">
                            @endforeach
                        </div>
                    </div>

                    <div class="field-group" style="margin-top:10px;">
                        <label class="field-label"><i class="fa fa-keyboard"></i> Manual Student Input <span style="color:#aaa; font-weight:400;">(Optional, comma or new line separated)</span></label>
                        <textarea
                            id="manualStudentInput"
                            name="manual_student_names"
                            class="field-input"
                            rows="3"
                            placeholder="Type student names separated by comma or new line"
                            style="resize:vertical; min-height:88px;"
                        ></textarea>
                        <div style="font-size:12px; color:#888; margin-top:8px;">Use this for students without accounts. Input is also shown in the MOA list.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== EDIT COMPANY MODAL =============== -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-pen"></i> Edit MOA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCompanyForm" action="" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="modal-section"><i class="fa fa-building"></i> Company Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-building"></i> Company Name <span style="color:var(--red);">*</span></label>
                        <input id="edit_company_name" class="field-input" type="text" name="company_name" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-map-marker-alt"></i> Company Address <span style="color:var(--red);">*</span></label>
                        <input id="edit_company_address" class="field-input" type="text" name="company_address" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-user-tie"></i> Company Representative <span style="color:var(--red);">*</span></label>
                        <input id="edit_company_rep" class="field-input" type="text" name="company_rep" required>
                    </div>

                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-phone"></i> Contact Number <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input id="edit_company_no" class="field-input" type="text" name="companyNo" placeholder="Leave blank if none">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><i class="fa fa-envelope"></i> Email Address <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                            <input id="edit_company_email" class="field-input" type="email" name="company_email" placeholder="Leave blank if none">
                        </div>
                    </div>

                    <div class="modal-section"><i class="fa fa-calendar-alt"></i> Academic Details</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year <span style="color:var(--red);">*</span></label>
                        <div class="year-row">
                            <select id="edit_school_year_start" class="field-input" name="school_year_start" required>
                                @foreach ($schoolYearOptions as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <span>&ndash;</span>
                            <select id="edit_school_year_end" class="field-input" name="school_year_end" required></select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                            <span><i class="fa fa-calendar-check"></i> Date Notarized</span>
                            <span style="font-size: 11.5px; color: #777; font-weight: 400;">Select the date when the MOA was notarized.</span>
                        </label>
                        <input id="editDateNotarized" class="field-input" type="date" name="date_notarized">
                    </div>

                    <div class="field-group">
                        <label class="field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                            <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                            <span style="font-size: 11.5px; color: #777; font-weight: 400;">Select the MOA expiry date.</span>
                        </label>
                        <input id="editValidUntil" class="field-input" type="date" name="valid_until" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-graduation-cap"></i> Course <span style="color:var(--red);">*</span></label>
                        <div class="course-picker-shell">
                            <input
                                type="text"
                                id="editMoaCourseSearch"
                                class="course-picker-search"
                                placeholder="Search course acronym or name..."
                            >

                            <div id="editMoaCourseSelect" class="course-picker-scroll course-checkbox-group">
                                @foreach ($course as $courseItem)
                                    <label class="course-option" data-course-name="{{ strtolower($courseItem->course) }}" data-course-acronym="{{ strtolower($courseItem->acronym ?? '') }}">
                                        <input
                                            class="form-check-input edit-course-checkbox"
                                            type="checkbox"
                                            name="course[]"
                                            id="editCourse{{ $courseItem->id }}"
                                            value="{{ $courseItem->course }}">
                                        <span class="course-option-content">
                                            <span class="course-option-acronym">{{ $courseItem->acronym ?: $courseItem->course }}</span>
                                            <span class="course-option-name">{{ $courseItem->course }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div style="font-size:11.5px; color:#777;">Select one or more courses.</div>
                    </div>

                    <div class="modal-section"><i class="fa fa-paperclip"></i> MOA Document</div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-file-pdf"></i> Replace MOA File <span style="color:#aaa; font-weight:400;">(Optional)</span></label>
                        <input class="field-input" type="file" name="file" data-max-size-mb="30" accept="application/pdf" style="padding:8px 13px;">
                        <div style="font-size:12px; color:#888; margin-top:8px;">Leave this blank to keep the current PDF.</div>
                        <div style="margin-top:6px; font-size:12px; color:#777;">PDF only | Max file size: 30 MB</div>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>

                    <div class="modal-section"><i class="fa fa-users"></i> Assign Students</div>

                    <div class="field-group" style="margin-top:10px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                            <div>
                                <label class="field-label" style="margin-bottom:4px;"><i class="fa fa-user-graduate"></i> Assigned Students</label>
                                <div id="editMoaAssignedStudentsSummary" style="font-size:12px; color:#666;">No students assigned yet.</div>
                            </div>
                            <button type="button" class="btn-modal-submit" id="openEditAssignStudentsModal" style="padding:10px 14px; white-space:nowrap;">
                                <i class="fa fa-user-plus me-1"></i> Assign
                            </button>
                        </div>
                        <div id="editMoaAssignedStudentInputs" style="display:none;"></div>
                    </div>

                    <div class="field-group" style="margin-top:10px;">
                        <label class="field-label"><i class="fa fa-keyboard"></i> Manual Student Input <span style="color:#aaa; font-weight:400;">(Optional, comma or new line separated)</span></label>
                        <textarea
                            id="editManualStudentInput"
                            name="manual_student_names"
                            class="field-input"
                            rows="3"
                            placeholder="Type student names separated by comma or new line"
                            style="resize:vertical; min-height:88px;"
                        ></textarea>
                        <div style="font-size:12px; color:#888; margin-top:8px;">Use this for students without accounts. Input is also shown in the MOA list.</div>
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

<!-- =============== ASSIGN STUDENTS MODAL =============== -->
<div class="modal fade" id="assignStudentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-user-plus"></i> Assign Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#fff; max-height:78vh;">
                <div style="display:grid; grid-template-columns: 1.1fr 1fr; gap:12px; margin-bottom:14px;">
                    <div class="field-group" style="margin:0;">
                        <label class="field-label"><i class="fa fa-graduation-cap"></i> Course</label>
                        <select id="assignStudentsCourse" class="field-select">
                            <option value="">All Courses</option>
                            @foreach ($course as $courseItem)
                                <option value="{{ $courseItem->course }}">{{ $courseItem->course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group" style="margin:0;">
                        <label class="field-label"><i class="fa fa-calendar-alt"></i> School Year</label>
                        <select id="assignStudentsSchoolYear" class="field-select">
                            <option value="">All School Years</option>
                            @foreach ($studentSchoolYears as $studentSchoolYear)
                                <option value="{{ $studentSchoolYear }}">{{ $studentSchoolYear }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field-group" style="margin:0 0 14px;">
                    <label class="field-label"><i class="fa fa-search"></i> Search Students</label>
                    <input id="assignStudentsSearch" class="field-input" type="text" placeholder="Search by name, student number, or section">
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                    <div id="assignStudentsSelectedInfo" style="font-size:12.5px; color:#6b7280;">No students selected yet.</div>
                    <button type="button" class="btn-modal-close" id="assignStudentsClear" style="padding:9px 14px;">
                        <i class="fa fa-eraser me-1"></i> Clear Selection
                    </button>
                </div>

                <div id="assignStudentsSelectedChips" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px;"></div>

                <div id="assignStudentsStatus" style="display:none; padding:14px; border:1px dashed #f3b3b3; border-radius:12px; color:#6b7280; font-size:12.5px; background:#fff7f7; margin-bottom:12px;"></div>

                <div style="max-height: 52vh; overflow-y: auto; padding-right: 4px;">
                    <div id="assignStudentsList" style="display:grid; gap:10px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="margin-right:auto; font-size:12px; color:#6b7280;">Choose one or more students, then click Apply.</div>
                <button type="button" class="btn-modal-close btn-modal-cancel-red" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn-modal-submit" id="assignStudentsApply">
                    <i class="fa fa-check me-1"></i> Apply Assignments
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== SEND MODAL =============== -->
<div class="modal fade" id="sendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-paper-plane"></i> Send MOA File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/sendFile') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="send-company-banner"
                         style="display:flex; align-items:center; gap:10px; background:#fff5f5; border:1px solid #fecaca; border-radius:12px; padding:12px 14px; margin-bottom:18px;">
                        <div style="width:36px; height:36px; border-radius:9px; background:#fee2e2; display:flex; align-items:center; justify-content:center; color:var(--red); font-size:14px; flex-shrink:0;">
                            <i class="fa fa-building"></i>
                        </div>
                        <div>
                            <div id="send-company-name" style="font-size:13.5px; font-weight:700; color:#1a1a1a;"></div>
                            <div style="font-size:12px; color:#888; margin-top:2px;">Send MOA document via email</div>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label"><i class="fa fa-envelope"></i> Recipient Email Address</label>
                        <input class="field-input" type="email" name="email" placeholder="Enter email address" required>
                    </div>

                    <input type="hidden" id="send-file-id" name="file_id" value="">
                    <input type="hidden" id="send-company-name-input" name="company_name" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn-modal-close btn-modal-cancel-red" type="button" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-paper-plane me-1"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== PRINT PREVIEW MODAL =============== -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-print"></i> Print Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scrollable-modal-body">
                <iframe id="viewIframe" style="width:100%; height:520px; border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-close" type="button" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
                <button type="button" onclick="printRegularPreview()" class="btn-modal-submit">
                    <i class="fa fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    window.companiesConfig = {
        assignStudentsUrl: @json(route('moa.assignableStudents')),
        selectedCreateEndYear: @json($selectedCreateEndYear ?? ''),
        fileError: @json($errors->first('file'))
    };
</script>
<script src="{{ vasset('js/coordinator/companies.js') }}?v={{ time() }}"></script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
<script src="{{ vasset('assets/js/upload-size-guard.js') }}"></script>

<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
<script src="{{ vasset('js/mobile-utils.js') }}"></script>
</body>
</html>
