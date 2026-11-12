@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $isEdit = (bool) $company;
        $field = fn (string $name, string $column = null) => old($name, $company->{$column ?? $name} ?? '');
    @endphp

    <style>
        .cm-section {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px 20px 5px;
            margin-bottom: 20px;
            background: #fbfbfc;
        }

        .cm-section h6 {
            font-weight: 700;
            color: #17a2b8;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .card .card-header {
            background: #ffffff;
            border-bottom: 2px solid #17a2b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        label.form-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 4px;
        }

        .form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
        }

        .invalid-feedback {
            font-size: 12px;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">
            <div class="container mt-15">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Company Master</h3>
                        <div>
                            <a href="{{ route('invoice.companies') }}" class="btn btn-outline-secondary btn-sm">Back To List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div id="companyForm" class="mb-3">
                            <h4 class="mb-1">{{ $isEdit ? 'Edit Company' : 'Add New Company' }}</h4>
                            <p class="text-muted mb-0">Maintain the company details used on invoices.</p>
                        </div>

                        <form
                            action="{{ $isEdit ? route('invoice.companies.update', $company->CompanyID) : route('invoice.companies.store') }}"
                            method="post"
                            class="row g-3 mb-4 needs-validation"
                            enctype="multipart/form-data"
                            novalidate
                        >
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif
                            <input type="hidden" name="company_id" value="{{ (int) ($company->CompanyID ?? 0) }}">

                            <div class="cm-section col-12">
                                <h6>Basic Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" name="company_name" class="form-control"
                                            placeholder="e.g. Neural Info Pvt Ltd" required maxlength="150"
                                            value="{{ $field('company_name', 'CompanyName') }}">
                                        <div class="invalid-feedback">Company name is required.</div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Code</label>
                                        <input type="text" name="company_code" class="form-control" placeholder="e.g. NI001"
                                            maxlength="20" value="{{ $field('company_code', 'CompanyCode') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Invoice Logo</label>
                                        <input type="file" name="company_logo" class="form-control"
                                            accept="image/png,image/jpeg,image/jpg,image/webp">
                                        @if (!empty($company->CompanyLogo))
                                            <small class="text-muted d-block mt-1">Current: {{ $company->CompanyLogo }}</small>
                                        @endif
                                        <div class="invalid-feedback">Only PNG, JPG, JPEG, WEBP allowed.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            @php($selectedStatus = (string) old('status', (int) ($company->DelStatus ?? 1) === 1 ? 'Active' : 'Inactive'))
                                            <option value="Active" {{ $selectedStatus === 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ $selectedStatus === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <div class="invalid-feedback">Please select status.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="e.g. accounts@company.com"
                                            value="{{ $field('email', 'EmailAddress') }}">
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Mobile</label>
                                        <input type="tel" name="mobile" class="form-control" placeholder="e.g. 9876543210"
                                            pattern="[0-9]{10,15}" inputmode="numeric" maxlength="15"
                                            value="{{ $field('mobile', 'MobileNo') }}">
                                        <div class="invalid-feedback">Enter a valid mobile number (10-15 digits).</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" placeholder="e.g. 011-23456789"
                                            maxlength="20" value="{{ $field('phone', 'PhoneNo') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Website</label>
                                        <input type="url" name="website" class="form-control"
                                            placeholder="e.g. https://www.company.com"
                                            value="{{ $field('website', 'Website') }}">
                                        <div class="invalid-feedback">Please enter a valid URL.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">CIN / Registration No</label>
                                        <input type="text" name="cin" class="form-control text-uppercase"
                                            placeholder="e.g. L12345MH2001PLC123456" pattern="[A-Za-z0-9]{10,25}"
                                            maxlength="25" value="{{ $field('cin', 'RegistrationNo') }}">
                                        <div class="invalid-feedback">10-25 alphanumeric characters only.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">PAN No</label>
                                        <input type="text" name="pan" class="form-control text-uppercase"
                                            placeholder="e.g. ABCDE1234F" pattern="[A-Za-z0-9]{10,15}" maxlength="15"
                                            value="{{ $field('pan', 'PanNo') }}">
                                        <div class="invalid-feedback">10-15 alphanumeric characters only.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">GST Number</label>
                                        <input type="text" name="gst_number" class="form-control text-uppercase"
                                            placeholder="e.g. 07ABCDE1234F1Z5" pattern="[A-Za-z0-9]{10,25}" maxlength="25"
                                            value="{{ $field('gst_number', 'GSTNumber') }}">
                                        <div class="invalid-feedback">10-25 alphanumeric characters only.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">IEC No</label>
                                        <input type="text" name="iec_no" class="form-control text-uppercase"
                                            placeholder="e.g. AACCN4385L" pattern="[A-Za-z0-9]{5,30}" maxlength="30"
                                            value="{{ $field('iec_no', 'IECNo') }}">
                                        <div class="invalid-feedback">5-30 alphanumeric characters only.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">LUT No</label>
                                        <input type="text" name="lut_no" class="form-control text-uppercase"
                                            placeholder="e.g. AD0909210147432" pattern="[A-Za-z0-9]{5,50}" maxlength="50"
                                            value="{{ $field('lut_no', 'LUTNo') }}">
                                        <div class="invalid-feedback">5-50 alphanumeric characters only.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">TAN</label>
                                        <input type="text" name="tan_no" class="form-control text-uppercase"
                                            placeholder="e.g. KNPN01596A" pattern="[A-Za-z0-9]{5,30}" maxlength="30"
                                            value="{{ $field('tan_no', 'TANNo') }}">
                                        <div class="invalid-feedback">5-30 alphanumeric characters only.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control" rows="2"
                                            placeholder="Registered office address">{{ $field('address', 'Address') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="cm-section col-12">
                                <h6>Bank Account 1</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">A/c Name</label>
                                        <input type="text" name="bank1_account_name" class="form-control"
                                            placeholder="Account holder name"
                                            value="{{ $field('bank1_account_name', 'Bank1AccountName') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Account No</label>
                                        <input type="text" name="bank1_account_no" class="form-control"
                                            placeholder="e.g. 123456789012" inputmode="numeric"
                                            value="{{ $field('bank1_account_no', 'Bank1AccountNo') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" name="bank1_bank_name" class="form-control"
                                            placeholder="e.g. State Bank of India"
                                            value="{{ $field('bank1_bank_name', 'Bank1BankName') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">IFSC</label>
                                        <input type="text" name="bank1_ifsc" class="form-control text-uppercase"
                                            placeholder="e.g. SBIN0001234" pattern="[A-Za-z]{4}0[A-Za-z0-9]{6}"
                                            maxlength="11" value="{{ $field('bank1_ifsc', 'Bank1IFSC') }}">
                                        <div class="invalid-feedback">Format: 4 letters + 0 + 6 alphanumeric.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">MICR</label>
                                        <input type="text" name="bank1_micr" class="form-control" placeholder="e.g. 110002345"
                                            maxlength="9" value="{{ $field('bank1_micr', 'Bank1MICR') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Swift Code</label>
                                        <input type="text" name="bank1_swift" class="form-control text-uppercase"
                                            placeholder="e.g. SBININBBXXX" maxlength="11"
                                            value="{{ $field('bank1_swift', 'Bank1SwiftCode') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Branch Code</label>
                                        <input type="text" name="bank1_branch_code" class="form-control" placeholder="e.g. 001234"
                                            value="{{ $field('bank1_branch_code', 'Bank1BranchCode') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Branch</label>
                                        <input type="text" name="bank1_branch" class="form-control"
                                            placeholder="e.g. Connaught Place, New Delhi"
                                            value="{{ $field('bank1_branch', 'Bank1Branch') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="cm-section col-12">
                                <h6>Bank Account 2 (Optional)</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">A/c Name</label>
                                        <input type="text" name="bank2_account_name" class="form-control"
                                            placeholder="Account holder name"
                                            value="{{ $field('bank2_account_name', 'Bank2AccountName') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Account No</label>
                                        <input type="text" name="bank2_account_no" class="form-control"
                                            placeholder="e.g. 123456789012" inputmode="numeric"
                                            value="{{ $field('bank2_account_no', 'Bank2AccountNo') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" name="bank2_bank_name" class="form-control" placeholder="e.g. HDFC Bank"
                                            value="{{ $field('bank2_bank_name', 'Bank2BankName') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">IFSC</label>
                                        <input type="text" name="bank2_ifsc" class="form-control text-uppercase"
                                            placeholder="e.g. HDFC0001234" pattern="[A-Za-z]{4}0[A-Za-z0-9]{6}"
                                            maxlength="11" value="{{ $field('bank2_ifsc', 'Bank2IFSC') }}">
                                        <div class="invalid-feedback">Format: 4 letters + 0 + 6 alphanumeric.</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">MICR</label>
                                        <input type="text" name="bank2_micr" class="form-control" placeholder="e.g. 110002345"
                                            maxlength="9" value="{{ $field('bank2_micr', 'Bank2MICR') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Swift Code</label>
                                        <input type="text" name="bank2_swift" class="form-control text-uppercase"
                                            placeholder="e.g. HDFCINBBXXX" maxlength="11"
                                            value="{{ $field('bank2_swift', 'Bank2SwiftCode') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Branch Code</label>
                                        <input type="text" name="bank2_branch_code" class="form-control" placeholder="e.g. 001234"
                                            value="{{ $field('bank2_branch_code', 'Bank2BranchCode') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Branch</label>
                                        <input type="text" name="bank2_branch" class="form-control"
                                            placeholder="e.g. MG Road, Bengaluru"
                                            value="{{ $field('bank2_branch', 'Bank2Branch') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">{{ $isEdit ? 'Update' : 'Add' }}</button>
                            </div>
                            @if ($isEdit)
                                <div class="col-md-2 align-self-end">
                                    <a href="{{ route('invoice.companies') }}" class="btn btn-secondary w-100">Cancel</a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>

    <script>
        (function () {
            'use strict';

            document.querySelectorAll('input.text-uppercase').forEach(function (el) {
                el.addEventListener('input', function () {
                    var pos = el.selectionStart;
                    el.value = el.value.toUpperCase();
                    el.setSelectionRange(pos, pos);
                });
            });

            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection
