<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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

                <a href="{{ url('/professor/accountinfo') }}" style="text-decoration: none;">
                    <span class="iconname">
                        <ion-icon name="person-circle-outline"></ion-icon>
                    </span>
                    <span class="name"> {{ $data->full_name }} </span>
                    <span class="name2">PROFESSOR </span>
                </a>
                <a href="{{ url('/professor/accountinfo') }}" style="text-decoration: none;">
                    <span class="hidden-on-big">{{ $data->full_name }}</span>
                    <!-- <div class="toggle" id="toggle2">
                        <ion-icon name="menu-outline"></ion-icon>
                    </div> -->
                </a>

                <li>
                    <a href="{{ url('/professor/home') }}">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title" >Dashboard</span>
                    </a>
                </li>



                <li class="active">
                    <a href="{{ url('/professor/class') }}">
                        <span class="icon">
                            <ion-icon name="clipboard-outline"></ion-icon>
                        </span>
                        <span class="title">Class</span>
                    </a>
                </li>


                <li>
                    <a href="{{ url('/professor/upload') }}">
                        <span class="icon">
                            <ion-icon name="document-outline"></ion-icon>
                        </span>
                        <span class="title">Upload Templates</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/reportsExpiredProf') }}">
                        <span class="icon">
                            <ion-icon name="cellular-outline"></ion-icon>
                        </span>
                        <span class="title">MOA</span>
                       
                    </a>
                </li>
                <li>
                    <a href="{{ url('/professor/maintain') }}">
                        <span class="icon">
                            <ion-icon name="code-working-outline"></ion-icon>
                        </span>
                        <span class="title">Maintenance</span>
                       
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
                <h1 >Class</h1>
            </div>



            <!-- ================ Order Details List =================-->

            <div class="buttons" style="margin-left: 70%;">
                <div class="AddProfBtn">
                    <i class="fa fa-arrow-left" aria-hidden="true" style="font-size: 15px;color:white;"></i>
                    <button class="updateBtn" type="button" onclick="window.location.href='{{ url('/professor/class') }}'">
                        Previous
                    </button>
                </div>
            </div>
            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Student Requests</h2>
                    </div>

                    <table id="fileTable" class="display">
                        <thead>
                            <tr>
                                <td data-orderable="true">Student Name</td>
                                <td data-orderable="true">Course</td>
                                <td data-orderable="true">Year and Section</td>
                                <td>Approve</td>
                                <td>Deny</td>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->course }}</td>
                                <td>{{ $student->year_and_section }}</td>
                                <td>
                                    <form id="approveForm" method="POST" action="{{ url('professor/approve', $student->email) }}">
                                        @csrf
                                        <button type="submit" style="background-color: green; border-radius: 12px; padding: 5px 10px; border-color: green; color: white;">
                                            Approve
                                        </button>
                                    </form>
                                </td> 
                                <td>
                                    
                                    <button id="denyButton" style="background-color: red; border-radius: 12px; padding: 5px 10px; border-color: red; color: white;">Deny</button>


                                    <!-- Modal -->
                                    

                                    <div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Reason to Deny</h1>
                                                </div>
                        
                                                <div class="modal-body">
                                                    <form id="denyForm" action="{{ url('professor/deny', $student->email) }}" method="post" enctype="multipart/form-data">
                        
                                                        @csrf
                                                      
                                                            <div class="form-group" style="font-size: 22px;">
                                                                <label class="form-label" for="reason">Reason:</label>
                                                               <textarea class="form-input" id="reason" name="reason" rows="4" required></textarea>
                                                            </div>
                                   
                                                        <br>
                                                        <div class="buttonsSection">
                                                            <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                                            <button type="submit" class="printBtn"> Deny </button>
                                                        </div>
                        
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // Get the modal
                                        var modal = document.getElementById('denyModal');

                                        // Get the button that opens the modal
                                        var btn = document.getElementById("denyButton");

                                        // Get the <span> element that closes the modal
                                        var span = document.getElementsByClassName("close")[0];

                                        // Get the "Previous" button
                                        var previousButton = document.getElementById("previousButton");

                                        // When the user clicks on the button, open the modal and disable the "Previous" button
                                        btn.onclick = function() {
                                            modal.style.display = "block";
                                            previousButton.disabled = true;
                                        }

                                        // When the user clicks on <span> (x), close the modal and enable the "Previous" button
                                        span.onclick = function() {
                                            modal.style.display = "none";
                                            previousButton.disabled = false;
                                        }

                                        // When the user clicks anywhere outside of the modal, close it and enable the "Previous" button
                                        window.onclick = function(event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                                previousButton.disabled = false;
                                            }
                                        }
                                    </script>
                                    

                                </td>                           
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                    
                    
                </div>

            </div> 
            
        </div>
        
    </div>

    

</body>
</html>


<script src="{{url('/assets/js/main.js')}}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<!-- Include jQuery and DataTables scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Enable sorting for the fileTable -->
<script>
    $(document).ready(function() {
        $('#fileTable').DataTable();
    });
</script>


<!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script for Approve button -->
<script>
    // Function to show Sweet Alert toast for approval
    function showApproveToast() {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "success",
            title: "Student approved successfully"
        });
    }

    // Add event listener to form submission
    document.addEventListener('DOMContentLoaded', function() {
        const approveForm = document.getElementById('approveForm');
        if (approveForm) {
            approveForm.addEventListener('submit', function(event) {
                // Prevent default form submission
                event.preventDefault();
                // Call the showApproveToast function
                showApproveToast();
                // Submit the form after displaying the toast
                this.submit();
            });
        }
    });
</script>


<script>
    // Function to show Sweet Alert toast for denial
    function showDenyToast() {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "warning",
            title: "Student has been denied"
        });
    }

    // Add event listener to form submission
    document.addEventListener('DOMContentLoaded', function() {
        const denyForm = document.getElementById('denyForm');
        if (denyForm) {
            denyForm.addEventListener('submit', function(event) {
                // Prevent default form submission
                event.preventDefault();
                // Call the showDenyToast function
                showDenyToast();
                // Submit the form after displaying the toast
                this.submit();
            });
        }
    });
</script>