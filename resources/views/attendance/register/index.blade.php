@extends('layout.master')
@section('title', $title ?? 'Attendance Register Report')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .register-page .stat-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .register-page .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .register-page .filter-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
        }

        .register-page .matrix-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Register Matrix Table */
        .register-table-wrapper {
            position: relative;
            overflow-x: auto;
            max-height: 720px;
        }

        .register-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: 12px;
        }

        .register-table th,
        .register-table td {
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            padding: 7px 5px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .register-table thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 11px;
            z-index: 10;
        }

        .register-table thead th.weekend-col {
            background-color: #fef3c7 !important;
            color: #92400e;
        }

        .register-table tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Sticky Left Columns */
        .sticky-col-1 {
            position: sticky;
            left: 0;
            background: #ffffff;
            z-index: 5;
            min-width: 50px;
            text-align: center;
        }

        .sticky-col-2 {
            position: sticky;
            left: 50px;
            background: #ffffff;
            z-index: 5;
            min-width: 200px;
            text-align: left !important;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.04);
        }

        .register-table thead th.sticky-col-1,
        .register-table thead th.sticky-col-2 {
            z-index: 15 !important;
            background: #f1f5f9 !important;
        }

        .register-table tfoot th.sticky-col-1,
        .register-table tfoot th.sticky-col-2 {
            z-index: 15 !important;
            background: #f8fafc !important;
        }

        /* Attendance Status Indicators */
        .cell-p {
            background-color: #dcfce7 !important;
            color: #15803d !important;
            font-weight: 800;
            border-radius: 4px;
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            font-size: 11px;
        }

        .cell-a {
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
            font-weight: 800;
            border-radius: 4px;
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            font-size: 11px;
        }

        .col-summary-p {
            background-color: #f0fdf4 !important;
            color: #166534 !important;
            font-weight: 700;
        }

        .col-summary-a {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            font-weight: 700;
        }

        .badge-legend {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            margin-right: 4px;
        }
    </style>

    <div class="page-wrapper register-page">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="ti ti-calendar-stats text-primary"></i> Attendance Register Report
                    </h3>
                    <nav>
                        <ol class="breadcrumb mb-0 fs-12">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item">Attendance</li>
                            <li class="breadcrumb-item active">Attendance Register</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @php
                        $exportParams = request()->all();
                    @endphp
                    <a href="{{ route('attendance.register.export.excel', $exportParams) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-2 shadow-sm" title="Export to Excel (.xlsx)">
                        <i class="ti ti-file-spreadsheet fs-16"></i> Export Excel
                    </a>
                    <a href="{{ route('attendance.register.export.pdf', $exportParams) }}" class="btn btn-outline-danger d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-2 shadow-sm" title="Export to PDF (.pdf)">
                        <i class="ti ti-file-type-pdf fs-16"></i> Export PDF
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-card p-4 mb-4">
                <form action="{{ route('attendance.register.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        
                        <!-- Agency Filter -->
                        <div class="col-md-3">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                <i class="ti ti-building text-primary"></i> Agency
                            </label>
                            <select name="agency_id" class="form-select select2" id="filterAgency">
                                <option value="">All Agencies</option>
                                <option value="unassigned" @selected(request('agency_id') === 'unassigned')>Direct / In-House</option>
                                @foreach ($agencies as $agency)
                                    <option value="{{ $agency->id }}" @selected(request('agency_id') == $agency->id)>
                                        {{ $agency->agency_name }} {{ $agency->agency_code ? '('.$agency->agency_code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employee Filter -->
                        <div class="col-md-3">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                <i class="ti ti-user text-primary"></i> Employee
                            </label>
                            <select name="employee_id" class="form-select select2" id="filterEmployee">
                                <option value="">All Employees</option>
                                @foreach ($allEmployees as $emp)
                                    <option value="{{ $emp->employee_id }}" @selected(request('employee_id') == $emp->employee_id)>
                                        {{ $emp->employee_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div class="col-md-2">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1 d-flex align-items-center gap-1">
                                <i class="ti ti-calendar text-primary"></i> Month
                            </label>
                            <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}" id="filterMonth">
                        </div>

                        <!-- Date Range Filters -->
                        <div class="col-md-2">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">From Date (Optional)</label>
                            <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}" id="fromDateInput">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">To Date (Optional)</label>
                            <input type="date" name="to_date" class="form-control" value="{{ $toDate }}" id="toDateInput">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 d-flex align-items-center justify-content-between pt-2 border-top">
                            <div class="fs-12 text-muted">
                                <strong>Active Filters:</strong> {{ $filterAgencyName }} &bull; {{ $filterEmployeeName }} &bull; {{ $dateRangeText }}
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('attendance.register.index') }}" class="btn btn-light btn-sm px-3 fw-medium">
                                    <i class="ti ti-rotate-clockwise me-1"></i> Reset
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-filter fs-14"></i> Filter Register
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-20">
                            <i class="ti ti-users"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Total Employees</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ count($rows) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center fs-20">
                            <i class="ti ti-calendar-event"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Total Register Days</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ count($dates) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center fs-20">
                            <i class="ti ti-user-check"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Total Present (P)</span>
                            <h4 class="fw-bold text-success mb-0 fs-20">{{ $grandTotalPresent }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center fs-20">
                            <i class="ti ti-user-x"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Total Absent (A)</span>
                            <h4 class="fw-bold text-danger mb-0 fs-20">{{ $grandTotalAbsent }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Register Matrix Card -->
            <div class="matrix-card mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="fw-bold text-dark mb-0">Register Matrix</h5>
                        <span class="badge bg-light text-muted border fs-11">{{ $dateRangeText }}</span>
                    </div>

                    <!-- Status Legend -->
                    <div class="d-flex align-items-center gap-3 fs-12">
                        <span class="d-flex align-items-center">
                            <span class="badge-legend cell-p">P</span>
                            <span class="text-muted">Present</span>
                        </span>
                        <span class="d-flex align-items-center">
                            <span class="badge-legend cell-a">A</span>
                            <span class="text-muted">Absent</span>
                        </span>
                        <span class="d-flex align-items-center">
                            <span class="badge-legend" style="background-color: #fef3c7; color: #92400e;">W</span>
                            <span class="text-muted">Weekend</span>
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="register-table-wrapper">
                        <table class="register-table">
                            <thead>
                                <tr>
                                    <th class="sticky-col-1" rowspan="2" style="width: 50px;">#</th>
                                    <th class="sticky-col-2" rowspan="2" style="min-width: 220px;">Employee</th>
                                    <th rowspan="2" style="min-width: 140px;">Agency</th>
                                    <th rowspan="2" style="min-width: 130px;">Designation</th>

                                    {{-- Day number headers --}}
                                    @foreach ($dates as $d)
                                        <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}" style="width: 38px;">
                                            <span class="fs-12">{{ $d['day'] }}</span>
                                        </th>
                                    @endforeach

                                    {{-- Summary Columns --}}
                                    <th class="col-summary-p" rowspan="2" style="width: 50px;" title="Total Present Days">P</th>
                                    <th class="col-summary-a" rowspan="2" style="width: 50px;" title="Total Absent Days">A</th>
                                    <th rowspan="2" style="width: 65px;" title="Attendance Percentage">%</th>
                                </tr>
                                <tr>
                                    {{-- Weekday name headers --}}
                                    @foreach ($dates as $d)
                                        <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}" style="font-size: 9px; font-weight: normal;">
                                            {{ substr($d['day_name'], 0, 2) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $index => $row)
                                    <tr>
                                        <td class="sticky-col-1 text-muted fs-12">{{ $index + 1 }}</td>
                                        <td class="sticky-col-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-11" style="width: 28px; height: 28px;">
                                                    {{ strtoupper(substr($row['employee_name'], 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-12">{{ $row['employee_name'] }}</div>
                                                    <div class="text-muted fs-10">{{ $row['employee_email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($row['agency_name'] !== 'Direct / In-House')
                                                <span class="badge border fs-10" style="background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                                    {{ $row['agency_name'] }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border fs-10">
                                                    Direct
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border fs-10">
                                                {{ $row['designation_name'] }}
                                            </span>
                                        </td>

                                        {{-- Daily Attendance Matrix Cells --}}
                                        @foreach ($dates as $d)
                                            @php
                                                $status = $row['attendance'][$d['date']] ?? 'A';
                                            @endphp
                                            <td style="{{ $d['is_weekend'] ? 'background-color: #fffbeb;' : '' }}">
                                                @if ($status === 'P')
                                                    <span class="cell-p" title="{{ $d['date'] }}: Present">P</span>
                                                @else
                                                    <span class="cell-a" title="{{ $d['date'] }}: Absent">A</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        {{-- Summary Values --}}
                                        <td class="col-summary-p">
                                            <span class="fw-bold text-success">{{ $row['total_present'] }}</span>
                                        </td>
                                        <td class="col-summary-a">
                                            <span class="fw-bold text-danger">{{ $row['total_absent'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $row['percentage'] >= 75 ? 'bg-success-subtle text-success' : ($row['percentage'] >= 50 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }} border fs-11 font-monospace">
                                                {{ $row['percentage'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($dates) + 7 }}" class="text-center py-5">
                                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-calendar-off fs-28"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">No Attendance Records Found</h6>
                                            <p class="text-muted fs-13 mb-0">Try changing your filters or choosing another month.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (count($rows) > 0)
                                <tfoot class="bg-light fw-bold text-dark">
                                    {{-- Daily Total Present Row --}}
                                    <tr>
                                        <th class="sticky-col-1 text-center text-success fs-11">P</th>
                                        <th class="sticky-col-2 text-success fs-12">Daily Present (P)</th>
                                        <th colspan="2" class="text-muted fs-11 text-end pe-2">Daily Count:</th>
                                        @foreach ($dates as $d)
                                            <th class="text-success fs-11 {{ $d['is_weekend'] ? 'weekend-col' : '' }}">
                                                {{ $dailyTotals[$d['date']]['P'] ?? 0 }}
                                            </th>
                                        @endforeach
                                        <th class="col-summary-p fs-12">{{ $grandTotalPresent }}</th>
                                        <th class="text-muted">-</th>
                                        <th class="text-muted">-</th>
                                    </tr>
                                    {{-- Daily Total Absent Row --}}
                                    <tr>
                                        <th class="sticky-col-1 text-center text-danger fs-11">A</th>
                                        <th class="sticky-col-2 text-danger fs-12">Daily Absent (A)</th>
                                        <th colspan="2" class="text-muted fs-11 text-end pe-2">Daily Count:</th>
                                        @foreach ($dates as $d)
                                            <th class="text-danger fs-11 {{ $d['is_weekend'] ? 'weekend-col' : '' }}">
                                                {{ $dailyTotals[$d['date']]['A'] ?? 0 }}
                                            </th>
                                        @endforeach
                                        <th class="text-muted">-</th>
                                        <th class="col-summary-a fs-12">{{ $grandTotalAbsent }}</th>
                                        <th class="text-muted">-</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
