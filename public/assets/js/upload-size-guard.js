(function () {
    function formatLimit(maxBytes) {
        if (maxBytes >= 1024 * 1024) {
            return (maxBytes / (1024 * 1024)).toFixed(maxBytes % (1024 * 1024) === 0 ? 0 : 1) + ' MB';
        }
        if (maxBytes >= 1024) {
            return (maxBytes / 1024).toFixed(maxBytes % 1024 === 0 ? 0 : 1) + ' KB';
        }
        return maxBytes + ' bytes';
    }

    function setError(input, message) {
        const errorEl = input.parentElement ? input.parentElement.querySelector('.file-size-error') : null;
        input.setCustomValidity(message || '');

        if (errorEl) {
            errorEl.textContent = message || '';
            errorEl.style.display = message ? 'block' : 'none';
        }
    }

    function validateFile(input) {
        const maxMb = Number(input.dataset.maxSizeMb || 0);
        if (!maxMb) {
            return true;
        }

        const file = input.files && input.files[0] ? input.files[0] : null;
        const maxBytes = maxMb * 1024 * 1024;

        if (!file) {
            setError(input, '');
            return true;
        }

        if (file.size > maxBytes) {
            setError(input, 'File is too large. Maximum allowed size is ' + formatLimit(maxBytes) + '.');
            if (typeof input.reportValidity === 'function') {
                input.reportValidity();
            }
            return false;
        }

        setError(input, '');
        return true;
    }

    function bindInput(input) {
        const form = input.form;

        input.addEventListener('change', function () {
            validateFile(input);
        });

        if (form) {
            form.addEventListener('submit', function (e) {
                if (!validateFile(input)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    input.focus();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[type="file"][data-max-size-mb]').forEach(bindInput);
    });
})();
