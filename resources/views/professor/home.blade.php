<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

                <li class="active">
                    <a href="{{ url('/professor/home') }}">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title" >Dashboard</span>
                    </a>

                </li>

                
                <li>
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
                <h1>Dashboard</h1>
            </div>



            <!-- ======================= Cards ================== -->
            <div class="cardBox">
                <a href="{{ url('/allStudents') }}" style="color:maroon;text-decoration:none;">
                <div class="card">
                    
                    <div>
                        
                        <div class="numbers">{{ $roleCount }}</div>
                        <div class="cardName">Students</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <a href="{{ url('/professor/upload') }}" style="color:maroon;text-decoration:none;">
                        <div class="numbers">{{ $fileCount }}</div>
                        <div class="cardName" style="font-size: 18px;">Downloadable Templates</div>
                        
                    </div>

                    <div class="iconBx">
                        <ion-icon name="cloud-upload-outline"></ion-icon>
                    </div>
                    </a>
                </div>

            </div>
            <br>
            <!-- ================ Order Details List =================-->
            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Companies</h2>
                    </div>
                    <div class="table-container">
                        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $('#companyTable').DataTable({
                                    "order": [[0, 'desc']], // Sort by the first column (ID) in descending order
                                    "columnDefs": [
                                        { "targets": 0, "visible": false } // Hide the first column (index 0)
                                    ]
                                });
                            });
                        </script>

                        <table id="companyTable" class="display">
                            <thead>
                                <tr>
                                    <td>id</td>
                                    <td data-orderable="true">Company Name</td>
                                    <td data-orderable="true">Company Address</td>
                                    {{-- <td data-orderable="true">Company Representative</td> --}}
                                    <td data-orderable="true">Company Contact No.</td>
                                    <td data-orderable="true">Company Email</td>
                                    {{-- <td data-orderable="true">School Year</td> --}}
                                
                              
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($companies as $companies)
                                <tr>
                                    <td>{{$companies->id}}</td>
                                    <td>{{$companies->company_name}}</td>
                                    <td>{{$companies->company_address}}</td>
                                    {{-- <td>{{$companies->company_rep}}</td> --}}
                                    <td>{{$companies->companyNo}}</td>
                                    <td>{{$companies->company_email}}</td>
                                    {{-- <td>{{$companies->school_year}}</td> --}}

                                 

                                  
                                        
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


            
</body>
</html>


<script src="{{url('/assets/js/main.js')}}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>