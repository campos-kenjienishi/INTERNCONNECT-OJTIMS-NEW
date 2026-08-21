/* ==========================================================================
   Coordinator Upload Page Scripts
   Extracted from ojtCoordinator/upload.blade.php
   ========================================================================== */

// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const menuToggle = document.getElementById('menuToggle');
const overlay = document.getElementById('sidebarOverlay');

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

// Dark mode toggle
$(document).ready(function () {

    // DataTable
    $('#fileTable').DataTable({
        order: [],
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false
    });

    // File input: show selected filename in dropzone
    $('#fileInput').on('change', function () {
        const fileName = this.files[0] ? this.files[0].name : '';
        if (fileName) {
            $('#selectedFileName').text(fileName).show();
            $('#dropzone').css('border-color', 'var(--red)');
        }
    });

    // Drag and drop visual feedback
    const dz = document.getElementById('dropzone');
    dz.addEventListener('dragover', () => dz.classList.add('dragover'));
    dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
    dz.addEventListener('drop', () => dz.classList.remove('dragover'));

    // Download button with SweetAlert toast
    $(document).on('click', '.btn-dl-item', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');

        const Toast = Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({ icon: 'info', title: 'File download initiated' });

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', '');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Remove button with SweetAlert confirmation
    $(document).on('click', '.remove-button', function () {
        const fileId = $(this).data('file-id');
        Swal.fire({
            title: 'Remove Template?',
            text: 'This will permanently delete the template.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/remove/' + fileId,
                    data: { _token: (window.coordinatorConfig && window.coordinatorConfig.csrfToken) || (meta[name = "csrf-token"].attr('content') || '') },
                    success: function () { location.reload(); },
                    error: function () { console.error('Remove failed.'); }
                });
            }
        });
    });

    // File preview modal handler
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

    });
