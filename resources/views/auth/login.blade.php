<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Login</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/pages/auth-login.css') }}">
</head>

<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container">

        <!-- LEFT PANEL (hidden on centered layout) -->
        <div class="left-panel">
            <div class="left-panel-content">
                <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="PUP Logo" class="side-logo">
                <h1 class="side-brand">InternConnect</h1>
                <p class="side-desc">PUP Taguig On-the-Job Training Management System</p>
                <div class="side-stats">
                    <div>
                        <div class="stat-num">Fast</div>
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
            @php
                $activePortal = request()->query('portal', session('target_login_portal', 'student'));
            @endphp

            <div style="width: 100%; margin-bottom: 12px; text-align: left;">
                <a href="{{ url('/login-gateway?portal=' . $activePortal) }}" class="back-btn-pill">
                    <i class="fa fa-arrow-left"></i> Back to Sign-In Options
                </a>
            </div>

            <div class="auth-brand">
                <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect Logo" class="auth-logo">
                <div class="auth-brand-copy">
                    <div class="brand-name">Intern<span>Connect</span></div>
                    <div class="system-title">On-the-Job Training (OJT) Information Management System</div>
                </div>
            </div>

            <div class="login-header">
                @if($activePortal === 'faculty')
                    <div class="portal-badge">
                        <div class="portal-badge-icon-wrap">
                            <i class="fa fa-user-shield"></i>
                        </div>
                        <span><span class="portal-badge-highlight">FACULTY &amp; STAFF</span> PORTAL</span>
                    </div>
                    <p>Sign in using your Faculty or Coordinator Account</p>
                @else
                    <div class="portal-badge">
                        <div class="portal-badge-icon-wrap">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <span><span class="portal-badge-highlight">STUDENT</span> PORTAL</span>
                    </div>
                    <p>Sign in using your Student Account</p>
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
                        <span class="terms-line-top">By using this service, you understand and agree to the PUP Online Services</span>
                        <span class="terms-line-bottom">
                            <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a> <span class="terms-line-separator">and</span>
                            <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>.
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
<script src="{{ vasset('js/pages/auth-login.js') }}"></script>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>
