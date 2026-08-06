<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <!-- CRITICAL: Prevents white flash -->
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Welcome</title>
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
            text-transform: none;
        }

        .login-header {
            margin-bottom: 24px;
            text-align: center;
        }

        .login-header p {
            margin-bottom: 0;
            font-size: 14px;
            text-transform: none;
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

        .terms-wrap {
            margin-top: 20px;
            margin-bottom: 14px;
        }

        .terms-text {
            font-size: 13px;
            line-height: 1.5;
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

        .gateway-options {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
        }

        .btn-idp {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #800000 0%, #b30000 100%);
            color: #ffffff;
            border: 1px solid #d32f2f;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-idp:hover {
            background: linear-gradient(135deg, #990000 0%, #cc0000 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(128, 0, 0, 0.4);
        }

        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            margin: 6px 0;
        }

        .divider-container::before,
        .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .divider-container span {
            padding: 0 12px;
        }

        .local-login-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            color: #fca5a5;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            border: 1px dashed rgba(252, 165, 165, 0.4);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .local-login-link:hover {
            color: #ffffff;
            background: rgba(252, 165, 165, 0.1);
            border-color: rgba(252, 165, 165, 0.8);
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
                <p>Welcome! Choose a sign in method to continue</p>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if(Session::has('fail'))
                <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            @endif

            <div class="gateway-options">
                <a href="{{ route('login.external') }}" class="btn-idp">
                    <i class="fa fa-shield-alt me-2"></i> Sign In With Identity Provider (IdP)
                </a>

                <div class="divider-container">
                    <span>or sign in with a local account</span>
                </div>

                <a href="{{ route('login') }}" class="local-login-link">
                    <i class="fa fa-key me-2"></i> Use Local Credentials
                </a>
            </div>

            <div class="terms-wrap">
                <div class="terms-text text-center">
                    <span class="terms-line-top">By using this service, you understood and agree to the PUP Online Services</span>
                    <span class="terms-line-bottom">
                        <a href="{{ url('/terms') }}" target="_blank">Terms of Use</a> <span class="terms-line-separator">and</span>
                        <a href="{{ url('/privacy') }}" target="_blank">Privacy Statement</a>.
                    </span>
                </div>
            </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
</html>
