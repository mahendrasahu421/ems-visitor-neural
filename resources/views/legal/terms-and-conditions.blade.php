<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{{ $title }}</title>
    <link rel="shortcut icon" href="{{ asset('frontent/assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.css') }}">
    <style>
        :root {
            --primary-color: #00B4EE;
            --primary-dark: #008ebc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.7;
        }
        .header-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        .legal-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 20px -4px rgba(0,0,0,0.05);
            padding: 40px;
            margin-top: 30px;
            margin-bottom: 50px;
        }
        .badge-effective {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }
        .section-title {
            color: #0f172a;
            font-size: 1.35rem;
            font-weight: 700;
            margin-top: 32px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
        }
        .section-title i {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 1.45rem;
        }
        .toc-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .toc-box ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        .toc-box li {
            margin-bottom: 8px;
        }
        .toc-box a {
            color: #334155;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .toc-box a:hover {
            color: var(--primary-color);
        }
        .alert-rule {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 16px 20px;
            border-radius: 0 10px 10px 0;
            margin: 20px 0;
            color: #92400e;
        }
        .danger-rule {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 16px 20px;
            border-radius: 0 10px 10px 0;
            margin: 20px 0;
            color: #991b1b;
        }
        .footer-legal {
            background: #ffffff;
            border-top: 1px solid var(--card-border);
            padding: 24px 0;
            color: var(--text-muted);
            font-size: 14px;
        }
        @media print {
            .header-bar, .toc-box, .btn-print, .btn-back { display: none !important; }
            .legal-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header-bar">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
                    <span style="font-weight: 800; font-size: 1.45rem; color: #00B4EE; letter-spacing: 1px;">EMS</span>
                    <span class="ms-1" style="font-weight: 600; font-size: 1.15rem; color: #1e293b;">VISITOR</span>
                </a>
                <span class="text-muted d-none d-sm-inline">|</span>
                <span class="text-muted small d-none d-sm-inline">{{ $companyName }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-flex align-items-center btn-print">
                    <i class="ti ti-printer me-1"></i>Print
                </button>
                <a href="{{ route('privacy.policy') }}" class="btn btn-outline-primary btn-sm">
                    Privacy Policy
                </a>
                @if(auth()->guard('employee')->check())
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-primary btn-sm btn-back">
                        <i class="ti ti-arrow-left me-1"></i>Employee Portal
                    </a>
                @elseif(auth()->check())
                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm btn-back">
                        <i class="ti ti-arrow-left me-1"></i>Admin Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm btn-back">
                        <i class="ti ti-login me-1"></i>Sign In
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="legal-card">
            
            <div class="text-center mb-4">
                <span class="badge-effective mb-2 d-inline-block">
                    <i class="ti ti-calendar me-1"></i>Effective Date: {{ $effectiveDate }} &bull; Last Updated: {{ $lastUpdated }}
                </span>
                <h1 class="fw-bold mt-2" style="color: #0f172a;">Terms and Conditions</h1>
                <p class="text-muted fs-15 max-w-700 mx-auto">
                    Rules, operational obligations, and acceptable usage policy governing access to the <strong>{{ $appName }}</strong> provided by <strong>{{ $companyName }}</strong>.
                </p>
            </div>

            <!-- Table of Contents -->
            <div class="toc-box">
                <h6 class="fw-bold text-uppercase fs-12 text-muted mb-3"><i class="ti ti-list me-1"></i>Table of Contents</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><a href="#terms-1">1. Acceptance of Terms</a></li>
                            <li><a href="#terms-2">2. User Accounts &amp; Credential Confidentiality</a></li>
                            <li><a href="#terms-3">3. Attendance &amp; Anti-Proxy Policy</a></li>
                            <li><a href="#terms-4">4. GPS &amp; Location Verification Rules</a></li>
                            <li><a href="#terms-5">5. Daily Work Reports &amp; Duty Logs</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><a href="#terms-6">6. Intellectual Property &amp; Confidential Data</a></li>
                            <li><a href="#terms-7">7. Acceptable Use &amp; Prohibited Conduct</a></li>
                            <li><a href="#terms-8">8. Termination &amp; Access Revocation</a></li>
                            <li><a href="#terms-9">9. Limitation of Liability</a></li>
                            <li><a href="#terms-10">10. Governing Law &amp; Jurisdiction</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 1 -->
            <section id="terms-1">
                <h3 class="section-title"><i class="ti ti-file-certificate"></i>1. Acceptance of Terms</h3>
                <p>
                    By logging into, downloading, or using the <strong>{{ $appName }}</strong> (available via web browser or mobile client), you agree to be bound by these Terms and Conditions and the associated <a href="{{ route('privacy.policy') }}">Privacy Policy</a>.
                </p>
                <p>
                    If you are an employee, contractor, or staff member of <strong>{{ $companyName }}</strong>, adherence to these terms is an explicit condition of your employment and duty assignments.
                </p>
            </section>

            <!-- Section 2 -->
            <section id="terms-2">
                <h3 class="section-title"><i class="ti ti-key"></i>2. User Accounts &amp; Credential Confidentiality</h3>
                <ul>
                    <li>Each user is assigned unique login credentials (Employee ID / Mobile / Email, Password, and 4-digit quick login PIN).</li>
                    <li><strong>Individual Responsibility:</strong> You are solely responsible for all activities, attendance punches, daily reports, and leave applications conducted through your account.</li>
                    <li><strong>Prohibition of Sharing:</strong> You must never disclose, share, or loan your password, PIN, or multi-factor OTPs to coworkers, family members, or third parties.</li>
                    <li>If you suspect unauthorized access to your account, you must immediately change your password and notify the HR / IT Department.</li>
                </ul>
            </section>

            <!-- Section 3 -->
            <section id="terms-3">
                <h3 class="section-title"><i class="ti ti-clock-check"></i>3. Attendance &amp; Anti-Proxy Policy</h3>
                <div class="danger-rule">
                    <i class="ti ti-alert-octagon me-1"></i>
                    <strong>Zero-Tolerance on Proxy Attendance:</strong> Punching in or out on behalf of another employee, or requesting another person to record attendance on your behalf, constitutes gross misconduct and is grounds for immediate disciplinary action, deduction of wages, or termination.
                </div>
                <ul>
                    <li>Attendance punch times recorded through the system are considered final for monthly working hours and payroll calculations.</li>
                    <li>Unapproved absences or failure to punch will be treated as Loss of Pay (LWP) in accordance with the company leave policy.</li>
                </ul>
            </section>

            <!-- Section 4 -->
            <section id="terms-4">
                <h3 class="section-title"><i class="ti ti-map-2"></i>4. GPS &amp; Location Verification Rules</h3>
                <p>
                    For field staff and mobile app users, duty punches include automatic GPS location verification to confirm physical presence at designated client sites or branches.
                </p>
                <div class="alert-rule">
                    <i class="ti ti-alert-triangle me-1"></i>
                    <strong>Prohibition of GPS Spoofing:</strong> The use of "Mock Location" developer tools, fake GPS applications, VPN routing, or location emulator software to falsify punch coordinates is strictly monitored and flagged by the system as fraudulent activity.
                </div>
                <ul>
                    <li>Punches flagged with tampered coordinates will be marked as invalid and escalated to the department supervisor.</li>
                </ul>
            </section>

            <!-- Section 5 -->
            <section id="terms-5">
                <h3 class="section-title"><i class="ti ti-report"></i>5. Daily Work Reports &amp; Task Submissions</h3>
                <ul>
                    <li>Employees assigned to project tracking must submit truthful, comprehensive Daily Work Reports (DR) reflecting actual hours worked and deliverables completed.</li>
                    <li>Fabricating project hours or copying work descriptions without performing actual work violates professional standards.</li>
                    <li>Supervisors review and verify daily submissions prior to monthly client billing and productivity evaluations.</li>
                </ul>
            </section>

            <!-- Section 6 -->
            <section id="terms-6">
                <h3 class="section-title"><i class="ti ti-copyright"></i>6. Intellectual Property &amp; Confidential Data</h3>
                <ul>
                    <li>All software code, user interface designs, database structures, business processes, and project documentation in the EMS platform are the exclusive intellectual property of <strong>{{ $companyName }}</strong>.</li>
                    <li><strong>Confidentiality:</strong> Client details, invoices, pricing structures, payroll data, and employee personal information accessed through the portal are confidential. Unauthorized export, sharing, or distribution of this data is strictly prohibited by law.</li>
                </ul>
            </section>

            <!-- Section 7 -->
            <section id="terms-7">
                <h3 class="section-title"><i class="ti ti-ban"></i>7. Acceptable Use &amp; Prohibited Conduct</h3>
                <p>Users shall not:</p>
                <ul>
                    <li>Attempt to probe, scan, or test the vulnerability of the platform or circumvent access controls.</li>
                    <li>Transmit malicious software, viruses, worms, or automated scraping scripts.</li>
                    <li>Use the system to transmit harassing, abusive, or defamatory content in feedback, ticket replies, or chat notes.</li>
                    <li>Attempt to reverse-engineer, decompile, or disassemble mobile APK/IPA client packages.</li>
                </ul>
            </section>

            <!-- Section 8 -->
            <section id="terms-8">
                <h3 class="section-title"><i class="ti ti-user-x"></i>8. Termination &amp; Access Revocation</h3>
                <ul>
                    <li><strong>Exit / Resignation:</strong> Upon completion of the formal resignation exit workflow or employment separation, access to employee self-service features, document vaults, and mobile tokens is automatically revoked.</li>
                    <li><strong>Immediate Suspension:</strong> Management reserves the right to immediately suspend portal access pending investigation in the event of suspected data breach, policy violation, or unauthorized activity.</li>
                </ul>
            </section>

            <!-- Section 9 -->
            <section id="terms-9">
                <h3 class="section-title"><i class="ti ti-shield-half"></i>9. Limitation of Liability</h3>
                <p>
                    While <strong>{{ $companyName }}</strong> strives for 99.9% system availability, we do not guarantee uninterrupted or error-free operation during scheduled maintenance, network carrier outages, or third-party cloud infrastructure downtimes.
                </p>
                <p>
                    In the event of temporary network failure during punch-in, employees should notify their supervisor immediately for manual attendance regularisation.
                </p>
            </section>

            <!-- Section 10 -->
            <section id="terms-10">
                <h3 class="section-title"><i class="ti ti-scale"></i>10. Governing Law &amp; Jurisdiction</h3>
                <p>
                    These Terms and Conditions shall be governed by, construed, and enforced in accordance with the laws of <strong>India</strong>. Any disputes arising out of or relating to these terms or system usage shall be subject to the exclusive jurisdiction of the competent courts in <strong>Kanpur, Uttar Pradesh, India</strong>.
                </p>
                <div class="card bg-light border-0 p-3 mt-3">
                    <p class="mb-1"><strong>{{ $companyName }}</strong></p>
                    <p class="mb-1 text-muted"><i class="ti ti-map-pin me-1"></i>{{ $address }}</p>
                    <p class="mb-1 text-muted"><i class="ti ti-mail me-1"></i>Email: <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
                    <p class="mb-0 text-muted"><i class="ti ti-phone me-1"></i>Phone: {{ $supportPhone }}</p>
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-legal text-center">
        <div class="container">
            <p class="mb-1">Copyright &copy; {{ date('Y') }} <strong>{{ $companyName }}</strong>. All Rights Reserved.</p>
            <p class="mb-0 small text-muted">
                <a href="{{ route('privacy.policy') }}" class="text-muted text-decoration-none fw-semibold">Privacy Policy</a>
                <span class="mx-2">&bull;</span>
                <a href="{{ route('terms.conditions') }}" class="text-muted text-decoration-none fw-semibold">Terms &amp; Conditions</a>
                <span class="mx-2">&bull;</span>
                <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
            </p>
        </div>
    </footer>

</body>
</html>
