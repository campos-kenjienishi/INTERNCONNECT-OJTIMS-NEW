/* ==========================================================================
   Coordinator MOA View Scripts
   Extracted from ojtCoordinator/MOAview.blade.php
   ========================================================================== */

    // ====== DARK MODE TOGGLE ======
    // ====== SIDEBAR TOGGLE ======
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle  = document.getElementById('menuToggle');
        const overlay     = document.getElementById('sidebarOverlay');

        if (menuToggle && sidebar && mainContent) {
            menuToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                const isMobile = window.innerWidth <= 900;
                if (isMobile) {
                    const shouldOpen = !sidebar.classList.contains('mobile-open');
                    sidebar.classList.toggle('mobile-open', shouldOpen);
                    overlay.classList.toggle('active', shouldOpen);
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }

        const closeMobileSidebar = function () {
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
            }
            if (overlay) {
                overlay.classList.remove('active');
            }
        };

        ['click', 'touchstart'].forEach(function (eventName) {
            document.addEventListener(eventName, function (event) {
                if (window.innerWidth > 900 || !sidebar || !sidebar.classList.contains('mobile-open')) {
                    return;
                }

                const clickedInsideSidebar = sidebar.contains(event.target);
                const clickedMenuToggle = menuToggle && menuToggle.contains(event.target);

                if (!clickedInsideSidebar && !clickedMenuToggle) {
                    closeMobileSidebar();
                }
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) {
                closeMobileSidebar();
            }
        });
    });

    // ====== STUDENT SEARCH ======
    document.addEventListener('DOMContentLoaded', function() {
        const studentSearchInput = document.getElementById('studentSearchInput');
        if (studentSearchInput) {
            studentSearchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const cards = Array.from(document.querySelectorAll('.student-list .student-card'));
                let visibleCount = 0;

                cards.forEach(function (card) {
                    const nameEl = card.querySelector('.student-name');
                    const studentName = (nameEl ? nameEl.textContent : '').toLowerCase();
                    const isVisible = !query || studentName.includes(query);
                    card.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        visibleCount++;
                    }
                });

                const noMatchEl = document.getElementById('studentNoMatch');
                if (noMatchEl) {
                    noMatchEl.style.display = (cards.length > 0 && visibleCount === 0) ? 'block' : 'none';
                }
            });
        }
    });
