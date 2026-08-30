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

    // Floating Flyout for Submenus in Collapsed Mode (Body-Portaled)
    function initCollapsedFlyout() {
        let flyout = document.getElementById('sidebarCollapsedFlyout');
        if (!flyout) {
            flyout = document.createElement('div');
            flyout.id = 'sidebarCollapsedFlyout';
            flyout.className = 'sidebar-collapsed-flyout';
            document.body.appendChild(flyout);
        }

        let hideTimer = null;

        function isSidebarCollapsed() {
            if (window.innerWidth <= 900) return false;
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return false;
            return sidebar.classList.contains('collapsed') ||
                   document.documentElement.classList.contains('sidebar-is-collapsed') ||
                   sidebar.getBoundingClientRect().width <= 110;
        }

        function showFlyout(targetEl) {
            if (!isSidebarCollapsed()) return;

            const group = targetEl.closest('.nav-group-reports') || targetEl.closest('.nav-item-reports')?.closest('.nav-group-reports') || targetEl.closest('.nav-group-reports');
            if (!group) return;

            const navSub = group.querySelector('.nav-sub');
            if (!navSub) return;

            clearTimeout(hideTimer);

            const rect = targetEl.getBoundingClientRect();
            flyout.innerHTML = `
                <div class="flyout-header">
                    <i class="fa fa-chart-bar"></i> Reports
                </div>
                <div class="flyout-links">
                    ${navSub.innerHTML}
                </div>
            `;

            const topPos = Math.max(10, Math.min(window.innerHeight - 170, rect.top));
            flyout.style.top = topPos + 'px';
            flyout.style.left = (rect.right + 2) + 'px';
            flyout.classList.add('visible');
        }

        function scheduleHide() {
            hideTimer = setTimeout(function () {
                flyout.classList.remove('visible');
            }, 300);
        }

        // Mousemove & Mouseover delegated listener
        function handleHover(e) {
            if (!isSidebarCollapsed()) {
                flyout.classList.remove('visible');
                return;
            }
            const reportTarget = e.target.closest('.nav-group-reports') || e.target.closest('.nav-item-reports');
            if (reportTarget) {
                showFlyout(reportTarget);
            } else if (e.target.closest('#sidebarCollapsedFlyout')) {
                clearTimeout(hideTimer);
            } else {
                scheduleHide();
            }
        }

        document.addEventListener('mouseover', handleHover);
        document.addEventListener('mousemove', function(e) {
            if (e.target.closest('.nav-group-reports') || e.target.closest('#sidebarCollapsedFlyout')) {
                clearTimeout(hideTimer);
            }
        });

        // Click handler on Reports when collapsed
        document.addEventListener('click', function(e) {
            if (isSidebarCollapsed()) {
                const reportTarget = e.target.closest('.nav-group-reports') || e.target.closest('.nav-item-reports');
                if (reportTarget && !e.target.closest('.nav-sub-item')) {
                    e.preventDefault();
                    showFlyout(reportTarget);
                }
            }
        });

        flyout.addEventListener('mouseenter', function () {
            clearTimeout(hideTimer);
        });

        flyout.addEventListener('mouseleave', function () {
            scheduleHide();
        });
    }

    function init() {
        applyState();
        initCollapsedFlyout();

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
