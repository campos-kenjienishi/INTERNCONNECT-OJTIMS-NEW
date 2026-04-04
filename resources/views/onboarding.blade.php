<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Onboarding</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <style>
                /* Fix input text color for onboarding */
                .form-step input[type="text"],
                .form-step input[type="number"],
                .form-step input[type="email"],
                .form-step select {
                    color: #fff !important;
                    background: #3b0000 !important;
                }
                .form-step input[disabled],
                .form-step input[readonly] {
                    color: #fff !important;
                    background: #3b0000 !important;
                    opacity: 1;
                }
                .form-step input::placeholder {
                    color: #bbb !important;
                }
                .input-wrap {
                    position: relative;
                }
                .mic-circle {
                    position: absolute;
                    right: 10px;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 32px;
                    height: 32px;
                    background: linear-gradient(135deg, #dc2626, #991b1b);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #fff;
                    font-size: 1.1em;
                    cursor: pointer;
                    z-index: 2;
                    transition: background 0.2s;
                }
                .mic-circle.active {
                    background: #fff;
                    color: #dc2626;
                    border: 2px solid #dc2626;
                }
        /* Step indicator and button styles copied from registration */
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
    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="login-container">
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
                    Welcome<br>
                    <span>Onboard</span><br>
                    to OJT IMS
                </h1>
                <p class="hero-desc">
                    Please complete your registration to access InternConnect.
                </p>
            </div>
        </div>
        <div class="right-panel">
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
                        <div class="reg-header">
                            <h2>Personal Information</h2>
                            <p>Step 1 of 2 — Review your details</p>
                        </div>
                        <div class="fields-grid">
                            <div class="field-group full-width">
                                <label class="form-label">First Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['first_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group full-width">
                                <label class="form-label">Middle Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['middle_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group full-width">
                                <label class="form-label">Last Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" class="form-control" value="{{ $idp['last_name'] ?? '' }}" disabled>
                                </div>
                            </div>
                            <div class="field-group full-width">
                                <label class="form-label">E-mail Address</label>
                                <div class="input-wrap">
                                    <i class="fa fa-envelope i-icon"></i>
                                    <input type="email" class="form-control" value="{{ $idp['email'] ?? '' }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="nav-btn-row">
                            <button type="button" id="btnProceed" class="btn-proceed" onclick="goToStep2()">
                                Proceed to Academic Information &nbsp;<i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <!-- STEP 2: Academic Info -->
                    <div class="form-step" id="step2">
                        <div class="reg-header">
                            <h2>Academic Information</h2>
                            <p>Step 2 of 2 — Fill in your academic details</p>
                        </div>
                        <div class="fields-grid">
                                <div class="field-group">
                                    <label class="form-label">Student Number</label>
                                    <div class="input-wrap">
                                        <i class="fa fa-id-card i-icon"></i>
                                        <input type="text" name="studentNum" class="form-control" required placeholder="Enter student number">
                                    </div>
                                </div>
                            <div class="field-group">
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
                                <div class="field-group full-width">
                                    <label class="form-label">Academic Year</label>
                                    <div class="year-row">
                                        <div class="input-wrap">
                                            <i class="fa fa-calendar-alt i-icon"></i>
                                            <input type="text" name="academic_year_start" class="form-control" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="Start Year" required>
                                        </div>
                                        <span class="year-sep">—</span>
                                        <div class="input-wrap">
                                            <i class="fa fa-calendar-alt i-icon"></i>
                                            <input type="text" name="academic_year_end" class="form-control" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="End Year" required>
                                        </div>
                                    </div>
                                </div>
                            <div class="field-group full-width">
                                <label class="form-label">Professor</label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-chalkboard-teacher i-icon"></i>
                                    <select name="adviser_name" class="form-control" required>
                                        <option value="">Select Professor</option>
                                        @foreach($professors as $prof)
                                            <option value="{{ $prof->full_name }}">{{ $prof->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="form-label">Course</label>
                                <div class="input-wrap has-select">
                                    <i class="fa fa-university i-icon"></i>
                                    <select name="course" class="form-control" required>
                                        @foreach($courses as $c)
                                            <option value="{{ $c->course }}">{{ $c->course }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="form-label">Year and Section</label>
                                <div class="input-wrap">
                                    <i class="fa fa-users i-icon"></i>
                                    <input type="text" name="year_and_section" class="form-control" required placeholder="e.g. 4-1">
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
</script>
</body>
</html>

