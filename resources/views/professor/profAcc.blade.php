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
                <h1 >Account Information</h1>
            </div>

             <!-- Editable Account Information Form -->
             <div class="detailsacc">
                <div class="recentOrdersacc">
                    <div class="cardHeader">
                        <h2>Personal Details</h2>
                    </div>

                    <div class="accdetails">
                        <div class="accounts">
                    <form class="account-form" action="{{url('/professor/edit',$data->email)}}" method="post" style="color: white;">
                        @csrf
                        @method('PUT')

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First Name:</label>
                                <input class="form-input" type="text" id="first_name" name="first_name" value="{{$data->first_name}}">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="middle_name">Middle Name:</label>
                                <input class="form-input" type="text" id="middle_name" name="middle_name" value={{$data->middle_name}}>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name:</label>
                                <input class="form-input" type="text" id="last_name" name="last_name" value="{{$data->last_name}}">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="suffix">Suffix:</label>
                                <input class="form-input" type="text" id="suffix" name="suffix" value={{$data->suffix}}>
                            </div>
                            <!-- Add more form fields for the first column here -->
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="address">Address:</label>
                                <input class="form-inputadd" type="text" id="address" name="address" value="{{$data->address}}">
                            </div>
                        </div>

                        <div class="form-row">
                            
                            <div class="form-group">
                                <label class="form-label" for="contact_number">Contact No:</label>
                                <input class="form-input" type="text" id="contact_number" name="contact_number" value={{$data->contact_number}}>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="date_of_birth">Date of Birth:</label>
                                <input class="form-input" type="date" id="date_of_birth" name="date_of_birth" value={{$data->date_of_birth}}>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">E-mail:</label>
                                <input class="form-input" type="email" id="email" name="email" value={{$data->email}}>
                            </div>
                            <!-- Add more form fields for the second column here -->
                        </div>
                    

                    {{-- <div class="form-group">
                        <button class="edit-button" type="submit">Update</button>
                    </div> --}}

                    
                        
                        <button class="updateBtn" type="submit">Update</button>
                    </form>

                    <button class="updateBtnPass" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <span class ="btnText">Change Password </span>
                        <i class="uil uil-lock" style="font-size: 14px; margin-left: 5px;"></i>
                    </button>  
                        
                        
                    
                        
                       
                         
                            
                        
                        </div>  
                    </div>
                </div>
            </div>



                       <!-- Modal -->
                       <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Change Password</h1>
                                </div>
        
                                <div class="modal-body">

                                                                        <!-- Display Validation Errors -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <td>{{ $error }}</td>
            @endforeach
        </ul>
    </div>
@endif

                                               <!-- Change Password -->
                    <form class="account-form" action="{{url('/change_password',$data->id)}}" method="post">
                        @csrf
                        @method('PUT')
            
                    <div class="form-group form-row">
                        <label class="form-label" for="current_password">Current Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            <span class="input-group-text" id="toggleCurrent" style="cursor:pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                            <span class="text-danger">@error('current_password') {{$message}} @enderror</span>
                        </div>
                    </div>

                    <div class="form-group form-row">
                        <label class="form-label" for="new_password">New Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <span class="input-group-text" id="toggleNew" style="cursor:pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                            <span class="text-danger">@error('new_password') {{$message}} @enderror</span>
                        </div>
                    </div>

                    <div class="form-group form-row">
                        <label class="form-label" for="confirm_password">Confirm New Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            <span class="input-group-text" id="toggleConfirm" style="cursor:pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                            <span class="text-danger">@error('confirm_password') {{$message}} @enderror</span>
                        </div>
                    </div>
                
                        <div class="buttonsSection" >
                            <button class="closeBtn" type="button" data-bs-dismiss="modal"> Close </button>
                            <button type="submit" class="printBtn">Update</button>
                        </div>
                    <script>
                    document.addEventListener("DOMContentLoaded", function() {

                        function setupToggle(toggleId, inputId) {
                            const toggle = document.getElementById(toggleId);
                            const input = document.getElementById(inputId);

                            toggle.addEventListener('click', function () {
                                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                                input.setAttribute('type', type);

                                this.querySelector('i').classList.toggle('fa-eye-slash');
                            });
                        }

                        setupToggle('toggleCurrent', 'current_password');
                        setupToggle('toggleNew', 'new_password');
                        setupToggle('toggleConfirm', 'confirm_password');

                    });
                    </script>
                                    </form>
        
                                </div>
                                
                            </div>
                            
                        </div>
                        
                    </div>


    <!-- =========== Scripts =========  -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

</body>

</html>