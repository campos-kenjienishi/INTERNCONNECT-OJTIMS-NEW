/* Student Requirements Matrix Scripts */

                    $(document).ready(function () {
                        var table = $('#fileTable').DataTable({
                            order: [[2, 'desc']], // default: newest first (date column)
                            scrollX: true,
                            autoWidth: false,
                            columnDefs: [
                                { targets: [2], type: 'date' }
                            ]
                        });

                        $('#applySort').on('click', function (e) {
                            e.preventDefault();
                            var dateVal = $('#dateSort').val();
                            var nameVal = $('#nameSort').val();
                            var ordering = [];

                            // Category sort is the primary sort when selected.
                            if (nameVal === 'az') {
                                ordering.push([0, 'asc']);
                            } else if (nameVal === 'za') {
                                ordering.push([0, 'desc']);
                            }

                            if (dateVal === 'newest') {
                                ordering.push([2, 'desc']);
                            } else if (dateVal === 'oldest') {
                                ordering.push([2, 'asc']);
                            }

                            // If no ordering selected, fallback to default
                            if (ordering.length === 0) {
                                ordering = [[2, 'desc']];
                            }

                            table.order(ordering).draw();
                        });
                    });
                

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

    $(document).on('click', '.open-deny-modal', function () {
        const action = $(this).data('action');
        const category = $(this).data('category');
        const file = $(this).data('file');

        $('#denyRequirementForm').attr('action', action);
        $('#denyRequirementCategory').text(category || 'Requirement document');
        $('#denyRequirementFile').text(file || '');
        $('#denyRequirementReason').val('');
    });

    $('#approveAllFilesForm').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Approve all files?',
            text: 'Are you sure you want to approve all files?',
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
