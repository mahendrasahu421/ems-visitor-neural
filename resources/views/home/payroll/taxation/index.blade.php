@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* Scope styles for taxation master page */
    .taxation-page {
        color: #1e293b;
    }

    /* KPI Summary Cards */
    .taxation-page .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .taxation-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .taxation-page .stat-icon {
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

    .stat-icon-rose {
        background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%);
        color: #e11d48;
    }

    .stat-icon-cyan {
        background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
        color: #0891b2;
    }

    .stat-icon-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    /* Filter Card */
    .taxation-page .filter-card {
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
    .taxation-page .table-container-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .tax-table {
        margin-bottom: 0 !important;
    }

    .tax-table th {
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

    .tax-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .tax-table tbody tr:hover {
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

    .badge-deduction {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-weight: 600;
        font-size: 11.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-employer {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        font-weight: 600;
        font-size: 11.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-statutory {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
        font-weight: 600;
        font-size: 11.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-base {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-weight: 500;
        font-size: 11.5px;
        padding: 3px 8px;
        border-radius: 6px;
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

    .badge-soft-indigo {
        background-color: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }

    .badge-soft-cyan {
        background-color: #cffafe;
        color: #0891b2;
        border: 1px solid #a5f3fc;
    }

    .badge-soft-rose {
        background-color: #ffe4e6;
        color: #e11d48;
        border: 1px solid #fecdd3;
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
    .taxation-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .taxation-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .taxation-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

    .taxation-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .taxation-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .taxation-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #4338ca !important;
        border-color: #4338ca !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(67, 56, 202, 0.28) !important;
    }

    .taxation-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .taxation-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Offcanvas Slider Drawers */
    #taxationDrawer,
    #taxationDetailDrawer {
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

    .tax-drawer-hero {
        background: linear-gradient(145deg, #1e1b4b 0%, #0f172a 100%);
        position: relative;
        padding: 24px 24px 20px;
        color: #ffffff;
        overflow: hidden;
    }

    .tax-drawer-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(99, 102, 241, 0) 70%);
        pointer-events: none;
    }

    .tax-drawer-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -30px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(236, 72, 153, 0.22) 0%, rgba(236, 72, 153, 0) 70%);
        pointer-events: none;
    }

    .tax-type-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tax-type-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .tax-type-card.active-deduction {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .tax-type-card.active-employer {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .section-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .metric-preview-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
    }
</style>

<div class="page-wrapper taxation-page">
    <div class="content container-fluid py-4">

        <!-- Page Header & Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-indigo-subtle text-indigo fw-medium px-2 py-1 rounded" style="background-color: #e0e7ff; color: #4338ca;">
                        Payroll Master
                    </span>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">Taxation & Compliance</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">Taxation & Statutory Rules</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3" id="btnRefreshTaxCatalog">
                    <i class="ti ti-refresh"></i> Refresh
                </button>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 shadow-sm" id="btnOpenAddTaxDrawer">
                    <i class="ti ti-circle-plus"></i> Add Taxation Rule
                </button>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Tax Rules -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Statutory Rules</span>
                        <div class="stat-icon stat-icon-indigo">
                            <i class="ti ti-receipt-tax"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiTotalTax">{{ $totalTaxRules }}</h3>
                        <span class="badge badge-soft-indigo fs-11">Compliance Master</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Active statutory & tax compliance policies</p>
                </div>
            </div>

            <!-- Employee Deductions -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Employee Deductions</span>
                        <div class="stat-icon stat-icon-rose">
                            <i class="ti ti-user-minus"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiDeductionTax">{{ $deductionRules }}</h3>
                        <span class="badge badge-soft-rose fs-11">Withholding</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">EPF, ESIC, Professional Tax & TDS</p>
                </div>
            </div>

            <!-- Employer Contributions -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Employer Share</span>
                        <div class="stat-icon stat-icon-cyan">
                            <i class="ti ti-building-bank"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiEmployerTax">{{ $employerContribRules }}</h3>
                        <span class="badge badge-soft-cyan fs-11">Company Share</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">EPF Employer, ESIC & Gratuity Accrual</p>
                </div>
            </div>

            <!-- Active in Payroll -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Active in Payroll</span>
                        <div class="stat-icon stat-icon-green">
                            <i class="ti ti-shield-check"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiActiveTax">{{ $activeTaxRules }}</h3>
                        <span class="badge badge-soft-success fs-11">In Effect</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Enforced during payslip calculations</p>
                </div>
            </div>
        </div>

        <!-- Multi-Filter Toolbar Card -->
        <div class="card filter-card mb-4">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <!-- Search input -->
                    <div class="col-lg-3 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="filterTaxSearch" class="form-control border-start-0 ps-0"
                                placeholder="Search by rule name, code...">
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterTaxType" class="form-select">
                            <option value="all">All Tax Types</option>
                            <option value="deduction">Employee Deductions</option>
                            <option value="employer_contribution">Employer Contributions</option>
                            <option value="statutory_tax">Statutory Taxes</option>
                        </select>
                    </div>

                    <!-- Applicable Base Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterTaxBase" class="form-select">
                            <option value="all">All Base Salaries</option>
                            <option value="basic">Basic Salary</option>
                            <option value="gross">Gross Salary</option>
                            <option value="net">Net Salary</option>
                            <option value="taxable_income">Taxable Income</option>
                            <option value="ctc">Annual CTC</option>
                        </select>
                    </div>

                    <!-- Calculation Basis Filter -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <select id="filterTaxBasis" class="form-select">
                            <option value="all">All Calculation Methods</option>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed_slab">Fixed Slab (₹)</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <select id="filterTaxStatus" class="form-select">
                            <option value="all">All Statuses</option>
                            <option value="Active">Active Only</option>
                            <option value="Inactive">Inactive Only</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-lg-1 col-md-4 col-12 text-end">
                        <button type="button" id="btnResetTaxFilters" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1" title="Reset Filters">
                            <i class="ti ti-rotate-clockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container Card -->
        <div class="card table-container-card">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-receipt-tax fs-18 text-primary"></i>
                    <h5 class="mb-0 fw-bold text-dark">Statutory & Tax Rules Catalog</h5>
                    <span class="badge bg-light text-secondary border px-2 ms-1" id="taxTableCount">{{ count($taxes) }} rules</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12">Click any item or action button to inspect compliance rules.</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table tax-table align-middle" id="taxationTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Tax Rule Name & Code</th>
                                <th>Category / Type</th>
                                <th>Applicable Base</th>
                                <th>Statutory Rate</th>
                                <th>Wage Ceiling / Limit</th>
                                <th>Effective Date</th>
                                <th>Status</th>
                                <th width="120" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <x-footer />
</div>

<!-- ==========================================
     OFFCANVAS: ADD / EDIT TAXATION RULE DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="taxationDrawer" aria-labelledby="taxationDrawerLabel">
    <!-- Header Hero Banner -->
    <div class="tax-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="taxDrawerPill">
                <i class="ti ti-plus me-1 text-primary"></i> NEW TAX RULE
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="taxDrawerTitle">Add Taxation Rule</h4>
        <p class="text-white-50 fs-12 mb-0" id="taxDrawerSubtitle">Configure statutory deduction rates, wage caps & applicable bases.</p>
    </div>

    <!-- Drawer Form Body -->
    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <form id="taxationForm">
            @csrf
            <input type="hidden" id="tax_id" name="id" value="">
            <input type="hidden" id="tax_form_method" name="_method" value="POST">

            <!-- Validation Error Alert -->
            <div class="alert alert-danger d-none py-2 px-3 fs-12 rounded-3 mb-3" id="taxAlertBox"></div>

            <!-- Identity Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">1. Rule Identification</div>

                <!-- Tax Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Tax Rule Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="input_tax_name" name="tax_name"
                        placeholder="e.g. Employee Provident Fund (EPF), Professional Tax" required>
                </div>

                <!-- Tax Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">
                        Tax / Statutory Code <span class="text-danger">*</span>
                        <small class="text-muted fw-normal">(Unique identifier, e.g. EPF_EMP)</small>
                    </label>
                    <input type="text" class="form-control text-uppercase" id="input_tax_code" name="tax_code"
                        placeholder="e.g. EPF_EMP, ESIC_DED, PT_STATE" style="letter-spacing: 0.5px;" required>
                </div>

                <!-- Category / Type -->
                <div class="mb-2">
                    <label class="form-label fw-semibold fs-13 mb-1">Classification Type <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="tax-type-card w-100" id="labelTaxDeduction">
                                <input type="radio" name="type" id="typeTaxDeduction" value="deduction" class="form-check-input mt-0" checked>
                                <div>
                                    <div class="fw-bold fs-13 text-danger">
                                        <i class="ti ti-arrow-down-right me-1"></i> Deduction
                                    </div>
                                    <div class="text-muted fs-11">Employee deduction</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="tax-type-card w-100" id="labelTaxEmployer">
                                <input type="radio" name="type" id="typeTaxEmployer" value="employer_contribution" class="form-check-input mt-0">
                                <div>
                                    <div class="fw-bold fs-13 text-primary">
                                        <i class="ti ti-building-bank me-1"></i> Employer Share
                                    </div>
                                    <div class="text-muted fs-11">Company liability</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formula & Basis Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">2. Calculation Formula & Base</div>

                <!-- Applicable On -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Applicable On Base <span class="text-danger">*</span></label>
                    <select class="form-select" id="input_applicable_on" name="applicable_on" required>
                        <option value="basic">Basic Salary</option>
                        <option value="gross">Gross Salary</option>
                        <option value="net">Net Salary</option>
                        <option value="taxable_income">Taxable Income</option>
                        <option value="ctc">Annual CTC</option>
                    </select>
                </div>

                <!-- Calculation Basis -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Calculation Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="input_tax_calc_basis" name="calculation_basis" required>
                        <option value="percentage">Percentage Rate (%)</option>
                        <option value="fixed_slab">Fixed Slab Amount (₹)</option>
                    </select>
                </div>

                <!-- Percentage Input -->
                <div class="mb-3" id="wrapperTaxRate">
                    <label class="form-label fw-semibold fs-13 mb-1">Percentage Rate (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                            id="input_tax_rate" name="rate" placeholder="e.g. 12.00" value="0">
                        <span class="input-group-text bg-light text-dark fw-bold">%</span>
                    </div>
                </div>

                <!-- Fixed Amount Input -->
                <div class="mb-3 d-none" id="wrapperTaxFixed">
                    <label class="form-label fw-semibold fs-13 mb-1">Fixed Amount (₹ / month)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                        <input type="number" step="0.01" min="0" class="form-control"
                            id="input_tax_fixed" name="fixed_amount" placeholder="e.g. 200.00" value="0">
                    </div>
                </div>

                <!-- Threshold Ceiling -->
                <div class="mb-2">
                    <label class="form-label fw-semibold fs-13 mb-1">
                        Wage Ceiling / Threshold Cap (₹)
                        <small class="text-muted fw-normal">(0 = No ceiling)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                        <input type="number" step="0.01" min="0" class="form-control"
                            id="input_threshold_limit" name="threshold_limit" placeholder="e.g. 15000.00" value="0">
                    </div>
                    <small class="text-muted fs-11 mt-1 d-block">e.g. ₹15,000 EPF ceiling or ₹21,000 ESIC gross salary wage threshold.</small>
                </div>
            </div>

            <!-- Validity & Status Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">3. Validity & Regulatory Notes</div>

                <div class="row g-3">
                    <!-- Effective Date -->
                    <div class="col-6">
                        <label class="form-label fw-semibold fs-13 mb-1">Effective From</label>
                        <input type="date" class="form-control" id="input_effective_from" name="effective_from" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Status -->
                    <div class="col-6">
                        <label class="form-label fw-semibold fs-13 mb-1">Status</label>
                        <select class="form-select" id="input_tax_status" name="status" required>
                            <option value="Active">Active (In use)</option>
                            <option value="Inactive">Inactive (Disabled)</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Regulatory Reference / Description</label>
                        <textarea class="form-control" id="input_tax_description" name="description" rows="2"
                            placeholder="e.g. IT Act Section 192, EPFO Circular 2026 guidelines..."></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="offcanvas-footer p-3 bg-white border-top d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="offcanvas">
            <i class="ti ti-x me-1"></i> Cancel
        </button>
        <button type="button" class="btn btn-primary rounded-3 px-4 d-flex align-items-center gap-2" id="btnSaveTaxRule">
            <span class="spinner-border spinner-border-sm d-none" id="saveTaxSpinner"></span>
            <i class="ti ti-device-floppy" id="saveTaxIcon"></i>
            <span id="saveTaxBtnText">Save Tax Rule</span>
        </button>
    </div>
</div>

<!-- ==========================================
     OFFCANVAS: TAX RULE DETAIL PREVIEW DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="taxationDetailDrawer" aria-labelledby="taxationDetailDrawerLabel">
    <div class="tax-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="detailTaxPill">
                STATUTORY RULE DETAIL
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="detailTaxName">-</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge badge-code" id="detailTaxCode">-</span>
            <span id="detailTaxTypeBadge">-</span>
            <span id="detailTaxStatusBadge">-</span>
        </div>
    </div>

    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <!-- Highlights -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="metric-preview-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Statutory Rate</div>
                    <div class="fw-bold text-primary fs-15" id="detailTaxRateValue">-</div>
                </div>
            </div>
            <div class="col-6">
                <div class="metric-preview-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Wage Threshold</div>
                    <div class="fw-bold text-dark fs-15" id="detailTaxCeilingValue">-</div>
                </div>
            </div>
        </div>

        <!-- Compliance Specification -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
            <div class="section-label">Compliance Specifications</div>
            <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Applicable Salary Base:</span>
                    <span class="fw-semibold fs-13" id="detailTaxBaseLabel">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Calculation Basis:</span>
                    <span class="fw-semibold fs-13" id="detailTaxBasisLabel">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Effective From:</span>
                    <span class="fw-semibold fs-13" id="detailTaxEffectiveLabel">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2">
                    <span class="text-muted fs-13">Current Policy Status:</span>
                    <span class="fw-semibold fs-13" id="detailTaxStatusLabel">-</span>
                </li>
            </ul>
        </div>

        <!-- Legal References & Description -->
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="section-label">Legal Authority & Circular Guidelines</div>
            <p class="text-muted fs-13 mb-0" id="detailTaxDescription">-</p>
        </div>
    </div>

    <div class="offcanvas-footer p-3 bg-white border-top d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="offcanvas">
            Close
        </button>
        <button type="button" class="btn btn-primary rounded-3 px-3 d-flex align-items-center gap-2" id="btnDetailTaxEditTrigger">
            <i class="ti ti-edit"></i> Edit Rule
        </button>
    </div>
</div>

<!-- ==========================================
     MODAL: DELETE TAX RULE CONFIRMATION
     ========================================== -->
<div class="modal fade" id="deleteTaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle">
                        <i class="ti ti-trash fs-28"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Delete Tax Rule?</h5>
                <p class="text-muted fs-13 mb-3">
                    Are you sure you want to delete <span class="fw-bold text-dark" id="deleteTaxName"></span>?
                    This statutory policy will be removed from future payroll runs.
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-3 d-flex align-items-center gap-1" id="btnConfirmTaxDelete">
                        <span class="spinner-border spinner-border-sm d-none" id="deleteTaxSpinner"></span>
                        <i class="ti ti-trash" id="deleteTaxIcon"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after_scripts')
<script>
$(document).ready(function() {
    let currentTaxData = [];
    let activeTaxEditingId = null;
    let deletingTaxId = null;

    // Toast notification helper
    function showToast(message, type = 'success') {
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const toastHtml = `
            <div class="toast align-items-center text-white ${bgClass} border-0 show shadow-lg position-fixed bottom-0 end-0 m-3"
                 role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body fs-13 fw-medium">
                        <i class="ti ti-${type === 'success' ? 'circle-check' : 'alert-circle'} me-1"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        const $toast = $(toastHtml).appendTo('body');
        setTimeout(() => {
            $toast.fadeOut(400, function() { $(this).remove(); });
        }, 3500);
    }

    // Initialize DataTable with AJAX
    const taxTable = $('#taxationTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('masters.payroll.taxation') }}",
            type: "GET",
            data: function(d) {
                d.type = $('#filterTaxType').val();
                d.applicable_on = $('#filterTaxBase').val();
                d.calculation_basis = $('#filterTaxBasis').val();
                d.status = $('#filterTaxStatus').val();
                d.search = $('#filterTaxSearch').val();
            },
            dataSrc: function(json) {
                currentTaxData = json.data || [];
                $('#taxTableCount').text(currentTaxData.length + ' rules');
                updateTaxKPIs(currentTaxData);
                return currentTaxData;
            }
        },
        columns: [
            {
                data: null,
                render: function(data, type, row, meta) {
                    return `<span class="text-muted fw-semibold">${meta.row + 1}</span>`;
                },
                className: "text-center",
                orderable: false
            },
            {
                data: null,
                render: function(data) {
                    const desc = data.description && data.description !== 'No description provided.'
                        ? `<div class="text-muted fs-11 text-truncate" style="max-width: 260px;" title="${data.description}">${data.description}</div>`
                        : '';
                    return `
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark fs-13">${data.tax_name}</span>
                                <span class="badge badge-code">${data.tax_code}</span>
                            </div>
                            ${desc}
                        </div>
                    `;
                }
            },
            {
                data: 'type',
                render: function(data) {
                    if (data === 'deduction') {
                        return `<span class="badge-deduction"><i class="ti ti-arrow-down-right"></i> Deduction</span>`;
                    } else if (data === 'employer_contribution') {
                        return `<span class="badge-employer"><i class="ti ti-building-bank"></i> Employer Share</span>`;
                    } else {
                        return `<span class="badge-statutory"><i class="ti ti-receipt-tax"></i> Statutory Tax</span>`;
                    }
                }
            },
            {
                data: 'applicable_label',
                render: function(data) {
                    return `<span class="badge badge-base"><i class="ti ti-layers-subtract me-1 text-primary"></i>${data}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `<span class="fw-bold text-dark fs-13">${data.formatted_rate}</span>`;
                }
            },
            {
                data: 'formatted_limit',
                render: function(data) {
                    if (data === 'No Limit') {
                        return `<span class="text-muted fs-12">No Limit</span>`;
                    }
                    return `<span class="badge bg-light text-dark border">${data}</span>`;
                }
            },
            {
                data: 'effective_from',
                render: function(data) {
                    return `<span class="text-muted fs-12">${data}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    const isChecked = data.status === 'Active' ? 'checked' : '';
                    const badgeClass = data.status === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary';
                    return `
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch form-check-status mb-0">
                                <input class="form-check-input tax-status-toggle" type="checkbox"
                                       data-id="${data.id}" ${isChecked} title="Click to toggle status">
                            </div>
                            <span class="badge ${badgeClass} fs-11">${data.status}</span>
                        </div>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                className: "text-end",
                render: function(data) {
                    return `
                        <div class="d-inline-flex align-items-center gap-1">
                            <button type="button" class="btn-action-icon btn-view btn-tax-view" data-id="${data.id}" title="View Details">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-edit btn-tax-edit" data-id="${data.id}" title="Edit Rule">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-delete btn-tax-delete" data-id="${data.id}" data-name="${data.tax_name}" title="Delete Rule">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        dom: '<"d-flex align-items-center justify-content-between p-3 border-bottom"<"length-control"l><"dt-search"f>>rt<"d-flex align-items-center justify-content-between p-3 border-top"<"info-control"i><"paginate-control"p>>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search within tax rules...",
            lengthMenu: "Show _MENU_ rules",
            info: "Showing _START_ to _END_ of _TOTAL_ rules",
            infoEmpty: "No taxation rules found",
            emptyTable: "No taxation rules configured yet.",
            paginate: {
                previous: '<i class="ti ti-chevron-left"></i>',
                next: '<i class="ti ti-chevron-right"></i>'
            }
        }
    });

    // KPI Counters updater
    function updateTaxKPIs(items) {
        const total = items.length;
        const deductions = items.filter(i => i.type === 'deduction').length;
        const employer = items.filter(i => i.type === 'employer_contribution').length;
        const active = items.filter(i => i.status === 'Active').length;

        $('#kpiTotalTax').text(total);
        $('#kpiDeductionTax').text(deductions);
        $('#kpiEmployerTax').text(employer);
        $('#kpiActiveTax').text(active);
    }

    // Filters listener
    $('#filterTaxType, #filterTaxBase, #filterTaxBasis, #filterTaxStatus').on('change', function() {
        taxTable.ajax.reload();
    });

    let searchTaxTimeout;
    $('#filterTaxSearch').on('keyup', function() {
        clearTimeout(searchTaxTimeout);
        searchTaxTimeout = setTimeout(() => {
            taxTable.ajax.reload();
        }, 300);
    });

    $('#btnResetTaxFilters').on('click', function() {
        $('#filterTaxSearch').val('');
        $('#filterTaxType').val('all');
        $('#filterTaxBase').val('all');
        $('#filterTaxBasis').val('all');
        $('#filterTaxStatus').val('all');
        taxTable.ajax.reload();
    });

    $('#btnRefreshTaxCatalog').on('click', function() {
        taxTable.ajax.reload(null, false);
        showToast('Taxation catalog refreshed!', 'success');
    });

    // Calculation Basis selector toggle
    $('#input_tax_calc_basis').on('change', function() {
        if ($(this).val() === 'percentage') {
            $('#wrapperTaxRate').removeClass('d-none');
            $('#wrapperTaxFixed').addClass('d-none');
        } else {
            $('#wrapperTaxRate').addClass('d-none');
            $('#wrapperTaxFixed').removeClass('d-none');
        }
    });

    // Type radio toggle
    $('input[name="type"]').on('change', function() {
        updateTaxRadioCards();
    });

    function updateTaxRadioCards() {
        const val = $('input[name="type"]:checked').val();
        if (val === 'deduction') {
            $('#labelTaxDeduction').addClass('active-deduction');
            $('#labelTaxEmployer').removeClass('active-employer');
        } else {
            $('#labelTaxDeduction').removeClass('active-deduction');
            $('#labelTaxEmployer').addClass('active-employer');
        }
    }
    updateTaxRadioCards();

    // Auto capitalize tax code input
    $('#input_tax_code').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    // Auto suggest code from name if creating
    $('#input_tax_name').on('input', function() {
        if (!activeTaxEditingId && $('#input_tax_code').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 10);
            $('#input_tax_code').val(suggested);
        }
    });

    // Open Add Tax Drawer
    $('#btnOpenAddTaxDrawer').on('click', function() {
        activeTaxEditingId = null;
        $('#taxAlertBox').addClass('d-none').empty();
        $('#taxationForm')[0].reset();
        $('#tax_id').val('');
        $('#tax_form_method').val('POST');

        $('#taxDrawerPill').html('<i class="ti ti-plus me-1 text-primary"></i> NEW TAX RULE');
        $('#taxDrawerTitle').text('Add Taxation Rule');
        $('#taxDrawerSubtitle').text('Configure statutory deduction rates, wage caps & applicable bases.');
        $('#saveTaxBtnText').text('Save Tax Rule');

        // Defaults
        $('#typeTaxDeduction').prop('checked', true).trigger('change');
        $('#input_applicable_on').val('basic');
        $('#input_tax_calc_basis').val('percentage').trigger('change');
        $('#input_tax_rate').val('0');
        $('#input_tax_fixed').val('0');
        $('#input_threshold_limit').val('0');
        $('#input_effective_from').val(new Date().toISOString().split('T')[0]);
        $('#input_tax_status').val('Active');

        const drawer = new bootstrap.Offcanvas(document.getElementById('taxationDrawer'));
        drawer.show();
    });

    // Open Edit Tax Drawer
    $(document).on('click', '.btn-tax-edit', function() {
        const id = $(this).data('id');
        openEditTaxDrawer(id);
    });

    function openEditTaxDrawer(id) {
        const item = currentTaxData.find(t => t.id == id);
        if (!item) return;

        activeTaxEditingId = id;
        $('#taxAlertBox').addClass('d-none').empty();
        $('#tax_id').val(item.id);
        $('#tax_form_method').val('PUT');

        $('#taxDrawerPill').html('<i class="ti ti-edit me-1 text-warning"></i> EDIT RULE');
        $('#taxDrawerTitle').text('Edit ' + item.tax_name);
        $('#taxDrawerSubtitle').text('Modify statutory formula, applicable salary base or wage ceiling.');
        $('#saveTaxBtnText').text('Update Tax Rule');

        $('#input_tax_name').val(item.tax_name);
        $('#input_tax_code').val(item.tax_code);

        if (item.type === 'deduction') {
            $('#typeTaxDeduction').prop('checked', true).trigger('change');
        } else {
            $('#typeTaxEmployer').prop('checked', true).trigger('change');
        }

        $('#input_applicable_on').val(item.applicable_on);
        $('#input_tax_calc_basis').val(item.calculation_basis).trigger('change');
        $('#input_tax_rate').val(item.rate);
        $('#input_tax_fixed').val(item.fixed_amount);
        $('#input_threshold_limit').val(item.threshold_limit);
        $('#input_effective_from').val(item.raw_effective_from || '');
        $('#input_tax_status').val(item.status);
        $('#input_tax_description').val(item.description && item.description !== 'No description provided.' ? item.description : '');

        // Close preview if open
        const detailDrawerEl = document.getElementById('taxationDetailDrawer');
        const detailInstance = bootstrap.Offcanvas.getInstance(detailDrawerEl);
        if (detailInstance) detailInstance.hide();

        const drawer = new bootstrap.Offcanvas(document.getElementById('taxationDrawer'));
        drawer.show();
    }

    // Save or Update Tax Rule
    $('#btnSaveTaxRule').on('click', function(e) {
        e.preventDefault();
        $('#taxAlertBox').addClass('d-none').empty();

        const form = document.getElementById('taxationForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const isEditing = Boolean(activeTaxEditingId);
        const url = isEditing
            ? `{{ url('masters/payroll/taxation/update') }}/${activeTaxEditingId}`
            : `{{ route('masters.payroll.taxation.store') }}`;

        const formData = {
            _token: "{{ csrf_token() }}",
            tax_name: $('#input_tax_name').val(),
            tax_code: $('#input_tax_code').val(),
            type: $('input[name="type"]:checked').val(),
            applicable_on: $('#input_applicable_on').val(),
            calculation_basis: $('#input_tax_calc_basis').val(),
            rate: $('#input_tax_rate').val(),
            fixed_amount: $('#input_tax_fixed').val(),
            threshold_limit: $('#input_threshold_limit').val(),
            effective_from: $('#input_effective_from').val(),
            status: $('#input_tax_status').val(),
            description: $('#input_tax_description').val(),
        };

        if (isEditing) {
            formData._method = 'PUT';
        }

        $('#btnSaveTaxRule').prop('disabled', true);
        $('#saveTaxSpinner').removeClass('d-none');
        $('#saveTaxIcon').addClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#btnSaveTaxRule').prop('disabled', false);
                $('#saveTaxSpinner').addClass('d-none');
                $('#saveTaxIcon').removeClass('d-none');

                if (res.success) {
                    showToast(res.message, 'success');
                    const drawerEl = document.getElementById('taxationDrawer');
                    const drawer = bootstrap.Offcanvas.getInstance(drawerEl);
                    if (drawer) drawer.hide();

                    taxTable.ajax.reload(null, false);
                } else {
                    $('#taxAlertBox').removeClass('d-none').text(res.message || 'Operation failed.');
                }
            },
            error: function(xhr) {
                $('#btnSaveTaxRule').prop('disabled', false);
                $('#saveTaxSpinner').addClass('d-none');
                $('#saveTaxIcon').removeClass('d-none');

                let errorMsg = 'An error occurred while saving.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#taxAlertBox').removeClass('d-none').html(errorMsg);
            }
        });
    });

    // View Tax Rule Detail Drawer
    $(document).on('click', '.btn-tax-view', function() {
        const id = $(this).data('id');
        const item = currentTaxData.find(t => t.id == id);
        if (!item) return;

        $('#detailTaxName').text(item.tax_name);
        $('#detailTaxCode').text(item.tax_code);

        if (item.type === 'deduction') {
            $('#detailTaxTypeBadge').html('<span class="badge-deduction"><i class="ti ti-arrow-down-right"></i> Employee Deduction</span>');
        } else if (item.type === 'employer_contribution') {
            $('#detailTaxTypeBadge').html('<span class="badge-employer"><i class="ti ti-building-bank"></i> Employer Contribution</span>');
        } else {
            $('#detailTaxTypeBadge').html('<span class="badge-statutory"><i class="ti ti-receipt-tax"></i> Statutory Tax</span>');
        }

        const statusClass = item.status === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary';
        $('#detailTaxStatusBadge').html(`<span class="badge ${statusClass}">${item.status}</span>`);

        $('#detailTaxRateValue').text(item.formatted_rate);
        $('#detailTaxCeilingValue').text(item.formatted_limit);

        $('#detailTaxBaseLabel').text(item.applicable_label);
        $('#detailTaxBasisLabel').text(item.calculation_basis === 'percentage' ? 'Percentage Rate (%)' : 'Fixed Monthly Slab (₹)');
        $('#detailTaxEffectiveLabel').text(item.effective_from);
        $('#detailTaxStatusLabel').text(item.status);
        $('#detailTaxDescription').text(item.description || 'Standard government regulatory compliance guideline.');

        $('#btnDetailTaxEditTrigger').off('click').on('click', function() {
            openEditTaxDrawer(item.id);
        });

        const drawer = new bootstrap.Offcanvas(document.getElementById('taxationDetailDrawer'));
        drawer.show();
    });

    // Status Toggle Switch
    $(document).on('change', '.tax-status-toggle', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        const originalState = !checkbox.is(':checked');

        $.ajax({
            url: `{{ url('masters/payroll/taxation/toggle-status') }}/${id}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    taxTable.ajax.reload(null, false);
                } else {
                    checkbox.prop('checked', originalState);
                    showToast(res.message || 'Status toggle failed.', 'danger');
                }
            },
            error: function() {
                checkbox.prop('checked', originalState);
                showToast('Network error while toggling status.', 'danger');
            }
        });
    });

    // Delete Tax Modal
    $(document).on('click', '.btn-tax-delete', function() {
        deletingTaxId = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteTaxName').text(name);

        const modal = new bootstrap.Modal(document.getElementById('deleteTaxModal'));
        modal.show();
    });

    $('#btnConfirmTaxDelete').on('click', function() {
        if (!deletingTaxId) return;

        $('#btnConfirmTaxDelete').prop('disabled', true);
        $('#deleteTaxSpinner').removeClass('d-none');
        $('#deleteTaxIcon').addClass('d-none');

        $.ajax({
            url: `{{ url('masters/payroll/taxation/delete') }}/${deletingTaxId}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            dataType: 'json',
            success: function(res) {
                $('#btnConfirmTaxDelete').prop('disabled', false);
                $('#deleteTaxSpinner').addClass('d-none');
                $('#deleteTaxIcon').removeClass('d-none');

                const modalEl = document.getElementById('deleteTaxModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (res.success) {
                    showToast(res.message, 'success');
                    taxTable.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Delete operation failed.', 'danger');
                }
                deletingTaxId = null;
            },
            error: function() {
                $('#btnConfirmTaxDelete').prop('disabled', false);
                $('#deleteTaxSpinner').addClass('d-none');
                $('#deleteTaxIcon').removeClass('d-none');
                showToast('Failed to delete taxation rule.', 'danger');
                deletingTaxId = null;
            }
        });
    });
});
</script>
@endpush
