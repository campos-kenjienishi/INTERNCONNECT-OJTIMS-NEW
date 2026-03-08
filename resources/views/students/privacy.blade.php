<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect – Privacy Statement</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red:        #dc2626;
            --red-dark:   #991b1b;
            --red-deeper: #7f0000;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
            padding: 0 40px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(127,0,0,0.35);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .header-brand img {
            width: 34px; height: 34px;
            object-fit: contain;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.25));
        }

        .header-brand-name {
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .header-brand-name span { color: #fca5a5; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            background: rgba(255,255,255,0.12);
            border: 1.5px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.22);
            border-color: rgba(255,255,255,0.45);
            color: #fff;
            transform: translateX(-2px);
        }

        .hero {
            background: linear-gradient(160deg, #1a0000 0%, #4a0000 45%, #7f0000 100%);
            padding: 56px 40px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -80px; left: 50%;
            transform: translateX(-50%);
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(220,38,38,0.08);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(220,38,38,0.2);
            border: 1px solid rgba(220,38,38,0.4);
            border-radius: 20px;
            padding: 5px 16px;
            font-size: 11.5px;
            font-weight: 600;
            color: #fca5a5;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 18px;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 38px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .hero h1 span { color: #fca5a5; }

        .hero p {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }

        .hero-meta {
            display: inline-flex;
            align-items: center;
            gap: 20px;
            margin-top: 24px;
            position: relative;
            z-index: 1;
            flex-wrap: wrap;
            justify-content: center;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: rgba(255,255,255,0.45);
        }

        .hero-meta-item i { color: #fca5a5; font-size: 11px; }

        .main-container {
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
            padding: 48px 24px 60px;
            flex: 1;
        }

        .toc-card {
            background: #fff;
            border: 1px solid rgba(220,38,38,0.12);
            border-left: 4px solid var(--red);
            border-radius: 14px;
            padding: 22px 26px;
            margin-bottom: 36px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .toc-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .toc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 8px;
        }

        .toc-link {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #555;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .toc-link:hover { background: #fee2e2; color: var(--red); }

        .toc-num {
            width: 22px; height: 22px;
            border-radius: 6px;
            background: #fee2e2;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: var(--red);
            flex-shrink: 0;
        }

        .terms-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            margin-bottom: 14px;
            overflow: hidden;
            transition: box-shadow 0.25s;
        }

        .terms-section:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.08); }

        .section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 22px;
            border-bottom: 1px solid transparent;
            cursor: pointer;
            user-select: none;
            transition: border-color 0.2s;
        }

        .terms-section.open .section-header { border-bottom-color: #f5f5f5; }

        .section-num {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .section-title {
            flex: 1;
            font-size: 15px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .section-chevron {
            color: #bbb;
            font-size: 13px;
            transition: transform 0.25s;
        }

        .terms-section.open .section-chevron { transform: rotate(180deg); }

        .section-body {
            padding: 0 22px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.3s;
        }

        .terms-section.open .section-body {
            padding: 20px 22px 26px;
            max-height: 3000px;
        }

        .section-body p {
            font-size: 13.5px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 12px;
        }

        .section-body p:last-child { margin-bottom: 0; }

        .section-body h4 {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 16px 0 8px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .section-body h4 i { color: var(--red); font-size: 11px; }

        .section-body ul {
            list-style: none;
            padding: 0;
            margin: 8px 0 14px;
        }

        .section-body ul li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            color: #555;
            line-height: 1.75;
            padding: 6px 0;
            border-bottom: 1px solid #f9f9f9;
        }

        .section-body ul li:last-child { border-bottom: none; }

        .section-body ul li::before {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--red);
            margin-top: 8px;
            flex-shrink: 0;
        }

        .section-body strong { color: #1a1a1a; font-weight: 600; }
        .section-body a { color: var(--red); text-decoration: none; }
        .section-body a:hover { text-decoration: underline; }

        .highlight-box {
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-left: 3px solid var(--red);
            border-radius: 10px;
            padding: 13px 16px;
            margin: 14px 0;
            font-size: 13px;
            color: var(--red-dark);
            line-height: 1.7;
        }

        .highlight-box i { margin-right: 6px; }

        /* Sub-section box */
        .sub-box {
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            padding: 16px 18px;
            margin: 12px 0;
        }

        .sub-box-title {
            font-size: 12px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sub-box-title i { color: var(--red); }

        /* Third-party links grid */
        .tp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .tp-link {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            color: var(--red);
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: #fff;
            transition: all 0.2s;
        }

        .tp-link:hover { background: #fee2e2; border-color: var(--red); }
        .tp-link i { font-size: 13px; width: 16px; text-align: center; }

        .agreement-card {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
            border-radius: 16px;
            padding: 36px 32px;
            text-align: center;
            margin-top: 36px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(185,28,28,0.25);
        }

        .agreement-card::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            pointer-events: none;
        }

        .agreement-card::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }

        .agreement-card h3 {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            position: relative; z-index: 1;
        }

        .agreement-card p {
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 24px;
            max-width: 500px;
            margin-left: auto; margin-right: auto;
            line-height: 1.6;
            position: relative; z-index: 1;
        }

        .btn-agree {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 13px 32px;
            background: #fff;
            border: none;
            border-radius: 12px;
            color: var(--red);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            position: relative; z-index: 1;
        }

        .btn-agree:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            color: var(--red-dark);
        }

        .page-footer {
            background: #fff;
            border-top: 1px solid #f0f0f0;
            color: #aaa;
            padding: 16px 40px;
            font-size: 12.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .page-footer .footer-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-footer .footer-logo {
            width: 20px; height: 20px;
            object-fit: contain;
            opacity: 0.5;
        }

        .page-footer a { color: #aaa; text-decoration: none; transition: color 0.2s; }
        .page-footer a:hover { color: var(--red); }
        .page-footer .divider { color: #e5e5e5; margin: 0 4px; }

        @media (max-width: 640px) {
            .page-header { padding: 0 18px; }
            .hero { padding: 38px 18px 34px; }
            .hero h1 { font-size: 28px; }
            .main-container { padding: 28px 14px 48px; }
            .page-footer { padding: 14px 18px; flex-direction: column; text-align: center; }
            .agreement-card { padding: 26px 18px; }
            .tp-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- ── TOP HEADER ── -->
<header class="page-header">
    <a href="{{ url('/student/home') }}" class="header-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <span class="header-brand-name">Intern<span>Connect</span></span>
    </a>
    <a href="javascript:history.back()" class="btn-back">
    <i class="fa fa-arrow-left"></i> Back
</a>
</header>

<!-- ── HERO ── -->
<div class="hero">
    <div class="hero-badge">
        <i class="fa fa-user-shield"></i> Data Privacy
    </div>
    <h1>Privacy <span>Statement</span></h1>
    <p>Please read this privacy statement carefully to understand how PUP collects, uses, and protects your personal information.</p>
    <div class="hero-meta">
        <div class="hero-meta-item"><i class="fa fa-calendar-alt"></i> Last Updated: June 11, 2018</div>
        <div class="hero-meta-item"><i class="fa fa-university"></i> Polytechnic University of the Philippines</div>
        <div class="hero-meta-item"><i class="fa fa-map-marker-alt"></i> Anonas St., Sta. Mesa, Manila</div>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="main-container">

    <!-- Table of Contents -->
    <div class="toc-card">
        <div class="toc-title"><i class="fa fa-list"></i> Table of Contents</div>
        <div class="toc-grid">
            <a href="#s1"  class="toc-link"><span class="toc-num">1</span>  Collection of Personal Data</a>
            <a href="#s2"  class="toc-link"><span class="toc-num">2</span>  Use of Personal Data</a>
            <a href="#s3"  class="toc-link"><span class="toc-num">3</span>  Sharing Personal Data</a>
            <a href="#s4"  class="toc-link"><span class="toc-num">4</span>  Accessing Your Data</a>
            <a href="#s5"  class="toc-link"><span class="toc-num">5</span>  Cookies & Technologies</a>
            <a href="#s6"  class="toc-link"><span class="toc-num">6</span>  User Accounts</a>
            <a href="#s7"  class="toc-link"><span class="toc-num">7</span>  Security of Data</a>
            <a href="#s8"  class="toc-link"><span class="toc-num">8</span>  Data Storage & Location</a>
            <a href="#s9"  class="toc-link"><span class="toc-num">9</span>  Retention of Data</a>
            <a href="#s10" class="toc-link"><span class="toc-num">10</span> Data from Minors</a>
            <a href="#s11" class="toc-link"><span class="toc-num">11</span> Preview & Beta Releases</a>
            <a href="#s12" class="toc-link"><span class="toc-num">12</span> Enforcement & Changes</a>
            <a href="#s13" class="toc-link"><span class="toc-num">13</span> Contact Information</a>
        </div>
    </div>

    <!-- ── SECTION 1 ── -->
    <div class="terms-section open" id="s1">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">1</div>
            <div class="section-title">Collection of Personal Data</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>PUP collects data to operate effectively and provide you the best experiences with our Services. You provide some of this data directly, such as when you register for an entrance test/exam online, submit a search query to the PUP Website, send us feedback online, upload media to the PUP Media Gallery, participate in the PUP Online Survey, purchase a book from the PUP Bookstore, sign up for PUP WebMail or Office 365, or contact us for inquiries and technical support. We get some of it by recording how you interact with our Services by, for example, using technologies like cookies, and receiving error reports or usage data from Services running on your device.</p>

            <h4><i class="fa fa-database"></i> Third-Party Data Sources</h4>
            <p>We also obtain data from third parties. These third-party sources vary over time, but have included:</p>
            <ul>
                <li>Social networks when you grant permission to PUP Services to access your data on one or more networks</li>
                <li>Service providers that help us determine a location based on your IP address in order to customize certain services to your location</li>
                <li>Partners with which we offer co-branded services or engage in joint research activities</li>
                <li>Publicly-available sources such as open government databases or other data in the public domain</li>
            </ul>

            <div class="highlight-box">
                <i class="fa fa-info-circle"></i>
                You have choices about the data we collect. When you are asked to provide personal data, you may decline. But if you choose not to provide data that is necessary to provide a service or feature, you may not be able to use the Services.
            </div>

            <h4><i class="fa fa-list-ul"></i> Types of Data Collected</h4>
            <ul>
                <li><strong>Name and Contact Information</strong> — First, middle and last name, name prefixes and suffixes per PSA birth certificate, email address, postal and mailing address, phone and mobile number/s, and other similar contact information.</li>
                <li><strong>Credentials</strong> — Student numbers, passwords, password hints, and similar security information used for authentication and account access.</li>
                <li><strong>Demographic Data</strong> — Date of birth, age, sex, country, religion, written and spoken language/s, and occupation (if applicable).</li>
                <li><strong>Payment Data</strong> — Payment instrument numbers and security codes for online fee transactions. The University uses LANDBANK's Electronic Payment System for PUP Online Payment.</li>
                <li><strong>Service Use Data</strong> — Features you use, items you purchase, web pages visited inside the Services, and search queries or commands used in the Services.</li>
                <li><strong>Device, Connectivity & Configuration Data</strong> — Operating systems, browser type, IP address, device identifiers, regional and language settings.</li>
                <li><strong>Error Reports & Performance Data</strong> — Problem type, severity, details related to an error, and data about other software on your device to help diagnose and improve the Services.</li>
                <li><strong>Technical Support Data</strong> — Hardware/software details, communications content, and system configuration data collected during support sessions.</li>
                <li><strong>Content</strong> — Messages, feedback, reviews, and questions you send to us. Sessions with our offices/employees may be monitored and recorded.</li>
                <li><strong>Office 365 Data</strong> — Subject lines and bodies of emails, text/content of instant messages, audio/video recordings of video messages, and transcripts of voice messages when using Office 365/Outlook/Skype.</li>
                <li><strong>Physical Appearance</strong> — Image captured by security cameras on University premises; recent photo required for exam registration and PUP ID applications.</li>
            </ul>
        </div>
    </div>

    <!-- ── SECTION 2 ── -->
    <div class="terms-section" id="s2">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">2</div>
            <div class="section-title">Use of Personal Data</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>PUP uses the data we collect for three (3) fundamental purposes: to operate as a higher education institution (HEI) and provide the services we offer, to send communication including informative communication, and to promote the University whether in our Services or in third-party services supported by advertising.</p>
            <p>In carrying out these purposes, we combine data we collect to give you a more seamless, consistent and customized experience. However, to enhance privacy, we have safety measures designed to prevent certain data combinations.</p>

            <h4><i class="fa fa-cogs"></i> Providing and Improving Our Services</h4>
            <ul>
                <li><strong>Providing the Services</strong> — We use data to carry out your transactions with the University and to provide our Services to you.</li>
                <li><strong>Technical Support</strong> — We use data to diagnose problems in the Services, repair the problem, and provide other customer care and support services.</li>
                <li><strong>Account Activation/Deactivation</strong> — We use data, including subscription and status identifiers, for user accounts that require activation/deactivation.</li>
                <li><strong>Improvement of Services</strong> — We use data to continually improve our Services, including adding new capabilities or features, using error reports to improve processes, and using feedback to improve content accuracy.</li>
                <li><strong>Security and Safety</strong> — We use data to protect the security and safety of our Services, detect and prevent fraud, confirm the validity of user accounts, resolve disputes, and enforce our policies.</li>
                <li><strong>University Operations</strong> — We use data to make aggregate analysis and business intelligence that enable the University to operate, protect, make informed decisions, and report on the performance of our Services.</li>
                <li><strong>Communications</strong> — We use data to deliver and personalize communications — such as announcements, advisories, system updates, and support follow-ups — specific to your user type.</li>
                <li><strong>Advertising</strong> — Some Services are supported by advertising. Third-party providers such as Office 365, Facebook, Twitter, and YouTube may use shared data to deliver interest-based ads. PUP does not use what you say in email, chat, video calls, voicemail, or your documents, photos, or personal files to target ads to you.</li>
            </ul>

            <div class="highlight-box">
                <i class="fa fa-ban"></i>
                You can opt out of receiving interest-based advertising from third parties by visiting their respective websites. Third-party services linked from our Services may have different privacy practices.
            </div>
        </div>
    </div>

    <!-- ── SECTION 3 ── -->
    <div class="terms-section" id="s3">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">3</div>
            <div class="section-title">Rationale in Sharing Personal Data</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>We share your personal data with your consent or when necessary to complete any transaction or provide services you have requested or authorized — for example, when you send an email using Office 365, share photos and documents on OneDrive/SharePoint, or link accounts with third-party services. When you provide data to pay your tuition or application fee using PUP Online Payment, we will share that data with our partner bank/s and other entities that process online payment transactions.</p>
            <p>We also share personal data with partners working on our behalf — including technology support providers and security firms — who must abide by our data privacy and security requirements and are not allowed to use personal data they receive from us for any other purpose.</p>

            <h4><i class="fa fa-exclamation-triangle"></i> We May Also Disclose Personal Data To:</h4>
            <ul>
                <li>Comply with applicable law or respond to valid legal process, including from law enforcement or other government agencies</li>
                <li>Protect our stakeholders — for example, to prevent spam or attempts to defraud users of third-party products or services, or to help prevent the loss of life or serious injury of anyone</li>
                <li>Operate and maintain the security of our Services, including prevention or stopping an attack on our computer systems, networks, or IT infrastructure</li>
                <li>Protect the rights or property of PUP, including enforcing the terms governing the use of the Services</li>
            </ul>

            <div class="sub-box">
                <div class="sub-box-title"><i class="fa fa-external-link-alt"></i> Third-Party Privacy Statements</div>
                <p style="font-size:12.5px;color:#888;margin-bottom:10px;">If you provide personal data to third-party services, your data is governed by their privacy statements:</p>
                <div class="tp-grid">
                    <a href="https://www.facebook.com/policy.php" target="_blank" class="tp-link"><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook</a>
                    <a href="https://policies.google.com/privacy" target="_blank" class="tp-link"><i class="fab fa-google" style="color:#ea4335;"></i> Google</a>
                    <a href="https://help.instagram.com/519522125107875" target="_blank" class="tp-link"><i class="fab fa-instagram" style="color:#e1306c;"></i> Instagram</a>
                    <a href="https://products.office.com/en/business/office-365-trust-center-privacy" target="_blank" class="tp-link"><i class="fab fa-microsoft" style="color:#0078d4;"></i> Office 365</a>
                    <a href="https://privacy.microsoft.com/" target="_blank" class="tp-link"><i class="fa fa-cloud" style="color:#0078d4;"></i> OneDrive</a>
                    <a href="https://twitter.com/en/privacy" target="_blank" class="tp-link"><i class="fab fa-twitter" style="color:#1da1f2;"></i> Twitter</a>
                    <a href="https://privacy.userreport.com/" target="_blank" class="tp-link"><i class="fa fa-chart-bar" style="color:#888;"></i> UserReport</a>
                    <a href="https://www.youtube.com/yt/about/policies/" target="_blank" class="tp-link"><i class="fab fa-youtube" style="color:#ff0000;"></i> YouTube</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── SECTION 4 ── -->
    <div class="terms-section" id="s4">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">4</div>
            <div class="section-title">Accessing Your Personal Data</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>You may access and request updates to your personal data through the following University systems. A valid PUP ID and required supporting documents are required.</p>
            <ul>
                <li><strong>PUP Student Information System (SIS)</strong> — Go to the PUP Office of the University Registrar or PUP Branch/Campus Registrar to have your personal information updated.</li>
                <li><strong>PUP Human Resource Information System (HRIS)</strong> — Go to the PUP HRIS 201 module to update your information. All changes undergo validation and approval from your supervisor and the PUP HRMD.</li>
                <li><strong>PUP Online Document Request System (ODRS)</strong> — Go to the PUP ODRS profile module to have your personal information updated.</li>
                <li><strong>PUP iApply</strong> — For PUP Sta. Mesa: go to the PUP ICT Office Helpdesk. For PUP Branches and Campuses: go to the Office of the Branch/Campus Registrar and Admissions. Present a valid ID and required supporting documents.</li>
                <li><strong>Office 365</strong> — View and control activity data across Office 365 services at <a href="https://portal.office.com/account" target="_blank">portal.office.com/account</a>, including profile, payment info, and browsing/search/location data.</li>
            </ul>

            <h4><i class="fa fa-desktop"></i> Browser-Based Controls</h4>
            <ul>
                <li><strong>Cookie Controls</strong> — Relevant browser-based cookie controls are described in the Cookies section of this privacy statement.</li>
                <li><strong>Protection from Tracking</strong> — Most modern Web browsers allow you to block third-party content, including cookies, from any site, limiting what those sites can collect about you.</li>
                <li><strong>Do Not Track (DNT)</strong> — Most modern Web browsers have "Do Not Track" (DNT) features that can send a signal to websites you visit indicating you do not wish to be tracked.</li>
            </ul>

            <div class="highlight-box">
                <i class="fa fa-user-lock"></i>
                For InternConnect-specific data concerns, update your information directly from your <strong>Account Settings</strong> page, or contact your OJT Coordinator.
            </div>
        </div>
    </div>

    <!-- ── SECTION 5 ── -->
    <div class="terms-section" id="s5">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">5</div>
            <div class="section-title">Cookies & Similar Technologies</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The Services use cookies (small text files placed on your device) and similar technologies to provide information to our Web sites and Services, and to help collect data. Third-party technologies use other identifiers for similar purposes, and many of our Services and third-party providers also contain web beacons or other similar technologies.</p>

            <h4><i class="fa fa-cookie-bite"></i> Our Use of Cookies and Similar Technologies</h4>
            <ul>
                <li><strong>Sign-in and Authentication</strong> — When you sign into the Services using your PUP account, we store a unique ID number and the time you signed in in an encrypted cookie on your device, allowing you to move from page to page without signing in again.</li>
                <li><strong>Remembering Preferences and Settings</strong> — Settings that enable our Services to operate properly or maintain your preferences over time may be stored on your device in a cookie until you clear browsing data.</li>
                <li><strong>Interest-Based Advertising</strong> — Third-party providers use cookies to collect data about your online activity and identify your interests so that provided ads are most relevant to you. You can opt out through their privacy statement.</li>
                <li><strong>Analytics</strong> — Cookies and other identifiers are used to gather usage and performance data, such as counting unique visitors and generating statistics about the operations of the Services. Opt out of Google Analytics at <a href="https://tools.google.com/dlpage/gaoptout" target="_blank">tools.google.com/dlpage/gaoptout</a>.</li>
                <li><strong>Flash Cookies (Local Shared Objects)</strong> — Adobe Flash technologies may use Local Shared Objects to store data on your device. Manage them via <a href="https://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager.html" target="_blank">Macromedia Flash Settings Manager</a>.</li>
            </ul>

            <h4><i class="fa fa-eye"></i> Web Beacons and Analytics Services</h4>
            <p>Third-party providers may contain electronic images known as web beacons (single-pixel gifs) used to help deliver cookies, count visitors, and deliver co-branded services. They are also included in promotional email messages to determine whether you open and act on them. Analytics providers are prohibited from using web beacons on our Services to collect information that directly identifies you.</p>

            <div class="highlight-box">
                <i class="fa fa-exclamation-triangle"></i>
                Disabling cookies may prevent you from signing in or using certain features of the Services. Instructions for blocking or deleting cookies are available at <a href="https://www.wikihow.com/Disable-Cookies" target="_blank">wikihow.com/Disable-Cookies</a>.
            </div>
        </div>
    </div>

    <!-- ── SECTION 6 ── -->
    <div class="terms-section" id="s6">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">6</div>
            <div class="section-title">User Accounts</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>With a user account, you can sign into the Services. Signing into your account enables access to specific services and permits you to access and use third-party services. When you sign into your account to access a Service, that is recorded and maintained.</p>
            <ul>
                <li><strong>Creating Your Account</strong> — You will be asked for certain personal data and required documents as proof of your current status in PUP (currently employed or enrolled). A unique ID number will be generated to identify your account. Services require a real and complete name.</li>
                <li><strong>Sign-in Records</strong> — When you sign in, a record is created including the date and time, the Service you signed into, your sign-in name, the unique number assigned to your account, a unique device identifier, your IP address, and your operating system and Web browser version.</li>
                <li><strong>Office 365 Sign-in</strong> — Enables personalization, seamless experiences across devices, cloud data storage access, and payment via stored instruments. You remain signed in until you sign out. Some products will display your name or username and profile photo as part of your use of Office 365 products.</li>
                <li><strong>Third-Party Sign-in</strong> — If you sign into a third-party service using your PUP account, you will be asked to consent to share account data required by that service. The third-party can use or share data it receives according to its own practices and policies.</li>
                <li><strong>Social Network Connections</strong> — You may connect your PUP account to accounts on social networks such as Facebook, Twitter, or LinkedIn. If you do so, Office 365 will store data about your social network accounts on their servers.</li>
            </ul>
            <div class="highlight-box">
                <i class="fa fa-lock"></i>
                Sharing of user accounts and passwords is strictly prohibited. You are entirely responsible for all activities that occur within your account. Notify the PUP ICT Office immediately of any unauthorized use or breach of security.
            </div>
        </div>
    </div>

    <!-- ── SECTION 7 ── -->
    <div class="terms-section" id="s7">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">7</div>
            <div class="section-title">Security of Personal Data</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>PUP is committed to protecting the security of your personal data. We use a variety of security technologies and procedures to help protect your personal data from unauthorized access, use or disclosure:</p>
            <ul>
                <li>Personal data is stored on computer systems that have limited access and are in controlled facilities</li>
                <li>Highly confidential data (such as your name or password) is protected through the use of encryption when transmitted over the Internet</li>
                <li>Services have the capability to identify abusive actions and may block the user and/or remove content if it violates our Terms</li>
                <li>Fraud detection and account validity checks are used to protect all users of the Services</li>
            </ul>
            <div class="highlight-box">
                <i class="fa fa-shield-alt"></i>
                While we implement strong safeguards, no system is completely secure. Use a strong, unique password and always log out after each session, especially on shared devices.
            </div>
        </div>
    </div>

    <!-- ── SECTION 8 ── -->
    <div class="terms-section" id="s8">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">8</div>
            <div class="section-title">Location Where Personal Data Is Stored & Processed</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>Personal data collected by PUP through the Services are stored and processed in the University data center, in your region or in any other country where PUP or its service providers maintain facilities.</p>
            <ul>
                <li>The primary storage location of your personal data is typically in the <strong>Philippines</strong>, often with a backup to a data center in another region</li>
                <li>Office 365 maintains data centers in the United States, Canada, Brazil, Ireland, the Netherlands, Austria, Finland, India, Singapore, Malaysia, Hong Kong, Japan, and Australia</li>
                <li>Storage locations are chosen by Office 365 to operate efficiently, improve performance, and create redundancies to protect data in the event of an outage or other problem</li>
                <li>All data is processed according to the provisions of this privacy statement and the requirements of applicable law</li>
            </ul>
            <p>When applicable, if third-party providers transfer personal data to other countries, they might use a variety of legal mechanisms, including contracts, to help ensure protections travel with your data.</p>
        </div>
    </div>

    <!-- ── SECTION 9 ── -->
    <div class="terms-section" id="s9">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">9</div>
            <div class="section-title">Retention of Personal Data</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The University retains personal data for as long as necessary to provide the services and fulfill the transactions you have requested or may request in the future, or for other essential purposes such as complying with our commitments, legal obligations, resolving disputes, and enforcing our agreements. The criteria used to determine the retention periods include:</p>
            <ul>
                <li><strong>Service Necessity</strong> — How long is the personal data needed to provide the services operated by the University? This includes maintaining and improving performance, keeping our systems secure, and maintaining appropriate academic, student, and financial records.</li>
                <li><strong>User-Maintained Data</strong> — Do users provide, create, or maintain the data with the expectation we will retain it until they affirmatively remove it? Examples include a document stored in OneDrive or an email message kept in your Office 365 Outlook inbox. Deleted items remain in their system for up to 30 days before final deletion.</li>
                <li><strong>Legal Obligations</strong> — Is PUP subject to a legal, contractual, or similar obligation to retain the data? Examples include mandatory data retention laws in the Philippines, Government orders to preserve data relevant to an investigation, or data that must be retained for the purposes of due process.</li>
            </ul>
        </div>
    </div>

    <!-- ── SECTION 10 ── -->
    <div class="terms-section" id="s10">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">10</div>
            <div class="section-title">Collection of Data from Minors</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The University collects data from applicants or users below 18 years of age, which is necessary to provide the Services (particularly for Junior and Senior High School).</p>
            <ul>
                <li>Minor accounts are treated much like any other account</li>
                <li>The minor may have access to communication services like email, instant messaging and online message boards</li>
                <li>Minors may be able to communicate freely with other users of all ages within the Services</li>
            </ul>
            <div class="highlight-box">
                <i class="fa fa-child"></i>
                As required by Philippine law, users below eighteen (18) years of age must obtain <strong>parental consent</strong> before engaging with any transactions through the Services.
            </div>
        </div>
    </div>

    <!-- ── SECTION 11 ── -->
    <div class="terms-section" id="s11">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">11</div>
            <div class="section-title">Preview & Beta Releases</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The Services offer preview, beta or other pre-released versions and features ("Previews") to enable you to evaluate them while providing feedback, including performance and usage data, to the University or the third-party provider.</p>
            <ul>
                <li>Previews can automatically collect additional data beyond what is collected in standard Services</li>
                <li>Previews may provide fewer controls and otherwise employ different privacy and security measures than those typically present in the Services</li>
                <li>If you participate in previews, we may contact you about your feedback or your interest in continuing to use the particular online service after general release</li>
            </ul>
        </div>
    </div>

    <!-- ── SECTION 12 ── -->
    <div class="terms-section" id="s12">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">12</div>
            <div class="section-title">Enforcement & Changes to This Privacy Statement</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>In our quest to uphold our commitment to protecting the privacy of your Personal Information, the University discloses its information practices, and to have its privacy practices reviewed for compliance.</p>
            <p>We will update this privacy statement when necessary. When we make changes to this statement, we will revise the "last updated" date at the top of the statement. For material changes to the statement, or in how PUP will use personal data, we will post notifications in the PUP Website and official PUP social media channels.</p>
            <div class="highlight-box">
                <i class="fa fa-sync-alt"></i>
                We encourage you to periodically review this privacy statement to learn how the University is protecting your information.
            </div>
        </div>
    </div>

    <!-- ── SECTION 13 ── -->
    <div class="terms-section" id="s13">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">13</div>
            <div class="section-title">Contact Information</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The University welcomes your input and suggestions regarding this privacy statement, or if you have a data privacy concern or a question for the PUP Data Privacy Officer, please contact us by e-mail.</p>
            <div class="sub-box">
                <div class="sub-box-title"><i class="fa fa-building"></i> PUP Online Services Privacy Statement</div>
                <ul style="margin:0;">
                    <li><strong>Institution</strong> — Polytechnic University of the Philippines</li>
                    <li><strong>Office</strong> — Information and Communications Technology Office</li>
                    <li><strong>Email</strong> — <a href="mailto:dataprivacy@pup.edu.ph">dataprivacy@pup.edu.ph</a></li>
                    <li><strong>Legal Concerns</strong> — <a href="mailto:legal@pup.edu.ph">legal@pup.edu.ph</a> (Office of the Chief Legal Counsel)</li>
                    <li><strong>Website</strong> — <a href="https://www.pup.edu.ph" target="_blank">www.pup.edu.ph</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Agreement Card -->
    <div class="agreement-card">
        <h3>Your Privacy Matters to Us</h3>
        <p>We are committed to handling your personal information responsibly and transparently in accordance with the Philippine Data Privacy Act of 2012.</p>
        <a href="javascript:history.back()" class="btn-agree">
    <i class="fa fa-home"></i> Back to Dashboard
        </a>
    </div>

</div>

<!-- ── PAGE FOOTER ── -->
<footer class="page-footer">
    <div class="footer-left">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
        <span>© 1998–2026 <a href="https://www.pup.edu.ph/" target="_blank">Polytechnic University of the Philippines</a></span>
    </div>
    <div>
        <a href="{{ url('/terms') }}">Terms of Use</a>
        <span class="divider">|</span>
        <a href="{{ url('/privacy') }}">Privacy Statement</a>
    </div>
</footer>

<script>
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
</script>

</body>
</html>