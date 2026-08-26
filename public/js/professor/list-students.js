/* Professor Students in Class Scripts */

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

    $(document).ready(function () {

        // DataTable
        $('#fileTable').DataTable({
            scrollX: true,
            autoWidth: false
        });

        // Populate deny modal with student data dynamically
        $(document).on('click', '.open-deny-modal', function () {
            const name   = $(this).data('name');
            const course = $(this).data('course');
            const email  = $(this).data('email');

            $('#denyAvatar').text(name ? name.charAt(0).toUpperCase() : '?');
            $('#denyStudentName').text(name);
            $('#denyStudentCourse').text(course);

            // Update form action with correct email
            $('#denyForm').attr('action', '/professor/deny/' + email);

            // Clear textarea
            $('#reason').val('');
        });

        // Approve — SweetAlert toast
        $(document).on('submit', '.approveForm', function (e) {
            e.preventDefault();
            const form = this;
            const Toast = Swal.mixin({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2500, timerProgressBar: true,
            });
            Toast.fire({ icon: 'success', title: 'Student approved successfully' });
            setTimeout(() => form.submit(), 500);
        });

        $('#approveAllStudentsForm').on('submit', function (e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Approve all students?',
                text: 'Are you sure you want to approve all students?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, approve all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Deny — SweetAlert toast
        $('#denyForm').on('submit', function (e) {
            e.preventDefault();
            const form = this;
            const Toast = Swal.mixin({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2500, timerProgressBar: true,
            });
            Toast.fire({ icon: 'warning', title: 'Student request has been denied' });
            setTimeout(() => form.submit(), 500);
        });

    });
