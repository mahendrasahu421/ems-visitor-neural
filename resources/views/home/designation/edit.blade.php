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
                                    <a href="{{ route('masters.organisation.designation') }}"
                                        class="btn btn-primary d-flex align-items-center">
                                        <i class="ti ti-list-details me-2"></i> Designation List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="needs-validation" novalidate method="POST"
                            action="{{ route('masters.organisation.designation.update', $designation->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-row row">
                                <!-- Select Company -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="company_id">Select Company *</label>
                                    <select class="form-control" id="company_id" name="company_id" required>
                                        <option value="1" {{ $designation->company_id == 1 ? 'selected' : '' }}>
                                            Nerual Info Solution Pvt Ltd
                                        </option>
                                    </select>
                                    @error('company_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Department Name -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="department_id">Department *</label>
                                    <select class="form-control" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        <option value="1" {{ $designation->department_id == 1 ? 'selected' : '' }}>Human Resources</option>
                                        <option value="2" {{ $designation->department_id == 2 ? 'selected' : '' }}>Finance</option>
                                        <option value="3" {{ $designation->department_id == 3 ? 'selected' : '' }}>Development</option>
                                        <option value="4" {{ $designation->department_id == 4 ? 'selected' : '' }}>Marketing</option>
                                    </select>
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Designation Name -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="designation_name">Designation Name *</label>
                                    <input type="text" class="form-control" id="designation_name" name="designation_name"
                                        value="{{ old('designation_name', $designation->designation_name) }}"
                                        placeholder="Enter Designation Name" required>
                                    @error('designation_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row row">
                                <!-- Designation Code -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="designation_code">Designation Code *</label>
                                    <input type="text" class="form-control" id="designation_code" name="designation_code"
                                        value="{{ old('designation_code', $designation->designation_code) }}"
                                        placeholder="Enter Designation Code" required>
                                    @error('designation_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="status">Status *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="1" {{ $designation->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $designation->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-row row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Enter Description (optional)">{{ old('description', $designation->description) }}</textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end mt-3">
                                <a href="{{ route('masters.organisation.designation') }}" class="btn btn-light me-2">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </a>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                                <button class="btn btn-primary" type="submit">
                                    <i class="ti ti-device-floppy me-1"></i> Update Designation
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
