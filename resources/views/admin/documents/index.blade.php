@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    /* Scope styles for document vault page */
    .doc-vault-page {
        color: #1e293b;
    }

    /* KPI Summary Cards */
    .doc-vault-page .stat-card {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .doc-vault-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .doc-vault-page .stat-icon {
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

    .stat-icon-amber {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }

    .stat-icon-rose {
        background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%);
        color: #e11d48;
    }

    /* Filter Card */
    .doc-vault-page .filter-card {
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
    .doc-vault-page .table-container-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .doc-table {
        margin-bottom: 0 !important;
    }

    .doc-table th {
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

    .doc-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .doc-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Custom Badges */
    .badge-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #e2e8f0;
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

    .badge-soft-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-soft-blue {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .badge-soft-purple {
        background-color: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .badge-file-ext {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 3px 6px;
        border-radius: 4px;
    }

    .badge-ext-pdf {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    .badge-ext-img {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #93c5fd;
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

    .btn-action-icon.btn-verify:hover {
        background: #f0fdf4;
        color: #16a34a;
        border-color: #bbf7d0;
    }

    .btn-action-icon.btn-download:hover {
        background: #f5f3ff;
        color: #7c3aed;
        border-color: #ddd6fe;
    }

    .btn-action-icon.btn-delete:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* Links with primary hover */
    .hover-primary {
        transition: color 0.15s ease;
    }
    .hover-primary:hover {
        color: #4338ca !important;
        text-decoration: underline !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* DataTables Pagination & Info */
    .doc-vault-page .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_length {
        margin: 0 !important;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_length label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 0;
        font-weight: 500;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 24px 4px 10px;
        font-size: 12px;
        color: #334155;
        background-color: #ffffff;
    }

    /* Supports both DataTables standard & Bootstrap 5 pagination */
    .doc-vault-page .dataTables_wrapper .dataTables_paginate,
    .doc-vault-page .dataTables_wrapper .dataTables_paginate ul.pagination {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_paginate .paginate_button,
    .doc-vault-page .dataTables_wrapper .pagination .page-item .page-link {
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
        line-height: 1 !important;
        box-shadow: none !important;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled),
    .doc-vault-page .dataTables_wrapper .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .doc-vault-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
    .doc-vault-page .dataTables_wrapper .pagination .page-item.active .page-link {
        background: #4338ca !important;
        border-color: #4338ca !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(67, 56, 202, 0.28) !important;
    }

    .doc-vault-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .doc-vault-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
    .doc-vault-page .dataTables_wrapper .pagination .page-item.disabled .page-link {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Offcanvas Slider Drawers */
    #documentDrawer,
    #documentVerificationDrawer {
        width: 520px !important;
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

    .doc-drawer-hero {
        background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
        position: relative;
        padding: 24px 24px 20px;
        color: #ffffff;
        overflow: hidden;
    }

    .doc-drawer-hero::before {
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

    .doc-drawer-hero .drawer-badge {
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

    /* File drop upload styling */
    .file-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-upload-zone:hover {
        border-color: #6366f1;
        background: #f5f7ff;
    }

    .doc-preview-container {
        min-height: 180px;
        max-height: 280px;
        overflow: hidden;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .doc-preview-img {
        max-width: 100%;
        max-height: 260px;
        object-fit: contain;
        transition: transform 0.2s ease;
    }

    .group-preview:hover .doc-preview-img {
        transform: scale(1.02);
    }

    .preview-zoom-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(15, 23, 42, 0.82);
        color: #ffffff;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        backdrop-filter: blur(4px);
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }

    .group-preview:hover .preview-zoom-badge {
        background: #4338ca;
    }
</style>

<div class="page-wrapper doc-vault-page">
    <div class="content container-fluid py-4">

        <!-- Page Header & Actions -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-indigo text-white px-2 py-1 fs-11 rounded">HR Lifecycle</span>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">Employee Document Vault &amp; KYC Verification</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3" id="btnRefreshTable">
                    <i class="ti ti-rotate-clockwise" id="refreshIcon"></i> Refresh
                </button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2 rounded-3 px-3">
                    <i class="ti ti-users"></i> Employee Directory
                </a>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 shadow-sm" id="btnOpenUploadDrawer">
                    <i class="ti ti-upload fs-16"></i> Upload Document
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
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Active Staff Documents</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1" id="kpiTotalDocs">{{ $totalDocuments }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">Credentials of active staff</small>
                        </div>
                        <div class="stat-icon stat-icon-indigo">
                            <i class="ti ti-files"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Verified &amp; Approved</span>
                            <h3 class="fw-bold mb-0 text-success mt-1" id="kpiApprovedDocs">{{ $approvedDocuments }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">KYC approved by HR</small>
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
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Pending Review</span>
                            <h3 class="fw-bold mb-0 text-warning mt-1" id="kpiPendingDocs">{{ $pendingDocuments }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">Awaiting HR verification</small>
                        </div>
                        <div class="stat-icon stat-icon-amber">
                            <i class="ti ti-clock-hour-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase letter-spacing-1">Rejected / Resubmit</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1" id="kpiRejectedDocs">{{ $rejectedDocuments }}</h3>
                            <small class="text-muted fs-11 mt-1 d-block">Requires document re-upload</small>
                        </div>
                        <div class="stat-icon stat-icon-rose">
                            <i class="ti ti-alert-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card filter-card mb-4 p-3">
            <div class="row g-2 align-items-center">
                <div class="col-xl-3 col-md-4 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <i class="ti ti-search fs-14"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-1" id="filterSearch"
                               placeholder="Search employee, doc title, number...">
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-sm-6">
                    <select class="form-select" id="filterEmployeeStatus" title="Employee Status Filter">
                        <option value="active" selected>Active Staff (Default)</option>
                        <option value="inactive">Inactive Staff</option>
                        <option value="all">All Staff (Active &amp; Inactive)</option>
                    </select>
                </div>

                <div class="col-xl-3 col-md-4 col-sm-6">
                    <select class="form-select" id="filterEmployee">
                        <option value="all">All Employees</option>
                        <optgroup label="Active Employees ({{ $activeEmployees->count() }})">
                            @foreach($activeEmployees as $emp)
                                <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} (#EMP-{{ $emp->employee_id }})</option>
                            @endforeach
                        </optgroup>
                        @if($inactiveEmployees->count() > 0)
                            <optgroup label="Inactive Staff ({{ $inactiveEmployees->count() }})">
                                @foreach($inactiveEmployees as $emp)
                                    <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} (#EMP-{{ $emp->employee_id }}) [Inactive]</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <div class="col-xl-2 col-md-4 col-sm-6">
                    <select class="form-select" id="filterDocType">
                        <option value="all">All Document Types</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-1 col-md-4 col-sm-6">
                    <select class="form-select" id="filterStatus">
                        <option value="all">All Status</option>
                        <option value="Approved">Approved</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-xl-1 col-md-4 col-sm-6 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3 w-100 justify-content-center" id="btnResetFilters" title="Reset Filters to Default">
                        <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container Card -->
        <div class="card table-container-card mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Employee Document Register</h5>
                    <p class="text-muted fs-12 mb-0 mt-1">Centralized verification repository for government ID proofs, degrees, bank cheques &amp; experience letters</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-2 py-1 fs-12" id="filteredCountBadge">
                        Displaying active employee documents
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table doc-table align-middle" id="documentsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">#</th>
                            <th>Employee Details</th>
                            <th>Document &amp; Identifier</th>
                            <th>File &amp; Size</th>
                            <th>Validity Period</th>
                            <th class="text-center" style="width: 110px;">Verification</th>
                            <th class="text-center" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded dynamically via AJAX DataTables -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <x-footer />
</div>

<!-- ==========================================
     OFFCANVAS: Upload New Document Drawer
=========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="documentDrawer" aria-labelledby="documentDrawerLabel" data-bs-backdrop="true">
    <div class="doc-drawer-hero d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="drawer-badge">
                <i class="ti ti-file-upload fs-13"></i> Vault Upload
            </span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div>
            <h4 class="fw-bold mb-1 text-white">Upload Employee Document</h4>
            <p class="text-white-50 fs-12 mb-0">Attach verified KYC proofs, academic certificates, or previous service records</p>
        </div>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body p-4 custom-scrollbar">
        <form id="uploadDocForm" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Employee Selection -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Select Employee <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm rounded-2" id="inputEmployeeId" name="employee_id" required>
                    <option value="">-- Choose Employee --</option>
                    <optgroup label="Active Employees ({{ $activeEmployees->count() }})">
                        @foreach($activeEmployees as $emp)
                            <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} (#EMP-{{ $emp->employee_id }})</option>
                        @endforeach
                    </optgroup>
                    @if($inactiveEmployees->count() > 0)
                        <optgroup label="Inactive Staff ({{ $inactiveEmployees->count() }})">
                            @foreach($inactiveEmployees as $emp)
                                <option value="{{ $emp->employee_id }}">{{ $emp->employee_name }} (#EMP-{{ $emp->employee_id }}) [Inactive]</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                <div class="invalid-feedback fs-11">Please select an employee.</div>
            </div>

            <!-- Document Category / Type -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Document Type <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm rounded-2" id="inputDocType" name="document_type" required>
                    <option value="">-- Select Category --</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback fs-11">Please select a document category.</div>
            </div>

            <!-- Document Title / Name -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Document Title / Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm rounded-2" id="inputDocName" name="document_name"
                       placeholder="e.g. Government Identity - Aadhaar Card" required maxlength="191">
                <div class="invalid-feedback fs-11">Document title is required.</div>
            </div>

            <!-- Document Identifier / Number -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Document / ID Number (Optional)</label>
                <input type="text" class="form-control form-control-sm rounded-2 font-monospace" id="inputDocNumber" name="document_number"
                       placeholder="e.g. XXXX-XXXX-1234 or PAN ABCDE1234F" maxlength="100">
                <small class="text-muted fs-11">Unique identifier number printed on the credential.</small>
            </div>

            <!-- File Upload Input -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Attach Document File <span class="text-danger">*</span></label>
                <input type="file" class="form-control form-control-sm rounded-2" id="inputDocFile" name="document_file"
                       accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                <small class="text-muted fs-11">Supported formats: PDF, JPG, PNG, WEBP (Max: 10MB)</small>
                <div class="invalid-feedback fs-11">Please select a file to upload.</div>
            </div>

            <!-- Issue & Expiry Dates -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fs-12 fw-semibold text-dark">Issue Date</label>
                    <input type="date" class="form-control form-control-sm rounded-2" id="inputIssueDate" name="issue_date">
                </div>
                <div class="col-6">
                    <label class="form-label fs-12 fw-semibold text-dark">Expiry Date</label>
                    <input type="date" class="form-control form-control-sm rounded-2" id="inputExpiryDate" name="expiry_date">
                    <small class="text-muted fs-11">Required for Passport/Visa</small>
                </div>
            </div>

            <!-- Initial Verification Status -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Verification Status <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="verification_status" id="uploadStatusApproved" value="Approved" checked>
                        <label class="form-check-label fs-13" for="uploadStatusApproved">
                            <span class="badge badge-soft-success">Approved / Verified</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="verification_status" id="uploadStatusPending" value="Pending">
                        <label class="form-check-label fs-13" for="uploadStatusPending">
                            <span class="badge badge-soft-warning">Pending Review</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <label class="form-label fs-12 fw-semibold text-dark">Verification Remarks &amp; Notes</label>
                <textarea class="form-control form-control-sm rounded-2" id="inputNotes" name="notes" rows="2"
                          placeholder="Add any verification observations, portal reference numbers, or compliance notes..." maxlength="1000"></textarea>
            </div>
        </form>
    </div>

    <!-- Drawer Footer -->
    <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2" data-bs-dismiss="offcanvas">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm px-4 rounded-2 d-flex align-items-center gap-2 shadow-sm" id="btnSaveDocument">
            <i class="ti ti-upload"></i>
            <span id="btnSaveText">Upload Document</span>
        </button>
    </div>
</div>

<!-- ==========================================
     OFFCANVAS: Document Review & Verification Drawer
=========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="documentVerificationDrawer" aria-labelledby="documentVerificationDrawerLabel" data-bs-backdrop="true">
    <div class="doc-drawer-hero d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="drawer-badge" id="reviewDocTypeBadge">
                <i class="ti ti-certificate fs-13"></i> KYC Verification
            </span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div>
            <h4 class="fw-bold mb-1 text-white" id="reviewDocName">Document Name</h4>
            <p class="text-white-50 fs-12 mb-2" id="reviewDrawerHeroSubtitle">Document Details &amp; KYC Verification</p>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="badge bg-white text-dark font-monospace" id="reviewDocNumber">NUMBER</span>
                <span class="badge" id="reviewStatusBadge">Approved</span>
            </div>
        </div>
    </div>

    <div class="offcanvas-body p-4 custom-scrollbar">

        <!-- Employee Info Card -->
        <div class="p-3 bg-light rounded-3 border mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md rounded-circle bg-white border d-flex align-items-center justify-content-center text-primary fs-18" style="width: 44px; height: 44px;">
                    <i class="ti ti-user"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0" id="reviewEmpName">Employee Name</h6>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-secondary-subtle text-secondary font-monospace fs-11" id="reviewEmpCode">#EMP-0</span>
                        <span class="text-muted fs-12" id="reviewEmpDesignation">Designation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Details & Preview Box -->
        <div class="p-3 bg-white rounded-3 border mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fs-12 fw-semibold text-dark"><i class="ti ti-paperclip text-primary me-1"></i> Attached Credential</span>
                <a href="#" id="reviewDownloadBtn" class="btn btn-outline-primary btn-sm px-2 py-1 fs-11" target="_blank">
                    <i class="ti ti-download me-1"></i> Download File
                </a>
            </div>

            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light border mb-2">
                <span class="badge bg-dark text-white font-monospace" id="reviewFileExt">PDF</span>
                <span class="text-truncate fs-12 fw-medium text-dark flex-grow-1" id="reviewFileName">document.pdf</span>
                <span class="text-muted fs-11" id="reviewFileSize">1.2 MB</span>
            </div>

            <!-- Preview Container -->
            <div class="doc-preview-container p-2" id="reviewPreviewContainer">
                <!-- Dynamically rendered image or pdf icon -->
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="p-2 border rounded bg-light">
                    <span class="text-muted fs-11 d-block">Issue Date</span>
                    <span class="fw-semibold fs-12 text-dark" id="reviewIssueDate">-</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 border rounded bg-light">
                    <span class="text-muted fs-11 d-block">Expiry Date</span>
                    <span class="fw-semibold fs-12 text-dark" id="reviewExpiryDate">-</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 border rounded bg-light">
                    <span class="text-muted fs-11 d-block">Uploaded Date</span>
                    <span class="fw-semibold fs-12 text-dark" id="reviewUploadedDate">-</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 border rounded bg-light">
                    <span class="text-muted fs-11 d-block">Verified By</span>
                    <span class="fw-semibold fs-12 text-dark" id="reviewVerifiedBy">-</span>
                </div>
            </div>
        </div>

        <!-- Remarks / Notes Card -->
        <div class="p-3 bg-light rounded-3 border mb-3">
            <span class="text-muted fs-11 d-block fw-semibold text-uppercase mb-1"><i class="ti ti-notes me-1"></i> Remarks / Notes</span>
            <p class="fs-12 text-dark mb-0" id="reviewDocNotes">No additional notes recorded.</p>
        </div>

        <!-- Rejection Alert (if rejected) -->
        <div class="alert alert-danger border rounded-3 p-3 mb-3 d-none" id="reviewRejectionBox">
            <h6 class="fw-bold mb-1 fs-12 text-danger"><i class="ti ti-alert-triangle me-1"></i> Rejection Reason</h6>
            <p class="fs-12 mb-0" id="reviewRejectionReason">-</p>
        </div>

        <!-- HR Verification Action Box -->
        <div class="card border border-primary-subtle bg-light p-3 rounded-3 mb-2" id="hrDecisionCard">
            <label class="fs-12 fw-bold text-dark text-uppercase letter-spacing-1 mb-2">
                <i class="ti ti-stamp text-primary me-1"></i> HR Verification Decision
            </label>

            <!-- Rejection Input Area (Toggleable) -->
            <div class="mb-3 d-none" id="rejectionInputArea">
                <label class="form-label fs-11 fw-semibold text-danger">Specify Rejection Reason <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-sm rounded-2" id="inputRejectionReason" rows="2"
                          placeholder="e.g. Document image is illegible or name does not match official employee records..."></textarea>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-success btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1 shadow-sm" id="btnApproveDoc">
                    <i class="ti ti-check"></i> Approve &amp; Verify
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1" id="btnRejectToggle">
                    <i class="ti ti-x"></i> Reject Document
                </button>
            </div>
        </div>

    </div>

    <!-- Drawer Footer -->
    <div class="offcanvas-footer p-3 border-top bg-light d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2" data-bs-dismiss="offcanvas">Close</button>
        <button type="button" class="btn btn-outline-warning btn-sm px-3 rounded-2" id="btnResetToPending">
            <i class="ti ti-rotate-clockwise me-1"></i> Reset to Pending
        </button>
    </div>
</div>

<!-- ==========================================
     MODAL: Document Preview Lightbox Modal
=========================================== -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-2 py-1 fs-11 rounded" id="previewModalDocType">KYC Proof</span>
                    <h5 class="modal-title fw-bold text-white mb-0 fs-15" id="previewModalTitle">Document Preview</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="previewModalDownloadBtn" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 rounded-2" target="_blank">
                        <i class="ti ti-download fs-14"></i> Download
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 text-center bg-dark bg-opacity-95 position-relative" style="min-height: 380px; max-height: 80vh; overflow: auto;">
                <div id="previewModalContent" class="p-3 d-flex align-items-center justify-content-center" style="min-height: 380px;">
                    <!-- Dynamically populated -->
                </div>
            </div>
            <div class="modal-footer bg-white py-2 px-4 d-flex justify-content-between align-items-center border-top">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12 font-monospace" id="previewModalMetaInfo">filename.pdf • 1.2 MB</span>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-2" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL: Delete Document Confirmation
=========================================== -->
<div class="modal fade" id="deleteDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="stat-icon stat-icon-rose mx-auto mb-3" style="width: 56px; height: 56px; font-size: 26px;">
                    <i class="ti ti-trash"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Delete Document?</h5>
                <p class="text-muted fs-12 mb-3">
                    Are you sure you want to remove <strong id="deleteDocName" class="text-dark"></strong> from the employee's vault? The attached file will be permanently deleted.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3 rounded-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3 rounded-2" id="btnConfirmDeleteDoc">Delete File</button>
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

    let currentReviewDocId = null;
    let currentDeleteDocId = null;

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

    // Helper: format status badge
    function getStatusBadge(status, reason) {
        switch (status) {
            case 'Approved':
                return '<span class="badge badge-soft-success"><i class="ti ti-check me-1"></i>Approved</span>';
            case 'Pending':
                return '<span class="badge badge-soft-warning"><i class="ti ti-clock me-1"></i>Pending Review</span>';
            case 'Rejected':
                const tooltip = reason ? ` title="${reason.replace(/"/g, '&quot;')}" data-bs-toggle="tooltip"` : '';
                return `<span class="badge badge-soft-danger"${tooltip}><i class="ti ti-alert-circle me-1"></i>Rejected</span>`;
            default:
                return '<span class="badge badge-soft-secondary">' + status + '</span>';
        }
    }

    // Initialize DataTable with AJAX
    const table = $('#documentsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('employees.documents.index') }}",
            type: "GET",
            data: function(d) {
                d.employee_status = $('#filterEmployeeStatus').val() || 'active';
                d.status = $('#filterStatus').val();
                d.employee_id = $('#filterEmployee').val();
                d.document_type = $('#filterDocType').val();
                d.search = $('#filterSearch').val();
            },
            dataSrc: function(json) {
                if (json && json.data) {
                    const st = $('#filterEmployeeStatus').val() || 'active';
                    let suffix = ' (Active Staff)';
                    if (st === 'inactive') suffix = ' (Inactive Staff)';
                    else if (st === 'all') suffix = ' (All Staff)';
                    $('#filteredCountBadge').text('Displaying ' + json.data.length + ' documents' + suffix);
                    return json.data;
                }
                return [];
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center text-muted fw-medium',
                orderable: false,
                render: function(data, type, row, meta) {
                    return `<span class="badge bg-light text-secondary border font-monospace fs-11">#${meta.row + 1}</span>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center gap-2.5">
                            <img src="${row.avatar}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;" alt="${row.employee_name}">
                            <div class="d-flex flex-column">
                                <a href="{{ url('employees') }}/${row.employee_id}" class="fw-bold text-dark fs-13 text-decoration-none hover-primary">
                                    ${row.employee_name}
                                </a>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                    <span class="badge-code">${row.employee_code}</span>
                                    <span class="text-muted fs-11">• ${row.designation}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex flex-column">
                            <span class="badge badge-soft-blue mb-1 align-self-start">${row.document_type}</span>
                            <a href="javascript:void(0);" onclick="viewDocumentDetails(${row.id})"
                               class="fw-semibold text-dark fs-12 text-decoration-none hover-primary text-truncate"
                               style="max-width: 220px;" title="${row.document_name}">
                                ${row.document_name}
                            </a>
                            ${row.document_number !== '-' ? `<span class="text-muted fs-11 font-monospace mt-0.5">${row.document_number}</span>` : ''}
                        </div>
                    `;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    const extBadge = row.is_image ? 'badge-ext-img' : 'badge-ext-pdf';
                    return `
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-file-ext ${extBadge}">${row.file_extension}</span>
                            <div class="d-flex flex-column">
                                <a href="javascript:void(0);" onclick="viewDocumentDetails(${row.id})"
                                   class="fs-12 text-dark text-truncate text-decoration-none hover-primary"
                                   style="max-width: 140px;" title="${row.file_name}">
                                    ${row.file_name}
                                </a>
                                <span class="text-muted fs-11">${row.file_size}</span>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (!row.issue_date && !row.expiry_date) {
                        return '<span class="text-muted fs-11">No Expiry</span>';
                    }
                    let out = '<div class="d-flex flex-column">';
                    if (row.issue_date) {
                        out += `<span class="text-muted fs-11">Issued: ${row.issue_date}</span>`;
                    }
                    if (row.expiry_date) {
                        const expiredClass = row.is_expired ? 'text-danger fw-bold' : 'text-dark';
                        out += `<span class="fs-11 ${expiredClass}">Expires: ${row.expiry_date}</span>`;
                    }
                    out += '</div>';
                    return out;
                }
            },
            {
                data: 'verification_status',
                className: 'text-center',
                render: function(data, type, row) {
                    return getStatusBadge(data, row.rejection_reason);
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn-action-icon btn-view" title="View Document Details & Preview"
                                    onclick="viewDocumentDetails(${row.id})">
                                <i class="ti ti-eye"></i>
                            </button>
                           
                            <a href="${row.download_url}" class="btn-action-icon btn-download" title="Download Document" target="_blank">
                                <i class="ti ti-download"></i>
                            </a>
                            <button type="button" class="btn-action-icon btn-delete" title="Delete Document"
                                    onclick="confirmDeleteDoc(${row.id}, '${row.document_name.replace(/'/g, "\\'")}')">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pagingType: 'full_numbers',
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        dom: "<'table-responsive'tr>" +
             "<'row align-items-center justify-content-between px-3 py-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center gap-3'li><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
        order: [[0, 'asc']],
        language: {
            lengthMenu: "Show _MENU_ documents",
            info: "Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> documents",
            infoEmpty: "Showing 0 to 0 of 0 documents",
            infoFiltered: "(filtered from _MAX_ total)",
            paginate: {
                first: '<i class="ti ti-chevrons-left"></i>',
                previous: '<i class="ti ti-chevron-left"></i>',
                next: '<i class="ti ti-chevron-right"></i>',
                last: '<i class="ti ti-chevrons-right"></i>'
            },
            emptyTable: `
                <div class="text-center py-5">
                    <div class="stat-icon stat-icon-indigo mx-auto mb-3" style="width: 56px; height: 56px; font-size: 26px;">
                        <i class="ti ti-folder-off"></i>
                    </div>
                    <h6 class="fw-bold text-dark">No Employee Documents Found</h6>
                    <p class="text-muted fs-12 mb-0">Try clearing your filters or upload a new credential using the button above.</p>
                </div>
            `,
            zeroRecords: `
                <div class="text-center py-5">
                    <div class="stat-icon stat-icon-indigo mx-auto mb-3" style="width: 56px; height: 56px; font-size: 26px;">
                        <i class="ti ti-search-off"></i>
                    </div>
                    <h6 class="fw-bold text-dark">No Matching Documents</h6>
                    <p class="text-muted fs-12 mb-0">No documents match your filter criteria.</p>
                </div>
            `,
            processing: `
                <div class="d-flex align-items-center justify-content-center py-4">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    <span class="fs-13 text-muted">Loading employee vault documents...</span>
                </div>
            `
        },
        drawCallback: function(settings) {
            // Re-init tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Filter Listeners with Debounce for search
    let searchTimeout = null;
    $('#filterSearch').on('keyup input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 300);
    });

    $('#filterEmployeeStatus, #filterEmployee, #filterDocType, #filterStatus').on('change', function() {
        table.ajax.reload();
        updateKpis();
    });

    $('#btnResetFilters').on('click', function() {
        $('#filterSearch').val('');
        $('#filterEmployeeStatus').val('active');
        $('#filterEmployee').val('all');
        $('#filterDocType').val('all');
        $('#filterStatus').val('all');
        table.ajax.reload();
        updateKpis();
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
        updateKpis();
    });

    // KPI update helper
    function updateKpis() {
        $.ajax({
            url: "{{ route('employees.documents.index') }}",
            type: "GET",
            data: {
                employee_status: $('#filterEmployeeStatus').val() || 'active',
                employee_id: $('#filterEmployee').val(),
                document_type: $('#filterDocType').val(),
                status: $('#filterStatus').val()
            },
            dataType: "json",
            success: function(res) {
                if (res.data) {
                    const total = res.data.length;
                    const approved = res.data.filter(r => r.verification_status === 'Approved').length;
                    const pending = res.data.filter(r => r.verification_status === 'Pending').length;
                    const rejected = res.data.filter(r => r.verification_status === 'Rejected').length;

                    $('#kpiTotalDocs').text(total);
                    $('#kpiApprovedDocs').text(approved);
                    $('#kpiPendingDocs').text(pending);
                    $('#kpiRejectedDocs').text(rejected);
                }
            }
        });
    }

    // Open Upload Drawer
    const uploadDrawerEl = document.getElementById('documentDrawer');
    const uploadDrawerInstance = new bootstrap.Offcanvas(uploadDrawerEl);

    $('#btnOpenUploadDrawer').on('click', function() {
        $('#uploadDocForm')[0].reset();
        $('#uploadDocForm').removeClass('was-validated');
        $('#uploadStatusApproved').prop('checked', true);
        uploadDrawerInstance.show();
    });

    // Save/Upload Document Handler
    $('#btnSaveDocument').on('click', function() {
        const form = document.getElementById('uploadDocForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const btn = $(this);
        const originalText = $('#btnSaveText').text();
        btn.prop('disabled', true);
        $('#btnSaveText').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading...');

        const formData = new FormData(form);

        $.ajax({
            url: "{{ route('employees.documents.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false);
                $('#btnSaveText').text(originalText);

                if (response.success) {
                    showToast(response.message, 'success');
                    uploadDrawerInstance.hide();
                    table.ajax.reload();
                    updateKpis();
                } else {
                    showToast(response.message || 'Error uploading document', 'error');
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

    // Lightbox modal instance
    const previewModalEl = document.getElementById('documentPreviewModal');
    const previewModalInstance = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;

    window.openLightboxModal = function(fileUrl, docName, docType, fileName, fileSize, isImage) {
        $('#previewModalTitle').text(docName);
        $('#previewModalDocType').text(docType);
        $('#previewModalDownloadBtn').attr('href', fileUrl);
        $('#previewModalMetaInfo').text(`${fileName} • ${fileSize}`);

        if (isImage) {
            $('#previewModalContent').html(`
                <img src="${fileUrl}" alt="${docName}" class="img-fluid rounded shadow" style="max-height: 72vh; object-fit: contain;">
            `);
        } else {
            $('#previewModalContent').html(`
                <iframe src="${fileUrl}" style="width: 100%; height: 72vh; border: none; border-radius: 8px; background: #fff;"></iframe>
            `);
        }

        if (previewModalInstance) {
            previewModalInstance.show();
        }
    };

    // Open Review & Verification Drawer
    const reviewDrawerEl = document.getElementById('documentVerificationDrawer');
    const reviewDrawerInstance = new bootstrap.Offcanvas(reviewDrawerEl);

    window.openDocumentDrawer = function(id, mode = 'view') {
        currentReviewDocId = id;
        $('#rejectionInputArea').addClass('d-none');
        $('#inputRejectionReason').val('');

        $.ajax({
            url: `{{ url('employees/documents') }}/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.document) {
                    const d = res.document;

                    if (mode === 'verify') {
                        $('#reviewDocTypeBadge').html('<i class="ti ti-stamp fs-13"></i> KYC Verification Review');
                        $('#reviewDrawerHeroSubtitle').text('Review compliance proofs and update verification decision');
                    } else {
                        $('#reviewDocTypeBadge').html('<i class="ti ti-eye fs-13"></i> Document Viewer & Preview');
                        $('#reviewDrawerHeroSubtitle').text('Inspect document details, attached file & verification status');
                    }

                    $('#reviewDocName').text(d.document_name);
                    $('#reviewDocNumber').text(d.document_number);

                    let statusClass = 'badge-soft-warning';
                    if (d.verification_status === 'Approved') statusClass = 'badge-soft-success';
                    if (d.verification_status === 'Rejected') statusClass = 'badge-soft-danger';
                    $('#reviewStatusBadge').attr('class', 'badge ' + statusClass).text(d.verification_status);

                    $('#reviewEmpName').text(d.employee_name);
                    $('#reviewEmpCode').text(d.employee_code);
                    $('#reviewEmpDesignation').text(d.designation);

                    $('#reviewFileExt').text(d.file_extension);
                    $('#reviewFileName').text(d.file_name);
                    $('#reviewFileSize').text(d.file_size);
                    $('#reviewDownloadBtn').attr('href', d.download_url);

                    $('#reviewIssueDate').text(d.issue_date);
                    $('#reviewExpiryDate').text(d.expiry_date);
                    $('#reviewUploadedDate').text(d.uploaded_at);
                    $('#reviewVerifiedBy').text(d.verifier_name || 'Pending Review');
                    $('#reviewDocNotes').text(d.notes || 'No additional notes recorded.');

                    // Preview Box content
                    if (d.is_image) {
                        $('#reviewPreviewContainer').html(`
                            <div class="position-relative w-100 text-center cursor-pointer group-preview"
                                 onclick="openLightboxModal('${d.file_url}', '${d.document_name.replace(/'/g, "\\'")}', '${d.document_type}', '${d.file_name}', '${d.file_size}', true)"
                                 title="Click to view full image in lightbox">
                                <img src="${d.file_url}" alt="${d.document_name}" class="doc-preview-img shadow-sm rounded">
                                <div class="preview-zoom-badge">
                                    <i class="ti ti-zoom-in me-1"></i> Full View
                                </div>
                            </div>
                        `);
                    } else {
                        $('#reviewPreviewContainer').html(`
                            <div class="text-center py-4 w-100">
                                <div class="avatar avatar-lg rounded-circle bg-danger-subtle text-danger mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 24px;">
                                    <i class="ti ti-file-text"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">${d.file_name}</h6>
                                <p class="text-muted fs-11 mb-3">Portable Document Format (PDF)</p>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-primary px-3 rounded-2"
                                            onclick="openLightboxModal('${d.file_url}', '${d.document_name.replace(/'/g, "\\'")}', '${d.document_type}', '${d.file_name}', '${d.file_size}', false)">
                                        <i class="ti ti-eye me-1"></i> Preview PDF
                                    </button>
                                    <a href="${d.download_url}" target="_blank" class="btn btn-sm btn-outline-secondary px-3 rounded-2">
                                        <i class="ti ti-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        `);
                    }

                    // Rejection alert box
                    if (d.verification_status === 'Rejected' && d.rejection_reason) {
                        $('#reviewRejectionReason').text(d.rejection_reason);
                        $('#reviewRejectionBox').removeClass('d-none');
                    } else {
                        $('#reviewRejectionBox').addClass('d-none');
                    }

                    reviewDrawerInstance.show();

                    if (mode === 'verify') {
                        setTimeout(() => {
                            $('#hrDecisionCard')[0]?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 300);
                    }
                }
            },
            error: function() {
                showToast('Failed to load document review data', 'error');
            }
        });
    };

    window.viewDocumentDetails = function(id) {
        openDocumentDrawer(id, 'view');
    };

    window.openVerificationDrawer = function(id) {
        openDocumentDrawer(id, 'verify');
    };

    // Toggle rejection input
    $('#btnRejectToggle').on('click', function() {
        const area = $('#rejectionInputArea');
        if (area.hasClass('d-none')) {
            area.removeClass('d-none');
            $('#inputRejectionReason').focus();
            $(this).removeClass('btn-outline-danger').addClass('btn-danger').html('<i class="ti ti-x"></i> Confirm Rejection');
        } else {
            // Submit rejection
            const reason = $('#inputRejectionReason').val().trim();
            if (!reason) {
                showToast('Please provide a reason for rejecting this document', 'error');
                return;
            }
            submitVerification('Rejected', reason);
        }
    });

    // Approve Document
    $('#btnApproveDoc').on('click', function() {
        submitVerification('Approved', null);
    });

    // Reset to Pending
    $('#btnResetToPending').on('click', function() {
        submitVerification('Pending', null);
    });

    function submitVerification(status, reason) {
        if (!currentReviewDocId) return;

        $.ajax({
            url: `{{ url('employees/documents') }}/${currentReviewDocId}/verify`,
            type: 'POST',
            data: {
                status: status,
                rejection_reason: reason
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    reviewDrawerInstance.hide();
                    table.ajax.reload();
                    updateKpis();
                } else {
                    showToast(response.message || 'Error updating status', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error processing verification';
                showToast(msg, 'error');
            }
        });
    }

    // Delete Confirmation Modal Handler
    const deleteDocModalEl = document.getElementById('deleteDocModal');
    const deleteDocModal = new bootstrap.Modal(deleteDocModalEl);

    window.confirmDeleteDoc = function(id, name) {
        currentDeleteDocId = id;
        $('#deleteDocName').text(`"${name}"`);
        deleteDocModal.show();
    };

    $('#btnConfirmDeleteDoc').on('click', function() {
        if (!currentDeleteDocId) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...');

        $.ajax({
            url: `{{ url('employees/documents') }}/${currentDeleteDocId}`,
            type: 'DELETE',
            success: function(response) {
                btn.prop('disabled', false).text('Delete File');
                deleteDocModal.hide();

                if (response.success) {
                    showToast(response.message, 'success');
                    table.ajax.reload();
                    updateKpis();
                } else {
                    showToast(response.message || 'Error deleting file', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Delete File');
                deleteDocModal.hide();
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting document';
                showToast(msg, 'error');
            }
        });
    });
});
</script>
@endpush

@endsection
