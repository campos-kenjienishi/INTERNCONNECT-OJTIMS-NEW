<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Onboarding</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="{{ vasset('css/pages/onboarding.css') }}?v={{ time() }}">
</head>
<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container">
        <div class="right-panel">
            <div class="d-flex justify-content-center w-100 mb-2">
                <div class="auth-brand">
                    <div class="auth-brand-row">
                        <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect Logo" class="auth-logo">
                        <div class="brand-name">Intern<span>Connect</span></div>
                    </div>
                    <div class="system-title">On-the-Job Training (OJT) Information Management System</div>
                </div>
            </div>
            <!-- Step indicator -->
            <div class="step-indicator">
                <div class="step-dot active" id="dot1">1</div>
                <div class="step-line" id="line1"></div>
                <div class="step-dot" id="dot2">2</div>
            </div>
            <div class="step-label-row">
                <span class="step-label active" id="label1">Personal Information</span>
                <span class="step-label" id="label2">Academic Information</span>
            </div>
            <form method="POST" action="{{ route('onboarding.store', ['email' => $idp['email'] ?? '']) }}" id="onboardForm">
                @csrf

                <div class="form-steps-wrapper">
                    <!-- STEP 1: Personal Info -->
                    <div class="form-step active" id="step1">
                        @if(!empty($guisisData))
                            <div class="alert alert-success-custom d-flex align-items-center mb-3">
                                <i class="fa fa-check-circle me-2 alert-icon"></i>
                                <div>
                                    <strong class="alert-title">Guidance System (GuiSIS) Match:</strong> Your academic record was auto-found! Details have been pre-filled for verification.
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info-custom d-flex align-items-center mb-3">
                                <i class="fa fa-info-circle me-2 alert-icon"></i>
                                <div>
                                    <strong class="alert-title">Manual Registration:</strong> Record not found in Guidance System (GuiSIS). Please complete your profile details manually.
                                </div>
                            </div>
                        @endif
                        <div class="reg-header mb-3">
                            <h2>Personal Information</h2>
                            <p>Step 1 of 2 — Review your details</p>
                        </div>
                        <div class="fields-grid">
                            <div class="field-group span-3">
                                <label class="form-label">First Name <span class="required-star">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['first_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Middle Name <span class="required-star">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['middle_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Last Name <span class="required-star">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['last_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">E-mail Address <span class="required-star">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-envelope i-icon"></i>
                                    <input type="email" class="form-control" value="{{ $idp['email'] ?? '' }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="nav-btn-row mt-4">
                            <button type="button" id="btnProceed" class="btn-proceed" onclick="goToStep2()">
                                Proceed to Academic Information &nbsp;<i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <!-- STEP 2: Academic Info -->
                    <div class="form-step" id="step2">
                        <div class="reg-header mb-3">
                            <h2>Academic Information</h2>
                            <p>Step 2 of 2 — Fill in your academic details</p>
                        </div>
                        @php
                            $fetchedStudentNum = $guisisData['studentNumber'] ?? $guisisData['student_number'] ?? $guisisData['studentNum'] ?? '';
                            $fetchedYearSection = (isset($guisisData['yearLevel']) && isset($guisisData['section'])) 
                                ? ($guisisData['yearLevel'] . '-' . $guisisData['section']) 
                                : ($guisisData['year_and_section'] ?? $guisisData['year_section'] ?? '');
                            $fetchedCourse = (is_array($guisisData['program'] ?? null) ? ($guisisData['program']['name'] ?? $guisisData['program']['program'] ?? '') : ($guisisData['program'] ?? ''))
                                ?: (is_array($guisisData['course'] ?? null) ? ($guisisData['course']['course'] ?? $guisisData['course']['name'] ?? '') : ($guisisData['course'] ?? ''));
                        @endphp
                        <div class="fields-grid">
                            <div class="field-group span-3">
                                <label class="form-label">Student Number <span style="color:#fca5a5;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-id-card i-icon"></i>
                                    <input type="text" name="studentNum" class="form-control" required placeholder="Enter student number" value="{{ old('studentNum', $fetchedStudentNum) }}">
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Semester <span style="color:#fca5a5;">*</span></label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-calendar i-icon"></i>
                                    <select name="semester" class="form-control" required>
                                        <option value="" disabled {{ old('semester') ? '' : 'selected' }}>Select Semester</option>
                                        <option value="1st Sem" {{ old('semester') === '1st Sem' ? 'selected' : '' }}>1st Sem</option>
                                        <option value="2nd Sem" {{ old('semester') === '2nd Sem' ? 'selected' : '' }}>2nd Sem</option>
                                        <option value="Summer" {{ old('semester') === 'Summer' ? 'selected' : '' }}>Summer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Academic Year <span style="color:#fca5a5;">*</span> <small style="color: #fca5a5; font-size: 11px;">(Auto-consecutive)</small></label>
                                <div class="year-row d-flex gap-2 align-items-center">
                                    <div class="input-wrap has-select flex-grow-1">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <select name="academic_year_start" id="academic_year_start" class="form-control" required>
                                            <option value="" disabled {{ old('academic_year_start') ? '' : 'selected' }}>Start Year</option>
                                            @php $currY = (int) date('Y'); @endphp
                                            @for ($year = ($currY + 2); $year >= ($currY - 5); $year--)
                                                <option value="{{ $year }}" {{ old('academic_year_start') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <span class="year-sep" style="color: #ffffff; font-weight: 700; font-size: 16px;">—</span>
                                    <div class="input-wrap has-select flex-grow-1">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <select id="academic_year_end_display" class="form-control" disabled style="opacity: 0.85; cursor: not-allowed;">
                                            <option value="" disabled {{ old('academic_year_end') ? '' : 'selected' }}>End Year</option>
                                            @for ($year = ($currY + 3); $year >= ($currY - 4); $year--)
                                                <option value="{{ $year }}" {{ old('academic_year_end') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                        <input type="hidden" name="academic_year_end" id="academic_year_end" value="{{ old('academic_year_end', '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Year and Section <span style="color:#fca5a5;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-users i-icon"></i>
                                    <input type="text" name="year_and_section" class="form-control" required placeholder="e.g. 4-1" value="{{ old('year_and_section', $fetchedYearSection) }}">
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Course <span style="color:#fca5a5;">*</span></label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-university i-icon"></i>
                                    <select name="course" class="form-control" required>
                                        @foreach($courses as $c)
                                            <option value="{{ $c->course }}" {{ (strtolower($fetchedCourse) === strtolower($c->course)) ? 'selected' : '' }}>{{ $c->course }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Professor <span style="color:#fca5a5;">*</span></label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-chalkboard-teacher i-icon"></i>
                                    <select name="adviser_name" class="form-control" required>
                                        <option value="">Select Professor</option>
                                        <option value="Not Yet Listed">Not Yet Listed</option>
                                        @foreach($professors as $prof)
                                            <option value="{{ $prof->full_name }}">{{ $prof->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="nav-btn-row">
                            <button type="button" class="btn-back-step" onclick="goToStep1()">
                                <i class="fa fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn-proceed">
                                <i class="fa fa-user-plus"></i> Complete Registration
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
<script src="{{ vasset('js/pages/onboarding.js') }}"></script>
</body>
</html>