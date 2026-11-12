@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $isEdit = (bool) $client;
    @endphp

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <a href="{{ route('invoice.clients') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list me-2"></i>Client List
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ $isEdit ? route('invoice.clients.update', $client->ClientID) : route('invoice.clients.store') }}" method="POST" id="client-form" class="row g-3 mb-4" enctype="multipart/form-data">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif

                        <div class="col-md-4">
                            <label class="form-label">Client Name</label>
                            <input required type="text" name="client_name" class="form-control" value="{{ old('client_name', $client->ClientName ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Concern Person</label>
                            <input required type="text" name="concern_person" class="form-control" value="{{ old('concern_person', $client->ConcernOwner ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CIN <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="cin" class="form-control" value="{{ old('cin', $client->Fax ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input required type="text" name="website" class="form-control" value="{{ old('website', $client->website ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input required type="email" name="email" class="form-control" value="{{ old('email', $client->EmailAddress ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input required type="tel" name="mobile" class="form-control" value="{{ old('mobile', $client->MobileNo ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Portal Password @if($isEdit)<span class="text-muted">(Optional)</span>@endif</label>
                            <input {{ $isEdit ? '' : 'required' }} type="password" name="password" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm Password @if($isEdit)<span class="text-muted">(Optional)</span>@endif</label>
                            <input {{ $isEdit ? '' : 'required' }} type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="send_login_email" name="send_login_email" value="1" @checked(old('send_login_email'))>
                                <label class="form-check-label" for="send_login_email">Send portal login email</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <select name="country" id="country" class="form-control" required>
                                <option value="">--Select Country--</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->CountryID }}" @selected(old('country', $client->Country ?? '') == $country->CountryID)>{{ $country->CountryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <select name="state" id="state" class="form-control" required>
                                <option value="">--Select State--</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GST Number <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $client->gst_number ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="1" @selected((string) old('status', $client->DelStatus ?? 1) === '1')>Active</option>
                                <option value="0" @selected((string) old('status', $client->DelStatus ?? 1) === '0')>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <textarea required name="address" class="form-control" rows="2">{{ old('address', $client->Address ?? '') }}</textarea>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-2"></i>{{ $isEdit ? 'Update Client' : 'Save Client' }}
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
    <script>
        $(document).ready(function () {
            function fetchStates(countryId, selectedStateId) {
                $('#state').html('<option value="">Loading...</option>');
                if (!countryId) {
                    $('#state').html('<option value="">--Select Country First--</option>');
                    return;
                }
                $.ajax({
                    url: '{{ route('invoice.clients.fetch-states') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        country_id: countryId
                    },
                    dataType: 'json',
                    success: function (states) {
                        $('#state').html('<option value="">--Select State--</option>');
                        if (states.length > 0) {
                            $.each(states, function (index, state) {
                                const selected = (state.StateID == selectedStateId) ? ' selected' : '';
                                $('#state').append('<option value="' + state.StateID + '"' + selected + '>' + state.StateName + '</option>');
                            });
                            if (states.length === 1 && (states[0].StateName || '').toLowerCase() === 'international') {
                                $('#state').val(states[0].StateID);
                            }
                        } else {
                            $('#state').html('<option value="">No states available</option>');
                        }
                    },
                    error: function () {
                        alert('Error fetching states.');
                        $('#state').html('<option value="">--Error--</option>');
                    },
                });
            }

            $('#country').change(function () {
                fetchStates($(this).val(), 0);
            });

            @php
                $selectedCountry = old('country', $client->Country ?? '');
                $selectedState = old('state', $client->StateID ?? 0);
            @endphp
            @if ($selectedCountry)
                fetchStates({{ (int) $selectedCountry }}, {{ (int) $selectedState }});
            @endif
        });
    </script>
@endpush
