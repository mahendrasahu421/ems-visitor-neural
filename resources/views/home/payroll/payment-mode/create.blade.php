@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .paymode-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .paymode-form-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .channel-choice-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .channel-choice-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .channel-choice-card.active-electronic {
        border-color: #0284c7;
        background: #f0f9ff;
    }

    .channel-choice-card.active-physical {
        border-color: #d97706;
        background: #fffbeb;
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
                    <a href="{{ route('masters.payroll.payment-mode') }}" class="text-primary text-decoration-none fs-13 fw-medium">
                        Payment Modes
                    </a>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">{{ isset($mode) ? 'Edit Mode' : 'New Disbursement Mode' }}</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <a href="{{ route('masters.payroll.payment-mode') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3">
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
                <div class="card paymode-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ isset($mode) ? 'Edit Payment Mode' : 'Configure New Payment Mode' }}
                            </h5>
                            <p class="text-muted fs-12 mb-0">Set disbursement channel parameters, banking requirement, and fee rules.</p>
                        </div>
                        <span class="badge {{ isset($mode) ? 'bg-warning-subtle text-warning border' : 'bg-primary-subtle text-primary border' }} px-2 py-1 rounded">
                            {{ isset($mode) ? 'EDIT MODE' : 'NEW MODE' }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ isset($mode) ? route('masters.payroll.payment-mode.update', $mode->id) : route('masters.payroll.payment-mode.store') }}">
                            @csrf
                            @if(isset($mode))
                                @method('PUT')
                            @endif

                            <!-- SECTION 1: Channel Identification -->
                            <div class="section-title">1. Channel Identification</div>

                            <div class="row g-3 mb-4">
                                <!-- Mode Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Payment Mode Name <span class="text-danger">*</span></label>
                                    <input type="text" name="mode_name" class="form-control"
                                           value="{{ old('mode_name', $mode->mode_name ?? '') }}"
                                           placeholder="e.g. Bank Transfer, Cheque, Cash, UPI" required id="fieldName">
                                </div>

                                <!-- Mode Code -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Mode Code <span class="text-danger">*</span></label>
                                    <input type="text" name="mode_code" class="form-control text-uppercase"
                                           value="{{ old('mode_code', $mode->mode_code ?? '') }}"
                                           placeholder="e.g. BANK_TRANSFER, CHEQUE, CASH" required id="fieldCode" style="letter-spacing: 0.5px;">
                                </div>

                                <!-- Channel Type Radio Cards -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13 mb-2">Disbursement Medium <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            @php $isElectronic = old('channel_type', $mode->channel_type ?? 'electronic') === 'electronic'; @endphp
                                            <label class="channel-choice-card {{ $isElectronic ? 'active-electronic' : '' }}" id="cardChannelElectronic">
                                                <input type="radio" name="channel_type" value="electronic" class="form-check-input mt-0" {{ $isElectronic ? 'checked' : '' }}>
                                                <div>
                                                    <div class="fw-bold fs-14 text-primary">
                                                        <i class="ti ti-wifi me-1"></i> Electronic / Digital
                                                    </div>
                                                    <div class="text-muted fs-12">Automated direct credit via ACH / NEFT / RTGS / UPI</div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            @php $isPhysical = old('channel_type', $mode->channel_type ?? '') === 'physical'; @endphp
                                            <label class="channel-choice-card {{ $isPhysical ? 'active-physical' : '' }}" id="cardChannelPhysical">
                                                <input type="radio" name="channel_type" value="physical" class="form-check-input mt-0" {{ $isPhysical ? 'checked' : '' }}>
                                                <div>
                                                    <div class="fw-bold fs-14 text-warning">
                                                        <i class="ti ti-cash me-1"></i> Physical Instrument
                                                    </div>
                                                    <div class="text-muted fs-12">Paper cheque or cash envelope with signed voucher</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 2: Banking & Fee Rules -->
                            <div class="section-title">2. Banking & Fee Rules</div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Mandatory Employee Bank Account?</label>
                                    @php $reqBank = old('requires_bank_details', isset($mode) ? ($mode->requires_bank_details ? '1' : '0') : '1'); @endphp
                                    <select name="requires_bank_details" class="form-select">
                                        <option value="1" {{ $reqBank == '1' ? 'selected' : '' }}>Yes (Bank Account & IFSC Required)</option>
                                        <option value="0" {{ $reqBank == '0' ? 'selected' : '' }}>No (Not Required for this mode)</option>
                                    </select>
                                    <small class="text-muted fs-11 mt-1 d-block">Requires verified bank details before payroll processing.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Processing / Gateway Fee (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                                        <input type="number" step="0.01" min="0" name="processing_fee"
                                               class="form-control" value="{{ old('processing_fee', $mode->processing_fee ?? 0) }}"
                                               placeholder="0.00">
                                    </div>
                                    <small class="text-muted fs-11 mt-1 d-block">Disbursement fee per transaction if applicable.</small>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch pt-2">
                                        @php $isDefault = old('is_default', isset($mode) && $mode->is_default); @endphp
                                        <input class="form-check-input" type="checkbox" id="fieldDefault" name="is_default" value="1" {{ $isDefault ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold fs-13" for="fieldDefault">
                                            Set as Organization Primary Default Mode
                                        </label>
                                    </div>
                                    <small class="text-muted fs-11 ms-5 d-block">Automatically prefilled for newly onboarded staff CTC.</small>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 3: Status & Operational Notes -->
                            <div class="section-title">3. Status & Operational Policy</div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Channel Status <span class="text-danger">*</span></label>
                                    @php $status = old('status', $mode->status ?? 'Active'); @endphp
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ $status === 'Active' ? 'selected' : '' }}>Active (Available for Payroll)</option>
                                        <option value="Inactive" {{ $status === 'Inactive' ? 'selected' : '' }}>Inactive (Disabled)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Operational Guidelines & Turnaround SLA <small class="text-muted fw-normal">(Optional)</small></label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="e.g. Salary credited via corporate HDFC gateway batch transfer on the last working day...">{{ old('description', $mode->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('masters.payroll.payment-mode') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm">
                                    <i class="ti ti-device-floppy"></i>
                                    {{ isset($mode) ? 'Update Payment Mode' : 'Save Payment Mode' }}
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
    $('input[name="channel_type"]').on('change', function() {
        if ($(this).val() === 'electronic') {
            $('#cardChannelElectronic').addClass('active-electronic');
            $('#cardChannelPhysical').removeClass('active-physical');
        } else {
            $('#cardChannelElectronic').removeClass('active-electronic');
            $('#cardChannelPhysical').addClass('active-physical');
        }
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
