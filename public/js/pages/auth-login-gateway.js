/* Auth Login Gateway Scripts */

    function selectPortal(portal, pushHistory = true) {
        document.getElementById('stepPortalSelect').style.display = 'none';
        document.getElementById('stepMethodSelect').style.display = 'block';
        var topBack = document.getElementById('topBackBtnWrap');
        if (topBack) topBack.style.display = 'block';

        var badgeText = document.getElementById('portalBadgeText');
        var badgeIcon = document.getElementById('portalBadgeIcon');
        var btnIdp = document.getElementById('btnIdpLogin');
        var btnLocal = document.getElementById('btnLocalLogin');

        if (portal === 'faculty') {
            badgeText.innerHTML = '<span class="portal-badge-highlight">FACULTY &amp; STAFF</span> PORTAL';
            badgeIcon.className = 'fa fa-user-shield';
            btnIdp.href = (window.authGatewayConfig?.idpLoginUrl || '/login/external') + '?portal=faculty';
            btnLocal.href = (window.authGatewayConfig?.localLoginUrl || '/login') + '?portal=faculty';
        } else {
            badgeText.innerHTML = '<span class="portal-badge-highlight">STUDENT</span> PORTAL';
            badgeIcon.className = 'fa fa-graduation-cap';
            btnIdp.href = (window.authGatewayConfig?.idpLoginUrl || '/login/external') + '?portal=student';
            btnLocal.href = (window.authGatewayConfig?.localLoginUrl || '/login') + '?portal=student';
        }

        if (pushHistory) {
            var url = new URL(window.location);
            url.searchParams.set('portal', portal);
            window.history.pushState({ portal: portal }, '', url.toString());
        }
    }

    function resetPortalSelection(pushHistory = true) {
        document.getElementById('stepMethodSelect').style.display = 'none';
        document.getElementById('stepPortalSelect').style.display = 'block';
        var topBack = document.getElementById('topBackBtnWrap');
        if (topBack) topBack.style.display = 'none';

        if (pushHistory) {
            var url = new URL(window.location);
            url.searchParams.delete('portal');
            window.history.pushState({}, '', url.pathname + (url.hash || ''));
        }
    }

    // Auto-open portal if requested via query parameter on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const portalParam = urlParams.get('portal');
        if (portalParam === 'faculty' || portalParam === 'student') {
            selectPortal(portalParam, false);
        }
    });

    // Handle browser Back and Forward navigation
    window.addEventListener('popstate', function(e) {
        const urlParams = new URLSearchParams(window.location.search);
        const portalParam = urlParams.get('portal');
        if (portalParam === 'faculty' || portalParam === 'student') {
            selectPortal(portalParam, false);
        } else {
            resetPortalSelection(false);
        }
    });