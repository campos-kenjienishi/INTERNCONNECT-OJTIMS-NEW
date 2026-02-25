<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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


                <li class="active">
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
                <h1>Students</h1>

            </div>

            <!-- Add a container for the modal content -->
            <div id="printPreviewModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" style="width:fit-content;min-width:100%;">

                        <div class="modal-body" id="printPreviewContent"></div>

                        <br>
                        <br>
                        <div class="buttonsSection">
                            <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                            <button type="button" onclick="printReport()" class="printBtn">Print</button>
                        </div>
                    </div>
                </div>
            </div>

            <form id="reportForm" action="{{ route('ojt.report.generate') }}" method="post" target="_blank">
                @csrf

                <div class="form-groupdate">
                    <!-- <label class="form-labeldate" for="start_date">Start Date:</label>
                    <input class="form-inputdate datepicker" type="text" id="start_date" name="start_date" required>

                    <label class="form-labeldate" for="start_date">End Date:</label>
                    <input class="form-inputdate datepicker" type="text" id="end_date" name="end_date" required> -->

                    <label class="form-labeldate" for="course">Course:</label>
                    <select class="form-inputcourse form-control" ype="text" id="course" name="course" required>
                        @foreach ($course as $course)
                        <option value="{{$course->course}}">{{$course->course}}</option>
                        @endforeach
                    </select>

                </div>


                <button class="updateBtn" type="button" onclick="generateReportPreview()">Generate Report</button>

            </form>

            <script>
                function generateReportPreview() {
                    // Get the form data
                    var formData = new FormData(document.getElementById('reportForm'));

                    // Make an AJAX request to fetch the report content
                    $.ajax({
                        url: "{{ route('ojt.report.generate') }}",
                        method: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            // Set the content of the print preview modal
                            $("#printPreviewContent").html(data);

                            // Open the print preview modal
                            $("#printPreviewModal").modal('show');
                        },
                        error: function() {
                            alert('An error occurred while fetching the report content.');
                        }
                    });
                }



                function printReport() {
                    var printContents = document.getElementById("printPreviewContent").innerHTML;
                    var printFrame = document.getElementById("printFrame").contentWindow;

                    printFrame.document.open();
                    printFrame.document.write('<html><head><title></title></head><body>' + printContents + '</body></html>');
                    printFrame.document.close();

                    printFrame.focus();
                    printFrame.print();
                }
            </script>

            <script>
                flatpickr('.datepicker', {
                    dateFormat: 'Y-m-d',
                });
            </script>


            <iframe id="printFrame" style="display:none;"></iframe>


            <!-- ================ Order Details List =================-->
            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Active Student List</h2>
                    </div>

                    <table id="fileTable" class="display">

                        <thead>
                            <tr>
                                <td data-orderable="true">Student Name</td>
                                <td data-orderable="true">Course</td>
                                <td data-orderable="true">Year & Section</td>
                                <td data-orderable="true">Professor's Name</td>
                                <td data-orderable="true">Subject Code</td>
                                <td>Personal Information</td>
                                <td>OJT Information</td>
                                <td></td>
                                <td></td>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($studentData as $data)

                            <tr>
                                <td>{{$data['student']->first_name }} {{ $data['student']->last_name }}</td>
                                <td>{{$data['student']->course }}</td>
                                <td>{{ $data['student']->year_and_section }}</td>
                                <td>{{ $data['student']->adviser_name }}</td>
                                <td>
                                    @if (isset($data['subjects']) && count($data['subjects']) > 0)
                                        @foreach ($data['subjects'] as $subject)
                                            {{ $subject['subject_code'] ?? '--' }}<br>
                                        @endforeach
                                    @else
                                        --
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button class="btnView" data-bs-toggle="modal" data-bs-target="#exampleModal" style="margin-left:1%" data-full-name="{{ $data['student']->full_name }}" data-contact-number="{{ $data['student']->contact_number }}" data-email="{{ $data['student']->email }}" data-address="{{ $data['student']->address }}" data-date-of-birth="{{ $data['student']->date_of_birth }}" data-student-num="{{ $data['ojt']->studentNum }}">
                                        <i class="fa fa-eye"></i>
                                        View
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Student Personal Information</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">


                                                    @csrf

                                                    <h4 style="color: black" id="full-name"></h4>
                                                    <p style="color: maroon">Contact Number: <span id="contact-number"></span></p>
                                                    <p style="color: maroon">Email Address: <span id="email"></span></p>
                                                    <p style="color: maroon">Address: <span id="address"></span></p>
                                                    <p style="color: maroon">Date of Birth: <span id="date-of-birth"></span></p>
                                                    <p style="color: maroon">Student Number:<span id="student-num"></span></p>



                                                </div>

                                                <!-- <div class="buttons" style="margin-left: 450px;">

                                                    <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="width: 100px; font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                                </div> -->

                                                <br>
                                                <br>
                                                <div class="buttonsSection">
                                                    <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <button class="btnView" data-bs-toggle="modal" data-bs-target="#exModal" style="margin-left:1%" data-full-name="{{ $data['student']->full_name }}" data-company-name="{{ $data['ojt']->company_name }}" data-company-address="{{ $data['ojt']->company_address }}" data-nature-of-business="{{ $data['ojt']->nature_of_bus }}" data-nature-of-linkages="{{ $data['ojt']->nature_of_link }}" data-level="{{ $data['ojt']->level }}" data-start-date="{{ $data['ojt']->start_date }}" data-finish-date="{{ $data['ojt']->finish_date }}" data-report-time="{{ $data['ojt']->report_time }}" data-contact-name="{{ $data['ojt']->contact_name }}" data-contact-position="{{ $data['ojt']->contact_position }}" data-contact-number="{{ $data['ojt']->contact_number }}">
                                        <i class="fa fa-eye"></i>
                                        View
                                    </button>


                                    <!-- Modal -->
                                    <div class="modal fade" id="exModal" tabindex="-1" aria-labelledby="exModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exLabel">Student OJT Information</h1>
                                                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                                                </div>

                                                <div class="modal-body">


                                                    @csrf

                                                    <h4 style="color: black" id="full-name"></h4>
                                                    <p style="color: maroon">Company Name: <span id="company-name"></span></p>
                                                    <p style="color: maroon">Company Address: <span id="company-address"></span></p>
                                                    <p style="color: maroon">Nature of Business: <span id="nature-of-business"></span></p>
                                                    <p style="color: maroon">Nature of Networking or Linkages: <span id="nature-of-linkages"></span></p>
                                                    <p style="color: maroon">Level: <span id="level"></span></p>
                                                    <p style="color: maroon">Start Date: <span id="start-date"></span></p>
                                                    <p style="color: maroon">End Date: <span id="finish-date"></span></p>
                                                    <p style="color: maroon">Reporting Time: <span id="report-time"></span></p>
                                                    <p style="color: maroon">Contact Name: <span id="contact-name"></span></p>
                                                    <p style="color: maroon">Position of Contact: <span id="contact-position"></span></p>
                                                    <p style="color: maroon">Contact Number of Representative: <span id="contact-number"></span></p>

                                                </div>

                                                <br>
                                                <br>
                                                <div class="buttonsSection">
                                                    <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </td>


                                <script>
                                    $(document).ready(function() {
                                        // Handle "Personal Information" modal
                                        $('#exampleModal').on('show.bs.modal', function(event) {
                                            var button = $(event.relatedTarget);
                                            var modal = $(this);

                                            // Populate modal with data from "data-*" attributes
                                            modal.find('#full-name').text(button.data('full-name'));
                                            modal.find('#contact-number').text(button.data('contact-number'));
                                            modal.find('#email').text(button.data('email'));
                                            modal.find('#address').text(button.data('address'));
                                            modal.find('#date-of-birth').text(button.data('date-of-birth'));
                                            modal.find('#student-num').text(button.data('student-num'));
                                        });

                                        // Handle "OJT Information" modal
                                        $('#exModal').on('show.bs.modal', function(event) {
                                            var button = $(event.relatedTarget);
                                            var modal = $(this);

                                            // Populate modal with data from "data-*" attributes
                                            modal.find('#full-name').text(button.data('full-name'));
                                            modal.find('#company-name').text(button.data('company-name'));
                                            modal.find('#company-address').text(button.data('company-address'));
                                            modal.find('#nature-of-business').text(button.data('nature-of-business'));
                                            modal.find('#nature-of-linkages').text(button.data('nature-of-linkages'));
                                            modal.find('#level').text(button.data('level'));
                                            modal.find('#start-date').text(button.data('start-date'));
                                            modal.find('#finish-date').text(button.data('finish-date'));
                                            modal.find('#report-time').text(button.data('report-time'));
                                            modal.find('#contact-name').text(button.data('contact-name'));
                                            modal.find('#contact-position').text(button.data('contact-position'));
                                            modal.find('#contact-number').text(button.data('contact-number'));
                                            // Populate other data fields similarly
                                        });
                                    });
                                </script>


                                <td>
                                    <button class="btnStatus" data-student="{{ $data['ojt']->studentNum }}" data-status="{{ $data['ojt']->status }}" data-bs-toggle="modal" data-bs-target="#ex1Modal">
                                        Status <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </button>
                                    <script>
                                        $(document).ready(function() {
                                            $('.btnSend').on('click', function() {
                                                var button = $(this);
                                                var studentNum = button.data('student');
                                                var status = button.data('status'); // Get the status from the data-status attribute

                                                var modal = $('#ex1Modal');

                                                // Populate the status input field with the initial status
                                                modal.find('#status').val(status);

                                                // Populate the hidden input field with the student number
                                                modal.find('#student').val(studentNum);
                                            });

                                            // Handle form submission for status update
                                            $('#statusUpdateForm').on('submit', function(event) {
                                                event.preventDefault(); // Prevent the default form submission

                                                // Extract data from the form
                                                var studentNum = $(this).find('#student').val();
                                                var newStatus = $(this).find('#status').val();

                                                // Perform an AJAX request to update the status
                                                $.ajax({
                                                    type: 'POST',
                                                    url: '/status/' + studentNum,

                                                    data: {
                                                        _token: "{{ csrf_token() }}",
                                                        status: newStatus
                                                    },
                                                    success: function(data) {



                                                        location.reload(); // Reload the current page


                                                    },
                                                    error: function() {
                                                        alert('An error occurred while processing your request.');
                                                    }
                                                });
                                            });
                                        });
                                    </script>


                                    <!-- Modal -->
                                    <div class="modal fade" id="ex1Modal" tabindex="-1" aria-labelledby="ex1ModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="ex1ModalLabel">Status</h1>
                                                </div>

                                                <div class="modal-body">
                                                    <form id="statusUpdateForm" action="{{ url('/status') }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <script>
                                                            // JavaScript code to populate hidden fields when the "Send" button is clicked
                                                            $(document).ready(function() {
                                                                $('#ex1Modal').on('show.bs.modal', function(event) {
                                                                    var button = $(event.relatedTarget);
                                                                    var studentNum = button.data('student');
                                                                    var modal = $(this);

                                                                    // Populate the hidden input fields with file information
                                                                    modal.find('#student').val(studentNum);
                                                                });
                                                            });
                                                        </script>

                                                        <div class="form-group" style="font-size: 22px;">
                                                            <label class="form-label" for="status">Status:</label>
                                                            <select class="form-input" name="status" id="status">
                                                                <option value="" disabled selected>Select status</option>
                                                                <option value="Pending">Pending</option>
                                                                <option value="Approved and For Notary">Approved and For Notary</option>
                                                                <option value="With Revision">With Revision</option>

                                                            </select>
                                                        </div>

                                                        <input type="hidden" id="student" name="student" value="">

                                                        <!-- <div class="buttons" style="margin-left: 100px;">
                                                            <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                                            <button type="submit" style="font-size: 18px; font-weight: 400;"> Update</button>
                                                        </div> -->

                                                        <br>
                                                        <br>
                                                        <div class="buttonsSection">
                                                            <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                                            <button type="submit" class="printBtn"> Update </button>
                                                        </div>

                                                    </form>

                                                </div>

                                            </div>
                                        </div>

                                    </div>


                                    <script>
                                        $(document).ready(function() {
                                            $('#ex1Modal').on('show.bs.modal', function(event) {
                                                var button = $(event.relatedTarget);
                                                var studentNum = button.data('student');
                                                var status = button.data('status');
                                                var modal = $(this);

                                                // Populate the hidden input fields with file information
                                                modal.find('#student').val(studentNum);

                                                // Set the selected status in the dropdown
                                                modal.find('#status').val(status);
                                            });
                                        });
                                    </script>



                                </td>

                                <td>

                                    <form id="notifyForm" action="{{ url('/notify', $data['ojt']->studentNum) }}" method="POST">
                                        @csrf

                                        <button type="submit" class="btnNotify" >
                                        <!-- style="border-radius: 12px;height:36px;" -->

                                            Notify<i class="fa fa-bell" aria-hidden="true"></i>

                                        </button>
                                    </form>

                                    <script>
                                        $(document).ready(function() {
                                            $('#notifyForm').submit(function(event) {
                                                event.preventDefault();

                                                var form = $(this);
                                                var studentNum = form.find('input[name="studentNum"]').val();
                                                location.reload();
                                                $.ajax({
                                                    type: 'POST',
                                                    url: form.attr('action'),
                                                    data: {
                                                        _token: "{{ csrf_token() }}",
                                                        studentNum: studentNum
                                                    },
                                                    success: function(data) {

                                                    },
                                                    error: function() {
                                                        console.error('An error occurred while processing your request.');
                                                    }
                                                });
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
        </div>
    </div>



</body>

</html>

<!-- =========== Scripts =========  -->
<script src="assets/js/main.js"></script>

<!-- ====== ionicons ======= -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

<!-- Include jQuery and DataTables scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Enable sorting for the fileTable -->
<script>
    $(document).ready(function() {
        $('#fileTable').DataTable();
    });
</script>