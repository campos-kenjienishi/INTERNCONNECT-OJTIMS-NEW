/* Student Scripts */

    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', function () {
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

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

