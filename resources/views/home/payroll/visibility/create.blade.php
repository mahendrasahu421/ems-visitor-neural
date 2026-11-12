@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .visibility-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .visibility-form-card .card-header {
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
                    <a href="{{ route('masters.payroll.visibility') }}" class="text-primary text-decoration-none fs-13 fw-medium">
                        Visibility & Access Control
                    </a>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">{{ isset($visibility) ? 'Edit Rule' : 'New Rule' }}</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <a href="{{ route('masters.payroll.visibility') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3">
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
                <div class="card visibility-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ isset($visibility) ? 'Edit Visibility & Access Rule' : 'Configure New Visibility & Access Rule' }}
                            </h5>
                            <p class="text-muted fs-12 mb-0">Specify access levels, salary redaction, structure management, and batch processing privileges.</p>
                        </div>
                        @if(isset($visibility))
                            <span class="badge bg-light text-secondary border px-3 py-2 font-monospace fs-12">
                                ID #{{ $visibility->id }}
                            </span>
                        @endif
                    </div>

                    <div class="card-body p-4">
                        @if(!isset($visibility))
                            <!-- Quick Presets -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <label class="section-title d-flex align-items-center gap-1 mb-2">
                                    <i class="ti ti-sparkles text-warning fs-14"></i> Quick Role Presets
                                </label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="preset-pill-btn" onclick="fillVisPreset('super_admin')">
                                        <i class="ti ti-crown"></i> Super Admin
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillVisPreset('hr')">
                                        <i class="ti ti-users"></i> HR Admin
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillVisPreset('finance')">
                                        <i class="ti ti-building-bank"></i> Finance
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillVisPreset('manager')">
                                        <i class="ti ti-user-check"></i> Manager (Masked)
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillVisPreset('employee')">
                                        <i class="ti ti-user"></i> Employee ESS
                                    </button>
                                    <button type="button" class="preset-pill-btn" onclick="fillVisPreset('auditor')">
                                        <i class="ti ti-file-certificate"></i> Auditor
                                    </button>
                                </div>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ isset($visibility) ? route('masters.payroll.visibility.update', $visibility->id) : route('masters.payroll.visibility.store') }}"
                              id="fullVisibilityForm">
                            @csrf
                            @if(isset($visibility))
                                @method('PUT')
                            @endif

                            <!-- SECTION 1: Rule Identity -->
                            <div class="section-title">1. Policy Identification & Target Clearance</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-7">
                                    <label class="form-label fs-13 fw-semibold text-dark">Rule Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-2" id="input_rule_name" name="rule_name"
                                           placeholder="e.g. HR Operations & Payroll Administration"
                                           value="{{ old('rule_name', $visibility->rule_name ?? '') }}" required maxlength="150">
                                    <small class="text-muted fs-11">Descriptive name of the access control policy.</small>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-13 fw-semibold text-dark">Rule Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-2 text-uppercase font-monospace" id="input_rule_code" name="rule_code"
                                           placeholder="e.g. VIS_HR_ADMIN"
                                           value="{{ old('rule_code', $visibility->rule_code ?? '') }}" required maxlength="50">
                                    <small class="text-muted fs-11">Unique identifier used by the authorization engine.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Applies To Role / Department <span class="text-danger">*</span></label>
                                    <select class="form-select rounded-2" id="input_applies_to" name="applies_to" required>
                                        @php
                                            $currTarget = old('applies_to', $visibility->applies_to ?? '');
                                        @endphp
                                        <option value="">Select Target Cohort</option>
                                        <optgroup label="System Roles">
                                            @foreach($rolesList as $role)
                                                <option value="{{ $role }}" {{ $currTarget === $role ? 'selected' : '' }}>{{ $role }}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Departments">
                                            @foreach($departmentsList as $dept)
                                                <option value="{{ $dept }}" {{ $currTarget === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    <small class="text-muted fs-11">Target role or department governed by this policy.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Access Level <span class="text-danger">*</span></label>
                                    @php
                                        $currLevel = old('access_level', $visibility->access_level ?? 'Read Only');
                                    @endphp
                                    <select class="form-select rounded-2" id="input_access_level" name="access_level" required>
                                        <option value="Full Access" {{ $currLevel === 'Full Access' ? 'selected' : '' }}>Full Access (View, Edit, Process & Disburse)</option>
                                        <option value="Read Only" {{ $currLevel === 'Read Only' ? 'selected' : '' }}>Read Only (Inspection & Audit only, No Edit)</option>
                                        <option value="Restricted" {{ $currLevel === 'Restricted' ? 'selected' : '' }}>Restricted (Subordinates only, Sensitive CTC Masked)</option>
                                        <option value="Self Only" {{ $currLevel === 'Self Only' ? 'selected' : '' }}>Self Only (Employee Self-Service Own Records)</option>
                                    </select>
                                    <small class="text-muted fs-11">High-level clearance level.</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- SECTION 2: Granular Permissions Matrix -->
                            <div class="section-title">2. Granular Permissions & Salary Redaction</div>
                            <div class="card bg-light border p-3 mb-4 rounded-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="input_can_view_salary" name="can_view_salary" value="1"
                                                   {{ old('can_view_salary', isset($visibility) ? $visibility->can_view_salary : true) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13 fw-semibold text-dark" for="input_can_view_salary">
                                                Can View Salary Details
                                            </label>
                                            <small class="text-muted fs-11 d-block">Authorized to see monthly base salaries and deductions.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="input_can_edit_structure" name="can_edit_structure" value="1"
                                                   {{ old('can_edit_structure', isset($visibility) ? $visibility->can_edit_structure : false) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13 fw-semibold text-dark" for="input_can_edit_structure">
                                                Can Edit Salary Structures & Components
                                            </label>
                                            <small class="text-muted fs-11 d-block">Modify employee compensation breakdown and master rules.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="input_can_process_payroll" name="can_process_payroll" value="1"
                                                   {{ old('can_process_payroll', isset($visibility) ? $visibility->can_process_payroll : false) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13 fw-semibold text-dark" for="input_can_process_payroll">
                                                Can Process & Approve Payroll Batches
                                            </label>
                                            <small class="text-muted fs-11 d-block">Generate monthly payroll, lock batches, and approve payouts.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="input_can_download_reports" name="can_download_reports" value="1"
                                                   {{ old('can_download_reports', isset($visibility) ? $visibility->can_download_reports : false) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13 fw-semibold text-dark" for="input_can_download_reports">
                                                Can Export Payslips & Bank Disbursement Files
                                            </label>
                                            <small class="text-muted fs-11 d-block">Download PDF payslips, bank NEFT/RTGS txt/csv sheets.</small>
                                        </div>
                                    </div>

                                    <div class="col-12 pt-2 border-top">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="input_mask_sensitive_components" name="mask_sensitive_components" value="1"
                                                   {{ old('mask_sensitive_components', isset($visibility) ? $visibility->mask_sensitive_components : false) ? 'checked' : '' }}>
                                            <label class="form-check-label fs-13 fw-semibold text-dark" for="input_mask_sensitive_components">
                                                Mask Sensitive CTC & Executive Bonuses (***)
                                            </label>
                                            <small class="text-muted fs-11 d-block">Redacts annual total cost and confidential bonus packages on screens & printouts.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- SECTION 3: Enforcement & Description -->
                            <div class="section-title">3. Status & Data Governance Justification</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fs-13 fw-semibold text-dark">Rule Status <span class="text-danger">*</span></label>
                                    @php
                                        $currStatus = old('status', $visibility->status ?? 'Active');
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
                                    <small class="text-muted fs-11">Inactive rules revert this cohort to default restrictive access.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fs-13 fw-semibold text-dark">Data Governance Justification & Policy Notes</label>
                                    <textarea class="form-control rounded-2" id="input_description" name="description" rows="3"
                                              placeholder="Cite data privacy regulations (e.g. GDPR, DPDP Act), confidentiality agreements, or executive audit mandates..."
                                              maxlength="1000">{{ old('description', $visibility->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Bottom Action Buttons -->
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <a href="{{ route('masters.payroll.visibility') }}" class="btn btn-outline-secondary rounded-2 px-3">
                                    <i class="ti ti-arrow-left me-1"></i> Cancel
                                </a>
                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light border rounded-2 px-3">
                                        <i class="ti ti-rotate-clockwise me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary rounded-2 px-4 shadow-sm">
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ isset($visibility) ? 'Update Visibility Policy' : 'Save & Enforce Policy' }}
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
    function fillVisPreset(type) {
        switch(type) {
            case 'super_admin':
                document.getElementById('input_rule_name').value = 'Executive & Super Admin Oversight';
                document.getElementById('input_rule_code').value = 'VIS_SUPER_ADMIN';
                document.getElementById('input_applies_to').value = 'Super Admin';
                document.getElementById('input_access_level').value = 'Full Access';
                document.getElementById('input_can_view_salary').checked = true;
                document.getElementById('input_can_edit_structure').checked = true;
                document.getElementById('input_can_process_payroll').checked = true;
                document.getElementById('input_can_download_reports').checked = true;
                document.getElementById('input_mask_sensitive_components').checked = false;
                document.getElementById('input_description').value = 'Complete unrestricted visibility over company-wide compensation, executive CTC, and disbursement batches.';
                break;
            case 'hr':
                document.getElementById('input_rule_name').value = 'HR Operations & Payroll Administration';
                document.getElementById('input_rule_code').value = 'VIS_HR_ADMIN';
                document.getElementById('input_applies_to').value = 'Human Resources';
                document.getElementById('input_access_level').value = 'Full Access';
                document.getElementById('input_can_view_salary').checked = true;
                document.getElementById('input_can_edit_structure').checked = true;
                document.getElementById('input_can_process_payroll').checked = true;
                document.getElementById('input_can_download_reports').checked = true;
                document.getElementById('input_mask_sensitive_components').checked = false;
                document.getElementById('input_description').value = 'Operational authority to configure salary components, process monthly batches, and release employee payslips.';
                break;
            case 'finance':
                document.getElementById('input_rule_name').value = 'Finance & Accounts Disbursement Team';
                document.getElementById('input_rule_code').value = 'VIS_FINANCE';
                document.getElementById('input_applies_to').value = 'Finance';
                document.getElementById('input_access_level').value = 'Full Access';
                document.getElementById('input_can_view_salary').checked = true;
                document.getElementById('input_can_edit_structure').checked = false;
                document.getElementById('input_can_process_payroll').checked = true;
                document.getElementById('input_can_download_reports').checked = true;
                document.getElementById('input_mask_sensitive_components').checked = false;
                document.getElementById('input_description').value = 'Finance department review of approved wage registers, bank payout batches (NEFT/RTGS), and statutory challans.';
                break;
            case 'manager':
                document.getElementById('input_rule_name').value = 'Department Managers & Supervisors';
                document.getElementById('input_rule_code').value = 'VIS_MANAGERS';
                document.getElementById('input_applies_to').value = 'Managers';
                document.getElementById('input_access_level').value = 'Restricted';
                document.getElementById('input_can_view_salary').checked = true;
                document.getElementById('input_can_edit_structure').checked = false;
                document.getElementById('input_can_process_payroll').checked = false;
                document.getElementById('input_can_download_reports').checked = false;
                document.getElementById('input_mask_sensitive_components').checked = true;
                document.getElementById('input_description').value = 'Line managers can inspect attendance and monthly base pay for direct subordinates; executive CTC & bonuses remain masked.';
                break;
            case 'employee':
                document.getElementById('input_rule_name').value = 'Employee Self-Service (ESS) Portal';
                document.getElementById('input_rule_code').value = 'VIS_EMPLOYEE_ESS';
                document.getElementById('input_applies_to').value = 'Employees';
                document.getElementById('input_access_level').value = 'Self Only';
                document.getElementById('input_can_view_salary').checked = true;
                document.getElementById('input_can_edit_structure').checked = false;
                document.getElementById('input_can_process_payroll').checked = false;
                document.getElementById('input_can_download_reports').checked = true;
                document.getElementById('input_mask_sensitive_components').checked = false;
                document.getElementById('input_description').value = 'Standard employee access restricted solely to their personal monthly payslips, Form 16, and tax deduction certificates.';
                break;
            case 'auditor':
                document.getElementById('input_rule_name').value = 'Statutory Compliance & External Audit';
                document.getElementById('input_rule_code').value = 'VIS_AUDITOR';
                document.getElementById('input_applies_to').value = 'Auditors';
                document.getElementById('input_access_level').value = 'Read Only';
                document.getElementById('input_can_view_salary').checked = true;
                document.getElementById('input_can_edit_structure').checked = false;
                document.getElementById('input_can_process_payroll').checked = false;
                document.getElementById('input_can_download_reports').checked = true;
                document.getElementById('input_mask_sensitive_components').checked = false;
                document.getElementById('input_description').value = 'Read-only audit access to statutory registers (PF, ESIC, PT, TDS) for periodic regulatory compliance audits.';
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
