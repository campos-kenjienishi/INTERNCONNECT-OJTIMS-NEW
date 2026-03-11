<?php
$email = $_GET['email'] ?? '';
?>

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
        <div class="right-panel">

            <!-- Floating shield icon -->
            <div class="shield-icon-wrap">
                <div class="shield-circle">
                    <i class="fa fa-shield-alt"></i>
                </div>
            </div>

            <div class="reset-header">
                <h2>Set New Password</h2>
                <p>Your new password must be different from your previous password.</p>
            </div>

            <form action="{{ url('/reset-password') }}?email={{ $email }}" method="post">
                @csrf

                @if(Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
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
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/frontend/js/script.js') }}"></script>
<script>
    // Toggle new password
    document.getElementById('toggleNewPassword').addEventListener('click', function () {
        const input = document.getElementById('new_password');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        this.classList.remove('toggled');
        void this.offsetWidth;
        this.classList.add('toggled');
    });

    // Toggle confirm password
    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const input = document.getElementById('confirm_password');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        this.classList.remove('toggled');
        void this.offsetWidth;
        this.classList.add('toggled');
    });

    // Password strength checker
    const newPasswordInput  = document.getElementById('new_password');
    const confirmInput      = document.getElementById('confirm_password');
    const strengthBar       = document.getElementById('strengthBar');
    const strengthLabel     = document.getElementById('strengthLabel');
    const matchIndicator    = document.getElementById('matchIndicator');
    const matchText         = document.getElementById('matchText');

    function checkStrength(password) {
        let score = 0;
        if (password.length >= 8)  score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    newPasswordInput.addEventListener('input', function () {
        const val   = this.value;
        const score = checkStrength(val);

        const levels = [
            { width: '0%',   color: 'transparent',               label: 'Enter a password' },
            { width: '20%',  color: '#ef4444',                   label: 'Very weak' },
            { width: '40%',  color: '#f97316',                   label: 'Weak' },
            { width: '60%',  color: '#eab308',                   label: 'Fair' },
            { width: '80%',  color: '#84cc16',                   label: 'Strong' },
            { width: '100%', color: '#22c55e',                   label: 'Very strong' },
        ];

        const level = val.length === 0 ? levels[0] : levels[Math.min(score, 5)];
        strengthBar.style.width      = level.width;
        strengthBar.style.background = level.color;
        strengthLabel.textContent    = level.label;
        strengthLabel.style.color    = val.length === 0 ? 'rgba(255,255,255,0.4)' : level.color;

        checkMatch();
    });

    confirmInput.addEventListener('input', checkMatch);

    function checkMatch() {
        const pw  = newPasswordInput.value;
        const cpw = confirmInput.value;

        if (cpw.length === 0) {
            matchIndicator.classList.remove('visible', 'match', 'no-match');
            return;
        }

        matchIndicator.classList.add('visible');

        if (pw === cpw) {
            matchIndicator.classList.add('match');
            matchIndicator.classList.remove('no-match');
            matchIndicator.querySelector('i').className = 'fa fa-check-circle';
            matchText.textContent = 'Passwords match';
        } else {
            matchIndicator.classList.add('no-match');
            matchIndicator.classList.remove('match');
            matchIndicator.querySelector('i').className = 'fa fa-times-circle';
            matchText.textContent = 'Passwords do not match';
        }
    }

    // Email from URL
    function getEmailQueryParam() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('email');
    }

    const email = getEmailQueryParam();
</script>
</body>
</html>