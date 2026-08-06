@if(session('show_password_setup') && Auth::user() && !Auth::user()->has_local_password)
<!-- Password Setup Modal -->
<div class="modal fade show" id="idpPasswordSetupModal" tabindex="-1" aria-labelledby="idpPasswordSetupModalLabel" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.6); z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #800000 0%, #b30000 100%); color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title font-weight-bold" id="idpPasswordSetupModalLabel">
                    <i class="fas fa-key me-2"></i> Set Up Local Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="dismissPasswordSetupModal()"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted" style="font-size: 14px;">
                    You logged in using the <strong>Identity Provider (IdP)</strong>. Setting up a local password allows you to access InternConnect even if the IdP system is temporarily unavailable.
                </p>

                <div id="passwordSetupAlert" class="alert alert-danger d-none" style="font-size: 13px;"></div>

                <form id="idpPasswordSetupForm">
                    @csrf
                    <div class="mb-3">
                        <label for="new_password" class="form-label font-weight-bold" style="font-size: 13px;">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="At least 8 characters" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label font-weight-bold" style="font-size: 13px;">Confirm Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter password" required minlength="8">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light btn-sm" onclick="dismissPasswordSetupModal()">Skip for Now</button>
                        <button type="submit" class="btn btn-danger btn-sm px-4" id="btnSavePassword">Save Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function dismissPasswordSetupModal() {
        const modal = document.getElementById('idpPasswordSetupModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('idpPasswordSetupForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const alertBox = document.getElementById('passwordSetupAlert');
            const submitBtn = document.getElementById('btnSavePassword');
            
            alertBox.classList.add('d-none');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

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
                    setTimeout(() => {
                        dismissPasswordSetupModal();
                    }, 1200);
                } else {
                    alertBox.className = "alert alert-danger";
                    alertBox.innerText = data.message || "Failed to set password.";
                    alertBox.classList.remove('d-none');
                    submitBtn.disabled = false;
                    submitBtn.innerText = "Save Password";
                }
            })
            .catch(err => {
                alertBox.className = "alert alert-danger";
                alertBox.innerText = "An error occurred. Please try again.";
                alertBox.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.innerText = "Save Password";
            });
        });
    });
</script>
@endif
