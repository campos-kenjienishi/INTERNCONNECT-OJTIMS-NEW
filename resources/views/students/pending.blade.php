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
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">


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

                    <li >
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

                        <li  >
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


            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Pending for Approval MOA</h2>
                    </div>

                    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#company1Table').DataTable();
                        });
                    </script>

                    <table id="company1Table" class="display">
                        <thead>
                            <tr>
                                <td data-orderable="true">Company Name</td>
                                <td data-orderable="true">Company Contact No.</td>
                                <td data-orderable="true">Company Address</td>
                                <td data-orderable="true">Status</td>
                               

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ojt as $ojt)
                            <tr>
                                <td>{{$ojt->company_name}}</td>
                                <td>{{$ojt->contact_number}}</td>
                                <td>{{$ojt->company_address}}</td>
                                <td>{{$ojt->status}}</td>
                                

                                    
                            </tr>
                            @endforeach
                        </tbody>
                            
                    </table>
                        
                </div>

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
</script><!DOCTYPE html>
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
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">


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

                    <li >
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

                        <li  >
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


            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Pending for Approval MOA</h2>
                    </div>

                    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#company1Table').DataTable();
                        });
                    </script>

                    <table id="company1Table" class="display">
                        <thead>
                            <tr>
                                <td data-orderable="true">Company Name</td>
                                <td data-orderable="true">Company Contact No.</td>
                                <td data-orderable="true">Company Address</td>
                                <td data-orderable="true">Status</td>
                               

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ojt as $ojt)
                            <tr>
                                <td>{{$ojt->company_name}}</td>
                                <td>{{$ojt->contact_number}}</td>
                                <td>{{$ojt->company_address}}</td>
                                <td>{{$ojt->status}}</td>
                                

                                    
                            </tr>
                            @endforeach
                        </tbody>
                            
                    </table>
                        
                </div>

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