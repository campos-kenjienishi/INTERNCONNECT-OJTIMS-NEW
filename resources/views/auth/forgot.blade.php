<!DOCTYPE html>
<html lang="en">
<head>
    
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">

    <link rel="stylesheet" href="/frontend/css/custom.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>

<body>
    <div class="card">
        <img src="/images/final-puptg_logo-ojtims_nbg.png">
        <br>
        
        <div class="container">
            <div class="row">
                <!-- <h2>On-<span>t</span>he-<span>j</span>ob<span> T</span>raining<span> I</span>nformation<span> M</span>anagement<span> S</span>ystem</h2> -->
                <h2>InternConnect<br>OJT INFORMATION MANAGEMENT SYSTEM</h2>
                <h4>Reset Password</h4>

                <form action="{{url('/forgotPass')}}" method="post">
                    @if(Session::has('success'))
                    <div class="alert alert-success">{{Session::get('success')}}</div>
                    @endif
                    @if(Session::has('fail'))
                    <div class="alert alert-danger">{{Session::get('fail')}}</div>
                    @endif

                    @csrf

                    <br>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="text" class="form-control" placeholder="Enter Email"
                        name="email" >
                    </div>

                    <br>

                    <div class="form-group">
                        <button class="btn btn-block btn-primary" type="submit">Send Reset Link</button>
                    </div>

                    <br>
                    <a href="login" style="text-decoration: none; color:maroon;">Login Here...</a>
                </form>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</html>