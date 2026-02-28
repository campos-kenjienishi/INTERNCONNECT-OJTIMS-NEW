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
                /* Style the content container */
                .content-container {
                    display: flex; /* Use flexbox layout */
                    flex-wrap: wrap; /* Allow columns to wrap to the next row on smaller screens */
                    gap: 20px; /* Spacing between columns */
                    justify-content: space-between; /* Distribute columns evenly horizontally */
                }
            
                /* Style each column */
                .column {
                    flex-basis: calc(50% - 10px); /* Set column width to 50% minus spacing */
                    box-sizing: border-box; /* Include padding and border in column width */
                    padding: 10px; /* Add padding to the columns */
                    background-color: #f0f0f0; /* Background color for columns (adjust as needed) */
                   
                }
            
                /* Center the iframe within the second column */
                .column iframe {
                    display: block; /* Make the iframe a block-level element */
                    margin: 0 auto; /* Center horizontally within the column */
                }
            
                /* Add additional styling for the content within columns */
                .student-info {
                    margin-bottom: 10px; /* Add spacing between student info items */
                }
    
    
                /* Add this CSS to your stylesheet */
                .scrollable-container {
                    max-height: 80vh; /* Adjust the maximum height as needed */
                    overflow-y: auto;
                    padding: 20px; /* Optional: Add padding for spacing */
                    /* Add any other styles you need for the container */
                }
                 /* Add this CSS to your stylesheet */
                .scrollable-container {
             
                    max-height: 600px;
                    overflow-y: auto;
                    padding: 20px; /* Optional: Add padding for spacing */
                    /* Add any other styles you need for the container */
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
                <h1>Company Information</h1>
            </div>

            <!-- ================ Order Details List =================-->

            <div class="buttons" style="margin-left: 70%;">
                <div class="AddProfBtn">
                <i class="fa fa-arrow-left" aria-hidden="true"  style="font-size: 15px;color:white;"></i>
                    <button type="button" class="updateBtn"><a href="{{ url('/MOA') }}" style="color:white;text-decoration:none;">
                        Previous
                    </button>
                   
                </div>
            </div>

   
            <div class="details" >
                <div class="recentOrders" style="height: 700px; overflow-y: auto; font-size:12px;">
                    <div class="cardHeader">
                        
                    </div>
        
                    <table>
                        <thead>
                            <tr>
                                <td>Company Name</td>
                                <td>Company Address</td>
                                <td>Company Representative</td>
                                <td>Company Contact No.</td>
                                <td>Company Email</td>
                                <td>School Year</td>
                            </tr>
                        </thead>
        
                        <tbody>
                            <tr>
                                <td>{{ $company->company_name }}</td>
                                <td>{{ $company->company_address }}</td>
                                <td>{{ $company->company_rep }}</td>
                                <td>{{ $company->companyNo }}</td>
                                <td>{{ $company->company_email }}</td>
                                <td>{{ $company->school_year }}</td>
                            </tr>
                        </tbody>
                    </table>
        
                    <br>
        
                    <div class="content-container">
                        <div class="column">
                            <h3>Student List</h3>
                            <div class="scrollable-container">
                                @foreach ($company->students as $student)
                                <div class="student-info" style="color: black;">
                                    <strong>Student Name:</strong> {{ $student->full_name }}<br>
                                    <strong>Email:</strong> {{ $student->email }}<br>
                                    <strong>Student No:</strong> {{ $student->studentNum }}<br>
                                    <strong>Date of Birth:</strong> {{ $student->date_of_birth }}<br>
                                    <strong>Contact Number:</strong> {{ $student->contact_number }}<br>
                                    <strong>Address:</strong> {{ $student->address }}<br>
                                    <strong>Course:</strong> {{ $student->course }}<br>
                                    <strong>Year and Section:</strong> {{ $student->year_and_section }}<br>
                                    <strong>Professor Name:</strong> {{ $student->adviser_name }}<br>
                                    <br>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="column">
                            <h3>Memorandum of Agreement</h3>
                            <div style="text-align: center;">
                              
                                    <iframe height="600" width="650" src="/assets/{{ $company->file }}"></iframe>
                                
                            </div>
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



