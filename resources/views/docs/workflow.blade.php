<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'EMS — Employee Management, Leave & Payroll Process Complete Guide' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('frontent/assets/img/favicon.png') }}">

    <!-- Google Fonts & Tabler Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Mermaid.js for interactive diagrams -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({ startOnLoad: true, theme: 'default', securityLevel: 'loose' });
    </script>

    <style>
        :root {
            --brand-primary: #0284c7;
            --brand-primary-dark: #0369a1;
            --brand-slate: #0f172a;
            --brand-surface: #1e293b;
            --sidebar-width: 310px;
            --header-height: 68px;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --accent-purple: #8b5cf6;
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
        .workflow-navbar {
            height: var(--header-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.04);
        }

        /* Sidebar */
        .workflow-sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            position: fixed;
            top: var(--header-height);
            left: 0;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            z-index: 1020;
            padding: 1.25rem 0.75rem 2.5rem 1rem;
        }

        .sidebar-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 700;
            margin: 1.2rem 0 0.4rem 0.5rem;
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
            background-color: #e0f2fe;
            color: #0284c7;
            font-weight: 600;
        }

        /* Main Content */
        .workflow-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2.2rem 2.8rem 4rem;
            max-width: 1320px;
        }

        @media (max-width: 991.98px) {
            .workflow-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease-in-out;
            }
            .workflow-sidebar.show {
                transform: translateX(0);
            }
            .workflow-content {
                margin-left: 0;
                padding: 1.5rem 1rem 3rem;
            }
        }

        /* UI Cards */
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-custom-header {
            padding: 1.25rem 1.6rem;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .badge-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #0284c7;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            margin-right: 8px;
        }

        .btn-action-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }

        .action-table th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 10px 14px;
        }

        .action-table td {
            font-size: 13.5px;
            padding: 12px 14px;
            vertical-align: middle;
        }

        .callout-box {
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            border-left: 4px solid;
        }

        .callout-info {
            background-color: #f0f9ff;
            border-color: #0284c7;
            color: #0369a1;
        }

        .callout-success {
            background-color: #f0fdf4;
            border-color: #10b981;
            color: #047857;
        }

        .callout-warning {
            background-color: #fffbeb;
            border-color: #f59e0b;
            color: #b45309;
        }

        .img-preview-box {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.06);
            max-width: 100%;
            transition: transform 0.2s ease;
        }

        .img-preview-box:hover {
            transform: scale(1.01);
        }

        .search-pill {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <!-- TOP NAVBAR -->
    <header class="workflow-navbar d-flex align-items-center justify-content-between px-3 px-lg-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="sidebarToggle">
                <i class="ti ti-menu-2 fs-5"></i>
            </button>
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('frontent/assets/img/icons/logo_1.png') }}" alt="Logo" height="34" onerror="this.src='{{ asset('frontent/assets/img/icons/logo.png') }}'">
                <div class="d-none d-md-block">
                    <span class="fw-bold text-dark fs-15 d-block lh-1">Chitragupta EMS</span>
                    <span class="text-muted fs-11">Employee Management, Leave &amp; Payroll Process</span>
                </div>
            </a>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 fs-12 ms-2">
                Operational Guide
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Search Button / Screen -->
            <div class="position-relative d-none d-sm-block">
                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted"></i>
                <input type="text" id="btnFilterInput" class="form-control form-control-sm search-pill ps-4" style="width: 250px;" placeholder="Search any button or screen...">
            </div>

            <!-- API Docs Link -->
            <a href="{{ route('api.docs') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="ti ti-api"></i>
                <span class="d-none d-md-inline">API Docs</span>
            </a>

            <!-- Download Word Document -->
            <a href="{{ route('project.workflow.download') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i class="ti ti-file-download"></i>
                <span>Download Word (.docx)</span>
                <span class="badge bg-white text-primary rounded-pill fs-10 px-1.5 ms-1">{{ $docxSize }}</span>
            </a>

            <!-- Open App Portal -->
            <a href="{{ route('login') }}" class="btn btn-sm btn-dark d-inline-flex align-items-center gap-1">
                <i class="ti ti-login"></i>
                <span class="d-none d-md-inline">Login Portal</span>
            </a>
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="workflow-sidebar" id="workflowSidebar">
        <div class="sidebar-section-title">Navigation &amp; Flow</div>
        <a href="#overview" class="sidebar-link active"><i class="ti ti-layout-grid"></i> 1. Executive Overview</a>
        <a href="#user-roles" class="sidebar-link"><i class="ti ti-users"></i> 2. User Roles Matrix (5 Roles)</a>
        
        <div class="sidebar-section-title">Core Pillars</div>
        <a href="#employee-management" class="sidebar-link"><i class="ti ti-user-plus"></i> 3. Employee Management</a>
        <a href="#leave-management" class="sidebar-link"><i class="ti ti-calendar-off"></i> 4. Leave Lifecycle &amp; Quota</a>
        <a href="#attendance-tracking" class="sidebar-link"><i class="ti ti-map-pin"></i> 5. Attendance &amp; Geo-Punch</a>
        <a href="#payroll-engine" class="sidebar-link"><i class="ti ti-cash"></i> 6. Payroll Process &amp; Batches</a>
        <a href="#salary-structure" class="sidebar-link"><i class="ti ti-calculator"></i> 7. Salary Structure &amp; CTC</a>
        <a href="#increments" class="sidebar-link"><i class="ti ti-trending-up"></i> 8. Annual CTC Increments</a>

        <div class="sidebar-section-title">Screen Action Guide</div>
        <a href="#screen-login" class="sidebar-link"><i class="ti ti-lock"></i> 9.1 Login &amp; Redirection</a>
        <a href="#screen-admin-dashboard" class="sidebar-link"><i class="ti ti-dashboard"></i> 9.2 Admin Executive Dashboard</a>
        <a href="#screen-employee-self" class="sidebar-link"><i class="ti ti-user"></i> 9.3 Employee Self-Service</a>
        <a href="#screen-leave-actions" class="sidebar-link"><i class="ti ti-calendar-check"></i> 9.4 Leave Request Actions</a>
        <a href="#screen-payroll-actions" class="sidebar-link"><i class="ti ti-receipt"></i> 9.5 Payroll Batch Actions</a>

        <div class="sidebar-section-title">Database &amp; Compliance</div>
        <a href="#database-schema" class="sidebar-link"><i class="ti ti-database"></i> 10. Database Schema</a>
        <a href="#statutory-compliance" class="sidebar-link"><i class="ti ti-shield-check"></i> 11. Statutory Compliance (PF/ESI)</a>

        <div class="mt-4 pt-3 border-top text-center">
            <p class="fs-11 text-muted mb-2">&copy; {{ date('Y') }} Neural Info Solutions Pvt. Ltd.<br>Chitragupta EMS Platform</p>
            <a href="{{ route('project.workflow.download') }}" class="btn btn-sm btn-outline-success w-100 fs-12">
                <i class="ti ti-download me-1"></i> Download Word Docx
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="workflow-content">

        <!-- 1. EXECUTIVE OVERVIEW -->
        <section id="overview" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">1. Executive Overview (Project Ka Parichay)</h2>
                    <p class="text-muted fs-14 mb-0">Chitragupta EMS &bull; Employee Management, Multi-Tier Leave System &amp; Automated Payroll Processing</p>
                </div>
                <span class="badge bg-dark px-3 py-2 fs-12">EMS Version 2.0 &bull; Laravel 11</span>
            </div>

            <div class="callout-box callout-info">
                <h6 class="fw-bold mb-1"><i class="ti ti-info-circle me-1"></i> Pura Application Kaise Kaam Karta Hai?</h6>
                <p class="mb-0 fs-13">
                    Yeh system <strong>Employee Directory</strong>, <strong>Attendance &amp; Leave Workflow</strong>, aur <strong>Attendance-Linked Payroll Engine</strong> ka unified platform hai. 
                    Employee ke daily GPS punches aur approved leaves ka data directly monthly payroll me feed hota hai, jisse Loss of Pay (LWP), Statutory Deductions (PF, ESI, Professional Tax, TDS), Net Salary, aur A4 PDF Payslips automatically 1-click me calculate aur generate ho jaati hain.
                </p>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 bg-white" style="border-radius: 12px; border-left: 4px solid #0284c7 !important;">
                        <h5 class="fw-bold text-primary mb-2"><i class="ti ti-users me-1"></i> 1. Employee Directory</h5>
                        <p class="text-muted fs-13 mb-0">
                            Complete staff bio-data, bulk Excel import, designation &amp; department assignment, staffing agencies, shifts, aur Aadhaar/PAN Document Vault verification.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 bg-white" style="border-radius: 12px; border-left: 4px solid #10b981 !important;">
                        <h5 class="fw-bold text-success mb-2"><i class="ti ti-calendar-off me-1"></i> 2. Leave &amp; Attendance</h5>
                        <p class="text-muted fs-13 mb-0">
                            Designation-based leave quota (CL, SL, PL), mobile/web GPS punch in/out, employee leave request, manager recommendation, aur HR approval workflow.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 bg-white" style="border-radius: 12px; border-left: 4px solid #f59e0b !important;">
                        <h5 class="fw-bold text-warning mb-2"><i class="ti ti-cash me-1"></i> 3. Payroll &amp; CTC Engine</h5>
                        <p class="text-muted fs-13 mb-0">
                            Automatic LWP calculation, Monthly Gross &amp; CTC split (Basic 50%, HRA 25%, PF, ESI), batch generation, disbursement, PDF payslips, aur annual increments.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. USER ROLES MATRIX -->
        <section id="user-roles" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">2. User Roles &amp; Access Hierarchy (Kitne Users / Roles Hain?)</h2>
                    <p class="text-muted fs-14 mb-0">System me <strong>5 primary roles</strong> hain jo staff administration, attendance, leave approval aur payroll disbursement ko govern karte hain:</p>
                </div>
            </div>

            <!-- Roles Graphic -->
            <div class="text-center mb-4">
                <img src="{{ asset('frontent/assets/img/graphics/ems_user_roles.png') }}" alt="EMS User Roles" class="img-fluid img-preview-box" style="max-height: 420px;">
                <p class="text-muted fs-12 mt-1"><em>Figure 2.1: EMS 5-Tier User Hierarchy and Security Access Matrix</em></p>
            </div>

            <div class="table-responsive card-custom">
                <table class="table table-hover action-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Auth Guard &amp; ID</th>
                            <th>Portal URL</th>
                            <th>Responsibilities &amp; Permissions (Kaam Kya Hai?)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold text-primary">1</td>
                            <td class="fw-bold">Super Admin</td>
                            <td><span class="badge bg-danger">web</span> (ID: 1)</td>
                            <td><code>/home</code></td>
                            <td>Poore system par 100% bypass control. Spatie permissions, Roles create karna, Users delete karna, Master settings aur Audit logs.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">2</td>
                            <td class="fw-bold">System Admin</td>
                            <td><span class="badge bg-primary">web</span> (ID: 2)</td>
                            <td><code>/home</code></td>
                            <td>Daily operations: Monthly Payroll Batches generate &amp; approve karna, Agency &amp; Branch masters, Shift timings, Salary structures setup.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">3</td>
                            <td class="fw-bold">HR Manager</td>
                            <td><span class="badge bg-purple" style="background:#7c3aed; color:#fff;">web</span> (ID: 3)</td>
                            <td><code>/home</code></td>
                            <td>Staff onboarding, Document Vault me Aadhaar/PAN verify karna, Leave requests approve/reject karna, Monthly attendance registers export.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">4</td>
                            <td class="fw-bold">Supervisor / Manager</td>
                            <td><span class="badge bg-warning text-dark">web</span> (ID: 6)</td>
                            <td><code>/home</code></td>
                            <td>Apne subordinate team members ki daily punch logs dekhna, team task monitoring, aur team leave requests recommend karna.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">5</td>
                            <td class="fw-bold">Employee</td>
                            <td><span class="badge bg-success">employee</span> (Guard)</td>
                            <td><code>/employee/dashboard</code></td>
                            <td>Self-service portal: GPS coordinates se Punch In/Out, Leave quota dekhna, Leave apply karna, Monthly Payslip PDF download.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 3. EMPLOYEE MANAGEMENT -->
        <section id="employee-management" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">3. Employee Management &amp; Document Vault</h2>
                    <p class="text-muted fs-14 mb-0">Karmachariyon ka onboarding, bio-data, posting hierarchy aur document verification.</p>
                </div>
            </div>

            <div class="card-custom p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="ti ti-id me-1 text-primary"></i> Employee Bio-Data Form (7 Comprehensive Tabs)</h5>
                <p class="fs-13 text-secondary mb-3">Jab Admin ya HR <code>/employees/create</code> par jata hai, toh system 7 structured tabs me data collect karta hai:</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <strong class="fs-13 d-block text-dark mb-1">1. Personal &amp; Bio-Data</strong>
                            <span class="fs-12 text-muted">Full Name, Email, Mobile, Blood Group, Father/Spouse name, Marital Status, DOB.</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <strong class="fs-13 d-block text-dark mb-1">2. Organization Posting</strong>
                            <span class="fs-12 text-muted">Agency, Company, Branch, Department, Designation, Reporting Manager (Supervisor).</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <strong class="fs-13 d-block text-dark mb-1">3. Shift &amp; Timings</strong>
                            <span class="fs-12 text-muted">Shift Master (Morning/Evening/General), Grace period, Week-off days (Sunday/Saturday).</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <strong class="fs-13 d-block text-dark mb-1">4. Bank &amp; Payment Details</strong>
                            <span class="fs-12 text-muted">Bank Name, Account Number, IFSC Code, Payment Mode (NEFT/Bank Transfer/Cheque).</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <strong class="fs-13 d-block text-dark mb-1">5. Statutory Compliance</strong>
                            <span class="fs-12 text-muted">PAN Card Number, Universal Account Number (UAN), PF Number, ESI IP Number.</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <strong class="fs-13 d-block text-dark mb-1">6. Document Vault</strong>
                            <span class="fs-12 text-muted">Aadhaar card scan, PAN, Educational degrees, Previous experience letters.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. LEAVE LIFECYCLE WORKFLOW -->
        <section id="leave-management" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">4. Leave Lifecycle &amp; Quota Management (Leaves Kaise Kaam Karti Hain?)</h2>
                    <p class="text-muted fs-14 mb-0">Leave allocation, application, supervisor recommendation, HR approval aur payroll LWP sync.</p>
                </div>
            </div>

            <!-- Graphic Preview -->
            <div class="text-center mb-4">
                <img src="{{ asset('frontent/assets/img/graphics/leave_management_workflow.png') }}" alt="Leave Management Workflow" class="img-fluid img-preview-box" style="max-height: 480px;">
                <p class="text-muted fs-12 mt-1"><em>Figure 4.1: Complete End-to-End Leave Workflow from Quota Setup to Payroll Sync</em></p>
            </div>

            <!-- Interactive Mermaid Diagram -->
            <div class="card-custom p-4 bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="ti ti-binary-tree me-1"></i> Interactive Leave State Flow</h6>
                <div class="mermaid text-center">
                    flowchart LR
                        A["📋 1. Leave Quota\n(Designation-based CL/SL/PL)"] --> B["👤 2. Employee Applies\n(Dates, Reason, Upload)"]
                        B --> C{"3. Balance Check"}
                        C -->|Quota Exceeded| D["⚠️ Mark as Loss of Pay\n(LWP Leave)"]
                        C -->|Balance Available| E["⏳ Status: PENDING\n(Alert to Supervisor)"]
                        E --> F["👨‍💼 4. Supervisor Review\n(Recommend to HR)"]
                        F --> G{"5. HR Decision"}
                        G -->|❌ Reject| H["🔴 Status: REJECTED\n(Remarks sent to Employee)"]
                        G -->|✅ Approve| I["🟢 Status: APPROVED\n(Deduct Balance)"]
                        I --> J["📅 6. Register Marks 'L'\n(Paid Day for Payroll)"]
                        D --> K["📉 7. Deducted in Payroll\n(LWP Days Deduction)"]

                        style A fill:#e0f2fe,stroke:#0284c7,stroke-width:2px
                        style I fill:#dcfce7,stroke:#10b981,stroke-width:2px
                        style H fill:#fee2e2,stroke:#ef4444,stroke-width:2px
                        style K fill:#fef3c7,stroke:#f59e0b,stroke-width:2px
                </div>
            </div>
        </section>

        <!-- 5. ATTENDANCE & GEO-PUNCH -->
        <section id="attendance-tracking" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">5. Attendance Tracking &amp; Geo-Punching</h2>
                    <p class="text-muted fs-14 mb-0">Mobile aur web se GPS coordinate aur IP-stamped Punch In/Out tracking.</p>
                </div>
            </div>

            <div class="card-custom p-4 bg-white mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h6 class="fw-bold text-dark mb-2"><i class="ti ti-map-pin me-1 text-primary"></i> GPS Coordinates &amp; Geo-Fencing</h6>
                        <p class="fs-13 text-secondary mb-3">
                            Jab employee <code>[ Punch In ]</code> ya <code>[ Punch Out ]</code> button dabata hai, toh browser/app device se live <strong>Latitude</strong>, <strong>Longitude</strong>, aur <strong>IP Address</strong> capture karke <code>employee_attendances</code> table me save karta hai.
                        </p>
                        <ul class="fs-13 text-secondary ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong>Shift In-Time Grace:</strong> Shift timing ke baad aane par auto-mark <code>Late In</code>.</li>
                            <li><strong>Total Working Hours:</strong> <code>out_time - in_time</code> calculate karke total productive hours display hote hain.</li>
                            <li><strong>Monthly Attendance Register:</strong> Day 1 se Day 31 tak ka matrix (P = Present, A = Absent, L = Approved Leave, WO = Week Off, H = Holiday).</li>
                        </ul>
                    </div>
                    <div class="col-lg-5 text-center mt-3 mt-lg-0">
                        <img src="{{ asset('frontent/assets/img/graphics/admin_dashboard_mockup.png') }}" alt="Dashboard Mockup" class="img-fluid img-preview-box" style="max-height: 240px;">
                        <p class="text-muted fs-11 mt-1"><em>Attendance metrics &amp; punctuality monitoring</em></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. PAYROLL PROCESS PIPELINE -->
        <section id="payroll-engine" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">6. Payroll Processing Pipeline &amp; Batch Generation</h2>
                    <p class="text-muted fs-14 mb-0">Attendance-Linked salary calculations, statutory deductions aur monthly disbursement.</p>
                </div>
            </div>

            <!-- Payroll Flowchart Graphic -->
            <div class="text-center mb-4">
                <img src="{{ asset('frontent/assets/img/graphics/payroll_lifecycle_flowchart.png') }}" alt="Payroll Lifecycle Flowchart" class="img-fluid img-preview-box" style="max-height: 480px;">
                <p class="text-muted fs-12 mt-1"><em>Figure 6.1: 6-Stage Automated Payroll Calculation Pipeline</em></p>
            </div>

            <div class="card-custom p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="ti ti-calculator me-1 text-primary"></i> 6-Stage Payroll Lifecycle:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 18%;">Stage</th>
                                <th style="width: 25%;">System Action</th>
                                <th>Operational Description (Kaise Process Hota Hai?)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-primary">Stage 1</td>
                                <td>Month-End Cut-Off</td>
                                <td>Month end par daily attendance punches aur approved leaves ko freeze/lock kiya jata hai.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary">Stage 2</td>
                                <td>LWP &amp; Payable Days</td>
                                <td>
                                    <code>Attended Days = Present + Approved Leaves + Holidays</code><br>
                                    <code>LWP Days = Total Working Days - Attended Days</code><br>
                                    <code>Payable Days = Total Month Days - LWP Days</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary">Stage 3</td>
                                <td>Batch Generation</td>
                                <td>Admin <code>/payroll/process</code> par Month &amp; Year select karke <code>[Generate Payroll]</code> dabata hai. System sabhi active employees ke calculation records banata hai.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary">Stage 4</td>
                                <td>Batch Review &amp; Audit</td>
                                <td>Admin <code>/payroll/batches</code> par total Gross, Deductions aur Net In-hand verify karta hai aur <code>[Approve Batch]</code> dabakar figures freeze karta hai.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary">Stage 5</td>
                                <td>Disbursement &amp; UTR</td>
                                <td>Bank transfer execute karne ke baad Admin <code>[Disburse Selected]</code> dabata hai aur UTR / Payment Reference number enter karta hai.</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-primary">Stage 6</td>
                                <td>PDF Payslip Release</td>
                                <td>Employees ke portal par monthly payslip release ho jaati hai. Individual A4 formatted PDF Payslip (Indian Currency words ke sath) download hoti hai.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 7. SALARY STRUCTURE & CTC -->
        <section id="salary-structure" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">7. Salary Structure &amp; CTC Calculation Matrix</h2>
                    <p class="text-muted fs-14 mb-0">Monthly Gross, Earning Heads, Statutory Deductions aur Annual CTC breakdown.</p>
                </div>
            </div>

            <!-- Salary Structure Mockup Graphic -->
            <div class="text-center mb-4">
                <img src="{{ asset('frontent/assets/img/graphics/salary_structure_mockup.png') }}" alt="Salary Structure Mockup" class="img-fluid img-preview-box" style="max-height: 440px;">
                <p class="text-muted fs-12 mt-1"><em>Figure 7.1: Real-time Earnings vs Statutory Deductions Breakdown</em></p>
            </div>

            <div class="table-responsive card-custom">
                <table class="table table-hover action-table mb-0">
                    <thead>
                        <tr>
                            <th>Component Name</th>
                            <th>Category</th>
                            <th>Calculation Rule &amp; Statutory Cap</th>
                            <th>Example (on ₹50,000 Gross)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Basic Salary</td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td>50% of Monthly Gross</td>
                            <td class="fw-bold text-success">₹25,000</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">House Rent Allowance (HRA)</td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td>25% of Monthly Gross</td>
                            <td class="fw-bold text-success">₹12,500</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Conveyance Allowance</td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td>Fixed standard allowance</td>
                            <td class="fw-bold text-success">₹2,000</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Medical Allowance</td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td>Fixed standard allowance</td>
                            <td class="fw-bold text-success">₹1,500</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Special / Other Allowance</td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td>Balancing head ensuring total matches Gross</td>
                            <td class="fw-bold text-success">₹9,000</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PF Employee Contribution</td>
                            <td><span class="badge bg-danger">Deduction</span></td>
                            <td>12% of Basic Salary (Statutory cap ₹1,800/mo)</td>
                            <td class="fw-bold text-danger">₹1,800</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">ESI Employee Contribution</td>
                            <td><span class="badge bg-danger">Deduction</span></td>
                            <td>0.75% of Gross (Applicable only if Gross &le; ₹21,000)</td>
                            <td class="fw-bold text-secondary">₹0 (Gross &gt; 21k)</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Professional Tax (PT)</td>
                            <td><span class="badge bg-danger">Deduction</span></td>
                            <td>State slab governed</td>
                            <td class="fw-bold text-danger">₹200</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Loss of Pay (LWP)</td>
                            <td><span class="badge bg-danger">Deduction</span></td>
                            <td>(Monthly Gross / Month Days) &times; LWP Days</td>
                            <td class="fw-bold text-danger">Dynamic</td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="3" class="fw-bold text-dark">ESTIMATED NET IN-HAND SALARY:</td>
                            <td class="fw-bold text-primary fs-15">₹48,000 / mo</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 8. ANNUAL CTC INCREMENTS -->
        <section id="increments" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">8. Annual Increments &amp; Salary Revisions</h2>
                    <p class="text-muted fs-14 mb-0">Appraisal reviews, revised CTC preview, aur Increment Letters download.</p>
                </div>
            </div>

            <div class="card-custom p-4 bg-white mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h6 class="fw-bold text-dark mb-2"><i class="ti ti-trending-up me-1 text-primary"></i> Increments Management Module (<code>/payroll/increments</code>)</h6>
                        <p class="fs-13 text-secondary mb-3">
                            Admin jab annual appraisal execute karta hai, toh employee ka existing CTC load hota hai. Percentage ya direct new CTC amount enter karne par revised components auto-calculate ho jate hain.
                        </p>
                        <ul class="fs-13 text-secondary ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong>Preview Revised CTC:</strong> Naye Basic, HRA aur in-hand salary ka live comparative preview.</li>
                            <li><strong>Designation Revision:</strong> Promotion ke sath naya designation map karne ki suvidha.</li>
                            <li><strong>Official Increment Letter:</strong> 1-Click me printable company-branded Increment / Appraisal Letter PDF format me download hota hai.</li>
                        </ul>
                    </div>
                    <div class="col-lg-5 text-center mt-3 mt-lg-0">
                        <img src="{{ asset('frontent/assets/img/SelfAssessment.png') }}" alt="PMT Self Assessment" class="img-fluid img-preview-box" style="max-height: 220px;" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. SCREEN-BY-SCREEN BUTTON ACTION GUIDE -->
        <section id="action-guide" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">9. Screen-by-Screen &amp; Button-by-Button Detailed Action Guide</h2>
                    <p class="text-muted fs-14 mb-0"><em>"Kha se kya ho rha hai, kis button se kya ho rha hai, kaise chal rha hai"</em> — Har screen ke buttons ka exact execution breakdown:</p>
                </div>
            </div>

            <!-- 9.1 Login -->
            <div class="card-custom" id="screen-login">
                <div class="card-custom-header">
                    <div>
                        <span class="badge-step">1</span>
                        <strong class="fs-15 text-dark">Unified Login &amp; Role-Based Redirection</strong>
                        <span class="badge bg-light text-muted border ms-2"><code>/login</code> &amp; <code>/employee/login</code></span>
                    </div>
                    <span class="badge bg-primary">AuthController@login</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped action-table-item mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 28%;">Button / Field Name</th>
                                    <th style="width: 22%;">Screen Location</th>
                                    <th>What Happens on Click (Button Dabane Par Kya Hota Hai?)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="btn-action-badge text-primary"><i class="ti ti-login"></i> [ Sign In ]</span></td>
                                    <td>Center of Login Card</td>
                                    <td>
                                        <strong>1.</strong> Email aur password validate karta hai.<br>
                                        <strong>2.</strong> Check karta hai user <code>status === 1</code> (Active) hai ya nahi.<br>
                                        <strong>3.</strong> Role identify karta hai:
                                        <ul>
                                            <li>Agar <strong>Employee</strong> hai ➡️ Redirects to <code>/employee/dashboard</code></li>
                                            <li>Agar <strong>Admin / HR / Supervisor</strong> hai ➡️ Redirects to <code>/home</code></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge"><i class="ti ti-eye"></i> [ Toggle Password Eye ]</span></td>
                                    <td>Right side of Password Input</td>
                                    <td>Input type ko JavaScript se <code>password</code> se <code>text</code> toggle karta hai taaki user ko password dikhe ya hide ho.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 9.2 Admin Dashboard -->
            <div class="card-custom" id="screen-admin-dashboard">
                <div class="card-custom-header">
                    <div>
                        <span class="badge-step">2</span>
                        <strong class="fs-15 text-dark">Admin &amp; HR Executive Dashboard</strong>
                        <span class="badge bg-light text-muted border ms-2"><code>/home</code></span>
                    </div>
                    <span class="badge bg-primary">DashboardController@index</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped action-table-item mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 28%;">Button / Widget Name</th>
                                    <th style="width: 22%;">Screen Location</th>
                                    <th>What Happens on Click?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="btn-action-badge text-primary"><i class="ti ti-user-plus"></i> [ ➕ Add Employee ]</span></td>
                                    <td>Banner top right</td>
                                    <td>Directly redirect karta hai <code>/employees/create</code> par naye staff registration ka 7-tab form open karne ke liye.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-success"><i class="ti ti-report"></i> [ Attendance Reports ]</span></td>
                                    <td>Banner top right</td>
                                    <td>Redirect karta hai <code>/attendance/attendance-dashboard/org-reports</code> par filter-based daily/monthly attendance report dekhne.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-danger"><i class="ti ti-calendar-off"></i> [ Leaves Awaiting Approval ]</span></td>
                                    <td>Welcome Hero Banner</td>
                                    <td>Directly <code>/attendance/leave/request</code> screen par le jata hai jaha pending leave requests queue me padi hain.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 9.3 Employee Self Service -->
            <div class="card-custom" id="screen-employee-self">
                <div class="card-custom-header">
                    <div>
                        <span class="badge-step">3</span>
                        <strong class="fs-15 text-dark">Employee Self-Service Dashboard</strong>
                        <span class="badge bg-light text-muted border ms-2"><code>/employee/dashboard</code></span>
                    </div>
                    <span class="badge bg-success">Employee Portal</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped action-table-item mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 28%;">Button Name</th>
                                    <th style="width: 22%;">Screen Location</th>
                                    <th>What Happens on Click?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="btn-action-badge text-primary"><i class="ti ti-map-pin"></i> [ 📍 Punch In / Check-In ]</span></td>
                                    <td>Attendance Card Top</td>
                                    <td>Browser/Mobile device se live GPS Latitude, Longitude aur IP Address uthakar <code>in_time</code> stamp karta hai.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-danger"><i class="ti ti-map-pin-off"></i> [ 📍 Punch Out / Check-Out ]</span></td>
                                    <td>Attendance Card Top</td>
                                    <td>Current timestamp par <code>out_time</code> record karke total shift working hours calculate karta hai.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-success"><i class="ti ti-download"></i> [ Download Payslip PDF ]</span></td>
                                    <td>Payslips Tab</td>
                                    <td>Monthly official salary slip PDF watermark aur company seal ke sath download karta hai.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 9.4 Leave Actions -->
            <div class="card-custom" id="screen-leave-actions">
                <div class="card-custom-header">
                    <div>
                        <span class="badge-step">4</span>
                        <strong class="fs-15 text-dark">Leave Application &amp; Approval Actions</strong>
                        <span class="badge bg-light text-muted border ms-2"><code>/employee/leaves</code> &amp; <code>/attendance/leave/request</code></span>
                    </div>
                    <span class="badge bg-warning text-dark">Leave Workflow</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped action-table-item mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 28%;">Button Name</th>
                                    <th style="width: 22%;">Screen Location</th>
                                    <th>What Happens on Click?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="btn-action-badge text-primary"><i class="ti ti-calendar-plus"></i> [ Apply New Leave ]</span></td>
                                    <td>Employee Leaves Page</td>
                                    <td>Leave modal open karta hai (Leave Type select, From/To dates, Reason, aur medical document upload).</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-success"><i class="ti ti-check"></i> [ ✅ Approve Leave ]</span></td>
                                    <td>HR / Supervisor Leave Queue</td>
                                    <td>Leave status ko <strong>APPROVED</strong> karta hai, employee ke balance se din deduct karta hai, aur attendance register me 'L' mark karta hai.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-danger"><i class="ti ti-x"></i> [ ❌ Reject Leave ]</span></td>
                                    <td>HR / Supervisor Leave Queue</td>
                                    <td>Rejection remarks ke sath request decline karta hai aur employee ko alert notification bhejta hai.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 9.5 Payroll Batch Actions -->
            <div class="card-custom" id="screen-payroll-actions">
                <div class="card-custom-header">
                    <div>
                        <span class="badge-step">5</span>
                        <strong class="fs-15 text-dark">Payroll Processing &amp; Disbursement Actions</strong>
                        <span class="badge bg-light text-muted border ms-2"><code>/payroll/*</code></span>
                    </div>
                    <span class="badge bg-primary">AdminPayrollController</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped action-table-item mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 28%;">Button Name</th>
                                    <th style="width: 22%;">Screen Location</th>
                                    <th>What Happens on Click?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="btn-action-badge text-primary"><i class="ti ti-calculator"></i> [ Create Batch ]</span></td>
                                    <td><code>/payroll/process</code></td>
                                    <td>Month, Year, Branch aur Agency select karke payroll calculation engine initiate karta hai.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-success"><i class="ti ti-lock"></i> [ Approve Batch ]</span></td>
                                    <td><code>/payroll/batches</code></td>
                                    <td>Draft salary batch ko lock karta hai taaki calculations freeze ho sakein.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-info"><i class="ti ti-send"></i> [ Disburse Selected ]</span></td>
                                    <td>Batch Details View</td>
                                    <td>Payment status 'Disbursed' mark karke employees ke dashboard par monthly payslips release karta hai.</td>
                                </tr>
                                <tr>
                                    <td><span class="btn-action-badge text-warning"><i class="ti ti-trending-up"></i> [ ➕ Add Increment ]</span></td>
                                    <td><code>/payroll/increments</code></td>
                                    <td>Revised CTC preview karta hai aur official <strong>Increment / Appraisal Letter</strong> PDF generate karta hai.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. DATABASE SCHEMA -->
        <section id="database-schema" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">10. Database Schema &amp; Key Tables</h2>
                    <p class="text-muted fs-14 mb-0">Backend MySQL database structure and relations:</p>
                </div>
            </div>

            <div class="table-responsive card-custom">
                <table class="table table-hover action-table mb-0">
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th>Primary / Foreign Keys</th>
                            <th>Business Purpose (Kya Data Save Hota Hai)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold text-primary">users</td>
                            <td><code>id, email, password, role_id, status</code></td>
                            <td>Admin, HR, Super Admin users ke web login credentials.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">employees</td>
                            <td><code>id, employee_name, employee_code, email, mobile, dept_id, designation_id, agency_id</code></td>
                            <td>Staff master bio-data, designation, branch posting aur service record.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">employee_attendances</td>
                            <td><code>id, employee_id, punch_date, in_time, out_time, latitude, longitude, ip_address</code></td>
                            <td>Daily punch logs, GPS location coordinates aur biometric tracking data.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">leaves</td>
                            <td><code>id, employee_id, leave_type_id, start_date, end_date, total_days, reason, status</code></td>
                            <td>Leave applications, approval history aur supervisor comments.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">leave_mappings</td>
                            <td><code>id, designation_id, leave_type_id, allow_days</code></td>
                            <td>Designation-wise annual quota mapping (CL, SL, PL allowed days).</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">salary_structures</td>
                            <td><code>id, employee_id, gross_salary, basic, hra, conveyance, pf, esi, ctc, status</code></td>
                            <td>Active salary package, earning heads aur statutory deductions.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">payroll_batches</td>
                            <td><code>id, batch_month, batch_year, agency_id, total_gross, total_net, status</code></td>
                            <td>Monthly salary batch calculations (Draft, Approved, Disbursed).</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">payslips</td>
                            <td><code>id, payroll_batch_id, employee_id, payable_days, lwp_days, net_salary, pdf_path</code></td>
                            <td>Generated monthly payslips aur payment reference logs.</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-primary">employee_increments</td>
                            <td><code>id, employee_id, previous_ctc, revised_ctc, increment_percentage, effective_date</code></td>
                            <td>Annual CTC appraisal history aur revised salary structures.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 11. STATUTORY COMPLIANCE -->
        <section id="statutory-compliance" class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">11. Statutory Compliance (PF, ESI, TDS &amp; LWP)</h2>
                    <p class="text-muted fs-14 mb-0">Labour law compliance aur audit rules:</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100 bg-white">
                        <h6 class="fw-bold text-primary mb-2"><i class="ti ti-building-bank me-1"></i> Employees' Provident Fund (EPF)</h6>
                        <p class="text-muted fs-13 mb-0">
                            Basic Salary ka 12% employee contribution aur 12% employer contribution automatically calculate hota hai. Statutory wage ceiling cap ₹1,800/month follow kiya gaya hai.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100 bg-white">
                        <h6 class="fw-bold text-success mb-2"><i class="ti ti-heart-rate-monitor me-1"></i> Employee State Insurance (ESIC)</h6>
                        <p class="text-muted fs-13 mb-0">
                            Agar employee ki monthly gross salary ₹21,000 ya usse kam hai, toh employee ka 0.75% aur employer ka 3.25% automatic deduct hota hai.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom p-4 h-100 bg-white">
                        <h6 class="fw-bold text-warning mb-2"><i class="ti ti-file-certificate me-1"></i> Professional Tax (PT) &amp; TDS</h6>
                        <p class="text-muted fs-13 mb-0">
                            State government rules ke mutabik standard ₹200/month PT aur employee ke annual taxable income slab ke according monthly TDS split calculate hota hai.
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Interactive Search / Filter Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile Sidebar Toggle
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('workflowSidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                });
            }

            // Quick Filter Input
            const filterInput = document.getElementById('btnFilterInput');
            if (filterInput) {
                filterInput.addEventListener('keyup', function () {
                    const query = this.value.toLowerCase().trim();
                    const tableRows = document.querySelectorAll('.action-table-item tbody tr');

                    tableRows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        if (query === '' || text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Smooth Scroll for Sidebar Links
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href');
                    if (targetId.startsWith('#')) {
                        e.preventDefault();
                        const targetElem = document.querySelector(targetId);
                        if (targetElem) {
                            targetElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                            this.classList.add('active');
                            if (window.innerWidth < 992 && sidebar) {
                                sidebar.classList.remove('show');
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
