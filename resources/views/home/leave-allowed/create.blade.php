@extends('layout.master')
@section('title', $title ?? 'Assign Leave Policy')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .leave-alloc-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .alloc-section-header {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.01em;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }

        .alloc-section-header i {
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

        .policy-info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
        }
    </style>

    <div class="page-wrapper leave-alloc-create-page">
        <div class="content container-fluid">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-link-plus fs-20"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Assign Leave Allocation' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('settings.leave-allow') }}" class="text-muted">Leave Allocation</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Assign</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('settings.leave-allow') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1.5"></i>Back to Allocations
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
            <div class="card leave-alloc-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Leave Allocation Assignment</h5>
                        <p class="text-muted fs-12 mb-0 mt-0.5">Map a specific leave policy with allowed day limits to an employee job designation.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-11 font-monospace">
                        <i class="ti ti-shield-check me-1"></i>Quota Assignment
                    </span>
                </div>

                <div class="card-body p-4">
                    <form class="needs-validation" novalidate method="POST" action="{{ route('settings.leave.mapping.store') }}" id="createAllocForm">
                        @csrf

                        <!-- SECTION 1: TARGET DESIGNATION & LEAVE POLICY -->
                        <div class="alloc-section-header">
                            <i class="ti ti-user-star text-primary"></i> 1. Job Role & Leave Policy Selection
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Target Designation / Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="designation_id" name="designation_id" required>
                                    <option value="">-- Choose Designation --</option>
                                    @foreach ($designations as $desig)
                                        <option value="{{ $desig->id }}" @selected(old('designation_id') == $desig->id)>
                                            {{ $desig->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Leave Policy / Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="leave_type_id" name="leave_type_id" required>
                                    <option value="">-- Choose Leave Policy --</option>
                                    @foreach ($leaves as $lt)
                                        <option value="{{ $lt->id }}" 
                                                data-total="{{ $lt->total_leaves }}"
                                                data-code="{{ $lt->leave_code }}"
                                                data-category="{{ $lt->leave_category }}"
                                                @selected(old('leave_type_id') == $lt->id)>
                                            {{ $lt->leave_name }} ({{ $lt->leave_code }}) &bull; {{ $lt->total_leaves }} Days Max
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <!-- SECTION 2: QUOTA ALLOCATION -->
                        <div class="alloc-section-header">
                            <i class="ti ti-calendar-stats text-primary"></i> 2. Day Entitlement & Status
                        </div>

                        <div class="row g-3 align-items-start">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Policy Base Max Quota
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="text" readonly class="form-control form-control-custom bg-light font-monospace fw-bold" 
                                           id="total_leaves" placeholder="Select Leave Type" value="{{ old('total_leaves') }}">
                                </div>
                                <span class="fs-11 text-muted">Annual ceiling defined in policy.</span>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Allowed Days for this Role <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-check"></i></span>
                                    <input type="number" min="1" class="form-control form-control-custom font-monospace fw-bold" 
                                           placeholder="e.g. 12" name="allow_days" id="allow_days" value="{{ old('allow_days') }}" required>
                                </div>
                                <small id="allow_days_msg" class="text-danger fs-11 mt-1 d-none">
                                    <i class="ti ti-alert-triangle me-1"></i>Allowed days cannot exceed policy max!
                                </small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Allocation Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="status" name="status" required>
                                    <option value="Active" @selected(old('status', 'Active') === 'Active')>Active (In Operation)</option>
                                    <option value="Inactive" @selected(old('status') === 'Inactive')>Inactive (Suspended)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Policy Info Summary Box -->
                        <div class="mt-4 policy-info-box d-none" id="policySummaryBox">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                        <i class="ti ti-info-circle text-primary me-1.5 fs-18"></i>Selected Policy Overview
                                    </h6>
                                    <p class="text-muted fs-12 mb-0 mt-0.5" id="summaryPolicyText">Leave policy details will appear here.</p>
                                </div>
                                <div class="d-flex gap-1.5" id="policyBadges"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('settings.leave-allow') }}" class="btn btn-light px-4 py-2">
                                Cancel
                            </a>
                            <button class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center" type="submit" id="saveAllocBtn">
                                <i class="ti ti-check me-1.5"></i>Save Allocation
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

            function syncPolicyInfo() {
                const $selected = $('#leave_type_id option:selected');
                const leaveTypeId = $selected.val();

                if (leaveTypeId) {
                    const totalDays = $selected.data('total') || 0;
                    const code = $selected.data('code') || '';
                    const category = $selected.data('category') || 'General';

                    $('#total_leaves').val(totalDays + ' Days');
                    $('#allow_days').attr('max', totalDays);
                    if (!$('#allow_days').val()) {
                        $('#allow_days').val(totalDays);
                    }

                    $('#summaryPolicyText').text(`${$selected.text()}`);
                    $('#policyBadges').html(`
                        <span class="badge bg-primary-subtle text-primary border font-monospace">${code}</span>
                        <span class="badge bg-success-subtle text-success border">${category}</span>
                    `);
                    $('#policySummaryBox').removeClass('d-none');
                } else {
                    $('#total_leaves').val('');
                    $('#allow_days').removeAttr('max');
                    $('#policySummaryBox').addClass('d-none');
                }
            }

            $('#leave_type_id').on('change', syncPolicyInfo);
            if ($('#leave_type_id').val()) {
                syncPolicyInfo();
            }

            $('#allow_days').on('input', function() {
                const max = parseInt($(this).attr('max'));
                const val = parseInt($(this).val());

                if (max && val > max) {
                    $('#allow_days_msg').removeClass('d-none');
                    $(this).val(max);
                } else {
                    $('#allow_days_msg').addClass('d-none');
                }
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
                            $('#saveAllocBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
        });
    </script>
@endpush
