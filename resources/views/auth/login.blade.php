<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <!-- CRITICAL: Prevents white flash -->
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Login</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">

</head>

<body>
<div class="main-wrapper">
    <div class="login-container">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
            <div class="orb orb-4"></div>

            <div class="brand-area">
                <div class="logo-wrapper">
                    <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="logo-img">
                    <div>
                        <div class="brand-name">Intern<span>Connect</span> - BETA</div>
                        <div class="system-title">OJT Information Management System</div>
                    </div>
                </div>

                <h1 class="hero-heading">
                    Smarter<br>
                    <span>OJT Management</span><br>
                    Starts Here.
                </h1>

                <p class="hero-desc">
                    InternConnect brings the On-The-Job training process into one centralized platform, helping students, coordinators, and professors manage information more efficiently.
                </p>

                <div class="stats-row">
                    <div>
                        <div class="stat-num">100%</div>
                        <div class="stat-label">Digital Management</div>
                    </div>
                    <div>
                        <div class="stat-num">Secure</div>
                        <div class="stat-label">Information System</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your InternConnect Account</p>
            </div>

            <form action="{{route('login-user')}}" method="post">
                @csrf

                @if(Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if(Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <div class="field-group">
                    <label class="form-label">E-mail Address</label>
                    <div class="input-wrap">
                        <i class="fa fa-envelope i-icon"></i>
                        <input type="text" placeholder="Enter your email" name="email" value="{{ old('email') }}" autocomplete="email">
                    </div>
                    <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                </div>

                <div class="field-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fa fa-lock i-icon"></i>
                        <input type="password" placeholder="Enter your password" name="password" autocomplete="current-password" required id="id_password">
                        <i class="far fa-eye toggle-pw" id="togglePassword"></i>
                    </div>
                    <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                </div>

                <div class="terms-wrap">
                    <div class="terms-text">
                        By signing in, you agree to our
                        <a href="{{ url('/terms') }}" target="_blank">Terms of Use</a> and
                        <a href="{{ url('/privacy') }}" target="_blank">Privacy Statement</a>.
                    </div>
                </div>

                <div class="btn-wrap">
                    <button type="submit" class="btn-login">
                        <i class="fa fa-sign-in-alt me-2"></i> Sign In
                    </button>
                </div>

                <div class="footer-wrap">
                    <div class="footer-links">
                        <a href="forgot"><i class="fa fa-key"></i> Forgot Password?</a>
                        <a href="registration"><i class="fa fa-user-plus"></i> Create Account</a>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/frontend/js/script.js') }}"></script>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('id_password');

    togglePassword.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        this.classList.remove('toggled');
        void this.offsetWidth;
        this.classList.add('toggled');
    });
</script>
</body>
</html>