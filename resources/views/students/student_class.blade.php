<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png"> 
    <!-- ======= Styles ====== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

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

                <a href="{{ url('/student/accountinfo') }}" style="text-decoration: none;">
                    <span class="iconname">
                        <ion-icon name="person-circle-outline"></ion-icon>
                    </span>
                    <span class="name"> {{ $data->full_name }} </span>
                    <span class="name2">STUDENT </span>
                </a>

                <a href="{{ url('/student/accountinfo') }}" style="text-decoration: none;">
                    <span class="hidden-on-big">{{ $data->full_name }}</span>
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

                <li class="active">
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
                <h1>Class</h1>
            </div>

    <div class="details">
        <!-- Room List -->
        <div class="recentOrders" style="overflow-x: auto;">
            <div class="cardHeader">
                <h2>Rooms</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <td>Course</td>
                        <td>Room</td>
                        <td>Status</td>
                        <td>Action</td>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($class as $classItem)
                        <tr>
                            <td>{{ $classItem->course }}</td>
                            <td>{{ $classItem->room }}</td>
                            <td>
                                @if ($data->status == 1)
                                    <span style="color: green;">Approved</span>
                                @elseif ($data->status == 2)
                                    <span style="color: red;">Denied</span>
                                @elseif ($data->status == 3)
                                    <span style="color: orange;">Pending</span>
                                @else
                                    <span style="color: gray;">Not Joined</span>
                                @endif
                            </td>
                            <td>
                                @if ($data->status != 1 && $data->status != 3)
                                    <button class="btnJoin5" onclick="joinStudent('{{ url('/student/join', $data->email) }}')">Join</button>
                                @else
                                    <button class="btnRemove" onclick="leaveStudent()">Leave</button>
                                @endif

                                <button class="btnView1" data-bs-toggle="modal" data-bs-target="#modal{{ $loop->iteration }}" style="margin-left: 10%;">
                                    <i class="fa fa-eye"></i> View
                                </button>

                                <!-- Modal -->
                                <div class="modal" id="modal{{ $loop->iteration }}" tabindex="-1" aria-labelledby="modalLabel{{ $loop->iteration }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="modalLabel{{ $loop->iteration }}">Room Details</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Room Name:</strong> {{ $classItem->room }}</p>
                                                <p><strong>Course:</strong> {{ $classItem->course }}</p>
                                                <p><strong>Status:</strong>
                                                    @if ($data->status == 1)
                                                        <span style="color: green;">Approved</span>
                                                    @elseif ($data->status == 2)
                                                        <span style="color: red;">Denied</span>
                                                    @elseif ($data->status == 3)
                                                        <span style="color: orange;">Pending</span>
                                                    @else
                                                        <span style="color: gray;">Not Joined</span>
                                                    @endif
                                                </p>
                                                <p><strong>Adviser:</strong> {{ $classItem->adviser_name }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <script>
        function joinStudent(url) {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    location.reload();
                },
                error: function() {
                    console.error('An error occurred while processing your request.');
                }
            });
        }
        </script>

        <script>
        function leaveStudent() {
            Swal.fire({
                title: 'Leave this room?',
                text: 'You will be removed from this class.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, leave',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("/student/leave") }}',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            Swal.fire({
                                toast: true,
                                icon: 'success',
                                title: 'You left the room',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            location.reload();
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Oops!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }
        </script>

        <br>

        <!-- Announcements -->
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>Announcements</h2>
            </div>

            <table id="ATable" class="display">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Comments</th>
                        <th>Date</th>
                        <th>Announced By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announce as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->content }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->announcer }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
