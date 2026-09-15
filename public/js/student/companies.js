/* ==========================================================================
   Student MOA & Companies Scripts
   Extracted from resources/views/students/companiesup.blade.php
   ========================================================================== */

// Unlock request modal opener
window.openUnlockRequestModal = function(type, isOwner) {
    const select = document.getElementById('modalRequestType');
    if (select) {
        const editOpt = select.querySelector('option[value="edit"]');
        const unlinkOpt = select.querySelector('option[value="unlink"]');
        const switchOpt = select.querySelector('option[value="switch_external"]');

        if (type === 'switch_external') {
            if (editOpt) editOpt.style.display = 'none';
            if (unlinkOpt) unlinkOpt.style.display = 'none';
            if (switchOpt) switchOpt.style.display = 'block';
            select.value = 'switch_external';
        } else if (isOwner === false) {
            if (editOpt) editOpt.style.display = 'none';
            if (unlinkOpt) unlinkOpt.style.display = 'block';
            if (switchOpt) switchOpt.style.display = 'none';
            select.value = 'unlink';
        } else {
            if (editOpt) editOpt.style.display = 'block';
            if (unlinkOpt) unlinkOpt.style.display = 'block';
            if (switchOpt) switchOpt.style.display = 'none';
            select.value = type || 'edit';
        }
    }
    const modalEl = document.getElementById('requestUnlockModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

// Remove / Unlink MOA confirmation
window.confirmStudentRemove = function(companyId, companyName, isOwner) {
    const title = isOwner ? 'Remove MOA?' : 'Unlink MOA?';
    const html = isOwner
        ? 'This will remove your notarized MOA record for <strong>' + companyName + '</strong>.'
        : 'This will unlink <strong>' + companyName + '</strong> from your account.';
    const confirmText = isOwner ? 'Yes, remove it' : 'Yes, unlink it';

    if (typeof Swal === 'undefined') {
        if (confirm(title)) {
            const form = document.getElementById('student-remove-form-' + companyId);
            if (form) form.submit();
        }
        return;
    }

    Swal.fire({
        title: title,
        html: html,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancel',
    }).then(function (result) {
        if (result.isConfirmed) {
            const form = document.getElementById('student-remove-form-' + companyId);
            if (form) form.submit();
        }
    });
};

// PDF preview / print modal
window.openPdfPreview = function(url) {
    const iframe = document.getElementById('viewIframe');
    const modalEl = document.getElementById('viewModal');
    if (iframe) iframe.src = url;
    if (modalEl) new bootstrap.Modal(modalEl).show();
};

window.openVoucherModal = function(url) {
    const iframe = document.getElementById('voucherIframe');
    const modalEl = document.getElementById('voucherModal');
    if (iframe) iframe.src = url;
    if (modalEl) new bootstrap.Modal(modalEl).show();
};

window.syncSchoolYearEnd = function(startId, endId, selectedEndYear = null) {
    const startSelect = document.getElementById(startId);
    const endSelect = document.getElementById(endId);

    if (!startSelect || !endSelect || !startSelect.value) {
        return;
    }

    const startYear = parseInt(startSelect.value, 10);
    if (Number.isNaN(startYear)) {
        return;
    }

    const endYear = selectedEndYear ? parseInt(selectedEndYear, 10) : startYear + 1;
    endSelect.innerHTML = '';

    const option = document.createElement('option');
    option.value = String(endYear);
    option.textContent = String(endYear);
    option.selected = true;
    endSelect.appendChild(option);
    endSelect.value = String(endYear);
};

window.openEditMoaModal = function(button) {
    const form = document.getElementById('editMoaForm');
    if (!form) return;

    const schoolYear = (button.dataset.schoolYear || '').split('-');
    const currentFile = button.dataset.fileName || '';

    form.action = button.dataset.updateUrl;
    document.getElementById('editCompanyName').value = button.dataset.companyName || '';
    document.getElementById('editCompanyAddress').value = button.dataset.companyAddress || '';
    document.getElementById('editCompanyRep').value = button.dataset.companyRep || '';
    document.getElementById('editCompanyNo').value = button.dataset.companyNo || '';
    document.getElementById('editCompanyEmail').value = button.dataset.companyEmail || '';
    document.getElementById('editSchoolYearStart').value = schoolYear[0] || '';
    window.syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd', schoolYear[1] || '');
    document.getElementById('editDateNotarized').value = button.dataset.dateNotarized || '';
    document.getElementById('editValidUntil').value = button.dataset.validUntil || '';
    document.getElementById('editMoaFileInput').value = '';
    document.getElementById('editMoaFileLabel').textContent = 'Leave empty to keep the current notarized MOA PDF';
    document.getElementById('editMoaCurrentFile').textContent = currentFile
        ? 'Current file: ' + currentFile + '. Leave the file empty if you only need to update the company details.'
        : 'Leave the file empty if you only need to update the company details.';

    const modalEl = document.getElementById('editMoaModal');
    if (modalEl) new bootstrap.Modal(modalEl).show();
};

window.printRegularPreview = function() {
    const iframe = document.getElementById('viewIframe');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.print();
    }
};

window.bindPdfInputValidation = function(inputId, labelId, emptyLabel) {
    const input = document.getElementById(inputId);
    if (!input) return;

    input.addEventListener('change', function () {
        const label = document.getElementById(labelId);
        const file = this.files.length > 0 ? this.files[0] : null;

        if (file && !file.name.toLowerCase().endsWith('.pdf')) {
            this.value = '';
            if (label) label.textContent = emptyLabel;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'PDF only',
                    text: 'Please upload the notarized MOA as a PDF file.',
                    confirmButtonColor: '#d32f2f',
                });
            } else {
                alert('Please upload the notarized MOA as a PDF file.');
            }
            return;
        }

        if (label) label.textContent = file ? file.name : emptyLabel;
    });
};

// Sidebar mobile handler
(function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function closeMobileSidebar() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('mobile-sidebar-open');
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                if (sidebar && sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    if (sidebar) sidebar.classList.add('mobile-open');
                    if (overlay) overlay.classList.add('active');
                    document.body.classList.add('mobile-sidebar-open');
                }
            } else {
                if (sidebar) sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    document.addEventListener('click', function (event) {
        if (window.innerWidth > 900 || !sidebar || !sidebar.classList.contains('mobile-open')) return;
        if (sidebar.contains(event.target) || (menuToggle && menuToggle.contains(event.target))) return;
        closeMobileSidebar();
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) closeMobileSidebar();
    });
})();

// Document Ready Initialization
$(document).ready(function () {
    // DataTable for MOA
    if ($('#moaTable').length && $.fn.DataTable) {
        if (!$.fn.DataTable.isDataTable('#moaTable')) {
            $('#moaTable').DataTable({
                scrollX: true,
                autoWidth: false,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: 4 }
                ]
            });
        }
    }

    // DataTable for Company1 (pending)
    if ($('#company1Table').length && $.fn.DataTable) {
        if (!$.fn.DataTable.isDataTable('#company1Table')) {
            $('#company1Table').DataTable();
        }
    }

    function validateForm($form) {
        let valid = true;
        $form.find('input[required]').each(function () {
            const errorId = $(this).attr('name') + '-error';
            if ($(this).val() === '') {
                valid = false;
                $('#' + errorId).show();
            } else {
                $('#' + errorId).hide();
            }
        });
        return valid;
    }

    ['#studentMoaForm', '#editMoaForm'].forEach(function (selector) {
        $(selector).on('submit', function (e) {
            if (!validateForm($(this))) {
                e.preventDefault();
                return;
            }

            if (this.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }

            this.dataset.submitting = 'true';

            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
            }
        });
    });

    window.bindPdfInputValidation('moaFileInput', 'moaFileLabel', 'Click or drag your notarized MOA file here');
    window.bindPdfInputValidation('editMoaFileInput', 'editMoaFileLabel', 'Leave empty to keep the current notarized MOA PDF');

    const cfg = window.companiesConfig || {};
    window.syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd', cfg.selectedCreateEndYear || null);
    window.syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd');

    $('#schoolYearStart').on('change', function () {
        window.syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd');
    });

    $('#editSchoolYearStart').on('change', function () {
        window.syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd');
    });

    const existingMoaSearch = document.getElementById('existingMoaSearch');
    if (existingMoaSearch) {
        existingMoaSearch.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const items = Array.from(document.querySelectorAll('.existing-moa-item'));
            let visibleCount = 0;

            items.forEach(function (item) {
                const companyName = item.dataset.companyName || '';
                const matches = companyName.includes(query);
                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount += 1;
            });

            const noResults = document.getElementById('existingMoaNoResults');
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        });
    }

    let pendingFormType = null;
    let pendingCompanyId = null;

    document.querySelectorAll('.existing-moa-link-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const companyIdInput = document.getElementById('linkExistingMoaCompanyId');
            const linkForm = document.getElementById('linkExistingMoaForm');
            if (!companyIdInput || !linkForm) return;

            pendingCompanyId = this.dataset.companyId || '';
            companyIdInput.value = pendingCompanyId;

            const item = this.closest('.existing-moa-item');
            const companyName = item ? (item.querySelector('[style*="font-weight:800"]')?.innerText || 'this company') : 'this company';

            const confirmTextEl = document.getElementById('confirmCompanyNameText');
            if (confirmTextEl) confirmTextEl.innerText = companyName;
            pendingFormType = 'link';

            const addModalEl = document.getElementById('addMoaModal');
            if (addModalEl) {
                const addModalInst = bootstrap.Modal.getInstance(addModalEl);
                if (addModalInst) addModalInst.hide();
            }

            const confirmModalEl = document.getElementById('confirmMoaLockModal');
            if (confirmModalEl) {
                const confirmModal = new bootstrap.Modal(confirmModalEl);
                confirmModal.show();
            }
        });
    });

    const studentMoaForm = document.getElementById('studentMoaForm');
    if (studentMoaForm) {
        studentMoaForm.addEventListener('submit', function (e) {
            if (window.moaLockConfirmed) return;

            e.preventDefault();
            const companyNameInput = this.querySelector('input[name="company_name"]');
            const compName = companyNameInput ? companyNameInput.value.trim() : 'this company';

            const confirmTextEl = document.getElementById('confirmCompanyNameText');
            if (confirmTextEl) confirmTextEl.innerText = compName || 'this company';
            pendingFormType = 'create';

            const addModalEl = document.getElementById('addMoaModal');
            if (addModalEl) {
                const addModalInst = bootstrap.Modal.getInstance(addModalEl);
                if (addModalInst) addModalInst.hide();
            }

            const confirmModalEl = document.getElementById('confirmMoaLockModal');
            if (confirmModalEl) {
                const confirmModal = new bootstrap.Modal(confirmModalEl);
                confirmModal.show();
            }
        });
    }

    const btnConfirmLockSubmit = document.getElementById('btnConfirmLockSubmit');
    if (btnConfirmLockSubmit) {
        btnConfirmLockSubmit.addEventListener('click', function () {
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Processing...';

            if (pendingFormType === 'link') {
                const linkForm = document.getElementById('linkExistingMoaForm');
                if (linkForm) linkForm.submit();
            } else if (pendingFormType === 'create') {
                window.moaLockConfirmed = true;
                if (studentMoaForm) studentMoaForm.submit();
            }
        });
    }

    $('.submitBtn').on('click', function (event) {
        let valid = true;
        $('input[required]').each(function () {
            let errorMessageId = $(this).attr('name') + '-error';
            if ($(this).val() === '') {
                valid = false;
                $('#' + errorMessageId).show();
            } else {
                $('#' + errorMessageId).hide();
            }
        });
        if (!valid) {
            event.preventDefault();
        }
    });
});

// Delegate click for preview buttons
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-btn');
    if (btn) {
        const url = btn.getAttribute('data-url');
        if (url) window.openPdfPreview(url);
    }
});

// Auto-open voucher modal if session flashed
(function() {
    const cfg = window.companiesConfig || {};
    if (cfg.showVoucherModal) {
        window.addEventListener('load', function () {
            window.openVoucherModal(cfg.showVoucherModal);
        });
    }
})();
