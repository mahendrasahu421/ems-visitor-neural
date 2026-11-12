@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Top Bar -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="mb-1 font-weight-bold text-dark">{{ $title }}</h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-smart-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.resignation.index') }}">Resignations &amp; Exits</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Initiate Separation</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back to Report
                </a>
            </div>
        </div>

        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="ti ti-alert-circle me-1"></i> Please fix the following errors:</div>
                <ul class="mb-0 ps-3 fs-13">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                                <i class="ti ti-user-minus fs-18"></i>
                            </span>
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Initiate Employee Separation Notice</h5>
                                <small class="text-muted">Enter separation details. Automated notifications will be sent to the employee, their supervisor, and specified CCs.</small>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.resignation.store') }}" enctype="multipart/form-data" class="card-body p-4" id="adminExitForm">
                        @csrf

                        <!-- Employee Selection -->
                        <div class="mb-4">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Select Employee <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" id="employeeSelect" class="form-select select2" required>
                                <option value="">-- Choose Employee to Separate --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->employee_id }}"
                                        data-supervisor="{{ $emp->supervisorEmployee?->employee_name ?? 'None' }}"
                                        data-supervisor-email="{{ $emp->supervisorEmployee?->employee_email ?? '' }}"
                                        data-agency="{{ $emp->agency?->agency_name ?? 'Direct' }}"
                                        data-designation="{{ $emp->designation?->name ?? 'Staff' }}"
                                        data-email="{{ $emp->employee_email }}"
                                        {{ old('employee_id') == $emp->employee_id ? 'selected' : '' }}>
                                        {{ $emp->employee_name }} ({{ $emp->employee_id }}) - {{ $emp->designation?->name ?? 'Staff' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Live Employee Card Preview -->
                        <div id="employeePreviewCard" class="p-3 bg-light rounded-3 mb-4 d-none" style="border: 1px dashed #cbd5e1;">
                            <div class="row g-2 fs-13">
                                <div class="col-sm-6">
                                    <span class="text-muted">Email:</span> <strong id="previewEmail" class="text-dark"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Designation:</span> <strong id="previewDesig" class="text-dark"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Supervisor:</span> <strong id="previewSup" class="text-dark"></strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted">Agency:</span> <strong id="previewAgency" class="text-dark"></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Reason for Separation -->
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Primary Separation Reason <span class="text-danger">*</span>
                            </label>
                            <select name="reason" class="form-select" required>
                                <option value="">-- Select Reason --</option>
                                <option value="Voluntary Resignation" {{ old('reason') == 'Voluntary Resignation' ? 'selected' : '' }}>Voluntary Resignation (Logged on Employee Behalf)</option>
                                <option value="Performance Separation" {{ old('reason') == 'Performance Separation' ? 'selected' : '' }}>Performance Separation</option>
                                <option value="Mutual Separation Agreement" {{ old('reason') == 'Mutual Separation Agreement' ? 'selected' : '' }}>Mutual Separation Agreement</option>
                                <option value="End of Contract / Project Completion" {{ old('reason') == 'End of Contract / Project Completion' ? 'selected' : '' }}>End of Contract / Project Completion</option>
                                <option value="Redundancy / Restructuring" {{ old('reason') == 'Redundancy / Restructuring' ? 'selected' : '' }}>Redundancy / Restructuring</option>
                                <option value="Disciplinary / Policy Non-compliance" {{ old('reason') == 'Disciplinary / Policy Non-compliance' ? 'selected' : '' }}>Disciplinary / Policy Non-compliance</option>
                                <option value="Prolonged Absence / Job Abandonment" {{ old('reason') == 'Prolonged Absence / Job Abandonment' ? 'selected' : '' }}>Prolonged Absence / Job Abandonment</option>
                                <option value="Health / Medical Grounds" {{ old('reason') == 'Health / Medical Grounds' ? 'selected' : '' }}>Health / Medical Grounds</option>
                                <option value="Other" {{ old('reason') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Notice Period & Last Working Day -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark">
                                    Notice Period (Days) <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="noticePeriodInput" name="notice_period_days"
                                    class="form-control" min="0" max="180"
                                    value="{{ old('notice_period_days', $defaultNoticeDays) }}" required>
                                <small class="text-muted">Enter 0 for immediate separation/relieving.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark">
                                    Last Working Day (LWD) <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="lwdInput" name="requested_last_working_day"
                                    class="form-control"
                                    value="{{ old('requested_last_working_day', $suggestedLwd) }}" required>
                                <small class="text-muted">Auto-computed from notice period, or pick a custom date.</small>
                            </div>
                        </div>

                        <!-- Separation Statement / Notes -->
                        <div class="mb-4">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Detailed Reason / Separation Statement <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason_details" rows="5" class="form-control fs-13" required
                                placeholder="Detail the context of the exit, handover roadmap, terms of separation, or reference communications...">{{ old('reason_details') }}</textarea>
                            <small class="text-muted">Minimum 10 characters.</small>
                        </div>

                        <!-- Dynamic CC Email Recipients -->
                        <div class="mb-4 p-3 bg-light rounded-3" style="border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <label class="form-label fs-13 fw-bold text-dark mb-0">
                                        <i class="ti ti-mail-forward me-1 text-primary"></i> CC Email Recipients (Optional)
                                    </label>
                                    <small class="text-muted d-block fs-12">Add other stakeholders (HR, Project Head, Client Manager) who should receive separation notifications.</small>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addCcBtn">
                                    <i class="ti ti-plus me-1"></i> Add CC
                                </button>
                            </div>

                            <div id="ccEmailContainer">
                                @if (old('cc_emails'))
                                    @foreach (old('cc_emails') as $oldCc)
                                        <div class="input-group input-group-sm mb-2 cc-row">
                                            <span class="input-group-text bg-white"><i class="ti ti-at text-muted"></i></span>
                                            <input type="email" name="cc_emails[]" class="form-control" value="{{ $oldCc }}" placeholder="colleague@example.com">
                                            <button type="button" class="btn btn-outline-danger remove-cc-btn"><i class="ti ti-trash"></i></button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Optional Document Attachment -->
                        <div class="mb-4">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Attach Supporting Document (Optional)
                            </label>
                            <input type="file" name="resignation_document" class="form-control fs-13" accept=".pdf,.doc,.docx,.jpg,.png">
                            <small class="text-muted">Attach resignation email copy, termination notice, clearance form, etc. (Max 5MB)</small>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <a href="{{ route('admin.resignation.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center shadow-sm">
                                <i class="ti ti-send me-1.5 fs-16"></i> Initiate Separation &amp; Send Notifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column Guidelines -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-info-circle text-primary me-1"></i> Separation Workflow Guide
                        </h6>
                    </div>
                    <div class="card-body p-4 fs-13 text-muted">
                        <ul class="ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong>Email Notifications:</strong> Once initiated, an email notification with action links will be sent to the employee, their supervisor, and all added CCs.</li>
                            <li><strong>Decision Response:</strong> The supervisor or administrator can confirm and accept the separation date.</li>
                            <li><strong>Notice Execution:</strong> The employee continues through their notice period until the approved Last Working Day.</li>
                            <li><strong>Final Relieving:</strong> When clicking "Complete Exit &amp; Relieve", the employee's system status is set to <strong>Inactive</strong>, disabling mobile &amp; web access automatically.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <x-footer />
</div>

@push('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const noticeInput = document.getElementById('noticePeriodInput');
    const lwdInput = document.getElementById('lwdInput');
    const ccContainer = document.getElementById('ccEmailContainer');
    const addCcBtn = document.getElementById('addCcBtn');
    const employeeSelect = document.getElementById('employeeSelect');
    const previewCard = document.getElementById('employeePreviewCard');

    function updateLwd() {
        const days = parseInt(noticeInput.value, 10);
        if (!isNaN(days) && days >= 0) {
            const today = new Date();
            today.setDate(today.getDate() + days);
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            lwdInput.value = `${yyyy}-${mm}-${dd}`;
        }
    }

    if (noticeInput) {
        noticeInput.addEventListener('input', updateLwd);
    }

    // Dynamic CC Addition
    if (addCcBtn && ccContainer) {
        addCcBtn.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'input-group input-group-sm mb-2 cc-row';
            row.innerHTML = `
                <span class="input-group-text bg-white"><i class="ti ti-at text-muted"></i></span>
                <input type="email" name="cc_emails[]" class="form-control" placeholder="colleague@example.com" required>
                <button type="button" class="btn btn-outline-danger remove-cc-btn"><i class="ti ti-trash"></i></button>
            `;
            ccContainer.appendChild(row);
            const input = row.querySelector('input');
            if (input) input.focus();
        });

        ccContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-cc-btn')) {
                e.target.closest('.cc-row').remove();
            }
        });
    }

    // Employee Select preview update
    function updateEmployeePreview() {
        if (!employeeSelect) return;
        const selected = employeeSelect.options[employeeSelect.selectedIndex];
        if (selected && selected.value) {
            document.getElementById('previewEmail').textContent = selected.getAttribute('data-email') || 'N/A';
            document.getElementById('previewDesig').textContent = selected.getAttribute('data-designation') || 'Staff';
            document.getElementById('previewSup').textContent = selected.getAttribute('data-supervisor') || 'None';
            document.getElementById('previewAgency').textContent = selected.getAttribute('data-agency') || 'Direct';
            previewCard.classList.remove('d-none');
        } else {
            previewCard.classList.add('d-none');
        }
    }

    if (employeeSelect) {
        employeeSelect.addEventListener('change', updateEmployeePreview);
        updateEmployeePreview();
    }
});
</script>
@endpush

@endsection
