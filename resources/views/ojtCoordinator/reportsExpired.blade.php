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
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">



    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/jquery.printPage.js"></script>
    <style>
        .table-container {
            max-height: 400px; /* Set your desired maximum height */
            overflow-y: auto;
        }
    </style>

    
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

                <li >
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

                    <li >
                        <a href="{{ url('/reports') }}">
                            <span class="title" style="margin-left: 60px; padding: 10px; width: 78%; white-space: nowrap;">Student OJT Information</span>
                        </a>
                    </li>

                    <li class="active" >
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


            <form action="{{ route('reports.generate') }}" method="post">
                @csrf
            
                <div class="form-group">
                    <label class="form-labeldate" for="school_year">School Year*:</label>
                    <div class="input-group">
                        <select class="form-select" name="school_year_start" id="school_year_start" required>
                            <!-- Default blank option -->
                            <option value="">Select Year</option>
                            <!-- Generate options for the start year dropdown -->
                            @for ($year = (date('Y') - 10); $year <= (date('Y') + 10); $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                        <span>-</span>
                        <select class="form-select" name="school_year_end" id="school_year_end" required>
                            <!-- Default blank option -->
                            <option value="">Select Year</option>
                            <!-- Options for the end year dropdown will be updated dynamically via JavaScript -->
                        </select>
                    </div>
                    <div class="error-message" id="school_year-error">Please select the school year.</div>
                </div>
                
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const startYearSelect = document.getElementById('school_year_start');
                        const endYearSelect = document.getElementById('school_year_end');
                        
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
                    <label class="form-labeldate" for="course">Course:</label>
                    <select class="form-inputcourse form-control" ype="text" id="course" name="course" required>
                        @foreach ($course as $course)
                        <option value="{{$course->course}}">{{$course->course}}</option>
                        @endforeach
                    </select>
                </div>
            
                <button type="submit" class="updateBtn">Generate Report</button>
                <button type="button" class="updateBtn" onclick="openPrintPreviewModal()">Print Preview</button>

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
                                <button type="submit" onclick="printReport()" class="printBtn"> Print </button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                
                <script>
                    function openPrintPreviewModal() {
                    // Get the table content
                    var tableContent = document.getElementById('companyTable').outerHTML;
                
                    // Set the table content in the print preview modal
                    document.getElementById('printPreviewContent').innerHTML = tableContent;
                
                    // Open the print preview modal
                    $('#printPreviewModal').modal('show');
                }
                
                function printReport() {
                    // Get the table content
                    var tableContent = document.getElementById('companyTable').outerHTML;
                
                    // Set the table content in the print preview modal
                    document.getElementById('printPreviewContent').innerHTML = tableContent;
                
                    // Get the printContents
                    var printContents = document.getElementById("printPreviewContent").innerHTML;
                    var printFrame = document.getElementById("printFrame").contentWindow;
                
                    // Add CSS styles to the print contents
                    printContents = `
                        <html>
                        <head>
                            <title></title>
                            <style>
                                 .tablestyle{
                
                                
                                    border-style: solid;
                
                                }
                                body {
                                    font-family: Arial, sans-serif;
                                    font-size: 12px;
                                    color: #333;
                                }
                                thead {
                                    background-color: #f2f2f2;
                                    
                                }
                                table {
                                    border-collapse:collapse;
                                    max-width:500px;
                                    margin: 0 auto;
                                    text-align: left;
                                }
                                th, td {
                                    border: 1px solid #ddd;
                                    padding: 8px;
                                    text-align: left;
                                }
                               
                                tr:nth-child(even) {
                                    background-color: #f2f2f2;
                                }
                                tr:hover {
                                    background-color: #ddd;
                                }
                            </style>
                        </head>
                        <body>` + printContents + `</body></html>`;
                
                    // Write the styled contents to the print frame and print it
                    printFrame.document.open();
                    printFrame.document.write(printContents);
                    printFrame.document.close();
                
                    printFrame.focus();
                    printFrame.print();
                }

                
            </script>


<script>
    function sendEmail(course, userEmail) {
        // Disable the "Send Email" button to prevent multiple submissions
        document.getElementById("sendEmailBtn").disabled = true;

        $.ajax({
            url: "{{ route('reportsExpired.send.email') }}",
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                email: userEmail,
                course: course
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

    $(document).ready(function() {
        // Function to serialize form data and trigger sendEmail function
        $('#sendEmailForm').submit(function(event) {
            event.preventDefault(); // Prevent default form submission

            
            var userEmail = '{{ $user->email }}';

            // Call the sendEmail function with the retrieved data
            sendEmail( course, userEmail);
        });
    });
</script>

<!-- Update the form to include an ID and use the sendEmail function -->
<form id="sendEmailForm" action="{{url('/reportsExpired/send-email')}}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="course" name="course" value="{{ $course }}"> <!-- Assuming $course contains the selected course -->
    <input type="hidden" id="email" name="email" value="{{ $user->email }}"> <!-- Set the user's email as a hidden input field -->
    <input type="hidden" id="printContentsInput">
    <div class="buttonsSectionOJT">
   
    {{-- <button id="sendEmailBtn" type="submit" >Send Email</button> --}}
</div>
</form>

            <!-- ================ Order Details List =================-->

           
                <div class="details">
                    <div class="recentOrders">
                        <div class="cardHeader">
                            <h2>Expired Memorandum of Agreement</h2>
                        </div>
                        <div class="table-container">
                            
                            <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

                            <script>
                                $(document).ready(function() {
                                    $('#companyTable').DataTable({
                                        "order": [[0, 'desc']], // Sort by the first column (ID) in descending order
                                        
                                    });
                                });
                            </script>
    
                            <table id="companyTable" class="display">
                                <thead>
                                <tr>
                                    <td style="display: none;">id</td>
                                    <td data-orderable="true" >Company Name</td>
                                    <td data-orderable="true">Company Address</td>
                                    <td data-orderable="true">Company Representative</td>
                                    <td data-orderable="true">Company Contact No.</td>
                                    <td data-orderable="true">Company Email</td>
                                    <td data-orderable="true">School Year</td>
                                    {{-- <td>Student List</td>
                                    <td>Status</td> --}}
                                    

                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($companies as $company)
                                <tr>
                                    <td style="display: none;">{{$company->id}}</td>
                                    <td>{{$company->company_name}}</td>
                                    <td>{{$company->company_address}}</td>
                                    <td>{{$company->company_rep}}</td>
                                    <td>{{$company->companyNo}}</td>
                                    <td>{{$company->company_email}}</td>
                                    <td>{{$company->school_year}}</td>
                                    
                                    {{-- <td>
                                        @foreach ($company->students as $student)
                                    
                                        <li>{{ $student->full_name }}</li>
                                        @endforeach
                                    </td>

                                    <td>Expired</td> --}}

                                                                 
                                </tr>
                                @endforeach
                            </tbody>
                                
                        </table>
                    </div>   
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
                    
        // Function to display an alert
       function showAlert(message) {
           alert(message);
       }

       // Check if there are errors in the 'file' field and display a pop-up
       @if ($errors->has('file'))
           showAlert("{{ $errors->first('file') }}");
       @endif
   </script>
</body>
</html>


<!-- =========== Scripts =========  -->
<script src="assets/js/main.js"></script>

<!-- ====== ionicons ======= -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>



<!-- Add this script at the end of your HTML file -->
<script>
    $(document).ready(function() {
        // Function to validate and show error messages
        function validateForm() {
            let valid = true;

            // Check each required field
            $('input[required]').each(function() {
                if ($(this).val() === '') {
                    valid = false;
                    let errorMessageId = $(this).attr('name') + '-error';
                    $('#' + errorMessageId).show();
                } else {
                    let errorMessageId = $(this).attr('name') + '-error';
                    $('#' + errorMessageId).hide();
                }
            });

            return valid;
        }

        $('.submitBtn').click(function(event) { // Include the event parameter
    if (!validateForm()) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
});
    });
</script>