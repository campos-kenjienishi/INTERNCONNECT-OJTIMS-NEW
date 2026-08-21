/* ==========================================================================
   Coordinator Search Page Scripts
   Extracted from ojtCoordinator/search.blade.php
   ========================================================================== */

    document.querySelectorAll('.legacy-remove-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const templateName = form.dataset.templateName || 'this template';
            const proceed = function () { form.submit(); };

            if (typeof Swal === 'undefined') {
                if (window.confirm('Remove ' + templateName + '? This cannot be undone.')) {
                    proceed();
                }
                return;
            }

            Swal.fire({
                title: 'Remove template?',
                html: 'This will permanently delete <strong>' + templateName + '</strong>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    proceed();
                }
            });
        });
    });
