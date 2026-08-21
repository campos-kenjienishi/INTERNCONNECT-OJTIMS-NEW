/* Student Scripts */

    function toggleSection(header) {
        const section = header.closest('.terms-section');
        section.classList.toggle('open');
    }

    document.querySelectorAll('.toc-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (!target.classList.contains('open')) {
                    target.querySelector('.section-header').click();
                }
            }
        });
    });

