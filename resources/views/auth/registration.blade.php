<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <!-- CRITICAL: Prevents white flash -->
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
                        <div class="brand-name">Intern<span>Connect</span></div>
                        <div class="system-title">OJT Information Management System</div>
                    </div>
                </div>

                <h1 class="hero-heading">
                    Start your<br>
                    <span>OJT journey</span><br>
                    today.
                </h1>

                <p class="hero-desc">
                    Create your InternConnect account to access document submissions, DTR tracking, supervisor evaluations, and clearance processing — all in one place.
                </p>

                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-user-plus"></i></div>
                        <div class="step-text">
                            <strong>Create your account</strong>
                            Fill in your personal and academic details
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-file-alt"></i></div>
                        <div class="step-text">
                            <strong>Submit requirements</strong>
                            Upload and track your OJT documents
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="step-text">
                            <strong>Get cleared</strong>
                            Complete evaluations and earn your clearance
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="reg-header">
                <h2>Create Account ✍️</h2>
                <p>Fill in the details below to register as a student</p>
            </div>

            <form action="{{route('register-user')}}" method="post">
                @csrf

                @if(Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if(Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <!-- PERSONAL INFO -->
                <div class="section-label"><i class="fa fa-user me-1"></i> Personal Information</div>

                <div class="fields-grid">

                    <div class="field-group">
                        <label class="form-label">First Name</label>
                        <div class="input-wrap">
                            <i class="fa fa-user i-icon"></i>
                            <input type="text" placeholder="First name" name="first_name" value="{{ old('first_name') }}">
                        </div>
                        <span class="text-danger">@error('first_name') {{ $message }} @enderror</span>
                    </div>

                    <div class="field-group">
                        <label class="form-label">Middle Name</label>
                        <div class="input-wrap">
                            <i class="fa fa-user i-icon"></i>
                            <input type="text" placeholder="Middle name" name="middle_name" value="{{ old('middle_name') }}">
                        </div>
                        <span class="text-danger">@error('middle_name') {{ $message }} @enderror</span>
                    </div>

                    <div class="field-group">
                        <label class="form-label">Last Name</label>
                        <div class="input-wrap">
                            <i class="fa fa-user i-icon"></i>
                            <input type="text" placeholder="Last name" name="last_name" value="{{ old('last_name') }}">
                        </div>
                        <span class="text-danger">@error('last_name') {{ $message }} @enderror</span>
                    </div>

                    <div class="field-group">
                        <label class="form-label">E-mail Address</label>
                        <div class="input-wrap">
                            <i class="fa fa-envelope i-icon"></i>
                            <input type="text" placeholder="Enter email" name="email" value="{{ old('email') }}">
                        </div>
                        <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                    </div>

                    <div class="field-group">
                        <label class="form-label">Student No.</label>
                        <div class="input-wrap">
                            <i class="fa fa-id-card i-icon"></i>
                            <input type="text" placeholder="Student number" name="studentNum">
                        </div>
                        <span class="text-danger">@error('studentNum') {{ $message }} @enderror</span>
                    </div>

                    <div class="field-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrap">
                            <i class="fa fa-lock i-icon"></i>
                            <input type="password" placeholder="Create password" name="password" id="reg_password">
                            <i class="far fa-eye toggle-pw" id="toggleRegPassword"></i>
                        </div>
                        <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                    </div>

                </div>

                <!-- ACADEMIC INFO -->
                <div class="section-label"><i class="fa fa-graduation-cap me-1"></i> Academic Information</div>

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
                                        <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const startYearSelect = document.getElementById('academic_year_start');
                            const endYearSelect = document.getElementById('academic_year_end');
                            
                            // Function to update end year options based on selected start year
                            function updateEndYearOptions() {
                                const selectedStartYear = parseInt(startYearSelect.value);
                                const selectedEndYear = parseInt(endYearSelect.value);
                                endYearSelect.innerHTML = ''; // Clear existing options
                                
                                // Add default blank option
                                const defaultOption = document.createElement('option');
                                defaultOption.value = '';
                                defaultOption.textContent = 'Select Year';
                                endYearSelect.appendChild(defaultOption);
                                
                                for (let year = selectedStartYear + 1; year <= (selectedStartYear + 10); year++) {
                                    const option = document.createElement('option');
                                    option.value = year;
                                    option.textContent = year;
                                    endYearSelect.appendChild(option);
                                }
                                
                                // If end year is less than or equal to start year, select the default option
                                if (selectedEndYear <= selectedStartYear) {
                                    endYearSelect.value = '';
                                }
                            }
                            
                            // Initial update of end year options based on default start year
                            updateEndYearOptions();
                            
                            // Add event listener to start year dropdown to update end year options
                            startYearSelect.addEventListener('change', updateEndYearOptions);
                        });
                    </script>

                    <div class="field-group full-width">
                        <label class="form-label">Professor</label>
                        <div class="input-wrap has-select">
                            <i class="fa fa-chalkboard-teacher i-icon"></i>
                            <select name="adviser_name"  required>
                                <option value="">Select Professor</option>
                                @foreach($data as $professor)
                                    <option value="{{ $professor->full_name }}">{{ $professor->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        const semesterSelect = document.getElementById('semester');
                        const startYearSelect = document.getElementById('academic_year_start');
                        const endYearSelect = document.getElementById('academic_year_end');
                        const adviserNameSelect = document.getElementById('adviser_name');

                        // Function to fetch professors based on semester and academic year
                        function fetchProfessors(semester, startYear, endYear) {
                            // Send AJAX request to fetch professors based on semester and academic year
                            fetch(`/fetch-professors/${semester}/${startYear}/${endYear}`)
                                .then(response => response.json())
                                .then(data => {
                                    // Clear existing options
                                    adviserNameSelect.innerHTML = '';

                                    // Add default option
                                    const defaultOption = document.createElement('option');
                                    defaultOption.value = '';
                                    defaultOption.textContent = 'Select Professor';
                                    adviserNameSelect.appendChild(defaultOption);

                                    // Add fetched professors as options
                                    data.forEach(professor => {
                                        const option = document.createElement('option');
                                        option.value = professor;
                                        option.textContent = professor;
                                        adviserNameSelect.appendChild(option);
                                    });
                                })
                                .catch(error => {
                                    console.error('Error fetching professors:', error);
                                });
                        }

                        // Add event listeners to semester and academic year dropdowns
                        semesterSelect.addEventListener('change', function() {
                            fetchProfessors(this.value, startYearSelect.value, endYearSelect.value);
                        });

                        startYearSelect.addEventListener('change', function() {
                            fetchProfessors(semesterSelect.value, this.value, endYearSelect.value);
                        });

                        endYearSelect.addEventListener('change', function() {
                            fetchProfessors(semesterSelect.value, startYearSelect.value, this.value);
                        });

                        // Initial fetch when page loads
                        fetchProfessors(semesterSelect.value, startYearSelect.value, endYearSelect.value);
                    });
                    </script>

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
                            <input type="text" placeholder="e.g. 4-A" name="year_and_section">
                        </div>
                    </div>

                </div>

                <div class="btn-wrap">
                    <button type="submit" class="btn-register">
                        <i class="fa fa-user-plus me-2"></i> Create Account
                    </button>
                </div>

                <div class="footer-wrap">
                    <a href="login"><i class="fa fa-sign-in-alt"></i> Already registered? Sign in here</a>
                </div>

            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    document.getElementById('toggleRegPassword').addEventListener('click', function () {
        const input = document.getElementById('reg_password');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        this.classList.remove('toggled');
        void this.offsetWidth;
        this.classList.add('toggled');
    });
</script>
</body>
</html>