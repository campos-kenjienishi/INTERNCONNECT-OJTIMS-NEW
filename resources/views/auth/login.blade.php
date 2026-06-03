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
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-responsive.css') }}">

    <style>
        body.auth-centered-page .login-container {
            max-width: 600px;
            min-height: auto;
        }

        body.auth-centered-page .main-wrapper {
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        body.auth-centered-page .left-panel {
            display: none !important;
        }

        body.auth-centered-page .right-panel {
            width: 100%;
            flex: 1 1 auto;
            min-height: 450px;
            padding: 30px 38px;
            justify-content: center;
        }

        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .auth-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 0 16px rgba(255,255,255,0.18));
        }

        .auth-brand-copy .brand-name {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .auth-brand-copy .system-title {
            font-size: 10px;
            letter-spacing: 2px;
        }

        .auth-brand-copy .system-title,
        .login-header p {
            text-transform: none;
        }

        .login-header {
            margin-bottom: 24px;
            text-align: center;
        }

        .login-header p {
            margin-bottom: 0;
            font-size: 14px;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .input-wrap input {
            background: #f7f4ee;
            border: 1px solid #ddd7cb;
            color: #3b0000;
        }

        .input-wrap input::placeholder {
            color: #9a9080;
        }

        .input-wrap input:focus {
            background: #fffdf9;
            border-color: #cdbfa9;
        }

        .input-wrap .i-icon {
            color: #ef4444 !important;
            z-index: 2;
        }

        .footer-wrap {
            margin-top: 8px;
        }

        .signup-outside {
            margin-top: 0;
            text-align: center;
            width: 100%;
            max-width: 860px;
        }

        .signup-outside span {
            color: #fff;
        }

        .signup-outside a {
            color: #fca5a5;
            text-decoration: underline;
            font-weight: 600;
        }

        .signup-outside a:hover {
            text-decoration: underline;
        }

        .terms-wrap {
            margin-top: 16px;
            margin-bottom: 14px;
        }

        .terms-text {
            font-size: 13px;
            line-height: 1.5;
        }

        .terms-text span {
            display: inline;
        }

        .terms-line-top {
            display: block;
            margin-bottom: 2px;
            color: rgba(255,255,255,0.5);
        }

        .terms-line-bottom {
            display: block;
            color: #fff;
        }

        .terms-line-bottom a {
            color: #fca5a5;
            text-decoration: underline;
        }

        .terms-line-separator {
            color: rgba(255,255,255,0.5);
        }

        .btn-wrap {
            margin-top: 6px;
        }

        .btn-login {
            width: 100%;
        }

        @media (max-width: 767px) {
            body.auth-centered-page .right-panel {
                min-height: auto;
                padding: 32px 22px;
            }

            .auth-logo {
                width: 58px;
                height: 58px;
            }

            .auth-brand-copy .brand-name {
                font-size: 20px;
            }
        }
    </style>

</head>

<body class="auth-centered-page">
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
                        <div class="system-title">On-The-Job Training Information Management System</div>
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
            <div class="auth-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="auth-logo">
                <div class="auth-brand-copy">
                    <div class="brand-name">Intern<span>Connect</span> - BETA</div>
                    <div class="system-title">On-The-Job Training Information Management System</div>
                </div>
            </div>

            <div class="login-header">
                <p>Log in to your InternConnect Account</p>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if(Session::has('fail'))
                <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            @endif

            {{-- IdP fallback warning removed --}}

            @if(config('services.idp.enabled'))
                <div class="terms-wrap">
                    <div class="terms-text">
                        Authentication is managed by our Identity Provider. Use external sign-in to continue.
                    </div>
                </div>

                <div class="btn-wrap">
                    <a href="{{ route('login.external') }}" class="btn-login text-decoration-none d-inline-flex align-items-center justify-content-center">
                        <i class="fa fa-sign-in-alt me-2"></i> Sign In With Identity Provider
                    </a>
                </div>
            @else
                <form action="{{ route('login-user') }}" method="post">
                    @csrf

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

                    <div class="footer-wrap" style="margin-top: -2px; margin-bottom: 12px; text-align: left;">
                        <div class="footer-links" style="justify-content: flex-start;">
                            <a href="forgot"><i class="fa fa-key"></i> Forgot Password?</a>
                        </div>
                    </div>

                    <div class="terms-wrap">
                        <div class="terms-text">
                            <span class="terms-line-top">By using this service, you understood and agree to the PUP Online Services</span>
                            <span class="terms-line-bottom">
                                <a href="{{ url('/terms') }}" target="_blank">Terms of Use</a> <span class="terms-line-separator">and</span>
                                <a href="{{ url('/privacy') }}" target="_blank">Privacy Statement</a>.
                            </span>
                        </div>
                    </div>

                    <div class="btn-wrap">
                        <button type="submit" class="btn-login">
                            <i class="fa fa-sign-in-alt me-2"></i> Log in
                        </button>
                    </div>

                </form>
            @endif
        </div>

    </div>

    <div class="signup-outside">
        <span>Don't have an account? </span><a href="registration">Sign up</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/frontend/js/script.js') }}"></script>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('id_password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            this.classList.remove('toggled');
            void this.offsetWidth;
            this.classList.add('toggled');
        });
    }
</script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
<script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
</html>