<!DOCTYPE html>
<html lang="en">
<head>
    
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">

    <link rel="stylesheet" href="/frontend/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>

<body>
    <div class="card">
        <img src="/images/final-puptg_logo-ojtims_nbg.png">
        <br>

        <div class="container">
            <div class="row">
            
                <!-- <h2>On-<span>t</span>he-<span>j</span>ob<span> T</span>raining<span> I</span>nformation<span> M</span>anagement<span> S</span>ystem</h2> -->
                <h2>InternConnect<br>OJT INFORMATION MANAGEMENT SYSTEM</h2>
                <h4>Registration</h4>
                
                <form action="{{route('register-user')}}" method="post">

                    @if(Session::has('success'))
                    <div class="alert alert-success">{{Session::get('success')}}</div>
                    @endif
                    @if(Session::has('fail'))
                    <div class="alert alert-danger">{{Session::get('fail')}}</div>
                    @endif

                    @csrf

                    <br>
                    <label for="first_name">First Name</label>
                    <div class="form-group">
                        
                        <input type="text" class="form-control" placeholder="Enter First Name"
                        name="first_name" value = "{{old('first_name')}}">
                        <span class="text-danger">@error('first_name') {{$message}} @enderror</span>
                    </div>
                    
                    <label for="middle_name">Middle Name</label>
                    <div class="form-group">
                        
                        <input type="text" class="form-control" placeholder="Enter Middle Name"
                        name="middle_name" value = "{{old('middle_name')}}">
                        <span class="text-danger">@error('middle_name') {{$message}} @enderror</span>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" placeholder="Enter Last Name"
                        name="last_name" value = "{{old('last_name')}}">
                        <span class="text-danger">@error('last_name') {{$message}} @enderror</span>
                    </div>
                    

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="text" class="form-control" placeholder="Enter Email"
                        name="email" value = "{{old('email')}}">
                        <span class="text-danger">@error('email') {{$message}} @enderror</span>
                    </div>

                    <div class="form-group">
                        <label for="studentNum">Student No.</label>
                        <input type="text" class="form-control" placeholder="Enter Student No."
                        name="studentNum" value = "">
                        <span class="text-danger">@error('studentNum') {{$message}} @enderror</span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password">
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        <span class="text-danger">@error('password') {{$message}} @enderror</span>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const togglePassword = document.getElementById('togglePassword');
                        const password = document.getElementById('password');

                        togglePassword.addEventListener('click', function () {
                            // Toggle the type attribute
                            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                            password.setAttribute('type', type);

                            // Toggle the eye / eye-slash icon
                            this.querySelector('i').classList.toggle('fa-eye-slash');
                        });
                    });
                    </script>

                    <div class="form-group" >
                        <label class="form-label" for="semester">Semester:</label>
                        <select class="form-select" id="semester" name="semester">
                            <option value="1st Sem">1st Sem</option>
                            <option value="2nd Sem">2nd Sem</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="academic_year">Academic Year*:</label>
                        <div class="input-group">
                            <select class="form-select" name="academic_year_start" id="academic_year_start" required>
                                <!-- Default blank option -->
                                <option value="">Select Year</option>
                                <!-- Generate options for the start year dropdown -->
                                @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <span>-</span>
                            <select class="form-select" name="academic_year_end" id="academic_year_end" required>
                                <!-- Default blank option -->
                                <option value="">Select Year</option>
                                <!-- Options for the end year dropdown will be updated dynamically via JavaScript -->
                            </select>
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
                    
                    <div class="form-group">
                        <label for="subject_code">Subject Code</label>
                        <input type="text" name="subject_code" class="form-control"
                            value="@foreach($schedules as $schedule)@if($schedule->subject){{ $schedule->subject->subject_code }}@break @endif @endforeach">
                    </div>

                    <div class="form-group">
                        <label for="adviser_name">Professor</label>
                        <select name="adviser_name" class="form-control" required>
                            <option value="">Select Professor</option>
                            @foreach($data as $professor)
                                <option value="{{ $professor->full_name }}">{{ $professor->full_name }}</option>
                            @endforeach
                        </select>
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
                    
                    <div class="form-group">
                        <label for="course">Course</label>
                        <select name="course" class="form-control">
                            @foreach ($course as $course)
                            <option value="{{$course->course}}">{{$course->course}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="year_and_section">Year and Section</label>
                        <input type="text" class="form-control" placeholder="Enter Year and Section"
                        name="year_and_section" value = "">
                    </div>

                    <br>

                    <div class="form-group">
                        <button class="btn btn-block btn-primary" type="submit">Register</button>
                    </div>

                    <br>
                    <a href="login">Already Registered? Login Here...</a>
                </form>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</html>