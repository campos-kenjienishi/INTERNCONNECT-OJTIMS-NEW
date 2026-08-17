<!DOCTYPE html>
<html lang="en" style="background: #3b0000;">
<head>
    <!-- CRITICAL: Prevents white flash -->
    <style>
        html, body { background: #3b0000 !important; }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Welcome Portal</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ url('/css/dashboard-global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login-responsive.css') }}">

    <style>
        body.auth-centered-page .login-container {
            max-width: 620px;
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
            min-height: 480px;
            padding: 32px 38px;
            justify-content: center;
        }

        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
            margin-bottom: 24px;
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
            color: #ffffff !important;
        }

        .auth-brand-copy .system-title {
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: none;
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .portal-selection-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .portal-card {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.22) !important;
            border-radius: 14px;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #ffffff !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }

        .portal-card:hover {
            background: rgba(255, 255, 255, 0.18) !important;
            border-color: #ffffff !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.4);
            color: #ffffff !important;
        }

        .portal-card.active {
            background: rgba(220, 38, 38, 0.35) !important;
            border-color: #ef4444 !important;
            box-shadow: 0 0 22px rgba(239, 68, 68, 0.5);
        }

        .portal-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(252, 165, 165, 0.12) !important;
            border: 1.5px solid rgba(252, 165, 165, 0.35) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .portal-icon i {
            color: #fca5a5 !important;
            font-size: 26px !important;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
            transition: all 0.3s ease;
        }

        .portal-card:hover .portal-icon {
            background: #dc2626 !important;
            border-color: #ffffff !important;
            transform: scale(1.1);
            box-shadow: 0 6px 18px rgba(220, 38, 38, 0.5);
        }

        .portal-card:hover .portal-icon i {
            color: #ffffff !important;
        }

        .portal-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #ffffff !important;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .portal-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.85) !important;
            line-height: 1.35;
        }

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.18) !important;
            border: 1.5px solid rgba(252, 165, 165, 0.4) !important;
            color: #fca5a5 !important;
            padding: 7px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 0.8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.3);
            backdrop-filter: blur(8px);
        }

        .portal-badge i,
        .portal-badge span,
        #portalBadgeText {
            color: #fca5a5 !important;
            font-weight: 700 !important;
        }

        .gateway-options {
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
        }

        .btn-idp {
            width: 100%;
            padding: 14px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-idp:hover {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
            color: #ffffff !important;
            border-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.5);
        }

        .btn-idp i {
            color: #ffffff !important;
        }

        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 13px;
            font-weight: 500;
            margin: 6px 0;
        }

        .divider-container::before,
        .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.25) !important;
        }

        .divider-container span {
            padding: 0 14px;
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .local-login-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 20px;
            color: #fca5a5 !important;
            font-weight: 600;
            font-size: 14.5px;
            text-decoration: none;
            background: rgba(252, 165, 165, 0.08) !important;
            border: 1.5px dashed rgba(252, 165, 165, 0.45) !important;
            border-radius: 12px;
            transition: all 0.25s ease;
            backdrop-filter: blur(6px);
        }

        .local-login-link:hover {
            color: #ffffff !important;
            background: rgba(252, 165, 165, 0.18) !important;
            border-color: #fca5a5 !important;
            border-style: solid !important;
            transform: translateY(-1px);
        }

        .local-login-link i {
            color: #fca5a5 !important;
        }

        .btn-switch-portal {
            background: rgba(252, 165, 165, 0.1) !important;
            border: 1px solid rgba(252, 165, 165, 0.35) !important;
            color: #fca5a5 !important;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 8px 18px;
            border-radius: 20px;
            margin-top: 10px;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(8px);
        }

        .btn-switch-portal:hover {
            color: #ffffff !important;
            background: rgba(252, 165, 165, 0.22) !important;
            border-color: #fca5a5 !important;
            transform: translateY(-1px);
        }

        .btn-switch-portal i {
            color: #fca5a5 !important;
        }

        .terms-wrap {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .terms-text {
            font-size: 12.5px;
            line-height: 1.5;
        }

        .terms-line-top {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 12.5px;
        }

        .terms-line-bottom {
            display: block;
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 13px;
        }

        .terms-line-separator {
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0 3px;
        }

        .terms-line-bottom a {
            color: #fca5a5 !important;
            font-weight: 600;
            text-decoration: underline;
            transition: color 0.2s ease;
        }

        .terms-line-bottom a:hover {
            color: #ffffff !important;
        }

        @media (max-width: 767px) {
            .portal-selection-grid {
                grid-template-columns: 1fr;
            }
            body.auth-centered-page .right-panel {
                padding: 24px 18px;
            }
        }
    </style>
</head>

<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container">
        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div id="topBackBtnWrap" style="width: 100%; margin-bottom: 14px; text-align: left; display: none;">
                <button type="button" class="btn-switch-portal" onclick="resetPortalSelection()" style="margin-top: 0;">
                    <i class="fa fa-arrow-left"></i> Change User Portal
                </button>
            </div>

            <div class="auth-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect Logo" class="auth-logo">
                <div class="auth-brand-copy">
                    <div class="brand-name">Intern<span>Connect</span> - BETA</div>
                    <div class="system-title">On-The-Job Training Information Management System</div>
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success py-2 px-3 mb-3 text-center" style="font-size: 13px;">{{ Session::get('success') }}</div>
            @endif
            @if(Session::has('fail'))
                <div class="alert alert-danger py-2 px-3 mb-3 text-center" style="font-size: 13px;">{{ Session::get('fail') }}</div>
            @endif

            <!-- STEP 1: PORTAL SELECTION -->
            <div id="stepPortalSelect">
                <div class="text-center mb-3">
                    <p style="color: #ffffff !important; font-size: 15px; font-weight: 600; margin-bottom: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">Please select your user portal to continue:</p>
                </div>

                <div class="portal-selection-grid">
                    <div class="portal-card" onclick="selectPortal('student')">
                        <div class="portal-icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="portal-title">Student Portal</div>
                            <div class="portal-desc">For OJT Students & Trainees</div>
                        </div>
                    </div>

                    <div class="portal-card" onclick="selectPortal('faculty')">
                        <div class="portal-icon">
                            <i class="fa fa-user-shield"></i>
                        </div>
                        <div>
                            <div class="portal-title">Faculty & Staff</div>
                            <div class="portal-desc">For Professors & Coordinators</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: METHOD SELECTION -->
            <div id="stepMethodSelect" style="display: none;">
                <div class="text-center">
                    <div class="portal-badge" id="portalBadge">
                        <i class="fa fa-user" id="portalBadgeIcon"></i>
                        <span id="portalBadgeText">STUDENT PORTAL</span>
                    </div>
                </div>

                <div class="gateway-options">
                    <a href="#" id="btnIdpLogin" class="btn-idp">
                        <i class="fa fa-shield-alt me-2"></i> Sign In With Identity Provider (IdP)
                    </a>

                    <div class="divider-container">
                        <span>or sign in with local credentials</span>
                    </div>

                    <a href="#" id="btnLocalLogin" class="local-login-link">
                        <i class="fa fa-key me-2"></i> Use Local Credentials
                    </a>
                </div>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function selectPortal(portal) {
        document.getElementById('stepPortalSelect').style.display = 'none';
        document.getElementById('stepMethodSelect').style.display = 'block';
        var topBack = document.getElementById('topBackBtnWrap');
        if (topBack) topBack.style.display = 'block';

        var badgeText = document.getElementById('portalBadgeText');
        var badgeIcon = document.getElementById('portalBadgeIcon');
        var btnIdp = document.getElementById('btnIdpLogin');
        var btnLocal = document.getElementById('btnLocalLogin');

        if (portal === 'faculty') {
            badgeText.textContent = 'FACULTY & STAFF PORTAL';
            badgeIcon.className = 'fa fa-user-shield';
            btnIdp.href = "{{ route('login.external') }}?portal=faculty";
            btnLocal.href = "{{ route('login') }}?portal=faculty";
        } else {
            badgeText.textContent = 'STUDENT PORTAL';
            badgeIcon.className = 'fa fa-graduation-cap';
            btnIdp.href = "{{ route('login.external') }}?portal=student";
            btnLocal.href = "{{ route('login') }}?portal=student";
        }
    }

    function resetPortalSelection() {
        document.getElementById('stepMethodSelect').style.display = 'none';
        document.getElementById('stepPortalSelect').style.display = 'block';
        var topBack = document.getElementById('topBackBtnWrap');
        if (topBack) topBack.style.display = 'none';
    }

    // Auto-open portal if requested via query parameter
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const portalParam = urlParams.get('portal');
        if (portalParam === 'faculty' || portalParam === 'student') {
            selectPortal(portalParam);
        }
    });
</script>
</body>
</html>
