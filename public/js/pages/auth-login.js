/* Auth Login Scripts */

document.addEventListener('DOMContentLoaded', function () {
    const togglePasswordBtn = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#id_password') || document.querySelector('input[name="password"]');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';

            if (isHidden) {
                togglePasswordBtn.classList.remove('fa-eye');
                togglePasswordBtn.classList.add('fa-eye-slash');
            } else {
                togglePasswordBtn.classList.remove('fa-eye-slash');
                togglePasswordBtn.classList.add('fa-eye');
            }
        });
    }
});
