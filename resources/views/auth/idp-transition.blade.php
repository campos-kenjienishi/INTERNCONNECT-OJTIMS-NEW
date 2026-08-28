<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to Identity Provider - InternConnect</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/pages/auth-idp-transition.css') }}?v={{ time() }}">
</head>

<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="transition-card">
        
        <!-- Logo & Branding -->
        <div class="brand-section">
            <div class="logo-glow-wrapper">
                <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect Logo" class="brand-logo">
            </div>
            <div class="brand-name">Intern<span>Connect</span> <span class="badge-beta">BETA</span></div>
            <div class="brand-sub">Central Authentication Service</div>
        </div>

        @if(isset($error) || Session::has('idp_error'))
            <div class="error-box">
                <div class="error-icon"><i class="fa fa-exclamation-triangle"></i></div>
                <div class="error-title">Connection Failed</div>
                <div class="error-msg">{{ $error ?? Session::get('idp_error') }}</div>
            </div>

            <div class="action-buttons">
                <a href="{{ route('idp.redirect') }}" class="btn-action btn-primary-action">
                    <i class="fa fa-sync-alt me-2"></i> Try Again
                </a>
                <a href="{{ route('login') }}" class="btn-action btn-secondary-action">
                    <i class="fa fa-key me-2"></i> Sign In Locally
                </a>
            </div>
        @else
            <!-- Loading & Status Section -->
            <div class="loader-section">
                <div class="glow-spinner-wrapper">
                    <div class="spinner-ring outer-ring"></div>
                    <div class="spinner-ring inner-ring"></div>
                    <div class="spinner-center-icon"><i class="fa fa-shield-alt"></i></div>
                </div>

                <div class="status-badge">
                    <span class="status-dot"></span> Secure SSO Redirect
                </div>

                <h2 class="status-heading">Connecting to Identity Provider</h2>
                <p class="status-description">
                    Please hold on while we securely hand over your session to the University Authentication Portal.
                </p>

                <div class="progress-bar-container">
                    <div class="progress-bar-fill"></div>
                </div>
            </div>

            <div class="cancel-link-wrap">
                <a href="{{ route('login') }}" class="cancel-link">
                    <i class="fa fa-arrow-left me-1"></i> Cancel and return to Sign In
                </a>
            </div>

            @php
                $currentPortal = request()->query('portal', session('target_login_portal', 'student'));
            @endphp
            <script>
                window.authIdpConfig = {
                    redirectUrl: @json(route('idp.redirect', ['portal' => $currentPortal]))
                };
            </script>
            <script src="{{ vasset('js/pages/auth-idp-transition.js') }}"></script>
        @endif

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>