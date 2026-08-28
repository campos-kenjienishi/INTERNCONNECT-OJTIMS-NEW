<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Landing Page</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
     <link rel="stylesheet" href="{{ vasset('css/pages/landing-page.css') }}">
</head>
<body>
    <header class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <img src="/images/puplogo.png" alt="PUP Taguig">
            <div class="brand-text">
                <span class="brand-title">Polytechnic University of the Philippines &ndash; Taguig Campus</span>
                <span class="brand-subtitle">The Country's 1st Polytechnic University</span>
            </div>
        </div>

        <nav class="nav" aria-label="Primary">
            <a href="#home">Home</a>
            <a href="#Features">Features</a>
            <a href="#Contact Us">Contact Us</a>
        </nav>

        <a class="portal-btn" href="{{ route('login.gateway') }}">
            <i class="fa fa-sign-in-alt"></i>
            Launch Portal
        </a>
    </div>
</header>

    <main id="home" class="hero">
    <button class="hero-arrow" id="heroArrow" aria-label="Show developer team">
        <i class="fa fa-chevron-right"></i>
    </button>

    <div class="hero-card" id="heroCardDefault">
        <div class="hero-media reveal" data-reveal="bounce">
            <img class="hero-mark" src="/images/OJTIMS LOGO.png" alt="InternConnect mark">
            <p class="hero-tagline">Your OJT Journey, All in One Place</p>
        </div>

        <div class="hero-text">
            <h1 class="hero-title">Intern<span>Connect</span>:</h1>
            <h2 class="hero-subtitle">On-the-Job (OJT) Training<br>Information Management System</h2>
            <p class="hero-copy">
                A centralized and secure platform for OJT management, including MOA management, OJT requirements tracking, and company evaluations for PUP&ndash;Taguig Campus.
            </p>
        </div>
    </div>

    <div class="hero-card hero-card-alt" id="heroCardTeam">
        <div class="hero-media reveal" data-reveal="bounce">
            <img class="hero-mark" src="/images/WardsLogo.png" alt="InternConnect mark">
            <p class="hero-tagline">Team Wards</p>
        </div>

        <div class="hero-text">
    <span class="hero-eyebrow">Developer Team</span>
    <div class="hero-eyebrow-line"></div>
    <h2 class="hero-subtitle hero-subtitle-alt">
        Guided to <span class="accent-gold">Create</span>,<br>
        Driven to <span class="accent-red">Innovate</span>.
    </h2>
    <p class="hero-copy">
        Wards is a five-member team of BSIT 4-1 students from PUP&ndash;Taguig Campus committed to applying technology to address practical challenges. Through collaboration and innovation, the team develops solutions designed to improve processes and user experiences. InternConnect: OJTIMS reflects this commitment by providing a centralized platform for a more organized and efficient OJT management process.
    </p>
</div>
    </div>
</main>

    <section id="Features" class="section section-centered">
        <div class="section-inner">
            <div class="section-label reveal" data-reveal="fade"><i class="fa fa-info-circle"></i> Features</div>
            <h2 class="reveal" data-reveal="slide">Built for students, coordinators, and professors.</h2>
            <p class="reveal" data-reveal="fade">
                InternConnect brings OJT workflows into one place so users can submit requirements, track approvals, and manage training progress without the usual paper chase.
            </p>

            <div class="feature-grid">
                <article class="feature-card reveal" data-reveal="slide">
                    <div class="feature-icon"><i class="fa fa-file-upload"></i></div>
                    <h3>Document Submission</h3>
                    <p>Upload requirements quickly through a clear portal flow designed for mobile and desktop users.</p>
                </article>
                <article class="feature-card reveal" data-reveal="bounce">
                    <div class="feature-icon"><i class="fa fa-clipboard-check"></i></div>
                    <h3>Transparent Evaluation</h3>
                    <p>Track approvals, denials, and completion states with less back-and-forth and fewer manual checks.</p>
                </article>
                <article class="feature-card reveal" data-reveal="slide">
                    <div class="feature-icon"><i class="fa fa-users-cog"></i></div>
                    <h3>Program Management</h3>
                    <p>Keep students, companies, and coordinators aligned inside one system that follows the workflow.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="Contact Us" class="section alt section-centered contact-section">
        <div class="section-inner">
            <div class="section-label reveal" data-reveal="fade"><i class="fa fa-headset"></i> Contact Us</div>
            <h2 class="reveal" data-reveal="slide">Need help getting started?</h2>
            <p class="contact-intro reveal" data-reveal="fade">
                Reach out if you need help with submissions, account access, or follow-up questions about your OJT requirements. We keep support simple: quick messages through Facebook Messenger, or a direct email if you need a formal concern documented.
            </p>

            <div class="contact-highlights">
                <div class="contact-highlight location reveal" data-reveal="slide">
                    <i class="fa fa-map-marker-alt"></i>
                    <strong>Location</strong>
                    <span>PUP Taguig Campus, for on-site coordination and office visits when needed.</span>
                </div>
                <div class="contact-highlight contact reveal" data-reveal="bounce">
                    <i class="fa fa-comments"></i>
                    <strong>Contact</strong>
                    <span>Use Facebook Messenger for fast concerns or email for formal support messages.</span>
                </div>
                <div class="contact-highlight reveal" data-reveal="fade">
                    <i class="fa fa-envelope"></i>
                    <strong>Email Support</strong>
                    <span>Use email for formal concerns, attachments, or issues that need a written record.</span>
                </div>
                <div class="contact-highlight reveal" data-reveal="slide">
                    <i class="fa fa-clock"></i>
                    <strong>Working Hours</strong>
                    <span>We usually reply during Mon-Fri, 8AM-5PM, as soon as the support team is available.</span>
                </div>
            </div>

                        <div class="contact-card reveal" data-reveal="bounce">
                <div class="contact-card-left">
                    <h3>Reach us directly</h3>
                    <p>Message us on Facebook or send an email and we'll get back to you as soon as we can.</p>
                </div>

                <div class="contact-card-divider"></div>

                <div class="contact-card-right">
                    <span class="contact-eyebrow">Stay connected, follow us on our socials</span>

                    <a class="contact-info-row" href="mailto:internconnect.ojtims@gmail.com">
                        <span class="contact-info-icon"><i class="fa fa-envelope"></i></span>
                        <span class="contact-info-text">internconnect.ojtims@gmail.com</span>
                    </a>

                    <a class="contact-info-row" href="https://www.facebook.com/profile.php?id=61593939354633" target="_blank" rel="noopener noreferrer">
                        <span class="contact-info-icon"><i class="fab fa-facebook-f"></i></span>
                        <span class="contact-info-text">InternConnect: On-the-Job Training Information Management System</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="PUP logo">
                <div class="footer-meta">
                    <span class="footer-copy">© {{ date('Y') }} <span>InternConnect</span></span>
                    <span class="footer-divider">|</span>
                    <span>OJT Information Management System</span>
                </div>
            </div>

            <div class="footer-links">
                <a href="https://www.pup.edu.ph/" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                    PUP Website
                </a>
                <span class="footer-divider">|</span>
                <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                    Terms of Use
                </a>
                <span class="footer-divider">|</span>
                <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                    Privacy Statement
                </a>
            </div>
        </div>
    </footer>
            <script>
                (() => {
                    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    const revealTargets = document.querySelectorAll('.reveal');

                    if (reduceMotion || !('IntersectionObserver' in window)) {
                        revealTargets.forEach((element) => {
                            element.classList.add('is-visible');
                        });
                        return;
                    }

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
                })();
    const heroArrow = document.getElementById('heroArrow');
    const heroSection = document.getElementById('home');

    heroArrow.addEventListener('click', () => {
        heroSection.classList.toggle('show-team');
        heroArrow.classList.toggle('is-flipped');
        heroArrow.setAttribute(
            'aria-label',
            heroSection.classList.contains('show-team') ? 'Show intro' : 'Show developer team'
        );
    });
            </script>
</body>
</html>
