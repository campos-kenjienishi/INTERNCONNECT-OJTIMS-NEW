/* ==========================================================================
   INTERNCONNECT OJTIMS — CENTRALIZED DARK MODE JAVASCRIPT ENGINE
   Single Source of Truth for Student, Professor, and Coordinator Portals
   ========================================================================== */

(function () {
    'use strict';

    // 1. IMMEDIATE EXECUTION TO PREVENT FLASH OF UNSTYLED CONTENT (FOUC)
    function getStoredTheme() {
        try {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || theme === 'light') {
                return theme;
            }
            // Backward compatibility with legacy storage keys
            var legacy = localStorage.getItem('darkMode') || localStorage.getItem('internconnect_darkmode');
            if (legacy === 'true' || legacy === 'enabled' || legacy === '1') {
                return 'dark';
            } else if (legacy === 'false' || legacy === 'disabled' || legacy === '0') {
                return 'light';
            }
            // Default to system preference if no stored value
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
        } catch (e) {
            console.warn('Dark mode storage read error:', e);
        }
        return 'light';
    }

    var initialTheme = getStoredTheme();
    var isInitialDark = initialTheme === 'dark';

    if (isInitialDark) {
        document.documentElement.classList.add('dark-mode');
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark-mode');
        document.documentElement.setAttribute('data-theme', 'light');
    }

    // 2. DARK MODE MANAGER OBJECT
    var DarkModeManager = {
        isDark: isInitialDark,

        init: function () {
            this.isDark = document.documentElement.classList.contains('dark-mode') || 
                          (document.body && document.body.classList.contains('dark-mode')) ||
                          getStoredTheme() === 'dark';

            this.applyTheme(this.isDark, false);
            this.setupEventListeners();
            this.observeDOM();
        },

        isDarkMode: function () {
            return this.isDark;
        },

        setDarkMode: function (enable) {
            this.isDark = !!enable;
            this.applyTheme(this.isDark, true);
        },

        toggle: function () {
            this.setDarkMode(!this.isDark);
        },

        applyTheme: function (isDark, saveToStorage) {
            this.isDark = isDark;
            var themeName = isDark ? 'dark' : 'light';

            // Apply classes & attributes to both <html> and <body>
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
                document.documentElement.setAttribute('data-theme', 'dark');
                if (document.body) {
                    document.body.classList.add('dark-mode');
                    document.body.setAttribute('data-theme', 'dark');
                }
            } else {
                document.documentElement.classList.remove('dark-mode');
                document.documentElement.setAttribute('data-theme', 'light');
                if (document.body) {
                    document.body.classList.remove('dark-mode');
                    document.body.setAttribute('data-theme', 'light');
                }
            }

            // Sync storage
            if (saveToStorage) {
                try {
                    localStorage.setItem('theme', themeName);
                    // Sync legacy keys for backward safety
                    localStorage.setItem('darkMode', isDark ? 'true' : 'false');
                    localStorage.setItem('internconnect_darkmode', isDark ? '1' : '0');
                } catch (e) {
                    console.warn('Dark mode storage write error:', e);
                }
            }

            // Update UI toggles and icons
            this.updateToggleIcons(isDark);

            // Dispatch reactive events for charts / components
            try {
                window.dispatchEvent(new CustomEvent('themeChanged', { 
                    detail: { theme: themeName, isDark: isDark, isDarkMode: isDark } 
                }));
                window.dispatchEvent(new CustomEvent('darkModeChanged', { 
                    detail: { isDarkMode: isDark, isDark: isDark, theme: themeName } 
                }));
            } catch (e) {}
        },

        updateToggleIcons: function (isDark) {
            var toggleButtons = document.querySelectorAll('.darkmode-toggle, #darkmodeToggle, [data-toggle="darkmode"]');
            var label = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';

            toggleButtons.forEach(function (btn) {
                btn.setAttribute('aria-label', label);
                btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                btn.setAttribute('title', label);

                // Find icon inside button or globally
                var icon = btn.querySelector('i, svg') || document.getElementById('darkmodeIcon');
                if (icon && icon.classList) {
                    if (isDark) {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    } else {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    }
                }
            });

            // Also check any standalone darkmodeIcon elements
            var standaloneIcons = document.querySelectorAll('#darkmodeIcon');
            standaloneIcons.forEach(function (icon) {
                if (isDark) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                    icon.title = label;
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                    icon.title = label;
                }
            });
        },

        setupEventListeners: function () {
            var self = this;

            // Global delegated click listener for any toggle button
            document.addEventListener('click', function (e) {
                var toggleBtn = e.target.closest('.darkmode-toggle, #darkmodeToggle, [data-toggle="darkmode"]');
                if (toggleBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggle();

                    // Subtle feedback animation
                    toggleBtn.style.transform = 'scale(0.92)';
                    setTimeout(function () {
                        toggleBtn.style.transform = '';
                    }, 150);
                }
            });

            // Listen for cross-tab storage changes
            window.addEventListener('storage', function (e) {
                if (e.key === 'theme' || e.key === 'darkMode' || e.key === 'internconnect_darkmode') {
                    var newTheme = getStoredTheme();
                    self.setDarkMode(newTheme === 'dark');
                }
            });

            // Listen for system theme changes if user hasn't explicitly set a preference
            if (window.matchMedia) {
                var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                var handler = function (e) {
                    if (!localStorage.getItem('theme')) {
                        self.setDarkMode(e.matches);
                    }
                };
                if (mediaQuery.addEventListener) {
                    mediaQuery.addEventListener('change', handler);
                } else if (mediaQuery.addListener) {
                    mediaQuery.addListener(handler);
                }
            }
        },

        observeDOM: function () {
            var self = this;
            if (window.MutationObserver && document.body) {
                var observer = new MutationObserver(function (mutations) {
                    var needsUpdate = false;
                    for (var i = 0; i < mutations.length; i++) {
                        var added = mutations[i].addedNodes;
                        for (var j = 0; j < added.length; j++) {
                            var node = added[j];
                            if (node.nodeType === 1) {
                                if (node.matches && (node.matches('.darkmode-toggle, #darkmodeToggle') || node.querySelector('.darkmode-toggle, #darkmodeToggle'))) {
                                    needsUpdate = true;
                                    break;
                                }
                            }
                        }
                        if (needsUpdate) break;
                    }
                    if (needsUpdate) {
                        self.updateToggleIcons(self.isDark);
                    }
                });

                observer.observe(document.body, { childList: true, subtree: true });
            }
        }
    };

    // 3. INITIALIZE ON DOM READY OR IMMEDIATELY
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            DarkModeManager.init();
        });
    } else {
        DarkModeManager.init();
    }

    // 4. EXPOSE GLOBAL APIS FOR COMPATIBILITY
    window.DarkModeManager = DarkModeManager;
    window.toggleDarkMode = function (enable) {
        if (typeof enable === 'boolean') {
            DarkModeManager.setDarkMode(enable);
        } else {
            DarkModeManager.toggle();
        }
    };
})();

