/**
 * InternConnect - Persisted Sidebar Handler
 * Ensures sidebar collapsed/expanded state is preserved across page navigation
 * and handles desktop/mobile toggles seamlessly with smooth animation.
 */
(function () {
    const STORAGE_KEY = 'internconnect_sidebar_collapsed';

    // 1. Immediately apply class to <html> if saved as collapsed (prevents flash of expanded sidebar)
    try {
        if (localStorage.getItem(STORAGE_KEY) === 'true' && window.innerWidth > 900) {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    } catch (e) {}

    function applyState() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent') || document.querySelector('.main-content');
        const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';

        if (window.innerWidth > 900) {
            if (isCollapsed) {
                if (sidebar) sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
                document.documentElement.classList.add('sidebar-is-collapsed');
            } else {
                if (sidebar) sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
                document.documentElement.classList.remove('sidebar-is-collapsed');
            }
        } else {
            document.documentElement.classList.remove('sidebar-is-collapsed');
            if (sidebar) sidebar.classList.remove('collapsed');
            if (mainContent) mainContent.classList.remove('expanded');
        }
    }

    function initToggle() {
        applyState();

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent') || document.querySelector('.main-content');
        const menuToggle = document.getElementById('menuToggle');
        const overlay = document.getElementById('sidebarOverlay') || document.querySelector('.sidebar-overlay');

        if (menuToggle && !menuToggle.__hasPersistListener) {
            menuToggle.__hasPersistListener = true;
            menuToggle.addEventListener('click', function (e) {
                const isMobile = window.innerWidth <= 900;
                if (isMobile) {
                    if (sidebar) sidebar.classList.toggle('mobile-open');
                    if (overlay) overlay.classList.toggle('active');
                } else {
                    if (sidebar) sidebar.classList.toggle('collapsed');
                    if (mainContent) mainContent.classList.toggle('expanded');
                    const nowCollapsed = sidebar ? sidebar.classList.contains('collapsed') : false;
                    localStorage.setItem(STORAGE_KEY, nowCollapsed ? 'true' : 'false');
                    if (nowCollapsed) {
                        document.documentElement.classList.add('sidebar-is-collapsed');
                    } else {
                        document.documentElement.classList.remove('sidebar-is-collapsed');
                    }
                }
            });
        }

        if (overlay && !overlay.__hasPersistListener) {
            overlay.__hasPersistListener = true;
            overlay.addEventListener('click', function () {
                if (sidebar) sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToggle);
    } else {
        initToggle();
    }
})();
