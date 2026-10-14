/* Landing Page Scripts — Reveal on Scroll & Team Toggle */

document.addEventListener('DOMContentLoaded', () => {
    // ── Reveal on Scroll (IntersectionObserver) ──
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const budToggle = document.querySelector('[data-bud-toggle]');

    if (budToggle) {
        const budImage = budToggle.querySelector('.bud-mascot-img');
        const cheeringImage = new Image();
        cheeringImage.src = budToggle.dataset.budCheering;
        const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
        let cheering = false;
        let reactionAnimation;

        budToggle.addEventListener('click', () => {
            cheering = !cheering;
            budImage.src = cheering ? budToggle.dataset.budCheering : budToggle.dataset.budOriginal;
            budImage.alt = cheering
                ? 'Bud smiling with both paws raised'
                : 'Bud, your friendly OJT buddy, waving hello';
            budToggle.setAttribute('aria-pressed', String(cheering));

            reactionAnimation?.cancel();
            if (!motionPreference.matches && typeof budImage.animate === 'function') {
                reactionAnimation = budImage.animate([
                    { transform: 'scale(1) rotate(0deg)' },
                    { transform: 'scale(0.94) rotate(-3deg)', offset: 0.25 },
                    { transform: 'scale(1.06) rotate(3deg)', offset: 0.6 },
                    { transform: 'scale(1) rotate(0deg)' },
                ], { duration: 450, easing: 'ease-out' });
            }
        });
    }

    const revealTargets = document.querySelectorAll('.reveal');

    if (reduceMotion || !('IntersectionObserver' in window)) {
        revealTargets.forEach((element) => {
            element.classList.add('is-visible');
        });
    } else {
        let lastScrollY = window.scrollY;
        let scrollingDown = true;

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            scrollingDown = currentScrollY >= lastScrollY;
            lastScrollY = currentScrollY;
        }, { passive: true });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.toggle('reveal-up', scrollingDown);
                    entry.target.classList.toggle('reveal-down', !scrollingDown);
                    entry.target.classList.add('is-visible');
                } else {
                    entry.target.classList.remove('is-visible', 'reveal-up', 'reveal-down');
                }
            });
        }, {
            threshold: 0.18,
            rootMargin: '0px 0px -10% 0px',
        });

        revealTargets.forEach((element) => observer.observe(element));
    }

    // ── Team Wards Toggle Arrow ──
    const heroArrow = document.getElementById('heroArrow');
    const heroSection = document.getElementById('home');

    if (heroArrow && heroSection) {
        heroArrow.addEventListener('click', () => {
            heroSection.classList.toggle('show-team');
            heroArrow.classList.toggle('is-flipped');
            heroArrow.setAttribute(
                'aria-label',
                heroSection.classList.contains('show-team') ? 'Show intro' : 'Show developer team'
            );
        });
    }
});
