/* ==========================================================================
   Coordinator MOA Unlock Requests Scripts
   Extracted from ojtCoordinator/moaUnlockRequests.blade.php
   ========================================================================== */

    function openViewReasonModal(button) {
        const studentName = button.getAttribute('data-student-name') || 'Student';
        const studentInfo = button.getAttribute('data-student-info') || '';
        const companyName = button.getAttribute('data-company-name') || 'N/A';
        const reason = button.getAttribute('data-reason') || 'No reason provided.';
        const date = button.getAttribute('data-date') || '';

        document.getElementById('modalReasonAvatar').innerText = studentName.charAt(0).toUpperCase();
        document.getElementById('modalReasonStudentName').innerText = studentName;
        document.getElementById('modalReasonStudentInfo').innerText = studentInfo;
        document.getElementById('modalReasonCompanyName').innerText = companyName;
        document.getElementById('modalReasonContent').innerText = reason;
        document.getElementById('modalReasonDate').innerText = date ? ('Submitted on: ' + date) : '';

        const modal = new bootstrap.Modal(document.getElementById('viewReasonModal'));
        modal.show();
    }

    function confirmApproveUnlock(form, studentName) {
        if (form.dataset.confirmed === 'true') {
            return true;
        }

        Swal.fire({
            title: 'Approve Unlock Request?',
            html: `Are you sure you want to approve <strong>${studentName}</strong>'s unlock request?<br><br><span style="font-size:13px; color:#64748b;">The student will be unlinked from their company and allowed to select a new MOA.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-check me-1"></i> Yes, Approve',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });

        return false;
    }

    function confirmDenyUnlock(form, studentName) {
        if (form.dataset.confirmed === 'true') {
            return true;
        }

        Swal.fire({
            title: 'Deny Unlock Request?',
            html: `Are you sure you want to deny <strong>${studentName}</strong>'s unlock request?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-times me-1"></i> Yes, Deny',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });

        return false;
    }

    $(document).ready(function() {
        $('#requestsTable').DataTable({
            scrollX: true,
            order: [[3, 'asc'], [4, 'desc']]
        });

        // Sidebar toggle logic
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');

        if (menuToggle && sidebar && mainContent) {
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
    });
