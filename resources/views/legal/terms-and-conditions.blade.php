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
                    <input type="text" id="termsSearchInput" class="search-pill" placeholder="Quick search terms (e.g. GPS, Proxy)...">
                </div>

                <!-- Action Controls -->
                <div class="d-flex align-items-center gap-2">
                    <button onclick="window.print()" class="btn-modern-outline d-none d-sm-inline-flex" title="Print or save as PDF">
                        <i class="ti ti-printer"></i>
                        <span>Print</span>
                    </button>
                    
                    <a href="{{ route('privacy.policy') }}" class="btn-modern-outline">
                        <i class="ti ti-shield-lock"></i>
                        <span class="d-none d-md-inline">Privacy Policy</span>
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
                        <i class="ti ti-certificate"></i>
                        <span>Enterprise Governance Document</span>
                    </div>

                    <h2 class="hero-title">Terms &amp; Conditions of Service</h2>

                    <p class="hero-lead">
                        Operational rules, acceptable usage guidelines, attendance integrity, and legal obligations governing your access to the <strong>{{ $appName }}</strong> provided by <strong>{{ $companyName }}</strong>.
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
                            <i class="ti ti-shield-check"></i>
                            <span>Status: <strong class="text-info">Legally Binding</strong></span>
                        </div>
                        <div class="hero-meta-item">
                            <i class="ti ti-clock"></i>
                            <span>Estimated Read: <strong>6 Minutes</strong></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Horizontal Quick Scroll Pill Bar -->
    <nav class="mobile-pill-bar">
        <a href="#section-acceptance">1. Acceptance</a>
        <a href="#section-accounts">2. Credentials</a>
        <a href="#section-attendance">3. Anti-Proxy</a>
        <a href="#section-gps">4. GPS Rules</a>
        <a href="#section-daily-reports">5. Daily Reports</a>
        <a href="#section-ip">6. IP &amp; Secrecy</a>
        <a href="#section-acceptable-use">7. Conduct</a>
        <a href="#section-termination">8. Termination</a>
        <a href="#section-liability">9. Liability</a>
        <a href="#section-governing-law">10. Jurisdiction</a>
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
                                    <a href="#section-acceptance" class="toc-link">
                                        <i class="ti ti-file-certificate"></i>
                                        <span>1. Acceptance of Terms</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-accounts" class="toc-link">
                                        <i class="ti ti-key"></i>
                                        <span>2. Credential Security</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-attendance" class="toc-link">
                                        <i class="ti ti-clock-check"></i>
                                        <span>3. Attendance &amp; Anti-Proxy</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-gps" class="toc-link">
                                        <i class="ti ti-map-pin"></i>
                                        <span>4. GPS &amp; Geo-fencing</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-daily-reports" class="toc-link">
                                        <i class="ti ti-report"></i>
                                        <span>5. Daily Work Reports</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-ip" class="toc-link">
                                        <i class="ti ti-copyright"></i>
                                        <span>6. IP &amp; Data Secrecy</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-acceptable-use" class="toc-link">
                                        <i class="ti ti-ban"></i>
                                        <span>7. Prohibited Conduct</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-termination" class="toc-link">
                                        <i class="ti ti-user-x"></i>
                                        <span>8. Separation &amp; Revocation</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-liability" class="toc-link">
                                        <i class="ti ti-shield-alert"></i>
                                        <span>9. Limitation of Liability</span>
                                    </a>
                                </li>
                                <li class="toc-item">
                                    <a href="#section-governing-law" class="toc-link">
                                        <i class="ti ti-scale"></i>
                                        <span>10. Governing Law</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Sidebar Quick Support Card -->
                        <div class="support-card">
                            <h6><i class="ti ti-headset me-1 text-primary"></i>Compliance Desk</h6>
                            <p class="text-muted mb-2">Have questions about these terms or company employment policies?</p>
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
                    <article class="legal-section-card" id="section-acceptance">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-file-certificate"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">1. Acceptance &amp; Scope of Agreement</h3>
                                <p class="section-subtitle-text">Binding contractual terms for web and mobile client users</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                By accessing, browsing, registering for, downloading the mobile application for, or logging into the <strong>{{ $appName }}</strong> (the "Platform"), you confirm that you have read, understood, and irrevocably agree to be bound by these <strong>Terms and Conditions</strong> and our accompanying <a href="{{ route('privacy.policy') }}" class="fw-semibold text-primary">Privacy Policy</a>.
                            </p>
                            <p>
                                If you are an active employee, apprentice, intern, retainer, consultant, or field staff of <strong>{{ $companyName }}</strong>, strict adherence to these operational terms is an explicit covenant of your employment contract and code of professional conduct.
                            </p>

                            <div class="spec-grid">
                                <div class="spec-item">
                                    <div class="spec-item-title"><i class="ti ti-device-desktop"></i>Web Portal</div>
                                    <span class="text-muted">Desktop administrative dashboards, attendance portals, payroll records, and management consoles.</span>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-item-title"><i class="ti ti-device-mobile"></i>Mobile Client</div>
                                    <span class="text-muted">Android / iOS mobile applications used for real-time duty punching, selfie captures, and daily reporting.</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Section 2 -->
                    <article class="legal-section-card" id="section-accounts">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-key"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">2. User Accounts &amp; Credential Confidentiality</h3>
                                <p class="section-subtitle-text">Sole responsibility for account security and authentication tokens</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                Every authorized user receives designated, individual credentials comprising an <strong>Employee ID / Mobile Number / Official Email</strong>, an encrypted <strong>Password</strong>, and a 4-digit <strong>Quick-Login PIN</strong> for mobile biometric authentication.
                            </p>

                            <div class="callout-box callout-warning">
                                <i class="ti ti-alert-triangle"></i>
                                <div>
                                    <strong>Strict Prohibition of Credential Sharing:</strong> You must keep your credentials strictly confidential. You are expressly forbidden from sharing, transmitting, or disclosing your password, quick-login PIN, or session tokens to any colleague, supervisor, friend, or third party under any circumstances.
                                </div>
                            </div>

                            <ul>
                                <li><strong>Account Accountability:</strong> All actions initiated through your authenticated session—including duty punches, leave applications, daily task submissions, and ticket filings—are legally attributed to you.</li>
                                <li><strong>Device Security:</strong> You must ensure that devices used to access the portal possess screen lock mechanisms (PIN, pattern, or fingerprint) and are not accessible by unauthorized persons.</li>
                                <li><strong>Immediate Compromise Reporting:</strong> In the event you suspect your account has been breached, leaked, or accessed by someone else, you must immediately reset your password and notify the IT / HR Helpdesk at <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 3 -->
                    <article class="legal-section-card" id="section-attendance">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-clock-check"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">3. Attendance Integrity &amp; Anti-Proxy Policy</h3>
                                <p class="section-subtitle-text">Zero-tolerance standard for punch-in falsification and proxy actions</p>
                            </div>
                        </div>
                        <div class="section-body">
                            
                            <div class="callout-box callout-danger">
                                <i class="ti ti-shield-x"></i>
                                <div>
                                    <strong class="d-block mb-1">Zero-Tolerance on Proxy Attendance:</strong>
                                    Recording attendance, punching in/out, or submitting work logs on behalf of another colleague, or asking another individual to punch for you, constitutes <strong>gross professional misconduct and digital forgery</strong>. Any employee found engaging in proxy attendance will face immediate disciplinary penalties, deduction of salary, and potential summary termination of employment.
                                </div>
                            </div>

                            <ul>
                                <li><strong>Payroll Computation Source:</strong> System timestamp logs recorded by the server clock constitute the sole authoritative record for duty hours, shift timings, overtime calculation, and monthly payroll disbursement.</li>
                                <li><strong>Shift Punctuality:</strong> Employees must adhere to assigned shift schedules (General, Morning, Night). Late check-ins and premature check-outs beyond company grace periods will trigger automated salary deductions as specified in company HR policies.</li>
                                <li><strong>Unrecorded Presence:</strong> Physical presence without an active digital punch-in/out record will be designated as Loss of Pay (LWP) unless formally regularised by the designated Department Head within the permitted window.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 4 -->
                    <article class="legal-section-card" id="section-gps">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">4. GPS Geo-Fencing &amp; Anti-Spoofing Protocols</h3>
                                <p class="section-subtitle-text">Mandatory physical presence verification for field and office personnel</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                To validate field visits, client on-site deployments, and branch reporting, the EMS Mobile App captures real-time GPS coordinates, network provider triangulation, and cellular metadata at the precise moment of duty punching.
                            </p>

                            <div class="callout-box callout-danger">
                                <i class="ti ti-ban"></i>
                                <div>
                                    <strong>Prohibition of GPS Spoofing &amp; Emulators:</strong>
                                    The use of Android "Mock Location" developer settings, virtual location injector applications (Fake GPS), GPS spoofing tweaks, or proxy VPN tunnels to fabricate physical coordinates is actively monitored by the EMS anti-tampering engine. Any detected location manipulation results in immediate punch invalidation and automated HR escalation.
                                </div>
                            </div>

                            <div class="spec-grid">
                                <div class="spec-item">
                                    <div class="spec-item-title"><i class="ti ti-current-location"></i>Geo-Fenced Perimeters</div>
                                    <span class="text-muted">Office branch punches must occur within the authorized radial perimeter of designated premises.</span>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-item-title"><i class="ti ti-camera"></i>Biometric Liveness Verification</div>
                                    <span class="text-muted">Punch snapshots require real-time camera capture; uploading pre-existing gallery photographs is disabled.</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Section 5 -->
                    <article class="legal-section-card" id="section-daily-reports">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-report"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">5. Daily Work Reports (DWR) &amp; Task Deliverables</h3>
                                <p class="section-subtitle-text">Accurate logging of project milestones, productive hours, and client work</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                Employees assigned to software engineering, client operations, design, or project tasks must submit accurate, comprehensive Daily Work Reports before the end of each duty shift.
                            </p>

                            <div class="callout-box callout-info">
                                <i class="ti ti-info-circle"></i>
                                <div>
                                    <strong>Audit &amp; Billing Pre-requisite:</strong> Daily work submissions are matched against project timecards, client invoice logs, and supervisor productivity appraisals. Incomplete or delayed daily reports directly delay project approvals.
                                </div>
                            </div>

                            <ul>
                                <li><strong>Truthful Reporting:</strong> Logging fabricated hours, duplicating descriptions from past dates without performing genuine work, or claiming task completion prematurely violates corporate work ethics.</li>
                                <li><strong>Supervisor Verification:</strong> Project leads and reporting managers review submissions on daily basis and possess the right to request clarification or reject substandard activity logs.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 6 -->
                    <article class="legal-section-card" id="section-ip">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-copyright"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">6. Intellectual Property &amp; Data Secrecy</h3>
                                <p class="section-subtitle-text">Exclusive ownership of platform assets and strict non-disclosure obligations</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                All source code, database architectures, algorithmic logic, user interface designs, logos, graphics, and documentation embodied within the <strong>{{ $appName }}</strong> are the exclusive, proprietary intellectual property of <strong>{{ $companyName }}</strong>.
                            </p>

                            <div class="callout-box callout-warning">
                                <i class="ti ti-lock"></i>
                                <div>
                                    <strong>Strict Corporate Confidentiality:</strong> Data accessible via this portal—including client rosters, proprietary invoice financials, employee salary slips, internal rate cards, and technical blueprints—constitutes protected trade secrets.
                                </div>
                            </div>

                            <ul>
                                <li><strong>No Unauthorized Export:</strong> Users shall not download, scrape, screenshot for external dissemination, email to personal accounts, or share confidential company records with any external party without written authorization from executive directors.</li>
                                <li><strong>Continuing Obligation:</strong> Non-disclosure obligations survive indefinitely, including following termination of employment or completion of contractual engagements.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 7 -->
                    <article class="legal-section-card" id="section-acceptable-use">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-ban"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">7. Acceptable Use &amp; Prohibited Conduct</h3>
                                <p class="section-subtitle-text">System integrity standards and unlawful activity prohibitions</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>While utilizing the platform and its network services, you agree not to:</p>
                            
                            <ul>
                                <li>Attempt to decompile, reverse engineer, disassemble, or derive the source code of any mobile APK/IPA, backend API, or compiled frontend asset.</li>
                                <li>Deploy automated scrapers, crawling spiders, bots, DDoS stress testers, or vulnerability scanners against system infrastructure without express written permission from the Chief Information Security Officer.</li>
                                <li>Attempt SQL injection, Cross-Site Scripting (XSS), privilege escalation, or unauthorized access to other employee, supervisor, or administrative profiles.</li>
                                <li>Transmit or store defamatory, abusive, harassing, obscene, or legally actionable content through internal support tickets, feedback forms, or document uploads.</li>
                                <li>Bypass, alter, or disable any biometric verification, SSL certificate pinning, or tamper detection mechanisms built into the platform.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 8 -->
                    <article class="legal-section-card" id="section-termination">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-user-x"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">8. Employment Separation &amp; Access Revocation</h3>
                                <p class="section-subtitle-text">Clearance protocols upon resignation, contract conclusion, or termination</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <ul>
                                <li><strong>Formal Resignation Process:</strong> Employees departing the organization must initiate and complete formal exit workflows, asset handovers, and clearance procedures through the HR Resignation module.</li>
                                <li><strong>Immediate Token Invalidation:</strong> Upon the effective last working date (LWD) or formal separation, all web sessions, API authorization tokens, and mobile app refresh tokens are automatically invalidated.</li>
                                <li><strong>Document Download Window:</strong> Relieving letters, service experience certificates, and final Form 16 / tax documentation will be made accessible via designated post-exit dispatch or portal clearance protocols.</li>
                                <li><strong>Immediate Suspension Right:</strong> Company management reserves the right to instantaneously freeze or terminate account access pending internal inquiry in instances of data theft, insubordination, or code-of-conduct breaches.</li>
                            </ul>
                        </div>
                    </article>

                    <!-- Section 9 -->
                    <article class="legal-section-card" id="section-liability">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-shield-alert"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">9. System Availability &amp; Limitation of Liability</h3>
                                <p class="section-subtitle-text">Service continuity warranties and manual regularisation procedures</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                <strong>{{ $companyName }}</strong> maintains high-availability cloud infrastructure and endeavors to deliver 99.9% platform availability. However, service may occasionally be disrupted due to scheduled maintenance, cloud host downtime, or telecom network anomalies.
                            </p>
                            <p>
                                <strong>Offline Attendance Contingency:</strong> In the rare circumstance of telecom service failure, device malfunction, or application downtime during shift start, the employee is required to inform their reporting supervisor via SMS / email immediately. The supervisor shall execute manual attendance regularisation within 24 hours of notification.
                            </p>
                        </div>
                    </article>

                    <!-- Section 10 -->
                    <article class="legal-section-card" id="section-governing-law">
                        <div class="section-header-wrap">
                            <div class="section-icon-badge">
                                <i class="ti ti-scale"></i>
                            </div>
                            <div>
                                <h3 class="section-title-text">10. Governing Law, Dispute Resolution &amp; Jurisdiction</h3>
                                <p class="section-subtitle-text">Statutory legal jurisdiction and corporate entity contact details</p>
                            </div>
                        </div>
                        <div class="section-body">
                            <p>
                                These Terms and Conditions shall be governed by, interpreted, and construed in accordance with the substantive <strong>Laws of India</strong>, including the Information Technology Act, 2000, and rules framed thereunder.
                            </p>
                            <p>
                                Any controversy, claim, or dispute arising out of or relating to these terms, platform access, or employment data management shall be submitted to the exclusive jurisdiction of the competent courts of law situated in <strong>Kanpur, Uttar Pradesh, India</strong>.
                            </p>

                            <!-- Corporate Entity Details Card -->
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
                        <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
                        <span class="text-muted">&bull;</span>
                        <a href="{{ route('terms.conditions') }}" class="text-white fw-bold">Terms &amp; Conditions</a>
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
            const searchInput = document.getElementById('termsSearchInput');
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