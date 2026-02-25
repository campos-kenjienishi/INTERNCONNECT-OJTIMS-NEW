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
                    </li>

                    <li class="active">
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
                <h1>File Requirements</h1>
            </div>

            <!-- ================ Order Details List =================-->


            <div class="buttons" style="margin-left: 70%;">
                <div class="AddProfBtn">
                    <button class="updateBtn" type="updateBtn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                       Upload Document
                    </button>
                    <i class="uil uil-plus" style="font-size: 15px;"></i>
                </div>
            </div>
  
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Submit File</h1>
                        </div>

                        <div class="modal-body modal-scrollable">
                            <form action="{{url('/uploadReq')}}" method="post" enctype="multipart/form-data">

                                @csrf
                                <table>
                                    
                                    <div class="form-group" style="font-size: 22px;">
                                        
                                        <label class="form-label" for="fileName">Category:</label>
                                        <select class="form-select" name="fileName" required>
                                            <option value="">Select Category</option>
                                            @foreach($fileCategories as $category)
                                                <option value="{{ $category->fileName }}">{{ $category->fileName }}</option>
                                            @endforeach
                                        </select>
                                        <br>
                                        <label class="form-label" for="file">Choose File:</label>
                                        <input class="form-input" type="file" name="file" required>
                                        <!-- Hidden input field to store-->
                                         <input type="hidden" name="uploadedBy" value="{{ $user->full_name }}">
                                         <input type="hidden" name="adviser" value="{{ $user->adviser_name }}">
   

                                    </div>

                                </table> 
                              

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
                       
                       
                        
                        <table id="fileTable" class="display">
                            <thead>
                                <tr>
                                    <th data-orderable="true">Category</th>
                                    <th>File</th>
                                    <th data-orderable="true">Status</th>
                                    <th data-orderable="true">Date and Time Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                @foreach($data as $files)
                                <tr>
                                    <td>{{ $files->fileName }}</td>
                                    <td>{{ $files->file }}</td>
                                    <td>
                                        @if ($files->status == 1)
                                        <span style="color: green;">Approved</span>
                                        @elseif ($files->status == 2)
                                        <span style="color: red;">Denied</span>
                                        @elseif ($files->status == 0)
                                        <span style="color: orange;">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $files->created_at }}</td>
                                    <td>
                                        <!-- Button for removing item -->
                                        <button class="btnRemove" style="margin-left: 10%;">
                                            <i class="fa fa-trash"></i>
                                            <span class="remove-button" data-file-id="{{ $files->id }}">Remove</span>
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
</body>
</html>


<!-- =========== Scripts =========  -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- ====== ionicons ======= -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Include DataTables -->
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
 <!-- Include SweetAlert library -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
<!-- Your DataTables initialization script -->
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#fileTable').DataTable();

        // Add event listener to "Remove" button click
        $('.remove-button').click(function(event) {
            // Prevent default button click behavior
            event.preventDefault();
            // Get the file ID associated with the button
            var fileId = $(this).data('file-id');
            // Call the showRemoveConfirmation function
            showRemoveConfirmation(fileId);
        });
    });

    // Function to show Sweet Alert confirmation dialog for removal
    function showRemoveConfirmation(fileId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to remove this requirement.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If user confirms, proceed with removal
                removeFileCategory(fileId);
            }
        });
    }

    // Function to remove file category via AJAX
    function removeFileCategory(fileId) {
        $.ajax({
            type: 'POST',
            url: '/remove/filesReq/' + fileId, // Make sure the route is correct
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                // Reload the page after successful removal
                location.reload();
            },
            error: function() {
                console.error('An error occurred while processing your request.');
            }
        });
    }
</script>