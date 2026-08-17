<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Onboarding</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/registration-responsive.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body.auth-centered-page {
            background: #3b0000 url('/images/20 Blur.png') no-repeat center center fixed !important;
            background-size: cover !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 16px;
        }

        body.auth-centered-page .login-container {
            max-width: 840px;
            width: 100%;
            min-height: auto;
            background: linear-gradient(135deg, rgba(26, 0, 0, 0.92), rgba(60, 0, 0, 0.92)) !important;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        body.auth-centered-page .left-panel {
            display: none !important;
        }

        body.auth-centered-page .right-panel {
            width: 100%;
            flex: 1 1 auto;
            min-height: auto;
            padding: 34px 38px;
            color: #ffffff !important;
            justify-content: center;
        }

        .auth-brand {
            display: flex;
            flex-direction: row;
            align-items: center;
            text-align: left;
            gap: 14px;
            margin-bottom: 16px;
            justify-content: center;
        }

        .auth-logo {
            width: 52px;
            height: 52px;
            object-fit: contain;
            filter: drop-shadow(0 0 12px rgba(255,255,255,0.25));
        }

        .auth-brand-copy .brand-name {
            font-size: 22px;
            margin-bottom: 0px;
            text-align: left;
            font-weight: 800;
            color: #ffffff !important;
            line-height: 1.1;
        }

        .auth-brand-copy .brand-name span { color: #fca5a5; }

        .auth-brand-copy .system-title {
            font-size: 10px;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.7) !important;
            text-transform: uppercase;
        }

        .reg-header h2 {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 2px;
        }

        .reg-header p {
            color: #fca5a5 !important;
            font-size: 13px;
        }

        .form-label {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .step-label {
            color: rgba(255,255,255,0.6) !important;
            font-weight: 500;
            font-size: 11.5px;
        }
        .step-label.active {
            color: #fca5a5 !important;
            font-weight: 700;
        }

        .step-line { max-width: 240px !important; }

        .input-wrap {
            position: relative !important;
        }

        .input-wrap .i-icon {
            position: absolute !important;
            left: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #ef4444 !important;
            font-size: 15px !important;
            z-index: 10 !important;
            pointer-events: none !important;
            display: inline-block !important;
        }

        body.auth-centered-page .right-panel .input-wrap input,
        body.auth-centered-page .right-panel .input-wrap select {
            background: #f7f4ee !important;
            border: 1px solid #ddd7cb !important;
            color: #3b0000 !important;
            border-radius: 12px;
            padding-left: 44px !important;
            height: 48px;
            font-size: 14px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        body.auth-centered-page .right-panel .input-wrap input[disabled],
        body.auth-centered-page .right-panel .input-wrap input[readonly] {
            background: #f7f4ee !important;
            color: #3b0000 !important;
            opacity: 0.95;
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
            left: 14px;
        }

        /* Alert Styling */
        .alert-info-custom {
            background: rgba(30, 144, 255, 0.18) !important;
            border: 1px solid rgba(147, 197, 253, 0.5) !important;
            color: #bfdbfe !important;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
        }

        .alert-success-custom {
            background: rgba(22, 163, 74, 0.18) !important;
            border: 1px solid rgba(134, 239, 172, 0.5) !important;
            color: #bbf7d0 !important;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
        }

        /* Fix double dropdown arrows */
        .input-wrap.has-select::after {
            display: none !important;
        }

        /* Select2 Light Theme styling for onboarding */
        .select2-container--default .select2-selection--single {
            background-color: #f7f4ee !important;
            border: 1px solid #ddd7cb !important;
            border-radius: 12px !important;
            height: 48px !important;
            padding: 8px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #3b0000 !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 14px !important;
            line-height: 28px !important;
            padding-left: 28px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #ef4444 transparent transparent transparent !important;
            border-width: 6px 5px 0 5px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #ef4444 transparent !important;
            border-width: 0 5px 6px 5px !important;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #ddd7cb !important;
            color: #3b0000 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            z-index: 9999 !important;
        }
        .select2-search__field {
            background-color: #f7f4ee !important;
            color: #3b0000 !important;
            border: 1px solid #ddd7cb !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            font-family: 'Poppins', sans-serif !important;
        }
        .select2-results__option {
            color: #3b0000 !important;
            font-size: 13.5px !important;
            font-family: 'Poppins', sans-serif !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #dc2626 !important;
            color: #ffffff !important;
        }

        .input-wrap { position: relative; }
        .btn-proceed {
            padding: 14px 24px;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
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
        .nav-btn-row {
            display: flex;
            gap: 10px;
            margin-top: 8px;
            position: relative;
            z-index: 10;
        }
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
    </style>
</head>
<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container">
        <div class="right-panel">
            <div class="auth-brand d-flex flex-row align-items-center justify-content-center mb-3" style="gap: 16px; margin: 0 auto;">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="auth-logo" style="width: 54px; height: 54px; flex-shrink: 0; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">
                <div class="auth-brand-copy" style="text-align: left;">
                    <div class="brand-name" style="font-size: 22px; font-weight: 800; color: #ffffff !important; line-height: 1.1; margin: 0;">Intern<span style="color: #fca5a5;">Connect</span> <span style="font-size: 10px; font-weight: 700; background: rgba(220,38,38,0.4); color: #fca5a5; padding: 2px 8px; border-radius: 12px; border: 1px solid rgba(220,38,38,0.5); vertical-align: middle; margin-left: 4px;">BETA</span></div>
                    <div class="system-title" style="font-size: 10px; letter-spacing: 1.8px; color: rgba(255,255,255,0.75) !important; text-transform: uppercase; margin-top: 2px;">OJT Information Management System</div>
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
                            <div class="alert alert-success-custom d-flex align-items-center mb-3" style="background: rgba(22, 163, 74, 0.18) !important; border: 1px solid rgba(134, 239, 172, 0.5) !important; color: #bbf7d0 !important;">
                                <i class="fa fa-check-circle me-2" style="font-size: 18px; color: #86efac !important;"></i>
                                <div style="color: #bbf7d0 !important;">
                                    <strong style="color: #ffffff !important;">Guidance System (GuiSIS) Match:</strong> Your academic record was auto-found! Details have been pre-filled for verification.
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info-custom d-flex align-items-center mb-3" style="background: rgba(30, 144, 255, 0.18) !important; border: 1px solid rgba(147, 197, 253, 0.5) !important; color: #bfdbfe !important;">
                                <i class="fa fa-info-circle me-2" style="font-size: 18px; color: #93c5fd !important;"></i>
                                <div style="color: #bfdbfe !important;">
                                    <strong style="color: #ffffff !important;">Manual Registration:</strong> Record not found in Guidance System (GuiSIS). Please complete your profile details manually.
                                </div>
                            </div>
                        @endif
                        <div class="reg-header mb-3">
                            <h2>Personal Information</h2>
                            <p>Step 1 of 2 — Review your details</p>
                        </div>
                        <div class="fields-grid">
                            <div class="field-group span-3">
                                <label class="form-label">First Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['first_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Middle Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['middle_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Last Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['last_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">E-mail Address</label>
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
                        <div class="fields-grid">
                            <div class="field-group span-3">
                                <label class="form-label">Student Number</label>
                                <div class="input-wrap">
                                    <i class="fa fa-id-card i-icon"></i>
                                    <input type="text" name="studentNum" class="form-control" required placeholder="Enter student number" value="{{ old('studentNum', $guisisData['student_number'] ?? $guisisData['studentNum'] ?? '') }}">
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Semester</label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-calendar i-icon"></i>
                                    <select name="semester" class="form-control" required>
                                        <option value="1st Sem">1st Sem</option>
                                        <option value="2nd Sem">2nd Sem</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Academic Year</label>
                                <div class="year-row d-flex gap-2 align-items-center">
                                    <div class="input-wrap flex-grow-1">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <input type="text" name="academic_year_start" class="form-control" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="Start Year" required value="{{ date('Y') }}">
                                    </div>
                                    <span class="year-sep" style="color: #666; font-weight: 700;">—</span>
                                    <div class="input-wrap flex-grow-1">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <input type="text" name="academic_year_end" class="form-control" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="End Year" required value="{{ date('Y') + 1 }}">
                                    </div>
                                </div>
                            </div>
                            <div class="field-group span-3">
                                <label class="form-label">Year and Section</label>
                                <div class="input-wrap">
                                    <i class="fa fa-users i-icon"></i>
                                    <input type="text" name="year_and_section" class="form-control" required placeholder="e.g. 4-1" value="{{ old('year_and_section', $guisisData['year_and_section'] ?? $guisisData['year_section'] ?? '') }}">
                                </div>
                            </div>
                            @php
                                $fetchedCourse = (is_array($guisisData['program'] ?? null) ? ($guisisData['program']['name'] ?? $guisisData['program']['program'] ?? '') : ($guisisData['program'] ?? ''))
                                    ?: (is_array($guisisData['course'] ?? null) ? ($guisisData['course']['course'] ?? $guisisData['course']['name'] ?? '') : ($guisisData['course'] ?? ''));
                            @endphp
                            <div class="field-group span-3">
                                <label class="form-label">Course</label>
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
                                <label class="form-label">Professor</label>
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
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
<script>
    function goToStep2() {
        // No required fields in step 1 for onboarding (all disabled)
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

    $(document).ready(function() {
        if ($.fn.select2) {
            $('select[name="adviser_name"]').select2({
                placeholder: 'Search or Select Professor...',
                allowClear: true,
                width: '100%'
            });
            $('select[name="course"]').select2({
                placeholder: 'Select Course...',
                width: '100%'
            });
            $('select[name="semester"]').select2({
                placeholder: 'Select Semester...',
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }
    });
</script>
</body>
</html>

