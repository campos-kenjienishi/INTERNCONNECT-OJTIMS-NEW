/* Auth Login Gateway Scripts */

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
            btnIdp.href = (window.authGatewayConfig?.idpLoginUrl || '/login/external') + '?portal=faculty';
            btnLocal.href = (window.authGatewayConfig?.localLoginUrl || '/login') + '?portal=faculty';
        } else {
            badgeText.textContent = 'STUDENT PORTAL';
            badgeIcon.className = 'fa fa-graduation-cap';
            btnIdp.href = (window.authGatewayConfig?.idpLoginUrl || '/login/external') + '?portal=student';
            btnLocal.href = (window.authGatewayConfig?.localLoginUrl || '/login') + '?portal=student';
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