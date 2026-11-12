@php
    $menus = $menus ?? collect();
@endphp
<!DOCTYPE html>
<html lang="en">




<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRXpert Dashboard | HRM, Payroll & CRM Admin Template</title>

    <meta name="description"
        content="HRXpert - A responsive Bootstrap 5 admin dashboard template for HRM, payroll, attendance, recruitment, and team management. Streamline your Employees management with an intuitive interface.">
    <meta name="keywords"
        content="HR dashboard, HRM admin template, Bootstrap 5 HR template, employee management, payroll, attendance, recruitment, HR analytics, CRM dashboard, team management">
    <meta name="author" content="HRXpert">
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
    {{-- <link rel="stylesheet" href="{{ asset('frontent/assets/css/dataTables.bootstrap5.min.css') }}"> --}}


    <!-- DataTables CSS -->


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
                        <img src="assets/img/logo.svg" alt="Logo">
                    </a>
                    <a href="#" class="dark-logo">
                        <img src="assets/img/logo-white.svg" alt="Logo">
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
                            <div class="input-group input-group-flat d-inline-flex me-1">
                                <span class="input-icon-addon">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search in HRMS">
                                <span class="input-group-text">
                                    <kbd>CTRL + / </kbd>
                                </span>
                            </div>
                            <!-- /Search -->
                            <div class="dropdown crm-dropdown">
                                <a href="#" class="btn btn-menubar me-1" data-bs-toggle="dropdown">
                                    <i class="ti ti-layout-grid"></i>
                                </a>
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
                            <a href="#" class="btn btn-menubar">
                                <i class="ti ti-settings-cog"></i>
                            </a>
                        </div>


                        <!-- /Horizontal Single -->

                        <div class="d-flex align-items-center">
                            <div class="me-1">
                                <a href="#" class="btn btn-menubar btnFullscreen">
                                    <i class="ti ti-maximize"></i>
                                </a>
                            </div>
                            <div class="dropdown me-1">
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
                            </div>
                            <div class="me-1">
                                <a href="#" class="btn btn-menubar position-relative">
                                    <i class="ti ti-brand-hipchat"></i>
                                    <span
                                        class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
                                </a>
                            </div>
                            <div class="me-1">
                                <a href="#" class="btn btn-menubar">
                                    <i class="ti ti-mail"></i>
                                </a>
                            </div>
                            <div class="me-1 notification_item">
                                <a href="#" class="btn btn-menubar position-relative me-1"
                                    id="notification_popup" data-bs-toggle="dropdown">
                                    <i class="ti ti-bell"></i>
                                    <span class="notification-status-dot"></span>
                                </a>
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
                                                            <img src="assets/img/profiles/avatar-27.jpg"
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
                                                            <img src="assets/img/profiles/avatar-23.jpg"
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
                                                            <img src="assets/img/profiles/avatar-25.jpg"
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
                                                            <img src="assets/img/profiles/avatar-01.jpg"
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
                                        <img src="frontent/assets/img/profiles/avatar-12.jpg" alt="Img"
                                            class="img-fluid rounded-circle">
                                    </span>
                                </a>
                                <div class="dropdown-menu shadow-none">
                                    <div class="card mb-0">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg me-2 avatar-rounded">
                                                    <img src="frontend/assets/img/profiles/avatar-12.jpg" alt="img">
                                                </span>
                                                <div>
                                                    <h5 class="mb-0">Kevin Larry</h5>
                                                    <p class="fs-12 fw-medium mb-0"><a
                                                            href="/cdn-cgi/l/email-protection"
                                                            class="__cf_email__"
                                                            data-cfemail="99eef8ebebfcf7d9fce1f8f4e9f5fcb7faf6f4">[email&#160;protected]</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
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
                                        </div>
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

                <!-- Mobile Menu -->
                <div class="dropdown mobile-user-menu">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">My Profile</a>
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
                    {{-- <img src="{{ $imageUrl }}" alt="Logo"> --}}
                </a>
                <a href="#" class="dark-logo">
                    {{-- <img src="{{ $imageUrl }}" alt="Logo"> --}}
                </a>
            </div>
            <!-- /Logo -->
            <style>
                .sidebar .sidebar-menu>ul>li>a span {
                    font-size: 13px;
                    font-weight: 500;
                    color: rgb(75, 85, 99);
                }

                .sidebar .sidebar-menu>ul>li.submenu ul li a {
                    font-weight: 500;
                    font-size: 14px;
                    position: relative;
                    display: block;
                    padding: 8px 8px 8px 40px;
                    padding-left: 30px;
                }
            </style>
            <style>
                /* Active menu highlight */
                .active-red {
                    color: #F26522 !important;
                    font-weight: bold;
                }
            </style>

            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        @foreach ($menus as $menu)
                            @php
                                $hasChildren = $menu->children->isNotEmpty();
                                $isActive =
                                    request()->routeIs($menu->route_name . '*') ||
                                    $menu->children
                                        ->pluck('route_name')
                                        ->contains(fn($r) => request()->routeIs($r . '*'));
                            @endphp

                            <li class="{{ $hasChildren ? 'submenu' : '' }}">
                                <a href="{{ $hasChildren ? 'javascript:void(0);' : ($menu->route_name ? route($menu->route_name) : '#') }}"
                                    class="{{ $isActive && !$hasChildren ? 'active-red' : '' }}">
                                    <i class="{{ $menu->icon }}"></i>
                                    <span>{{ $menu->title }}</span>
                                    @if ($hasChildren)
                                        <span class="menu-arrow"></span>
                                    @endif
                                </a>

                                @if ($hasChildren)
                                    <ul style="{{ $isActive ? 'display: block;' : '' }}">
                                        @foreach ($menu->children as $submenu)
                                            <li>
                                                <a href="{{ $submenu->route_name ? route($submenu->route_name) : '#' }}"
                                                    class="{{ request()->routeIs($submenu->route_name . '*') ? 'active-red' : '' }}">
                                                    {{ $submenu->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>






        </div>
        <!-- /Sidebar -->



        @yield('main-section')
    </div> <!-- Bootstrap Core JS -->


    <script data-cfasync="false" src="{{ asset('frontent/assets/email-decode.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('frontent/assets/js/feather.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('frontent/assets/js/jquery.slimscroll.min.js') }}"></script>
    {{-- <script src="{{ asset('frontent/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/dataTables.bootstrap5.min.js') }}"></script> --}}

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

    <!-- Custom JS -->
    <script src="{{ asset('frontent/assets/js/todo.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/script.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/custom-select2.js') }}"></script>
    <!-- DataTables CSS -->


</body>


<!-- Mirrored from /demo/html/template/index by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 08 Aug 2025 05:19:55 GMT -->

</html>
