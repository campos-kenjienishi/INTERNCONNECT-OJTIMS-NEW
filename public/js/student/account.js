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

    // Password toggles
    function setupToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input  = document.getElementById(inputId);
        if (!toggle || !input) return;

        toggle.addEventListener('click', function () {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupToggle('toggleCurrent', 'current_password');
        setupToggle('toggleNew',     'new_password');
        setupToggle('toggleConfirm', 'confirm_password');

        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const updatePasswordButton = document.getElementById('updatePasswordButton');
        const passwordForm = document.querySelector('#changePasswordModal form');
        const currentPasswordBubble = document.getElementById('currentPasswordBubble');
        const newPasswordBubble = document.getElementById('newPasswordBubble');
        const confirmPasswordBubble = document.getElementById('confirmPasswordBubble');
        const verifyCurrentUrl = passwordForm ? passwordForm.dataset.verifyCurrentUrl : '';
        let currentPasswordState = 'idle';
        let verifyCurrentPasswordTimer = null;
        let verifyCurrentPasswordSequence = 0;

        function isNewPasswordValid(value) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/.test(value);
        }

        function getNewPasswordUnmetRules(value) {
            const unmetRules = [];

            if (value.length < 8) {
                unmetRules.push('Use at least 8 characters.');
            }
            if (!/[A-Z]/.test(value)) {
                unmetRules.push('Add an uppercase letter.');
            }
            if (!/[a-z]/.test(value)) {
                unmetRules.push('Add a lowercase letter.');
            }
            if (!/\d/.test(value)) {
                unmetRules.push('Add a number.');
            }
            if (!/[!@#$%^&*]/.test(value)) {
                unmetRules.push('Add one symbol: ! @ # $ % ^ & *.');
            }
            if (/[^A-Za-z\d!@#$%^&*]/.test(value)) {
                unmetRules.push('Use only these symbols: ! @ # $ % ^ & *.');
            }

            return unmetRules;
        }

        function showBubble(bubble, messages) {
            if (!bubble) {
                return;
            }

            if (!messages.length) {
                bubble.innerHTML = '';
                bubble.classList.remove('active');
                return;
            }

            bubble.innerHTML = messages.map(function (message) {
                return '<div>' + message + '</div>';
            }).join('');
            bubble.classList.add('active');
        }

        function verifyCurrentPassword() {
            if (!currentPasswordInput || !verifyCurrentUrl) {
                return;
            }

            const currentPassword = currentPasswordInput.value.trim();

            if (!currentPassword.length) {
                currentPasswordState = 'idle';
                showBubble(currentPasswordBubble, []);
                syncPasswordModalState();
                return;
            }

            currentPasswordState = 'checking';
            syncPasswordModalState();

            const requestSequence = ++verifyCurrentPasswordSequence;

            fetch(verifyCurrentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''),
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                }),
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Verification failed.');
                }

                return response.json();
            })
            .then(function (data) {
                if (requestSequence !== verifyCurrentPasswordSequence) {
                    return;
                }

                currentPasswordState = data.valid ? 'valid' : 'invalid';
                showBubble(currentPasswordBubble, data.valid ? [] : [data.message || 'Current password is incorrect.']);
                syncPasswordModalState();
            })
            .catch(function () {
                if (requestSequence !== verifyCurrentPasswordSequence) {
                    return;
                }

                currentPasswordState = 'idle';
                showBubble(currentPasswordBubble, ['We could not verify the current password right now.']);
                syncPasswordModalState();
            });
        }

        function queueCurrentPasswordVerification() {
            if (verifyCurrentPasswordTimer) {
                clearTimeout(verifyCurrentPasswordTimer);
            }

            verifyCurrentPasswordTimer = setTimeout(verifyCurrentPassword, 350);
        }

        function syncPasswordModalState() {
            if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput || !updatePasswordButton) {
                return;
            }

            const currentPassword = currentPasswordInput.value.trim();
            const newPassword = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            const isCurrentPasswordPresent = currentPassword.length > 0;
            const isCurrentPasswordValid = currentPasswordState === 'valid';
            const unmetRules = getNewPasswordUnmetRules(newPassword);
            const hasValidNewPassword = newPassword.length > 0 && unmetRules.length === 0 && isNewPasswordValid(newPassword);
            const isConfirmPasswordMatching = newPassword === confirmPassword && confirmPassword.length > 0;
            const canSubmit = isCurrentPasswordValid && hasValidNewPassword && isConfirmPasswordMatching;

            updatePasswordButton.disabled = !canSubmit;

            if (!isCurrentPasswordPresent) {
                showBubble(currentPasswordBubble, []);
            } else if (currentPasswordState === 'checking') {
                showBubble(currentPasswordBubble, ['Checking current password...']);
            }

            showBubble(newPasswordBubble, newPassword.length > 0 && !hasValidNewPassword ? unmetRules : []);
            showBubble(confirmPasswordBubble, confirmPassword.length > 0 && !isConfirmPasswordMatching ? ['Confirmation password must match the new password.'] : []);
        }

        [currentPasswordInput, newPasswordInput, confirmPasswordInput].forEach(function (input) {
            if (!input) return;
            input.addEventListener('input', syncPasswordModalState);
        });

        if (currentPasswordInput) {
            currentPasswordInput.addEventListener('input', function () {
                currentPasswordState = currentPasswordInput.value.trim().length ? 'checking' : 'idle';
                queueCurrentPasswordVerification();
            });
            currentPasswordInput.addEventListener('blur', verifyCurrentPassword);
        }

        syncPasswordModalState();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const profileForm = document.getElementById('studentProfileForm');
        if (!profileForm) {
            return;
        }

        const emailCheckUrl = profileForm.dataset.emailCheckUrl || '';
        const currentUserId = profileForm.dataset.currentUserId || '';
        const nameFieldConfig = [
            { id: 'first_name', bubbleId: 'firstNameBubble', optional: false },
            { id: 'middle_name', bubbleId: 'middleNameBubble', optional: true },
            { id: 'last_name', bubbleId: 'lastNameBubble', optional: false },
        ];
        const emailInput = document.getElementById('email');
        const studentNumInput = document.getElementById('studentNum');
        const yearAndSectionInput = document.getElementById('year_and_section');
        const startYearSelect = document.getElementById('academic_year_start');
        const endYearSelect = document.getElementById('academic_year_end');
        const initialEndYear = window.studentAccountConfig?.initialEndYear || (endYearSelect ? endYearSelect.value : '');
        let emailCheckTimer = null;
        let emailRequestCounter = 0;
        let emailState = 'idle';

        function setInputErrorState(input, hasError) {
            if (!input) {
                return;
            }

            input.style.borderColor = hasError ? '#dc2626' : '';
            input.style.boxShadow = hasError ? '0 0 0 3px rgba(220,38,38,0.1)' : '';
        }

        function showFieldBubble(bubbleId, message) {
            const bubble = document.getElementById(bubbleId);
            if (!bubble) {
                return;
            }

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

        function getNameValidationError(value, isOptional) {
            const trimmed = (value || '').trim();
            if (!trimmed) {
                return isOptional ? '' : 'This field is required.';
            }

            if (!/^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u.test(trimmed)) {
                return 'Use letters only. Apostrophes and hyphens are allowed.';
            }

            if (!/^[\p{Lu}]/u.test(trimmed)) {
                return 'Name must start with a capital letter.';
            }

            return '';
        }

        function getStudentNumberValidationError(value) {
            const trimmed = (value || '').trim().toUpperCase();
            if (!trimmed) {
                return 'Student number is required.';
            }

            if (!/^\d{4}-\d{5}-TG-[01]$/.test(trimmed)) {
                return 'Use this format: YYYY-12345-TG-0 or YYYY-12345-TG-1.';
            }

            return '';
        }

        function sanitizeYearAndSectionValue(value) {
            let sanitized = (value || '').replace(/[^\d-]/g, '');
            sanitized = sanitized.replace(/-{2,}/g, '-');

            const firstHyphenIndex = sanitized.indexOf('-');
            if (firstHyphenIndex !== -1) {
                const before = sanitized.slice(0, firstHyphenIndex + 1);
                const after = sanitized.slice(firstHyphenIndex + 1).replace(/-/g, '');
                sanitized = before + after;
            }

            return sanitized;
        }

        function getYearAndSectionValidationError(value) {
            const trimmed = (value || '').trim();
            if (!trimmed) {
                return 'Year and section is required.';
            }

            if (!/^\d+-\d+$/.test(trimmed)) {
                return 'Use this format: 4-1.';
            }

            return '';
        }

        function updateAcademicYearEndOptions() {
            if (!startYearSelect || !endYearSelect) {
                return;
            }

            const selectedStartYear = parseInt(startYearSelect.value, 10);
            endYearSelect.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'End Year';
            endYearSelect.appendChild(defaultOption);

            if (!isNaN(selectedStartYear)) {
                const nextYear = selectedStartYear + 1;
                const option = document.createElement('option');
                option.value = String(nextYear);
                option.textContent = String(nextYear);
                endYearSelect.appendChild(option);

                if (initialEndYear !== '' && initialEndYear === String(nextYear)) {
                    endYearSelect.value = initialEndYear;
                } else {
                    endYearSelect.value = String(nextYear);
                }
            } else {
                endYearSelect.value = '';
            }
        }

        async function checkEmailAvailability(email) {
            const trimmedEmail = (email || '').trim();

            if (!trimmedEmail) {
                emailState = 'idle';
                return { available: false, message: 'Email is required.' };
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
                emailState = 'invalid';
                return { available: false, message: 'Please enter a valid email address.' };
            }

            emailState = 'checking';
            const requestId = ++emailRequestCounter;

            try {
                const response = await fetch(emailCheckUrl + '?email=' + encodeURIComponent(trimmedEmail) + '&ignore_id=' + encodeURIComponent(currentUserId), {
                    headers: { 'Accept': 'application/json' }
                });
                const payload = await response.json();

                if (requestId !== emailRequestCounter) {
                    return { available: false, message: 'Checking email availability...' };
                }

                emailState = payload.available ? 'available' : 'taken';
                return {
                    available: Boolean(payload.available),
                    message: payload.message || (payload.available ? 'Email is available.' : 'This email is already in use.')
                };
            } catch (error) {
                emailState = 'error';
                return { available: false, message: 'Unable to verify email right now. Please try again.' };
            }
        }

        nameFieldConfig.forEach(function (field) {
            const input = document.getElementById(field.id);
            if (!input) return;

            function syncNameField(showBubble, isLive = false) {
                input.value = sanitizeNameValue(input.value, isLive);
                const validationError = getNameValidationError(input.value, field.optional);
                input.setCustomValidity(validationError);
                setInputErrorState(input, Boolean(validationError));
                showFieldBubble(field.bubbleId, showBubble ? validationError : '');
            }

            input.addEventListener('input', function () {
                syncNameField(false, true);
            });

            input.addEventListener('blur', function () {
                syncNameField(Boolean(input.value.trim()), false);
            });
        });

        if (studentNumInput) {
            studentNumInput.addEventListener('input', function () {
                const validationError = getStudentNumberValidationError(studentNumInput.value);
                studentNumInput.setCustomValidity(validationError);
                setInputErrorState(studentNumInput, Boolean(validationError && studentNumInput.value.trim()));
                showFieldBubble('studentNumBubble', validationError && studentNumInput.value.trim() ? validationError : '');
            });

            studentNumInput.addEventListener('blur', function () {
                const validationError = getStudentNumberValidationError(studentNumInput.value);
                studentNumInput.setCustomValidity(validationError);
                showFieldBubble('studentNumBubble', validationError && studentNumInput.value.trim() ? validationError : '');
            });
        }

        if (yearAndSectionInput) {
            yearAndSectionInput.addEventListener('input', function () {
                yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
                const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
                yearAndSectionInput.setCustomValidity(validationError);
                setInputErrorState(yearAndSectionInput, Boolean(validationError && yearAndSectionInput.value.trim()));
                showFieldBubble('yearSectionBubble', validationError && yearAndSectionInput.value.trim() ? validationError : '');
            });

            yearAndSectionInput.addEventListener('blur', function () {
                yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
                const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
                yearAndSectionInput.setCustomValidity(validationError);
                showFieldBubble('yearSectionBubble', validationError && yearAndSectionInput.value.trim() ? validationError : '');
            });
        }

        if (startYearSelect && endYearSelect) {
            updateAcademicYearEndOptions();
            startYearSelect.addEventListener('change', updateAcademicYearEndOptions);
        }

        if (emailInput) {
            emailInput.addEventListener('input', function () {
                const value = emailInput.value.trim();
                emailState = 'idle';

                if (emailCheckTimer) {
                    clearTimeout(emailCheckTimer);
                }

                if (!value) {
                    emailInput.setCustomValidity('Email is required.');
                    setInputErrorState(emailInput, false);
                    showFieldBubble('emailBubble', '');
                    return;
                }

                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    emailInput.setCustomValidity('Please enter a valid email address.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('emailBubble', 'Please enter a valid email address.');
                    return;
                }

                emailInput.setCustomValidity('');
                setInputErrorState(emailInput, false);
                showFieldBubble('emailBubble', '');

                emailCheckTimer = setTimeout(async function () {
                    const result = await checkEmailAvailability(value);
                    if (emailInput.value.trim() !== value) {
                        return;
                    }

                    if (!result.available) {
                        emailInput.setCustomValidity(result.message);
                        setInputErrorState(emailInput, true);
                        showFieldBubble('emailBubble', result.message);
                    } else {
                        emailInput.setCustomValidity('');
                        setInputErrorState(emailInput, false);
                        showFieldBubble('emailBubble', '');
                    }
                }, 350);
            });
        }

        profileForm.addEventListener('submit', async function (event) {
            if (profileForm.dataset.submitting === 'true') {
                return;
            }

            event.preventDefault();
            let hasError = false;

            nameFieldConfig.forEach(function (field) {
                const input = document.getElementById(field.id);
                if (!input) return;

                input.value = sanitizeNameValue(input.value);
                const validationError = getNameValidationError(input.value, field.optional);
                input.setCustomValidity(validationError);
                setInputErrorState(input, Boolean(validationError));
                showFieldBubble(field.bubbleId, validationError);
                if (validationError) {
                    hasError = true;
                }
            });

            if (studentNumInput) {
                const validationError = getStudentNumberValidationError(studentNumInput.value);
                studentNumInput.setCustomValidity(validationError);
                setInputErrorState(studentNumInput, Boolean(validationError));
                showFieldBubble('studentNumBubble', validationError);
                if (validationError) {
                    hasError = true;
                }
            }

            if (yearAndSectionInput) {
                yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
                const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
                yearAndSectionInput.setCustomValidity(validationError);
                setInputErrorState(yearAndSectionInput, Boolean(validationError));
                showFieldBubble('yearSectionBubble', validationError);
                if (validationError) {
                    hasError = true;
                }
            }

            if (emailInput) {
                const value = emailInput.value.trim();

                if (!value) {
                    emailInput.setCustomValidity('Email is required.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('emailBubble', 'Email is required.');
                    hasError = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    emailInput.setCustomValidity('Please enter a valid email address.');
                    setInputErrorState(emailInput, true);
                    showFieldBubble('emailBubble', 'Please enter a valid email address.');
                    hasError = true;
                } else {
                    const result = await checkEmailAvailability(value);
                    if (!result.available) {
                        emailInput.setCustomValidity(result.message);
                        setInputErrorState(emailInput, true);
                        showFieldBubble('emailBubble', result.message);
                        hasError = true;
                    } else {
                        emailInput.setCustomValidity('');
                        setInputErrorState(emailInput, false);
                        showFieldBubble('emailBubble', '');
                    }
                }
            }

            if (hasError) {
                return;
            }

            profileForm.dataset.submitting = 'true';
            profileForm.submit();
        });

        var alertHelper = window.SyncAlert || {
            confirm: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, icon: 'question', showCancelButton: true, showDenyButton: false }); },
            loading: function(o) { return Swal.fire({ title: o.title, text: o.subtitle, allowOutsideClick: false, showConfirmButton: false, showDenyButton: false }); },
            success: function(o) { return Swal.fire({ title: o.title, icon: 'success', showDenyButton: false }); },
            error: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'error', showDenyButton: false }); },
            notice: function(o) { return Swal.fire({ title: o.title, text: o.message, icon: 'warning', showDenyButton: false }); }
        };

        // Sync from GuiSIS Handler
        $('#btnSyncGuidance').on('click', function(e) {
            e.preventDefault();

            alertHelper.confirm({
                system: 'guisis',
                title: 'Sync Profile from GuiSIS?',
                subtitle: 'Automatically fetch your verified academic & demographic details from Guidance.',
                bullets: [
                    'Updates Student Number, Course/Program, and Year & Section',
                    'Synchronizes Birthdate, Contact Number, and Home Address',
                    'Keeps your student internship profile 100% verified and up-to-date'
                ],
                note: 'Safe Operation: Your existing application files and submissions remain safe.',
                confirmBtnText: 'Yes, Sync My Profile'
            }).then((result) => {
                if (result.isConfirmed) {
                    alertHelper.loading({
                        system: 'guisis',
                        title: 'Connecting to GuiSIS...',
                        subtitle: 'Fetching your official student record from GuiSIS...',
                        cautionText: 'Please keep this tab open while your profile details are updating.'
                    });

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: (window.studentAccountConfig?.syncGuidanceUrl || '/student/sync-guidance'),
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                var d = res.data || {};
                                var syncedList = [];

                                if (d.full_name) syncedList.push({ label: 'Full Name', value: d.full_name, icon: 'fa-user' });
                                if (d.studentNum) syncedList.push({ label: 'Student Number', value: d.studentNum, icon: 'fa-id-card' });
                                if (d.course) syncedList.push({ label: 'Program / Course', value: d.course, icon: 'fa-graduation-cap' });
                                if (d.year_and_section) syncedList.push({ label: 'Year & Section', value: d.year_and_section, icon: 'fa-layer-group' });
                                if (d.contact_number) syncedList.push({ label: 'Contact Number', value: d.contact_number, icon: 'fa-phone' });
                                if (d.date_of_birth) syncedList.push({ label: 'Date of Birth', value: d.date_of_birth, icon: 'fa-calendar-alt' });
                                if (d.address) syncedList.push({ label: 'Home Address', value: d.address, icon: 'fa-map-marker-alt' });

                                if (syncedList.length === 0 && d.synced_fields) {
                                    syncedList = d.synced_fields;
                                }

                                alertHelper.success({
                                    system: 'guisis',
                                    title: 'Profile Synced!',
                                    subtitle: res.message || 'Your verified student details were synchronized from GuiSIS.',
                                    stats: [
                                        { label: 'Fields Synced', value: syncedList.length || 1, delta: true, colorClass: 'text-success', iconType: 'success', icon: 'fa-check-circle' },
                                        { label: 'Data Source', value: 'GuiSIS', colorClass: 'text-primary', iconType: 'primary', icon: 'fa-university' }
                                    ],
                                    syncedDetails: syncedList,
                                    detailsTitle: 'Information Synced from Guidance',
                                    confirmBtnText: 'Done & Refresh'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                alertHelper.notice({
                                    system: 'guisis',
                                    title: 'Sync Notice',
                                    message: res.message || 'Unable to sync profile details from Guidance System.'
                                });
                            }
                        },
                        error: function(xhr) {
                            var errMsg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Error connecting to Guidance System (GuiSIS). Please try again.';
                            alertHelper.error({
                                title: 'GuiSIS Sync Failed',
                                message: errMsg
                            });
                        }
                    });
                }
            });
        });
    });

