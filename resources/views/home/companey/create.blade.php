@extends('layout.master')
@section('title', $title)
@section('main-section')
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
                                    <h2 class="mb-1">Company</h2>
                                    <nav>
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item" aria-current="page">
                                                Master / Organisation / Company / Create
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('masters.organisation.company') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-circle-plus me-2"></i>Company List
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Breadcrumb -->
                        </div>

                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST"
                                action="{{ route('masters.organisation.company.store') }}">
                                @csrf
                                <div class="form-row row">
                                    <!-- Company Name -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="company_name">Company Name *</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                            placeholder="Enter Company Name" required>
                                        <div class="invalid-feedback">Please enter company name.</div>
                                    </div>

                                    <!-- Company Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="company_code">Company Code *</label>
                                        <input type="text" class="form-control" id="company_code" name="company_code"
                                            placeholder="Enter Company Code" required>
                                        <div class="invalid-feedback">Please enter company code.</div>
                                    </div>

                                    <!-- Contact Number -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="contact_no">Contact Number *</label>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no"
                                            placeholder="Enter Contact Number" required>
                                        <div class="invalid-feedback">Please enter contact number.</div>
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- Email -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="email">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter Company Email" required>
                                        <div class="invalid-feedback">Please enter valid email.</div>
                                    </div>

                                    <!-- GST Number -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="gst_no">GST Number</label>
                                        <input type="text" class="form-control" id="gst_no" name="gst_no"
                                            placeholder="Enter GST Number">
                                    </div>

                                    <!-- Website -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="website">Website</label>
                                        <input type="text" class="form-control" id="website" name="website"
                                            placeholder="Enter Website route">
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- Address -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="address">Address *</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter Address" required></textarea>
                                        <div class="invalid-feedback">Please enter address.</div>
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label" for="country">Country *</label>
                                        <input type="text" class="form-control" id="country" name="country"
                                            placeholder="Enter Country" required>
                                        <div class="invalid-feedback">Please enter country.</div>
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label" for="state">State *</label>
                                        <input type="text" class="form-control" id="state" name="state"
                                            placeholder="Enter State" required>
                                        <div class="invalid-feedback">Please enter state.</div>
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- City -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="city">City *</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            placeholder="Enter City" required>
                                        <div class="invalid-feedback">Please enter city.</div>
                                    </div>

                                    <!-- Pin Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="pincode">Pin Code *</label>
                                        <input type="text" class="form-control" id="pincode" name="pincode"
                                            placeholder="Enter Pin Code" required>
                                        <div class="invalid-feedback">Please enter pincode.</div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="status">Status *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback">Please select status.</div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('masters.organisation.company') }}"
                                        class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Save Company
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
