@php
    $modalUser = null;
    if (session('loginId')) {
        $modalUser = \App\Models\User::find(session('loginId'));
    }
@endphp

@if(session('show_password_setup') && $modalUser && !$modalUser->has_local_password)
<link rel="stylesheet" href="{{ vasset('css/pages/password-setup-modal.css') }}">

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
    window.passwordSetupConfig = {
        setUrl: @json(route('auth.setLocalPassword')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/pages/password-setup-modal.js') }}"></script>
@endif