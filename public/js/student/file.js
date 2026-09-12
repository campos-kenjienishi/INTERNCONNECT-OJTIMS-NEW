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
            var fileExt = ($(this).data('file-ext') || '').toString().toLowerCase();
            var downloadUrl = $(this).data('download-url');

            $('#filePreviewTitle').text(fileName || 'Document Preview');
            $('#filePreviewSubTitle').text(fileName || '');
            $('#filePreviewDownloadBtn').attr('href', downloadUrl);
            $('#fileNoticeDownloadBtn').attr('href', downloadUrl);

            var previewableExts = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'svg'];
            if (previewableExts.includes(fileExt)) {
                $('#filePreviewNotice').hide();
                $('#filePreviewFrame').attr('src', fileUrl).show();
                $('#filePreviewBadge').html('<i class="fa fa-eye"></i> Preview');
            } else {
                $('#filePreviewFrame').attr('src', 'about:blank').hide();
                var extUpper = fileExt ? fileExt.toUpperCase() : 'DOCX';
                var iconClass = (fileExt === 'docx' || fileExt === 'doc') ? 'fa-file-word' :
                                (fileExt === 'xlsx' || fileExt === 'xls') ? 'fa-file-excel' :
                                (fileExt === 'pptx' || fileExt === 'ppt') ? 'fa-file-powerpoint' : 'fa-file-alt';
                
                $('#fileNoticeIcon').attr('class', 'fa ' + iconClass);
                $('#fileNoticeHeading').text('Preview Not Supported for ' + extUpper + ' Documents');
                $('#fileNoticeText').text('In-browser preview is not supported for .' + fileExt + ' files. Instead of viewing in browser, please download the file to open and view it directly on your device.');
                $('#filePreviewNotice').show();
                $('#filePreviewBadge').html('<i class="fa fa-info-circle"></i> Download Required');
            }

            var modalEl = document.getElementById('filePreviewModal');
            if (modalEl && modalEl.parentNode !== document.body) {
                document.body.appendChild(modalEl);
            }
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && $('#fileTable').length && !$.fn.DataTable.isDataTable('#fileTable')) {
                $('#fileTable').DataTable({
                    order: [[2, 'desc']],
                    autoWidth: false,
                    pageLength: 10,
                    columnDefs: [
                        { width: "28%", targets: 0 },
                        { width: "20%", targets: 1 },
                        { width: "16%", targets: 2 },
                        { width: "16%", targets: 3 },
                        { width: "20%", targets: 4, orderable: false, searchable: false, className: "text-end" }
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search files..."
                    }
                });
            }

            var modalEl = document.getElementById('filePreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    var frame = document.getElementById('filePreviewFrame');
                    if (frame) frame.src = 'about:blank';
                    $('#filePreviewNotice').hide();
                });
            }
        });