/* Student Scripts */

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

        $(document).on('click', '.btn-preview-file', function (e) {
            e.preventDefault();
            var fileUrl = $(this).data('file-url');
            var fileName = $(this).data('file-name');
            var downloadUrl = $(this).data('download-url');

            $('#filePreviewTitle').text(fileName || 'Document Preview');
            $('#filePreviewSubTitle').text(fileName || '');
            $('#filePreviewDownloadBtn').attr('href', downloadUrl);
            $('#filePreviewFrame').attr('src', fileUrl);

            var modalEl = document.getElementById('filePreviewModal');
            if (modalEl && modalEl.parentNode !== document.body) {
                document.body.appendChild(modalEl);
            }
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });

        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('filePreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    var frame = document.getElementById('filePreviewFrame');
                    if (frame) frame.src = 'about:blank';
                });
            }
        });