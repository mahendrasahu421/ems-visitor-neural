@php
    $limitedCompany = config('company.profile') === 'limited_company';
@endphp
<!DOCTYPE html>
<html lang="en">




<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'Employee Dashboard | Neural Info Solution')</title>

    <meta name="description"
        content="Chitragupta Employee Portal – Manage your attendance, leaves, payroll, and profile with ease. Access payslips, apply for leave, and track performance in a secure and user-friendly HRM system.">

    <meta name="keywords"
        content="Employee dashboard, HRM employee panel, payroll management, leave tracking, attendance system, employee self service, payslip download, HR portal, profile management, Chitragupta HRMS, Employees management">

    <meta name="author" content="Chitragupta Team">
    <meta name="robots" content="index, follow">



    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontent/assets/img/apple-touch-icon.png') }}">

    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('frontent') }}assets/img/favicon.png" type="image/x-icon">

    <!-- Theme Script js -->
    <script src="{{ asset('assets/js/theme-script.js') }}"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/icons/feather/feather.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/select2/css/select2.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Bootstrap Tagsinput CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

    <!-- Summernote CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/summernote/summernote-lite.min.css') }}">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/%40simonwep/pickr/themes/nano.min.css') }}">

    <link rel="stylesheet" href="{{ asset('frontent/assets/css/style.css') }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">


    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

    {{-- <div id="global-loader">
		<div class="page-loader"></div>
	</div> --}}

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">
            <div class="main-header">

                <div class="header-left">
                    <a href="#" class="logo">
                        <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                    </a>
                    <a href="#" class="dark-logo">
                        <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                    </a>
                </div>


                <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>

                <div class="header-user">
                    <div class="nav user-menu nav-list">

                        <div class="me-auto d-flex align-items-center" id="header-search">
                            <a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
                                <i class="ti ti-arrow-bar-to-left"></i>
                            </a>
                            <!-- Search -->
                            {{-- <div class="input-group input-group-flat d-inline-flex me-1">
                                <span class="input-icon-addon">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search in HRMS">
                                <span class="input-group-text">
                                    <kbd>CTRL + / </kbd>
                                </span>
                            </div> --}}
                            <!-- /Search -->
                            <div class="dropdown crm-dropdown">
                                {{-- <a href="#" class="btn btn-menubar me-1" data-bs-toggle="dropdown">
                                    <i class="ti ti-layout-grid"></i>
                                </a> --}}
                                <div class="dropdown-menu dropdown-lg dropdown-menu-start">
                                    <div class="card mb-0 border-0 shadow-none">
                                        <div class="card-header">
                                            <h4>CRM</h4>
                                        </div>
                                        <div class="card-body pb-1">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <a href="#"
                                                        class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                        <span class="d-flex align-items-center me-3">
                                                            <i class="ti ti-user-shield text-default me-2"></i>Contacts
                                                        </span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </a>
                                                    <a href="#"
                                                        class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                        <span class="d-flex align-items-center me-3">
                                                            <i
                                                                class="ti ti-heart-handshake text-default me-2"></i>Deals
                                                        </span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </a>
                                                    <a href="#"
                                                        class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                        <span class="d-flex align-items-center me-3">
                                                            <i
                                                                class="ti ti-timeline-event-text text-default me-2"></i>Pipeline
                                                        </span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </a>
                                                </div>
                                                <div class="col-sm-6">
                                                    <a href="#"
                                                        class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                        <span class="d-flex align-items-center me-3">
                                                            <i class="ti ti-building text-default me-2"></i>Companies
                                                        </span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </a>
                                                    <a href="#"
                                                        class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                        <span class="d-flex align-items-center me-3">
                                                            <i class="ti ti-user-check text-default me-2"></i>Leads
                                                        </span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </a>
                                                    <a href="#"
                                                        class="d-flex align-items-center justify-content-between p-2 crm-link mb-3">
                                                        <span class="d-flex align-items-center me-3">
                                                            <i class="ti ti-activity text-default me-2"></i>Activities
                                                        </span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <a href="#" class="btn btn-menubar">
                                <i class="ti ti-settings-cog"></i>
                            </a> --}}
                        </div>



                        <div class="d-flex align-items-center">
                            {{-- <div class="me-1">
                                <a href="#" class="btn btn-menubar btnFullscreen">
                                    <i class="ti ti-maximize"></i>
                                </a>
                            </div>  --}}
                            {{-- <div class="dropdown me-1">
                                <a href="#" class="btn btn-menubar" data-bs-toggle="dropdown">
                                    <i class="ti ti-layout-grid-remove"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="card mb-0 border-0 shadow-none">
                                        <div class="card-header">
                                            <h4>Applications</h4>
                                        </div>
                                        <div class="card-body">
                                            <a href="#" class="d-block pb-2">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-calendar text-gray-9"></i></span>Calendar
                                            </a>
                                            <a href="#" class="d-block py-2">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-subtask text-gray-9"></i></span>To Do
                                            </a>
                                            <a href="#" class="d-block py-2">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-notes text-gray-9"></i></span>Notes
                                            </a>
                                            <a href="#" class="d-block py-2">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-folder text-gray-9"></i></span>File Manager
                                            </a>
                                            <a href="#" class="d-block py-2">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-layout-kanban text-gray-9"></i></span>Kanban
                                            </a>
                                            <a href="#" class="d-block py-2 pb-0">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-file-invoice text-gray-9"></i></span>Invoices
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="me-1">
                                <a href="#" class="btn btn-menubar position-relative">
                                    <i class="ti ti-brand-hipchat"></i>
                                    <span
                                        class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
                                </a>
                            </div>
                            <div class="me-1">
                                <a href="#" class="btn btn-menubar" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="ti ti-message"></i>
                                </a>
                            </div> --}}
                            <div class="me-1 notification_item">
                                {{-- <a href="#" class="btn btn-menubar position-relative me-1"
                                    id="notification_popup" data-bs-toggle="dropdown">
                                    <i class="ti ti-bell"></i>
                                    <span class="notification-status-dot"></span>
                                </a> --}}
                                <div class="dropdown-menu dropdown-menu-end notification-dropdown p-4">
                                    <div
                                        class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
                                        <h4 class="notification-title">Notifications (2)</h4>
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="text-primary fs-15 me-3 lh-1">Mark all as
                                                read</a>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="bg-white dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti ti-calendar-due me-1"></i>Today
                                                </a>
                                                <ul class="dropdown-menu mt-2 p-3">
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">
                                                            This Week
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">
                                                            Last Week
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">
                                                            Last Month
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="noti-content">
                                        <div class="d-flex flex-column">
                                            <div class="border-bottom mb-3 pb-3">
                                                <a href="#">
                                                    <div class="d-flex">
                                                        <span class="avatar avatar-lg me-2 flex-shrink-0">
                                                            <img src="#/profiles/avatar-27.jpg" alt="Profile">
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1"><span
                                                                    class="text-dark fw-semibold">Shawn</span>
                                                                performance in Math is below the threshold.</p>
                                                            <span>Just Now</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="border-bottom mb-3 pb-3">
                                                <a href="#" class="pb-0">
                                                    <div class="d-flex">
                                                        <span class="avatar avatar-lg me-2 flex-shrink-0">
                                                            <img src="#/profiles/avatar-23.jpg" alt="Profile">
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1"><span
                                                                    class="text-dark fw-semibold">Sylvia</span> added
                                                                appointment on 02:00 PM</p>
                                                            <span>10 mins ago</span>
                                                            <div
                                                                class="d-flex justify-content-start align-items-center mt-1">
                                                                <span class="btn btn-light btn-sm me-2">Deny</span>
                                                                <span class="btn btn-primary btn-sm">Approve</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="border-bottom mb-3 pb-3">
                                                <a href="#">
                                                    <div class="d-flex">
                                                        <span class="avatar avatar-lg me-2 flex-shrink-0">
                                                            <img src="#/profiles/avatar-25.jpg" alt="Profile">
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">New student record <span
                                                                    class="text-dark fw-semibold"> George</span> is
                                                                created by <span class="text-dark fw-semibold">
                                                                    Teressa</span></p>
                                                            <span>2 hrs ago</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="border-0 mb-3 pb-0">
                                                <a href="#">
                                                    <div class="d-flex">
                                                        <span class="avatar avatar-lg me-2 flex-shrink-0">
                                                            <img src="#/profiles/avatar-01.jpg" alt="Profile">
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">A new teacher record for <span
                                                                    class="text-dark fw-semibold">Elisa</span> </p>
                                                            <span>09:45 AM</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex p-0">
                                        <a href="#" class="btn btn-light w-100 me-2">Cancel</a>
                                        <a href="#" class="btn btn-primary w-100">View All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown profile-dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    <span class="avatar avatar-sm online">
                                        
                                        <img src="{{ asset('frontent/assets/img/profiles/avatar-12.jpg') }}" alt="Img"
                                            class="img-fluid rounded-circle">
                                    </span>
                                </a>
                                <div class="dropdown-menu shadow-none">
                                    <div class="card mb-0">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg me-2 avatar-rounded">
                                                    <img src="{{ asset('frontent/assets/img/profiles/avatar-12.jpg') }}" alt="img">
                                                </span>
                                                <div>
                                                    <h5 class="mb-0">{{ Auth::guard('employee')->user()->employee_name }}</h5>
                                                    <p class="fs-12 fw-medium mb-0"><a href="#"
                                                            class="__cf_email__"
                                                            data-cfemail="d9aeb8ababbcb799bca1b8b4a9b5bcf7bab6b4">[{{ Auth::guard('employee')->user()->employee_email }}]</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="card-body">
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="#">
                                                <i class="ti ti-user-circle me-1"></i>My Profile
                                            </a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="#">
                                                <i class="ti ti-settings me-1"></i>Settings
                                            </a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="#">
                                                <i class="ti ti-circle-arrow-up me-1"></i>My Account
                                            </a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="#">
                                                <i class="ti ti-question-mark me-1"></i>Knowledge Base
                                            </a>
                                        </div> -->
                                        <div class="card-footer py-1">
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="{{ route('project.workflow') }}" target="_blank"><i
                                                    class="ti ti-route me-2 text-primary"></i>Workflow Guide</a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="{{ route('employee.logout') }}"><i
                                                    class="ti ti-login me-2"></i>Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div class="dropdown mobile-user-menu">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">My
                            Profile</a>
                        <a class="dropdown-item" href="#">Settings</a>
                        <a class="dropdown-item" href="#">Logout</a>
                    </div>
                </div>
                <!-- /Mobile Menu -->

            </div>

        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <a href="#" class="logo logo-normal">
                    <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                </a>
                <a href="#" class="logo-small">
                    <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                </a>
                <a href="#" class="dark-logo">
                    <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                </a>
            </div>
            <!-- /Logo -->
            <div class="modern-profile p-3 pb-0">
                <div class="text-center rounded bg-light p-3 mb-4 user-profile">
                    <div class="avatar avatar-lg online mb-3">
                        <img src="#/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
                    </div>
                    <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
                    <p class="fs-10">System Admin</p>
                </div>
                <div class="sidebar-nav mb-3">
                    <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                        role="tablist">
                        <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                        <li class="nav-item"><a class="nav-link border-0" href="#">Chats</a></li>
                        <li class="nav-item"><a class="nav-link border-0" href="#">Inbox</a></li>
                    </ul>
                </div>
            </div>
            <div class="sidebar-header p-3 pb-0 pt-2">
                <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
                    <div class="avatar avatar-md onlin">
                        <img src="#/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
                    </div>
                    <div class="text-start sidebar-profile-info ms-2">
                        <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
                        <p class="fs-10">System Admin</p>
                    </div>
                </div>
                <div class="input-group input-group-flat d-inline-flex mb-4">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search in HRMS">
                    <span class="input-group-text">
                        <kbd>CTRL + / </kbd>
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                    <div class="me-3">
                        <a href="#" class="btn btn-menubar">
                            <i class="ti ti-layout-grid-remove"></i>
                        </a>
                    </div>
                    <div class="me-3">
                        <a href="#" class="btn btn-menubar position-relative">
                            <i class="ti ti-brand-hipchat"></i>
                            <span
                                class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
                        </a>
                    </div>
                    <div class="me-3 notification-item">
                        <a href="#" class="btn btn-menubar position-relative me-1">
                            <i class="ti ti-bell"></i>
                            <span class="notification-status-dot"></span>
                        </a>
                    </div>
                    <div class="me-0">
                        <a href="#" class="btn btn-menubar">
                            <i class="ti ti-message"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Dashboard</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="{{ route('employee.dashboard') }}">
                                        <i class="ti ti-layout-navbar"></i><span>Dashboard</span>
                                    </a>
                                </li>
                                @unless ($limitedCompany)
                                    <li>
                                        <a href="{{ route('employee.tickets.index') }}" class="{{ request()->routeIs('employee.tickets.*') ? 'active' : '' }}">
                                            <i class="ti ti-ticket"></i><span>My Tickets</span>
                                        </a>
                                    </li>
                                @endunless

                            </ul>
                        </li>
                        @unless ($limitedCompany)
                            <li class="menu-title"><span>Daily Reports</span></li>
                            <li>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);">
                                            <i class="ti ti-smart-home"></i><span>Daily Reports</span><span
                                                class="menu-arrow"></span>
                                        </a>
                                        <ul>

                                            <li><a href="{{route('employee.daily.reports')}}">Fill DR
                                                </a></li>
                                            <li><a href="{{route('employee.daily.reports.view')}}">View DR
                                                </a></li>

                                        </ul>
                                    </li>

                                </ul>
                            </li>
                        @endunless
                        <li class="menu-title"><span>Leave Management</span></li>
                        <li>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-smart-home"></i><span>Leave Management</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>

                                        <li><a href="{{ route('employee.leaves.apply') }}">Apply Leaves
                                            </a></li>
                                        <li><a href="{{ route('employee.leaves') }}">Leaves List
                                            </a></li>
                                        <li><a href="{{ route('employee.holidays') }}">Holiday List
                                            </a></li>
                                        <li><a href="{{ route('employee.attendance') }}">Attendance
                                            </a></li>

                                    </ul>
                                </li>

                            </ul>
                        </li>

                        <li class="menu-title"><span>Payroll & Appraisal</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="{{ route('employee.increments') }}" class="{{ request()->routeIs('employee.increments*') ? 'active' : '' }}">
                                        <i class="ti ti-chart-arrows-vertical"></i><span>My Increments & Appraisal</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="menu-title"><span>Career &amp; Exit</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="{{ route('employee.resignation.index') }}" class="{{ request()->routeIs('employee.resignation*') ? 'active' : '' }}">
                                        <i class="ti ti-door-exit"></i><span>Resignation &amp; Exit</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- <li class="menu-title"><span>Appraisal</span></li>
                        <li>
                            <ul>
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-smart-home"></i><span>Appraisal</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>

                                        <li><a href="{{ route('employee.self.appraisal') }}">Self Appraisal
                                            </a></li>
                                        <li><a href="{{ route('employee.competencies') }}">KPI
                                            </a></li>
                                        <li><a href="{{ route('employee.form-c') }}">Form C
                                            </a></li>

                                    </ul>
                                </li>

                            </ul>
                        </li> --}}
                        <li class="menu-title"><span>Legal &amp; Compliance</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="{{ route('privacy.policy') }}" target="_blank">
                                        <i class="ti ti-shield-check"></i><span>Privacy Policy</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('terms.conditions') }}" target="_blank">
                                        <i class="ti ti-file-certificate"></i><span>Terms &amp; Conditions</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Sidebar -->


        @yield('main-section')

        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script> --}}
    </div> <!-- Bootstrap Core JS -->
    <script data-cfasync="false" src="../../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="{{ asset('frontent/assets/js/jquery-3.7.1.min.js') }}"></script>


    <!-- Feather Icon JS -->
    <script src="{{ asset('frontent/assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('frontent/assets/js/jquery.slimscroll.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('frontent/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('frontent/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/plugins/chartjs/chart-data.js') }}"></script>

    <!-- Datetimepicker JS -->
    <script src="{{ asset('frontent/assets/js/moment.min.js') }}"></script>

    <script src="{{ asset('frontent/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/plugins/fullcalendar/index.global.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/plugins/fullcalendar/calendar-data.js') }}"></script>
    <!-- Daterangepikcer JS -->
    <script src="{{ asset('frontent/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- Summernote JS -->
    <script src="{{ asset('frontent/assets/plugins/summernote/summernote-lite.min.js') }}"></script>

    <!-- Bootstrap Tagsinput JS -->
    <script src="{{ asset('frontent/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('frontent/assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('frontent/assets/plugins/%40simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('frontent/assets/js/todo.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/script.js') }}"></script>
    @stack('after_scripts')
</body>


<!-- Mirrored from /demo/html/template/index by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 08 Aug 2025 05:19:55 GMT -->

</html>
