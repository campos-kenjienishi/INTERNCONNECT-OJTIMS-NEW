<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Welcome Portal</title>
    <link rel="shortcut icon" href="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ vasset('css/pages/auth-login-gateway.css') }}">
</head>

<body class="auth-centered-page">
<div class="main-wrapper">
    <div class="login-container">
        <!-- RIGHT PANEL / CARD -->
        <div class="right-panel">
            <div id="topBackBtnWrap" style="width: 100%; margin-bottom: 12px; text-align: left; display: none;">
                <button type="button" class="btn-switch-portal" onclick="resetPortalSelection()">
                    <i class="fa fa-arrow-left"></i> Change User Portal
                </button>
            </div>

            <div class="auth-brand">
                <img src="{{ vasset('images/final-puptg_logo-ojtims_nbg.png') }}" alt="InternConnect Logo" class="auth-logo">
                <div class="auth-brand-copy">
                    <div class="brand-name">Intern<span>Connect</span></div>
                    <div class="system-title">On-the-Job Training (OJT) Information Management System</div>
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
                <div class="portal-prompt-text">
                    Please select your user portal to continue:
                </div>

                <div class="portal-selection-grid">
                    <div class="portal-card" onclick="selectPortal('student')">
                        <div class="portal-icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div class="portal-card-body">
                            <div class="portal-title">Student</div>
                            <div class="portal-desc">For OJT Students &amp; Trainees</div>
                        </div>
                    </div>

                    <div class="portal-card" onclick="selectPortal('faculty')">
                        <div class="portal-icon">
                            <i class="fa fa-user-shield"></i>
                        </div>
                        <div class="portal-card-body">
                            <div class="portal-title">Faculty &amp; Staff</div>
                            <div class="portal-desc">For Professors &amp; Coordinators</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: METHOD SELECTION -->
            <div id="stepMethodSelect" style="display: none;">
                <div class="text-center">
                    <div class="portal-badge" id="portalBadge">
                        <div class="portal-badge-icon-wrap">
                            <i class="fa fa-graduation-cap" id="portalBadgeIcon"></i>
                        </div>
                        <span id="portalBadgeText"><span class="portal-badge-highlight">STUDENT</span> PORTAL</span>
                    </div>
                </div>

                <div class="gateway-options">
                    <a href="#" id="btnIdpLogin" class="btn-idp">
                        <i class="fa fa-shield-alt"></i>
                        <span>Sign In with Identity Provider (IdP)</span>
                    </a>

                    <div class="divider-container">
                        <span>or in case IdP is down / unavailable</span>
                    </div>

                    <a href="#" id="btnLocalLogin" class="local-login-link">
                        <i class="fa fa-key"></i>
                        <span>Use Local Credentials</span>
                    </a>
                </div>
            </div>

            <div class="terms-wrap">
                <div class="terms-text text-center">
                    <span class="terms-line-top">By using this service, you understand and agree to the PUP Online Services</span>
                    <span class="terms-line-bottom">
                        <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a> <span class="terms-line-separator">and</span>
                        <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.authGatewayConfig = {
        idpLoginUrl: @json(route('login.external')),
        localLoginUrl: @json(route('login'))
    };
</script>
<script src="{{ vasset('js/pages/auth-login-gateway.js') }}?v={{ time() }}"></script>
</body>
</html>