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

                <li>
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
                <h1>Requirements</h1>
            </div>

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
                    window.location.href = "{{ url('/professor/classList', $course) }}";
                });
            </script>

   
            <!-- ================ Order Details List =================-->
            <div class="details">
                <div class="recentOrders">
                    
                    <div class="table-container">
                        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                        <table id="fileTable" class="display">
                            <thead>
                                <tr>
                                    <th data-orderable="true">Category</th>
                                    <th>File</th>
                                    <th data-orderable="true">Date and Time Submitted</th>
                                    <th>Actions</th>
                                    <th></th>
                                    <th></th>

                                  
                                </tr>
                            </thead>
                        
                            <tbody>
                                @foreach($files as $files)
                                <tr>
                                    <td>{{ $files->fileName }}</td>
                                    <td>{{ $files->file }}</td>
                         
                                    <td>{{ $files->created_at }}</td>
                                    <td>
                                        @if($files->status != 1 && $files->status != 2)
                                        <!-- Approve Button Form -->
                                    <form id="approveForm" method="POST" action="/update/approve/status/{{ $files->id }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="approveButton" style="background-color: green; border-radius: 12px; padding: 5px 10px; border-color: green; color: white;">
                                            Approve
                                        </button>
                                    </form>
                                            @else
                                            <!-- Display the status -->
                                            @if ($files->status == 1)
                                                <span style="color: green;">Approved</span>
                                            @elseif ($files->status == 2)
                                                <span style="color: red;">Denied</span>
                                            
                                            @endif
                                        @endif
                                        
                                    </td>  
                                    
                                    <td>
                                        @if($files->status != 1 && $files->status != 2)
                                        <!-- Deny Button Form -->
                                    <form id="denyForm" method="POST" action="/update/denied/status/{{ $files->id }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="denyButton" style="background-color: red; border-radius: 12px; padding: 5px 10px; border-color: red; color: white;">
                                            Deny
                                        </button>
                                    </form>
                                            @else
                                            <!-- Display the status -->
                                            @if ($files->status == 1)
                                                <span style="color: green;"></span>
                                            @elseif ($files->status == 2)
                                                <span style="color: red;"></span>
                                            
                                            @endif
                                        @endif
                                    </td>


                                    <td>   
                                        
                                        <button class="btnView">
                                        <a href="/requireview?file={{ $files->fileName }}&value={{ $value }}" style="color: white; text-decoration: none;">
                                            <span class="remove-button">View</span>
                                        </a>
                                    </button>

                                    <button style="background-color: green;border-radius: 12px;padding: 5px 10px;border-color : green">
                                        <a href="{{url('/download/req',$files->id)}}" style="color:white;font-family: 'Poppins', sans-serif;text-decoration:none;">Download</a>
                                    </button>
                                
                                
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
    </div>


            
</body>
</html>


<script src="{{url('/assets/js/main.js')}}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<!-- Enable sorting for the fileTable -->
<script>
    $(document).ready(function() {
        $('#fileTable').DataTable();
    });
</script>
