@extends('layout.master')
@section('title', $title ?? 'Edit Leave Policy')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .leave-policy-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .policy-section-header {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.01em;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }

        .policy-section-header i {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .form-section-divider {
            border-top: 1px dashed #e2e8f0;
            margin: 1.75rem 0;
        }

        .input-group-custom .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }

        .form-control-custom {
            border-color: #e2e8f0;
            font-size: 0.92rem;
            padding: 0.6rem 0.85rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            height: 42px !important;
            padding: 6px 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1e293b !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-container+.select2-container {
            display: none !important;
        }
    </style>

    <div class="page-wrapper leave-type-edit-page">
        <div class="content container-fluid">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-edit fs-20"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Edit Leave Policy' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('settings.leave-type') }}" class="text-muted">Leave Types</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Edit #{{ $leaveType->id }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('settings.leave-type') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1.5"></i>Back to Policies
                    </a>
                </div>
            </div>

            {{-- Validation Alert --}}
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-circle fs-20 me-2 mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Please fix the following validation errors:</h6>
                            <ul class="mb-0 ps-3 fs-13">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Main Form Card --}}
            <div class="card leave-policy-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Edit Policy: {{ $leaveType->leave_name }}</h5>
                        <p class="text-muted fs-12 mb-0 mt-0.5">Update policy parameters, allowed quota, and entitlement rules.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-11 font-monospace">
                        Code: {{ $leaveType->leave_code }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <form class="needs-validation" novalidate method="POST" action="{{ route('settings.leave-type.update', $leaveType->id) }}" id="editLeaveTypeForm">
                        @csrf
                        @method('PUT')

                        <!-- SECTION 1: BASIC INFORMATION -->
                        <div class="policy-section-header">
                            <i class="ti ti-file-text text-primary"></i> 1. Policy Identification & Agency
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Agency Name <span class="text-danger">*</span>
                                </label>
                                <select name="agency_id" class="form-select select2" id="agency_id" required>
                                    <option value="">-- Choose Agency --</option>
                                    @foreach ($agencies as $agency)
                                        <option value="{{ $agency->id }}" @selected(old('agency_id', $leaveType->agency_id ?? $leaveType->company_id) == $agency->id)>
                                            {{ $agency->agency_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Leave Type Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-calendar-event"></i></span>
                                    <input type="text" class="form-control form-control-custom" id="leave_name" name="leave_name"
                                           value="{{ old('leave_name', $leaveType->leave_name) }}" placeholder="e.g. Annual Vacation Leave" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Leave Code <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-barcode"></i></span>
                                    <input type="text" class="form-control form-control-custom font-monospace" id="leave_code" name="leave_code"
                                           value="{{ old('leave_code', $leaveType->leave_code) }}" placeholder="e.g. AL-01, CL-01" style="text-transform: uppercase;" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Policy Description <span class="text-muted fw-normal fs-12">(Optional guidance for employees)</span>
                                </label>
                                <textarea class="form-control form-control-custom" id="description" name="description" rows="2"
                                          placeholder="Explain when this leave can be availed, required notice period, or guidelines...">{{ old('description', $leaveType->description) }}</textarea>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <!-- SECTION 2: QUOTA & ENTITLEMENTS -->
                        <div class="policy-section-header">
                            <i class="ti ti-calculator text-primary"></i> 2. Annual Quota & Rollover Rules
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Annual Quota (Days / Year) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-calendar-stats"></i></span>
                                    <input type="number" min="0" max="365" class="form-control form-control-custom font-monospace" id="total_leaves" name="total_leaves"
                                           value="{{ old('total_leaves', $leaveType->total_leaves) }}" placeholder="12" required>
                                </div>
                                <span class="fs-11 text-muted">Total number of days credited annually.</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Carry Forward to Next Year <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="carry_forward" name="carry_forward" required>
                                    <option value="1" @selected(old('carry_forward', $leaveType->carry_forward) == 1)>Yes (Allow Rollover)</option>
                                    <option value="0" @selected(old('carry_forward', $leaveType->carry_forward) == 0)>No (Lapses at Year End)</option>
                                </select>
                                <span class="fs-11 text-muted">Unused balance rolls over to next calendar year.</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Encashable Balance <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="encashable" name="encashable" required>
                                    <option value="0" @selected(old('encashable', $leaveType->encashable) == 0)>No (Non-encashable)</option>
                                    <option value="1" @selected(old('encashable', $leaveType->encashable) == 1)>Yes (Encashable in Payroll)</option>
                                </select>
                                <span class="fs-11 text-muted">Whether unutilized days can be paid out.</span>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <!-- SECTION 3: APPLICABILITY & CLASSIFICATION -->
                        <div class="policy-section-header">
                            <i class="ti ti-users-group text-primary"></i> 3. Classification & Eligibility
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Applicable For <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="applicable_for" name="applicable_for" required>
                                    <option value="All" @selected(old('applicable_for', $leaveType->applicable_for) === 'All')>All Employees (Universal)</option>
                                    <option value="Male" @selected(old('applicable_for', $leaveType->applicable_for) === 'Male')>Male Staff Only</option>
                                    <option value="Female" @selected(old('applicable_for', $leaveType->applicable_for) === 'Female')>Female Staff Only</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Leave Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="leave_category" name="leave_category" required>
                                    <option value="Paid" @selected(old('leave_category', $leaveType->leave_category) === 'Paid')>Paid (Standard Salary)</option>
                                    <option value="Unpaid" @selected(old('leave_category', $leaveType->leave_category) === 'Unpaid')>Unpaid (Salary Deduction / LOP)</option>
                                    <option value="Special" @selected(old('leave_category', $leaveType->leave_category) === 'Special')>Special / Exceptional</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Policy Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="status" name="status" required>
                                    <option value="Active" @selected(old('status', $leaveType->status) === 'Active')>Active (Available for application)</option>
                                    <option value="Inactive" @selected(old('status', $leaveType->status) === 'Inactive')>Inactive (Archived)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('settings.leave-type') }}" class="btn btn-light px-4 py-2">
                                Cancel
                            </a>
                            <button class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center" type="submit" id="updatePolicyBtn">
                                <i class="ti ti-check me-1.5"></i>Update Leave Policy
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
        <x-footer />
    </div>

@endsection

@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Auto-format leave code to uppercase
            $('#leave_code').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Form validation
            (function() {
                'use strict';
                var forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            $('#updatePolicyBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating Policy...');
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
        });
    </script>
@endpush
