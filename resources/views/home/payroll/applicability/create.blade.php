@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .applicability-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .applicability-form-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .preset-pill-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 8px;
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 500;
        color: #334155;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }

    .preset-pill-btn:hover {
        background: #eef2ff;
        border-color: #6366f1;
        color: #4338ca;
    }

    .section-title {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin-bottom: 12px;
    }
</style>

<div class="page-wrapper">
    <div class="content container-fluid py-4">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('masters.payroll.applicability') }}" class="text-primary text-decoration-none fs-13 fw-medium">
                        Applicability Rules
                    </a>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">{{ isset($applicability) ? 'Edit Rule' : 'New Rule' }}</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <a href="{{ route('masters.payroll.applicability') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3">
                    <i class="ti ti-arrow-left"></i> Back to Catalog
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="ti ti-circle-check me-2 fs-18 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="ti ti-alert-circle me-2 fs-18 align-middle"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card applicability-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ isset($applicability) ? 'Edit Applicability Rule' : 'Configure New Applicability Rule' }}
                            </h5>
                            <p class="text-muted fs-12 mb-0">Define qualification criteria, wage ceilings, and employee group applicability for payroll computations.</p>
                        </div>
                        @if(isset($applicability))
                            <span class="badge bg-light text-secondary border px-3 py-2 font-monospace fs-12">
                                ID #{{ $applicability->id }}
                            </span>
                        @endif
                    </div>

                    <div class="card-body p-4">
                        @if(!isset($applicability))
                            <!-- Quick Presets -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <label class="section-title d-flex align-items-center gap-1 mb-2">
                                    <i class="ti ti-sparkles text-warning fs-14"></i> Quick Statutory Presets
                                </label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="preset-pill-btn" onclick="fillPreset('pf')">
                                        <i class="ti ti-building-bank"></i> PF (&le; ₹15k)
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillPreset('esic')">
                                        <i class="ti ti-heart-handshake"></i> ESIC (&le; ₹21k)
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillPreset('pt')">
                                        <i class="ti ti-receipt-tax"></i> PT (&gt; ₹10k)
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillPreset('gratuity')">
                                        <i class="ti ti-shield-check"></i> Gratuity (5 Yrs)
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillPreset('intern')">
                                        <i class="ti ti-school"></i> Intern Exempt
                                    </button>
                                </div>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ isset($applicability) ? route('masters.payroll.applicability.update', $applicability->id) : route('masters.payroll.applicability.store') }}"
                              id="fullApplicabilityForm">
                            @csrf
                            @if(isset($applicability))
                                @method('PUT')
                            @endif

                            <!-- SECTION 1: Rule Identity -->
                            <div class="section-title">1. Rule Identification & Target Cohort</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-7">
                                    <label class="form-label fs-13 fw-semibold text-dark">Rule Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-2" id="input_rule_name" name="rule_name"
                                           placeholder="e.g. Employee State Insurance (ESIC) Coverage"
                                           value="{{ old('rule_name', $applicability->rule_name ?? '') }}" required maxlength="150">
                                    <small class="text-muted fs-11">Descriptive title of the applicability rule or statutory policy.</small>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-13 fw-semibold text-dark">Rule Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-2 text-uppercase font-monospace" id="input_rule_code" name="rule_code"
                                           placeholder="e.g. ESI_APPLY"
                                           value="{{ old('rule_code', $applicability->rule_code ?? '') }}" required maxlength="50">
                                    <small class="text-muted fs-11">System key used during wage & deduction computations.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Applies To <span class="text-danger">*</span></label>
                                    <select class="form-select rounded-2" id="input_applies_to" name="applies_to" required>
                                        @php
                                            $currType = old('applies_to', $applicability->applies_to ?? 'all');
                                        @endphp
                                        <option value="all" {{ $currType === 'all' ? 'selected' : '' }}>All Employees (Company-wide)</option>
                                        <option value="permanent" {{ $currType === 'permanent' ? 'selected' : '' }}>Permanent Employees</option>
                                        <option value="contractual" {{ $currType === 'contractual' ? 'selected' : '' }}>Contractual / Vendor Staff</option>
                                        <option value="intern" {{ $currType === 'intern' ? 'selected' : '' }}>Interns / Trainees</option>
                                        <option value="probation" {{ $currType === 'probation' ? 'selected' : '' }}>Probationary Staff</option>
                                    </select>
                                    <small class="text-muted fs-11">Employee employment classification for rule eligibility.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Salary Condition Summary</label>
                                    <input type="text" class="form-control rounded-2" id="input_salary_condition" name="salary_condition"
                                           placeholder="e.g. Gross monthly pay &le; ₹21,000"
                                           value="{{ old('salary_condition', $applicability->salary_condition ?? '') }}" maxlength="150">
                                    <small class="text-muted fs-11">Human-readable condition shown on salary calculation breakdowns.</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- SECTION 2: Salary & Service Criteria -->
                            <div class="section-title">2. Financial Slabs & Service Period Criteria</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fs-13 fw-semibold text-dark">Min Salary Threshold (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted">₹</span>
                                        <input type="number" step="0.01" min="0" class="form-control rounded-end-2" id="input_min_salary" name="min_salary"
                                               placeholder="0.00" value="{{ old('min_salary', $applicability->min_salary ?? '0.00') }}">
                                    </div>
                                    <small class="text-muted fs-11">Minimum earnings to qualify (0 = No bottom limit).</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fs-13 fw-semibold text-dark">Max Salary Threshold (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted">₹</span>
                                        <input type="number" step="0.01" min="0" class="form-control rounded-end-2" id="input_max_salary" name="max_salary"
                                               placeholder="0.00" value="{{ old('max_salary', $applicability->max_salary ?? '0.00') }}">
                                    </div>
                                    <small class="text-muted fs-11">Ceiling wage for applicability (0 = No upper limit).</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fs-13 fw-semibold text-dark">Min Service Duration (Days)</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control rounded-start-2" id="input_min_service_days" name="min_service_days"
                                               placeholder="0" value="{{ old('min_service_days', $applicability->min_service_days ?? '0') }}">
                                        <span class="input-group-text bg-light text-muted">Days</span>
                                    </div>
                                    <small class="text-muted fs-11">Qualifying tenure (e.g. 1825 for Gratuity).</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- SECTION 3: Enforcement & Description -->
                            <div class="section-title">3. Effective Schedule & Policy Notes</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Effective From Date</label>
                                    <input type="date" class="form-control rounded-2" id="input_effective_from" name="effective_from"
                                           value="{{ old('effective_from', isset($applicability) && $applicability->effective_from ? $applicability->effective_from->format('Y-m-d') : '') }}">
                                    <small class="text-muted fs-11">Date from which this rule is enforced (blank = immediate).</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Rule Status <span class="text-danger">*</span></label>
                                    @php
                                        $currStatus = old('status', $applicability->status ?? 'Active');
                                    @endphp
                                    <div class="d-flex align-items-center gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="createStatusActive" value="Active"
                                                   {{ $currStatus === 'Active' ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13" for="createStatusActive">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="createStatusInactive" value="Inactive"
                                                   {{ $currStatus === 'Inactive' ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13" for="createStatusInactive">
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted fs-11">Only Active rules are applied during payroll batch generation.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fs-13 fw-semibold text-dark">Statutory Notes & Policy Reference</label>
                                    <textarea class="form-control rounded-2" id="input_description" name="description" rows="3"
                                              placeholder="Cite statutory act references, notification numbers, or internal organization applicability rationale..."
                                              maxlength="1000">{{ old('description', $applicability->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Bottom Action Buttons -->
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <a href="{{ route('masters.payroll.applicability') }}" class="btn btn-outline-secondary rounded-2 px-3">
                                    <i class="ti ti-arrow-left me-1"></i> Cancel
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light border rounded-2 px-3">
                                        <i class="ti ti-rotate-clockwise me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary rounded-2 px-4 shadow-sm">
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ isset($applicability) ? 'Update Applicability Rule' : 'Save & Enforce Rule' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <x-footer />
</div>

@push('scripts')
<script>
    function fillPreset(type) {
        switch(type) {
            case 'pf':
                document.getElementById('input_rule_name').value = "Employees' Provident Fund (EPF) Applicability";
                document.getElementById('input_rule_code').value = 'PF_APPLY';
                document.getElementById('input_applies_to').value = 'permanent';
                document.getElementById('input_salary_condition').value = 'Basic <= ₹15,000 or voluntary contribution';
                document.getElementById('input_min_salary').value = '0.00';
                document.getElementById('input_max_salary').value = '15000.00';
                document.getElementById('input_min_service_days').value = '0';
                document.getElementById('input_description').value = 'Statutory Provident Fund scheme applicable to permanent staff with monthly basic wages up to INR 15,000.';
                break;
            case 'esic':
                document.getElementById('input_rule_name').value = 'Employee State Insurance (ESIC) Coverage';
                document.getElementById('input_rule_code').value = 'ESI_APPLY';
                document.getElementById('input_applies_to').value = 'all';
                document.getElementById('input_salary_condition').value = 'Gross monthly earnings <= ₹21,000';
                document.getElementById('input_min_salary').value = '0.00';
                document.getElementById('input_max_salary').value = '21000.00';
                document.getElementById('input_min_service_days').value = '0';
                document.getElementById('input_description').value = 'Mandatory social security and medical benefit coverage for staff whose gross salary is at or below INR 21,000 per month.';
                break;
            case 'pt':
                document.getElementById('input_rule_name').value = 'Professional Tax (PT) State Liability';
                document.getElementById('input_rule_code').value = 'PT_APPLY';
                document.getElementById('input_applies_to').value = 'all';
                document.getElementById('input_salary_condition').value = 'Gross salary exceeding state threshold (> ₹10,000)';
                document.getElementById('input_min_salary').value = '10000.01';
                document.getElementById('input_max_salary').value = '0.00';
                document.getElementById('input_min_service_days').value = '0';
                document.getElementById('input_description').value = 'State revenue tax levied according to employee work location and monthly gross income slabs.';
                break;
            case 'gratuity':
                document.getElementById('input_rule_name').value = 'Payment of Gratuity Benefit';
                document.getElementById('input_rule_code').value = 'GRATUITY_APPLY';
                document.getElementById('input_applies_to').value = 'permanent';
                document.getElementById('input_salary_condition').value = 'Continuous active tenure >= 5 continuous years (1825 days)';
                document.getElementById('input_min_salary').value = '0.00';
                document.getElementById('input_max_salary').value = '0.00';
                document.getElementById('input_min_service_days').value = '1825';
                document.getElementById('input_description').value = 'Gratuity entitlement payable upon exit or retirement after minimum qualifying tenure of 5 continuous years.';
                break;
            case 'intern':
                document.getElementById('input_rule_name').value = 'Intern Stipend & Allowance Exemption';
                document.getElementById('input_rule_code').value = 'INTERN_APPLY';
                document.getElementById('input_applies_to').value = 'intern';
                document.getElementById('input_salary_condition').value = 'Fixed Stipend only (Exempt from PF/ESIC deductions)';
                document.getElementById('input_min_salary').value = '5000.00';
                document.getElementById('input_max_salary').value = '35000.00';
                document.getElementById('input_min_service_days').value = '0';
                document.getElementById('input_description').value = 'Internship remuneration schedule exempt from statutory employee deductions and governed by academic agreement.';
                break;
        }
    }

    // Auto-uppercase rule code
    document.getElementById('input_rule_code').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush

@endsection
