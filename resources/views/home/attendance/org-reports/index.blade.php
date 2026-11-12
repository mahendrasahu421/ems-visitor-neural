@extends('layout.master')
@section('title', $title ?? 'Employee Attendance Reports')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .org-reports-page .stat-kpi-card {
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

        .org-reports-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .org-reports-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .org-reports-page .kpi-icon-box {
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

        .org-reports-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .org-reports-page .filter-bar-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            padding: 18px 20px;
        }

        .org-reports-page {
            font-size: 13.5px;
        }

        .org-reports-page .filter-pill-btn {
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .org-reports-page .filter-pill-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .org-reports-page .filter-pill-btn.active {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
        }

        .org-reports-page .emp-avatar {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
            cursor: pointer;
        }

        .org-reports-page .emp-name-link {
            color: #0f172a;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            transition: color 0.15s ease;
            cursor: pointer;
        }

        .org-reports-page .emp-name-link:hover {
            color: #0d6efd;
            text-decoration: underline;
        }

        .org-reports-page .time-badge {
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .org-reports-page .time-badge-in {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .org-reports-page .time-badge-out {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .org-reports-page .time-badge-progress {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .org-reports-page .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-animation 1.8s infinite;
        }

        @keyframes pulse-animation {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            }

            70% {
                box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Modern DataTables Pagination & Info */
        .org-reports-page .dataTables_wrapper {
            padding: 0;
        }

        .org-reports-page .dataTables_wrapper .dataTables_info {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 14px 20px !important;
            margin: 0 !important;
        }

        .org-reports-page .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 0 !important;
            padding: 14px 20px !important;
        }

        .org-reports-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

        .org-reports-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .org-reports-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .org-reports-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
        }

        .org-reports-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .org-reports-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
        }

        .org-reports-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .org-reports-page .dataTables_processing {
            background: rgba(255, 255, 255, 0.9) !important;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: #0d6efd;
            z-index: 100;
            padding: 15px 25px !important;
        }

        /* Monthly Modal Stats */
        .monthly-stat-pill {
            padding: 10px 14px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
        }

        .monthly-stat-pill .num {
            font-size: 18px;
            font-weight: 700;
            display: block;
        }

        .monthly-stat-pill .lbl {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Register Matrix Table */
        .org-reports-page .register-table-wrapper {
            position: relative;
            overflow-x: auto;
            max-height: 720px;
        }

        .org-reports-page .register-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: 13.5px;
        }

        .org-reports-page .register-table th,
        .org-reports-page .register-table td {
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .org-reports-page .register-table thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            font-size: 12px;
            z-index: 10;
        }

        .org-reports-page .register-table thead th.weekend-col {
            background-color: #fef3c7 !important;
            color: #92400e;
        }

        .org-reports-page .register-table tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Sticky Left Columns */
        .org-reports-page .sticky-col-1 {
            position: sticky;
            left: 0;
            background: #ffffff;
            z-index: 5;
            min-width: 50px;
            text-align: center;
            font-size: 13px;
        }

        .org-reports-page .sticky-col-2 {
            position: sticky;
            left: 50px;
            background: #ffffff;
            z-index: 5;
            min-width: 220px;
            text-align: left !important;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.04);
            font-size: 13.5px;
        }

        .org-reports-page .register-table thead th.sticky-col-1,
        .org-reports-page .register-table thead th.sticky-col-2 {
            z-index: 15 !important;
            background: #f1f5f9 !important;
        }

        .org-reports-page .register-table tfoot th.sticky-col-1,
        .org-reports-page .register-table tfoot th.sticky-col-2 {
            z-index: 15 !important;
            background: #f8fafc !important;
        }

        /* Attendance Status Indicators */
        .org-reports-page .cell-p {
            background-color: #dcfce7 !important;
            color: #15803d !important;
            font-weight: 800;
            border-radius: 5px;
            display: inline-block;
            width: 26px;
            height: 26px;
            line-height: 26px;
            text-align: center;
            font-size: 12px;
        }

        .org-reports-page .cell-a {
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
            font-weight: 800;
            border-radius: 5px;
            display: inline-block;
            width: 26px;
            height: 26px;
            line-height: 26px;
            text-align: center;
            font-size: 12px;
        }

        .org-reports-page .col-summary-p {
            background-color: #f0fdf4 !important;
            color: #166534 !important;
            font-weight: 700;
            font-size: 13px !important;
        }

        .org-reports-page .col-summary-a {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            font-weight: 700;
            font-size: 13px !important;
        }

        .org-reports-page .badge-legend {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 4px;
        }

        /* Global Font-Size Boost for Attendance Reports Table & Elements */
        .org-reports-page #attendanceReportTable {
            font-size: 13.5px !important;
        }

        .org-reports-page #attendanceReportTable thead th {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            padding: 12px 14px !important;
        }

        .org-reports-page #attendanceReportTable tbody td {
            font-size: 13.5px !important;
            padding: 12px 14px !important;
        }

        .org-reports-page .form-label {
            font-size: 13.5px !important;
        }

        .org-reports-page .form-select,
        .org-reports-page .form-control {
            font-size: 13.5px !important;
        }

        .org-reports-page .select2-container .select2-selection--single {
            font-size: 13.5px !important;
        }

        .org-reports-page .stat-kpi-card h3 {
            font-size: 22px !important;
        }

        .org-reports-page .stat-kpi-card .text-uppercase {
            font-size: 12.5px !important;
        }

        .org-reports-page .nav-pills .nav-link {
            font-size: 13.5px !important;
        }
    </style>

    <div class="page-wrapper org-reports-page">
        <div class="content">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span
                            class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-clock-check fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Organization Attendance Reports</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11"
                                    id="totalFilteredBadge">
                                    Today's Records
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i
                                                class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted">Attendance</li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">Org Check-in Reports
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm"
                        id="quickRefreshBtn" title="Refresh Table">
                        <i class="ti ti-refresh fs-15"></i> Refresh
                    </button>
                    <a href="{{ route('attendance.leave.request') }}"
                        class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-calendar-event fs-15"></i> Leave Requests
                    </a>
                </div>
            </div>

            {{-- View Switcher Nav Bar & Export Actions --}}
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 bg-white p-2 rounded-3 border shadow-sm">
                <ul class="nav nav-pills gap-1" id="reportViewTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $viewMode === 'register' ? 'active fw-bold' : 'text-dark' }} d-flex align-items-center gap-1.5 px-3 py-2 rounded-2"
                            id="tab-register" data-bs-toggle="pill" data-bs-target="#pane-register" type="button"
                            role="tab" aria-selected="{{ $viewMode === 'register' ? 'true' : 'false' }}">
                            <i class="ti ti-calendar-stats fs-16"></i> Register Matrix View (P / A Sheet)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $viewMode === 'logs' ? 'active fw-bold' : 'text-dark' }} d-flex align-items-center gap-1.5 px-3 py-2 rounded-2"
                            id="tab-logs" data-bs-toggle="pill" data-bs-target="#pane-logs" type="button" role="tab"
                            aria-selected="{{ $viewMode === 'logs' ? 'true' : 'false' }}">
                            <i class="ti ti-clock-check fs-16"></i> Detailed Check-in / Out Logs
                        </button>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @php
                        $exportParams = request()->all();
                    @endphp
                    <a href="{{ route('attendance.register.export.excel', $exportParams) }}"
                        class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-1.5 shadow-sm"
                        title="Export Register to Excel (.xlsx)">
                        <i class="ti ti-file-spreadsheet fs-15"></i> Export Excel
                    </a>
                    <a href="{{ route('attendance.register.export.pdf', $exportParams) }}"
                        class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-1.5 shadow-sm"
                        title="Export Register to PDF (.pdf)">
                        <i class="ti ti-file-type-pdf fs-15"></i> Export PDF
                    </a>
                </div>
            </div>

            {{-- Tab Content Panes --}}
            <div class="tab-content" id="reportViewTabContent">

                {{-- ===================== TAB 1: REGISTER MATRIX VIEW (P / A SHEET) ===================== --}}
                <div class="tab-pane fade {{ $viewMode === 'register' ? 'show active' : '' }}" id="pane-register"
                    role="tabpanel" aria-labelledby="tab-register">

                    {{-- 4 Register KPI Summary Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="stat-kpi-card p-3 h-100" style="--card-accent: #0d6efd; --card-soft-bg: #eff6ff;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Total
                                            Employees</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-dark fs-20">{{ count($rows) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-users text-primary"></i> In Current Filter
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box">
                                        <i class="ti ti-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="stat-kpi-card p-3 h-100" style="--card-accent: #06b6d4; --card-soft-bg: #ecfeff;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Register
                                            Days</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-info fs-20">{{ count($dates) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-calendar-event text-info"></i> Active Columns
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #06b6d4; --card-soft-bg: #ecfeff;">
                                        <i class="ti ti-calendar-stats text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="stat-kpi-card p-3 h-100" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Total
                                            Present (P)</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-success fs-20">
                                            {{ number_format($grandTotalPresent) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-user-check text-success"></i> Org-wide P Records
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                                        <i class="ti ti-user-check text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="stat-kpi-card p-3 h-100" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Total
                                            Absent (A)</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-danger fs-20">
                                            {{ number_format($grandTotalAbsent) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-user-x text-danger"></i> Org-wide A Records
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                                        <i class="ti ti-user-x text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Register Filter Card --}}
                    <div class="filter-bar-card p-3 mb-4">
                        <form action="{{ route('attendance.attendance-dashboard.org-reports') }}" method="GET"
                            id="registerFilterForm">
                            <input type="hidden" name="view" value="register">
                            <div class="row g-3 align-items-end">

                                <!-- Show Staff Filter (Default: All Employees) -->
                                <div class="col-md-3">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-user-check text-primary"></i> Show Staff
                                    </label>
                                    <select name="attendance_scope" class="form-select" id="filterRegisterScope">
                                        <option value="all" @selected(($attendanceScope ?? 'all') === 'all')>
                                            All Employees ({{ $totalOrgEmployeesCount }})
                                        </option>
                                        <option value="today" @selected(($attendanceScope ?? '') === 'today')>
                                            Checked-in Today ({{ $totalCheckedInTodayCount }})
                                        </option>
                                        <option value="not_checked_in" @selected(($attendanceScope ?? '') === 'not_checked_in')>
                                            Not Checked-in / Absent ({{ max($totalOrgEmployeesCount - $totalCheckedInPeriodCount, 0) }})
                                        </option>
                                    </select>
                                </div>

                                <!-- Agency Filter -->
                                <div class="col-md-2">
                                    <label
                                        class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-building text-primary"></i> Agency
                                    </label>
                                    <select name="agency_id" class="form-select select2" id="filterRegisterAgency">
                                        <option value="">All Agencies</option>
                                        <option value="unassigned" @selected(request('agency_id') === 'unassigned')>Direct / In-House</option>
                                        @foreach ($agencies as $agency)
                                            <option value="{{ $agency->id }}" @selected(request('agency_id') == $agency->id)>
                                                {{ $agency->agency_name }}
                                                {{ $agency->agency_code ? '(' . $agency->agency_code . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Employee Filter -->
                                <div class="col-md-2">
                                    <label
                                        class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-user text-primary"></i> Employee
                                    </label>
                                    <select name="employee_id" class="form-select select2" id="filterRegisterEmployee">
                                        <option value="">All Filtered Staff</option>
                                        @foreach ($allEmployees as $emp)
                                            <option value="{{ $emp->employee_id }}" @selected(request('employee_id') == $emp->employee_id)>
                                                {{ $emp->employee_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Staff Status Filter -->
                                <div class="col-md-2">
                                    <label
                                        class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-shield-check text-success"></i> Status
                                    </label>
                                    <select name="staff_status" class="form-select" id="filterRegisterStaffStatus">
                                        <option value="active" @selected(($staffStatus ?? request('staff_status', 'active')) === 'active')>Active Only</option>
                                        <option value="all" @selected(($staffStatus ?? request('staff_status')) === 'all')>All Status</option>
                                        <option value="inactive" @selected(($staffStatus ?? request('staff_status')) === 'inactive')>Inactive Only</option>
                                    </select>
                                </div>

                                <!-- Month Filter -->
                                <div class="col-md-1">
                                    <label
                                        class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-calendar text-primary"></i> Month
                                    </label>
                                    <input type="month" name="month" class="form-control px-2"
                                        value="{{ $selectedMonth }}" id="filterRegisterMonth">
                                </div>

                                <!-- Date Range Filters -->
                                <div class="col-md-1">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1">From</label>
                                    <input type="date" name="from_date" class="form-control px-2"
                                        value="{{ $fromDate }}" id="fromRegisterDate">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fs-13 fw-semibold text-dark mb-1">To</label>
                                    <input type="date" name="to_date" class="form-control px-2"
                                        value="{{ $toDate }}" id="toRegisterDate">
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="col-12 d-flex align-items-center justify-content-between pt-2 border-top flex-wrap gap-2">
                                    <div class="fs-12 text-muted">
                                        <strong>Active Filters:</strong> {{ $filterAgencyName }} &bull;
                                        {{ $filterEmployeeName }} &bull; <span
                                            class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($staffStatus ?? 'active') }}
                                            Staff</span> &bull; {{ $dateRangeText }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('attendance.attendance-dashboard.org-reports', ['view' => 'register']) }}"
                                            class="btn btn-light btn-sm px-3 fw-medium">
                                            <i class="ti ti-rotate-clockwise me-1"></i> Reset
                                        </a>
                                        <button type="submit"
                                            class="btn btn-primary btn-sm px-4 fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-filter fs-14"></i> Filter Register
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Register Matrix Table Card --}}
                    <div class="table-container-card mb-4">
                        <div
                            class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-grid-dots text-primary"></i> Register Matrix (P / A Sheet)
                                </h5>
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">{{ $dateRangeText }}</span>

                                <!-- Quick Filter Switcher Pills -->
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('attendance.attendance-dashboard.org-reports', array_merge(request()->all(), ['view' => 'register', 'attendance_scope' => 'checked_in'])) }}"
                                       class="btn {{ ($attendanceScope ?? 'checked_in') === 'checked_in' ? 'btn-primary active fw-semibold' : 'btn-outline-secondary' }}"
                                       title="Show only employees who checked in">
                                       <i class="ti ti-user-check me-1"></i> Checked-in ({{ $totalCheckedInPeriodCount }})
                                    </a>
                                    <a href="{{ route('attendance.attendance-dashboard.org-reports', array_merge(request()->all(), ['view' => 'register', 'attendance_scope' => 'all'])) }}"
                                       class="btn {{ ($attendanceScope ?? '') === 'all' ? 'btn-primary active fw-semibold' : 'btn-outline-secondary' }}"
                                       title="Show all employees in organization">
                                       <i class="ti ti-users me-1"></i> All Employees ({{ $totalOrgEmployeesCount }})
                                    </a>
                                </div>
                            </div>

                            <!-- Status Legend -->
                            <div class="d-flex align-items-center gap-2 fs-12 flex-wrap">
                                <span class="d-flex align-items-center me-1">
                                    <span class="badge-legend cell-p">P</span>
                                    <span class="text-muted">Present</span>
                                </span>
                                <span class="d-flex align-items-center me-1">
                                    <span class="badge-legend cell-a">A</span>
                                    <span class="text-muted">Absent</span>
                                </span>
                                <span class="d-flex align-items-center me-1">
                                    <span class="badge-legend" style="background-color: #fef3c7; color: #92400e;">W</span>
                                    <span class="text-muted">Weekend</span>
                                </span>
                                <span class="d-flex align-items-center me-1">
                                    <span class="badge-legend" style="background-color: #e0f2fe; color: #0369a1;">H</span>
                                    <span class="text-muted">Holiday</span>
                                </span>
                                <span class="d-flex align-items-center me-1">
                                    <span class="badge-legend" style="background-color: #f3e8ff; color: #7e22ce;">L</span>
                                    <span class="text-muted">Leave</span>
                                </span>
                                <span class="d-flex align-items-center">
                                    <span class="badge-legend" style="background-color: #f1f5f9; color: #94a3b8;">-</span>
                                    <span class="text-muted">Future / NA</span>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="register-table-wrapper">
                                <table class="register-table">
                                    <thead>
                                        <tr>
                                            <th class="sticky-col-1" rowspan="2" style="width: 50px;">#</th>
                                            <th class="sticky-col-2" rowspan="2" style="min-width: 220px;">Employee
                                            </th>
                                            {{-- <th rowspan="2" style="min-width: 140px;">Agency</th> --}}
                                            {{-- <th rowspan="2" style="min-width: 130px;">Designation</th> --}}

                                            {{-- Day number headers --}}
                                            @foreach ($dates as $d)
                                                <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}"
                                                    style="width: 38px;">
                                                    <span class="fs-12">{{ $d['day'] }}</span>
                                                </th>
                                            @endforeach

                                            {{-- Summary Columns --}}
                                            <th class="col-summary-p" rowspan="2" style="width: 50px;"
                                                title="Total Present Days">P</th>
                                            <th class="col-summary-a" rowspan="2" style="width: 50px;"
                                                title="Total Absent Working Days">A</th>
                                            <th rowspan="2" style="width: 65px;" title="Attendance Percentage">%</th>
                                            <th rowspan="2" style="width: 80px;">Action</th>
                                        </tr>
                                        <tr>
                                            {{-- Weekday name headers --}}
                                            @foreach ($dates as $d)
                                                <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}"
                                                    style="font-size: 10.5px; font-weight: 500;">
                                                    {{ substr($d['day_name'], 0, 2) }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rows as $index => $row)
                                            <tr>
                                                <td class="sticky-col-1 text-muted fs-13">{{ $index + 1 }}</td>
                                                <td class="sticky-col-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="{{ route('attendance.attendance-dashboard.org-reports.employee', $row['employee_id']) }}"
                                                            class="emp-avatar text-decoration-none"
                                                            title="Click to view 1-Month attendance sheet">
                                                            {{ strtoupper(substr($row['employee_name'], 0, 2)) }}
                                                        </a>
                                                        <div>
                                                            <a href="{{ route('attendance.attendance-dashboard.org-reports.employee', $row['employee_id']) }}"
                                                                class="emp-name-link">
                                                                {{ $row['employee_name'] }}
                                                            </a>
                                                            @if (!empty($row['employee_email']))
                                                                <div class="text-muted fs-12"><i
                                                                        class="ti ti-mail me-0.5"></i>{{ $row['employee_email'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                {{-- <td>
                                                    @if ($row['agency_id'])
                                                        <span
                                                            class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">
                                                            <i class="ti ti-building me-0.5"></i>{{ $row['agency_name'] }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-secondary border fs-11">
                                                            {{ $row['agency_name'] }}
                                                        </span>
                                                    @endif
                                                </td> --}}
                                                {{-- <td class="text-muted fs-11">{{ $row['designation_name'] }}</td> --}}

                                                {{-- Daily Attendance Columns --}}
                                                @foreach ($dates as $d)
                                                    @php
                                                        $status = $row['attendance'][$d['date']] ?? 'A';
                                                    @endphp
                                                    <td class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}"
                                                        title="{{ $row['employee_name'] }} - {{ $d['date'] }}: {{ $status === 'P' ? 'Present' : ($status === 'W' ? 'Weekly Off / Weekend' : ($status === 'H' ? 'Organization Holiday' : ($status === 'L' ? 'Approved Leave' : ($status === '-' ? 'Future Date / NA' : 'Absent')))) }}">
                                                        @if ($status === 'P')
                                                            <span class="cell-p">P</span>
                                                        @elseif ($status === 'W')
                                                            <span class="badge-legend" style="background-color: #fef3c7; color: #92400e;">W</span>
                                                        @elseif ($status === 'H')
                                                            <span class="badge-legend" style="background-color: #e0f2fe; color: #0369a1;" title="{{ $holidays[$d['date']] ?? 'Holiday' }}">H</span>
                                                        @elseif ($status === 'L')
                                                            <span class="badge-legend" style="background-color: #f3e8ff; color: #7e22ce;" title="Approved Leave">L</span>
                                                        @elseif ($status === '-')
                                                            <span class="text-muted fw-bold fs-11">-</span>
                                                        @else
                                                            <span class="cell-a">A</span>
                                                        @endif
                                                    </td>
                                                @endforeach

                                                {{-- Summary Columns --}}
                                                <td class="col-summary-p fs-13">{{ $row['total_present'] }}</td>
                                                <td class="col-summary-a fs-13">{{ $row['total_absent'] }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $row['percentage'] >= 75 ? 'bg-success-subtle text-success' : ($row['percentage'] >= 50 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }} border px-2 py-1 fs-12 fw-bold">
                                                        {{ $row['percentage'] }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('attendance.attendance-dashboard.org-reports.employee', $row['employee_id']) }}"
                                                        class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm py-1 px-2 fs-12"
                                                        title="View Month Sheet">
                                                        <i class="ti ti-calendar-event"></i> Sheet
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($dates) + 6 }}"
                                                    class="text-center py-5 text-muted">
                                                    <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="ti ti-user-x fs-24"></i>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">
                                                        @if (($attendanceScope ?? 'checked_in') === 'checked_in')
                                                            No Checked-in Employees in this Period
                                                        @else
                                                            No Register Records Found
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted fs-12 mb-3">
                                                        @if (($attendanceScope ?? 'checked_in') === 'checked_in')
                                                            No employees have checked in yet during {{ $dateRangeText }}.
                                                        @else
                                                            No active employees found matching the selected filters.
                                                        @endif
                                                    </p>
                                                    @if (($attendanceScope ?? 'checked_in') === 'checked_in')
                                                        <a href="{{ route('attendance.attendance-dashboard.org-reports', array_merge(request()->all(), ['view' => 'register', 'attendance_scope' => 'all'])) }}"
                                                           class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
                                                            <i class="ti ti-users me-1"></i> Show All Employees ({{ $totalOrgEmployeesCount }})
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if (count($rows) > 0)
                                        <tfoot>
                                            <tr class="fw-bold bg-light">
                                                <th class="sticky-col-1 text-center">TOTAL</th>
                                                <th class="sticky-col-2">Daily Summary</th>
                                                <th>-</th>
                                                <th>-</th>
                                                @foreach ($dates as $d)
                                                    <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}"
                                                        style="padding: 4px 2px;">
                                                        @if (($dailyTotals[$d['date']]['P'] ?? 0) > 0)
                                                            <div class="fs-11 text-success fw-bold" title="Present count">P:{{ $dailyTotals[$d['date']]['P'] }}</div>
                                                        @endif
                                                        @if (($dailyTotals[$d['date']]['A'] ?? 0) > 0)
                                                            <div class="fs-11 text-danger fw-bold" title="Absent count">A:{{ $dailyTotals[$d['date']]['A'] }}</div>
                                                        @endif
                                                        @if (($dailyTotals[$d['date']]['W'] ?? 0) > 0 && ($dailyTotals[$d['date']]['P'] ?? 0) === 0)
                                                            <div class="fs-11 text-warning fw-bold" title="Weekend">W</div>
                                                        @endif
                                                        @if (($dailyTotals[$d['date']]['-'] ?? 0) > 0 && ($dailyTotals[$d['date']]['P'] ?? 0) === 0 && ($dailyTotals[$d['date']]['A'] ?? 0) === 0)
                                                            <div class="fs-11 text-muted" title="Future / Upcoming">-</div>
                                                        @endif
                                                    </th>
                                                @endforeach
                                                <th class="col-summary-p fs-13">{{ $grandTotalPresent }}</th>
                                                <th class="col-summary-a fs-13">{{ $grandTotalAbsent }}</th>
                                                <th colspan="2">-</th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ===================== TAB 2: DETAILED CHECK-IN / OUT LOGS ===================== --}}
                <div class="tab-pane fade {{ $viewMode === 'logs' ? 'show active' : '' }}" id="pane-logs"
                    role="tabpanel" aria-labelledby="tab-logs">

                    {{-- Daily Stat / KPI Summary Cards (with Late In & Early Out tracking) --}}
                    <div class="row g-3 mb-4">
                        {{-- Total Active Employees --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100" style="--card-accent: #0d6efd; --card-soft-bg: #eff6ff;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Active
                                            Staff</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-dark fs-20">
                                            {{ number_format($totalEmployees ?? 0) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-users text-primary"></i> Active Employees
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box">
                                        <i class="ti ti-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Checked In Today --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100 kpi-filter-trigger" data-filter-punctuality=""
                                style="--card-accent: #6366f1; --card-soft-bg: #eef2ff;"
                                title="Click to view all check-ins">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Checked
                                            In</span>
                                        <h3 class="mb-0 mt-1 fw-bold fs-20" style="color: #4f46e5;">
                                            {{ number_format($checkedInToday ?? 0) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-login text-indigo" style="color: #4f46e5;"></i> Total Entries
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box">
                                        <i class="ti ti-login"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Late In Today --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100 kpi-filter-trigger" data-filter-punctuality="late"
                                style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;"
                                title="Click to filter Late In arrivals">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-danger fs-12 fw-semibold text-uppercase tracking-wider">Late In
                                            Today</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-danger fs-20">
                                            {{ number_format($todayLateCount ?? 0) }}</h3>
                                        <div class="text-danger fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-clock-exclamation"></i> After Grace Time
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                                        <i class="ti ti-clock-exclamation text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Early Out Today --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100 kpi-filter-trigger" data-filter-punctuality="early_out"
                                style="--card-accent: #f59e0b; --card-soft-bg: #fffbeb;"
                                title="Click to filter Early Out departures">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-warning fs-12 fw-semibold text-uppercase tracking-wider">Early
                                            Out Today</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-warning fs-20">
                                            {{ number_format($todayEarlyCount ?? 0) }}</h3>
                                        <div class="text-warning fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-clock-x"></i> Left Early
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #f59e0b; --card-soft-bg: #fffbeb;">
                                        <i class="ti ti-clock-x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- On Time Today --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100 kpi-filter-trigger" data-filter-punctuality="on_time"
                                style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;"
                                title="Click to filter On-Time entries">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-success fs-12 fw-semibold text-uppercase tracking-wider">On-Time
                                            Today</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-success fs-20">
                                            {{ number_format($todayOnTimeCount ?? 0) }}</h3>
                                        <div class="text-success fs-11 mt-1 d-flex align-items-center gap-1">
                                            <i class="ti ti-clock-check"></i> On Time
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                                        <i class="ti ti-clock-check text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Early In Today (Early Comers) --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100 kpi-filter-trigger" data-filter-punctuality="early_in"
                                style="--card-accent: #06b6d4; --card-soft-bg: #ecfeff;"
                                title="Click to filter Early In arrivals">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fs-12 fw-semibold text-uppercase tracking-wider"
                                            style="color: #0891b2;">Early In Today</span>
                                        <h3 class="mb-0 mt-1 fw-bold fs-20" style="color: #0891b2;">
                                            {{ number_format($todayEarlyComerCount ?? 0) }}</h3>
                                        <div class="fs-11 mt-1 d-flex align-items-center gap-1" style="color: #0891b2;">
                                            <i class="ti ti-clock-play"></i> Before Shift
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #06b6d4; --card-soft-bg: #ecfeff;">
                                        <i class="ti ti-clock-play" style="color: #0891b2;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Currently In Office --}}
                        <div class="col-xxl-2 col-lg-4 col-sm-6">
                            <div class="stat-kpi-card p-3 h-100" style="--card-accent: #64748b; --card-soft-bg: #f1f5f9;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Inside
                                            Office</span>
                                        <h3 class="mb-0 mt-1 fw-bold text-dark fs-20">
                                            {{ number_format($currentlyInside ?? 0) }}</h3>
                                        <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                            <span class="pulse-dot me-1"></span> Working Now
                                        </div>
                                    </div>
                                    <div class="kpi-icon-box" style="--card-accent: #64748b; --card-soft-bg: #f1f5f9;">
                                        <i class="ti ti-building-community text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Advanced Filter Bar --}}
                    <div class="filter-bar-card mb-4">
                        <div
                            class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-filter text-primary fs-18"></i>
                                <span class="fw-bold text-dark fs-14">Filter Attendance Logs</span>
                            </div>
                            {{-- Quick Date Range Preset Buttons (Default: Today) --}}
                            <div class="d-flex flex-wrap align-items-center gap-1.5" id="datePresets">
                                <button type="button" class="filter-pill-btn active" data-range="today">Today</button>
                                <button type="button" class="filter-pill-btn" data-range="yesterday">Yesterday</button>
                                <button type="button" class="filter-pill-btn" data-range="this_week">This Week</button>
                                <button type="button" class="filter-pill-btn" data-range="this_month">This
                                    Month</button>
                                <button type="button" class="filter-pill-btn" data-range="all">All Time</button>
                            </div>
                        </div>

                        <div class="row g-2.5 align-items-end">
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label fs-12 fw-bold text-muted mb-1">From Date</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="ti ti-calendar text-muted"></i></span>
                                    <input type="date" id="fromDate" class="form-control border-start-0"
                                        value="{{ now()->toDateString() }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label fs-12 fw-bold text-muted mb-1">To Date</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="ti ti-calendar text-muted"></i></span>
                                    <input type="date" id="toDate" class="form-control border-start-0"
                                        value="{{ now()->toDateString() }}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label fs-12 fw-bold text-muted mb-1">Employee</label>
                                <select id="employeeFilter" class="form-select form-select-sm select2">
                                    <option value="">All Employees</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->employee_id }}">{{ $employee->employee_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label fs-12 fw-bold text-muted mb-1">Punctuality</label>
                                <select id="punctualityFilter" class="form-select form-select-sm">
                                    <option value="">All Punctuality</option>
                                    <option value="late" {{ request('punctuality') === 'late' ? 'selected' : '' }}>Late
                                        In Only</option>
                                    <option value="early_in"
                                        {{ request('punctuality') === 'early_in' ? 'selected' : '' }}>Early In (Early
                                        Comers)</option>
                                    <option value="early_out"
                                        {{ request('punctuality') === 'early_out' ? 'selected' : '' }}>Early Out Only
                                    </option>
                                    <option value="on_time" {{ request('punctuality') === 'on_time' ? 'selected' : '' }}>
                                        On Time Only</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label fs-12 fw-bold text-muted mb-1">Staff Status</label>
                                <select id="staffStatusFilter" class="form-select form-select-sm">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-9 col-sm-6 d-flex gap-2">
                                <button type="button" id="filterBtn"
                                    class="btn btn-primary btn-sm flex-fill d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                    <i class="ti ti-search fs-14"></i> Apply
                                </button>
                                <button type="button" id="resetBtn"
                                    class="btn btn-light border btn-sm d-flex align-items-center justify-content-center gap-1"
                                    title="Reset All Filters">
                                    <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Table Container Card --}}
                    <div class="table-container-card">
                        <div
                            class="card-header bg-white border-bottom py-3 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-list fs-16"></i>
                                </span>
                                <div>
                                    <h5 class="card-title mb-0 fs-15 fw-bold text-dark">Check-in & Check-out Records</h5>
                                    <small class="text-muted">Live attendance punctuality, shifts, check-in/out timestamps
                                        and duration</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm" style="width: 220px;">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="ti ti-search text-muted"></i></span>
                                    <input type="text" id="customSearchInput" class="form-control border-start-0"
                                        placeholder="Search employee, IP...">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="attendanceReportTable" class="table table-hover align-middle mb-0 w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="4%">#</th>
                                        <th width="20%">Employee</th>
                                        <th width="15%">Work Shift</th>
                                        <th width="11%">Date</th>
                                        <th width="15%">Check In</th>
                                        <th width="15%">Check Out</th>
                                        <th width="9%">Duration</th>
                                        <th width="11%" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        $(document).ready(function() {
            let currentSelectedEmpId = null;
            let currentSelectedMonth = new Date().getMonth() + 1;
            let currentSelectedYear = new Date().getFullYear();

            // Initialize Select2 if available
            if ($.fn.select2) {
                $('#employeeFilter, #filterRegisterEmployee').select2({
                    width: '100%',
                    placeholder: 'All Employees',
                    allowClear: true
                });
                $('#filterRegisterAgency').select2({
                    width: '100%',
                    placeholder: 'All Agencies',
                    allowClear: true
                });
            }

            // Adjust table on tab switch
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                if (e.target.id === 'tab-logs') {
                    if ($.fn.DataTable.isDataTable('#attendanceReportTable')) {
                        $('#attendanceReportTable').DataTable().columns.adjust();
                    }
                }
            });

            // Quick Date Range Preset Buttons Handler
            $('#datePresets .filter-pill-btn').on('click', function() {
                $('#datePresets .filter-pill-btn').removeClass('active');
                $(this).addClass('active');

                const range = $(this).data('range');
                const today = new Date();
                const formatDate = (d) => d.toISOString().split('T')[0];

                if (range === 'today') {
                    $('#fromDate').val(formatDate(today));
                    $('#toDate').val(formatDate(today));
                } else if (range === 'yesterday') {
                    const yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 1);
                    $('#fromDate').val(formatDate(yesterday));
                    $('#toDate').val(formatDate(yesterday));
                } else if (range === 'this_week') {
                    const day = today.getDay();
                    const diffToMon = today.getDate() - day + (day === 0 ? -6 : 1);
                    const monday = new Date(today.setDate(diffToMon));
                    $('#fromDate').val(formatDate(monday));
                    $('#toDate').val(formatDate(new Date()));
                } else if (range === 'this_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    $('#fromDate').val(formatDate(firstDay));
                    $('#toDate').val(formatDate(new Date()));
                } else if (range === 'all') {
                    $('#fromDate').val('');
                    $('#toDate').val('');
                }

                table.ajax.reload();
            });

            // Initialize DataTable with AJAX Server-side Pagination
            const table = $('#attendanceReportTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                dom: '<"top">rt<"d-flex flex-wrap justify-content-between align-items-center px-3 py-3 border-top"ip><"clear">',
                ajax: {
                    url: "{{ route('attendance.attendance-dashboard.org-reports.list') }}",
                    data: function(d) {
                        d.from_date = $('#fromDate').val();
                        d.to_date = $('#toDate').val();
                        d.employee_id = $('#employeeFilter').val();
                        d.punctuality = $('#punctualityFilter').val();
                        d.staff_status = $('#staffStatusFilter').val();
                    }
                },
                order: [],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading records...',
                    emptyTable: `
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="ti ti-calendar-off fs-28"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Attendance Records Found</h6>
                            <p class="text-muted fs-13 mb-0">Try changing the date range or punctuality filter criteria.</p>
                        </div>
                    `,
                    zeroRecords: `
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="ti ti-search-off fs-28"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Matching Results</h6>
                            <p class="text-muted fs-13 mb-0">No records match your search criteria.</p>
                        </div>
                    `,
                    info: "Showing _START_ to _END_ of _TOTAL_ attendance logs",
                    infoEmpty: "Showing 0 to 0 of 0 logs",
                    infoFiltered: "(filtered from _MAX_ total records)",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                        last: '<i class="ti ti-chevrons-right"></i>'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-muted fw-semibold fs-13'
                    },
                    {
                        data: 'employee',
                        render: function(data, type, row) {
                            const empUrl =
                                "{{ url('attendance/attendance-dashboard/org-reports/employee') }}/" +
                                row.employee_id;
                            return `
                                <div class="d-flex align-items-center gap-2.5">
                                    <a href="${empUrl}" class="emp-avatar text-decoration-none" title="Click to view full month attendance">${row.avatar_text || 'EM'}</a>
                                    <div>
                                        <a href="${empUrl}" class="emp-name-link">${data}</a>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5 fs-11 fw-medium">${row.designation || 'Staff'}</span>
                                            ${row.email && row.email !== '-' ? `<span class="text-muted fs-12"><i class="ti ti-mail me-0.5"></i>${row.email}</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'shift_name',
                        render: function(data, type, row) {
                            return `
                                <div>
                                    <div class="fw-semibold text-dark fs-13"><i class="ti ti-clock-play me-1 text-primary"></i>${data || 'General Shift'}</div>
                                    <div class="text-muted fs-12 mt-0.5">${row.shift_timing || ''}</div>
                                    ${row.shift_grace ? `<div class="text-muted fs-11 mt-0.5"><span class="badge bg-light text-muted border px-1 py-0 fs-11">Grace: ${row.shift_grace}m</span></div>` : ''}
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'date',
                        render: function(data, type, row) {
                            return `
                                <div>
                                    <div class="fw-bold text-dark fs-14"><i class="ti ti-calendar me-1 text-primary"></i>${data}</div>
                                    <small class="text-muted fs-12">${row.day_name || ''}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'check_in',
                        render: function(data, type, row) {
                            if (!data || data === '-') return '<span class="text-muted">-</span>';
                            const ipInfo = row.check_in_ip ? ` title="IP: ${row.check_in_ip}"` : '';
                            let punctualityBadge = '';
                            if (row.is_late) {
                                punctualityBadge = `<div class="mt-1">${row.late_badge}</div>`;
                            } else {
                                punctualityBadge =
                                    `<div class="mt-1"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fs-12 fw-semibold"><i class="ti ti-circle-check me-0.5"></i>On Time</span></div>`;
                            }
                            return `
                                <div>
                                    <span class="time-badge time-badge-in"${ipInfo}>
                                        <i class="ti ti-login fs-13"></i> ${data}
                                    </span>
                                    ${punctualityBadge}
                                    ${row.check_in_ip ? `<div class="text-muted fs-11 mt-0.5"><i class="ti ti-world me-0.5"></i>${row.check_in_ip}</div>` : ''}
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'check_out',
                        render: function(data, type, row) {
                            if (data && data !== '-') {
                                const ipInfo = row.check_out_ip ?
                                    ` title="IP: ${row.check_out_ip}"` : '';
                                let earlyBadge = '';
                                if (row.is_early_out) {
                                    earlyBadge = `<div class="mt-1">${row.early_badge}</div>`;
                                } else {
                                    earlyBadge =
                                        `<div class="mt-1"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fs-12 fw-semibold"><i class="ti ti-circle-check me-0.5"></i>Shift Done</span></div>`;
                                }
                                return `
                                    <div>
                                        <span class="time-badge time-badge-out"${ipInfo}>
                                            <i class="ti ti-logout fs-13"></i> ${data}
                                        </span>
                                        ${earlyBadge}
                                        ${row.check_out_ip ? `<div class="text-muted fs-11 mt-0.5"><i class="ti ti-world me-0.5"></i>${row.check_out_ip}</div>` : ''}
                                    </div>
                                `;
                            }
                            return `
                                <div>
                                    <span class="time-badge time-badge-progress">
                                        <span class="pulse-dot me-1"></span> In Office
                                    </span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'working_hours',
                        render: function(data, type, row) {
                            if (!data || data === '-') return '<span class="text-muted">-</span>';
                            if (data === 'In Progress') {
                                return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-12"><i class="ti ti-clock-play me-1"></i>In Progress</span>';
                            }
                            const isGood = (row.total_minutes || 0) >= 480; // >= 8 hours
                            const badgeClass = isGood ?
                                'bg-success-subtle text-success border-success-subtle' :
                                'bg-primary-subtle text-primary border-primary-subtle';
                            return `<span class="badge ${badgeClass} border px-2 py-1 fs-13 font-monospace fw-bold"><i class="ti ti-clock me-1"></i>${data}</span>`;
                        }
                    },
                    {
                        data: 'employee_id',
                        orderable: false,
                        searchable: false,
                        className: 'text-end',
                        render: function(data) {
                            const empUrl =
                                "{{ url('attendance/attendance-dashboard/org-reports/employee') }}/" +
                                data;
                            return `
                                <a href="${empUrl}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm" title="View 1-Month Attendance Sheet">
                                    <i class="ti ti-calendar-event fs-13"></i> Sheet
                                </a>
                            `;
                        }
                    }
                ]
            });

            // KPI Card Click Trigger for Punctuality
            $(document).on('click', '.kpi-filter-trigger', function() {
                const p = $(this).data('filter-punctuality');
                $('#punctualityFilter').val(p);
                table.ajax.reload();
            });

            // Live Search Integration
            $('#customSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Filter Buttons Handler
            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset Button Handler (Resets to Today and Active)
            $('#resetBtn').on('click', function() {
                $('#datePresets .filter-pill-btn').removeClass('active');
                $('#datePresets .filter-pill-btn[data-range="today"]').addClass('active');

                const todayStr = new Date().toISOString().split('T')[0];
                $('#fromDate').val(todayStr);
                $('#toDate').val(todayStr);
                if ($('#employeeFilter').hasClass('select2-hidden-accessible')) {
                    $('#employeeFilter').val('').trigger('change');
                } else {
                    $('#employeeFilter').val('');
                }
                $('#staffStatusFilter').val('active');
                $('#punctualityFilter').val('');
                $('#customSearchInput').val('');
                table.search('').ajax.reload();
            });

            // Quick Refresh Button Handler
            $('#quickRefreshBtn').on('click', function() {
                const $btn = $(this);
                $btn.find('i').addClass('ti-spin');
                table.ajax.reload(function() {
                    $btn.find('i').removeClass('ti-spin');
                });
            });

            // Update Header Record Badge on Table Draw
            table.on('draw', function() {
                const info = table.page.info();
                const fromD = $('#fromDate').val();
                const toD = $('#toDate').val();
                const todayD = new Date().toISOString().split('T')[0];
                const isToday = (fromD === todayD && toD === todayD);
                $('#totalFilteredBadge').text((isToday ? "Today's " : '') + info.recordsDisplay +
                    ' Records');
            });
        });
    </script>
@endpush
