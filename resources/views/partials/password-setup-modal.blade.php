@php
    $modalUser = null;
    if (session('loginId')) {
        $modalUser = \App\Models\User::find(session('loginId'));
    }
@endphp

@if(session('show_password_setup') && $modalUser && !$modalUser->has_local_password)
<style>
    #idpPasswordSetupModalOverlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        z-index: 99999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 16px !important;
        margin: 0 !important;
    }

    #idpPasswordSetupModal .modal-dialog {
        width: 100%;
        max-width: 500px;
        margin: auto !important;
    }

    #idpPasswordSetupModal .modal-content {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid rgba(0,0,0,0.08) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
        overflow: hidden !important;
        font-family: 'Poppins', sans-serif !important;
        max-height: 90vh !important;
        display: flex !important;
        flex-direction: column !important;
    }

    #idpPasswordSetupModal .modal-header {
        background: linear-gradient(135deg, #800000 0%, #a80000 100%) !important;
        color: #ffffff !important;
        padding: 18px 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        border-bottom: none !important;
        flex-shrink: 0 !important;
    }

    #idpPasswordSetupModal .modal-title {
        color: #ffffff !important;
        font-size: 17px !important;
        font-weight: 700 !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    #idpPasswordSetupModal .modal-body {
        background: #ffffff !important;
        color: #334155 !important;
        padding: 22px 24px !important;
        overflow-y: auto !important;
        flex: 1 1 auto !important;
    }

    .pw-info-banner {
        background: #fff5f5;
        border: 1px solid #fecaca;
        border-left: 4px solid #dc2626;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        color: #991b1b;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    #idpPasswordSetupModal label {
        color: #1e293b !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        margin-bottom: 6px !important;
        display: block !important;
    }

    .pw-input-wrap {
        position: relative !important;
        width: 100% !important;
    }

    #idpPasswordSetupModal input.form-control {
        width: 100% !important;
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #0f172a !important;
        padding: 10px 42px 10px 14px !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        height: 44px !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }

    #idpPasswordSetupModal input.form-control:focus {
        background: #ffffff !important;
        border-color: #800000 !important;
        box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.12) !important;
    }

    .pw-toggle-eye {
        position: absolute !important;
        right: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #94a3b8 !important;
        cursor: pointer !important;
        font-size: 14px !important;
        z-index: 10 !important;
        transition: color 0.2s !important;
    }

    .pw-toggle-eye:hover {
        color: #800000 !important;
    }

    /* Requirement Checklist Card */
    .pw-req-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 14px;
        margin-bottom: 18px;
    }

    .pw-req-title {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .pw-req-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 12px;
    }

    .pw-req-item {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
    }

    .pw-req-item.met {
        color: #166534;
        font-weight: 600;
    }

    .pw-req-item i {
        font-size: 11px;
    }

    .pw-match-status {
        font-size: 12px;
        font-weight: 600;
        margin-top: 6px;
        display: block;
    }
    .pw-match-status.match { color: #166534; }
    .pw-match-status.mismatch { color: #dc2626; }

    #idpPasswordSetupModal .modal-close-btn {
        background: transparent !important;
        border: none !important;
        color: #ffffff !important;
        font-size: 18px !important;
        cursor: pointer !important;
        opacity: 0.85 !important;
        transition: opacity 0.2s !important;
        padding: 4px 8px !important;
        line-height: 1 !important;
        box-shadow: none !important;
        margin: 0 !important;
    }

    #idpPasswordSetupModal .modal-close-btn:hover {
        opacity: 1 !important;
    }

    #idpPasswordSetupModal .btn-light {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        padding: 9px 20px !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        transition: all 0.2s !important;
    }

    #idpPasswordSetupModal .btn-light:hover {
        background: #e2e8f0 !important;
        color: #1e293b !important;
    }

    #idpPasswordSetupModal .btn-danger {
        background: linear-gradient(135deg, #800000, #b30000) !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 600 !important;
        padding: 9px 24px !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        box-shadow: 0 4px 14px rgba(128,0,0,0.3) !important;
        transition: all 0.2s !important;
    }

    #idpPasswordSetupModal .btn-danger:disabled {
        background: #cbd5e1 !important;
        color: #94a3b8 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }

    @media (max-width: 576px) {
        #idpPasswordSetupModal .modal-dialog {
            margin: 10px auto !important;
            max-width: calc(100vw - 20px) !important;
        }
        #idpPasswordSetupModal .modal-header {
            padding: 14px 18px !important;
        }
        #idpPasswordSetupModal .modal-body {
            padding: 18px 18px !important;
        }
        .pw-req-grid {
            grid-template-columns: 1fr;
        }
        #idpPasswordSetupModal .pw-modal-footer {
            flex-direction: column-reverse !important;
            gap: 8px !important;
        }
        #idpPasswordSetupModal .pw-modal-footer button {
            width: 100% !important;
            padding: 11px 14px !important;
            font-size: 13.5px !important;
            justify-content: center !important;
        }
    }
</style>

<!-- Password Setup Modal -->
<div class="modal fade show" id="idpPasswordSetupModalOverlay" tabindex="-1" aria-labelledby="idpPasswordSetupModalLabel" aria-modal="true" role="dialog">
    <div id="idpPasswordSetupModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="idpPasswordSetupModalLabel">
                        <i class="fas fa-shield-alt"></i> Set Up Local Fallback Password
                    </h5>
                    <button type="button" class="modal-close-btn" onclick="dismissPasswordSetupModal()" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="pw-info-banner">
                        <i class="fas fa-info-circle me-1"></i> You logged in via <strong>Identity Provider (IdP)</strong>. Setting a local password lets you log in even if IdP is temporarily unavailable.
                    </div>

                    <div id="passwordSetupAlert" class="alert alert-danger d-none" style="font-size: 13px;"></div>

                    <form id="idpPasswordSetupForm">
                        @csrf
                        <div class="mb-3">
                            <label for="new_password">New Password</label>
                            <div class="pw-input-wrap">
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Create a strong password" required>
                                <i class="fas fa-eye pw-toggle-eye" onclick="toggleSetupPwVisibility('new_password', this)"></i>
                            </div>
                        </div>

                        <!-- Real-Time Password Restrictions Checklist -->
                        <div class="pw-req-card">
                            <div class="pw-req-title"><i class="fas fa-lock me-1"></i> Password Requirements</div>
                            <div class="pw-req-grid">
                                <div class="pw-req-item" id="reqMinLen"><i class="fas fa-circle"></i> At least 8 characters</div>
                                <div class="pw-req-item" id="reqUpper"><i class="fas fa-circle"></i> One uppercase (A-Z)</div>
                                <div class="pw-req-item" id="reqLower"><i class="fas fa-circle"></i> One lowercase (a-z)</div>
                                <div class="pw-req-item" id="reqNum"><i class="fas fa-circle"></i> One number (0-9)</div>
                                <div class="pw-req-item" id="reqSpecial" style="grid-column: 1 / -1;"><i class="fas fa-circle"></i> One special symbol (!@#$%^&*)</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation">Confirm Password</label>
                            <div class="pw-input-wrap">
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter password to confirm" required>
                                <i class="fas fa-eye pw-toggle-eye" onclick="toggleSetupPwVisibility('new_password_confirmation', this)"></i>
                            </div>
                            <small class="pw-match-status d-none" id="pwMatchStatus"></small>
                        </div>

                        <div class="pw-modal-footer d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" onclick="dismissPasswordSetupModal()">Skip for Now</button>
                            <button type="submit" class="btn btn-danger" id="btnSavePassword" disabled>Save Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

                fetch("{{ route('auth.setLocalPassword') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
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
</script>
@endif
