/* ==========================================================================
   Coordinator Audit Logs Page Scripts
   Extracted from ojtCoordinator/audit.blade.php
   ========================================================================== */

                    $(document).ready(function () {
                        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                            if (settings.nTable.id !== 'auditTable') {
                                return true;
                            }

                            const rowNode = settings.aoData[dataIndex]?.nTr;
                            if (!rowNode) {
                                return true;
                            }

                            const selectedAction = ($('#actionFilter').val() || '').toLowerCase();
                            const selectedRole = ($('#roleFilter').val() || '').toLowerCase();
                            const selectedMonth = $('#monthFilter').val();
                            const selectedYear = $('#yearFilter').val();
                            const rowAction = (rowNode.getAttribute('data-action') || '').toLowerCase();
                            const rowRole = (rowNode.getAttribute('data-role') || '').toLowerCase();
                            const rowMonth = rowNode.getAttribute('data-month') || '';
                            const rowYear = rowNode.getAttribute('data-year') || '';

                            if (selectedAction && rowAction !== selectedAction) {
                                return false;
                            }

                            if (selectedRole && rowRole !== selectedRole) {
                                return false;
                            }

                            if (selectedMonth && rowMonth !== selectedMonth) {
                                return false;
                            }

                            if (selectedYear && rowYear !== selectedYear) {
                                return false;
                            }

                            return true;
                        });

                        const table = $('#auditTable').DataTable({
                            "order": [[0, 'desc']],
                            "pageLength": 10,
                            "scrollX": true,
                            "autoWidth": false,
                            "dom": '<"audit-toolbar"lf>rtip',
                            "initComplete": function() {
                                $('#auditTableLoading').fadeOut(220, function() {
                                    $(this).remove();
                                });
                                $('#auditTable, .dataTables_scrollHead table, .dataTables_scroll').css('opacity', '1');
                            }
                        });

                        const $toolbar = $('#auditTable_wrapper .audit-toolbar');
                        const $length = $toolbar.find('.dataTables_length');
                        const $filter = $toolbar.find('.dataTables_filter');
                        const $customFilters = $('#auditFilters');
                        const $headerSearch = $('#auditTableHeaderSearch');

                        $customFilters.insertAfter($length);
                        $headerSearch.append($filter);

                        $('#actionFilter, #roleFilter, #monthFilter, #yearFilter').on('change', function () {
                            table.draw();
                        });

                        $(document).on('click', '.btn-view-desc', function () {
                            const modal = document.getElementById('descModal');
                            document.getElementById('descModalAction').textContent = $(this).data('action') || 'N/A';
                            document.getElementById('descModalRole').textContent = $(this).data('role') || 'Unknown';
                            document.getElementById('descModalModule').textContent = $(this).data('module') || 'N/A';
                            document.getElementById('descModalDate').textContent = $(this).data('datetime') || 'N/A';
                            document.getElementById('descModalText').textContent = $(this).data('description') || 'No description available.';
                            modal.classList.add('show');
                            document.body.style.overflow = 'hidden';
                        });

                        function closeDescModal() {
                            $('#descModal').removeClass('show');
                            document.body.style.overflow = '';
                        }

                        $('#descModalClose').on('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            closeDescModal();
                        });

                        $('#descModal').on('click', function (event) {
                            if (event.target === this) {
                                closeDescModal();
                            }
                        });

                        $(document).on('keydown', function (event) {
                            if (event.key === 'Escape' && $('#descModal').hasClass('show')) {
                                closeDescModal();
                            }
                        });
                    });

    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            const shouldOpen = !sidebar.classList.contains('mobile-open');
            sidebar.classList.toggle('mobile-open', shouldOpen);
            overlay.classList.toggle('active', shouldOpen);
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    const closeMobileSidebar = function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    };

    ['click', 'touchstart'].forEach(function (eventName) {
        document.addEventListener(eventName, function (event) {
            if (window.innerWidth > 900 || !sidebar.classList.contains('mobile-open')) {
                return;
            }

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedMenuToggle = menuToggle.contains(event.target);

            if (!clickedInsideSidebar && !clickedMenuToggle) {
                closeMobileSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileSidebar();
        }
    });
