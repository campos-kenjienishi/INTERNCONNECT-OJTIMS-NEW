<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect – Terms of Use</title>
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
        <i class="fa fa-shield-alt"></i> Legal Information
    </div>
    <h1>Terms of <span>Use</span></h1>
    <p>Please read these terms carefully before using the InternConnect OJT Information Management System and all PUP online services.</p>
    <div class="hero-meta">
        <div class="hero-meta-item"><i class="fa fa-calendar-alt"></i> Last Updated: July 4, 2018</div>
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
            <a href="#s1"  class="toc-link"><span class="toc-num">1</span>  Acceptance of Terms</a>
            <a href="#s2"  class="toc-link"><span class="toc-num">2</span>  Description of Services</a>
            <a href="#s3"  class="toc-link"><span class="toc-num">3</span>  Limitation of Use</a>
            <a href="#s4"  class="toc-link"><span class="toc-num">4</span>  No Unlawful Use</a>
            <a href="#s5"  class="toc-link"><span class="toc-num">5</span>  Use of Services</a>
            <a href="#s6"  class="toc-link"><span class="toc-num">6</span>  Software via Services</a>
            <a href="#s7"  class="toc-link"><span class="toc-num">7</span>  Documents via Services</a>
            <a href="#s8"  class="toc-link"><span class="toc-num">8</span>  User Account & Security</a>
            <a href="#s9"  class="toc-link"><span class="toc-num">9</span>  Materials Posted to PUP</a>
            <a href="#s10" class="toc-link"><span class="toc-num">10</span> Copyright Infringement</a>
            <a href="#s11" class="toc-link"><span class="toc-num">11</span> Links to Other Sites</a>
            <a href="#s12" class="toc-link"><span class="toc-num">12</span> Unsolicited Ideas</a>
        </div>
    </div>

    <!-- ── SECTION 1 ── -->
    <div class="terms-section open" id="s1">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">1</div>
            <div class="section-title">Acceptance of Terms</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The online services that PUP provides to you are subject to the following Terms of Use ("Terms"). This is an agreement between you (either an individual or a single entity) and the University. By visiting, browsing and/or interacting with our online services, you agree to be bound by this Terms.</p>
            <p>PUP reserves the right to update the Terms at any time without notice to you. The most current version of the Terms can be viewed by clicking on the "Terms of Use" hypertext link located at the bottom of our Web pages and online services.</p>
            <p>The University offers various online services wherein additional terms or requirements may apply. As such, these terms will be available to relevant online services, and those additional terms become part of your agreement with the University if you use those online services.</p>
            <div class="highlight-box">
                <i class="fa fa-exclamation-circle"></i>
                <strong>Important:</strong> By using PUP online services, you are agreeing to these terms. If you do not agree, please discontinue use immediately.
            </div>
        </div>
    </div>

    <!-- ── SECTION 2 ── -->
    <div class="terms-section" id="s2">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">2</div>
            <div class="section-title">Description of Services</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The University provides you access to its online services, including its official Website (www.pup.edu.ph), portals (intranet and learning), apps (Web, mobile and desktop), social media channels, associated media, digital materials, online/electronic documentation, and University information (collectively referred to as "Services").</p>
            <p>The Services, its updates, enhancements, new features, and/or the addition of any new online service are subject to this Terms.</p>
        </div>
    </div>

    <!-- ── SECTION 3 ── -->
    <div class="terms-section" id="s3">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">3</div>
            <div class="section-title">Limitation of Use</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The Services are for your academic, non-profit and non-commercial use. PUP grants you conditional access to visit, browse, and interact with the Services as long as you abide with this Terms on your computers running validly licensed copies of software for which the Services were designed.</p>
            <p>The information including images and photos contained within the Services ("Information") are provided by PUP and may be used for informational purposes only. You may not copy, modify, distribute, transmit, display, reproduce, publish, license, develop derivative/creative works from, transfer, or sell any Information, forms or documentation obtained from the Services.</p>
            <p>PUP and its associated symbols, consisting the logo and the typeface, should follow the standard. It must not be altered in any way, such as in layout, shape, font, font style, position or color.</p>
            <div class="highlight-box">
                <i class="fa fa-info-circle"></i>
                When written permission to reproduce materials is granted, PUP should be acknowledged as the source of the materials.
            </div>
            <p>If any component of the Service is marked "Preview", "Pre-released", "Beta", "Currently being updated", "To be Updated" or "Under Construction", that component constitutes pre-release Information and may be changed substantially before its formal release.</p>
        </div>
    </div>

    <!-- ── SECTION 4 ── -->
    <div class="terms-section" id="s4">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">4</div>
            <div class="section-title">No Unlawful or Prohibited Use of the Services</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>As a condition of your use of the Services, you will not use the Services for any purpose that is unlawful or prohibited by these Terms, conditions, and notices. You may not use the Services in any manner that could damage, disable, overburden, or impair any PUP server, or the network(s) connected to any PUP server, or interfere with any other party's use and enjoyment of any Services.</p>
            <ul>
                <li>You must not attempt to gain unauthorized access to any Services, other accounts, computer systems or networks connected to any PUP server through hacking, password mining or any other means</li>
                <li>You may not obtain or attempt to obtain any materials or information through any means not intentionally made available through the Services</li>
                <li>You must not infiltrate, reverse engineer, decompile, or disassemble the Services or any of its components</li>
                <li>You must not use any special hardware or software to download all the files from the Services</li>
            </ul>
        </div>
    </div>

    <!-- ── SECTION 5 ── -->
    <div class="terms-section" id="s5">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">5</div>
            <div class="section-title">Use of Services (Communication Services)</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The Services may contain e-mail features, forum and bulletin board, messaging, virtual communities, portals, calendars, media galleries, social media channels, document and communication management systems, and/or other message or communication facilities designed to enable you to communicate with others (each referred to as "Communication Service").</p>
            <p>The University adheres to the basic principles of human decency, respect and values. We have zero tolerance for messages and comments that curse, trash, degrade, humiliate and intimidate.</p>

            <h4><i class="fa fa-ban"></i> When using the Communication Services, you will NOT:</h4>
            <ul>
                <li>Use the Communication Services in connection with surveys, contests, pyramid schemes, chain letters, junk email, spamming or any duplicative or unsolicited messages</li>
                <li>Defame, abuse, harass, insult, curse, stalk, threaten or otherwise violate the legal rights of others</li>
                <li>Disrespect the views of others or engage in mudslinging and flaming</li>
                <li>Publish, post, or disseminate any inappropriate, political, profane, defamatory, obscene, vulgar, inaccurate, harassing, hateful, threatening, indecent or unlawful topic, name, material or information</li>
                <li>Upload files that contain images, photographs, or software protected by intellectual property laws unless you own or control the rights</li>
                <li>Use any material or information in any manner that infringes any copyright, trademark, patent, trade secret, or other proprietary right</li>
                <li>Upload files that contain viruses, Trojan horses, worms, time bombs, cancelbots, corrupted files, malware, or ransomware</li>
                <li>Advertise or offer to sell or buy any goods or services for any business or commercial purpose</li>
                <li>Download any file posted by another user that cannot be legally reproduced, displayed, performed, and/or distributed</li>
                <li>Falsify or delete any copyright management information, author attributions, or proprietary designations</li>
                <li>Restrict or inhibit any other user from using and enjoying the Communication Services</li>
                <li>Violate any code of conduct or other guidelines applicable to any particular Communication Service</li>
                <li>Harvest or otherwise collect information about others, including e-mail addresses</li>
                <li>Violate any applicable laws or regulations</li>
                <li>Create a false identity for the purpose of misleading others and the University</li>
                <li>Type in ALL CAPS, use excessive line spacing, or flood posts with nonsense messages</li>
                <li>Upload unnecessarily large images that ruin the layout or slow down page loading</li>
                <li>Post personal information (email, mobile number, landline number, etc.) of anyone without consent</li>
                <li>Use or register to the Services if you are below eighteen (18) years of age without parental consent</li>
            </ul>

            <div class="highlight-box">
                <i class="fa fa-gavel"></i>
                PUP reserves the right to review, remove, and terminate access to any Communication Services at any time, without notice, for any reason whatsoever.
            </div>

            <p>Always use caution when giving out any personally identifiable information about yourself, your family or your children in any Communication Services. Site managers and hosts are not authorized PUP spokespersons, and their views do not necessarily reflect those of PUP.</p>
        </div>
    </div>

    <!-- ── SECTION 6 ── -->
    <div class="terms-section" id="s6">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">6</div>
            <div class="section-title">Software Available via Services</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>Any software that is made available to download from the Services ("Software") is the copyrighted work of its respective provider and/or its suppliers. Use of the Software is governed by terms in its end user license agreement ("License Agreement"). End-users will be unable to install any Software that has a License Agreement, unless they first agree to the License Agreement terms.</p>
            <ul>
                <li>The Software is made available for download solely for use by end-users according to the License Agreement</li>
                <li>Any reproduction or redistribution not in accordance with the License Agreement is expressly prohibited by law</li>
                <li>Copying or reproduction of the Software to any other server for redistribution is expressly prohibited unless permitted by the License Agreement</li>
                <li>Third-party scripts or code linked from the Services are granted by their respective companies, not by PUP</li>
            </ul>
            <p>The Services and Software are provided with RESTRICTED RIGHTS. Use, duplication, or disclosure by the Philippine Government is subject to restrictions as set forth in its law and constitution, as applicable.</p>
        </div>
    </div>

    <!-- ── SECTION 7 ── -->
    <div class="terms-section" id="s7">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">7</div>
            <div class="section-title">Documents Available via Services</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>The Information, articles, research papers, press releases, statistical data, directory of offices and its officials, reports, forms and FAQs ("Documents") from the Services is owned by PUP and is protected by intellectual property rights and/or copyright, and may not be distributed, modified, or reproduced in whole or in part without prior written permission from the Office of the University President.</p>
            <h4><i class="fa fa-file-alt"></i> When permission to use is granted:</h4>
            <ul>
                <li>PUP should be acknowledged as the source of the materials and the copyright notice must appear in all copies</li>
                <li>Use of such Documents is for informational, non-profit and non-commercial or personal use only</li>
                <li>No modifications of any Documents are made</li>
            </ul>
            <p>Educational institutions duly recognized and accredited by the Government may download and reproduce the Documents for distribution in the classroom. Distribution outside the classroom requires written permission.</p>
            <div class="highlight-box">
                <i class="fa fa-exclamation-triangle"></i>
                Documents specified in this Terms do not include the design or layout of the Services. Elements of the Services are protected by intellectual property, trade dress, trademark, unfair competition, and other laws and may not be copied or imitated in whole or in part.
            </div>
            <p>All such Documents and related graphics are provided "AS IS" WITHOUT WARRANTY OF ANY KIND. PUP hereby DISCLAIM ALL WARRANTIES AND CONDITIONS WITH REGARD TO THIS INFORMATION.</p>
        </div>
    </div>

    <!-- ── SECTION 8 ── -->
    <div class="terms-section" id="s8">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">8</div>
            <div class="section-title">User Account, Password and Security</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>If any of the Services requires you to open an account, you must complete the registration process by providing PUP with current, complete and accurate information as prompted by the applicable online registration form. Most Services may require a username and a password.</p>
            <ul>
                <li>Your username will uniquely identify you from other users and is unchangeable throughout your use of the Services</li>
                <li>You are entirely responsible for maintaining the confidentiality of your password and account</li>
                <li>You are entirely responsible for any and all activities that occur within your account</li>
                <li>You agree to notify the PUP ICT Office immediately of any unauthorized use of your account or any other breach of security</li>
                <li>You may not use anyone else's account at any time</li>
                <li>Sharing of user account and password is strictly prohibited</li>
            </ul>
            <div class="highlight-box">
                <i class="fa fa-lock"></i>
                PUP, ICTO, its developers and third-party providers will not be liable for any loss incurred as a result of someone else using your password or account. However, you could be held liable for losses incurred by PUP or another party due to someone else using your account.
            </div>
        </div>
    </div>

    <!-- ── SECTION 9 ── -->
    <div class="terms-section" id="s9">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">9</div>
            <div class="section-title">Materials Provided to PUP or Posted at Any Services</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>PUP does not claim ownership of the materials you provide to the University (including feedback and suggestions) or post, upload, input or submit to any Services. However, by posting your Submission you are granting PUP permission to use your Submission in connection with the University's operation, including the license rights to:</p>
            <ul>
                <li>Copy, distribute, transmit, publicly display, publicly perform, reproduce, edit, translate and reformat your Submission</li>
                <li>Publish your name in connection with your Submission</li>
                <li>Share such rights to the University's colleges, offices, branches and campuses</li>
            </ul>
            <p>No compensation will be paid with respect to the use of your Submission. PUP is under no obligation to post or use any Submission and may remove any Submission at any time in its sole discretion, without notice.</p>
            <p>By posting images as part of your Submission, you warrant that you are the copyright owner or have obtained all necessary permissions, and you grant PUP a non-exclusive, world-wide, royalty-free license to copy, distribute, transmit, publicly display, reproduce, edit, translate and reformat your Images.</p>
        </div>
    </div>

    <!-- ── SECTION 10 ── -->
    <div class="terms-section" id="s10">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">10</div>
            <div class="section-title">Copyright Infringement</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>PUP respects the intellectual property rights of others. If your copyright or trademark is being infringed, you may contact the PUP Office of the Chief Legal Counsel.</p>
            <div class="highlight-box">
                <i class="fa fa-envelope"></i>
                Contact the PUP Office of the Chief Legal Counsel at <a href="mailto:legal@pup.edu.ph">legal@pup.edu.ph</a> for copyright or trademark infringement concerns.
            </div>
        </div>
    </div>

    <!-- ── SECTION 11 ── -->
    <div class="terms-section" id="s11">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">11</div>
            <div class="section-title">Links to Other Sites</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>Links that are present in the Services will let you leave PUP's site. The linked sites are not under the control of PUP and the University is not responsible for the contents of any linked site or any link contained in a linked site, or any changes or updates to such sites.</p>
            <p>PUP is not responsible for webcasting or any other form of transmission received from any linked site. PUP is providing these links to you only as a convenience, and the inclusion of any link does not imply endorsement by the University of that site.</p>
        </div>
    </div>

    <!-- ── SECTION 12 ── -->
    <div class="terms-section" id="s12">
        <div class="section-header" onclick="toggleSection(this)">
            <div class="section-num">12</div>
            <div class="section-title">Submission of Unsolicited Ideas</div>
            <i class="fa fa-chevron-down section-chevron"></i>
        </div>
        <div class="section-body">
            <p>PUP or any of its employees and developers or third-party providers do not accept or consider unsolicited ideas, including ideas for new campaigns, new promotions, new products or technologies, processes, materials, marketing plans or new product names. Please do not send any original creative artwork, samples, demos, or other works.</p>
            <p>The sole purpose of this policy is to avoid potential misunderstandings or disputes when PUP's services or strategies might seem similar to ideas submitted to PUP. If, despite our request, you still send unsolicited ideas and materials, please understand that PUP makes no assurances that your ideas and materials will be treated as confidential or proprietary.</p>
            <div class="highlight-box">
                <i class="fa fa-lightbulb"></i>
                For official feedback and suggestions about the InternConnect System, please use the feedback channels available within the platform itself.
            </div>
        </div>
    </div>

    <!-- Agreement Card -->
    <div class="agreement-card">
        <h3>Ready to Get Started?</h3>
        <p>By clicking the button below, you confirm that you have read and agree to the Terms of Use.</p>
        <a href="javascript:history.back()" class="btn-agree">
    <i class="fa fa-check-circle"></i> I Agree – Go to Dashboard
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