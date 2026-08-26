/* Auth Reset Password Scripts */

document.addEventListener('DOMContentLoaded', function () {
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const newPasswordInput  = document.getElementById('new_password');
    const confirmInput      = document.getElementById('confirm_password');
    const strengthBar       = document.getElementById('strengthBar');
    const strengthLabel     = document.getElementById('strengthLabel');
    const matchIndicator    = document.getElementById('matchIndicator');
    const matchText         = document.getElementById('matchText');
    const passwordBubble    = document.getElementById('passwordBubble');
    const bubbleWarning     = document.getElementById('bubbleWarning');
    const confirmBubble     = document.getElementById('confirmBubble');
    const resetForm         = document.querySelector('form[action*="reset-password"]');

    // Requirements elements
    const reqLength = document.getElementById('req-length');
    const reqUpper  = document.getElementById('req-upper');
    const reqLower  = document.getElementById('req-lower');
    const reqNum    = document.getElementById('req-num');
    const reqSym    = document.getElementById('req-sym');

    // Password visibility toggles
    if (toggleNewPassword && newPasswordInput) {
        toggleNewPassword.addEventListener('click', function () {
            newPasswordInput.type = newPasswordInput.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            this.classList.remove('toggled');
            void this.offsetWidth;
            this.classList.add('toggled');
        });
    }

    if (toggleConfirmPassword && confirmInput) {
        toggleConfirmPassword.addEventListener('click', function () {
            confirmInput.type = confirmInput.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            this.classList.remove('toggled');
            void this.offsetWidth;
            this.classList.add('toggled');
        });
    }

    function evaluateRequirements(password) {
        const hasLength = password.length >= 8;
        const hasUpper  = /[A-Z]/.test(password);
        const hasLower  = /[a-z]/.test(password);
        const hasNum    = /\d/.test(password);
        const hasSym    = /[!@#$%^&*]/.test(password);

        updateReqItem(reqLength, hasLength);
        updateReqItem(reqUpper, hasUpper);
        updateReqItem(reqLower, hasLower);
        updateReqItem(reqNum, hasNum);
        updateReqItem(reqSym, hasSym);

        const allValid = hasLength && hasUpper && hasLower && hasNum && hasSym;
        if (allValid && bubbleWarning) {
            bubbleWarning.style.display = 'none';
        }
        return allValid;
    }

    function updateReqItem(el, isValid) {
        if (!el) return;
        const icon = el.querySelector('i');
        if (isValid) {
            el.classList.add('valid');
            el.classList.remove('invalid');
            if (icon) icon.className = 'fa fa-check-circle';
        } else {
            el.classList.remove('valid');
            if (icon) icon.className = 'fa fa-circle';
        }
    }

    function checkStrength(password) {
        let score = 0;
        if (password.length >= 8)  score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[!@#$%^&*]/.test(password)) score++;
        return score;
    }

    function checkMatch() {
        if (!newPasswordInput || !confirmInput) return false;

        const pw  = newPasswordInput.value;
        const cpw = confirmInput.value;

        if (confirmBubble) {
            confirmBubble.style.display = 'none';
        }

        if (cpw.length === 0) {
            if (matchIndicator) matchIndicator.classList.remove('visible', 'match', 'no-match');
            return false;
        }

        if (matchIndicator) matchIndicator.classList.add('visible');

        if (pw === cpw) {
            if (matchIndicator) {
                matchIndicator.classList.add('match');
                matchIndicator.classList.remove('no-match');
                const icon = matchIndicator.querySelector('i');
                if (icon) icon.className = 'fa fa-check-circle';
                if (matchText) matchText.textContent = 'Passwords match';
            }
            return true;
        } else {
            if (matchIndicator) {
                matchIndicator.classList.add('no-match');
                matchIndicator.classList.remove('match');
                const icon = matchIndicator.querySelector('i');
                if (icon) icon.className = 'fa fa-times-circle';
                if (matchText) matchText.textContent = 'Passwords do not match';
            }
            return false;
        }
    }

    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function () {
            const val = this.value;
            evaluateRequirements(val);

            if (strengthBar && strengthLabel) {
                const score = checkStrength(val);
                const levels = [
                    { width: '0%',   color: 'transparent', label: 'Enter a password' },
                    { width: '20%',  color: '#ef4444',     label: 'Very weak' },
                    { width: '40%',  color: '#f97316',     label: 'Weak' },
                    { width: '60%',  color: '#eab308',     label: 'Fair' },
                    { width: '80%',  color: '#84cc16',     label: 'Strong' },
                    { width: '100%', color: '#22c55e',     label: 'Very strong' },
                ];

                const level = val.length === 0 ? levels[0] : levels[Math.min(score, 5)];
                strengthBar.style.width      = level.width;
                strengthBar.style.background = level.color;
                strengthLabel.textContent    = level.label;
                strengthLabel.style.color    = val.length === 0 ? 'rgba(255,255,255,0.45)' : level.color;
            }

            if (confirmInput && confirmInput.value.length > 0) {
                checkMatch();
            }
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkMatch);
    }

    // Form submission validation
    if (resetForm) {
        resetForm.addEventListener('submit', function (e) {
            const passwordVal = newPasswordInput ? newPasswordInput.value : '';
            const confirmVal  = confirmInput ? confirmInput.value : '';
            const isValidReq  = evaluateRequirements(passwordVal);

            if (!isValidReq) {
                e.preventDefault();
                if (bubbleWarning) bubbleWarning.style.display = 'flex';
                if (passwordBubble) {
                    passwordBubble.classList.remove('invalid-shake');
                    void passwordBubble.offsetWidth;
                    passwordBubble.classList.add('invalid-shake');
                }
                if (newPasswordInput) newPasswordInput.focus();
                return false;
            }

            if (passwordVal !== confirmVal || confirmVal.length === 0) {
                e.preventDefault();
                if (confirmBubble) confirmBubble.style.display = 'flex';
                if (confirmInput) confirmInput.focus();
                return false;
            }
        });
    }
});