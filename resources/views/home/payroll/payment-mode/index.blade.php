@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* Scope styles for payment mode master page */
    .paymode-page {
        color: #1e293b;
    }

    /* KPI Summary Cards */
    .paymode-page .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .paymode-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .paymode-page .stat-icon {
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

    .stat-icon-cyan {
        background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
        color: #0891b2;
    }

    .stat-icon-amber {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }

    .stat-icon-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    /* Filter Card */
    .paymode-page .filter-card {
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
    .paymode-page .table-container-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .paymode-table {
        margin-bottom: 0 !important;
    }

    .paymode-table th {
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

    .paymode-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .paymode-table tbody tr:hover {
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

    .badge-channel-electronic {
        background: #ecfeff;
        color: #0e7490;
        border: 1px solid #a5f3fc;
        font-weight: 600;
        font-size: 11.5px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-channel-physical {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
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

    .badge-soft-cyan {
        background-color: #cffafe;
        color: #0891b2;
        border: 1px solid #a5f3fc;
    }

    .badge-soft-amber {
        background-color: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
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

    .btn-action-icon.btn-default-star:hover {
        background: #fef9c3;
        color: #ca8a04;
        border-color: #fde047;
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
    .paymode-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .paymode-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .paymode-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

    .paymode-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .paymode-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .paymode-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
    }

    .paymode-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .paymode-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Offcanvas Slider Drawers */
    #paymentModeDrawer,
    #paymentModeDetailDrawer {
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

    .paymode-drawer-hero {
        background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
        position: relative;
        padding: 24px 24px 20px;
        color: #ffffff;
        overflow: hidden;
    }

    .paymode-drawer-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.35) 0%, rgba(14, 165, 233, 0) 70%);
        pointer-events: none;
    }

    .paymode-drawer-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: -30px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0) 70%);
        pointer-events: none;
    }

    .channel-select-card {
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

    .channel-select-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .channel-select-card.active-electronic {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .channel-select-card.active-physical {
        border-color: #d97706;
        background: #fffbeb;
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

<div class="page-wrapper paymode-page">
    <div class="content container-fluid py-4">

        <!-- Page Header & Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary fw-medium px-2 py-1 rounded">
                        Payroll Master
                    </span>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">Disbursement Configuration</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">Salary Payment Modes</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3" id="btnRefreshModeCatalog">
                    <i class="ti ti-refresh"></i> Refresh
                </button>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 shadow-sm" id="btnOpenAddModeDrawer">
                    <i class="ti ti-circle-plus"></i> Add Payment Mode
                </button>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Modes -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Payment Modes</span>
                        <div class="stat-icon stat-icon-purple">
                            <i class="ti ti-credit-card"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiTotalModes">{{ $totalModes }}</h3>
                        <span class="badge badge-soft-purple fs-11">Catalog</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Configured salary disbursement methods</p>
                </div>
            </div>

            <!-- Electronic / Digital Modes -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Digital / Electronic</span>
                        <div class="stat-icon stat-icon-cyan">
                            <i class="ti ti-device-mobile"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiElectronicModes">{{ $electronicModes }}</h3>
                        <span class="badge badge-soft-cyan fs-11">Automated</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">NEFT, RTGS, IMPS & UPI Channels</p>
                </div>
            </div>

            <!-- Physical Modes -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Physical Channels</span>
                        <div class="stat-icon stat-icon-amber">
                            <i class="ti ti-cash"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="mb-0 fw-bold text-dark" id="kpiPhysicalModes">{{ $physicalModes }}</h3>
                        <span class="badge badge-soft-amber fs-11">Manual Payout</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Company Cheques & Cash Disbursements</p>
                </div>
            </div>

            <!-- Primary Default Channel -->
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-12 text-uppercase">Default Channel</span>
                        <div class="stat-icon stat-icon-green">
                            <i class="ti ti-circle-check"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h5 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 170px;" id="kpiDefaultModeName">
                            {{ $defaultMode ? $defaultMode->mode_name : 'Bank Transfer' }}
                        </h5>
                        <span class="badge badge-soft-success fs-11">Primary</span>
                    </div>
                    <p class="text-muted fs-12 mb-0 mt-2">Default payment method for new employees</p>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar Card -->
        <div class="card filter-card mb-4">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <!-- Search input -->
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="filterModeSearch" class="form-control border-start-0 ps-0"
                                placeholder="Search by payment mode name or code...">
                        </div>
                    </div>

                    <!-- Channel Type Filter -->
                    <div class="col-lg-3 col-md-3 col-6">
                        <select id="filterModeChannel" class="form-select">
                            <option value="all">All Channels</option>
                            <option value="electronic">Electronic / Digital</option>
                            <option value="physical">Physical (Cheque / Cash)</option>
                        </select>
                    </div>

                    <!-- Bank Requirement Filter -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filterModeBankReq" class="form-select">
                            <option value="all">All Requirements</option>
                            <option value="1">Requires Bank Details</option>
                            <option value="0">No Bank Required</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <select id="filterModeStatus" class="form-select">
                            <option value="all">All Statuses</option>
                            <option value="Active">Active Only</option>
                            <option value="Inactive">Inactive Only</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-lg-1 col-md-4 col-6 text-end">
                        <button type="button" id="btnResetModeFilters" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1" title="Reset Filters">
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
                    <i class="ti ti-wallet fs-18 text-primary"></i>
                    <h5 class="mb-0 fw-bold text-dark">Payment Modes Master List</h5>
                    <span class="badge bg-light text-secondary border px-2 ms-1" id="modeTableCount">{{ count($modes) }} modes</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12">Click action buttons to edit, inspect or change the default channel.</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table paymode-table align-middle" id="paymentModesTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Mode Name & Code</th>
                                <th>Channel Type</th>
                                <th>Bank Account Required</th>
                                <th>Processing Fee</th>
                                <th>Default Status</th>
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
     OFFCANVAS: ADD / EDIT PAYMENT MODE DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="paymentModeDrawer" aria-labelledby="paymentModeDrawerLabel">
    <!-- Header Hero Banner -->
    <div class="paymode-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="modeDrawerPill">
                <i class="ti ti-plus me-1 text-primary"></i> NEW PAYMENT MODE
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="modeDrawerTitle">Add Payment Mode</h4>
        <p class="text-white-50 fs-12 mb-0" id="modeDrawerSubtitle">Configure disbursement method, banking validations & fees.</p>
    </div>

    <!-- Drawer Form Body -->
    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <form id="paymentModeForm">
            @csrf
            <input type="hidden" id="mode_id" name="id" value="">
            <input type="hidden" id="mode_form_method" name="_method" value="POST">

            <!-- Validation Error Alert -->
            <div class="alert alert-danger d-none py-2 px-3 fs-12 rounded-3 mb-3" id="modeAlertBox"></div>

            <!-- Identity Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">1. Channel Identification</div>

                <!-- Mode Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Payment Mode Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="input_mode_name" name="mode_name"
                        placeholder="e.g. Bank Transfer, Cheque, Cash, UPI..." required>
                </div>

                <!-- Mode Code -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">
                        Mode Code <span class="text-danger">*</span>
                        <small class="text-muted fw-normal">(Unique identifier, e.g. BANK_TRANSFER)</small>
                    </label>
                    <input type="text" class="form-control text-uppercase" id="input_mode_code" name="mode_code"
                        placeholder="e.g. BANK_TRANSFER, CHEQUE, CASH, UPI" style="letter-spacing: 0.5px;" required>
                </div>

                <!-- Channel Type Radio -->
                <div class="mb-2">
                    <label class="form-label fw-semibold fs-13 mb-1">Channel Medium <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="channel-select-card w-100" id="labelChannelElectronic">
                                <input type="radio" name="channel_type" id="channelElectronic" value="electronic" class="form-check-input mt-0" checked>
                                <div>
                                    <div class="fw-bold fs-13 text-primary">
                                        <i class="ti ti-wifi me-1"></i> Electronic
                                    </div>
                                    <div class="text-muted fs-11">Digital / ACH / UPI</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="channel-select-card w-100" id="labelChannelPhysical">
                                <input type="radio" name="channel_type" id="channelPhysical" value="physical" class="form-check-input mt-0">
                                <div>
                                    <div class="fw-bold fs-13 text-warning">
                                        <i class="ti ti-cash me-1"></i> Physical
                                    </div>
                                    <div class="text-muted fs-11">Cheque / Cash</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parameters Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">2. Banking & Fee Rules</div>

                <!-- Requires Bank Details -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Mandatory Bank Details?</label>
                    <select class="form-select" id="input_requires_bank_details" name="requires_bank_details">
                        <option value="1">Yes (Requires Account No & IFSC)</option>
                        <option value="0">No (Cash/Cheque/Unbanked)</option>
                    </select>
                    <small class="text-muted fs-11 mt-1 d-block">Prevents payroll generation if employee bank data is missing.</small>
                </div>

                <!-- Processing Fee -->
                <div class="mb-3">
                    <label class="form-label fw-semibold fs-13 mb-1">Processing / Gateway Fee (₹)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                        <input type="number" step="0.01" min="0" class="form-control"
                            id="input_processing_fee" name="processing_fee" placeholder="0.00" value="0">
                    </div>
                    <small class="text-muted fs-11 mt-1 d-block">Cost per transaction incurred by company or deducted.</small>
                </div>

                <!-- Is Default Checkbox -->
                <div class="mb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="input_is_default" name="is_default" value="1">
                        <label class="form-check-label fw-semibold fs-13" for="input_is_default">
                            Set as Organization Primary Default Mode
                        </label>
                    </div>
                    <small class="text-muted fs-11 d-block ms-5">Auto-selected for new employees during salary setup.</small>
                </div>
            </div>

            <!-- Status & Notes Section -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
                <div class="section-label">3. Status & Operational Notes</div>

                <div class="row g-3">
                    <!-- Status -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="input_mode_status" name="status" required>
                            <option value="Active">Active (Available for Payroll)</option>
                            <option value="Inactive">Inactive (Suspended)</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold fs-13 mb-1">Operational Guidelines / SLA Description</label>
                        <textarea class="form-control" id="input_mode_description" name="description" rows="2"
                            placeholder="e.g. Cleared via automated HDFC corporate salary portal on 1st of every month..."></textarea>
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
        <button type="button" class="btn btn-primary rounded-3 px-4 d-flex align-items-center gap-2" id="btnSavePaymentMode">
            <span class="spinner-border spinner-border-sm d-none" id="saveModeSpinner"></span>
            <i class="ti ti-device-floppy" id="saveModeIcon"></i>
            <span id="saveModeBtnText">Save Payment Mode</span>
        </button>
    </div>
</div>

<!-- ==========================================
     OFFCANVAS: DETAIL PREVIEW DRAWER
     ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="paymentModeDetailDrawer" aria-labelledby="paymentModeDetailDrawerLabel">
    <div class="paymode-drawer-hero">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-white text-dark px-2 py-1 rounded-pill fs-11 fw-semibold shadow-sm" id="detailModePill">
                PAYMENT MODE DETAIL
            </span>
            <button type="button" class="btn btn-sm btn-icon text-white-50 hover-text-white bg-white bg-opacity-10 rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close" style="width: 32px; height: 32px;">
                <i class="ti ti-x fs-16"></i>
            </button>
        </div>
        <h4 class="fw-bold mb-1 text-white" id="detailModeName">-</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge badge-code" id="detailModeCode">-</span>
            <span id="detailModeChannelBadge">-</span>
            <span id="detailModeDefaultBadge">-</span>
        </div>
    </div>

    <div class="offcanvas-body p-4 bg-light flex-grow-1 overflow-auto">
        <!-- Highlights -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="metric-preview-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Medium Channel</div>
                    <div class="fw-bold text-dark fs-14" id="detailModeChannelLabel">-</div>
                </div>
            </div>
            <div class="col-6">
                <div class="metric-preview-box">
                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Transaction Fee</div>
                    <div class="fw-bold text-primary fs-14" id="detailModeFeeValue">-</div>
                </div>
            </div>
        </div>

        <!-- Specifications -->
        <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
            <div class="section-label">Disbursement Channel Specifications</div>
            <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Bank Account Required:</span>
                    <span class="fw-semibold fs-13" id="detailModeBankReq">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Primary Default Mode:</span>
                    <span class="fw-semibold fs-13" id="detailModeIsDefault">-</span>
                </li>
                <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <span class="text-muted fs-13">Disbursement Status:</span>
                    <span class="fw-semibold fs-13" id="detailModeStatus">-</span>
                </li>
            </ul>
        </div>

        <!-- Description -->
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="section-label">Operational Notes & SLA</div>
            <p class="text-muted fs-13 mb-0" id="detailModeDescription">-</p>
        </div>
    </div>

    <div class="offcanvas-footer p-3 bg-white border-top d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="offcanvas">
            Close
        </button>
        <button type="button" class="btn btn-primary rounded-3 px-3 d-flex align-items-center gap-2" id="btnDetailModeEditTrigger">
            <i class="ti ti-edit"></i> Edit Mode
        </button>
    </div>
</div>

<!-- ==========================================
     MODAL: DELETE PAYMENT MODE CONFIRMATION
     ========================================== -->
<div class="modal fade" id="deleteModeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle">
                        <i class="ti ti-trash fs-28"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Delete Payment Mode?</h5>
                <p class="text-muted fs-13 mb-3">
                    Are you sure you want to delete <span class="fw-bold text-dark" id="deleteModeName"></span>?
                    This disbursement option will be removed from future payroll cycles.
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-3 d-flex align-items-center gap-1" id="btnConfirmModeDelete">
                        <span class="spinner-border spinner-border-sm d-none" id="deleteModeSpinner"></span>
                        <i class="ti ti-trash" id="deleteModeIcon"></i> Delete
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
    let currentModeData = [];
    let activeModeEditingId = null;
    let deletingModeId = null;

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
    const modeTable = $('#paymentModesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('masters.payroll.payment-mode') }}",
            type: "GET",
            data: function(d) {
                d.channel_type = $('#filterModeChannel').val();
                d.requires_bank_details = $('#filterModeBankReq').val();
                d.status = $('#filterModeStatus').val();
                d.search = $('#filterModeSearch').val();
            },
            dataSrc: function(json) {
                currentModeData = json.data || [];
                $('#modeTableCount').text(currentModeData.length + ' modes');
                updateModeKPIs(currentModeData);
                return currentModeData;
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
                    const desc = data.description && data.description !== 'No operational description provided.'
                        ? `<div class="text-muted fs-11 text-truncate" style="max-width: 260px;" title="${data.description}">${data.description}</div>`
                        : '';
                    return `
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark fs-13">${data.mode_name}</span>
                                <span class="badge badge-code">${data.mode_code}</span>
                            </div>
                            ${desc}
                        </div>
                    `;
                }
            },
            {
                data: 'channel_type',
                render: function(data) {
                    if (data === 'electronic') {
                        return `<span class="badge-channel-electronic"><i class="ti ti-wifi"></i> Electronic / Digital</span>`;
                    } else {
                        return `<span class="badge-channel-physical"><i class="ti ti-wallet"></i> Physical</span>`;
                    }
                }
            },
            {
                data: 'requires_bank_details',
                render: function(data) {
                    return data
                        ? `<span class="badge bg-light text-dark border"><i class="ti ti-building-bank text-primary me-1"></i>Required</span>`
                        : `<span class="text-muted fs-12">Not Required</span>`;
                }
            },
            {
                data: 'formatted_fee',
                render: function(data) {
                    return `<span class="fw-bold text-dark fs-13">${data}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.is_default) {
                        return `<span class="badge-default-active"><i class="ti ti-star-filled text-warning"></i> Default Channel</span>`;
                    } else {
                        return `
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11 btn-make-default"
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
                                <input class="form-check-input mode-status-toggle" type="checkbox"
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
                        ? `<button type="button" class="btn-action-icon disabled opacity-50" title="Cannot delete default mode" disabled><i class="ti ti-trash"></i></button>`
                        : `<button type="button" class="btn-action-icon btn-delete btn-mode-delete" data-id="${data.id}" data-name="${data.mode_name}" title="Delete Mode"><i class="ti ti-trash"></i></button>`;

                    return `
                        <div class="d-inline-flex align-items-center gap-1">
                            <button type="button" class="btn-action-icon btn-view btn-mode-view" data-id="${data.id}" title="View Details">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button type="button" class="btn-action-icon btn-edit btn-mode-edit" data-id="${data.id}" title="Edit Mode">
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
            searchPlaceholder: "Search payment modes...",
            lengthMenu: "Show _MENU_ modes",
            info: "Showing _START_ to _END_ of _TOTAL_ modes",
            infoEmpty: "No payment modes found",
            emptyTable: "No payment modes registered yet.",
            paginate: {
                previous: '<i class="ti ti-chevron-left"></i>',
                next: '<i class="ti ti-chevron-right"></i>'
            }
        }
    });

    // KPI Counters updater
    function updateModeKPIs(items) {
        const total = items.length;
        const electronic = items.filter(i => i.channel_type === 'electronic').length;
        const physical = items.filter(i => i.channel_type === 'physical').length;
        const def = items.find(i => i.is_default);

        $('#kpiTotalModes').text(total);
        $('#kpiElectronicModes').text(electronic);
        $('#kpiPhysicalModes').text(physical);
        if (def) {
            $('#kpiDefaultModeName').text(def.mode_name);
        }
    }

    // Filter listeners
    $('#filterModeChannel, #filterModeBankReq, #filterModeStatus').on('change', function() {
        modeTable.ajax.reload();
    });

    let searchTimeout;
    $('#filterModeSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            modeTable.ajax.reload();
        }, 300);
    });

    $('#btnResetModeFilters').on('click', function() {
        $('#filterModeSearch').val('');
        $('#filterModeChannel').val('all');
        $('#filterModeBankReq').val('all');
        $('#filterModeStatus').val('all');
        modeTable.ajax.reload();
    });

    $('#btnRefreshModeCatalog').on('click', function() {
        modeTable.ajax.reload(null, false);
        showToast('Payment mode catalog refreshed!', 'success');
    });

    // Channel radio toggle cards
    $('input[name="channel_type"]').on('change', function() {
        updateChannelCards();
    });

    function updateChannelCards() {
        const val = $('input[name="channel_type"]:checked').val();
        if (val === 'electronic') {
            $('#labelChannelElectronic').addClass('active-electronic');
            $('#labelChannelPhysical').removeClass('active-physical');
        } else {
            $('#labelChannelElectronic').removeClass('active-electronic');
            $('#labelChannelPhysical').addClass('active-physical');
        }
    }
    updateChannelCards();

    // Auto capitalize code
    $('#input_mode_code').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    // Auto suggest code from name if creating
    $('#input_mode_name').on('input', function() {
        if (!activeModeEditingId && $('#input_mode_code').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 15);
            $('#input_mode_code').val(suggested);
        }
    });

    // Open Add Mode Drawer
    $('#btnOpenAddModeDrawer').on('click', function() {
        activeModeEditingId = null;
        $('#modeAlertBox').addClass('d-none').empty();
        $('#paymentModeForm')[0].reset();
        $('#mode_id').val('');
        $('#mode_form_method').val('POST');

        $('#modeDrawerPill').html('<i class="ti ti-plus me-1 text-primary"></i> NEW PAYMENT MODE');
        $('#modeDrawerTitle').text('Add Payment Mode');
        $('#modeDrawerSubtitle').text('Configure disbursement method, banking validations & fees.');
        $('#saveModeBtnText').text('Save Payment Mode');

        $('#channelElectronic').prop('checked', true).trigger('change');
        $('#input_requires_bank_details').val('1');
        $('#input_processing_fee').val('0');
        $('#input_is_default').prop('checked', false);
        $('#input_mode_status').val('Active');

        const drawer = new bootstrap.Offcanvas(document.getElementById('paymentModeDrawer'));
        drawer.show();
    });

    // Open Edit Mode Drawer
    $(document).on('click', '.btn-mode-edit', function() {
        const id = $(this).data('id');
        openEditModeDrawer(id);
    });

    function openEditModeDrawer(id) {
        const item = currentModeData.find(m => m.id == id);
        if (!item) return;

        activeModeEditingId = id;
        $('#modeAlertBox').addClass('d-none').empty();
        $('#mode_id').val(item.id);
        $('#mode_form_method').val('PUT');

        $('#modeDrawerPill').html('<i class="ti ti-edit me-1 text-warning"></i> EDIT MODE');
        $('#modeDrawerTitle').text('Edit ' + item.mode_name);
        $('#modeDrawerSubtitle').text('Modify disbursement channel rules, account validation or fees.');
        $('#saveModeBtnText').text('Update Payment Mode');

        $('#input_mode_name').val(item.mode_name);
        $('#input_mode_code').val(item.mode_code);

        if (item.channel_type === 'electronic') {
            $('#channelElectronic').prop('checked', true).trigger('change');
        } else {
            $('#channelPhysical').prop('checked', true).trigger('change');
        }

        $('#input_requires_bank_details').val(item.requires_bank_details ? '1' : '0');
        $('#input_processing_fee').val(item.processing_fee);
        $('#input_is_default').prop('checked', item.is_default);
        $('#input_mode_status').val(item.status);
        $('#input_mode_description').val(item.description && item.description !== 'No operational description provided.' ? item.description : '');

        // Close preview if open
        const detailDrawerEl = document.getElementById('paymentModeDetailDrawer');
        const detailInstance = bootstrap.Offcanvas.getInstance(detailDrawerEl);
        if (detailInstance) detailInstance.hide();

        const drawer = new bootstrap.Offcanvas(document.getElementById('paymentModeDrawer'));
        drawer.show();
    }

    // Save or Update Payment Mode
    $('#btnSavePaymentMode').on('click', function(e) {
        e.preventDefault();
        $('#modeAlertBox').addClass('d-none').empty();

        const form = document.getElementById('paymentModeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const isEditing = Boolean(activeModeEditingId);
        const url = isEditing
            ? `{{ url('masters/payroll/payment-mode/update') }}/${activeModeEditingId}`
            : `{{ route('masters.payroll.payment-mode.store') }}`;

        const formData = {
            _token: "{{ csrf_token() }}",
            mode_name: $('#input_mode_name').val(),
            mode_code: $('#input_mode_code').val(),
            channel_type: $('input[name="channel_type"]:checked').val(),
            requires_bank_details: $('#input_requires_bank_details').val(),
            processing_fee: $('#input_processing_fee').val(),
            is_default: $('#input_is_default').is(':checked') ? 1 : 0,
            status: $('#input_mode_status').val(),
            description: $('#input_mode_description').val(),
        };

        if (isEditing) {
            formData._method = 'PUT';
        }

        $('#btnSavePaymentMode').prop('disabled', true);
        $('#saveModeSpinner').removeClass('d-none');
        $('#saveModeIcon').addClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                $('#btnSavePaymentMode').prop('disabled', false);
                $('#saveModeSpinner').addClass('d-none');
                $('#saveModeIcon').removeClass('d-none');

                if (res.success) {
                    showToast(res.message, 'success');
                    const drawerEl = document.getElementById('paymentModeDrawer');
                    const drawer = bootstrap.Offcanvas.getInstance(drawerEl);
                    if (drawer) drawer.hide();

                    modeTable.ajax.reload(null, false);
                } else {
                    $('#modeAlertBox').removeClass('d-none').text(res.message || 'Operation failed.');
                }
            },
            error: function(xhr) {
                $('#btnSavePaymentMode').prop('disabled', false);
                $('#saveModeSpinner').addClass('d-none');
                $('#saveModeIcon').removeClass('d-none');

                let errorMsg = 'An error occurred while saving.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#modeAlertBox').removeClass('d-none').html(errorMsg);
            }
        });
    });

    // View Details Drawer
    $(document).on('click', '.btn-mode-view', function() {
        const id = $(this).data('id');
        const item = currentModeData.find(m => m.id == id);
        if (!item) return;

        $('#detailModeName').text(item.mode_name);
        $('#detailModeCode').text(item.mode_code);

        if (item.channel_type === 'electronic') {
            $('#detailModeChannelBadge').html('<span class="badge-channel-electronic"><i class="ti ti-wifi"></i> Electronic</span>');
            $('#detailModeChannelLabel').text('Digital / Electronic Clearing');
        } else {
            $('#detailModeChannelBadge').html('<span class="badge-channel-physical"><i class="ti ti-wallet"></i> Physical</span>');
            $('#detailModeChannelLabel').text('Physical Cheque / Cash Paper');
        }

        if (item.is_default) {
            $('#detailModeDefaultBadge').html('<span class="badge-default-active"><i class="ti ti-star-filled text-warning"></i> Primary Default</span>');
            $('#detailModeIsDefault').html('<span class="badge bg-primary text-white">Yes (Active Default)</span>');
        } else {
            $('#detailModeDefaultBadge').html('');
            $('#detailModeIsDefault').text('No');
        }

        $('#detailModeFeeValue').text(item.formatted_fee);
        $('#detailModeBankReq').html(item.requires_bank_details
            ? '<span class="badge bg-light text-dark border"><i class="ti ti-check text-success me-1"></i>Mandatory (Bank & IFSC)</span>'
            : '<span class="badge bg-light text-muted border">Not Required</span>');

        $('#detailModeStatus').text(item.status);
        $('#detailModeDescription').text(item.description || 'Standard disbursement channel operational policy.');

        $('#btnDetailModeEditTrigger').off('click').on('click', function() {
            openEditModeDrawer(item.id);
        });

        const drawer = new bootstrap.Offcanvas(document.getElementById('paymentModeDetailDrawer'));
        drawer.show();
    });

    // Make Default Button
    $(document).on('click', '.btn-make-default', function() {
        const id = $(this).data('id');

        $.ajax({
            url: `{{ url('masters/payroll/payment-mode/set-default') }}/${id}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    modeTable.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Operation failed.', 'danger');
                }
            },
            error: function() {
                showToast('Failed to set default mode.', 'danger');
            }
        });
    });

    // Status Toggle
    $(document).on('change', '.mode-status-toggle', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        const originalState = !checkbox.is(':checked');

        $.ajax({
            url: `{{ url('masters/payroll/payment-mode/toggle-status') }}/${id}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    modeTable.ajax.reload(null, false);
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
    $(document).on('click', '.btn-mode-delete', function() {
        deletingModeId = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteModeName').text(name);

        const modal = new bootstrap.Modal(document.getElementById('deleteModeModal'));
        modal.show();
    });

    $('#btnConfirmModeDelete').on('click', function() {
        if (!deletingModeId) return;

        $('#btnConfirmModeDelete').prop('disabled', true);
        $('#deleteModeSpinner').removeClass('d-none');
        $('#deleteModeIcon').addClass('d-none');

        $.ajax({
            url: `{{ url('masters/payroll/payment-mode/delete') }}/${deletingModeId}`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            dataType: 'json',
            success: function(res) {
                $('#btnConfirmModeDelete').prop('disabled', false);
                $('#deleteModeSpinner').addClass('d-none');
                $('#deleteModeIcon').removeClass('d-none');

                const modalEl = document.getElementById('deleteModeModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (res.success) {
                    showToast(res.message, 'success');
                    modeTable.ajax.reload(null, false);
                } else {
                    showToast(res.message || 'Delete operation failed.', 'danger');
                }
                deletingModeId = null;
            },
            error: function(xhr) {
                $('#btnConfirmModeDelete').prop('disabled', false);
                $('#deleteModeSpinner').addClass('d-none');
                $('#deleteModeIcon').removeClass('d-none');

                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Failed to delete payment mode.';
                showToast(msg, 'danger');
                deletingModeId = null;
            }
        });
    });
});
</script>
@endpush
