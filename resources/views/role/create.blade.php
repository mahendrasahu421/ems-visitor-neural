@extends('layout.master')
@section('title', $title ?? 'Role & Permissions')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .role-form-page .module-perm-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .role-form-page .module-perm-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        }

        .role-form-page .module-perm-header {
            background: #f8fafc;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .role-form-page .perm-item-box {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-form-page .perm-item-box:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .role-form-page .perm-item-box.selected {
            background: #eff6ff;
            border-color: #93c5fd;
            box-shadow: 0 1px 4px rgba(13, 110, 253, 0.08);
        }

        .role-form-page .sticky-action-card {
            position: sticky;
            top: 80px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.06);
            padding: 20px;
        }
    </style>

    <div class="page-wrapper role-form-page">
        <div class="content">

            @php
                $isEdit = isset($role) && $role->exists;
                $selectedPermissions = old('permissions', $selectedPermissions ?? []);
                $totalPerms = $totalPermissionsCount ?? 0;
            @endphp

            {{-- Breadcrumbs & Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('role') }}" class="btn btn-sm btn-white border rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                            <i class="ti ti-arrow-left fs-16 text-dark"></i>
                        </a>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $isEdit ? 'Edit Role: ' . $role->name : 'Create New Role' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('role') }}" class="text-muted">Roles</a></li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $isEdit ? 'Edit' : 'Create' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('role') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-arrow-left fs-14"></i> Back to Roles
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ $isEdit ? route('roles.update', $role->id) : route('role.store') }}" id="roleForm">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    {{-- Left Column: Role Details & Actions Sidebar --}}
                    <div class="col-xl-4 col-lg-5">
                        <div class="sticky-action-card">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="ti ti-shield-check text-primary"></i> Role Information
                            </h5>

                            {{-- Role Name --}}
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-bold text-dark mb-1">Role Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-shield text-muted"></i></span>
                                    <input type="text" name="name" id="roleNameInput" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                           placeholder="e.g. Account Manager, Team Lead" 
                                           value="{{ old('name', $role->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted fs-11">Unique identifier name for this role.</small>
                            </div>

                            {{-- Guard Name --}}
                            <div class="mb-4">
                                <label class="form-label fs-12 fw-bold text-dark mb-1">Authentication Guard</label>
                                <select name="guard_name" class="form-select form-select-sm">
                                    <option value="web" {{ old('guard_name', $role->guard_name ?? 'web') === 'web' ? 'selected' : '' }}>Web (Internal Staff & Admins)</option>
                                    <option value="client" {{ old('guard_name', $role->guard_name ?? 'web') === 'client' ? 'selected' : '' }}>Client (Client Portal)</option>
                                    <option value="api" {{ old('guard_name', $role->guard_name ?? 'web') === 'api' ? 'selected' : '' }}>API</option>
                                </select>
                                <small class="text-muted fs-11">Determines which login guard this role applies to.</small>
                            </div>

                            {{-- Selection Summary Card --}}
                            <div class="bg-light p-3 rounded-3 border mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted fs-12 fw-semibold">Permissions Assigned</span>
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1 fs-12" id="selectedCountBadge">
                                        <span id="selectedCount">0</span> / {{ $totalPerms }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" id="selectionProgressBar" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>

                            {{-- Form Submit Buttons --}}
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-1.5 shadow-sm py-2">
                                    <i class="ti ti-check fs-16"></i> {{ $isEdit ? 'Update Role & Permissions' : 'Create & Save Role' }}
                                </button>
                                <a href="{{ route('role') }}" class="btn btn-light border py-2">Cancel</a>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Interactive Permission Matrix --}}
                    <div class="col-xl-8 col-lg-7">
                        {{-- Quick Search & Global Toggle Header --}}
                        <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                            <div class="card-body p-3 bg-white">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 380px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                                            <input type="text" id="permissionSearch" class="form-control border-start-0" placeholder="Filter permissions (e.g. view, leave, invoice)...">
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm" id="btnSelectAllGlobal">
                                            <i class="ti ti-checkbox fs-14"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm" id="btnDeselectAllGlobal">
                                            <i class="ti ti-square fs-14"></i> Deselect All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modular Grouped Permissions --}}
                        @if (isset($groupedPermissions) && count($groupedPermissions) > 0)
                            @foreach ($groupedPermissions as $moduleName => $permissions)
                                @php
                                    $moduleIcon = match ($moduleName) {
                                        'Dashboard' => 'ti-layout-dashboard text-primary',
                                        'User Management' => 'ti-users text-info',
                                        'Employee Management' => 'ti-user-check text-success',
                                        'Attendance Tracking' => 'ti-clock-check text-warning',
                                        'Leaves & Holidays' => 'ti-calendar-off text-purple',
                                        'Payroll & Finance' => 'ti-cash text-success',
                                        'Invoicing & Payments' => 'ti-file-invoice text-indigo',
                                        'Clients & Tickets' => 'ti-briefcase text-info',
                                        'Projects & Tasks' => 'ti-folder text-warning',
                                        'Recruitment & Jobs' => 'ti-briefcase text-primary',
                                        'Settings & System' => 'ti-settings text-secondary',
                                        'Access Control' => 'ti-shield-lock text-danger',
                                        default => 'ti-lock text-primary'
                                    };
                                    $moduleId = \Illuminate\Support\Str::slug($moduleName);
                                @endphp

                                <div class="module-perm-card" data-module-name="{{ strtolower($moduleName) }}">
                                    <div class="module-perm-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti {{ $moduleIcon }} fs-18"></i>
                                            <h6 class="mb-0 fw-bold text-dark fs-14">{{ $moduleName }}</h6>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 fs-11">
                                                {{ $permissions->count() }}
                                            </span>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input module-select-all" type="checkbox" role="switch" id="switch_{{ $moduleId }}" data-target-group="{{ $moduleId }}" title="Toggle all permissions for {{ $moduleName }}">
                                            <label class="form-check-label fs-12 fw-semibold text-muted" for="switch_{{ $moduleId }}">Select All</label>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-white">
                                        <div class="row g-2.5">
                                            @foreach ($permissions as $permission)
                                                @php
                                                    $isChecked = in_array($permission->name, $selectedPermissions, true);
                                                    $permLabel = ucwords(str_replace(['.', '_', '-'], ' ', $permission->name));
                                                @endphp
                                                <div class="col-md-6 col-12 perm-item-wrapper" data-perm-name="{{ strtolower($permission->name) }}" data-perm-label="{{ strtolower($permLabel) }}">
                                                    <label class="perm-item-box {{ $isChecked ? 'selected' : '' }}" for="perm_{{ $permission->id }}">
                                                        <input class="form-check-input perm-checkbox m-0 {{ $moduleId }}-perm" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->name }}" 
                                                               id="perm_{{ $permission->id }}"
                                                               {{ $isChecked ? 'checked' : '' }}>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <div class="fw-bold text-dark fs-12 text-truncate">{{ $permLabel }}</div>
                                                            <code class="text-muted fs-10 font-monospace">{{ $permission->name }}</code>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
                                <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                    <i class="ti ti-key-off fs-28"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Permissions Defined</h6>
                                <p class="text-muted fs-13 mb-3">You need to create system permissions before assigning them to roles.</p>
                                <div>
                                    <a href="{{ route('permission.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-plus me-1"></i> Create Permissions
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>

        </div>

        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        $(document).ready(function() {
            const totalPermissions = {{ $totalPerms }};

            function updateCounter() {
                const checkedCount = $('.perm-checkbox:checked').length;
                $('#selectedCount').text(checkedCount);
                const percent = totalPermissions > 0 ? Math.round((checkedCount / totalPermissions) * 100) : 0;
                $('#selectionProgressBar').css('width', percent + '%');

                // Update box styling
                $('.perm-checkbox').each(function() {
                    if ($(this).is(':checked')) {
                        $(this).closest('.perm-item-box').addClass('selected');
                    } else {
                        $(this).closest('.perm-item-box').removeClass('selected');
                    }
                });

                // Update module switches
                $('.module-select-all').each(function() {
                    const group = $(this).data('target-group');
                    const groupCheckboxes = $('.' + group + '-perm');
                    const groupChecked = $('.' + group + '-perm:checked');
                    $(this).prop('checked', groupCheckboxes.length > 0 && groupCheckboxes.length === groupChecked.length);
                });
            }

            // Checkbox change handler
            $(document).on('change', '.perm-checkbox', function() {
                updateCounter();
            });

            // Module Select All toggle switch
            $(document).on('change', '.module-select-all', function() {
                const group = $(this).data('target-group');
                const isChecked = $(this).is(':checked');
                $('.' + group + '-perm').prop('checked', isChecked);
                updateCounter();
            });

            // Global Select All
            $('#btnSelectAllGlobal').on('click', function() {
                $('.perm-checkbox').prop('checked', true);
                updateCounter();
            });

            // Global Deselect All
            $('#btnDeselectAllGlobal').on('click', function() {
                $('.perm-checkbox').prop('checked', false);
                updateCounter();
            });

            // Live Search Filter
            $('#permissionSearch').on('keyup', function() {
                const term = $(this).val().toLowerCase().trim();

                if (term === '') {
                    $('.module-perm-card').show();
                    $('.perm-item-wrapper').show();
                    return;
                }

                $('.module-perm-card').each(function() {
                    let hasVisible = false;
                    const $module = $(this);

                    $module.find('.perm-item-wrapper').each(function() {
                        const name = $(this).data('perm-name') || '';
                        const label = $(this).data('perm-label') || '';

                        if (name.includes(term) || label.includes(term)) {
                            $(this).show();
                            hasVisible = true;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (hasVisible) {
                        $module.show();
                    } else {
                        $module.hide();
                    }
                });
            });

            // Initial calculation
            updateCounter();
        });
    </script>
@endpush

