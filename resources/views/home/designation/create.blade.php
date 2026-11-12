@extends('layout.master')
@section('title', $title)
@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />
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
                                        <a href="{{ route(name: 'settings.designation') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-list-details me-2"></i> Designation List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST"
                                action="{{ route('settings.designation.store') }}">
                                @csrf

                                <div class="form-row row">
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


                                    <!-- Category Name -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="category_id">Category *</label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="">Select category</option>
                                            @foreach ($categorys as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Department Name -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="department_id">Department *</label>
                                        <select id="department_id" class="form-control" name="department_id">
                                            <option value="">Select Department</option>
                                        </select>
                                        @error('department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Designation Name -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="designation_name">Designation Name *</label>
                                        <input placeholder="Designation Name" id="designation_name" class="form-control"
                                            name="name">

                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Designation Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="designation_code">Designation Code *</label>
                                        <input type="text" class="form-control" id="designation_code"
                                            name="designation_code" value="{{ old('designation_code') }}"
                                            placeholder="Enter Designation Code" required>
                                        @error('designation_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- kpi_weightage Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="kpi_weightage">KPI Weightage *</label>
                                        <input type="text" class="form-control" id="kpi_weightage" name="kpi_weightage"
                                            value="{{ old('kpi_weightage') }}" placeholder="Enter KPI Weightage " required>
                                        @error('kpi_weightage')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Designation Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="competency_weight">Competency Weight *</label>
                                        <input type="text" class="form-control" id="competency_weight"
                                            name="competency_weight" value="{{ old('competency_weight') }}"
                                            placeholder="Enter Competency Weight" required>
                                        @error('competency_weight')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="status">Status *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="Active" selected>Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>




                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('settings.designation') }}" class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Save Designation
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
@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#category_id').change(function() {
                let categoryId = $(this).val();

                if (categoryId) {
                    $.ajax({
                        url: '/get-departments/' + categoryId,
                        type: 'GET',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append('<option value="' + value
                                    .id + '">' + value.department_name + '</option>'
                                );
                            });
                        },
                        error: function() {
                            alert('Error fetching departments.');
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            });
          
        });
    </script>
@endpush
