@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .payfreq-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .payfreq-form-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .preset-pill-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 12.5px;
        font-weight: 500;
        color: #334155;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .preset-pill-btn:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1d4ed8;
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
                    <a href="{{ route('masters.payroll.payment-frequency') }}" class="text-primary text-decoration-none fs-13 fw-medium">
                        Payment Frequencies
                    </a>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">{{ isset($frequency) ? 'Edit Schedule' : 'New Frequency' }}</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <a href="{{ route('masters.payroll.payment-frequency') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3">
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
            <div class="col-lg-9 col-xl-8">
                <div class="card payfreq-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ isset($frequency) ? 'Edit Payment Frequency' : 'Configure New Payment Frequency' }}
                            </h5>
                            <p class="text-muted fs-12 mb-0">Define cycle recurrence, days per period, and scheduled payday timing.</p>
                        </div>
                        <span class="badge {{ isset($frequency) ? 'bg-warning-subtle text-warning border' : 'bg-primary-subtle text-primary border' }} px-2 py-1 rounded">
                            {{ isset($frequency) ? 'EDIT MODE' : 'NEW CADENCE' }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ isset($frequency) ? route('masters.payroll.payment-frequency.update', $frequency->id) : route('masters.payroll.payment-frequency.store') }}">
                            @csrf
                            @if(isset($frequency))
                                @method('PUT')
                            @endif

                            <!-- SECTION 1: Cadence Identification -->
                            <div class="section-title">1. Frequency Cadence</div>

                            <!-- Quick Preset Selector -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold fs-12 mb-1 text-muted">Quick Schedule Presets:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="preset-pill-btn btn-preset-action" data-name="Monthly (Standard Corporate)" data-code="MONTHLY" data-cycles="12" data-days="30" data-payday="Last working day of the month">
                                        <i class="ti ti-calendar"></i> Monthly (12/yr)
                                    </button>
                                    <button type="button" class="preset-pill-btn btn-preset-action" data-name="Semi-Monthly (Bi-Monthly)" data-code="SEMI_MONTHLY" data-cycles="24" data-days="15" data-payday="15th and last day of month">
                                        <i class="ti ti-calendar-event"></i> Semi-Monthly (24/yr)
                                    </button>
                                    <button type="button" class="preset-pill-btn btn-preset-action" data-name="Bi-Weekly (Fortnightly)" data-code="BI_WEEKLY" data-cycles="26" data-days="14" data-payday="Every alternate Friday">
                                        <i class="ti ti-clock"></i> Bi-Weekly (26/yr)
                                    </button>
                                    <button type="button" class="preset-pill-btn btn-preset-action" data-name="Weekly Schedule" data-code="WEEKLY" data-cycles="52" data-days="7" data-payday="Every Saturday">
                                        <i class="ti ti-calendar-stats"></i> Weekly (52/yr)
                                    </button>
                                    <button type="button" class="preset-pill-btn btn-preset-action" data-name="Daily Wage" data-code="DAILY" data-cycles="365" data-days="1" data-payday="Daily upon punch regularization">
                                        <i class="ti ti-bolt"></i> Daily (365/yr)
                                    </button>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <!-- Frequency Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Frequency Name <span class="text-danger">*</span></label>
                                    <input type="text" name="frequency_name" class="form-control"
                                           value="{{ old('frequency_name', $frequency->frequency_name ?? '') }}"
                                           placeholder="e.g. Monthly, Bi-Weekly, Weekly" required id="fieldName">
                                </div>

                                <!-- Frequency Code -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Frequency Code <span class="text-danger">*</span></label>
                                    <input type="text" name="frequency_code" class="form-control text-uppercase"
                                           value="{{ old('frequency_code', $frequency->frequency_code ?? '') }}"
                                           placeholder="e.g. MONTHLY, BI_WEEKLY, WEEKLY" required id="fieldCode" style="letter-spacing: 0.5px;">
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 2: Cycle Metrics & Schedule Rules -->
                            <div class="section-title">2. Cycle Metrics & Payday Rules</div>

                            <div class="row g-3 mb-4">
                                <!-- Cycles Per Year -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Cycles / Year <span class="text-danger">*</span></label>
                                    <input type="number" min="1" max="365" name="cycles_per_year" class="form-control"
                                           value="{{ old('cycles_per_year', $frequency->cycles_per_year ?? 12) }}"
                                           required id="fieldCycles">
                                    <small class="text-muted fs-11">Total payroll cycles processed per year (e.g. 12, 24, 26, 52).</small>
                                </div>

                                <!-- Days Per Cycle -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Days / Cycle <span class="text-danger">*</span></label>
                                    <input type="number" min="1" max="365" name="days_per_cycle" class="form-control"
                                           value="{{ old('days_per_cycle', $frequency->days_per_cycle ?? 30) }}"
                                           required id="fieldDays">
                                    <small class="text-muted fs-11">Nominal calendar days per period (e.g. 30, 15, 14, 7, 1).</small>
                                </div>

                                <!-- Scheduled Pay Day Rule -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Scheduled Pay Day Rule</label>
                                    <input type="text" name="pay_day_rule" class="form-control"
                                           value="{{ old('pay_day_rule', $frequency->pay_day_rule ?? '') }}"
                                           placeholder="e.g. Last working day of the month, Every alternate Friday" id="fieldPayday">
                                    <small class="text-muted fs-11 mt-1 d-block">Expected salary computation and credit day.</small>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 3: Organization Settings & Status -->
                            <div class="section-title">3. Status & Organization Policy</div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="form-check form-switch pt-1">
                                        @php $isDefault = old('is_default', isset($frequency) && $frequency->is_default); @endphp
                                        <input class="form-check-input" type="checkbox" id="fieldDefault" name="is_default" value="1" {{ $isDefault ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold fs-13" for="fieldDefault">
                                            Set as Organization Default Payroll Cadence
                                        </label>
                                    </div>
                                    <small class="text-muted fs-11 ms-5 d-block">Pre-selected for new employee salary structures.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Cadence Status <span class="text-danger">*</span></label>
                                    @php $status = old('status', $frequency->status ?? 'Active'); @endphp
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ $status === 'Active' ? 'selected' : '' }}>Active (Available for Payroll)</option>
                                        <option value="Inactive" {{ $status === 'Inactive' ? 'selected' : '' }}>Inactive (Disabled)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Operational Notes & Attendance Cutoff Guidelines <small class="text-muted fw-normal">(Optional)</small></label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="e.g. Attendance cutoff is 25th of month for monthly payroll run...">{{ old('description', $frequency->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('masters.payroll.payment-frequency') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm">
                                    <i class="ti ti-device-floppy"></i>
                                    {{ isset($frequency) ? 'Update Frequency' : 'Save Frequency' }}
                                </button>
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

@endsection

@push('after_scripts')
<script>
$(document).ready(function() {
    $('.btn-preset-action').on('click', function() {
        $('#fieldName').val($(this).data('name'));
        $('#fieldCode').val($(this).data('code'));
        $('#fieldCycles').val($(this).data('cycles'));
        $('#fieldDays').val($(this).data('days'));
        $('#fieldPayday').val($(this).data('payday'));
    });

    $('#fieldCode').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    $('#fieldName').on('input', function() {
        if ($('#fieldCode').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 15);
            $('#fieldCode').val(suggested);
        }
    });
});
</script>
@endpush
