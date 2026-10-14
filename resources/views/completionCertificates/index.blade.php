<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Certificate of Completion</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/completion-certificates.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
    <script src="{{ vasset('js/darkmode.js') }}"></script>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <a href="#" class="sidebar-brand">
        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJTIMS</span>
        </div>
    </a>

    <a href="{{ $portal === 'student' ? url('/student/accountinfo') : ($portal === 'professor' ? url('/professor/accountinfo') : url('/accountinfo')) }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa {{ $portal === 'student' ? 'fa-user-graduate' : 'fa-user-tie' }}"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
            <span class="user-role">{{ $portal === 'student' ? 'Student' : ($portal === 'professor' ? 'Professor' : 'OJT Coordinator') }}</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        @if($portal === 'student')
            <a href="{{ url('/student/home') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-home"></i></span><span class="nav-label">Home</span><span class="tooltip-label">Home</span></a>
            <a href="{{ url('/student/ojtinfo') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-layer-group"></i></span><span class="nav-label">OJT Information</span><span class="tooltip-label">OJT Information</span></a>
            <a href="{{ url('/student/class') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-clipboard"></i></span><span class="nav-label">Class</span><span class="tooltip-label">Class</span></a>
            <a href="{{ url('/student/files') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-download"></i></span><span class="nav-label">Downloadable Files</span><span class="tooltip-label">Downloadable Files</span></a>
            <a href="{{ url('/student/MOA') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-file-contract"></i></span><span class="nav-label">Notarized MOA</span><span class="tooltip-label">Notarized MOA</span></a>
            <a href="{{ url('/student/requirements') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span><span class="nav-label">Requirements</span><span class="tooltip-label">Requirements</span></a>
            <a href="{{ route('student.completion-certificate.index') }}" class="nav-item active"><span class="nav-icon"><i class="fa fa-certificate"></i></span><span class="nav-label">Completion Certificate</span><span class="tooltip-label">Completion Certificate</span></a>
            <a href="{{ url('/student/evaluation') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-star-half-alt"></i></span><span class="nav-label">Evaluation</span><span class="tooltip-label">Evaluation</span></a>
        @elseif($portal === 'professor')
            <a href="{{ route('professor_home') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-home"></i></span><span class="nav-label">Dashboard</span><span class="tooltip-label">Dashboard</span></a>
            <a href="{{ url('/professor/class') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-clipboard"></i></span><span class="nav-label">Class</span><span class="tooltip-label">Class</span></a>
            <a href="{{ route('professor.requirementStatus.classes') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-clipboard-check"></i></span><span class="nav-label">Requirement Status</span><span class="tooltip-label">Requirement Status</span></a>
            <a href="{{ route('professor.completion-certificates.index') }}" class="nav-item active"><span class="nav-icon"><i class="fa fa-certificate"></i></span><span class="nav-label">COC Validation</span><span class="tooltip-label">COC Validation</span></a>
            <a href="{{ url('/professor/analytics') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-chart-line"></i></span><span class="nav-label">Analytics</span><span class="tooltip-label">Analytics</span></a>
            <a href="{{ url('/reportsExpiredProf') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-file-contract"></i></span><span class="nav-label">MOA</span><span class="tooltip-label">MOA</span></a>
            <a href="{{ url('/professor/maintain') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-cogs"></i></span><span class="nav-label">Maintenance</span><span class="tooltip-label">Maintenance</span></a>
            <a href="{{ url('/professor/evaluation') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-star-half-alt"></i></span><span class="nav-label">Evaluation</span><span class="tooltip-label">Evaluation</span></a>
        @else
            <a href="{{ url('/dashboard') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-home"></i></span><span class="nav-label">Dashboard</span><span class="tooltip-label">Dashboard</span></a>
            <a href="{{ url('/studentLists') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-users"></i></span><span class="nav-label">Students</span><span class="tooltip-label">Students</span></a>
            <a href="{{ url('/professorTab') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-chalkboard-teacher"></i></span><span class="nav-label">Professors</span><span class="tooltip-label">Professors</span></a>
            <a href="{{ url('/uploadpage') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-file-upload"></i></span><span class="nav-label">Upload Templates</span><span class="tooltip-label">Upload Templates</span></a>
            <a href="{{ url('/maintenance') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-cogs"></i></span><span class="nav-label">Maintenance</span><span class="tooltip-label">Maintenance</span></a>
            <a href="{{ url('/MOA') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-file-contract"></i></span><span class="nav-label">MOA</span><span class="tooltip-label">MOA</span></a>
            <a href="{{ route('coordinator.completion-certificates.index') }}" class="nav-item active"><span class="nav-icon"><i class="fa fa-certificate"></i></span><span class="nav-label">COC Validation</span><span class="tooltip-label">COC Validation</span></a>
            <div class="nav-group-reports">
                <a href="{{ url('/reports') }}" class="nav-item nav-item-reports"><span class="nav-icon"><i class="fa fa-chart-bar"></i></span><span class="nav-label">Reports</span><span class="tooltip-label">Reports</span></a>
                <div class="nav-sub">
                    <a href="{{ url('/reports') }}" class="nav-sub-item"><i class="fa fa-user-graduate"></i> Student OJT Info</a>
                    <a href="{{ url('/reportsExpired') }}" class="nav-sub-item"><i class="fa fa-file-contract"></i> MOA</a>
                </div>
            </div>
            <a href="{{ url('/analytics') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-chart-line"></i></span><span class="nav-label">Analytics</span><span class="tooltip-label">Analytics</span></a>
            <a href="{{ url('/auditlog') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-clipboard-list"></i></span><span class="nav-label">Audit Log</span><span class="tooltip-label">Audit Log</span></a>
        @endif
    </nav>
    <div class="sidebar-footer"><a href="{{ url('/logout') }}" class="nav-item"><span class="nav-icon"><i class="fa fa-sign-out-alt"></i></span><span class="nav-label">Log Out</span><span class="tooltip-label">Log Out</span></a></div>
</aside>

<main class="main-content" id="mainContent">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <button class="darkmode-toggle" id="darkmodeToggle"><i class="fa fa-moon"></i></button>
            <span class="topbar-title">On-the-Job Training <span>Information Management System</span></span>
        </div>
        <div class="topbar-right">
            @if($portal === 'student')
                <div class="topbar-badge"><i class="fa fa-graduation-cap"></i> Student Portal</div>
            @elseif($portal === 'professor')
                <div class="topbar-badge"><i class="fa fa-chalkboard-teacher"></i> Professor Portal</div>
            @else
                <div class="topbar-badge"><i class="fa fa-user-shield"></i> OJT Coordinator</div>
            @endif
        </div>
    </header>

    <div class="page-content coc-page">
        <div class="page-header">
            <div>
                <h1>Certificate of <span>Completion</span></h1>
                <div class="breadcrumb"><i class="fa fa-home"></i><span>{{ ucfirst($portal) }} Portal</span><i class="fa fa-chevron-right"></i><span>Certificate of Completion</span></div>
            </div>
        </div>

        @if(session('success'))<div class="coc-alert success"><i class="fa fa-check-circle"></i>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="coc-alert error"><i class="fa fa-times-circle"></i>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="coc-alert error"><i class="fa fa-exclamation-circle"></i>{{ $errors->first() }}</div>@endif

        @if($portal === 'student')
            @php
                $canUpload = !$currentCertificate || $currentCertificate->canBeResubmitted();
                $statusClass = $currentCertificate ? str_replace('_', '-', $currentCertificate->status) : 'missing';
                $professorValidated = $currentCertificate?->hasProfessorValidation() ?? false;
                $professorValidationSkipped = $currentCertificate?->professorValidationWasSkipped() ?? false;
                $professorRejected = $currentCertificate?->status === 'professor_rejected';
            @endphp
            <section class="coc-hero">
                <div class="coc-hero-icon"><i class="fa fa-award"></i></div>
                <div>
                    <span class="eyebrow">Official OJT Completion Document</span>
                    <h2>Submit your Certificate of Completion</h2>
                    <p>Professor review is an optional endorsement. Final validation by the OJT coordinator is the required step for completion.</p>
                </div>
            </section>

            <div class="student-coc-grid">
                <section class="coc-card">
                    <div class="coc-card-header"><div><h3>Current Submission</h3><p>Your latest certificate and validation progress</p></div></div>
                    <div class="coc-card-body">
                        @if($currentCertificate)
                            <div class="current-document">
                                <div class="document-icon"><i class="fa fa-file-pdf"></i></div>
                                <div class="document-details">
                                    <strong>{{ $currentCertificate->original_name ?: 'Certificate of Completion.pdf' }}</strong>
                                    <span>Version {{ $currentCertificate->version }} · Submitted {{ $currentCertificate->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <span class="status-pill {{ $statusClass }}">{{ $currentCertificate->statusLabel() }}</span>
                            </div>
                            <div class="review-progress">
                                <div class="review-step complete"><i class="fa fa-check"></i><div><strong>Uploaded</strong><span>{{ $currentCertificate->created_at->format('M d, Y') }}</span></div></div>
                                <div class="review-line {{ $professorValidated ? 'complete' : ($professorValidationSkipped ? 'skipped' : '') }}"></div>
                                <div class="review-step {{ $professorRejected ? 'rejected' : ($professorValidated ? 'complete' : ($professorValidationSkipped ? 'skipped' : 'optional')) }}">
                                    <i class="fa {{ $professorRejected ? 'fa-times' : ($professorValidated ? 'fa-user-check' : ($professorValidationSkipped ? 'fa-forward' : 'fa-user-clock')) }}"></i>
                                    <div>
                                        <strong>Professor Review (Optional)</strong>
                                        <span>
                                            @if($professorRejected)
                                                Rejected {{ $currentCertificate->professor_reviewed_at?->format('M d, Y') }}
                                            @elseif($professorValidated)
                                                {{ $currentCertificate->professor_reviewed_at->format('M d, Y') }}
                                            @elseif($professorValidationSkipped)
                                                Skipped by coordinator
                                            @elseif(!$currentCertificate->professor_id)
                                                No professor assigned
                                            @else
                                                Available for review
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="review-line {{ $currentCertificate->status === 'approved' ? ($professorValidationSkipped ? 'skipped' : 'complete') : '' }}"></div>
                                <div class="review-step {{ $currentCertificate->status === 'approved' ? 'complete' : ($currentCertificate->status === 'coordinator_rejected' ? 'rejected' : '') }}"><i class="fa {{ $currentCertificate->status === 'coordinator_rejected' ? 'fa-times' : 'fa-user-shield' }}"></i><div><strong>Coordinator Review</strong><span>{{ $currentCertificate->coordinator_reviewed_at?->format('M d, Y') ?: 'Pending' }}</span></div></div>
                            </div>
                            @if($currentCertificate->professor_remarks || $currentCertificate->coordinator_remarks)
                                <div class="review-remarks"><strong><i class="fa fa-comment-alt"></i> Review remarks</strong><p>{{ $currentCertificate->coordinator_remarks ?: $currentCertificate->professor_remarks }}</p></div>
                            @endif
                            <div class="document-actions">
                                <button type="button" class="coc-btn secondary" data-bs-toggle="modal" data-bs-target="#cocPdfModal" data-coc-pdf-url="{{ route('completion-certificates.view', $currentCertificate) }}" data-coc-pdf-title="{{ $currentCertificate->original_name ?: 'Certificate of Completion.pdf' }}"><i class="fa fa-eye"></i> View PDF</button>
                                <a href="{{ route('completion-certificates.download', $currentCertificate) }}" class="coc-btn secondary"><i class="fa fa-download"></i> Download</a>
                            </div>
                        @else
                            <div class="empty-state"><i class="fa fa-file-upload"></i><h3>No certificate submitted</h3><p>Upload the official PDF provided by your training company.</p></div>
                        @endif
                    </div>
                </section>

                <section class="coc-card">
                    <div class="coc-card-header"><div><h3>{{ $currentCertificate?->status === 'approved' ? 'OJT Completion' : ($currentCertificate ? 'Submit Corrected Copy' : 'Upload Certificate') }}</h3><p>{{ $currentCertificate?->status === 'approved' ? 'Your final validation is complete' : 'PDF only, maximum 30 MB' }}</p></div></div>
                    <div class="coc-card-body">
                        @if($canUpload)
                            <form method="POST" action="{{ route('student.completion-certificate.upload') }}" enctype="multipart/form-data" data-upload-validation data-upload-description="Checking the Certificate of Completion PDF type, structure, size, and security requirements. Please keep this page open.">
                                @csrf
                                <label class="coc-dropzone" data-coc-dropzone>
                                    <input type="file" name="file" accept="application/pdf,.pdf" required>
                                    <span class="drop-icon"><i class="fa fa-cloud-upload-alt"></i></span>
                                    <strong data-file-title>Choose Certificate PDF</strong>
                                    <span data-file-hint>The file will be privately stored and security validated.</span>
                                </label>
                                <button type="submit" class="coc-btn primary full"><i class="fa fa-shield-alt"></i> Validate and Submit</button>
                            </form>
                        @elseif($currentCertificate->status === 'approved')
                            <div class="completion-celebration">
                                <div class="celebration-icon"><i class="fa fa-trophy"></i></div>
                                <span class="celebration-eyebrow">OJT Journey Complete</span>
                                <h3>Congratulations!</h3>
                                <p>Your Certificate of Completion has been fully validated by the OJT coordinator. Your hard work, dedication, and training have paid off.</p>
                                <div class="celebration-badge"><i class="fa fa-star"></i> Well done, future professional!</div>
                            </div>
                        @else
                            <div class="locked-state"><i class="fa fa-lock"></i><h3>Upload currently locked</h3><p>Wait for the current review to finish before submitting another copy.</p></div>
                        @endif
                    </div>
                </section>
            </div>

            @if($history->total() > 1)
                <section class="coc-card history-card">
                    <div class="coc-card-header"><div><h3>Submission History</h3><p>Previous certificate versions are retained for audit purposes</p></div></div>
                    <div class="table-responsive"><table class="coc-table mobile-stack"><thead><tr><th>Version</th><th>Submitted</th><th>Status</th><th>Professor Review</th><th>Coordinator Review</th><th>File</th></tr></thead><tbody>
                    @foreach($history as $certificate)
                        <tr>
                            <td data-label="Version">Version {{ $certificate->version }}</td>
                            <td data-label="Submitted">{{ $certificate->created_at->format('M d, Y') }}</td>
                            <td data-label="Status"><span class="status-pill {{ str_replace('_', '-', $certificate->status) }}">{{ $certificate->statusLabel() }}</span></td>
                            <td data-label="Professor Review">{{ $certificate->professor_reviewed_at?->format('M d, Y') ?: ($certificate->professorValidationWasSkipped() ? 'Skipped' : '—') }}</td>
                            <td data-label="Coordinator Review">{{ $certificate->coordinator_reviewed_at?->format('M d, Y') ?: '—' }}</td>
                            <td data-label="File"><button type="button" class="table-link" data-bs-toggle="modal" data-bs-target="#cocPdfModal" data-coc-pdf-url="{{ route('completion-certificates.view', $certificate) }}" data-coc-pdf-title="Certificate of Completion - Version {{ $certificate->version }}"><i class="fa fa-eye"></i> View</button></td>
                        </tr>
                    @endforeach
                    </tbody></table></div>
                    @if($history->hasPages())
                        @php
                            $historyPaginationStart = max(1, $history->currentPage() - 2);
                            $historyPaginationEnd = min($history->lastPage(), $history->currentPage() + 2);
                        @endphp
                        <div class="pagination-wrap">
                            <div class="pagination-meta">Showing {{ $history->firstItem() }} to {{ $history->lastItem() }} of {{ $history->total() }} submissions</div>
                            <div class="pagination-nav" aria-label="Certificate history pages">
                                <a href="{{ $history->previousPageUrl() ?: '#' }}" class="page-btn {{ $history->onFirstPage() ? 'disabled' : '' }}" aria-label="Previous history page"><i class="fa fa-chevron-left"></i></a>
                                @if($historyPaginationStart > 1)
                                    <a href="{{ $history->url(1) }}" class="page-btn">1</a>
                                    @if($historyPaginationStart > 2)<span class="pagination-ellipsis">&hellip;</span>@endif
                                @endif
                                @for($page = $historyPaginationStart; $page <= $historyPaginationEnd; $page++)
                                    @if($page === $history->currentPage())
                                        <span class="page-btn active" aria-current="page">{{ $page }}</span>
                                    @else
                                        <a href="{{ $history->url($page) }}" class="page-btn">{{ $page }}</a>
                                    @endif
                                @endfor
                                @if($historyPaginationEnd < $history->lastPage())
                                    @if($historyPaginationEnd < $history->lastPage() - 1)<span class="pagination-ellipsis">&hellip;</span>@endif
                                    <a href="{{ $history->url($history->lastPage()) }}" class="page-btn">{{ $history->lastPage() }}</a>
                                @endif
                                <a href="{{ $history->nextPageUrl() ?: '#' }}" class="page-btn {{ $history->hasMorePages() ? '' : 'disabled' }}" aria-label="Next history page"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    @endif
                </section>
            @endif
        @else
            <section class="coc-hero compact">
                <div class="coc-hero-icon"><i class="fa {{ $portal === 'professor' ? 'fa-user-check' : 'fa-user-shield' }}"></i></div>
                <div><span class="eyebrow">{{ $portal === 'professor' ? 'Optional Endorsement' : 'Required Final Validation' }}</span><h2>{{ $portal === 'professor' ? 'Professor COC Review' : 'Coordinator COC Review' }}</h2><p>{{ $portal === 'professor' ? 'Optionally review certificates submitted by your assigned students before coordinator validation.' : 'Perform the required final validation. You may proceed even when optional professor validation is still pending.' }}</p></div>
            </section>

            <section class="coc-card review-card">
                <div class="coc-card-header review-header">
                    <div><h3>Certificate Submissions</h3><p>{{ $records->total() }} current student submission{{ $records->total() === 1 ? '' : 's' }}</p></div>
                    <form method="GET" class="review-filters">
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search student, number, or program">
                        <select name="program" onchange="this.form.submit()"><option value="">All programs</option>@foreach($programs as $program)<option value="{{ $program }}" @selected(request('program') === $program)>{{ $program }}</option>@endforeach</select>
                        <select name="school_year" onchange="this.form.submit()"><option value="">All school years</option>@foreach($schoolYears as $schoolYear)<option value="{{ $schoolYear }}" @selected(request('school_year') === $schoolYear)>{{ str_replace('-', ' - ', $schoolYear) }}</option>@endforeach</select>
                        <select name="status" onchange="this.form.submit()"><option value="">All statuses</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
                        <select name="per_page" aria-label="Entries per page" onchange="this.form.submit()">@foreach([10, 25, 50] as $size)<option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }} entries</option>@endforeach</select>
                        <button class="coc-btn secondary" type="submit"><i class="fa fa-search"></i> Search</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="coc-table review-table mobile-stack">
                        <thead><tr><th>Student</th><th>Program / Section</th><th>Professor</th><th>Submitted</th><th>Status</th><th>Review</th></tr></thead>
                        <tbody>
                        @forelse($records as $certificate)
                            <tr>
                                <td data-label="Student"><div class="student-cell"><span class="student-avatar">{{ strtoupper(substr($certificate->student?->full_name ?: 'S', 0, 1)) }}</span><div><strong>{{ $certificate->student?->full_name ?: 'Unknown Student' }}</strong><span>{{ $certificate->student?->studentNum ?: 'No student number' }}</span></div></div></td>
                                <td data-label="Program / Section"><strong>{{ $certificate->student?->course ?: '—' }}</strong><span class="subtext">{{ $certificate->student?->year_and_section ?: '—' }}</span>@if($certificate->student?->school_year_start && $certificate->student?->school_year_end)<span class="subtext">S.Y. {{ $certificate->student->school_year_start }} - {{ $certificate->student->school_year_end }}</span>@endif</td>
                                <td data-label="Professor">{{ $certificate->professor?->full_name ?: 'Not assigned' }}</td>
                                <td data-label="Submitted">{{ $certificate->created_at->format('M d, Y') }}<span class="subtext">Version {{ $certificate->version }}</span></td>
                                <td data-label="Status"><span class="status-pill {{ str_replace('_', '-', $certificate->status) }}">{{ $certificate->statusLabel() }}</span></td>
                                <td data-label="Review">
                                    <div class="review-actions">
                                        <button type="button" class="icon-btn view" title="View PDF" aria-label="View PDF" data-bs-toggle="modal" data-bs-target="#cocPdfModal" data-coc-pdf-url="{{ route('completion-certificates.view', $certificate) }}" data-coc-pdf-title="{{ $certificate->student?->full_name ?: 'Student' }} - Certificate of Completion"><i class="fa fa-eye"></i></button>
                                        <a href="{{ route('completion-certificates.download', $certificate) }}" class="icon-btn download" title="Download"><i class="fa fa-download"></i></a>
                                        @if($portal === 'professor' && $certificate->status === 'pending_professor')
                                            <form method="POST" action="{{ route('professor.completion-certificates.approve', $certificate) }}" data-review-confirm data-confirm-title="Approve this certificate?" data-confirm-message="This will forward the Certificate of Completion to the OJT coordinator for final validation." data-confirm-button="Approve and Forward" data-confirm-color="#16a34a">@csrf<button class="icon-btn approve" title="Approve"><i class="fa fa-check"></i></button></form>
                                            <button type="button" class="icon-btn reject" title="Reject" aria-label="Reject certificate" data-bs-toggle="modal" data-bs-target="#cocRejectModal" data-reject-action="{{ route('professor.completion-certificates.reject', $certificate) }}" data-student-name="{{ $certificate->student?->full_name ?: 'this student' }}"><i class="fa fa-times"></i></button>
                                        @elseif($portal === 'coordinator' && in_array($certificate->status, ['pending_professor', 'pending_coordinator'], true))
                                            @php
                                                $requiresProfessorBypass = ! $certificate->hasProfessorValidation();
                                            @endphp
                                            <form method="POST" action="{{ route('coordinator.completion-certificates.approve', $certificate) }}" data-review-confirm data-confirm-title="{{ $requiresProfessorBypass ? 'Professor validation is still pending' : 'Final-approve this certificate?' }}" data-confirm-message="{{ $requiresProfessorBypass ? 'This COC has not been validated by a professor. Professor validation is optional. Are you sure you want to complete the required coordinator validation?' : 'This confirms that the student\'s Certificate of Completion passed both validation levels.' }}" data-confirm-button="{{ $requiresProfessorBypass ? 'Validate Without Professor' : 'Final Approve' }}" data-confirm-color="{{ $requiresProfessorBypass ? '#d97706' : '#16a34a' }}" data-confirm-icon="{{ $requiresProfessorBypass ? 'warning' : 'question' }}">@csrf<button class="icon-btn approve" title="{{ $requiresProfessorBypass ? 'Validate without professor review' : 'Final Approve' }}"><i class="fa fa-check-double"></i></button></form>
                                            <button type="button" class="icon-btn reject" title="Reject" aria-label="Reject certificate" data-bs-toggle="modal" data-bs-target="#cocRejectModal" data-reject-action="{{ route('coordinator.completion-certificates.reject', $certificate) }}" data-student-name="{{ $certificate->student?->full_name ?: 'this student' }}"><i class="fa fa-times"></i></button>
                                        @endif
                                    </div>
                                    @if($certificate->professor_remarks || $certificate->coordinator_remarks)<div class="table-remarks"><i class="fa fa-comment-alt"></i> {{ $certificate->coordinator_remarks ?: $certificate->professor_remarks }}</div>@endif
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row"><td colspan="6"><div class="empty-state"><i class="fa fa-certificate"></i><h3>No certificate submissions found</h3><p>New student submissions will appear here.</p></div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @php
                    $paginationStart = max(1, $records->currentPage() - 2);
                    $paginationEnd = min($records->lastPage(), $records->currentPage() + 2);
                @endphp
                <div class="pagination-wrap">
                    <div class="pagination-meta">
                        @if($records->total())
                            Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} submissions
                        @else
                            Showing 0 submissions
                        @endif
                    </div>
                    <div class="pagination-nav" aria-label="Certificate submission pages">
                        <a href="{{ $records->previousPageUrl() ?: '#' }}" class="page-btn {{ $records->onFirstPage() ? 'disabled' : '' }}" aria-label="Previous page"><i class="fa fa-chevron-left"></i></a>
                        @if($paginationStart > 1)
                            <a href="{{ $records->url(1) }}" class="page-btn">1</a>
                            @if($paginationStart > 2)<span class="pagination-ellipsis">&hellip;</span>@endif
                        @endif
                        @for($page = $paginationStart; $page <= $paginationEnd; $page++)
                            @if($page === $records->currentPage())
                                <span class="page-btn active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $records->url($page) }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endfor
                        @if($paginationEnd < $records->lastPage())
                            @if($paginationEnd < $records->lastPage() - 1)<span class="pagination-ellipsis">&hellip;</span>@endif
                            <a href="{{ $records->url($records->lastPage()) }}" class="page-btn">{{ $records->lastPage() }}</a>
                        @endif
                        <a href="{{ $records->nextPageUrl() ?: '#' }}" class="page-btn {{ $records->hasMorePages() ? '' : 'disabled' }}" aria-label="Next page"><i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
            </section>
        @endif
    </div>

    <footer class="dashboard-footer">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" class="footer-logo" alt="PUP">
            <span class="footer-copy">
                &copy; 1998&ndash;{{ date('Y') }} <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div class="footer-links">
            <a href="https://www.pup.edu.ph/" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                PUP Website
            </a>
            <span class="divider">|</span>
            <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
            <span class="divider">|</span>
            <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
        </div>
    </footer>
</main>

@if($portal !== 'student')
<div class="modal fade coc-reject-modal" id="cocRejectModal" tabindex="-1" aria-labelledby="cocRejectModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="modal-eyebrow">Certificate Review</span>
                    <h2 class="modal-title" id="cocRejectModalTitle"><i class="fa fa-times-circle"></i> Reason for Rejection</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" data-coc-reject-form>
                @csrf
                <div class="modal-body">
                    <div class="reject-student-banner">
                        <span class="reject-student-icon"><i class="fa fa-user-graduate"></i></span>
                        <div>
                            <strong data-reject-student>Student</strong>
                            <span>The student will be asked to correct and resubmit the certificate.</span>
                        </div>
                    </div>
                    <label for="cocRejectRemarks"><i class="fa fa-comment-alt"></i> Reason for rejection</label>
                    <textarea id="cocRejectRemarks" name="remarks" rows="5" maxlength="1000" required placeholder="Explain what the student must correct before resubmitting..."></textarea>
                    <div class="remarks-counter"><span data-remarks-count>0</span>/1000 characters</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="coc-btn secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="coc-btn danger"><i class="fa fa-ban"></i> Reject Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade coc-pdf-modal" id="cocPdfModal" tabindex="-1" aria-labelledby="cocPdfModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-heading">
                    <h2 class="modal-title" id="cocPdfModalTitle"><i class="fa fa-eye"></i> Document Preview</h2>
                    <span class="modal-file-name" data-coc-pdf-title>Certificate of Completion</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="pdf-loading" data-pdf-loading><i class="fa fa-spinner fa-spin"></i><span>Loading secure PDF preview...</span></div>
                <iframe title="Certificate of Completion PDF preview" data-coc-pdf-frame></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="coc-modal-close" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const menu = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const main = document.getElementById('mainContent');
    menu?.addEventListener('click', function () {
        if (window.innerWidth <= 900) { sidebar.classList.toggle('mobile-open'); overlay.classList.toggle('active'); }
        else { sidebar.classList.toggle('collapsed'); main.classList.toggle('expanded'); }
    });
    overlay?.addEventListener('click', function () { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });

    document.querySelectorAll('form[data-review-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === 'true') return;

            event.preventDefault();

            const submitReview = function () {
                form.dataset.confirmed = 'true';
                form.querySelectorAll('button[type="submit"]').forEach(function (button) {
                    button.disabled = true;
                });
                HTMLFormElement.prototype.submit.call(form);
            };

            if (!window.Swal) {
                if (window.confirm(form.dataset.confirmMessage)) submitReview();
                return;
            }

            window.Swal.fire({
                title: form.dataset.confirmTitle,
                text: form.dataset.confirmMessage,
                icon: form.dataset.confirmIcon || 'question',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton,
                cancelButtonText: 'Cancel',
                confirmButtonColor: form.dataset.confirmColor || '#16a34a',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) submitReview();
            });
        });
    });

    const rejectModal = document.getElementById('cocRejectModal');
    const rejectForm = rejectModal?.querySelector('[data-coc-reject-form]');
    const rejectStudent = rejectModal?.querySelector('[data-reject-student]');
    const rejectRemarks = rejectModal?.querySelector('#cocRejectRemarks');
    const remarksCount = rejectModal?.querySelector('[data-remarks-count]');

    rejectModal?.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        rejectForm.action = trigger?.dataset.rejectAction || '';
        rejectStudent.textContent = trigger?.dataset.studentName || 'Student';
        rejectRemarks.value = '';
        remarksCount.textContent = '0';
    });

    rejectModal?.addEventListener('shown.bs.modal', function () {
        rejectRemarks?.focus();
    });

    rejectRemarks?.addEventListener('input', function () {
        remarksCount.textContent = String(rejectRemarks.value.length);
    });

    rejectForm?.addEventListener('submit', function () {
        rejectForm.querySelectorAll('button').forEach(function (button) {
            button.disabled = true;
        });
    });

    const pdfModal = document.getElementById('cocPdfModal');
    const pdfFrame = pdfModal?.querySelector('[data-coc-pdf-frame]');
    const pdfTitle = pdfModal?.querySelector('[data-coc-pdf-title]');
    const pdfLoading = pdfModal?.querySelector('[data-pdf-loading]');

    pdfModal?.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const pdfUrl = trigger?.dataset.cocPdfUrl;

        pdfTitle.textContent = trigger?.dataset.cocPdfTitle || 'Certificate of Completion';
        pdfLoading.classList.remove('is-hidden');
        pdfFrame.classList.remove('is-ready');
        pdfFrame.src = pdfUrl || 'about:blank';
    });

    pdfFrame?.addEventListener('load', function () {
        if (!pdfFrame.src || pdfFrame.src === 'about:blank') return;
        pdfLoading.classList.add('is-hidden');
        pdfFrame.classList.add('is-ready');
    });

    pdfModal?.addEventListener('hidden.bs.modal', function () {
        pdfFrame.src = 'about:blank';
        pdfFrame.classList.remove('is-ready');
        pdfLoading.classList.remove('is-hidden');
    });

    document.querySelectorAll('[data-coc-dropzone]').forEach(function (dropzone) {
        const input = dropzone.querySelector('input[type="file"]');
        const title = dropzone.querySelector('[data-file-title]');
        const hint = dropzone.querySelector('[data-file-hint]');
        const icon = dropzone.querySelector('.drop-icon i');

        input?.addEventListener('change', function () {
            const file = input.files && input.files[0];

            if (!file) {
                dropzone.classList.remove('has-file');
                title.textContent = 'Choose Certificate PDF';
                hint.textContent = 'The file will be privately stored and security validated.';
                icon.className = 'fa fa-cloud-upload-alt';
                return;
            }

            const size = file.size >= 1048576
                ? (file.size / 1048576).toFixed(2) + ' MB'
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';
            dropzone.classList.add('has-file');
            title.textContent = 'PDF ready to upload';
            hint.textContent = file.name + ' · ' + size;
            icon.className = 'fa fa-check';
        });
    });
});
</script>
<script src="{{ vasset('js/sidebar-persist.js') }}"></script>
@if($portal === 'student')
    @include('partials.upload-validation')
@endif
</body>
</html>
