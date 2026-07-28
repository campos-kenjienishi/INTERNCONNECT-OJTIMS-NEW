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
    <style>
        :root {
            --maroon: #8f1111;
            --maroon-deep: #6f0707;
            --gold: #f8c62b;
            --cream: #fff7e8;
            --text: #ffffff;
            --shadow: 0 18px 50px rgba(0, 0, 0, 0.25);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: 'Poppins', sans-serif;
            background: #140606;
            color: var(--text);
            overflow-x: hidden;
        }

        body {
            min-height: 100vh;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            padding: 12px 0;
            background:
                linear-gradient(180deg, rgba(143, 17, 17, 0.96) 0%, rgba(111, 7, 7, 0.96) 100%),
                rgba(20, 6, 6, 0.35);
            border-bottom: 1px solid rgba(248, 198, 43, 0.24);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.24);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 12px clamp(16px, 4vw, 24px);
            max-width: 1440px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand img {
            width: 62px;
            height: 62px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 8px 14px rgba(0, 0, 0, 0.22));
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 24px;
            flex: 1;
            justify-content: center;
            flex-wrap: wrap;
            padding: 8px;
            border-radius: 999px;
            background: rgba(20, 6, 6, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .nav a {
            position: relative;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.02em;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: transparent;
            transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .nav a::after {
            content: none;
        }

        .nav a:hover,
        .nav a:focus-visible {
            color: #fff;
            background: linear-gradient(180deg, rgba(248, 198, 43, 0.22) 0%, rgba(255, 255, 255, 0.08) 100%);
            border-color: rgba(248, 198, 43, 0.3);
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.14);
            transform: translateY(-1px);
        }

        .portal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 190px;
            min-height: 46px;
            padding: 0 22px;
            border-radius: 999px;
            border: 1px solid rgba(248, 198, 43, 0.35);
            color: #fff7dc;
            background: linear-gradient(180deg, rgba(248, 198, 43, 0.2) 0%, rgba(255, 255, 255, 0.06) 100%);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
            white-space: nowrap;
        }

        .portal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.24);
            border-color: rgba(248, 198, 43, 0.55);
            background: linear-gradient(180deg, rgba(248, 198, 43, 0.28) 0%, rgba(255, 255, 255, 0.1) 100%);
        }

        .hero {
            position: relative;
            min-height: calc(100vh - 94px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 48px 20px;
            background:
                linear-gradient(180deg, rgba(76, 8, 8, 0.38), rgba(40, 10, 10, 0.58)),
                url('/images/LandingPage.jpg') center center / cover no-repeat;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top, rgba(255, 222, 123, 0.12), transparent 40%);
            pointer-events: none;
        }

        .hero-card {
            position: relative;
            z-index: 1;
            width: min(100%, 1020px);
            padding: 36px 24px 30px;
            animation: heroCardEntrance 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes heroCardEntrance {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes heroMarkEntrance {
            0% {
                opacity: 0;
                transform: translateY(-24px) scale(0.82);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes heroMarkFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .hero-mark {
            width: 140px;
            max-width: 40vw;
            margin: 0 auto 16px;
            display: block;
            filter: drop-shadow(0 16px 24px rgba(0, 0, 0, 0.25));
            animation: heroMarkEntrance 0.9s cubic-bezier(0.22, 1, 0.36, 1) both,
                       heroMarkFloat 4.5s ease-in-out 0.9s infinite;
        }

        .hero-title {
            margin: 0;
            font-size: clamp(1.6rem, 9vw, 4.1rem);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.04em;
            text-shadow: 0 8px 20px rgba(0, 0, 0, 0.28);
            animation: heroTextRise 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.14s both;
            overflow-wrap: break-word;
            word-break: break-word;
            padding: 0 8px;
        }

        .hero-title span {
            color: var(--gold);
        }

        .hero-copy {
            max-width: 860px;
            margin: 14px auto 0;
            font-size: clamp(1rem, 2.6vw, 1.2rem);
            line-height: 1.45;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.96);
            text-shadow: 0 5px 14px rgba(0, 0, 0, 0.28);
            animation: heroTextRise 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.28s both;
        }

        .hero-actions {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            animation: heroTextRise 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.42s both;
        }

        @keyframes heroTextRise {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .launch-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 220px;
            min-height: 54px;
            padding: 0 28px;
            border-radius: 14px;
            text-decoration: none;
            color: #7c1b1b;
            background: linear-gradient(180deg, #fffdf7 0%, #fff 100%);
            font-size: 17px;
            font-weight: 800;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .launch-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 54px rgba(0, 0, 0, 0.28);
        }

        .section {
            padding: 84px clamp(18px, 5vw, 64px);
            background: linear-gradient(180deg, #fffaf5 0%, #fff 100%);
            color: #3a1414;
        }

        .section.alt {
            background: linear-gradient(180deg, #fff 0%, #fef2f2 100%);
        }

        .section-inner {
            max-width: 1180px;
            margin: 0 auto;
        }

        .section-centered .section-inner {
            text-align: center;
        }

        .section-centered .section-label {
            margin-left: auto;
            margin-right: auto;
        }

        .section-centered .section-inner > p {
            margin-left: auto;
            margin-right: auto;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #fee2e2;
            color: #8f1111;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .section h2 {
            margin: 0;
            font-size: clamp(1.8rem, 4vw, 3rem);
            line-height: 1.1;
            color: #5c1010;
        }

        .section p {
            max-width: 820px;
            margin: 16px 0 0;
            font-size: 1rem;
            line-height: 1.7;
            color: #5f5151;
        }

        .reveal {
            opacity: 0;
            will-change: transform, opacity;
        }

        .reveal.is-visible {
            opacity: 1;
        }

        .reveal[data-reveal="fade"] {
            transform: translateY(10px);
        }

        .reveal[data-reveal="slide"] {
            transform: translateY(24px);
        }

        .reveal[data-reveal="bounce"] {
            transform: translateY(18px) scale(0.98);
        }

        .reveal.is-visible[data-reveal="fade"] {
            animation: fadeInSoft 0.8s ease both;
        }

        .reveal.is-visible[data-reveal="slide"].reveal-up {
            animation: slideInUp 0.75s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .reveal.is-visible[data-reveal="slide"].reveal-down {
            animation: slideInDown 0.75s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .reveal.is-visible[data-reveal="bounce"] {
            animation: bounceInSoft 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeInSoft {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceInSoft {
            0% {
                opacity: 0;
                transform: translateY(18px) scale(0.96);
            }

            60% {
                opacity: 1;
                transform: translateY(-6px) scale(1.02);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 30px;
        }

        .feature-card {
            background: #fff;
            border: 1px solid rgba(143, 17, 17, 0.08);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 14px 32px rgba(143, 17, 17, 0.08);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(143, 17, 17, 0.16);
            box-shadow: 0 20px 38px rgba(143, 17, 17, 0.14);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #fee2e2 0%, #fff5f5 100%);
            color: #8f1111;
            font-size: 18px;
            margin-bottom: 14px;
        }

        .feature-card h3 {
            margin: 0 0 8px;
            font-size: 18px;
            color: #3b1111;
        }

        .feature-card p {
            margin: 0;
            font-size: 14px;
            color: #6b5d5d;
            max-width: 280px;
        }

        .support-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 30px;
        }

        .support-card {
            border-radius: 22px;
            padding: 22px;
            background: linear-gradient(180deg, #8f1111 0%, #5e0b0b 100%);
            color: #fff;
            box-shadow: 0 18px 32px rgba(143, 17, 17, 0.18);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease;
        }

        .support-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 42px rgba(143, 17, 17, 0.24);
            filter: saturate(1.04);
        }

        .support-card a {
            color: #fff2b1;
            text-decoration: none;
            font-weight: 700;
        }

        .contact-section {
            background:
                radial-gradient(circle at top left, rgba(248, 198, 43, 0.18), transparent 32%),
                radial-gradient(circle at bottom right, rgba(143, 17, 17, 0.12), transparent 34%),
                linear-gradient(180deg, #fff7e6 0%, #fff 100%);
        }

        .contact-intro {
            max-width: 760px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-highlights {
            margin-top: 26px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .contact-highlight {
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(135deg, #fff 0%, #fff6f0 100%);
            border: 1px solid rgba(143, 17, 17, 0.08);
            box-shadow: 0 12px 26px rgba(143, 17, 17, 0.08);
            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .contact-highlight:hover {
            transform: translateY(-5px);
            border-color: rgba(143, 17, 17, 0.15);
            box-shadow: 0 18px 32px rgba(143, 17, 17, 0.14);
        }

        .contact-highlight i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            margin-bottom: 10px;
            color: #fff;
            background: linear-gradient(180deg, #a01616 0%, var(--maroon) 100%);
        }

        .contact-highlight strong {
            display: block;
            margin-bottom: 6px;
            color: #3b1111;
        }

        .contact-highlight span {
            font-size: 13px;
            line-height: 1.6;
            color: #6b5d5d;
        }

        .meet-devs-section {
            background:
                radial-gradient(circle at top, rgba(248, 198, 43, 0.18), transparent 28%),
                linear-gradient(180deg, #fff 0%, #fff8f0 100%);
        }

        .meet-devs-grid {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .dev-card {
            position: relative;
            min-height: 380px;
            border-radius: 26px;
            overflow: hidden;
            border: 1px solid rgba(143, 17, 17, 0.1);
            box-shadow: 0 16px 34px rgba(143, 17, 17, 0.12);
            background: linear-gradient(180deg, #fff 0%, #fff6ef 100%);
            transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
            display: flex;
            flex-direction: column;
        }

        .dev-card:hover {
            transform: translateY(-8px);
            border-color: rgba(143, 17, 17, 0.18);
            box-shadow: 0 24px 46px rgba(143, 17, 17, 0.18);
        }

        .dev-card-media {
            position: relative;
            height: 300px;
            overflow: hidden;
            background: linear-gradient(135deg, #6f0707 0%, #8f1111 45%, #f8c62b 100%);
        }

        .dev-card-media::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 30% 25%, rgba(255, 255, 255, 0.28), transparent 18%),
                radial-gradient(circle at 70% 25%, rgba(255, 255, 255, 0.18), transparent 16%),
                radial-gradient(circle at 50% 70%, rgba(255, 255, 255, 0.12), transparent 32%);
            mix-blend-mode: screen;
            opacity: 0.8;
        }

        .dev-avatar {
            position: absolute;
            inset: 18px 18px 0;
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.18);
            border: 4px solid rgba(255, 255, 255, 0.24);
            border-radius: 22px 22px 0 0;
            overflow: hidden;
            background:
                radial-gradient(circle at 50% 32%, rgba(255, 255, 255, 0.95) 0 13%, transparent 14%),
                radial-gradient(circle at 50% 52%, rgba(255, 255, 255, 0.94) 0 24%, transparent 25%),
                radial-gradient(circle at 50% 52%, rgba(255, 224, 179, 0.36) 0 40%, transparent 41%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.04));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dev-avatar::before {
            content: attr(data-initials);
            width: 110px;
            height: 110px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #fff;
            background: linear-gradient(180deg, rgba(143, 17, 17, 0.96) 0%, rgba(95, 11, 11, 0.96) 100%);
            box-shadow: 0 16px 30px rgba(95, 11, 11, 0.34);
        }

        .dev-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            display: block;
        }

        .dev-avatar.has-photo::before {
            display: none;
        }

        .dev-avatar.kenji::after,
        .dev-avatar.kian::after,
        .dev-avatar.rica::after {
            content: "";
            position: absolute;
            inset: 16px;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            pointer-events: none;
        }

        .dev-avatar.kenji {
            background:
                radial-gradient(circle at 50% 26%, rgba(255, 255, 255, 0.96) 0 12%, transparent 13%),
                radial-gradient(circle at 50% 48%, rgba(255, 255, 255, 0.92) 0 23%, transparent 24%),
                linear-gradient(180deg, rgba(143, 17, 17, 0.2), rgba(248, 198, 43, 0.18)),
                linear-gradient(135deg, #8f1111 0%, #f8c62b 100%);
        }

        .dev-avatar.kian {
            background:
                radial-gradient(circle at 50% 26%, rgba(255, 255, 255, 0.96) 0 12%, transparent 13%),
                radial-gradient(circle at 50% 48%, rgba(255, 255, 255, 0.92) 0 23%, transparent 24%),
                linear-gradient(180deg, rgba(15, 118, 110, 0.18), rgba(248, 198, 43, 0.16)),
                linear-gradient(135deg, #0f766e 0%, #f8c62b 100%);
        }

        .dev-avatar.rica {
            background:
                radial-gradient(circle at 50% 26%, rgba(255, 255, 255, 0.96) 0 12%, transparent 13%),
                radial-gradient(circle at 50% 48%, rgba(255, 255, 255, 0.92) 0 23%, transparent 24%),
                linear-gradient(180deg, rgba(180, 83, 9, 0.18), rgba(248, 198, 43, 0.16)),
                linear-gradient(135deg, #b45309 0%, #8f1111 100%);
        }

        .dev-card-content {
            padding: 18px 20px 22px;
            text-align: center;
        }

        .dev-name {
            margin: 0;
            font-size: 1.2rem;
            line-height: 1.2;
            color: #3b1111;
            font-weight: 700;
        }

        .dev-position {
            margin: 8px 0 0;
            font-size: 14px;
            line-height: 1.5;
            color: #6b5d5d;
            font-weight: 500;
        }

        .contact-highlight.location i {
            background: linear-gradient(180deg, #f59e0b 0%, #b45309 100%);
        }

        .contact-highlight.contact i {
            background: linear-gradient(180deg, #0f766e 0%, #115e59 100%);
        }

        /* --- Contact / social icons --- */
        .contact-card {
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            flex-direction: column;
            padding: 28px 30px;
            border-radius: 22px;
            background:
                linear-gradient(135deg, rgba(143, 17, 17, 0.96) 0%, rgba(95, 11, 11, 0.98) 60%, rgba(184, 134, 11, 0.92) 100%);
            border: 1px solid rgba(248, 198, 43, 0.24);
            box-shadow: 0 20px 40px rgba(95, 11, 11, 0.18);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .contact-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 48px rgba(95, 11, 11, 0.24);
        }

        .contact-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.16), transparent 28%);
            pointer-events: none;
        }

        .contact-card-text h3 {
            margin: 0 0 6px;
            font-size: 18px;
            color: #fff7de;
            position: relative;
            z-index: 1;
        }

        .contact-card-text p {
            margin: 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.86);
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 1;
        }

        .contact-card-text,
        .social-links {
            position: relative;
            z-index: 1;
        }

        .social-links {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
            justify-content: center;
            flex-wrap: wrap;
        }

        .social-icon {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            color: #fff;
            font-size: 20px;
            text-decoration: none;
            background: linear-gradient(180deg, #fff7de 0%, #ffe2b0 100%);
            color: #5e0b0b;
            box-shadow: 0 12px 22px rgba(0, 0, 0, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .social-icon::after {
            content: attr(data-label);
            position: absolute;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%) translateY(4px);
            white-space: nowrap;
            background: #2f0606;
            color: #fff7e8;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 5px 10px;
            border-radius: 8px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .social-icon:hover,
        .social-icon:focus-visible {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.55);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.22);
        }

        .social-icon:hover::after,
        .social-icon:focus-visible::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .social-icon.facebook {
            background: linear-gradient(180deg, #ffffff 0%, #cfe0ff 100%);
        }

        .social-icon.gmail {
            background: linear-gradient(180deg, #fff4db 0%, #ffd07a 100%);
        }

        @media (max-width: 640px) {
            .contact-highlights {
                grid-template-columns: 1fr;
            }

            .contact-card {
                padding: 22px;
            }

            .social-links {
                width: 100%;
                justify-content: center;
            }

            .social-icon::after {
                display: none;
            }
        }

        .footer {
            background: linear-gradient(180deg, #2f0606 0%, #180404 100%);
            color: rgba(255, 255, 255, 0.82);
            padding: 28px clamp(18px, 5vw, 48px) 32px;
            border-top: 1px solid rgba(248, 198, 43, 0.2);
        }

        .footer-inner {
            max-width: 1180px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .footer-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
            opacity: 0.9;
        }

        .footer-copy {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
        }

        .footer-copy span {
            color: var(--gold);
            font-weight: 600;
        }

        .footer-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.7);
            flex-wrap: wrap;
        }

        .footer-links {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--gold);
        }

        .footer-divider {
            color: rgba(255, 255, 255, 0.25);
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-mark {
                animation: none;
            }

            .hero-card,
            .hero-title,
            .hero-copy,
            .hero-actions {
                animation: none;
            }

            .reveal,
            .reveal.is-visible,
            .reveal[data-reveal="fade"],
            .reveal[data-reveal="slide"],
            .reveal[data-reveal="bounce"] {
                opacity: 1;
                transform: none;
                animation: none;
            }

            .nav a,
            .nav a::after {
                transition: none;
            }
        }

        @media (max-width: 980px) {
            .topbar-inner {
                flex-wrap: wrap;
                justify-content: center;
                border-radius: 18px;
            }

            .nav {
                order: 3;
                margin-left: 0;
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
                width: 100%;
            }

            .feature-grid,
            .support-grid {
                grid-template-columns: 1fr;
            }

            .meet-devs-grid {
                grid-template-columns: 1fr;
            }

            .contact-highlights {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .topbar-inner {
                padding: 12px 14px;
            }

            .topbar {
                padding: 8px 0;
            }

            .brand img {
                width: 50px;
                height: 50px;
            }

            .nav {
                gap: 8px;
                padding: 6px;
            }

            .nav a {
                font-size: 13px;
                padding: 9px 12px;
            }

            .portal-btn {
                width: 100%;
                min-width: 0;
            }

            .hero {
                min-height: calc(100vh - 122px);
                padding: 34px 16px 42px;
            }

            .hero-card {
                padding: 10px 0;
            }

            .hero-mark {
                width: 108px;
            }

            .hero-copy {
                font-size: 0.98rem;
            }

            .launch-btn {
                width: 100%;
                min-width: 0;
            }

            .section {
                padding: 64px 18px;
            }

            .contact-highlights {
                grid-template-columns: 1fr;
            }

            .dev-card {
                min-height: 360px;
            }

            .dev-card-content {
                padding: 18px;
            }
        }
        .dev-card-media {
    position: relative;
    aspect-ratio: 1 / 1;      /* was: height: 300px */
    width: 100%;
    overflow: hidden;
    background: linear-gradient(135deg, #6f0707 0%, #8f1111 45%, #f8c62b 100%);
}

.dev-card-media::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 30% 25%, rgba(255, 255, 255, 0.28), transparent 18%),
        radial-gradient(circle at 70% 25%, rgba(255, 255, 255, 0.18), transparent 16%),
        radial-gradient(circle at 50% 70%, rgba(255, 255, 255, 0.12), transparent 32%);
    mix-blend-mode: screen;
    opacity: 0.8;
}

.dev-avatar {
    position: absolute;
    inset: 0;                 /* was: inset: 18px 18px 0 */
    border: none;             /* the frame no longer floats inside the card */
    border-radius: 0;         /* was: 22px 22px 0 0 */
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        radial-gradient(circle at 50% 32%, rgba(255, 255, 255, 0.95) 0 13%, transparent 14%),
        radial-gradient(circle at 50% 52%, rgba(255, 255, 255, 0.94) 0 24%, transparent 25%),
        radial-gradient(circle at 50% 52%, rgba(255, 224, 179, 0.36) 0 40%, transparent 41%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.04));
}

.dev-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;  /* was: center top — no longer needed once the frame IS square */
    display: block;
}

/* Drop the fixed min-height now that the card grows from a true square photo */
.dev-card {
    min-height: 0;             /* was: min-height: 380px */
}

/* kenji/kian/rica inner-border accents can go — the photo now fills the whole tile */
.dev-avatar.kenji::after,
.dev-avatar.kian::after,
.dev-avatar.rica::after {
    display: none;
}
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <img src="/images/puplogo.png" alt="InternConnect">
            </div>

            <nav class="nav" aria-label="Primary">
                <a href="#home">Home</a>
                <a href="#Our Services">Our Services</a>
                <a href="#Meet The Developers">Meet The Developers</a>
                <a href="#Get In Touch">Get In Touch</a>
            </nav>

            <a class="portal-btn" href="{{ route('login') }}">
                <i class="fa fa-sign-in-alt"></i>
                Go to Portal
            </a>
        </div>
    </header>

    <main id="home" class="hero">
        <div class="hero-card">
            <img class="hero-mark reveal" data-reveal="bounce" src="/images/WardsLogo.png" alt="InternConnect mark">
            <h1 class="hero-title">Intern<span>Connect</span> - BETA</h1>
            <p class="hero-copy">
                Your digital companion for streamlined OJT document submissions, transparent evaluations, and hassle-free program implementation.
            </p>
        </div>
    </main>

    <section id="Our Services" class="section section-centered">
        <div class="section-inner">
            <div class="section-label reveal" data-reveal="fade"><i class="fa fa-info-circle"></i> Our Services</div>
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

    <section id="Meet The Developers" class="section section-centered meet-devs-section">
        <div class="section-inner">
            <div class="section-label reveal" data-reveal="fade"><i class="fa fa-users"></i> Meet The Developers</div>

            <div class="meet-devs-grid">
                <article class="dev-card reveal" data-reveal="slide" tabindex="0">
                    <div class="dev-card-media">
                        <div class="dev-avatar kenji has-photo" data-initials="KC">
                            <img src="/images/kenji1x1.jpg" alt="Kenji Campos">
                        </div>
                    </div>
                    <div class="dev-card-content">
                        <h3 class="dev-name">Kenji Campos</h3>
                        <p class="dev-position">Full Stack Developer / Project Manager</p>
                    </div>
                </article>

                <article class="dev-card reveal" data-reveal="bounce" tabindex="0">
                    <div class="dev-card-media">
                        <div class="dev-avatar kian has-photo" data-initials="KM">
                            <img src="/images/Kian1x1.jpg" alt="Kian Benedict U. Miguel">
                        </div>
                    </div>
                    <div class="dev-card-content">
                        <h3 class="dev-name">Kian Benedict U. Miguel</h3>
                        <p class="dev-position">Quality Assurance / Frontend Developer</p>
                    </div>
                </article>

                <article class="dev-card reveal" data-reveal="slide" tabindex="0">
                    <div class="dev-card-media">
                        <div class="dev-avatar rica has-photo" data-initials="RS">
                            <img src="/images/Rica1x1.jpg" alt="Rica Genevive Salespara">
                        </div>
                    </div>
                    <div class="dev-card-content">
                        <h3 class="dev-name">Rica Genevive Salespara</h3>
                        <p class="dev-position">Document Analyst / UI UX Designer</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="Get In Touch" class="section alt section-centered contact-section">
        <div class="section-inner">
            <div class="section-label reveal" data-reveal="fade"><i class="fa fa-headset"></i> Get In Touch</div>
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
                <div class="contact-card-text">
                    <h3>Reach us directly</h3>
                    <p>Message us on Facebook or send an email and we'll get back to you as soon as we can.</p>
                </div>
                <div class="social-links">
                    <a
                        class="social-icon facebook"
                        href=""
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Message us on Facebook Messenger"
                        data-label="Facebook"
                    >
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a
                        class="social-icon gmail"
                        href=""
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Send us an email"
                        data-label="Email"
                    >
                        <i class="fa fa-envelope"></i>
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
            </script>
</body>
</html>