@extends('layout.master')
@section('title', $title ?? 'Roles Management')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .role-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .role-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .role-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .role-page .kpi-icon-box {
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

        .role-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .role-page .dataTables_wrapper {
            padding: 0;
        }

        .role-page .dataTables_wrapper .dataTables_info {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 14px 20px !important;
            margin: 0 !important;
        }

        .role-page .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 0 !important;
            padding: 14px 20px !important;
        }

        .role-page .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 34px !important;
            height: 34px !important;
            padding: 0 10px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #334155 !important;
            cursor: pointer !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
            margin: 0 !important;
        }

        .role-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .role-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .role-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
        }

        .role-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
        }
    </style>

    <div class="page-wrapper role-page">
        <div class="content">

            {{-- Breadcrumbs & Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-shield-lock fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Roles & Access Control</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $totalRoles ?? 0 }} Total Roles
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted">Administration</li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">Roles</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('permission') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-key fs-15"></i> View Permissions
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
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $totalRoles ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-shield text-primary"></i> System Access Roles
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-shield-lock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Permissions --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #6366f1; --card-soft-bg: #eef2ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Total Permissions</span>
                                <h3 class="mb-0 mt-1 fw-bold" style="color: #4f46e5;">{{ $totalPermissions ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-key" style="color: #4f46e5;"></i> Granular Capabilities
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-key"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Users Assigned --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Admin Users</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ $totalUsers ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-users text-success"></i> Assigned User Accounts
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Web Guard Roles --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #f59e0b; --card-soft-bg: #fffbeb;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Web Roles</span>
                                <h3 class="mb-0 mt-1 fw-bold text-warning">{{ $webRolesCount ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-browser text-warning"></i> Portal Roles
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-browser"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Container Card --}}
            <div class="table-container-card">
                <div class="card-header bg-white border-bottom py-3 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-list fs-16"></i>
                        </span>
                        <div>
                            <h5 class="card-title mb-0 fs-15 fw-bold text-dark">Roles List</h5>
                            <small class="text-muted">Manage roles, assign modular permissions, and configure portal menu access</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 240px;">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                            <input type="text" id="roleSearchInput" class="form-control border-start-0" placeholder="Search role or permission...">
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="refreshRolesBtn" title="Refresh Table">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="rolesTable" class="table table-hover align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">#</th>
                                <th width="25%">Role Details</th>
                                <th width="35%">Assigned Permissions</th>
                                <th width="12%">Users Count</th>
                                <th width="10%">Created</th>
                                <th width="13%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>

        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        $(document).ready(function() {
            const table = $('#rolesTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                dom: '<"top">rt<"d-flex flex-wrap justify-content-between align-items-center px-3 py-3 border-top"ip><"clear">',
                ajax: {
                    url: "{{ route('role.list') }}",
                },
                order: [],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading roles...',
                    emptyTable: `
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="ti ti-shield-off fs-28"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Roles Defined</h6>
                            <p class="text-muted fs-13 mb-0">Click "Add New Role" to create your first user role.</p>
                        </div>
                    `,
                    zeroRecords: `
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="ti ti-search-off fs-28"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Matching Roles</h6>
                            <p class="text-muted fs-13 mb-0">No records match your search criteria.</p>
                        </div>
                    `,
                    info: "Showing _START_ to _END_ of _TOTAL_ roles",
                    infoEmpty: "Showing 0 to 0 of 0 roles",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                        last: '<i class="ti ti-chevrons-right"></i>'
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted fw-semibold fs-12' },
                    { data: 'name' },
                    { data: 'permissions' },
                    { data: 'users' },
                    { data: 'created_at', className: 'text-muted fs-12' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-end' }
                ]
            });

            // Live Search
            $('#roleSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Quick Refresh
            $('#refreshRolesBtn').on('click', function() {
                const $icon = $(this).find('i');
                $icon.addClass('ti-spin');
                table.ajax.reload(function() {
                    $icon.removeClass('ti-spin');
                });
            });

            // Delete Role SweetAlert Handler
            $(document).on('click', '.btn-delete-role', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const roleName = $btn.data('role-name');
                const usersCount = parseInt($btn.data('users-count') || 0);
                const $form = $btn.closest('form.delete-role-form');

                if (usersCount > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Delete Role',
                        html: `Role <strong>${roleName}</strong> has <strong>${usersCount}</strong> user(s) currently assigned. Please reassign those users to another role before deleting.`,
                        confirmButtonText: 'Understood',
                        customClass: {
                            confirmButton: 'btn btn-primary btn-sm'
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: 'Delete Role?',
                    html: `Are you sure you want to delete role <strong>${roleName}</strong>? All assigned permissions for this role will be removed.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ti ti-trash me-1"></i> Yes, Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger btn-sm me-2',
                        cancelButton: 'btn btn-light btn-sm'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $form.submit();
                    }
                });
            });
        });
    </script>
@endpush

