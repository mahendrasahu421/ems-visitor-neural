@extends('layout.master')
@section('title', $title ?? 'User Account')

@section('main-section')
    @php
        $isEdit = $user->exists;
        $selectedPermissions = old('permissions', $selectedPermissions ?? []);
        $currentRoleId = (int) old('role_id', $selectedRoleId ?? 0);
    @endphp

    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .user-form-page .form-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .user-form-page .form-card .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 20px;
        }

        .user-form-page .form-card .card-body {
            padding: 20px;
        }

        .user-form-page .input-group-icon-text {
            background-color: #f8fafc;
            border-right: 0;
            color: #64748b;
        }

        .user-form-page .input-group .form-control {
            border-left: 0;
        }

        .user-form-page .input-group .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .user-form-page .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-radius: 0.375rem;
        }

        .user-form-page .input-group:focus-within .input-group-text,
        .user-form-page .input-group:focus-within .form-control {
            border-color: #86b7fe;
        }

        .user-form-page .status-selector {
            display: flex;
            gap: 12px;
        }

        .user-form-page .status-option {
            flex: 1;
            position: relative;
        }

        .user-form-page .status-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .user-form-page .status-card {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 13px;
            color: #64748b;
            background: #f8fafc;
        }

        .user-form-page .status-option input[type="radio"]:checked + .status-card.status-active {
            border-color: #198754;
            background: #e8f7ee;
            color: #198754;
        }

        .user-form-page .status-option input[type="radio"]:checked + .status-card.status-inactive {
            border-color: #6c757d;
            background: #f1f5f9;
            color: #475569;
        }

        /* Module Permission Card */
        .user-form-page .module-perm-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            margin-bottom: 14px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .user-form-page .module-perm-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .user-form-page .module-perm-header {
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-form-page .perm-checkbox-item {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            background: #ffffff;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-form-page .perm-checkbox-item:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .user-form-page .perm-checkbox-item.is-selected {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .user-form-page .perm-checkbox-item.is-role-inherited {
            background: #fdf4ff;
            border-color: #f0abfc;
        }

        .user-form-page .avatar-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
    </style>

    <div class="page-wrapper user-form-page">
        <div class="content container-fluid">

            {{-- Breadcrumbs & Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-white border rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ti ti-arrow-left fs-16 text-dark"></i>
                        </a>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">{{ $title }}</h3>
                                <span class="badge {{ $isEdit ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary' }} rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $isEdit ? 'Update Account' : 'New User Setup' }}
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-muted">Admin Users</a></li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $isEdit ? 'Edit User' : 'Create User' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-users fs-15"></i> All Users
                    </a>
                    <a href="{{ route('role') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-shield fs-15"></i> Manage Roles
                    </a>
                </div>
            </div>

            {{-- Validation Errors Summary --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ti ti-alert-triangle fs-20 text-danger mt-0.5"></i>
                        <div>
                            <strong class="d-block mb-1">Please fix the following issues:</strong>
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

            <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" id="userForm">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    {{-- Left Column: User Account Information & Role --}}
                    <div class="col-xl-5 col-lg-6">

                        {{-- Profile Header Card --}}
                        <div class="form-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-placeholder" id="avatarPreview">
                                        {{ strtoupper(substr($user->name ?: 'U', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0 fw-bold text-dark" id="displayUserName">{{ $user->name ?: 'New User' }}</h5>
                                        <p class="text-muted fs-12 mb-0" id="displayUserEmail">{{ $user->email ?: 'user@domain.com' }}</p>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10 px-2 py-0.5" id="displayUserRole">
                                                {{ $user->roles->first()?->name ? ucfirst($user->roles->first()->name) : 'No Role Selected' }}
                                            </span>
                                            <span class="badge bg-light text-muted border fs-10 px-2 py-0.5" id="displayUserStatus">
                                                {{ (int) ($user->status ?? 1) === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Primary Details Card --}}
                        <div class="form-card">
                            <div class="card-header">
                                <h5 class="mb-0 fw-bold text-dark fs-15">
                                    <i class="ti ti-id me-1.5 text-primary"></i> Personal Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    {{-- Full Name --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark fs-13">Full Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text input-group-icon-text">
                                                <i class="ti ti-user fs-16"></i>
                                            </span>
                                            <input type="text" name="name" id="nameInput" class="form-control fs-13 @error('name') is-invalid @enderror"
                                                   value="{{ old('name', $user->name) }}" placeholder="e.g. John Doe" required autofocus>
                                        </div>
                                        @error('name')
                                            <div class="text-danger fs-11 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email Address --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark fs-13">Email Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text input-group-icon-text">
                                                <i class="ti ti-mail fs-16"></i>
                                            </span>
                                            <input type="email" name="email" id="emailInput" class="form-control fs-13 @error('email') is-invalid @enderror"
                                                   value="{{ old('email', $user->email) }}" placeholder="e.g. john@company.com" required>
                                        </div>
                                        @error('email')
                                            <div class="text-danger fs-11 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Phone Number --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark fs-13">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text input-group-icon-text">
                                                <i class="ti ti-phone fs-16"></i>
                                            </span>
                                            <input type="text" name="phone" id="phoneInput" class="form-control fs-13 @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone', $user->phone) }}" placeholder="e.g. +91 98765 43210">
                                        </div>
                                        @error('phone')
                                            <div class="text-danger fs-11 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Status Selection --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark fs-13">Account Status <span class="text-danger">*</span></label>
                                        <div class="status-selector">
                                            <label class="status-option">
                                                <input type="radio" name="status" value="1" {{ (string) old('status', $user->status ?? 1) === '1' ? 'checked' : '' }} class="status-radio">
                                                <div class="status-card status-active">
                                                    <i class="ti ti-circle-check fs-16"></i>
                                                    <span>Active</span>
                                                </div>
                                            </label>
                                            <label class="status-option">
                                                <input type="radio" name="status" value="0" {{ (string) old('status', $user->status ?? 1) === '0' ? 'checked' : '' }} class="status-radio">
                                                <div class="status-card status-inactive">
                                                    <i class="ti ti-circle-x fs-16"></i>
                                                    <span>Inactive</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Role Assignment Card --}}
                        <div class="form-card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 fw-bold text-dark fs-15">
                                    <i class="ti ti-shield-lock me-1.5 text-primary"></i> Assign System Role <span class="text-danger">*</span>
                                </h5>
                                <a href="{{ route('role.create') }}" class="btn btn-outline-primary btn-2xs d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-plus fs-12"></i> New Role
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark fs-13">Primary Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-group-icon-text">
                                            <i class="ti ti-shield fs-16"></i>
                                        </span>
                                        <select name="role_id" id="roleSelect" class="form-select fs-13 @error('role_id') is-invalid @enderror" required>
                                            <option value="">-- Choose Role --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                        data-perm-count="{{ $role->permissions->count() }}"
                                                        data-guard="{{ $role->guard_name }}"
                                                        {{ $currentRoleId === $role->id ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }} ({{ $role->permissions->count() }} permissions)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role_id')
                                        <div class="text-danger fs-11 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Role Info Callout --}}
                                <div class="p-3 bg-light rounded-3 border fs-12 text-muted" id="roleInfoBox">
                                    <div class="d-flex align-items-center gap-1.5 text-dark fw-semibold mb-1">
                                        <i class="ti ti-info-circle text-primary fs-15"></i>
                                        <span id="roleInfoName">Select a role above</span>
                                    </div>
                                    <span id="roleInfoDesc">Assigning a role automatically grants all permissions bundled inside that role to this user.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Security Credentials Card --}}
                        <div class="form-card">
                            <div class="card-header">
                                <h5 class="mb-0 fw-bold text-dark fs-15">
                                    <i class="ti ti-lock me-1.5 text-primary"></i> Security & Password
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($isEdit)
                                    <div class="alert alert-info-subtle border-0 text-dark fs-12 p-2.5 rounded-3 mb-3 d-flex align-items-center gap-2">
                                        <i class="ti ti-info-circle text-info fs-16"></i>
                                        <span>Leave password blank to keep current password unchanged.</span>
                                    </div>
                                @endif

                                <div class="row g-3">
                                    {{-- Password --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark fs-13">
                                            {{ $isEdit ? 'New Password (Optional)' : 'Password' }} <span class="{{ $isEdit ? 'd-none' : 'text-danger' }}">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text input-group-icon-text">
                                                <i class="ti ti-key fs-16"></i>
                                            </span>
                                            <input type="password" name="password" id="passwordInput" class="form-control fs-13 @error('password') is-invalid @enderror"
                                                   placeholder="At least 6 characters" {{ $isEdit ? '' : 'required' }}>
                                            <button class="btn btn-outline-light border text-muted toggle-password" type="button" data-target="passwordInput">
                                                <i class="ti ti-eye fs-15"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="text-danger fs-11 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark fs-13">
                                            Confirm Password <span class="{{ $isEdit ? 'd-none' : 'text-danger' }}">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text input-group-icon-text">
                                                <i class="ti ti-lock-check fs-16"></i>
                                            </span>
                                            <input type="password" name="password_confirmation" id="passwordConfirmInput" class="form-control fs-13"
                                                   placeholder="Re-enter password" {{ $isEdit ? '' : 'required' }}>
                                            <button class="btn btn-outline-light border text-muted toggle-password" type="button" data-target="passwordConfirmInput">
                                                <i class="ti ti-eye fs-15"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right Column: Direct Permission Overrides Matrix --}}
                    <div class="col-xl-7 col-lg-6">
                        <div class="form-card">
                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-md-flex d-block align-items-center justify-content-between gap-2 mb-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h5 class="mb-0 fw-bold text-dark fs-15">
                                                <i class="ti ti-adjustments-check me-1 text-primary"></i> Direct User Permissions
                                            </h5>
                                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-0.5 fs-11" id="directPermCounter">
                                                {{ count($selectedPermissions) }} Direct
                                            </span>
                                        </div>
                                        <p class="text-muted fs-12 mb-0 mt-0.5">
                                            Direct permissions override and extend the user's role-based access.
                                        </p>
                                    </div>

                                    <div class="d-flex align-items-center gap-1.5 mt-2 mt-md-0">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAllPerms">
                                            <i class="ti ti-checks fs-13"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAllPerms">
                                            <i class="ti ti-square fs-13"></i> Clear All
                                        </button>
                                    </div>
                                </div>

                                {{-- Search Box --}}
                                <div class="input-group input-group-sm mt-2">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-search fs-14"></i>
                                    </span>
                                    <input type="text" id="permissionSearchInput" class="form-control bg-light border-start-0 fs-13" placeholder="Filter permissions (e.g. employee, invoice, payroll)...">
                                    <button class="btn btn-outline-light border text-muted" type="button" id="clearPermSearch">
                                        <i class="ti ti-x fs-13"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-3" style="max-height: 750px; overflow-y: auto;">
                                <div id="permissionModulesContainer">
                                    @forelse ($groupedPermissions as $moduleName => $perms)
                                        @php
                                            $moduleSlug = \Illuminate\Support\Str::slug($moduleName);
                                            $moduleIcon = match ($moduleName) {
                                                'Dashboard' => 'ti-layout-dashboard text-primary',
                                                'User Management' => 'ti-users text-info',
                                                'Employee Management' => 'ti-user-circle text-success',
                                                'Attendance Tracking' => 'ti-calendar-time text-warning',
                                                'Leaves & Holidays' => 'ti-calendar-event text-danger',
                                                'Payroll & Finance' => 'ti-receipt-2 text-primary',
                                                'Invoicing & Payments' => 'ti-file-invoice text-success',
                                                'Clients & Tickets' => 'ti-briefcase text-info',
                                                'Projects & Tasks' => 'ti-subtask text-purple',
                                                'Recruitment & Jobs' => 'ti-user-plus text-warning',
                                                'Settings & System' => 'ti-settings text-secondary',
                                                'Access Control' => 'ti-lock text-danger',
                                                default => 'ti-apps text-secondary'
                                            };
                                        @endphp

                                        <div class="module-perm-card" data-module="{{ $moduleSlug }}">
                                            {{-- Module Header --}}
                                            <div class="module-perm-header">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="avatar avatar-xs rounded bg-white border d-inline-flex align-items-center justify-content-center shadow-2xs">
                                                        <i class="ti {{ $moduleIcon }} fs-14"></i>
                                                    </span>
                                                    <h6 class="mb-0 fw-bold fs-13 text-dark module-title-text">{{ $moduleName }}</h6>
                                                    <span class="badge bg-light text-muted border px-1.5 py-0 fs-10">{{ $perms->count() }}</span>
                                                </div>

                                                <button type="button" class="btn btn-outline-light border btn-2xs text-dark select-module-btn" data-module-target="{{ $moduleSlug }}">
                                                    Toggle Module
                                                </button>
                                            </div>

                                            {{-- Permissions Grid --}}
                                            <div class="p-3">
                                                <div class="row g-2">
                                                    @foreach ($perms as $perm)
                                                        @php
                                                            $isChecked = in_array($perm->name, $selectedPermissions, true);
                                                        @endphp
                                                        <div class="col-md-6 perm-item-col" data-perm-name="{{ strtolower($perm->name) }}">
                                                            <div class="perm-checkbox-item {{ $isChecked ? 'is-selected' : '' }}" id="permWrapper-{{ $perm->id }}">
                                                                <div class="form-check m-0 d-flex align-items-center gap-2">
                                                                    <input class="form-check-input direct-perm-checkbox"
                                                                           type="checkbox"
                                                                           name="permissions[]"
                                                                           id="perm_{{ $perm->id }}"
                                                                           value="{{ $perm->name }}"
                                                                           data-perm-name="{{ $perm->name }}"
                                                                           {{ $isChecked ? 'checked' : '' }}>
                                                                    <label class="form-check-label fs-12 text-dark mb-0 fw-medium perm-label-text" for="perm_{{ $perm->id }}">
                                                                        {{ $perm->name }}
                                                                    </label>
                                                                </div>

                                                                {{-- Role Inherited Pill --}}
                                                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle fs-10 px-1.5 py-0 d-none role-inherited-badge"
                                                                      id="inheritedBadge-{{ $perm->id }}"
                                                                      title="Granted by selected role">
                                                                    <i class="ti ti-shield-check me-0.5"></i>Role
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">
                                            <i class="ti ti-lock-off fs-28 d-block mb-1"></i>
                                            No permissions available.
                                        </div>
                                    @endforelse
                                </div>

                                {{-- No search match alert --}}
                                <div id="noPermSearchMatch" class="text-center py-4 bg-white rounded border d-none">
                                    <i class="ti ti-search-off fs-24 text-muted mb-1 d-block"></i>
                                    <p class="text-muted fs-13 mb-0">No permissions match your search term.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Sticky Form Submission Footer --}}
                        <div class="d-flex align-items-center justify-content-between p-3 bg-white border rounded-3 shadow-xs sticky-bottom">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                                <i class="ti ti-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm d-inline-flex align-items-center gap-1.5">
                                <i class="ti ti-check fs-15"></i>
                                <span>{{ $isEdit ? 'Update User Account' : 'Create User Account' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('after_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rolePermissionsMap = @json($rolePermissionsMap ?? []);
            const roleSelect = document.getElementById('roleSelect');
            const roleInfoName = document.getElementById('roleInfoName');
            const roleInfoDesc = document.getElementById('roleInfoDesc');
            const displayUserRole = document.getElementById('displayUserRole');
            const displayUserName = document.getElementById('displayUserName');
            const displayUserEmail = document.getElementById('displayUserEmail');
            const displayUserStatus = document.getElementById('displayUserStatus');
            const avatarPreview = document.getElementById('avatarPreview');
            const nameInput = document.getElementById('nameInput');
            const emailInput = document.getElementById('emailInput');
            const directPermCounter = document.getElementById('directPermCounter');
            const allPermCheckboxes = document.querySelectorAll('.direct-perm-checkbox');

            // 1. Live Name & Email Typing updates in header card
            nameInput?.addEventListener('input', function() {
                const val = this.value.trim();
                if (displayUserName) displayUserName.textContent = val || 'New User';
                if (avatarPreview) avatarPreview.textContent = (val ? val.charAt(0) : 'U').toUpperCase();
            });

            emailInput?.addEventListener('input', function() {
                const val = this.value.trim();
                if (displayUserEmail) displayUserEmail.textContent = val || 'user@domain.com';
            });

            // 2. Status Radios
            document.querySelectorAll('.status-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (displayUserStatus) {
                        displayUserStatus.textContent = this.value === '1' ? 'Active' : 'Inactive';
                        displayUserStatus.className = this.value === '1' ? 'badge bg-success-subtle text-success border fs-10 px-2 py-0.5' : 'badge bg-light text-muted border fs-10 px-2 py-0.5';
                    }
                });
            });

            // 3. Password Eye Toggle
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    if (input) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('ti-eye');
                            icon.classList.add('ti-eye-off');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('ti-eye-off');
                            icon.classList.add('ti-eye');
                        }
                    }
                });
            });

            // 4. Role selection dynamic updates & inherited permissions tagging
            function updateRoleInheritedTags(roleId, isRoleChange = false) {
                const roleIdInt = parseInt(roleId);
                const inheritedPerms = (rolePermissionsMap[roleIdInt] || []).map(String);

                // Update info box
                const selectedOpt = roleSelect.options[roleSelect.selectedIndex];
                if (roleIdInt && selectedOpt && selectedOpt.value) {
                    const roleText = selectedOpt.text.split('(')[0].trim();
                    const permCount = selectedOpt.getAttribute('data-perm-count') || inheritedPerms.length;
                    if (roleInfoName) roleInfoName.textContent = roleText + ` (${permCount} bundled permissions)`;
                    if (roleInfoDesc) roleInfoDesc.textContent = `Users with this role automatically inherit all ${permCount} permissions. Any direct permissions selected on the right will act as additional overrides.`;
                    if (displayUserRole) displayUserRole.textContent = roleText;
                } else {
                    if (roleInfoName) roleInfoName.textContent = 'No role selected';
                    if (roleInfoDesc) roleInfoDesc.textContent = 'Please select a role to automatically bundle standard permissions.';
                    if (displayUserRole) displayUserRole.textContent = 'No Role';
                }

                // Tag & check permission checkboxes that belong to this role
                allPermCheckboxes.forEach(cb => {
                    const permName = cb.getAttribute('data-perm-name');
                    const wrapper = cb.closest('.perm-checkbox-item');
                    const badge = wrapper?.querySelector('.role-inherited-badge');

                    if (inheritedPerms.includes(permName)) {
                        wrapper?.classList.add('is-role-inherited');
                        wrapper?.classList.add('is-selected');
                        badge?.classList.remove('d-none');
                        cb.checked = true;
                    } else {
                        wrapper?.classList.remove('is-role-inherited');
                        badge?.classList.add('d-none');
                        if (isRoleChange) {
                            cb.checked = false;
                            wrapper?.classList.remove('is-selected');
                        }
                    }
                });

                updateDirectPermCounter();
            }

            roleSelect?.addEventListener('change', function() {
                updateRoleInheritedTags(this.value, true);
            });

            // 5. Direct Checkbox highlight and counter
            function updateDirectPermCounter() {
                const checkedCount = document.querySelectorAll('.direct-perm-checkbox:checked').length;
                if (directPermCounter) {
                    directPermCounter.textContent = checkedCount + ' Selected';
                }
            }

            allPermCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const wrapper = this.closest('.perm-checkbox-item');
                    if (wrapper) {
                        wrapper.classList.toggle('is-selected', this.checked);
                    }
                    updateDirectPermCounter();
                });
            });

            // 6. Select All / Deselect All
            document.getElementById('btnSelectAllPerms')?.addEventListener('click', function() {
                allPermCheckboxes.forEach(cb => {
                    cb.checked = true;
                    cb.closest('.perm-checkbox-item')?.classList.add('is-selected');
                });
                updateDirectPermCounter();
            });

            document.getElementById('btnDeselectAllPerms')?.addEventListener('click', function() {
                allPermCheckboxes.forEach(cb => {
                    cb.checked = false;
                    cb.closest('.perm-checkbox-item')?.classList.remove('is-selected');
                });
                updateDirectPermCounter();
            });

            // 7. Toggle Module All / None
            document.querySelectorAll('.select-module-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const moduleCard = this.closest('.module-perm-card');
                    if (moduleCard) {
                        const moduleCbs = moduleCard.querySelectorAll('.direct-perm-checkbox');
                        const allChecked = Array.from(moduleCbs).every(cb => cb.checked);
                        moduleCbs.forEach(cb => {
                            cb.checked = !allChecked;
                            cb.closest('.perm-checkbox-item')?.classList.toggle('is-selected', !allChecked);
                        });
                        updateDirectPermCounter();
                    }
                });
            });

            // 8. Live Search / Filter in Permissions
            const permissionSearchInput = document.getElementById('permissionSearchInput');
            const clearPermSearch = document.getElementById('clearPermSearch');
            const noPermSearchMatch = document.getElementById('noPermSearchMatch');

            function filterPermissions(query) {
                const term = query.trim().toLowerCase();
                let visibleModuleCount = 0;

                document.querySelectorAll('.module-perm-card').forEach(card => {
                    let hasMatchInModule = false;
                    const items = card.querySelectorAll('.perm-item-col');

                    items.forEach(item => {
                        const name = item.getAttribute('data-perm-name') || '';
                        if (term === '' || name.includes(term)) {
                            item.style.display = '';
                            hasMatchInModule = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (term === '' || hasMatchInModule) {
                        card.style.display = '';
                        visibleModuleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noPermSearchMatch) {
                    noPermSearchMatch.classList.toggle('d-none', visibleModuleCount > 0 || term === '');
                }
            }

            permissionSearchInput?.addEventListener('input', function() {
                filterPermissions(this.value);
            });

            clearPermSearch?.addEventListener('click', function() {
                if (permissionSearchInput) {
                    permissionSearchInput.value = '';
                    filterPermissions('');
                    permissionSearchInput.focus();
                }
            });

            // Initial trigger
            if (roleSelect && roleSelect.value) {
                updateRoleInheritedTags(roleSelect.value);
            }
        });
    </script>
    @endpush
@endsection

