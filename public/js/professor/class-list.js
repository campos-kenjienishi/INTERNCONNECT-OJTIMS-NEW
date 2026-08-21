/* ==========================================================================
   Professor Class List Scripts
   Extracted from professor/classList.blade.php
   ========================================================================== */
                    $(document).ready(function () {
                        $('#fileTable').DataTable({
                            order: [],
                            scrollX: true,
                            autoWidth: false
                        });
                    });
                

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

    $(document).ready(function () {

        // Personal Info Modal
        $(document).on('click', '.btn-view-info', function () {
            var btn = $(this);
            var name = btn.data('full-name');
            $('#pi-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#pi-full-name').text(name);
            $('#pi-contact-number').text(btn.data('contact-number') || '—');
            $('#pi-email').text(btn.data('email') || '—');
            $('#pi-address').text(btn.data('address') || '—');
            $('#pi-dob').text(btn.data('date-of-birth') || '—');
            $('#pi-student-num').text(btn.data('student-num') || '—');
        });

        // OJT Info Modal
        $(document).on('click', '.btn-view-ojt', function () {
            var btn = $(this);
            var name = btn.data('full-name');
            $('#ojt-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#ojt-full-name').text(name);
            $('#ojt-company-name').text(btn.data('company-name') || '—');
            $('#ojt-company-address').text(btn.data('company-address') || '—');
            $('#ojt-nature-business').text(btn.data('nature-of-business') || '—');
            $('#ojt-nature-link').text(btn.data('nature-of-linkages') || '—');
            $('#ojt-level').text(btn.data('level') || '—');
            $('#ojt-start-date').text(btn.data('start-date') || '—');
            $('#ojt-finish-date').text(btn.data('finish-date') || '—');
            $('#ojt-report-time').text(btn.data('report-time') || '—');
            $('#ojt-contact-name').text(btn.data('contact-name') || '—');
            $('#ojt-contact-position').text(btn.data('contact-position') || '—');
            $('#ojt-contact-number').text(btn.data('contact-number') || '—');
        });

    });
