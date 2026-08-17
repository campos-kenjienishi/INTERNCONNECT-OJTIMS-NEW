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
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/login-responsive.css') }}?v={{ time() }}">

    <style>
        /* Back to Portal Pill Button */
        .back-btn-pill,
        .btn-switch-portal {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            color: #fca5a5 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            background: rgba(255, 255, 255, 0.08) !important;
            padding: 6px 16px !important;
            border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            transition: all 0.25s ease !important;
            cursor: pointer !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
        }

        .back-btn-pill:hover,
        .btn-switch-portal:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.18) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            transform: translateY(-1px) !important;
            text-decoration: none !important;
        }

        body.auth-centered-page {
            position: relative !important;
            z-index: 1 !important;
        }

        body.auth-centered-page .main-wrapper {
            position: relative !important;
            z-index: 5 !important;
            width: 100% !important;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        body.auth-centered-page .login-container {
            max-width: 600px;
            min-height: auto;
            position: relative !important;
            z-index: 999 !important;
            animation: none !important;
            transform: none !important;
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
            position: relative !important;
            z-index: 1000 !important;
            pointer-events: auto !important;
            animation: none !important;
            transform: none !important;
        }

        body.auth-centered-page .right-panel input,
        body.auth-centered-page .right-panel button:not(.voice-mic-button),
        body.auth-centered-page .right-panel a,
        body.auth-centered-page .right-panel .input-wrap {
            pointer-events: auto !important;
            position: relative !important;
            z-index: 1001 !important;
            touch-action: manipulation !important;
        }

        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .auth-logo {
            object-fit: contain;
            filter: drop-shadow(0 0 16px rgba(255,255,255,0.18));
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

        .local-signin-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fca5a5;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .input-wrap {
            position: relative !important;
            width: 100% !important;
        }

        .input-wrap {
            position: relative !important;
            width: 100% !important;
            display: block !important;
        }

        body.auth-centered-page .right-panel .input-wrap input {
            width: 100% !important;
            background: #f7f4ee !important;
            border: 1px solid #ddd7cb !important;
            color: #3b0000 !important;
            padding: 12px 48px 12px 46px !important;
            height: 48px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            position: relative !important;
            z-index: 2 !important;
            pointer-events: auto !important;
        }

        .input-wrap input::placeholder {
            color: #9a9080 !important;
        }

        .input-wrap input:focus {
            background: #fffdf9 !important;
            border-color: #cdbfa9 !important;
        }

        .input-wrap .i-icon {
            position: absolute !important;
            left: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #ef4444 !important;
            font-size: 15px !important;
            z-index: 10 !important;
            pointer-events: none !important;
            display: inline-block !important;
        }

        .input-wrap .toggle-pw {
            position: absolute !important;
            right: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #ef4444 !important;
            font-size: 16px !important;
            z-index: 50 !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            display: inline-block !important;
            line-height: 1 !important;
        }

        .input-wrap .toggle-pw:hover {
            color: #fca5a5 !important;
        }

        /* Clean Voice Input Mic Button */
        body.auth-centered-page .right-panel .voice-mic-button,
        .input-wrap .voice-mic-button {
            position: absolute !important;
            right: 12px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 28px !important;
            height: 28px !important;
            border-radius: 50% !important;
            background: #ffffff !important;
            border: 1.5px solid #ef4444 !important;
            color: #ef4444 !important;
            font-size: 13px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 100 !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
            transition: all 0.2s !important;
        }

        /* On password field with toggle-pw, shift voice-mic-button left */
        body.auth-centered-page .right-panel .input-wrap #id_password ~ .voice-mic-button,
        body.auth-centered-page .right-panel .input-wrap input[type="password"] ~ .voice-mic-button {
            right: 46px !important;
        }

        .btn-wrap {
            margin-top: 14px;
            width: 100%;
        }

        .btn-login {
            width: 100% !important;
            height: 48px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35) !important;
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
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .terms-line-bottom {
            display: block;
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .terms-line-bottom a {
            color: #fca5a5 !important;
            text-decoration: underline;
        }

        .terms-line-separator {
            color: rgba(255, 255, 255, 0.85) !important;
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
            <div style="width: 100%; margin-bottom: 12px; text-align: left;">
                <a href="{{ url('/login-gateway') }}" class="back-btn-pill">
                    <i class="fa fa-arrow-left"></i> Back to Portal Selection
                </a>
            </div>

            <div class="auth-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="auth-logo">
                <div class="auth-brand-copy">
                    <div class="brand-name">Intern<span>Connect</span> - BETA</div>
                    <div class="system-title">On-The-Job Training Information Management System</div>
                </div>
            </div>

            @php
                $activePortal = request()->query('portal', session('target_login_portal', 'student'));
            @endphp

            <div class="login-header">
                @if($activePortal === 'faculty')
                    <div class="local-signin-badge" style="background: rgba(220, 38, 38, 0.2); border-color: #ef4444; color: #fca5a5;">
                        <i class="fa fa-user-shield me-1"></i> FACULTY & STAFF PORTAL (Local Sign In)
                    </div>
                    <p>Sign in using your Faculty or Coordinator account</p>
                @else
                    <div class="local-signin-badge" style="background: rgba(30, 144, 255, 0.2); border-color: #1e90ff; color: #93c5fd;">
                        <i class="fa fa-graduation-cap me-1"></i> STUDENT PORTAL (Local Sign In)
                    </div>
                    <p>Sign in using your Student account</p>
                @endif
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success py-2 px-3 mb-3 text-center" style="font-size: 13px;">{{ Session::get('success') }}</div>
            @endif
            @if(Session::has('fail'))
                <div class="alert alert-danger py-2 px-3 mb-3 text-center" style="font-size: 13px;">{{ Session::get('fail') }}</div>
            @endif

            <form action="{{ route('login-user') }}" method="post">
                @csrf
                <input type="hidden" name="portal" value="{{ $activePortal }}">

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
                            <i class="fa fa-eye toggle-pw" id="togglePassword" aria-label="Toggle password visibility"></i>
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
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/frontend/js/script.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
</body>
</html>