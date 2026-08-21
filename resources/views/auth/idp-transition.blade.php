<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to Identity Provider - InternConnect</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/pages/auth-idp-transition.css') }}">
</head>

<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container">
        <div class="right-panel">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" style="width: 64px; height: 64px; margin-bottom: 16px;">
            
            @if(isset($error) || Session::has('idp_error'))
                <div class="error-box">
                    <i class="fa fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                    <strong>Identity Provider Connection Failed</strong><br>
                    <span>{{ $error ?? Session::get('idp_error') }}</span>
                </div>

                <div class="actions">
                    <a href="{{ route('idp.redirect') }}" class="btn-retry">
                        <i class="fa fa-sync-alt me-1"></i> Try Again
                    </a>
                    <a href="{{ route('login') }}" class="btn-fallback">
                        <i class="fa fa-key me-1"></i> Sign In Locally
                    </a>
                </div>
            @else
                <div class="spinner-wrapper">
                    <div class="custom-spinner"></div>
                </div>

                <div class="status-title">Connecting to Identity Provider...</div>
                <div class="status-desc">Please wait while we redirect you to the central authentication service.</div>

                <script>
                    window.authIdpConfig = {
                        redirectUrl: @json(route('idp.redirect'))
                    };
                </script>
                <script src="{{ vasset('js/pages/auth-idp-transition.js') }}"></script>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>