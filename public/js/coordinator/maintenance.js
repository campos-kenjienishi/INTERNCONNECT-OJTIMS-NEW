/* ==========================================================================
   Coordinator Maintenance Page Scripts
   Extracted from ojtCoordinator/maintenance.blade.php
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function () {
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');
            }
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }
});

$(document).ready(function () {

    // DataTable Initialization
    if ($('#courseTable').length) {
        $('#courseTable').DataTable({
            order: [],
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            language: {
                search: '',
                searchPlaceholder: 'Search programs...',
            }
        });
    }

    // Edit program button modal data population
    $(document).on('click', '.edit-button', function () {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        const courseAcronym = $(this).data('course-acronym');

        $('#editCourseForm').attr('action', '/courses/' + courseId);
        $('#edit-course-name').val(courseName || '');
        $('#edit-course-acronym').val(courseAcronym || '');
    });

    // Remove program button
    $(document).on('click', '.remove-button', function (e) {
        e.preventDefault();
        const courseId = $(this).data('course-id');

        Swal.fire({
            title: 'Remove this program?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Remove',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                const token = (window.coordinatorConfig && window.coordinatorConfig.csrfToken) || ($('meta[name="csrf-token"]').attr('content') || '');
                $.ajax({
                    type: 'POST',
                    url: '/remove/course/' + courseId,
                    data: { _token: token },
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function () {
                        Swal.fire({
                            title: 'Removed!',
                            text: 'The program has been removed.',
                            icon: 'success',
                            confirmButtonColor: '#dc2626',
                        }).then(() => location.reload());
                    },
                    error: function () {
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    }
                });
            }
        });
    });

    // Sync Programs with PUPTAS Admission System
    $('#btnSyncPuptas').on('click', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const $icon = $('#puptasSyncIcon');

        var alertHelper = window.SyncAlert || {
            confirm: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, icon: 'question', showCancelButton: true, showDenyButton: false }); },
            loading: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, allowOutsideClick: false, showConfirmButton: false, showDenyButton: false }); },
            success: function(o) { return Swal.fire({ title: o.title, icon: 'success', showDenyButton: false }); },
            error: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'error', showDenyButton: false }); },
            notice: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'warning', showDenyButton: false }); }
        };

        alertHelper.confirm({
            system: 'puptas',
            title: 'Sync Programs from PUPTAS?',
            subtitle: 'Pull latest degree and diploma programs from the Admission System.',
            bullets: [
                'Imports official degree and diploma programs',
                'Standardizes program naming & acronyms',
                'Cascades program updates to enrolled students & classes'
            ],
            note: 'Safe Operation: Existing programs and student records are updated without loss of data.',
            confirmBtnText: 'Yes, Sync Programs'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true);
                $icon.addClass('fa-spin');

                alertHelper.loading({
                    system: 'puptas',
                    title: 'Syncing Programs Data...',
                    subtitle: 'Connecting to PUPTAS API & standardizing academic programs...',
                    cautionText: 'Please do not refresh, close this window, or navigate away while synchronization is in progress.'
                });

                window.onbeforeunload = function () {
                    return "Program sync is currently in progress. Navigating away may interrupt the sync process.";
                };

                const token = (window.coordinatorConfig && window.coordinatorConfig.csrfToken) || ($('meta[name="csrf-token"]').attr('content') || '');

                $.ajax({
                    url: '/coordinator/sync-programs-puptas',
                    type: 'POST',
                    data: {
                        _token: token,
                        force: true
                    },
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function (res) {
                        window.onbeforeunload = null;
                        $btn.prop('disabled', false);
                        $icon.removeClass('fa-spin');

                        if (res.success) {
                            var stats = [
                                { label: 'New Added', value: res.created || 0, delta: true, colorClass: 'text-success', iconType: 'success', icon: 'fa-plus-circle' },
                                { label: 'Standardized', value: res.updated || 0, colorClass: 'text-primary', iconType: 'primary', icon: 'fa-check-circle' },
                                { label: 'Up to Date', value: res.unchanged || 0, colorClass: 'text-secondary', iconType: 'neutral', icon: 'fa-layer-group' },
                                { label: 'Students Cascaded', value: res.students_updated || 0, colorClass: 'text-warning', iconType: 'warning', icon: 'fa-user-graduate' }
                            ];

                            alertHelper.success({
                                system: 'puptas',
                                title: 'Programs Synchronized!',
                                subtitle: res.message || 'Academic programs have been successfully synchronized with PUPTAS.',
                                stats: stats,
                                confirmBtnText: 'Done & Refresh'
                            }).then(function () {
                                location.reload();
                            });
                        } else {
                            alertHelper.notice({
                                system: 'puptas',
                                title: 'Sync Notice',
                                message: res.message || 'Could not synchronize with PUPTAS Admission System.'
                            });
                        }
                    },
                    error: function (xhr) {
                        window.onbeforeunload = null;
                        $btn.prop('disabled', false);
                        $icon.removeClass('fa-spin');

                        let msg = 'An error occurred while connecting to the server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        alertHelper.error({
                            title: 'PUPTAS Sync Failed',
                            message: msg
                        });
                    }
                });
            }
        });
    });

});
