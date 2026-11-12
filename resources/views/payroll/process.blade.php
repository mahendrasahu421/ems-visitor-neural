@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 font-weight-bold text-dark">Create Monthly Payroll Batch</h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-12 font-weight-bold">
                        <i class="ti ti-bolt me-1"></i> Workflow Step 2
                    </span>
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.batches') }}" class="text-secondary">Payroll</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Create Batch</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <a href="{{ route('masters.payroll.salary-structure.create') }}" class="btn btn-outline-primary shadow-sm d-flex align-items-center">
                    <i class="ti ti-user-plus me-1.5"></i> Add Employee Salary
                </a>
                <a href="{{ route('payroll.batches') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center">
                    <i class="ti ti-list-details me-1.5"></i> All Payroll Batches
                </a>
                <a href="{{ route('masters.payroll.calculation-rules') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center">
                    <i class="ti ti-settings-cog me-1.5"></i> Working Days Master
                </a>
            </div>
        </div>

        <!-- Workflow Step Navigator -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-3 text-center text-md-start">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3 p-2 rounded bg-light">
                            <div class="avatar avatar-md rounded-circle bg-success text-white d-flex align-items-center justify-content-center font-weight-bold fs-16" style="width: 36px; height: 36px;">
                                <i class="ti ti-check"></i>
                            </div>
                            <div>
                                <span class="fs-11 text-uppercase text-muted fw-bold d-block">Step 1 &bull; Prerequisite</span>
                                <a href="{{ route('masters.payroll.salary-structure') }}" class="text-dark fw-bold fs-13 text-decoration-none">
                                    Add Employee Salary (CTC) <i class="ti ti-external-link fs-11 text-muted"></i>
                                </a>
                                <span class="d-block fs-11 text-success">{{ $employeesWithStructure ?? 0 }} Staff Configured</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3 p-2 rounded border border-primary" style="background-color: #f0f6ff;">
                            <div class="avatar avatar-md rounded-circle bg-primary text-white d-flex align-items-center justify-content-center font-weight-bold fs-16" style="width: 36px; height: 36px;">
                                2
                            </div>
                            <div>
                                <span class="fs-11 text-uppercase text-primary fw-bold d-block">Step 2 &bull; Current Action</span>
                                <span class="text-primary fw-bold fs-13 d-block">Create Payroll Batch</span>
                                <span class="d-block fs-11 text-muted">Compile attendance, leaves &amp; deductions</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-3 p-2 rounded bg-light">
                            <div class="avatar avatar-md rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center font-weight-bold fs-16" style="width: 36px; height: 36px;">
                                3
                            </div>
                            <div>
                                <span class="fs-11 text-uppercase text-muted fw-bold d-block">Step 3 &bull; Final Disbursal</span>
                                <a href="{{ route('payroll.batches') }}" class="text-dark fw-bold fs-13 text-decoration-none">
                                    Payroll Process &amp; Matrix <i class="ti ti-external-link fs-11 text-muted"></i>
                                </a>
                                <span class="d-block fs-11 text-muted">Review 31-day register &amp; disburse pay</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-circle-check fs-22 me-2"></i>
                    <div>
                        <strong class="d-block">Success!</strong>
                        <span class="fs-13">{{ session('success') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-22 me-2"></i>
                    <div>
                        <strong class="d-block">Processing Error:</strong>
                        <span class="fs-13">{{ session('error') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 4 Key Metrics Overview Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-primary text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-users"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Active Employees</span>
                            <h3 class="fw-bold text-dark mb-0 fs-22">{{ $activeEmployeesCount }}</h3>
                            <span class="fs-11 text-muted">Eligible for payroll processing</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-success text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-cash"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Salary Setup Ready</span>
                            <h3 class="fw-bold text-success mb-0 fs-22">{{ $employeesWithStructure }}</h3>
                            @php
                                $coverage = $activeEmployeesCount > 0 ? round(($employeesWithStructure / $activeEmployeesCount) * 100) : 0;
                            @endphp
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">
                                {{ $coverage }}% Headcount Ready
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fefce8 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-warning text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-calculator"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Active Working Days Rule</span>
                            <h5 class="fw-bold text-dark mb-0 fs-15 text-truncate" style="max-width: 170px;" title="{{ $activeRule->rule_name ?? 'Fixed 26 Days' }}">
                                {{ $activeRule->rule_name ?? 'Fixed 26 Days' }}
                            </h5>
                            <a href="{{ route('masters.payroll.calculation-rules') }}" class="fs-11 text-warning fw-semibold text-decoration-none">
                                <i class="ti ti-pencil me-0.5"></i> Change Policy
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-info text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-wallet"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Monthly Gross Commitment</span>
                            <h4 class="fw-bold text-dark mb-0 fs-18">&#8377; {{ number_format($totalMonthlyBudget ?? 0, 2) }}</h4>
                            <span class="fs-11 text-muted">Active monthly budget</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Payroll Generator Form -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i class="ti ti-bolt fs-14"></i>
                            </span>
                            <h5 class="card-title mb-0 font-weight-bold text-dark">Run Payroll Generator</h5>
                        </div>
                        <span class="badge bg-light text-secondary border fs-12">Batch Engine</span>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('payroll.generate') }}" method="POST" id="payrollGenerateForm">
                            @csrf

                            <!-- Period Selection -->
                            <div class="row g-3 mb-3">
                                <div class="col-sm-7">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Payroll Month <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ti ti-calendar"></i></span>
                                        <select name="month" id="select_month" class="form-select form-control font-weight-bold" required onchange="updatePeriodPreview()">
                                            @for($m = 1; $m <= 12; $m++)
                                                @php $monthName = \Carbon\Carbon::createFromDate(null, $m, 1)->format('F'); @endphp
                                                <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                                                    {{ $monthName }} ({{ sprintf('%02d', $m) }})
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Year <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ti ti-calendar-event"></i></span>
                                        <select name="year" id="select_year" class="form-select form-control font-weight-bold" required onchange="updatePeriodPreview()">
                                            @for($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                                                <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Period & Rule Preview Card -->
                            <div class="p-3 mb-3 rounded border" style="background-color: #f8fafc;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted fs-12 font-weight-bold text-uppercase">Target Cycle:</span>
                                    <span class="badge bg-primary text-white fs-12 font-weight-bold px-2 py-0.5" id="preview_target_period">
                                        {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}
                                    </span>
                                </div>
                                <div class="fs-12 text-secondary d-flex align-items-center justify-content-between border-top pt-2 mt-1">
                                    <span><i class="ti ti-calculator text-primary me-1"></i> Deduction Divisor:</span>
                                    <span class="font-weight-bold text-dark">
                                        @if(($activeRule->calculation_basis ?? '') === 'fixed_26_days')
                                            Fixed 26 Working Days
                                        @elseif(($activeRule->calculation_basis ?? '') === 'calendar_days')
                                            Actual Calendar Month Days
                                        @elseif(($activeRule->calculation_basis ?? '') === 'fixed_30_days')
                                            Fixed 30 Days
                                        @else
                                            {{ ucwords(str_replace('_', ' ', $activeRule->calculation_basis ?? 'Fixed 26 Days')) }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Remarks / Notes -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold fs-13 text-secondary">Batch Audit Remarks / Notes</label>
                                <textarea name="notes" rows="2" class="form-control fs-13"
                                    placeholder="e.g. Regular monthly payroll run with biometric sync & statutory compliance..."></textarea>
                                <small class="text-muted fs-11">Optional audit trail description for this batch.</small>
                            </div>

                            <!-- Processing Steps Callout -->
                            <div class="p-3 mb-4 rounded border bg-light-subtle">
                                <span class="font-weight-bold fs-12 text-dark d-block mb-1.5">
                                    <i class="ti ti-cpu text-primary me-1"></i> Automated Processing Pipeline:
                                </span>
                                <ul class="list-unstyled mb-0 fs-12 text-secondary d-flex flex-column gap-1">
                                    <li><i class="ti ti-check text-success me-1"></i> 1. Syncs biometric punch attendance &amp; approved leaves.</li>
                                    <li><i class="ti ti-check text-success me-1"></i> 2. Computes LWP and deducts using active Working Days Rule.</li>
                                    <li><i class="ti ti-check text-success me-1"></i> 3. Calculates statutory deductions (PF, ESI, PT, TDS) or zero cuts for exempt staff.</li>
                                    <li><i class="ti ti-check text-success me-1"></i> 4. Generates individual PDF Payslips &amp; 31-day Review Matrix.</li>
                                </ul>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 fs-14 font-weight-bold shadow-sm d-flex align-items-center justify-content-center"
                                onclick="return confirm('Generate monthly payroll for the selected period?');">
                                <i class="ti ti-player-play me-2 fs-18"></i> Generate Monthly Payroll Batch
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Payroll Batches -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                <i class="ti ti-history fs-14"></i>
                            </span>
                            <h5 class="card-title mb-0 font-weight-bold text-dark">Recent Payroll Batches</h5>
                        </div>
                        <a href="{{ route('payroll.batches') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                            <span>View All Batches</span>
                            <i class="ti ti-chevron-right fs-12"></i>
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light fs-12">
                                    <tr>
                                        <th class="ps-3">Batch Number</th>
                                        <th>Cycle Period</th>
                                        <th>Employees</th>
                                        <th>Total Net Pay</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-13">
                                    @forelse($recentBatches as $b)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ route('payroll.batches.show', $b->id) }}" class="fw-bold text-primary text-decoration-none">
                                                        {{ $b->batch_number }}
                                                    </a>
                                                </div>
                                                <small class="text-muted fs-11">Created {{ $b->created_at ? $b->created_at->diffForHumans() : 'Recently' }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <i class="ti ti-calendar text-muted fs-14"></i>
                                                    <span class="fw-semibold text-dark">
                                                        {{ \Carbon\Carbon::createFromDate($b->year, $b->month, 1)->format('F Y') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                                                    <i class="ti ti-users me-1 text-primary"></i> {{ $b->total_employees }} Staff
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success fs-13">
                                                    &#8377; {{ number_format($b->total_net_pay, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($b->status === 'Paid')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 px-2 py-1">
                                                        <i class="ti ti-check me-1"></i> Paid
                                                    </span>
                                                @elseif($b->status === 'Approved')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 px-2 py-1">
                                                        <i class="ti ti-thumb-up me-1"></i> Approved
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11 px-2 py-1">
                                                        <i class="ti ti-clock me-1"></i> Generated
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('payroll.batches.show', $b->id) }}"
                                                    class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-2.5 py-1">
                                                    <i class="ti ti-layout-grid fs-13"></i>
                                                    <span>Review Grid</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <div class="avatar avatar-xl bg-light text-muted rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-folder-off fs-28"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">No Payroll Batches Found</h6>
                                                <p class="fs-13 text-muted mb-0">Select month &amp; year on the left and click Generate to start your first payroll run.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function updatePeriodPreview() {
        const m = parseInt(document.getElementById('select_month').value) || 1;
        const y = parseInt(document.getElementById('select_year').value) || 2026;
        const date = new Date(y, m - 1, 1);
        const monthName = date.toLocaleString('default', { month: 'long' });
        document.getElementById('preview_target_period').innerText = monthName + ' ' + y;
    }
</script>

@endsection
