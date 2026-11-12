@extends('layout.master')
@section('title', $title)

@section('main-section')

    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">

                        <div class="card-header">
                            <!-- Breadcrumb -->
                            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                                <div class="my-auto mb-2">
                                    <h2 class="mb-1">Create Job</h2>
                                    <nav>
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item" aria-current="page">
                                                Recruitment / Job / Create
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('recruitment.jobs') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-list me-2"></i> Job List
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Breadcrumb -->
                        </div>
                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST" action="#">
                                @csrf

                                <div class="row">
                                    <!-- Job Title -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Job Title *</label>
                                        <input type="text" class="form-control" name="job_title"
                                            placeholder="Enter Job Title" required>
                                        <div class="invalid-feedback">Please enter job title.</div>
                                    </div>

                                    <!-- Functional Area -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Functional Area *</label>
                                        <select class="form-control" name="functional_area" required>
                                            <option value="">Select Functional Area</option>
                                            <option value="IT">IT</option>
                                            <option value="HR">Human Resource</option>
                                            <option value="Marketing">Marketing</option>
                                        </select>
                                        <div class="invalid-feedback">Please select functional area.</div>
                                    </div>

                                    <!-- Test Skills -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Test Skills *</label>
                                        <input type="text" class="form-control" name="test_skills"
                                            placeholder="Enter Test Skills" required>
                                        <div class="invalid-feedback">Please enter test skills.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- No of Positions -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">No of Position *</label>
                                        <input type="number" class="form-control" name="positions"
                                            placeholder="Enter Number of Positions" required>
                                        <div class="invalid-feedback">Please enter no. of positions.</div>
                                    </div>

                                    <!-- Job Type -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Job Type *</label>
                                        <select class="form-control" name="job_type" required>
                                            <option value="">Select Job Type</option>
                                            <option value="Full-Time">Full-Time</option>
                                            <option value="Part-Time">Part-Time</option>
                                            <option value="Internship">Internship</option>
                                        </select>
                                        <div class="invalid-feedback">Please select job type.</div>
                                    </div>

                                    <!-- CTC From -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">CTC From (Lac/Year)</label>
                                        <input type="number" step="0.1" class="form-control" name="ctc_from"
                                            placeholder="Enter CTC From">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- CTC To -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">CTC To (Lac/Year)</label>
                                        <input type="number" step="0.1" class="form-control" name="ctc_to"
                                            placeholder="Enter CTC To">
                                    </div>

                                    <!-- Min Experience -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Min Experience *</label>
                                        <select class="form-control" name="min_exp" required>
                                            <option value="">Select Min Experience</option>
                                            @for ($i = 0; $i <= 20; $i++)
                                                <option value="{{ $i }}">{{ $i }} Years</option>
                                            @endfor
                                        </select>
                                        <div class="invalid-feedback">Please select minimum experience.</div>
                                    </div>

                                    <!-- Max Experience -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Max Experience *</label>
                                        <select class="form-control" name="max_exp" required>
                                            <option value="">Select Max Experience</option>
                                            @for ($i = 0; $i <= 20; $i++)
                                                <option value="{{ $i }}">{{ $i }} Years</option>
                                            @endfor
                                        </select>
                                        <div class="invalid-feedback">Please select maximum experience.</div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Select Locations -->
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Select Locations *</label>
                                        <select class="form-control" name="location" required>
                                            <option value="">Select Locations</option>
                                            <option value="Delhi">Delhi</option>
                                            <option value="Mumbai">Mumbai</option>
                                            <option value="Bangalore">Bangalore</option>
                                            <option value="Hyderabad">Hyderabad</option>
                                            <option value="Chennai">Chennai</option>
                                            <option value="Pune">Pune</option>
                                        </select>
                                        <div class="invalid-feedback">Please select job type.</div>
                                    </div>
                                </div>



                                <!-- Job Description -->
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Job Description *</label>
                                        <textarea id="job_description" class="form-control" name="job_description" rows="4" required
                                            placeholder="Enter Job Description"></textarea>

                                        <div class="invalid-feedback">Please enter job description.</div>
                                    </div>
                                </div>

                                <!-- Keywords -->
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Keywords</label>
                                        <input type="text" class="form-control" name="keywords"
                                            placeholder="Enter Keywords">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('recruitment.jobs') }}" class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Save Job
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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create(document.querySelector('#job_description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
