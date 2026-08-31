/* ==========================================================================
   Coordinator Students Page Scripts
   Extracted from ojtCoordinator/students.blade.php
   ========================================================================== */

    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    if(menuToggle) {
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
    }

    if(overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    $(document).ready(function () {
        // DataTable
        const table = $('#fileTable').DataTable({
            order: [],
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            columnDefs: [
                { targets: 6, visible: false, searchable: false },
                { targets: 7, visible: false, searchable: true }
            ]
        });

        $('#courseFilter').on('change', function () {
            const value = $(this).val();
            table.column(1).search(value || '', false, true).draw();
        });

        $('#schoolYearFilter').on('change', function () {
            const value = $(this).val();
            table.column(7).search(value || '', false, true).draw();
        });

        // Personal Info Modal
        $(document).on('click', '.btn-view-personal', function () {
            const name = $(this).data('full-name');
            $('#pi-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#pi-full-name').text(name || '—');
            $('#pi-contact-number').text($(this).data('contact-number') || '—');
            $('#pi-email').text($(this).data('email') || '—');
            $('#pi-address').text($(this).data('address') || '—');
            $('#pi-date-of-birth').text($(this).data('date-of-birth') || '—');
            $('#pi-student-num').text($(this).data('student-num') || '—');
        });

        // OJT Info Modal
        $(document).on('click', '.btn-view-ojt', function () {
            const name = $(this).data('full-name');
            $('#ojt-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#ojt-full-name').text(name || '—');
            $('#ojt-company-name').text($(this).data('company-name') || '—');
            $('#ojt-company-address').text($(this).data('company-address') || '—');
            $('#ojt-nature-of-business').text($(this).data('nature-of-business') || '—');
            $('#ojt-nature-of-linkages').text($(this).data('nature-of-linkages') || '—');
            $('#ojt-level').text($(this).data('level') || '—');
            $('#ojt-start-date').text($(this).data('start-date') || '—');
            $('#ojt-finish-date').text($(this).data('finish-date') || '—');
            $('#ojt-report-time').text($(this).data('report-time') || '—');
            $('#ojt-contact-name').text($(this).data('contact-name') || '—');
            $('#ojt-contact-position').text($(this).data('contact-position') || '—');
            $('#ojt-contact-number').text($(this).data('contact-number') || '—');
        });

        // Status Modal
        $(document).on('click', '.btn-status', function () {
            const name = $(this).data('name');
            const studentNum = $(this).data('student');
            const status = $(this).data('status');
            $('#st-avatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#st-name').text(name || '—');
            $('#status-student').val(studentNum);
            $('#status-select').val(status);
            $('#statusUpdateForm').attr('action', '/status/' + studentNum);
        });

        // Status form AJAX
        $('#statusUpdateForm').on('submit', function (e) {
            e.preventDefault();
            const studentNum = $('#status-student').val();
            const newStatus  = $('#status-select').val();
            $.ajax({
                type: 'POST',
                url: '/status/' + studentNum,
                data: { _token: (window.studentsConfig && window.studentsConfig.csrfToken) || (meta[name="csrf-token"].attr('content') || ''), status: newStatus },
                success: function () { location.reload(); },
                error:   function () { alert('An error occurred.'); }
            });
        });

        // Notify form with confirmation modal
        $(document).on('submit', '.notifyForm', function (e) {
            e.preventDefault();
            const form = $(this);
            const studentName = form.data('student-name') || 'this student';
            const studentNum = form.data('student-num') || '';
            const btn = form.find('button[type="submit"]');
            const originalHtml = btn.html();
            const toastDelay = 2600;
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: toastDelay,
                timerProgressBar: true
            });

            Swal.fire({
                title: 'Send Reminder Notification?',
                html: '<div style="text-align:center; margin: 10px 0 16px;">' +
                          '<div style="font-size:15px; font-weight:700; color:#111827;">' + $('<div>').text(studentName).html() + '</div>' +
                          (studentNum ? '<div style="font-size:12.5px; color:#6b7280; margin-top:2px;">Student #' + $('<div>').text(studentNum).html() + '</div>' : '') +
                      '</div>' +
                      '<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 14px; text-align:left; font-size:12.5px; color:#166534; line-height:1.5;">' +
                          '<i class="fa fa-bell me-1" style="color:#16a34a;"></i> This will send an automated reminder notification to the student regarding their OJT requirements and progress.' +
                      '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-paper-plane me-1"></i> Yes, Send Notification',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                    
                    Swal.fire({
                        title: 'Sending Notification...',
                        html: '<div style="margin: 15px 0;">' +
                                  '<div class="spinner-border text-success" role="status" style="width: 2.5rem; height: 2.5rem;">' +
                                      '<span class="visually-hidden">Sending...</span>' +
                                  '</div>' +
                              '</div>' +
                              '<div style="margin-top: 10px; font-size: 13px; color: #6b7280;">' +
                                  '<i class="fa fa-envelope text-success me-1"></i> Sending email reminder. Please wait...</strong>' +
                              '</div>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    $.ajax({
                        type: 'POST',
                        url: form.attr('action'),
                        data: form.serialize(),
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            btn.prop('disabled', false).html(originalHtml);
                            Swal.fire({
                                icon: 'success',
                                title: 'Notification Sent!',
                                text: (response && response.message) ? response.message : 'Notification sent successfully to ' + studentName + '.',
                                confirmButtonColor: '#16a34a'
                            });
                        },
                        error: function (xhr) {
                            btn.prop('disabled', false).html(originalHtml);
                            const rawResponse = (xhr.responseText || '').trim();
                            const isHtmlResponse = rawResponse.startsWith('<!DOCTYPE html') || rawResponse.startsWith('<html');
                            const message = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : isHtmlResponse
                                    ? 'Notification could not be sent because the mail server returned an error.'
                                    : (rawResponse || 'Failed to send notification. Please try again.');
                            Swal.fire({
                                icon: 'error',
                                title: 'Notification Failed',
                                text: message,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    });
                }
            });
        });

        // --- 1. Sync IDP UUIDs Handler ---
        $('#btnSyncIdp').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Sync Student UUIDs from IDP?',
                html: 'This will cross-reference existing students in your database with the Identity Provider:<br><br>' +
                      '<ul style="text-align:left; font-size:13px; color:#4b5563; padding-left:20px;">' +
                      '<li>Backfills missing <strong>IDP UUIDs</strong> on student accounts.</li>' +
                      '<li>Synchronizes official name fields from IDP.</li>' +
                      '</ul>' +
                      '<div style="font-size:12.5px; color:#6b7280; margin-top:8px;"><strong>Note:</strong> Only updates existing local accounts.</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Sync IDP UUIDs'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Connecting to Identity Provider...',
                        html:
                            '<div style="margin: 15px 0;">' +
                                '<div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">' +
                                    '<span class="visually-hidden">Syncing...</span>' +
                                '</div>' +
                            '</div>' +
                            '<div style="margin-top: 10px; font-size: 13px; color: #6b7280;">' +
                                '<i class="fas fa-lock text-primary me-1"></i> Linking student UUIDs from IDP. Please wait...</strong>' +
                            '</div>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    window.onbeforeunload = function() {
                        return "IDP sync is in progress. Navigating away may interrupt the process.";
                    };

                    $.ajax({
                        url: (window.studentsConfig && window.studentsConfig.syncUsersIdpUrl) || '/coordinator/sync-users-idp',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': (window.studentsConfig && window.studentsConfig.csrfToken) || (meta[name="csrf-token"].attr('content') || '') },
                        timeout: 120000,
                        success: function(res) {
                            window.onbeforeunload = null;
                            var s = res.summary || {};
                            Swal.fire({
                                title: 'IDP UUID Sync Complete!',
                                html:
                                    '<div style="text-align:left; margin: 10px 0; font-size: 14px;">' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6;"><span><i class="fas fa-users text-primary me-2"></i>Total Students:</span><strong>' + (s.total_students || 0) + '</strong></div>' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6;"><span><i class="fas fa-link text-success me-2"></i>Newly Linked UUIDs:</span><strong style="color:#10b981;">+' + (s.idp_linked || 0) + '</strong></div>' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6;"><span><i class="fas fa-check-circle text-info me-2"></i>Already Linked:</span><strong>' + (s.already_linked || 0) + '</strong></div>' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0;"><span><i class="fas fa-exclamation-triangle text-warning me-2"></i>Not Found in IDP:</span><strong>' + (s.not_in_idp || 0) + '</strong></div>' +
                                    '</div>',
                                icon: 'success',
                                confirmButtonColor: '#4f46e5'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            window.onbeforeunload = null;
                            var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Failed to sync IDP records. Please try again.';
                            Swal.fire('IDP Sync Failed', errMsg, 'error');
                        }
                    });
                }
            });
        });

        // --- 2. Sync GuiSIS Handler ---
        $('#btnSyncGuisis').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Sync from GuiSIS (Guidance)?',
                html: 'This will update academic & demographic records for existing students from GuiSIS:<br><br>' +
                      '<ul style="text-align:left; font-size:13px; color:#4b5563; padding-left:20px;">' +
                      '<li>Updates Student Number, Program, and Year &amp; Section.</li>' +
                      '<li>Synchronizes Birthdate, Contact #, and Address.</li>' +
                      '</ul>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Sync GuiSIS'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Connecting to GuiSIS...',
                        html:
                            '<div style="margin: 15px 0;">' +
                                '<div class="spinner-border text-success" role="status" style="width: 2.5rem; height: 2.5rem;">' +
                                    '<span class="visually-hidden">Syncing...</span>' +
                                '</div>' +
                            '</div>' +
                            '<div style="margin-top: 10px; font-size: 13px; color: #6b7280;">' +
                                '<i class="fas fa-graduation-cap text-success me-1"></i> Pulling profiles from GuiSIS. Please wait...</strong>' +
                            '</div>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    window.onbeforeunload = function() {
                        return "GuiSIS sync is in progress. Navigating away may interrupt the process.";
                    };

                    $.ajax({
                        url: (window.studentsConfig && window.studentsConfig.syncUsersGuisisUrl) || '/coordinator/sync-users-guisis',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': (window.studentsConfig && window.studentsConfig.csrfToken) || (meta[name="csrf-token"].attr('content') || '') },
                        timeout: 120000,
                        success: function(res) {
                            window.onbeforeunload = null;
                            var s = res.summary || {};
                            Swal.fire({
                                title: 'GuiSIS Sync Complete!',
                                html:
                                    '<div style="text-align:left; margin: 10px 0; font-size: 14px;">' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6;"><span><i class="fas fa-users text-primary me-2"></i>Total Students:</span><strong>' + (s.total_students || 0) + '</strong></div>' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6;"><span><i class="fas fa-user-graduate text-success me-2"></i>Profiles Synced:</span><strong style="color:#059669;">' + (s.guisis_synced || 0) + '</strong></div>' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6;"><span><i class="fas fa-database text-info me-2"></i>GuiSIS Pool:</span><strong>' + (s.guisis_total_pool || 0) + '</strong></div>' +
                                    '<div style="display:flex; justify-content:space-between; padding: 6px 0;"><span><i class="fas fa-exclamation-triangle text-warning me-2"></i>Not in GuiSIS:</span><strong>' + (s.guisis_not_found || 0) + '</strong></div>' +
                                    '</div>',
                                icon: 'success',
                                confirmButtonColor: '#0d9488'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            window.onbeforeunload = null;
                            var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Failed to sync GuiSIS records. Please try again.';
                            Swal.fire('GuiSIS Sync Failed', errMsg, 'error');
                        }
                    });
                }
            });
        });
    });

