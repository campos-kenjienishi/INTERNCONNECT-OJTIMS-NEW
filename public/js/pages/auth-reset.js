/* Auth Reset Password Scripts */

    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const newPasswordInput  = document.getElementById('new_password');
    const confirmInput      = document.getElementById('confirm_password');
    const strengthBar       = document.getElementById('strengthBar');
    const strengthLabel     = document.getElementById('strengthLabel');
    const matchIndicator    = document.getElementById('matchIndicator');
    const matchText         = document.getElementById('matchText');

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

    function checkStrength(password) {
        let score = 0;
        if (password.length >= 8)  score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    function checkMatch() {
        if (!newPasswordInput || !confirmInput || !matchIndicator || !matchText) {
            return;
        }

        const pw  = newPasswordInput.value;
        const cpw = confirmInput.value;

        if (cpw.length === 0) {
            matchIndicator.classList.remove('visible', 'match', 'no-match');
            return;
        }

        matchIndicator.classList.add('visible');

        if (pw === cpw) {
            matchIndicator.classList.add('match');
            matchIndicator.classList.remove('no-match');
            matchIndicator.querySelector('i').className = 'fa fa-check-circle';
            matchText.textContent = 'Passwords match';
        } else {
            matchIndicator.classList.add('no-match');
            matchIndicator.classList.remove('match');
            matchIndicator.querySelector('i').className = 'fa fa-times-circle';
            matchText.textContent = 'Passwords do not match';
        }
    }

    if (newPasswordInput && strengthBar && strengthLabel) {
        newPasswordInput.addEventListener('input', function () {
            const val   = this.value;
            const score = checkStrength(val);

            const levels = [
                { width: '0%',   color: 'transparent',               label: 'Enter a password' },
                { width: '20%',  color: '#ef4444',                   label: 'Very weak' },
                { width: '40%',  color: '#f97316',                   label: 'Weak' },
                { width: '60%',  color: '#eab308',                   label: 'Fair' },
                { width: '80%',  color: '#84cc16',                   label: 'Strong' },
                { width: '100%', color: '#22c55e',                   label: 'Very strong' },
            ];

            const level = val.length === 0 ? levels[0] : levels[Math.min(score, 5)];
            strengthBar.style.width      = level.width;
            strengthBar.style.background = level.color;
            strengthLabel.textContent    = level.label;
            strengthLabel.style.color    = val.length === 0 ? 'rgba(255,255,255,0.4)' : level.color;

            checkMatch();
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkMatch);
    }

    // Email from URL
    function getEmailQueryParam() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('email');
    }

    const email = getEmailQueryParam();