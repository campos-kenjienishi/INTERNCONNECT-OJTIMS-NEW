<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->

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


                <li class="active">
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
                <h1>Class</h1>
            </div>



<!-- ================ Add Room Button & Modal ================= -->
<div class="buttons" style="margin-left: 70%;">
    <div class="AddProfBtn">
        <button class="updateBtn" type="button" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            Add New Room
        </button>
        <i class="uil uil-plus" style="font-size: 15px;color:white;"></i>
    </div>
</div> 

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addRoomModalLabel">Add Room</h1>
            </div>

            <div class="modal-body">
                <form id="addRoomForm" action="{{ url('/roomCreate') }}" method="post">
                    @csrf
                    <div class="form-group" style="font-size: 22px;">
                        <label class="form-label" for="room">Room Name:</label>
                        <input class="form-input" type="text" name="room" required>
                    </div>

                    <div class="form-group" style="font-size: 22px;">
                        <label class="form-label" for="course">Course:</label>
                        <select name="course" class="form-input" required>
                            @foreach ($course as $c)
                                <option value="{{ $c->course }}">{{ $c->course }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="buttons" style="margin-left: 100px;">            
                        <button class="modalCloseBtn" type="button" data-bs-dismiss="modal" style="font-size: 18px; font-weight: 400; background-color:#FFA800;">
                            Close
                        </button>
                        <button class="submitBtn" type="submit" style="font-size: 18px; font-weight: 400;">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 

<!-- ================ Room List ================= -->
<div class="details">
    <div class="recentOrders">
        <div class="cardHeader">
            <h2>Rooms</h2>
        </div>

        <table id="fileTable" class="display">
            <thead>
                <tr>
                    <td data-orderable="true">Course</td>
                    <td>Room Name</td>
                    <td>Needing Approval</td>
                    <td>Students List</td>
                    <td>Add Announcements</td>
                    <td>Delete Room</td>
                </tr>
            </thead>

            <tbody>
                @foreach ($class as $room)
                    <tr>
                        <td>{{ $room->course }}</td>
                        <td>{{ $room->room }}</td>

                        <td>
                            <button class="btnView1">
                                <i class="fa fa-eye"></i>
                                <a href="{{ url('/professor/listStudents', $room->course) }}" style="color: white; text-decoration: none;">
                                    View
                                </a>
                            </button>
                        </td>

                        <td>
                            <button class="btnView1">
                                <i class="fa fa-eye"></i>
                                <a href="{{ url('/professor/classList', $room->course) }}" style="color: white; text-decoration: none;">
                                    View
                                </a>
                            </button>
                        </td>

                        <td>
                            <!-- Add Announcement Modal Trigger -->
                            <button type="button" class="btnAdd" data-bs-toggle="modal" data-bs-target="#announcementModal{{ $loop->index }}">
                                <i class="fa fa-plus"></i> Add
                            </button>

                            <!-- Announcement Modal -->
                            <div class="modal fade" id="announcementModal{{ $loop->index }}" tabindex="-1" aria-labelledby="announcementModalLabel{{ $loop->index }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="announcementModalLabel{{ $loop->index }}">
                                                Create Announcement for {{ $room->room }}
                                            </h1>
                                        </div>
                                        <div class="modal-body">
                                            <form class="announcementForm" method="POST" action="{{ url('/announcements') }}">
                                                @csrf
                                                <input type="hidden" name="course" value="{{ $room->course }}">
                                                <input type="hidden" name="room" value="{{ $room->room }}">

                                                <div class="form-group" style="font-size: 22px;">
                                                    <label class="form-label" for="title">Title:</label>
                                                    <input class="form-input" type="text" id="title" name="title" required>
                                                </div>

                                                <div class="form-group" style="font-size: 22px;">
                                                    <label class="form-label" for="content">Content:</label>
                                                    <textarea class="form-input" id="content" name="content" rows="4" required></textarea>
                                                </div>

                                                <button type="submit" class="submitBtn">
                                                    Create Announcement
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button class="btnRemove" data-id="{{ $room->id }}">
                                Remove
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Include jQuery and DataTables scripts -->
                        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
                        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    
                        <!-- Enable sorting for the fileTable -->
                        <script>
                            $(document).ready(function() {
                                $('#fileTable').DataTable();
                            });
                        </script>
    

    <!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Function to show SweetAlert toast
    function showSuccessToast() {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "success",
            title: "Announcement created successfully!",
            customClass: {
                icon: 'custom-icon-class',
                title: 'custom-title-class'
            }
        });
    }
</script>

<!-- ================ AJAX for Add Room & Announcements ================= -->
<script>
$(document).ready(function() {

    // Add Room AJAX
    $('#addRoomForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                $('#addRoomModal').modal('hide');
                form[0].reset();

                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Room created successfully!',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                // Optionally: reload the table or append the new room dynamically
                location.reload();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error creating room');
            }
        });
    });

    // Announcement AJAX
    $('.announcementForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var modal = form.closest('.modal');

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                modal.modal('hide');
                form[0].reset();

                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Announcement created successfully!',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error creating announcement');
            }
        });
    });

    $('.btnRemove').on('click', function() {

        let roomId = $(this).data('id');

        Swal.fire({
            title: 'Remove this room?',
            text: "This will permanently delete the room and its records!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: 'POST',
                    url: '/roomDelete/' + roomId,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'Room deleted successfully!',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        location.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Error deleting room');
                    }
                });

            }
        });
    });
    // Initialize DataTables
    $('#fileTable').DataTable();
});
    
</script>
</body>
</html>


<!-- =========== Scripts =========  -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- ====== ionicons ======= -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
                 