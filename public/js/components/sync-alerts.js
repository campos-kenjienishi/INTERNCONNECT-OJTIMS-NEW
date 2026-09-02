/**
 * InternConnect Modern Sync Alerts Utility
 * High-end, animated, branded modal experiences for SweetAlert2
 */
(function (window, $) {
    'use strict';

    var SYSTEM_CONFIG = {
        flss: {
            badge: 'FLSS Faculty System',
            icon: 'fa-chalkboard-teacher',
            theme: 'theme-flss',
            confirmColor: '#059669',
            btnGradient: 'linear-gradient(135deg, #059669 0%, #0d9488 100%)'
        },
        idp: {
            badge: 'IDP Directory Service',
            icon: 'fa-id-badge',
            theme: 'theme-idp',
            confirmColor: '#4f46e5',
            btnGradient: 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)'
        },
        guisis: {
            badge: 'GuiSIS Guidance System',
            icon: 'fa-graduation-cap',
            theme: 'theme-guisis',
            confirmColor: '#0d9488',
            btnGradient: 'linear-gradient(135deg, #0d9488 0%, #16a34a 100%)'
        },
        success: {
            badge: 'Sync Succeeded',
            icon: 'fa-check',
            theme: 'theme-success',
            confirmColor: '#10b981',
            btnGradient: 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
        },
        error: {
            badge: 'Sync Alert',
            icon: 'fa-exclamation-triangle',
            theme: 'theme-error',
            confirmColor: '#ef4444',
            btnGradient: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'
        }
    };

    function cleanSweetAlertDOM(popup, hideCancel) {
        if (!popup) return;
        var selectors = ['.swal2-checkbox', '.swal2-deny', '.swal2-input', '.swal2-file', '.swal2-radio', '.swal2-select', '.swal2-textarea', '.swal2-range', '.swal2-validation-message'];
        if (hideCancel) {
            selectors.push('.swal2-cancel');
        }
        selectors.forEach(function (sel) {
            var el = popup.querySelector(sel);
            if (el) {
                el.style.display = 'none';
                try { el.remove(); } catch(e){}
            }
        });
    }

    var SyncAlert = {
        /**
         * 1. Confirmation Modal before running sync
         */
        confirm: function (options) {
            options = options || {};
            var sysKey = options.system || 'flss';
            var cfg = SYSTEM_CONFIG[sysKey] || SYSTEM_CONFIG.flss;

            var title = options.title || 'Sync Records?';
            var subtitle = options.subtitle || 'Synchronize external records with your local database.';
            var bullets = options.bullets || [];
            var note = options.note || 'Safe Operation: Existing records will be updated without data loss.';
            var confirmBtnText = options.confirmBtnText || 'Yes, Sync Now';
            var cancelBtnText = options.cancelBtnText || 'Cancel';
            var iconClass = options.icon || cfg.icon;

            var bulletsHtml = '';
            if (bullets.length > 0) {
                bulletsHtml = '<div class="sync-feature-list">';
                bullets.forEach(function (item) {
                    bulletsHtml +=
                        '<div class="sync-feature-item">' +
                        '<div class="sync-feature-bullet ' + cfg.theme + '"><i class="fas fa-check"></i></div>' +
                        '<span>' + item + '</span>' +
                        '</div>';
                });
                bulletsHtml += '</div>';
            }

            var noteHtml = '';
            if (note) {
                noteHtml =
                    '<div class="sync-safe-notice">' +
                    '<i class="fas fa-shield-alt"></i>' +
                    '<span>' + note + '</span>' +
                    '</div>';
            }

            var headerHtml =
                '<div class="sync-hero-header ' + cfg.theme + '">' +
                '<div class="sync-system-pill ' + cfg.theme + '">' +
                '<i class="fas fa-sync-alt fa-spin"></i> ' + (options.badge || cfg.badge) +
                '</div>' +
                '<div class="sync-orbit-wrapper ' + cfg.theme + '">' +
                '<div class="sync-orbit-ring"></div>' +
                '<div class="sync-orbit-icon-box ' + cfg.theme + '">' +
                '<i class="fas ' + iconClass + '"></i>' +
                '</div>' +
                '</div>' +
                '<h3 class="sync-modal-title">' + title + '</h3>' +
                '<p class="sync-modal-subtitle">' + subtitle + '</p>' +
                '</div>';

            var bodyHtml =
                '<div class="sync-confirm-body">' +
                bulletsHtml +
                noteHtml +
                '</div>';

            return Swal.fire({
                html: headerHtml + bodyHtml,
                showCancelButton: true,
                showDenyButton: false,
                confirmButtonText: '<i class="fas fa-bolt me-1"></i> ' + confirmBtnText,
                cancelButtonText: '<i class="fas fa-times me-1"></i> ' + cancelBtnText,
                confirmButtonColor: cfg.confirmColor,
                customClass: {
                    container: 'sync-alert-container',
                    popup: 'sync-alert-popup'
                },
                buttonsStyling: true,
                willOpen: function (popup) {
                    cleanSweetAlertDOM(popup, false);
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            });
        },

        /**
         * 2. Live Loading / In-Progress Modal
         */
        loading: function (options) {
            options = options || {};
            var sysKey = options.system || 'flss';
            var cfg = SYSTEM_CONFIG[sysKey] || SYSTEM_CONFIG.flss;

            var title = options.title || 'Syncing Data...';
            var subtitle = options.subtitle || 'Connecting to remote API endpoint...';
            var cautionText = options.cautionText || 'Please do not refresh, close this window, or navigate away while synchronization is running.';
            var iconClass = options.icon || cfg.icon;

            var headerHtml =
                '<div class="sync-hero-header ' + cfg.theme + '">' +
                '<div class="sync-system-pill ' + cfg.theme + '">' +
                '<i class="fas fa-circle-notch fa-spin"></i> Live Syncing' +
                '</div>' +
                '<div class="sync-loader-orbit ' + cfg.theme + '">' +
                '<div class="outer-ring"></div>' +
                '<div class="inner-glow-core">' +
                '<i class="fas ' + iconClass + '"></i>' +
                '</div>' +
                '</div>' +
                '<h3 class="sync-modal-title">' + title + '</h3>' +
                '<p class="sync-modal-subtitle">' + subtitle + '</p>' +
                '</div>';

            var bodyHtml =
                '<div class="sync-loading-stage">' +
                '<div class="sync-shimmer-bar-wrapper ' + cfg.theme + '">' +
                '<div class="sync-shimmer-bar-fill"></div>' +
                '</div>' +
                '<div class="sync-caution-banner">' +
                '<i class="fas fa-exclamation-triangle sync-caution-icon"></i>' +
                '<div class="sync-caution-text"><strong>Keep Tab Open:</strong> ' + cautionText + '</div>' +
                '</div>' +
                '</div>';

            return Swal.fire({
                html: headerHtml + bodyHtml,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCancelButton: false,
                showDenyButton: false,
                customClass: {
                    container: 'sync-alert-container',
                    popup: 'sync-alert-popup'
                },
                willOpen: function (popup) {
                    cleanSweetAlertDOM(popup, true);
                }
            });
        },

        /**
         * 3. Summary / Complete Modal with Stats
         */
        success: function (options) {
            options = options || {};
            var sysKey = options.system || 'flss';
            var cfg = SYSTEM_CONFIG[sysKey] || SYSTEM_CONFIG.flss;

            var title = options.title || 'Sync Completed!';
            var subtitle = options.subtitle || 'Your database has been successfully synchronized.';
            var stats = options.stats || [];
            var missingNotice = options.missingNotice || null;
            var confirmBtnText = options.confirmBtnText || 'Done & Refresh';

            var statsHtml = '';
            if (stats.length > 0) {
                statsHtml = '<div class="sync-stat-grid">';
                stats.forEach(function (st) {
                    var colorClass = st.colorClass || 'text-dark';
                    var iconColor = st.iconType || 'neutral';
                    var deltaPrefix = st.delta ? '+' : '';

                    statsHtml +=
                        '<div class="sync-stat-card">' +
                        '<div class="sync-stat-header">' +
                        '<div class="sync-stat-icon ' + iconColor + '"><i class="fas ' + (st.icon || 'fa-chart-bar') + '"></i></div>' +
                        '</div>' +
                        '<div class="sync-stat-val ' + colorClass + '">' + deltaPrefix + (st.value !== undefined ? st.value : 0) + '</div>' +
                        '<div class="sync-stat-label">' + (st.label || 'Records') + '</div>' +
                        '</div>';
                });
                statsHtml += '</div>';
            }

            var missingHtml = '';
            if (missingNotice) {
                missingHtml =
                    '<div class="sync-missing-notice-card">' +
                    '<div class="sync-missing-notice-left">' +
                    '<i class="fas fa-exclamation-circle text-warning fa-lg"></i>' +
                    '<div class="sync-missing-notice-text">' +
                    '<strong>' + (missingNotice.count || 0) + ' Unmatched Records</strong><br>' +
                    '<span>' + (missingNotice.text || 'Records require manual review.') + '</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            }

            var detailsHtml = '';
            var detailsList = options.syncedDetails || options.details || null;

            if (detailsList) {
                var items = [];
                if (Array.isArray(detailsList)) {
                    items = detailsList;
                } else if (typeof detailsList === 'object') {
                    Object.keys(detailsList).forEach(function (k) {
                        if (detailsList[k] !== null && detailsList[k] !== undefined && detailsList[k] !== '') {
                            items.push({ label: k, value: detailsList[k] });
                        }
                    });
                }

                if (items.length > 0) {
                    detailsHtml =
                        '<div class="sync-details-box">' +
                        '<div class="sync-details-title">' +
                        '<span><i class="fas fa-database text-success me-1"></i> ' + (options.detailsTitle || 'Synced Profile Details') + '</span>' +
                        '<span class="sync-details-badge-count">' + items.length + ' fields updated</span>' +
                        '</div>' +
                        '<div class="sync-details-list">';

                    items.forEach(function (it) {
                        var iconTag = it.icon ? '<i class="fas ' + it.icon + ' text-muted me-1"></i> ' : '<i class="fas fa-check-circle text-success me-1" style="font-size:10px;"></i> ';
                        detailsHtml +=
                            '<div class="sync-detail-row">' +
                            '<div class="sync-detail-key">' + iconTag + it.label + '</div>' +
                            '<div class="sync-detail-val">' + it.value + '</div>' +
                            '</div>';
                    });

                    detailsHtml += '</div></div>';
                }
            }

            var nowStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            var footerHtml =
                '<div class="sync-timestamp-footer">' +
                '<i class="fas fa-check-circle text-success"></i> Synchronized at ' + nowStr +
                '</div>';

            var headerHtml =
                '<div class="sync-hero-header theme-success">' +
                '<div class="sync-system-pill theme-success">' +
                '<i class="fas fa-check-double"></i> Complete' +
                '</div>' +
                '<div class="sync-orbit-wrapper theme-success">' +
                '<div class="sync-orbit-ring"></div>' +
                '<div class="sync-orbit-icon-box theme-success">' +
                '<i class="fas fa-check"></i>' +
                '</div>' +
                '</div>' +
                '<h3 class="sync-modal-title">' + title + '</h3>' +
                '<p class="sync-modal-subtitle">' + subtitle + '</p>' +
                '</div>';

            var bodyHtml =
                '<div class="sync-summary-body">' +
                statsHtml +
                detailsHtml +
                missingHtml +
                footerHtml +
                '</div>';

            return Swal.fire({
                html: headerHtml + bodyHtml,
                showConfirmButton: true,
                showCancelButton: false,
                showDenyButton: false,
                confirmButtonText: '<i class="fas fa-check me-1"></i> ' + confirmBtnText,
                confirmButtonColor: cfg.confirmColor,
                customClass: {
                    container: 'sync-alert-container',
                    popup: 'sync-alert-popup'
                },
                buttonsStyling: true,
                willOpen: function (popup) {
                    cleanSweetAlertDOM(popup, true);
                }
            });
        },

        /**
         * 4. Error Alert
         */
        error: function (options) {
            options = options || {};
            var title = options.title || 'Sync Operation Failed';
            var message = options.message || 'Unable to connect to the external service. Please verify your credentials and network connection.';
            var confirmBtnText = options.confirmBtnText || 'Close';

            var headerHtml =
                '<div class="sync-hero-header theme-error">' +
                '<div class="sync-system-pill theme-error">' +
                '<i class="fas fa-times-circle"></i> Sync Error' +
                '</div>' +
                '<div class="sync-orbit-wrapper theme-error">' +
                '<div class="sync-orbit-ring"></div>' +
                '<div class="sync-orbit-icon-box theme-error">' +
                '<i class="fas fa-exclamation-triangle"></i>' +
                '</div>' +
                '</div>' +
                '<h3 class="sync-modal-title">' + title + '</h3>' +
                '<p class="sync-modal-subtitle">An unexpected problem occurred during synchronization</p>' +
                '</div>';

            var bodyHtml =
                '<div class="sync-error-card">' +
                '<div class="d-flex align-items-center gap-2 mb-1">' +
                '<i class="fas fa-info-circle text-danger"></i> <strong>Details:</strong>' +
                '</div>' +
                '<div>' + message + '</div>' +
                '</div>' +
                '<div class="text-muted" style="font-size: 12px; text-align: center; margin-top: 10px;">' +
                'If this issue persists, please check your network connection or contact your administrator.' +
                '</div>';

            return Swal.fire({
                html: headerHtml + bodyHtml,
                showConfirmButton: true,
                showCancelButton: false,
                showDenyButton: false,
                confirmButtonText: '<i class="fas fa-times me-1"></i> ' + confirmBtnText,
                confirmButtonColor: '#ef4444',
                customClass: {
                    container: 'sync-alert-container',
                    popup: 'sync-alert-popup'
                },
                buttonsStyling: true,
                willOpen: function (popup) {
                    cleanSweetAlertDOM(popup, true);
                }
            });
        },

        /**
         * 5. Notice Alert
         */
        notice: function (options) {
            options = options || {};
            var title = options.title || 'Sync Notice';
            var message = options.message || '';
            var sysKey = options.system || 'flss';
            var cfg = SYSTEM_CONFIG[sysKey] || SYSTEM_CONFIG.flss;

            var headerHtml =
                '<div class="sync-hero-header ' + cfg.theme + '">' +
                '<div class="sync-system-pill ' + cfg.theme + '">' +
                '<i class="fas fa-info-circle"></i> Notice' +
                '</div>' +
                '<div class="sync-orbit-wrapper ' + cfg.theme + '">' +
                '<div class="sync-orbit-ring"></div>' +
                '<div class="sync-orbit-icon-box ' + cfg.theme + '">' +
                '<i class="fas fa-info"></i>' +
                '</div>' +
                '</div>' +
                '<h3 class="sync-modal-title">' + title + '</h3>' +
                '</div>';

            var bodyHtml =
                '<div style="text-align:center; padding: 10px 0; font-size: 14px; color: #475569;">' +
                message +
                '</div>';

            return Swal.fire({
                html: headerHtml + bodyHtml,
                showConfirmButton: true,
                showCancelButton: false,
                showDenyButton: false,
                confirmButtonText: 'Understood',
                confirmButtonColor: cfg.confirmColor,
                customClass: {
                    container: 'sync-alert-container',
                    popup: 'sync-alert-popup'
                },
                buttonsStyling: true,
                willOpen: function (popup) {
                    cleanSweetAlertDOM(popup, true);
                }
            });
        },

        close: function () {
            Swal.close();
        }
    };

    window.SyncAlert = SyncAlert;

})(window, window.jQuery || window.$);
