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
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"><link rel="stylesheet" href="assets/css/style.css">

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



                <li  class="active">
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
                <i class="fa fa-arrow-left" aria-hidden="true"  style="font-size: 15px;color:white;"></i>
                    <!-- Remove the href attribute from the button -->
                    <button class="updateBtn" type="button" id="previousButton" >Previous</button>
                   
                </div>
            </div>


            <script>
                document.getElementById('previousButton').addEventListener('click', function () {
                    // Redirect to the previous page when the button is clicked
                    window.location.href = "{{ url('/professor/class') }}";
                });
            </script>
                <div class="details">
                    <div class="recentOrders">
                        <div class="cardHeader">
                            <h2>Student List</h2>
                        </div>

                        <table id="fileTable" class="display">
                            <thead>
                                <tr>
                                    <td>Student Name</td>
                                    <td>Course</td>
                                    <td>Year & Section</td>
                                    <td>School Year</td>
                                    <td>Personal Information</td>
                                    <td>OJT Information</td>
                                    <td>Student Requirements</td>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($studentData as $data)
                                <tr>
                                    <td>{{ $data['student']->full_name }}</td>
                                    <td>{{ $data['student']->course }}</td>
                                    <td>{{ $data['student']->year_and_section }}</td>
                                    <td>{{ $data['student']->studentInfo->school_year_start ?? '-' }} - {{ $data['student']->studentInfo->school_year_end ?? '-' }}</td>

                                    <!-- Personal Info Button -->
                                    <td class="text-center">
                                        <button class="btnView" data-bs-toggle="modal" data-bs-target="#personalInfoModal"
                                            data-full-name="{{ $data['student']->full_name }}"
                                            data-contact-number="{{ $data['student']->contact_number }}"
                                            data-email="{{ $data['student']->email }}"
                                            data-address="{{ $data['student']->address }}"
                                            data-date-of-birth="{{ $data['student']->date_of_birth }}"
                                            data-student-num="{{ $data['ojt']->studentNum }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                    </td>

                                    <!-- OJT Info Button -->
                                    <td class="text-center">
                                        <button class="btnView" data-bs-toggle="modal" data-bs-target="#ojtInfoModal"
                                            data-full-name="{{ $data['student']->full_name }}"
                                            data-company-name="{{ $data['ojt']->company_name }}"
                                            data-company-address="{{ $data['ojt']->company_address }}"
                                            data-nature-of-business="{{ $data['ojt']->nature_of_bus }}"
                                            data-nature-of-linkages="{{ $data['ojt']->nature_of_link }}"
                                            data-level="{{ $data['ojt']->level }}"
                                            data-start-date="{{ $data['ojt']->start_date }}"
                                            data-finish-date="{{ $data['ojt']->finish_date }}"
                                            data-report-time="{{ $data['ojt']->report_time }}"
                                            data-contact-name="{{ $data['ojt']->contact_name }}"
                                            data-contact-position="{{ $data['ojt']->contact_position }}"
                                            data-contact-number="{{ $data['ojt']->contact_number }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                    </td>

                                    <!-- Requirements -->
                                    <td class="text-center">
                                        <button class="btnView" onclick="window.location.href='/studentrequire?value={{ $data['student']->full_name }}'">
                                            <i class="fa fa-eye"></i> Requirements
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Personal Info Modal -->
                <div class="modal fade" id="personalInfoModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Student Personal Information</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h4 id="pi-full-name"></h4>
                                <p>Contact Number: <span id="pi-contact-number"></span></p>
                                <p>Email: <span id="pi-email"></span></p>
                                <p>Address: <span id="pi-address"></span></p>
                                <p>Date of Birth: <span id="pi-dob"></span></p>
                                <p>Student Number: <span id="pi-student-num"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btnView" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OJT Info Modal -->
                <div class="modal fade" id="ojtInfoModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Student OJT Information</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h4 id="ojt-full-name"></h4>
                                <p>Company Name: <span id="ojt-company-name"></span></p>
                                <p>Company Address: <span id="ojt-company-address"></span></p>
                                <p>Nature of Business: <span id="ojt-nature-business"></span></p>
                                <p>Nature of Linkages: <span id="ojt-nature-link"></span></p>
                                <p>Level: <span id="ojt-level"></span></p>
                                <p>Start Date: <span id="ojt-start-date"></span></p>
                                <p>End Date: <span id="ojt-finish-date"></span></p>
                                <p>Reporting Time: <span id="ojt-report-time"></span></p>
                                <p>Contact Name: <span id="ojt-contact-name"></span></p>
                                <p>Position: <span id="ojt-contact-position"></span></p>
                                <p>Contact Number: <span id="ojt-contact-number"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btnView" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JS for modals and DataTable -->
                <script>
                $(document).ready(function() {
                    // Personal Info modal
                    $('.btnView').click(function() {
                        var button = $(this);
                        $('#pi-full-name').text(button.data('full-name'));
                        $('#pi-contact-number').text(button.data('contact-number'));
                        $('#pi-email').text(button.data('email'));
                        $('#pi-address').text(button.data('address'));
                        $('#pi-dob').text(button.data('date-of-birth'));
                        $('#pi-student-num').text(button.data('student-num'));
                    });

                    // OJT Info modal
                    $('.btnView').click(function() {
                        var button = $(this);
                        $('#ojt-full-name').text(button.data('full-name'));
                        $('#ojt-company-name').text(button.data('company-name'));
                        $('#ojt-company-address').text(button.data('company-address'));
                        $('#ojt-nature-business').text(button.data('nature-of-business'));
                        $('#ojt-nature-link').text(button.data('nature-of-linkages'));
                        $('#ojt-level').text(button.data('level'));
                        $('#ojt-start-date').text(button.data('start-date'));
                        $('#ojt-finish-date').text(button.data('finish-date'));
                        $('#ojt-report-time').text(button.data('report-time'));
                        $('#ojt-contact-name').text(button.data('contact-name'));
                        $('#ojt-contact-position').text(button.data('contact-position'));
                        $('#ojt-contact-number').text(button.data('contact-number'));
                    });
                });
                </script>
                </tbody>
                </table>
                
            </div>

        </div> 
    </div>
</div>



</body>
</html>

<!-- =========== Scripts =========  -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- ====== ionicons ======= -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

                    <!-- Include jQuery and DataTables scripts -->
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                
                    <script>
                        $(document).ready(function() {
                            $('#fileTable').DataTable({
                                order: [] 
                            });
                        });
                    </script>