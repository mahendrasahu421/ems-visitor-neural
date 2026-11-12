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
                                    <h2 class="mb-1">Branch</h2>
                                    <nav>
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item" aria-current="page">
                                                Master / Organisation / Branch / Create
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                                    <div class="mb-2">
                                        <a href="{{ route('masters.organisation.branch') }}"
                                            class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-circle-plus me-2"></i>Branch List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST"
                                action="{{ route('masters.organisation.branch.store') }}">
                                @csrf
                                <div class="form-row row">
                                    <!-- Company -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="company_id">Company Name *</label>
                                        <select class="form-control select2" id="company_id" name="company_id" required>
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select company name.</div>
                                    </div>

                                    <!-- Branch Name -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="branch_name">Branch Name *</label>
                                        <input type="text" class="form-control" id="branch_name" name="branch_name"
                                            placeholder="Enter Branch Name" required>
                                        <div class="invalid-feedback">Please enter branch name.</div>
                                    </div>

                                    <!-- Branch Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="branch_code">Branch Code *</label>
                                        <input type="text" class="form-control" id="branch_code" name="branch_code"
                                            placeholder="Enter Branch Code" required>
                                        <div class="invalid-feedback">Please enter branch code.</div>
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- Contact Number -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="contact_no">Contact Number *</label>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no"
                                            placeholder="Enter Contact Number" required>
                                        <div class="invalid-feedback">Please enter contact number.</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="email">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter Branch Email" required>
                                        <div class="invalid-feedback">Please enter valid email.</div>
                                    </div>

                                    <!-- GST -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="gst_no">GST Number</label>
                                        <input type="text" class="form-control" id="gst_no" name="gst_no"
                                            placeholder="Enter GST Number">
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- Website -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="website">Website</label>
                                        <input type="text" class="form-control" id="website" name="website"
                                            placeholder="Enter Website URL">
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="address">Address *</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter Address" required></textarea>
                                        <div class="invalid-feedback">Please enter address.</div>
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="country_id">Country *</label>
                                        <select class="form-control select2" id="country_id" name="country_id" required>
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select country.</div>
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- State -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="state_id">State *</label>
                                        <select class="form-control select2" id="state_id" name="state_id" required>
                                            <option value="">Select State</option>
                                        </select>
                                        <div class="invalid-feedback">Please select state.</div>
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="city_id">City *</label>
                                        <select class="form-control select2" id="city_id" name="city_id" required>
                                            <option value="">Select City</option>
                                        </select>
                                        <div class="invalid-feedback">Please select city.</div>
                                    </div>

                                    <!-- Pin Code -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="pincode">Pin Code *</label>
                                        <input type="text" class="form-control" id="pincode" name="pincode"
                                            placeholder="Enter Pin Code" required>
                                        <div class="invalid-feedback">Please enter pincode.</div>
                                    </div>
                                </div>

                                <div class="form-row row">
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
                                    <a href="{{ route('masters.organisation.branch') }}" class="btn btn-light me-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="reset" class="btn btn-secondary me-2">
                                        <i class="ti ti-x me-1"></i> Cancel
                                    </button>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-device-floppy me-1"></i> Save Branch
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#company_id, #country_id, #state_id, #city_id').each(function() {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy');
                }
                $(this).select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    width: '100%'
                });
            });

            // Country -> State AJAX
            $('#country_id').on('change', function() {
                let countryId = $(this).val();
                $('#state_id').empty().append('<option value="">Loading...</option>').trigger('change');
                $('#city_id').empty().append('<option value="">Select City</option>').trigger('change');

                if (countryId) {
                    $.getJSON("{{ url('/get-state') }}/" + countryId, function(states) {
                        $('#state_id').empty().append('<option value="">Select State</option>');
                        $.each(states, function(i, state) {
                            $('#state_id').append('<option value="' + state.id + '">' +
                                state.name + '</option>');
                        });
                        $('#state_id').trigger('change');
                    });
                } else {
                    $('#state_id').empty().append('<option value="">Select State</option>').trigger(
                        'change');
                }
            });

            // State -> City AJAX
            $('#state_id').on('change', function() {
                let stateId = $(this).val();
                $('#city_id').empty().append('<option value="">Loading...</option>').trigger('change');

                if (stateId) {
                    $.getJSON("{{ url('/get-city') }}/" + stateId, function(cities) {
                        $('#city_id').empty().append('<option value="">Select City</option>');
                        $.each(cities, function(i, city) {
                            $('#city_id').append('<option value="' + city.id + '">' + city
                                .name + '</option>');
                        });
                        $('#city_id').trigger('change');
                    });
                } else {
                    $('#city_id').empty().append('<option value="">Select City</option>').trigger('change');
                }
            });

        });
    </script>
@endpush
