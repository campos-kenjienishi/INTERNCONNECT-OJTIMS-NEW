/* ==========================================================================
   Auth Registration Page Scripts
   Extracted from auth/registration.blade.php
   ========================================================================== */

let emailAvailabilityStatus = 'idle';
let emailCheckRequestCounter = 0;

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
        unmet,
    };
}

function showFieldBubble(bubbleId, message) {
    const bubble = document.getElementById(bubbleId);
    if (!bubble) {
        return;
    }

    if (!message) {
        bubble.textContent = '';
        bubble.classList.remove('active');
        return;
    }

    const messages = Array.isArray(message) ? message : [message];
    bubble.innerHTML = messages.map(item => `<div>${item}</div>`).join('');
    bubble.classList.add('active');
}

function hideFieldBubble(bubbleId) {
    showFieldBubble(bubbleId, '');
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

async function goToStep2() {
    if (!document.getElementById('step1') || !document.getElementById('step2')) {
        return;
    }

    const firstName = document.getElementById('first_name').value.trim();
    const middleName = document.getElementById('middle_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('reg_email').value.trim();
    const studentNum = document.getElementById('studentNum').value.trim();
    const password = document.getElementById('reg_password').value.trim();
    const confirmPassword = document.getElementById('reg_confirm_password').value.trim();

    const requiredFields = [
        { id: 'first_name', val: firstName },
        { id: 'last_name', val: lastName },
        { id: 'reg_email', val: email },
        { id: 'studentNum', val: studentNum },
        { id: 'reg_password', val: password },
        { id: 'reg_confirm_password', val: confirmPassword },
    ];

    let hasError = false;
    requiredFields.forEach(f => {
        const el = document.getElementById(f.id);
        if (!f.val) {
            hasError = true;
            el.style.borderColor = '#dc2626';
            el.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
        } else {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        }
    });

    const passwordValidation = evaluatePasswordRequirements(password);
    const passwordInput = document.getElementById('reg_password');
    const confirmPasswordInput = document.getElementById('reg_confirm_password');
    const emailInput = document.getElementById('reg_email');
    const studentNumInput = document.getElementById('studentNum');
    const studentNumValidationError = getStudentNumberValidationError(studentNum);

    if (!passwordValidation.isValid && passwordInput) {
        hasError = true;
        passwordInput.style.borderColor = '#dc2626';
        passwordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
        showFieldBubble('passwordBubble', passwordValidation.unmet);
    } else {
        hideFieldBubble('passwordBubble');
    }

    if (confirmPassword && password !== confirmPassword && confirmPasswordInput) {
        hasError = true;
        confirmPasswordInput.style.borderColor = '#dc2626';
        confirmPasswordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
        showFieldBubble('confirmPasswordBubble', 'Password confirmation does not match.');
    } else {
        hideFieldBubble('confirmPasswordBubble');
    }

    if (email && emailInput) {
        const emailCheck = await checkEmailAvailability(email, true);
        if (!emailCheck.available) {
            hasError = true;
            emailInput.style.borderColor = '#dc2626';
            emailInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            showFieldBubble('emailBubble', emailCheck.message);
        } else {
            hideFieldBubble('emailBubble');
        }
    }

    if (studentNumValidationError && studentNumInput) {
        hasError = true;
        studentNumInput.style.borderColor = '#dc2626';
        studentNumInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
        showFieldBubble('studentNumBubble', studentNumValidationError);
    } else {
        hideFieldBubble('studentNumBubble');
    }

    if (hasError) return;

    // Switch steps
    const step2 = document.getElementById('step2');
    step2.classList.remove('going-back');
    document.getElementById('step1').classList.remove('active');
    step2.classList.add('active');

    // Update indicators
    const dot1 = document.getElementById('dot1');
    dot1.classList.remove('active');
    dot1.classList.add('done');
    dot1.innerHTML = '<i class="fa fa-check" style="font-size:12px;"></i>';
    document.getElementById('dot2').classList.add('active');
    document.getElementById('line1').classList.add('done');
    document.getElementById('label1').classList.remove('active');
    document.getElementById('label1').classList.add('done');
    document.getElementById('label2').classList.add('active');

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToStep1() {
    if (!document.getElementById('step1') || !document.getElementById('step2')) {
        return;
    }

    const step2 = document.getElementById('step2');
    step2.classList.add('going-back');
    step2.classList.remove('active');
    document.getElementById('step1').classList.add('active');

    const dot1 = document.getElementById('dot1');
    dot1.classList.add('active');
    dot1.classList.remove('done');
    dot1.innerHTML = '1';
    document.getElementById('dot2').classList.remove('active');
    document.getElementById('line1').classList.remove('done');
    document.getElementById('label1').classList.add('active');
    document.getElementById('label1').classList.remove('done');
    document.getElementById('label2').classList.remove('active');

    window.scrollTo({ top: 0, behavior: 'smooth' });
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

function getNameValidationError(value, isOptional = false) {
    const trimmed = (value || '').trim();
    if (!trimmed) {
        return isOptional ? '' : 'This field is required.';
    }

    if (!/^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u.test(trimmed)) {
        return "Use letters only. Apostrophes and hyphens are allowed.";
    }

    if (!/^[\p{Lu}]/u.test(trimmed)) {
        return 'Name must start with a capital letter.';
    }

    return '';
}

function normalizeNameField(fieldId) {
    const input = document.getElementById(fieldId);
    if (!input) return;

    input.value = sanitizeNameValue(input.value);
}

function validateNameFields() {
    const fieldIds = ['first_name', 'middle_name', 'last_name'];

    for (const fieldId of fieldIds) {
        const input = document.getElementById(fieldId);
        if (!input) continue;

        const value = input.value.trim();
        input.setCustomValidity('');
        const isOptional = fieldId === 'middle_name';
        const validationError = getNameValidationError(value, isOptional);

        if (validationError) {
            input.setCustomValidity(validationError);
            input.reportValidity();
            input.style.borderColor = '#dc2626';
            input.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            return false;
        }

        input.style.borderColor = '';
        input.style.boxShadow = '';
    }

    return true;
}

async function checkEmailAvailability(email, forceCheck = false) {
    const trimmedEmail = (email || '').trim();

    if (!trimmedEmail) {
        emailAvailabilityStatus = 'idle';
        return { available: false, message: 'Email is required.' };
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(trimmedEmail)) {
        emailAvailabilityStatus = 'invalid';
        return { available: false, message: 'Please enter a valid email address.' };
    }

    if (!forceCheck && emailAvailabilityStatus === 'checking') {
        return { available: false, message: 'Checking email availability...' };
    }

    emailAvailabilityStatus = 'checking';
    const requestId = ++emailCheckRequestCounter;

    try {
        const response = await fetch(`/check-email-availability?email=${encodeURIComponent(trimmedEmail)}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const payload = await response.json();

        if (requestId !== emailCheckRequestCounter) {
            return { available: false, message: 'Checking email availability...' };
        }

        emailAvailabilityStatus = payload.available ? 'available' : 'taken';
        return {
            available: Boolean(payload.available),
            message: payload.message || (payload.available ? 'Email is available.' : 'This email is already in use.')
        };
    } catch (error) {
        emailAvailabilityStatus = 'error';
        return { available: false, message: 'Unable to verify email right now. Please try again.' };
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const regForm = document.getElementById('regForm');
    if (!regForm) {
        return;
    }

    const emailInput = document.getElementById('reg_email');
    const studentNumInput = document.getElementById('studentNum');
    const yearAndSectionInput = document.getElementById('year_and_section');
    const toggleRegPassword = document.getElementById('toggleRegPassword');
    const toggleRegConfirmPassword = document.getElementById('toggleRegConfirmPassword');
    const passwordInput = document.getElementById('reg_password');
    const confirmPasswordInput = document.getElementById('reg_confirm_password');
    if (toggleRegPassword) {
        toggleRegPassword.addEventListener('click', function () {
            if (!passwordInput) {
                return;
            }

            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (toggleRegConfirmPassword) {
        toggleRegConfirmPassword.addEventListener('click', function () {
            if (!confirmPasswordInput) {
                return;
            }

            confirmPasswordInput.type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
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
            hideFieldBubble('passwordBubble');
            hideFieldBubble('confirmPasswordBubble');
            passwordInput.style.borderColor = '';
            passwordInput.style.boxShadow = '';
            confirmPasswordInput.style.borderColor = '';
            confirmPasswordInput.style.boxShadow = '';
            return;
        }

        if (!passwordValidation.isValid) {
            showFieldBubble('passwordBubble', passwordValidation.unmet);
            passwordInput.style.borderColor = '#dc2626';
            passwordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
        } else {
            hideFieldBubble('passwordBubble');
            passwordInput.style.borderColor = '';
            passwordInput.style.boxShadow = '';
        }

        if (hasConfirmValue && !passwordsMatch) {
            showFieldBubble('confirmPasswordBubble', 'Password confirmation does not match.');
            confirmPasswordInput.style.borderColor = '#dc2626';
            confirmPasswordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
        } else {
            hideFieldBubble('confirmPasswordBubble');
            confirmPasswordInput.style.borderColor = '';
            confirmPasswordInput.style.boxShadow = '';
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', syncPasswordValidationState);
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', syncPasswordValidationState);
    }

    const nameFieldIds = ['first_name', 'middle_name', 'last_name'];
    nameFieldIds.forEach(function (fieldId) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        input.addEventListener('input', function () {
            input.value = sanitizeNameValue(input.value, true);
            const validationError = getNameValidationError(input.value, fieldId === 'middle_name');
            input.setCustomValidity(validationError);
        });

        input.addEventListener('blur', function () {
            normalizeNameField(fieldId);
            const validationError = getNameValidationError(input.value, fieldId === 'middle_name');
            input.setCustomValidity(validationError);
        });
    });

    if (studentNumInput) {
        studentNumInput.addEventListener('input', function () {
            const validationError = getStudentNumberValidationError(studentNumInput.value);
            studentNumInput.setCustomValidity(validationError);

            if (validationError && studentNumInput.value.trim()) {
                showFieldBubble('studentNumBubble', validationError);
                studentNumInput.style.borderColor = '#dc2626';
                studentNumInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            } else {
                hideFieldBubble('studentNumBubble');
                studentNumInput.style.borderColor = '';
                studentNumInput.style.boxShadow = '';
            }
        });

        studentNumInput.addEventListener('blur', function () {
            const validationError = getStudentNumberValidationError(studentNumInput.value);
            studentNumInput.setCustomValidity(validationError);

            if (validationError && studentNumInput.value.trim()) {
                showFieldBubble('studentNumBubble', validationError);
            } else {
                hideFieldBubble('studentNumBubble');
            }
        });
    }

    if (yearAndSectionInput) {
        yearAndSectionInput.addEventListener('input', function () {
            yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
            const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
            yearAndSectionInput.setCustomValidity(validationError);

            if (validationError && yearAndSectionInput.value.trim()) {
                showFieldBubble('yearSectionBubble', validationError);
                yearAndSectionInput.style.borderColor = '#dc2626';
                yearAndSectionInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
            } else {
                hideFieldBubble('yearSectionBubble');
                yearAndSectionInput.style.borderColor = '';
                yearAndSectionInput.style.boxShadow = '';
            }
        });

        yearAndSectionInput.addEventListener('blur', function () {
            yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
            const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
            yearAndSectionInput.setCustomValidity(validationError);

            if (validationError && yearAndSectionInput.value.trim()) {
                showFieldBubble('yearSectionBubble', validationError);
            } else {
                hideFieldBubble('yearSectionBubble');
            }
        });
    }

    if (emailInput) {
        let emailCheckTimer = null;

        emailInput.addEventListener('input', function () {
            const value = this.value.trim();
            emailAvailabilityStatus = 'idle';

            if (emailCheckTimer) {
                clearTimeout(emailCheckTimer);
            }

            if (!value) {
                this.style.borderColor = '';
                this.style.boxShadow = '';
                hideFieldBubble('emailBubble');
                return;
            }

            emailCheckTimer = setTimeout(async () => {
                const result = await checkEmailAvailability(value);
                if (emailInput.value.trim() !== value) {
                    return;
                }

                if (!result.available) {
                    this.style.borderColor = '#dc2626';
                    this.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showFieldBubble('emailBubble', result.message);
                } else {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                    hideFieldBubble('emailBubble');
                }
            }, 350);
        });
    }

    regForm.addEventListener('submit', function (event) {
        nameFieldIds.forEach(normalizeNameField);

        if (passwordInput) {
            const passwordValidation = evaluatePasswordRequirements(passwordInput.value.trim());
            const confirmPasswordValue = confirmPasswordInput ? confirmPasswordInput.value.trim() : '';
            if (!passwordValidation.isValid) {
                event.preventDefault();
                passwordInput.style.borderColor = '#dc2626';
                passwordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                showFieldBubble('passwordBubble', passwordValidation.unmet);
            } else {
                hideFieldBubble('passwordBubble');
            }

            if (confirmPasswordInput && passwordInput.value.trim() !== confirmPasswordValue) {
                event.preventDefault();
                confirmPasswordInput.style.borderColor = '#dc2626';
                confirmPasswordInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                showFieldBubble('confirmPasswordBubble', 'Password confirmation does not match.');
            } else {
                hideFieldBubble('confirmPasswordBubble');
                if (confirmPasswordInput) {
                    confirmPasswordInput.style.borderColor = '';
                    confirmPasswordInput.style.boxShadow = '';
                }
            }
        }

        if (studentNumInput) {
            const validationError = getStudentNumberValidationError(studentNumInput.value);
            studentNumInput.setCustomValidity(validationError);
            if (validationError) {
                event.preventDefault();
                studentNumInput.style.borderColor = '#dc2626';
                studentNumInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                showFieldBubble('studentNumBubble', validationError);
            } else {
                hideFieldBubble('studentNumBubble');
                studentNumInput.style.borderColor = '';
                studentNumInput.style.boxShadow = '';
            }
        }

        if (yearAndSectionInput) {
            yearAndSectionInput.value = sanitizeYearAndSectionValue(yearAndSectionInput.value);
            const validationError = getYearAndSectionValidationError(yearAndSectionInput.value);
            yearAndSectionInput.setCustomValidity(validationError);
            if (validationError) {
                event.preventDefault();
                yearAndSectionInput.style.borderColor = '#dc2626';
                yearAndSectionInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                showFieldBubble('yearSectionBubble', validationError);
            } else {
                hideFieldBubble('yearSectionBubble');
                yearAndSectionInput.style.borderColor = '';
                yearAndSectionInput.style.boxShadow = '';
            }
        }

        if (!validateNameFields()) {
            event.preventDefault();
        }
    });

    const startYearSelect = document.getElementById('academic_year_start');
    const endYearSelect = document.getElementById('academic_year_end');
    const semesterSelect = document.getElementById('semester');
    const adviserNameSelect = document.getElementById('adviser_name');
    const defaultProfessorOptions = adviserNameSelect.innerHTML;

    if (!startYearSelect || !endYearSelect || !semesterSelect || !adviserNameSelect) {
        return;
    }

    function updateEndYearOptions() {
        const selectedStartYear = parseInt(startYearSelect.value);
        endYearSelect.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'End Year';
        endYearSelect.appendChild(defaultOption);

        if (!isNaN(selectedStartYear)) {
            const nextYear = selectedStartYear + 1;
            const option = document.createElement('option');
            option.value = nextYear;
            option.textContent = nextYear;
            endYearSelect.appendChild(option);
            endYearSelect.value = String(nextYear);
        } else {
            endYearSelect.value = '';
        }

        fetchProfessors(semesterSelect.value, startYearSelect.value, endYearSelect.value);
    }

    function fetchProfessors(semester, startYear, endYear) {
        if (!semester || !startYear || !endYear) {
            adviserNameSelect.innerHTML = defaultProfessorOptions;
            return;
        }

        fetch(`/fetch-professors/${semester}/${startYear}/${endYear}`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    adviserNameSelect.innerHTML = defaultProfessorOptions;
                    return;
                }

                adviserNameSelect.innerHTML = '<option value="">Select Professor</option><option value="Not Yet Listed">Not Yet Listed</option>';
                data.forEach(professor => {
                    const option = document.createElement('option');
                    option.value = professor;
                    option.textContent = professor;
                    adviserNameSelect.appendChild(option);
                });
            })
            .catch(error => {
                adviserNameSelect.innerHTML = defaultProfessorOptions;
                console.error('Error fetching professors:', error);
            });
    }

    updateEndYearOptions();
    startYearSelect.addEventListener('change', updateEndYearOptions);
    semesterSelect.addEventListener('change', function () {
        fetchProfessors(this.value, startYearSelect.value, endYearSelect.value);
    });
    endYearSelect.addEventListener('change', function () {
        fetchProfessors(semesterSelect.value, startYearSelect.value, this.value);
    });
    fetchProfessors(semesterSelect.value, startYearSelect.value, endYearSelect.value);

    if (window.jQuery && $.fn.select2) {
        $('#adviser_name').select2({
            placeholder: 'Search or Select Professor...',
            allowClear: true,
            width: '100%'
        });
    }
});
