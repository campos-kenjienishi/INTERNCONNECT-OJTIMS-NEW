<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
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
                    <span class="name">{{ $data->full_name }}</span>
                    <span class="name2">OJT COORDINATOR</span>
                </a>

                <li>
                    <a href="{{ url('/dashboard') }}">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
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

                <li>
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

                <li class="active">
                    <a href="{{ url('/auditlog') }}">
                        <span class="icon">
                            <ion-icon name="clipboard-outline"></ion-icon>
                        </span>
                        <span class="title">Audit Log</span>
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
                <span class="subtitle">On-the-Job Training Information Management System</span>
            </div>

            <div class="dash">
                <h1>Audit Log</h1>
            </div>

            <!-- Audit Log Table -->
            <div class="details" style="margin-left: 2%; width: 95%; overflow-x: auto;">
                <div class="recentOrders" style="width: 100%;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Action</th>
                                <th>Name</th>
                                <th>Module</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->affected_name ?? 'N/A' }}</td>
                                    <td>{{ $log->module }}</td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; color: gray; font-style: italic;">
                                        No audit logs yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>