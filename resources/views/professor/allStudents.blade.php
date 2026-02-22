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

                
                <a href="{{ url('/professor/accountinfo') }}" style="text-decoration: none;">
                    <span class="iconname">
                        <ion-icon name="person-circle-outline"></ion-icon>
                    </span>
                    <span class="name"> {{ $user->full_name }} </span>
                    <span class="name2">PROFESSOR </span>
                </a>

                <a href="{{ url('/professor/accountinfo') }}" style="text-decoration: none;">
                    <span class="hidden-on-big">{{ $user->full_name }}</span>
                    <!-- <div class="toggle" id="toggle2">
                        <ion-icon name="menu-outline"></ion-icon>
                    </div> -->
                </a>

                <li >
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
                <h1>All Students</h1>
            </div>



           
           
            <!-- ================ Order Details List =================-->
            <div class="details">
                <div class="recentOrders">
                    {{-- <div class="cardHeader">
                        <h2>All Students</h2>
                    </div> --}}
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

<table id="fileTable" class="display">

    <thead>
        <tr>
            <td data-orderable="true">Student Name</td>
            <td data-orderable="true">Course</td>
            <td data-orderable="true">Year & Section</td>
          
            <td data-orderable="true">Subject Code</td>
      
          

        </tr>
    </thead>

    <tbody>
        @foreach ($studentData as $data)

        <tr>
            <td>{{$data['student']->first_name }} {{ $data['student']->last_name }}</td>
            <td>{{$data['student']->course }}</td>
            <td>{{ $data['student']->year_and_section }}</td>
            @if (isset($data['subjects']))
            @foreach ($data['subjects'] as $subject)
            <td>{{ $subject['subject_code'] }}</td>
            @endforeach
            @else
            <td>No subjects</td>

            @endif



            







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