<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'EMS REST API Documentation' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('frontent/assets/img/favicon.png') }}">

    <!-- Google Fonts & Font Awesome & Tabler Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Prism.js for Syntax Highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

    <style>
        :root {
            --brand-primary: #4f46e5;
            --brand-primary-rgb: 79, 70, 229;
            --brand-dark: #0f172a;
            --brand-surface: #1e293b;
            --sidebar-width: 290px;
            --header-height: 64px;
            --method-get: #10b981;
            --method-post: #3b82f6;
            --method-put: #f59e0b;
            --method-delete: #ef4444;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        code, pre, .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Top Navbar */
        .api-navbar {
            height: var(--header-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        /* Sidebar Navigation */
        .api-sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            position: fixed;
            top: var(--header-height);
            left: 0;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            z-index: 1020;
            padding: 1.25rem 0.75rem 2rem 1rem;
        }

        .sidebar-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 700;
            margin: 1.25rem 0 0.5rem 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.65rem;
            border-radius: 8px;
            color: #475569;
            font-size: 0.84rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .sidebar-link.active {
            background-color: #eef2ff;
            color: var(--brand-primary);
            font-weight: 600;
        }

        /* Method Badges */
        .badge-method {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            min-width: 44px;
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
        }

        .badge-method.GET { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-method.POST { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-method.PUT { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-method.DELETE { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* Main Content Wrapper */
        .api-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2.25rem 2.5rem 5rem 2.5rem;
            max-width: 1440px;
        }

        /* Endpoint Card */
        .endpoint-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            margin-bottom: 2.25rem;
            box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            scroll-margin-top: calc(var(--header-height) + 1.5rem);
        }

        .endpoint-header {
            padding: 1.15rem 1.5rem;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: gap-2;
        }

        .endpoint-uri-bar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.4rem 0.85rem;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
        }

        /* Code & Sandbox Box */
        .code-console {
            background: var(--brand-dark);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #334155;
        }

        .code-console-header {
            background: var(--brand-surface);
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #334155;
        }

        .nav-tabs-code .nav-link {
            color: #94a3b8;
            font-size: 0.78rem;
            padding: 0.35rem 0.65rem;
            border: none;
            background: transparent;
            font-weight: 500;
        }

        .nav-tabs-code .nav-link.active {
            color: #38bdf8;
            border-bottom: 2px solid #38bdf8;
            background: transparent;
        }

        pre[class*="language-"] {
            margin: 0 !important;
            padding: 1rem 1.25rem !important;
            background: var(--brand-dark) !important;
            font-size: 0.8rem !important;
            border-radius: 0 0 10px 10px;
            max-height: 420px;
        }

        /* Token Config Banner */
        .token-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            border-radius: 12px;
            color: #ffffff;
            padding: 1.25rem 1.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.25);
        }

        /* Parameter Table */
        .param-table {
            font-size: 0.84rem;
        }

        .param-table th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.65rem 0.85rem;
        }

        .param-table td {
            padding: 0.75rem 0.85rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .param-required {
            color: #e11d48;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .param-optional {
            color: #64748b;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        /* Copy Button */
        .btn-copy {
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.25rem 0.55rem;
            font-size: 0.72rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-copy:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Live Tester Drawer */
        .test-drawer {
            display: none;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }

        @media (max-width: 991.98px) {
            .api-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .api-sidebar.show {
                transform: translateX(0);
            }
            .api-content {
                margin-left: 0;
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- TOP NAVBAR -->
    <header class="api-navbar d-flex align-items-center justify-content-between px-3 px-lg-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-lg-none border" id="sidebarToggleBtn">
                <i class="ti ti-menu-2 fs-18"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary text-white rounded-3 p-1.5 d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;">
                    <i class="ti ti-api fs-20"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark fs-15">EMS REST API Docs</h6>
                    <span class="text-muted fs-11">v1.0 &bull; Developer Reference</span>
                </div>
            </div>
        </div>

        <!-- Global Search Input -->
        <div class="d-none d-md-flex align-items-center position-relative" style="width: 320px;">
            <i class="ti ti-search position-absolute text-muted" style="left: 12px;"></i>
            <input type="text" id="endpointSearchInput" class="form-control form-control-sm ps-4 pe-4 bg-light border-0" placeholder="Search endpoints (press '/' to focus)..." style="border-radius: 20px;">
            <span class="position-absolute text-muted fs-11" style="right: 12px; pointer-events: none;"><kbd class="bg-white text-muted border px-1.5 py-0.5 rounded">/</kbd></span>
        </div>

        <!-- Right Quick Actions -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('project.workflow') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 rounded-pill px-3 shadow-sm">
                <i class="ti ti-route fs-15"></i>
                <span class="d-none d-sm-inline">Project Workflow Guide</span>
            </a>
            <a href="{{ route('api.docs.postman') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 rounded-pill px-3 shadow-sm" download>
                <i class="ti ti-download fs-15"></i>
                <span class="d-none d-sm-inline">Postman Collection</span>
            </a>
            @auth
                <a href="{{ route('home') }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1.5 rounded-pill px-3">
                    <i class="ti ti-dashboard fs-15"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1.5 rounded-pill px-3">
                    <i class="ti ti-login fs-15"></i>
                    <span class="d-none d-sm-inline">Portal Login</span>
                </a>
            @endauth
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="api-sidebar" id="apiSidebar">
        <div class="mb-3 px-1">
            <label class="text-muted fs-11 fw-semibold text-uppercase mb-1">Base API URL</label>
            <div class="input-group input-group-sm">
                <input type="text" id="globalBaseUrlInput" class="form-control font-mono fs-11 bg-light border" value="{{ $baseUrl }}" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="copyText('{{ $baseUrl }}', this)" title="Copy Base URL">
                    <i class="ti ti-copy"></i>
                </button>
            </div>
        </div>

        <nav id="sidebarNav">
            <div class="sidebar-section-title">Overview</div>
            <a href="#section-introduction" class="sidebar-link active"><i class="ti ti-sparkles text-primary"></i> Introduction</a>
            <a href="#section-authentication" class="sidebar-link"><i class="ti ti-key text-warning"></i> Authentication</a>
            <a href="#section-status-codes" class="sidebar-link"><i class="ti ti-checklist text-info"></i> Status Codes</a>

            <div class="sidebar-section-title">1. Auth & Security (PIN)</div>
            <a href="#ep-login" class="sidebar-link"><span class="badge-method POST">POST</span> Universal Login</a>
            <a href="#ep-login-mobile" class="sidebar-link"><span class="badge-method POST">POST</span> Login with Mobile</a>
            <a href="#ep-login-empid" class="sidebar-link"><span class="badge-method POST">POST</span> Login with Emp ID</a>
            <a href="#ep-login-pin" class="sidebar-link"><span class="badge-method POST">POST</span> Quick PIN Login</a>
            <a href="#ep-pin-setup" class="sidebar-link"><span class="badge-method POST">POST</span> PIN Setup</a>
            <a href="#ep-pin-change" class="sidebar-link"><span class="badge-method POST">POST</span> Change PIN</a>
            <a href="#ep-pin-forgot" class="sidebar-link"><span class="badge-method POST">POST</span> Forgot PIN (OTP)</a>
            <a href="#ep-pin-reset" class="sidebar-link"><span class="badge-method POST">POST</span> Reset PIN</a>
            <a href="#ep-pin-toggle" class="sidebar-link"><span class="badge-method POST">POST</span> Enable / Disable PIN</a>
            <a href="#ep-pin-status" class="sidebar-link"><span class="badge-method GET">GET</span> PIN Status</a>
            <a href="#ep-auth-logs" class="sidebar-link"><span class="badge-method GET">GET</span> Security Logs</a>
            <a href="#ep-profile" class="sidebar-link"><span class="badge-method GET">GET</span> Profile</a>
            <a href="#ep-agencies" class="sidebar-link"><span class="badge-method GET">GET</span> Agencies List</a>
            <a href="#ep-logout" class="sidebar-link"><span class="badge-method POST">POST</span> Logout</a>

            <div class="sidebar-section-title">2. Attendance APIs</div>
            <a href="#ep-checkin" class="sidebar-link"><span class="badge-method POST">POST</span> Check-In</a>
            <a href="#ep-checkout" class="sidebar-link"><span class="badge-method POST">POST</span> Check-Out</a>
            <a href="#ep-att-today" class="sidebar-link"><span class="badge-method GET">GET</span> Today Status</a>
            <a href="#ep-att-history" class="sidebar-link"><span class="badge-method GET">GET</span> History</a>
            <a href="#ep-att-toggle" class="sidebar-link"><span class="badge-method POST">POST</span> Toggle (Legacy)</a>

            <div class="sidebar-section-title">3. Calendar APIs</div>
            <a href="#ep-cal-month" class="sidebar-link"><span class="badge-method GET">GET</span> Monthly Calendar</a>
            <a href="#ep-cal-day" class="sidebar-link"><span class="badge-method GET">GET</span> Date Details</a>

            <div class="sidebar-section-title">4. Leave Management</div>
            <a href="#ep-leave-types" class="sidebar-link"><span class="badge-method GET">GET</span> Leave Types</a>
            <a href="#ep-leave-apply" class="sidebar-link"><span class="badge-method POST">POST</span> Apply Leave</a>
            <a href="#ep-leave-list" class="sidebar-link"><span class="badge-method GET">GET</span> Applications</a>
            <a href="#ep-leave-details" class="sidebar-link"><span class="badge-method GET">GET</span> Leave Details</a>

            <div class="sidebar-section-title">5. Holidays</div>
            <a href="#ep-holiday-list" class="sidebar-link"><span class="badge-method GET">GET</span> Yearly List</a>
            <a href="#ep-holiday-restricted" class="sidebar-link"><span class="badge-method GET">GET</span> Restricted List</a>
            <a href="#ep-holiday-calendar" class="sidebar-link"><span class="badge-method GET">GET</span> Calendar Grouped</a>

            <div class="sidebar-section-title">6. Supervisor Management</div>
            <a href="#ep-sup-dashboard" class="sidebar-link"><span class="badge-method GET">GET</span> Team Dashboard</a>
            <a href="#ep-sup-attendance" class="sidebar-link"><span class="badge-method GET">GET</span> Team Attendance</a>
            <a href="#ep-sup-members" class="sidebar-link"><span class="badge-method GET">GET</span> Team Members</a>
            <a href="#ep-sup-meta-options" class="sidebar-link"><span class="badge-method GET">GET</span> Form Dropdowns</a>
            <a href="#ep-sup-member-create" class="sidebar-link"><span class="badge-method POST">POST</span> Add Subordinate</a>
            <a href="#ep-sup-member-profile" class="sidebar-link"><span class="badge-method GET">GET</span> Member Profile</a>
            <a href="#ep-sup-member-update" class="sidebar-link"><span class="badge-method PUT">PUT</span> Update Subordinate</a>
            <a href="#ep-sup-member-attendance" class="sidebar-link"><span class="badge-method GET">GET</span> Member Attendance</a>
            <a href="#ep-sup-leave-balances" class="sidebar-link"><span class="badge-method GET">GET</span> Member Leave Quotas</a>
            <a href="#ep-sup-leaves" class="sidebar-link"><span class="badge-method GET">GET</span> Team Leaves Queue</a>
            <a href="#ep-sup-leave-details" class="sidebar-link"><span class="badge-method GET">GET</span> Leave Conflicts</a>
            <a href="#ep-sup-leave-approve" class="sidebar-link"><span class="badge-method POST">POST</span> Approve Leave</a>
            <a href="#ep-sup-leave-reject" class="sidebar-link"><span class="badge-method POST">POST</span> Reject Leave</a>
            <a href="#ep-sup-leave-apply-member" class="sidebar-link"><span class="badge-method POST">POST</span> Apply Member Leave</a>
            <a href="#ep-sup-monthly-summary" class="sidebar-link"><span class="badge-method GET">GET</span> Monthly Summary</a>
            <a href="#ep-sup-report-summary" class="sidebar-link"><span class="badge-method GET">GET</span> Reports Overview</a>
            <a href="#ep-sup-report-attendance" class="sidebar-link"><span class="badge-method GET">GET</span> Punch Logs & Export</a>
            <a href="#ep-sup-report-attendance-register" class="sidebar-link"><span class="badge-method GET">GET</span> Attendance Register</a>
            <a href="#ep-sup-report-daily-work" class="sidebar-link"><span class="badge-method GET">GET</span> Daily Work Reports</a>
            <a href="#ep-sup-report-leaves" class="sidebar-link"><span class="badge-method GET">GET</span> Leave Reports & Export</a>
            <a href="#ep-sup-team-notify" class="sidebar-link"><span class="badge-method POST">POST</span> Broadcast Team Push</a>

            <div class="sidebar-section-title">7. Push Notifications (FCM)</div>
            <a href="#ep-fcm-register" class="sidebar-link"><span class="badge-method POST">POST</span> Register FCM Token</a>
            <a href="#ep-fcm-delete" class="sidebar-link"><span class="badge-method POST">POST</span> Unregister Token</a>
            <a href="#ep-notifications-list" class="sidebar-link"><span class="badge-method GET">GET</span> Notification Inbox</a>
            <a href="#ep-notification-read" class="sidebar-link"><span class="badge-method POST">POST</span> Mark as Read</a>
            <a href="#ep-notifications-read-all" class="sidebar-link"><span class="badge-method POST">POST</span> Mark All as Read</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="api-content">

        <!-- Global Interactive Token Bar -->
        <div class="token-banner d-md-flex align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-shield-lock fs-20"></i> Interactive Bearer Token Sandbox
                </h5>
                <p class="mb-0 fs-13 text-white-50">
                    Paste your Bearer token once here. It will automatically update all code snippets and power the live in-browser testing consoles below.
                </p>
            </div>
            <div class="mt-3 mt-md-0" style="min-width: 320px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white bg-opacity-25 border-0 text-white font-mono fs-12">Bearer</span>
                    <input type="text" id="globalBearerTokenInput" class="form-control border-0 font-mono fs-12" placeholder="Paste token (1|...)" oninput="updateGlobalToken(this.value)">
                    <button class="btn btn-light text-primary fw-semibold" type="button" onclick="testSavedToken()">Save</button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- OVERVIEW & AUTHENTICATION -->
        <!-- ========================================== -->
        <section id="section-introduction" class="mb-5">
            <h3 class="fw-bold text-dark mb-2">Introduction</h3>
            <p class="text-muted fs-15 leading-relaxed">
                Welcome to the <strong>EMS Visitor & Employee Portal API</strong> documentation. This API allows mobile apps, external clients, and integrations to authenticate employees, manage real-time attendance with geolocation, check interactive monthly calendars, submit leave requests, and view company holidays.
            </p>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <div class="p-3 bg-white border rounded-3 h-100 shadow-xs">
                        <div class="d-flex align-items-center gap-2 text-primary fw-semibold fs-14 mb-1">
                            <i class="ti ti-lock-access fs-18"></i> Sanctum Tokens
                        </div>
                        <p class="text-muted fs-13 mb-0">Secure state-of-the-art token authentication using Laravel Sanctum for mobile apps and clients.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-white border rounded-3 h-100 shadow-xs">
                        <div class="d-flex align-items-center gap-2 text-success fw-semibold fs-14 mb-1">
                            <i class="ti ti-map-pin fs-18"></i> Geo-Attendance
                        </div>
                        <p class="text-muted fs-13 mb-0">Precision GPS latitude, longitude, and client IP capturing with duplicate check prevention.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-white border rounded-3 h-100 shadow-xs">
                        <div class="d-flex align-items-center gap-2 text-purple fw-semibold fs-14 mb-1">
                            <i class="ti ti-file-text fs-18"></i> Standard JSON
                        </div>
                        <p class="text-muted fs-13 mb-0">Consistent JSON structure with explicit status codes, timestamps, and error dictionaries.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="section-authentication" class="mb-5">
            <h4 class="fw-bold text-dark mb-3">Authentication Headers</h4>
            <p class="text-muted fs-14">
                Except for the public login endpoint, all API calls require an <code>Authorization</code> header containing the Bearer token returned upon login:
            </p>
            <div class="code-console mb-3">
                <div class="code-console-header text-muted fs-12 font-mono">Request Headers Example</div>
                <pre class="language-http"><code>Accept: application/json
Content-Type: application/json
Authorization: Bearer 1|nK0R2bL8uP3qXyZ87163f8d...</code></pre>
            </div>
        </section>

        <section id="section-status-codes" class="mb-5">
            <h4 class="fw-bold text-dark mb-3">HTTP Status Codes</h4>
            <div class="table-responsive bg-white border rounded-3 shadow-xs">
                <table class="table param-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Code</th>
                            <th style="width: 180px;">Status</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle font-mono">200</span></td>
                            <td class="fw-semibold">OK</td>
                            <td>Request succeeded. Requested data returned in <code>data</code> field.</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle font-mono">201</span></td>
                            <td class="fw-semibold">Created</td>
                            <td>Resource created successfully (e.g. Leave application submitted).</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle font-mono">401</span></td>
                            <td class="fw-semibold">Unauthorized</td>
                            <td>Missing, expired, or invalid Bearer token / credentials.</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">403</span></td>
                            <td class="fw-semibold">Forbidden</td>
                            <td>Account is inactive or token does not have employee permissions.</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle font-mono">409</span></td>
                            <td class="fw-semibold">Conflict</td>
                            <td>Duplicate action (e.g. check-in already marked for today).</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">422</span></td>
                            <td class="fw-semibold">Unprocessable Content</td>
                            <td>Validation error (missing fields, invalid date formats, overlapping leave).</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <hr class="my-5 border-slate-200">

        <!-- ========================================== -->
        <!-- 1. AUTH & PROFILE APIS -->
        <!-- ========================================== -->
        <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-user-check text-primary"></i> 1. Authentication & Profile APIs
        </h4>

        <!-- 1.1 Login -->
        <div class="endpoint-card" id="ep-login">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Employee Login</span>
                    <span class="badge bg-light text-muted border fs-11">Public</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/login</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/login', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Authenticates an employee using their <strong>Email</strong>, <strong>Mobile number</strong>, or unique <strong>Employee ID</strong>, along with their account password. Returns a Bearer token and employee profile summary.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">login</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Employee Email, Mobile, or ID</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">password</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional*</span></td>
                                        <td>Account password (*required if PIN omitted)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional*</span></td>
                                        <td>4 to 6-digit numeric PIN (*required if password omitted)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">device_name</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Device / Client name (Default: Mobile App)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "login": "7668036640",
  "password": "Password@123",
  "pin": "1234",
  "device_name": "Mobile App"
}</code></pre>
                            </div>
                        </div>

                        <button class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5" onclick="toggleTestDrawer('drawer-login')">
                            <i class="ti ti-player-play fs-14"></i> Try in Browser
                        </button>
                    </div>

                    <div class="col-lg-6">
                        <!-- Code Console -->
                        <div class="code-console">
                            <div class="code-console-header">
                                <ul class="nav nav-tabs nav-tabs-code" role="tablist">
                                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#curl-login">cURL</button></li>
                                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#js-login">JS</button></li>
                                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#php-login">PHP</button></li>
                                </ul>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="curl-login">
                                    <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "login": "emp@example.com",
    "password": "Password@123",
    "device_name": "Samsung Galaxy S24"
  }'</code></pre>
                                </div>
                                <div class="tab-pane fade" id="js-login">
                                    <pre class="language-javascript"><code>const response = await fetch("{{ $baseUrl }}/v1/employee/auth/login", {
  method: "POST",
  headers: { "Content-Type": "application/json", "Accept": "application/json" },
  body: JSON.stringify({
    login: "emp@example.com",
    password: "Password@123",
    device_name: "Mobile App"
  })
});
const data = await response.json();</code></pre>
                                </div>
                                <div class="tab-pane fade" id="php-login">
                                    <pre class="language-php"><code>$client = new \GuzzleHttp\Client();
$res = $client->post('{{ $baseUrl }}/v1/employee/auth/login', [
    'json' => [
        'login' => 'emp@example.com',
        'password' => 'Password@123'
    ]
]);</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Response Preview -->
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Employee login successful.",
  "data": {
    "token": "1|nK0R2bL8uP3qXyZ87163f8d...",
    "token_type": "Bearer",
    "employee": {
      "employee_id": 101,
      "name": "John Doe",
      "email": "emp@example.com",
      "mobile": "9876543210",
      "designation": "Software Engineer",
      "agency_id": 2,
      "agency_name": "Neural Info Overseas Private Limited",
      "agency_code": "NIOPL-001",
      "agency": {
        "id": 2,
        "agency_name": "Neural Info Overseas Private Limited",
        "agency_code": "NIOPL-001"
      },
      "status": "Active"
    }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Drawer for Login -->
            <div class="test-drawer" id="drawer-login">
                <h6 class="fw-bold fs-13 text-dark mb-2">Live Tester: Employee Login</h6>
                <div class="row g-2 mb-2">
                    <div class="col-sm-5">
                        <input type="text" id="test-login-user" class="form-control form-control-sm font-mono" placeholder="Email/Mobile/ID" value="mrsrivastava@neuralinfo.org">
                    </div>
                    <div class="col-sm-5">
                        <input type="password" id="test-login-pass" class="form-control form-control-sm font-mono" placeholder="Password">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-sm btn-primary w-100" onclick="executeLoginTest()">Run Test</button>
                    </div>
                </div>
                <div id="test-login-result" style="display: none;">
                    <span class="badge mb-1 fs-11" id="test-login-status"></span>
                    <pre class="p-2 bg-dark text-white rounded font-mono fs-12 mb-0" id="test-login-output"></pre>
                </div>
            </div>
        </div>

        <!-- 1.2 Login with Mobile Number -->
        <div class="endpoint-card" id="ep-login-mobile">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Login with Mobile Number</span>
                    <span class="badge bg-light text-muted border fs-11">Public</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/login/mobile</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/login/mobile', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Allows employees to authenticate specifically using their 10-digit registered <strong>Mobile Number</strong> along with either their password or their 4-6 digit numeric PIN.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">mobile</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>10-digit registered mobile number</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">password</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional*</span></td>
                                        <td>Account password (*required if PIN omitted)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional*</span></td>
                                        <td>4-6 digit numeric PIN (*required if password omitted)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">device_name</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Device / Client name</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "mobile": "7668036640",
  "pin": "1234",
  "device_name": "Mobile App"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/login/mobile" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "mobile": "7668036640",
    "pin": "1234",
    "device_name": "Android Client"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "2|hD92vX...plainTextToken",
    "token_type": "Bearer",
    "employee": {
      "employee_id": 101,
      "name": "Mahendra Sahu",
      "mobile": "9876500001",
      "designation": "Senior Developer",
      "is_pin_enabled": true
    }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.3 Login with Employee ID -->
        <div class="endpoint-card" id="ep-login-empid">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Login with Employee ID</span>
                    <span class="badge bg-light text-muted border fs-11">Public</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/login/empid</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/login/empid', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Authenticates an employee using their official <strong>Employee ID</strong> (numeric ID or organization employee code) with either password or security PIN.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">employee_id</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Employee ID number or identifier</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">password</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional*</span></td>
                                        <td>Account password (*required if PIN omitted)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional*</span></td>
                                        <td>4-6 digit numeric PIN (*required if password omitted)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">device_name</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Device / Client name</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "employee_id": "101",
  "pin": "1234",
  "device_name": "Mobile App"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/login/empid" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "employee_id": "101",
    "pin": "1234"
  }'</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.4 Quick PIN Login -->
        <div class="endpoint-card" id="ep-login-pin">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Quick PIN Login</span>
                    <span class="badge bg-light text-muted border fs-11">Public</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/login/pin</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/login/pin', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Fast biometric/passcode style unlock endpoint. Authenticates an employee using any identifier (Mobile, Email, or Employee ID) combined with their 4-6 digit numeric PIN.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">login</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Mobile number, Email, or Employee ID</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>4 to 6 digit numeric security PIN</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "login": "7668036640",
  "pin": "1234"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/login/pin" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "login": "7668036640",
    "pin": "1234"
  }'</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.5 PIN Setup -->
        <div class="endpoint-card" id="ep-pin-setup">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">PIN Setup</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/pin/setup</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/pin/setup', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Allows employees to create and set a secure <strong>4-6 digit numeric PIN</strong> after initial login or account activation. PIN is encrypted using Bcrypt. If a PIN is already set on the account, the employee's current account password must be provided to verify identity.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>4 to 6 digit numeric PIN (regex: <code>^[0-9]{4,6}$</code>)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin_confirmation</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Must match <code>pin</code> exactly</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">password</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Conditional</span></td>
                                        <td>Current password (only required if overriding an existing PIN)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "pin": "1234",
  "pin_confirmation": "1234",
  "password": "Password@123"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/pin/setup" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "pin": "1234",
    "pin_confirmation": "1234"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Security PIN created and enabled successfully.",
  "data": {
    "is_pin_set": true,
    "is_pin_enabled": true,
    "pin_set_at": "2026-09-22T17:30:00+05:30"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.6 Change PIN -->
        <div class="endpoint-card" id="ep-pin-change">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Change PIN</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/pin/change</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/pin/change', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Allows employees to change their existing PIN after verifying their current active PIN. Validates that the new PIN is different from the current one.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">current_pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Current active 4-6 digit numeric PIN</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">new_pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>New 4-6 digit numeric PIN (must differ from current)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">new_pin_confirmation</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Must match <code>new_pin</code> exactly</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "current_pin": "1234",
  "new_pin": "5678",
  "new_pin_confirmation": "5678"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/pin/change" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "current_pin": "1234",
    "new_pin": "5678",
    "new_pin_confirmation": "5678"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "PIN changed successfully.",
  "data": {
    "is_pin_set": true,
    "is_pin_enabled": true,
    "pin_set_at": "2026-09-22T17:35:00+05:30"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.7 Forgot PIN (OTP Request) -->
        <div class="endpoint-card" id="ep-pin-forgot">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Forgot PIN / Request OTP</span>
                    <span class="badge bg-light text-muted border fs-11">Public</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/pin/forgot-request</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/pin/forgot-request', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Initiates the PIN recovery process. Generates a secure 6-digit numeric OTP valid for 10 minutes sent to the employee's registered contact information.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">identifier</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Registered Mobile number, Email, or Employee ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "identifier": "7668036640"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/pin/forgot-request" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "identifier": "7668036640"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "A 6-digit OTP has been generated for PIN recovery. It will expire in 10 minutes.",
  "data": {
    "identifier": "7668036640",
    "masked_mobile": "******6640",
    "expires_in_minutes": 10,
    "expires_at": "2026-09-22T17:45:00+05:30",
    "otp_code": "856651"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.8 Reset PIN with OTP -->
        <div class="endpoint-card" id="ep-pin-reset">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Reset PIN with OTP</span>
                    <span class="badge bg-light text-muted border fs-11">Public</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/pin/reset</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/pin/reset', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Validates the 6-digit OTP generated by the Forgot PIN request and creates a new security PIN. Automatically enables PIN login and invalidates the OTP.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">identifier</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Registered Mobile, Email, or Employee ID</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">otp</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>6-digit numeric OTP received</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">new_pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>New 4 to 6 digit numeric PIN</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">new_pin_confirmation</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Must match <code>new_pin</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "identifier": "7668036640",
  "otp": "856651",
  "new_pin": "9999",
  "new_pin_confirmation": "9999"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/pin/reset" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "identifier": "7668036640",
    "otp": "856651",
    "new_pin": "9999",
    "new_pin_confirmation": "9999"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Your security PIN has been reset successfully. You can now login using your new PIN.",
  "data": {
    "is_pin_set": true,
    "is_pin_enabled": true,
    "pin_set_at": "2026-09-22T17:40:00+05:30"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.9 Enable / Disable PIN Login (Toggle) -->
        <div class="endpoint-card" id="ep-pin-toggle">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Enable / Disable PIN Login</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/pin/toggle</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/pin/toggle', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Enables or disables PIN-based authentication for the logged-in employee. Current PIN verification is mandatory to prevent unauthorized modification.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">enable</td>
                                        <td class="font-mono text-muted">boolean</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td><code>true</code> to enable PIN login, <code>false</code> to disable</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono fw-semibold text-primary">pin</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Current 4-6 digit numeric PIN for authorization</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "enable": true,
  "pin": "1234"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/auth/pin/toggle" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "enable": true,
    "pin": "1234"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "PIN login has been enabled successfully.",
  "data": {
    "is_pin_set": true,
    "is_pin_enabled": true,
    "pin_set_at": "2026-09-22T17:30:00+05:30"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.10 PIN Status -->
        <div class="endpoint-card" id="ep-pin-status">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get PIN Status</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/auth/pin/status</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/pin/status', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Checks whether the employee currently has a security PIN configured, whether PIN authentication is enabled, and retrieves the last login timestamp.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Required Headers</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Header</th><th>Value</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary fw-semibold">Authorization</td><td class="font-mono text-muted">Bearer &lt;token&gt;</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">Accept</td><td class="font-mono text-muted">application/json</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/auth/pin/status" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "data": {
    "employee_id": 101,
    "is_pin_set": true,
    "is_pin_enabled": true,
    "pin_set_at": "2026-09-22T17:30:00+05:30",
    "last_login_at": "2026-09-22T17:28:45+05:30"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.11 Security Activity Logs -->
        <div class="endpoint-card" id="ep-auth-logs">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Authentication & PIN Activity Logs</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/auth/activity-logs</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/activity-logs', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>

            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves the last 20 authentication and PIN security audit records for the employee, including login events, PIN creations/changes, device IP, and timestamp.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Required Headers</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Header</th><th>Value</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary fw-semibold">Authorization</td><td class="font-mono text-muted">Bearer &lt;token&gt;</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">Accept</td><td class="font-mono text-muted">application/json</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/auth/activity-logs" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "data": [
    {
      "id": 45,
      "action": "login_pin",
      "status": "success",
      "ip_address": "127.0.0.1",
      "device_name": "Samsung Galaxy S24",
      "created_at": "2026-09-22T17:28:45+05:30"
    },
    {
      "id": 44,
      "action": "pin_change",
      "status": "success",
      "ip_address": "127.0.0.1",
      "device_name": "Samsung Galaxy S24",
      "created_at": "2026-09-22T17:20:10+05:30"
    }
  ]
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.12 Profile -->
        <div class="endpoint-card" id="ep-profile">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Employee Profile</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/profile</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/profile', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves full profile details for the authenticated employee including designation, supervisor info, work shift timings, posting location, employment type, confirmation dates, and PIN status.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Required Headers</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Header</th><th>Value</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">Authorization</td>
                                        <td class="font-mono text-muted">Bearer &lt;token&gt;</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">Accept</td>
                                        <td class="font-mono text-muted">application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/profile" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "data": {
    "employee_id": 101,
    "name": "Mahendra Sahu",
    "email": "mahendra.s@neuralinfo.org",
    "mobile": "9876500001",
    "designation": { "id": 1, "name": "Senior Developer" },
    "agency": {
      "id": 1,
      "agency_name": "Neural Info Overseas Private Limited",
      "agency_code": "NIOPL-001",
      "status": "Active"
    },
    "shift": {
      "id": 1,
      "shift_name": "General Shift",
      "start_time": "09:30:00",
      "end_time": "18:30:00",
      "grace_period": 15,
      "break_duration": 60
    },
    "location": {
      "country": "India",
      "state": "Uttar Pradesh",
      "city": "Kanpur"
    },
    "is_pin_set": true,
    "is_pin_enabled": true,
    "employment_type": "Full Time",
    "status": "Active"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.13 Agencies List -->
        <div class="endpoint-card" id="ep-agencies">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Agencies List</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/agencies</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/agencies', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves all active staffing agencies registered in the system with their code, contact details, and locations.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Required Headers</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Header</th><th>Value</th></tr></thead>
                                <tbody>
                                    <tr><td class="font-mono text-primary fw-semibold">Authorization</td><td class="font-mono text-muted">Bearer &lt;token&gt;</td></tr>
                                    <tr><td class="font-mono text-primary fw-semibold">Accept</td><td class="font-mono text-muted">application/json</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/agencies" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.14 Logout -->
        <div class="endpoint-card" id="ep-logout">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Employee Logout</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/auth/logout</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/auth/logout', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-0">
                    Revokes the currently active token so it cannot be used again. Returns success confirmation.
                </p>
            </div>
        </div>

        <hr class="my-5 border-slate-200">

        <!-- ========================================== -->
        <!-- 2. ATTENDANCE APIS -->
        <!-- ========================================== -->
        <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-fingerprint text-success"></i> 2. Attendance Management APIs
        </h4>

        <!-- 2.1 Check-In -->
        <div class="endpoint-card" id="ep-checkin">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Mark Check-In</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/attendance/check-in</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/attendance/check-in', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Marks attendance check-in for the current date. Automatically records client IP, timestamp, and prevents duplicate check-ins for the same day.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Body (JSON)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">latitude</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>GPS Latitude (-90 to 90)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">longitude</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>GPS Longitude (-180 to 180)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">note</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Location / Check-in remarks (max: 1000)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">device_info</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Client device name/model</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "latitude": 23.259933,
  "longitude": 77.412613,
  "note": "Office Main Gate Check-in",
  "device_info": "Samsung Galaxy S24"
}</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/attendance/check-in" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "latitude": 23.259933,
    "longitude": 77.412613,
    "note": "Office Main Gate"
  }'</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2.2 Check-Out -->
        <div class="endpoint-card" id="ep-checkout">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Mark Check-Out</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/attendance/check-out</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/attendance/check-out', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Marks attendance check-out. Automatically verifies that check-in was recorded first, and computes total worked minutes and formatted hours (e.g. <code>9h 15m</code>).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Body (JSON)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">latitude</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>GPS Latitude (-90 to 90)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">longitude</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>GPS Longitude (-180 to 180)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">note</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Check-out remarks (max: 1000)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">device_info</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Client device name/model</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "latitude": 23.259933,
  "longitude": 77.412613,
  "note": "Office Exit Check-out",
  "device_info": "Samsung Galaxy S24"
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/attendance/check-out" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "latitude": 23.259933,
    "longitude": 77.412613,
    "note": "Office Exit Check-out"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 200 OK Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Checked out successfully at 06:30 PM.",
  "data": {
    "attendance_id": 154,
    "attendance_date": "2026-09-22",
    "check_in_at": "09:30 AM",
    "check_out_at": "06:30 PM",
    "working_minutes": 540,
    "working_hours": "9h 00m",
    "status": "checked_out"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2.3 Today Status -->
        <div class="endpoint-card" id="ep-att-today">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Today's Attendance Status</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/attendance/today</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/attendance/today', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-0">
                    Returns today's real-time check-in and check-out status, times, and working duration.
                </p>
            </div>
        </div>

        <!-- 2.4 History -->
        <div class="endpoint-card" id="ep-att-history">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Attendance History</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/attendance/history</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/attendance/history', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves paginated attendance logs with optional date range filters (<code>start_date</code>, <code>end_date</code>), status filter, and aggregate total working hours for the selected period.
                </p>
                <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                <div class="table-responsive border rounded-3 mb-0">
                    <table class="table param-table mb-0">
                        <thead>
                            <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-mono text-primary fw-semibold">start_date</td>
                                <td class="font-mono text-muted">date</td>
                                <td><span class="param-optional">Optional</span></td>
                                <td>Format: <code>YYYY-MM-DD</code> (e.g. <code>2026-09-01</code>)</td>
                            </tr>
                            <tr>
                                <td class="font-mono text-primary fw-semibold">end_date</td>
                                <td class="font-mono text-muted">date</td>
                                <td><span class="param-optional">Optional</span></td>
                                <td>Format: <code>YYYY-MM-DD</code> (&gt;= start_date)</td>
                            </tr>
                            <tr>
                                <td class="font-mono text-primary fw-semibold">status</td>
                                <td class="font-mono text-muted">string</td>
                                <td><span class="param-optional">Optional</span></td>
                                <td><code>checked_in</code>, <code>checked_out</code>, <code>present</code>, <code>absent</code>, <code>half_day</code></td>
                            </tr>
                            <tr>
                                <td class="font-mono text-primary fw-semibold">per_page</td>
                                <td class="font-mono text-muted">integer</td>
                                <td><span class="param-optional">Optional</span></td>
                                <td>1 to 100 (Default: 20)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2.5 Attendance Quick Toggle (Smart Punch) -->
        <div class="endpoint-card" id="ep-att-toggle">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Attendance Smart Toggle (Check-In / Check-Out)</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/employee/attendance/toggle</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ url('/api/employee/attendance/toggle') }}', this)" title="Copy URL"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Intelligent single-button toggle endpoint. If not punched in today, performs Check-In; if already punched in and not checked out, performs Check-Out; if already checked out, returns conflict status.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Body (JSON)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">latitude</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>GPS Latitude coordinate</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">longitude</td>
                                        <td class="font-mono text-muted">numeric</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>GPS Longitude coordinate</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">note</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Punch location or remarks</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "latitude": 23.259933,
  "longitude": 77.412613,
  "note": "Smart mobile button tap"
}</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ url('/api/employee/attendance/toggle') }}" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "latitude": 23.259933,
    "longitude": 77.412613,
    "note": "Smart mobile button tap"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "action": "check_in",
  "message": "Checked in successfully at 09:30 AM",
  "data": {
    "attendance_id": 482,
    "check_in_at": "2026-10-01 09:30:15",
    "check_out_at": null,
    "status": "checked_in",
    "working_time_clock": "00:00:00"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-slate-200">

        <!-- ========================================== -->
        <!-- 3. CALENDAR APIS -->
        <!-- ========================================== -->
        <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-calendar text-info"></i> 3. Calendar & Scheduling APIs
        </h4>

        <!-- 3.1 Month Calendar -->
        <div class="endpoint-card" id="ep-cal-month">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Monthly Aggregated Calendar</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/calendar?month=9&year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/calendar?month=9&year=2026', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Computes a complete day-by-day month schedule for mobile calendar views. Consolidates:
                    <strong>Attendance records (Green)</strong>, <strong>Sundays / Weekly Offs (Amber)</strong>, <strong>Public Holidays (Cyan)</strong>, and <strong>Approved Leaves (Purple)</strong>. Includes overall counts of present, absent, leaves, and holidays.
                </p>
                <div class="code-console">
                    <div class="code-console-header">
                        <span class="fs-12 text-muted font-mono">cURL Example</span>
                        <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                    </div>
                    <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/calendar?month=9&year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                </div>
            </div>
        </div>

        <!-- 3.2 Date Details -->
        <div class="endpoint-card" id="ep-cal-day">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Date Details Breakdown</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/calendar/date-details?date=2026-10-01</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/calendar/date-details?date=2026-10-01', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Provides exact attendance punch records, public holiday info, or approved leave details for a single selected calendar day.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">date</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Target date format: <code>YYYY-MM-DD</code> (e.g. <code>2026-10-01</code>)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/calendar/date-details?date=2026-10-01" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "date": "2026-10-01",
    "day": "Thursday",
    "attendance": {
      "id": 482,
      "check_in_at": "2026-10-01 09:30:15",
      "check_out_at": "2026-10-01 18:35:42",
      "working_hours": "9h 5m 27s",
      "status": "PRESENT"
    },
    "holiday": null,
    "leave": null,
    "is_weekly_off": false
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-slate-200">

        <!-- ========================================== -->
        <!-- 4. LEAVE MANAGEMENT APIS -->
        <!-- ========================================== -->
        <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-plane-departure text-purple"></i> 4. Leave Management APIs
        </h4>

        <!-- 4.1 Leave Types -->
        <div class="endpoint-card" id="ep-leave-types">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Active Leave Types</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/leave/types</span>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Returns active leave types configured in the system (Casual Leave, Sick Leave, Earned Leave, Maternity Leave) along with <strong>individual employee quotas</strong>, approved used days, pending application counts, remaining balances, gender applicability checks, and an overall annual leave balance summary.
                </p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters (Optional)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">year</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Calendar year for leave balance calculation (e.g. <code>2026</code>). Defaults to current year.</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">applicable_only</td>
                                        <td class="font-mono text-muted">boolean</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Pass <code>1</code> or <code>true</code> to filter and return only leave types applicable to the employee's gender.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-3">
                            <h6 class="fw-bold text-dark fs-13 mb-2"><i class="ti ti-info-circle text-primary me-1"></i> Calculation & Logic Details:</h6>
                            <ul class="fs-12 text-muted mb-0 ps-3">
                                <li><strong>Allotted Quota:</strong> Evaluated from designation mappings in <code>leave_mappings</code> first. If not overridden, falls back to default base quota from <code>leave_types.total_leaves</code>.</li>
                                <li><strong>Used Days:</strong> Sum of approved leave durations in the requested year.</li>
                                <li><strong>Pending Days:</strong> Leaves currently submitted and pending approval.</li>
                                <li><strong>Remaining Days:</strong> <code>max(allotted - used, 0)</code>.</li>
                                <li><strong>Available Days:</strong> <code>max(allotted - used - pending, 0)</code>.</li>
                                <li><strong>Gender Applicability:</strong> Checks employee gender against <code>applicable_for</code> (e.g. Maternity Leave is marked <code>is_applicable: false</code> for male employees).</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider">
                                <i class="ti ti-terminal-2 me-1 text-success"></i> Sample 200 OK Response:
                            </span>
                            <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-11 text-success font-mono">200 OK - application/json</span>
                            </div>
                            <pre class="language-json"><code>{
  "success": true,
  "summary": {
    "year": 2026,
    "total_allotted": 24,
    "total_used": 3,
    "total_pending": 0,
    "total_remaining": 21,
    "total_available": 21
  },
  "data": [
    {
      "id": 1,
      "name": "Casual Leave",
      "leave_name": "Casual Leave",
      "code": "CL-001",
      "leave_code": "CL-001",
      "type": "Paid",
      "category": "Paid",
      "leave_category": "Paid",
      "days": 6,
      "total_leaves": 6,
      "allotted_days": 6,
      "used_days": 3,
      "used_leaves": 3,
      "pending_days": 0,
      "remaining_days": 3,
      "remaining_leaves": 3,
      "available_days": 3,
      "carry_forward": true,
      "encashable": false,
      "applicable_for": "All",
      "is_applicable": true,
      "not_applicable_reason": null,
      "description": "Casual leave for personal work or emergencies.",
      "color": "#8B5CF6",
      "icon": "calendar-heart",
      "status": "Active"
    },
    {
      "id": 2,
      "name": "Sick Leave",
      "leave_name": "Sick Leave",
      "code": "SL-001",
      "leave_code": "SL-001",
      "type": "Paid",
      "category": "Paid",
      "leave_category": "Paid",
      "days": 8,
      "total_leaves": 8,
      "allotted_days": 8,
      "used_days": 0,
      "used_leaves": 0,
      "pending_days": 0,
      "remaining_days": 8,
      "remaining_leaves": 8,
      "available_days": 8,
      "carry_forward": true,
      "encashable": false,
      "applicable_for": "All",
      "is_applicable": true,
      "not_applicable_reason": null,
      "description": "Sick leave for medical or health-related issues.",
      "color": "#EF4444",
      "icon": "activity",
      "status": "Active"
    },
    {
      "id": 4,
      "name": "Maternity Leave",
      "leave_name": "Maternity Leave",
      "code": "ML-001",
      "leave_code": "ML-001",
      "type": "Special",
      "category": "Special",
      "leave_category": "Special",
      "days": 0,
      "total_leaves": 0,
      "allotted_days": 0,
      "used_days": 0,
      "used_leaves": 0,
      "pending_days": 0,
      "remaining_days": 0,
      "remaining_leaves": 0,
      "available_days": 0,
      "carry_forward": false,
      "encashable": false,
      "applicable_for": "Female",
      "is_applicable": false,
      "not_applicable_reason": "Applicable for female employees only",
      "description": "Leave for female employees during maternity period.",
      "color": "#EC4899",
      "icon": "heart",
      "status": "Active"
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4.2 Apply Leave -->
        <div class="endpoint-card" id="ep-leave-apply">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method POST fs-12">POST</span>
                    <span class="fw-bold text-dark fs-15">Apply for Leave</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-primary font-mono fw-semibold">/api/v1/employee/leave/apply</span>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Submits a new leave request. Automatically prevents overlapping requests, logs an activity event, and optionally attaches supporting files (PDF/Images up to 5MB).
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Request Parameters (JSON Body)</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead>
                                    <tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">leave_type_id</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Valid ID from <code>/leave/types</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">from_date</td>
                                        <td class="font-mono text-muted">date</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Format: <code>YYYY-MM-DD</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">to_date</td>
                                        <td class="font-mono text-muted">date</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Format: <code>YYYY-MM-DD</code> (&gt;= from_date)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">reason</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Reason for application (max: 2000)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">document</td>
                                        <td class="font-mono text-muted">file</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Medical certificate or proof (pdf, jpg, png &le; 5MB)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 mb-3">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-block mb-1">
                                <i class="ti ti-code me-1 text-primary"></i> Request Body (Raw JSON Payload):
                            </span>
                            <div class="code-console">
                                <div class="code-console-header">
                                    <span class="fs-11 text-muted font-mono">application/json</span>
                                    <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy JSON</button>
                                </div>
                                <pre class="language-json"><code>{
  "leave_type_id": 1,
  "from_date": "2026-10-01",
  "to_date": "2026-10-03",
  "reason": "Personal work at home"
}</code></pre>
                            </div>
                            <small class="text-muted fs-11 mt-1 d-block">
                                <i class="ti ti-info-circle"></i> Note: When uploading a <code>document</code> file, send the request as <code>multipart/form-data</code>.
                            </small>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X POST "{{ $baseUrl }}/v1/employee/leave/apply" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "leave_type_id": 1,
    "from_date": "2026-10-01",
    "to_date": "2026-10-03",
    "reason": "Personal work at home"
  }'</code></pre>
                        </div>
                        <div class="mt-3">
                            <span class="fs-12 fw-bold text-muted text-uppercase">Sample 201 Created Response:</span>
                            <div class="code-console mt-1">
                                <pre class="language-json"><code>{
  "success": true,
  "message": "Leave application submitted successfully.",
  "data": {
    "leave_id": 42,
    "leave_type": "Casual Leave",
    "from_date": "2026-10-01",
    "to_date": "2026-10-03",
    "duration_days": 3,
    "status": "SENT",
    "reason": "Personal work at home",
    "applied_at": "2026-09-22 18:30:00"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4.3 List Applications -->
        <div class="endpoint-card" id="ep-leave-list">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">List Employee's Leaves</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/leave/list?status=ALL&year=2026&per_page=15</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/leave/list', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves user's leave requests with status filters: <code>ALL</code>, <code>DRAFT</code>, <code>SENT</code> (Pending), <code>APPROVED</code>, <code>REJECTED</code>, plus year filter and pagination.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">status</td>
                                        <td class="font-mono text-muted">string</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td><code>ALL</code>, <code>SENT</code> (Pending), <code>APPROVED</code>, <code>REJECTED</code>, <code>DRAFT</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">year</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Filter by calendar year (e.g. <code>2026</code>)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">per_page</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Records per page (1 - 100, default 15)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/leave/list?status=ALL&year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "leaves": [
      {
        "id": 42,
        "leave_type": "Casual Leave",
        "from_date": "2026-10-01",
        "to_date": "2026-10-03",
        "duration_days": 3,
        "status": "APPROVED",
        "status_label": "Approved",
        "reason": "Personal work at home",
        "supervisor_remarks": "Approved",
        "created_at": "2026-09-22 18:30:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total_records": 1
    }
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4.4 Leave Details -->
        <div class="endpoint-card" id="ep-leave-details">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Leave Details</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/employee/leave/{id}</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/employee/leave/42', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Fetches full leave record including supervisor remarks, approver details, document attachment URL, and timeline activity audit trail.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">URL Path Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">id</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-required">Required</span></td>
                                        <td>Unique Leave ID (e.g. <code>42</code>)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/employee/leave/42" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "id": 42,
    "leave_type": {
      "id": 1,
      "name": "Casual Leave"
    },
    "from_date": "2026-10-01",
    "to_date": "2026-10-03",
    "duration_days": 3,
    "status": "APPROVED",
    "reason": "Personal work at home",
    "document_url": null,
    "supervisor": {
      "id": 122,
      "name": "Team Supervisor",
      "remarks": "Approved",
      "action_at": "2026-09-24 11:00:30"
    }
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-slate-200">

        <!-- ========================================== -->
        <!-- 5. HOLIDAY APIS -->
        <!-- ========================================== -->
        <h4 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-gift text-warning"></i> 5. Common Holiday APIs
        </h4>

        <!-- 5.1 Holidays List -->
        <div class="endpoint-card" id="ep-holiday-list">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Holiday List</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/holidays?year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/holidays?year=2026', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Returns public, gazetted, and national holidays configured in the system for the given calendar year.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">year</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Calendar year (e.g. <code>2026</code>). Defaults to current year.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/holidays?year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "year": 2026,
    "total_holidays": 14,
    "holidays": [
      {
        "id": 1,
        "holiday_name": "Republic Day",
        "from_date": "2026-01-26",
        "to_date": "2026-01-26",
        "day": "Monday",
        "duration_days": 1,
        "is_restricted": false
      },
      {
        "id": 2,
        "holiday_name": "Independence Day",
        "from_date": "2026-08-15",
        "to_date": "2026-08-15",
        "day": "Saturday",
        "duration_days": 1,
        "is_restricted": false
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5.2 Restricted Holidays List -->
        <div class="endpoint-card" id="ep-holiday-restricted">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Restricted / Optional Holidays</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/holidays/restricted?year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/holidays/restricted?year=2026', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Retrieves optional / restricted holidays (RH) that employees can select to apply for restricted holiday leaves.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">year</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Filter by calendar year (default: current year)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/holidays/restricted?year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "year": 2026,
    "restricted_holidays": [
      {
        "id": 11,
        "holiday_name": "Govardhan Puja",
        "date": "2026-11-09",
        "day": "Monday",
        "is_restricted": true
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5.3 Holiday Calendar -->
        <div class="endpoint-card" id="ep-holiday-calendar">
            <div class="endpoint-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge-method GET fs-12">GET</span>
                    <span class="fw-bold text-dark fs-15">Get Holiday Calendar (Grouped by Month)</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Bearer Token</span>
                </div>
                <div class="endpoint-uri-bar font-mono">
                    <span class="text-success font-mono fw-semibold">/api/v1/holidays/calendar?year=2026</span>
                    <button class="btn btn-sm btn-link text-muted p-0" onclick="copyText('{{ $baseUrl }}/v1/holidays/calendar?year=2026', this)"><i class="ti ti-copy"></i></button>
                </div>
            </div>
            <div class="p-4">
                <p class="text-muted fs-14 mb-3">
                    Returns holidays pre-grouped by month (e.g. January, August, October) for easy UI accordion/tab rendering on mobile screens.
                </p>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark fs-13 text-uppercase tracking-wider mb-2">Query Parameters</h6>
                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table param-table mb-0">
                                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-primary fw-semibold">year</td>
                                        <td class="font-mono text-muted">integer</td>
                                        <td><span class="param-optional">Optional</span></td>
                                        <td>Filter by calendar year (default: current year)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-console">
                            <div class="code-console-header">
                                <span class="fs-12 text-muted font-mono">cURL Example</span>
                                <button class="btn-copy" onclick="copyCode(this)"><i class="ti ti-copy me-1"></i>Copy</button>
                            </div>
                            <pre class="language-bash"><code>curl -X GET "{{ $baseUrl }}/v1/holidays/calendar?year=2026" \
  -H "Authorization: Bearer <span class="inject-token">YOUR_TOKEN</span>" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="fs-12 fw-bold text-muted text-uppercase">Sample Response (200 OK):</span>
                        <div class="code-console mt-1">
                            <pre class="language-json"><code>{
  "success": true,
  "data": {
    "year": 2026,
    "months": {
      "January": [
        {
          "holiday_name": "Republic Day",
          "date": "2026-01-26",
          "day": "Monday"
        }
      ],
      "August": [
        {
          "holiday_name": "Independence Day",
          "date": "2026-08-15",
          "day": "Saturday"
        }
      ]
    }
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-slate-200">

        @include('docs.partials.supervisor')
        @include('docs.partials.notifications')

    </main>

    <!-- Bootstrap 5 & jQuery JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-http.min.js"></script>

    <script>
        // Copy text helper
        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                let original = $(btn).html();
                $(btn).html('<i class="ti ti-check text-success"></i>');
                setTimeout(() => $(btn).html(original), 1500);
            });
        }

        // Copy Code helper
        function copyCode(btn) {
            let pre = $(btn).closest('.code-console').find('.tab-pane.active pre, pre').first();
            let text = pre.text();
            navigator.clipboard.writeText(text).then(() => {
                let original = $(btn).html();
                $(btn).html('<i class="ti ti-check me-1 text-success"></i>Copied!');
                setTimeout(() => $(btn).html(original), 2000);
            });
        }

        // Global Token management
        let currentToken = localStorage.getItem('ems_api_token') || '';
        if (currentToken) {
            $('#globalBearerTokenInput').val(currentToken);
            applyTokenToSnippets(currentToken);
        }

        function updateGlobalToken(token) {
            currentToken = token.trim();
            localStorage.setItem('ems_api_token', currentToken);
            applyTokenToSnippets(currentToken);
        }

        function applyTokenToSnippets(token) {
            let displayToken = token || 'YOUR_TOKEN';
            $('.inject-token').text(displayToken);
        }

        function testSavedToken() {
            let val = $('#globalBearerTokenInput').val().trim();
            if (!val) {
                alert('Please enter a Bearer token first.');
                return;
            }
            updateGlobalToken(val);
            alert('Bearer token saved locally! Snippets and live test requests are now armed with this token.');
        }

        // Drawer toggle
        function toggleTestDrawer(id) {
            $('#' + id).slideToggle(200);
        }

        // Live Login test
        function executeLoginTest() {
            let user = $('#test-login-user').val();
            let pass = $('#test-login-pass').val();

            if (!user || !pass) {
                alert('Please enter username and password to test.');
                return;
            }

            $('#test-login-result').show();
            $('#test-login-status').attr('class', 'badge bg-secondary mb-1 fs-11').text('Sending request...');
            $('#test-login-output').text('Connecting...');

            $.ajax({
                url: "{{ $baseUrl }}/v1/employee/auth/login",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    login: user,
                    password: pass,
                    device_name: "Web Docs Tester"
                }),
                success: function(res) {
                    $('#test-login-status').attr('class', 'badge bg-success mb-1 fs-11').text('200 OK');
                    $('#test-login-output').text(JSON.stringify(res, null, 2));
                    if (res.data && res.data.token) {
                        $('#globalBearerTokenInput').val(res.data.token);
                        updateGlobalToken(res.data.token);
                    }
                },
                error: function(xhr) {
                    $('#test-login-status').attr('class', 'badge bg-danger mb-1 fs-11').text(xhr.status + ' ' + xhr.statusText);
                    try {
                        $('#test-login-output').text(JSON.stringify(JSON.parse(xhr.responseText), null, 2));
                    } catch(e) {
                        $('#test-login-output').text(xhr.responseText);
                    }
                }
            });
        }

        // Search filter in sidebar
        $('#endpointSearchInput').on('keyup input', function() {
            let q = $(this).val().toLowerCase().trim();
            $('.endpoint-card').each(function() {
                let cardText = $(this).text().toLowerCase();
                if (!q || cardText.indexOf(q) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('#sidebarNav a.sidebar-link').each(function() {
                let linkText = $(this).text().toLowerCase();
                let href = $(this).attr('href');
                if (href.startsWith('#section')) return;

                if (!q || linkText.indexOf(q) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Keyboard shortcut: press '/' to focus search
        $(document).on('keydown', function(e) {
            if (e.key === '/' && !$(e.target).is('input, textarea')) {
                e.preventDefault();
                $('#endpointSearchInput').focus();
            }
        });

        // Mobile sidebar toggle
        $('#sidebarToggleBtn').on('click', function() {
            $('#apiSidebar').toggleClass('show');
        });
    </script>
</body>
</html>
