@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                            <div class="my-auto mb-2">
                                <h2 class="mb-1">{{ $title }}</h2>
                            </div>
                            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                <div class="mb-2">
                                    <a href="{{ route('masters.organisation.leave-type') }}"
                                        class="btn btn-primary d-flex align-items-center">
                                        <i class="ti ti-list-details me-2"></i> Leave Type List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="needs-validation" novalidate method="POST"
                            action="{{ route('masters.organisation.leave-type.update', $leaveType->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Company -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="company_id">Company Name *</label>
                                    <select class="form-control" id="company_id" name="company_id" required>
                                        <option value="">Select Company</option>
                                        <option value="1" {{ $leaveType->company_id == 1 ? 'selected' : '' }}>Dreams Technologies Pvt. Ltd.</option>
                                        <option value="2" {{ $leaveType->company_id == 2 ? 'selected' : '' }}>TechVision Solutions</option>
                                        <option value="3" {{ $leaveType->company_id == 3 ? 'selected' : '' }}>NextGen Innovations</option>
                                    </select>
                                    <div class="invalid-feedback">Please select company name.</div>
                                </div>

                                <!-- Leave Type Name -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="leave_name">Leave Type Name *</label>
                                    <input type="text" class="form-control" id="leave_name" name="leave_name"
                                        value="{{ old('leave_name', $leaveType->leave_name) }}" placeholder="Enter Leave Type Name" required>
                                    <div class="invalid-feedback">Please enter leave type name.</div>
                                </div>

                                <!-- Leave Code -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="leave_code">Leave Code *</label>
                                    <input type="text" class="form-control" id="leave_code" name="leave_code"
                                        value="{{ old('leave_code', $leaveType->leave_code) }}" placeholder="Enter Leave Code" required>
                                    <div class="invalid-feedback">Please enter leave code.</div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Total Leaves per Year -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="total_leaves">Total Leaves / Year *</label>
                                    <input type="number" class="form-control" id="total_leaves" name="total_leaves"
                                        value="{{ old('total_leaves', $leaveType->total_leaves) }}" placeholder="Enter Total Leaves" required>
                                    <div class="invalid-feedback">Please enter total leaves.</div>
                                </div>

                                <!-- Carry Forward -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="carry_forward">Carry Forward *</label>
                                    <select class="form-control" id="carry_forward" name="carry_forward" required>
                                        <option value="1" {{ old('carry_forward', $leaveType->carry_forward) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('carry_forward', $leaveType->carry_forward) == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                    <div class="invalid-feedback">Please select carry forward option.</div>
                                </div>

                                <!-- Encashable -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="encashable">Encashable *</label>
                                    <select class="form-control" id="encashable" name="encashable" required>
                                        <option value="1" {{ old('encashable', $leaveType->encashable) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('encashable', $leaveType->encashable) == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                    <div class="invalid-feedback">Please select encashable option.</div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Applicable For -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="applicable_for">Applicable For *</label>
                                    <select class="form-control" id="applicable_for" name="applicable_for" required>
                                        <option value="">Select Option</option>
                                        <option value="Male" {{ old('applicable_for', $leaveType->applicable_for) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('applicable_for', $leaveType->applicable_for) == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="All" {{ old('applicable_for', $leaveType->applicable_for) == 'All' ? 'selected' : '' }}>All</option>
                                    </select>
                                    <div class="invalid-feedback">Please select applicable option.</div>
                                </div>

                                <!-- Leave Category -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="leave_category">Leave Category *</label>
                                    <select class="form-control" id="leave_category" name="leave_category" required>
                                        <option value="">Select Category</option>
                                        <option value="Paid" {{ old('leave_category', $leaveType->leave_category) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="Unpaid" {{ old('leave_category', $leaveType->leave_category) == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="Special" {{ old('leave_category', $leaveType->leave_category) == 'Special' ? 'selected' : '' }}>Special</option>
                                    </select>
                                    <div class="invalid-feedback">Please select leave category.</div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="status">Status *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="Active" {{ old('status', $leaveType->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $leaveType->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback">Please select status.</div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Enter Description (optional)">{{ old('description', $leaveType->description) }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end mt-3">
                                <a href="{{ route('masters.organisation.leave-type') }}" class="btn btn-light me-2">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </a>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                                <button class="btn btn-primary" type="submit">
                                    <i class="ti ti-device-floppy me-1"></i> Update Leave Type
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <x-footer />
</div>

@endsection
