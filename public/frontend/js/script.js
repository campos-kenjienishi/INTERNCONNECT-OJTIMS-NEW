document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordBtn = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#id_password');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';

            const icon = this.querySelector('i') || (this.tagName.toLowerCase() === 'i' ? this : null);
            if (icon) {
                if (isHidden) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }
});

function togglePasswordVisibility() {
    const password = document.getElementById("password") || document.getElementById("id_password");
    if (password) {
        password.type = password.type === "password" ? "text" : "password";
    }
}