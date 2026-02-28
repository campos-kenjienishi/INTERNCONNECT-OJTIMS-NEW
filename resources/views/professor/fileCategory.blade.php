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
                <li class="active">
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
                <h1>File Category</h1>
            </div>

              <!-- ================ Order Details List ================= -->
              <div class="buttons" style="margin-left: 70%;">
                <div class="AddProfBtn">
                    <button type="button" class="updateBtn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Add File Category
                        
                    </button><i class="uil uil-plus" style="font-size: 15px;"></i>
                </div>
            </div>
            
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add File Category</h1>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('/fileCategory') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="font-size: 22px;">
                                    <label class="form-label" for="fileName">File Name:</label>
                                    <input class="form-input" type="text" name="fileName">
                                </div>
                                <!-- Hidden input field to store the uploadedBy value -->
                                <input type="hidden" name="uploadedBy" value="{{ $userName }}">
                               
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

            <!--Courses--> 
            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>File Categories</h2>
                    </div>
                    
                    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function() {
                    $('#fileTable').DataTable();
                });
                </script>
                    <table id="fileTable" class="display">
                        <thead>
                            <tr>
                                <td data-orderable="true">File Name</td>
                                <td data-orderable="true">Uploaded By</td>
                                <td></td>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($files as $file)  
                        <tr>
                            <td>{{ $file->fileName }}</td> 
                            <td>{{ $file->uploadedBy }}</td>
                            <td>
                                <button class="btnRemove" style="margin-left: 50%;">
                                    <i class="fa fa-trash"></i>
                                    <span class="remove-button" data-file-id="{{ $file->id }}">Remove</span>  
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
  <script src="assets/js/main.js"></script>

  <!-- ====== ionicons ======= -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>




  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  
  <!-- Include SweetAlert library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
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
            text: 'You are about to remove this file category.',
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
            url: '/remove/files/' + fileId, // Make sure the route is correct
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