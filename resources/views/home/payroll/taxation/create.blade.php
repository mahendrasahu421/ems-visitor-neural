@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .tax-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .tax-form-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .tax-select-card {
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

    .tax-select-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .tax-select-card.active-deduction {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .tax-select-card.active-employer {
        border-color: #3b82f6;
        background: #eff6ff;
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
                    <a href="{{ route('masters.payroll.taxation') }}" class="text-primary text-decoration-none fs-13 fw-medium">
                        Taxation & Compliance
                    </a>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">{{ isset($tax) ? 'Edit Rule' : 'New Statutory Rule' }}</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <a href="{{ route('masters.payroll.taxation') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3">
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
                <div class="card tax-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ isset($tax) ? 'Edit Statutory Tax Rule' : 'Configure New Taxation Rule' }}
                            </h5>
                            <p class="text-muted fs-12 mb-0">Set statutory deduction percentages, salary base and wage ceiling limits.</p>
                        </div>
                        <span class="badge {{ isset($tax) ? 'bg-warning-subtle text-warning border' : 'bg-primary-subtle text-primary border' }} px-2 py-1 rounded">
                            {{ isset($tax) ? 'EDIT MODE' : 'NEW RULE' }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ isset($tax) ? route('masters.payroll.taxation.update', $tax->id) : route('masters.payroll.taxation.store') }}">
                            @csrf
                            @if(isset($tax))
                                @method('PUT')
                            @endif

                            <!-- SECTION 1: Rule Identity -->
                            <div class="section-title">1. Rule Identification</div>

                            <div class="row g-3 mb-4">
                                <!-- Tax Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Tax Rule Name <span class="text-danger">*</span></label>
                                    <input type="text" name="tax_name" class="form-control"
                                           value="{{ old('tax_name', $tax->tax_name ?? '') }}"
                                           placeholder="e.g. Employee Provident Fund (EPF), Professional Tax" required id="fieldName">
                                </div>

                                <!-- Tax Code -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Tax Code <span class="text-danger">*</span></label>
                                    <input type="text" name="tax_code" class="form-control text-uppercase"
                                           value="{{ old('tax_code', $tax->tax_code ?? '') }}"
                                           placeholder="e.g. EPF_EMP, ESIC_EMP, PT_STATE" required id="fieldCode" style="letter-spacing: 0.5px;">
                                </div>

                                <!-- Classification Type -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13 mb-2">Rule Classification <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            @php $isDeduction = old('type', $tax->type ?? 'deduction') === 'deduction'; @endphp
                                            <label class="tax-select-card {{ $isDeduction ? 'active-deduction' : '' }}" id="cardTypeDeduction">
                                                <input type="radio" name="type" value="deduction" class="form-check-input mt-0" {{ $isDeduction ? 'checked' : '' }}>
                                                <div>
                                                    <div class="fw-bold fs-14 text-danger">
                                                        <i class="ti ti-arrow-down-right me-1"></i> Employee Deduction
                                                    </div>
                                                    <div class="text-muted fs-12">Subtracted from employee salary paycheck</div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            @php $isEmployer = old('type', $tax->type ?? '') === 'employer_contribution'; @endphp
                                            <label class="tax-select-card {{ $isEmployer ? 'active-employer' : '' }}" id="cardTypeEmployer">
                                                <input type="radio" name="type" value="employer_contribution" class="form-check-input mt-0" {{ $isEmployer ? 'checked' : '' }}>
                                                <div>
                                                    <div class="fw-bold fs-14 text-primary">
                                                        <i class="ti ti-building-bank me-1"></i> Employer Contribution
                                                    </div>
                                                    <div class="text-muted fs-12">Company liability matching statutory rules</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 2: Calculation Formula & Base -->
                            <div class="section-title">2. Calculation Formula & Wage Base</div>

                            <div class="row g-3 mb-4">
                                <!-- Applicable On Base -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Applicable Base Salary <span class="text-danger">*</span></label>
                                    @php $base = old('applicable_on', $tax->applicable_on ?? 'basic'); @endphp
                                    <select name="applicable_on" class="form-select" required>
                                        <option value="basic" {{ $base === 'basic' ? 'selected' : '' }}>Basic Salary</option>
                                        <option value="gross" {{ $base === 'gross' ? 'selected' : '' }}>Gross Salary</option>
                                        <option value="net" {{ $base === 'net' ? 'selected' : '' }}>Net Salary</option>
                                        <option value="taxable_income" {{ $base === 'taxable_income' ? 'selected' : '' }}>Taxable Income</option>
                                        <option value="ctc" {{ $base === 'ctc' ? 'selected' : '' }}>Annual CTC</option>
                                    </select>
                                </div>

                                <!-- Calculation Method -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Calculation Method <span class="text-danger">*</span></label>
                                    @php $calcBasis = old('calculation_basis', $tax->calculation_basis ?? 'percentage'); @endphp
                                    <select name="calculation_basis" id="fieldCalcBasis" class="form-select" required>
                                        <option value="percentage" {{ $calcBasis === 'percentage' ? 'selected' : '' }}>Percentage Rate (%)</option>
                                        <option value="fixed_slab" {{ $calcBasis === 'fixed_slab' ? 'selected' : '' }}>Fixed Slab Amount (₹)</option>
                                    </select>
                                </div>

                                <!-- Percentage Rate -->
                                <div class="col-md-6" id="wrapRate">
                                    <label class="form-label fw-semibold fs-13">Statutory Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" name="rate"
                                               class="form-control" value="{{ old('rate', $tax->rate ?? 0) }}"
                                               placeholder="e.g. 12.00">
                                        <span class="input-group-text bg-light text-dark fw-bold">%</span>
                                    </div>
                                    <small class="text-muted fs-11">Calculated as dynamic percentage on chosen base salary.</small>
                                </div>

                                <!-- Fixed Amount -->
                                <div class="col-md-6 d-none" id="wrapFixed">
                                    <label class="form-label fw-semibold fs-13">Fixed Monthly Slab (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                                        <input type="number" step="0.01" min="0" name="fixed_amount"
                                               class="form-control" value="{{ old('fixed_amount', $tax->fixed_amount ?? 0) }}"
                                               placeholder="e.g. 200.00">
                                    </div>
                                    <small class="text-muted fs-11">Fixed rupee amount deducted per month.</small>
                                </div>

                                <!-- Threshold Ceiling -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">
                                        Wage Ceiling / Cap (₹)
                                        <small class="text-muted fw-normal">(0 = Unlimited)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                                        <input type="number" step="0.01" min="0" name="threshold_limit"
                                               class="form-control" value="{{ old('threshold_limit', $tax->threshold_limit ?? 0) }}"
                                               placeholder="e.g. 15000.00">
                                    </div>
                                    <small class="text-muted fs-11">Max salary ceiling subject to this statutory rule.</small>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 3: Compliance & Validity -->
                            <div class="section-title">3. Validity & Regulatory Notes</div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Effective From Date</label>
                                    @php
                                        $effDate = old('effective_from', isset($tax) && $tax->effective_from ? $tax->effective_from->format('Y-m-d') : date('Y-m-d'));
                                    @endphp
                                    <input type="date" name="effective_from" class="form-control" value="{{ $effDate }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Policy Status <span class="text-danger">*</span></label>
                                    @php $status = old('status', $tax->status ?? 'Active'); @endphp
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ $status === 'Active' ? 'selected' : '' }}>Active (Enforced in Payroll)</option>
                                        <option value="Inactive" {{ $status === 'Inactive' ? 'selected' : '' }}>Inactive (Disabled)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Regulatory Reference & Description <small class="text-muted fw-normal">(Optional)</small></label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="e.g. Employee Provident Funds Act 1952, Section 192 IT Act guidelines...">{{ old('description', $tax->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('masters.payroll.taxation') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm">
                                    <i class="ti ti-device-floppy"></i>
                                    {{ isset($tax) ? 'Update Rule' : 'Save Tax Rule' }}
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
    function syncBasis() {
        const val = $('#fieldCalcBasis').val();
        if (val === 'percentage') {
            $('#wrapRate').removeClass('d-none');
            $('#wrapFixed').addClass('d-none');
        } else {
            $('#wrapRate').addClass('d-none');
            $('#wrapFixed').removeClass('d-none');
        }
    }
    $('#fieldCalcBasis').on('change', syncBasis);
    syncBasis();

    $('input[name="type"]').on('change', function() {
        if ($(this).val() === 'deduction') {
            $('#cardTypeDeduction').addClass('active-deduction');
            $('#cardTypeEmployer').removeClass('active-employer');
        } else {
            $('#cardTypeDeduction').removeClass('active-deduction');
            $('#cardTypeEmployer').addClass('active-employer');
        }
    });

    $('#fieldCode').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    $('#fieldName').on('input', function() {
        if ($('#fieldCode').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 10);
            $('#fieldCode').val(suggested);
        }
    });
});
</script>
@endpush
