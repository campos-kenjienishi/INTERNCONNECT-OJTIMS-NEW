/* Student Scripts */

        const SIDEBAR_COLLAPSED_KEY = 'internconnect_sidebar_collapsed';
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle  = document.getElementById('menuToggle');
        const overlay     = document.getElementById('sidebarOverlay');

        // Restore persisted desktop sidebar state
        if (localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === 'true' && window.innerWidth > 900) {
            if (sidebar) sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
            document.documentElement.classList.add('sidebar-is-collapsed');
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', function () {
                const isMobile = window.innerWidth <= 900;
                if (isMobile) {
                    if (sidebar) sidebar.classList.toggle('mobile-open');
                    if (overlay) overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem(SIDEBAR_COLLAPSED_KEY, isCollapsed ? 'true' : 'false');
                    if (isCollapsed) {
                        document.documentElement.classList.add('sidebar-is-collapsed');
                    } else {
                        document.documentElement.classList.remove('sidebar-is-collapsed');
                    }
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                if (sidebar) sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }

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