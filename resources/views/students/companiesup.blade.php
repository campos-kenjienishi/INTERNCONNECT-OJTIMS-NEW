<!DOCTYPE html>
<html lang="en">

<head>
    

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <!-- ======= Styles ====== -->
    <style>

.btnPrintVoucher {
  
  background-color: blue;
  padding: 6px 10px;
  border-radius: 12px;
  border-color: #999;
  font-size: 14px;
  color: var(--white);
}
.btnPrintVoucher:hover {
    background-color: rgb(7, 7, 66);
}


.scrollable-modal-body {
        max-height: 100%;
        overflow-y: auto;
    }
    </style>
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/jquery.printPage.js"></script>
    
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
    
                    <a href="{{ url('/student/accountinfo') }}" style="text-decoration: none;">
                        <span class="iconname">
                            <ion-icon name="person-circle-outline"></ion-icon>
                        </span>
                        <span class="name"> {{ $user->full_name }} </span>
                        <span class="name2">STUDENT </span>
                    </a>

                    <a href="{{ url('/student/accountinfo') }}" style="text-decoration: none;">
                    <span class="hidden-on-big">{{  $user->full_name }}</span>
                    <!-- <div class="toggle" id="toggle2">
                        <ion-icon name="menu-outline"></ion-icon>
                    </div> -->
                    </a>
    
    
                    <li>
                        <a href="{{ url('/student/home') }}">
                            <span class="icon">
                                <ion-icon name="home-outline"></ion-icon>
                            </span>
                            <span class="title" >Home</span>
                        </a>
                    </li>
    
    
                    <li>
                        <a href="{{ url('/student/ojtinfo') }}">
                            <span class="icon">
                                <ion-icon name="albums-outline"></ion-icon>
                            </span>
                            <span class="title">OJT Information</span>
                        </a>
                    </li>
    
                    <li>
                        <a href="{{ url('/student/class') }}">
                            <span class="icon">
                                <ion-icon name="clipboard-outline"></ion-icon>
                            </span>
                            <span class="title">Class</span>
                        </a>
                    </li>
    
                    <li>
                        <a href="{{ url('/student/files') }}">
                            <span class="icon">
                                <ion-icon name="download-outline"></ion-icon>
                            </span>
                            <span class="title">Downloadable Files</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/student/MOA') }}">
                            <span class="icon">
                                <ion-icon name="document-outline"></ion-icon>
                            </span>
                            <span class="title">MOA</span>
                            <span class="icon" style="margin-left: 30%; font-size: 22px;">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span>
                        </a>
                        {{-- 
                        <li >
                            <a href="{{ url('/student/pending') }}">
                                <span class="title" style="margin-left: 60px; padding: 10px; width: 78%; white-space: nowrap;">Pending MOA</span>
                            </a>
                        </li>
                        --}}
                        <li class="active" >
                            <a href="{{ url('/student/MOA') }}">
                                <span class="title" style="margin-left: 60px; padding: 10px; width: 78%; white-space: nowrap;">Notarized MOA</span>
                            </a>
                        </li>

                       
                    </li>

                    <li >
                        <a href="{{ url('/student/requirements') }}">
                            <span class="icon">
                                <ion-icon name="cloud-upload-outline"></ion-icon>
                            </span>
                            <span class="title">Requirements</span>
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
                    <button class="updateBtn" type="updateBtn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add Notarized MOA
                    </button>
                    <i class="uil uil-plus" style="font-size: 15px;"></i>
                </div>
            </div>
  
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Submit Notarized MOA</h1>
                        </div>

                        <div class="modal-body modal-scrollable">
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


                                        <label class="form-label" for="companyNo">Company Number*:</label>
                                        <input class="form-input" type="text" name="companyNo" required>
                                        <div class="error-message" id="companyNo-error">Please enter the company name.</div>


                                        <label class="form-label" for="company_email">Company E-mail*:</label>
                                        <input class="form-input" type="text" name="company_email" required>
                                        <div class="error-message" id="company_email-error">Please enter the company email.</div>


                                        <label class="form-label" for="school_year">School Year*:</label>
                                        <input type="text" name="school_year_start" placeholder="Start Year" required>
                                            <span>-</span>
                                            <input type="text" name="school_year_end" placeholder="End Year" required>
                                        <div class="error-message" id="school_year-error">Please enter the school year.</div>



                                        <label class="form-label" for="file">Choose File*:</label>
                                        <input class="form-input" type="file" name="file" required>
                                        <div class="error-message" id="file-error">Please attach the file.</div>


                                        

                                    </div>

                                </table> 
                                
                                <!-- <div class="buttons" style="margin-left: 100px;">
                                                
                                    <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                    
                                    <button class="submitBtn" type="submit" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400;"> Submit </button>
                                </div> -->

                                <div class="buttonsSectionOJT">
                                    <button class="closeBtn" type="button" data-bs-dismiss="modal" style="background-color: #6a6969; margin-right: 3%;"> Close </button>
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
                            <h2>Notarized MOA</h2>
                        </div>

                        <script>
                            $(document).ready(function() {
                                $('#companyTable').DataTable();
                            });
                        </script>

                        <table id="companyTable" class="display">
                            <thead>
                                <tr>
                                    <td data-orderable="true">Company Name</td>
                                    <td data-orderable="true">Company Contact No.</td>
                                    <td data-orderable="true">Company Email</td>
                                    <td data-orderable="true">School Year</td>
                                   
                                    <td></td>

                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($companies as $companies)
                                <tr>
                                    <td>{{$companies->company_name}}</td>
                                    <td>{{$companies->companyNo}}</td>
                                    <td>{{$companies->company_email}}</td>
                                    <td>{{$companies->school_year}}</td>
                                    
                                    <td>
                                    
                                        <button class="btnDownload" style="margin-left: 5px;">
                                            <i class="fa fa-download"></i>
                                            <a href="{{url('/moa/download',$companies->file)}}" style="color: white; text-decoration: none;" >Download</a>
                                        </button>
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

                                                    <div class="buttons" style="margin-left:75%">
                                                        <button class="updateBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                                                        <button type="button" onclick="printRegularPreview()" class="updateBtn" style="font-size: 18px; font-weight: 400;">Print</button>
                                                    </div>
                                                    
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btnPrint" style="margin-left: 5px;" onclick="openViewModal('{{ route('print-data', ['company' => $companies->id]) }}')">
                                            <i class="uil uil-print"></i>
                                            <span style="color: var(--white); text-decoration: none; font-family: 'poppins'; font-style: normal;">Print Company Details</span>
                                        </button>
                                    </td>          
                                    <td>
                                                                        
  <!-- Button trigger modal -->
<button class="btn btn-primary btnPrintVoucher" onclick="openViewModal('{{ route('voucher', ['company' => $companies->id]) }}')">
    <i class="uil uil-print"></i> Print Voucher
</button>

<!-- Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" >
            <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
                <iframe id="voucherIframe" width="100%" height="500" frameborder="0"></iframe>
            
            <div class="buttons" style="margin-left:75%">
                <button class="updateBtn" type="button" data-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;"> Close </button>
                <button type="button" onclick="printVoucherPreview()" class="updateBtn" style="font-size: 18px; font-weight: 400;">Print</button>
            </div>
            
            
        </div>
    </div>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- Your custom script -->
<script>
     function openViewModal(routeUrl) {
        var iframe = document.getElementById('viewIframe');
        iframe.src = routeUrl;

        // Show the modal
        $('#viewModal').modal('show');
    }

    function openViewModal1(routeUrl) {
        var iframe = document.getElementById('voucherIframe');
        iframe.src = routeUrl;

        // Show the modal when the iframe content is loaded
        iframe.onload = function() {
            // Set the background image inside the iframe
            iframe.contentDocument.body.style.backgroundImage = "url('/images/OJTIMS.jpg')";
            $('#voucherModal').modal('show');
        };
    }

    function printVoucherPreview() {
    var iframe = document.getElementById('voucherIframe');

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
                                                                                
                                    </td>
                                        
                                </tr>
                                @endforeach
                            </tbody>
                                
                        </table>                           
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
<script src="{{ asset('assets/js/main.js') }}"></script>

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

        // Handle form submission
        $('.submitBtn').click(function() {
            if (!validateForm()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
</script>