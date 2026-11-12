@extends('layout.master')
@section('title', $title ?? 'Employee Payslips Archive')
@section('main-section')

<style>
    /* KPI Summary Cards */
    .payslips-page .stat-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .payslips-page .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }

    /* Table & Container Card */
    .payslips-page .table-container-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        background: #ffffff;
    }

    .payslip-table th {
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc !important;
        color: #475569;
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .payslip-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .payslip-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .avatar-initials {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #e0e7ff;
        color: #4338ca;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .badge-soft-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-soft-warning {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-soft-secondary {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Modern DataTables Pagination & Info (Same as Employee / Salary Structure) */
    .payslips-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .payslips-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .payslips-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

    .payslips-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .payslips-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .payslips-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
    }

    .payslips-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .payslips-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Modern Employee-Style Profile Slider Drawer (Offcanvas) */
    #payslipDrawer {
        width: 490px !important;
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

    #payslipDrawer .offcanvas-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px 24px;
    }

    #payslipDrawer .offcanvas-body {
        padding: 0 !important;
        background: #f8fafc;
        overflow-y: auto;
        flex: 1;
    }

    .emp-slider-hero {
        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        position: relative;
        padding: 26px 24px 22px;
        color: #ffffff;
        overflow: hidden;
    }

    .emp-slider-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.35) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }

    .emp-slider-hero::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -30px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.25) 0%, rgba(168, 85, 247, 0) 70%);
        pointer-events: none;
    }

    .emp-avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 8px;
    }

    .emp-avatar-img {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        border: 3px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        object-fit: cover;
        background: #ffffff;
    }

    .emp-status-floating-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        background-color: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
    }

    .emp-quick-action-bar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .emp-quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px 6px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 11px;
        font-weight: 600;
    }

    .emp-quick-action-btn i {
        font-size: 18px;
        margin-bottom: 4px;
        transition: transform 0.2s ease;
    }

    .emp-quick-action-btn:hover {
        transform: translateY(-2px);
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        color: #0d6efd;
        border-color: #cbd5e1;
    }

    .emp-quick-action-btn:hover i {
        transform: scale(1.15);
    }

    .emp-slider-section {
        padding: 20px;
    }

    .emp-slider-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }

    .emp-slider-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
    }

    .emp-card-title {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .emp-data-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #f1f5f9;
    }

    .emp-data-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .emp-data-row:first-child {
        padding-top: 0;
    }

    .emp-data-label {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .emp-data-value {
        font-size: 13px;
        color: #1e293b;
        font-weight: 600;
    }
</style>

<div class="page-wrapper payslips-page">
    <div class="content">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-receipt-2 text-primary"></i> Employee Payslips Archive
                </h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Payroll</li>
                        <li class="breadcrumb-item active">Payslips</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('payroll.process') }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-player-play fs-15"></i> Process New Batch
                </a>
                <a href="{{ route('masters.payroll.salary-structure') }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-cash-banknote fs-16"></i> Salary Structures
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-files"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Total Payslips</span>
                        <h4 class="fw-bold text-dark mb-0 fs-20">{{ number_format($totalPayslips ?? 0) }}</h4>
                        <span class="fs-11 text-muted">Generated across all runs</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-circle-check"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Disbursed Net Pay</span>
                        <h4 class="fw-bold text-success mb-0 fs-18">&#8377; {{ number_format($totalDisbursedAmount ?? 0, 2) }}</h4>
                        <span class="fs-11 text-muted">{{ $paidCount ?? 0 }} Payslips Marked Paid</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-clock"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Pending Disbursals</span>
                        <h4 class="fw-bold text-warning mb-0 fs-18">&#8377; {{ number_format($totalPendingAmount ?? 0, 2) }}</h4>
                        <span class="fs-11 text-muted">{{ $pendingCount ?? 0 }} Awaiting disbursement</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-wallet"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Gross Earnings Volume</span>
                        <h4 class="fw-bold text-dark mb-0 fs-18">&#8377; {{ number_format($totalGrossAmount ?? 0, 2) }}</h4>
                        <span class="fs-11 text-muted">Cumulative gross payroll</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card p-3 mb-4 border-0 shadow-sm rounded-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Quick Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                        <input type="text" id="payslipSearch" class="form-control border-start-0" placeholder="Name, email, ID, slip no...">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Month</label>
                    <select id="payslipMonthFilter" class="form-select form-select-sm">
                        <option value="all">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Year</label>
                    <select id="payslipYearFilter" class="form-select form-select-sm">
                        <option value="all">All Years</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == \Carbon\Carbon::now()->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Payment Status</label>
                    <select id="payslipStatusFilter" class="form-select form-select-sm">
                        <option value="all">All Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Agency</label>
                    <select id="payslipAgencyFilter" class="form-select form-select-sm">
                        <option value="all">All Agencies</option>
                        <option value="unassigned">Direct / In-House</option>
                        @foreach($agencies as $ag)
                            <option value="{{ $ag->id }}">{{ $ag->agency_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="button" id="btnFilterReset" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center" title="Reset Filters">
                        <i class="ti ti-rotate-clockwise fs-14"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Payslips Master DataTable Card -->
        <div class="card shadow-sm border-0 table-container-card">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0 fw-bold">Issued Employee Payslips</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11" id="payslipCountBadge">Loading...</span>
                </div>
                <div class="fs-12 text-muted">
                    Click any payslip or eye icon to view complete breakdown drawer
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 payslip-table w-100" id="payslipsTable">
                        <thead>
                            <tr>
                                <th width="45" class="text-center">#</th>
                                <th>Payslip No &amp; Period</th>
                                <th>Employee Details</th>
                                <th class="text-center">Payable Days</th>
                                <th class="text-end">Earned Gross</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net Take-Home</th>
                                <th class="text-center">Status</th>
                                <th width="110" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= MODERN PROFILE-STYLE SLIDER DRAWER (OFFCANVAS) ================= -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="payslipDrawer" aria-labelledby="payslipDrawerLabel">
    <!-- Header -->
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="ti ti-receipt-2 fs-18"></i>
            </span>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark mb-0" id="payslipDrawerLabel">Employee Payslip Breakdown</h6>
                <small class="text-muted" id="drawerPayslipNo">PSL-000000</small>
            </div>
        </div>
        <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body -->
    <div class="offcanvas-body" id="payslipDrawerBody">
        <!-- Hero Gradient Banner (Matches Employee Profile Style) -->
        <div class="emp-slider-hero">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="emp-avatar-wrapper">
                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Profile" 
                         class="emp-avatar-img" id="drawerEmpImg">
                    <span class="emp-status-floating-dot" id="drawerEmpStatusDot"></span>
                </div>
                <div class="d-flex flex-column align-items-end gap-1.5">
                    <span class="badge bg-opacity-15 text-white border border-white border-opacity-20 font-monospace fs-11" id="drawerEmpIdBadge">
                        #EMP---
                    </span>
                    <span class="badge bg-success-subtle text-success fs-11 px-2.5 py-1 rounded-pill fw-bold" id="drawerPaymentStatus">
                        Paid
                    </span>
                </div>
            </div>

            <h4 class="fw-bold text-white mb-1" id="drawerEmpName">Employee Name</h4>
            <div class="text-white-50 fs-13 mb-3 d-flex align-items-center gap-1.5 flex-wrap">
                <span id="drawerEmpEmail"><i class="ti ti-mail me-1"></i>email@example.com</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-opacity-20 text-white border border-white border-opacity-25 fs-11 px-2.5 py-1 rounded-pill" id="drawerPeriodBadge">
                    <i class="ti ti-calendar me-1"></i>Period
                </span>
                <span class="badge bg-opacity-20 text-white border border-white border-opacity-25 fs-11 px-2.5 py-1 rounded-pill" id="drawerDesignation">
                    <i class="ti ti-briefcase me-1"></i>Designation
                </span>
                <span class="badge fs-11 text-white border border-white border-opacity-25 px-2.5 py-1 rounded-pill" id="drawerAgency" style="background-color: rgba(168, 85, 247, 0.45);">
                    <i class="ti ti-building me-1"></i>Agency
                </span>
            </div>
        </div>

        <!-- Quick Action Hub -->
        <div class="emp-quick-action-bar">
            <a href="javascript:void(0);" id="drawerDownloadBtn" class="emp-quick-action-btn" title="Download Official Payslip PDF">
                <i class="ti ti-download text-danger"></i>
                <span>Download PDF</span>
            </a>
            <a href="javascript:void(0);" id="drawerProfileBtn" class="emp-quick-action-btn" title="View Full Employee Profile">
                <i class="ti ti-user text-primary"></i>
                <span>Profile</span>
            </a>
            <a href="javascript:void(0);" onclick="window.print();" class="emp-quick-action-btn" title="Print Breakdown">
                <i class="ti ti-printer text-secondary"></i>
                <span>Print</span>
            </a>
        </div>

        <!-- Slider Content Sections -->
        <div class="emp-slider-section">

            <!-- Highlight Banner Card (Gross, Deductions & Net Pay) -->
            <div class="emp-slider-card p-3 mb-3" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <span class="fs-10 text-muted text-uppercase fw-semibold d-block">Gross Earned</span>
                        <h6 class="fw-bold text-dark mb-0 fs-14 mt-1" id="drawerGross">&#8377; 0.00</h6>
                    </div>
                    <div class="col-4 border-start border-end">
                        <span class="fs-10 text-danger text-uppercase fw-semibold d-block">Deductions</span>
                        <h6 class="fw-bold text-danger mb-0 fs-14 mt-1" id="drawerDeductions">&#8377; 0.00</h6>
                    </div>
                    <div class="col-4">
                        <span class="fs-10 text-success text-uppercase fw-semibold d-block">Take-Home</span>
                        <h6 class="fw-bold text-success mb-0 fs-14 mt-1" id="drawerNet">&#8377; 0.00</h6>
                    </div>
                </div>
            </div>

            <!-- Attendance & Working Days Metrics Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-calendar-stats text-primary fs-15"></i>Attendance &amp; Days Reconciliation</span>
                    <span class="badge bg-primary-subtle text-primary fs-10" id="drawerMonthLabel">-</span>
                </div>

                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-calendar text-muted"></i>Calendar Days in Month</span>
                    <span class="emp-data-value" id="drawerTotalDays">0</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-check text-success"></i>Present / Worked Days</span>
                    <span class="emp-data-value text-success" id="drawerPresentDays">0</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-thumb-up text-info"></i>Approved Paid Leaves</span>
                    <span class="emp-data-value text-info" id="drawerPaidLeaves">0</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-confetti text-purple"></i>Public Holidays</span>
                    <span class="emp-data-value" id="drawerHolidays">0</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-sun text-secondary"></i>Weekly Offs (Sundays)</span>
                    <span class="emp-data-value" id="drawerWeeklyOffs">0</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-alert-triangle text-danger"></i>Loss of Pay (LWP Days)</span>
                    <span class="emp-data-value text-danger" id="drawerLwpDays">0</span>
                </div>
                <div class="emp-data-row bg-primary-subtle rounded px-2 py-1.5 mt-2">
                    <span class="emp-data-label fw-bold text-primary"><i class="ti ti-calculator text-primary"></i>Net Payable Working Days</span>
                    <span class="emp-data-value fw-bold text-primary fs-14" id="drawerPayableDays">0 Days</span>
                </div>
            </div>

            <!-- Earnings Breakdown Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-circle-arrow-up text-success fs-15"></i>Earned Salary Components</span>
                    <span class="badge bg-success-subtle text-success fs-10">Earnings</span>
                </div>

                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-coin text-muted"></i>Basic Salary (Earned)</span>
                    <span class="emp-data-value fw-semibold" id="drawerEarnedBasic">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-home text-muted"></i>HRA (House Rent)</span>
                    <span class="emp-data-value" id="drawerEarnedHra">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-car text-muted"></i>Conveyance Allowance</span>
                    <span class="emp-data-value" id="drawerEarnedConveyance">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-heart-rate-monitor text-muted"></i>Medical Allowance</span>
                    <span class="emp-data-value" id="drawerEarnedMedical">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-motorbike text-muted"></i>Bike Maintenance Allowance</span>
                    <span class="emp-data-value" id="drawerEarnedBike">&#8377; 1,500.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-star text-muted"></i>Special Allowance</span>
                    <span class="emp-data-value" id="drawerEarnedSpecial">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row" id="drawerOvertimeRow">
                    <span class="emp-data-label"><i class="ti ti-clock-plus text-muted"></i>Overtime / Bonus</span>
                    <span class="emp-data-value" id="drawerBonus">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row bg-primary-subtle rounded px-2 py-1.5 mt-2">
                    <span class="emp-data-label fw-bold text-primary"><i class="ti ti-plus text-primary"></i>Total Gross Earnings</span>
                    <span class="emp-data-value fw-bold text-primary fs-14" id="drawerTotalEarnings">&#8377; 0.00</span>
                </div>
            </div>

            <!-- Deductions Breakdown Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-circle-arrow-down text-danger fs-15"></i>Deductions Breakdown</span>
                    <span class="badge bg-danger-subtle text-danger fs-10">Deductions</span>
                </div>

                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-shield text-muted"></i>Provident Fund (PF)</span>
                    <span class="emp-data-value text-danger" id="drawerPf">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-first-aid-kit text-muted"></i>Employee State Insurance (ESI)</span>
                    <span class="emp-data-value text-danger" id="drawerEsi">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-certificate text-muted"></i>Professional Tax (PT)</span>
                    <span class="emp-data-value text-danger" id="drawerPt">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-receipt-tax text-muted"></i>TDS / Income Tax</span>
                    <span class="emp-data-value text-danger" id="drawerTds">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row" id="drawerLwpDedRow">
                    <span class="emp-data-label"><i class="ti ti-calendar-off text-muted"></i>Loss of Pay (LWP Deduction)</span>
                    <span class="emp-data-value text-danger" id="drawerLwpDed">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row" id="drawerOtherDedRow">
                    <span class="emp-data-label"><i class="ti ti-dots text-muted"></i>Other Deductions</span>
                    <span class="emp-data-value text-danger" id="drawerOtherDed">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row" id="drawerGratuityRow">
                    <span class="emp-data-label"><i class="ti ti-award text-muted"></i>Gratuity (Employer Retiral)</span>
                    <span class="emp-data-value text-info" id="drawerGratuity">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row bg-danger-subtle rounded px-2 py-1.5 mt-2">
                    <span class="emp-data-label fw-bold text-danger"><i class="ti ti-minus text-danger"></i>Total Deductions</span>
                    <span class="emp-data-value fw-bold text-danger fs-14" id="drawerTotalDeductions">&#8377; 0.00</span>
                </div>
            </div>

            <!-- Payment & Bank Details Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title">
                    <i class="ti ti-building-bank text-primary fs-15"></i>Banking &amp; Settlement Details
                </div>

                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-building-bank text-muted"></i>Bank Name</span>
                    <span class="emp-data-value fw-semibold" id="drawerBankName">-</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-credit-card text-muted"></i>Account Number</span>
                    <span class="emp-data-value font-monospace" id="drawerBankAcc">-</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-barcode text-muted"></i>IFSC Code</span>
                    <span class="emp-data-value font-monospace" id="drawerIfsc">-</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-calendar-event text-muted"></i>Payment Date</span>
                    <span class="emp-data-value" id="drawerPaymentDate">-</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-cash text-muted"></i>Payment Mode</span>
                    <span class="emp-data-value" id="drawerPaymentMode">Bank Transfer</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-receipt text-muted"></i>Transaction Ref</span>
                    <span class="emp-data-value font-monospace" id="drawerTxnRef">-</span>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('after_scripts')
<script>
    function formatINR(val) {
        const num = parseFloat(val) || 0;
        return '&#8377; ' + num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $(document).ready(function() {
        const avatarColors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777'];
        let tableDataMap = {};

        const table = $('#payslipsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('payroll.payslips') }}",
                data: function(d) {
                    d.month = $('#payslipMonthFilter').val();
                    d.year = $('#payslipYearFilter').val();
                    d.payment_status = $('#payslipStatusFilter').val();
                    d.agency_id = $('#payslipAgencyFilter').val();
                },
                dataSrc: function(json) {
                    tableDataMap = {};
                    const items = json.data || [];
                    items.forEach(item => {
                        tableDataMap[item.id] = item;
                    });
                    $('#payslipCountBadge').text(`${items.length} Payslips`);
                    return items;
                }
            },
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            dom: "<'row'<'col-12'tr>>" +
                 "<'row align-items-center justify-content-between p-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center'i><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ payslips",
                infoEmpty: "Showing 0 to 0 of 0 payslips",
                infoFiltered: "(filtered from _MAX_ total)",
                paginate: {
                    first: '<i class="ti ti-chevrons-left"></i>',
                    previous: '<i class="ti ti-chevron-left"></i>',
                    next: '<i class="ti ti-chevron-right"></i>',
                    last: '<i class="ti ti-chevrons-right"></i>'
                },
                emptyTable: "No employee payslips found",
                zeroRecords: "No matching payslips found"
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => `<span class="badge bg-light text-secondary border font-monospace fs-11">#${meta.row + 1}</span>`
                },
                {
                    data: 'payslip_number',
                    render: function(data, type, row) {
                        return `
                            <div>
                                <a href="javascript:void(0);" onclick="openPayslipDrawer(${row.id})" class="fw-bold text-dark text-decoration-none hover-primary font-monospace fs-13">
                                    ${data}
                                </a>
                                <div class="mt-0.5">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10" style="padding: 2px 6px;">
                                        <i class="ti ti-calendar me-0.5"></i>${row.period}
                                    </span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'employee_name',
                    render: function(data, type, row, meta) {
                        const name = data || 'Employee';
                        const email = row.employee_email || '-';
                        const words = name.trim().split(/\s+/);
                        const initials = words.length >= 2 && words[0] && words[1]
                            ? (words[0][0] + words[1][0]).toUpperCase()
                            : name.substring(0, 2).toUpperCase();
                        const bgCol = avatarColors[meta.row % avatarColors.length];

                        let avatarHtml = '';
                        if (row.profile_image && row.profile_image !== 'default.png') {
                            avatarHtml = `<img src="{{ asset('uploads/employees') }}/${row.profile_image}" alt="${name}" class="rounded-circle me-2 border" width="38" height="38" style="object-fit: cover;">`;
                        } else {
                            avatarHtml = `<span class="avatar-initials me-2" style="background-color: ${bgCol};">${initials}</span>`;
                        }

                        let agencyTag = row.has_agency && row.agency && row.agency !== 'Direct / In-House'
                            ? `<span class="badge border fs-10 ms-1" style="background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff !important;">${row.agency}</span>`
                            : '';

                        return `
                            <div class="d-flex align-items-center">
                                ${avatarHtml}
                                <div>
                                    <div class="fw-semibold text-dark fs-13 d-flex align-items-center gap-1">
                                        <a href="javascript:void(0);" onclick="openPayslipDrawer(${row.id})" class="text-dark text-decoration-none hover-primary fw-bold">${name}</a>
                                        ${agencyTag}
                                    </div>
                                    <div class="fs-11 text-muted d-flex align-items-center gap-1.5 mt-0.5">
                                        <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 10px;">#EMP-${row.employee_id}</span>
                                        <span>${row.designation}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'payable_days',
                    className: 'text-center',
                    render: function(data, type, row) {
                        let lwpBadge = '';
                        if (row.lwp_days > 0) {
                            lwpBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-10 d-block mt-0.5">${row.lwp_days} LWP</span>`;
                        }
                        return `
                            <div>
                                <span class="badge bg-light text-dark border fs-11">${data} / ${row.total_days} Days</span>
                                ${lwpBadge}
                            </div>
                        `;
                    }
                },
                {
                    data: 'total_earnings',
                    className: 'text-end fw-bold text-dark fs-13',
                    render: data => '&#8377; ' + (parseFloat(data) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                },
                {
                    data: 'total_deductions',
                    className: 'text-end fw-semibold text-danger fs-13',
                    render: data => '&#8377; ' + (parseFloat(data) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                },
                {
                    data: 'net_salary',
                    className: 'text-end fw-bold text-success fs-14',
                    render: data => '&#8377; ' + (parseFloat(data) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                },
                {
                    data: 'payment_status',
                    className: 'text-center',
                    render: function(data) {
                        if (data === 'Paid') {
                            return `<span class="badge badge-soft-success px-2 py-1 fs-11"><i class="ti ti-check me-0.5"></i> Paid</span>`;
                        }
                        return `<span class="badge badge-soft-warning px-2 py-1 fs-11"><i class="ti ti-clock me-0.5"></i> Pending</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-info" title="View Full Breakdown in Drawer" onclick="openPayslipDrawer(${row.id})">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <a href="${row.download_url}" class="btn btn-outline-danger" title="Download Official PDF">
                                    <i class="ti ti-download"></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Search Box live search
        $('#payslipSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filters reload
        $('#payslipMonthFilter, #payslipYearFilter, #payslipStatusFilter, #payslipAgencyFilter').on('change', function() {
            table.ajax.reload();
        });

        $('#btnFilterReset').on('click', function() {
            $('#payslipSearch').val('');
            $('#payslipMonthFilter').val('all');
            $('#payslipYearFilter').val("{{ \Carbon\Carbon::now()->year }}");
            $('#payslipStatusFilter').val('all');
            $('#payslipAgencyFilter').val('all');
            table.search('').ajax.reload();
        });

        // Open Slider Drawer
        window.openPayslipDrawer = function(id) {
            const item = tableDataMap[id];
            if (!item) return;

            let imgUrl = (item.profile_image && item.profile_image !== 'default.png')
                ? `{{ asset('uploads/employees') }}/${item.profile_image}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.employee_name || 'Staff')}&background=0d6efd&color=fff&size=128&bold=true`;

            $('#drawerEmpImg').attr('src', imgUrl);
            $('#drawerPayslipNo').text(item.payslip_number);
            $('#drawerEmpIdBadge').text(`#EMP-${item.employee_id}`);
            $('#drawerEmpName').text(item.employee_name || 'Employee');
            $('#drawerEmpEmail').html(`<i class="ti ti-mail me-1"></i>${item.employee_email || '-'}`);

            // Payment status
            if (item.payment_status === 'Paid') {
                $('#drawerPaymentStatus').removeClass('bg-warning-subtle text-warning').addClass('bg-success-subtle text-success').text('Paid');
                $('#drawerEmpStatusDot').css('background-color', '#10b981');
            } else {
                $('#drawerPaymentStatus').removeClass('bg-success-subtle text-success').addClass('bg-warning-subtle text-warning').text('Pending');
                $('#drawerEmpStatusDot').css('background-color', '#f59e0b');
            }

            // Badges
            $('#drawerPeriodBadge').html(`<i class="ti ti-calendar me-1"></i>${item.period_full}`);
            $('#drawerDesignation').html(`<i class="ti ti-briefcase me-1"></i>${item.designation || 'Staff'}`);
            $('#drawerAgency').html(`<i class="ti ti-building me-1"></i>${item.agency || 'Direct / In-House'}`);
            $('#drawerMonthLabel').text(item.period_full);

            // Action links
            $('#drawerDownloadBtn').attr('href', item.download_url);
            $('#drawerProfileBtn').attr('href', `{{ url('employees') }}/${item.employee_id}`);

            // Highlight metrics
            $('#drawerGross').html(formatINR(item.total_earnings));
            $('#drawerDeductions').html(formatINR(item.total_deductions));
            $('#drawerNet').html(formatINR(item.net_salary));

            // Working Days
            $('#drawerTotalDays').text(item.total_days);
            $('#drawerPresentDays').text(item.present_days);
            $('#drawerPaidLeaves').text(item.paid_leave_days);
            $('#drawerHolidays').text(item.holiday_days);
            $('#drawerWeeklyOffs').text(item.weekly_off_days);
            $('#drawerLwpDays').text(item.lwp_days);
            $('#drawerPayableDays').text(`${item.payable_days} Days`);

            // Earnings
            $('#drawerEarnedBasic').html(formatINR(item.earned_basic));
            $('#drawerEarnedHra').html(formatINR(item.earned_hra));
            $('#drawerEarnedConveyance').html(formatINR(item.earned_conveyance));
            $('#drawerEarnedMedical').html(formatINR(item.earned_medical));
            $('#drawerEarnedBike').html(formatINR(item.earned_bike_allowance !== undefined ? item.earned_bike_allowance : 1500));
            $('#drawerEarnedSpecial').html(formatINR(item.earned_special_allowance));
            const bonusTotal = (parseFloat(item.bonus) || 0) + (parseFloat(item.incentive) || 0) + (parseFloat(item.overtime_pay) || 0);
            $('#drawerBonus').html(formatINR(bonusTotal));
            $('#drawerTotalEarnings').html(formatINR(item.total_earnings));

            // Deductions
            $('#drawerPf').html(formatINR(item.pf_deduction));
            $('#drawerEsi').html(formatINR(item.esi_deduction));
            $('#drawerPt').html(formatINR(item.pt_deduction));
            $('#drawerTds').html(formatINR(item.tds_deduction));
            $('#drawerLwpDed').html(formatINR(item.lwp_deduction));
            $('#drawerOtherDed').html(formatINR(item.other_deductions));
            if (parseFloat(item.gratuity_accrual) > 0) {
                $('#drawerGratuityRow').show();
                $('#drawerGratuity').html(formatINR(item.gratuity_accrual));
            } else {
                $('#drawerGratuityRow').hide();
            }
            $('#drawerTotalDeductions').html(formatINR(item.total_deductions));

            // Banking & Settlement
            $('#drawerBankName').text(item.bank_name || '-');
            $('#drawerBankAcc').text(item.bank_account_no || '-');
            $('#drawerIfsc').text(item.ifsc_code || '-');
            $('#drawerPaymentDate').text(item.payment_date || '-');
            $('#drawerPaymentMode').text(item.payment_mode || 'Bank Transfer');
            $('#drawerTxnRef').text(item.transaction_reference || '-');

            const offcanvasEl = document.getElementById('payslipDrawer');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        };
    });
</script>
@endpush
