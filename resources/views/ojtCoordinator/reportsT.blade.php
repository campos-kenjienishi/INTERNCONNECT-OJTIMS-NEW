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
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"><link rel="stylesheet" href="assets/css/style.css">
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
                        <span class="title" >Dashboard</span>
                    </a>
                </li>

                <li>


                <li >
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

                <li >
                    <a href="{{ url('/reports') }}">
                        <span class="icon">
                            <ion-icon name="cellular-outline"></ion-icon>
                        </span>
                        <span class="title">Reports</span>
                   
                        <span class="icon" style="margin-left: 30%; font-size: 22px;">
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </span>
                    </a>

                    <li class="active">
                        <a href="{{ url('/reports') }}">
                            <span class="title" style="margin-left: 60px; padding: 10px; width: 78%; white-space: nowrap;">Student OJT Information</span>
                        </a>
                    </li>

                    <li  >
                        <a href="{{ url('/reportsExpired') }}">
                        <span class="title" style="margin-left: 60px; padding: 10px; width: 78%; white-space: nowrap;">Expired MOA</span>
                        </a>
                    </li>

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
                <h1>Reports</h1>
                
            </div> 



<form id="reportForm" action="{{ route('studentojt.report.generate') }}" method="post">
    @csrf
    
    <div class="form-groupdate">
        <label class="form-labeldate" for="start_date">Start Date:</label>
        <input class="form-inputdate datepicker" type="text" id="start_date" name="start_date" required>

        <label class="form-labeldate" for="start_date">End Date:</label>
        <input class="form-inputdate datepicker" type="text" id="end_date" name="end_date" required>

        <label class="form-labeldate" for="course">Course:</label>
        <select class="form-inputcourse form-control" ype="text" id="course" name="course" required>
            @foreach ($course as $course)
            <option value="{{$course->course}}">{{$course->course}}</option>
            @endforeach
        </select>

    </div>
    
    <button type="submit" class="updateBtn">Generate Report</button>
   

<!-- Add an iframe element for printing -->
<iframe id="printFrame" style="display: none;"></iframe>

<!-- Add a container for the modal content -->
<div id="printPreviewModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" style=" height:400px;max-height:400px;width: 100%; min-width: 50%;">

            <div class="modal-body" id="printPreviewContent" style="max-height: 300px; overflow-y: auto;overflow-x: auto;">
                <!-- Table content will be loaded here -->
            </div>
            
            <div class="buttonsSection">
                <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                <button type="button" onclick="printReport()" class="printBtn"> Print </button>
            </div>

        </div>
    </div>
</div>
</form>

<script>
    function openPrintPreviewModal() {
    // Get the table content
    var tableContent = document.getElementById('fileTable').outerHTML;

    // Set the table content in the print preview modal
    document.getElementById('printPreviewContent').innerHTML = tableContent;

    // Open the print preview modal
    $('#printPreviewModal').modal('show');
}

function printReport() {
    // Get the table content
    var tableContent = document.getElementById('fileTable').outerHTML;

    // Set the table content in the print preview modal
    document.getElementById('printPreviewContent').innerHTML = tableContent;

    var printContents = document.getElementById("printPreviewContent").innerHTML;
    var printFrame = document.getElementById("printFrame").contentWindow;

    printContents = `
        <html>
        <head>
            <title>OJT Information Report</title>
            <style>
                @page {
                    size: A4 landscape;
                    margin: 5mm; /* Reduced margins to give more room */
                }
                body {
                    font-family: "Segoe UI", Tahoma, Helvetica, Arial, sans-serif;
                    font-size: 8px; /* Much smaller font for high density */
                    color: #000;
                    margin: 0;
                    padding: 0;
                }
                h1 {
                    text-align: center;
                    font-size: 14px;
                    margin-bottom: 10px;
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                    table-layout: fixed; /* Crucial: Stops columns from stretching */
                }
                th, td {
                    border: 0.5pt solid #ccc; /* Thinner borders */
                    padding: 3px 2px; /* Minimal padding */
                    word-wrap: break-word; /* Forces long text to wrap to new line */
                    overflow-wrap: break-word;
                    vertical-align: top;
                }
                thead {
                    background-color: #800000 !important;
                    color: white !important;
                    -webkit-print-color-adjust: exact; /* Ensures color prints */
                }
                th {
                    font-size: 8px;
                    text-transform: uppercase;
                }
                /* Custom column widths to prioritize important info */
                th:nth-child(1) { width: 80px; }  /* Student Name */
                th:nth-child(2) { width: 100px; } /* Company Name */
                th:nth-child(3) { width: 150px; } /* Address - Needs more room */
                th:nth-child(12) { width: 80px; } /* Phone Number */
                
                tr:nth-child(even) { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
            </style>
        </head>
        <body>
            <h1>Student OJT Information Report</h1>
            ` + printContents + `
        </body>
        </html>`;

    printFrame.document.open();
    printFrame.document.write(printContents);
    printFrame.document.close();

    printFrame.focus();
    setTimeout(function() {
        printFrame.print();
    }, 500);
}

function sendEmail(studentData, userEmail) {
    // Disable the "Send Email" button to prevent multiple submissions
    document.getElementById("sendEmailBtn").disabled = true;

    $.ajax({
        url: "{{ route('reports.send.email') }}",
        method: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            email: userEmail,
            studentData: studentData
        },
        success: function(response) {
            alert("Email sent successfully!");
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        },
        complete: function() {
            // Re-enable the "Send Email" button after the request is completed
            document.getElementById("sendEmailBtn").disabled = false;
        }
    });
}
</script>

   
<script>
    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
    });
    
    

</script>


<!-- Add a form to enter the email address -->
<form action="{{url('/reports/send-email')}}" method="post" enctype="multipart/form-data">

    @csrf
   
<input type="hidden" id="email" name="email" value="{{ $user->email }}"> <!-- Set the user's email as a hidden input field -->
<input type="hidden" id="printContentsInput">

<div class="buttonsSectionOJT">
    <button type="button" class="updateBtn" onclick="openPrintPreviewModal()" >Print Preview</button>
    {{-- <button type="submit" class="updateBtn" >Send Email</button> --}}
</div>

</form>


<!-- ================ Order Details List =================-->
<div class="details">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Student OJT Information</h2>
        </div>

        <table id="fileTable" class="display">
            <thead>
                <tr>
                    <td data-orderable="true">Student Name</td>
                    <td data-orderable="true">Company Name</td>
                    <td data-orderable="true">Company Address</td>
                    <td data-orderable="true">Nature of Business</td>
                    <td data-orderable="true">Nature of Networking or Linkages</td>
                    <td data-orderable="true">Level</td>
                    <td data-orderable="true">Start Date</td>
                    <td data-orderable="true">End Date</td>
                    <td data-orderable="true">Reporting Time</td>
                    <td data-orderable="true">Contact Name</td>
                    <td data-orderable="true">Position of Contact</td>
                    <td data-orderable="true">Contact Number of Representative</td>
                </tr>
            </thead>

            <tbody>
                @foreach ($studentData as $data)
                <tr id="studentRow{{ $loop->index }}">
                    <td>{{ $data['student']->full_name }}</td>
                    <td>{{ $data['ojt']->company_name }}</td>
                    <td>{{ $data['ojt']->company_address }}</td>
                    <td>{{ $data['ojt']->nature_of_bus }}</td>
                    <td>{{ $data['ojt']->nature_of_link }}</td>
                    <td>{{ $data['ojt']->level }}</td>
                    <td>{{ $data['ojt']->start_date }}</td>
                    <td>{{ $data['ojt']->finish_date }}</td>
                    <td>{{ $data['ojt']->report_time }}</td>
                    <td>{{ $data['ojt']->contact_name }}</td>
                    <td>{{ $data['ojt']->contact_position }}</td>
                    <td>{{ $data['ojt']->contact_number }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

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
