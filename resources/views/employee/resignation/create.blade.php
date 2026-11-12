@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="mb-1 font-weight-bold text-dark">{{ $title }}</h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="ti ti-smart-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.resignation.index') }}">Resignation &amp; Exit</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Apply</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('employee.resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back to History
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
                                <i class="ti ti-file-upload fs-18"></i>
                            </span>
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Submit Resignation Notice</h5>
                                <small class="text-muted">Fill out your notice particulars below. An automated email will be sent to your supervisor &amp; HR.</small>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('employee.resignation.store') }}" enctype="multipart/form-data" class="card-body p-4" id="resignationForm">
                        @csrf

                        <!-- Reason for Resignation -->
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Primary Reason for Leaving <span class="text-danger">*</span>
                            </label>
                            <select name="reason" id="resignationReasonSelect" class="form-select" required>
                                <option value="">-- Select Resignation Reason --</option>
                                <option value="Better Career Opportunity" @selected(old('reason') === 'Better Career Opportunity')>Better Career Opportunity</option>
                                <option value="Higher Education / Studies" @selected(old('reason') === 'Higher Education / Studies')>Higher Education / Studies</option>
                                <option value="Relocation / Moving City" @selected(old('reason') === 'Relocation / Moving City')>Relocation / Moving City</option>
                                <option value="Personal & Family Obligations" @selected(old('reason') === 'Personal & Family Obligations')>Personal &amp; Family Obligations</option>
                                <option value="Health Concerns / Medical" @selected(old('reason') === 'Health Concerns / Medical')>Health Concerns / Medical</option>
                                <option value="Career Transition / Industry Change" @selected(old('reason') === 'Career Transition / Industry Change')>Career Transition / Industry Change</option>
                                <option value="Compensation & Benefits" @selected(old('reason') === 'Compensation & Benefits')>Compensation &amp; Benefits</option>
                                <option value="Work-Life Balance" @selected(old('reason') === 'Work-Life Balance')>Work-Life Balance</option>
                                <option value="Other" @selected(old('reason') === 'Other')>Other Reasons</option>
                            </select>
                        </div>

                        <!-- Notice Period & Requested Last Working Day -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark">
                                    Notice Period (Days) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="notice_period_days" id="noticePeriodInput" class="form-control"
                                        min="0" max="180" value="{{ old('notice_period_days', $defaultNoticeDays) }}" required>
                                    <span class="input-group-text bg-light fs-13">Days</span>
                                </div>
                                <small class="text-muted fs-11">Standard employment agreement notice is {{ $defaultNoticeDays }} days.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark">
                                    Requested Last Working Day <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="requested_last_working_day" id="requestedLwdInput" class="form-control"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('requested_last_working_day', $suggestedLwd) }}" required>
                                <small class="text-muted fs-11" id="lwdCalculationHint">Auto-calculated based on notice period.</small>
                            </div>
                        </div>

                        <!-- Resignation Letter / Detailed Reason -->
                        <div class="mb-4">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Resignation Statement / Letter Notes <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason_details" class="form-control" rows="6" required minlength="15"
                                placeholder="Dear Management, Please accept this email as formal notification that I am resigning from my position as {{ $employee->designation->name ?? 'Staff' }}. I would like to thank you for the opportunities during my time with the organization...">{{ old('reason_details') }}</textarea>
                            <small class="text-muted fs-11">Provide clear remarks and handover details for your reporting supervisor.</small>
                        </div>

                        <!-- Dynamic CC Emails Section -->
                        <div class="p-3 border rounded-3 mb-4 bg-light-subtle">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fs-13 fw-semibold text-dark mb-0 d-flex align-items-center gap-1">
                                    <i class="ti ti-mail-forward text-primary fs-16"></i> Add Additional CC Email Recipients (Optional)
                                </label>
                                <button type="button" class="btn btn-outline-primary btn-sm px-2.5 py-1 rounded-pill" id="addCcBtn">
                                    <i class="ti ti-plus me-1"></i> Add Recipient
                                </button>
                            </div>
                            <p class="text-muted fs-12 mb-3">
                                By default, your <strong>Supervisor ({{ $supervisor->employee_name ?? 'Assigned Manager' }})</strong> and <strong>HR / Admin</strong> will automatically receive this resignation email. You can add extra colleagues, project managers, or team leads below.
                            </p>

                            <div id="ccEmailContainer" class="d-flex flex-column gap-2">
                                @php
                                    $oldCcs = old('cc_emails', ['']);
                                    if (empty($oldCcs)) $oldCcs = [''];
                                @endphp
                                @foreach ($oldCcs as $idx => $cc)
                                    <div class="input-group cc-row">
                                        <span class="input-group-text bg-white"><i class="ti ti-mail text-muted"></i></span>
                                        <input type="email" name="cc_emails[]" class="form-control" value="{{ $cc }}" placeholder="colleague@example.com, manager@domain.com">
                                        <button type="button" class="btn btn-outline-danger remove-cc-btn" title="Remove"><i class="ti ti-trash"></i></button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Supporting Document Upload -->
                        <div class="mb-4">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Upload Signed Resignation Letter / Document (Optional)
                            </label>
                            <input type="file" name="resignation_document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                            <small class="text-muted fs-11">Allowed file types: PDF, DOCX, JPG, PNG (Max 5 MB).</small>
                        </div>

                        <!-- Declaration -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="declarationCheck" required>
                            <label class="form-check-label fs-13 text-muted" for="declarationCheck">
                                I confirm that this resignation submission is voluntary and accurate, and I agree to fulfill the required handover and clearance protocols before my last working day.
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('employee.resignation.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center shadow-sm" id="submitBtn">
                                <i class="ti ti-send me-1.5"></i> Submit Resignation
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Side Guidelines Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #f8fafc;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="avatar avatar-xs rounded bg-primary text-white d-inline-flex align-items-center justify-content-center">
                                <i class="ti ti-info-circle fs-15"></i>
                            </span>
                            <h5 class="card-title mb-0 fw-bold text-dark">Exit Policy Summary</h5>
                        </div>
                        <ul class="ps-3 fs-13 text-muted mb-4" style="line-height: 1.7;">
                            <li><strong>Notice Obligation:</strong> Standard notice duration is 30 days unless negotiated with your reporting manager.</li>
                            <li><strong>Approval Workflow:</strong> Your supervisor and HR management will review your request and confirm your official Relieving Date.</li>
                            <li><strong>Knowledge Transfer:</strong> You will be required to prepare a handover document and clear pending items with your designated team members.</li>
                            <li><strong>Full &amp; Final:</strong> Gratuity, leaves encashment, and final payroll settlement are processed in the subsequent payroll cycle post relieving.</li>
                        </ul>

                        <div class="p-3 bg-white border rounded-3">
                            <div class="fs-12 fw-bold text-dark text-uppercase mb-1">Assigned Supervisor</div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center">
                                    {{ strtoupper(substr($supervisor->employee_name ?? 'S', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark fs-13">{{ $supervisor->employee_name ?? 'Direct Management' }}</div>
                                    <small class="text-muted">{{ $supervisor->employee_email ?? 'Support / HR' }}</small>
                                </div>
                            </div>
                        </div>
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
        const noticePeriodInput = document.getElementById('noticePeriodInput');
        const requestedLwdInput = document.getElementById('requestedLwdInput');
        const lwdHint = document.getElementById('lwdCalculationHint');

        // Dynamic notice period to LWD calculation
        function updateLwd() {
            const days = parseInt(noticePeriodInput.value) || 0;
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + days);

            const yyyy = targetDate.getFullYear();
            const mm = String(targetDate.getMonth() + 1).padStart(2, '0');
            const dd = String(targetDate.getDate()).padStart(2, '0');

            requestedLwdInput.value = `${yyyy}-${mm}-${dd}`;
            if (lwdHint) {
                lwdHint.textContent = `Calculated: ${days} days from today (${dd}/${mm}/${yyyy})`;
            }
        }

        noticePeriodInput.addEventListener('input', updateLwd);

        // Dynamic CC row addition & removal
        const ccContainer = document.getElementById('ccEmailContainer');
        const addCcBtn = document.getElementById('addCcBtn');

        addCcBtn.addEventListener('click', function () {
            const div = document.createElement('div');
            div.className = 'input-group cc-row';
            div.innerHTML = `
                <span class="input-group-text bg-white"><i class="ti ti-mail text-muted"></i></span>
                <input type="email" name="cc_emails[]" class="form-control" placeholder="colleague@example.com">
                <button type="button" class="btn btn-outline-danger remove-cc-btn" title="Remove"><i class="ti ti-trash"></i></button>
            `;
            ccContainer.appendChild(div);
            attachRemoveEvents();
        });

        function attachRemoveEvents() {
            document.querySelectorAll('.remove-cc-btn').forEach(btn => {
                btn.onclick = function () {
                    const row = this.closest('.cc-row');
                    if (document.querySelectorAll('.cc-row').length > 1) {
                        row.remove();
                    } else {
                        row.querySelector('input').value = '';
                    }
                };
            });
        }
        attachRemoveEvents();
    });
</script>
@endpush

@endsection
