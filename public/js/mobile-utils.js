/**
 * Mobile View Utilities
 * Handles sidebar toggle, overlay interactions, and mobile-specific functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // =============== MOBILE SIDEBAR TOGGLE ===============
    const initSidebarToggle = () => {
        const sidebar = document.querySelector('.sidebar');
        const toggleButtons = document.querySelectorAll(
            '.sidebar-toggle, .menu-toggle, .hamburger, [data-toggle="sidebar"]'
        );
        const overlay = document.querySelector('.sidebar-overlay') || createOverlay();

        if (!sidebar) return;

        // Create overlay if it doesn't exist
        function createOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
            return overlay;
        }

        // Toggle sidebar
        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function() {
            closeSidebar();
        });

        // Close sidebar when clicking nav items
        const navItems = sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on outside click
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && 
                !Array.from(toggleButtons).some(btn => btn.contains(e.target))) {
                closeSidebar();
            }
        });

        function toggleSidebar() {
            const isOpen = sidebar.classList.contains('active') || 
                          sidebar.classList.contains('open');
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        function openSidebar() {
            sidebar.classList.add('active', 'open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('active', 'open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeSidebar();
                sidebar.style.transform = '';
            }
        });
    };

    // =============== MOBILE MODALS ===============
    const initMobileModals = () => {
        const modals = document.querySelectorAll('.modal, .dialog, .popup');
        
        modals.forEach(modal => {
            // Close button
            const closeBtn = modal.querySelector('.modal-close, .dialog-close, [data-dismiss="modal"]');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => closeModal(modal));
            }

            // Escape key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal(modal);
                }
            });

            // Click outside to close
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });
        });

        function closeModal(modal) {
            modal.classList.remove('active', 'show');
            document.body.style.overflow = '';
        }
    };

    // =============== MOBILE TABLE OPTIMIZATION ===============
    const initTableOptimization = () => {
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            if (!table.closest('.table-responsive') && !table.closest('.table-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    };

    // =============== MOBILE FORM OPTIMIZATION ===============
    const initFormOptimization = () => {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            // Add mobile-friendly input attributes
            const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], textarea');
            inputs.forEach(input => {
                if (!input.hasAttribute('autocorrect')) {
                    input.setAttribute('autocorrect', 'off');
                    input.setAttribute('autocapitalize', 'off');
                    input.setAttribute('spellcheck', 'false');
                }
            });

            // Add class for mobile styling
            form.classList.add('mobile-form');
        });
    };

    // =============== MOBILE TOUCH GESTURES ===============
    const initTouchGestures = () => {
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, false);

        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);

        function handleSwipe() {
            const sidebar = document.querySelector('.sidebar');
            if (!sidebar) return;

            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            // Swipe left to close sidebar
            if (diff > swipeThreshold) {
                sidebar.classList.remove('active', 'open');
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) overlay.classList.remove('active');
            }

            // Swipe right to open sidebar (if touch starts from left edge)
            if (touchStartX < 20 && Math.abs(diff) > swipeThreshold) {
                sidebar.classList.add('active', 'open');
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) overlay.classList.add('active');
            }
        }
    };

    // =============== MOBILE NOTIFICATIONS ===============
    const showMobileNotification = (message, type = 'info', duration = 3000) => {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} mobile-notification`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 70px;
            left: 10px;
            right: 10px;
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    };

    // =============== MOBILE DROPDOWN/MENU ===============
    const initMobileDropdowns = () => {
        const dropdowns = document.querySelectorAll('.dropdown, [data-toggle="dropdown"]');
        
        dropdowns.forEach(dropdown => {
            const trigger = dropdown.querySelector('a, button, [data-toggle="dropdown"]');
            const menu = dropdown.querySelector('.dropdown-menu, .menu-content');

            if (trigger && menu) {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    menu.classList.toggle('show');
                });

                // Close on outside click
                document.addEventListener('click', () => {
                    menu.classList.remove('show');
                });

                // Close on item click
                const items = menu.querySelectorAll('.dropdown-item, .menu-item');
                items.forEach(item => {
                    item.addEventListener('click', () => {
                        menu.classList.remove('show');
                    });
                });
            }
        });
    };

    // =============== MOBILE NUMBER INPUT ===============
    const initNumberInputs = () => {
        const numberInputs = document.querySelectorAll('input[type="number"]');
        
        numberInputs.forEach(input => {
            input.setAttribute('inputmode', 'numeric');
            input.setAttribute('pattern', '[0-9]*');
        });
    };

    // =============== VIEWPORT HEIGHT FIX (for mobile keyboards) ===============
    const initViewportHeightFix = () => {
        const updateHeight = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        };

        updateHeight();
        window.addEventListener('resize', updateHeight);
        window.addEventListener('orientationchange', updateHeight);
    };

    // =============== PREVENT ZOOM ON DOUBLE TAP ===============
    const preventDoubleTabZoom = () => {
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    };

    // =============== INITIALIZE ALL ===============
    const initialize = () => {
        if (window.innerWidth < 768) {
            initSidebarToggle();
            initMobileModals();
            initTableOptimization();
            initFormOptimization();
            initTouchGestures();
            initMobileDropdowns();
            initNumberInputs();
            initViewportHeightFix();
            preventDoubleTabZoom();

            // Expose notification function globally
            window.showMobileNotification = showMobileNotification;
        }
    };

    initialize();

    // Re-initialize on page updates (for dynamically added content)
    window.addEventListener('load', initialize);
});

// =============== UTILITY FUNCTIONS ===============
// Check if device is mobile
window.isMobile = () => window.innerWidth < 768;

// Check if device is tablet
window.isTablet = () => window.innerWidth >= 768 && window.innerWidth < 992;

// Check if device is in landscape
window.isLandscape = () => window.innerHeight < window.innerWidth;

// Toggle dark mode
window.toggleDarkMode = (enable = null) => {
    const html = document.documentElement;
    const body = document.body;
    
    if (enable === null) {
        enable = !html.classList.contains('dark-mode') && !body.classList.contains('dark-mode');
    }

    if (enable) {
        html.classList.add('dark-mode');
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'true');
    } else {
        html.classList.remove('dark-mode');
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'false');
    }
};

// Load saved dark mode preference
window.addEventListener('DOMContentLoaded', () => {
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'true') {
        window.toggleDarkMode(true);
    }
});
