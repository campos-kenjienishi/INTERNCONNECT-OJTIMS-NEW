/* Requirement Category Scripts */

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

    // Remove category
    $(document).ready(function () {
        $('#fileTable').DataTable({
            scrollX: true,
            autoWidth: false
        });
        const editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));

        $('.edit-button').on('click', function () {
            const fileId = $(this).data('file-id');
            const fileName = $(this).data('file-name');
            const filePhase = $(this).data('file-phase');

            $('#editCategoryForm').attr('action', '/fileCategory/' + fileId);
            $('#editCategoryName').val(fileName);
            $('#editCategoryPhase').val(filePhase || 'other');
            editCategoryModal.show();
        });

        $('.remove-button').on('click', function (e) {
            e.preventDefault();
            var fileId = $(this).data('file-id');

            Swal.fire({
                title: 'Remove this category?',
                text: 'This will permanently delete the file category.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-trash"></i> Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/remove/files/' + fileId,
                        data: { _token: (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '') },
                        success: function () {
                            Swal.fire({
                                toast: true, icon: 'success',
                                title: 'Category removed successfully!',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1800, timerProgressBar: true
                            });
                            setTimeout(() => location.reload(), 1800);
                        },
                        error: function () {
                            Swal.fire('Oops!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    });
