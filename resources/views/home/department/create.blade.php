@extends('layout.master')

@section('title', $title)

@section('main-section')
 <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- Breadcrumb -->
                            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                                <div class="my-auto mb-2">
                                    <h2 class="mb-1">Create Department</h2>
                                    <nav>
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item" aria-current="page">
                                                Master / Organisation / Department / Create
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('settings.department') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-list me-2"></i> Department List
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Breadcrumb -->
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.department.store') }}">
                                @csrf

                                <div class="row">
                                    <!-- Select Company -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="company_id">Select Company *</label>
                                        <select class="form-control" id="company_id" name="company_id" required>
                                            <option value="">Select Company</option>
                                            <option value="1">Nerual Info Solution Pvt Ltd</option>
                                        </select>
                                        @error('company_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Select Company -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="category_id">Select Category *</label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            @foreach ($category as $categorys)
                                                <option value="{{ $categorys->id }}">{{ $categorys->name }}</option>
                                            @endforeach

                                        </select>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Department Name -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="department_name">Department Name *</label>
                                        <input type="text" class="form-control" id="department_name"
                                            name="department_name" placeholder="Enter Department Name"
                                            value="{{ old('department_name') }}" required>
                                        @error('department_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Department Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="department_code">Department Code *</label>
                                        <input type="text" class="form-control" id="department_code"
                                            name="department_code" placeholder="Enter Department Code"
                                            value="{{ old('department_code') }}" required>
                                        @error('department_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Department Head -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="department_head">Department Head</label>
                                        <input type="text" class="form-control" id="department_head"
                                            name="department_head" placeholder="Enter Department Head"
                                            value="{{ old('department_head') }}">
                                        @error('department_head')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="status">Status *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="Active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="Inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('settings.department') }}" class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Save Department
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
    <!-- /Page Wrapper -->
@endsection
