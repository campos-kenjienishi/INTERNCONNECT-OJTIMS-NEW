/* Password Setup Modal Scripts */

    function dismissPasswordSetupModal() {
        const overlay = document.getElementById('idpPasswordSetupModalOverlay');
        if (overlay) {
            overlay.style.setProperty('display', 'none', 'important');
            overlay.remove();
        }
    }

    function toggleSetupPwVisibility(inputId, icon) {
        if (!icon) return;
        const input = document.getElementById(inputId);
        if (!input) return;

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        if (isHidden) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('idpPasswordSetupForm');
        const pwInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('new_password_confirmation');
        const saveBtn = document.getElementById('btnSavePassword');
        const matchStatus = document.getElementById('pwMatchStatus');

        if (!pwInput || !confirmInput || !saveBtn) return;

        function checkPasswordRequirements() {
            const val = pwInput.value || '';
            const confirmVal = confirmInput.value || '';

            const isMinLen = val.length >= 8;
            const isUpper = /[A-Z]/.test(val);
            const isLower = /[a-z]/.test(val);
            const isNum = /\d/.test(val);
            const isSpecial = /[!@#$%^&*]/.test(val);

            updateReqItem('reqMinLen', isMinLen);
            updateReqItem('reqUpper', isUpper);
            updateReqItem('reqLower', isLower);
            updateReqItem('reqNum', isNum);
            updateReqItem('reqSpecial', isSpecial);

            const allRequirementsMet = isMinLen && isUpper && isLower && isNum && isSpecial;

            // Password confirmation match check
            let isMatched = false;
            if (confirmVal.length > 0) {
                matchStatus.classList.remove('d-none');
                if (confirmVal === val) {
                    matchStatus.textContent = '✓ Passwords match';
                    matchStatus.className = 'pw-match-status match';
                    isMatched = true;
                } else {
                    matchStatus.textContent = '✕ Passwords do not match';
                    matchStatus.className = 'pw-match-status mismatch';
                    isMatched = false;
                }
            } else {
                matchStatus.classList.add('d-none');
            }

            saveBtn.disabled = !(allRequirementsMet && isMatched);
        }

        function updateReqItem(elemId, isMet) {
            const elem = document.getElementById(elemId);
            if (!elem) return;
            if (isMet) {
                elem.classList.add('met');
                elem.querySelector('i').className = 'fas fa-check-circle me-1';
            } else {
                elem.classList.remove('met');
                elem.querySelector('i').className = 'fas fa-circle me-1';
            }
        }

        pwInput.addEventListener('input', checkPasswordRequirements);
        confirmInput.addEventListener('input', checkPasswordRequirements);

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const alertBox = document.getElementById('passwordSetupAlert');
                alertBox.classList.add('d-none');
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

                const formData = new FormData(form);

                fetch(window.passwordSetupConfig?.setUrl || "/auth/set-local-password", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": (window.passwordSetupConfig?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""),
                        "Accept": "application/json"
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alertBox.className = "alert alert-success";
                        alertBox.innerText = data.message || "Password set successfully!";
                        alertBox.classList.remove('d-none');
                        saveBtn.innerHTML = '<i class="fas fa-check me-1"></i> Saved!';
                        saveBtn.style.background = '#166534';
                        saveBtn.style.borderColor = '#166534';
                        setTimeout(() => {
                            dismissPasswordSetupModal();
                        }, 500);
                    } else {
                        alertBox.className = "alert alert-danger";
                        alertBox.innerText = data.message || "Failed to set password.";
                        alertBox.classList.remove('d-none');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = "Save Password";
                    }
                })
                .catch(err => {
                    alertBox.className = "alert alert-danger";
                    alertBox.innerText = "An error occurred. Please try again.";
                    alertBox.classList.remove('d-none');
                    saveBtn.disabled = false;
                    saveBtn.innerText = "Save Password";
                });
            });
        }
    });