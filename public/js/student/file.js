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

        var fileTableInstance = null;
        var classFileTableInstance = null;

        function initDataTables() {
            if (window.jQuery && $('#fileTable').length && !$.fn.DataTable.isDataTable('#fileTable')) {
                fileTableInstance = $('#fileTable').DataTable({
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
                        searchPlaceholder: "Search general files..."
                    }
                });
            }

            if (window.jQuery && $('#classFileTable').length && !$.fn.DataTable.isDataTable('#classFileTable')) {
                classFileTableInstance = $('#classFileTable').DataTable({
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
                        searchPlaceholder: "Search class templates..."
                    }
                });
            }
        }

        function switchFileTab(tabName) {
            $('.file-tab-btn').removeClass('active');
            $('.tab-pane').hide().removeClass('active');

            if (tabName === 'class') {
                $('#tabBtnClass').addClass('active');
                $('#classTabPane').fadeIn(150).addClass('active');
                if (classFileTableInstance) {
                    classFileTableInstance.columns.adjust().draw();
                }
            } else {
                $('#tabBtnGeneral').addClass('active');
                $('#generalTabPane').fadeIn(150).addClass('active');
                if (fileTableInstance) {
                    fileTableInstance.columns.adjust().draw();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initDataTables();

            // Tab button clicks
            $('.file-tab-btn').on('click', function () {
                var targetTab = $(this).attr('id') === 'tabBtnClass' ? 'class' : 'general';
                switchFileTab(targetTab);
                if (history.pushState) {
                    history.pushState(null, null, targetTab === 'class' ? '#class' : '#general');
                } else {
                    location.hash = targetTab === 'class' ? '#class' : '#general';
                }
            });

            // Activate tab from URL hash
            if (window.location.hash === '#class') {
                switchFileTab('class');
            }

            // Handle popstate / hashchange
            window.addEventListener('hashchange', function () {
                if (window.location.hash === '#class') {
                    switchFileTab('class');
                } else {
                    switchFileTab('general');
                }
            });

            var modalEl = document.getElementById('filePreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    var frame = document.getElementById('filePreviewFrame');
                    if (frame) frame.src = 'about:blank';
                    $('#filePreviewNotice').hide();
                });
            }
        });