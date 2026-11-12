@php
    $client = Auth::guard('client')->user();
    $menus = $menus ?? collect();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'Client Portal')</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontent/assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/style.css') }}">
    @stack('styles')
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="main-header">
                <div class="header-left">
                    <a href="{{ route('client.dashboard') }}" class="logo">
                        <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                    </a>
                </div>

                <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                    <span class="bar-icon"><span></span><span></span><span></span></span>
                </a>

                <div class="header-user">
                    <div class="nav user-menu nav-list">
                        <div class="me-auto d-flex align-items-center">
                            <a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
                                <i class="ti ti-arrow-bar-to-left"></i>
                            </a>
                        </div>

                        <div class="dropdown profile-dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm online">
                                    <img src="{{ asset('frontent/assets/img/profiles/avatar-12.jpg') }}" alt="Client" class="img-fluid rounded-circle">
                                </span>
                            </a>
                            <div class="dropdown-menu shadow-none">
                                <div class="card mb-0">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ $client?->full_name ?: $client?->company_name }}</h5>
                                        <p class="fs-12 fw-medium mb-0">{{ $client?->email }}</p>
                                    </div>
                                    <div class="card-footer py-1">
                                        <a class="dropdown-item d-inline-flex align-items-center p-0 py-2" href="{{ route('client.logout') }}">
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

        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <a href="{{ route('client.dashboard') }}" class="logo logo-normal">
                    <img src="{{ asset('frontent/assets/img/icons/logo.png') }}" alt="Logo">
                </a>
            </div>

            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>CLIENT PORTAL</span></li>
                        <li>
                            <ul>
                                @forelse ($menus as $menu)
                                    @php
                                        $routeName = $menu->route_name;
                                        $href = $routeName && $routeName !== '#' && Route::has($routeName) ? route($routeName) : '#';
                                        $isActive = $routeName && request()->routeIs($routeName . '*');
                                    @endphp
                                    <li>
                                        <a href="{{ $href }}" class="{{ $isActive ? 'active' : '' }}">
                                            <i class="{{ $menu->icon ?: 'ti ti-circle' }}"></i>
                                            <span>{{ $menu->title }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li>
                                        <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                                            <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('client.tickets.index') }}" class="{{ request()->routeIs('client.tickets.*') ? 'active' : '' }}">
                                            <i class="ti ti-ticket"></i><span>Project Tickets</span>
                                        </a>
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

        @yield('main-section')
    </div>

    <script src="{{ asset('frontent/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('frontent/assets/js/script.js') }}"></script>
    @stack('after_scripts')
</body>

</html>
