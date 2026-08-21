/* ==========================================================================
   Coordinator Account Info Page Scripts
   Extracted from ojtCoordinator/accountinfo.blade.php
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

// Password toggles
document.addEventListener('DOMContentLoaded', function () {
    function setupToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        if (!toggle || !input) return;
        toggle.addEventListener('click', function () {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    setupToggle('toggleCurrent', 'current_password');
    setupToggle('toggleNew', 'new_password');
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
                'X-CSRF-TOKEN': (window.accountInfoConfig && window.accountInfoConfig.csrfToken) || (meta[name = "csrf-token"].attr('content') || ''),
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

    // Auto-open password modal if there are validation errors
    const changePasswordModalEl = document.getElementById('changePasswordModal');
    if (changePasswordModalEl && changePasswordModalEl.querySelector('.text-danger, [style*="color:var(--red)"]')) {
        new bootstrap.Modal(changePasswordModalEl).show();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const profileForm = document.getElementById('coordinatorProfileForm');
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
    let emailCheckTimer = null;
    let emailRequestCounter = 0;

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
            const response = await fetch(emailCheckUrl + '?email=' + encodeURIComponent(trimmedEmail) + '&ignore_id=' + encodeURIComponent(currentUserId), {
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

    if (emailInput) {
        emailInput.addEventListener('input', function () {
            const value = emailInput.value.trim();

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
});
