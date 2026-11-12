@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                            <div class="my-auto mb-2">
                                <h2 class="mb-1">{{ $title }}</h2>
                            </div>
                            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                <div class="mb-2">
                                    <a href="{{ route('masters.organisation.policy') }}"
                                        class="btn btn-primary d-flex align-items-center">
                                        <i class="ti ti-list-details me-2"></i> Policy List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <form class="needs-validation" novalidate method="POST"
                            action="{{ route('masters.organisation.policy.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <!-- Policy Title -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Policy Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="policy_title"
                                        value="Work From Home Policy" placeholder="Enter Policy Title" required>
                                    <div class="invalid-feedback">Please enter policy title.</div>
                                </div>

                                <!-- Policy Code -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Policy Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="policy_code"
                                        value="POL-002" placeholder="Enter Policy Code" required>
                                    <div class="invalid-feedback">Please enter policy code.</div>
                                </div>

                                <!-- Department -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-control" name="department_id" required>
                                        <option value="">Select Department</option>
                                        <option value="1" selected>Human Resources</option>
                                        <option value="2">Finance</option>
                                        <option value="3">IT Department</option>
                                        <option value="4">Marketing</option>
                                    </select>
                                    <div class="invalid-feedback">Please select department.</div>
                                </div>

                                <!-- Effective From -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Effective From <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="effective_from" value="2025-02-15" required>
                                    <div class="invalid-feedback">Please select effective date.</div>
                                </div>

                                <!-- Expiry Date -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" name="expiry_date" value="2026-02-14">
                                </div>

                                <!-- Upload Policy Document -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Upload Policy Document (PDF)</label>
                                    <input type="file" class="form-control" name="policy_file" accept=".pdf">
                                </div>

                                <!-- Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="invalid-feedback">Please select status.</div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="4"
                                        placeholder="Enter policy details">This policy allows employees to work remotely for up to 3 days a week, ensuring productivity and work-life balance.</textarea>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <a href="{{ route('masters.organisation.policy') }}" class="btn btn-light me-2">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </a>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i> Save Policy
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
