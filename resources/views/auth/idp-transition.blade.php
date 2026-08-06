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
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">

    <style>
        body.auth-centered-page .login-container {
            max-width: 520px;
            min-height: auto;
        }

        body.auth-centered-page .right-panel {
            width: 100%;
            padding: 40px 32px;
            text-align: center;
            align-items: center;
            justify-content: center;
        }

        .spinner-wrapper {
            margin: 30px 0;
        }

        .custom-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-left-color: #ef4444;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .status-desc {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 24px;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            color: #fca5a5;
            font-size: 14px;
        }

        .btn-retry {
            background: #800000;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            margin-right: 8px;
            display: inline-block;
        }

        .btn-retry:hover {
            background: #a00000;
            color: white;
        }

        .btn-fallback {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }

        .btn-fallback:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
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
                    setTimeout(function() {
                        window.location.href = "{{ route('idp.redirect') }}";
                    }, 800);
                </script>
            @endif
        </div>
    </div>
</div>
</body>
</html>
