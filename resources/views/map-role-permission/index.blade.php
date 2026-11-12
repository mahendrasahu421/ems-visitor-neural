@extends('layout.master')
@section('title', $title ?? 'Role-wise Menu Access Management')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .map-perm-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .map-perm-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .map-perm-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .map-perm-page .kpi-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--card-soft-bg, #eef5ff);
            color: var(--card-accent, #0d6efd);
            flex-shrink: 0;
        }

        /* Role Picker Sidebar */
        .map-perm-page .role-card-item {
            border: 1.5px solid #edf2f7;
            border-radius: 12px;
            background: #ffffff;
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .map-perm-page .role-card-item:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateX(3px);
        }

        .map-perm-page .role-card-item.active {
            border-color: #0d6efd;
            background: #f0f7ff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.12);
        }

        .map-perm-page .role-card-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10px;
            bottom: 10px;
            width: 3.5px;
            background: #0d6efd;
            border-radius: 0 4px 4px 0;
        }

        /* Tree Node Styles */
        .map-perm-page .menu-parent-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            transition: all 0.2s ease;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .map-perm-page .menu-parent-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .map-perm-page .menu-parent-header {
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
        }

        .map-perm-page .menu-parent-card.is-open .menu-parent-header {
            border-bottom-color: #e2e8f0;
            background: #f1f5f9;
        }

        .map-perm-page .menu-children-container {
            padding: 12px 16px 12px 36px;
            background: #ffffff;
        }

        .map-perm-page .submenu-item {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            background: #ffffff;
            margin-bottom: 6px;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .map-perm-page .submenu-item:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .map-perm-page .submenu-item.checked-item {
            background: #f0f7ff;
            border-color: #bfdbfe;
        }

        .map-perm-page .child-item-lvl3 {
            padding: 6px 10px;
            border-radius: 6px;
            background: #f8fafc;
            margin-top: 4px;
            margin-left: 24px;
            border: 1px dashed #e2e8f0;
        }

        .map-perm-page .chevron-rotate {
            transition: transform 0.2s ease;
        }

        .map-perm-page .collapsed .chevron-rotate {
            transform: rotate(-90deg);
        }

        /* Checkbox styling */
        .map-perm-page .form-check-input {
            width: 1.15rem;
            height: 1.15rem;
            cursor: pointer;
        }

        .map-perm-page .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        /* Custom Scrollbar for sidebar */
        .map-perm-page .role-list-scroll {
            max-height: 640px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .map-perm-page .role-list-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .map-perm-page .role-list-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>

    <div class="page-wrapper map-perm-page">
        <div class="content container-fluid">

            {{-- Breadcrumbs & Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-lock-access fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Role-wise Menu Access</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    Navigation Control
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted">Access Control</li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">Menu Permissions</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('role') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-shield fs-15"></i> Manage Roles
                    </a>
                    <a href="{{ route('permission') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-key fs-15"></i> All Permissions
                    </a>
                    <a href="{{ route('role.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-circle-plus fs-15"></i> Add New Role
                    </a>
                </div>
            </div>

            {{-- 4 KPI Metric Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Roles --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #0d6efd; --card-soft-bg: #eff6ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Defined Roles</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $stats['totalRoles'] ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-shield text-primary"></i> Active user roles
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-shield-lock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Menu Items --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #198754; --card-soft-bg: #e8f7ee;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Total Menu Items</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $stats['totalMenus'] ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <span class="text-success fw-medium">{{ $stats['totalTopMenus'] ?? 0 }} Modules</span> &bull; {{ $stats['totalSubMenus'] ?? 0 }} Submenus
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-layout-sidebar"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configured Roles --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #0dcaf0; --card-soft-bg: #e0f8fc;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Configured Roles</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $stats['mappedRoles'] ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-check text-info"></i> Roles with menu access
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-checkbox"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Granular Permissions --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #6f42c1; --card-soft-bg: #f3effc;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Action Permissions</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $stats['totalPermissions'] ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-key text-purple"></i> Spatie permission rules
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-lock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main 2-Column Split Workspace --}}
            <div class="row g-4">

                {{-- Left Column: Role Selector Sidebar --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="card-title mb-0 fs-15 fw-bold text-dark">
                                    <i class="ti ti-users me-1 text-primary"></i> Select Role to Configure
                                </h5>
                                <span class="badge bg-light text-muted border px-2 py-1 fs-11">
                                    {{ $roles->count() }} Roles
                                </span>
                            </div>
                            {{-- Role Search Box --}}
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="ti ti-search fs-14"></i>
                                </span>
                                <input type="text" id="roleFilterInput" class="form-control bg-light border-start-0 fs-13" placeholder="Search roles by name...">
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div class="role-list-scroll d-flex flex-column gap-2" id="roleListContainer">
                                @forelse ($roles as $role)
                                    @php
                                        $assignedMenuCount = $role->menus->count();
                                        $assignedUserCount = $userCounts[$role->id] ?? 0;
                                        $roleIcon = match (strtolower($role->name)) {
                                            'super admin', 'superadmin' => 'ti-shield-lock text-danger',
                                            'admin' => 'ti-user-shield text-primary',
                                            'hr' => 'ti-user-heart text-info',
                                            'employee' => 'ti-user-check text-success',
                                            'client' => 'ti-briefcase text-warning',
                                            default => 'ti-shield text-secondary'
                                        };
                                        $isSelected = ($selectedRoleId === $role->id);
                                    @endphp
                                    <div class="role-card-item {{ $isSelected ? 'active' : '' }}"
                                         data-role-id="{{ $role->id }}"
                                         data-role-name="{{ ucfirst($role->name) }}"
                                         data-guard-name="{{ $role->guard_name }}"
                                         data-user-count="{{ $assignedUserCount }}"
                                         data-perm-count="{{ $role->permissions->count() }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded-circle bg-white border d-inline-flex align-items-center justify-content-center shadow-2xs">
                                                    <i class="ti {{ $roleIcon }} fs-14"></i>
                                                </span>
                                                <div>
                                                    <h6 class="mb-0 fw-bold fs-13 text-dark role-name-text">{{ ucfirst($role->name) }}</h6>
                                                    <span class="text-muted fs-11">Guard: <span class="badge bg-light text-muted border px-1 py-0 fs-10">{{ $role->guard_name }}</span></span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $assignedMenuCount > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border' }} rounded-pill px-2 py-0.5 fs-11 role-menu-count-badge" id="roleBadge-{{ $role->id }}">
                                                    <i class="ti ti-menu-2 me-0.5"></i>{{ $assignedMenuCount }} Menus
                                                </span>
                                                <div class="text-muted fs-10 mt-1">
                                                    <i class="ti ti-users me-0.5"></i>{{ $assignedUserCount }} Users
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="ti ti-alert-circle fs-28 d-block mb-1"></i>
                                        No roles found. <a href="{{ route('role.create') }}">Create a role</a> first.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-footer bg-light border-top py-2 px-3 d-flex align-items-center justify-content-between fs-12 text-muted">
                            <span><i class="ti ti-info-circle me-1"></i>Click role to switch tree</span>
                            <a href="{{ route('role.create') }}" class="text-primary fw-semibold text-decoration-none hover-underline">
                                <i class="ti ti-plus me-0.5"></i>New Role
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Interactive Menu Access Tree --}}
                <div class="col-xl-8 col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <form id="menuMappingForm" method="POST" action="{{ route('map-role-permission.store') }}">
                            @csrf
                            <input type="hidden" name="role_id" id="formRoleId" value="{{ $selectedRoleId }}">

                            {{-- Tree Top Header & Toolbar --}}
                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-md-flex d-block align-items-center justify-content-between gap-3 mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                                        <div class="avatar avatar-sm rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center">
                                            <i class="ti ti-shield-check fs-16"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="mb-0 fw-bold text-dark fs-16">
                                                    Permissions for: <span id="currentRoleTitle" class="text-primary">{{ ucfirst(optional($selectedRole)->name ?? 'Selected Role') }}</span>
                                                </h5>
                                                <span class="badge bg-light text-muted border px-2 py-0.5 fs-11" id="currentRoleGuard">
                                                    {{ optional($selectedRole)->guard_name ?? 'web' }}
                                                </span>
                                            </div>
                                            <span class="text-muted fs-12">
                                                Select which sidebar navigations and pages this role is allowed to view.
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Selected Counter Badge --}}
                                    <div class="text-md-end">
                                        <span class="badge bg-primary text-white px-3 py-1.5 rounded-pill fs-12 shadow-sm">
                                            <i class="ti ti-checkbox me-1"></i>
                                            <span id="selectedCountDisplay">0</span> / <span id="totalCountDisplay">{{ $stats['totalMenus'] }}</span> Menus Active
                                        </span>
                                    </div>
                                </div>

                                {{-- Quick Filter & Action Toolbar --}}
                                <div class="row g-2 align-items-center pt-2 border-top">
                                    <div class="col-md-5">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0 text-muted">
                                                <i class="ti ti-search fs-14"></i>
                                            </span>
                                            <input type="text" id="menuTreeSearch" class="form-control bg-light border-start-0 fs-13" placeholder="Filter menus (e.g. Invoices, Leave, Payroll)...">
                                            <button class="btn btn-outline-light border text-muted" type="button" id="clearMenuSearch" title="Clear search">
                                                <i class="ti ti-x fs-13"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-7 d-flex align-items-center justify-content-md-end gap-1.5 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" id="btnSelectAll">
                                            <i class="ti ti-checks fs-14"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" id="btnDeselectAll">
                                            <i class="ti ti-square fs-14"></i> Deselect All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light border text-dark d-inline-flex align-items-center gap-1" id="btnExpandAll" title="Expand all sections">
                                            <i class="ti ti-chevrons-down fs-14"></i> Expand
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light border text-dark d-inline-flex align-items-center gap-1" id="btnCollapseAll" title="Collapse all sections">
                                            <i class="ti ti-chevrons-up fs-14"></i> Collapse
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tree Body Container --}}
                            <div class="card-body p-3 p-md-4" style="background-color: #fafbfc; min-height: 480px;">
                                <div id="menuTreeContainer">
                                    @forelse ($menus as $menu)
                                        @php
                                            $childrenCount = $menu->children->count();
                                            $menuIcon = $menu->icon ?: 'ti ti-folder';
                                            if (!str_contains($menuIcon, 'ti-') && !str_contains($menuIcon, 'fa-') && !str_contains($menuIcon, 'feather-')) {
                                                $menuIcon = 'ti ti-' . $menuIcon;
                                            }
                                        @endphp

                                        <div class="menu-parent-card" data-parent-id="{{ $menu->id }}">
                                            {{-- Parent Menu Header --}}
                                            <div class="menu-parent-header" data-bs-toggle="collapse" data-bs-target="#menuCollapse-{{ $menu->id }}" aria-expanded="true">
                                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                    {{-- Checkbox --}}
                                                    <div class="form-check m-0" onclick="event.stopPropagation();">
                                                        <input class="form-check-input parent-menu-checkbox"
                                                               type="checkbox"
                                                               name="menu_ids[]"
                                                               id="menu-{{ $menu->id }}"
                                                               value="{{ $menu->id }}"
                                                               data-menu-id="{{ $menu->id }}">
                                                    </div>

                                                    {{-- Icon & Title --}}
                                                    <label class="form-check-label fw-bold text-dark fs-14 d-flex align-items-center gap-2 mb-0"
                                                           for="menu-{{ $menu->id }}"
                                                           onclick="event.stopPropagation();">
                                                        <span class="avatar avatar-2xs rounded bg-white border text-primary d-inline-flex align-items-center justify-content-center">
                                                            <i class="{{ $menuIcon }} fs-14"></i>
                                                        </span>
                                                        <span class="menu-search-target">{{ $menu->title }}</span>
                                                    </label>

                                                    {{-- Route Badge (if any) --}}
                                                    @if ($menu->route)
                                                        <span class="badge bg-light text-muted border px-1.5 py-0.5 fs-10 font-monospace">
                                                            {{ $menu->route }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($childrenCount > 0)
                                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 fs-11">
                                                            {{ $childrenCount }} {{ $childrenCount === 1 ? 'Submenu' : 'Submenus' }}
                                                        </span>
                                                        <i class="ti ti-chevron-down text-muted fs-15 chevron-rotate"></i>
                                                    @else
                                                        <span class="badge bg-light text-muted border px-1.5 py-0.5 fs-10">Single Page</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Submenus Container --}}
                                            @if ($childrenCount > 0)
                                                <div class="collapse show" id="menuCollapse-{{ $menu->id }}">
                                                    <div class="menu-children-container">
                                                        @foreach ($menu->children as $submenu)
                                                            @php
                                                                $level3Count = $submenu->children->count();
                                                            @endphp
                                                            <div class="submenu-item" data-submenu-id="{{ $submenu->id }}">
                                                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                                    <div class="form-check m-0">
                                                                        <input class="form-check-input submenu-checkbox"
                                                                               type="checkbox"
                                                                               name="menu_ids[]"
                                                                               id="menu-{{ $submenu->id }}"
                                                                               value="{{ $submenu->id }}"
                                                                               data-parent-id="{{ $menu->id }}"
                                                                               data-menu-id="{{ $submenu->id }}">
                                                                    </div>
                                                                    <label class="form-check-label fs-13 text-dark mb-0 fw-medium menu-search-target"
                                                                           for="menu-{{ $submenu->id }}">
                                                                        <i class="ti ti-corner-down-right text-muted me-1 fs-12"></i>
                                                                        {{ $submenu->title }}
                                                                    </label>
                                                                    @if ($submenu->route)
                                                                        <span class="badge bg-light text-muted border px-1.5 py-0.5 fs-10 font-monospace">
                                                                            {{ $submenu->route }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                @if ($level3Count > 0)
                                                                    <span class="badge bg-light text-muted border px-1.5 py-0.5 fs-10">
                                                                        {{ $level3Count }} items
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            {{-- Level 3 Deep Submenus (if present) --}}
                                                            @if ($level3Count > 0)
                                                                <div class="mb-2">
                                                                    @foreach ($submenu->children as $child)
                                                                        <div class="child-item-lvl3 d-flex align-items-center justify-content-between">
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <div class="form-check m-0">
                                                                                    <input class="form-check-input child-checkbox"
                                                                                           type="checkbox"
                                                                                           name="menu_ids[]"
                                                                                           id="menu-{{ $child->id }}"
                                                                                           value="{{ $child->id }}"
                                                                                           data-parent-id="{{ $submenu->id }}"
                                                                                           data-root-parent-id="{{ $menu->id }}"
                                                                                           data-menu-id="{{ $child->id }}">
                                                                                </div>
                                                                                <label class="form-check-label fs-12 text-muted mb-0 menu-search-target"
                                                                                       for="menu-{{ $child->id }}">
                                                                                    {{ $child->title }}
                                                                                </label>
                                                                            </div>
                                                                            @if ($child->route)
                                                                                <span class="badge bg-light text-muted border px-1 py-0 fs-10 font-monospace">
                                                                                    {{ $child->route }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-5 bg-white rounded border">
                                            <i class="ti ti-layout-sidebar-inactive fs-36 text-muted mb-2 d-block"></i>
                                            <h6 class="fw-bold text-dark">No Navigation Menus Found</h6>
                                            <p class="text-muted fs-13 mb-0">Please seed or register menus in the system database.</p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- No Search Results Alert --}}
                                <div id="noSearchAlert" class="text-center py-5 bg-white rounded border d-none">
                                    <i class="ti ti-search-off fs-32 text-muted mb-2 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">No Matching Menus Found</h6>
                                    <p class="text-muted fs-13 mb-0">Try searching with a different term.</p>
                                </div>
                            </div>

                            {{-- Sticky Action Footer --}}
                            <div class="card-footer bg-white border-top py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 sticky-bottom shadow-xs">
                                <div class="d-flex align-items-center gap-2 text-muted fs-12">
                                    <i class="ti ti-info-circle text-primary fs-16"></i>
                                    <span>Changes apply immediately to this role upon saving.</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnResetChanges">
                                        <i class="ti ti-refresh fs-14 me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm d-inline-flex align-items-center gap-1.5" id="btnSavePermissions">
                                        <i class="ti ti-check fs-15"></i>
                                        <span id="btnSaveText">Save Menu Permissions</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="btnSaveSpinner" role="status"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

    @push('after_scripts')
    {{-- SweetAlert2 CDN if not present --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data maps from backend
            const roleMenuMap = @json($roleMenuMap);
            let activeRoleId = parseInt("{{ $selectedRoleId }}") || null;

            const form = document.getElementById('menuMappingForm');
            const formRoleIdInput = document.getElementById('formRoleId');
            const currentRoleTitle = document.getElementById('currentRoleTitle');
            const currentRoleGuard = document.getElementById('currentRoleGuard');
            const selectedCountDisplay = document.getElementById('selectedCountDisplay');
            const allCheckboxes = document.querySelectorAll('#menuTreeContainer input[type="checkbox"]');
            const totalCheckboxesCount = allCheckboxes.length;

            // 1. Function to apply role selection to checkboxes
            function loadRolePermissions(roleId) {
                activeRoleId = parseInt(roleId);
                formRoleIdInput.value = activeRoleId;

                const assignedMenus = (roleMenuMap[activeRoleId] || []).map(Number);

                allCheckboxes.forEach(cb => {
                    const menuId = parseInt(cb.value);
                    const isChecked = assignedMenus.includes(menuId);
                    cb.checked = isChecked;

                    // Update parent card styling if needed
                    updateItemHighlight(cb);
                });

                updateSelectionCount();
            }

            // 2. Update visual count badge
            function updateSelectionCount() {
                const checkedCount = document.querySelectorAll('#menuTreeContainer input[type="checkbox"]:checked').length;
                if (selectedCountDisplay) {
                    selectedCountDisplay.textContent = checkedCount;
                }
            }

            // 3. Highlight checked submenu item
            function updateItemHighlight(checkbox) {
                const submenuItem = checkbox.closest('.submenu-item');
                if (submenuItem) {
                    if (checkbox.checked) {
                        submenuItem.classList.add('checked-item');
                    } else {
                        submenuItem.classList.remove('checked-item');
                    }
                }
            }

            // 4. Role list card clicking & active state switching
            const roleCards = document.querySelectorAll('.role-card-item');
            roleCards.forEach(card => {
                card.addEventListener('click', function() {
                    const roleId = parseInt(this.getAttribute('data-role-id'));
                    const roleName = this.getAttribute('data-role-name');
                    const guardName = this.getAttribute('data-guard-name');

                    roleCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    if (currentRoleTitle) currentRoleTitle.textContent = roleName;
                    if (currentRoleGuard) currentRoleGuard.textContent = guardName;

                    loadRolePermissions(roleId);
                });
            });

            // 5. Parent-Child Checkbox cascading synchronization
            // A. Top-level parent checkbox clicked
            document.querySelectorAll('.parent-menu-checkbox').forEach(parentCb => {
                parentCb.addEventListener('change', function() {
                    const parentCard = this.closest('.menu-parent-card');
                    if (parentCard) {
                        const childCheckboxes = parentCard.querySelectorAll('.submenu-checkbox, .child-checkbox');
                        childCheckboxes.forEach(child => {
                            child.checked = parentCb.checked;
                            updateItemHighlight(child);
                        });
                    }
                    updateSelectionCount();
                });
            });

            // B. Level 2 Submenu checkbox clicked
            document.querySelectorAll('.submenu-checkbox').forEach(subCb => {
                subCb.addEventListener('change', function() {
                    updateItemHighlight(this);

                    const parentCard = this.closest('.menu-parent-card');
                    if (parentCard) {
                        const parentCb = parentCard.querySelector('.parent-menu-checkbox');
                        if (this.checked && parentCb) {
                            parentCb.checked = true; // Auto-check parent if child is checked
                        }

                        // Check Level 3 children under this submenu if any
                        const nextSiblings = this.closest('.submenu-item').nextElementSibling;
                        if (nextSiblings && nextSiblings.classList.contains('mb-2')) {
                            nextSiblings.querySelectorAll('.child-checkbox').forEach(c => {
                                c.checked = subCb.checked;
                            });
                        }
                    }
                    updateSelectionCount();
                });
            });

            // C. Level 3 Child checkbox clicked
            document.querySelectorAll('.child-checkbox').forEach(childCb => {
                childCb.addEventListener('change', function() {
                    if (this.checked) {
                        const parentCard = this.closest('.menu-parent-card');
                        if (parentCard) {
                            const parentCb = parentCard.querySelector('.parent-menu-checkbox');
                            if (parentCb) parentCb.checked = true;
                        }
                        const parentSubmenuId = this.getAttribute('data-parent-id');
                        const submenuCb = document.querySelector(`.submenu-checkbox[data-menu-id="${parentSubmenuId}"]`);
                        if (submenuCb) {
                            submenuCb.checked = true;
                            updateItemHighlight(submenuCb);
                        }
                    }
                    updateSelectionCount();
                });
            });

            // 6. Select All / Deselect All Toolbar Buttons
            document.getElementById('btnSelectAll')?.addEventListener('click', function() {
                allCheckboxes.forEach(cb => {
                    cb.checked = true;
                    updateItemHighlight(cb);
                });
                updateSelectionCount();
            });

            document.getElementById('btnDeselectAll')?.addEventListener('click', function() {
                allCheckboxes.forEach(cb => {
                    cb.checked = false;
                    updateItemHighlight(cb);
                });
                updateSelectionCount();
            });

            // 7. Expand All / Collapse All
            document.getElementById('btnExpandAll')?.addEventListener('click', function() {
                document.querySelectorAll('#menuTreeContainer .collapse').forEach(el => {
                    bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).show();
                });
            });

            document.getElementById('btnCollapseAll')?.addEventListener('click', function() {
                document.querySelectorAll('#menuTreeContainer .collapse').forEach(el => {
                    bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).hide();
                });
            });

            // 8. Reset changes button
            document.getElementById('btnResetChanges')?.addEventListener('click', function() {
                if (activeRoleId) {
                    loadRolePermissions(activeRoleId);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: 'Selection reset to saved state',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });

            // 9. Live Search in Menu Tree
            const menuTreeSearch = document.getElementById('menuTreeSearch');
            const noSearchAlert = document.getElementById('noSearchAlert');
            const clearMenuSearch = document.getElementById('clearMenuSearch');

            function filterMenuTree(query) {
                const term = query.trim().toLowerCase();
                let visibleParentCount = 0;

                document.querySelectorAll('.menu-parent-card').forEach(card => {
                    const targets = card.querySelectorAll('.menu-search-target');
                    let matchesCard = false;

                    targets.forEach(target => {
                        const text = target.textContent.toLowerCase();
                        if (text.includes(term)) {
                            matchesCard = true;
                            // Highlight or show item
                            const subItem = target.closest('.submenu-item') || target.closest('.child-item-lvl3');
                            if (subItem) subItem.style.display = '';
                        } else if (term !== '') {
                            const subItem = target.closest('.submenu-item') || target.closest('.child-item-lvl3');
                            if (subItem && !subItem.querySelector('.menu-search-target').textContent.toLowerCase().includes(term)) {
                                subItem.style.display = 'none';
                            }
                        } else {
                            const subItem = target.closest('.submenu-item') || target.closest('.child-item-lvl3');
                            if (subItem) subItem.style.display = '';
                        }
                    });

                    if (term === '' || matchesCard) {
                        card.style.display = '';
                        visibleParentCount++;
                        if (term !== '') {
                            // Expand collapse if searching
                            const collapseEl = card.querySelector('.collapse');
                            if (collapseEl) {
                                bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }).show();
                            }
                        }
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noSearchAlert) {
                    noSearchAlert.classList.toggle('d-none', visibleParentCount > 0 || term === '');
                }
            }

            menuTreeSearch?.addEventListener('input', function() {
                filterMenuTree(this.value);
            });

            clearMenuSearch?.addEventListener('click', function() {
                if (menuTreeSearch) {
                    menuTreeSearch.value = '';
                    filterMenuTree('');
                    menuTreeSearch.focus();
                }
            });

            // 10. Role Sidebar Filter
            const roleFilterInput = document.getElementById('roleFilterInput');
            roleFilterInput?.addEventListener('input', function() {
                const term = this.value.trim().toLowerCase();
                document.querySelectorAll('#roleListContainer .role-card-item').forEach(item => {
                    const name = item.querySelector('.role-name-text')?.textContent.toLowerCase() || '';
                    item.style.display = (term === '' || name.includes(term)) ? '' : 'none';
                });
            });

            // 11. Form AJAX submission with SweetAlert2
            form?.addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = document.getElementById('btnSavePermissions');
                const btnText = document.getElementById('btnSaveText');
                const spinner = document.getElementById('btnSaveSpinner');

                if (btn) btn.disabled = true;
                if (spinner) spinner.classList.remove('d-none');
                if (btnText) btnText.textContent = 'Saving...';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        // Update roleMenuMap locally
                        roleMenuMap[data.role_id] = data.menu_ids;

                        // Update sidebar badge
                        const badge = document.getElementById(`roleBadge-${data.role_id}`);
                        if (badge) {
                            badge.innerHTML = `<i class="ti ti-menu-2 me-0.5"></i>${data.assigned_count} Menus`;
                            if (data.assigned_count > 0) {
                                badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-11 role-menu-count-badge';
                            } else {
                                badge.className = 'badge bg-light text-muted border rounded-pill px-2 py-0.5 fs-11 role-menu-count-badge';
                            }
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Permissions Saved!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to save menu permissions.'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    // Fallback to regular form submission if AJAX fails
                    form.submit();
                })
                .finally(() => {
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.classList.add('d-none');
                    if (btnText) btnText.textContent = 'Save Menu Permissions';
                });
            });

            // Initial load of selected role permissions
            if (activeRoleId) {
                loadRolePermissions(activeRoleId);
            }
        });
    </script>
    @endpush
@endsection

