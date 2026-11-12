@extends('layout.master')
@section('title', $title ?? 'Add Employee')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .employee-form-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .employee-section-header {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.01em;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }

        .employee-section-header i {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .form-section-divider {
            border-top: 1px dashed #e2e8f0;
            margin: 1.75rem 0;
        }

        .input-group-custom .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }

        .form-control-custom {
            border-color: #e2e8f0;
            font-size: 0.92rem;
            padding: 0.6rem 0.85rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        /* Avatar Upload Dropzone */
        .avatar-dropzone {
            border: 2px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 14px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .avatar-dropzone:hover,
        .avatar-dropzone.dragover {
            background-color: #f1f5f9;
            border-color: #3b82f6;
        }

        .avatar-dropzone .upload-icon {
            font-size: 2.2rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            transition: transform 0.2s ease;
        }

        .avatar-dropzone:hover .upload-icon {
            transform: translateY(-3px);
            color: #3b82f6;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            height: 42px !important;
            padding: 6px 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1e293b !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        .select2-container+.select2-container {
            display: none !important;
        }
    </style>

    <div class="page-wrapper employee-create-page">
        <div class="content container-fluid">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-user-plus fs-20"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Add Employee' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}" class="text-muted">Employees</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Create</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                        <i class="ti ti-file-spreadsheet me-1.5 fs-16"></i>Bulk Import (Excel / CSV)
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1.5"></i>Back to Directory
                    </a>
                </div>
            </div>

            {{-- Bulk Import Quick Alert Banner --}}
            <div class="alert border-0 shadow-sm rounded-4 mb-4 p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-left: 5px solid #0284c7 !important;">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar avatar-md rounded-circle bg-white text-info shadow-sm d-inline-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="ti ti-file-spreadsheet fs-22"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold text-dark mb-0.5">Need to add multiple employees at once?</h6>
                        <p class="text-muted fs-12 mb-0">You can upload a CSV or Excel (.xlsx) file with all your employees' details to register them in bulk.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('employees.import.sample') }}" class="btn btn-sm btn-white bg-white border shadow-sm text-dark fw-semibold">
                        <i class="ti ti-download me-1 text-primary"></i>Download Sample Template
                    </a>
                    <button type="button" class="btn btn-sm btn-primary shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                        <i class="ti ti-upload me-1"></i>Open Import Wizard
                    </button>
                </div>
            </div>

            {{-- Validation Alert --}}
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-circle fs-20 me-2 mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Please fix the following validation errors:</h6>
                            <ul class="mb-0 ps-3 fs-13">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Main Form Card --}}
            <div class="card employee-form-card">
                <div class="card-header bg-white border-bottom py-3.5 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Single Employee Registration Form</h5>
                        <p class="text-muted fs-12 mb-0 mt-0.5">Enter the employee's personal, operational, and posting details below.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-11 font-monospace">
                        <i class="ti ti-id me-1"></i>Single Entry
                    </span>
                </div>

                <div class="card-body p-4">
                    <form class="needs-validation" novalidate method="POST" action="{{ route('employees.store') }}"
                        enctype="multipart/form-data" id="createEmployeeForm">
                        @csrf

                        <!-- SECTION 1: PERSONAL & ACCOUNT IDENTITY -->
                        <div class="employee-section-header">
                            <i class="ti ti-user-circle text-primary"></i> 1. Personal & Account Details
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" name="employee_name" class="form-control form-control-custom" 
                                           placeholder="e.g. John Doe" value="{{ old('employee_name') }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" name="employee_email" class="form-control form-control-custom" 
                                           placeholder="john.doe@example.com" value="{{ old('employee_email') }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Mobile Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                    <input type="text" name="employee_mobile" class="form-control form-control-custom" 
                                           placeholder="9876543210" value="{{ old('employee_mobile') }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Portal Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="employee_password" id="employee_password"
                                           class="form-control form-control-custom" placeholder="Min 6 characters" required>
                                    <button class="btn btn-outline-light border text-muted" type="button" id="togglePassword">
                                        <i class="ti ti-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Gender <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" name="employee_gender" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="1" @selected(old('employee_gender') == '1')>Male</option>
                                    <option value="2" @selected(old('employee_gender') == '2')>Female</option>
                                    <option value="3" @selected(old('employee_gender') == '3')>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Blood Group <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <select class="form-select select2" name="blood_group">
                                    <option value="">-- Select Blood Group --</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" @selected(old('blood_group') === $bg)>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Account Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-select select2" required>
                                    <option value="Active" @selected(old('status', 'Active') === 'Active')>Active</option>
                                    <option value="Inactive" @selected(old('status') === 'Inactive')>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <!-- SECTION 2: WORK & DESIGNATION DETAILS -->
                        <div class="employee-section-header">
                            <i class="ti ti-briefcase text-primary"></i> 2. Designation & Employment Terms
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Designation <span class="text-danger">*</span>
                                </label>
                                <select name="designation_id" id="designation_id" class="form-select select2" required>
                                    <option value="">-- Choose Designation --</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}" @selected(old('designation_id') == $designation->id)>
                                            {{ $designation->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Employment Type <span class="text-danger">*</span>
                                </label>
                                <select name="employment_type" class="form-select select2" required>
                                    <option value="">-- Choose Type --</option>
                                    <option value="Full Time" @selected(old('employment_type', 'Full Time') === 'Full Time')>Full Time</option>
                                    <option value="Part Time" @selected(old('employment_type') === 'Part Time')>Part Time</option>
                                    <option value="Contract" @selected(old('employment_type') === 'Contract')>Contract</option>
                                    <option value="Intern" @selected(old('employment_type') === 'Intern')>Intern</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Joining Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    <input type="date" name="joining_date" class="form-control form-control-custom" 
                                           value="{{ old('joining_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Date of Confirmation <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-calendar-check"></i></span>
                                    <input type="date" name="date_of_confirmation" class="form-control form-control-custom" 
                                           value="{{ old('date_of_confirmation') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Assigned Agency <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <select name="agency_id" class="form-select select2">
                                    <option value="">-- Direct / In-House (No Agency) --</option>
                                    @foreach ($agencies ?? [] as $agency)
                                        <option value="{{ $agency->id }}" @selected(old('agency_id') == $agency->id)>
                                            {{ $agency->agency_name }} {{ $agency->agency_code ? '('.$agency->agency_code.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Work Shift <span class="text-danger">*</span>
                                </label>
                                <select name="shift_id" class="form-select select2">
                                    @foreach ($shifts ?? [] as $shift)
                                        <option value="{{ $shift->id }}" @selected(old('shift_id', 1) == $shift->id)>
                                            {{ $shift->shift_name }} ({{ date('h:i A', strtotime($shift->start_time)) }} - {{ date('h:i A', strtotime($shift->end_time)) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Supervisor Role & Reporting Assignment -->
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-center" style="background-color: #f8fafc;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-check-label fs-13 fw-semibold text-dark mb-0 cursor-pointer" for="is_supervisor">
                                            <i class="ti ti-crown text-warning me-1 fs-16"></i> Mark as Supervisor
                                        </label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="is_supervisor" value="1" id="is_supervisor" @checked(old('is_supervisor')) style="width: 2.5em; height: 1.3em;">
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 11.5px;">
                                        Supervisors can have team members mapped to them, view team attendance, and approve/reject leave requests.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6" id="reportingSupervisorWrapper">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    <i class="ti ti-user-check text-primary me-1"></i> Reporting Supervisor <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <select name="supervisor_id" id="supervisor_id" class="form-select select2">
                                    <option value="">-- No Supervisor (Independent) --</option>
                                    @foreach ($supervisors ?? [] as $sup)
                                        <option value="{{ $sup->employee_id }}" @selected(old('supervisor_id') == $sup->employee_id)>
                                            {{ $sup->employee_name }} ({{ $sup->designation?->name ?? 'Supervisor' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Select which supervisor this employee will report to.
                                </small>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <!-- SECTION 3: POSTING LOCATION -->
                        <div class="employee-section-header">
                            <i class="ti ti-map-pin text-primary"></i> 3. Posting Location Details
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Posting Country <span class="text-danger">*</span>
                                </label>
                                <select name="posting_country" id="posting_country" class="form-select select2" required>
                                    <option value="">-- Choose Country --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" @selected(old('posting_country') == $country->id)>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Posting State <span class="text-danger">*</span>
                                </label>
                                <select name="posting_state" id="posting_state" class="form-select select2" required>
                                    <option value="">-- Choose State --</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Posting City <span class="text-danger">*</span>
                                </label>
                                <select name="posting_city" id="posting_city" class="form-select select2" required>
                                    <option value="">-- Choose City --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <!-- SECTION 4: PROFILE IMAGE UPLOAD -->
                        <div class="employee-section-header">
                            <i class="ti ti-photo text-primary"></i> 4. Profile Picture
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-8">
                                <div class="avatar-dropzone" id="avatarDropzone" onclick="document.getElementById('profileImageInput').click()">
                                    <input type="file" name="profile_image" id="profileImageInput" class="d-none" 
                                           accept="image/jpeg,image/png,image/jpg" required onchange="handleImageSelect(this)">
                                    
                                    <div id="dropzonePrompt">
                                        <div class="upload-icon">
                                            <i class="ti ti-cloud-upload"></i>
                                        </div>
                                        <h6 class="fs-14 fw-bold text-dark mb-1">Click to browse or drag & drop photo here</h6>
                                        <p class="text-muted fs-12 mb-2">Supported formats: JPEG, PNG, JPG &bull; Max 2MB</p>
                                        <span class="btn btn-sm btn-outline-primary px-3">Choose Photo</span>
                                    </div>

                                    <div id="avatarPreviewBox" class="d-none text-center">
                                        <img id="avatarImgPreview" src="#" alt="Preview" class="rounded-circle border border-3 border-primary shadow-sm mb-2" width="90" height="90" style="object-fit: cover;">
                                        <div class="fs-13 fw-semibold text-dark" id="avatarFileName"></div>
                                        <button type="button" class="btn btn-xs btn-outline-danger mt-2" onclick="removeAvatar(event)">
                                            <i class="ti ti-trash me-1"></i>Change Picture
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Footer Bar -->
                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('employees.index') }}" class="btn btn-light px-4 py-2">
                                Cancel
                            </a>
                            <button type="submit" id="saveEmployeeBtn" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center">
                                <i class="ti ti-check me-1.5"></i>Save Employee
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

        {{-- Bulk Import Modal --}}
        <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light bg-opacity-75 py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                                <i class="ti ti-file-spreadsheet fs-18"></i>
                            </span>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" id="bulkImportModalLabel">Bulk Import Employees</h5>
                                <p class="text-muted fs-12 mb-0">Upload Excel (.xlsx, .xls) or CSV (.csv) to register multiple employees simultaneously.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" id="bulkImportForm">
                        @csrf
                        <div class="modal-body p-4">

                            {{-- Step 1: Download Sample File --}}
                            <div class="p-3 mb-4 rounded-3 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fs-11 fw-bold">Step 1</span>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">Download Template First</div>
                                        <div class="text-muted fs-12">Use our pre-formatted spreadsheet template with sample columns.</div>
                                    </div>
                                </div>
                                <a href="{{ route('employees.import.sample') }}" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center">
                                    <i class="ti ti-download me-1.5"></i>Download Template (.csv)
                                </a>
                            </div>

                            {{-- Step 2: Upload File Area --}}
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold text-dark fs-13 mb-0 d-flex align-items-center gap-2">
                                        <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fs-11 fw-bold">Step 2</span>
                                        Select or Drag & Drop File <span class="text-danger">*</span>
                                    </label>
                                    <span class="text-muted fs-11">Formats: .xlsx, .xls, .csv &bull; Max: 10MB</span>
                                </div>

                                <div class="avatar-dropzone p-4 rounded-3" id="importDropzone" onclick="document.getElementById('employeeImportFileInput').click();">
                                    <div id="importDropzonePrompt">
                                        <i class="ti ti-cloud-upload upload-icon text-primary d-block mb-2" style="font-size: 2.5rem;"></i>
                                        <h6 class="fw-bold text-dark mb-1">Click to browse or drag file here</h6>
                                        <p class="text-muted fs-12 mb-0">Supported files: Microsoft Excel (.xlsx, .xls) and Comma-Separated Values (.csv)</p>
                                    </div>
                                    <div id="importFilePreview" class="d-none">
                                        <div class="d-flex align-items-center justify-content-center gap-3 py-2">
                                            <i class="ti ti-file-spreadsheet text-success fs-36"></i>
                                            <div class="text-start">
                                                <div class="fw-bold text-dark fs-14" id="importFileName">file.xlsx</div>
                                                <div class="text-muted fs-12" id="importFileSize">0 KB</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-light text-danger border ms-2" onclick="removeImportFile(event)">
                                                <i class="ti ti-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" name="employee_file" id="employeeImportFileInput" class="d-none" accept=".csv, .xlsx, .xls, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="handleImportFileSelect(this)">
                                </div>
                            </div>

                            {{-- Instructions & Column Mapping Help --}}
                            <div class="card border rounded-3 bg-light bg-opacity-25 mb-0">
                                <div class="card-header bg-transparent py-2.5 px-3 d-flex align-items-center justify-content-between">
                                    <span class="fs-12 fw-bold text-dark d-flex align-items-center gap-1.5">
                                        <i class="ti ti-info-circle text-info"></i> Column Mapping & Instructions
                                    </span>
                                    <span class="badge bg-success-subtle text-success fs-10">Smart Auto-Resolution Enabled</span>
                                </div>
                                <div class="card-body p-3 fs-12 text-muted">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <ul class="mb-0 ps-3">
                                                <li><strong>Employee Name <span class="text-danger">*</span></strong>: Full name of employee.</li>
                                                <li><strong>Employee Email <span class="text-danger">*</span></strong>: Unique valid email address.</li>
                                                <li><strong>Mobile Number <span class="text-danger">*</span></strong>: 10-digit mobile number.</li>
                                                <li><strong>Password</strong>: Optional (defaults to <code>123456</code>).</li>
                                                <li><strong>Gender <span class="text-danger">*</span></strong>: <em>Male</em>, <em>Female</em>, <em>Other</em>.</li>
                                                <li><strong>Blood Group</strong>: <em>A+, B+, O+, AB+, etc.</em></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <ul class="mb-0 ps-3">
                                                <li><strong>Designation <span class="text-danger">*</span></strong>: Name (e.g. <em>Software Engineer</em>) or ID.</li>
                                                <li><strong>Assigned Agency</strong>: Agency name, code, or ID.</li>
                                                <li><strong>Work Shift</strong>: Shift name (e.g. <em>General Shift</em>) or ID.</li>
                                                <li><strong>Employment Type</strong>: <em>Full Time</em>, <em>Part Time</em>, <em>Contract</em>.</li>
                                                <li><strong>Joining Date <span class="text-danger">*</span></strong>: <code>YYYY-MM-DD</code> (defaults to today).</li>
                                                <li><strong>Date of Confirmation</strong>: <code>YYYY-MM-DD</code>.</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <ul class="mb-0 ps-3">
                                                <li><strong>Mark as Supervisor</strong>: <em>Yes</em> / <em>No</em> (or auto-set if designation is Supervisor).</li>
                                                <li><strong>Reporting Supervisor</strong>: Supervisor name, email, mobile, or ID.</li>
                                                <li><strong>Country / State / City <span class="text-danger">*</span></strong>: Names or IDs.</li>
                                                <li><strong>Account Status <span class="text-danger">*</span></strong>: <em>Active</em> or <em>Inactive</em>.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer bg-light bg-opacity-50 py-3 px-4 border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="submitImportBtn" class="btn btn-primary btn-sm px-4 fw-semibold d-inline-flex align-items-center" disabled>
                                <i class="ti ti-upload me-1.5"></i>Start Bulk Import
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Password toggle
            $('#togglePassword').click(function() {
                const passwordField = $('#employee_password');
                const eyeIcon = $('#eyeIcon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('ti-eye').addClass('ti-eye-off');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('ti-eye-off').addClass('ti-eye');
                }
            });

            // Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Form validation
            (function() {
                'use strict';
                var forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            $('#saveEmployeeBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();

            // Country -> State
            $('#posting_country').change(function() {
                let country_id = $(this).val();
                $('#posting_state').html('<option value="">Loading states...</option>').trigger('change');
                $('#posting_city').html('<option value="">Select City</option>').trigger('change');

                if (country_id) {
                    $.get("{{ url('employees/get-states') }}/" + country_id, function(data) {
                        let options = '<option value="">-- Choose State --</option>';
                        $.each(data, function(key, state) {
                            options += `<option value="${state.id}">${state.name}</option>`;
                        });
                        $('#posting_state').html(options).trigger('change');
                    });
                }
            });

            // State -> City
            $('#posting_state').change(function() {
                let state_id = $(this).val();
                $('#posting_city').html('<option value="">Loading cities...</option>').trigger('change');

                if (state_id) {
                    $.get("{{ url('employees/get-cities') }}/" + state_id, function(data) {
                        let options = '<option value="">-- Choose City --</option>';
                        $.each(data, function(key, city) {
                            options += `<option value="${city.id}">${city.name}</option>`;
                        });
                        $('#posting_city').html(options).trigger('change');
                    });
                }
            });

            // Auto-check supervisor toggle when Designation is 'Supervisor'
            $('#designation_id').on('change', function() {
                let selectedText = $(this).find('option:selected').text().trim().toLowerCase();
                if (selectedText.includes('supervisor')) {
                    $('#is_supervisor').prop('checked', true);
                }
            });

            // Form Submit loader for bulk import
            $('#bulkImportForm').on('submit', function() {
                const btn = $('#submitImportBtn');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span>Importing Employees...');
            });
        });

        // Image Dropzone Preview
        function handleImageSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size exceeds 2 MB limit.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatarImgPreview').attr('src', e.target.result);
                    $('#avatarFileName').text(file.name);
                    $('#dropzonePrompt').addClass('d-none');
                    $('#avatarPreviewBox').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeAvatar(e) {
            if (e) e.stopPropagation();
            $('#profileImageInput').val('');
            $('#avatarPreviewBox').addClass('d-none');
            $('#dropzonePrompt').removeClass('d-none');
        }

        // Bulk Import File Dropzone Preview
        function handleImportFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size exceeds 10 MB limit.');
                    input.value = '';
                    return;
                }

                $('#importFileName').text(file.name);
                $('#importFileSize').text((file.size / 1024).toFixed(1) + ' KB');
                $('#importDropzonePrompt').addClass('d-none');
                $('#importFilePreview').removeClass('d-none');
                $('#submitImportBtn').prop('disabled', false);
            }
        }

        function removeImportFile(e) {
            if (e) e.stopPropagation();
            $('#employeeImportFileInput').val('');
            $('#importFilePreview').addClass('d-none');
            $('#importDropzonePrompt').removeClass('d-none');
            $('#submitImportBtn').prop('disabled', true);
        }

        // Drag & drop support for bulk import dropzone
        const importDropzone = document.getElementById('importDropzone');
        if (importDropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                importDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    importDropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                importDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    importDropzone.classList.remove('dragover');
                }, false);
            });

            importDropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length) {
                    const input = document.getElementById('employeeImportFileInput');
                    input.files = files;
                    handleImportFileSelect(input);
                }
            }, false);
        }
    </script>
@endpush
