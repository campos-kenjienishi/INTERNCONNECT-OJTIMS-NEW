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

                <li class="active">
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
                <h1>Memorandum of Agreement</h1>
            </div>

            <!-- ================ Order Details List =================-->

            <div class="buttons" style="margin-left: 70%;">
                <div class="AddProfBtn">
                    <button type="button" class="updateBtn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add New Company
                    </button>
                    <i class="uil uil-plus" style="font-size: 15px;"></i>
                </div>
            </div>
  
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Company</h1>
                        </div>

                        <div class="modal-body modal-scrollable" style="height: 500px;">
                            <form action="{{url('/companyCreate')}}" method="post" enctype="multipart/form-data">

                                @csrf
                                <table>
                                    
                                    <div class="form-group" style="font-size: 22px;">
                                        
                                        <label class="form-label" for="company_name">Company Name*:</label>
                                        <input class="form-input" type="text" name="company_name" required>
                                        <div class="error-message" id="company_name-error">Please enter the company name.</div>


                                        <label class="form-label" for="company_address">Company Address*:</label>
                                        <input class="form-input" type="text" name="company_address" required>
                                        <div class="error-message" id="company_address-error">Please enter the company address.</div>


                                        <label class="form-label" for="company_rep">Company Representative*:</label>
                                        <input class="form-input" type="text" name="company_rep" required>
                                        <div class="error-message" id="company_rep-error">Please enter the company representative.</div>


                                        <label class="form-label" for="companyNo">Company Number: (Optional)</label>
                                        <input class="form-input" type="text" name="companyNo" >
                                       


                                        <label class="form-label" for="company_email">Company E-mail: (Optional)</label>
                                        <input class="form-input" type="text" name="company_email" >
                                      

                                       
                                        <label class="form-label" for="school_year">School Year*:</label>
                                        <input class="form-input3" type="text" name="school_year_start" placeholder="Start Year" required>
                                            <span>-</span>
                                        <input class="form-input3" type="text" name="school_year_end" placeholder="End Year" required>
                                        <div class="error-message" id="school_year-error">Please enter the school year.</div>

                                        <br>

                                        <label class="form-label" for="file">Choose File*:</label>
                                        <input class="form-input" type="file" name="file" required>
                                        <div class="error-message" id="file-error">Please attach the file.</div>


                                        <label class="form-label" for="student_names">Student Names (Optional and can select multiple):</label>
                                    
                                        <select name="student_names[]" class="form-input" multiple >
                                            @foreach ($stu as $student)
                                                <option value="{{$student->full_name}}">{{$student->full_name}}</option>
                                            @endforeach
                                        </select>
                                        


                                    </div>

                                </table> 
                                
                                <!-- <div class="buttons" style="margin-top: -20px; margin-left: 90px;">
                                                
                                    <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 12px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                    
                                    <button class="submitBtn" type="submit" data-bs-dismiss="modal" style="font-size: 12px; font-weight: 400;"> Submit </button>

                                </div> -->

                                <br>
                                <br>
                                <div class="buttonsSection" style="margin-bottom: -65%; @media (min-width: 768px) { margin-top: 155%; margin-bottom: -40%;}">
                                    <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                                    <button type="submit" class="printBtn"> Submit </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <!--Room List-->          
                <div class="details">
                    <div class="recentOrders">
                        <div class="cardHeader">
                            <h2>Companies</h2>
                        </div>
                        <div class="table-container">
                            <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
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
                                    {{-- <td data-orderable="true">Company Address</td> --}}
                                    {{-- <td data-orderable="true">Company Representative</td> --}}
                                    <td data-orderable="true">Company Contact No.</td>
                                    <td data-orderable="true">Company Email</td>
                                    <td data-orderable="true">School Year</td>
                                    <td>Student List</td>
                                    <td>Status</td>
                                    <td width="31%"></td>

                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($companies as $company)
                                <tr>
                                    <td style="display: none;">{{$company->id}}</td>
                                    <td>{{$company->company_name}}</td>
                                    {{-- <td>{{$companies->company_address}}</td> --}}
                                    {{-- <td>{{$companies->company_rep}}</td> --}}
                                    <td>{{$company->companyNo}}</td>
                                    <td>{{$company->company_email}}</td>
                                    <td>{{$company->school_year}}</td>
                                    
                                    <td>
                                        @foreach ($company->students as $student)
                                    
                                        <li>{{ $student->full_name }}</li>
                                        @endforeach
                                    </td>

                                    <td>Active</td>

                                    <td>
                                        
                                        
                                    <div class="tooltip">
                                    <button class="btnView1">
                                        <a href="{{ route('moa.view', ['companyId' => $company->id]) }}" style="color: white; text-decoration: none;">
                                        <i class="fa fa-eye"></i> View
                                        </a>
                                    </button>
                                    <span class="tooltiptext">View</span>
                                    </div>




                                    <div class="tooltip">
                                    <button class="btnDownload" style="margin-left: 5px;">
                                            
                                            <a href="{{url('/moa/download',$company->file)}}" style="color: white; text-decoration: none;" ><i class="fa fa-download"></i> Download</a>
                                        </button>
                                    <span class="tooltiptext">Download</span>
                                    </div>


                                        
                                
                                        

                                    <div class="tooltip">
                                        <button class="btnSend" data-file-id="{{$company->id}}" data-file-name="{{$company->company_name}}" data-bs-toggle="modal" data-bs-target="#exModal" style="margin-left: 5px;">
                                            <i class="uil uil-message"></i> Send
                                            
                                        </button>

                                    <span class="tooltiptext">Send</span>
                                    </div>

                                        
                                        <script>
                                            // JavaScript code to populate hidden fields when the "Send" button is clicked
                                            $(document).ready(function () {
                                                $('#exModal').on('show.bs.modal', function (event) {
                                                    var button = $(event.relatedTarget);
                                                    var fileId = button.data('file-id');
                                                    var companyName = button.data('company-name');
                                                    var modal = $(this);
    
                                                    // Populate the hidden input fields with file information
                                                    modal.find('#file-id').val(fileId);
                                                    modal.find('#company-name').val(companyName);
                                                });
                                            });
                                        </script>
    
                                        <!-- Modal -->
                                        <div class="modal fade" id="exModal" tabindex="-1" aria-labelledby="exModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exModalLabel">Send File To</h1>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="{{url('/sendFile')}}" method="post" enctype="multipart/form-data">

                                                            @csrf
                                                            <script>
                                                                // JavaScript code to populate hidden fields when the "Send" button is clicked
                                                                $(document).ready(function () {
                                                                    $('#exModal').on('show.bs.modal', function (event) {
                                                                        var button = $(event.relatedTarget);
                                                                        var fileId = button.data('file-id');
                                                                        var companyName = button.data('company-name');
                                                                        var modal = $(this);
                                                            
                                                                        // Populate the hidden input fields with file information
                                                                        modal.find('#file-id').val(fileId);
                                                                        modal.find('#company-name').val(companyName);
                                                                    });
                                                                });
                                                            </script>

                                                            <table>

                                                                <div class="form-group" style="font-size: 22px;">
                                                                    <label class="form-label" for="email">E-mail Address:</label>
                                                                    <input class="form-input" type="text" name="email" placeholder="Enter E-mail Address">
                                                                </div>

                                                            </table>

                                                            <input type="hidden" id="file-id" name="file_id" value="">
                                                            <input type="hidden" id="company-name" name="company_name" value="">

                                                            
                                                            <!-- <div class="buttons" style="margin-left: 100px;">
                                                                <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                                                <button type="submit" style="font-size: 18px; font-weight: 400;"> Submit </button>

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

                                    

                                    <div class="tooltip">
                                        <button class="btnPrint" style="margin-left: 5px;" onclick="openViewModal('{{ route('print-data', ['company' => $company->id]) }}')">
                                            <i class="uil uil-print"></i>
                                            <span style="color: var(--white); text-decoration: none; font-family: 'poppins'; font-style: normal;">Print</span>
                                        </button>
                                    <span class="tooltiptext">Print Company Details</span>
                                    </div>

                                       

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body scrollable-modal-body">
                <iframe id="viewIframe" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>

            <!-- <div class="buttons" style="margin-left:75%">
                <button class="updateBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                <button type="button" onclick="printRegularPreview()" class="updateBtn" style="font-size: 18px; font-weight: 400;">Print</button>
            </div> -->

            <br>
            <br>
            <div class="buttonsSection">
                <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                <button type="button" onclick="printRegularPreview()" class="printBtn"> Print </button>
            </div>
            
           
        </div>
    </div>
</div>





<!-- Your custom script -->
<script>
     function openViewModal(routeUrl) {
        var iframe = document.getElementById('viewIframe');
        iframe.src = routeUrl;

        // Show the modal
        $('#viewModal').modal('show');
    }

 

    
function printRegularPreview() {
    var iframe = document.getElementById('viewIframe');

    // Ensure content is fully loaded before printing
    if (iframe.contentDocument.readyState === 'complete') {
        iframe.contentWindow.print();
    } else {
        // If content is not loaded yet, wait for the load event
        iframe.onload = function () {
            iframe.contentWindow.print();
        };
    }
}
</script>
                                        
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