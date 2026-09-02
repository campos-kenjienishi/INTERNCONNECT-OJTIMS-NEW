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

        var alertHelper = window.SyncAlert || {
            confirm: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, icon: 'question', showCancelButton: true, showDenyButton: false }); },
            loading: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, allowOutsideClick: false, showConfirmButton: false, showDenyButton: false }); },
            success: function(o) { return Swal.fire({ title: o.title, icon: 'success', showDenyButton: false }); },
            error: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'error', showDenyButton: false }); },
            notice: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'warning', showDenyButton: false }); }
        };

        // --- 1. Sync IDP UUIDs Handler ---
        $('#btnSyncIdp').on('click', function(e) {
            e.preventDefault();

            alertHelper.confirm({
                system: 'idp',
                title: 'Sync Student UUIDs from IDP?',
                subtitle: 'Cross-reference student accounts with campus Identity Provider directory.',
                bullets: [
                    'Backfills missing IDP UUID identifiers for students',
                    'Synchronizes official student names & authentication fields',
                    'Preserves all local progress, requirements, and submissions'
                ],
                note: 'Safe Operation: Only existing local student records are matched.',
                confirmBtnText: 'Yes, Sync IDP UUIDs'
            }).then((result) => {
                if (result.isConfirmed) {
                    alertHelper.loading({
                        system: 'idp',
                        title: 'Connecting to IDP...',
                        subtitle: 'Querying Identity Provider directory & linking student UUIDs...',
                        cautionText: 'Please keep this tab open while IDP synchronization is in progress.'
                    });

                    window.onbeforeunload = function() {
                        return "IDP sync is in progress. Navigating away may interrupt the process.";
                    };

                    $.ajax({
                        url: (window.studentsConfig && window.studentsConfig.syncUsersIdpUrl) || '/coordinator/sync-users-idp',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': (window.studentsConfig && window.studentsConfig.csrfToken) || ($('meta[name="csrf-token"]').attr('content') || '') },
                        timeout: 120000,
                        success: function(res) {
                            window.onbeforeunload = null;
                            var s = res.summary || {};
                            var notInIdp = s.not_in_idp || 0;

                            var stats = [
                                { label: 'Total Students', value: s.total_students || 0, colorClass: 'text-primary', iconType: 'primary', icon: 'fa-users' },
                                { label: 'Newly Linked', value: s.idp_linked || 0, delta: true, colorClass: 'text-success', iconType: 'success', icon: 'fa-link' },
                                { label: 'Already Linked', value: s.already_linked || 0, colorClass: 'text-info', iconType: 'info', icon: 'fa-check-circle' },
                                { label: 'Not in IDP', value: notInIdp, colorClass: notInIdp ? 'text-warning' : 'text-secondary', iconType: notInIdp ? 'warning' : 'neutral', icon: 'fa-exclamation-triangle' }
                            ];

                            alertHelper.success({
                                system: 'idp',
                                title: 'IDP UUID Sync Complete!',
                                subtitle: 'Student IDP identifiers have been cross-referenced and linked.',
                                stats: stats,
                                confirmBtnText: 'Done & Refresh'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            window.onbeforeunload = null;
                            var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Failed to sync IDP records. Please try again.';
                            alertHelper.error({
                                title: 'IDP Sync Failed',
                                message: errMsg
                            });
                        }
                    });
                }
            });
        });

        // --- 2. Sync GuiSIS Handler ---
        $('#btnSyncGuisis').on('click', function(e) {
            e.preventDefault();

            alertHelper.confirm({
                system: 'guisis',
                title: 'Sync from GuiSIS (Guidance)?',
                subtitle: 'Update academic and demographic records for existing students from GuiSIS.',
                bullets: [
                    'Updates Student Numbers, Program/Course, and Year & Section',
                    'Synchronizes Birthdates, Contact Numbers, and Home Addresses',
                    'Maintains full synchronization with Guidance Counselor database'
                ],
                note: 'Safe Operation: Existing student accounts are updated without data loss.',
                confirmBtnText: 'Yes, Sync GuiSIS'
            }).then((result) => {
                if (result.isConfirmed) {
                    alertHelper.loading({
                        system: 'guisis',
                        title: 'Connecting to GuiSIS...',
                        subtitle: 'Pulling latest student records and demographics from GuiSIS...',
                        cautionText: 'Please keep this tab open while GuiSIS synchronization is running.'
                    });

                    window.onbeforeunload = function() {
                        return "GuiSIS sync is in progress. Navigating away may interrupt the process.";
                    };

                    $.ajax({
                        url: (window.studentsConfig && window.studentsConfig.syncUsersGuisisUrl) || '/coordinator/sync-users-guisis',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': (window.studentsConfig && window.studentsConfig.csrfToken) || ($('meta[name="csrf-token"]').attr('content') || '') },
                        timeout: 120000,
                        success: function(res) {
                            window.onbeforeunload = null;
                            var s = res.summary || {};
                            var notFound = s.guisis_not_found || 0;

                            var stats = [
                                { label: 'Total Students', value: s.total_students || 0, colorClass: 'text-primary', iconType: 'primary', icon: 'fa-users' },
                                { label: 'Profiles Synced', value: s.guisis_synced || 0, colorClass: 'text-success', iconType: 'success', icon: 'fa-user-graduate' },
                                { label: 'GuiSIS Pool', value: s.guisis_total_pool || 0, colorClass: 'text-info', iconType: 'info', icon: 'fa-database' },
                                { label: 'Not in GuiSIS', value: notFound, colorClass: notFound ? 'text-warning' : 'text-secondary', iconType: notFound ? 'warning' : 'neutral', icon: 'fa-exclamation-triangle' }
                            ];

                            alertHelper.success({
                                system: 'guisis',
                                title: 'GuiSIS Sync Complete!',
                                subtitle: 'Student profiles have been updated from the Guidance System.',
                                stats: stats,
                                confirmBtnText: 'Done & Refresh'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            window.onbeforeunload = null;
                            var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Failed to sync GuiSIS records. Please try again.';
                            alertHelper.error({
                                title: 'GuiSIS Sync Failed',
                                message: errMsg
                            });
                        }
                    });
                }
            });
        });
    });

