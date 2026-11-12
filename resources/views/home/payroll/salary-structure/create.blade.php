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
                    @if(isset($structure) && $structure)
                        <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 fs-12">
                            <i class="ti ti-edit me-1"></i> Editing #{{ $structure->id }}
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1 fs-12">
                            <i class="ti ti-plus me-1"></i> New Setup
                        </span>
                    @endif
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('masters.payroll.salary-structure') }}" class="text-secondary">Salary Structures</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ isset($structure) && $structure ? 'Edit Structure' : 'Define Structure' }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <a href="{{ route('masters.payroll.salary-structure') }}"
                    class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back to Salary List
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-20 me-2"></i>
                    <div>
                        <strong>Please correct the following errors:</strong>
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

        <!-- Quick Help & Exemption Preset Guidance -->
        <div class="card bg-light border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="ti ti-shield-dollar fs-22"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">Salary Configuration &amp; Exemption Quick Presets</h6>
                            <p class="text-muted fs-13 mb-0">
                                Set up monthly compensation. For employees exempt from PF, ESI, or Taxes, choose <strong>Zero Deductions</strong> so Gross equals 100% In-Hand pay.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="applyPreset('standard')">
                            <i class="ti ti-sparkles me-1"></i> Standard Statutory (PF + ESI + PT)
                        </button>
                        <button type="button" class="btn btn-sm btn-success text-white shadow-sm" onclick="applyPreset('zero_deduction')">
                            <i class="ti ti-shield-check me-1"></i> Zero Deductions (No PF / No ESI / No Tax)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" id="salaryStructureForm" action="{{ isset($structure) && $structure ? route('masters.payroll.salary-structure.update', $structure->id) : route('masters.payroll.salary-structure.store') }}">
            @csrf
            @if(isset($structure) && $structure)
                @method('PUT')
            @endif

            <!-- Section 1: Employee & Base Compensation -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Employee &amp; Base Compensation</h5>
                    </div>
                    <span class="badge bg-light text-secondary border fs-12">Core Setup</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Select Employee <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" class="form-select select2" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach($employees as $emp)
                                    @php
                                        $empAgency = $emp->agency->agency_name ?? 'Direct Payroll';
                                        $empDesig = $emp->designation->name ?? $emp->designation->Designation ?? 'Staff';
                                        $isSelected = (old('employee_id', $selectedEmployeeId ?? '') == $emp->employee_id);
                                    @endphp
                                    <option value="{{ $emp->employee_id }}" {{ $isSelected ? 'selected' : '' }}>
                                        [ID: {{ $emp->employee_id }}] {{ $emp->employee_name }} &bull; {{ $empDesig }} ({{ $empAgency }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted fs-12">Select staff member to assign this salary structure.</small>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Monthly Gross Salary (&#8377;) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark font-weight-bold">&#8377;</span>
                                <input type="number" step="0.01" id="monthly_gross" name="monthly_gross" class="form-control form-control-lg font-weight-bold border-primary"
                                    placeholder="e.g. 50000" value="{{ old('monthly_gross', $structure->monthly_gross ?? 50000) }}" required oninput="autoCalculateFromGross()">
                            </div>
                            <small class="text-primary fs-12"><i class="ti ti-bulb me-1"></i>Type Gross to auto-calculate components.</small>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Annual CTC (&#8377;) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">&#8377;</span>
                                <input type="number" step="0.01" id="ctc_annual" name="ctc_annual" class="form-control"
                                    value="{{ old('ctc_annual', $structure->ctc_annual ?? 621600) }}" required>
                            </div>
                            <small class="text-muted fs-12">Total Annual Cost to Company</small>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">
                                Effective From <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="effective_from" class="form-control"
                                value="{{ old('effective_from', isset($structure->effective_from) ? \Carbon\Carbon::parse($structure->effective_from)->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d')) }}" required>
                            <small class="text-muted fs-12">Payroll cycle start date</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Financial Breakdown Sticky Summary -->
            <div class="card border border-primary-subtle shadow-sm mb-4" style="background: linear-gradient(135deg, #f8faff 0%, #edf2fc 100%);">
                <div class="card-body p-3">
                    <div class="row align-items-center g-3 text-center text-md-start">
                        <div class="col-md-3 border-end">
                            <div class="d-flex align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                                <span class="badge bg-primary-subtle text-primary p-1 rounded"><i class="ti ti-wallet fs-16"></i></span>
                                <span class="text-muted fs-12 font-weight-bold text-uppercase">Monthly Gross Earnings</span>
                            </div>
                            <h4 class="mb-0 text-primary font-weight-bold fs-22" id="preview_earnings">&#8377; 50,000.00</h4>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="d-flex align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                                <span class="badge bg-danger-subtle text-danger p-1 rounded"><i class="ti ti-percentage fs-16"></i></span>
                                <span class="text-muted fs-12 font-weight-bold text-uppercase">Total Monthly Deductions</span>
                            </div>
                            <h4 class="mb-0 text-danger font-weight-bold fs-22" id="preview_deductions">&#8377; 2,000.00</h4>
                            <span class="fs-11 text-muted" id="preview_deduction_label">Statutory: PF, ESI, PT, Tax</span>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="d-flex align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                                <span class="badge bg-success-subtle text-success p-1 rounded"><i class="ti ti-cash fs-16"></i></span>
                                <span class="text-muted fs-12 font-weight-bold text-uppercase">Net Take-Home (In-Hand)</span>
                            </div>
                            <h4 class="mb-0 text-success font-weight-bold fs-22" id="preview_net">&#8377; 48,000.00</h4>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 mt-1" id="preview_inhand_percent">
                                96% In-Hand Ratio
                            </span>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2 mb-1 justify-content-center justify-content-md-start">
                                <span class="badge bg-dark-subtle text-dark p-1 rounded"><i class="ti ti-building-bank fs-16"></i></span>
                                <span class="text-muted fs-12 font-weight-bold text-uppercase">Annual Cost to Company</span>
                            </div>
                            <h4 class="mb-0 text-dark font-weight-bold fs-22" id="preview_ctc">&#8377; 6,21,600.00</h4>
                            <span class="fs-11 text-muted">Incl. Employer Contributions (12m)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Section 2: Monthly Earnings Components -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                                <h5 class="card-title mb-0 font-weight-bold text-dark">Monthly Earnings Components</h5>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-12" id="badge_earnings_total">
                                Total: &#8377; 50,000.00
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Basic Salary (50%) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="basic_salary" name="basic_salary" class="form-control calc-earning"
                                            value="{{ old('basic_salary', $structure->basic_salary ?? 25000) }}" required oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">EPF base component (Typically 50% of Gross)</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        House Rent Allowance (HRA - 25%)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="hra" name="hra" class="form-control calc-earning"
                                            value="{{ old('hra', $structure->hra ?? 12500) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Standard housing allowance</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Conveyance Allowance
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="conveyance_allowance" name="conveyance_allowance" class="form-control calc-earning"
                                            value="{{ old('conveyance_allowance', $structure->conveyance_allowance ?? 2000) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Standard travel allowance</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Medical Allowance
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="medical_allowance" name="medical_allowance" class="form-control calc-earning"
                                            value="{{ old('medical_allowance', $structure->medical_allowance ?? 1500) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Standard medical allowance</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Special Allowance (Balance)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="special_allowance" name="special_allowance" class="form-control calc-earning"
                                            value="{{ old('special_allowance', $structure->special_allowance ?? 9000) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Balancing figure to equal Gross</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        Other / Variable Allowance
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="other_allowance" name="other_allowance" class="form-control calc-earning"
                                            value="{{ old('other_allowance', $structure->other_allowance ?? 0) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Optional miscellaneous allowance</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary d-flex justify-content-between">
                                        <span>Bike Maintenance Allowance</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Fixed &#8377;1,500</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="bike_allowance" name="bike_allowance" class="form-control calc-earning"
                                            value="{{ old('bike_allowance', $structure->bike_allowance ?? 1500) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Fixed monthly vehicle &amp; commute allowance</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Statutory Deductions & Tax Management (Exemptions) -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">3</span>
                                <h5 class="card-title mb-0 font-weight-bold text-dark">Deductions &amp; Tax Management</h5>
                            </div>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-12" id="badge_deductions_total">
                                Total: &#8377; 2,000.00
                            </span>
                        </div>
                        <div class="card-body p-4">

                            <!-- Statutory Exemption Switches -->
                            <div class="p-3 mb-3 rounded border bg-light-subtle">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-bold fs-13 text-dark">
                                        <i class="ti ti-settings-cog text-primary me-1"></i> Statutory Applicability &amp; Exemption Toggles
                                    </span>
                                    <span class="badge bg-info-subtle text-info border fs-11">Zero Tax / Exempt Controls</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="toggle_pf" checked onchange="handleStatutoryToggle('pf')">
                                            <label class="form-check-label fs-13 fw-semibold text-secondary" for="toggle_pf" id="label_toggle_pf">
                                                Provident Fund (PF)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="toggle_esi" onchange="handleStatutoryToggle('esi')">
                                            <label class="form-check-label fs-13 fw-semibold text-secondary" for="toggle_esi" id="label_toggle_esi">
                                                ESI (State Insurance)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="toggle_pt" checked onchange="handleStatutoryToggle('pt')">
                                            <label class="form-check-label fs-13 fw-semibold text-secondary" for="toggle_pt" id="label_toggle_pt">
                                                Professional Tax (PT)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="toggle_tds" onchange="handleStatutoryToggle('tds')">
                                            <label class="form-check-label fs-13 fw-semibold text-secondary" for="toggle_tds" id="label_toggle_tds">
                                                TDS / Income Tax
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check form-switch d-flex align-items-center gap-2">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="toggle_gratuity" checked onchange="handleStatutoryToggle('gratuity')">
                                            <label class="form-check-label fs-13 fw-semibold text-secondary" for="toggle_gratuity" id="label_toggle_gratuity">
                                                Gratuity Benefit (4.81%)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top fs-11 text-muted">
                                    <i class="ti ti-info-circle text-primary me-1"></i>
                                    <strong>How exemptions work:</strong> When unselected, statutory fields stay at &#8377;0.00. The payroll processor will make zero deductions, disbursing 100% of earnings directly to the employee.
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- PF Deductions -->
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary d-flex justify-content-between">
                                        <span>PF Employee (12%)</span>
                                        <span class="badge bg-light text-muted border fs-11" id="badge_pf_status">Active</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="pf_employee" name="pf_employee" class="form-control calc-deduction"
                                            value="{{ old('pf_employee', $structure->pf_employee ?? 1800) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Deducted from employee's salary</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        PF Employer (12% CTC)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="pf_employer" name="pf_employer" class="form-control"
                                            value="{{ old('pf_employer', $structure->pf_employer ?? 1800) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Company contribution (Added to CTC)</small>
                                </div>

                                <!-- ESI Deductions -->
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary d-flex justify-content-between">
                                        <span>ESI Employee (0.75%)</span>
                                        <span class="badge bg-light text-muted border fs-11" id="badge_esi_status">Exempt</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="esi_employee" name="esi_employee" class="form-control calc-deduction"
                                            value="{{ old('esi_employee', $structure->esi_employee ?? 0) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Applicable if Gross &le; &#8377;21,000</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary">
                                        ESI Employer (3.25% CTC)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="esi_employer" name="esi_employer" class="form-control"
                                            value="{{ old('esi_employer', $structure->esi_employer ?? 0) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Company contribution (Added to CTC)</small>
                                </div>

                                <!-- PT & TDS -->
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary d-flex justify-content-between">
                                        <span>Professional Tax (PT)</span>
                                        <span class="badge bg-light text-muted border fs-11" id="badge_pt_status">Active</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="professional_tax" name="professional_tax" class="form-control calc-deduction"
                                            value="{{ old('professional_tax', $structure->professional_tax ?? 200) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">State professional tax (e.g. &#8377;200 / month)</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold fs-13 text-secondary d-flex justify-content-between">
                                        <span>Monthly TDS / Income Tax</span>
                                        <span class="badge bg-light text-muted border fs-11" id="badge_tds_status">Exempt</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="tds_monthly" name="tds_monthly" class="form-control calc-deduction"
                                            value="{{ old('tds_monthly', $structure->tds_monthly ?? 0) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">&#8377;0 if tax liability is zero</small>
                                </div>

                                <!-- Gratuity (Statutory Retiral Benefit for CTC) -->
                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold fs-13 text-secondary d-flex justify-content-between">
                                        <span>Gratuity Accrual (4.81% of Basic - Included in CTC)</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-11" id="badge_gratuity_status">Active (4.81%)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">&#8377;</span>
                                        <input type="number" step="0.01" id="gratuity" name="gratuity" class="form-control"
                                            value="{{ old('gratuity', $structure->gratuity ?? 1202.50) }}" oninput="recalculateSummary()">
                                    </div>
                                    <small class="text-muted fs-11">Statutory company provision (15 days wages / year &approx; 4.81% of Basic; added to Annual CTC)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Bank, Statutory Identifiers & System Status -->
            <div class="card shadow-sm border-0 my-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">4</span>
                        <h5 class="card-title mb-0 font-weight-bold text-dark">Banking, Statutory Identifiers &amp; Status</h5>
                    </div>
                    <span class="badge bg-info-subtle text-info border fs-12">Disbursal Details</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">Bank Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ti ti-building-bank"></i></span>
                                <input type="text" name="bank_name" class="form-control" placeholder="e.g. HDFC Bank / SBI"
                                    value="{{ old('bank_name', $structure->bank_name ?? '') }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">Bank Account Number</label>
                            <input type="text" name="bank_account_no" class="form-control font-monospace" placeholder="e.g. 501004928172"
                                value="{{ old('bank_account_no', $structure->bank_account_no ?? '') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control font-monospace text-uppercase" placeholder="e.g. HDFC0001234"
                                value="{{ old('ifsc_code', $structure->ifsc_code ?? '') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-select" required>
                                @php $pm = old('payment_mode', $structure->payment_mode ?? 'Bank Transfer'); @endphp
                                <option value="Bank Transfer" {{ $pm == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/RTGS/IMPS)</option>
                                <option value="Cheque" {{ $pm == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Cash" {{ $pm == 'Cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">PAN Card Number</label>
                            <input type="text" name="pan_no" class="form-control font-monospace text-uppercase" placeholder="e.g. ABCDE1234F"
                                value="{{ old('pan_no', $structure->pan_no ?? '') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">UAN Number (EPFO)</label>
                            <input type="text" name="uan_no" class="form-control font-monospace" placeholder="e.g. 101234567890"
                                value="{{ old('uan_no', $structure->uan_no ?? '') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">PF Account Number</label>
                            <input type="text" name="pf_no" class="form-control font-monospace" placeholder="e.g. MH/BAN/0012345/000/0001234"
                                value="{{ old('pf_no', $structure->pf_no ?? '') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">ESI Insurance Number</label>
                            <input type="text" name="esi_no" class="form-control font-monospace" placeholder="e.g. 31000123450000001"
                                value="{{ old('esi_no', $structure->esi_no ?? '') }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">Structure Status</label>
                            <select name="status" class="form-select" required>
                                @php $st = old('status', $structure->status ?? 'Active'); @endphp
                                <option value="Active" {{ $st == 'Active' ? 'selected' : '' }}>Active (Applied in Payroll)</option>
                                <option value="Inactive" {{ $st == 'Inactive' ? 'selected' : '' }}>Inactive (Archived)</option>
                            </select>
                        </div>

                        <div class="col-lg-9 col-md-6">
                            <label class="form-label font-weight-bold fs-13 text-secondary">Internal Remarks / Notes</label>
                            <input type="text" name="remarks" class="form-control" placeholder="e.g. Annual revision, non-statutory contractor, zero-tax agreement..."
                                value="{{ old('remarks', $structure->remarks ?? '') }}">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-4 mt-3 border-top">
                        <div class="text-muted fs-13">
                            <i class="ti ti-lock me-1 text-secondary"></i> All salary revisions take effect starting from the selected effective date.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('masters.payroll.salary-structure') }}" class="btn btn-outline-secondary px-3">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                                <i class="ti ti-device-floppy me-1"></i> {{ isset($structure) && $structure ? 'Update Salary Structure' : 'Save Salary Structure' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    function val(id) {
        const el = document.getElementById(id);
        if (!el) return 0;
        const v = parseFloat(el.value);
        return isNaN(v) ? 0 : v;
    }

    function setVal(id, number) {
        const el = document.getElementById(id);
        if (el) {
            el.value = Number(number).toFixed(2);
        }
    }

    // Handle Quick Presets (Standard vs Zero Deductions)
    function applyPreset(presetType) {
        const gross = val('monthly_gross');
        if (gross <= 0) {
            alert('Please enter a valid Monthly Gross Salary first.');
            document.getElementById('monthly_gross').focus();
            return;
        }

        if (presetType === 'zero_deduction') {
            // Uncheck all deduction switches
            document.getElementById('toggle_pf').checked = false;
            document.getElementById('toggle_esi').checked = false;
            document.getElementById('toggle_pt').checked = false;
            document.getElementById('toggle_tds').checked = false;
            if (document.getElementById('toggle_gratuity')) document.getElementById('toggle_gratuity').checked = false;

            handleStatutoryToggle('pf');
            handleStatutoryToggle('esi');
            handleStatutoryToggle('pt');
            handleStatutoryToggle('tds');
            handleStatutoryToggle('gratuity');

            // Distribute earnings cleanly
            autoCalculateFromGross(false);
            recalculateSummary(false);
        } else if (presetType === 'standard') {
            document.getElementById('toggle_pf').checked = true;
            document.getElementById('toggle_esi').checked = (gross <= 21000);
            document.getElementById('toggle_pt').checked = (gross >= 15000);
            document.getElementById('toggle_tds').checked = false;
            if (document.getElementById('toggle_gratuity')) document.getElementById('toggle_gratuity').checked = true;

            handleStatutoryToggle('pf');
            handleStatutoryToggle('esi');
            handleStatutoryToggle('pt');
            handleStatutoryToggle('tds');
            handleStatutoryToggle('gratuity');

            autoCalculateFromGross(true);
        }
    }

    // Handle Individual Statutory Switch Toggles
    function handleStatutoryToggle(type) {
        const gross = val('monthly_gross');
        const basic = val('basic_salary');

        if (type === 'pf') {
            const isEnabled = document.getElementById('toggle_pf').checked;
            const pfEmpInput = document.getElementById('pf_employee');
            const pfEmprInput = document.getElementById('pf_employer');
            const badge = document.getElementById('badge_pf_status');

            if (!isEnabled) {
                pfEmpInput.value = '0.00';
                pfEmprInput.value = '0.00';
                pfEmpInput.readOnly = true;
                pfEmprInput.readOnly = true;
                pfEmpInput.classList.add('bg-light');
                pfEmprInput.classList.add('bg-light');
                badge.className = 'badge bg-secondary-subtle text-secondary border fs-11';
                badge.innerText = 'Exempt';
            } else {
                pfEmpInput.readOnly = false;
                pfEmprInput.readOnly = false;
                pfEmpInput.classList.remove('bg-light');
                pfEmprInput.classList.remove('bg-light');
                const calcPf = (basic > 0) ? Math.min(1800, Math.round(basic * 0.12)) : (gross > 0 ? Math.min(1800, Math.round(gross * 0.5 * 0.12)) : 0);
                pfEmpInput.value = calcPf.toFixed(2);
                pfEmprInput.value = calcPf.toFixed(2);
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle fs-11';
                badge.innerText = 'Active (12%)';
            }
        } else if (type === 'esi') {
            const isEnabled = document.getElementById('toggle_esi').checked;
            const esiEmpInput = document.getElementById('esi_employee');
            const esiEmprInput = document.getElementById('esi_employer');
            const badge = document.getElementById('badge_esi_status');

            if (!isEnabled) {
                esiEmpInput.value = '0.00';
                esiEmprInput.value = '0.00';
                esiEmpInput.readOnly = true;
                esiEmprInput.readOnly = true;
                esiEmpInput.classList.add('bg-light');
                esiEmprInput.classList.add('bg-light');
                badge.className = 'badge bg-secondary-subtle text-secondary border fs-11';
                badge.innerText = 'Exempt';
            } else {
                esiEmpInput.readOnly = false;
                esiEmprInput.readOnly = false;
                esiEmpInput.classList.remove('bg-light');
                esiEmprInput.classList.remove('bg-light');
                const esiEmp = (gross > 0) ? Math.round(gross * 0.0075) : 0;
                const esiEmpr = (gross > 0) ? Math.round(gross * 0.0325) : 0;
                esiEmpInput.value = esiEmp.toFixed(2);
                esiEmprInput.value = esiEmpr.toFixed(2);
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle fs-11';
                badge.innerText = 'Active (0.75%)';
            }
        } else if (type === 'pt') {
            const isEnabled = document.getElementById('toggle_pt').checked;
            const ptInput = document.getElementById('professional_tax');
            const badge = document.getElementById('badge_pt_status');

            if (!isEnabled) {
                ptInput.value = '0.00';
                ptInput.readOnly = true;
                ptInput.classList.add('bg-light');
                badge.className = 'badge bg-secondary-subtle text-secondary border fs-11';
                badge.innerText = 'Exempt';
            } else {
                ptInput.readOnly = false;
                ptInput.classList.remove('bg-light');
                ptInput.value = (gross >= 15000 ? 200 : 0).toFixed(2);
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle fs-11';
                badge.innerText = 'Active (₹200)';
            }
        } else if (type === 'tds') {
            const isEnabled = document.getElementById('toggle_tds').checked;
            const tdsInput = document.getElementById('tds_monthly');
            const badge = document.getElementById('badge_tds_status');

            if (!isEnabled) {
                tdsInput.value = '0.00';
                tdsInput.readOnly = true;
                tdsInput.classList.add('bg-light');
                badge.className = 'badge bg-secondary-subtle text-secondary border fs-11';
                badge.innerText = 'Exempt (₹0)';
            } else {
                tdsInput.readOnly = false;
                tdsInput.classList.remove('bg-light');
                badge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle fs-11';
                badge.innerText = 'Custom TDS';
            }
        } else if (type === 'gratuity') {
            const toggleEl = document.getElementById('toggle_gratuity');
            if (toggleEl) {
                const isEnabled = toggleEl.checked;
                const gratuityInput = document.getElementById('gratuity');
                const badge = document.getElementById('badge_gratuity_status');

                if (!isEnabled) {
                    gratuityInput.value = '0.00';
                    gratuityInput.readOnly = true;
                    gratuityInput.classList.add('bg-light');
                    badge.className = 'badge bg-secondary-subtle text-secondary border fs-11';
                    badge.innerText = 'Exempt (₹0)';
                } else {
                    gratuityInput.readOnly = false;
                    gratuityInput.classList.remove('bg-light');
                    const calcG = (basic > 0) ? Math.round(basic * 0.0481) : (gross > 0 ? Math.round(gross * 0.5 * 0.0481) : 0);
                    gratuityInput.value = calcG.toFixed(2);
                    badge.className = 'badge bg-success-subtle text-success border border-success-subtle fs-11';
                    badge.innerText = 'Active (4.81%)';
                }
            }
        }

        recalculateSummary(false);
    }

    // Auto-calculate earnings and statutory deductions from Monthly Gross
    function autoCalculateFromGross(calculateStatutory = true) {
        const gross = val('monthly_gross');
        if (gross < 0) return;

        // Earnings distribution
        const basic = Math.round(gross * 0.50);
        const hra = Math.round(gross * 0.25);
        const conveyance = gross >= 15000 ? 2000 : (gross > 0 ? Math.min(1000, Math.round(gross * 0.10)) : 0);
        const medical = gross >= 15000 ? 1500 : (gross > 0 ? Math.min(500, Math.round(gross * 0.05)) : 0);
        const bike = 1500; // Fixed Bike Maintenance Allowance
        const special = Math.max(0, gross - (basic + hra + conveyance + medical + bike));

        setVal('basic_salary', basic);
        setVal('hra', hra);
        setVal('conveyance_allowance', conveyance);
        setVal('medical_allowance', medical);
        setVal('bike_allowance', bike);
        setVal('special_allowance', special);
        setVal('other_allowance', 0);

        if (calculateStatutory) {
            // Apply based on active switch states
            if (document.getElementById('toggle_pf').checked) {
                const pf = Math.min(1800, Math.round(basic * 0.12));
                setVal('pf_employee', pf);
                setVal('pf_employer', pf);
            }
            if (document.getElementById('toggle_esi').checked) {
                const esiEmp = (gross <= 21000) ? Math.round(gross * 0.0075) : 0;
                const esiEmpr = (gross <= 21000) ? Math.round(gross * 0.0325) : 0;
                setVal('esi_employee', esiEmp);
                setVal('esi_employer', esiEmpr);
            }
            if (document.getElementById('toggle_pt').checked) {
                const pt = gross >= 15000 ? 200 : 0;
                setVal('professional_tax', pt);
            }
            if (document.getElementById('toggle_gratuity')?.checked) {
                const gratuity = Math.round(basic * 0.0481);
                setVal('gratuity', gratuity);
            }
        }

        recalculateSummary(false);
    }

    // Recalculate summary metrics across earnings, deductions, in-hand & annual CTC
    function recalculateSummary(syncGrossField = true) {
        const basic = val('basic_salary');
        const hra = val('hra');
        const conveyance = val('conveyance_allowance');
        const medical = val('medical_allowance');
        const bike = val('bike_allowance');
        const special = val('special_allowance');
        const other = val('other_allowance');

        const earnings = basic + hra + conveyance + medical + bike + special + other;

        const pfEmp = val('pf_employee');
        const pfEmpr = val('pf_employer');
        const esiEmp = val('esi_employee');
        const esiEmpr = val('esi_employer');
        const pt = val('professional_tax');
        const tds = val('tds_monthly');
        const gratuity = val('gratuity');

        const deductions = pfEmp + esiEmp + pt + tds;
        const netInHand = Math.max(0, earnings - deductions);
        const employerContributions = pfEmpr + esiEmpr + gratuity;
        const annualCtc = (earnings + employerContributions) * 12;

        if (syncGrossField) {
            setVal('monthly_gross', earnings);
        }
        setVal('ctc_annual', annualCtc);

        // Format Currency Helper
        const formatInr = (n) => '&#8377; ' + Number(n).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Update Summary Cards
        document.getElementById('preview_earnings').innerHTML = formatInr(earnings);
        document.getElementById('preview_deductions').innerHTML = formatInr(deductions);
        document.getElementById('preview_net').innerHTML = formatInr(netInHand);
        document.getElementById('preview_ctc').innerHTML = formatInr(annualCtc);

        document.getElementById('badge_earnings_total').innerHTML = 'Total: ' + formatInr(earnings);
        document.getElementById('badge_deductions_total').innerHTML = 'Total: ' + formatInr(deductions);

        // Deduction description helper
        const dedLabel = document.getElementById('preview_deduction_label');
        if (deductions === 0) {
            dedLabel.innerHTML = '<span class="text-success font-weight-bold"><i class="ti ti-check me-1"></i>Zero Deductions (100% In-Hand)</span>';
        } else {
            const parts = [];
            if (pfEmp > 0) parts.push('PF: ₹' + pfEmp);
            if (esiEmp > 0) parts.push('ESI: ₹' + esiEmp);
            if (pt > 0) parts.push('PT: ₹' + pt);
            if (tds > 0) parts.push('TDS: ₹' + tds);
            dedLabel.innerHTML = parts.join(' | ');
        }

        // In-hand percentage ratio
        const ratio = earnings > 0 ? Math.round((netInHand / earnings) * 100) : 100;
        const ratioBadge = document.getElementById('preview_inhand_percent');
        ratioBadge.innerText = ratio + '% In-Hand Ratio';
        if (ratio >= 98) {
            ratioBadge.className = 'badge bg-success-subtle text-success border border-success-subtle fs-11 mt-1';
        } else if (ratio >= 90) {
            ratioBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle fs-11 mt-1';
        } else {
            ratioBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle fs-11 mt-1';
        }
    }

    // Initialize toggle states on page load (reflecting saved values)
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize PF toggle based on current value
        const initialPf = val('pf_employee') + val('pf_employer');
        document.getElementById('toggle_pf').checked = (initialPf > 0);
        handleStatutoryToggle('pf');

        // Initialize ESI toggle
        const initialEsi = val('esi_employee') + val('esi_employer');
        document.getElementById('toggle_esi').checked = (initialEsi > 0);
        handleStatutoryToggle('esi');

        // Initialize PT toggle
        const initialPt = val('professional_tax');
        document.getElementById('toggle_pt').checked = (initialPt > 0);
        handleStatutoryToggle('pt');

        // Initialize TDS toggle
        const initialTds = val('tds_monthly');
        document.getElementById('toggle_tds').checked = (initialTds > 0);
        handleStatutoryToggle('tds');

        // Initialize Gratuity toggle
        const initialGratuity = val('gratuity');
        if (document.getElementById('toggle_gratuity')) {
            document.getElementById('toggle_gratuity').checked = (initialGratuity > 0 || initialGratuity === 0);
            handleStatutoryToggle('gratuity');
        }

        recalculateSummary(false);
    });
</script>

@endsection
