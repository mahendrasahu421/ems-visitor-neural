<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{{ $title }}</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('frontent/assets/img/favicon.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">
    
    <!-- Tabler Icons & FontAwesome -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --primary: #00B4EE;
            --primary-dark: #008ebc;
            --primary-light: #e0f6fd;
            --primary-rgb: 0, 180, 238;
            --secondary: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --border-subtle: #e2e8f0;
            --border-highlight: #cbd5e1;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ------------------ Top Glass Header ------------------ */
        .glass-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .header-content {
            padding: 14px 0;
        }
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .brand-badge {
            background: linear-gradient(135deg, #00B4EE 0%, #008ebc 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.1rem;
            padding: 6px 12px;
            border-radius: 10px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 180, 238, 0.3);
        }
        .brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--secondary);
            margin: 0;
            letter-spacing: -0.2px;
        }
        .brand-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
            font-weight: 500;
        }

        /* Header search pill */
        .search-pill-wrap {
            position: relative;
            width: 260px;
        }
        .search-pill {
            width: 100%;
            padding: 8px 14px 8px 36px;
            border-radius: 30px;
            border: 1px solid var(--border-subtle);
            background: #f1f5f9;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            outline: none;
        }
        .search-pill:focus {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 180, 238, 0.15);
        }
        .search-pill-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Buttons */
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 8px 18px;
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 180, 238, 0.25);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-modern-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 180, 238, 0.35);
        }
        .btn-modern-outline {
            background: #ffffff;
            color: var(--text-main) !important;
            border: 1px solid var(--border-subtle);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-modern-outline:hover {
            border-color: var(--primary);
            color: var(--primary) !important;
            background: var(--primary-light);
        }

        /* ------------------ Hero Banner ------------------ */
        .hero-banner {
            position: relative;
            background: linear-gradient(135deg, #0b192c 0%, #1e293b 60%, #0f172a 100%);
            color: #ffffff;
            padding: 55px 0 65px;
            overflow: hidden;
        }
        .hero-banner::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 180, 238, 0.25) 0%, rgba(0, 180, 238, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-banner::after {
            content: "";
            position: absolute;
            bottom: -30%;
            left: 5%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, rgba(14, 165, 233, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #38bdf8;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
        }
        .hero-title {
            font-size: 2.35rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.25;
            margin-bottom: 14px;
        }
        .hero-lead {
            font-size: 1.05rem;
            color: #94a3b8;
            max-width: 680px;
            margin-bottom: 22px;
            font-weight: 400;
        }
        .hero-meta-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        .hero-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.06);
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .hero-meta-item i {
            color: #38bdf8;
        }

        /* ------------------ Mobile Quick-Pill Bar ------------------ */
        .mobile-pill-bar {
            display: none;
            background: #ffffff;
            border-bottom: 1px solid var(--border-subtle);
            padding: 10px 15px;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            position: sticky;
            top: 61px;
            z-index: 1010;
        }
        .mobile-pill-bar a {
            display: inline-block;
            padding: 5px 14px;
            margin-right: 8px;
            background: #f1f5f9;
            color: var(--text-main);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .mobile-pill-bar a:hover,
        .mobile-pill-bar a.active {
            background: var(--primary);
            color: #ffffff;
        }

        /* ------------------ Content Section ------------------ */
        .content-container {
            padding: 40px 0 80px;
        }

        /* Sticky Sidebar */
        .sticky-toc-wrap {
            position: sticky;
            top: 90px;
        }
        .toc-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 24px;
            box-shadow: var(--shadow-card);
            margin-bottom: 24px;
        }
        .toc-header {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .toc-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .toc-item {
            margin-bottom: 4px;
        }
        .toc-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: #334155;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            border-left: 2px solid transparent;
        }
        .toc-link i {
            font-size: 16px;
            color: #94a3b8;
            transition: color 0.2s ease;
            width: 18px;
            text-align: center;
        }
        .toc-link:hover {
            color: var(--primary-dark);
            background: var(--primary-light);
            border-left-color: var(--primary);
        }
        .toc-link:hover i {
            color: var(--primary);
        }
        .toc-link.active {
            color: var(--primary-dark);
            background: var(--primary-light);
            font-weight: 600;
            border-left-color: var(--primary);
        }
        .toc-link.active i {
            color: var(--primary);
        }

        /* Support Box in Sidebar */
        .support-card {
            background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 20px;
            font-size: 0.85rem;
        }
        .support-card h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        .support-card a {
            color: var(--primary-dark);
            font-weight: 600;
            text-decoration: none;
        }
        .support-card a:hover {
            text-decoration: underline;
        }

        /* ------------------ Legal Content Cards ------------------ */
        .legal-section-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
            padding: 32px 36px;
            margin-bottom: 28px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }
        .legal-section-card:hover {
            border-color: var(--border-highlight);
            box-shadow: var(--shadow-lg);
        }

        .section-header-wrap {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section-icon-badge {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-light) 0%, #bae6fd 100%);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .section-title-text {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--secondary);
            margin: 0;
            letter-spacing: -0.3px;
        }
        .section-subtitle-text {
            font-size: 0.825rem;
            color: var(--text-muted);
            margin: 2px 0 0;
            font-weight: 500;
        }

        .section-body {
            font-size: 0.95rem;
            color: #334155;
            line-height: 1.75;
        }
        .section-body p {
            margin-bottom: 14px;
        }
        .section-body ul {
            padding-left: 20px;
            margin-bottom: 16px;
        }
        .section-body li {
            margin-bottom: 10px;
            position: relative;
        }

        /* Highlighted Callout Boxes */
        .callout-box {
            border-radius: var(--radius-md);
            padding: 18px 22px;
            margin: 20px 0;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            font-size: 0.92rem;
            line-height: 1.6;
        }
        .callout-box i {
            font-size: 22px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .callout-danger {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        .callout-danger i {
            color: #ef4444;
        }
        .callout-warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }
        .callout-warning i {
            color: #f59e0b;
        }
        .callout-info {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            color: #075985;
        }
        .callout-info i {
            color: #0284c7;
        }
        .callout-success {
            background: #f0fdf4;
            border-left: 4px solid #16a34a;
            color: #166534;
        }
        .callout-success i {
            color: #16a34a;
        }

        /* Feature grid cards within sections */
        .spec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin: 20px 0;
        }
        .spec-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 16px;
            font-size: 0.88rem;
        }
        .spec-item-title {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .spec-item-title i {
            color: var(--primary);
            font-size: 16px;
        }

        /* Back to top float button */
        .btn-float-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 14px rgba(0, 180, 238, 0.4);
            border: none;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            cursor: pointer;
        }
        .btn-float-top.show {
            opacity: 1;
            visibility: visible;
        }
        .btn-float-top:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            color: #ffffff;
        }

        /* ------------------ Footer ------------------ */
        .legal-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 45px 0 30px;
            font-size: 0.88rem;
            border-top: 1px solid #1e293b;
        }
        .legal-footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .legal-footer a:hover {
            color: var(--primary);
        }
        .footer-bottom-bar {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid #1e293b;
            font-size: 0.8rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        /* ------------------ Print Styling ------------------ */
        @media print {
            .glass-header, .hero-banner, .sticky-toc-wrap, .btn-float-top, .mobile-pill-bar, .legal-footer, .search-pill-wrap, .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .content-container {
                padding: 0 !important;
            }
            .legal-section-card {
                border: none !important;
                box-shadow: none !important;
                padding: 15px 0 !important;
                page-break-inside: avoid;
            }
            .section-icon-badge {
                display: none !important;
            }
        }

        /* ------------------ Responsive Breakpoints ------------------ */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 1.85rem;
            }
            .sticky-toc-wrap {
                display: none;
            }
            .mobile-pill-bar {
                display: block;
            }
            .legal-section-card {
                padding: 24px 20px;
            }
            .search-pill-wrap {
                display: none;
            }
        }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#toc-nav" data-bs-offset="120">

    <!-- Top Sticky Glass Header -->
    <header class="glass-header">
        <div class="container">
            <div class="header-content d-flex align-items-center justify-content-between">
                
                <!-- Brand Logo & Entity -->
                <a href="{{ url('/') }}" class="brand-logo-wrap">
                    <span class="brand-badge">EMS</span>
                    <div>
                        <h1 class="brand-title">VISITOR &bull; PORTAL</h1>
                        <p class="brand-subtitle">{{ $companyName }}</p>
                    </div>
                </a>

                <!-- Search Input Filter -->
                <div class="search-pill-wrap d-none d-lg-block">
                    <i class="ti ti-search"></i>
                    <input type="text" id="policySearchInput" class="search-pill" placeholder="Quick search privacy terms...">
                </div>

                <!-- Action Controls -->
                <div class="d-flex align-items-center gap-2">
                    <button onclick="window.print()" class="btn-modern-outline d-none d-sm-inline-flex" title="Print or save as PDF">
                        <i class="ti ti-printer"></i>
                        <span>Print</span>
                    </button>
                    
                    <a href="{{ route('terms.conditions') }}" class="btn-modern-outline">
                        <i class="ti ti-file-certificate"></i>
                        <span class="d-none d-md-inline">Terms &amp; Conditions</span>
                    </a>

                    @if(auth()->guard('employee')->check())
                        <a href="{{ route('employee.dashboard') }}" class="btn-modern-primary">
                            <i class="ti ti-arrow-left"></i>
                            <span>Dashboard</span>
                        </a>
                    @elseif(auth()->check())
                        <a href="{{ route('home') }}" class="btn-modern-primary">
                            <i class="ti ti-arrow-left"></i>
                            <span>Admin Portal</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-modern-primary">
                            <i class="ti ti-login"></i>
                            <span>Sign In</span>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </header>

    <!-- Hero Header Banner -->
    <section class="hero-banner">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-9">
                    
                    <div class="hero-badge">
                        <i class="ti ti-shield-lock"></i>
                        <span>Workforce &amp; Visitor Data Protection</span>
                    </div>

                    <h2 class="hero-title">Privacy Policy</h2>

                    <p class="hero-lead">
                        This Privacy Policy outlines how <strong>{{ $companyName }}</strong> handles personal information, biometric verification, GPS attendance logs, and device permissions in the <strong>{{ $appName }}</strong>.
                    </p>

                    <div class="hero-meta-bar">
                        <div class="hero-meta-item">
                            <i class="ti ti-calendar"></i>
                            <span>Effective: <strong>{{ $effectiveDate }}</strong></span>
                        </div>
                        <div class="hero-meta-item">
                            <i class="ti ti-clock-edit"></i>
                            <span>Last Updated: <strong>{{ $lastUpdated }}</strong></span>
                        </div>
                        <div class="hero-meta-item">
                            <i class="ti ti-lock-check"></i>
                            <span>Security: <strong class="text-info">Encrypted TLS/Bcrypt</strong></span>
                        </div>
                        <div class="hero-meta-item">
                            <i class="ti ti-clock"></i>
                            <span>Estimated Read: <strong>7 Minutes</strong></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Horizontal Quick Scroll Pill Bar -->
    <nav class="mobile-pill-bar">
        <a href="#section-scope">1. Scope</a>
        <a href="#section-collect">2. Data Collected</a>
        <a href="#section-gps-location">3. GPS &amp; Location</a>
        <a href="#section-biometric">4. Camera &amp; Photo</a>
        <a href="#section-usage">5. Purpose</a>
        <a href="#section-permissions">6. Permissions</a>
        <a href="#section-security">7. Security</a>
        <a href="#section-retention">8. Retention</a>
        <a href="#section-rights">9. User Rights</a>
        <a href="#section-grievance">10. Grievance Officer</a>
    </nav>

    <!-- Main Content Area -->
    <main class="content-container">
        <div class="container">
            <div class="row">
                
                <!-- Left Sidebar: Sticky Navigation (Desktop) -->
                <aside class="col-lg-4 col-xl-3">
                    <div class="sticky-toc-wrap">
                        
                        <div class="toc-card" id="toc-nav">
                            <div class="toc-header">
                                <span><i class="ti ti-list-details me-1"></i>Contents</span>
                                <span class="badge bg-light text-secondary">10 Clauses</span>
                            </div>
                            <ul class="toc-list">
                                <li class="toc-item">
                                    <a href="#section-scope" class="toc-link">
                                        <i class="ti ti-shield-check"></i>
                                        <span>1. Scope &amp; Applicability</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-collect" class="toc-link">
                                        <i class="ti ti-id"></i>
                                        <span>2. Data We Collect</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-gps-location" class="toc-link">
                                        <i class="ti ti-map-pin"></i>
                                        <span>3. GPS &amp; Location Data</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-biometric" class="toc-link">
                                        <i class="ti ti-camera"></i>
                                        <span>4. Camera &amp; Biometrics</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-usage" class="toc-link">
                                        <i class="ti ti-settings-cog"></i>
                                        <span>5. How We Use Data</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-permissions" class="toc-link">
                                        <i class="ti ti-device-mobile"></i>
                                        <span>6. Mobile Permissions</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-security" class="toc-link">
                                        <i class="ti ti-lock"></i>
                                        <span>7. Security &amp; Encryption</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-retention" class="toc-link">
                                        <i class="ti ti-database"></i>
                                        <span>8. Retention &amp; Sharing</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-rights" class="toc-link">
                                        <i class="ti ti-user-check"></i>
                                        <span>9. Employee Rights</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-grievance" class="toc-link">
                                        <i class="ti ti-mail"></i>
                                        <span>10. Grievance Desk</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Sidebar Quick Support Card -->
                        <div class="support-card">
                            <h6><i class="ti ti-headset me-1 text-primary"></i>Privacy Support</h6>
                            <p class="text-muted mb-2">Questions regarding personal data storage, logs, or privacy rights?</p>
                            <div class="mb-1">
                                <i class="ti ti-mail me-1 text-muted"></i>
                                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                            </div>
                            <div>
                                <i class="ti ti-phone me-1 text-muted"></i>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}">{{ $supportPhone }}</a>
                            </div>
                        </div>

                    </div>
                </aside>

                <!-- Right Column: Structured Clause Cards -->
                <div class="col-lg-8 col-xl-9">
                    
                    <!-- Section 1 -->
                    <article class="legal-section-card" id="section-scope">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-shield-check"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">1. Scope and Applicability</h3>
                                <p class="section-subtitle-text">Coverage across employees, contractors, visitors, and corporate stakeholders</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                This Privacy Policy explains data practices across all web portals, employee self-service consoles, visitor management desks, and mobile applications of <strong>{{ $appName }}</strong> operated by <strong>{{ $companyName }}</strong>.
                            </p>
                            
                            <div class="spec-grid">
                                <div class="spec-item">
                                    <div class="spec-item-title"><i class="ti ti-users"></i>Workforce Personnel</div>
                                    <span class="text-muted">Permanent employees, contract staff, interns, field supervisors, and management.</span>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-item-title"><i class="ti ti-id-badge"></i>Premises Visitors</div>
                                    <span class="text-muted">Guests, clients, interview candidates, and vendors registering for temporary access badges.</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Section 2 -->
                    <article class="legal-section-card" id="section-collect">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-id"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">2. Categories of Information We Collect</h3>
                                <p class="section-subtitle-text">Necessary data elements required for employment operations and security</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                We collect and maintain only the minimum necessary data points essential for employment record-keeping, biometric verification, and statutory payroll:
                            </p>

                            <ul>
                                <li><strong>Identity &amp; Profile Details:</strong> Full Name, Employee ID, Date of Birth, Gender, Blood Group, Designation, Department, Branch, Shift Timings, and Agency assignment.</li>
                                <li><strong>Official Contact Records:</strong> Registered corporate email, personal emergency email, official contact number, emergency contact details.</li>
                                <li><strong>Payroll &amp; Statutory Identifiers:</strong> Bank Account details, IFSC codes, PAN, Provident Fund (PF) UAN numbers, and ESI insurance data.</li>
                                <li><strong>Visitor Records:</strong> Visitor Name, Mobile number, Photo ID badge capture, Host Employee name, purpose of visit, entry and exit gate timestamps.</li>
                                <li><strong>Technical &amp; Network Identifiers:</strong> IP address, device model, operating system version, Firebase Cloud Messaging (FCM) tokens for push notifications, and cryptographic session tokens.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 3 -->
                    <article class="legal-section-card" id="section-gps-location">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">3. GPS Location &amp; Duty Punch Tracking</h3>
                                <p class="section-subtitle-text">Event-driven location verification without continuous background surveillance</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <div class="callout-box callout-info">
                                <i class="ti ti-current-location"></i>
                                <div>
                                    <strong>Transparent, Event-Based Tracking:</strong> GPS latitude and longitude coordinates are captured <strong>strictly at the single instant</strong> when an employee punches in or out, or submits a field task. We do <strong>NOT</strong> continuously track personal movements when the app is closed.
                                </div>
                            </div>

                            <ul>
                                <li><strong>Geofencing Validation:</strong> Punch coordinates are matched against pre-configured office perimeters or assigned client project sites to confirm genuine physical presence.</li>
                                <li><strong>Strict Anti-Surveillance Commitment:</strong> We respect employee privacy during lunch intervals, after shift completion, and on declared holidays. No background telemetry is stored outside duty hours.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 4 -->
                    <article class="legal-section-card" id="section-biometric">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-camera"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">4. Camera, Photos &amp; Biometric Verification</h3>
                                <p class="section-subtitle-text">Live selfie identity verification and visitor photo registration</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                The platform accesses the camera hardware solely for the following explicit workflows:
                            </p>
                            <ul>
                                <li><strong>Attendance Selfie Check-in:</strong> Real-time snapshot captured during shift check-in to ensure the authenticated user is physically present at the device.</li>
                                <li><strong>Visitor Pass Generation:</strong> Capturing visitor portrait photos to produce digital or printed gate security badges.</li>
                                <li><strong>Document Attachments:</strong> Permitting users to take photos of receipts, medical certificates, or project deliverables for HR ticket attachments.</li>
                            </ul>
                            <div class="callout-box callout-success">
                                <i class="ti ti-shield-check"></i>
                                <div>
                                    Attendance photos are stored on isolated, encrypted cloud servers and are never shared with facial recognition advertising databases or third-party marketing entities.
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Section 5 -->
                    <article class="legal-section-card" id="section-usage">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-settings-cog"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">5. How We Use Collected Information</h3>
                                <p class="section-subtitle-text">Operational management, payroll calculation, and statutory compliance</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <ul>
                                <li><strong>Automated Attendance &amp; Shifts:</strong> Calculating productive duty hours, overtime, shifts, late check-in penalties, and leave accruals.</li>
                                <li><strong>Payroll Computation &amp; Tax Filings:</strong> Generation of monthly payslips, TDS deduction computation, salary disbursement processing, and provident fund compliance.</li>
                                <li><strong>Workplace Security &amp; Fire Safety:</strong> Maintaining instantaneous headcount rosters during emergencies, audits, and facility management.</li>
                                <li><strong>Transactional Alerts:</strong> Transmitting essential notifications regarding leave approvals, shift changes, salary credits, and organizational announcements.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 6 -->
                    <article class="legal-section-card" id="section-permissions">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-device-mobile"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">6. Mobile Device Permissions</h3>
                                <p class="section-subtitle-text">Transparent hardware permissions utilized by the EMS Mobile Client</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Permission</th>
                                            <th>Operational Purpose</th>
                                            <th>Invocation Model</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>ACCESS_FINE_LOCATION</strong></td>
                                            <td>Validating attendance check-in within designated radial geofence.</td>
                                            <td><span class="badge bg-info-subtle text-info border">Only on Punch</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>CAMERA</strong></td>
                                            <td>Live photo capture during duty punch &amp; visitor pass generation.</td>
                                            <td><span class="badge bg-primary-subtle text-primary border">User Triggered</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>READ/WRITE_STORAGE</strong></td>
                                            <td>Downloading PDF payslips, saving documents, uploading medical proofs.</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary border">User Triggered</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>POST_NOTIFICATIONS</strong></td>
                                            <td>Delivering shift alerts, leave approval statuses, and HR broadcasts.</td>
                                            <td><span class="badge bg-success-subtle text-success border">Configurable</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>

                    <!-- Section 7 -->
                    <article class="legal-section-card" id="section-security">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-lock"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">7. Data Security &amp; Cryptographic Controls</h3>
                                <p class="section-subtitle-text">Industry standard encryption, role-based controls, and intrusion protection</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                <strong>{{ $companyName }}</strong> employs defense-in-depth security architectures to ensure total data integrity:
                            </p>
                            <ul>
                                <li><strong>Transport Encryption:</strong> All client-server traffic is strictly enforced over HTTPS utilizing TLS 1.3 encryption.</li>
                                <li><strong>Salted Password Hashing:</strong> Passwords and mobile 4-digit PINs are securely hashed using Bcrypt/Argon2. Plaintext credentials are never saved.</li>
                                <li><strong>Role-Based Access Control (RBAC):</strong> Administrative permissions, salary access, and employee records are segregated according to strict organizational hierarchies.</li>
                                <li><strong>Continuous Database Backups:</strong> Database instances are backed up regularly to secure, redundantly replicated cloud storage.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 8 -->
                    <article class="legal-section-card" id="section-retention">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-database"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">8. Retention &amp; Third-Party Disclosures</h3>
                                <p class="section-subtitle-text">Retention periods, zero data monetization policy, and statutory compliance</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <div class="callout-box callout-success">
                                <i class="ti ti-circle-check"></i>
                                <div>
                                    <strong>Zero Data Monetization Guarantee:</strong> We do not sell, rent, commercialize, or share employee or visitor personal information with advertisers or data brokers under any circumstances.
                                </div>
                            </div>
                            <p>
                                <strong>Statutory Disclosures:</strong> Data may be disclosed exclusively when required by court orders, lawful requests from labor commissioners, income tax authorities, or law enforcement agencies pursuant to Indian law.
                            </p>
                        </div>
                    </article>

                    <!-- Section 9 -->
                    <article class="legal-section-card" id="section-rights">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-user-check"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">9. Workforce Rights &amp; Access Controls</h3>
                                <p class="section-subtitle-text">Employee access to attendance logs, payslips, and profile corrections</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>As an active user of the portal, you retain the right to:</p>
                            <ul>
                                <li>Access and review all your personal attendance histories, leave balances, and salary slips anytime via the Employee Self-Service dashboard.</li>
                                <li>Request immediate rectification of erroneous profile attributes, bank details, or contact numbers via HR ticket submissions.</li>
                                <li>Change your access password or mobile quick-PIN whenever desired directly through account settings.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 10 -->
                    <article class="legal-section-card" id="section-grievance">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-mail"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">10. Contact Us &amp; Grievance Redressal</h3>
                                <p class="section-subtitle-text">Designated Data Grievance Officer details under Indian Information Technology Rules</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                If you have any inquiries, concerns, or grievances concerning this Privacy Policy, please write to our designated Compliance and Grievance Officer:
                            </p>

                            <div class="card border-0 bg-light p-4 mt-4 rounded-3">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="ti ti-building text-primary fs-4"></i>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $companyName }}</h6>
                                </div>
                                <div class="row g-3 fs-14">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted"><i class="ti ti-map-pin me-2 text-primary"></i><strong>Corporate Office:</strong></p>
                                        <p class="mb-0 text-dark ps-4">{{ $address }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted"><i class="ti ti-mail me-2 text-primary"></i><strong>Official Support Email:</strong></p>
                                        <p class="mb-2 text-dark ps-4"><a href="mailto:{{ $contactEmail }}" class="text-primary fw-semibold">{{ $contactEmail }}</a></p>
                                        
                                        <p class="mb-1 text-muted"><i class="ti ti-phone me-2 text-primary"></i><strong>Telephone Assistance:</strong></p>
                                        <p class="mb-0 text-dark ps-4"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" class="text-primary fw-semibold">{{ $supportPhone }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        </div>
    </main>

    <!-- Floating Back to Top Button -->
    <button type="button" class="btn-float-top" id="btnBackToTop" aria-label="Back to top">
        <i class="ti ti-arrow-up"></i>
    </button>

    <!-- Corporate Footer -->
    <footer class="legal-footer">
        <div class="container">
            <div class="row gy-4 align-items-center">
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary px-2 py-1">EMS</span>
                        <strong class="text-white">{{ $appName }}</strong>
                    </div>
                    <p class="mb-0 small text-muted">
                        Enterprise Human Resource Management, Attendance, Biometrics &amp; Daily Reporting Portal.
                    </p>
                </div>

                <div class="col-md-6 text-md-end">
                    <div class="d-flex flex-wrap justify-content-md-end gap-3 small">
                        <a href="{{ route('privacy.policy') }}" class="text-white fw-bold">Privacy Policy</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('terms.conditions') }}">Terms &amp; Conditions</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ url('/') }}">Home</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('login') }}">Staff Login</a>
                    </div>
                </div>

            </div>

            <div class="footer-bottom-bar">
                <div>
                    Copyright &copy; {{ date('Y') }} <strong>{{ $companyName }}</strong>. All rights reserved.
                </div>
                <div class="text-muted">
                    Regulated under IT Act, 2000 &bull; Kanpur, Uttar Pradesh
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS (for Scrollspy) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Back to top floating button logic
            const btnBackToTop = document.getElementById('btnBackToTop');
            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) {
                    btnBackToTop.classList.add('show');
                } else {
                    btnBackToTop.classList.remove('show');
                }
            });
            btnBackToTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Real-time client-side filter
            const searchInput = document.getElementById('policySearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const q = e.target.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.legal-section-card');
                    
                    cards.forEach(card => {
                        const text = card.textContent.toLowerCase();
                        if (!q || text.includes(q)) {
                            card.style.display = 'block';
                            if (q) {
                                card.style.borderColor = '#00B4EE';
                            } else {
                                card.style.borderColor = '';
                            }
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Smooth scrolling for TOC links
            document.querySelectorAll('.toc-link, .mobile-pill-bar a').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href');
                    if (targetId.startsWith('#')) {
                        const targetEl = document.querySelector(targetId);
                        if (targetEl) {
                            e.preventDefault();
                            const offset = 90;
                            const bodyRect = document.body.getBoundingClientRect().top;
                            const elementRect = targetEl.getBoundingClientRect().top;
                            const elementPosition = elementRect - bodyRect;
                            const offsetPosition = elementPosition - offset;

                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });

        });
    </script>
</body>
</html>