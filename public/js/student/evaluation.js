/* Student Scripts */

        (function () {
            if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined' || !document.getElementById('historyTable')) {
                return;
            }

            const historyTable = $('#historyTable').DataTable({
                dom: 't<"history-bottom"ip>',
                order: [[3, 'desc']],
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                autoWidth: false,
                language: {
                    emptyTable: 'No evaluation requests yet.'
                },
                columnDefs: [
                    { targets: [5], orderable: false, searchable: false }
                ]
            });

            $('#historyPerPage').on('change', function () {
                historyTable.page.len(Number(this.value)).draw();
            });

            $('#historySearch').on('input', function () {
                historyTable.search(this.value).draw();
            });

            $('#historySort').on('change', function () {
                historyTable.order([[3, this.value === 'oldest' ? 'asc' : 'desc']]).draw();
            });
        })();

        (function () {
            const form = document.getElementById('sendEvaluationForm');
            if (!form) {
                return;
            }

            const emailInput = form.querySelector('input[name="supervisor_email"]');
            const confirmInput = document.getElementById('confirmEmailMismatch');
            const submitButton = document.getElementById('sendEvaluationButton');
            const emailBubble = document.getElementById('supervisorEmailBubble');
            const expectedEmail = (form.dataset.expectedEmail || '').trim().toLowerCase();
            const ownEmail = (window.studentEvaluationConfig?.ownEmail || '').trim().toLowerCase();

            function showEmailBubble(message) {
                if (!emailBubble) {
                    return;
                }

                if (!message) {
                    emailBubble.textContent = '';
                    emailBubble.classList.remove('active');
                    return;
                }

                emailBubble.textContent = message;
                emailBubble.classList.add('active');
            }

            function syncSupervisorEmailGuard() {
                if (!emailInput) {
                    return false;
                }

                const entered = (emailInput.value || '').trim().toLowerCase();
                const isOwnEmail = Boolean(ownEmail) && entered !== '' && entered === ownEmail;

                if (submitButton) {
                    submitButton.disabled = isOwnEmail;
                    submitButton.style.opacity = isOwnEmail ? '0.6' : '';
                    submitButton.style.cursor = isOwnEmail ? 'not-allowed' : '';
                }

                if (isOwnEmail) {
                    emailInput.setCustomValidity('Do not use your own student email.');
                    emailInput.style.borderColor = '#dc2626';
                    emailInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showEmailBubble("Don't use your own student email. Enter your supervisor's email.");
                    return true;
                }

                emailInput.setCustomValidity('');
                emailInput.style.borderColor = '';
                emailInput.style.boxShadow = '';
                showEmailBubble('');
                return false;
            }

            if (emailInput) {
                emailInput.addEventListener('input', syncSupervisorEmailGuard);
                emailInput.addEventListener('blur', syncSupervisorEmailGuard);
                syncSupervisorEmailGuard();
            }

            form.addEventListener('submit', function (event) {
                if (!emailInput || !confirmInput) {
                    return;
                }

                if (syncSupervisorEmailGuard()) {
                    event.preventDefault();
                    return;
                }

                confirmInput.value = '0';
                const entered = (emailInput.value || '').trim().toLowerCase();

                if (expectedEmail && entered && expectedEmail !== entered) {
                    event.preventDefault();

                    const proceed = function () {
                        confirmInput.value = '1';
                        form.submit();
                    };

                    if (typeof Swal === 'undefined') {
                        if (window.confirm('The entered email does not match the email from your submitted MOA. Are you sure you want to continue?')) {
                            proceed();
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'Use different supervisor email?',
                        html: 'The email you entered does not match the supervisor email from your submitted MOA.<br><br>Only continue if this is intentional.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, continue',
                        cancelButtonText: 'Go back',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceed();
                        }
                    });
                }
            });
        })();

        (function () {
            document.querySelectorAll('.cancel-evaluation-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const email = form.dataset.supervisorEmail || 'this supervisor';
                    const proceed = function () { form.submit(); };

                    if (typeof Swal === 'undefined') {
                        if (window.confirm('Cancel this evaluation request for ' + email + '?')) {
                            proceed();
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'Cancel evaluation request?',
                        html: 'This will cancel the request sent to <strong>' + email + '</strong>.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, cancel it',
                        cancelButtonText: 'Keep it',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceed();
                        }
                    });
                });
            });
        })();