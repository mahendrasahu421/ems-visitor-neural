@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 font-weight-bold">{{ $title }}</h2>
                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1 fs-12">
                        <i class="ti ti-chart-arrows-vertical me-1"></i> Salary Revision Flow
                    </span>
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.increments.index') }}" class="text-secondary">Annual Increments</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Process Increment</li>
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
                        <strong>Please resolve the following errors:</strong>
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

        <form action="{{ route('payroll.increments.store') }}" method="POST" id="incrementForm">
            @csrf

            <!-- Section 1: Employee Selection & Current State -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Employee Selection &amp; Current Compensation</h5>
                    </div>
                    <span class="badge bg-light text-secondary border fs-12">Staff Baseline</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-6 col-md-12">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Select Employee <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" id="employee_id" class="form-select select2" required onchange="handleEmployeeChange()">
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $emp)
                                    @php
                                        $empGross = $emp->salaryStructure ? (float)$emp->salaryStructure->monthly_gross : 0;
                                        $empCtc = $emp->salaryStructure ? (float)$emp->salaryStructure->ctc_annual : ($empGross * 12);
                                        $desig = $emp->designation->name ?? $emp->designation->Designation ?? 'Staff';
                                        $isSelected = (old('employee_id', $selectedEmployeeId ?? '') == $emp->employee_id);
                                    @endphp
                                    <option value="{{ $emp->employee_id }}"
                                        data-gross="{{ $empGross }}"
                                        data-ctc="{{ $empCtc }}"
                                        data-desig-id="{{ $emp->designation_id }}"
                                        data-desig-name="{{ $desig }}"
                                        data-joining="{{ $emp->joining_date ? \Carbon\Carbon::parse($emp->joining_date)->format('d M, Y') : 'N/A' }}"
                                        {{ $isSelected ? 'selected' : '' }}>
                                        [ID: {{ $emp->employee_id }}] {{ $emp->employee_name }} &bull; {{ $desig }} (Current Gross: &#8377;{{ number_format($empGross, 0) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted fs-12">Select the employee whose annual compensation is being revised.</small>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="p-3 bg-light rounded border border-light-subtle" id="employee_meta_box">
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <div class="text-muted fs-11 text-uppercase font-weight-bold">Current Designation</div>
                                        <div class="font-weight-bold text-dark fs-13 mt-1" id="current_desig_text">
                                            {{ $selectedEmployee?->designation?->name ?? 'Select employee' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4 border-start ps-3">
                                        <div class="text-muted fs-11 text-uppercase font-weight-bold">Current Gross / Mo</div>
                                        <div class="font-weight-bold text-dark fs-13 mt-1" id="current_gross_text">
                                            &#8377; {{ number_format($selectedEmployee?->salaryStructure?->monthly_gross ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4 border-start ps-3">
                                        <div class="text-muted fs-11 text-uppercase font-weight-bold">Current Annual CTC</div>
                                        <div class="font-weight-bold text-primary fs-13 mt-1" id="current_ctc_text">
                                            &#8377; {{ number_format($selectedEmployee?->salaryStructure?->ctc_annual ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Revision Parameters -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Increment Parameters &amp; Promotion</h5>
                    </div>
                    <span class="badge bg-light text-secondary border fs-12">Revision Specs</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Increment Type <span class="text-danger">*</span>
                            </label>
                            <select name="increment_type" id="increment_type" class="form-select" onchange="calculatePreview()">
                                <option value="percentage" {{ old('increment_type') == 'percentage' ? 'selected' : '' }}>Percentage Hike (% on Salary)</option>
                                <option value="fixed_amount" {{ old('increment_type') == 'fixed_amount' ? 'selected' : '' }}>Fixed Monthly Amount (&#8377; / mo)</option>
                                <option value="direct_ctc" {{ old('increment_type') == 'direct_ctc' ? 'selected' : '' }}>Direct Revised Annual CTC (&#8377; / yr)</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Increment Value <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark font-weight-bold" id="inc_value_symbol">%</span>
                                <input type="number" step="0.01" min="0" name="increment_value" id="increment_value"
                                    class="form-control form-control-lg font-weight-bold border-primary"
                                    value="{{ old('increment_value', 10) }}" required oninput="calculatePreview()">
                            </div>
                            <small class="text-muted fs-11" id="inc_value_hint">e.g. 10.00 for 10% annual salary hike</small>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Effective From <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="effective_date" id="effective_date" class="form-control"
                                value="{{ old('effective_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                            <small class="text-muted fs-11">Date from which new salary applies</small>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Review Cycle <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="review_cycle" class="form-control"
                                value="{{ old('review_cycle', 'Annual Appraisal ' . date('Y')) }}" required>
                            <small class="text-muted fs-11">e.g. Annual Appraisal 2026-27</small>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Promotion / Revised Designation <span class="text-muted font-weight-normal">(Optional)</span>
                            </label>
                            <select name="new_designation_id" id="new_designation_id" class="form-select select2" onchange="calculatePreview()">
                                <option value="">-- Same Designation (No Promotion) --</option>
                                @foreach($designations as $des)
                                    <option value="{{ $des->id }}" {{ old('new_designation_id') == $des->id ? 'selected' : '' }}>
                                        {{ $des->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted fs-11">Select a higher designation if promoted.</small>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Performance Rating <span class="text-muted font-weight-normal">(Optional)</span>
                            </label>
                            <select name="performance_rating" class="form-select">
                                <option value="Outstanding (5/5)">5/5 - Outstanding / Exceeds All Goals</option>
                                <option value="Exceeds Expectations (4/5)" selected>4/5 - Exceeds Expectations</option>
                                <option value="Meets Expectations (3/5)">3/5 - Meets Expectations (Satisfactory)</option>
                                <option value="Needs Improvement (2/5)">2/5 - Needs Improvement</option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-12">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                HR Notes &amp; Rationale <span class="text-muted font-weight-normal">(Internal)</span>
                            </label>
                            <input type="text" name="remarks" class="form-control"
                                placeholder="e.g. Merit increment based on annual performance rating."
                                value="{{ old('remarks', 'Annual merit increment applied.') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Comparison Preview Sticky Card -->
            <div class="card border border-primary-subtle shadow-sm mb-4" style="background: linear-gradient(135deg, #f8faff 0%, #edf2fc 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3 text-center text-md-start">
                        <div class="col-md-3 border-end">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                <i class="ti ti-wallet text-primary me-1"></i> Monthly Gross Salary
                            </div>
                            <div class="d-flex align-items-baseline gap-2 justify-content-center justify-content-md-start">
                                <span class="text-muted text-decoration-line-through fs-13" id="prev_gross_display">&#8377; 0.00</span>
                                <h3 class="mb-0 text-dark font-weight-bold fs-22" id="new_gross_display">&#8377; 0.00</h3>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 mt-1 font-weight-semibold" id="gross_diff_badge">
                                +&#8377; 0.00 / month
                            </span>
                        </div>

                        <div class="col-md-3 border-end">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                <i class="ti ti-building-bank text-dark me-1"></i> Annual Cost to Company (CTC)
                            </div>
                            <div class="d-flex align-items-baseline gap-2 justify-content-center justify-content-md-start">
                                <span class="text-muted text-decoration-line-through fs-13" id="prev_ctc_display">&#8377; 0.00</span>
                                <h3 class="mb-0 text-primary font-weight-bold fs-22" id="new_ctc_display">&#8377; 0.00</h3>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 mt-1 font-weight-semibold" id="ctc_diff_badge">
                                +&#8377; 0.00 / year
                            </span>
                        </div>

                        <div class="col-md-3 border-end">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                <i class="ti ti-percentage text-success me-1"></i> Effective Hike %
                            </div>
                            <h2 class="mb-0 text-success font-weight-bold" id="effective_hike_display">0%</h2>
                            <span class="fs-11 text-muted">Annual revision rate</span>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted fs-12 font-weight-bold text-uppercase mb-1">
                                <i class="ti ti-cash text-success me-1"></i> Est. Take-Home (In-Hand)
                            </div>
                            <h3 class="mb-0 text-success font-weight-bold fs-22" id="new_net_display">&#8377; 0.00</h3>
                            <span class="fs-11 text-muted">After statutory deductions</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Detailed Salary Breakdown Customization (Collapsible) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">3</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Auto-Calculated Salary Components</h5>
                    </div>
                    <span class="badge bg-light text-muted border fs-12">Component Breakdown</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fs-12 text-muted mb-1">Basic Salary (50% of Gross)</label>
                            <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-12 text-muted mb-1">HRA (40% of Basic)</label>
                            <input type="number" step="0.01" name="hra" id="hra" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-12 text-muted mb-1">Special Allowance (Balance)</label>
                            <input type="number" step="0.01" name="special_allowance" id="special_allowance" class="form-control" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-12 text-muted mb-1">Conveyance Allowance</label>
                            <input type="number" step="0.01" name="conveyance_allowance" id="conveyance_allowance" class="form-control" value="1600">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-12 text-muted mb-1">Medical Allowance</label>
                            <input type="number" step="0.01" name="medical_allowance" id="medical_allowance" class="form-control" value="1250">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-12 text-muted mb-1">Other Allowance</label>
                            <input type="number" step="0.01" name="other_allowance" id="other_allowance" class="form-control" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-12 text-muted mb-1">PF Employee Deduction</label>
                            <input type="number" step="0.01" name="pf_employee" id="pf_employee" class="form-control" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Personalized Increment Letter Content -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="ti ti-file-text text-primary me-2"></i> Increment Letter Message (Optional)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fs-13 text-secondary font-weight-bold">
                        Personalized Management Note for the Employee's Increment Letter
                    </label>
                    <textarea name="letter_notes" class="form-control" rows="3"
                        placeholder="e.g. We deeply appreciate your sincere commitment, outstanding deliverables and team contribution throughout the past fiscal year. Keep up the remarkable work!">{{ old('letter_notes') }}</textarea>
                    <small class="text-muted fs-11">This message will appear prominently on the official downloadable PDF Increment Letter.</small>
                </div>
            </div>

            <!-- Hidden Inputs for Final Calculated Values -->
            <input type="hidden" name="new_gross" id="new_gross_hidden" value="0">
            <input type="hidden" name="new_ctc" id="new_ctc_hidden" value="0">
            <input type="hidden" name="hike_percentage" id="hike_percentage_hidden" value="0">
            <input type="hidden" name="hike_amount" id="hike_amount_hidden" value="0">

            <!-- Submit Button Bar -->
            <div class="card border-0 shadow-sm bg-white mb-5">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-13"><i class="ti ti-info-circle me-1"></i>Saving will immediately apply this revision and activate the new salary structure.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('payroll.increments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center shadow-sm">
                            <i class="ti ti-check me-1 fs-16"></i> Apply Increment &amp; Activate Revision
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
    function handleEmployeeChange() {
        const sel = $('#employee_id').find(':selected');
        const gross = parseFloat(sel.data('gross')) || 0;
        const ctc = parseFloat(sel.data('ctc')) || 0;
        const desig = sel.data('desig-name') || 'Staff';

        $('#current_desig_text').text(desig);
        $('#current_gross_text').text('₹ ' + gross.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#current_ctc_text').text('₹ ' + ctc.toLocaleString('en-IN', {minimumFractionDigits: 2}));

        $('#prev_gross_display').text('₹ ' + gross.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#prev_ctc_display').text('₹ ' + ctc.toLocaleString('en-IN', {minimumFractionDigits: 2}));

        calculatePreview();
    }

    function calculatePreview() {
        const empId = $('#employee_id').val();
        if (!empId) return;

        const sel = $('#employee_id').find(':selected');
        const currentGross = parseFloat(sel.data('gross')) || 0;
        const currentCtc = parseFloat(sel.data('ctc')) || (currentGross * 12);

        const incType = $('#increment_type').val();
        const incVal = parseFloat($('#increment_value').val()) || 0;

        if (incType === 'percentage') {
            $('#inc_value_symbol').text('%');
            $('#inc_value_hint').text('e.g. 10.00 for 10% annual salary hike');
        } else if (incType === 'fixed_amount') {
            $('#inc_value_symbol').text('₹');
            $('#inc_value_hint').text('Fixed monthly gross increment amount in rupees');
        } else {
            $('#inc_value_symbol').text('₹');
            $('#inc_value_hint').text('Direct new revised annual CTC in rupees');
        }

        // Send AJAX preview request
        $.ajax({
            url: "{{ route('payroll.increments.preview') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                employee_id: empId,
                increment_type: incType,
                increment_value: incVal,
                new_designation_id: $('#new_designation_id').val()
            },
            success: function(res) {
                if (res.success) {
                    const d = res.data;
                    $('#new_gross_display').text('₹ ' + d.new_gross.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#new_ctc_display').text('₹ ' + d.new_ctc.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    $('#effective_hike_display').text(d.hike_percentage + '%');

                    $('#gross_diff_badge').text('+₹ ' + d.monthly_hike.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ' / month');
                    $('#ctc_diff_badge').text('+₹ ' + d.hike_amount.toLocaleString('en-IN', {minimumFractionDigits: 2}) + ' / year');
                    $('#new_net_display').text('₹ ' + d.net_take_home.toLocaleString('en-IN', {minimumFractionDigits: 2}));

                    // Update breakdown inputs
                    $('#basic_salary').val(d.basic_salary);
                    $('#hra').val(d.hra);
                    $('#special_allowance').val(d.special_allowance);
                    $('#conveyance_allowance').val(d.conveyance_allowance);
                    $('#medical_allowance').val(d.medical_allowance);
                    $('#other_allowance').val(d.other_allowance);
                    $('#pf_employee').val(d.pf_employee);

                    // Update hidden inputs
                    $('#new_gross_hidden').val(d.new_gross);
                    $('#new_ctc_hidden').val(d.new_ctc);
                    $('#hike_percentage_hidden').val(d.hike_percentage);
                    $('#hike_amount_hidden').val(d.hike_amount);
                }
            }
        });
    }

    $(document).ready(function() {
        if ($('#employee_id').val()) {
            handleEmployeeChange();
        }
    });
</script>
@endpush
