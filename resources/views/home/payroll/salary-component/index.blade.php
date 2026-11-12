@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* Scope styles for salary components page */
    .salary-comp-page {
        color: #1e293b;
    }

    /* KPI Summary Cards */
    .salary-comp-page .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .salary-comp-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .salary-comp-page .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-icon-purple {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #7c3aed;
    }

    .stat-icon-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    .stat-icon-rose {
        background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%);
        color: #e11d48;
    }

    .stat-icon-blue {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0284c7;
    }

    /* Filter Card */
    .salary-comp-page .filter-card {
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
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    /* Table Container Card */
    .salary-comp-page .table-container-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .comp-table {
        margin-bottom: 0 !important;
    }

    .comp-table th {
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

    .comp-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .comp-table tbody tr:hover {
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

    .badge-earning {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-weight: 600;
        font-size: 11.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
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

    .badge-calc-perc {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        font-weight: 500;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .badge-calc-fixed {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        font-weight: 500;
        font-size: 11px;
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

    .badge-soft-warning {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-soft-info {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .badge-soft-purple {
        background-color: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
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

    /* Modern DataTables Styling */
    .salary-comp-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .salary-comp-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .salary-comp-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

    .salary-comp-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .salary-comp-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .salary-comp-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
    }

    .salary-comp-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .salary-comp-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Offcanvas Slider Drawer */
    #salaryComponentDrawer,
    #salaryComponentDetailDrawer {
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

    .comp-drawer-hero {
        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        position: relative;
        padding: 24px 24px 20px;
        color: #ffffff;
        overflow: hidden;
    }

    .comp-drawer-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.35) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }

    .comp-drawer-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -30px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.25) 0%, rgba(168, 85, 247, 0) 70%);
        pointer-events: none;
    }

    .type-radio-card {
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

    .type-radio-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .type-radio-card.active-earning {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .type-radio-card.active-deduction {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .detail-section-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .detail-metric-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
    }
</style>

<div class="page-wrapper salary-comp-page">
    <div class="content container-fluid py-4">

        <!-- Page Header & Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary fw-medium px-2 py-1 rounded">Payroll Master</span>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">Earnings & Deductions</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">Salary Components Master</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3" id="btnRefreshCatalog">
                    <i class="ti ti-refresh"></i> Refresh
                </button>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 shadow-sm" id="btnOpenAddDrawer">
                    <i class="ti ti-circle-plus"></i> Add Component
                </button>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Components -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Total Components</span>
                        <div class="stat-icon stat-icon-purple">
                            <i class="ti ti-components"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiTotalCount">{{ $totalComponents }}</h3>
                        <span class="badge badge-soft-purple fs-11">Catalog</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Configured in organization salary master</p>
                </div>
            </div>

            <!-- Earning Components -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Earnings (Additions)</span>
                        <div class="stat-icon stat-icon-green">
                            <i class="ti ti-circle-arrow-up"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiEarningsCount">{{ $earningComponents }}</h3>
                        <span class="badge badge-soft-success fs-11">
                            {{ $totalComponents > 0 ? round(($earningComponents / $totalComponents) * 100) : 0 }}% of total
                        </span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Base, HRA, Conveyance & allowances</p>
                </div>
            </div>

            <!-- Deduction Components -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Deductions (Subtractions)</span>
                        <div class="stat-icon stat-icon-rose">
                            <i class="ti ti-circle-arrow-down"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiDeductionsCount">{{ $deductionComponents }}</h3>
                        <span class="badge badge-soft-warning fs-11">
                            {{ $totalComponents > 0 ? round(($deductionComponents / $totalComponents) * 100) : 0 }}% of total
                        </span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">EPF, ESI, Professional Tax & TDS</p>
                </div>
            </div>

            <!-- Active in Payroll -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Active in Payroll</span>
                        <div class="stat-icon stat-icon-blue">
                            <i class="ti ti-shield-check"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiActiveCount">{{ $activeComponents }}</h3>
                        <span class="badge badge-soft-info fs-11">Ready to Use</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Active components for CTC generation</p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card filter-card mb-4">
            <div class="card-body p-3">
                <!-- Row 1: Search, Designation, State, City -->
                <div class="row g-2.5 align-items-center mb-2.5">
                    <!-- Search -->
                    <div class="col-lg-3 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="filterSearch" class="form-control border-start-0 ps-0"
                                placeholder="Search by name, code, role, city...">
                        </div>
                    </div>

                    <!-- Designation Filter -->
                    <div class="col-lg-3 col-md-6">
                        <select id="filterDesignation" class="form-select">
                            <option value="all">Role: All Roles &amp; Designations</option>
                            <option value="global">Global Only (All Roles)</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->DesignationID }}">{{ $desig->Designation }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- State Filter -->
                    <div class="col-lg-3 col-md-6">
                        <select id="filterState" class="form-select">
                            <option value="all">State: All States (Nation-Wide)</option>
                            <option value="global">Nation-Wide Only (No State Lock)</option>
                            @foreach($states as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter -->
                    <div class="col-lg-3 col-md-6">
                        <select id="filterCity" class="form-select">
                            <option value="all">City: All Cities</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Type, Calculation Basis, Taxable, Status, Reset -->
                <div class="row g-2.5 align-items-center">
                    <!-- Type Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterType" class="form-select">
                            <option value="all">Type: All Types</option>
                            <option value="earning">Earnings Only</option>
                            <option value="deduction">Deductions Only</option>
                        </select>
                    </div>

                    <!-- Calc Basis Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterCalcType" class="form-select">
                            <option value="all">Calc: All Formulas</option>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>

                    <!-- Taxable Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterTaxable" class="form-select">
                            <option value="all">Tax: All Status</option>
                            <option value="1">Taxable</option>
                            <option value="0">Non-Taxable</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterStatus" class="form-select">
                            <option value="all">Status: All Statuses</option>
                            <option value="Active">Active Only</option>
                            <option value="Inactive">Inactive Only</option>
                        </select>
                    </div>

                    <!-- Reset Filter Button -->
                    <div class="col-lg-2 col-md-12 ms-auto text-end">
                        <button type="button" id="btnResetFilters" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1" title="Reset Filters">
                            <i class="ti ti-rotate-clockwise"></i> Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container Card -->
        <div class="card table-container-card">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-list-details fs-18 text-primary"></i>
                    <h5 class="mb-0 fw-bold text-dark">Component List</h5>
                    <span class="badge bg-light text-secondary border px-2 ms-1" id="tableRecordCount">{{ count($components) }} items</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12">Click any row or action icon to inspect or edit details.</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table comp-table align-middle" id="salaryComponentsTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="40" class="text-center">#</th>
                                <th>Component Name &amp; Code</th>
                                <th>Role (Designation)</th>
                                <th>Region (State / City)</th>
                                <th>Type</th>
                                <th>Calculation Basis</th>
                                <th>Rate / Value</th>
                                <th>Taxable</th>
                                <th>Statutory</th>
                                <th>Status</th>
                                <th width="110" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated via AJAX DataTables -->
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
     OFFCANVAS: ADD / EDIT SALARY COMPONENT DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="salaryComponentDrawer" aria-labelledby="salaryComponentDrawerLabel">
    <!-- Header Hero Banner -->
    <div class="comp-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="drawerHeroPill">
                <i class="ti ti-plus me-1 text-primary"></i> NEW COMPONENT
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="drawerHeroTitle">Add Salary Component</h4>
        <p class="text-white-50 fs-12 mb-0" id="drawerHeroSubtitle">Configure salary head, formula, tax rules & compliance settings.</p>
    </div>

    <!-- Drawer Body Form -->
    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <form id="componentForm">
            @csrf
            <input type="hidden" id="component_id" name="id" value="">
            <input type="hidden" id="form_method" name="_method" value="POST">

            <!-- Validation Alert Box -->
            <div class="alert alert-danger d-none py-2 px-3 fs-12 rounded-3 mb-3" id="drawerAlertBox"></div>

            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="detail-section-title">General Information</div>

                <!-- Component Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Component Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="input_component_name" name="component_name"
                        placeholder="e.g. Basic Salary, Special Allowance, PF..." required>
                </div>

                <!-- Component Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">
                        Component Code <span class="text-danger">*</span>
                        <small class="text-muted fw-normal">(Unique identifier, e.g. BASIC, HRA)</small>
                    </label>
                    <input type="text" class="form-control text-uppercase" id="input_component_code" name="component_code"
                        placeholder="e.g. BASIC01, PF_EMP" style="letter-spacing: 0.5px;" required>
                </div>

                <!-- Component Type (Earning vs Deduction) -->
                <div class="mb-2">
                    <label class="form-label fw-semibold fs-13 mb-1">Component Type <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="type-radio-card w-100" id="labelTypeEarning">
                                <input type="radio" name="type" id="typeEarning" value="earning" class="form-check-input mt-0" checked>
                                <div>
                                    <div class="fw-bold fs-13 text-success">
                                        <i class="ti ti-arrow-up-right me-1"></i> Earning
                                    </div>
                                    <div class="text-muted fs-11">Added to Gross Pay</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="type-radio-card w-100" id="labelTypeDeduction">
                                <input type="radio" name="type" id="typeDeduction" value="deduction" class="form-check-input mt-0">
                                <div>
                                    <div class="fw-bold fs-13 text-danger">
                                        <i class="ti ti-arrow-down-right me-1"></i> Deduction
                                    </div>
                                    <div class="text-muted fs-11">Subtracted from Pay</div>
                                </div>
                            </label>
                        </div>
                    </div>
            <!-- Section: Designation & Regional Location Scope -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="detail-section-title d-flex align-items-center justify-content-between mb-2">
                    <span class="text-primary fw-bold"><i class="ti ti-map-pin me-1"></i> Role &amp; Regional Scope</span>
                    <span class="badge bg-purple-subtle text-purple border fs-10">Role &amp; City Rules</span>
                </div>
                <div class="text-muted fs-11 mb-3">Leave as "All" for standard company-wide components, or restrict to specific Designation, State, or City.</div>

                <!-- Designation Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1 d-flex justify-content-between">
                        <span>Designation / Role Applicability</span>
                        <span class="text-muted fw-normal fs-11">Role specific</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="ti ti-briefcase"></i></span>
                        <select class="form-select" id="input_designation_id" name="designation_id">
                            <option value="">All Designations (Company-Wide / All Roles)</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->DesignationID }}">{{ $desig->Designation }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted fs-11">e.g. Field Supervisor, Senior Developer, Delivery Staff</small>
                </div>

                <!-- State Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1 d-flex justify-content-between">
                        <span>State Applicability</span>
                        <span class="text-muted fw-normal fs-11">State tax &amp; wage slabs</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="ti ti-map"></i></span>
                        <select class="form-select" id="input_state_id" name="state_id">
                            <option value="">All States (Nation-Wide)</option>
                            @foreach($states as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted fs-11">e.g. Maharashtra (PT ₹200), UP (PT ₹0), Delhi</small>
                </div>

                <!-- City Selection -->
                <div class="mb-1">
                    <label class="form-label fw-semibold fs-13 mb-1 d-flex justify-content-between">
                        <span>City / Region Applicability</span>
                        <span class="text-muted fw-normal fs-11">City allowance / HRA tier</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="ti ti-building"></i></span>
                        <select class="form-select" id="input_city_id" name="city_id">
                            <option value="">All Cities / Default</option>
                        </select>
                    </div>
                    <small class="text-muted fs-11">Select a state above to filter specific cities.</small>
                </div>
            </div>

            <!-- Calculation Method & Values -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="detail-section-title">Calculation Rules</div>

                <!-- Calculation Basis -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Calculation Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="input_calculation_type" name="calculation_type" required>
                        <option value="percentage">Percentage (%) of Basic / Gross</option>
                        <option value="fixed">Fixed Monthly Amount (₹)</option>
                    </select>
                </div>

                <!-- Percentage Field (conditional) -->
                <div class="mb-3" id="wrapperPercentage">
                    <label class="form-label fw-semibold fs-13 mb-1">Percentage Rate (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                            id="input_percentage" name="percentage" placeholder="e.g. 40.00" value="0">
                        <span class="input-group-text bg-light text-dark fw-bold">%</span>
                    </div>
                    <small class="text-muted fs-11 mt-1 d-block">Calculated dynamically during payroll run.</small>
                </div>

                <!-- Fixed Amount Field (conditional) -->
                <div class="mb-3 d-none" id="wrapperFixedAmount">
                    <label class="form-label fw-semibold fs-13 mb-1">Fixed Amount (₹ / month)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                        <input type="number" step="0.01" min="0" class="form-control"
                            id="input_fixed_amount" name="fixed_amount" placeholder="e.g. 1600.00" value="0">
                    </div>
                    <small class="text-muted fs-11 mt-1 d-block">Fixed sum assigned per monthly payroll cycle.</small>
                </div>
            </div>

            <!-- Compliance & Status Settings -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="detail-section-title">Tax & Statutory Compliance</div>

                <div class="row g-3">
                    <!-- Taxable -->
                    <div class="col-6">
                        <label class="form-label fw-semibold fs-13 mb-1">Taxability Status</label>
                        <select class="form-select" id="input_is_taxable" name="is_taxable" required>
                            <option value="1">Taxable</option>
                            <option value="0">Tax Exempt</option>
                        </select>
                    </div>

                    <!-- Statutory -->
                    <div class="col-6">
                        <label class="form-label fw-semibold fs-13 mb-1">Statutory Rule?</label>
                        <select class="form-select" id="input_is_statutory" name="is_statutory">
                            <option value="0">No (Custom)</option>
                            <option value="1">Yes (EPF/ESI/PT/TDS)</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Status</label>
                        <select class="form-select" id="input_status" name="status" required>
                            <option value="Active">Active (In use)</option>
                            <option value="Inactive">Inactive (Disabled)</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Description / Notes <small class="text-muted fw-normal">(Optional)</small></label>
                        <textarea class="form-control" id="input_description" name="description" rows="2"
                            placeholder="Add brief details about this component's purpose..."></textarea>
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
        <button type="button" class="btn btn-primary rounded-3 px-4 d-flex align-items-center gap-2" id="btnSaveComponent">
            <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
            <i class="ti ti-device-floppy" id="saveIcon"></i>
            <span id="saveBtnText">Save Component</span>
        </button>
    </div>
</div>

<!-- ==========================================
     OFFCANVAS: COMPONENT DETAILS PREVIEW DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="salaryComponentDetailDrawer" aria-labelledby="salaryComponentDetailDrawerLabel">
    <div class="comp-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="detailPill">
                COMPONENT DETAIL
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="detailName">-</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge badge-code" id="detailCode">-</span>
            <span id="detailTypeBadge">-</span>
            <span id="detailStatusBadge">-</span>
        </div>
    </div>

    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <!-- Metric Highlights -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="detail-metric-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Calculation Method</div>
                    <div class="fw-bold text-dark fs-14" id="detailCalcMethod">-</div>
                </div>
            </div>
            <div class="col-6">
                <div class="detail-metric-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Rate / Value</div>
                    <div class="fw-bold text-primary fs-14" id="detailValue">-</div>
                </div>
            </div>
        <!-- Designation & Location Scope Card -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
            <div class="detail-section-title">Designation &amp; Location Scope</div>
            <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Role / Designation:</span>
                    <span class="fw-semibold fs-13" id="detailDesignation">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">State Scope:</span>
                    <span class="fw-semibold fs-13" id="detailState">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2">
                    <span class="text-muted fs-13">City / Region Scope:</span>
                    <span class="fw-semibold fs-13" id="detailCity">-</span>
                </li>
            </ul>
        </div>

        <!-- Compliance & Rules Card -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
            <div class="detail-section-title">Compliance & Rules Summary</div>
            <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Income Tax Implication:</span>
                    <span class="fw-semibold fs-13" id="detailTaxable">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Statutory Classification:</span>
                    <span class="fw-semibold fs-13" id="detailStatutory">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2">
                    <span class="text-muted fs-13">Current Master Status:</span>
                    <span class="fw-semibold fs-13" id="detailStatus">-</span>
                </li>
            </ul>
        </div>

        <!-- Description Card -->
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="detail-section-title">Description & Purpose</div>
            <p class="text-muted fs-13 mb-0" id="detailDescription">-</p>
        </div>
    </div>

    <div class="offcanvas-footer p-3 bg-white border-top d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="offcanvas">
            Close
        </button>
        <button type="button" class="btn btn-primary rounded-3 px-3 d-flex align-items-center gap-2" id="btnDetailEditTrigger">
            <i class="ti ti-edit"></i> Edit Component
        </button>
    </div>
</div>

<!-- ==========================================
     MODAL: DELETE COMPONENT CONFIRMATION
     ========================================== -->
<div class="modal fade" id="deleteComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle">
                        <i class="ti ti-trash fs-28"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Delete Component?</h5>
                <p class="text-muted fs-13 mb-3">
                    Are you sure you want to remove <span class="fw-bold text-dark" id="deleteComponentName"></span>?
                    This will remove it from the master catalog.
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-3 d-flex align-items-center gap-1" id="btnConfirmDelete">
                        <span class="spinner-border spinner-border-sm d-none" id="deleteSpinner"></span>
                        <i class="ti ti-trash" id="deleteIcon"></i> Delete
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
    let currentTableData = [];
    let activeEditingId = null;
    let deletingComponentId = null;

    // Toast feedback helper
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

    // Initialize DataTable with AJAX loading
    const table = $('#salaryComponentsTable').DataTable({
        processing: true,
        serverSide: false, // Client side filtering & snappy pagination
        ajax: {
            url: "{{ route('masters.payroll.salary-component') }}",
            type: "GET",
            data: function(d) {
                d.type = $('#filterType').val();
                d.calculation_type = $('#filterCalcType').val();
                d.is_taxable = $('#filterTaxable').val();
                d.status = $('#filterStatus').val();
                d.designation_id = $('#filterDesignation').val();
                d.state_id = $('#filterState').val();
                d.city_id = $('#filterCity').val();
                d.search = $('#filterSearch').val();
            },
            dataSrc: function(json) {
                currentTableData = json.data || [];
                $('#tableRecordCount').text(currentTableData.length + ' items');
                updateKPICounters(currentTableData);
                return currentTableData;
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
                        ? `<div class="text-muted fs-11 text-truncate" style="max-width: 220px;" title="${data.description}">${data.description}</div>`
                        : '';
                    return `
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark fs-13">${data.component_name}</span>
                                <span class="badge badge-code">${data.component_code}</span>
                            </div>
                            ${desc}
                        </div>
                    `;
                }
            },
            {
                data: 'designation_badge',
                render: function(data) {
                    return data || '<span class="badge bg-light text-muted border px-2 py-1 rounded-pill fs-11"><i class="ti ti-world me-1"></i>All Roles</span>';
                }
            },
            {
                data: 'location_badge',
                render: function(data) {
                    return data || '<span class="badge bg-light text-muted border px-2 py-1 rounded-pill fs-11"><i class="ti ti-map-pins me-1"></i>All Regions</span>';
                }
            },
            {
                data: 'type',
                render: function(data) {
                    if (data === 'earning') {
                        return `<span class="badge-earning"><i class="ti ti-arrow-up-right"></i> Earning</span>`;
                    } else {
                        return `<span class="badge-deduction"><i class="ti ti-arrow-down-right"></i> Deduction</span>`;
                    }
                }
            },
            {
                data: 'calculation_type',
                render: function(data) {
                    if (data === 'percentage') {
                        return `<span class="badge-calc-perc"><i class="ti ti-percentage me-1"></i>Percentage</span>`;
                    } else {
                        return `<span class="badge-calc-fixed"><i class="ti ti-currency-rupee me-1"></i>Fixed Amount</span>`;
                    }
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.calculation_type === 'percentage') {
                        return `<span class="fw-bold text-dark">${parseFloat(data.percentage).toFixed(2)}%</span> <span class="text-muted fs-11">of Basic</span>`;
                    } else {
                        return `<span class="fw-bold text-dark">₹${parseFloat(data.fixed_amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span> <span class="text-muted fs-11">/mo</span>`;
                    }
                }
            },
            {
                data: 'is_taxable',
                render: function(data) {
                    return data
                        ? `<span class="badge bg-light text-dark border"><i class="ti ti-check text-success me-1"></i>Taxable</span>`
                        : `<span class="badge bg-light text-muted border"><i class="ti ti-x text-secondary me-1"></i>Exempt</span>`;
                }
            },
            {
                data: 'is_statutory',
                render: function(data) {
                    return data
                        ? `<span class="badge badge-soft-purple fs-11"><i class="ti ti-building-bank me-1"></i>Statutory</span>`
                        : `<span class="text-muted fs-12">Standard</span>`;
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
                                <input class="form-check-input status-toggle-input" type="checkbox"
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
                            <button type="button" class="btn-action-icon btn-view" data-id="${data.id}" title="View Details">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-edit" data-id="${data.id}" title="Edit Component">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-delete" data-id="${data.id}" data-name="${data.component_name}" title="Delete Component">
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
            searchPlaceholder: "Search within table...",
            lengthMenu: "Show _MENU_ components",
            info: "Showing _START_ to _END_ of _TOTAL_ components",
            infoEmpty: "No components found",
            emptyTable: "No salary components recorded yet.",
            paginate: {
                previous: '<i class="ti ti-chevron-left"></i>',
                next: '<i class="ti ti-chevron-right"></i>'
            }
        }
    });

    // Update KPI Counters dynamically
    function updateKPICounters(items) {
        const total = items.length;
        const earnings = items.filter(i => i.type === 'earning').length;
        const deductions = items.filter(i => i.type === 'deduction').length;
        const active = items.filter(i => i.status === 'Active').length;

        $('#kpiTotalCount').text(total);
        $('#kpiEarningsCount').text(earnings);
        $('#kpiDeductionsCount').text(deductions);
        $('#kpiActiveCount').text(active);
    }

    // Filter Listeners
    $('#filterType, #filterCalcType, #filterTaxable, #filterStatus, #filterDesignation, #filterCity').on('change', function() {
        table.ajax.reload();
    });

    // State Filter Dynamic City Population
    $('#filterState').on('change', function() {
        const stateId = $(this).val();
        const citySelect = $('#filterCity');
        citySelect.html('<option value="all">City: All Cities</option>');

        if (stateId && stateId !== 'all' && stateId !== 'global') {
            $.ajax({
                url: `{{ url('masters/payroll/salary-component/get-cities') }}/${stateId}`,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.data) {
                        res.data.forEach(function(c) {
                            citySelect.append(`<option value="${c.id}">${c.name}</option>`);
                        });
                    }
                    table.ajax.reload();
                },
                error: function() {
                    table.ajax.reload();
                }
            });
        } else {
            table.ajax.reload();
        }
    });

    let searchTimeout;
    $('#filterSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            table.ajax.reload();
        }, 300);
    });

    $('#btnResetFilters').on('click', function() {
        $('#filterSearch').val('');
        $('#filterDesignation').val('all');
        $('#filterState').val('all');
        $('#filterCity').html('<option value="all">City: All Cities</option>').val('all');
        $('#filterType').val('all');
        $('#filterCalcType').val('all');
        $('#filterTaxable').val('all');
        $('#filterStatus').val('all');
        table.ajax.reload();
    });

    $('#btnRefreshCatalog').on('click', function() {
        table.ajax.reload(null, false);
        showToast('Salary component catalog refreshed!', 'success');
    });

    // Dynamic City Loader for Drawer Form
    $('#input_state_id').on('change', function() {
        loadDrawerCities($(this).val(), null);
    });

    function loadDrawerCities(stateId, selectedCityId = null) {
        const citySelect = $('#input_city_id');
        citySelect.html('<option value="">All Cities / Default</option>');

        if (stateId) {
            citySelect.prop('disabled', true);
            $.ajax({
                url: `{{ url('masters/payroll/salary-component/get-cities') }}/${stateId}`,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    citySelect.prop('disabled', false);
                    if (res.success && res.data) {
                        res.data.forEach(function(c) {
                            const isSel = (selectedCityId && selectedCityId == c.id) ? 'selected' : '';
                            citySelect.append(`<option value="${c.id}" ${isSel}>${c.name}</option>`);
                        });
                    }
                },
                error: function() {
                    citySelect.prop('disabled', false);
                }
            });
        }
    }

    // Calculation Type toggle in Drawer Form
    $('#input_calculation_type').on('change', function() {
        if ($(this).val() === 'percentage') {
            $('#wrapperPercentage').removeClass('d-none');
            $('#wrapperFixedAmount').addClass('d-none');
        } else {
            $('#wrapperPercentage').addClass('d-none');
            $('#wrapperFixedAmount').removeClass('d-none');
        }
    });

    // Type Radio toggle styling
    $('input[name="type"]').on('change', function() {
        updateTypeRadioCards();
    });

    function updateTypeRadioCards() {
        const val = $('input[name="type"]:checked').val();
        if (val === 'earning') {
            $('#labelTypeEarning').addClass('active-earning');
            $('#labelTypeDeduction').removeClass('active-deduction');
        } else {
            $('#labelTypeEarning').removeClass('active-earning');
            $('#labelTypeDeduction').addClass('active-deduction');
        }
    }
    updateTypeRadioCards();

    // Auto capitalize component code input
    $('#input_component_code').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    // Auto-suggest component code when name is typed (if adding new)
    $('#input_component_name').on('input', function() {
        if (!activeEditingId && $('#input_component_code').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 10);
            $('#input_component_code').val(suggested);
        }
    });

    // Open Add Component Drawer
    $('#btnOpenAddDrawer').on('click', function() {
        activeEditingId = null;
        $('#drawerAlertBox').addClass('d-none').empty();
        $('#componentForm')[0].reset();
        $('#component_id').val('');
        $('#form_method').val('POST');

        $('#drawerHeroPill').html('<i class="ti ti-plus me-1 text-primary"></i> NEW COMPONENT');
        $('#drawerHeroTitle').text('Add Salary Component');
        $('#drawerHeroSubtitle').text('Configure salary head, formula, role, state & city scope.');
        $('#saveBtnText').text('Save Component');

        // Defaults
        $('#input_designation_id').val('');
        $('#input_state_id').val('');
        $('#input_city_id').html('<option value="">All Cities / Default</option>').val('');
        $('#typeEarning').prop('checked', true).trigger('change');
        $('#input_calculation_type').val('percentage').trigger('change');
        $('#input_percentage').val('0');
        $('#input_fixed_amount').val('0');
        $('#input_is_taxable').val('1');
        $('#input_is_statutory').val('0');
        $('#input_status').val('Active');

        const drawer = new bootstrap.Offcanvas(document.getElementById('salaryComponentDrawer'));
        drawer.show();
    });

    // Open Edit Component Drawer
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        openEditDrawer(id);
    });

    function openEditDrawer(id) {
        const item = currentTableData.find(c => c.id == id);
        if (!item) return;

        activeEditingId = id;
        $('#drawerAlertBox').addClass('d-none').empty();
        $('#component_id').val(item.id);
        $('#form_method').val('PUT');

        $('#drawerHeroPill').html('<i class="ti ti-edit me-1 text-warning"></i> EDIT MODE');
        $('#drawerHeroTitle').text('Edit ' + item.component_name);
        $('#drawerHeroSubtitle').text('Modify rate calculation, role, state or city parameters.');
        $('#saveBtnText').text('Update Component');

        $('#input_component_name').val(item.component_name);
        $('#input_component_code').val(item.component_code);
        $('#input_designation_id').val(item.designation_id || '');
        $('#input_state_id').val(item.state_id || '');

        if (item.state_id) {
            loadDrawerCities(item.state_id, item.city_id);
        } else {
            $('#input_city_id').html('<option value="">All Cities / Default</option>').val('');
        }

        if (item.type === 'earning') {
            $('#typeEarning').prop('checked', true).trigger('change');
        } else {
            $('#typeDeduction').prop('checked', true).trigger('change');
        }

        $('#input_calculation_type').val(item.calculation_type).trigger('change');
        $('#input_percentage').val(item.percentage);
        $('#input_fixed_amount').val(item.fixed_amount);
        $('#input_is_taxable').val(item.is_taxable ? '1' : '0');
        $('#input_is_statutory').val(item.is_statutory ? '1' : '0');
        $('#input_status').val(item.status);
        $('#input_description').val(item.description && item.description !== 'No description provided.' ? item.description : '');

        // Close preview drawer if open
        const detailDrawerEl = document.getElementById('salaryComponentDetailDrawer');
        const detailInstance = bootstrap.Offcanvas.getInstance(detailDrawerEl);
        if (detailInstance) detailInstance.hide();

        const drawer = new bootstrap.Offcanvas(document.getElementById('salaryComponentDrawer'));
        drawer.show();
    }

    // Save or Update Component Submission
    $('#btnSaveComponent').on('click', function(e) {
        e.preventDefault();
        $('#drawerAlertBox').addClass('d-none').empty();

        const form = document.getElementById('componentForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const isEditing = Boolean(activeEditingId);
        const url = isEditing
            ? `{{ url('masters/payroll/salary-component/update') }}/${activeEditingId}`
            : `{{ route('masters.payroll.salary-component.store') }}`;

        const formData = {
            _token: "{{ csrf_token() }}",
            component_name: $('#input_component_name').val(),
            component_code: $('#input_component_code').val(),
            designation_id: $('#input_designation_id').val() || '',
            state_id: $('#input_state_id').val() || '',
            city_id: $('#input_city_id').val() || '',
            type: $('input[name="type"]:checked').val(),
            calculation_type: $('#input_calculation_type').val(),
            percentage: $('#input_percentage').val(),
            fixed_amount: $('#input_fixed_amount').val(),
            is_taxable: $('#input_is_taxable').val(),
            is_statutory: $('#input_is_statutory').val(),
            status: $('#input_status').val(),
            description: $('#input_description').val(),
        };

        if (isEditing) {
            formData._method = 'PUT';
        }

        // Toggle loading state
        $('#btnSaveComponent').prop('disabled', true);
        $('#saveSpinner').removeClass('d-none');
        $('#saveIcon').addClass('d-none');

        $.ajax({
            url: url,
            type: isEditing ? 'POST' : 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#btnSaveComponent').prop('disabled', false);
                $('#saveSpinner').addClass('d-none');
                $('#saveIcon').removeClass('d-none');

                if (res.success) {
                    showToast(res.message, 'success');
                    const drawerEl = document.getElementById('salaryComponentDrawer');
                    const drawer = bootstrap.Offcanvas.getInstance(drawerEl);
                    if (drawer) drawer.hide();

                    table.ajax.reload(null, false);
                } else {
                    $('#drawerAlertBox').removeClass('d-none').text(res.message || 'Operation failed.');
                }
            },
            error: function(xhr) {
                $('#btnSaveComponent').prop('disabled', false);
                $('#saveSpinner').addClass('d-none');
                $('#saveIcon').removeClass('d-none');

                let errorMsg = 'An error occurred while saving.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#drawerAlertBox').removeClass('d-none').html(errorMsg);
            }
        });
    });

    // View Component Details Drawer
    $(document).on('click', '.btn-view', function() {
        const id = $(this).data('id');
        const item = currentTableData.find(c => c.id == id);
        if (!item) return;

        $('#detailName').text(item.component_name);
        $('#detailCode').text(item.component_code);

        if (item.type === 'earning') {
            $('#detailTypeBadge').html('<span class="badge-earning"><i class="ti ti-arrow-up-right"></i> Earning</span>');
        } else {
            $('#detailTypeBadge').html('<span class="badge-deduction"><i class="ti ti-arrow-down-right"></i> Deduction</span>');
        }

        const statusClass = item.status === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary';
        $('#detailStatusBadge').html(`<span class="badge ${statusClass}">${item.status}</span>`);

        $('#detailCalcMethod').text(item.calculation_type === 'percentage' ? 'Percentage Formula' : 'Fixed Monthly Sum');
        $('#detailValue').text(item.calculation_type === 'percentage' ? `${parseFloat(item.percentage).toFixed(2)}% of Basic` : `₹${parseFloat(item.fixed_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})} / month`);

        // Designation & Location Scope
        $('#detailDesignation').html(item.designation_badge || '<span class="badge bg-light text-muted border">All Roles (Global)</span>');
        $('#detailState').text(item.state_name || 'All States (Nation-Wide)');
        $('#detailCity').text(item.city_name || 'All Cities');

        $('#detailTaxable').html(item.is_taxable
            ? '<span class="badge bg-light text-dark border"><i class="ti ti-check text-success me-1"></i>Taxable Component</span>'
            : '<span class="badge bg-light text-muted border"><i class="ti ti-x text-secondary me-1"></i>Tax Exempt</span>');

        $('#detailStatutory').html(item.is_statutory
            ? '<span class="badge badge-soft-purple fs-11"><i class="ti ti-building-bank me-1"></i>Statutory Rule (PF/ESI/PT)</span>'
            : '<span class="badge bg-light text-muted border">Company Standard Policy</span>');

        $('#detailStatus').text(item.status);
        $('#detailDescription').text(item.description || 'No additional notes documented for this component.');

        // Wire edit trigger
        $('#btnDetailEditTrigger').off('click').on('click', function() {
            openEditDrawer(item.id);
        });

        const drawer = new bootstrap.Offcanvas(document.getElementById('salaryComponentDetailDrawer'));
        drawer.show();
    });

    // Toggle Status switch via AJAX
    $(document).on('change', '.status-toggle-input', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        const originalState = !checkbox.is(':checked');

        $.ajax({
            url: `{{ url('masters/payroll/salary-component/toggle-status') }}/${id}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    table.ajax.reload(null, false);
                } else {
                    checkbox.prop('checked', originalState);
                    showToast(res.message || 'Status update failed.', 'danger');
                }
            },
            error: function() {
                checkbox.prop('checked', originalState);
                showToast('Network error while toggling status.', 'danger');
            }
        });
    });

    // Delete Component Modal
    $(document).on('click', '.btn-delete', function() {
        deletingComponentId = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteComponentName').text(name);

        const modal = new bootstrap.Modal(document.getElementById('deleteComponentModal'));
        modal.show();
    });

    $('#btnConfirmDelete').on('click', function() {
        if (!deletingComponentId) return;

        $('#btnConfirmDelete').prop('disabled', true);
        $('#deleteSpinner').removeClass('d-none');
        $('#deleteIcon').addClass('d-none');

        $.ajax({
            url: `{{ url('masters/payroll/salary-component/delete') }}/${deletingComponentId}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            dataType: 'json',
            success: function(res) {
                $('#btnConfirmDelete').prop('disabled', false);
                $('#deleteSpinner').addClass('d-none');
                $('#deleteIcon').removeClass('d-none');

                const modalEl = document.getElementById('deleteComponentModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (res.success) {
                    showToast(res.message, 'success');
                    table.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Delete operation failed.', 'danger');
                }
                deletingComponentId = null;
            },
            error: function() {
                $('#btnConfirmDelete').prop('disabled', false);
                $('#deleteSpinner').addClass('d-none');
                $('#deleteIcon').removeClass('d-none');
                showToast('Failed to delete component.', 'danger');
                deletingComponentId = null;
            }
        });
    });
});
</script>
@endpush
