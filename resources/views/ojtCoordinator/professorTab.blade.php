<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png">
                        <span class="toptitle">InternConnect</span>
                    </a>

  

                </li>

                <a href="{{ url('/accountinfo') }}" style="text-decoration: none;">
                    <span class="iconname">
                        <ion-icon name="person-circle-outline"></ion-icon>
                    </span>
                    <span class="name"> {{ $user->full_name }} </span>
                    <span class="name2">OJT COORDINATOR </span>
                </a>

                <a href="{{ url('/accountinfo') }}" style="text-decoration: none;">
                    <span class="hidden-on-big">{{ $user->full_name }}</span>
                    <!-- <div class="toggle" id="toggle2">
                        <ion-icon name="menu-outline"></ion-icon>
                    </div> -->
                </a>


                <li>
                    <a href="{{ url('/dashboard') }}">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>



                <li>
                    <a href="{{ url('/studentLists') }}">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Students</span>
                    </a>
                </li>

                <li class="active">
                    <a href="{{ url('/professorTab') }}">
                        <span class="icon">
                            <ion-icon name="people-circle-outline"></ion-icon>
                        </span>
                        <span class="title">Professors</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/uploadpage') }}">
                        <span class="icon">
                            <ion-icon name="document-outline"></ion-icon>
                        </span>
                        <span class="title">Upload Templates</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/maintenance') }}">
                        <span class="icon">
                            <ion-icon name="code-working-outline"></ion-icon>
                        </span>
                        <span class="title">Maintenance</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/MOA') }}">
                        <span class="icon">
                            <ion-icon name="folder-outline"></ion-icon>
                        </span>
                        <span class="title">MOA</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/reports') }}">
                        <span class="icon">
                            <ion-icon name="cellular-outline"></ion-icon>
                        </span>
                        <span class="title">Reports</span>
                        <span class="icon" style="margin-left: 30%; font-size: 22px;">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </span>
                    </a>
                </li>



                <li>
                    <a href="{{ url('/login') }}">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            

            <div class="topbar">

                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <span class="subtitle">On-the-Job Training Information Management System </span>
                
            </div>

            <div class="dash">
                <h1>Professors</h1>
            </div>

            <!-- ================ Order Details List ================= -->
            <div class="buttons"style="margin-left: 70%;">
                <div class="AddProfBtn">
                    <button class="updateBtn" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add New Professor
                    </button>
                    <i class="uil uil-plus" style="font-size: 15px;"></i>
                </div>
            </div>
  
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Professor</h1>
                        </div>

                        <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                           <!-- Professor Creation Form -->
                            <form action="{{url('/professorCreate')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="full_name">Professor Name:</label>
                                    <div class="name-inputs">
                                        <input class="form-input2" type="text" name="first_name" placeholder="Enter First Name">
                                        <input class="form-input2" type="text" name="last_name" placeholder="Enter Last Name">
                                    </div>
                                </div>

                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="email">E-mail Address:</label>
                                    <input class="form-input" type="text" name="email" placeholder="Enter E-mail Address">
                                </div>

                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="subject_code">Subject Code:</label>
                                    <input class="form-input" type="text" name="subject_code" placeholder="Enter Subject Code">
                                </div>

                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="subject_description">Subject Description:</label>
                                    <input class="form-input" type="text" name="subject_description" placeholder="Enter Subject Description">
                                </div>

                                <!-- Additional input fields for semester, academic year, schedule day, and time -->
                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="semester">Semester:</label>
                                    <select class="form-select" name="semester">
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
                                    <div class="error-message" id="academic_year-error">Please select the academic year.</div>
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

                                <div class="form-group schedule-day-group" style="font-size: 22px;">
                                    <label class="form-label">Schedule Day:</label>
                                    <div class="checkboxes">
                                        <input type="checkbox" id="monday" name="schedule_day[]" value="Monday">
                                        <label style="font-size: 16px;" for="monday">Monday</label><br>
                                        <input type="checkbox" id="tuesday" name="schedule_day[]" value="Tuesday">
                                        <label style="font-size: 16px;" for="tuesday">Tuesday</label><br>
                                        <input style="font-size: 16px;" type="checkbox" id="wednesday" name="schedule_day[]" value="Wednesday">
                                        <label style="font-size: 16px;" for="wednesday">Wednesday</label><br>
                                        <input type="checkbox" id="thursday" name="schedule_day[]" value="Thursday">
                                        <label style="font-size: 16px;" for="thursday">Thursday</label><br>
                                        <input type="checkbox" id="friday" name="schedule_day[]" value="Friday">
                                        <label style="font-size: 16px;" for="friday">Friday</label><br>
                                        <input type="checkbox" id="saturday" name="schedule_day[]" value="Saturday">
                                        <label style="font-size: 16px;" for="saturday">Saturday</label><br>
                                        <input type="checkbox" id="sunday" name="schedule_day[]" value="Sunday">
                                        <label style="font-size: 16px;" for="sunday">Sunday</label><br>
                                    </div>
                                </div>

                                
                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="time_slots">Number of Time Slots:</label>
                                    <select class="form-select" id="time_slots" name="time_slots">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        
                                    </select>
                                </div>

                                <div id="timeInputs">
                                    <!-- Start and end time inputs will be dynamically added here -->
                                </div>

                                <script>
                                    $(document).ready(function() {
                                    // Hide time inputs by default
                                    $('#timeInputs').hide();

                                    // Show time inputs based on the selected number of time slots
                                    $('#time_slots').change(function() {
                                        var numSlots = parseInt($(this).val());
                                        $('#timeInputs').empty(); // Clear previously added time inputs
                                        if (numSlots > 0) {
                                            $('#timeInputs').show();
                                            $('input[name="schedule_day[]"]:checked').each(function() {
                                                var day = $(this).val();
                                                for (var i = 1; i <= numSlots; i++) {
                                                    $('#timeInputs').append('<div class="time-inputs">' +
                                                        '<label class="form-label" for="' + day + '_start_time_' + i + '">Start Time ' + i + ' (' + day + '):</label>' +
                                                        '<input class="form-input start-time" type="time" name="' + day + '_start_time_' + i + '" id="' + day + '_start_time_' + i + '" placeholder="Enter Start Time">' +
                                                        '<label class="form-label" for="' + day + '_end_time_' + i + '">End Time ' + i + ' (' + day + '):</label>' +
                                                        '<input class="form-input end-time" type="time" name="' + day + '_end_time_' + i + '" id="' + day + '_end_time_' + i + '" placeholder="Enter End Time">' +
                                                        '</div>');
                                                }
                                            });
                                        } else {
                                            $('#timeInputs').hide();
                                        }
                                    });
                                });

                                </script>


                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="course">Course</label>
                                    <select name="course" class="form-control">
                                        @foreach ($course as $course)
                                        <option value="{{$course->course}}">{{$course->course}}</option>
                                        @endforeach
                                    </select>
                                </div>
                               
                               
                                <!-- Submit and Close Buttons -->
                                
                                <div class="row mt-3">
                                    <div class="col-12 text-end">
                                
                                    <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                    <button type="submit" class="printBtn"> Submit </button>
                                </div>
                            </div>
                        
                            </form>


                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
    
    

<!--Professors--> 
            <div class="details">
                <div class="recentOrders">
                    {{-- <div class="cardHeader">
                        <h2>Professors</h2>
                    </div> --}}
                    
                    
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
        $('#profTable').DataTable();
    });
    </script>
                    
                    <table id="profTable" class="display">
                        <thead>
                            <tr>
                                <th data-orderable="true">Name</th>
                                <th data-orderable="true">Email</th>
                                <th data-orderable="true">Subject Code</th>
                                <th data-orderable="true">Subject Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                                 @foreach ($data as $professor)
                                <tr>
                                    <td>{{ $professor->full_name }}</td>
                                    <td>{{ $professor->email }}</td>
                                    <td>
                                        @foreach ($professor->subjects as $subject)
                                            {{ $subject->subject_code }}
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($professor->subjects as $subject)
                                            {{ $subject->subject_description }}
                                        @endforeach
                                    </td>
                                    <td>
                                        <!-- Edit button -->
                                      <button class="btnView1" 
                                      data-professor-id="{{ $professor->id }}" 
                                      data-email="{{ $professor->email }}" 
                                      data-subject-code=" {{ $subject->subject_code }}" 
                                      data-subject-description=" {{ $subject->subject_description }}" 
                                      data-first-name="{{ $usersP->where('email', $professor->email)->first()->first_name }}"
                                      data-last-name="{{ $usersP->where('email', $professor->email)->first()->last_name }}"
                                      data-bs-toggle="modal" data-bs-target="#editProfessorModal">Edit</button>
                                      <button class="btnRemove" data-professor-id="{{ $professor->id }}">
                                        Remove
                                      </button>


                                        <!-- Modal for editing professor details -->
<div class="modal fade" id="editProfessorModal" tabindex="-1" aria-labelledby="editProfessorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editProfessorModalLabel">Edit Professor</h1>
            </div>
            <div class="modal-body">
                <form action="{{ url('/updateProfessor') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="editProfessorId" name="professor_id">
                    <table>


                        {{-- <div class="form-group" style="font-size: 22px;text-align:left;">
                            <label class="form-label" for="email">Professor Name:</label>
                            <div class="name-inputs">
                                <input class="form-input2" type="text" name="first_name" id="editFirstName" placeholder="Enter First Name" value="">
                                <input class="form-input2" type="text" name="last_name" id="editLastName" placeholder="Enter Last Name" value="">
                            </div>
                            
                        </div> --}}
                        <div class="form-group" style="font-size: 22px;text-align:left;">
                            <label class="form-label" for="email">E-mail Address:</label>
                            <input class="form-input" type="text" name="email" id="editEmail" placeholder="Enter E-mail Address" value="">
                        </div>
                        <div class="form-group" style="font-size: 22px;text-align:left;">
                            <label class="form-label" for="subject_code">Subject Code:</label>
                            <input class="form-input" type="text" name="subject_code" id="editSubjectCode" placeholder="Enter Subject Code" value="">
                        </div>
                        <div class="form-group" style="font-size: 22px;text-align:left;">
                            
                            <label class="form-label" for="subject_description">Subject Description:</label>
                            <input class="form-input" type="text" name="subject_description" id="editSubjectDescription" placeholder="Enter Subject Description" value="">
                        </div>
                    </table>

                    <!-- <div class="buttons" style="margin-left: 100px;text-align:left;">
                        <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400;">Close</button>
                        <button class="submitBtn" type="submit" style="font-size: 18px; font-weight: 400;">Save Changes</button>
                    </div> -->

                    <br>
                    <div class="buttonsSection">
                        <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                        <button type="submit" class="printBtn"> Save Changes </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $(document).on('click', '.btnRemove', function (event) {
        event.preventDefault();
        var professorId = $(this).data('professor-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will remove the professor permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/removeProfessor/' + professorId,
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        Swal.fire(
                            'Removed!',
                            response.message,
                            'success'
                        ).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Oops!',
                            'Something went wrong. Check console.',
                            'error'
                        );
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });
});
</script>

<script>
    $(document).ready(function () {
        $('#profTable').DataTable();

        // Edit button click event
        $('.btnView1').click(function () {
            // Retrieve professor data from data attributes
            var professorId = $(this).data('professor-id');
            var professorEmail = $(this).data('email');
            var professorFirstName = $(this).data('first-name'); // Access first_name data attribute
            var professorLastName = $(this).data('last-name'); // Access last_name data attribute
            var professorSubjectCode = $(this).data('subject-code');
            var professorSubjectDescription = $(this).data('subject-description');

            // Set the values in the modal input fields
            $('#editProfessorId').val(professorId);
            $('#editEmail').val(professorEmail);
            $('#editFirstName').val(professorFirstName); // Set the first_name in the modal
            $('#editLastName').val(professorLastName); // Set the last_name in the modal
            $('#editSubjectCode').val(professorSubjectCode);
            $('#editSubjectDescription').val(professorSubjectDescription);

            // Open the edit modal
            $('#editProfessorModal').modal('show');
        });
    });





    $(document).ready(function () {
    $('#profTable').DataTable();

    // Edit button click event
    $('.btnView1').click(function () {
        var professorId = $(this).data('professor-id');
        // ... Other code to populate the modal fields with professor details

        // Open the edit modal
        $('#editProfessorModal').modal('show');
    });
});

</script>



                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    


        </div>

    </div>


        

    <!-- =========== Scripts =========  -->
    <script src="assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>