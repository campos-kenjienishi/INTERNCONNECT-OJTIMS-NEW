(function () {
    'use strict';

    const forms = new Set();

    function hasSelectedFile(form) {
        return Array.from(form.querySelectorAll('input[type="file"]')).some(function (input) {
            return input.files && input.files.length > 0;
        });
    }

    function resetForm(form) {
        form.dataset.uploadValidationLocked = 'false';
        form.classList.remove('is-upload-validating');
        form.removeAttribute('aria-busy');

        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
            button.disabled = false;

            if (button._uploadValidationOriginalHtml !== undefined) {
                button.innerHTML = button._uploadValidationOriginalHtml;
                delete button._uploadValidationOriginalHtml;
            }

            if (button._uploadValidationOriginalValue !== undefined) {
                button.value = button._uploadValidationOriginalValue;
                delete button._uploadValidationOriginalValue;
            }
        });
    }

    function lockForm(form, submitter) {
        form.dataset.uploadValidationLocked = 'true';
        form.classList.add('is-upload-validating');
        form.setAttribute('aria-busy', 'true');

        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
            if (button.tagName === 'BUTTON') {
                button._uploadValidationOriginalHtml = button.innerHTML;
            } else {
                button._uploadValidationOriginalValue = button.value;
            }

            button.disabled = true;
        });

        if (submitter && submitter.tagName === 'BUTTON') {
            submitter.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Validating...';
        } else if (submitter) {
            submitter.value = 'Validating...';
        }
    }

    function showValidationDialog(form) {
        if (!window.Swal) {
            return;
        }

        window.Swal.fire({
            title: form.dataset.uploadTitle || 'Validating upload',
            text: form.dataset.uploadDescription || 'Checking the file type, structure, size, and security requirements. Please keep this page open.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function () {
                window.Swal.showLoading();
            }
        });
    }

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[data-upload-validation]');

        if (!form || event.defaultPrevented || !hasSelectedFile(form)) {
            return;
        }

        if (form.dataset.uploadValidationLocked === 'true') {
            event.preventDefault();
            return;
        }

        forms.add(form);
        lockForm(form, event.submitter || form.querySelector('button[type="submit"], input[type="submit"]'));
        showValidationDialog(form);
    });

    window.addEventListener('pageshow', function () {
        forms.forEach(resetForm);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const feedback = window.uploadValidationFeedback;

        if (!feedback || !window.Swal) {
            return;
        }

        window.Swal.fire({
            icon: feedback.type,
            title: feedback.title,
            text: feedback.message,
            confirmButtonText: 'Okay',
            confirmButtonColor: feedback.type === 'success' ? '#16a34a' : '#dc2626',
            allowOutsideClick: false
        }).then(function () {
            document.dispatchEvent(new CustomEvent('upload-validation:feedback-closed', {
                detail: feedback
            }));
        });
    });
})();
