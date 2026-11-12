@extends('layout.master')
@section('title', $title ?? 'Edit Employee')

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

    <div class="page-wrapper employee-edit-page">
        <div class="content container-fluid">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-user-edit fs-20"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Edit Employee' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}" class="text-muted">Employees</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Edit #{{ $employee->employee_id }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1.5"></i>Back to Directory
                    </a>
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
                        <h5 class="card-title mb-0 fw-bold text-dark">Edit Employee Details: {{ $employee->employee_name }}</h5>
                        <p class="text-muted fs-12 mb-0 mt-0.5">Update employee profile, contact information, and posting details.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-11 font-monospace">
                        ID: #{{ $employee->employee_id }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <form class="needs-validation" novalidate method="POST" action="{{ route('employees.update', $employee->employee_id) }}"
                        enctype="multipart/form-data" id="editEmployeeForm">
                        @csrf
                        @method('PUT')

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
                                           value="{{ old('employee_name', $employee->employee_name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" name="employee_email" class="form-control form-control-custom" 
                                           value="{{ old('employee_email', $employee->employee_email) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Mobile Number <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                    <input type="text" name="employee_mobile" class="form-control form-control-custom" 
                                           value="{{ old('employee_mobile', $employee->employee_mobile) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Password <span class="text-muted fw-normal fs-12">(Leave blank to keep unchanged)</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" name="employee_password" id="employee_password"
                                           class="form-control form-control-custom" placeholder="••••••••">
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
                                    <option value="1" @selected(old('employee_gender', $employee->employee_gender) == '1')>Male</option>
                                    <option value="2" @selected(old('employee_gender', $employee->employee_gender) == '2')>Female</option>
                                    <option value="3" @selected(old('employee_gender', $employee->employee_gender) == '3')>Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Blood Group <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <select class="form-select select2" name="blood_group">
                                    <option value="">-- Select Blood Group --</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" @selected(old('blood_group', $employee->blood_group) === $bg)>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Account Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-select select2" required>
                                    <option value="Active" @selected(old('status', $employee->status) === 'Active' || old('status', $employee->status) === '')>Active</option>
                                    <option value="Inactive" @selected(old('status', $employee->status) === 'Inactive')>Inactive</option>
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
                                        <option value="{{ $designation->id }}" @selected(old('designation_id', $employee->designation_id) == $designation->id)>
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
                                    <option value="Full Time" @selected(old('employment_type', $employee->employment_type) === 'Full Time' || old('employment_type', $employee->employment_type) === 'Full-time')>Full Time</option>
                                    <option value="Part Time" @selected(old('employment_type', $employee->employment_type) === 'Part Time')>Part Time</option>
                                    <option value="Contract" @selected(old('employment_type', $employee->employment_type) === 'Contract')>Contract</option>
                                    <option value="Intern" @selected(old('employment_type', $employee->employment_type) === 'Intern')>Intern</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Joining Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    <input type="date" name="joining_date" class="form-control form-control-custom" 
                                           value="{{ old('joining_date', $employee->joining_date) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Date of Confirmation <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text"><i class="ti ti-calendar-check"></i></span>
                                    <input type="date" name="date_of_confirmation" class="form-control form-control-custom" 
                                           value="{{ old('date_of_confirmation', $employee->date_of_confirmation) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                    Assigned Agency <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <select name="agency_id" class="form-select select2">
                                    <option value="">-- Direct / In-House (No Agency) --</option>
                                    @foreach ($agencies ?? [] as $agency)
                                        <option value="{{ $agency->id }}" @selected(old('agency_id', $employee->agency_id) == $agency->id)>
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
                                        <option value="{{ $shift->id }}" @selected(old('shift_id', $employee->shift_id) == $shift->id)>
                                            {{ $shift->shift_name }} ({{ date('h:i A', strtotime($shift->start_time)) }} - {{ date('h:i A', strtotime($shift->end_time)) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 border bg-light-subtle">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fs-13 fw-semibold text-dark mb-0 d-flex align-items-center gap-1">
                                            <i class="ti ti-calendar-off text-primary fs-16"></i> Custom Employee Week-Off Days (Optional Override)
                                        </label>
                                        <span class="badge bg-secondary-subtle text-secondary fs-11">Overrides Shift Week-Off</span>
                                    </div>
                                    <p class="text-muted fs-11 mb-2">
                                        Leave unselected to automatically inherit the assigned Shift's weekly off days. Or pick custom day(s) for this specific employee.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php
                                            $weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                            $rawEmpDays = old('week_off_days', $employee->week_off_days ?? []);
                                            $selectedEmpDays = is_array($rawEmpDays) ? $rawEmpDays : (json_decode($rawEmpDays, true) ?: []);
                                        @endphp
                                        @foreach ($weekDays as $day)
                                            <div class="form-check form-check-inline p-0 m-0">
                                                <input type="checkbox" class="btn-check" id="emp_weekoff_{{ $day }}" name="week_off_days[]" value="{{ $day }}"
                                                    {{ in_array($day, (array)$selectedEmpDays) ? 'checked' : '' }} autocomplete="off">
                                                <label class="btn btn-outline-primary btn-sm px-3 py-1 rounded-pill fs-12" for="emp_weekoff_{{ $day }}">
                                                    <i class="ti ti-calendar-event me-1"></i>{{ $day }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor Role & Reporting Assignment -->
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-center" style="background-color: #f8fafc;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-check-label fs-13 fw-semibold text-dark mb-0 cursor-pointer" for="is_supervisor">
                                            <i class="ti ti-crown text-warning me-1 fs-16"></i> Mark as Supervisor
                                        </label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="is_supervisor" value="1" id="is_supervisor" @checked(old('is_supervisor', $employee->is_supervisor)) style="width: 2.5em; height: 1.3em;">
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 11.5px;">
                                        Enables supervisor capabilities to manage mapped team members, inspect real-time attendance, and approve leaves.
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
                                        <option value="{{ $sup->employee_id }}" @selected(old('supervisor_id', $employee->supervisor_id) == $sup->employee_id)>
                                            {{ $sup->employee_name }} ({{ $sup->designation?->name ?? 'Supervisor' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Select which supervisor this employee reports to.
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
                                        <option value="{{ $country->id }}" @selected(old('posting_country', $employee->posting_country) == $country->id)>
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
                                           accept="image/jpeg,image/png,image/jpg" onchange="handleImageSelect(this)">
                                    
                                    @php
                                        $hasExistingImg = $employee->profile_image && $employee->profile_image !== 'default.png';
                                        $existingImgUrl = $hasExistingImg ? asset('uploads/employees/' . $employee->profile_image) : '';
                                    @endphp

                                    <div id="dropzonePrompt" class="{{ $hasExistingImg ? 'd-none' : '' }}">
                                        <div class="upload-icon">
                                            <i class="ti ti-cloud-upload"></i>
                                        </div>
                                        <h6 class="fs-14 fw-bold text-dark mb-1">Click to browse or drag & drop new photo</h6>
                                        <p class="text-muted fs-12 mb-2">Supported formats: JPEG, PNG, JPG &bull; Max 2MB</p>
                                        <span class="btn btn-sm btn-outline-primary px-3">Choose Photo</span>
                                    </div>

                                    <div id="avatarPreviewBox" class="{{ $hasExistingImg ? '' : 'd-none' }} text-center">
                                        <img id="avatarImgPreview" src="{{ $existingImgUrl ?: '#' }}" alt="Preview" 
                                             class="rounded-circle border border-3 border-primary shadow-sm mb-2" width="90" height="90" style="object-fit: cover;">
                                        <div class="fs-13 fw-semibold text-dark" id="avatarFileName">Current Picture</div>
                                        <button type="button" class="btn btn-xs btn-outline-danger mt-2" onclick="removeAvatar(event)">
                                            <i class="ti ti-trash me-1"></i>Replace Picture
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
                            <button type="submit" id="updateEmployeeBtn" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center">
                                <i class="ti ti-check me-1.5"></i>Update Employee
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            @if ($employee->is_supervisor || count($subordinates) > 0)
            <!-- SUPERVISOR TEAM MEMBERS / SUBORDINATES MANAGEMENT -->
            <div class="employee-form-card mb-4 mt-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="ti ti-users-group text-primary fs-18 me-2"></i>
                            Supervised Team Members (Mapped Subordinates)
                            <span class="badge bg-primary-subtle text-primary ms-2">{{ count($subordinates) }} Members</span>
                        </h6>
                        <small class="text-muted">These employees report directly to {{ $employee->employee_name }}. This supervisor can view their attendance and approve/reject leave requests.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Add more employees to this supervisor -->
                    <div class="p-3 mb-4 rounded-3 border bg-light">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">
                            <i class="ti ti-user-plus text-success me-1"></i> Add / Map More Employees to this Supervisor
                        </label>
                        <form id="mapSubordinatesForm" class="d-flex gap-2 align-items-center">
                            <select id="map_employee_ids" class="form-select select2" multiple="multiple" style="width: 100%;">
                                @foreach ($availableEmployees ?? [] as $avail)
                                    <option value="{{ $avail->employee_id }}">
                                        {{ $avail->employee_name }} ({{ $avail->designation?->name ?? 'Staff' }} - {{ $avail->agency ? $avail->agency->agency_name : 'Direct' }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="submitMapSubordinates()" class="btn btn-success text-nowrap px-3 d-inline-flex align-items-center">
                                <i class="ti ti-plus me-1"></i>Map to Team
                            </button>
                        </form>
                    </div>

                    <!-- List of currently mapped employees -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-12">#</th>
                                    <th class="fs-12">Employee Name</th>
                                    <th class="fs-12">Designation</th>
                                    <th class="fs-12">Agency</th>
                                    <th class="fs-12 text-center">Status</th>
                                    <th class="fs-12 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="subordinatesTableBody">
                                @forelse ($subordinates ?? [] as $index => $sub)
                                <tr id="subordinate-row-{{ $sub->employee_id }}">
                                    <td class="font-monospace fs-12 text-muted">#{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($sub->profile_image && $sub->profile_image !== 'default.png')
                                                <img src="{{ asset('uploads/employees/' . $sub->profile_image) }}" class="rounded-circle me-2 border" width="32" height="32" style="object-fit:cover;">
                                            @else
                                                <span class="rounded-circle bg-primary-subtle text-primary fw-bold d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 11px;">
                                                    {{ strtoupper(substr($sub->employee_name, 0, 2)) }}
                                                </span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark fs-13">{{ $sub->employee_name }}</div>
                                                <div class="text-muted fs-11">{{ $sub->employee_email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-secondary border fs-11">{{ $sub->designation?->name ?? 'Staff' }}</span></td>
                                    <td>
                                        @if ($sub->agency)
                                            <span class="badge border py-1 px-2 fs-11" style="background-color: #f3e8ff; color: #7e22ce; border-color: #d8b4fe !important;">
                                                <i class="ti ti-building me-1"></i>{{ $sub->agency->agency_name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border fs-11">Direct / In-House</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $sub->status === 'Inactive' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} fs-11">
                                            {{ $sub->status ?? 'Active' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" onclick="unmapSubordinate({{ $sub->employee_id }})" class="btn btn-xs btn-outline-danger" title="Unmap this employee from supervisor">
                                            <i class="ti ti-user-x me-1"></i>Unmap
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted fs-13">
                                        <i class="ti ti-users-off fs-24 d-block mb-1 text-secondary"></i>
                                        No team members are currently mapped to this supervisor. Select employees above to map them.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

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
                            $('#updateEmployeeBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();

            const currentCountryId = "{{ old('posting_country', $employee->posting_country) }}";
            const currentStateId = "{{ old('posting_state', $employee->posting_state) }}";
            const currentCityId = "{{ old('posting_city', $employee->posting_city) }}";

            // Load initial states if country selected
            if (currentCountryId) {
                loadStates(currentCountryId, currentStateId, function() {
                    if (currentStateId) {
                        loadCities(currentStateId, currentCityId);
                    }
                });
            }

            // Country -> State
            $('#posting_country').change(function() {
                let country_id = $(this).val();
                loadStates(country_id, null);
            });

            function loadStates(country_id, selectedStateId, callback) {
                $('#posting_state').html('<option value="">Loading states...</option>').trigger('change');
                $('#posting_city').html('<option value="">Select City</option>').trigger('change');

                if (country_id) {
                    $.get("{{ url('employees/get-states') }}/" + country_id, function(data) {
                        let options = '<option value="">-- Choose State --</option>';
                        $.each(data, function(key, state) {
                            let isSelected = (selectedStateId && selectedStateId == state.id) ? 'selected' : '';
                            options += `<option value="${state.id}" ${isSelected}>${state.name}</option>`;
                        });
                        $('#posting_state').html(options).trigger('change');
                        if (callback) callback();
                    });
                }
            }

            // State -> City
            $('#posting_state').change(function() {
                let state_id = $(this).val();
                loadCities(state_id, null);
            });

            function loadCities(state_id, selectedCityId) {
                $('#posting_city').html('<option value="">Loading cities...</option>').trigger('change');

                if (state_id) {
                    $.get("{{ url('employees/get-cities') }}/" + state_id, function(data) {
                        let options = '<option value="">-- Choose City --</option>';
                        $.each(data, function(key, city) {
                            let isSelected = (selectedCityId && selectedCityId == city.id) ? 'selected' : '';
                            options += `<option value="${city.id}" ${isSelected}>${city.name}</option>`;
                        });
                        $('#posting_city').html(options).trigger('change');
                    });
                }
            }

            // Auto-check supervisor toggle when Designation is 'Supervisor'
            $('#designation_id').on('change', function() {
                let selectedText = $(this).find('option:selected').text().trim().toLowerCase();
                if (selectedText.includes('supervisor')) {
                    $('#is_supervisor').prop('checked', true);
                }
            });
        });

        function submitMapSubordinates() {
            const selected = $('#map_employee_ids').val();
            if (!selected || selected.length === 0) {
                alert('Please select at least one employee to map.');
                return;
            }

            $.ajax({
                url: "{{ route('employees.mapSubordinates', $employee->employee_id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    employee_ids: selected
                },
                success: function(res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert(res.message || 'Failed to map employees.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error occurred while mapping employees.');
                }
            });
        }

        function unmapSubordinate(subordinateId) {
            if (!confirm('Are you sure you want to unmap this employee from this supervisor?')) {
                return;
            }

            $.ajax({
                url: "{{ route('employees.unmapSubordinate', $employee->employee_id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    subordinate_id: subordinateId
                },
                success: function(res) {
                    if (res.success) {
                        $('#subordinate-row-' + subordinateId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(res.message || 'Failed to unmap employee.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error occurred while unmapping employee.');
                }
            });
        }

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
            $('#profileImageInput').val('').click();
        }
    </script>
@endpush
