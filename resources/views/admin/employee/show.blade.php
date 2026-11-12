@extends('layout.master')
@section('title', $title ?? 'Employee Profile')

@section('main-section')

<style>
    .emp-profile-page .hero-card {
        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        border-radius: 18px;
        position: relative;
        padding: 30px;
        color: #ffffff;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
    }

    .emp-profile-page .hero-card::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.35) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }

    .emp-profile-page .hero-card::after {
        content: '';
        position: absolute;
        bottom: -70px;
        left: -40px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.25) 0%, rgba(168, 85, 247, 0) 70%);
        pointer-events: none;
    }

    .emp-avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .emp-avatar-img {
        width: 96px;
        height: 96px;
        border-radius: 24px;
        border: 4px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        object-fit: cover;
        background: #ffffff;
    }

    .emp-status-floating-dot {
        position: absolute;
        bottom: 3px;
        right: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2.5px solid #ffffff;
        background-color: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.8);
    }

    /* Quick Action Hub */
    .emp-action-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-top: 16px;
        margin-bottom: 24px;
    }

    .emp-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 10px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #334155;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 12px;
        font-weight: 600;
    }

    .emp-action-btn i {
        font-size: 20px;
        margin-bottom: 4px;
        transition: transform 0.2s ease;
    }

    .emp-action-btn:hover {
        transform: translateY(-2px);
        background: #ffffff;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        color: #0d6efd;
        border-color: #cbd5e1;
    }

    .emp-action-btn:hover i {
        transform: scale(1.15);
    }

    /* Detail Cards */
    .profile-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }

    .profile-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .profile-card-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-data-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #f1f5f9;
    }

    .profile-data-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .profile-data-row:first-child {
        padding-top: 0;
    }

    .profile-data-label {
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .profile-data-value {
        font-size: 13px;
        color: #1e293b;
        font-weight: 600;
    }

    .subordinate-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        transition: all 0.2s ease;
    }

    .subordinate-item:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="page-wrapper emp-profile-page">
    <div class="content">

        <!-- Breadcrumb & Header Navigation -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-id text-primary"></i> Employee Profile
                </h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active">{{ $emp->employee_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-arrow-left fs-15"></i> Back to Roster
                </a>
                <a href="{{ url('employees/edit/' . $emp->employee_id) }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-edit fs-15"></i> Edit Employee
                </a>
                <a href="{{ route('masters.payroll.salary-structure') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-cash-banknote fs-15"></i> Salary Structures
                </a>
                <a href="{{ route('employees.documents.index', ['employee_id' => $emp->employee_id]) }}" class="btn btn-outline-dark d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-files fs-15"></i> Document Vault
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $profileImgUrl = (!empty($emp->profile_image) && $emp->profile_image !== 'default.png')
                ? asset('uploads/employees/' . $emp->profile_image)
                : 'https://ui-avatars.com/api/?name=' . urlencode($emp->employee_name ?? 'Staff') . '&background=0d6efd&color=fff&size=160&bold=true';
            $isActive = ($emp->status === 'Active');
        @endphp

        <!-- Main Hero Profile Banner -->
        <div class="hero-card mb-3">
            <div class="d-flex flex-md-row flex-column align-items-md-center align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3.5 flex-wrap">
                    <div class="emp-avatar-wrapper">
                        <img src="{{ $profileImgUrl }}" alt="{{ $emp->employee_name }}" class="emp-avatar-img">
                        <span class="emp-status-floating-dot" style="background-color: {{ $isActive ? '#10b981' : '#ef4444' }};"></span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h3 class="fw-bold text-white mb-0">{{ $emp->employee_name }}</h3>
                            <span class="badge bg-opacity-20 text-white border border-white border-opacity-25 font-monospace fs-11">
                                #EMP-{{ $emp->employee_id }}
                            </span>
                            @if($isActive)
                                <span class="badge bg-success-subtle text-success fs-11 px-2.5 py-1 rounded-pill fw-bold">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-11 px-2.5 py-1 rounded-pill fw-bold">Inactive</span>
                            @endif
                        </div>

                        <div class="text-white-50 fs-13 d-flex align-items-center gap-3 flex-wrap mb-2">
                            <span><i class="ti ti-mail me-1"></i>{{ $emp->employee_email ?: '-' }}</span>
                            <span><i class="ti ti-phone me-1"></i>{{ $emp->employee_mobile ?: '-' }}</span>
                            @if(!empty($emp->full_location) && $emp->full_location !== '-')
                                <span><i class="ti ti-map-pin me-1 text-warning"></i>{{ $emp->full_location }}</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-opacity-20 text-white border border-white border-opacity-25 fs-11 px-2.5 py-1 rounded-pill">
                                <i class="ti ti-briefcase me-1"></i>{{ $emp->designation_name ?: 'Staff' }}
                            </span>
                            @if($emp->has_agency && $emp->agency_name !== 'Direct / In-House')
                                <span class="badge fs-11 text-white border border-white border-opacity-25 px-2.5 py-1 rounded-pill" style="background-color: rgba(168, 85, 247, 0.45);">
                                    <i class="ti ti-building me-1"></i>{{ $emp->agency_name }}
                                </span>
                            @else
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-15 fs-11 px-2.5 py-1 rounded-pill">
                                    <i class="ti ti-building-community me-1"></i>Direct / In-House
                                </span>
                            @endif
                            <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-15 fs-11 px-2.5 py-1 rounded-pill">
                                <i class="ti ti-clock me-1"></i>{{ $emp->shift_name ?: 'General Shift' }}
                            </span>
                            <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-15 fs-11 px-2.5 py-1 rounded-pill">
                                {{ $emp->employment_type ?: 'Full-time' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ url('employees/edit/' . $emp->employee_id) }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-edit"></i> Edit Details
                    </a>
                    <a href="{{ route('employees.index', ['view' => $emp->employee_id]) }}" class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-1">
                        <i class="ti ti-layout-sidebar-right"></i> Open In Drawer
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Action Hub -->
        <div class="emp-action-bar">
            <a href="tel:{{ $emp->employee_mobile }}" class="emp-action-btn" title="Call Mobile">
                <i class="ti ti-phone text-success"></i>
                <span>Call ({{ $emp->employee_mobile ?: '-' }})</span>
            </a>
            <a href="mailto:{{ $emp->employee_email }}" class="emp-action-btn" title="Send Email">
                <i class="ti ti-mail text-primary"></i>
                <span>Send Email</span>
            </a>
            <a href="{{ url('admin/attendance/monthly-report') }}?employee_id={{ $emp->employee_id }}" class="emp-action-btn" title="Attendance Sheet">
                <i class="ti ti-calendar-stats text-warning"></i>
                <span>Attendance Log</span>
            </a>
            <a href="{{ url('employees/edit/' . $emp->employee_id) }}" class="emp-action-btn" title="Edit Profile">
                <i class="ti ti-edit text-info"></i>
                <span>Edit Profile</span>
            </a>
        </div>

        <!-- Details Grid -->
        <div class="row g-3">
            <!-- Left Column: Personal & Hierarchy -->
            <div class="col-lg-6">

                <!-- Employment & Organization Details -->
                <div class="profile-card">
                    <div class="profile-card-title">
                        <i class="ti ti-briefcase text-primary fs-16"></i> Employment &amp; Organization
                    </div>

                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-id-badge text-muted"></i>Designation</span>
                        <span class="profile-data-value text-dark">{{ $emp->designation_name ?: 'Staff' }}</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-clock text-muted"></i>Employment Type</span>
                        <span class="profile-data-value">{{ $emp->employment_type ?: 'Full-time' }}</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-building text-muted"></i>Deployment Agency</span>
                        <span class="profile-data-value">
                            @if($emp->has_agency && $emp->agency_name !== 'Direct / In-House')
                                <span class="badge border fs-11" style="background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                    {{ $emp->agency_name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border fs-11">Direct / In-House</span>
                            @endif
                        </span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-calendar text-muted"></i>Joining Date</span>
                        <span class="profile-data-value">{{ $emp->joining_date ? \Carbon\Carbon::parse($emp->joining_date)->format('d M, Y') : '-' }}</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-calendar-check text-muted"></i>Date of Confirmation</span>
                        <span class="profile-data-value">{{ $emp->date_of_confirmation ? \Carbon\Carbon::parse($emp->date_of_confirmation)->format('d M, Y') : '-' }}</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-clock-hour-4 text-info"></i>Work Shift</span>
                        <span class="profile-data-value text-info fw-semibold">{{ $emp->shift_name ?: 'General Shift' }} ({{ $emp->shift_timing ?: '09:30 AM - 06:30 PM' }})</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-hourglass-empty text-muted"></i>Grace Period</span>
                        <span class="profile-data-value">{{ $emp->grace_period ?: '15 mins' }}</span>
                    </div>
                </div>

                <!-- Personal & Contact Information -->
                <div class="profile-card">
                    <div class="profile-card-title">
                        <i class="ti ti-user-circle text-primary fs-16"></i> Personal &amp; Contact Details
                    </div>

                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-mail text-muted"></i>Official Email</span>
                        <span class="profile-data-value"><a href="mailto:{{ $emp->employee_email }}">{{ $emp->employee_email ?: '-' }}</a></span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-phone text-muted"></i>Mobile Number</span>
                        <span class="profile-data-value"><a href="tel:{{ $emp->employee_mobile }}">{{ $emp->employee_mobile ?: '-' }}</a></span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-gender-bigender text-muted"></i>Gender</span>
                        <span class="profile-data-value">
                            @php
                                $genders = [1 => 'Male', 2 => 'Female', 3 => 'Other'];
                            @endphp
                            {{ $genders[$emp->employee_gender] ?? '-' }}
                        </span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-droplet text-muted"></i>Blood Group</span>
                        <span class="profile-data-value">
                            @if(!empty($emp->blood_group) && $emp->blood_group !== '-')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11">{{ $emp->blood_group }}</span>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-map-pin text-muted"></i>City / Station</span>
                        <span class="profile-data-value">{{ $emp->city_name ?: '-' }}</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-map-2 text-muted"></i>State &amp; Country</span>
                        <span class="profile-data-value">{{ $emp->state_name ?: '-' }}, {{ $emp->country_name ?: 'India' }}</span>
                    </div>
                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-map text-muted"></i>Full Address Location</span>
                        <span class="profile-data-value">{{ $emp->full_location ?: '-' }}</span>
                    </div>
                </div>

            </div>

            <!-- Right Column: Hierarchy & Compensation -->
            <div class="col-lg-6">

                <!-- Hierarchy & Supervision Card -->
                <div class="profile-card">
                    <div class="profile-card-title d-flex justify-content-between align-items-center">
                        <span><i class="ti ti-crown text-warning fs-16"></i> Hierarchy &amp; Reporting</span>
                        <span class="badge border py-1 px-2 fs-11 {{ $emp->is_supervisor ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-light text-muted' }}">
                            {{ $emp->is_supervisor ? 'Supervisor' : 'Standard Employee' }}
                        </span>
                    </div>

                    <div class="profile-data-row">
                        <span class="profile-data-label"><i class="ti ti-user-check text-muted"></i>Reports To (Supervisor)</span>
                        <span class="profile-data-value fw-semibold text-dark">
                            @if(!empty($emp->supervisor_name) && $emp->supervisor_name !== '-')
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="avatar avatar-xs rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <i class="ti ti-user fs-12"></i>
                                    </span>
                                    <span>{{ $emp->supervisor_name }}</span>
                                    @if(!empty($emp->supervisor_designation))
                                        <small class="text-muted">({{ $emp->supervisor_designation }})</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 fw-semibold text-dark">
                                <i class="ti ti-users text-primary me-1"></i> Supervised Team Members
                            </span>
                            <span class="badge bg-primary-subtle text-primary fs-11">
                                {{ $emp->subordinates_count }} Members
                            </span>
                        </div>

                        @if(!empty($subordinates) && count($subordinates) > 0)
                            <div class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                                @foreach($subordinates as $sub)
                                    <div class="subordinate-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm rounded-circle bg-white border text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="ti ti-user fs-14"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-12 mb-0">
                                                    <a href="{{ url('employees/' . $sub['employee_id']) }}" class="text-dark text-decoration-none hover-primary">
                                                        {{ $sub['employee_name'] }}
                                                    </a>
                                                </div>
                                                <div class="text-muted fs-11">{{ $sub['designation_name'] }} &bull; {{ $sub['employee_email'] }}</div>
                                            </div>
                                        </div>
                                        <span class="badge {{ $sub['status'] === 'Active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} fs-10">
                                            {{ $sub['status'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted fs-12 mb-0">No direct subordinates mapped to this employee.</p>
                        @endif
                    </div>
                </div>

                <!-- Salary & Compensation Structure Card -->
                <div class="profile-card">
                    <div class="profile-card-title d-flex justify-content-between align-items-center">
                        <span><i class="ti ti-cash-banknote text-success fs-16"></i> Compensation &amp; Payroll Setup</span>
                        @if($salaryStructure)
                            <span class="badge bg-success-subtle text-success fs-11">Configured</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning fs-11">Not Setup</span>
                        @endif
                    </div>

                    @if($salaryStructure)
                        <!-- Quick KPI Pills -->
                        <div class="row g-2 text-center mb-3">
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <span class="fs-10 text-muted text-uppercase fw-semibold d-block">Annual CTC</span>
                                    <h6 class="mb-0 fw-bold text-dark mt-1">&#8377; {{ number_format($salaryStructure->ctc_annual, 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-primary-subtle text-primary border-primary-subtle">
                                    <span class="fs-10 text-primary text-uppercase fw-semibold d-block">Monthly Gross</span>
                                    <h6 class="mb-0 fw-bold text-primary mt-1">&#8377; {{ number_format($salaryStructure->monthly_gross, 2) }}</h6>
                                </div>
                            </div>
                            @php
                                $ded = (float)$salaryStructure->pf_employee + (float)$salaryStructure->esi_employee + (float)$salaryStructure->professional_tax + (float)$salaryStructure->tds_monthly;
                                $net = max(0, (float)$salaryStructure->monthly_gross - $ded);
                            @endphp
                            <div class="col-4">
                                <div class="p-2 border rounded bg-success-subtle text-success border-success-subtle">
                                    <span class="fs-10 text-success text-uppercase fw-semibold d-block">Take-Home</span>
                                    <h6 class="mb-0 fw-bold text-success mt-1">&#8377; {{ number_format($net, 2) }}</h6>
                                </div>
                            </div>
                        </div>

                        <div class="profile-data-row">
                            <span class="profile-data-label"><i class="ti ti-coin text-muted"></i>Basic Salary</span>
                            <span class="profile-data-value">&#8377; {{ number_format($salaryStructure->basic_salary, 2) }}</span>
                        </div>
                        <div class="profile-data-row">
                            <span class="profile-data-label"><i class="ti ti-home text-muted"></i>HRA</span>
                            <span class="profile-data-value">&#8377; {{ number_format($salaryStructure->hra, 2) }}</span>
                        </div>
                        <div class="profile-data-row">
                            <span class="profile-data-label"><i class="ti ti-shield text-muted"></i>Employee PF (12%)</span>
                            <span class="profile-data-value text-danger">&#8377; {{ number_format($salaryStructure->pf_employee, 2) }}</span>
                        </div>
                        <div class="profile-data-row">
                            <span class="profile-data-label"><i class="ti ti-building-bank text-muted"></i>Payment Mode</span>
                            <span class="profile-data-value">{{ $salaryStructure->payment_mode ?: 'Bank Transfer' }}</span>
                        </div>
                        <div class="profile-data-row">
                            <span class="profile-data-label"><i class="ti ti-credit-card text-muted"></i>Bank Account</span>
                            <span class="profile-data-value font-monospace">{{ $salaryStructure->bank_account_no ?: '-' }} ({{ $salaryStructure->bank_name ?: '-' }})</span>
                        </div>

                        <div class="mt-3 pt-2 text-end border-top">
                            <a href="{{ route('masters.payroll.salary-structure.edit', $salaryStructure->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-edit me-1"></i> Edit Structure
                            </a>
                            <a href="{{ route('masters.payroll.salary-structure') }}" class="btn btn-primary btn-sm ms-1">
                                <i class="ti ti-table me-1"></i> View All Structures
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="avatar avatar-md rounded-circle bg-light text-muted mx-auto mb-2 d-flex align-items-center justify-content-center">
                                <i class="ti ti-wallet-off fs-20"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Salary Structure Configured</h6>
                            <p class="text-muted fs-12 mb-3">Salary structure and statutory allowances have not been configured for this employee yet.</p>
                            <a href="{{ route('masters.payroll.salary-structure.create', ['employee_id' => $emp->employee_id]) }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                                <i class="ti ti-plus me-1"></i> Configure Salary Structure
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Document Vault & KYC Verification Section -->
        <div class="row mt-3 mb-4">
            <div class="col-12">
                <div class="profile-card">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #e0e7ff; color: #4338ca;">
                                <i class="ti ti-files fs-16"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Document Vault &amp; KYC Verification</h5>
                                <small class="text-muted fs-11">Government identity proofs, academic credentials &amp; service certificates</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('employees.documents.index', ['employee_id' => $emp->employee_id]) }}" class="btn btn-outline-primary btn-sm px-3 rounded-2">
                                <i class="ti ti-external-link me-1"></i> Open in Document Vault
                            </a>
                        </div>
                    </div>

                    @if(isset($documents) && $documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fs-11 text-uppercase text-muted">Type</th>
                                        <th class="fs-11 text-uppercase text-muted">Document Name</th>
                                        <th class="fs-11 text-uppercase text-muted">Identifier #</th>
                                        <th class="fs-11 text-uppercase text-muted">File Size</th>
                                        <th class="fs-11 text-uppercase text-muted text-center">Status</th>
                                        <th class="fs-11 text-uppercase text-muted text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $doc)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $doc->document_type }}</span>
                                            </td>
                                            <td class="fw-semibold text-dark fs-12">
                                                {{ $doc->document_name }}
                                            </td>
                                            <td class="font-monospace fs-11 text-muted">
                                                {{ $doc->document_number ?: '-' }}
                                            </td>
                                            <td class="fs-11 text-muted">
                                                {{ $doc->formatted_file_size }} ({{ strtoupper($doc->file_extension) }})
                                            </td>
                                            <td class="text-center">
                                                @if($doc->verification_status === 'Approved')
                                                    <span class="badge bg-success-subtle text-success fs-11"><i class="ti ti-check me-1"></i>Approved</span>
                                                @elseif($doc->verification_status === 'Pending')
                                                    <span class="badge bg-warning-subtle text-warning fs-11"><i class="ti ti-clock me-1"></i>Pending</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger fs-11" title="{{ $doc->rejection_reason }}"><i class="ti ti-x me-1"></i>Rejected</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('employees.documents.download', $doc->id) }}" class="btn btn-sm btn-outline-secondary px-2 py-1 fs-11" title="Download">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="avatar avatar-md rounded-circle bg-light text-muted mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="ti ti-folder-off fs-20"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Documents Uploaded</h6>
                            <p class="text-muted fs-12 mb-3">No verified credentials or identity proofs have been attached to this profile yet.</p>
                            <a href="{{ route('employees.documents.index', ['employee_id' => $emp->employee_id]) }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                                <i class="ti ti-upload me-1"></i> Upload Document in Vault
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
