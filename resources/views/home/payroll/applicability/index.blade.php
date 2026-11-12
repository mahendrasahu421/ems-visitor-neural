@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* Scope styles for applicability master page */
    .applicability-page {
        color: #1e293b;
    }

    /* KPI Summary Cards */
    .applicability-page .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .applicability-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .applicability-page .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-icon-indigo {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4338ca;
    }

    .stat-icon-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    .stat-icon-purple {
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #7e22ce;
    }

    .stat-icon-amber {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }

    /* Filter Card */
    .applicability-page .filter-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 8px;
        font-size: 13px;
        border-color: #cbd5e1;
        padding: 8px 12px;
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #4338ca;
        box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.15);
    }

    /* Table Container Card */
    .applicability-page .table-container-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .app-table {
        margin-bottom: 0 !important;
    }

    .app-table th {
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc !important;
        color: #475569;
        vertical-align: middle;
        white-space: nowrap;
        padding: 13px 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .app-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .app-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Custom Badges */
    .badge-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #e2e8f0;
        letter-spacing: 0.3px;
    }

    .badge-soft-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-soft-secondary {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .badge-soft-purple {
        background-color: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .badge-soft-amber {
        background-color: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .badge-soft-blue {
        background-color: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .badge-soft-cyan {
        background-color: #ecfeff;
        color: #0891b2;
        border: 1px solid #a5f3fc;
    }

    /* Action Buttons in Table */
    .btn-action-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        transition: all 0.15s ease;
    }

    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .btn-action-icon.btn-view:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .btn-action-icon.btn-edit:hover {
        background: #f0fdf4;
        color: #16a34a;
        border-color: #bbf7d0;
    }

    .btn-action-icon.btn-delete:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Status switch toggle */
    .form-check-status .form-check-input {
        cursor: pointer;
        width: 34px;
        height: 18px;
    }

    /* DataTables Pagination & Info */
    .applicability-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .applicability-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .applicability-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 34px !important;
        height: 34px !important;
        padding: 0 10px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #334155 !important;
        cursor: pointer !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
        margin: 0 !important;
    }

    .applicability-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .applicability-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .applicability-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #4338ca !important;
        border-color: #4338ca !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(67, 56, 202, 0.28) !important;
    }

    .applicability-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .applicability-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Offcanvas Slider Drawers */
    #applicabilityDrawer,
    #applicabilityDetailDrawer {
        width: 500px !important;
        max-width: 95vw;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
        border: none;
        box-shadow: -12px 0 40px rgba(15, 23, 42, 0.22) !important;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        z-index: 1060;
    }

    .app-drawer-hero {
        background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
        position: relative;
        padding: 24px 24px 20px;
        color: #ffffff;
        overflow: hidden;
    }

    .app-drawer-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0) 70%);
        pointer-events: none;
    }

    .app-drawer-hero .drawer-badge {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(4px);
        padding: 4px 10px;
        border-radius: 20px;
        color: #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .preset-pill-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 8px;
        padding: 5px 11px;
        font-size: 11.5px;
        font-weight: 500;
        color: #334155;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }

    .preset-pill-btn:hover {
        background: #eef2ff;
        border-color: #6366f1;
        color: #4338ca;
    }

    /* Detail View Drawer styling */
    .detail-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 8px;
    }

    .detail-field-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
</style>

<div class="page-wrapper applicability-page">
    <div class="content container-fluid py-4">

        <!-- Page Header & Actions -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-indigo text-white px-2 py-1 fs-11 rounded">Payroll Master</span>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">Statutory & Coverage Rules</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3" id="btnRefreshTable">
                    <i class="ti ti-rotate-clockwise" id="refreshIcon"></i> Refresh
                </button>
                <a href="{{ route('masters.payroll.applicability.create') }}" class="btn btn-outline-primary d-flex align-items-center gap-2 rounded-3 px-3">
                    <i class="ti ti-external-link"></i> Standalone Create
                </a>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 shadow-sm" id="btnOpenAddDrawer">
                    <i class="ti ti-circle-plus fs-16"></i> Add Applicability Rule
                </button>
            </div>
        </div>

        <!-- Alert messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="ti ti-circle-check me-2 fs-18 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="ti ti-alert-circle me-2 fs-18 align-middle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Total Rules</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1" id="kpiTotalRules">{{ $totalRules }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">Configured applicability policies</small>
                        </div>
                        <div class="stat-icon stat-icon-indigo">
                            <i class="ti ti-scale"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Active in Payroll</span>
                            <h3 class="fw-bold mb-0 text-success mt-1" id="kpiActiveRules">{{ $activeRules }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">Enforced in live wage calculations</small>
                        </div>
                        <div class="stat-icon stat-icon-green">
                            <i class="ti ti-circle-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Permanent Staff Rules</span>
                            <h3 class="fw-bold mb-0 text-purple mt-1" id="kpiPermanentRules">{{ $permanentRules }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">PF, Gratuity & regular staff</small>
                        </div>
                        <div class="stat-icon stat-icon-purple">
                            <i class="ti ti-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Contract & Interns</span>
                            <h3 class="fw-bold mb-0 text-warning mt-1" id="kpiContractualRules">{{ $contractualRules }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">Wages Act & stipend policies</small>
                        </div>
                        <div class="stat-icon stat-icon-amber">
                            <i class="ti ti-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card filter-card mb-4 p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <i class="ti ti-search fs-14"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-1" id="filterSearch"
                               placeholder="Search by rule name, code, salary criteria...">
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3">
                    <select class="form-select" id="filterAppliesTo">
                        <option value="all">All Target Groups</option>
                        <option value="all_staff">All Employees</option>
                        <option value="permanent">Permanent Only</option>
                        <option value="contractual">Contractual Only</option>
                        <option value="intern">Interns</option>
                        <option value="probation">Probationary Staff</option>
                    </select>
                </div>

                <div class="col-sm-6 col-md-2 col-lg-2">
                    <select class="form-select" id="filterStatus">
                        <option value="all">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-2 col-lg-3 d-flex justify-content-md-end gap-2">
                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3" id="btnResetFilters">
                        <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container Card -->
        <div class="card table-container-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Applicability Rules Register</h5>
                    <p class="text-muted fs-12 mb-0 mt-1">Defines employee qualification criteria for wage components, statutory deductions & benefits</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-2 py-1 fs-12" id="filteredCountBadge">
                        Showing all rules
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table app-table align-middle" id="applicabilityTable" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 45px;" class="text-center">#</th>
                                <th>Rule & Identifier</th>
                                <th>Target Group</th>
                                <th>Eligibility & Salary Criteria</th>
                                <th>Effective Date</th>
                                <th class="text-center" style="width: 90px;">Status</th>
                                <th class="text-center" style="width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded dynamically via AJAX DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Custom Pagination Footer Container -->
            <div class="card-footer bg-white border-top py-3 px-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2" id="tableCustomFooter">
                <div id="tableInfoContainer"></div>
                <div id="tablePaginationContainer"></div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <x-footer />
</div>

<!-- ==========================================
     OFFCANVAS: Add / Edit Applicability Drawer
=========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="applicabilityDrawer" aria-labelledby="applicabilityDrawerLabel" data-bs-backdrop="true">
    <div class="app-drawer-hero d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="drawer-badge">
                <i class="ti ti-scale fs-13"></i> Statutory Policy
            </span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div>
            <h4 class="fw-bold mb-1 text-white" id="drawerTitle">Configure Applicability Rule</h4>
            <p class="text-white-50 fs-12 mb-0" id="drawerSubtitle">Set target staff group, salary criteria, and eligibility thresholds</p>
        </div>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body p-4 custom-scrollbar">

        <!-- Quick Presets -->
        <div class="mb-4">
            <label class="form-label fs-11 text-uppercase fw-bold text-muted letter-spacing-1 mb-2">
                <i class="ti ti-sparkles text-warning me-1"></i> Quick Presets
            </label>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="preset-pill-btn" onclick="applyPreset('pf')">
                    <i class="ti ti-building-bank"></i> PF (&le; ₹15k)
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('esic')">
                    <i class="ti ti-heart-handshake"></i> ESIC (&le; ₹21k)
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('pt')">
                    <i class="ti ti-receipt-tax"></i> PT (&gt; ₹10k)
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('gratuity')">
                    <i class="ti ti-shield-check"></i> Gratuity (5 Yrs)
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('intern')">
                    <i class="ti ti-school"></i> Intern Exempt
                </button>
            </div>
        </div>

        <form id="applicabilityForm" novalidate>
            @csrf
            <input type="hidden" id="ruleId" name="id" value="">
            <input type="hidden" id="formMethod" value="POST">

            <!-- Rule Name -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Rule Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm rounded-2" id="rule_name" name="rule_name"
                       placeholder="e.g. Employee State Insurance (ESIC) Coverage" required maxlength="150">
                <div class="invalid-feedback fs-11">Please provide a descriptive rule name.</div>
            </div>

            <!-- Rule Code -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Rule Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm rounded-2 text-uppercase font-monospace" id="rule_code" name="rule_code"
                       placeholder="e.g. ESI_APPLY" required maxlength="50">
                <small class="text-muted fs-11">Unique identifier used by the payroll calculation engine.</small>
                <div class="invalid-feedback fs-11">Rule code is required and must be unique.</div>
            </div>

            <!-- Target Group (applies_to) -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Applies To <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm rounded-2" id="applies_to" name="applies_to" required>
                    <option value="all">All Employees (Company-wide)</option>
                    <option value="permanent">Permanent Employees</option>
                    <option value="contractual">Contractual / Vendor Staff</option>
                    <option value="intern">Interns / Trainees</option>
                    <option value="probation">Probationary Staff</option>
                </select>
                <div class="invalid-feedback fs-11">Please select an employee cohort.</div>
            </div>

            <!-- Salary Condition Summary -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Salary Condition / Rule Summary</label>
                <input type="text" class="form-control form-control-sm rounded-2" id="salary_condition" name="salary_condition"
                       placeholder="e.g. Gross monthly pay &le; ₹21,000" maxlength="150">
                <small class="text-muted fs-11">Summary expression displayed in salary breakdown views.</small>
            </div>

            <!-- Min & Max Salary Thresholds -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fs-12 fw-semibold text-dark">Min Salary (₹)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">₹</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm rounded-end-2"
                               id="min_salary" name="min_salary" placeholder="0.00" value="0.00">
                    </div>
                    <small class="text-muted fs-11">0 = No lower limit</small>
                </div>
                <div class="col-6">
                    <label class="form-label fs-12 fw-semibold text-dark">Max Salary (₹)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">₹</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm rounded-end-2"
                               id="max_salary" name="max_salary" placeholder="0.00" value="0.00">
                    </div>
                    <small class="text-muted fs-11">0 = No upper cap</small>
                </div>
            </div>

            <!-- Min Service Days & Effective Date -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fs-12 fw-semibold text-dark">Min Service (Days)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" min="0" class="form-control form-control-sm rounded-start-2"
                               id="min_service_days" name="min_service_days" placeholder="0" value="0">
                        <span class="input-group-text bg-light text-muted">Days</span>
                    </div>
                    <small class="text-muted fs-11">e.g. 1825 for Gratuity (5 yrs)</small>
                </div>
                <div class="col-6">
                    <label class="form-label fs-12 fw-semibold text-dark">Effective From</label>
                    <input type="date" class="form-control form-control-sm rounded-2" id="effective_from" name="effective_from">
                    <small class="text-muted fs-11">Leave blank for immediate</small>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Rule Status <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="Active" checked>
                        <label class="form-check-label fs-13" for="statusActive">
                            <span class="badge badge-soft-success">Active</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="Inactive">
                        <label class="form-check-label fs-13" for="statusInactive">
                            <span class="badge badge-soft-secondary">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Policy Description & Statutory Notes</label>
                <textarea class="form-control form-control-sm rounded-2" id="description" name="description" rows="3"
                          placeholder="Provide statutory act reference, governing notifications, or internal payroll policy rationale..." maxlength="1000"></textarea>
            </div>
        </form>
    </div>

    <!-- Drawer Footer -->
    <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2" data-bs-dismiss="offcanvas">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm px-4 rounded-2 d-flex align-items-center gap-2 shadow-sm" id="btnSaveRule">
            <i class="ti ti-device-floppy"></i>
            <span id="btnSaveText">Save Rule</span>
        </button>
    </div>
</div>

<!-- ==========================================
     OFFCANVAS: View Detail Preview Drawer
=========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="applicabilityDetailDrawer" aria-labelledby="applicabilityDetailDrawerLabel" data-bs-backdrop="true">
    <div class="app-drawer-hero d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="drawer-badge" id="detailHeaderBadge">
                <i class="ti ti-scale fs-13"></i> Statutory Policy
            </span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div>
            <h4 class="fw-bold mb-1 text-white" id="detailRuleName">Rule Name</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-white text-dark font-monospace" id="detailRuleCode">CODE</span>
                <span class="badge" id="detailStatusBadge">Active</span>
            </div>
        </div>
    </div>

    <div class="offcanvas-body p-4 custom-scrollbar">
        <div class="detail-section-title">Eligibility Specifications</div>

        <div class="detail-field-card">
            <div class="row g-2">
                <div class="col-6">
                    <span class="text-muted fs-11 d-block">Applies To</span>
                    <span class="fw-bold fs-13 text-dark" id="detailAppliesTo">-</span>
                </div>
                <div class="col-6">
                    <span class="text-muted fs-11 d-block">Effective Date</span>
                    <span class="fw-bold fs-13 text-dark" id="detailEffectiveDate">-</span>
                </div>
            </div>
        </div>

        <div class="detail-section-title">Financial & Service Criteria</div>
        <div class="detail-field-card">
            <div class="mb-2">
                <span class="text-muted fs-11 d-block">Salary Condition</span>
                <span class="fw-semibold fs-13 text-dark" id="detailSalaryCondition">-</span>
            </div>
            <div class="row g-2 pt-2 border-top">
                <div class="col-4">
                    <span class="text-muted fs-11 d-block">Min Salary</span>
                    <span class="fw-bold fs-13 text-primary" id="detailMinSalary">₹0.00</span>
                </div>
                <div class="col-4">
                    <span class="text-muted fs-11 d-block">Max Salary</span>
                    <span class="fw-bold fs-13 text-primary" id="detailMaxSalary">₹0.00</span>
                </div>
                <div class="col-4">
                    <span class="text-muted fs-11 d-block">Min Service</span>
                    <span class="fw-bold fs-13 text-dark" id="detailMinService">0 Days</span>
                </div>
            </div>
        </div>

        <div class="detail-section-title">Statutory Notes & Description</div>
        <div class="detail-field-card">
            <p class="fs-12 text-secondary mb-0" id="detailDescription">-</p>
        </div>
    </div>

    <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2" data-bs-dismiss="offcanvas">Close</button>
        <button type="button" class="btn btn-primary btn-sm px-3 rounded-2 d-flex align-items-center gap-1 shadow-sm" id="btnEditFromDetail">
            <i class="ti ti-edit"></i> Edit Rule
        </button>
    </div>
</div>

<!-- ==========================================
     MODAL: Delete Confirmation
=========================================== -->
<div class="modal fade" id="deleteRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="stat-icon stat-icon-amber mx-auto mb-3" style="width: 56px; height: 56px; font-size: 26px; background: #fee2e2; color: #dc2626;">
                    <i class="ti ti-trash"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Delete Rule?</h5>
                <p class="text-muted fs-12 mb-3">
                    Are you sure you want to remove <strong id="deleteRuleName" class="text-dark"></strong>? This will detach its eligibility rules from pending payroll calculations.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3 rounded-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3 rounded-2" id="btnConfirmDelete">Delete Rule</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // CSRF Token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let currentEditingId = null;
    let currentDeletingId = null;

    // Toast Notification Helper
    function showToast(message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            Toast.fire({
                icon: type,
                title: message
            });
        } else {
            alert(message);
        }
    }

    // Helper: format currency
    function formatCurrency(val) {
        let num = parseFloat(val);
        if (isNaN(num) || num <= 0) return 'None';
        return '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    // Helper: format target group badge
    function getAppliesToBadge(type) {
        switch (type) {
            case 'all':
                return '<span class="badge badge-soft-blue"><i class="ti ti-users me-1"></i>All Staff</span>';
            case 'permanent':
                return '<span class="badge badge-soft-purple"><i class="ti ti-user-check me-1"></i>Permanent</span>';
            case 'contractual':
                return '<span class="badge badge-soft-amber"><i class="ti ti-file-text me-1"></i>Contractual</span>';
            case 'intern':
                return '<span class="badge badge-soft-cyan"><i class="ti ti-school me-1"></i>Interns</span>';
            case 'probation':
                return '<span class="badge badge-soft-secondary"><i class="ti ti-clock me-1"></i>Probation</span>';
            default:
                return '<span class="badge badge-soft-secondary">' + type + '</span>';
        }
    }

    // Initialize DataTable with AJAX
    const table = $('#applicabilityTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('masters.payroll.applicability') }}",
            type: "GET",
            data: function(d) {
                d.status = $('#filterStatus').val();
                d.applies_to = $('#filterAppliesTo').val();
                d.search = $('#filterSearch').val();
            },
            dataSrc: function(json) {
                if (json && json.data) {
                    $('#filteredCountBadge').text('Displaying ' + json.data.length + ' rules');
                    return json.data;
                }
                return [];
            }
        },
        columns: [
            {
                data: 'index',
                className: 'text-center text-muted fw-medium',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark fs-13 mb-1">${row.rule_name}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-code">${row.rule_code}</span>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'applies_to',
                render: function(data, type, row) {
                    return getAppliesToBadge(row.applies_to);
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let salarySummary = '';
                    if (row.min_salary > 0 && row.max_salary > 0) {
                        salarySummary = `<span class="badge bg-light text-dark border me-1"><i class="ti ti-currency-rupee"></i> ${formatCurrency(row.min_salary)} - ${formatCurrency(row.max_salary)}</span>`;
                    } else if (row.max_salary > 0) {
                        salarySummary = `<span class="badge bg-light text-dark border me-1">&le; ${formatCurrency(row.max_salary)}</span>`;
                    } else if (row.min_salary > 0) {
                        salarySummary = `<span class="badge bg-light text-dark border me-1">&gt; ${formatCurrency(row.min_salary)}</span>`;
                    }

                    let serviceSummary = '';
                    if (row.min_service_days > 0) {
                        serviceSummary = `<span class="badge badge-soft-secondary"><i class="ti ti-calendar-time me-1"></i>${row.min_service_days} days tenure</span>`;
                    }

                    return `
                        <div>
                            <div class="fw-medium text-dark fs-12 mb-1">${row.salary_condition}</div>
                            <div class="d-flex flex-wrap align-items-center gap-1">
                                ${salarySummary}
                                ${serviceSummary}
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'effective_display',
                render: function(data, type, row) {
                    return `<span class="badge bg-light text-dark border fs-11">${data}</span>`;
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function(data, type, row) {
                    const isChecked = data === 'Active' ? 'checked' : '';
                    return `
                        <div class="form-check form-switch form-check-status d-inline-block">
                            <input class="form-check-input btn-toggle-status" type="checkbox"
                                   data-id="${row.id}" data-name="${row.rule_name}" ${isChecked}>
                        </div>
                    `;
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn-action-icon btn-view" title="Preview Details"
                                    onclick="viewRuleDetails(${row.id})">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-edit" title="Quick Edit"
                                    onclick="openEditDrawer(${row.id})">
                                <i class="ti ti-edit"></i>
                            </button>
                            <a href="${row.edit_url}" class="btn-action-icon" title="Full Page Edit">
                                <i class="ti ti-external-link"></i>
                            </a>
                            <button type="button" class="btn-action-icon btn-delete" title="Delete Rule"
                                    onclick="confirmDeleteRule(${row.id}, '${row.rule_name.replace(/'/g, "\\'")}')">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        dom: 'rt<"d-none"i><"d-none"p>',
        order: [[0, 'asc']],
        pageLength: 10,
        language: {
            emptyTable: `
                <div class="text-center py-5">
                    <div class="stat-icon stat-icon-indigo mx-auto mb-3" style="width: 56px; height: 56px; font-size: 26px;">
                        <i class="ti ti-scale-outline"></i>
                    </div>
                    <h6 class="fw-bold text-dark">No Applicability Rules Found</h6>
                    <p class="text-muted fs-12">Try clearing your filters or create a new statutory rule using the button above.</p>
                </div>
            `,
            processing: `
                <div class="d-flex align-items-center justify-content-center py-4">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    <span class="fs-13 text-muted">Loading applicability rules...</span>
                </div>
            `
        },
        drawCallback: function(settings) {
            const api = this.api();
            const info = api.page.info();

            // Custom table info
            if (info.recordsDisplay > 0) {
                $('#tableInfoContainer').html(`
                    <span class="text-muted fs-12">
                        Showing <strong>${info.start + 1}</strong> to <strong>${info.end}</strong> of <strong>${info.recordsDisplay}</strong> applicability rules
                    </span>
                `);
            } else {
                $('#tableInfoContainer').html('<span class="text-muted fs-12">0 rules found</span>');
            }

            // Custom Pagination Controls
            let paginationHtml = '<div class="d-flex align-items-center gap-1">';
            paginationHtml += `
                <button type="button" class="paginate_button ${info.page === 0 ? 'disabled' : ''}" id="btnCustomPrev">
                    <i class="ti ti-chevron-left"></i>
                </button>
            `;

            for (let i = 0; i < info.pages; i++) {
                if (info.pages <= 7 || (i >= info.page - 2 && i <= info.page + 2) || i === 0 || i === info.pages - 1) {
                    paginationHtml += `
                        <button type="button" class="paginate_button ${i === info.page ? 'current' : ''}" data-page="${i}">
                            ${i + 1}
                        </button>
                    `;
                } else if (i === info.page - 3 || i === info.page + 3) {
                    paginationHtml += '<span class="px-2 text-muted">...</span>';
                }
            }

            paginationHtml += `
                <button type="button" class="paginate_button ${info.page >= info.pages - 1 || info.pages === 0 ? 'disabled' : ''}" id="btnCustomNext">
                    <i class="ti ti-chevron-right"></i>
                </button>
            `;
            paginationHtml += '</div>';

            $('#tablePaginationContainer').html(paginationHtml);
        }
    });

    // Custom Pagination Click Handlers
    $(document).on('click', '#btnCustomPrev:not(.disabled)', function() {
        table.page('previous').draw('page');
    });

    $(document).on('click', '#btnCustomNext:not(.disabled)', function() {
        table.page('next').draw('page');
    });

    $(document).on('click', '#tablePaginationContainer .paginate_button[data-page]', function() {
        const targetPage = parseInt($(this).data('page'));
        table.page(targetPage).draw('page');
    });

    // Filter Listeners with Debounce for search
    let searchTimeout = null;
    $('#filterSearch').on('keyup input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 300);
    });

    $('#filterAppliesTo, #filterStatus').on('change', function() {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function() {
        $('#filterSearch').val('');
        $('#filterAppliesTo').val('all');
        $('#filterStatus').val('all');
        table.ajax.reload();
    });

    // Refresh Button Click
    $('#btnRefreshTable').on('click', function() {
        const icon = $('#refreshIcon');
        icon.addClass('rotate-animation');
        table.ajax.reload(function() {
            setTimeout(function() {
                icon.removeClass('rotate-animation');
            }, 600);
        });
    });

    // Real-time Status Switch Toggle
    $(document).on('change', '.btn-toggle-status', function() {
        const checkbox = $(this);
        const ruleId = checkbox.data('id');
        const ruleName = checkbox.data('name');
        const originalChecked = !checkbox.prop('checked');

        $.ajax({
            url: `{{ url('masters/payroll/applicability/toggle-status') }}/${ruleId}`,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    updateKpis();
                } else {
                    checkbox.prop('checked', originalChecked);
                    showToast(response.message || 'Unable to change status', 'error');
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', originalChecked);
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error changing status';
                showToast(msg, 'error');
            }
        });
    });

    // KPI update helper
    function updateKpis() {
        $.ajax({
            url: "{{ route('masters.payroll.applicability') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.data) {
                    const total = res.data.length;
                    const active = res.data.filter(r => r.status === 'Active').length;
                    const perm = res.data.filter(r => r.applies_to === 'permanent').length;
                    const contr = res.data.filter(r => r.applies_to === 'contractual' || r.applies_to === 'intern').length;

                    $('#kpiTotalRules').text(total);
                    $('#kpiActiveRules').text(active);
                    $('#kpiPermanentRules').text(perm);
                    $('#kpiContractualRules').text(contr);
                }
            }
        });
    }

    // Open Drawer for Add Rule
    const drawerEl = document.getElementById('applicabilityDrawer');
    const drawerInstance = new bootstrap.Offcanvas(drawerEl);

    $('#btnOpenAddDrawer').on('click', function() {
        resetDrawerForm();
        $('#drawerTitle').text('Configure Applicability Rule');
        $('#drawerSubtitle').text('Set target staff group, salary criteria, and eligibility thresholds');
        $('#btnSaveText').text('Save Rule');
        $('#formMethod').val('POST');
        currentEditingId = null;
        drawerInstance.show();
    });

    function resetDrawerForm() {
        $('#applicabilityForm')[0].reset();
        $('#applicabilityForm').removeClass('was-validated');
        $('#ruleId').val('');
        $('#statusActive').prop('checked', true);
        $('#min_salary').val('0.00');
        $('#max_salary').val('0.00');
        $('#min_service_days').val('0');
    }

    // Quick Presets Handler
    window.applyPreset = function(preset) {
        switch(preset) {
            case 'pf':
                $('#rule_name').val("Employees' Provident Fund (EPF) Applicability");
                $('#rule_code').val('PF_APPLY');
                $('#applies_to').val('permanent');
                $('#salary_condition').val('Basic <= ₹15,000 or voluntary contribution');
                $('#min_salary').val('0.00');
                $('#max_salary').val('15000.00');
                $('#min_service_days').val('0');
                $('#description').val('Statutory Provident Fund scheme applicable to permanent staff with monthly basic wages up to INR 15,000.');
                break;
            case 'esic':
                $('#rule_name').val('Employee State Insurance (ESIC) Coverage');
                $('#rule_code').val('ESI_APPLY');
                $('#applies_to').val('all');
                $('#salary_condition').val('Gross monthly earnings <= ₹21,000');
                $('#min_salary').val('0.00');
                $('#max_salary').val('21000.00');
                $('#min_service_days').val('0');
                $('#description').val('Mandatory social security and medical benefit coverage for staff whose gross salary is at or below INR 21,000 per month.');
                break;
            case 'pt':
                $('#rule_name').val('Professional Tax (PT) State Liability');
                $('#rule_code').val('PT_APPLY');
                $('#applies_to').val('all');
                $('#salary_condition').val('Gross salary exceeding state threshold (> ₹10,000)');
                $('#min_salary').val('10000.01');
                $('#max_salary').val('0.00');
                $('#min_service_days').val('0');
                $('#description').val('State revenue tax levied according to employee work location and monthly gross income slabs.');
                break;
            case 'gratuity':
                $('#rule_name').val('Payment of Gratuity Benefit');
                $('#rule_code').val('GRATUITY_APPLY');
                $('#applies_to').val('permanent');
                $('#salary_condition').val('Continuous active tenure >= 5 continuous years (1825 days)');
                $('#min_salary').val('0.00');
                $('#max_salary').val('0.00');
                $('#min_service_days').val('1825');
                $('#description').val('Gratuity entitlement payable upon exit or retirement after minimum qualifying tenure of 5 continuous years.');
                break;
            case 'intern':
                $('#rule_name').val('Intern Stipend & Allowance Exemption');
                $('#rule_code').val('INTERN_APPLY');
                $('#applies_to').val('intern');
                $('#salary_condition').val('Fixed Stipend only (Exempt from PF/ESIC deductions)');
                $('#min_salary').val('5000.00');
                $('#max_salary').val('35000.00');
                $('#min_service_days').val('0');
                $('#description').val('Internship remuneration schedule exempt from statutory employee deductions and governed by academic agreement.');
                break;
        }
    };

    // Open Drawer for Edit
    window.openEditDrawer = function(id) {
        $.ajax({
            url: `{{ url('masters/payroll/applicability/edit') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.applicability) {
                    const rule = res.applicability;
                    resetDrawerForm();
                    currentEditingId = rule.id;
                    $('#ruleId').val(rule.id);
                    $('#formMethod').val('PUT');
                    $('#rule_name').val(rule.rule_name);
                    $('#rule_code').val(rule.rule_code);
                    $('#applies_to').val(rule.applies_to);
                    $('#salary_condition').val(rule.salary_condition || '');
                    $('#min_salary').val(rule.min_salary || '0.00');
                    $('#max_salary').val(rule.max_salary || '0.00');
                    $('#min_service_days').val(rule.min_service_days || '0');
                    if (rule.effective_from) {
                        $('#effective_from').val(rule.effective_from.substring(0, 10));
                    }
                    if (rule.status === 'Active') {
                        $('#statusActive').prop('checked', true);
                    } else {
                        $('#statusInactive').prop('checked', true);
                    }
                    $('#description').val(rule.description || '');

                    $('#drawerTitle').text('Edit Applicability Rule');
                    $('#drawerSubtitle').text('Update statutory qualifications, salary ceilings, and target staff');
                    $('#btnSaveText').text('Update Rule');

                    drawerInstance.show();
                }
            },
            error: function() {
                showToast('Failed to load rule configuration', 'error');
            }
        });
    };

    // Save Rule Click Handler (Add or Update via AJAX)
    $('#btnSaveRule').on('click', function() {
        const form = document.getElementById('applicabilityForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const btn = $(this);
        const originalText = $('#btnSaveText').text();
        btn.prop('disabled', true);
        $('#btnSaveText').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...');

        const isUpdate = $('#formMethod').val() === 'PUT';
        const url = isUpdate
            ? `{{ url('masters/payroll/applicability/update') }}/${currentEditingId}`
            : `{{ route('masters.payroll.applicability.store') }}`;

        const postData = $('#applicabilityForm').serialize();

        $.ajax({
            url: url,
            type: isUpdate ? 'PUT' : 'POST',
            data: postData,
            success: function(response) {
                btn.prop('disabled', false);
                $('#btnSaveText').text(originalText);

                if (response.success) {
                    showToast(response.message, 'success');
                    drawerInstance.hide();
                    table.ajax.reload();
                    updateKpis();
                } else {
                    showToast(response.message || 'Error saving rule', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                $('#btnSaveText').text(originalText);

                let errorMsg = 'Validation failed. Please check inputs.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    errorMsg = xhr.responseJSON.errors[firstKey][0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, 'error');
            }
        });
    });

    // Detail Preview Drawer Handler
    const detailDrawerEl = document.getElementById('applicabilityDetailDrawer');
    const detailDrawerInstance = new bootstrap.Offcanvas(detailDrawerEl);

    window.viewRuleDetails = function(id) {
        $.ajax({
            url: `{{ url('masters/payroll/applicability/edit') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.applicability) {
                    const r = res.applicability;
                    currentEditingId = r.id;

                    $('#detailRuleName').text(r.rule_name);
                    $('#detailRuleCode').text(r.rule_code);

                    let statusClass = r.status === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary';
                    $('#detailStatusBadge').attr('class', 'badge ' + statusClass).text(r.status);

                    let appliesMap = {
                        'all': 'All Employees (Company-wide)',
                        'permanent': 'Permanent Staff Only',
                        'contractual': 'Contractual / Agency Staff',
                        'intern': 'Interns / Trainees',
                        'probation': 'Probationary Employees'
                    };
                    $('#detailAppliesTo').text(appliesMap[r.applies_to] || r.applies_to);

                    $('#detailEffectiveDate').text(r.effective_from ? new Date(r.effective_from).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'Immediate');
                    $('#detailSalaryCondition').text(r.salary_condition || 'Standard wage calculations apply');
                    $('#detailMinSalary').text(formatCurrency(r.min_salary));
                    $('#detailMaxSalary').text(formatCurrency(r.max_salary));
                    $('#detailMinService').text(r.min_service_days > 0 ? r.min_service_days + ' Days' : 'None');
                    $('#detailDescription').text(r.description || 'No additional statutory notes recorded.');

                    detailDrawerInstance.show();
                }
            },
            error: function() {
                showToast('Failed to load rule preview', 'error');
            }
        });
    };

    // Edit directly from Detail Drawer
    $('#btnEditFromDetail').on('click', function() {
        detailDrawerInstance.hide();
        setTimeout(function() {
            if (currentEditingId) {
                openEditDrawer(currentEditingId);
            }
        }, 300);
    });

    // Delete Confirmation Modal Handler
    const deleteModalEl = document.getElementById('deleteRuleModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);

    window.confirmDeleteRule = function(id, name) {
        currentDeletingId = id;
        $('#deleteRuleName').text(`"${name}"`);
        deleteModal.show();
    };

    $('#btnConfirmDelete').on('click', function() {
        if (!currentDeletingId) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...');

        $.ajax({
            url: `{{ url('masters/payroll/applicability/delete') }}/${currentDeletingId}`,
            type: 'DELETE',
            success: function(response) {
                btn.prop('disabled', false).text('Delete Rule');
                deleteModal.hide();

                if (response.success) {
                    showToast(response.message, 'success');
                    table.ajax.reload();
                    updateKpis();
                } else {
                    showToast(response.message || 'Error deleting rule', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Delete Rule');
                deleteModal.hide();
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting rule';
                showToast(msg, 'error');
            }
        });
    });
});
</script>
@endpush

@endsection
