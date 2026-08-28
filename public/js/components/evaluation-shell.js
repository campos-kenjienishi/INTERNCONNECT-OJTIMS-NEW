/* Evaluation Shell Scripts */

    (function () {
        const SIDEBAR_COLLAPSED_KEY = 'internconnect_sidebar_collapsed';
        // ── Sidebar toggle only ──
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Restore persisted desktop sidebar state
        if (localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === 'true' && window.innerWidth > 900) {
            if (sidebar) sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
            document.documentElement.classList.add('sidebar-is-collapsed');
        }

        if (menuToggle && sidebar && mainContent) {
            menuToggle.addEventListener('click', () => {
                if (window.innerWidth <= 900) {
                    sidebar.classList.toggle('mobile-open');
                    if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem(SIDEBAR_COLLAPSED_KEY, isCollapsed ? 'true' : 'false');
                    if (isCollapsed) {
                        document.documentElement.classList.add('sidebar-is-collapsed');
                    } else {
                        document.documentElement.classList.remove('sidebar-is-collapsed');
                    }
                }
            });
        }

        if (sidebarOverlay && sidebar) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });
        }
    })();