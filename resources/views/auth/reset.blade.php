<?php
$email = $_GET['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Reset Password</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/pages/auth-reset.css') }}">
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
                        <div class="system-title">OJT Information Management System</div>
                    </div>
                </div>

                <h1 class="hero-heading">
                    Create your<br>
                    <span>New Password</span><br>
                    Securely.
                </h1>

                <p class="hero-desc">
                    You're almost there! Set a strong new password for your InternConnect account. Make sure it's something only you know.
                </p>

                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-shield-alt"></i></div>
                        <div class="step-text">
                            <strong>Use 8+ characters</strong>
                            Mix letters, numbers and symbols
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-eye-slash"></i></div>
                        <div class="step-text">
                            <strong>Keep it private</strong>
                            Never share your password with anyone
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-icon"><i class="fa fa-sync-alt"></i></div>
                        <div class="step-text">
                            <strong>Change it regularly</strong>
                            Update your password every few months
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel{{ Session::has('success') ? ' success-state' : '' }}">
            <!-- Floating shield icon -->
            <div class="shield-icon-wrap">
                <div class="shield-circle">
                    <i class="fa fa-shield-alt"></i>
                </div>
            </div>

            @if(Session::has('success'))
                <div class="success-compact">
                    <div class="reset-header">
                        <h2>Set New Password</h2>
                        <p>Your new password must be different from your previous password.</p>
                    </div>

                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                    <div class="btn-wrap">
                        <a href="{{ url('/login') }}" class="btn-reset" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                            <i class="fa fa-arrow-right me-2"></i> Proceed to Login
                        </a>
                    </div>
                </div>
            @else
                <div class="reset-header">
                    <h2>Set New Password</h2>
                    <p>Your new password must be different from your previous password.</p>
                </div>

                <form action="{{ url('/reset-password') }}?email={{ $email }}" method="post">
                    @csrf

                    @if(Session::has('fail'))
                        <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                    @endif

                    <!-- New Password -->
                    <div class="field-group">
                        <label class="form-label">New Password</label>
                        <div class="input-wrap">
                            <i class="fa fa-lock i-icon"></i>
                            <input type="password" placeholder="Enter new password" name="password" id="new_password">
                            <i class="far fa-eye toggle-pw" id="toggleNewPassword"></i>
                        </div>
                        <div style="margin-top:8px; padding:8px 10px; border-radius:8px; background:#fff7ed; border:1px solid #fdba74; color:#9a3412; font-size:12px; line-height:1.4;">
                            <strong>Password requirements:</strong> Use at least 8 characters with uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.
                        </div>
                        <!-- Strength bar -->
                        <div class="strength-wrap">
                            <div class="strength-bar-bg">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <span class="strength-label" id="strengthLabel">Enter a password</span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="field-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fa fa-lock i-icon"></i>
                            <input type="password" placeholder="Confirm new password" name="confirm_password" id="confirm_password">
                            <i class="far fa-eye toggle-pw" id="toggleConfirmPassword"></i>
                        </div>
                        <div class="match-indicator" id="matchIndicator">
                            <i class="fa fa-check-circle"></i>
                            <span id="matchText">Passwords match</span>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="btn-wrap">
                        <button type="submit" class="btn-reset" id="resetBtn">
                            <i class="fa fa-shield-alt me-2"></i> Reset Password
                        </button>
                    </div>

                    <div class="footer-wrap">
                        <a href="login"><i class="fa fa-arrow-left"></i> Back to Sign In</a>
                    </div>

                </form>
            @endif
        </div>

    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ vasset('js/pages/auth-reset.js') }}"></script>
</body>
</html>