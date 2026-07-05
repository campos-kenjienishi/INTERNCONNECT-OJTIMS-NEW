<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <style>
        /* Compact adjustments only for STEP 2 (Academic Information) */
        #step2 .fields-grid { gap: 8px 12px; }
        #step2 .field-group { margin-bottom: 8px; }
        #step2 .form-label { margin-bottom: 6px; font-size: 13px; }
        #step2 .input-wrap { margin-bottom: 6px; }
        #step2 .input-wrap input,
        #step2 .input-wrap select { padding: 10px 40px; font-size: 14px; }
        #step2 .nav-btn-row { margin-top: 8px; gap: 8px; }

        #step1 .fields-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px 14px;
            align-items: start;
        }

        #step1 .field-group.compact .input-wrap input {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        #step1 .field-group.span-2 {
            grid-column: span 2;
        }

        #step1 .field-group.span-3 {
            grid-column: span 3;
        }

        #step1 .field-group.full-width {
            grid-column: 1 / -1;
        }

        .input-wrap.has-bubble {
            overflow: visible;
        }

        .field-bubble {
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: calc(100% + 8px);
            z-index: 30;
            padding: 8px 11px;
            border-radius: 12px;
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
            font-size: 11.5px;
            line-height: 1.35;
            box-shadow: 0 10px 24px rgba(59, 0, 0, 0.18);
            visibility: hidden;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
            pointer-events: none;
        }

        .field-bubble.active {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .field-bubble::before {
            content: '';
            position: absolute;
            bottom: -7px;
            left: 22px;
            width: 12px;
            height: 12px;
            background: #fff7ed;
            border-bottom: 1px solid #fdba74;
            border-right: 1px solid #fdba74;
            transform: rotate(45deg);
        }

        @media (max-width: 1023px) {
            #step1 .fields-grid {
                grid-template-columns: 1fr 1fr;
            }

            #step1 .field-group.compact,
            #step1 .field-group.span-2,
            #step1 .field-group.span-3,
            #step1 .field-group.full-width {
                grid-column: auto;
            }

            #step1 .field-group.full-width {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 767px) {
            #step1 .fields-grid {
                grid-template-columns: 1fr;
            }

            #step1 .field-group.compact,
            #step1 .field-group.span-2,
            #step1 .field-group.span-3,
            #step1 .field-group.full-width {
                grid-column: 1;
            }
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Registration</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/registration-responsive.css') }}">

    <style>
        body.auth-centered-page .login-container {
            max-width: 940px;
            min-height: auto;
        }

        body.auth-centered-page .login-container.success-state {
            max-width: 640px;
        }

        body.auth-centered-page .left-panel {
            display: none !important;
        }

        body.auth-centered-page .right-panel {
            width: 100%;
            flex: 1 1 auto;
            min-height: 620px;
            padding: 48px 42px;
            justify-content: center;
        }

        body.auth-centered-page .right-panel.success-state {
            min-height: auto;
            padding: 36px 42px;
            justify-content: center;
        }

        .success-compact {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .success-compact .reg-header {
            margin-bottom: 0 !important;
        }

        .success-compact .reg-header h2 {
            margin-bottom: 6px;
        }

        .success-compact .reg-header p {
            margin-bottom: 0;
        }

        .success-compact .alert {
            margin-bottom: 0;
            padding: 10px 14px;
            font-size: 14px;
            line-height: 1.35;
        }

        .success-compact .nav-btn-row {
            margin-top: 0 !important;
        }

        .auth-brand {
            display: flex;
            flex-direction: row;
            align-items: center;
            text-align: left;
            gap: 14px;
            margin-bottom: 22px;
            justify-content: flex-start;
        }

        .auth-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 0 16px rgba(255,255,255,0.18));
        }

        .auth-brand-copy .brand-name {
            font-size: 24px;
            margin-bottom: 2px;
            text-align: left;
        }

        .auth-brand-copy .system-title {
            font-size: 10px;
            letter-spacing: 2px;
        }

        /* Make step connector line wider between step 1 and 2 */
        .step-line { max-width: 260px !important; }

        /* Ensure registration form doesn't force a scroll — restore original spacing */
        body.auth-centered-page .right-panel {
            /* keep the original padding/min-height defined above */
            overflow-y: visible !important;
        }

        body.auth-centered-page .right-panel .input-wrap input,
        body.auth-centered-page .right-panel .input-wrap select {
            background: #f7f4ee !important;
            border: 1px solid #ddd7cb !important;
            color: #3b0000 !important;
        }

        body.auth-centered-page .right-panel .input-wrap input::placeholder {
            color: #9a9080 !important;
        }

        body.auth-centered-page .right-panel .input-wrap input:focus,
        body.auth-centered-page .right-panel .input-wrap select:focus {
            background: #fffdf9 !important;
            border-color: #cdbfa9 !important;
        }

        body.auth-centered-page .right-panel .input-wrap .i-icon {
            color: #ef4444 !important;
            z-index: 2;
        }

        @media (max-width: 767px) {
            .auth-brand { flex-direction: column; align-items: center; text-align: center; }
            body.auth-centered-page .right-panel {
                min-height: auto;
                padding: 40px 24px;
            }

            body.auth-centered-page .right-panel.success-state {
                padding: 28px 20px;
            }

            .auth-logo {
                width: 58px;
                height: 58px;
            }

            .auth-brand-copy .brand-name {
                font-size: 20px;
            }
        }
    </style>

    <style>
        /* ── Step indicator ── */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 12px;
        }

        .step-dot {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #f0f0f0;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #aaa;
            transition: all 0.35s;
            position: relative;
            z-index: 1;
        }

        .step-dot.active {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            border-color: #dc2626;
            color: #fff;
            box-shadow: 0 4px 14px rgba(220,38,38,0.35);
        }

        .step-dot.done {
            background: #dcfce7;
            border-color: #16a34a;
            color: #16a34a;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e0e0e0;
            max-width: 80px;
            transition: background 0.35s;
        }

        .step-line.done { background: #16a34a; }

        .step-label-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 0 4px;
        }

        .step-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #bbb;
            text-align: center;
            flex: 1;
            transition: color 0.35s;
        }

        .step-label.active { color: #dc2626; }
        .step-label.done   { color: #16a34a; }

        /* ── Slide panels ── */
        .form-steps-wrapper { overflow: hidden; position: relative; }

        .form-step { display: none; animation: stepIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .form-step.active { display: block; }

        @keyframes stepIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes stepBack {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .form-step.going-back { animation: stepBack 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }

        /* ── Proceed button ── */
        .btn-proceed {
            flex: 1;
            padding: 13px 0;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            border-radius: 12px;
            color: #fff !important;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer !important;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            z-index: 10;
            pointer-events: all !important;
            width: 100%;
        }

        .btn-proceed:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(220,38,38,0.4);
            color: #fff !important;
        }

        /* ── Back step button ── */
        .btn-back-step {
            padding: 13px 20px;
            background: #f5f5f5;
            border: 1.5px solid #e5e5e5;
            border-radius: 12px;
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 10;
            pointer-events: all !important;
        }

        .btn-back-step:hover {
            background: #fee2e2;
            border-color: #fecaca;
            color: #dc2626;
        }

        /* ── Nav button row ── */
        .nav-btn-row {
            display: flex;
            gap: 10px;
            margin-top: 8px;
            position: relative;
            z-index: 10;
        }

        /* ── Fix overlay blocking clicks ── */
        body::before,
        body::after {
            pointer-events: none !important;
        }

        .main-wrapper {
            position: relative;
            z-index: 2 !important;
        }

        #btnProceed,
        .btn-proceed,
        .btn-back-step {
            position: relative !important;
            z-index: 9999 !important;
            pointer-events: all !important;
        }

    </style>
</head>

<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container{{ Session::has('success') ? ' success-state' : '' }}">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
            <div class="orb orb-4"></div>

            <div class="brand-area">
                <div class="logo-wrapper">
                    <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="logo-img">
                    <div>
                        <div class="brand-name">Intern<span>Connect</span> - BETA</div>
                        <div class="system-title">OJT Information Management System</div>
                    </div>
                </div>

                <h1 class="hero-heading">
                    Start your<br>
                    <span>OJT Journey</span><br>
                    Today.
                </h1>

                <p class="hero-desc">
                    Sign up for InternConnect to access a centralized platform designed to support and manage your On-The-Job training activities.
                </p>

                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-user-plus"></i></div>
                        <div class="step-text">
                            <strong>Create your Account</strong>
                            Fill in your personal and academic details
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-file-alt"></i></div>
                        <div class="step-text">
                            <strong>Submit Requirements</strong>
                            Upload and track your OJT documents
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="step-text">
                            <strong>Get Cleared</strong>
                            Complete evaluations and earn your clearance
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel{{ Session::has('success') ? ' success-state' : '' }}">

            @if(Session::has('success'))
                <div class="success-compact">
                    <div class="reg-header" style="text-align:center;">
                        <h2>Registration Complete</h2>
                        <p>Your account has been created successfully.</p>
                    </div>

                    <div class="alert alert-success">{{ Session::get('success') }}</div>

                    <div class="nav-btn-row">
                        <a href="{{ url('/login') }}" class="btn-proceed" style="text-decoration:none;">
                            <i class="fa fa-arrow-right"></i> Proceed to Login
                        </a>
                    </div>
                </div>
            @else
                {{-- header removed as requested --}}

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

                <form action="{{ route('register-user') }}" method="post" id="regForm">
                    @csrf

                    @if(Session::has('fail'))
                        <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                    @endif

                    <div class="form-steps-wrapper">

                    <!-- ═══ STEP 1 — Personal Info ═══ -->
                    <div class="form-step active" id="step1">

                        <div class="reg-header">
                            <h2>Personal Information</h2>
                            <p>Step 1 of 2 — Fill in your Personal Details</p>
                        </div>

                        <div class="fields-grid">

                            <div class="field-group span-2">
                                <label class="form-label">First Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" placeholder="eg. Juan" name="first_name"
                                        id="first_name" value="{{ old('first_name') }}" autocapitalize="words" autocomplete="given-name" spellcheck="false">
                                </div>
                                <span class="text-danger">@error('first_name') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group span-2">
                                <label class="form-label">Middle Name <span style="font-size:11px; color:#888; font-weight:500; margin-left:4px;">(Optional)</span></label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" placeholder="eg. Dela" name="middle_name"
                                        id="middle_name" value="{{ old('middle_name') }}" autocapitalize="words" autocomplete="additional-name" spellcheck="false">
                                </div>
                                <span class="text-danger">@error('middle_name') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group span-2">
                                <label class="form-label">Last Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" placeholder="eg. Cruz" name="last_name"
                                        id="last_name" value="{{ old('last_name') }}" autocapitalize="words" autocomplete="family-name" spellcheck="false">
                                </div>
                                <span class="text-danger">@error('last_name') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group span-3">
                                <label class="form-label">E-mail Address</label>
                                <div class="input-wrap has-bubble">
                                    <i class="fa fa-envelope i-icon"></i>
                                    <input type="text" placeholder="Enter email" name="email"
                                        id="reg_email" value="{{ old('email') }}">
                                    <div id="emailBubble" class="field-bubble"></div>
                                </div>
                                <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group span-3">
                                <label class="form-label">Student No. <span style="font-size:11px; color:#888; font-weight:500; margin-left:4px;">(Format: YYYY-12345-TG-0)</span></label>
                                <div class="input-wrap has-bubble">
                                    <i class="fa fa-id-card i-icon"></i>
                                    <input type="text" placeholder="Enter student number" name="studentNum"
                                        id="studentNum" value="{{ old('studentNum') }}" maxlength="16" autocapitalize="characters" spellcheck="false">
                                    <div id="studentNumBubble" class="field-bubble"></div>
                                </div>
                                <span class="text-danger">@error('studentNum') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group span-3">
                                <label class="form-label">Password <span style="font-size:11px; color:#888; font-weight:500; margin-left:4px;">(8-12 chars, upper/lower/number/!@#$%^&*)</span></label>
                                <div class="input-wrap has-bubble">
                                    <i class="fa fa-lock i-icon"></i>
                                    <input type="password" placeholder="Create password" name="password"
                                        id="reg_password" minlength="8" maxlength="12">
                                    <i class="far fa-eye toggle-pw" id="toggleRegPassword"></i>
                                    <div id="passwordBubble" class="field-bubble"></div>
                                </div>
                                <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group span-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-wrap has-bubble">
                                    <i class="fa fa-lock i-icon"></i>
                                    <input type="password" placeholder="Re-enter password" name="confirm_password"
                                        id="reg_confirm_password" minlength="8" maxlength="12">
                                    <i class="far fa-eye toggle-pw" id="toggleRegConfirmPassword"></i>
                                    <div id="confirmPasswordBubble" class="field-bubble"></div>
                                </div>
                                <span class="text-danger">@error('confirm_password') {{ $message }} @enderror</span>
                            </div>

                        </div>

                        <div class="nav-btn-row">
                            <button type="button"
                                    id="btnProceed"
                                    class="btn-proceed"
                                    onclick="goToStep2()">
                                Proceed to Academic Information &nbsp;<i class="fa fa-arrow-right"></i>
                            </button>
                        </div>

                        <div class="footer-wrap" style="margin-top: 16px;">
                            <a href="login"><i class="fa fa-sign-in-alt"></i> Already Registered? Sign in here</a>
                        </div>

                    </div>

                    <!-- ═══ STEP 2 — Academic Info ═══ -->
                    <div class="form-step" id="step2">

                        <div class="reg-header">
                            <h2>Academic Information</h2>
                            <p>Step 2 of 2 — Fill in your academic details</p>
                        </div>

                        <div class="fields-grid">

                            <div class="field-group">
                                <label class="form-label">Semester</label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-calendar i-icon"></i>
                                    <select id="semester" name="semester">
                                        <option value="1st Sem">1st Sem</option>
                                        <option value="2nd Sem">2nd Sem</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Subject Code</label>
                                <div class="input-wrap">
                                    <i class="fa fa-book i-icon"></i>
                                    <input type="text" name="subject_code" placeholder="Subject code"
                                        value="@foreach($schedules as $schedule)@if($schedule->subject){{ $schedule->subject->subject_code }}@break @endif @endforeach">
                                </div>
                            </div>

                            <div class="field-group full-width">
                                <label class="form-label">Academic Year <small style="color: #e65100; font-weight: normal;"><i class="fa fa-info-circle"></i> Enter the current school year (e.g. 2025–2026)</small></label>
                                <div class="year-row">
                                    <div class="input-wrap has-select">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <select name="academic_year_start" id="academic_year_start" required>
                                            <option value="">Start Year</option>
                                            @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                                <option value="{{ $year }}" {{ old('academic_year_start') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <span class="year-sep">—</span>
                                    <div class="input-wrap has-select">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <select name="academic_year_end" id="academic_year_end" required>
                                            <option value="">End Year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field-group full-width">
                                <label class="form-label">Professor</label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-chalkboard-teacher i-icon"></i>
                                    <select name="adviser_name" id="adviser_name" required>
                                        <option value="">Select Professor</option>
                                        @foreach($data as $professor)
                                            <option value="{{ $professor->full_name }}">{{ $professor->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Course</label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-university i-icon"></i>
                                    <select name="course">
                                        @foreach ($course as $c)
                                            <option value="{{ $c->course }}">{{ $c->course }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Year and Section <span style="font-size:11px; color:#888; font-weight:500; margin-left:4px;">(Format: 4-1)</span></label>
                                <div class="input-wrap has-bubble">
                                    <i class="fa fa-users i-icon"></i>
                                    <input type="text" placeholder="e.g. 4-1" name="year_and_section" id="year_and_section" value="{{ old('year_and_section') }}" maxlength="10" spellcheck="false">
                                    <div id="yearSectionBubble" class="field-bubble"></div>
                                </div>
                                <span class="text-danger">@error('year_and_section') {{ $message }} @enderror</span>
                            </div>

                        </div>

                        <div class="nav-btn-row">
                            <button type="button"
                                    class="btn-back-step"
                                    onclick="goToStep1()">
                                <i class="fa fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn-proceed">
                                <i class="fa fa-user-plus"></i> Create Account
                            </button>
                        </div>

                        <div class="footer-wrap" style="margin-top: 16px;">
                            <a href="login"><i class="fa fa-sign-in-alt"></i> Already Registered? Sign in here</a>
                        </div>

                    </div>

                    </div>
                </form>
            @endif
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let emailAvailabilityStatus = 'idle';
    let emailCheckRequestCounter = 0;

    function evaluatePasswordRequirements(password) {
        const unmet = [];
        if (password.length < 8 || password.length > 12) {
            unmet.push('Use 8 to 12 characters.');
        }
        if (!/[A-Z]/.test(password)) {
            unmet.push('Add an uppercase letter.');
        }
        if (!/[a-z]/.test(password)) {
            unmet.push('Add a lowercase letter.');
        }
        if (!/\d/.test(password)) {
            unmet.push('Add a number.');
        }
        if (!/[!@#$%^&*]/.test(password)) {
            unmet.push('Add one symbol: ! @ # $ % ^ & *.');
        }
        if (/[^A-Za-z\d!@#$%^&*]/.test(password)) {
            unmet.push('Use only these symbols: ! @ # $ % ^ & *.');
        }

        return {
            isValid: unmet.length === 0,
            unmet,
        };
    }

    function showFieldBubble(bubbleId, message) {
        const bubble = document.getElementById(bubbleId);
        if (!bubble) {
            return;
        }

        if (!message) {
            bubble.textContent = '';
            bubble.classList.remove('active');
            return;
        }

        const messages = Array.isArray(message) ? message : [message];
        bubble.innerHTML = messages.map(item => `<div>${item}</div>`).join('');
        bubble.classList.add('active');
    }

    function hideFieldBubble(bubbleId) {
        showFieldBubble(bubbleId, '');
    }

    function getStudentNumberValidationError(value) {
        const trimmed = (value || '').trim().toUpperCase();
        if (!trimmed) {
            return 'Student number is required.';
        }

        if (!/^\d{4}-\d{5}-TG-0$/.test(trimmed)) {
            return 'Use this format: YYYY-12345-TG-0.';
        }

        return '';
    }

    function sanitizeYearAndSectionValue(value) {
        let sanitized = (value || '').replace(/[^\d-]/g, '');
        sanitized = sanitized.replace(/-{2,}/g, '-');

        const firstHyphenIndex = sanitized.indexOf('-');
        if (firstHyphenIndex !== -1) {
            const before = sanitized.slice(0, firstHyphenIndex + 1);
            const after = sanitized.slice(firstHyphenIndex + 1).replace(/-/g, '');
            sanitized = before + after;
        }

        return sanitized;
    }

    function getYearAndSectionValidationError(value) {
        const trimmed = (value || '').trim();
        if (!trimmed) {
            return 'Year and section is required.';
        }

        if (!/^\d+-\d+$/.test(trimmed)) {
            return 'Use this format: 4-1.';
        }

        return '';
    }

    async function goToStep2() {
        if (!document.getElementById('step1') || !document.getElementById('step2')) {
            return;
        }

        const firstName  = document.getElementById('first_name').value.trim();
        const middleName = document.getElementById('middle_name').value.trim();
        const lastName   = document.getElementById('last_name').value.trim();
        const email      = document.getElementById('reg_email').value.trim();
        const studentNum = document.getElementById('studentNum').value.trim();
        const password   = document.getElementById('reg_password').value.trim();
        const confirmPassword = document.getElementById('reg_confirm_password').value.trim();

        const requiredFields = [
            { id: 'first_name',   val: firstName  },
            { id: 'last_name',    val: lastName   },
            { id: 'reg_email',    val: email      },
            { id: 'studentNum',   val: studentNum },
            { id: 'reg_password', val: password   },
            { id: 'reg_confirm_password', val: confirmPassword },
        ];

        let hasError = false;
        requiredFields.forEach(f => {
            const el = document.getElementById(f.id);
            if (!f.val) {
                hasError = true;
                el.style.borderColor = '#dc2626';
                el.style.boxShadow   = '0 0 0 3px rgba(220,38,38,0.1)';
            } else {
                el.style.borderColor = '';
                el.style.boxShadow   = '';
            }
        });

        const passwordValidation = evaluatePasswordRequirements(password);
        const passwordInput = document.getElementById('reg_password');
        const confirmPasswordInput = document.getElementById('reg_confirm_password');
        const emailInput = document.getElementById('reg_email');
        const studentNumInput = document.getElementById('studentNum');
        const studentNumValidationError = getStudentNumberValidationError(studentNum);

        if (!passwordValidation.isValid && passwordInput) {
            hasError = true;
            passwordInput.style.borderColor = '#dc2626';
            passwordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            showFieldBubble('passwordBubble', passwordValidation.unmet);
        } else {
            hideFieldBubble('passwordBubble');
        }

        if (confirmPassword && password !== confirmPassword && confirmPasswordInput) {
            hasError = true;
            confirmPasswordInput.style.borderColor = '#dc2626';
            confirmPasswordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            showFieldBubble('confirmPasswordBubble', 'Password confirmation does not match.');
        } else {
            hideFieldBubble('confirmPasswordBubble');
        }

        if (email && emailInput) {
            const emailCheck = await checkEmailAvailability(email, true);
            if (!emailCheck.available) {
                hasError = true;
                emailInput.style.borderColor = '#dc2626';
                emailInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                showFieldBubble('emailBubble', emailCheck.message);
            } else {
                hideFieldBubble('emailBubble');
            }
        }

        if (studentNumValidationError && studentNumInput) {
            hasError = true;
            studentNumInput.style.borderColor = '#dc2626';
            studentNumInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            showFieldBubble('studentNumBubble', studentNumValidationError);
        } else {
            hideFieldBubble('studentNumBubble');
        }

        if (hasError) return;

        // Switch steps
        const step2 = document.getElementById('step2');
        step2.classList.remove('going-back');
        document.getElementById('step1').classList.remove('active');
        step2.classList.add('active');

        // Update indicators
        const dot1 = document.getElementById('dot1');
        dot1.classList.remove('active');
        dot1.classList.add('done');
        dot1.innerHTML = '<i class="fa fa-check" style="font-size:12px;"></i>';
        document.getElementById('dot2').classList.add('active');
        document.getElementById('line1').classList.add('done');
        document.getElementById('label1').classList.remove('active');
        document.getElementById('label1').classList.add('done');
        document.getElementById('label2').classList.add('active');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function goToStep1() {
        if (!document.getElementById('step1') || !document.getElementById('step2')) {
            return;
        }

        const step2 = document.getElementById('step2');
        step2.classList.add('going-back');
        step2.classList.remove('active');
        document.getElementById('step1').classList.add('active');

        const dot1 = document.getElementById('dot1');
        dot1.classList.add('active');
        dot1.classList.remove('done');
        dot1.innerHTML = '1';
        document.getElementById('dot2').classList.remove('active');
        document.getElementById('line1').classList.remove('done');
        document.getElementById('label1').classList.add('active');
        document.getElementById('label1').classList.remove('done');
        document.getElementById('label2').classList.remove('active');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function sanitizeNameValue(value) {
        let sanitized = (value || '').replace(/[^\p{L}\s'\-]/gu, '');
        sanitized = sanitized.replace(/\s+/g, ' ');
        sanitized = sanitized.replace(/\s*-\s*/g, '-');
        sanitized = sanitized.replace(/\s*'\s*/g, "'");
        sanitized = sanitized.replace(/-{2,}/g, '-');
        sanitized = sanitized.replace(/'{2,}/g, "'");
        sanitized = sanitized.trim();

        return sanitized.replace(/(^|[\s'-])(\p{L})/gu, function (_, separator, character) {
            return separator + character.toUpperCase();
        });
    }

    function getNameValidationError(value, isOptional = false) {
        const trimmed = (value || '').trim();
        if (!trimmed) {
            return isOptional ? '' : 'This field is required.';
        }

        if (!/^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u.test(trimmed)) {
            return "Use letters only. Apostrophes and hyphens are allowed.";
        }

        if (!/^[\p{Lu}]/u.test(trimmed)) {
            return 'Name must start with a capital letter.';
        }

        return '';
    }

    function normalizeNameField(fieldId) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        input.value = sanitizeNameValue(input.value);
    }

    function validateNameFields() {
        const fieldIds = ['first_name', 'middle_name', 'last_name'];

        for (const fieldId of fieldIds) {
            const input = document.getElementById(fieldId);
            if (!input) continue;

            const value = input.value.trim();
            input.setCustomValidity('');
            const isOptional = fieldId === 'middle_name';
            const validationError = getNameValidationError(value, isOptional);

            if (validationError) {
                input.setCustomValidity(validationError);
                input.reportValidity();
                input.style.borderColor = '#dc2626';
                input.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                return false;
            }

            input.style.borderColor = '';
            input.style.boxShadow = '';
        }

        return true;
    }

    async function checkEmailAvailability(email, forceCheck = false) {
        const trimmedEmail = (email || '').trim();

        if (!trimmedEmail) {
            emailAvailabilityStatus = 'idle';
            return { available: false, message: 'Email is required.' };
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(trimmedEmail)) {
            emailAvailabilityStatus = 'invalid';
            return { available: false, message: 'Please enter a valid email address.' };
        }

        if (!forceCheck && emailAvailabilityStatus === 'checking') {
            return { available: false, message: 'Checking email availability...' };
        }

        emailAvailabilityStatus = 'checking';
        const requestId = ++emailCheckRequestCounter;

        try {
            const response = await fetch(`/check-email-availability?email=${encodeURIComponent(trimmedEmail)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const payload = await response.json();

            if (requestId !== emailCheckRequestCounter) {
                return { available: false, message: 'Checking email availability...' };
            }

            emailAvailabilityStatus = payload.available ? 'available' : 'taken';
            return {
                available: Boolean(payload.available),
                message: payload.message || (payload.available ? 'Email is available.' : 'This email is already in use.')
            };
        } catch (error) {
            emailAvailabilityStatus = 'error';
            return { available: false, message: 'Unable to verify email right now. Please try again.' };
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const regForm = document.getElementById('regForm');
        if (!regForm) {
            return;
        }

        const emailInput = document.getElementById('reg_email');
        const studentNumInput = document.getElementById('studentNum');
        const yearAndSectionInput = document.getElementById('year_and_section');
        const toggleRegPassword = document.getElementById('toggleRegPassword');
        const toggleRegConfirmPassword = document.getElementById('toggleRegConfirmPassword');
        const passwordInput = document.getElementById('reg_password');
        const confirmPasswordInput = document.getElementById('reg_confirm_password');
        if (toggleRegPassword) {
            toggleRegPassword.addEventListener('click', function () {
                if (!passwordInput) {
                    return;
                }

                passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        if (toggleRegConfirmPassword) {
            toggleRegConfirmPassword.addEventListener('click', function () {
                if (!confirmPasswordInput) {
                    return;
                }

                confirmPasswordInput.type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        function syncPasswordValidationState() {
            if (!passwordInput || !confirmPasswordInput) {
                return;
            }

            const passwordValidation = evaluatePasswordRequirements(passwordInput.value);
            const hasPasswordValue = passwordInput.value.length > 0;
            const hasConfirmValue = confirmPasswordInput.value.length > 0;
            const passwordsMatch = passwordInput.value === confirmPasswordInput.value;

            if (!hasPasswordValue && !hasConfirmValue) {
                hideFieldBubble('passwordBubble');
                hideFieldBubble('confirmPasswordBubble');
                passwordInput.style.borderColor = '';
                passwordInput.style.boxShadow = '';
                confirmPasswordInput.style.borderColor = '';
                confirmPasswordInput.style.boxShadow = '';
                return;
            }

            if (!passwordValidation.isValid) {
                showFieldBubble('passwordBubble', passwordValidation.unmet);
                passwordInput.style.borderColor = '#dc2626';
                passwordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            } else {
                hideFieldBubble('passwordBubble');
                passwordInput.style.borderColor = '';
                passwordInput.style.boxShadow = '';
            }

            if (hasConfirmValue && !passwordsMatch) {
                showFieldBubble('confirmPasswordBubble', 'Password confirmation does not match.');
                confirmPasswordInput.style.borderColor = '#dc2626';
                confirmPasswordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            } else {
                hideFieldBubble('confirmPasswordBubble');
                confirmPasswordInput.style.borderColor = '';
                confirmPasswordInput.style.boxShadow = '';
            }
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', syncPasswordValidationState);
        }

        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', syncPasswordValidationState);
        }

        const nameFieldIds = ['first_name', 'middle_name', 'last_name'];
        nameFieldIds.forEach(function (fieldId) {
            const input = document.getElementById(fieldId);
            if (!input) return;

            input.addEventListener('input', function () {
                normalizeNameField(fieldId);
                const validationError = getNameValidationError(input.value, fieldId === 'middle_name');
                input.setCustomValidity(validationError);
            });

            input.addEventListener('blur', function () {
                normalizeNameField(fieldId);
                const validationError = getNameValidationError(input.value, fieldId === 'middle_name');
                input.setCustomValidity(validationError);
            });
        });

        if (studentNumInput) {
            studentNumInput.addEventListener('input', function () {
                const validationError = getStudentNumberValidationError(studentNumInput.value);
                studentNumInput.setCustomValidity(validationError);

                if (validationError && studentNumInput.value.trim()) {
                    showFieldBubble('studentNumBubble', validationError);
                    studentNumInput.style.borderColor = '#dc2626';
                    studentNumInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                } else {
                    hideFieldBubble('studentNumBubble');
                    studentNumInput.style.borderColor = '';
                    studentNumInput.style.boxShadow = '';
                }
            });

            studentNumInput.addEventListener('blur', function () {
                const validationError = getStudentNumberValidationError(studentNumInput.value);
                studentNumInput.setCustomValidity(validationError);

                if (validationError && studentNumInput.value.trim()) {
                    showFieldBubble('studentNumBubble', validationError);
                } else {
                    hideFieldBubble('studentNumBubble');
                }
            });
        }

        if (yearAndSectionInput) {
            yearAndSectionInput.addEventListener('input', function () {
                yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
                const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
                yearAndSectionInput.setCustomValidity(validationError);

                if (validationError && yearAndSectionInput.value.trim()) {
                    showFieldBubble('yearSectionBubble', validationError);
                    yearAndSectionInput.style.borderColor = '#dc2626';
                    yearAndSectionInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                } else {
                    hideFieldBubble('yearSectionBubble');
                    yearAndSectionInput.style.borderColor = '';
                    yearAndSectionInput.style.boxShadow = '';
                }
            });

            yearAndSectionInput.addEventListener('blur', function () {
                yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
                const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
                yearAndSectionInput.setCustomValidity(validationError);

                if (validationError && yearAndSectionInput.value.trim()) {
                    showFieldBubble('yearSectionBubble', validationError);
                } else {
                    hideFieldBubble('yearSectionBubble');
                }
            });
        }

        if (emailInput) {
            let emailCheckTimer = null;

            emailInput.addEventListener('input', function () {
                const value = this.value.trim();
                emailAvailabilityStatus = 'idle';

                if (emailCheckTimer) {
                    clearTimeout(emailCheckTimer);
                }

                if (!value) {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                    hideFieldBubble('emailBubble');
                    return;
                }

                emailCheckTimer = setTimeout(async () => {
                    const result = await checkEmailAvailability(value);
                    if (emailInput.value.trim() !== value) {
                        return;
                    }

                    if (!result.available) {
                        this.style.borderColor = '#dc2626';
                        this.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                        showFieldBubble('emailBubble', result.message);
                    } else {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                        hideFieldBubble('emailBubble');
                    }
                }, 350);
            });
        }

        regForm.addEventListener('submit', function (event) {
            nameFieldIds.forEach(normalizeNameField);

            if (passwordInput) {
                const passwordValidation = evaluatePasswordRequirements(passwordInput.value.trim());
                const confirmPasswordValue = confirmPasswordInput ? confirmPasswordInput.value.trim() : '';
                if (!passwordValidation.isValid) {
                    event.preventDefault();
                    passwordInput.style.borderColor = '#dc2626';
                    passwordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showFieldBubble('passwordBubble', passwordValidation.unmet);
                } else {
                    hideFieldBubble('passwordBubble');
                }

                if (confirmPasswordInput && passwordInput.value.trim() !== confirmPasswordValue) {
                    event.preventDefault();
                    confirmPasswordInput.style.borderColor = '#dc2626';
                    confirmPasswordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showFieldBubble('confirmPasswordBubble', 'Password confirmation does not match.');
                } else {
                    hideFieldBubble('confirmPasswordBubble');
                    if (confirmPasswordInput) {
                        confirmPasswordInput.style.borderColor = '';
                        confirmPasswordInput.style.boxShadow = '';
                    }
                }
            }

            if (studentNumInput) {
                const validationError = getStudentNumberValidationError(studentNumInput.value);
                studentNumInput.setCustomValidity(validationError);
                if (validationError) {
                    event.preventDefault();
                    studentNumInput.style.borderColor = '#dc2626';
                    studentNumInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showFieldBubble('studentNumBubble', validationError);
                } else {
                    hideFieldBubble('studentNumBubble');
                    studentNumInput.style.borderColor = '';
                    studentNumInput.style.boxShadow = '';
                }
            }

            if (yearAndSectionInput) {
                yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
                const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
                yearAndSectionInput.setCustomValidity(validationError);
                if (validationError) {
                    event.preventDefault();
                    yearAndSectionInput.style.borderColor = '#dc2626';
                    yearAndSectionInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showFieldBubble('yearSectionBubble', validationError);
                } else {
                    hideFieldBubble('yearSectionBubble');
                    yearAndSectionInput.style.borderColor = '';
                    yearAndSectionInput.style.boxShadow = '';
                }
            }

            if (!validateNameFields()) {
                event.preventDefault();
            }
        });

        const startYearSelect   = document.getElementById('academic_year_start');
        const endYearSelect     = document.getElementById('academic_year_end');
        const semesterSelect    = document.getElementById('semester');
        const adviserNameSelect = document.getElementById('adviser_name');
        const defaultProfessorOptions = adviserNameSelect.innerHTML;

        if (!startYearSelect || !endYearSelect || !semesterSelect || !adviserNameSelect) {
            return;
        }

        function updateEndYearOptions() {
            const selectedStartYear = parseInt(startYearSelect.value);
            endYearSelect.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'End Year';
            endYearSelect.appendChild(defaultOption);

            if (!isNaN(selectedStartYear)) {
                const nextYear = selectedStartYear + 1;
                const option = document.createElement('option');
                option.value = nextYear;
                option.textContent = nextYear;
                endYearSelect.appendChild(option);
                endYearSelect.value = String(nextYear);
            } else {
                endYearSelect.value = '';
            }

            fetchProfessors(semesterSelect.value, startYearSelect.value, endYearSelect.value);
        }

        function fetchProfessors(semester, startYear, endYear) {
            if (!semester || !startYear || !endYear) {
                adviserNameSelect.innerHTML = defaultProfessorOptions;
                return;
            }

            fetch(`/fetch-professors/${semester}/${startYear}/${endYear}`)
                .then(response => response.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        adviserNameSelect.innerHTML = defaultProfessorOptions;
                        return;
                    }

                    adviserNameSelect.innerHTML = '<option value="">Select Professor</option>';
                    data.forEach(professor => {
                        const option = document.createElement('option');
                        option.value = professor;
                        option.textContent = professor;
                        adviserNameSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    adviserNameSelect.innerHTML = defaultProfessorOptions;
                    console.error('Error fetching professors:', error);
                });
        }

        updateEndYearOptions();
        startYearSelect.addEventListener('change', updateEndYearOptions);
        semesterSelect.addEventListener('change', function () {
            fetchProfessors(this.value, startYearSelect.value, endYearSelect.value);
        });
        endYearSelect.addEventListener('change', function () {
            fetchProfessors(semesterSelect.value, startYearSelect.value, this.value);
        });
        fetchProfessors(semesterSelect.value, startYearSelect.value, endYearSelect.value);
    });
</script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>
