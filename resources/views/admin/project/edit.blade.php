@extends('layout.master')
@section('title', $title)

@section('main-section')
    <style>
        .custom-select-wrapper {
            position: relative;
        }

        .custom-select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 2rem;
        }

        .dropdown-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
            pointer-events: none;
        }

        .custom-select-wrapper:focus-within .dropdown-icon {
            transform: translateY(-50%) rotate(180deg);
        }
    </style>

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
                                    <nav>
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item" aria-current="page">
                                                Projects / Edit
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('projects.index') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-circle-plus me-2"></i>Project List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST"
                                action="{{ route('projects.update', $project->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="form-row row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Client Name *</label>
                                        <input type="text" class="form-control" name="client_name"
                                            value="{{ $project->client_name }}" required>
                                        <div class="invalid-feedback">Please enter client name.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Project Category *</label>
                                        <input type="text" class="form-control" name="project_category"
                                            value="{{ $project->project_category }}" required>
                                        <div class="invalid-feedback">Please enter project category.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Project Name *</label>
                                        <input type="text" class="form-control" name="project_name"
                                            value="{{ $project->project_name }}" required>
                                        <div class="invalid-feedback">Please enter project name.</div>
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <div class="col-md-4 mb-3 position-relative">
                                        <label class="form-label">Status *</label>
                                        <div class="custom-select-wrapper">
                                            <select class="form-control" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive"
                                                    {{ $project->status == 'inactive' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            <i class="ti ti-chevron-down dropdown-icon"></i>
                                        </div>
                                        <div class="invalid-feedback">Please select status.</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('projects.index') }}" class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>

                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Reset
                                    </button>

                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Update Project
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
    <script>
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endpush
