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

    function toggleSidebar(e) {
        if (e) {
            if (e.__internconnect_sidebar_handled) return;
            e.__internconnect_sidebar_handled = true;
            if (typeof e.stopImmediatePropagation === 'function') {
                e.stopImmediatePropagation();
            }
            if (typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
        }

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent') || document.querySelector('.main-content');
        const overlay = document.getElementById('sidebarOverlay') || document.querySelector('.sidebar-overlay');
        const isMobile = window.innerWidth <= 900;

        if (isMobile) {
            const isOpen = sidebar ? sidebar.classList.contains('mobile-open') : false;
            if (isOpen) {
                if (sidebar) sidebar.classList.remove('mobile-open');
                if (overlay) {
                    overlay.classList.remove('active');
                    overlay.style.display = 'none';
                }
                document.body.classList.remove('mobile-sidebar-open');
            } else {
                if (sidebar) sidebar.classList.add('mobile-open');
                if (overlay) {
                    overlay.classList.add('active');
                    overlay.style.display = 'block';
                }
                document.body.classList.add('mobile-sidebar-open');
            }
        } else {
            if (sidebar) sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
            const nowCollapsed = sidebar ? sidebar.classList.contains('collapsed') : false;
            try {
                localStorage.setItem(STORAGE_KEY, nowCollapsed ? 'true' : 'false');
            } catch (err) {}
            if (nowCollapsed) {
                document.documentElement.classList.add('sidebar-is-collapsed');
            } else {
                document.documentElement.classList.remove('sidebar-is-collapsed');
            }
        }
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay') || document.querySelector('.sidebar-overlay');
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (overlay) {
            overlay.classList.remove('active');
            overlay.style.display = 'none';
        }
        document.body.classList.remove('mobile-sidebar-open');
    }

    // Expose global helpers
    window.toggleInternConnectSidebar = toggleSidebar;
    window.closeInternConnectMobileSidebar = closeMobileSidebar;

    function init() {
        applyState();

        // Use capture phase to intercept click on menuToggle before any duplicate page handlers run
        document.addEventListener('click', function (e) {
            const menuToggle = e.target && (e.target.closest ? e.target.closest('#menuToggle') : null);
            if (menuToggle) {
                toggleSidebar(e);
                return;
            }

            const overlay = e.target && (e.target.closest ? (e.target.closest('#sidebarOverlay') || e.target.closest('.sidebar-overlay')) : null);
            if (overlay) {
                e.stopImmediatePropagation();
                e.preventDefault();
                closeMobileSidebar();
                return;
            }

            // On mobile, clicking outside sidebar closes it
            if (window.innerWidth <= 900) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && sidebar.classList.contains('mobile-open')) {
                    if (!sidebar.contains(e.target)) {
                        closeMobileSidebar();
                    }
                }
            }
        }, true);

        // Window resize handler
        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) {
                closeMobileSidebar();
                applyState();
            } else {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('mainContent') || document.querySelector('.main-content');
                document.documentElement.classList.remove('sidebar-is-collapsed');
                if (sidebar) sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
