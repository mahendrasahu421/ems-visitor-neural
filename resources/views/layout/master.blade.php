<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'Admin Dashboard | Neural Info Solution')</title>

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
    <script src="assets/js/theme-script.js"></script>

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
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}

</head>

<body>

    {{-- <div id="global-loader">
		<div class="page-loader"></div>
	</div> --}}

    <!-- Main Wrapper -->
    <div class="main-wrapper">
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
                                                            <i class="ti ti-heart-handshake text-default me-2"></i>Deals
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


                        <!-- /Horizontal Single -->

                        <div class="d-flex align-items-center">
                            {{-- <div class="me-1">
                                <a href="#" class="btn btn-menubar btnFullscreen">
                                    <i class="ti ti-maximize"></i>
                                </a>
                            </div> --}}
                            <div class="dropdown me-1">
                                {{-- <a href="#" class="btn btn-menubar" data-bs-toggle="dropdown">
                                    <i class="ti ti-layout-grid-remove"></i>
                                </a> --}}
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
                                            <a href="{{ route('invoice.dashboard') }}" class="d-block py-2 pb-0">
                                                <span class="avatar avatar-md bg-transparent-dark me-2"><i
                                                        class="ti ti-file-invoice text-gray-9"></i></span>Invoices
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                            <img src="{{ asset('frontent/assets/img/profiles/avatar-27.jpg') }}"
                                                                alt="Profile">
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
                                                            <img src="{{ asset('frontent/assets/img/profiles/avatar-23.jpg') }}"
                                                                alt="Profile">
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
                                                            <img src="{{ asset('frontent/assets/img/profiles/avatar-25.jpg') }}"
                                                                alt="Profile">
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1">New student record <span
                                                                    class="text-dark fw-semibold"> George</span> is
                                                                created by <span
                                                                    class="text-dark fw-semibold">Teressa</span></p>
                                                            <span>2 hrs ago</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="border-0 mb-3 pb-0">
                                                <a href="#">
                                                    <div class="d-flex">
                                                        <span class="avatar avatar-lg me-2 flex-shrink-0">
                                                            <img src="{{ asset('frontent/assets/img/profiles/avatar-01.jpg') }}"
                                                                alt="Profile">
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
                                        <img src="{{ asset('frontent/assets/img/profiles/avatar-12.jpg') }}"
                                            alt="Img" class="img-fluid rounded-circle">
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
                                                    <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="card-body">
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
                                                href="{{ route('project.workflow') }}" target="_blank">
                                                <i class="ti ti-route me-1 text-primary"></i>Project Workflow Guide
                                            </a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="{{ route('api.docs') }}" target="_blank">
                                                <i class="ti ti-api me-1 text-info"></i>API Documentation
                                            </a>
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="{{ route('project.workflow.download') }}">
                                                <i class="ti ti-file-download me-1 text-success"></i>Download Docs (.docx)
                                            </a>
                                        </div> --}}
                                        <div class="card-footer">
                                            <a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
                                                href="{{ route('logout') }}">
                                                <i class="ti ti-login me-2"></i>Logout
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



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
                        <img src="{{ asset('frontent/assets/img/profiles/avatar-02.jpg') }}" alt="Img"
                            class="img-fluid rounded-circle">
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
                        <img src="{{ asset('frontent/assets/img/profiles/avatar-02.jpg') }}" alt="Img"
                            class="img-fluid rounded-circle">
                    </div>
                    <div class="text-start sidebar-profile-info ms-2">
                        <h6 class="fs-12 fw-normal mb-1">Adrian Herman</h6>
                        <p class="fs-10">System Admin</p>
                    </div>
                </div>
                {{-- <div class="input-group input-group-flat d-inline-flex mb-4">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search in HRMS">
                    <span class="input-group-text">
                        <kbd>CTRL + / </kbd>
                    </span>
                </div> --}}
                <div class="d-flex align-items-center justify-content-between menu-item mb-3">
                    {{-- <div class="me-3">
                        <a href="#" class="btn btn-menubar">
                            <i class="ti ti-layout-grid-remove"></i>
                        </a>
                    </div> --}}
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
                    @php
                        $dashboardOpen = request()->routeIs('home') || request()->routeIs('dashboard');
                        
                        $employeeOpen =
                            request()->routeIs('employees.*') ||
                            request()->routeIs('employee-report.*') ||
                            request()->routeIs('admin.resignation.*');

                        $clientsProjectsOpen =
                            request()->routeIs('invoice.clients*') ||
                            request()->routeIs('admin.clients*') ||
                            request()->routeIs('invoice.projects*') ||
                            request()->routeIs('projects.*') ||
                            request()->routeIs('admin.tickets.*');

                        $invoiceOpen =
                            request()->routeIs('invoice.dashboard') ||
                            request()->routeIs('invoice.invoices*') ||
                            request()->routeIs('invoice.payments*') ||
                            request()->routeIs('invoice.expenses*') ||
                            request()->routeIs('invoice.ledger*') ||
                            request()->routeIs('invoice.companies*');

                        $attendanceOpen =
                            request()->routeIs('attendance.register.*') ||
                            request()->routeIs('attendance.attendance-dashboard.*') ||
                            request()->routeIs('attendance.daily-attendance.*');

                        $leaveOpen =
                            request()->routeIs('attendance.leave.*') ||
                            request()->routeIs('settings.leave-type*') ||
                            request()->routeIs('settings.leave-allow*') ||
                            request()->routeIs('settings.leave.mapping*') ||
                            request()->routeIs('settings.holiday*');

                        $payrollOpsOpen =
                            request()->routeIs('masters.payroll.salary-structure*') ||
                            request()->routeIs('payroll.increments*') ||
                            request()->routeIs('payroll.process*') ||
                            request()->routeIs('payroll.batches*') ||
                            request()->routeIs('payroll.payslips*');

                        $payrollSetupOpen =
                            request()->routeIs('masters.payroll.salary-component*') ||
                            request()->routeIs('masters.payroll.calculation-rules*') ||
                            request()->routeIs('masters.payroll.taxation*') ||
                            request()->routeIs('masters.payroll.applicability*') ||
                            request()->routeIs('masters.payroll.payment-mode*') ||
                            request()->routeIs('masters.payroll.payment-frequency*') ||
                            request()->routeIs('masters.payroll.visibility*');

                        $mastersOpen =
                            request()->routeIs('masters.organisation.*') ||
                            request()->routeIs('settings.designation*') ||
                            request()->routeIs('settings.shift*');

                        $accessOpen =
                            request()->routeIs('role*') ||
                            request()->routeIs('roles.*') ||
                            request()->routeIs('admin.users.*') ||
                            request()->routeIs('permission*') ||
                            request()->routeIs('permissions.*');

                        $recruitmentOpen =
                            request()->routeIs('categories.*') ||
                            request()->routeIs('recruitment.*') ||
                            request()->routeIs('create-job-questionaire') ||
                            request()->routeIs('employee.onboarding');

                        $settingsOpen =
                            request()->routeIs('settings.email-templates') ||
                            request()->routeIs('email-templates.*') ||
                            request()->routeIs('settings.email-template.*');

                        $authUser = Auth::user();
                        $isSuperAdmin = $authUser && ($authUser->role_id == 1 || (method_exists($authUser, 'hasRole') && $authUser->hasRole('Super Admin')));
                        $isAdmin = $authUser && ($authUser->role_id == 2 || (method_exists($authUser, 'hasRole') && $authUser->hasRole('Admin')));

                        // Dynamic permission checker closure: Super admin & Admin have full bypass. For others, check specific permissions.
                        $canAccess = function ($permNames) use ($isSuperAdmin, $isAdmin, $authUser) {
                            if (!$authUser) return false;
                            if ($isSuperAdmin || $isAdmin) return true;
                            if (is_string($permNames)) $permNames = [$permNames];
                            foreach ($permNames as $p) {
                                if ($authUser->can($p) || (method_exists($authUser, 'hasPermissionTo') && $authUser->hasPermissionTo($p, 'web'))) {
                                    return true;
                                }
                            }
                            return false;
                        };

                        // Module visibility permissions
                        $canDashboard = $isSuperAdmin || $isAdmin || $canAccess(['dashboard.view']);
                        $canEmployee = $isSuperAdmin || $isAdmin || $canAccess(['employee.view', 'employee.create', 'employee.edit', 'employee.delete']);
                        $canAttendance = $isSuperAdmin || $isAdmin || $canAccess(['attendance.view', 'attendance.report.view']);
                        $canLeave = $isSuperAdmin || $isAdmin || $canAccess(['leave.view', 'leave.approve', 'leave.manage', 'holiday.view', 'holiday.manage']);
                        $canPayroll = $isSuperAdmin || $isAdmin || $canAccess(['payroll.view', 'payroll.manage']);
                        $canMasters = $isSuperAdmin || $isAdmin || $canAccess(['company.manage', 'branch.manage', 'settings.manage']);

                        $canRolePermission = $isSuperAdmin || $isAdmin || $canAccess(['role.view', 'role.create', 'role.edit', 'role.delete', 'permission.view', 'permission.create', 'permission.edit', 'permission.delete']);
                        $canUsers = $isSuperAdmin || $isAdmin || $canAccess(['user.view', 'user.create', 'user.edit', 'user.delete']);
                        $canRecruitment = $isSuperAdmin || $isAdmin || $canAccess(['recruitment.view', 'recruitment.manage', 'onboarding.view', 'onboarding.manage']);
                        $canSettings = $isSuperAdmin || $isAdmin || $canAccess(['settings.view', 'settings.manage']);
                        $canInvoice = $isSuperAdmin || $isAdmin || $canAccess(['invoice.view', 'invoice.manage']);

                        $hasAdminSection = $canRecruitment || $canSettings || $canRolePermission || $canUsers || $isSuperAdmin || $isAdmin;
                    @endphp

                    <ul>
                        {{-- ================= 1. MAIN MENU ================= --}}
                        <li class="menu-title"><span>MAIN MENU</span></li>
                        <li>
                            <ul>
                                @if ($canDashboard)
                                    <li>
                                        <a href="{{ route('home') }}" class="{{ $dashboardOpen ? 'active' : '' }}">
                                            <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- ================= 2. WORKFORCE / EMPLOYEES ================= --}}
                        @if ($canEmployee)
                            <li class="menu-title"><span>HR &amp; WORKFORCE</span></li>
                            <li>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $employeeOpen ? 'active subdrop' : '' }}">
                                            <i class="ti ti-users"></i><span>Employees</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <ul style="{{ $employeeOpen ? 'display: block;' : '' }}">
                                            @if ($isSuperAdmin || $isAdmin || $canAccess(['employee.view']))
                                                <li><a href="{{ route('employees.index') }}"
                                                        class="{{ request()->routeIs('employees.index') ? 'active' : '' }}">Employee Directory</a></li>
                                            @endif
                                            @if ($isSuperAdmin || $isAdmin || $canAccess(['employee.create']))
                                                <li><a href="{{ route('employees.create') }}"
                                                        class="{{ request()->routeIs('employees.create') ? 'active' : '' }}">Add New Employee</a></li>
                                            @endif
                                            @if ($isSuperAdmin || $isAdmin || $canAccess(['employee.view']))
                                                <li><a href="{{ route('employees.documents.index') }}"
                                                        class="{{ request()->routeIs('employees.documents.*') ? 'active' : '' }}">Document Vault</a></li>
                                            @endif
                                            @if ($isSuperAdmin || $isAdmin || $canAccess(['employee.view']))
                                                <li><a href="{{ route('admin.resignation.index') }}"
                                                        class="{{ request()->routeIs('admin.resignation.*') ? 'active' : '' }}">Resignations &amp; Exits</a></li>
                                            @endif
                                            @if ($isSuperAdmin || $isAdmin || $canAccess(['employee-report.view', 'employee.daily-report.view']))
                                                <li><a href="{{ route('employee-report.index') }}"
                                                        class="{{ request()->routeIs('employee-report.*') ? 'active' : '' }}">Daily Reports</a></li>
                                            @endif
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- ================= CLIENTS & PROJECTS ================= --}}
                        @if ($isSuperAdmin || $isAdmin || $canAccess(['client.view', 'client.manage']))
                            <li class="menu-title"><span>CLIENTS &amp; PROJECTS</span></li>
                            <li>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $clientsProjectsOpen ? 'active subdrop' : '' }}">
                                            <i class="ti ti-briefcase"></i><span>Clients &amp; Projects</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <ul style="{{ $clientsProjectsOpen ? 'display: block;' : '' }}">
                                            <li><a href="{{ route('invoice.clients') }}"
                                                    class="{{ request()->routeIs('invoice.clients*') || request()->routeIs('admin.clients*') ? 'active' : '' }}">Clients Directory</a></li>
                                            <li><a href="{{ route('invoice.projects') }}"
                                                    class="{{ request()->routeIs('invoice.projects*') || request()->routeIs('projects.*') ? 'active' : '' }}">Project Directory</a></li>
                                            <li><a href="{{ route('admin.tickets.index') }}"
                                                    class="{{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">Project Tickets</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- ================= INVOICE & BILLING ================= --}}
                        @if ($canInvoice)
                            <li class="menu-title"><span>INVOICE &amp; FINANCE</span></li>
                            <li>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $invoiceOpen ? 'active subdrop' : '' }}">
                                            <i class="ti ti-file-invoice"></i><span>Invoice &amp; Finance</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <ul style="{{ $invoiceOpen ? 'display: block;' : '' }}">
                                            <li><a href="{{ route('invoice.dashboard') }}"
                                                    class="{{ request()->routeIs('invoice.dashboard') ? 'active' : '' }}">Finance Dashboard</a></li>
                                            <li><a href="{{ route('invoice.invoices') }}"
                                                    class="{{ request()->routeIs('invoice.invoices') ? 'active' : '' }}">Invoices List</a></li>
                                            <li><a href="{{ route('invoice.invoices.create') }}"
                                                    class="{{ request()->routeIs('invoice.invoices.create') ? 'active' : '' }}">Create Invoice</a></li>
                                            <li><a href="{{ route('invoice.payments') }}"
                                                    class="{{ request()->routeIs('invoice.payments*') ? 'active' : '' }}">Payments Received</a></li>
                                            <li><a href="{{ route('invoice.expenses') }}"
                                                    class="{{ request()->routeIs('invoice.expenses*') ? 'active' : '' }}">Expenses</a></li>
                                            <li><a href="{{ route('invoice.ledger') }}"
                                                    class="{{ request()->routeIs('invoice.ledger*') ? 'active' : '' }}">Client Ledger</a></li>
                                            <li><a href="{{ route('invoice.companies') }}"
                                                    class="{{ request()->routeIs('invoice.companies*') ? 'active' : '' }}">Companies Master</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- ================= 3. TIME & ATTENDANCE ================= --}}
                        @if ($canAttendance || $canLeave)
                            <li class="menu-title"><span>ATTENDANCE &amp; LEAVES</span></li>
                            <li>
                                <ul>
                                    {{-- Attendance Submenu --}}
                                    @if ($canAttendance)
                                        <li class="submenu">
                                            <a href="javascript:void(0);" class="{{ $attendanceOpen ? 'active subdrop' : '' }}">
                                                <i class="ti ti-calendar-time"></i><span>Attendance</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul style="{{ $attendanceOpen ? 'display: block;' : '' }}">
                                                <li><a href="{{ route('attendance.register.index') }}"
                                                        class="{{ request()->routeIs('attendance.register.*') ? 'active' : '' }}">Attendance Register</a></li>
                                                <li><a href="{{ route('attendance.attendance-dashboard.org-reports') }}"
                                                        class="{{ request()->routeIs('attendance.attendance-dashboard.*') ? 'active' : '' }}">Attendance Reports</a></li>
                                                <li><a href="{{ route('attendance.daily-attendance.consistent-attendees') }}"
                                                        class="{{ request()->routeIs('attendance.daily-attendance.consistent-attendees') ? 'active' : '' }}">Consistent Attendees</a></li>
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- Leave Management Submenu --}}
                                    @if ($canLeave)
                                        <li class="submenu">
                                            <a href="javascript:void(0);" class="{{ $leaveOpen ? 'active subdrop' : '' }}">
                                                <i class="ti ti-calendar-event"></i><span>Leave Management</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul style="{{ $leaveOpen ? 'display: block;' : '' }}">
                                                <li><a href="{{ route('attendance.leave.request') }}"
                                                        class="{{ request()->routeIs('attendance.leave.request*') ? 'active' : '' }}">Leave Requests</a></li>
                                                <li><a href="{{ route('settings.leave-type') }}"
                                                        class="{{ request()->routeIs('settings.leave-type*') ? 'active' : '' }}">Leave Types</a></li>
                                                <li><a href="{{ route('settings.leave-allow') }}"
                                                        class="{{ request()->routeIs('settings.leave-allow*') || request()->routeIs('settings.leave.mapping*') ? 'active' : '' }}">Leave Allowance</a></li>
                                                <li><a href="{{ route('settings.holiday') }}"
                                                        class="{{ request()->routeIs('settings.holiday*') ? 'active' : '' }}">Holiday Calendar</a></li>
                                            </ul>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- ================= 4. PAYROLL MANAGEMENT ================= --}}
                        @if ($canPayroll)
                            <li class="menu-title"><span>PAYROLL MANAGEMENT</span></li>
                            <li>
                                <ul>
                                    {{-- Payroll Operations Submenu --}}
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $payrollOpsOpen ? 'active subdrop' : '' }}">
                                            <i class="ti ti-cash"></i><span>Payroll Operations</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <ul style="{{ $payrollOpsOpen ? 'display: block;' : '' }}">
                                            <li><a href="{{ route('masters.payroll.salary-structure') }}"
                                                    class="{{ request()->routeIs('masters.payroll.salary-structure*') ? 'active' : '' }}">Salary Structures</a></li>
                                            <li><a href="{{ route('payroll.increments.index') }}"
                                                    class="{{ request()->routeIs('payroll.increments*') ? 'active' : '' }}">Annual Increments</a></li>
                                            <li><a href="{{ route('payroll.process') }}"
                                                    class="{{ request()->routeIs('payroll.process*') ? 'active' : '' }}">Create Batch</a></li>
                                            <li><a href="{{ route('payroll.batches') }}"
                                                    class="{{ request()->routeIs('payroll.batches*') ? 'active' : '' }}">Payroll Batches</a></li>
                                            <li><a href="{{ route('payroll.payslips') }}"
                                                    class="{{ request()->routeIs('payroll.payslips*') ? 'active' : '' }}">Employee Payslips</a></li>
                                        </ul>
                                    </li>

                                    {{-- Payroll Setup / Rules Submenu --}}
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $payrollSetupOpen ? 'active subdrop' : '' }}">
                                            <i class="ti ti-calculator"></i><span>Payroll Setup</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <ul style="{{ $payrollSetupOpen ? 'display: block;' : '' }}">
                                            <li><a href="{{ route('masters.payroll.salary-component') }}"
                                                    class="{{ request()->routeIs('masters.payroll.salary-component*') ? 'active' : '' }}">Salary Components</a></li>
                                            <li><a href="{{ route('masters.payroll.calculation-rules') }}"
                                                    class="{{ request()->routeIs('masters.payroll.calculation-rules*') ? 'active' : '' }}">Calculation Rules</a></li>
                                            <li><a href="{{ route('masters.payroll.taxation') }}"
                                                    class="{{ request()->routeIs('masters.payroll.taxation*') ? 'active' : '' }}">Taxation Slabs</a></li>
                                            <li><a href="{{ route('masters.payroll.applicability') }}"
                                                    class="{{ request()->routeIs('masters.payroll.applicability*') ? 'active' : '' }}">Applicability Rules</a></li>
                                            <li><a href="{{ route('masters.payroll.payment-mode') }}"
                                                    class="{{ request()->routeIs('masters.payroll.payment-mode*') ? 'active' : '' }}">Payment Modes</a></li>
                                            <li><a href="{{ route('masters.payroll.payment-frequency') }}"
                                                    class="{{ request()->routeIs('masters.payroll.payment-frequency*') ? 'active' : '' }}">Payment Frequency</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- ================= 5. ORGANIZATION SETUP ================= --}}
                        @if ($canMasters)
                            <li class="menu-title"><span>ORGANIZATION</span></li>
                            <li>
                                <ul>
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $mastersOpen ? 'active subdrop' : '' }}">
                                            <i class="ti ti-building-community"></i><span>Organization Masters</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <ul style="{{ $mastersOpen ? 'display: block;' : '' }}">
                                            <li><a href="{{ route('masters.organisation.agency') }}"
                                                    class="{{ request()->routeIs('masters.organisation.agency') ? 'active' : '' }}">Agency Master</a></li>
                                            <li><a href="{{ route('masters.organisation.agency.mapping') }}"
                                                    class="{{ request()->routeIs('masters.organisation.agency.mapping') ? 'active' : '' }}">Agency Mapping</a></li>
                                            <li><a href="{{ route('masters.organisation.designation') }}"
                                                    class="{{ request()->routeIs('masters.organisation.designation*') || request()->routeIs('settings.designation*') ? 'active' : '' }}">Designation Master</a></li>
                                            <li><a href="{{ route('masters.organisation.shift') }}"
                                                    class="{{ request()->routeIs('masters.organisation.shift*') || request()->routeIs('settings.shift*') ? 'active' : '' }}">Shift Master</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- ================= 6. SYSTEM ADMINISTRATION ================= --}}
                        @if ($hasAdminSection)
                            <li class="menu-title"><span>ADMINISTRATION</span></li>
                            <li>
                                <ul>
                                    {{-- Role & Permission --}}
                                    @if ($canRolePermission || $canUsers)
                                        <li class="submenu">
                                            <a href="javascript:void(0);" class="{{ $accessOpen ? 'active subdrop' : '' }}">
                                                <i class="ti ti-shield-lock"></i><span>Role &amp; Permission</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul style="{{ $accessOpen ? 'display: block;' : '' }}">
                                                @if ($canUsers)
                                                    <li><a href="{{ route('admin.users.index') }}"
                                                            class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a></li>
                                                @endif
                                                @if ($canRolePermission)
                                                    <li><a href="{{ route('role') }}"
                                                            class="{{ request()->routeIs('role') || request()->routeIs('roles.*') ? 'active' : '' }}">Roles</a></li>
                                                    <li><a href="{{ route('permission') }}"
                                                            class="{{ request()->routeIs('permission') || request()->routeIs('permissions.*') ? 'active' : '' }}">Permissions</a></li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- Recruitment --}}
                                    @if ($canRecruitment)
                                        <li class="submenu">
                                            <a href="javascript:void(0);" class="{{ $recruitmentOpen ? 'active subdrop' : '' }}">
                                                <i class="ti ti-briefcase"></i><span>Recruitment</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul style="{{ $recruitmentOpen ? 'display: block;' : '' }}">
                                                @if ($canAccess(['recruitment.view', 'recruitment.manage']))
                                                    <li><a href="{{ route('categories.index') }}"
                                                            class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Job Categories</a></li>
                                                    <li><a href="{{ route('recruitment.jobs') }}"
                                                            class="{{ request()->routeIs('recruitment.jobs') ? 'active' : '' }}">Jobs</a></li>
                                                    <li><a href="{{ route('recruitment.jobs.create') }}"
                                                            class="{{ request()->routeIs('recruitment.jobs.create') ? 'active' : '' }}">Add Job</a></li>
                                                    <li><a href="{{ route('recruitment.jobs.applied-candidate') }}"
                                                            class="{{ request()->routeIs('recruitment.jobs.applied-candidate') ? 'active' : '' }}">Applied Candidates</a></li>
                                                    <li><a href="{{ route('create-job-questionaire') }}"
                                                            class="{{ request()->routeIs('create-job-questionaire') ? 'active' : '' }}">Job Questionnaire</a></li>
                                                @endif
                                                @if ($canAccess(['onboarding.view', 'onboarding.manage']))
                                                    <li><a href="{{ route('employee.onboarding') }}"
                                                            class="{{ request()->routeIs('employee.onboarding') ? 'active' : '' }}">Onboarding</a></li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- System Settings --}}
                                    @if ($canSettings)
                                        <li class="submenu">
                                            <a href="javascript:void(0);" class="{{ $settingsOpen ? 'active subdrop' : '' }}">
                                                <i class="ti ti-settings"></i><span>Settings</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul style="{{ $settingsOpen ? 'display: block;' : '' }}">
                                                <li><a href="{{ route('settings.email-templates') }}"
                                                        class="{{ $settingsOpen ? 'active' : '' }}">Email Templates</a></li>
                                                <li><a href="{{ route('privacy.policy') }}" target="_blank">Privacy Policy</a></li>
                                                <li><a href="{{ route('terms.conditions') }}" target="_blank">Terms &amp; Conditions</a></li>
                                            </ul>
                                        </li>
                                    @endif

                                    {{-- REST API Docs --}}
                                    @if ($isSuperAdmin || $isAdmin)
                                        <li>
                                            <a href="{{ route('api.docs') }}" target="_blank"
                                                class="{{ request()->routeIs('api.docs*') ? 'active' : '' }}">
                                                <i class="ti ti-code"></i><span>API Docs</span>
                                                <span class="badge bg-primary ms-auto" style="font-size: 10px;">REST</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="slimScrollBar"
                    style="background: rgb(204, 204, 204); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 30px;">
                </div>
                <div class="slimScrollRail"
                    style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;">
                </div>
            </div>
        </div>

        <!-- /Sidebar -->


        @yield('main-section')
    </div> <!-- Bootstrap Core JS -->
    {{-- <script src="{{ asset('frontent/assets/js/theia-sticky-sidebar.js') }}"></script> --}}
    <script src="{{ asset('frontent/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Feather Icon JS -->
    <script src="{{ asset('frontent/assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('frontent/assets/js/jquery.slimscroll.min.js') }}"></script>
    {{-- <script src="{{ asset('frontent/assets/js/jquery.dataTables.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('frontent/assets/js/dataTables.bootstrap5.min.js') }}"></script> --}}

    <!-- Chart JS -->
    <script src="{{ asset('frontent/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('frontent/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/plugins/chartjs/chart-data.js') }}"></script>

    <!-- Datetimepicker JS -->
    <script src="{{ asset('frontent/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

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
    <script src="{{ asset('frontent/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('frontent/assets/js/todo.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/script.js') }}"></script>
    @stack('scripts')
    @stack('after_scripts')
</body>


<!-- Mirrored from smartco.in/demo/html/template/index by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 08 Aug 2025 05:19:55 GMT -->

</html>
