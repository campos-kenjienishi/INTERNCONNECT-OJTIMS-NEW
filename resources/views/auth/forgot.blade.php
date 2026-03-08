<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <!-- CRITICAL: Prevents white flash -->
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Reset Password</title>
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
                        <div class="brand-name">Intern<span>Connect</span></div>
                        <div class="system-title">OJT Information Management System</div>
                    </div>
                </div>

                <h1 class="hero-heading">
                    Recover your<br>
                    <span>account</span><br>
                    securely.
                </h1>

                <p class="hero-desc">
                    No worries — it happens to everyone. Enter your registered email and we'll send you a secure link to reset your InternConnect password right away.
                </p>

                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-envelope"></i></div>
                        <div class="step-text">
                            <strong>Enter your email</strong>
                            Use the email linked to your account
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-paper-plane"></i></div>
                        <div class="step-text">
                            <strong>Check your inbox</strong>
                            We'll send a secure reset link instantly
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-lock"></i></div>
                        <div class="step-text">
                            <strong>Set a new password</strong>
                            Click the link and create a new password
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">

            <!-- Floating lock icon -->
            <div class="lock-icon-wrap">
                <div class="lock-circle">
                    <i class="fa fa-lock"></i>
                </div>
            </div>

            <div class="forgot-header">
                <h2>Forgot Password? 🔐</h2>
                <p>Enter your registered email address and we'll send you a link to reset your password.</p>
            </div>

            <form action="{{url('/forgotPass')}}" method="post">
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
                        <input type="text" placeholder="Enter your registered email" name="email" autocomplete="email">
                    </div>
                </div>

                <div class="info-note">
                    <i class="fa fa-info-circle"></i>
                    <p>Make sure to check your spam or junk folder if you don't see the email in your inbox within a few minutes.</p>
                </div>

                <div class="btn-wrap">
                    <button type="submit" class="btn-reset">
                        <i class="fa fa-paper-plane me-2"></i> Send Reset Link
                    </button>
                </div>

                <div class="footer-wrap">
                    <a href="login"><i class="fa fa-arrow-left"></i> Back to Sign In</a>
                </div>

            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/frontend/js/script.js') }}"></script>
</body>
</html>