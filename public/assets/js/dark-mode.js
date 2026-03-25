/* =====================================
   UNIVERSAL DARK MODE JAVASCRIPT
   ===================================== */

(function () {
    'use strict';

    // Dark mode manager object
    const DarkModeManager = {
        // Initialization
        init: function () {
            this.setupToggleButtons();
            this.loadSavedPreference();
            this.observeDOMChanges();
        },

        // Setup all dark mode toggle buttons
        setupToggleButtons: function () {
            const self = this;
            const toggleButtons = document.querySelectorAll('.darkmode-toggle');
            
            toggleButtons.forEach((btn) => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isDarkMode = document.body.classList.contains('dark-mode') ||
                                     document.documentElement.classList.contains('dark-mode');
                    self.setDarkMode(!isDarkMode);
                    
                    // Add animation feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        },

        // Load saved dark mode preference from localStorage
        loadSavedPreference: function () {
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            if (isDarkMode) {
                this.setDarkMode(true);
            } else {
                // Check system preference if no saved preference
                if (localStorage.getItem('darkMode') === null) {
                    this.checkSystemPreference();
                }
            }
        },

        // Check system color scheme preference
        checkSystemPreference: function () {
            if (window.matchMedia) {
                const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
                if (darkModeQuery.matches) {
                    this.setDarkMode(true);
                }
            }
        },

        // Set dark mode on or off
        setDarkMode: function (isDarkMode) {
            if (isDarkMode) {
                document.body.classList.add('dark-mode');
                document.documentElement.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
                document.documentElement.classList.remove('dark-mode');
            }

            // Update all toggle button icons
            this.updateToggleIcons(isDarkMode);

            // Save preference
            localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');

            // Dispatch custom event for other scripts
            window.dispatchEvent(new CustomEvent('darkModeChanged', { 
                detail: { isDarkMode: isDarkMode } 
            }));
        },

        // Update all toggle button icons
        updateToggleIcons: function (isDarkMode) {
            const icons = document.querySelectorAll('#darkmodeIcon');
            icons.forEach((icon) => {
                if (isDarkMode) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                    icon.title = 'Switch to Light Mode';
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                    icon.title = 'Switch to Dark Mode';
                }
            });
        },

        // Observe DOM changes to setup new toggle buttons
        observeDOMChanges: function () {
            const self = this;
            if (window.MutationObserver) {
                const observer = new MutationObserver(function (mutations) {
                    let hasNewToggleButton = false;

                    mutations.forEach((mutation) => {
                        if (mutation.addedNodes.length) {
                            mutation.addedNodes.forEach((node) => {
                                if (node.nodeType === 1) { // Element node
                                    if (node.classList && node.classList.contains('darkmode-toggle')) {
                                        hasNewToggleButton = true;
                                    }
                                    if (node.querySelector && node.querySelector('.darkmode-toggle')) {
                                        hasNewToggleButton = true;
                                    }
                                }
                            });
                        }
                    });

                    if (hasNewToggleButton) {
                        self.setupToggleButtons();
                    }
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        }
    };

    // Listen for storage changes from other tabs/windows
    window.addEventListener('storage', (e) => {
        if (e.key === 'darkMode') {
            const isDarkMode = e.newValue === 'true';
            DarkModeManager.setDarkMode(isDarkMode);
        }
    });

    // Initialize dark mode when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            DarkModeManager.init();
        });
    } else {
        DarkModeManager.init();
    }

    // Expose to window for external access if needed
    window.DarkModeManager = DarkModeManager;
})();
