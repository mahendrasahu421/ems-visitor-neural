@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-plus text-primary"></i> Add Working Days &amp; Calculation Rule
                </h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('masters.payroll.calculation-rules') }}">Calculation Rules</a></li>
                        <li class="breadcrumb-item active">Add Rule</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('masters.payroll.calculation-rules') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-arrow-left"></i> Back to Rules List
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Policy &amp; Calculation Configuration</h5>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('masters.payroll.calculation-rules.store') }}" id="ruleForm">
                            @csrf

                            <!-- Policy Name -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Policy Name <span class="text-danger">*</span></label>
                                <input type="text" name="policy_name" class="form-control" placeholder="e.g. Standard 26 Working Days Basis" value="{{ old('policy_name') }}" required>
                                <div class="form-text">A descriptive name for this working days calculation rule.</div>
                            </div>

                            <div class="row">
                                <!-- Calculation Basis (Divisor) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Working Days Divisor Basis <span class="text-danger">*</span></label>
                                    <select name="calculation_basis" class="form-select" id="calcBasisSelect" required onchange="updateFormulaPreview()">
                                        <option value="fixed_26_days" {{ old('calculation_basis') == 'fixed_26_days' ? 'selected' : '' }}>
                                            Fixed 26 Days (Standard Indian Payroll)
                                        </option>
                                        <option value="calendar_days" {{ old('calculation_basis') == 'calendar_days' ? 'selected' : '' }}>
                                            Calendar Month Days (28, 29, 30, 31 Days)
                                        </option>
                                        <option value="fixed_30_days" {{ old('calculation_basis') == 'fixed_30_days' ? 'selected' : '' }}>
                                            Fixed 30 Days (Uniform 30 Days Divisor)
                                        </option>
                                        <option value="actual_working_days" {{ old('calculation_basis') == 'actual_working_days' ? 'selected' : '' }}>
                                            Actual Working Days (Month Days - Weekends - Holidays)
                                        </option>
                                    </select>
                                    <div class="form-text">Defines the divisor used to calculate employee per-day wage rate.</div>
                                </div>

                                <!-- Weekly Off Pattern -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Weekly Off Pattern <span class="text-danger">*</span></label>
                                    <select name="weekly_off_pattern" class="form-select" required>
                                        <option value="sunday_only" {{ old('weekly_off_pattern') == 'sunday_only' ? 'selected' : '' }}>
                                            6 Days Week (Sundays Only)
                                        </option>
                                        <option value="saturday_sunday" {{ old('weekly_off_pattern') == 'saturday_sunday' ? 'selected' : '' }}>
                                            5 Days Week (Saturdays &amp; Sundays Off)
                                        </option>
                                        <option value="alternate_saturday_sunday" {{ old('weekly_off_pattern') == 'alternate_saturday_sunday' ? 'selected' : '' }}>
                                            Alternate Weekends (Sunday + 2nd &amp; 4th Saturday Off)
                                        </option>
                                    </select>
                                    <div class="form-text">Days designated as non-working weekly offs.</div>
                                </div>
                            </div>

                            <!-- Leave & Deduction Flags -->
                            <div class="card bg-light border-0 p-3 mb-3">
                                <h6 class="fw-bold mb-2">Leave &amp; Loss of Pay (LWP) Deduction Settings</h6>
                                
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="deduct_unpaid_leave_only" id="deductUnpaid" value="1" checked>
                                    <label class="form-check-label" for="deductUnpaid">
                                        <strong>Deduct Unpaid Leaves Only (LWP / Absent)</strong>
                                        <div class="small text-muted">Approved paid leaves (Casual Leave, Sick Leave, Earned Leave) will NOT be deducted from salary.</div>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="include_weekly_off_in_payable" id="incWeekly" value="1" checked>
                                    <label class="form-check-label" for="incWeekly">
                                        <strong>Weekly Offs are Paid</strong>
                                        <div class="small text-muted">Employees receive full pay for designated weekly off days.</div>
                                    </label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="include_holidays_in_payable" id="incHols" value="1" checked>
                                    <label class="form-check-label" for="incHols">
                                        <strong>Public Holidays are Paid</strong>
                                        <div class="small text-muted">Employees receive full pay for official company / gazetted holidays.</div>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Active" selected>Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Is Default -->
                                <div class="col-md-6 mb-3 d-flex align-items-center">
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_default" id="isDefaultCheck" value="1">
                                        <label class="form-check-label fw-bold" for="isDefaultCheck">
                                            Make this the Active Default Policy
                                        </label>
                                        <div class="small text-muted">If checked, this policy will be immediately used for monthly payroll processing.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Description / Notes</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="e.g. Applicable to all permanent staff on 26 days fixed calculation standard.">{{ old('description') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <a href="{{ route('masters.payroll.calculation-rules') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i> Save Policy Rule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="card-title mb-0 d-flex align-items-center gap-1">
                            <i class="ti ti-info-circle text-primary"></i> Deduction Formula Explanation
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div id="formulaExplanation">
                            <!-- Populated via JS -->
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <h6 class="fw-bold fs-13 mb-2">Example Calculation (Gross: &#8377; 26,000, LWP: 2 Days):</h6>
                            <div class="p-2 bg-white rounded border small" id="formulaExample">
                                <!-- Populated via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function updateFormulaPreview() {
        const basis = document.getElementById('calcBasisSelect').value;
        const explanationEl = document.getElementById('formulaExplanation');
        const exampleEl = document.getElementById('formulaExample');

        if (basis === 'fixed_26_days') {
            explanationEl.innerHTML = `
                <div class="badge bg-primary text-white mb-2">Fixed 26 Days Basis</div>
                <p class="fs-13 text-dark mb-2">Daily wage is computed by dividing monthly gross salary by <strong>26 fixed days</strong>.</p>
                <div class="p-2 bg-white rounded border font-monospace fs-12 mb-2 text-primary">
                    Per-Day Salary = Gross ÷ 26<br>
                    LWP Deduction = LWP Days × (Gross ÷ 26)
                </div>
            `;
            exampleEl.innerHTML = `
                <div>Per Day Salary: 26,000 ÷ 26 = <strong>&#8377; 1,000.00</strong></div>
                <div class="text-danger">LWP Deduction (2 days): 2 × 1,000 = <strong>- &#8377; 2,000.00</strong></div>
                <div class="text-success fw-bold mt-1">Earned Gross: &#8377; 24,000.00</div>
            `;
        } else if (basis === 'calendar_days') {
            explanationEl.innerHTML = `
                <div class="badge bg-info text-dark mb-2">Calendar Days Basis</div>
                <p class="fs-13 text-dark mb-2">Daily wage is computed by dividing monthly gross salary by <strong>total days in that month (e.g. 30 or 31)</strong>.</p>
                <div class="p-2 bg-white rounded border font-monospace fs-12 mb-2 text-info text-dark">
                    Per-Day Salary = Gross ÷ MonthDays<br>
                    LWP Deduction = LWP Days × (Gross ÷ MonthDays)
                </div>
            `;
            exampleEl.innerHTML = `
                <div>In a 30-day month (April):</div>
                <div>Per Day Salary: 26,000 ÷ 30 = <strong>&#8377; 866.67</strong></div>
                <div class="text-danger">LWP Deduction (2 days): 2 × 866.67 = <strong>- &#8377; 1,733.34</strong></div>
                <div class="text-success fw-bold mt-1">Earned Gross: &#8377; 24,266.66</div>
            `;
        } else if (basis === 'fixed_30_days') {
            explanationEl.innerHTML = `
                <div class="badge bg-secondary text-white mb-2">Fixed 30 Days Basis</div>
                <p class="fs-13 text-dark mb-2">Uniform 30 days divisor applied every month regardless of calendar length.</p>
                <div class="p-2 bg-white rounded border font-monospace fs-12 mb-2 text-secondary">
                    Per-Day Salary = Gross ÷ 30<br>
                    LWP Deduction = LWP Days × (Gross ÷ 30)
                </div>
            `;
            exampleEl.innerHTML = `
                <div>Per Day Salary: 26,000 ÷ 30 = <strong>&#8377; 866.67</strong></div>
                <div class="text-danger">LWP Deduction (2 days): 2 × 866.67 = <strong>- &#8377; 1,733.34</strong></div>
                <div class="text-success fw-bold mt-1">Earned Gross: &#8377; 24,266.66</div>
            `;
        } else {
            explanationEl.innerHTML = `
                <div class="badge bg-warning text-dark mb-2">Actual Working Days Basis</div>
                <p class="fs-13 text-dark mb-2">Daily wage is computed by dividing gross by <strong>net working days</strong> (excluding weekly offs and holidays).</p>
                <div class="p-2 bg-white rounded border font-monospace fs-12 mb-2 text-warning text-dark">
                    Working Days = MonthDays - Weekends - Holidays<br>
                    LWP Deduction = LWP Days × (Gross ÷ Working Days)
                </div>
            `;
            exampleEl.innerHTML = `
                <div>Assuming 22 actual working days in month:</div>
                <div>Per Day Salary: 26,000 ÷ 22 = <strong>&#8377; 1,181.82</strong></div>
                <div class="text-danger">LWP Deduction (2 days): 2 × 1,181.82 = <strong>- &#8377; 2,363.64</strong></div>
                <div class="text-success fw-bold mt-1">Earned Gross: &#8377; 23,636.36</div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', updateFormulaPreview);
</script>

@endsection
