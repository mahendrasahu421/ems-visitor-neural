@extends('layout.master')
@section('title', $title)
@section('main-section')

    <div class="page-wrapper">
        <div class="content">

            <div class="row">
                <div class="col-sm-12">

                    {{-- ======== Page Header ======== --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body d-md-flex d-block align-items-center justify-content-between">
                            <div>
                                <h2 class="mb-0">{{ $title }}</h2>
                            </div>
                            <div>
                                <a href="{{ route('employee') }}" class="btn btn-primary">
                                    <i class="ti ti-list-details me-2"></i> Employee List
                                </a>
                            </div>
                        </div>
                    </div>

                    <form class="needs-validation" novalidate method="POST" action="{{ route('employee.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        {{-- ========== Personal Details Card ========== --}}
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Personal Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Pat ID *</label>
                                        <input type="text" class="form-control" name="patId" value="EMP1001" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" name="employee_name" value="Rahul Sharma"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email"
                                            value="rahul.sharma@dreams.com" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Phone Number *</label>
                                        <input type="text" class="form-control" name="phone" value="9876543210"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Password</label>
                                        <input type="text" class="form-control" name="employee_password"
                                            value="9876543210" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Gender *</label>
                                        <select class="form-control" name="employee_gender" required>
                                            <option value=" ">Select Gender</option>
                                            @foreach ($gender as $genders)
                                                 <option value="{{ $genders->id }}">{{ $genders->name }}</option>
                                            @endforeach
                                           
                                           
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date of Birth *</label>
                                        <input type="date" class="form-control" name="dob" value="1995-06-15"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Marital Status</label>
                                        <select class="form-control" name="marital_status">
                                            <option value="Single" selected>Select Marital Status</option>
                                           @foreach ($MaritalStatus as $status)
                                               <option value="{{ $status->id }}" >{{ $status->name }}</option>
                                           @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="3">Dreams Tech Park, Pune, Maharashtra</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== Job Details Card ========== --}}
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Job Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Company *</label>
                                        <select class="form-control" name="company_id" required>
                                            @foreach ($company as $companys)
                                                 <option value="{{ $companys->id }}">{{ $companys->company_name }}</option>
                                            @endforeach
                                          
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Branch *</label>
                                        <select class="form-control" name="department" required>
                                            <option value="HR">Human Resource</option>
                                            <option value="IT" selected>IT</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Sales">Sales</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Category *</label>
                                        <select class="form-control" name="department" required>
                                            <option value="HR">Human Resource</option>
                                            <option value="IT" selected>IT</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Sales">Sales</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Designation *</label>
                                        <select class="form-control" name="designation" required>
                                            <option value="Manager">Manager</option>
                                            <option value="Developer" selected>Software Developer</option>
                                            <option value="Executive">Executive</option>
                                            <option value="Intern">Intern</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Employment Type *</label>
                                        <select class="form-control" name="employment_type" required>
                                            <option value="Full Time" selected>Full Time</option>
                                            <option value="Part Time">Part Time</option>
                                            <option value="Contract">Contract</option>
                                            <option value="Intern">Intern</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Joining Date *</label>
                                        <input type="date" class="form-control" name="joining_date"
                                            value="2023-03-01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Reporting Manager</label>
                                        <input type="text" class="form-control" name="reporting_manager"
                                            value="Amit Verma">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== Salary & Bank Card ========== --}}
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Salary & Bank Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Basic Salary (Monthly) *</label>
                                        <input type="number" class="form-control" name="basic_salary" value="45000"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Bank Name *</label>
                                        <input type="text" class="form-control" name="bank_name" value="HDFC Bank"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Account Number *</label>
                                        <input type="text" class="form-control" name="account_no"
                                            value="12345678901234" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">IFSC Code *</label>
                                        <input type="text" class="form-control" name="ifsc_code" value="HDFC0001234"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">PAN Number</label>
                                        <input type="text" class="form-control" name="pan_no" value="ABCDE1234F">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Aadhar Number</label>
                                        <input type="text" class="form-control" name="aadhar_no"
                                            value="1234 5678 9012">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== Emergency Contact Card ========== --}}
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Emergency Contact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Name *</label>
                                        <input type="text" class="form-control" name="emergency_name"
                                            value="Sanjay Sharma" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Relationship *</label>
                                        <input type="text" class="form-control" name="relationship" value="Father"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Number *</label>
                                        <input type="text" class="form-control" name="emergency_contact"
                                            value="9823123456" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== Documents Upload Card ========== --}}
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Document Uploads</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Profile Picture</label>
                                        <input type="file" class="form-control" name="profile_picture"
                                            accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Resume</label>
                                        <input type="file" class="form-control" name="resume"
                                            accept=".pdf,.doc,.docx">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== Status Card ========== --}}
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header">
                                <h5 class="mb-0">Employment Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="col-md-4">
                                    <label class="form-label">Status *</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ========== Action Buttons ========== --}}
                        <div class="card shadow-sm border-0">
                            <div class="card-body d-flex justify-content-end">
                                <a href="{{ route('employee') }}" class="btn btn-light me-2">
                                    <i class="ti ti-arrow-left me-1"></i> Back
                                </a>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                                <button class="btn btn-primary" type="submit">
                                    <i class="ti ti-device-floppy me-1"></i> Save Employee
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>

        <x-footer />
    </div>

@endsection
