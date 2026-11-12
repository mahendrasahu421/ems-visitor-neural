@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .bulk-inc-table th {
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
    .bulk-inc-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 font-weight-bold">{{ $title }}</h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12">
                        <i class="ti ti-users-group me-1"></i> 1-Click Company Batch Revision
                    </span>
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.increments.index') }}" class="text-secondary">Annual Increments</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Bulk Increment Center</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <a href="{{ route('payroll.increments.index') }}" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back to Increments Hub
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-20 me-2"></i>
                    <div>
                        <strong>Please resolve errors:</strong>
                        <ul class="mb-0 mt-1 ps-3 fs-13">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('payroll.increments.bulk.store') }}" method="POST" id="bulkIncrementForm">
            @csrf

            <!-- Section 1: Batch Configuration -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Batch Revision Parameters</h5>
                    </div>
                    <span class="badge bg-light text-secondary border fs-12">Universal Rules</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Review Cycle Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="review_cycle" class="form-control"
                                value="{{ old('review_cycle', 'Annual Appraisal ' . date('Y')) }}" required>
                            <small class="text-muted fs-11">Cycle identifier tagged on all records</small>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Uniform Hike Percentage (%) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark font-weight-bold">%</span>
                                <input type="number" step="0.1" min="0.1" max="100" name="hike_percentage" id="bulk_hike_percent"
                                    class="form-control form-control-lg font-weight-bold border-primary"
                                    value="{{ old('hike_percentage', 10.0) }}" required oninput="recalcBulkImpact()">
                            </div>
                            <small class="text-muted fs-11">Applied uniformly to selected staff</small>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Effective From <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="effective_date" class="form-control"
                                value="{{ old('effective_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                            <small class="text-muted fs-11">Effective payroll cycle start date</small>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Batch Remarks
                            </label>
                            <input type="text" name="remarks" class="form-control"
                                placeholder="e.g. Standard 10% annual organization-wide hike."
                                value="{{ old('remarks', 'Standard annual increment batch applied.') }}">
                            <small class="text-muted fs-11">Recorded in audit remarks</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Employee Selection Table -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Select Employees for Batch Revision</h5>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectDueOnly()">
                            <i class="ti ti-calendar-due me-1"></i> Select Only Due for Review ({{ $dueEmployees->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(true)">
                            <i class="ti ti-check-all me-1"></i> Select All Active ({{ $allEmployees->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-light border" onclick="selectAll(false)">
                            Deselect All
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table bulk-inc-table mb-0 w-100" id="bulkTable">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="check_all" class="form-check-input" onchange="toggleAllCheckboxes(this)">
                                    </th>
                                    <th>Employee</th>
                                    <th>Designation / Agency</th>
                                    <th>Joining Date</th>
                                    <th>Tenure</th>
                                    <th>Current Gross</th>
                                    <th>Current CTC</th>
                                    <th>Projected New Gross</th>
                                    <th>Projected New CTC</th>
                                    <th>Review Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dueIds = $dueEmployees->pluck('employee_id')->toArray();
                                @endphp
                                @foreach($allEmployees as $emp)
                                    @php
                                        $structure = $emp->salaryStructure;
                                        $gross = $structure ? (float)$structure->monthly_gross : 0;
                                        $ctc = $structure ? (float)$structure->ctc_annual : ($gross * 12);
                                        $isDue = in_array($emp->employee_id, $dueIds, true);
                                        $joining = $emp->joining_date ? \Carbon\Carbon::parse($emp->joining_date) : null;
                                        $tenure = $joining ? $joining->diffForHumans(['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) : 'N/A';
                                    @endphp
                                    <tr class="emp-row {{ $isDue ? 'table-warning-subtle' : '' }}" data-emp-id="{{ $emp->employee_id }}" data-gross="{{ $gross }}" data-ctc="{{ $ctc }}">
                                        <td class="text-center">
                                            <input type="checkbox" name="employee_ids[]" value="{{ $emp->employee_id }}"
                                                class="form-check-input emp-checkbox"
                                                data-is-due="{{ $isDue ? '1' : '0' }}"
                                                {{ $isDue ? 'checked' : '' }}
                                                onchange="recalcBulkImpact()">
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark fs-13">{{ $emp->employee_name }}</div>
                                            <div class="text-muted fs-11">ID: {{ $emp->employee_id }} &bull; {{ $emp->employee_email }}</div>
                                        </td>
                                        <td>
                                            <div class="text-dark fs-12">{{ $emp->designation->name ?? 'Staff' }}</div>
                                            <div class="text-muted fs-11">{{ $emp->agency->agency_name ?? 'Direct' }}</div>
                                        </td>
                                        <td>
                                            <span class="fs-12 text-secondary">{{ $joining ? $joining->format('d M, Y') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border fs-11">{{ $tenure }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-dark fs-12">&#8377; {{ number_format($gross, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-dark fs-12">&#8377; {{ number_format($ctc, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-success fs-13 proj-gross">&#8377; 0.00</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-primary fs-13 proj-ctc">&#8377; 0.00</span>
                                        </td>
                                        <td>
                                            @if($isDue)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">
                                                    <i class="ti ti-alarm me-1"></i>Due for Review
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border fs-11">Regular</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Live Financial Impact Bar -->
            <div class="card border border-primary-subtle shadow-sm mb-4" style="background: linear-gradient(135deg, #f8faff 0%, #edf2fc 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3 text-center text-md-start">
                        <div class="col-md-3 border-end">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                Selected Employees
                            </div>
                            <h2 class="mb-0 text-dark font-weight-bold" id="stat_selected_count">0</h2>
                            <span class="fs-11 text-muted">Eligible staff to be revised</span>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                Current Total Annual CTC
                            </div>
                            <h3 class="mb-0 text-muted fs-20 font-weight-bold" id="stat_current_ctc">&#8377; 0.00</h3>
                            <span class="fs-11 text-muted">Baseline cost</span>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                Projected Total Annual CTC
                            </div>
                            <h3 class="mb-0 text-primary fs-20 font-weight-bold" id="stat_projected_ctc">&#8377; 0.00</h3>
                            <span class="fs-11 text-muted">After uniform hike</span>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                Annual Budget Impact (+ Diff)
                            </div>
                            <h3 class="mb-0 text-success fs-20 font-weight-bold" id="stat_budget_diff">+&#8377; 0.00</h3>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 mt-1 font-weight-semibold">
                                Total Annual Cost Increment
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="card border-0 shadow-sm bg-white mb-5">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-13"><i class="ti ti-alert-circle me-1"></i>Submitting will create individual increment records and update salary structures for all selected staff.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('payroll.increments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center shadow-sm" onclick="return confirm('Are you sure you want to apply this annual increment batch to all selected employees? This will update their active salary structures.')">
                            <i class="ti ti-check me-1 fs-16"></i> Execute Bulk Annual Increment
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleAllCheckboxes(master) {
        $('.emp-checkbox').prop('checked', master.checked);
        recalcBulkImpact();
    }

    function selectAll(state) {
        $('.emp-checkbox').prop('checked', state);
        $('#check_all').prop('checked', state);
        recalcBulkImpact();
    }

    function selectDueOnly() {
        $('.emp-checkbox').each(function() {
            const isDue = $(this).data('is-due') === 1 || $(this).data('is-due') === '1';
            $(this).prop('checked', isDue);
        });
        $('#check_all').prop('checked', false);
        recalcBulkImpact();
    }

    function recalcBulkImpact() {
        const hikePercent = parseFloat($('#bulk_hike_percent').val()) || 0;
        const hikeMultiplier = 1 + (hikePercent / 100);

        let selectedCount = 0;
        let totalCurrentCtc = 0;
        let totalProjectedCtc = 0;

        $('.emp-row').each(function() {
            const row = $(this);
            const chk = row.find('.emp-checkbox');
            const gross = parseFloat(row.data('gross')) || 0;
            const ctc = parseFloat(row.data('ctc')) || (gross * 12);

            const newGross = gross * hikeMultiplier;
            const newCtc = ctc * hikeMultiplier;

            row.find('.proj-gross').text('₹ ' + newGross.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            row.find('.proj-ctc').text('₹ ' + newCtc.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            if (chk.is(':checked')) {
                selectedCount++;
                totalCurrentCtc += ctc;
                totalProjectedCtc += newCtc;
            }
        });

        const budgetDiff = totalProjectedCtc - totalCurrentCtc;

        $('#stat_selected_count').text(selectedCount);
        $('#stat_current_ctc').text('₹ ' + totalCurrentCtc.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#stat_projected_ctc').text('₹ ' + totalProjectedCtc.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#stat_budget_diff').text('+₹ ' + budgetDiff.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $(document).ready(function() {
        recalcBulkImpact();
    });
</script>
@endpush
