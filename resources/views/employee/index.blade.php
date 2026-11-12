@extends('employee.layout.layout')
@section('title', $title)

@section('main-section')
    <style>
        .info-icon {
            position: relative;
            display: inline-block;
            cursor: pointer; 
        }

        .info-icon i {
            font-size: 16px;
            color: #000;
            transition: 0.2s ease;
        }

        .info-icon:hover i {
            transform: scale(1.15);
        }

        .info-tooltip {
            visibility: hidden;
            opacity: 0;
            min-width: 220px;
            background: #f26522;
            color: #fff;
            text-align: left;
            padding: 12px 14px;
            border-radius: 8px;
            position: absolute;
            z-index: 20;
            top: -5px;
            left: 32px;
            font-size: 13px;
            line-height: 1.45;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            transition: all 0.28s ease;
            transform: translateY(6px) scale(0.95);
        }

        .info-tooltip::before {
            content: "";
            position: absolute;
            top: 12px;
            left: -8px;
            border-width: 8px;
            border-style: solid;
            border-color: transparent #f26522 transparent transparent;
        }

        .info-icon:hover .info-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateY(0px) scale(1);
        }
         .modal-content {
            border-radius: 12px !important;
        }

        .form-control-lg {
            padding: 10px 14px;
            border-radius: 8px;
        }

        .form-label {
            font-size: 14px;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-body {
            background: #f9fafb;
            border-radius: 0 0 12px 12px;
        }

        .btn-primary {
            border-radius: 8px;
        }

        .btn-outline-secondary {
            border-radius: 8px;
        }

        .phone-mask {
            cursor: pointer;
            user-select: none;
        }
    </style>
    <x-alert-modal />
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">


            <!-- Employee Modal -->
            <div class="modal fade" id="employeeEditModal" tabindex="-1" aria-labelledby="employeeEditModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0 rounded-3">

                        <!-- Header -->
                        <div class="modal-header bg-primary py-3 rounded-top">
                            <h5 class="modal-title fw-semibold text-white" id="employeeEditModalLabel">
                                <i class="ti ti-user-edit me-1"></i> Edit Employee Details
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body px-4 py-4">

                            <form id="employeeEditForm" class="row g-3">

                                <!-- Name -->
                                <div class="col-12">
                                    <label for="employeeName" class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control form-control-lg" id="employeeName"
                                        value="{{ Auth::guard('employee')->user()->employee_name }}">
                                </div>

                                <!-- Email -->
                                <div class="col-12">
                                    <label for="employeeEmail" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control form-control-lg" id="employeeEmail"
                                        value="{{ Auth::guard('employee')->user()->employee_email }}">
                                </div>

                                <!-- Phone -->
                                <div class="col-12">
                                    <label for="employeeMobile" class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" class="form-control form-control-lg" id="employeeMobile"
                                        value="{{ Auth::guard('employee')->user()->employee_mobile }}">
                                </div>

                                <!-- Location -->
                                <div class="col-12">
                                    <label for="employeeLocation" class="form-label fw-semibold">State & City</label>
                                    <input type="text" class="form-control form-control-lg" id="employeeLocation"
                                        value="Bihar, Patna">
                                </div>

                                <!-- Joining Date -->
                                <div class="col-12">
                                    <label for="employeeJoinDate" class="form-label fw-semibold">Joining Date</label>
                                    <input type="text" class="form-control form-control-lg" id="employeeJoinDate"
                                        value="01-Jan-2020">
                                </div>

                                <!-- Buttons -->
                                <div class="col-12 text-end mt-2">
                                    <button type="button" class="btn btn-outline-secondary px-4 me-2"
                                        data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ti ti-device-floppy me-1"></i> Save Changes
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>

            {{-- <div class="alert bg-secondary-transparent alert-dismissible fade show mb-4">
                Your Leave Request on“24th April 2024”has been Approved!!!
                <button type="button" class="btn-close fs-14" data-bs-dismiss="alert" aria-label="Close"><i
                        class="ti ti-x"></i></button>
            </div> --}}
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">

                            <li class="breadcrumb-item">
                                Dashboard
                            </li>
                            {{-- <li class="breadcrumb-item active" aria-current="page">Employee Dashboard</li> --}}
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->


            <div class="row">
                <div class="col-xl-4 d-flex">
                    <div class="card position-relative flex-fill">
                        <div class="card-header bg-dark">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-lg avatar-rounded border border-white border-2 flex-shrink-0 me-2">
                                    <img src="{{ $imageUrl }}" alt="Img">
                                </span>
                                <div>
                                    <h5 class="text-white mb-1">{{ Auth::guard('employee')->user()->employee_name }}
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <p class="text-white fs-12 mb-0">Sofware Engineer</p>
                                        <span class="mx-1"><!-- resources/views/layouts/partials/status-dot.blade.php -->

                                            @auth
                                                <i class="ti ti-point-filled text-success" title="Online"></i>
                                            @else
                                                <i class="ti ti-point-filled text-danger" title="Offline"></i>
                                            @endauth

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="btn btn-icon btn-sm text-white rounded-circle edit-top"
                                data-bs-toggle="modal" data-bs-target="#employeeEditModal">
                                <i class="ti ti-edit"></i>
                            </a>

                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="d-block mb-1 fs-13">Phone Number</span>
                                <p class="text-gray-9">@php
                                    $mobile = Auth::guard('employee')->user()->employee_mobile;
                                    $maskedMobile = substr($mobile, 0, -4) . '****';
                                @endphp

                                <p class="text-gray-9 phone-mask mb-0" data-full="{{ $mobile }}"
                                    data-masked="{{ $maskedMobile }}">
                                    {{ $maskedMobile }}
                                </p>

                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="d-block mb-1 fs-13">Email Address</span>
                                <p class="text-gray-9"><a href="{{ Auth::guard('employee')->user()->employee_email }}"
                                        class="__cf_email__"
                                        data-cfemail="fba88f9e8b9e899f9ecac9cfbb9e839a968b979ed5989496">{{ Auth::guard('employee')->user()->employee_email }}</a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <span class="d-block mb-1 fs-13">State & City</span>
                                <p class="text-gray-9">Kanpur, Uttar Pradesh</p>
                            </div>
                            <div>
                                <p class="d-block mb-1 fs-13">Join Date & Total Duration</p>

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-gray-9 mb-0">15-Jan-2020</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-9 mb-0">6 years, 4 months</p>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
                <div class="col-xl-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <h5>Leave Details</h5>
                                <div class="dropdown">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>2024
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2024</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2023</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2022</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center"><i
                                                    class="ti ti-circle-filled fs-8 text-dark me-1"></i>
                                                <span class="text-gray-9 fw-semibold me-1">1254</span>
                                                on time
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center"><i
                                                    class="ti ti-circle-filled fs-8 text-success me-1"></i>
                                                <span class="text-gray-9 fw-semibold me-1">32</span>
                                                Late Attendance
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center"><i
                                                    class="ti ti-circle-filled fs-8 text-primary me-1"></i>
                                                <span class="text-gray-9 fw-semibold me-1">658</span>
                                                Work From Home
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <p class="d-flex align-items-center"><i
                                                    class="ti ti-circle-filled fs-8 text-danger me-1"></i>
                                                <span class="text-gray-9 fw-semibold me-1">14</span>
                                                Absent
                                            </p>
                                        </div>
                                        <div>
                                            <p class="d-flex align-items-center"><i
                                                    class="ti ti-circle-filled fs-8 text-warning me-1"></i>
                                                <span class="text-gray-9 fw-semibold me-1">68</span>
                                                Sick Leave
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 d-flex justify-content-md-end">
                                        <div id="leaves_chart"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="todo1">
                                        <label class="form-check-label" for="todo1">Better than <span
                                                class="text-gray-9">85%</span> of Employees</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <h5>Leave Details</h5>
                                <div class="dropdown">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>2024
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2024</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2023</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2022</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <span class="d-block mb-1">Total Leaves</span>
                                        <h4>16</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <span class="d-block mb-1">Taken</span>
                                        <h4>10</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <span class="d-block mb-1">Absent</span>
                                        <h4>2</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <span class="d-block mb-1">Request</span>
                                        <h4>0</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <span class="d-block mb-1">Worked Days</span>
                                        <h4>240</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <span class="d-block mb-1">Loss of Pay</span>
                                        <h4>2</h4>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div>
                                        <a href="#" class="btn btn-dark w-100" data-bs-toggle="modal"
                                            data-bs-target="#add_leaves">Apply New Leave</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <h5>My Skills</h5>
                                <div class="dropdown">
                                    <a href="javascript:void(0);"
                                        class="btn btn-white border btn-sm d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>2024
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2024</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2023</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2022</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="d-block border border-2 h-12 border-primary rounded-5 me-2"></span>
                                            <div>
                                                <h6 class="fw-medium mb-1">Presentation skills</h6>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="d-block border border-2 h-12 border-success rounded-5 me-2"></span>
                                            <div>
                                                <h6 class="fw-medium mb-1">Leadership skills</h6>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="border border-dashed bg-transparent-light rounded p-2 mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="d-block border border-2 h-12 border-purple rounded-5 me-2"></span>
                                            <div>
                                                <h6 class="fw-medium mb-1">Time management - Prioratising tasks</h6>

                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h5>Team Members</h5>
                                <div>
                                    <a href="#" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                        <img src="/demo/html/template/assets/img/users/user-27.jpg"
                                            class="rounded-circle border border-2" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">Mahendra sahu</a>
                                        </h6>
                                        <p class="fs-13">Developer</p>
                                    </div>
                                   
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-phone fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-mail-bolt fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm"><i
                                            class="ti ti-brand-hipchat fs-16"></i></a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                        <img src="/demo/html/template/assets/img/users/user-42.jpg"
                                            class="rounded-circle border border-2" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">Sneha sahu</a>
                                        </h6>
                                        <p class="fs-13">Developer</p>
                                    </div>
                                   
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-phone fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-mail-bolt fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm"><i
                                            class="ti ti-brand-hipchat fs-16"></i></a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                        <img src="/demo/html/template/assets/img/users/user-42.jpg"
                                            class="rounded-circle border border-2" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">Pranshi</a></h6>
                                        <p class="fs-13">Developer</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-phone fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-mail-bolt fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm"><i
                                            class="ti ti-brand-hipchat fs-16"></i></a>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                        <img src="/demo/html/template/assets/img/users/user-11.jpg"
                                            class="rounded-circle border border-2" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">Pritima
                                                Shrivastava</a></h6>
                                        <p class="fs-13">Graphic Designer</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-phone fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-mail-bolt fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm"><i
                                            class="ti ti-brand-hipchat fs-16"></i></a>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                        <img src="/demo/html/template/assets/img/users/user-27.jpg"
                                            class="rounded-circle border border-2" alt="img">
                                    </a>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">Sheetanshu
                                                Shrivastva</a></h6>
                                        <p class="fs-13">Team Leader</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-phone fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm me-2"><i
                                            class="ti ti-mail-bolt fs-16"></i></a>
                                    <a href="#" class="btn btn-light btn-icon btn-sm"><i
                                            class="ti ti-brand-hipchat fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <x-footer />

    </div>
    <!-- /Page Wrapper -->

    <!-- Add Leaves -->
    <div class="modal fade" id="add_leaves">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Leave</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body pb-0">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Leave Type</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Medical Leave</option>
                                        <option>Casual Leave</option>
                                        <option>Annual Leave</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">From </label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker"
                                            placeholder="dd/mm/yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">To </label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control datetimepicker"
                                            placeholder="dd/mm/yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar text-gray-7"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No of Days</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Remaining Days</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Reason</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Leaves</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Leaves -->




@endsection
@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneElement = document.querySelector(".phone-mask");

            phoneElement.addEventListener("click", function() {
                if (phoneElement.textContent === phoneElement.dataset.masked) {
                    phoneElement.textContent = phoneElement.dataset.full;
                } else {
                    phoneElement.textContent = phoneElement.dataset.masked;
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('employee.dashboard.data') }}", // backend route
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#totalEmployees').text(response.total_employees);
                    $('#pendingLeaves').text(response.pending_leaves);
                    $('#approvedLeaves').text(response.approved_leaves);
                },
                error: function() {
                    console.log('Error loading dashboard data');
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.map(function(toastEl) {
                var toast = new bootstrap.Toast(toastEl, {
                    delay: 30000
                }); // 4 sec
                toast.show();
            })
        });
    </script>
@endpush
