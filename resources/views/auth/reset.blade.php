<?php
$email = $_GET['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
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
            
            <div class="auth-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="auth-logo">
                <div class="auth-brand-copy">
                    <div class="brand-name">Intern<span>Connect</span> - BETA</div>
                    <div class="system-title">On-The-Job Training Information Management System</div>
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
                        <a href="{{ url('/login') }}" class="btn-reset">
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

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle me-1"></i>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if(Session::has('fail'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle me-1"></i> {{ Session::get('fail') }}
                        </div>
                    @endif

                    <!-- New Password -->
                    <div class="field-group" style="position: relative;">
                        <label class="form-label">New Password</label>
                        <div class="input-wrap">
                            <i class="fa fa-lock i-icon"></i>
                            <input type="password" placeholder="Enter new password" name="password" id="new_password" autocomplete="new-password">
                            <i class="far fa-eye toggle-pw" id="toggleNewPassword"></i>
                        </div>

                        <!-- Interactive Speech Bubble Warning & Checklist -->
                        <div class="password-bubble" id="passwordBubble">
                            <div class="bubble-arrow"></div>
                            <div class="bubble-header">
                                <i class="fa fa-shield-alt"></i> Password Requirements
                            </div>
                            <div class="bubble-warning" id="bubbleWarning" style="display: none;">
                                <i class="fa fa-exclamation-circle"></i> Please fulfill all criteria below:
                            </div>
                            <ul class="bubble-checklist">
                                <li id="req-length"><i class="fa fa-circle"></i> At least 8 characters</li>
                                <li id="req-upper"><i class="fa fa-circle"></i> At least one uppercase letter (A-Z)</li>
                                <li id="req-lower"><i class="fa fa-circle"></i> At least one lowercase letter (a-z)</li>
                                <li id="req-num"><i class="fa fa-circle"></i> At least one number (0-9)</li>
                                <li id="req-sym"><i class="fa fa-circle"></i> One symbol: ! @ # $ % ^ & *</li>
                            </ul>
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
                    <div class="field-group" style="position: relative;">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fa fa-lock i-icon"></i>
                            <input type="password" placeholder="Confirm new password" name="confirm_password" id="confirm_password" autocomplete="new-password">
                            <i class="far fa-eye toggle-pw" id="toggleConfirmPassword"></i>
                        </div>
                        <div class="confirm-bubble" id="confirmBubble" style="display: none;">
                            <div class="bubble-arrow"></div>
                            <i class="fa fa-exclamation-triangle me-1"></i> Passwords do not match
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