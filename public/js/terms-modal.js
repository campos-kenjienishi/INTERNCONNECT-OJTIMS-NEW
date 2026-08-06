// terms-modal.js — External script to handle terms modal interaction
function termsModalInit() {
    var btn = document.getElementById('btnTermsContinue');
    var check = document.getElementById('agreeTermsCheck');
    var overlay = document.getElementById('termsModalOverlay');
    var card = document.getElementById('termsCheckboxCard');
    var alertBox = document.getElementById('termsAlertNotice');

    if (!btn || !check) return;

    function syncBtn() {
        if (check.checked) {
            btn.classList.add('tc-unlocked');
            if (card) card.style.borderColor = '#800000';
            if (alertBox) alertBox.style.display = 'none';
        } else {
            btn.classList.remove('tc-unlocked');
            if (card) card.style.borderColor = '#fecaca';
        }
    }

    check.addEventListener('change', syncBtn);
    check.addEventListener('click', function(e) {
        e.stopPropagation();
        syncBtn();
    });

    if (card) {
        card.addEventListener('click', function(e) {
            if (e.target !== check) {
                check.checked = !check.checked;
                syncBtn();
            }
        });
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (!check.checked) {
            if (alertBox) alertBox.style.display = 'block';
            if (card) card.style.borderColor = '#dc2626';
            return;
        }

        var acceptUrl = btn.getAttribute('data-accept-url');
        var csrfToken = btn.getAttribute('data-csrf');

        if (acceptUrl && csrfToken) {
            // Disable button temporarily to prevent double submission
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.7';

            fetch(acceptUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                if (res.ok) {
                    if (overlay) {
                        overlay.style.transition = 'opacity 0.25s ease';
                        overlay.style.opacity = '0';
                        setTimeout(function () {
                            overlay.style.display = 'none';
                        }, 250);
                    }
                } else {
                    btn.style.pointerEvents = 'auto';
                    btn.style.opacity = '1';
                    console.error('Terms acceptance failed on server. Status:', res.status);
                }
            })
            .catch(function(err) {
                btn.style.pointerEvents = 'auto';
                btn.style.opacity = '1';
                console.error('Terms acceptance network error:', err);
            });
        }
    });

    btn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            btn.click();
        }
    });

    syncBtn();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', termsModalInit);
} else {
    termsModalInit();
}
