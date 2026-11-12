@extends('layout.master')
@section('title', $title ?? 'Employee Directory')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .employee-directory-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .employee-directory-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .employee-directory-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .employee-directory-page .kpi-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--card-soft-bg, #eef5ff);
            color: var(--card-accent, #0d6efd);
            flex-shrink: 0;
        }

        .employee-directory-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .employee-directory-page .filter-bar-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 12px 16px;
        }

        .employee-directory-page .avatar-initials {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #ffffff;
            font-size: 13px;
            flex-shrink: 0;
        }

        .employee-directory-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .employee-directory-page .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .employee-directory-page .badge-pill-soft {
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
        }

        .employee-directory-page .status-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        /* Modern Profile Slider Drawer (Offcanvas) */
        #employeeSidebar {
            width: 480px !important;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            border: none;
            box-shadow: -12px 0 40px rgba(15, 23, 42, 0.22) !important;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            z-index: 1060;
        }

        #employeeSidebar .offcanvas-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 24px;
        }

        #employeeSidebar .offcanvas-body {
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
            width: 84px;
            height: 84px;
            border-radius: 22px;
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

        /* Quick Action Bar */
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

        /* Detail Cards in Slider */
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
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }

        /* Agency Highlight Card inside Slider */
        .agency-highlight-card {
            background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);
            border: 1px solid #f0abfc !important;
            border-radius: 14px;
            padding: 16px;
            position: relative;
            overflow: hidden;
            margin-bottom: 14px;
        }

        /* Drawer Sticky Footer */
        .emp-slider-footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Modern Table Status Switch / Slider */
        .form-check.form-switch .form-check-input.statusSwitch {
            width: 36px;
            height: 20px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .form-check.form-switch .form-check-input.statusSwitch:checked {
            background-color: #10b981;
            border-color: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);
        }

        /* Modern DataTables Pagination & Info */
        .employee-directory-page .dataTables_wrapper .dataTables_info {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .employee-directory-page .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .employee-directory-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

        .employee-directory-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .employee-directory-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .employee-directory-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
        }

        .employee-directory-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .employee-directory-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
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

    <div class="page-wrapper employee-directory-page">
        <div class="content">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-users fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Employee Directory' }}</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $totalEmployees ?? 0 }} Staff
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Employees</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Employees</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                        <i class="ti ti-file-spreadsheet me-1.5 fs-16"></i>Bulk Import
                    </button>
                    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center">
                        <i class="ti ti-user-plus me-1.5 fs-16"></i>Add Employee
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 shadow-xs" role="alert">
                    <i class="ti ti-check me-1.5"></i> {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-4 shadow-xs" role="alert">
                    <i class="ti ti-alert-triangle me-1.5"></i> {!! session('warning') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-xs" role="alert">
                    <i class="ti ti-alert-circle me-1.5"></i> {!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 4 Metric KPI Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Staff --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Employees</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($totalEmployees ?? 0) }}</h3>
                                <span class="badge bg-light text-secondary border fs-11 mt-1">All Staff Members</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-users"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Active Staff --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="Active" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Active Staff</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ number_format($activeEmployees ?? 0) }}</h3>
                                <span class="badge bg-success-subtle text-success fs-11 mt-1">Working / Active</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-user-check"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Inactive Staff --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="Inactive" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Inactive / On-Hold</span>
                                <h3 class="mb-0 mt-1 fw-bold text-danger">{{ number_format($inactiveEmployees ?? 0) }}</h3>
                                <span class="badge bg-danger-subtle text-danger fs-11 mt-1">Suspended / Left</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-user-x"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Full-Time Staff --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #6366f1; --card-soft-bg: #eef2ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Full-Time Staff</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($fulltimeEmployees ?? 0) }}</h3>
                                <span class="badge bg-primary-subtle text-primary fs-11 mt-1">Permanent Roles</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-id-badge"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="filter-bar-card mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-search"></i></span>
                            <input type="text" id="employeeSearchInput" class="form-control border-start-0 ps-0" placeholder="Search name, email, phone...">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-building"></i></span>
                            <select id="agencyFilterSelect" class="form-select border-start-0 ps-0">
                                <option value="">-- All Agencies --</option>
                                <option value="direct">Direct / In-House</option>
                                @foreach ($agencies ?? [] as $agency)
                                    <option value="{{ $agency->agency_name }}">{{ $agency->agency_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-briefcase"></i></span>
                            <select id="designationFilterSelect" class="form-select border-start-0 ps-0">
                                <option value="">-- All Designations --</option>
                                @foreach ($designations ?? [] as $desig)
                                    <option value="{{ $desig->name }}">{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @php
                        $activeFilter = $selectedStatus ?? request('status', 'Active');
                    @endphp
                    <div class="col-lg-3 col-md-6 col-12 d-flex align-items-center gap-1">
                        <div class="btn-group w-100" role="group" id="statusFilterGroup">
                            <button type="button" class="btn btn-sm {{ $activeFilter === '' || $activeFilter === 'all' ? 'btn-primary active' : 'btn-outline-secondary' }} status-pill-btn" data-status="">All ({{ $totalEmployees ?? 0 }})</button>
                            <button type="button" class="btn btn-sm {{ $activeFilter === 'Active' ? 'btn-primary active' : 'btn-outline-secondary' }} status-pill-btn" data-status="Active">Active ({{ $activeEmployees ?? 0 }})</button>
                            <button type="button" class="btn btn-sm {{ $activeFilter === 'Inactive' ? 'btn-primary active' : 'btn-outline-secondary' }} status-pill-btn" data-status="Inactive">Inactive ({{ $inactiveEmployees ?? 0 }})</button>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-12 col-12 d-flex justify-content-end gap-1">
                        <button type="button" id="refreshTableBtn" class="btn btn-light border btn-sm d-inline-flex align-items-center w-100 justify-content-center">
                            <i class="ti ti-rotate-clockwise me-1.5"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Employees DataTable Card --}}
            <div class="card table-container-card">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">Employees Roster</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('masters.organisation.agency.mapping') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                            <i class="ti ti-users-group text-primary"></i> Agency Mapping
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="employeeList" class="table table-hover align-middle mb-0 nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Sn</th>
                                    <th>Employee</th>
                                    <th>Assigned Agency</th>
                                    <th>Designation</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th style="width: 110px;" class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <x-footer />
    </div>

    <!-- Modern Employee Profile Slider Drawer -->
    <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="employeeSidebar">
        <!-- Header -->
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                    <i class="ti ti-id fs-16"></i>
                </span>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark mb-0">Employee Profile</h6>
                    <small class="text-muted" id="empCodeSubText">ID: #EMP---</small>
                </div>
            </div>
            <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body" id="employeeDetailsBody">
            <!-- Hero Gradient Card -->
            <div class="emp-slider-hero">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="emp-avatar-wrapper">
                        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Profile" 
                             class="emp-avatar-img" id="empProfile">
                        <span class="emp-status-floating-dot" id="empStatusDot"></span>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1.5">
                        <span class="badge bg-opacity-15 text-white border border-white border-opacity-20 font-monospace fs-11" id="empIdBadge">
                            #EMP---
                        </span>
                        <span class="badge bg-success-subtle text-success fs-11 px-2.5 py-1 rounded-pill fw-bold" id="empStatus">
                            Active
                        </span>
                    </div>
                </div>

                <h4 class="fw-bold text-white mb-1" id="empName">Employee Name</h4>
                <div class="text-white-50 fs-13 mb-3 d-flex align-items-center gap-1.5 flex-wrap">
                    <span id="empEmail"><i class="ti ti-mail me-1"></i>email@example.com</span>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge  bg-opacity-20 text-white border border-white border-opacity-25 fs-11 px-2.5 py-1 rounded-pill" id="empDesignation">
                        <i class="ti ti-briefcase me-1"></i>Designation
                    </span>
                    <span class="badge fs-11 text-white border border-white border-opacity-25 px-2.5 py-1 rounded-pill" id="empAgencyHeaderBadge" style="background-color: rgba(168, 85, 247, 0.45);">
                        <i class="ti ti-building me-1"></i>Agency
                    </span>
                    <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-15 fs-11 px-2 py-1 rounded-pill" id="empTypeBadge">
                        Full-time
                    </span>
                    <span class="badge  bg-opacity-15 text-white border border-white border-opacity-25 fs-11 px-2.5 py-1 rounded-pill" id="empLocationHeaderBadge" style="display: none;">
                        <i class="ti ti-map-pin me-1 text-warning"></i><span id="empLocationHeaderVal">-</span>
                    </span>
                </div>
            </div>

            <!-- Quick Action Hub -->
            <div class="emp-quick-action-bar">
                <a href="javascript:void(0);" id="quickCallBtn" class="emp-quick-action-btn" title="Call Mobile">
                    <i class="ti ti-phone text-success"></i>
                    <span>Call</span>
                </a>
                <a href="javascript:void(0);" id="quickEmailBtn" class="emp-quick-action-btn" title="Send Email">
                    <i class="ti ti-mail text-primary"></i>
                    <span>Email</span>
                </a>
                <a href="javascript:void(0);" id="quickReportBtn" class="emp-quick-action-btn" title="Month Attendance Sheet">
                    <i class="ti ti-calendar-stats text-warning"></i>
                    <span>Sheet</span>
                </a>
                <a href="javascript:void(0);" id="quickEditBtn" class="emp-quick-action-btn" title="Edit Profile">
                    <i class="ti ti-edit text-info"></i>
                    <span>Edit</span>
                </a>
            </div>

            <!-- Slider Content Sections -->
            <div class="emp-slider-section">

                <!-- Agency Highlight Card -->
                <div class="agency-highlight-card" id="agencyCardSection">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fs-11 fw-bold text-uppercase tracking-wider" style="color: #9333ea;">
                            <i class="ti ti-building-skyscraper me-1"></i>Staff Agency Assignment
                        </span>
                        <a href="{{ route('masters.organisation.agency.mapping') }}" class="badge bg-white text-dark border text-decoration-none shadow-xs" title="Change Agency Mapping">
                            <i class="ti ti-arrows-exchange me-0.5"></i>Change
                        </a>
                    </div>
                    <h6 class="fw-bold text-dark mb-0.5" id="empAgencyTitle">Direct / In-House</h6>
                    <p class="text-muted fs-12 mb-0" id="empAgencySubtitle">Company Staff Member</p>
                </div>

                <!-- Employment & Organization Details Card -->
                <div class="emp-slider-card">
                    <div class="emp-card-title">
                        <i class="ti ti-briefcase text-primary fs-15"></i>Employment Details
                    </div>

                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-id-badge text-muted"></i>Designation</span>
                        <span class="emp-data-value" id="empDesignationVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-clock text-muted"></i>Employment Type</span>
                        <span class="emp-data-value" id="empTypeVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-calendar text-muted"></i>Joining Date</span>
                        <span class="emp-data-value" id="empJoiningVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-calendar-check text-muted"></i>Confirmation Date</span>
                        <span class="emp-data-value" id="empConfirmationVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-hourglass-empty text-muted"></i>Service Tenure</span>
                        <span class="emp-data-value text-primary font-monospace" id="empTenureVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-clock-hour-4 text-info"></i>Work Shift</span>
                        <span class="emp-data-value text-info fw-semibold" id="empShiftVal">-</span>
                    </div>
                </div>

                <!-- Supervisor & Hierarchy Card -->
                <div class="emp-slider-card" id="supervisorCardSection">
                    <div class="emp-card-title d-flex align-items-center justify-content-between">
                        <span><i class="ti ti-crown text-warning fs-15 me-1"></i>Hierarchy & Supervision</span>
                        <span class="badge border py-0.5 px-2 fs-10" id="empSupervisorRoleBadge">Standard Employee</span>
                    </div>

                    <div class="emp-data-row" id="empReportingToRow">
                        <span class="emp-data-label"><i class="ti ti-user-check text-muted"></i>Reports To</span>
                        <span class="emp-data-value fw-semibold text-dark" id="empReportingSupervisorVal">-</span>
                    </div>

                    <div id="empSubordinatesSection" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                            <span class="fs-12 fw-semibold text-dark"><i class="ti ti-users me-1 text-primary"></i>Supervised Team Members</span>
                            <span class="badge bg-primary-subtle text-primary" id="empSubordinatesCountBadge">0 Members</span>
                        </div>
                        <div id="empSubordinatesList" class="mt-2 d-flex flex-column gap-1.5" style="max-height: 200px; overflow-y: auto;">
                            <!-- Dynamically loaded list of subordinates -->
                        </div>
                    </div>
                </div>

                <!-- Contact & Personal Details Card -->
                <div class="emp-slider-card">
                    <div class="emp-card-title">
                        <i class="ti ti-user text-success fs-15"></i>Contact & Personal Information
                    </div>

                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-phone text-muted"></i>Mobile Number</span>
                        <span class="emp-data-value" id="empMobileVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-mail text-muted"></i>Email Address</span>
                        <span class="emp-data-value text-break" id="empEmailVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-gender-bigender text-muted"></i>Gender</span>
                        <span class="emp-data-value" id="empGenderVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-droplet text-danger"></i>Blood Group</span>
                        <span class="emp-data-value text-danger fw-semibold" id="empBloodGroupVal">-</span>
                    </div>
                </div>

                <!-- Posting Location Card -->
                <div class="emp-slider-card">
                    <div class="emp-card-title d-flex align-items-center justify-content-between flex-wrap gap-1">
                        <div>
                            <i class="ti ti-map-pin text-danger fs-15 me-1"></i>Posting Location
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11 fw-semibold" id="empFullLocationBadge" style="display: none;">
                            <i class="ti ti-current-location me-1"></i><span id="empFullLocationText">-</span>
                        </span>
                    </div>

                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-building text-muted"></i>City</span>
                        <span class="emp-data-value fw-semibold text-dark" id="empCityVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-map text-muted"></i>State</span>
                        <span class="emp-data-value" id="empStateVal">-</span>
                    </div>
                    <div class="emp-data-row">
                        <span class="emp-data-label"><i class="ti ti-world text-muted"></i>Country</span>
                        <span class="emp-data-value" id="empCountryVal">-</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="emp-slider-footer">
            <a href="javascript:void(0);" id="editEmpLink" class="btn btn-primary flex-fill fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm">
                <i class="ti ti-edit fs-15"></i>Edit Profile
            </a>
            <a href="javascript:void(0);" id="sheetEmpLink" class="btn btn-outline-secondary fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5" title="View Full Month Attendance Sheet">
                <i class="ti ti-calendar-stats fs-15 text-primary"></i>Month Sheet
            </a>
            <button type="button" class="btn btn-light border px-3" data-bs-dismiss="offcanvas">
                Close
            </button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <span class="avatar avatar-xl rounded-circle bg-danger-subtle text-danger mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-32"></i>
                    </span>
                    <h5 class="fw-bold text-dark mb-1">Delete Employee?</h5>
                    <p class="text-muted fs-13 mb-4">Are you sure you want to delete this employee record? This action cannot be undone.</p>
                    <input type="hidden" id="deleteEmployeeId">
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger px-3 fw-semibold" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" id="status_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <span class="avatar avatar-xl rounded-circle bg-warning-subtle text-warning mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-user-exclamation fs-32"></i>
                    </span>
                    <h5 class="fw-bold text-dark mb-1">Update Status</h5>
                    <p class="text-muted fs-13 mb-4" id="statusConfirmText">Are you sure you want to update status?</p>
                    <input type="hidden" id="statusEmployeeId">
                    <input type="hidden" id="statusValue">
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary px-3 fw-semibold" id="confirmStatusBtn">Yes, Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-light bg-opacity-75 py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-file-spreadsheet fs-22"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="bulkImportModalLabel">Bulk Import Employees & Payroll Data</h5>
                            <p class="text-muted fs-12 mb-0">Upload Excel (.xlsx, .xls) or CSV (.csv) to register employees along with optional Salary CTC, PF, UAN, and Bank details.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" id="bulkImportForm">
                    @csrf
                    <div class="modal-body p-4">

                        {{-- Step 1: Download Sample File --}}
                        <div class="p-3 mb-4 rounded-3 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fs-11 fw-bold">Step 1</span>
                                <div>
                                    <div class="fw-semibold text-dark fs-13">Download Complete Template (.csv)</div>
                                    <div class="text-muted fs-12">Pre-formatted template with employee profile, designations, agencies, and complete salary / PF columns.</div>
                                </div>
                            </div>
                            <a href="{{ route('employees.import.sample') }}" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center">
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

                            <div class="avatar-dropzone p-4 rounded-3" id="importDropzone" onclick="document.getElementById('employeeImportFileInput').click();">
                                <div id="importDropzonePrompt">
                                    <i class="ti ti-cloud-upload upload-icon text-primary d-block mb-2" style="font-size: 2.5rem;"></i>
                                    <h6 class="fw-bold text-dark mb-1">Click to browse or drag file here</h6>
                                    <p class="text-muted fs-12 mb-0">Supported files: Microsoft Excel (.xlsx, .xls) and Comma-Separated Values (.csv)</p>
                                </div>
                                <div id="importFilePreview" class="d-none">
                                    <div class="d-flex align-items-center justify-content-center gap-3 py-2">
                                        <i class="ti ti-file-spreadsheet text-success fs-36"></i>
                                        <div class="text-start">
                                            <div class="fw-bold text-dark fs-14" id="importFileName">file.xlsx</div>
                                            <div class="text-muted fs-12" id="importFileSize">0 KB</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light text-danger border ms-2" onclick="removeImportFile(event)">
                                            <i class="ti ti-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                                <input type="file" name="employee_file" id="employeeImportFileInput" class="d-none" accept=".csv, .xlsx, .xls, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="handleImportFileSelect(this)">
                            </div>
                        </div>

                        {{-- Instructions & Column Mapping Help --}}
                        <div class="card border rounded-3 bg-light bg-opacity-25 mb-0">
                            <div class="card-header bg-transparent py-2.5 px-3 d-flex align-items-center justify-content-between">
                                <span class="fs-12 fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <i class="ti ti-info-circle text-info"></i> Column Mapping & Smart Upsert Guidelines
                                </span>
                                <span class="badge bg-success-subtle text-success fs-10">Smart Upsert & Salary Enabled</span>
                            </div>
                            <div class="card-body p-3 fs-12 text-muted">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1"><i class="ti ti-user me-1 text-primary"></i>Profile Info</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>Employee Name <span class="text-danger">*</span></strong></li>
                                            <li><strong>Employee Email <span class="text-danger">*</span></strong></li>
                                            <li><strong>Mobile Number</strong></li>
                                            <li><strong>Password</strong> (default 123456)</li>
                                            <li><strong>Gender & Blood Group</strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1"><i class="ti ti-briefcase me-1 text-info"></i>Role & Org</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>Designation <span class="text-danger">*</span></strong></li>
                                            <li><strong>Assigned Agency</strong></li>
                                            <li><strong>Work Shift & Type</strong></li>
                                            <li><strong>Joining Date</strong></li>
                                            <li><strong>Reporting Supervisor</strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1"><i class="ti ti-coin me-1 text-success"></i>Salary CTC</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>Monthly Gross</strong> (Auto calculates Basic 50%, HRA 40%, etc.)</li>
                                            <li><strong>Basic Salary & HRA</strong></li>
                                            <li><strong>Conveyance / Medical / Special</strong></li>
                                            <li><strong>Annual CTC</strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="fs-11 fw-bold text-uppercase text-dark mb-1"><i class="ti ti-shield-check me-1 text-warning"></i>PF, ESI & Bank</h6>
                                        <ul class="mb-0 ps-3">
                                            <li><strong>PF No & UAN Number</strong></li>
                                            <li><strong>ESI Number & PAN Number</strong></li>
                                            <li><strong>Employee / Employer PF (12%)</strong></li>
                                            <li><strong>Bank Name, Account & IFSC</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light bg-opacity-50 py-3 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitImportBtn" class="btn btn-primary btn-sm px-4 fw-semibold d-inline-flex align-items-center" disabled>
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
        $(document).ready(function() {
            const avatarColors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777'];
            const defaultStatus = "{{ $activeFilter ?? 'Active' }}";

            const table = $('#employeeList').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('employees.index') }}",
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                initComplete: function() {
                    if (defaultStatus && defaultStatus !== 'all' && defaultStatus !== '') {
                        this.api().column(5).search(`^${defaultStatus}$`, true, false).draw();
                    }
                },
                dom: "<'row'<'col-12'tr>>" +
                     "<'row align-items-center justify-content-between p-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center'i><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ employees",
                    infoEmpty: "Showing 0 to 0 of 0 employees",
                    infoFiltered: "(filtered from _MAX_ total)",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                        last: '<i class="ti ti-chevrons-right"></i>'
                    },
                    emptyTable: "No employees found in directory",
                    zeroRecords: "No matching employees found"
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => `<span class="badge bg-light text-secondary border font-monospace fs-11">#${meta.row + 1}</span>`
                    },
                    {
                        data: 'employee_name',
                        render: function(data, type, row, meta) {
                            if (type === 'filter') {
                                return (data || '') + ' ' + (row.employee_email || '') + ' ' + (row.employee_mobile || '');
                            }
                            if (type === 'sort') {
                                return data || '';
                            }
                            const name = data || 'Employee';
                            const email = row.employee_email || '-';
                            // When no profile image is available, show only the first
                            // letter of the employee's full name in the avatar.
                            const initial = name.trim().charAt(0).toUpperCase() || 'E';
                            const bgCol = avatarColors[meta.row % avatarColors.length];

                            let avatarHtml = '';
                            if (row.profile_image && row.profile_image !== 'default.png') {
                                avatarHtml = `<img src="{{ asset('uploads/employees') }}/${row.profile_image}" alt="${name}" class="rounded-circle me-2 border" width="38" height="38" style="object-fit: cover;">`;
                            } else {
                                avatarHtml = `<span class="avatar-initials me-2" style="background-color: ${bgCol};">${initial}</span>`;
                            }

                            let subAgencyTag = row.has_agency && row.agency_name && row.agency_name !== 'Direct / In-House'
                                ? `<div class="mt-0.5"><span class="text-muted fs-11"><i class="ti ti-building me-1 text-primary"></i>${row.agency_name}</span></div>`
                                : '';

                            return `
                                <div class="d-flex align-items-center">
                                    ${avatarHtml}
                                    <div>
                                        <div class="fw-semibold text-dark fs-13 d-flex align-items-center gap-1">
                                            <a href="javascript:void(0);" onclick="viewEmployee(${row.employee_id})" class="text-dark text-decoration-none hover-primary">${name}</a>
                                        </div>
                                        <div class="fs-11 text-muted"><a href="mailto:${email}" class="text-muted text-decoration-none">${email}</a></div>
                                        ${subAgencyTag}
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'agency_name',
                        render: function(data, type, row) {
                            if (type === 'filter' || type === 'sort') {
                                return data || '';
                            }
                            if (row.has_agency && row.agency_id && data && data !== 'Direct / In-House') {
                                const codeBadge = row.agency_code ? `<span class="badge bg-white text-dark ms-1.5 border" style="font-size: 10px; padding: 2px 5px;">${row.agency_code}</span>` : '';
                                return `
                                    <div class="d-flex align-items-center">
                                        <span class="badge border py-1.5 px-2.5 fs-12 fw-semibold d-inline-flex align-items-center" style="background-color: #f3e8ff; color: #7e22ce; border-color: #d8b4fe !important;">
                                            <i class="ti ti-building me-1.5 fs-14"></i>${data}${codeBadge}
                                        </span>
                                    </div>
                                `;
                            }
                            return `
                                <div>
                                    <span class="badge bg-light text-muted border py-1.5 px-2.5 fs-11 fw-normal d-inline-flex align-items-center">
                                        <i class="ti ti-building-community me-1 fs-12 text-secondary"></i>Direct / In-House
                                    </span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'designation_name',
                        render: function(data, type, row) {
                            if (type === 'filter' || type === 'sort') {
                                return data || '';
                            }
                            let html = `<span class="badge bg-light text-secondary border fs-11">${data || 'Staff'}</span>`;
                            if (row.is_supervisor) {
                                html += `
                                    <div class="mt-1">
                                        <span class="badge border py-0.5 px-2 fs-10 fw-semibold d-inline-flex align-items-center" style="background-color: #fef3c7; color: #b45309; border-color: #fde68a !important;">
                                            <i class="ti ti-crown me-1 text-warning"></i>Supervisor
                                        </span>
                                    </div>
                                `;
                                if (row.subordinates_count > 0) {
                                    html += `
                                        <div class="mt-0.5">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10" style="padding: 2px 6px;">
                                                <i class="ti ti-users me-0.5"></i>${row.subordinates_count} Subordinates
                                            </span>
                                        </div>
                                    `;
                                }
                            } else if (row.supervisor_name && row.supervisor_name !== '-') {
                                html += `
                                    <div class="mt-1">
                                        <span class="text-muted fs-11 d-inline-flex align-items-center" title="Reports to Supervisor: ${row.supervisor_name}">
                                            <i class="ti ti-user-check me-1 text-primary"></i>${row.supervisor_name}
                                        </span>
                                    </div>
                                `;
                            }
                            return html;
                        }
                    },
                    {
                        data: 'joining_date',
                        render: data => `<span class="fs-12 text-muted"><i class="ti ti-calendar me-1"></i>${data || '-'}</span>`
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if (type === 'filter' || type === 'sort') {
                                return data === 'Inactive' ? 'Inactive' : 'Active';
                            }

                            const isActive = (data === 'Active');
                            const checked = isActive ? 'checked' : '';
                            const text = isActive ? 'Active' : 'Inactive';
                            const badgeClass = isActive ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle';
                            const dotColor = isActive ? '#10b981' : '#ef4444';

                            return `
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input statusSwitch" type="checkbox" ${checked} data-id="${row.employee_id}" style="cursor: pointer;">
                                    </div>
                                    <span class="badge-pill-soft ${badgeClass} border cursor-pointer" onclick="openStatusModal(${row.employee_id}, '${text}')">
                                        <span class="status-pulse-dot" style="background-color: ${dotColor};"></span>
                                        ${text}
                                    </span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'employee_id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <div class="d-flex align-items-center justify-content-end gap-1 pe-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary action-btn" onclick="viewEmployee(${data})" title="View Details">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <a href="{{ url('employees/edit') }}/${data}" class="btn btn-sm btn-outline-secondary action-btn" title="Edit Employee">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn" onclick="deleteEmployee(${data})" title="Delete Employee">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            // Live Search
            $('#employeeSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Agency Filter Dropdown
            $('#agencyFilterSelect').on('change', function() {
                const val = $(this).val();
                if (!val) {
                    table.column(2).search('').draw();
                } else if (val === 'direct') {
                    const regex = '^' + $.fn.dataTable.util.escapeRegex('Direct / In-House') + '$';
                    table.column(2).search(regex, true, false).draw();
                } else {
                    const regex = '^' + $.fn.dataTable.util.escapeRegex(val) + '$';
                    table.column(2).search(regex, true, false).draw();
                }
            });

            // Designation Filter Dropdown
            $('#designationFilterSelect').on('change', function() {
                const val = $(this).val();
                if (!val) {
                    table.column(3).search('').draw();
                } else {
                    const regex = '^' + $.fn.dataTable.util.escapeRegex(val) + '$';
                    table.column(3).search(regex, true, false).draw();
                }
            });

            // Status Filter Buttons (Column 5 is status)
            $('.status-pill-btn').on('click', function() {
                $('.status-pill-btn').removeClass('btn-primary active').addClass('btn-outline-secondary');
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');

                const status = $(this).data('status');
                table.column(5).search(status ? `^${status}$` : '', true, false).draw();
            });

            // KPI card quick filter
            $('.kpi-filter-btn').on('click', function() {
                const status = $(this).data('status');
                $(`.status-pill-btn[data-status="${status}"]`).trigger('click');
            });

            // Refresh / Reset Table
            $('#refreshTableBtn').on('click', function() {
                $('#employeeSearchInput').val('');
                $('#agencyFilterSelect').val('');
                $('#designationFilterSelect').val('');
                table.search('').columns().search('');
                table.ajax.reload(function() {
                    $(`.status-pill-btn[data-status="Active"]`).trigger('click');
                }, false);
            });

            // Auto-open drawer if ?view=ID or ?employee_id=ID is in URL
            const urlParams = new URLSearchParams(window.location.search);
            const autoViewId = urlParams.get('view') || urlParams.get('employee_id');
            if (autoViewId) {
                setTimeout(function() {
                    viewEmployee(autoViewId);
                }, 300);
            }
        });

        // View Employee in Drawer
        function viewEmployee(id) {
            $.ajax({
                url: "{{ url('employees') }}/" + id,
                type: "GET",
                success: function(emp) {
                    let imgUrl = (emp.profile_image && emp.profile_image !== 'default.png')
                        ? `{{ asset('uploads/employees') }}/${emp.profile_image}`
                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.employee_name || 'Staff')}&background=0d6efd&color=fff&size=128&bold=true&length=1`;

                    $('#empProfile').attr('src', imgUrl);
                    $('#empName').text(emp.employee_name || '-');
                    $('#empEmail').html(`<i class="ti ti-mail me-1"></i>${emp.employee_email || '-'}`);
                    $('#empEmailVal').text(emp.employee_email || '-');
                    $('#empMobileVal').text(emp.employee_mobile || '-');
                    $('#empCodeSubText').text(`ID: #EMP-${emp.employee_id}`);
                    $('#empIdBadge').text(`#EMP-${emp.employee_id}`);

                    // Gender & Blood Group
                    const genderMap = { 1: 'Male', 2: 'Female', 3: 'Other' };
                    $('#empGenderVal').text(genderMap[emp.employee_gender] || '-');
                    $('#empBloodGroupVal').text(emp.blood_group || '-');

                    // Location
                    $('#empCityVal').text(emp.city_name || '-');
                    $('#empStateVal').text(emp.state_name || '-');
                    $('#empCountryVal').text(emp.country_name || '-');

                    if (emp.full_location && emp.full_location !== '-') {
                        $('#empLocationHeaderVal').text(emp.full_location);
                        $('#empLocationHeaderBadge').show();
                        $('#empFullLocationText').text(emp.full_location);
                        $('#empFullLocationBadge').show();
                    } else {
                        $('#empLocationHeaderBadge').hide();
                        $('#empFullLocationBadge').hide();
                    }

                    // Dates & Tenure
                    const joining = emp.joining_date ? new Date(emp.joining_date) : null;
                    const joiningStr = joining && !isNaN(joining)
                        ? joining.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                        : (emp.joining_date || '-');
                    $('#empJoiningVal').text(joiningStr);

                    const confirmDate = emp.date_of_confirmation ? new Date(emp.date_of_confirmation) : null;
                    const confirmStr = confirmDate && !isNaN(confirmDate)
                        ? confirmDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                        : (emp.date_of_confirmation || '-');
                    $('#empConfirmationVal').text(confirmStr);

                    if (joining && !isNaN(joining)) {
                        const now = new Date();
                        let months = (now.getFullYear() - joining.getFullYear()) * 12 + (now.getMonth() - joining.getMonth());
                        if (months < 0) months = 0;
                        const yrs = Math.floor(months / 12);
                        const remMonths = months % 12;
                        let tenureText = '';
                        if (yrs > 0) tenureText += `${yrs} yr${yrs > 1 ? 's' : ''} `;
                        tenureText += `${remMonths} mo${remMonths > 1 ? 's' : ''}`;
                        $('#empTenureVal').text(tenureText.trim() || '< 1 month');
                    } else {
                        $('#empTenureVal').text('-');
                    }

                    // Type & Designation
                    const empType = emp.employment_type || 'Full-time';
                    const designation = emp.designation_name || 'Staff Member';
                    $('#empTypeBadge').text(empType);
                    $('#empTypeVal').text(empType);
                    $('#empDesignation').html(`<i class="ti ti-briefcase me-1"></i>${designation}`);
                    $('#empDesignationVal').text(designation);
                    $('#empShiftVal').text(emp.shift_display || (emp.shift_name ? `${emp.shift_name} (${emp.shift_timing})` : 'General Shift (09:30 AM - 06:30 PM)'));

                    // Agency
                    if (emp.has_agency && emp.agency_name && emp.agency_name !== 'Direct / In-House') {
                        const codeSuffix = emp.agency_code ? ` (${emp.agency_code})` : '';
                        $('#empAgencyHeaderBadge').html(`<i class="ti ti-building me-1"></i>${emp.agency_name}`).show();
                        $('#empAgencyTitle').text(emp.agency_name + codeSuffix);
                        $('#empAgencySubtitle').text('Assigned Third-Party / Vendor Agency');
                    } else {
                        $('#empAgencyHeaderBadge').html(`<i class="ti ti-building-community me-1"></i>Direct / In-House`).show();
                        $('#empAgencyTitle').text('Direct / In-House');
                        $('#empAgencySubtitle').text('Internal Company Employees (No Agency)');
                    }

                    // Supervisor & Hierarchy
                    if (emp.is_supervisor) {
                        $('#empSupervisorRoleBadge').html('<i class="ti ti-crown me-1 text-warning"></i>Supervisor').css({
                            'background-color': '#fef3c7',
                            'color': '#b45309',
                            'border-color': '#fde68a'
                        });
                        if (emp.subordinates && emp.subordinates.length > 0) {
                            $('#empSubordinatesCountBadge').text(`${emp.subordinates.length} Members`);
                            let subListHtml = '';
                            emp.subordinates.forEach(sub => {
                                subListHtml += `
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border bg-white">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-light text-secondary border me-1.5 font-monospace fs-10">#${sub.employee_id}</span>
                                            <div>
                                                <div class="fw-semibold text-dark fs-12">${sub.employee_name}</div>
                                                <div class="text-muted fs-11">${sub.designation_name} &bull; ${sub.agency_name}</div>
                                            </div>
                                        </div>
                                        <span class="badge ${sub.status === 'Inactive' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'} fs-10">${sub.status}</span>
                                    </div>
                                `;
                            });
                            $('#empSubordinatesList').html(subListHtml);
                            $('#empSubordinatesSection').show();
                        } else {
                            $('#empSubordinatesCountBadge').text('0 Members');
                            $('#empSubordinatesList').html('<div class="text-muted fs-11 p-2 text-center bg-white rounded border">No team members are currently mapped to this supervisor.</div>');
                            $('#empSubordinatesSection').show();
                        }
                    } else {
                        $('#empSupervisorRoleBadge').text('Standard Employee').css({
                            'background-color': '#f1f5f9',
                            'color': '#475569',
                            'border-color': '#cbd5e1'
                        });
                        $('#empSubordinatesSection').hide();
                    }

                    if (emp.supervisor_name && emp.supervisor_name !== '-') {
                        const desig = emp.supervisor_designation ? ` (${emp.supervisor_designation})` : '';
                        $('#empReportingSupervisorVal').html(`<span class="text-primary fw-semibold"><i class="ti ti-user-check me-1"></i>${emp.supervisor_name}${desig}</span>`);
                        $('#empReportingToRow').show();
                    } else {
                        $('#empReportingSupervisorVal').html('<span class="text-muted">Direct / Independent (No Supervisor)</span>');
                        $('#empReportingToRow').show();
                    }

                    // Status
                    const isActive = (emp.status === 'Active' || emp.status === '1' || emp.status === 1);
                    $('#empStatus').text(isActive ? 'Active' : 'Inactive')
                                   .removeClass('bg-success-subtle text-success bg-danger-subtle text-danger')
                                   .addClass(isActive ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger');
                    $('#empStatusDot').css('background-color', isActive ? '#10b981' : '#ef4444');

                    // Quick Actions
                    if (emp.employee_mobile) {
                        $('#quickCallBtn').attr('href', `tel:${emp.employee_mobile}`).removeClass('disabled opacity-50');
                    } else {
                        $('#quickCallBtn').attr('href', 'javascript:void(0);').addClass('disabled opacity-50');
                    }

                    if (emp.employee_email) {
                        $('#quickEmailBtn').attr('href', `mailto:${emp.employee_email}`).removeClass('disabled opacity-50');
                    } else {
                        $('#quickEmailBtn').attr('href', 'javascript:void(0);').addClass('disabled opacity-50');
                    }

                    const monthReportUrl = `{{ url('attendance/attendance-dashboard/org-reports/employee') }}/${emp.employee_id}`;
                    $('#quickReportBtn').attr('href', monthReportUrl);
                    $('#sheetEmpLink').attr('href', monthReportUrl);
                    $('#quickEditBtn').attr('href', `{{ url('employees/edit') }}/${emp.employee_id}`);
                    $('#editEmpLink').attr('href', `{{ url('employees/edit') }}/${emp.employee_id}`);

                    let offcanvas = new bootstrap.Offcanvas(document.getElementById('employeeSidebar'));
                    offcanvas.show();
                }
            });
        }

        // Status Toggle
        function openStatusModal(id, status) {
            let actionText = (status === 'Active') ? 'deactivate' : 'activate';
            $('#statusEmployeeId').val(id);
            $('#statusValue').val(status === 'Active' ? 0 : 1);
            $('#statusConfirmText').text(`Are you sure you want to ${actionText} this employee account?`);
            $('#status_modal').modal('show');
        }

        $(document).on('change', '.statusSwitch', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;
            $('#statusEmployeeId').val(id);
            $('#statusValue').val(status);
            $('#statusConfirmText').text(`Are you sure you want to ${status ? 'activate' : 'deactivate'} this employee account?`);
            $('#status_modal').modal('show');
            $(this).prop('checked', !status);
        });

        $('#confirmStatusBtn').click(function() {
            let id = $('#statusEmployeeId').val();
            let status = $('#statusValue').val();
            $.ajax({
                url: "{{ url('employees/status') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function() {
                    $('#status_modal').modal('hide');
                    $('#employeeList').DataTable().ajax.reload(null, false);
                }
            });
        });

        // Delete Employee
        function deleteEmployee(id) {
            $('#deleteEmployeeId').val(id);
            $('#delete_modal').modal('show');
        }

        $('#confirmDeleteBtn').click(function() {
            let id = $('#deleteEmployeeId').val();
            $.ajax({
                url: "{{ url('employees/delete') }}/" + id,
                type: "POST",
                data: {
                    _method: "DELETE",
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    $('#delete_modal').modal('hide');
                    $('#employeeList').DataTable().ajax.reload(null, false);
                }
            });
        });

        // Bulk Import Dropzone Preview & Logic
        function handleImportFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size exceeds 10 MB limit.');
                    input.value = '';
                    return;
                }

                $('#importFileName').text(file.name);
                $('#importFileSize').text((file.size / 1024).toFixed(1) + ' KB');
                $('#importDropzonePrompt').addClass('d-none');
                $('#importFilePreview').removeClass('d-none');
                $('#submitImportBtn').prop('disabled', false);
            }
        }

        function removeImportFile(e) {
            if (e) e.stopPropagation();
            $('#employeeImportFileInput').val('');
            $('#importFilePreview').addClass('d-none');
            $('#importDropzonePrompt').removeClass('d-none');
            $('#submitImportBtn').prop('disabled', true);
        }

        $('#bulkImportForm').on('submit', function() {
            const btn = $('#submitImportBtn');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span>Importing Employees...');
        });

        const importDropzone = document.getElementById('importDropzone');
        if (importDropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                importDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    importDropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                importDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    importDropzone.classList.remove('dragover');
                }, false);
            });

            importDropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length) {
                    const input = document.getElementById('employeeImportFileInput');
                    input.files = files;
                    handleImportFileSelect(input);
                }
            }, false);
        }
    </script>
@endpush
