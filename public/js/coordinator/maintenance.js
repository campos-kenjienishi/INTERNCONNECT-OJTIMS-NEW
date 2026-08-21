/* ==========================================================================
   Coordinator Maintenance Page Scripts
   Extracted from ojtCoordinator/maintenance.blade.php
   ========================================================================== */

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

    // Dark mode toggle
    $(document).ready(function () {

        // DataTable
        $('#courseTable').DataTable({
            order: [],
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            language: {
                search: '',
                searchPlaceholder: 'Search courses...',
            }
        });

        $(document).on('click', '.edit-button', function () {
            const courseId = $(this).data('course-id');
            const courseName = $(this).data('course-name');
            const courseAcronym = $(this).data('course-acronym');

            $('#editCourseForm').attr('action', '/courses/' + courseId);
            $('#edit-course-name').val(courseName || '');
            $('#edit-course-acronym').val(courseAcronym || '');
        });

        // Remove button
        $(document).on('click', '.remove-button', function (e) {
            e.preventDefault();
            const courseId = $(this).data('course-id');

            Swal.fire({
                title: 'Remove this course?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/remove/course/' + courseId,
                        data: { _token: (window.coordinatorConfig && window.coordinatorConfig.csrfToken) || (meta[name="csrf-token"].attr('content') || '') },
                        success: function () {
                            Swal.fire({
                                title: 'Removed!',
                                text: 'The course has been removed.',
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

    });
