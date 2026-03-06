<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
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

                <li>
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

                <li class="active">
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
                    <a href="{{ url('/auditlog') }}">
                        <span class="icon">
                            <ion-icon name="clipboard-outline"></ion-icon>
                        </span>
                        <span class="title">Audit Log</span>
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
                <h1>Maintenance</h1>
            </div>

            <!-- ================ Order Details List ================= -->
            <div class="buttons" style="margin-left: 70%;">
                <div class="AddProfBtn">
                    <button type="button" class="updateBtn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add Course
                    </button>
                    <i class="uil uil-plus" style="font-size: 15px;"></i>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Course</h1>
                        </div>

                        <div class="modal-body">
                            <form action="{{url('/courses')}}" method="post" enctype="multipart/form-data">

                                @csrf
                                <table>

                                    <div class="form-group" style="font-size: 22px;">
                                        <label class="form-label" for="course">Course Name:</label>
                                        <input class="form-input" type="text" name="course">
                                    </div>

                                    <div class="form-group" style="font-size: 22px;">
                                        <label class="form-label" for="acronym">Acronym:</label>
                                        <input class="form-input" type="text" name="acronym">
                                    </div>

                                </table>

                                <!-- <div class="buttons" style="margin-left: 100px;">
                                        
                                    <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                    
                                    <button class="submitBtn" type="submit" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400;"> Submit </button>

                                </div> -->

                                <br>
                                <div class="buttonsSection">
                                    <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                    <button type="submit" class="printBtn"> Submit </button>
                                </div>
                                
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!--Courses--> 
            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Courses</h2>
                    </div>
                    
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function() {
                    $('#courseTable').DataTable();
                });
                </script>
                    <table id="courseTable" class="display">
                        <thead>
                            <tr>
                                <td data-orderable="true">Course Name</td>
                                <td data-orderable="true">Acronym</td>
                                <td></td>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($data as $data)
                        <tr>
                            <td>{{ $data->course }}</td>
                            <td>{{ $data->acronym }}</td>
                            <td>
                                <button class="btnRemove" style="margin-left: 10%;">
                                    <i class="fa fa-trash"></i>
                                    <span class="remove-button" data-course-id="{{ $data->id }}">Remove</span>
                                </button>
                            
                             
                                
                                               
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



<!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script for Remove button -->
<script>
    // Function to show Sweet Alert confirmation dialog for removal
    function showRemoveConfirmation(courseId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to remove this course.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If user confirms, proceed with removal
                removeCourse(courseId);
            }
        });
    }

    // Function to remove course via AJAX
    function removeCourse(courseId) {
        $.ajax({
            type: 'POST',
            url: '/remove/course/' + courseId,
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(data) {
                // Reload the page after successful removal
                location.reload();
            },
            error: function() {
                console.error('An error occurred while processing your request.');
            }
        });
    }

    // Add event listener to "Remove" button click
    $(document).ready(function() {
        $('.remove-button').click(function(event) {
            // Prevent default button click behavior
            event.preventDefault();
            // Get the course ID associated with the button
            var courseId = $(this).data('course-id');
            // Call the showRemoveConfirmation function
            showRemoveConfirmation(courseId);
        });
    });
</script>
