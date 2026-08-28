/* Student Scripts */

    const SIDEBAR_COLLAPSED_KEY = 'internconnect_sidebar_collapsed';
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    // Restore persisted desktop sidebar state
    if (localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === 'true' && window.innerWidth > 900) {
        if (sidebar) sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
        document.documentElement.classList.add('sidebar-is-collapsed');
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                if (sidebar) sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('active');
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

    if (overlay) {
        overlay.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    // Report time validation
    document.getElementById("report_time").addEventListener("blur", function () {
        const input = this.value.trim();
        const pattern = /^(\d{1,2}:\d{2} [APap][Mm] - \d{1,2}:\d{2} [APap][Mm])\s+\((Monday|Tuesday|Wednesday|Thursday|Friday)\s*-\s*(Monday|Tuesday|Wednesday|Thursday|Friday)\)$/;

        if (input !== '' && !input.match(pattern)) {
            this.style.borderColor = '#dc2626';
            this.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            alert("Invalid format. Please use: 9:00 am - 6:00 pm (Monday - Friday)");
        } else {
            this.style.borderColor = '';
            this.style.boxShadow = '';
        }
    });

