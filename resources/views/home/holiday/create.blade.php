@extends('layout.master')
@section('title', $title ?? 'Add New Holiday')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .holiday-create-page .form-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .holiday-create-page .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.01em;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .holiday-create-page .form-section-divider {
            border-top: 1px dashed #e2e8f0;
            margin: 1.75rem 0;
        }

        .holiday-create-page .form-control,
        .holiday-create-page .form-select {
            border-color: #cbd5e1;
            font-size: 0.92rem;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .holiday-create-page .form-control:focus,
        .holiday-create-page .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .holiday-create-page .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
            border-radius: 8px 0 0 8px;
        }

        /* Holiday Type Selectable Cards */
        .holiday-type-selector .type-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            background: #ffffff;
            height: 100%;
        }

        .holiday-type-selector .type-card:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
        }

        .holiday-type-selector input[type="radio"]:checked + .type-card.type-common {
            border-color: #dc2626;
            background-color: #fef2f2;
            box-shadow: 0 0 0 1px #dc2626;
        }

        .holiday-type-selector input[type="radio"]:checked + .type-card.type-restricted {
            border-color: #2563eb;
            background-color: #eff6ff;
            box-shadow: 0 0 0 1px #2563eb;
        }

        .holiday-type-selector input[type="radio"]:checked + .type-card.type-other {
            border-color: #4f46e5;
            background-color: #eef2ff;
            box-shadow: 0 0 0 1px #4f46e5;
        }

        /* Suggestion Chips */
        .suggestion-chip {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 500;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .suggestion-chip:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        /* Live Preview Card */
        .live-preview-box {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border-top: 4px solid #0d6efd;
        }

        .info-guide-box {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #f8fafc;
            padding: 16px;
        }
    </style>

    <div class="page-wrapper holiday-create-page">
        <div class="content">

            {{-- 1. Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-plus fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Add New Holiday</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">Calendar Master</span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Settings</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('settings.holiday') }}" class="text-muted">Holiday Master</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Add Holiday</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('settings.holiday') }}" class="btn btn-outline-secondary btn-md d-inline-flex align-items-center gap-1.5 fw-medium">
                        <i class="ti ti-arrow-left fs-16"></i> Back to Holiday List
                    </a>
                </div>
            </div>

            {{-- 2. Form & Preview Grid --}}
            <div class="row g-4">
                {{-- Left: Main Form Card --}}
                <div class="col-lg-8">
                    <div class="card form-card">
                        <div class="card-header bg-transparent py-3 px-4 border-bottom">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-forms text-primary"></i> Holiday Information Form
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            <form class="needs-validation" novalidate method="POST" action="{{ route('settings.holiday.store') }}" id="createHolidayForm">
                                @csrf

                                {{-- Section: Basic Information --}}
                                <div class="section-title">
                                    <i class="ti ti-info-circle text-primary"></i>
                                    <span>1. Basic Holiday Information</span>
                                </div>

                                {{-- Holiday Name --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark fs-13" for="holiday_name">
                                        Holiday / Festival Name <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-sparkles"></i></span>
                                        <input type="text" class="form-control" id="holiday_name" name="holiday_name"
                                            placeholder="e.g. Republic Day, Diwali, Eid-Ul-Fitr, Independence Day"
                                            value="{{ old('holiday_name') }}" required autofocus oninput="updateLivePreview()">
                                    </div>
                                    <div class="invalid-feedback">Please enter holiday / festival name.</div>
                                    @error('holiday_name')
                                        <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Holiday Category / Type --}}
                                <div class="mb-3.5">
                                    <label class="form-label fw-semibold text-dark fs-13 d-block">
                                        Holiday Category / Type <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-2 holiday-type-selector">
                                        {{-- Common Holiday --}}
                                        <div class="col-md-4 col-sm-6">
                                            <input type="radio" class="d-none" name="holiday_type" id="type_common" value="Common" checked onchange="updateLivePreview()">
                                            <label for="type_common" class="type-card type-common d-block">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="avatar avatar-xs rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center fs-11">
                                                        <i class="ti ti-star-filled"></i>
                                                    </span>
                                                    <span class="fw-bold text-dark fs-13">Common Holiday</span>
                                                </div>
                                                <p class="text-muted fs-11 mb-0">Mandatory: All offices remain closed.</p>
                                            </label>
                                        </div>

                                        {{-- Restricted Holiday --}}
                                        <div class="col-md-4 col-sm-6">
                                            <input type="radio" class="d-none" name="holiday_type" id="type_restricted" value="Restricted" onchange="updateLivePreview()">
                                            <label for="type_restricted" class="type-card type-restricted d-block">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="avatar avatar-xs rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fs-11">
                                                        <i class="ti ti-calendar-event"></i>
                                                    </span>
                                                    <span class="fw-bold text-dark fs-13">Restricted Holiday</span>
                                                </div>
                                                <p class="text-muted fs-11 mb-0">Optional: Employees can opt any 5.</p>
                                            </label>
                                        </div>

                                        {{-- Other / Regional --}}
                                        <div class="col-md-4 col-sm-12">
                                            <input type="radio" class="d-none" name="holiday_type" id="type_other" value="Other" onchange="updateLivePreview()">
                                            <label for="type_other" class="type-card type-other d-block">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="avatar avatar-xs rounded-circle bg-indigo text-white d-inline-flex align-items-center justify-content-center fs-11" style="background-color: #6366f1;">
                                                        <i class="ti ti-building"></i>
                                                    </span>
                                                    <span class="fw-bold text-dark fs-13">Company / Other</span>
                                                </div>
                                                <p class="text-muted fs-11 mb-0">Custom organization observance.</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Date, Day & Status Row --}}
                                <div class="row g-3 mb-3">
                                    {{-- Holiday Date --}}
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold text-dark fs-13" for="holiday_date">
                                            Holiday Date <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                            <input type="date" class="form-control" id="holiday_date" name="holiday_date"
                                                value="{{ old('holiday_date') }}" required onchange="onDateChanged()">
                                        </div>
                                        <div class="invalid-feedback">Please select holiday date.</div>
                                        @error('holiday_date')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Day of Week (Auto Computed) --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark fs-13" for="day_display">
                                            Day of Week <small class="text-muted fw-normal">(Auto)</small>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-clock"></i></span>
                                            <input type="text" class="form-control bg-light" id="day_display" readonly placeholder="Auto-calculated">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-dark fs-13" for="status">
                                            Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="status" name="status" required onchange="updateLivePreview()">
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback">Please select status.</div>
                                    </div>
                                </div>

                                <div class="form-section-divider"></div>

                                {{-- Section: Description & Remarks --}}
                                <div class="section-title">
                                    <i class="ti ti-notes text-primary"></i>
                                    <span>2. Remarks & Details</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark fs-13" for="description">
                                        Description / Remarks <small class="text-muted fw-normal">(Optional)</small>
                                    </label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Enter additional remarks or context for this holiday..."
                                        oninput="updateLivePreview()">{{ old('description') }}</textarea>

                                    {{-- Quick Chips --}}
                                    <div class="mt-2 d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="fs-11 text-muted me-1">Quick Suggestions:</span>
                                        <span class="suggestion-chip" onclick="applySuggestion('Mandatory Office Closed')">Mandatory Office Closed</span>
                                        <span class="suggestion-chip" onclick="applySuggestion('Optional Festival Holiday')">Optional Festival Holiday</span>
                                        <span class="suggestion-chip" onclick="applySuggestion('National Gazetted Holiday')">National Gazetted Holiday</span>
                                        <span class="suggestion-chip" onclick="applySuggestion('Religious Observance')">Religious Observance</span>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-4 flex-wrap gap-2">
                                    <a href="{{ route('settings.holiday') }}" class="btn btn-light border btn-md d-inline-flex align-items-center gap-1.5 text-muted">
                                        <i class="ti ti-x fs-15"></i> Cancel
                                    </a>

                                    <div class="d-flex align-items-center gap-2">
                                        <button type="reset" class="btn btn-outline-secondary btn-md" onclick="setTimeout(updateLivePreview, 50)">
                                            <i class="ti ti-rotate-clockwise fs-15"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center gap-1.5 px-4">
                                            <i class="ti ti-device-floppy fs-16"></i> Save Holiday
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right: Live Preview & Policy Guidelines --}}
                <div class="col-lg-4">
                    {{-- Live Preview Box --}}
                    <div class="card live-preview-box mb-4" id="livePreviewContainer">
                        <div class="card-header bg-transparent py-3 px-3.5 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fs-12 fw-bold text-dark text-uppercase tracking-wider d-flex align-items-center gap-1.5">
                                <i class="ti ti-eye text-primary"></i> Live Calendar Card Preview
                            </span>
                            <span class="badge bg-light text-muted border fs-10">Live</span>
                        </div>

                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="avatar avatar-sm rounded-3 bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center" id="previewIcon">
                                    <i class="ti ti-star-filled fs-14"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" id="previewName">Holiday Name Preview</h6>
                                    <span class="fs-11 text-muted" id="previewCategoryBadge">
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-10">Common Holiday</span>
                                    </span>
                                </div>
                            </div>

                            <div class="p-2.5 bg-white border rounded-3 mb-2.5">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-12 text-muted"><i class="ti ti-calendar me-1"></i>Date:</span>
                                    <span class="fw-semibold text-dark fs-12" id="previewDate">Select a date</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="fs-12 text-muted"><i class="ti ti-clock me-1"></i>Day:</span>
                                    <span class="badge bg-light text-secondary border px-2 py-0.5 fs-11" id="previewDay">—</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <span class="fs-12 text-muted"><i class="ti ti-toggle-right me-1"></i>Status:</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-10" id="previewStatus">Active</span>
                                </div>
                            </div>

                            <p class="fs-12 text-muted mb-0 bg-light p-2 rounded-2 border border-dashed" id="previewRemarks">
                                Remarks will appear here...
                            </p>
                        </div>
                    </div>

                    {{-- Policy Guidelines Box --}}
                    <div class="info-guide-box">
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-1.5 mb-2.5 fs-13">
                            <i class="ti ti-bulb text-warning fs-17"></i> Holiday Master Guidelines
                        </h6>
                        <ul class="list-unstyled mb-0 fs-12 text-muted d-flex flex-column gap-2">
                            <li class="d-flex align-items-start gap-1.5">
                                <i class="ti ti-check text-success fs-14 mt-0.5 flex-shrink-0"></i>
                                <span><strong>Common Holidays:</strong> Mandatory calendar closures where all branches and group offices remain closed.</span>
                            </li>
                            <li class="d-flex align-items-start gap-1.5">
                                <i class="ti ti-check text-success fs-14 mt-0.5 flex-shrink-0"></i>
                                <span><strong>Restricted Holidays:</strong> Optional list from which employees can opt for up to 5 festivals.</span>
                            </li>
                            <li class="d-flex align-items-start gap-1.5">
                                <i class="ti ti-check text-success fs-14 mt-0.5 flex-shrink-0"></i>
                                <span><strong>Live Sync:</strong> Newly added active holidays immediately sync with the Mobile Employee App and full calendar.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <x-footer />
    </div>

@endsection

@push('after_scripts')
    <script>
        function onDateChanged() {
            let dateInput = document.getElementById('holiday_date').value;
            let dayDisplay = document.getElementById('day_display');

            if (dateInput) {
                let parts = dateInput.split('-');
                let dt = new Date(parts[0], parts[1] - 1, parts[2]);
                let dayName = dt.toLocaleDateString('en-US', { weekday: 'long' });
                dayDisplay.value = dayName;
            } else {
                dayDisplay.value = '';
            }

            updateLivePreview();
        }

        function applySuggestion(text) {
            let textarea = document.getElementById('description');
            if (textarea.value.trim() === '') {
                textarea.value = text;
            } else {
                textarea.value += ' - ' + text;
            }
            updateLivePreview();
        }

        function updateLivePreview() {
            let name = document.getElementById('holiday_name').value.trim() || 'Holiday Name Preview';
            let dateInput = document.getElementById('holiday_date').value;
            let status = document.getElementById('status').value;
            let remarks = document.getElementById('description').value.trim() || 'Remarks will appear here...';

            let selectedType = 'Common';
            let radios = document.getElementsByName('holiday_type');
            for (let r of radios) {
                if (r.checked) {
                    selectedType = r.value;
                    break;
                }
            }

            // Preview Name
            document.getElementById('previewName').textContent = name;

            // Preview Category
            let catContainer = document.getElementById('previewCategoryBadge');
            let iconContainer = document.getElementById('previewIcon');
            let boxContainer = document.getElementById('livePreviewContainer');

            if (selectedType === 'Common') {
                catContainer.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-10">★ Common Holiday (Mandatory)</span>';
                iconContainer.className = 'avatar avatar-sm rounded-3 bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center';
                iconContainer.innerHTML = '<i class="ti ti-star-filled fs-14"></i>';
                boxContainer.style.borderTopColor = '#dc2626';
            } else if (selectedType === 'Restricted') {
                catContainer.innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fs-10">Restricted Holiday (Optional)</span>';
                iconContainer.className = 'avatar avatar-sm rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center';
                iconContainer.innerHTML = '<i class="ti ti-calendar fs-14"></i>';
                boxContainer.style.borderTopColor = '#2563eb';
            } else {
                catContainer.innerHTML = '<span class="badge bg-light text-dark border px-2 py-0.5 fs-10">Company Holiday</span>';
                iconContainer.className = 'avatar avatar-sm rounded-3 bg-light text-dark d-inline-flex align-items-center justify-content-center';
                iconContainer.innerHTML = '<i class="ti ti-building fs-14"></i>';
                boxContainer.style.borderTopColor = '#4f46e5';
            }

            // Preview Date & Day
            if (dateInput) {
                let parts = dateInput.split('-');
                let dt = new Date(parts[0], parts[1] - 1, parts[2]);
                document.getElementById('previewDate').textContent = dt.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
                document.getElementById('previewDay').textContent = dt.toLocaleDateString('en-US', { weekday: 'long' });
            } else {
                document.getElementById('previewDate').textContent = 'Select a date';
                document.getElementById('previewDay').textContent = '—';
            }

            // Preview Status
            let statusBadge = document.getElementById('previewStatus');
            if (status == '1') {
                statusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-10';
                statusBadge.textContent = 'Active';
            } else {
                statusBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-10';
                statusBadge.textContent = 'Inactive';
            }

            // Preview Remarks
            document.getElementById('previewRemarks').textContent = remarks;
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            onDateChanged();
            updateLivePreview();
        });
    </script>
@endpush
