@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
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
        padding: 6px 4px;
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
        min-width: 45px;
        text-align: center;
    }

    .sticky-col-2 {
        position: sticky;
        left: 45px;
        background: #ffffff;
        z-index: 5;
        min-width: 220px;
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
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        font-size: 11px;
    }

    .cell-a {
        background-color: #fee2e2 !important;
        color: #b91c1c !important;
        font-weight: 800;
        border-radius: 4px;
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        font-size: 11px;
    }

    .cell-w {
        background-color: #fef3c7 !important;
        color: #92400e !important;
        font-weight: 800;
        border-radius: 4px;
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        font-size: 11px;
    }

    .cell-h {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        font-weight: 800;
        border-radius: 4px;
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        font-size: 11px;
    }

    .cell-l {
        background-color: #f3e8ff !important;
        color: #7e22ce !important;
        font-weight: 800;
        border-radius: 4px;
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        font-size: 11px;
    }

    .cell-empty {
        background-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        font-weight: 600;
        border-radius: 4px;
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
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
        width: 22px;
        height: 22px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 11px;
        margin-right: 4px;
    }
</style>

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Payroll Review: {{ $batch->batch_number }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.batches') }}">Payroll Batches</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $batch->batch_number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2 me-2">
                    <span class="badge bg-light text-dark border px-3 py-2 fs-12 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="ti ti-calculator text-primary"></i> Rule: <strong>{{ $activeRule->policy_name }}</strong>
                    </span>
                </div>
                <div class="mb-2 me-2">
                    <a href="{{ route('masters.payroll.calculation-rules') }}" class="btn btn-outline-info" target="_blank" title="Manage Working Days Policy">
                        <i class="ti ti-settings me-1"></i> Working Days Master
                    </a>
                </div>
                <div class="mb-2 me-2">
                    <a href="{{ route('payroll.batches') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Back to Batches
                    </a>
                </div>
                <div class="mb-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkDisburseModal">
                        <i class="ti ti-cash me-1"></i> Pay / Disburse Selected
                    </button>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

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

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <span class="text-muted small">Period &amp; Batch No</span>
                    <h5 class="mb-0 mt-1 font-weight-bold text-dark">{{ \Carbon\Carbon::createFromDate($batch->year, $batch->month, 1)->format('F Y') }}</h5>
                    <span class="small text-muted">{{ $batch->batch_number }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <span class="text-muted small">Total Employees</span>
                    <h5 class="mb-0 mt-1 font-weight-bold text-primary">{{ $totalCount }} Employees</h5>
                    <span class="small text-muted">Total Net: &#8377; {{ number_format($batch->total_net_pay, 2) }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <span class="text-muted small">Paid / Transferred</span>
                    <h5 class="mb-0 mt-1 font-weight-bold text-success">{{ $paidCount }} Employees</h5>
                    <span class="small text-success font-weight-bold">&#8377; {{ number_format($paidAmount, 2) }} Paid</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <span class="text-muted small">Pending / Unpaid</span>
                    <h5 class="mb-0 mt-1 font-weight-bold {{ $pendingCount > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendingCount }} Employees</h5>
                    <span class="small {{ $pendingCount > 0 ? 'text-danger font-weight-bold' : 'text-muted' }}">&#8377; {{ number_format($pendingAmount, 2) }} Pending</span>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <form action="{{ route('payroll.batches.show', $batch->id) }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold">Search Employee</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by Name, Email, ID or Payslip No..." value="{{ $filterSearch }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small font-weight-bold">Payment Status</label>
                        <select name="payment_status" class="form-select form-control">
                            <option value="">All Statuses (Paid &amp; Pending)</option>
                            <option value="Pending" {{ $filterPaymentStatus == 'Pending' ? 'selected' : '' }}>Pending Only (Not Transferred)</option>
                            <option value="Paid" {{ $filterPaymentStatus == 'Paid' ? 'selected' : '' }}>Paid Only (Transferred)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small font-weight-bold">Attendance / LWP</label>
                        <select name="lwp_status" class="form-select form-control">
                            <option value="">All Employees</option>
                            <option value="zero_lwp" {{ $filterLwpStatus == 'zero_lwp' ? 'selected' : '' }}>Full Attendance (LWP = 0)</option>
                            <option value="with_lwp" {{ $filterLwpStatus == 'with_lwp' ? 'selected' : '' }}>Has LWP (Loss of Pay &gt; 0)</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100" title="Apply Filter"><i class="ti ti-filter"></i></button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('payroll.batches.show', $batch->id) }}" class="btn btn-outline-secondary w-100" title="Reset Filters"><i class="ti ti-refresh"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Mode Switcher Navigation (Pills) -->
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <ul class="nav nav-pills gap-2" id="batchViewTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-1" id="tab-salary" data-bs-toggle="pill" data-bs-target="#pane-salary" type="button" role="tab" aria-controls="pane-salary" aria-selected="true">
                        <i class="ti ti-table fs-15"></i> Salary &amp; Payment Breakdown ({{ count($payslips) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-1" id="tab-register" data-bs-toggle="pill" data-bs-target="#pane-register" type="button" role="tab" aria-controls="pane-register" aria-selected="false">
                        <i class="ti ti-calendar-stats fs-15"></i> Monthly Attendance Register Grid (1..{{ count($matrixDates) }} Days)
                    </button>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <span class="fs-12 text-muted">
                    Total In Batch: <strong>{{ $totalCount }}</strong> | Filtered: <strong>{{ count($payslips) }}</strong>
                </span>
            </div>
        </div>

        <div class="tab-content" id="batchViewTabContent">
            <!-- PANE 1: SALARY & PAYMENT BREAKDOWN TABLE -->
            <div class="tab-pane fade show active" id="pane-salary" role="tabpanel" aria-labelledby="tab-salary">
                <!-- Checkbox Action Toolbar -->
                <div class="card shadow-sm border-0 mb-3 bg-light">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                <label class="form-check-label font-weight-bold" for="selectAllCheckbox">
                                    Select All ({{ count($payslips) }} displayed)
                                </label>
                            </div>
                            <span class="badge bg-primary fs-13 py-2 px-3" id="selectionSummaryBadge">
                                0 Selected &bull; Total: &#8377; 0.00
                            </span>
                        </div>

                        <div class="d-flex align-items-center flex-wrap">
                            <button type="button" class="btn btn-sm btn-success me-2" onclick="openBulkDisburseModal()" id="btnDisburseSelected" disabled>
                                <i class="ti ti-check me-1"></i> Pay Selected Employees
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmRevertPending()" id="btnRevertSelected" disabled>
                                <i class="ti ti-rotate-clockwise me-1"></i> Mark Selected as Pending
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden Form for Reverting to Pending -->
                <form id="revertPendingForm" action="{{ route('payroll.batches.mark-pending', $batch->id) }}" method="POST" style="display: none;">
                    @csrf
                    <div id="revertPayslipIdsContainer"></div>
                </form>

                <!-- Employee Payslips Table -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Employee Salary &amp; Attendance Breakdown</h5>
                        <span class="text-muted small">Showing {{ count($payslips) }} employee record(s)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40" class="text-center">#</th>
                                        <th width="40">SL</th>
                                        <th>Employee</th>
                                        <th>Designation</th>
                                        <th>Payable / Total</th>
                                        <th>LWP</th>
                                        <th>Earned Gross</th>
                                        <th>Deductions</th>
                                        <th>Net Salary</th>
                                        <th>Payment Status</th>
                                        <th class="text-center" width="140">Attendance Log</th>
                                        <th class="text-end" width="90">PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payslips as $idx => $slip)
                                        <tr class="{{ $slip->payment_status == 'Paid' ? 'table-light' : '' }}">
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input payslip-checkbox"
                                                    value="{{ $slip->id }}"
                                                    data-amount="{{ $slip->net_salary }}"
                                                    data-name="{{ $slip->employee->employee_name ?? 'Employee' }}"
                                                    data-status="{{ $slip->payment_status }}"
                                                    onchange="updateSelectionState()">
                                            </td>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <strong>{{ $slip->employee->employee_name ?? 'N/A' }}</strong>
                                                <div class="small text-muted">ID: {{ $slip->employee_id }} | {{ $slip->employee->employee_email ?? '' }}</div>
                                            </td>
                                            <td>{{ $slip->designation_name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $slip->payable_days }} / {{ $slip->total_days }} Days
                                                </span>
                                            </td>
                                            <td>
                                                <span class="{{ $slip->lwp_days > 0 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                                    {{ $slip->lwp_days }} Days
                                                </span>
                                            </td>
                                            <td>&#8377; {{ number_format($slip->total_earnings, 2) }}</td>
                                            <td>
                                                <span class="text-danger small">&#8377; {{ number_format($slip->total_deductions, 2) }}</span>
                                            </td>
                                            <td class="text-success font-weight-bold fs-14">
                                                &#8377; {{ number_format($slip->net_salary, 2) }}
                                            </td>
                                            <td>
                                                @if($slip->payment_status == 'Paid')
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-check me-1"></i> Paid
                                                    </span>
                                                    @if($slip->payment_date)
                                                        <div class="small text-muted" style="font-size: 11px;">
                                                            {{ \Carbon\Carbon::parse($slip->payment_date)->format('d M, Y') }}
                                                        </div>
                                                    @endif
                                                    @if($slip->transaction_reference)
                                                        <div class="small text-muted" style="font-size: 10px;" title="{{ $slip->transaction_reference }}">
                                                            UTR: {{ Str::limit($slip->transaction_reference, 12) }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="ti ti-clock me-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <!-- View Attendance Log Button -->
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="openAttendanceModal({{ $slip->employee_id }}, '{{ addslashes($slip->employee->employee_name ?? 'Employee') }}', {{ $batch->month }}, {{ $batch->year }})"
                                                    title="View daily attendance punch-in, punch-out, and leaves">
                                                    <i class="ti ti-calendar-time me-1"></i> Details
                                                </button>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('payroll.payslips.download', $slip->id) }}" class="btn btn-sm btn-outline-danger" title="Download PDF Payslip">
                                                    <i class="ti ti-download"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-5 text-muted">
                                                <i class="ti ti-search fs-30 d-block mb-2"></i>
                                                No employee records found matching your filter criteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANE 2: MONTHLY ATTENDANCE REGISTER MATRIX GRID (Matching media_1791463091141.png) -->
            <div class="tab-pane fade" id="pane-register" role="tabpanel" aria-labelledby="tab-register">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <h5 class="fw-bold text-dark mb-0">Monthly Attendance Register Matrix</h5>
                            <span class="badge bg-light text-dark border fs-12">
                                {{ \Carbon\Carbon::createFromDate($batch->year, $batch->month, 1)->format('F Y') }} (1..{{ count($matrixDates) }} Days)
                            </span>
                        </div>

                        <!-- Status Legend matching media_1791463091141.png -->
                        <div class="d-flex align-items-center gap-3 fs-12 flex-wrap">
                            <span class="d-flex align-items-center">
                                <span class="badge-legend cell-p">P</span>
                                <span class="text-muted">Present</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="badge-legend cell-h">H</span>
                                <span class="text-muted">Holiday</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="badge-legend cell-w">W</span>
                                <span class="text-muted">Weekend</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="badge-legend cell-l">L</span>
                                <span class="text-muted">Leave</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="badge-legend cell-a">A</span>
                                <span class="text-muted">Absent / LWP</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <span class="badge-legend cell-empty">-</span>
                                <span class="text-muted">Unmarked</span>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="register-table-wrapper">
                            <table class="register-table">
                                <thead>
                                    <tr>
                                        <th class="sticky-col-1" rowspan="2" style="width: 45px;">#</th>
                                        <th class="sticky-col-2" rowspan="2" style="min-width: 220px;">Employee</th>
                                        <th rowspan="2" style="min-width: 140px;">Agency</th>
                                        <th rowspan="2" style="min-width: 130px;">Designation</th>

                                        {{-- Day number headers --}}
                                        @foreach ($matrixDates as $d)
                                            <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}" style="width: 38px;">
                                                <span class="fs-12">{{ $d['day'] }}</span>
                                            </th>
                                        @endforeach

                                        {{-- Summary Columns --}}
                                        <th class="col-summary-p" rowspan="2" style="width: 45px;" title="Total Present Days">P</th>
                                        <th class="col-summary-a" rowspan="2" style="width: 45px;" title="Total Absent / LWP Days">A</th>
                                        <th rowspan="2" style="width: 40px;" title="Weekly Offs">W</th>
                                        <th rowspan="2" style="width: 40px;" title="Holidays">H</th>
                                        <th rowspan="2" style="width: 40px;" title="Approved Leaves">L</th>
                                        <th rowspan="2" style="width: 60px;" title="Attendance Percentage">%</th>
                                        <th rowspan="2" style="width: 80px;" title="View Details">Action</th>
                                    </tr>
                                    <tr>
                                        {{-- Weekday name headers --}}
                                        @foreach ($matrixDates as $d)
                                            <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }}" style="font-size: 9px; font-weight: normal;">
                                                {{ substr($d['day_name'], 0, 2) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($matrixRows as $idx => $row)
                                        <tr>
                                            <td class="sticky-col-1 text-muted fs-12">{{ $idx + 1 }}</td>
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
                                            @foreach ($matrixDates as $d)
                                                @php
                                                    $st = $row['daily'][$d['date']] ?? '-';
                                                @endphp
                                                <td style="{{ $d['is_weekend'] ? 'background-color: #fffbeb;' : '' }}">
                                                    @if ($st === 'P')
                                                        <span class="cell-p" title="{{ $d['date'] }}: Present">P</span>
                                                    @elseif ($st === 'A')
                                                        <span class="cell-a" title="{{ $d['date'] }}: Absent / LWP">A</span>
                                                    @elseif ($st === 'W')
                                                        <span class="cell-w" title="{{ $d['date'] }}: Weekend">W</span>
                                                    @elseif ($st === 'H')
                                                        <span class="cell-h" title="{{ $d['date'] }}: Holiday">H</span>
                                                    @elseif ($st === 'L')
                                                        <span class="cell-l" title="{{ $d['date'] }}: Approved Leave">L</span>
                                                    @else
                                                        <span class="cell-empty" title="{{ $d['date'] }}">-</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            {{-- Summary Values --}}
                                            <td class="col-summary-p">
                                                <span class="fw-bold text-success">{{ $row['p_count'] }}</span>
                                            </td>
                                            <td class="col-summary-a">
                                                <span class="fw-bold text-danger">{{ $row['a_count'] }}</span>
                                            </td>
                                            <td>{{ $row['w_count'] }}</td>
                                            <td>{{ $row['h_count'] }}</td>
                                            <td>{{ $row['l_count'] }}</td>
                                            <td>
                                                <span class="badge {{ $row['percentage'] >= 75 ? 'bg-success-subtle text-success' : ($row['percentage'] >= 50 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }} border fs-10 font-monospace">
                                                    {{ $row['percentage'] }}%
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 fs-11"
                                                    onclick="openAttendanceModal({{ $row['employee']->employee_id }}, '{{ addslashes($row['employee_name']) }}', {{ $batch->month }}, {{ $batch->year }})"
                                                    title="View Detailed Logs">
                                                    <i class="ti ti-calendar-time me-1"></i> Logs
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($matrixDates) + 11 }}" class="text-center py-5 text-muted">
                                                No attendance records found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if (count($matrixRows) > 0)
                                    <tfoot class="bg-light fw-bold text-dark">
                                        {{-- Daily Total Summary Row matching media_1791463091141.png --}}
                                        <tr>
                                            <th class="sticky-col-1 text-center text-primary fs-11">TOTAL</th>
                                            <th class="sticky-col-2 text-primary fs-12">Daily Attendance Summary</th>
                                            <th colspan="2" class="text-muted fs-11 text-end pe-2">Daily Count:</th>
                                            @foreach ($matrixDates as $d)
                                                <th class="{{ $d['is_weekend'] ? 'weekend-col' : '' }} fs-10 p-1 text-center">
                                                    <div class="text-success fw-bold">P:{{ $matrixDailyTotals[$d['date']]['P'] }}</div>
                                                    @if($matrixDailyTotals[$d['date']]['A'] > 0)
                                                        <div class="text-danger fw-bold">A:{{ $matrixDailyTotals[$d['date']]['A'] }}</div>
                                                    @endif
                                                    @if($d['is_weekend'])
                                                        <div class="text-warning">W</div>
                                                    @endif
                                                </th>
                                            @endforeach
                                            <th class="col-summary-p fs-12 text-success">{{ $grandTotalPresent }}</th>
                                            <th class="col-summary-a fs-12 text-danger">{{ $grandTotalAbsent }}</th>
                                            <th class="text-muted">-</th>
                                            <th class="text-muted">-</th>
                                            <th class="text-muted">-</th>
                                            <th class="text-muted">-</th>
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

    </div>
</div>

<!-- ================= 1. ATTENDANCE & LEAVE REGISTER MODAL ================= -->
<div class="modal fade" id="attendanceRegisterModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title text-white mb-0" id="attendanceModalLabel">
                        <i class="ti ti-calendar-check me-2"></i> Monthly Attendance &amp; Leave Register
                    </h5>
                    <span class="small opacity-75" id="modalEmpSubTitle">Employee Details</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">

                <!-- Loading Spinner -->
                <div id="modalLoadingState" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Fetching biometric attendance punches and approved leaves...</p>
                </div>

                <!-- Content Body -->
                <div id="modalContentState" style="display: none;">
                    <!-- Quick Metrics Counter Bar -->
                    <div class="row g-2 mb-3 text-center" id="metricsCounterRow">
                        <div class="col-md-2 col-4">
                            <div class="p-2 border rounded bg-light">
                                <span class="small text-muted d-block">Total Days</span>
                                <h5 class="mb-0 font-weight-bold" id="m_total_days">0</h5>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="p-2 border rounded bg-success bg-opacity-10 text-success">
                                <span class="small d-block">Present Days</span>
                                <h5 class="mb-0 font-weight-bold text-success" id="m_present_days">0</h5>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="p-2 border rounded bg-info bg-opacity-10 text-info">
                                <span class="small d-block">Paid Leaves</span>
                                <h5 class="mb-0 font-weight-bold text-info" id="m_paid_leaves">0</h5>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="p-2 border rounded bg-secondary bg-opacity-10 text-secondary">
                                <span class="small d-block">Weekly Offs</span>
                                <h5 class="mb-0 font-weight-bold text-secondary" id="m_weekly_offs">0</h5>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="p-2 border rounded bg-warning bg-opacity-10 text-warning">
                                <span class="small d-block">Holidays</span>
                                <h5 class="mb-0 font-weight-bold text-warning" id="m_holidays">0</h5>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="p-2 border rounded bg-danger bg-opacity-10 text-danger">
                                <span class="small d-block">Loss of Pay (LWP)</span>
                                <h5 class="mb-0 font-weight-bold text-danger" id="m_lwp_days">0</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Register Table -->
                    <div class="table-responsive border rounded" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-sm table-striped table-hover align-middle mb-0 text-center">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th width="70">Date</th>
                                    <th width="100">Day</th>
                                    <th>Check-In </th>
                                    <th>Check-Out</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Details / Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="dailyAttendanceTableBody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= 2. SELECTIVE / BULK DISBURSE MODAL ================= -->
<div class="modal fade" id="bulkDisburseModal" tabindex="-1" aria-labelledby="disburseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('payroll.batches.disburse-selected', $batch->id) }}" method="POST" id="disburseForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white" id="disburseModalLabel">
                        <i class="ti ti-cash me-1"></i> Pay / Disburse Selected Employees
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Selected Info Box -->
                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="small text-muted d-block">Selected for Payment:</span>
                            <strong id="modalSelectedCount" class="fs-16">0 Employees</strong>
                        </div>
                        <div class="text-end">
                            <span class="small text-muted d-block">Total Payout Amount:</span>
                            <strong id="modalSelectedAmount" class="fs-18 text-success">&#8377; 0.00</strong>
                        </div>
                    </div>

                    <!-- Hidden inputs container for selected payslip_ids -->
                    <div id="selectedPayslipInputsContainer"></div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_mode" class="form-select form-control" required>
                            <option value="Bank Transfer">Bank Transfer (NEFT / RTGS / IMPS)</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Bank UTR / Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. UTR1234567890 / Chq #45210">
                        <small class="text-muted">Enter UTR number if transferred via corporate banking portal</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="ti ti-check me-1"></i> Confirm &amp; Mark as Paid
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // -------------------------------------------------------------
    // CHECKBOX SELECTION LOGIC
    // -------------------------------------------------------------
    function toggleSelectAll(selectAllEl) {
        const checkboxes = document.querySelectorAll('.payslip-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = selectAllEl.checked;
        });
        updateSelectionState();
    }

    function updateSelectionState() {
        const checkboxes = document.querySelectorAll('.payslip-checkbox:checked');
        let totalAmount = 0;
        let count = checkboxes.length;

        checkboxes.forEach(cb => {
            totalAmount += parseFloat(cb.dataset.amount || 0);
        });

        // Update Counter Banner
        const badge = document.getElementById('selectionSummaryBadge');
        if (badge) {
            badge.innerHTML = `${count} Selected &bull; Total: &#8377; ${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        }

        // Enable / Disable Action Buttons
        const btnDisburse = document.getElementById('btnDisburseSelected');
        const btnRevert = document.getElementById('btnRevertSelected');

        if (btnDisburse) btnDisburse.disabled = (count === 0);
        if (btnRevert) btnRevert.disabled = (count === 0);

        // Update Modal Information
        const modalCount = document.getElementById('modalSelectedCount');
        const modalAmount = document.getElementById('modalSelectedAmount');
        if (modalCount) modalCount.innerText = `${count} Employee(s)`;
        if (modalAmount) modalAmount.innerHTML = `&#8377; ${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

        // Populate Hidden Inputs for Disburse Form
        const container = document.getElementById('selectedPayslipInputsContainer');
        if (container) {
            container.innerHTML = '';
            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'payslip_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
        }
    }

    function openBulkDisburseModal() {
        const checkboxes = document.querySelectorAll('.payslip-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one employee from the list.');
            return;
        }
        const modal = new bootstrap.Modal(document.getElementById('bulkDisburseModal'));
        modal.show();
    }

    function confirmRevertPending() {
        const checkboxes = document.querySelectorAll('.payslip-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one employee to revert.');
            return;
        }

        if (!confirm(`Are you sure you want to revert ${checkboxes.length} selected employee(s) back to Pending status?`)) {
            return;
        }

        const revertContainer = document.getElementById('revertPayslipIdsContainer');
        revertContainer.innerHTML = '';
        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'payslip_ids[]';
            input.value = cb.value;
            revertContainer.appendChild(input);
        });

        document.getElementById('revertPendingForm').submit();
    }

    // -------------------------------------------------------------
    // ATTENDANCE & LEAVE REGISTER MODAL LOGIC (AJAX)
    // -------------------------------------------------------------
    function openAttendanceModal(employeeId, employeeName, month, year) {
        const modalEl = document.getElementById('attendanceRegisterModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        document.getElementById('modalEmpSubTitle').innerText = `${employeeName} [ID: ${employeeId}] &bull; Period: ${month}/${year}`;
        document.getElementById('modalLoadingState').style.display = 'block';
        document.getElementById('modalContentState').style.display = 'none';

        const url = `{{ url('payroll/attendance-details') }}/${employeeId}/${month}/${year}`;

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Could not fetch attendance records.');
                return;
            }

            // Update Metrics
            document.getElementById('m_total_days').innerText = data.metrics.total_days;
            document.getElementById('m_present_days').innerText = data.metrics.present_days;
            document.getElementById('m_paid_leaves').innerText = data.metrics.paid_leave_days;
            document.getElementById('m_weekly_offs').innerText = data.metrics.weekly_off_days;
            document.getElementById('m_holidays').innerText = data.metrics.holiday_days;
            document.getElementById('m_lwp_days').innerText = data.metrics.lwp_days;

            // Populate Daily Table
            const tbody = document.getElementById('dailyAttendanceTableBody');
            tbody.innerHTML = '';

            data.records.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="font-weight-bold">${r.day}</td>
                    <td class="text-start">${r.date} <span class="small text-muted">(${r.day_name.substr(0, 3)})</span></td>
                    <td class="${r.check_in !== '-' ? 'text-success font-weight-bold' : 'text-muted'}">${r.check_in}</td>
                    <td class="${r.check_out !== '-' ? 'text-primary font-weight-bold' : 'text-muted'}">${r.check_out}</td>
                    <td class="small">${r.duration}</td>
                    <td><span class="badge ${r.badge_class}">${r.status_label}</span></td>
                    <td class="text-start small text-muted">${r.remark}</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('modalLoadingState').style.display = 'none';
            document.getElementById('modalContentState').style.display = 'block';
        })
        .catch(err => {
            console.error(err);
            document.getElementById('modalLoadingState').innerHTML = '<div class="text-danger py-4">Failed to load attendance details.</div>';
        });
    }
</script>

@endsection
