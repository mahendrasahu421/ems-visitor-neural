@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* KPI Summary Cards */
    .salary-page .stat-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .salary-page .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }

    /* Table & Container Card */
    .salary-page .table-container-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        background: #ffffff;
    }

    .salary-table th {
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

    .salary-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .salary-table tbody tr:hover {
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

    .badge-soft-secondary {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Modern DataTables Pagination & Info */
    .salary-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .salary-page .dataTables_wrapper .dataTables_paginate {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .salary-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

    .salary-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .salary-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .salary-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
    }

    .salary-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .salary-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Modern Employee-Style Profile Slider Drawer (Offcanvas) */
    #salaryStructureSidebar {
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

    #salaryStructureSidebar .offcanvas-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px 24px;
    }

    #salaryStructureSidebar .offcanvas-body {
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
        grid-template-columns: repeat(4, 1fr);
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

    /* Avatar / File Dropzone */
    .avatar-dropzone {
        border: 2px dashed #cbd5e1;
        background-color: #f8fafc;
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .avatar-dropzone:hover,
    .avatar-dropzone.dragover {
        background-color: #f1f5f9;
        border-color: #3b82f6;
    }

    .avatar-dropzone .upload-icon {
        font-size: 2.2rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        transition: transform 0.2s ease;
    }

    .avatar-dropzone:hover .upload-icon {
        transform: translateY(-3px);
        color: #3b82f6;
    }
</style>

<div class="page-wrapper salary-page">
    <div class="content">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-cash-banknote text-primary"></i> Employee Salary Structures
                </h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Payroll</li>
                        <li class="breadcrumb-item active">Salary Structures</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('masters.payroll.calculation-rules') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-calculator fs-15"></i> Working Days Master
                </a>
                <a href="{{ route('payroll.process') }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-player-play fs-15"></i> Create Batch
                </a>
                <a href="{{ route('payroll.increments.index') }}" class="btn btn-outline-warning d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-chart-arrows-vertical fs-15"></i> Annual Increments
                </a>
                <a href="{{ route('masters.organisation.designation') }}" class="btn btn-outline-purple d-inline-flex align-items-center gap-1 shadow-sm" style="color: #7c3aed; border-color: #c4b5fd; background-color: #f5f3ff;">
                    <i class="ti ti-briefcase fs-16"></i> Assign by Designation
                </a>
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkImportSalaryModal">
                    <i class="ti ti-file-spreadsheet fs-16"></i> Bulk Import Salary & PF
                </button>
                <a href="{{ route('masters.payroll.salary-structure.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-plus fs-16"></i> Add / Assign Salary
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-triangle me-1"></i> {!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-1"></i> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-users"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Active Employees</span>
                        <h4 class="fw-bold text-dark mb-0 fs-20">{{ $totalEmployees ?? 0 }}</h4>
                        <span class="fs-11 text-muted">Eligible for payroll</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-check"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Salaries Assigned</span>
                        <h4 class="fw-bold text-success mb-0 fs-20">{{ $configuredCount ?? 0 }}</h4>
                        @php
                            $coveragePct = ($totalEmployees ?? 0) > 0 ? round((($configuredCount ?? 0) / $totalEmployees) * 100) : 0;
                        @endphp
                        <span class="fs-11 text-muted">{{ $coveragePct }}% Headcount Coverage</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-alert-triangle"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Pending Setup</span>
                        <h4 class="fw-bold text-warning mb-0 fs-20">{{ max(0, ($totalEmployees ?? 0) - ($configuredCount ?? 0)) }}</h4>
                        <span class="fs-11 text-muted">Awaiting structure setup</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                        <i class="ti ti-wallet"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-11 fw-semibold text-uppercase letter-spacing">Monthly Gross Budget</span>
                        <h4 class="fw-bold text-dark mb-0 fs-18">&#8377; {{ number_format($totalMonthlyGross ?? 0, 2) }}</h4>
                        <span class="fs-11 text-muted">Annual: &#8377; {{ number_format($totalAnnualCTC ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card p-3 mb-4 border-0 shadow-sm rounded-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Quick Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                        <input type="text" id="salarySearch" class="form-control border-start-0" placeholder="Search by employee name, email, or ID...">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Agency / Deployment</label>
                    <select id="salaryAgencyFilter" class="form-select form-select-sm">
                        <option value="all">All Agencies</option>
                        <option value="unassigned">Direct / In-House</option>
                        @foreach($agencies as $ag)
                            <option value="{{ $ag->id }}">{{ $ag->agency_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-12 fw-semibold text-dark mb-1">Status</label>
                    <select id="salaryStatusFilter" class="form-select form-select-sm">
                        <option value="all">All Statuses</option>
                        <option value="Active" selected>Active Only</option>
                        <option value="Inactive">Inactive Only</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="button" id="btnFilterReset" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1">
                        <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Salary Structures Master Table (Clean, Streamlined Columns) -->
        <div class="card shadow-sm border-0 table-container-card">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0 fw-bold">Configured Salary Structures</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11" id="structureCountBadge">Loading...</span>
                </div>
                <div class="fs-12 text-muted">
                    Click employee name or eye icon to view complete breakdown drawer
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 salary-table w-100" id="salaryTable">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Employee</th>
                                <th>Agency &amp; Role</th>
                                <th class="text-end">Annual CTC</th>
                                <th class="text-end">Monthly Gross</th>
                                <th class="text-end">Est. Net Pay</th>
                                <th class="text-center">Status</th>
                                <th width="120" class="text-center">Actions</th>
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
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="salaryStructureSidebar" aria-labelledby="salaryStructureSidebarLabel">
    <!-- Header -->
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="ti ti-cash-banknote fs-18"></i>
            </span>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark mb-0" id="salaryStructureSidebarLabel">Salary Structure &amp; CTC</h6>
                <small class="text-muted" id="drawerEmpCode">ID: #EMP---</small>
            </div>
        </div>
        <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body -->
    <div class="offcanvas-body" id="salaryStructureBody">
        <!-- Hero Gradient Card (Same as Employee Profile) -->
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
                    <span class="badge bg-success-subtle text-success fs-11 px-2.5 py-1 rounded-pill fw-bold" id="drawerEmpStatus">
                        Active
                    </span>
                </div>
            </div>

            <h4 class="fw-bold text-white mb-1" id="drawerEmpName">Employee Name</h4>
            <div class="text-white-50 fs-13 mb-3 d-flex align-items-center gap-1.5 flex-wrap">
                <span id="drawerEmpEmail"><i class="ti ti-mail me-1"></i>email@example.com</span>
                <span id="drawerEmpMobile" class="ms-2"><i class="ti ti-phone me-1"></i>-</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-opacity-20 text-white border border-white border-opacity-25 fs-11 px-2.5 py-1 rounded-pill" id="drawerEmpDesignation">
                    <i class="ti ti-briefcase me-1"></i>Designation
                </span>
                <span class="badge fs-11 text-white border border-white border-opacity-25 px-2.5 py-1 rounded-pill" id="drawerEmpAgency" style="background-color: rgba(168, 85, 247, 0.45);">
                    <i class="ti ti-building me-1"></i>Agency
                </span>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-15 fs-11 px-2 py-1 rounded-pill" id="drawerEmpPaymentMode">
                    <i class="ti ti-building-bank me-1"></i>Bank Transfer
                </span>
            </div>
        </div>

        <!-- Quick Action Hub -->
        <div class="emp-quick-action-bar">
            <a href="javascript:void(0);" id="drawerQuickEditBtn" class="emp-quick-action-btn" title="Edit Structure">
                <i class="ti ti-edit text-primary"></i>
                <span>Edit</span>
            </a>
            <a href="javascript:void(0);" id="drawerQuickProfileBtn" class="emp-quick-action-btn" title="Employee Directory Profile">
                <i class="ti ti-user text-info"></i>
                <span>Profile</span>
            </a>
            <a href="{{ route('payroll.process') }}" class="emp-quick-action-btn" title="Payroll Batch Processing">
                <i class="ti ti-player-play text-success"></i>
                <span>Run Batch</span>
            </a>
            <a href="javascript:void(0);" onclick="window.print();" class="emp-quick-action-btn" title="Print Details">
                <i class="ti ti-printer text-secondary"></i>
                <span>Print</span>
            </a>
        </div>

        <!-- Slider Content Sections -->
        <div class="emp-slider-section">

            <!-- Highlight Banner Card (CTC & Net) -->
            <div class="emp-slider-card p-3 mb-3" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <span class="fs-10 text-muted text-uppercase fw-semibold d-block">Annual CTC</span>
                        <h6 class="fw-bold text-dark mb-0 fs-14 mt-1" id="drawerCtc">&#8377; 0.00</h6>
                    </div>
                    <div class="col-4 border-start border-end">
                        <span class="fs-10 text-primary text-uppercase fw-semibold d-block">Gross / Mo</span>
                        <h6 class="fw-bold text-primary mb-0 fs-14 mt-1" id="drawerGross">&#8377; 0.00</h6>
                    </div>
                    <div class="col-4">
                        <span class="fs-10 text-success text-uppercase fw-semibold d-block">Take-Home</span>
                        <h6 class="fw-bold text-success mb-0 fs-14 mt-1" id="drawerNet">&#8377; 0.00</h6>
                    </div>
                </div>
            </div>

            <!-- Earnings Components Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-circle-arrow-up text-success fs-15"></i>Earnings Components</span>
                    <span class="badge bg-success-subtle text-success fs-10">Monthly</span>
                </div>

                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-coin text-muted"></i>Basic Salary</span>
                    <span class="emp-data-value fw-semibold" id="drawerBasic">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-home text-muted"></i>HRA (House Rent)</span>
                    <span class="emp-data-value" id="drawerHra">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-car text-muted"></i>Conveyance Allowance</span>
                    <span class="emp-data-value" id="drawerConveyance">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-heart-rate-monitor text-muted"></i>Medical Allowance</span>
                    <span class="emp-data-value" id="drawerMedical">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-star text-muted"></i>Special Allowance</span>
                    <span class="emp-data-value" id="drawerSpecial">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-dots text-muted"></i>Other Allowances</span>
                    <span class="emp-data-value" id="drawerOther">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row bg-primary-subtle rounded px-2 py-1.5 mt-2">
                    <span class="emp-data-label fw-bold text-primary"><i class="ti ti-calculator text-primary"></i>Total Monthly Gross</span>
                    <span class="emp-data-value fw-bold text-primary fs-14" id="drawerTotalGross">&#8377; 0.00</span>
                </div>
            </div>

            <!-- Statutory Deductions Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title d-flex justify-content-between align-items-center">
                    <span><i class="ti ti-circle-arrow-down text-danger fs-15"></i>Statutory Deductions</span>
                    <span class="badge bg-danger-subtle text-danger fs-10">Monthly</span>
                </div>

                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-shield text-muted"></i>Provident Fund (Employee 12%)</span>
                    <span class="emp-data-value text-danger" id="drawerPf">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-first-aid-kit text-muted"></i>ESI (Employee 0.75%)</span>
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
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-building-bank text-muted"></i>Employer PF (12%)</span>
                    <span class="emp-data-value text-muted" id="drawerPfEmployer">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-building-hospital text-muted"></i>Employer ESI (3.25%)</span>
                    <span class="emp-data-value text-muted" id="drawerEsiEmployer">&#8377; 0.00</span>
                </div>
                <div class="emp-data-row bg-danger-subtle rounded px-2 py-1.5 mt-2">
                    <span class="emp-data-label fw-bold text-danger"><i class="ti ti-minus text-danger"></i>Total Monthly Deductions</span>
                    <span class="emp-data-value fw-bold text-danger fs-14" id="drawerTotalDeductions">&#8377; 0.00</span>
                </div>
            </div>

            <!-- Banking & Statutory Identifiers Card -->
            <div class="emp-slider-card">
                <div class="emp-card-title">
                    <i class="ti ti-building-bank text-primary fs-15"></i>Banking &amp; Statutory Identifiers
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
                    <span class="emp-data-label"><i class="ti ti-id text-muted"></i>PAN Number</span>
                    <span class="emp-data-value font-monospace" id="drawerPan">-</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-fingerprint text-muted"></i>UAN Number</span>
                    <span class="emp-data-value font-monospace" id="drawerUan">-</span>
                </div>
                <div class="emp-data-row">
                    <span class="emp-data-label"><i class="ti ti-calendar-event text-muted"></i>Effective From</span>
                    <span class="emp-data-value text-primary fw-semibold" id="drawerEffectiveFrom">-</span>
                </div>
            </div>

        </div>
    </div>
</div>

    <!-- Bulk Import Salary & PF Modal -->
    <div class="modal fade" id="bulkImportSalaryModal" tabindex="-1" aria-labelledby="bulkImportSalaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-light bg-opacity-75 py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-file-spreadsheet fs-22"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="bulkImportSalaryModalLabel">Bulk Import Employees & Salary / PF Structure</h5>
                            <p class="text-muted fs-12 mb-0">Upload Excel (.xlsx, .xls) or CSV (.csv) with employee records, salary CTC breakdown, PF, UAN, and Bank details.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('masters.payroll.salary-structure.import') }}" method="POST" enctype="multipart/form-data" id="bulkImportSalaryForm">
                    @csrf
                    <div class="modal-body p-4">

                        {{-- Step 1: Download Sample File --}}
                        <div class="p-3 mb-4 rounded-3 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fs-11 fw-bold">Step 1</span>
                                <div>
                                    <div class="fw-semibold text-dark fs-13">Download Template with PF & Salary Columns</div>
                                    <div class="text-muted fs-12">Pre-configured template with sample rows for basic gross, high salary, contract staff, and full statutory PF/ESI setups.</div>
                                </div>
                            </div>
                            <a href="{{ route('masters.payroll.salary-structure.import.sample') }}" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center shadow-xs">
                                <i class="ti ti-download me-1.5"></i>Download Template (.csv)
                            </a>
                        </div>

                        {{-- Step 2: Upload File Area --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold text-dark fs-13 mb-0 d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fs-11 fw-bold">Step 2</span>
                                    Select or Drag & Drop File <span class="text-danger">*</span>
                                </label>
                                <span class="text-muted fs-11">Formats: .xlsx, .xls, .csv &bull; Max: 10MB</span>
                            </div>

                            <div class="avatar-dropzone p-4 rounded-3" id="salaryImportDropzone" onclick="document.getElementById('salaryImportFileInput').click();">
                                <div id="salaryImportDropzonePrompt">
                                    <i class="ti ti-cloud-upload upload-icon text-primary d-block mb-2" style="font-size: 2.5rem;"></i>
                                    <h6 class="fw-bold text-dark mb-1">Click to browse or drag file here</h6>
                                    <p class="text-muted fs-12 mb-0">Supported formats: Microsoft Excel (.xlsx, .xls) and Comma-Separated Values (.csv)</p>
                                </div>
                                <div id="salaryImportFilePreview" class="d-none">
                                    <div class="d-flex align-items-center justify-content-center gap-3 py-2">
                                        <i class="ti ti-file-spreadsheet text-success fs-36"></i>
                                        <div class="text-start">
                                            <div class="fw-bold text-dark fs-14" id="salaryImportFileName">file.xlsx</div>
                                            <div class="text-muted fs-12" id="salaryImportFileSize">0 KB</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light text-danger border ms-2" onclick="removeSalaryImportFile(event)">
                                            <i class="ti ti-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                                <input type="file" name="salary_file" id="salaryImportFileInput" class="d-none" accept=".csv, .xlsx, .xls, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="handleSalaryImportFileSelect(this)">
                            </div>
                        </div>

                        {{-- Instructions & Smart Upsert Guidelines --}}
                        <div class="card border rounded-3 bg-light bg-opacity-25 mb-0">
                            <div class="card-header bg-transparent py-2.5 px-3 d-flex align-items-center justify-content-between">
                                <span class="fs-12 fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <i class="ti ti-info-circle text-info"></i> Smart Upsert & Auto-Calculation Highlights
                                </span>
                                <span class="badge bg-success-subtle text-success fs-10">Smart Upsert Enabled</span>
                            </div>
                            <div class="card-body p-3 fs-12 text-muted">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1.5"><i class="ti ti-user-check me-1 text-primary"></i>Employee Matching</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>Matching Order</strong>: Matches existing employee by <em>Employee ID</em>, <em>Email</em>, or <em>Mobile</em>.</li>
                                            <li><strong>Existing Staff</strong>: Updates their record and active salary structure seamlessly.</li>
                                            <li><strong>New Staff</strong>: Automatically registers employee if Name & Email are provided.</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1.5"><i class="ti ti-calculator me-1 text-success"></i>Salary Breakdown</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>Quick Entry</strong>: Provide just <strong>Monthly Gross</strong> — the system automatically computes Basic (50%), HRA (40%), and Special Allowance balance.</li>
                                            <li><strong>Explicit Entry</strong>: You can also specify exact amounts for Basic, HRA, Conveyance, Medical, etc.</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1.5"><i class="ti ti-shield-check me-1 text-warning"></i>PF, ESI & Banking</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>PF & UAN</strong>: Provide PF Number / UAN. PF (12%) is auto-calculated or taken from sheet.</li>
                                            <li><strong>ESI & PAN</strong>: Provide ESI No (0.75% / 3.25%) & PAN Card details.</li>
                                            <li><strong>Bank Info</strong>: Bank Name, Account No, IFSC, and Payment Mode.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light bg-opacity-50 py-3 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitSalaryImportBtn" class="btn btn-primary btn-sm px-4 fw-semibold d-inline-flex align-items-center" disabled>
                            <i class="ti ti-upload me-1.5"></i>Start Bulk Import
                        </button>
                    </div>
                </form>
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

        const table = $('#salaryTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('masters.payroll.salary-structure') }}",
                data: function(d) {
                    d.status = $('#salaryStatusFilter').val();
                    d.agency_id = $('#salaryAgencyFilter').val();
                },
                dataSrc: function(json) {
                    tableDataMap = {};
                    const items = json.data || [];
                    items.forEach(item => {
                        tableDataMap[item.id] = item;
                    });
                    $('#structureCountBadge').text(`${items.length} Records`);
                    return items;
                }
            },
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            dom: "<'row'<'col-12'tr>>" +
                 "<'row align-items-center justify-content-between p-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center'i><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ structures",
                infoEmpty: "Showing 0 to 0 of 0 structures",
                infoFiltered: "(filtered from _MAX_ total)",
                paginate: {
                    first: '<i class="ti ti-chevrons-left"></i>',
                    previous: '<i class="ti ti-chevron-left"></i>',
                    next: '<i class="ti ti-chevron-right"></i>',
                    last: '<i class="ti ti-chevrons-right"></i>'
                },
                emptyTable: "No salary structures configured",
                zeroRecords: "No matching salary structures found"
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => `<span class="badge bg-light text-secondary border font-monospace fs-11">#${meta.row + 1}</span>`
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

                        return `
                            <div class="d-flex align-items-center">
                                ${avatarHtml}
                                <div>
                                    <div class="fw-semibold text-dark fs-13 d-flex align-items-center gap-1">
                                        <a href="javascript:void(0);" onclick="openSalaryStructureDrawer(${row.id})" class="text-dark text-decoration-none hover-primary fw-bold">${name}</a>
                                    </div>
                                    <div class="fs-11 text-muted d-flex align-items-center gap-1.5 mt-0.5">
                                        <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 10px;">ID: #${row.employee_id}</span>
                                        <span>${email}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'designation',
                    render: function(data, type, row) {
                        let agencyBadge = '';
                        if (row.has_agency && row.agency && row.agency !== 'Direct / In-House') {
                            agencyBadge = `
                                <span class="badge border fs-10" style="background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                    <i class="ti ti-building me-1"></i>${row.agency}
                                </span>
                            `;
                        } else {
                            agencyBadge = `<span class="badge bg-light text-muted border fs-10">Direct</span>`;
                        }

                        return `
                            <div>
                                ${agencyBadge}
                                <div class="text-dark fs-11 mt-1 fw-medium">${data || 'Staff'}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'ctc_annual',
                    className: 'text-end fw-bold text-dark fs-13',
                    render: data => '&#8377; ' + (parseFloat(data) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                },
                {
                    data: 'monthly_gross',
                    className: 'text-end fw-bold text-primary fs-13',
                    render: data => '&#8377; ' + (parseFloat(data) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                },
                {
                    data: 'est_net',
                    className: 'text-end fw-bold text-success fs-13',
                    render: data => '&#8377; ' + (parseFloat(data) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function(data) {
                        if (data === 'Active') {
                            return `<span class="badge badge-soft-success px-2 py-1 fs-11"><i class="ti ti-point-filled me-0.5"></i> Active</span>`;
                        }
                        return `<span class="badge badge-soft-secondary px-2 py-1 fs-11">Inactive</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-info" title="View Full Breakdown in Drawer" onclick="openSalaryStructureDrawer(${row.id})">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <a href="${row.edit_url}" class="btn btn-outline-primary" title="Edit Salary Structure">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete Structure" onclick="confirmDeleteSalary(${row.id}, '${row.employee_name.replace(/'/g, "\\'")}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Search Box live search
        $('#salarySearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filters reload
        $('#salaryAgencyFilter, #salaryStatusFilter').on('change', function() {
            table.ajax.reload();
        });

        $('#btnFilterReset').on('click', function() {
            $('#salarySearch').val('');
            $('#salaryAgencyFilter').val('all');
            $('#salaryStatusFilter').val('Active');
            table.search('').ajax.reload();
        });

        // Open Slider Drawer
        window.openSalaryStructureDrawer = function(id) {
            const item = tableDataMap[id];
            if (!item) return;

            let imgUrl = (item.profile_image && item.profile_image !== 'default.png')
                ? `{{ asset('uploads/employees') }}/${item.profile_image}`
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(item.employee_name || 'Staff')}&background=0d6efd&color=fff&size=128&bold=true`;

            $('#drawerEmpImg').attr('src', imgUrl);
            $('#drawerEmpCode').text(`ID: #EMP-${item.employee_id}`);
            $('#drawerEmpIdBadge').text(`#EMP-${item.employee_id}`);
            $('#drawerEmpName').text(item.employee_name || 'Employee');
            $('#drawerEmpEmail').html(`<i class="ti ti-mail me-1"></i>${item.employee_email || '-'}`);
            $('#drawerEmpMobile').html(`<i class="ti ti-phone me-1"></i>${item.employee_mobile || '-'}`);

            // Status
            if (item.status === 'Active') {
                $('#drawerEmpStatus').removeClass('bg-danger-subtle text-danger').addClass('bg-success-subtle text-success').text('Active');
                $('#drawerEmpStatusDot').css('background-color', '#10b981');
            } else {
                $('#drawerEmpStatus').removeClass('bg-success-subtle text-success').addClass('bg-danger-subtle text-danger').text('Inactive');
                $('#drawerEmpStatusDot').css('background-color', '#ef4444');
            }

            // Badges
            $('#drawerEmpDesignation').html(`<i class="ti ti-briefcase me-1"></i>${item.designation || 'Staff'}`);
            $('#drawerEmpAgency').html(`<i class="ti ti-building me-1"></i>${item.agency || 'Direct / In-House'}`);
            $('#drawerEmpPaymentMode').html(`<i class="ti ti-building-bank me-1"></i>${item.payment_mode || 'Bank Transfer'}`);

            // Quick links
            $('#drawerQuickEditBtn').attr('href', item.edit_url);
            $('#drawerQuickProfileBtn').attr('href', `{{ url('employees') }}/${item.employee_id}`);

            // Highlight metrics
            $('#drawerCtc').html(formatINR(item.ctc_annual));
            $('#drawerGross').html(formatINR(item.monthly_gross));
            $('#drawerNet').html(formatINR(item.est_net));

            // Earnings
            $('#drawerBasic').html(formatINR(item.basic_salary));
            $('#drawerHra').html(formatINR(item.hra));
            $('#drawerConveyance').html(formatINR(item.conveyance_allowance));
            $('#drawerMedical').html(formatINR(item.medical_allowance));
            $('#drawerSpecial').html(formatINR(item.special_allowance));
            $('#drawerOther').html(formatINR(item.other_allowance));
            $('#drawerTotalGross').html(formatINR(item.monthly_gross));

            // Deductions
            $('#drawerPf').html(formatINR(item.pf_employee));
            $('#drawerEsi').html(formatINR(item.esi_employee));
            $('#drawerPt').html(formatINR(item.professional_tax));
            $('#drawerTds').html(formatINR(item.tds_monthly));
            $('#drawerPfEmployer').html(formatINR(item.pf_employer));
            $('#drawerEsiEmployer').html(formatINR(item.esi_employer));
            $('#drawerTotalDeductions').html(formatINR(item.total_deductions));

            // Banking & Identifiers
            $('#drawerBankName').text(item.bank_name || '-');
            $('#drawerBankAcc').text(item.bank_account_no || '-');
            $('#drawerIfsc').text(item.ifsc_code || '-');
            $('#drawerPan').text(item.pan_no || '-');
            $('#drawerUan').text(item.uan_no || '-');
            $('#drawerEffectiveFrom').text(item.effective_from || '-');

            const offcanvasEl = document.getElementById('salaryStructureSidebar');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        };

        // Delete with SweetAlert & AJAX reload
        window.confirmDeleteSalary = function(id, name) {
            Swal.fire({
                title: 'Delete Salary Structure?',
                text: `Are you sure you want to remove the salary structure for "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('masters/payroll/salary-structure') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message || 'Salary structure has been deleted.', 'success');
                            table.ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Error', 'Unable to delete salary structure.', 'error');
                        }
                    });
                }
            });
        };

        // Backward compatibility
        window.openSalaryBreakdownOffcanvas = function(item, emp) {
            if (item && item.id) {
                openSalaryStructureDrawer(item.id);
            }
        };
        window.openSalaryBreakdownModal = window.openSalaryBreakdownOffcanvas;

        // Bulk Import Salary Dropzone Preview & Logic
        window.handleSalaryImportFileSelect = function(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire('File Too Large', 'File size exceeds the 10 MB limit.', 'warning');
                    input.value = '';
                    return;
                }

                $('#salaryImportFileName').text(file.name);
                $('#salaryImportFileSize').text((file.size / 1024).toFixed(1) + ' KB');
                $('#salaryImportDropzonePrompt').addClass('d-none');
                $('#salaryImportFilePreview').removeClass('d-none');
                $('#submitSalaryImportBtn').prop('disabled', false);
            }
        };

        window.removeSalaryImportFile = function(e) {
            if (e) e.stopPropagation();
            $('#salaryImportFileInput').val('');
            $('#salaryImportFilePreview').addClass('d-none');
            $('#salaryImportDropzonePrompt').removeClass('d-none');
            $('#submitSalaryImportBtn').prop('disabled', true);
        };

        $('#bulkImportSalaryForm').on('submit', function() {
            const btn = $('#submitSalaryImportBtn');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span>Importing Employees & Salaries...');
        });

        const salaryImportDropzone = document.getElementById('salaryImportDropzone');
        if (salaryImportDropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                salaryImportDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    salaryImportDropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                salaryImportDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    salaryImportDropzone.classList.remove('dragover');
                }, false);
            });

            salaryImportDropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length) {
                    const input = document.getElementById('salaryImportFileInput');
                    input.files = files;
                    window.handleSalaryImportFileSelect(input);
                }
            }, false);
        }
    });
</script>
@endpush
