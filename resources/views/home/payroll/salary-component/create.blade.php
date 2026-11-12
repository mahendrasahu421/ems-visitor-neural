@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .comp-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
        background: #ffffff;
    }

    .comp-form-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
    }

    .type-select-card {
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

    .type-select-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .type-select-card.active-earning {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .type-select-card.active-deduction {
        border-color: #ef4444;
        background: #fef2f2;
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
                    <a href="{{ route('masters.payroll.salary-component') }}" class="text-primary text-decoration-none fs-13 fw-medium">
                        Salary Components
                    </a>
                    <span class="text-muted fs-12">•</span>
                    <span class="text-muted fs-12">{{ isset($component) ? 'Edit Component' : 'Create New' }}</span>
                </div>
                <h3 class="mb-0 fw-bold text-dark">{{ $title }}</h3>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <a href="{{ route('masters.payroll.salary-component') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 rounded-3 px-3">
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
                <div class="card comp-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                {{ isset($component) ? 'Edit Salary Component' : 'Configure New Component' }}
                            </h5>
                            <p class="text-muted fs-12 mb-0">Specify component formula, taxability parameters and statutory settings.</p>
                        </div>
                        <span class="badge {{ isset($component) ? 'bg-warning-subtle text-warning border' : 'bg-primary-subtle text-primary border' }} px-2 py-1 rounded">
                            {{ isset($component) ? 'EDIT MODE' : 'NEW ENTRY' }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ isset($component) ? route('masters.payroll.salary-component.update', $component->id) : route('masters.payroll.salary-component.store') }}">
                            @csrf
                            @if(isset($component))
                                @method('PUT')
                            @endif

                            <!-- SECTION 1: Identity & Classification -->
                            <div class="section-title">1. Component Classification</div>

                            <div class="row g-3 mb-4">
                                <!-- Component Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Component Name <span class="text-danger">*</span></label>
                                    <input type="text" name="component_name" class="form-control"
                                           value="{{ old('component_name', $component->component_name ?? '') }}"
                                           placeholder="e.g. Basic Salary, House Rent Allowance" required id="fieldName">
                                </div>

                                <!-- Component Code -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Component Code <span class="text-danger">*</span></label>
                                    <input type="text" name="component_code" class="form-control text-uppercase"
                                           value="{{ old('component_code', $component->component_code ?? '') }}"
                                           placeholder="e.g. BASIC01, HRA001" required id="fieldCode" style="letter-spacing: 0.5px;">
                                </div>

                                <!-- Type -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13 mb-2">Component Category <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            @php $isEarning = old('type', $component->type ?? 'earning') === 'earning'; @endphp
                                            <label class="type-select-card {{ $isEarning ? 'active-earning' : '' }}" id="cardTypeEarning">
                                                <input type="radio" name="type" value="earning" class="form-check-input mt-0" {{ $isEarning ? 'checked' : '' }}>
                                                <div>
                                                    <div class="fw-bold fs-14 text-success">
                                                        <i class="ti ti-arrow-up-right me-1"></i> Earning Component
                                                    </div>
                                                    <div class="text-muted fs-12">Positive allowance added to employee gross salary</div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            @php $isDeduction = old('type', $component->type ?? '') === 'deduction'; @endphp
                                            <label class="type-select-card {{ $isDeduction ? 'active-deduction' : '' }}" id="cardTypeDeduction">
                                                <input type="radio" name="type" value="deduction" class="form-check-input mt-0" {{ $isDeduction ? 'checked' : '' }}>
                                                <div>
                                                    <div class="fw-bold fs-14 text-danger">
                                                        <i class="ti ti-arrow-down-right me-1"></i> Deduction Component
                                                    </div>
                                                    <div class="text-muted fs-12">Statutory or voluntary deduction subtracted from gross pay</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 2: Role & Regional Applicability -->
                            <div class="section-title d-flex align-items-center justify-content-between">
                                <span>2. Role & Regional Scope (Designation, State & City)</span>
                                <span class="badge bg-light text-muted border fw-normal text-capitalize">Leave blank for Company-Wide</span>
                            </div>

                            <div class="row g-3 mb-4">
                                <!-- Designation Selection -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-13">Designation / Role</label>
                                    @php $selectedDesig = old('designation_id', $component->designation_id ?? ''); @endphp
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="ti ti-briefcase"></i></span>
                                        <select name="designation_id" id="fieldDesignationId" class="form-select">
                                            <option value="">All Designations (Company-Wide)</option>
                                            @foreach($designations as $desig)
                                                <option value="{{ $desig->DesignationID }}" {{ (string)$selectedDesig === (string)$desig->DesignationID ? 'selected' : '' }}>
                                                    {{ $desig->Designation }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted fs-11">Assign only to this specific role or leave blank for all.</small>
                                </div>

                                <!-- State Selection -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-13">State Applicability</label>
                                    @php $selectedState = old('state_id', $component->state_id ?? ''); @endphp
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="ti ti-map"></i></span>
                                        <select name="state_id" id="fieldStateId" class="form-select">
                                            <option value="">All States (Nation-Wide)</option>
                                            @foreach($states as $st)
                                                <option value="{{ $st->id }}" {{ (string)$selectedState === (string)$st->id ? 'selected' : '' }}>
                                                    {{ $st->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted fs-11">For state-specific wages, PT, or regional allowances.</small>
                                </div>

                                <!-- City Selection -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-13">City / Tier</label>
                                    @php $selectedCity = old('city_id', $component->city_id ?? ''); @endphp
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="ti ti-building"></i></span>
                                        <select name="city_id" id="fieldCityId" class="form-select">
                                            <option value="">All Cities / Statewide</option>
                                        </select>
                                    </div>
                                    <small class="text-muted fs-11">Select state to choose specific city (e.g. Metro HRA).</small>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 3: Calculation Method -->
                            <div class="section-title">3. Calculation Method & Rates</div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Calculation Type <span class="text-danger">*</span></label>
                                    @php $calcType = old('calculation_type', $component->calculation_type ?? 'percentage'); @endphp
                                    <select name="calculation_type" id="fieldCalcType" class="form-select" required>
                                        <option value="percentage" {{ $calcType === 'percentage' ? 'selected' : '' }}>Percentage (%) of Base</option>
                                        <option value="fixed" {{ $calcType === 'fixed' ? 'selected' : '' }}>Fixed Monthly Amount (₹)</option>
                                    </select>
                                </div>

                                <div class="col-md-6" id="wrapPercentage">
                                    <label class="form-label fw-semibold fs-13">Percentage Value (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" name="percentage"
                                               class="form-control" value="{{ old('percentage', $component->percentage ?? 0) }}"
                                               placeholder="e.g. 40.00">
                                        <span class="input-group-text bg-light text-dark fw-bold">%</span>
                                    </div>
                                    <small class="text-muted fs-11">Calculated as dynamic percentage during payroll runs.</small>
                                </div>

                                <div class="col-md-6 d-none" id="wrapFixed">
                                    <label class="form-label fw-semibold fs-13">Fixed Monthly Amount (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-dark fw-bold">₹</span>
                                        <input type="number" step="0.01" min="0" name="fixed_amount"
                                               class="form-control" value="{{ old('fixed_amount', $component->fixed_amount ?? 0) }}"
                                               placeholder="e.g. 1600.00">
                                    </div>
                                    <small class="text-muted fs-11">Fixed rupees value assigned per employee monthly.</small>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <!-- SECTION 4: Taxability & Compliance -->
                            <div class="section-title">4. Compliance & Status</div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-13">Taxable Component? <span class="text-danger">*</span></label>
                                    @php $taxable = old('is_taxable', isset($component) ? ($component->is_taxable ? '1' : '0') : '1'); @endphp
                                    <select name="is_taxable" class="form-select" required>
                                        <option value="1" {{ $taxable == '1' ? 'selected' : '' }}>Yes (Subject to Income Tax)</option>
                                        <option value="0" {{ $taxable == '0' ? 'selected' : '' }}>No (Tax Exempt)</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-13">Statutory Component?</label>
                                    @php $statutory = old('is_statutory', isset($component) ? ($component->is_statutory ? '1' : '0') : '0'); @endphp
                                    <select name="is_statutory" class="form-select">
                                        <option value="0" {{ $statutory == '0' ? 'selected' : '' }}>No (Standard / Discretionary)</option>
                                        <option value="1" {{ $statutory == '1' ? 'selected' : '' }}>Yes (EPF, ESI, PT, TDS)</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold fs-13">Master Status <span class="text-danger">*</span></label>
                                    @php $status = old('status', $component->status ?? 'Active'); @endphp
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ $status === 'Active' ? 'selected' : '' }}>Active (Available in Payroll)</option>
                                        <option value="Inactive" {{ $status === 'Inactive' ? 'selected' : '' }}>Inactive (Archived)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Description / Remarks <small class="text-muted fw-normal">(Optional)</small></label>
                                    <textarea name="description" class="form-control" rows="3"
                                              placeholder="Enter functional description or policy guidelines...">{{ old('description', $component->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('masters.payroll.salary-component') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm">
                                    <i class="ti ti-device-floppy"></i>
                                    {{ isset($component) ? 'Update Component' : 'Save Component' }}
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
    // Calculation Type toggle
    function syncCalcType() {
        const val = $('#fieldCalcType').val();
        if (val === 'percentage') {
            $('#wrapPercentage').removeClass('d-none');
            $('#wrapFixed').addClass('d-none');
        } else {
            $('#wrapPercentage').addClass('d-none');
            $('#wrapFixed').removeClass('d-none');
        }
    }
    $('#fieldCalcType').on('change', syncCalcType);
    syncCalcType();

    // Type radio toggle visual card
    $('input[name="type"]').on('change', function() {
        if ($(this).val() === 'earning') {
            $('#cardTypeEarning').addClass('active-earning');
            $('#cardTypeDeduction').removeClass('active-deduction');
        } else {
            $('#cardTypeEarning').removeClass('active-earning');
            $('#cardTypeDeduction').addClass('active-deduction');
        }
    });

    // Dynamic city loader when state changes
    function loadCities(stateId, selectedCityId) {
        const $citySelect = $('#fieldCityId');
        if (!stateId) {
            $citySelect.html('<option value="">All Cities / Statewide</option>');
            return;
        }

        $citySelect.html('<option value="">Loading cities...</option>').prop('disabled', true);

        $.ajax({
            url: `{{ url('masters/payroll/salary-component/get-cities') }}/${stateId}`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                let options = '<option value="">All Cities / Statewide</option>';
                if (res.success && res.cities && res.cities.length > 0) {
                    res.cities.forEach(function(city) {
                        const isSel = (selectedCityId && String(selectedCityId) === String(city.id)) ? 'selected' : '';
                        options += `<option value="${city.id}" ${isSel}>${city.name}</option>`;
                    });
                }
                $citySelect.html(options).prop('disabled', false);
            },
            error: function() {
                $citySelect.html('<option value="">Error loading cities</option>').prop('disabled', false);
            }
        });
    }

    $('#fieldStateId').on('change', function() {
        loadCities($(this).val(), null);
    });

    // Auto-load on page load if state is already selected
    @if(isset($component) && $component->state_id)
        loadCities('{{ $component->state_id }}', '{{ $component->city_id }}');
    @elseif(old('state_id'))
        loadCities('{{ old('state_id') }}', '{{ old('city_id') }}');
    @endif

    // Auto format code
    $('#fieldCode').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    // Auto-suggest code if empty
    $('#fieldName').on('input', function() {
        if ($('#fieldCode').val().length === 0) {
            let suggested = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '_').substring(0, 10);
            $('#fieldCode').val(suggested);
        }
    });
});
</script>
@endpush