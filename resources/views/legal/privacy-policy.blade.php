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
        .highlight-box {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 16px 20px;
            border-radius: 0 10px 10px 0;
            margin: 20px 0;
            color: #166534;
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid var(--primary-color);
            padding: 16px 20px;
            border-radius: 0 10px 10px 0;
            margin: 20px 0;
            color: #075985;
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
                <a href="{{ route('terms.conditions') }}" class="btn btn-outline-primary btn-sm">
                    Terms &amp; Conditions
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
                <h1 class="fw-bold mt-2" style="color: #0f172a;">Privacy Policy</h1>
                <p class="text-muted fs-15 max-w-700 mx-auto">
                    This Privacy Policy describes how <strong>{{ $companyName }}</strong> collects, uses, protects, and handles personal data across the <strong>{{ $appName }}</strong> (Web &amp; Mobile applications).
                </p>
            </div>

            <div class="info-box">
                <i class="ti ti-info-circle me-1"></i>
                <strong>Notice for Workforce &amp; Visitors:</strong> By using our web portals, biometric attendance systems, mobile check-in applications, or visitor check-in desks, you acknowledge and consent to the practices described in this Privacy Policy.
            </div>

            <!-- Table of Contents -->
            <div class="toc-box">
                <h6 class="fw-bold text-uppercase fs-12 text-muted mb-3"><i class="ti ti-list me-1"></i>Table of Contents</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><a href="#section-1">1. Scope and Applicability</a></li>
                            <li><a href="#section-2">2. Information We Collect</a></li>
                            <li><a href="#section-3">3. GPS Location &amp; Punch Tracking</a></li>
                            <li><a href="#section-4">4. Camera, Photos &amp; Biometric Data</a></li>
                            <li><a href="#section-5">5. How We Use Collected Data</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><a href="#section-6">6. Device Permissions &amp; Mobile Access</a></li>
                            <li><a href="#section-7">7. Data Security &amp; Encryption</a></li>
                            <li><a href="#section-8">8. Data Retention &amp; Disclosure</a></li>
                            <li><a href="#section-9">9. Employee &amp; User Rights</a></li>
                            <li><a href="#section-10">10. Contact Us &amp; Grievance Redressal</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 1 -->
            <section id="section-1">
                <h3 class="section-title"><i class="ti ti-shield-check"></i>1. Scope and Applicability</h3>
                <p>
                    This Privacy Policy applies to all users of the <strong>{{ $appName }}</strong>, including:
                </p>
                <ul>
                    <li><strong>Employees &amp; Staff:</strong> Regular employees, contracted staff, supervisors, HR professionals, and administrators.</li>
                    <li><strong>Visitors &amp; Guests:</strong> Individuals registering upon visiting company premises, client offices, or operational job sites.</li>
                    <li><strong>Clients &amp; Stakeholders:</strong> External partners accessing client project monitoring and ticket management features.</li>
                </ul>
            </section>

            <!-- Section 2 -->
            <section id="section-2">
                <h3 class="section-title"><i class="ti ti-id"></i>2. Information We Collect</h3>
                <p>To deliver automated HRMS, payroll, and premises visitor monitoring, we collect the following categories of information:</p>
                
                <h6 class="fw-bold text-dark mt-3">A. Employee Profile &amp; Identity Data:</h6>
                <ul>
                    <li>Full Name, Gender, Blood Group, Date of Birth.</li>
                    <li>Official and Personal Email addresses, Mobile / Phone numbers.</li>
                    <li>Designation, Department, Branch, Shift timings, Assigned Agency.</li>
                    <li>Employee ID, joining date, reporting supervisor, and bank / PF details required for statutory payroll calculation.</li>
                </ul>

                <h6 class="fw-bold text-dark mt-3">B. Visitor Information:</h6>
                <ul>
                    <li>Visitor Name, mobile number, email address.</li>
                    <li>Organization / company represented, purpose of visit, host employee details.</li>
                    <li>Entry and exit timestamps, gate pass ID, visitor photo badge records.</li>
                </ul>

                <h6 class="fw-bold text-dark mt-3">C. Device &amp; Technical Identifiers:</h6>
                <ul>
                    <li>IP address, browser user-agent, device model and operating system version.</li>
                    <li>FCM Device Tokens (used solely for push notifications like attendance reminders and approval alerts).</li>
                    <li>Session cookies and encrypted authentication tokens (via Laravel Sanctum).</li>
                </ul>
            </section>

            <!-- Section 3 -->
            <section id="section-3">
                <h3 class="section-title"><i class="ti ti-map-pin"></i>3. GPS Location &amp; Duty Punch Tracking</h3>
                <p>
                    Our mobile app features GPS-verified check-in and check-out to eliminate proxy attendance and verify field-level duty presence.
                </p>
                <div class="highlight-box">
                    <strong>Transparent Location Collection:</strong> Location coordinates (latitude, longitude, and accuracy) are captured <strong>only at the precise moment</strong> of attendance punch-in or punch-out, or when completing an assigned field duty task.
                </div>
                <ul>
                    <li>We do <strong>NOT</strong> continuously track or sell background location data when the application is not actively being used for duty operations.</li>
                    <li>Location logs are tied exclusively to the employee's attendance record and are accessible only to authorized supervisors and HR personnel.</li>
                </ul>
            </section>

            <!-- Section 4 -->
            <section id="section-4">
                <h3 class="section-title"><i class="ti ti-camera"></i>4. Camera, Photos &amp; Biometric Verification</h3>
                <p>
                    The application may request access to the device camera for identity verification:
                </p>
                <ul>
                    <li><strong>Attendance Selfies / Face Verification:</strong> Taken during mobile check-in to confirm that the registered employee is physically present.</li>
                    <li><strong>Visitor Badges:</strong> Photo capture for temporary visitor ID pass printing.</li>
                    <li><strong>Document Uploads:</strong> Document photos (e.g. medical certificates for sick leaves, expense receipts) selected voluntarily by the employee.</li>
                    <li>Photos captured for attendance verification are processed securely on private company servers and are never shared with advertising networks.</li>
                </ul>
            </section>

            <!-- Section 5 -->
            <section id="section-5">
                <h3 class="section-title"><i class="ti ti-settings-cog"></i>5. How We Use Collected Data</h3>
                <p>We process personal and operational data for legitimate business, employment, and security purposes:</p>
                <ul>
                    <li><strong>Attendance &amp; Leave Administration:</strong> Real-time shift tracking, lateness calculation, overtime logs, leave quota balances, and holiday adjustments.</li>
                    <li><strong>Payroll &amp; Tax Compliance:</strong> Accurate calculation of Gross Salary, ESI, PF, Professional Tax, TDS deductions, and generation of PDF Payslips.</li>
                    <li><strong>Facility Security &amp; Safety:</strong> Monitoring on-premise occupants in case of emergencies, preventing unauthorized premises access.</li>
                    <li><strong>Service Communication:</strong> Automated SMS/email alerts for leave decisions, resignation workflows, and duty rosters.</li>
                </ul>
            </section>

            <!-- Section 6 -->
            <section id="section-6">
                <h3 class="section-title"><i class="ti ti-device-mobile"></i>6. Device Permissions &amp; Mobile Access</h3>
                <p>The <strong>EMS Visitor Mobile Application</strong> may request the following permissions:</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Permission</th>
                                <th>Purpose</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Location (ACCESS_FINE_LOCATION)</strong></td>
                                <td>To verify check-in / check-out at designated workplace or site geofence.</td>
                                <td>During Punch Action</td>
                            </tr>
                            <tr>
                                <td><strong>Camera (CAMERA)</strong></td>
                                <td>To capture photo verification during punch and visitor registration.</td>
                                <td>User Triggered</td>
                            </tr>
                            <tr>
                                <td><strong>Storage / Photos</strong></td>
                                <td>To save payslips, export reports, and upload leave documents.</td>
                                <td>User Triggered</td>
                            </tr>
                            <tr>
                                <td><strong>Notifications (POST_NOTIFICATIONS)</strong></td>
                                <td>To receive instant updates on leave approvals, shift changes, and announcements.</td>
                                <td>Optional / User Preference</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 7 -->
            <section id="section-7">
                <h3 class="section-title"><i class="ti ti-lock"></i>7. Data Security &amp; Encryption</h3>
                <p>
                    We implement stringent technical and organizational measures to safeguard your information:
                </p>
                <ul>
                    <li><strong>Encryption in Transit:</strong> All communications between web/mobile clients and our servers are encrypted using Transport Layer Security (HTTPS/TLS).</li>
                    <li><strong>Password Hashing:</strong> Passwords and quick login PINs are cryptographically hashed using salted Bcrypt/Argon2. Plain text credentials are never stored.</li>
                    <li><strong>Role-Based Access Control (RBAC):</strong> Strict access controls ensure that employees can access only their own records, supervisors view team records, and HR/Admin view authorized organizational data.</li>
                    <li><strong>Audit Logging:</strong> System activities, authentication attempts, and data modifications are logged for fraud prevention and security monitoring.</li>
                </ul>
            </section>

            <!-- Section 8 -->
            <section id="section-8">
                <h3 class="section-title"><i class="ti ti-database"></i>8. Data Retention &amp; Disclosure</h3>
                <ul>
                    <li><strong>Retention:</strong> Employee records, attendance logs, and payroll histories are retained during active employment and for the period required under statutory labor and tax laws following departure.</li>
                    <li><strong>No Commercial Sale:</strong> We strictly <strong>DO NOT SELL, LEASE, OR TRADE</strong> any employee or visitor personal information to third parties, marketers, or data brokers.</li>
                    <li><strong>Lawful Disclosures:</strong> We may disclose data only where required by statutory government authorities, law enforcement subpoenas, or legal processes in India.</li>
                </ul>
            </section>

            <!-- Section 9 -->
            <section id="section-9">
                <h3 class="section-title"><i class="ti ti-user-check"></i>9. Employee &amp; User Rights</h3>
                <p>Employees and registered users have the right to:</p>
                <ul>
                    <li>Inspect their personal attendance records, leave balances, and payslips via the Employee Self-Service (ESS) portal.</li>
                    <li>Request rectification of incorrect contact details or profile discrepancies through the HR Department.</li>
                    <li>Update their authentication credentials (password / PIN) securely at any time.</li>
                </ul>
            </section>

            <!-- Section 10 -->
            <section id="section-10">
                <h3 class="section-title"><i class="ti ti-mail"></i>10. Contact Us &amp; Grievance Redressal</h3>
                <p>
                    For questions, feedback, or grievance redressal regarding this Privacy Policy or data handling practices, please contact our Data Grievance Officer:
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
