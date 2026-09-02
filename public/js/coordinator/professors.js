/* ==========================================================================
   Coordinator Professors Page Scripts
   Extracted from ojtCoordinator/professorTab.blade.php
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

$(document).ready(function () {

    // DataTable
    $('#profTable').DataTable({
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false
    });

    // Academic year end options
    const startYearSelect = document.getElementById('academic_year_start');
    const endYearSelect = document.getElementById('academic_year_end');

    function updateEndYearOptions() {
        const selectedStart = parseInt(startYearSelect.value);
        endYearSelect.innerHTML = '<option value="">End Year</option>';
        for (let y = selectedStart + 1; y <= selectedStart + 10; y++) {
            const opt = document.createElement('option');
            opt.value = y; opt.textContent = y;
            endYearSelect.appendChild(opt);
        }
    }

    if (startYearSelect) {
        updateEndYearOptions();
        startYearSelect.addEventListener('change', updateEndYearOptions);
    }

    function buildTimeOptions() {
        let options = '';

        for (let hour = 0; hour < 24; hour++) {
            for (let minute = 0; minute < 60; minute += 15) {
                const value = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                options += '<option value="' + value + '"></option>';
            }
        }

        return options;
    }

    function ensureTimeSuggestions() {
        if (document.getElementById('scheduleTimeSuggestions')) {
            return;
        }

        const datalist = document.createElement('datalist');
        datalist.id = 'scheduleTimeSuggestions';
        datalist.innerHTML = buildTimeOptions();
        document.body.appendChild(datalist);
    }

    // Time slots dynamic inputs
    $('#time_slots').change(function () {
        const numSlots = parseInt($(this).val());
        $('#timeInputs').empty();
        ensureTimeSuggestions();
        if (numSlots > 0) {
            $('input[name="schedule_day[]"]:checked').each(function () {
                const day = $(this).val();
                for (let i = 1; i <= numSlots; i++) {
                    $('#timeInputs').append(`
                            <div style="margin-bottom:12px;">
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                    <div class="field-group">
                                        <label class="field-label"><i class="fa fa-clock"></i> Start Time ${i} (${day})</label>
                                        <input class="field-select schedule-time-input" type="text" name="${day}_start_time_${i}" placeholder="HH:MM" inputmode="numeric" list="scheduleTimeSuggestions" pattern="^([01]\\d|2[0-3]):([0-5]\\d)$" title="Enter time in 24-hour format like 08:00 or 13:30" required>
                                    </div>
                                    <div class="field-group">
                                        <label class="field-label"><i class="fa fa-clock"></i> End Time ${i} (${day})</label>
                                        <input class="field-select schedule-time-input" type="text" name="${day}_end_time_${i}" placeholder="HH:MM" inputmode="numeric" list="scheduleTimeSuggestions" pattern="^([01]\\d|2[0-3]):([0-5]\\d)$" title="Enter time in 24-hour format like 08:00 or 13:30" required>
                                    </div>
                                </div>
                            </div>
                        `);
                }
            });
        }
    });

    $(document).on('input change', '.schedule-time-input', function () {
        const isValid = /^([01]\d|2[0-3]):([0-5]\d)$/.test(this.value.trim());
        this.setCustomValidity(this.value.trim() === '' || isValid
            ? ''
            : 'Use HH:MM in 24-hour format, like 08:00 or 13:30.');
    });

    // Edit professor modal populate
    $(document).on('click', '.btnView1', function () {
        const $btn = $(this);
        $('#editProfessorId').val($btn.data('professor-id'));
        $('#editEmail').val($btn.data('email'));

        let firstName = $btn.data('first-name') || '';
        let middleName = $btn.data('middle-name') || '';
        let lastName = $btn.data('last-name') || '';
        let fullName = ($btn.data('full-name') || '').trim();

        if (!firstName && fullName) {
            const parts = fullName.split(/\s+/);
            if (parts.length === 1) {
                firstName = parts[0];
            } else if (parts.length === 2) {
                firstName = parts[0];
                lastName = parts[1];
            } else {
                firstName = parts[0];
                middleName = parts.slice(1, -1).join(' ');
                lastName = parts[parts.length - 1];
            }
        }

        $('#editFirstName').val(firstName);
        $('#editMiddleName').val(middleName);
        $('#editLastName').val(lastName);
    });

    // Remove professor with SweetAlert
    $(document).on('click', '.btnRemove', function () {
        const professorId = $(this).data('professor-id');
        Swal.fire({
            title: 'Remove Professor?',
            text: 'This will permanently remove the professor.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove!',
            cancelButtonText: 'Cancel',
            borderRadius: '16px',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/removeProfessor/' + professorId,
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        Swal.fire({
                            title: 'Removed!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#dc2626'
                        }).then(() => location.reload());
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Something went wrong.', 'error');
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });

    const addProfessorForm = document.getElementById('addProfessorForm');
    if (addProfessorForm) {
        const firstNameInput = document.getElementById('prof_first_name');
        const lastNameInput = document.getElementById('prof_last_name');
        const emailInput = document.getElementById('prof_email');
        const passwordInput = document.getElementById('prof_password');
        const confirmPasswordInput = document.getElementById('prof_password_confirmation');
        const togglePasswordButton = document.getElementById('toggleProfPassword');
        const toggleConfirmPasswordButton = document.getElementById('toggleProfPasswordConfirmation');
        const emailCheckUrl = addProfessorForm.dataset.emailCheckUrl || '';
        let emailCheckTimer = null;
        let emailRequestCounter = 0;

        function setupPasswordToggle(toggle, input) {
            if (!toggle || !input) return;

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';

                const icon = toggle.querySelector('i');
                if (icon) {
                    if (isHidden) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        }

        function setInputErrorState(input, hasError) {
            if (!input) return;
            input.style.borderColor = hasError ? '#dc2626' : '';
            input.style.boxShadow = hasError ? '0 0 0 3px rgba(220,38,38,0.1)' : '';
        }

        function showFieldBubble(bubbleId, message) {
            const bubble = document.getElementById(bubbleId);
            if (!bubble) return;

            if (!message) {
                bubble.innerHTML = '';
                bubble.classList.remove('active');
                return;
            }

            const messages = Array.isArray(message) ? message : [message];
            bubble.innerHTML = messages.map(function (item) {
                return '<div>' + item + '</div>';
            }).join('');
            bubble.classList.add('active');
        }

        function sanitizeNameValue(value, preserveTrailingSeparator = false) {
            const endsWithSeparator = preserveTrailingSeparator && /[\s'-]$/.test(value || '');
            let sanitized = (value || '').replace(/[^\p{L}\s'\-]/gu, '');
            sanitized = sanitized.replace(/\s+/g, ' ');
            sanitized = sanitized.replace(/\s*-\s*/g, '-');
            sanitized = sanitized.replace(/\s*'\s*/g, "'");
            sanitized = sanitized.replace(/-{2,}/g, '-');
            sanitized = sanitized.replace(/'{2,}/g, "'");
            sanitized = sanitized.trim();

            sanitized = sanitized.replace(/(^|[\s'-])(\p{L})/gu, function (_, separator, character) {
                return separator + character.toUpperCase();
            });

            if (endsWithSeparator && sanitized) {
                const trailingCharacter = (value || '').slice(-1);
                if (!/[\s'-]$/.test(sanitized)) {
                    sanitized += trailingCharacter;
                }
            }

            return sanitized;
        }

        function getNameValidationError(value) {
            const trimmed = (value || '').trim();
            if (!trimmed) {
                return 'This field is required.';
            }

            if (!/^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u.test(trimmed)) {
                return 'Use letters only. Apostrophes and hyphens are allowed.';
            }

            if (!/^[\p{Lu}]/u.test(trimmed)) {
                return 'Name must start with a capital letter.';
            }

            return '';
        }

        function evaluatePasswordRequirements(password) {
            const unmet = [];
            if (password.length < 8) {
                unmet.push('Use at least 8 characters.');
            }
            if (!/[A-Z]/.test(password)) {
                unmet.push('Add an uppercase letter.');
            }
            if (!/[a-z]/.test(password)) {
                unmet.push('Add a lowercase letter.');
            }
            if (!/\d/.test(password)) {
                unmet.push('Add a number.');
            }
            if (!/[!@#$%^&*]/.test(password)) {
                unmet.push('Add one symbol: ! @ # $ % ^ & *.');
            }
            if (/[^A-Za-z\d!@#$%^&*]/.test(password)) {
                unmet.push('Use only these symbols: ! @ # $ % ^ & *.');
            }

            return {
                isValid: unmet.length === 0,
                unmet: unmet,
            };
        }

        async function checkEmailAvailability(email) {
            const trimmedEmail = (email || '').trim();

            if (!trimmedEmail) {
                return { available: false, message: 'Email is required.' };
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
                return { available: false, message: 'Please enter a valid email address.' };
            }

            const requestId = ++emailRequestCounter;

            try {
                const response = await fetch(emailCheckUrl + '?email=' + encodeURIComponent(trimmedEmail), {
                    headers: { 'Accept': 'application/json' }
                });
                const payload = await response.json();

                if (requestId !== emailRequestCounter) {
                    return { available: false, message: 'Checking email availability...' };
                }

                return {
                    available: Boolean(payload.available),
                    message: payload.message || (payload.available ? 'Email is available.' : 'This email is already in use.')
                };
            } catch (error) {
                return { available: false, message: 'Unable to verify email right now. Please try again.' };
            }
        }

        [
            { input: firstNameInput, bubbleId: 'profFirstNameBubble' },
            { input: lastNameInput, bubbleId: 'profLastNameBubble' }
        ].forEach(function (field) {
            if (!field.input) return;

            function syncNameField(showBubble, isLive = false) {
                field.input.value = sanitizeNameValue(field.input.value, isLive);
                const validationError = getNameValidationError(field.input.value);
                field.input.setCustomValidity(validationError);
                setInputErrorState(field.input, Boolean(validationError));
                showFieldBubble(field.bubbleId, showBubble ? validationError : '');
            }

            field.input.addEventListener('input', function () {
                syncNameField(false, true);
            });

            field.input.addEventListener('blur', function () {
                syncNameField(Boolean(field.input.value.trim()), false);
            });
        });

        if (emailInput) {
            emailInput.addEventListener('input', function () {
                const value = emailInput.value.trim();

                if (emailCheckTimer) {
                    clearTimeout(emailCheckTimer);
                }

                if (!value) {
                    emailInput.setCustomValidity('Email is required.');
                    setInputErrorState(emailInput, false);
                    showFieldBubble('profEmailBubble', '');
                    return;
                }

                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    emailInput.setCustomValidity('Please enter a valid email address.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('profEmailBubble', 'Please enter a valid email address.');
                    return;
                }

                emailInput.setCustomValidity('');
                setInputErrorState(emailInput, false);
                showFieldBubble('profEmailBubble', '');

                emailCheckTimer = setTimeout(async function () {
                    const result = await checkEmailAvailability(value);
                    if (emailInput.value.trim() !== value) {
                        return;
                    }

                    if (!result.available) {
                        emailInput.setCustomValidity(result.message);
                        setInputErrorState(emailInput, true);
                        showFieldBubble('profEmailBubble', result.message);
                    } else {
                        emailInput.setCustomValidity('');
                        setInputErrorState(emailInput, false);
                        showFieldBubble('profEmailBubble', '');
                    }
                }, 350);
            });
        }

        function syncPasswordValidationState() {
            if (!passwordInput || !confirmPasswordInput) {
                return;
            }

            const passwordValidation = evaluatePasswordRequirements(passwordInput.value);
            const hasPasswordValue = passwordInput.value.length > 0;
            const hasConfirmValue = confirmPasswordInput.value.length > 0;
            const passwordsMatch = passwordInput.value === confirmPasswordInput.value;

            if (!hasPasswordValue && !hasConfirmValue) {
                showFieldBubble('profPasswordBubble', '');
                showFieldBubble('profConfirmPasswordBubble', '');
                setInputErrorState(passwordInput, false);
                setInputErrorState(confirmPasswordInput, false);
                return;
            }

            if (!passwordValidation.isValid) {
                showFieldBubble('profPasswordBubble', passwordValidation.unmet);
                setInputErrorState(passwordInput, true);
            } else {
                showFieldBubble('profPasswordBubble', '');
                setInputErrorState(passwordInput, false);
            }

            if (hasConfirmValue && !passwordsMatch) {
                showFieldBubble('profConfirmPasswordBubble', 'Password confirmation does not match.');
                setInputErrorState(confirmPasswordInput, true);
            } else {
                showFieldBubble('profConfirmPasswordBubble', '');
                setInputErrorState(confirmPasswordInput, false);
            }
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', syncPasswordValidationState);
        }

        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', syncPasswordValidationState);
        }

        setupPasswordToggle(togglePasswordButton, passwordInput);
        setupPasswordToggle(toggleConfirmPasswordButton, confirmPasswordInput);

        addProfessorForm.addEventListener('submit', async function (event) {
            if (addProfessorForm.dataset.submitting === 'true') {
                return;
            }

            event.preventDefault();
            let hasError = false;

            [
                { input: firstNameInput, bubbleId: 'profFirstNameBubble' },
                { input: lastNameInput, bubbleId: 'profLastNameBubble' }
            ].forEach(function (field) {
                if (!field.input) return;
                field.input.value = sanitizeNameValue(field.input.value);
                const validationError = getNameValidationError(field.input.value);
                field.input.setCustomValidity(validationError);
                setInputErrorState(field.input, Boolean(validationError));
                showFieldBubble(field.bubbleId, validationError);
                if (validationError) {
                    hasError = true;
                }
            });

            if (emailInput) {
                const value = emailInput.value.trim();
                if (!value) {
                    emailInput.setCustomValidity('Email is required.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('profEmailBubble', 'Email is required.');
                    hasError = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    emailInput.setCustomValidity('Please enter a valid email address.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('profEmailBubble', 'Please enter a valid email address.');
                    hasError = true;
                } else {
                    const result = await checkEmailAvailability(value);
                    if (!result.available) {
                        emailInput.setCustomValidity(result.message);
                        setInputErrorState(emailInput, true);
                        showFieldBubble('profEmailBubble', result.message);
                        hasError = true;
                    } else {
                        emailInput.setCustomValidity('');
                        setInputErrorState(emailInput, false);
                        showFieldBubble('profEmailBubble', '');
                    }
                }
            }

            if (passwordInput) {
                const passwordValidation = evaluatePasswordRequirements(passwordInput.value.trim());
                if (!passwordValidation.isValid) {
                    setInputErrorState(passwordInput, true);
                    showFieldBubble('profPasswordBubble', passwordValidation.unmet);
                    hasError = true;
                } else {
                    setInputErrorState(passwordInput, false);
                    showFieldBubble('profPasswordBubble', '');
                }
            }

            if (confirmPasswordInput && passwordInput) {
                if (passwordInput.value.trim() !== confirmPasswordInput.value.trim()) {
                    setInputErrorState(confirmPasswordInput, true);
                    showFieldBubble('profConfirmPasswordBubble', 'Password confirmation does not match.');
                    hasError = true;
                } else {
                    setInputErrorState(confirmPasswordInput, false);
                    showFieldBubble('profConfirmPasswordBubble', '');
                }
            }

            if (hasError) {
                return;
            }

            addProfessorForm.dataset.submitting = 'true';
            addProfessorForm.submit();
        });

        const addProfModalEl = document.getElementById('addProfessorModal');
        if (addProfModalEl && addProfModalEl.querySelector('.text-danger, [style*="color:var(--red)"]')) {
            new bootstrap.Modal(addProfModalEl).show();
        }
    }

    $('#btnSyncFlss').on('click', function () {
        var $btn = $(this);
        var $icon = $('#flssSyncIcon');

        var alertHelper = window.SyncAlert || {
            confirm: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, icon: 'question', showCancelButton: true, showDenyButton: false }); },
            loading: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, allowOutsideClick: false, showConfirmButton: false, showDenyButton: false }); },
            success: function(o) { return Swal.fire({ title: o.title, icon: 'success', showDenyButton: false }); },
            error: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'error', showDenyButton: false }); },
            notice: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'warning', showDenyButton: false }); }
        };

        alertHelper.confirm({
            system: 'flss',
            title: 'Sync Faculty from FLSS?',
            subtitle: 'Pull latest faculty and professor records from the Faculty Loading System.',
            bullets: [
                'Imports newly appointed faculty & instructors',
                'Updates departmental assignments & contact details',
                'Preserves all existing class records & student evaluations'
            ],
            note: 'Safe Operation: Existing accounts are updated without loss of data.',
            confirmBtnText: 'Yes, Sync Faculty'
        }).then((result) => {
            if (result.isConfirmed) {
                $btn.prop('disabled', true);
                $icon.addClass('fa-spin');

                // Show modern animated loading alert
                alertHelper.loading({
                    system: 'flss',
                    title: 'Syncing Faculty Data...',
                    subtitle: 'Connecting to FLSS Production API & processing records...',
                    cautionText: 'Please do not refresh, close this window, or navigate away while synchronization is in progress.'
                });

                // Prevent accidental page unload during sync
                window.onbeforeunload = function () {
                    return "Faculty sync is currently in progress. Navigating away may interrupt the sync process.";
                };

                $.ajax({
                    url: (window.professorsConfig && window.professorsConfig.syncFacultyUrl) || '/coordinator/sync-faculty',
                    type: "POST",
                    data: {
                        _token: (window.professorsConfig && window.professorsConfig.csrfToken) || ($('meta[name="csrf-token"]').attr('content') || '')
                    },
                    success: function (res) {
                        window.onbeforeunload = null;
                        $btn.prop('disabled', false);
                        $icon.removeClass('fa-spin');

                        if (res.success) {
                            var s = res.summary || {};
                            var hasMissing = Boolean(res.has_missing && res.missing_accounts && res.missing_accounts.length > 0);
                            var missingCount = (res.missing_accounts || []).length;

                            var stats = [
                                { label: 'Newly Created', value: s.created || 0, delta: true, colorClass: 'text-success', iconType: 'success', icon: 'fa-user-plus' },
                                { label: 'Updated Profiles', value: s.updated || 0, colorClass: 'text-primary', iconType: 'primary', icon: 'fa-user-check' },
                                { label: 'Unchanged / Skipped', value: s.skipped || 0, colorClass: 'text-secondary', iconType: 'neutral', icon: 'fa-forward' },
                                { label: 'Not in FLSS', value: missingCount, colorClass: hasMissing ? 'text-warning' : 'text-secondary', iconType: hasMissing ? 'warning' : 'neutral', icon: 'fa-exclamation-triangle' }
                            ];

                            alertHelper.success({
                                system: 'flss',
                                title: 'FLSS Sync Complete!',
                                subtitle: 'Faculty records have been successfully synchronized with FLSS.',
                                stats: stats,
                                missingNotice: hasMissing ? {
                                    count: missingCount,
                                    text: 'Local faculty accounts not found in FLSS. Review and manage them.'
                                } : null,
                                confirmBtnText: hasMissing ? 'Review Missing Faculty' : 'Done & Refresh'
                            }).then(function () {
                                if (hasMissing) {
                                    openPruneMissingFacultyModal(res.missing_accounts);
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            alertHelper.notice({
                                system: 'flss',
                                title: 'Sync Notice',
                                message: res.message || 'Unable to sync faculty records from FLSS.'
                            });
                        }
                    },
                    error: function (err) {
                        window.onbeforeunload = null;
                        $btn.prop('disabled', false);
                        $icon.removeClass('fa-spin');
                        var errMsg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error communicating with FLSS system.';
                        alertHelper.error({
                            title: 'FLSS Sync Failed',
                            message: errMsg
                        });
                    }
                });
            }
        });
    });
});
$(document).ready(function () {
    if ($.fn.select2) {
        $('#targetUserId').select2({
            placeholder: '-- Choose Active Faculty Member --',
            allowClear: true,
            dropdownParent: $('#transferCoordinatorModal'),
            width: '100%'
        });
    }
});

$('#transferCoordinatorForm').on('submit', function (e) {
    e.preventDefault();
    var targetId = $('#targetUserId').val();
    var targetName = $('#targetUserId option:selected').text();

    if (!targetId) {
        Swal.fire('Selection Required', 'Please select a faculty member to receive the Coordinator designation.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Confirm Role Transfer?',
        text: 'Are you sure you want to transfer your Coordinator designation to ' + targetName + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Transfer Designation'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#btnConfirmTransfer').prop('disabled', true).text('Transferring...');

            $.ajax({
                url: (window.professorsConfig && window.professorsConfig.transferRoleUrl) || '/coordinator/transfer-coordinator',
                type: "POST",
                data: {
                    _token: (window.professorsConfig && window.professorsConfig.csrfToken) || (meta[name = "csrf-token"].attr('content') || ''),
                    target_user_id: targetId
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            title: 'Role Transferred!',
                            text: res.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.href = res.redirect || ((window.professorsConfig && window.professorsConfig.professorHomeUrl) || '/professor_home');
                        });
                    } else {
                        $('#btnConfirmTransfer').prop('disabled', false).text('Confirm Transfer');
                        Swal.fire('Transfer Error', res.message || 'Unable to transfer role.', 'error');
                    }
                },
                error: function (err) {
                    $('#btnConfirmTransfer').prop('disabled', false).text('Confirm Transfer');
                    var errMsg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error transferring role.';
                    Swal.fire('Transfer Failed', errMsg, 'error');
                }
            });
        }
    });
});
// Prune Missing Faculty Modal Handlers
var missingFacultyCache = [];

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

window.openPruneMissingFacultyModal = function (missingAccounts) {
    missingFacultyCache = missingAccounts || [];
    var $tbody = $('#pruneFacultyTableBody');
    $tbody.empty();

    if (!missingAccounts || missingAccounts.length === 0) return;

    missingAccounts.forEach(function (acc) {
        var badgeClass = acc.is_manually_added ? 'bg-primary' : 'bg-secondary';
        var roleBadgeClass = acc.role_id == 1 ? 'bg-purple text-white' : 'bg-info text-dark';

        var warningHtml = '';
        if (acc.student_count > 0 || acc.class_count > 0) {
            warningHtml = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle me-1"></i> ' +
                acc.student_count + ' students, ' + acc.class_count + ' classes</span>';
        } else {
            warningHtml = '<span class="text-muted" style="font-size: 12px;">None</span>';
        }

        var trHtml = '<tr class="prune-account-row" data-search="' + (acc.full_name + ' ' + acc.email).toLowerCase() + '">' +
            '<td class="text-center">' +
            '<input type="checkbox" class="form-check-input prune-acc-checkbox" value="' + acc.id + '" checked style="width: 18px; height: 18px; cursor: pointer;">' +
            '</td>' +
            '<td>' +
            '<div class="font-weight-bold text-dark">' + escapeHtml(acc.full_name) + '</div>' +
            '<div class="text-muted" style="font-size: 12px;">' + escapeHtml(acc.email) + '</div>' +
            '</td>' +
            '<td><span class="badge ' + roleBadgeClass + '" style="font-size: 11px;">' + escapeHtml(acc.role_label) + '</span></td>' +
            '<td><span class="badge ' + badgeClass + '" style="font-size: 11px;">' + escapeHtml(acc.source_label) + '</span></td>' +
            '<td>' + warningHtml + '</td>' +
            '</tr>';

        $tbody.append(trHtml);
    });

    updatePruneSelectionStats();
    $('#pruneFacultySearchInput').val('');
    $('#selectAllPruneAccounts').prop('checked', true);
    $('#pruneMissingFacultyModal').modal('show');
};

$('#pruneFacultySearchInput').on('keyup input', function () {
    var query = $(this).val().toLowerCase().trim();
    $('#pruneFacultyTableBody tr.prune-account-row').each(function () {
        var searchData = $(this).attr('data-search') || '';
        if (!query || searchData.indexOf(query) !== -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$(document).on('keyup input', '#pruneFacultySearchInput', function () {
    var query = $(this).val().toLowerCase().trim();
    $('#pruneFacultyTableBody tr.prune-account-row').each(function () {
        var searchData = $(this).attr('data-search') || '';
        if (!query || searchData.indexOf(query) !== -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$(document).on('change', '#selectAllPruneAccounts', function () {
    var isChecked = $(this).is(':checked');
    $('#pruneFacultyTableBody tr.prune-account-row:visible .prune-acc-checkbox').prop('checked', isChecked);
    updatePruneSelectionStats();
});

$(document).on('change', '.prune-acc-checkbox', function () {
    updatePruneSelectionStats();
});

function updatePruneSelectionStats() {
    var totalVisible = $('#pruneFacultyTableBody .prune-acc-checkbox').length;
    var checkedCount = $('#pruneFacultyTableBody .prune-acc-checkbox:checked').length;

    $('#pruneSelectionCountText').text('Selected ' + checkedCount + ' of ' + totalVisible + ' account(s) for removal');
    $('#pruneSelectedCountBadge').text(checkedCount);

    if (checkedCount === 0) {
        $('#btnConfirmPruneFaculty').prop('disabled', true);
    } else {
        $('#btnConfirmPruneFaculty').prop('disabled', false);
    }
}

$(document).on('click', '#btnConfirmPruneFaculty', function (e) {
    e.preventDefault();
    var selectedIds = [];
    $('#pruneFacultyTableBody .prune-acc-checkbox:checked').each(function () {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        Swal.fire('No Accounts Selected', 'Please select at least one account to remove, or click Skip.', 'info');
        return;
    }

    Swal.fire({
        title: 'Remove ' + selectedIds.length + ' Faculty Account(s)?',
        text: 'This action will remove the selected missing faculty account(s) from InternConnect.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Remove Selected'
    }).then(function (result) {
        if (result.isConfirmed) {
            $('#btnConfirmPruneFaculty').prop('disabled', true).text('Removing...');

            $.ajax({
                url: (window.professorsConfig && window.professorsConfig.pruneMissingFacultyUrl) || '/coordinator/prune-missing-faculty',
                type: "POST",
                data: {
                    _token: (window.professorsConfig && window.professorsConfig.csrfToken) || (meta[name = "csrf-token"].attr('content') || ''),
                    selected_user_ids: selectedIds
                },
                success: function (res) {
                    $('#pruneMissingFacultyModal').modal('hide');
                    if (res.success) {
                        Swal.fire({
                            title: 'Faculty Removed!',
                            text: res.message,
                            icon: 'success'
                        }).then(function () {
                            location.reload();
                        });
                    } else {
                        $('#btnConfirmPruneFaculty').prop('disabled', false).html('<i class="fas fa-trash-alt me-1"></i> Confirm & Remove Selected (' + selectedIds.length + ')');
                        Swal.fire('Error', res.message || 'Failed to remove faculty accounts.', 'error');
                    }
                },
                error: function (err) {
                    $('#btnConfirmPruneFaculty').prop('disabled', false).html('<i class="fas fa-trash-alt me-1"></i> Confirm & Remove Selected (' + selectedIds.length + ')');
                    var errMsg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error removing accounts.';
                    Swal.fire('Error', errMsg, 'error');
                }
            });
        }
    });
});
