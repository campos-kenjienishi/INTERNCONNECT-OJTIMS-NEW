<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Registration</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">

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

<body>
<div class="main-wrapper">
    <div class="login-container">

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

            <form action="{{ route('register-user') }}" method="post" id="regForm">
                @csrf

                @if(Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
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

                            <div class="field-group">
                                <label class="form-label">First Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" placeholder="eg. Juan" name="first_name"
                                        id="first_name" value="{{ old('first_name') }}">
                                </div>
                                <span class="text-danger">@error('first_name') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Middle Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" placeholder="eg. Dela" name="middle_name"
                                        id="middle_name" value="{{ old('middle_name') }}">
                                </div>
                                <span class="text-danger">@error('middle_name') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Last Name</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user i-icon"></i>
                                    <input type="text" placeholder="eg. Cruz" name="last_name"
                                        id="last_name" value="{{ old('last_name') }}">
                                </div>
                                <span class="text-danger">@error('last_name') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group">
                                <label class="form-label">E-mail Address</label>
                                <div class="input-wrap">
                                    <i class="fa fa-envelope i-icon"></i>
                                    <input type="text" placeholder="Enter email" name="email"
                                        id="reg_email" value="{{ old('email') }}">
                                </div>
                                <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Student No.</label>
                                <div class="input-wrap">
                                    <i class="fa fa-id-card i-icon"></i>
                                    <input type="text" placeholder="202X-000XX-TG-0" name="studentNum"
                                        id="studentNum">
                                </div>
                                <span class="text-danger">@error('studentNum') {{ $message }} @enderror</span>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Password</label>
                                <div class="input-wrap">
                                    <i class="fa fa-lock i-icon"></i>
                                    <input type="password" placeholder="Create password" name="password"
                                        id="reg_password">
                                    <i class="far fa-eye toggle-pw" id="toggleRegPassword"></i>
                                </div>
                                <span class="text-danger">@error('password') {{ $message }} @enderror</span>
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
                                <label class="form-label">Academic Year</label>
                                <div class="year-row">
                                    <div class="input-wrap has-select">
                                        <i class="fa fa-calendar-alt i-icon"></i>
                                        <select name="academic_year_start" id="academic_year_start" required>
                                            <option value="">Start Year</option>
                                            @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                                <option value="{{ $year }}">{{ $year }}</option>
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
                                    <select name="adviser_name"required>
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
                                <label class="form-label">Year and Section</label>
                                <div class="input-wrap">
                                    <i class="fa fa-users i-icon"></i>
                                    <input type="text" placeholder="e.g. 4-1" name="year_and_section">
                                </div>
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
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

    function goToStep2() {
        const firstName  = document.getElementById('first_name').value.trim();
        const middleName = document.getElementById('middle_name').value.trim();
        const lastName   = document.getElementById('last_name').value.trim();
        const email      = document.getElementById('reg_email').value.trim();
        const studentNum = document.getElementById('studentNum').value.trim();
        const password   = document.getElementById('reg_password').value.trim();

        const requiredFields = [
            { id: 'first_name',   val: firstName  },
            { id: 'last_name',    val: lastName   },
            { id: 'reg_email',    val: email      },
            { id: 'studentNum',   val: studentNum },
            { id: 'reg_password', val: password   },
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

    document.addEventListener("DOMContentLoaded", function () {

        document.getElementById('toggleRegPassword').addEventListener('click', function () {
            const input = document.getElementById('reg_password');
            input.type = input.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        const startYearSelect   = document.getElementById('academic_year_start');
        const endYearSelect     = document.getElementById('academic_year_end');
        const semesterSelect    = document.getElementById('semester');
        const adviserNameSelect = document.getElementById('adviser_name');

        function updateEndYearOptions() {
            const selectedStartYear = parseInt(startYearSelect.value);
            const selectedEndYear   = parseInt(endYearSelect.value);
            endYearSelect.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'End Year';
            endYearSelect.appendChild(defaultOption);

            if (!isNaN(selectedStartYear)) {
                for (let year = selectedStartYear + 1; year <= (selectedStartYear + 10); year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    endYearSelect.appendChild(option);
                }
            }

            if (selectedEndYear <= selectedStartYear) endYearSelect.value = '';
            fetchProfessors(semesterSelect.value, startYearSelect.value, endYearSelect.value);
        }

        function fetchProfessors(semester, startYear, endYear) {
            if (!semester || !startYear || !endYear) return;
            fetch(`/fetch-professors/${semester}/${startYear}/${endYear}`)
                .then(response => response.json())
                .then(data => {
                    adviserNameSelect.innerHTML = '<option value="">Select Professor</option>';
                    data.forEach(professor => {
                        const option = document.createElement('option');
                        option.value = professor;
                        option.textContent = professor;
                        adviserNameSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching professors:', error));
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
</body>
</html>