@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* Scope styles for payment frequency master page */
    .payfreq-page {
        color: #1e293b;
    }

    /* KPI Summary Cards */
    .payfreq-page .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .payfreq-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .payfreq-page .stat-icon {
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

    .stat-icon-amber {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }

    .stat-icon-blue {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
    }

    /* Filter Card */
    .payfreq-page .filter-card {
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
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    /* Table Container Card */
    .payfreq-page .table-container-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .payfreq-table {
        margin-bottom: 0 !important;
    }

    .payfreq-table th {
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

    .payfreq-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .payfreq-table tbody tr:hover {
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

    .badge-cycles {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 600;
        font-size: 11.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-default-active {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 600;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
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
    .payfreq-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .payfreq-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .payfreq-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

    .payfreq-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .payfreq-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .payfreq-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
    }

    .payfreq-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .payfreq-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Offcanvas Slider Drawers */
    #paymentFrequencyDrawer,
    #paymentFrequencyDetailDrawer {
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

    .payfreq-drawer-hero {
        background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
        position: relative;
        padding: 24px 24px 20px;
        color: #ffffff;
        overflow: hidden;
    }

    .payfreq-drawer-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.35) 0%, rgba(139, 92, 246, 0) 70%);
        pointer-events: none;
    }

    .payfreq-drawer-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -30px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }

    .preset-btn {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 11.5px;
        font-weight: 500;
        color: #334155;
        transition: all 0.15s ease;
    }

    .preset-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
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

<div class="page-wrapper payfreq-page">
    <div class="content container-fluid py-4">

        <!-- Page Header & Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary fw-medium px-2 py-1 rounded">
                        Payroll Master
                    </span>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">Disbursement Frequency</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">Payroll Payment Frequencies</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3" id="btnRefreshFreqCatalog">
                    <i class="ti ti-refresh"></i> Refresh
                </button>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 shadow-sm" id="btnOpenAddFreqDrawer">
                    <i class="ti ti-circle-plus"></i> Add Payment Frequency
                </button>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Schedules -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Payment Schedules</span>
                        <div class="stat-icon stat-icon-purple">
                            <i class="ti ti-calendar-time"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiTotalFreq">{{ $totalFrequencies }}</h3>
                        <span class="badge badge-soft-purple fs-11">Catalog</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Configured payroll cycle intervals</p>
                </div>
            </div>

            <!-- Active Cycles -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Active in Payroll</span>
                        <div class="stat-icon stat-icon-green">
                            <i class="ti ti-circle-check"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiActiveFreq">{{ $activeFrequencies }}</h3>
                        <span class="badge badge-soft-success fs-11">Enforced</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Active calculation intervals for employees</p>
                </div>
            </div>

            <!-- Frequent Schedules -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Frequent Cadences</span>
                        <div class="stat-icon stat-icon-amber">
                            <i class="ti ti-bolt"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiFrequentSchedules">{{ $multiCycleCount }}</h3>
                        <span class="badge badge-soft-amber fs-11">&gt; 12 Cycles/Yr</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Semi-Monthly, Bi-Weekly, Weekly & Daily</p>
                </div>
            </div>

            <!-- Default Frequency -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Primary Cadence</span>
                        <div class="stat-icon stat-icon-blue">
                            <i class="ti ti-star-filled"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h5 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 170px;" id="kpiDefaultFreqName">
                            {{ $defaultFrequency ? $defaultFrequency->frequency_name : 'Monthly' }}
                        </h5>
                        <span class="badge badge-soft-blue fs-11">Default</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Standard cadence for company staff</p>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar Card -->
        <div class="card filter-card mb-4">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <!-- Search input -->
                    <div class="col-lg-5 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="filterFreqSearch" class="form-control border-start-0 ps-0"
                                placeholder="Search by frequency name, code or payday rule...">
                        </div>
                    </div>

                    <!-- Cadence Filter -->
                    <div class="col-lg-3 col-md-3 col-6">
                        <select id="filterFreqCadence" class="form-select">
                            <option value="all">All Cadence Types</option>
                            <option value="monthly">Monthly (12 Cycles)</option>
                            <option value="semi_monthly">Semi-Monthly (24 Cycles)</option>
                            <option value="bi_weekly">Bi-Weekly (26 Cycles)</option>
                            <option value="weekly">Weekly (52 Cycles)</option>
                            <option value="daily">Daily Wage (365 Cycles)</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-3 col-md-3 col-6">
                        <select id="filterFreqStatus" class="form-select">
                            <option value="all">All Statuses</option>
                            <option value="Active">Active Only</option>
                            <option value="Inactive">Inactive Only</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-lg-1 col-md-12 col-12 text-end">
                        <button type="button" id="btnResetFreqFilters" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1" title="Reset Filters">
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
                    <i class="ti ti-calendar-event fs-18 text-primary"></i>
                    <h5 class="mb-0 fw-bold text-dark">Payment Frequencies Master List</h5>
                    <span class="badge bg-light text-secondary border px-2 ms-1" id="freqTableCount">{{ count($frequencies) }} frequencies</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12">Click action buttons to edit, inspect or change the default schedule.</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table payfreq-table align-middle" id="paymentFrequenciesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Frequency Name & Code</th>
                                <th>Cycles / Year</th>
                                <th>Days / Cycle</th>
                                <th>Scheduled Pay Day Rule</th>
                                <th>Default Schedule</th>
                                <th>Status</th>
                                <th width="140" class="text-end">Actions</th>
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
     OFFCANVAS: ADD / EDIT PAYMENT FREQUENCY DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="paymentFrequencyDrawer" aria-labelledby="paymentFrequencyDrawerLabel">
    <!-- Header Hero Banner -->
    <div class="payfreq-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="freqDrawerPill">
                <i class="ti ti-plus me-1 text-primary"></i> NEW FREQUENCY
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="freqDrawerTitle">Add Payment Frequency</h4>
        <p class="text-white-50 fs-12 mb-0" id="freqDrawerSubtitle">Configure payroll cycles per year, cycle days, and payday rules.</p>
    </div>

    <!-- Drawer Form Body -->
    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <form id="paymentFrequencyForm">
            @csrf
            <input type="hidden" id="freq_id" name="id" value="">
            <input type="hidden" id="freq_form_method" name="_method" value="POST">

            <!-- Validation Error Alert -->
            <div class="alert alert-danger d-none py-2 px-3 fs-12 rounded-3 mb-3" id="freqAlertBox"></div>

            <!-- Identity Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">1. Frequency Cadence</div>

                <!-- Quick Presets -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-12 mb-1 text-muted">Quick Schedule Presets:</label>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="preset-btn btn-preset" data-name="Monthly (Standard Corporate)" data-code="MONTHLY" data-cycles="12" data-days="30" data-payday="Last working day of the month">Monthly</button>
                        <button type="button" class="preset-btn btn-preset" data-name="Semi-Monthly (Bi-Monthly)" data-code="SEMI_MONTHLY" data-cycles="24" data-days="15" data-payday="15th and last day of month">Semi-Monthly</button>
                        <button type="button" class="preset-btn btn-preset" data-name="Bi-Weekly (Fortnightly)" data-code="BI_WEEKLY" data-cycles="26" data-days="14" data-payday="Every alternate Friday">Bi-Weekly</button>
                        <button type="button" class="preset-btn btn-preset" data-name="Weekly Schedule" data-code="WEEKLY" data-cycles="52" data-days="7" data-payday="Every Saturday">Weekly</button>
                        <button type="button" class="preset-btn btn-preset" data-name="Daily Wage" data-code="DAILY" data-cycles="365" data-days="1" data-payday="Daily upon punch regularization">Daily</button>
                    </div>
                </div>

                <!-- Frequency Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Frequency Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="input_freq_name" name="frequency_name"
                        placeholder="e.g. Monthly, Bi-Weekly, Weekly" required>
                </div>

                <!-- Frequency Code -->
                <div class="mb-2">
                    <label class="form-label fw-semibold fs-13 mb-1">
                        Frequency Code <span class="text-danger">*</span>
                        <small class="text-muted fw-normal">(Unique identifier, e.g. MONTHLY)</small>
                    </label>
                    <input type="text" class="form-control text-uppercase" id="input_freq_code" name="frequency_code"
                        placeholder="e.g. MONTHLY, BI_WEEKLY, WEEKLY" style="letter-spacing: 0.5px;" required>
                </div>
            </div>

            <!-- Cycle Metrics Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">2. Cycle Metrics & Payday Rules</div>

                <div class="row g-3 mb-3">
                    <!-- Cycles Per Year -->
                    <div class="col-6">
                        <label class="form-label fw-semibold fs-13 mb-1">Cycles / Year <span class="text-danger">*</span></label>
                        <input type="number" min="1" max="365" class="form-control"
                            id="input_cycles_per_year" name="cycles_per_year" value="12" required>
                        <small class="text-muted fs-11">Total payroll runs in a year.</small>
                    </div>

                    <!-- Days Per Cycle -->
                    <div class="col-6">
                        <label class="form-label fw-semibold fs-13 mb-1">Days / Cycle <span class="text-danger">*</span></label>
                        <input type="number" min="1" max="365" class="form-control"
                            id="input_days_per_cycle" name="days_per_cycle" value="30" required>
                        <small class="text-muted fs-11">Duration of 1 pay period.</small>
                    </div>
                </div>

                <!-- Scheduled Pay Day Rule -->
                <div class="mb-2">
                    <label class="form-label fw-semibold fs-13 mb-1">Scheduled Pay Day Rule</label>
                    <input type="text" class="form-control" id="input_pay_day_rule" name="pay_day_rule"
                        placeholder="e.g. Last working day of the month, Every alternate Friday">
                    <small class="text-muted fs-11 mt-1 d-block">Specific timing when salary is computed and disbursed.</small>
                </div>
            </div>

            <!-- Organization & Status Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">3. Status & Organization Policy</div>

                <div class="row g-3">
                    <!-- Is Default Switch -->
                    <div class="col-12">
                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input" type="checkbox" id="input_freq_is_default" name="is_default" value="1">
                            <label class="form-check-label fw-semibold fs-13" for="input_freq_is_default">
                                Set as Organization Default Payroll Cadence
                            </label>
                        </div>
                        <small class="text-muted fs-11 ms-5 d-block">Assigned by default to newly configured employee salaries.</small>
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="input_freq_status" name="status" required>
                            <option value="Active">Active (In use)</option>
                            <option value="Inactive">Inactive (Suspended)</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Operational Description / Attendance Cutoff Notes</label>
                        <textarea class="form-control" id="input_freq_description" name="description" rows="2"
                            placeholder="e.g. Attendance cutoff is 25th of month for monthly payroll run..."></textarea>
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
        <button type="button" class="btn btn-primary rounded-3 px-4 d-flex align-items-center gap-2" id="btnSavePaymentFrequency">
            <span class="spinner-border spinner-border-sm d-none" id="saveFreqSpinner"></span>
            <i class="ti ti-device-floppy" id="saveFreqIcon"></i>
            <span id="saveFreqBtnText">Save Payment Frequency</span>
        </button>
    </div>
</div>

<!-- ==========================================
     OFFCANVAS: DETAIL PREVIEW DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="paymentFrequencyDetailDrawer" aria-labelledby="paymentFrequencyDetailDrawerLabel">
    <div class="payfreq-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="detailFreqPill">
                PAYMENT FREQUENCY DETAIL
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="detailFreqName">-</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge badge-code" id="detailFreqCode">-</span>
            <span id="detailFreqCyclesBadge">-</span>
            <span id="detailFreqDefaultBadge">-</span>
        </div>
    </div>

    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <!-- Highlights -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="metric-preview-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Annual Runs</div>
                    <div class="fw-bold text-primary fs-15" id="detailFreqAnnualRuns">-</div>
                </div>
            </div>
            <div class="col-6">
                <div class="metric-preview-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Cycle Duration</div>
                    <div class="fw-bold text-dark fs-15" id="detailFreqDaysCycle">-</div>
                </div>
            </div>
        </div>

        <!-- Specifications -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
            <div class="section-label">Payroll Schedule Specifications</div>
            <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Scheduled Pay Day:</span>
                    <span class="fw-semibold fs-13" id="detailFreqPayday">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Primary Default Cadence:</span>
                    <span class="fw-semibold fs-13" id="detailFreqIsDefault">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2">
                    <span class="text-muted fs-13">Cadence Status:</span>
                    <span class="fw-semibold fs-13" id="detailFreqStatus">-</span>
                </li>
            </ul>
        </div>

        <!-- Description -->
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="section-label">Operational Notes & Cutoff Window</div>
            <p class="text-muted fs-13 mb-0" id="detailFreqDescription">-</p>
        </div>
    </div>

    <div class="offcanvas-footer p-3 bg-white border-top d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="offcanvas">
            Close
        </button>
        <button type="button" class="btn btn-primary rounded-3 px-3 d-flex align-items-center gap-2" id="btnDetailFreqEditTrigger">
            <i class="ti ti-edit"></i> Edit Frequency
        </button>
    </div>
</div>

<!-- ==========================================
     MODAL: DELETE FREQUENCY CONFIRMATION
     ========================================== -->
<div class="modal fade" id="deleteFreqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle">
                        <i class="ti ti-trash fs-28"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Delete Payment Frequency?</h5>
                <p class="text-muted fs-13 mb-3">
                    Are you sure you want to delete <span class="fw-bold text-dark" id="deleteFreqName"></span>?
                    This frequency schedule will be removed from future salary setups.
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-3 d-flex align-items-center gap-1" id="btnConfirmFreqDelete">
                        <span class="spinner-border spinner-border-sm d-none" id="deleteFreqSpinner"></span>
                        <i class="ti ti-trash" id="deleteFreqIcon"></i> Delete
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
    let currentFreqData = [];
    let activeFreqEditingId = null;
    let deletingFreqId = null;

    // Toast helper
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

    // Initialize DataTable
    const freqTable = $('#paymentFrequenciesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('masters.payroll.payment-frequency') }}",
            type: "GET",
            data: function(d) {
                d.cycle_type = $('#filterFreqCadence').val();
                d.status = $('#filterFreqStatus').val();
                d.search = $('#filterFreqSearch').val();
            },
            dataSrc: function(json) {
                currentFreqData = json.data || [];
                $('#freqTableCount').text(currentFreqData.length + ' frequencies');
                updateFreqKPIs(currentFreqData);
                return currentFreqData;
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
                    const desc = data.description && data.description !== 'Standard payroll cadence.'
                        ? `<div class="text-muted fs-11 text-truncate" style="max-width: 260px;" title="${data.description}">${data.description}</div>`
                        : '';
                    return `
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark fs-13">${data.frequency_name}</span>
                                <span class="badge badge-code">${data.frequency_code}</span>
                            </div>
                            ${desc}
                        </div>
                    `;
                }
            },
            {
                data: 'cycles_per_year',
                render: function(data) {
                    return `<span class="badge-cycles"><i class="ti ti-repeat me-1"></i>${data} Cycles / Yr</span>`;
                }
            },
            {
                data: 'days_per_cycle',
                render: function(data) {
                    return `<span class="fw-bold text-dark fs-13">${data}</span> <span class="text-muted fs-11">Days</span>`;
                }
            },
            {
                data: 'pay_day_rule',
                render: function(data) {
                    return `
                        <div class="d-flex align-items-center gap-1 text-muted fs-12">
                            <i class="ti ti-calendar-event text-primary"></i> ${data}
                        </div>
                    `;
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.is_default) {
                        return `<span class="badge-default-active"><i class="ti ti-star-filled text-warning"></i> Default Schedule</span>`;
                    } else {
                        return `
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11 btn-freq-make-default"
                                    data-id="${data.id}" title="Set as organization default">
                                Set as Default
                            </button>
                        `;
                    }
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
                                <input class="form-check-input freq-status-toggle" type="checkbox"
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
                    const deleteBtn = data.is_default
                        ? `<button type="button" class="btn-action-icon disabled opacity-50" title="Cannot delete default schedule" disabled><i class="ti ti-trash"></i></button>`
                        : `<button type="button" class="btn-action-icon btn-delete btn-freq-delete" data-id="${data.id}" data-name="${data.frequency_name}" title="Delete Frequency"><i class="ti ti-trash"></i></button>`;

                    return `
                        <div class="d-inline-flex align-items-center gap-1">
                            <button type="button" class="btn-action-icon btn-view btn-freq-view" data-id="${data.id}" title="View Details">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-edit btn-freq-edit" data-id="${data.id}" title="Edit Frequency">
                                <i class="ti ti-edit"></i>
                            </button>
                            ${deleteBtn}
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
            searchPlaceholder: "Search frequencies...",
            lengthMenu: "Show _MENU_ schedules",
            info: "Showing _START_ to _END_ of _TOTAL_ frequencies",
            infoEmpty: "No frequencies found",
            emptyTable: "No payment frequencies registered yet.",
            paginate: {
                previous: '<i class="ti ti-chevron-left"></i>',
                next: '<i class="ti ti-chevron-right"></i>'
            }
        }
    });

    // KPI Counters updater
    function updateFreqKPIs(items) {
        const total = items.length;
        const active = items.filter(i => i.status === 'Active').length;
        const frequent = items.filter(i => i.cycles_per_year >= 24).length;
        const def = items.find(i => i.is_default);

        $('#kpiTotalFreq').text(total);
        $('#kpiActiveFreq').text(active);
        $('#kpiFrequentSchedules').text(frequent);
        if (def) {
            $('#kpiDefaultFreqName').text(def.frequency_name);
        }
    }

    // Filter listeners
    $('#filterFreqCadence, #filterFreqStatus').on('change', function() {
        freqTable.ajax.reload();
    });

    let searchTimeout;
    $('#filterFreqSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            freqTable.ajax.reload();
        }, 300);
    });

    $('#btnResetFreqFilters').on('click', function() {
        $('#filterFreqSearch').val('');
        $('#filterFreqCadence').val('all');
        $('#filterFreqStatus').val('all');
        freqTable.ajax.reload();
    });

    $('#btnRefreshFreqCatalog').on('click', function() {
        freqTable.ajax.reload(null, false);
        showToast('Payment frequency catalog refreshed!', 'success');
    });

    // Quick Preset Buttons handler
    $(document).on('click', '.btn-preset', function() {
        $('#input_freq_name').val($(this).data('name'));
        $('#input_freq_code').val($(this).data('code'));
        $('#input_cycles_per_year').val($(this).data('cycles'));
        $('#input_days_per_cycle').val($(this).data('days'));
        $('#input_pay_day_rule').val($(this).data('payday'));
    });

    // Auto capitalize code
    $('#input_freq_code').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    // Auto suggest code from name if creating
    $('#input_freq_name').on('input', function() {
        if (!activeFreqEditingId && $('#input_freq_code').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 15);
            $('#input_freq_code').val(suggested);
        }
    });

    // Open Add Frequency Drawer
    $('#btnOpenAddFreqDrawer').on('click', function() {
        activeFreqEditingId = null;
        $('#freqAlertBox').addClass('d-none').empty();
        $('#paymentFrequencyForm')[0].reset();
        $('#freq_id').val('');
        $('#freq_form_method').val('POST');

        $('#freqDrawerPill').html('<i class="ti ti-plus me-1 text-primary"></i> NEW FREQUENCY');
        $('#freqDrawerTitle').text('Add Payment Frequency');
        $('#freqDrawerSubtitle').text('Configure payroll cycles per year, cycle days, and payday rules.');
        $('#saveFreqBtnText').text('Save Payment Frequency');

        $('#input_cycles_per_year').val('12');
        $('#input_days_per_cycle').val('30');
        $('#input_pay_day_rule').val('Last working day of the month');
        $('#input_freq_is_default').prop('checked', false);
        $('#input_freq_status').val('Active');

        const drawer = new bootstrap.Offcanvas(document.getElementById('paymentFrequencyDrawer'));
        drawer.show();
    });

    // Open Edit Frequency Drawer
    $(document).on('click', '.btn-freq-edit', function() {
        const id = $(this).data('id');
        openEditFreqDrawer(id);
    });

    function openEditFreqDrawer(id) {
        const item = currentFreqData.find(f => f.id == id);
        if (!item) return;

        activeFreqEditingId = id;
        $('#freqAlertBox').addClass('d-none').empty();
        $('#freq_id').val(item.id);
        $('#freq_form_method').val('PUT');

        $('#freqDrawerPill').html('<i class="ti ti-edit me-1 text-warning"></i> EDIT FREQUENCY');
        $('#freqDrawerTitle').text('Edit ' + item.frequency_name);
        $('#freqDrawerSubtitle').text('Modify annual cycle count, interval days, or schedule rules.');
        $('#saveFreqBtnText').text('Update Payment Frequency');

        $('#input_freq_name').val(item.frequency_name);
        $('#input_freq_code').val(item.frequency_code);
        $('#input_cycles_per_year').val(item.cycles_per_year);
        $('#input_days_per_cycle').val(item.days_per_cycle);
        $('#input_pay_day_rule').val(item.pay_day_rule && item.pay_day_rule !== 'End of cycle' ? item.pay_day_rule : '');
        $('#input_freq_is_default').prop('checked', item.is_default);
        $('#input_freq_status').val(item.status);
        $('#input_freq_description').val(item.description && item.description !== 'Standard payroll cadence.' ? item.description : '');

        // Close detail preview if open
        const detailDrawerEl = document.getElementById('paymentFrequencyDetailDrawer');
        const detailInstance = bootstrap.Offcanvas.getInstance(detailDrawerEl);
        if (detailInstance) detailInstance.hide();

        const drawer = new bootstrap.Offcanvas(document.getElementById('paymentFrequencyDrawer'));
        drawer.show();
    }

    // Save or Update Frequency Submission
    $('#btnSavePaymentFrequency').on('click', function(e) {
        e.preventDefault();
        $('#freqAlertBox').addClass('d-none').empty();

        const form = document.getElementById('paymentFrequencyForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const isEditing = Boolean(activeFreqEditingId);
        const url = isEditing
            ? `{{ url('masters/payroll/frequency/update') }}/${activeFreqEditingId}`
            : `{{ route('masters.payroll.payment-frequency.store') }}`;

        const formData = {
            _token: "{{ csrf_token() }}",
            frequency_name: $('#input_freq_name').val(),
            frequency_code: $('#input_freq_code').val(),
            cycles_per_year: $('#input_cycles_per_year').val(),
            days_per_cycle: $('#input_days_per_cycle').val(),
            pay_day_rule: $('#input_pay_day_rule').val(),
            is_default: $('#input_freq_is_default').is(':checked') ? 1 : 0,
            status: $('#input_freq_status').val(),
            description: $('#input_freq_description').val(),
        };

        if (isEditing) {
            formData._method = 'PUT';
        }

        $('#btnSavePaymentFrequency').prop('disabled', true);
        $('#saveFreqSpinner').removeClass('d-none');
        $('#saveFreqIcon').addClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#btnSavePaymentFrequency').prop('disabled', false);
                $('#saveFreqSpinner').addClass('d-none');
                $('#saveFreqIcon').removeClass('d-none');

                if (res.success) {
                    showToast(res.message, 'success');
                    const drawerEl = document.getElementById('paymentFrequencyDrawer');
                    const drawer = bootstrap.Offcanvas.getInstance(drawerEl);
                    if (drawer) drawer.hide();

                    freqTable.ajax.reload(null, false);
                } else {
                    $('#freqAlertBox').removeClass('d-none').text(res.message || 'Operation failed.');
                }
            },
            error: function(xhr) {
                $('#btnSavePaymentFrequency').prop('disabled', false);
                $('#saveFreqSpinner').addClass('d-none');
                $('#saveFreqIcon').removeClass('d-none');

                let errorMsg = 'An error occurred while saving.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#freqAlertBox').removeClass('d-none').html(errorMsg);
            }
        });
    });

    // View Details Drawer
    $(document).on('click', '.btn-freq-view', function() {
        const id = $(this).data('id');
        const item = currentFreqData.find(f => f.id == id);
        if (!item) return;

        $('#detailFreqName').text(item.frequency_name);
        $('#detailFreqCode').text(item.frequency_code);

        $('#detailFreqCyclesBadge').html(`<span class="badge-cycles"><i class="ti ti-repeat me-1"></i>${item.cycles_per_year} Cycles/Yr</span>`);

        if (item.is_default) {
            $('#detailFreqDefaultBadge').html('<span class="badge-default-active"><i class="ti ti-star-filled text-warning"></i> Primary Default</span>');
            $('#detailFreqIsDefault').html('<span class="badge bg-primary text-white">Yes (Active Default)</span>');
        } else {
            $('#detailFreqDefaultBadge').html('');
            $('#detailFreqIsDefault').text('No');
        }

        $('#detailFreqAnnualRuns').text(item.cycles_per_year + ' Cycles / Year');
        $('#detailFreqDaysCycle').text(item.days_per_cycle + ' Days / Period');
        $('#detailFreqPayday').text(item.pay_day_rule);
        $('#detailFreqStatus').text(item.status);
        $('#detailFreqDescription').text(item.description || 'Standard payroll calculation frequency.');

        $('#btnDetailFreqEditTrigger').off('click').on('click', function() {
            openEditFreqDrawer(item.id);
        });

        const drawer = new bootstrap.Offcanvas(document.getElementById('paymentFrequencyDetailDrawer'));
        drawer.show();
    });

    // Make Default Button
    $(document).on('click', '.btn-freq-make-default', function() {
        const id = $(this).data('id');

        $.ajax({
            url: `{{ url('masters/payroll/frequency/set-default') }}/${id}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    freqTable.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Operation failed.', 'danger');
                }
            },
            error: function() {
                showToast('Failed to set default frequency.', 'danger');
            }
        });
    });

    // Status Toggle
    $(document).on('change', '.freq-status-toggle', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        const originalState = !checkbox.is(':checked');

        $.ajax({
            url: `{{ url('masters/payroll/frequency/toggle-status') }}/${id}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    freqTable.ajax.reload(null, false);
                } else {
                    checkbox.prop('checked', originalState);
                    showToast(res.message || 'Status toggle failed.', 'danger');
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', originalState);
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Network error while toggling status.';
                showToast(msg, 'danger');
            }
        });
    });

    // Delete Modal
    $(document).on('click', '.btn-freq-delete', function() {
        deletingFreqId = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteFreqName').text(name);

        const modal = new bootstrap.Modal(document.getElementById('deleteFreqModal'));
        modal.show();
    });

    $('#btnConfirmFreqDelete').on('click', function() {
        if (!deletingFreqId) return;

        $('#btnConfirmFreqDelete').prop('disabled', true);
        $('#deleteFreqSpinner').removeClass('d-none');
        $('#deleteFreqIcon').addClass('d-none');

        $.ajax({
            url: `{{ url('masters/payroll/frequency/delete') }}/${deletingFreqId}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            dataType: 'json',
            success: function(res) {
                $('#btnConfirmFreqDelete').prop('disabled', false);
                $('#deleteFreqSpinner').addClass('d-none');
                $('#deleteFreqIcon').removeClass('d-none');

                const modalEl = document.getElementById('deleteFreqModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (res.success) {
                    showToast(res.message, 'success');
                    freqTable.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Delete operation failed.', 'danger');
                }
                deletingFreqId = null;
            },
            error: function(xhr) {
                $('#btnConfirmFreqDelete').prop('disabled', false);
                $('#deleteFreqSpinner').addClass('d-none');
                $('#deleteFreqIcon').removeClass('d-none');

                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Failed to delete payment frequency.';
                showToast(msg, 'danger');
                deletingFreqId = null;
            }
        });
    });
});
</script>
@endpush
