/* Professor Evaluation List Scripts */

    (function () {
        if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined' || !document.getElementById('studentStatusTable')) {
            return;
        }

        const studentStatusTable = $('#studentStatusTable').DataTable({
            dom: 't<"history-bottom"ip>',
            order: [[2, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            autoWidth: false,
            language: {
                emptyTable: 'No students found for this class.'
            },
            columnDefs: [
                { targets: [5, 6], orderable: false }
            ]
        });

        $('#studentStatusPerPage').on('change', function () {
            studentStatusTable.page.len(Number(this.value)).draw();
        });

        $('#studentStatusSearch').on('input', function () {
            studentStatusTable.search(this.value).draw();
        });

        $('#studentStatusFilter').on('change', function () {
            const value = this.value;
            let pattern = '';

            if (value === 'submitted') {
                pattern = '^SUBMITTED$';
            } else if (value === 'in_progress') {
                pattern = '^(SENT|OPENED)$';
            } else if (value === 'other') {
                pattern = '^(EXPIRED|CANCELLED)$';
            } else if (value === 'not_sent') {
                pattern = '^NOT SENT$';
            }

            studentStatusTable.column(2).search(pattern, true, false).draw();
        });
    })();

    (function () {
        const openBtn = document.getElementById('openEvalPrintModalBtn');
        const reportUrl = (window.professorEvalListConfig?.printUrl || '/professor/evaluation/print');

        if (!openBtn) {
            return;
        }

        openBtn.addEventListener('click', function () {
            const frame = document.createElement('iframe');
            frame.style.position = 'fixed';
            frame.style.right = '0';
            frame.style.bottom = '0';
            frame.style.width = '0';
            frame.style.height = '0';
            frame.style.border = '0';
            frame.style.opacity = '0';
            frame.setAttribute('aria-hidden', 'true');
            frame.src = reportUrl;

            let cleanedUp = false;
            const cleanup = function () {
                if (cleanedUp) {
                    return;
                }
                cleanedUp = true;
                window.removeEventListener('afterprint', cleanup);
                if (frame.parentNode) {
                    frame.parentNode.removeChild(frame);
                }
            };

            frame.onload = function () {
                setTimeout(function () {
                    if (frame.contentWindow) {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                        window.addEventListener('afterprint', cleanup, { once: true });
                        setTimeout(cleanup, 1500);
                    } else {
                        cleanup();
                    }
                }, 150);
            };

            document.body.appendChild(frame);
        });
    })();
