@extends('layout.master')
@section('title', $title ?? 'Admin Users Management')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .users-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .users-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .users-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .users-page .kpi-icon-box {
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

        .users-page .user-table-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            background: #ffffff;
            overflow: hidden;
        }

        .users-page .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>

    <div class="page-wrapper users-page">
        <div class="content container-fluid">

            {{-- Breadcrumbs & Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-users fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Admin Users & Access</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $totalUsers ?? 0 }} Users
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted">Administration</li>
                                    <li class="breadcrumb-item active text-primary" aria-current="page">Users</li>
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
                        <i class="ti ti-key fs-15"></i> Permissions
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-user-plus fs-15"></i> Add New User
                    </a>
                </div>
            </div>

            {{-- 4 KPI Metric Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Users --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #0d6efd; --card-soft-bg: #eff6ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Total Users</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $totalUsers ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-users text-primary"></i> Registered accounts
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Accounts --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #198754; --card-soft-bg: #e8f7ee;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Active Users</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $activeUsers ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-user-check text-success"></i> Operational logins
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Inactive Accounts --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #6c757d; --card-soft-bg: #f8f9fa;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">Inactive Users</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $inactiveUsers ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-user-off text-secondary"></i> Suspended / Inactive
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-user-x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Available Roles --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card p-3" style="--card-accent: #6f42c1; --card-soft-bg: #f3effc;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-semibold text-uppercase tracking-wider">System Roles</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ $roles->count() ?? 0 }}</h3>
                                <div class="text-muted fs-11 mt-1 d-flex align-items-center gap-1">
                                    <i class="ti ti-shield text-purple"></i> Available role types
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-shield"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table Container --}}
            <div class="user-table-card">
                {{-- Filter Toolbar --}}
                <div class="p-3 bg-white border-bottom">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
                        {{-- Search Input --}}
                        <div class="col-md-5 col-lg-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="ti ti-search fs-14"></i>
                                </span>
                                <input type="text" name="search" class="form-control bg-light border-start-0 fs-13"
                                       value="{{ request('search') }}" placeholder="Search name, email, phone...">
                            </div>
                        </div>

                        {{-- Role Filter --}}
                        <div class="col-md-3 col-lg-3">
                            <select name="role_id" class="form-select form-select-sm bg-light fs-13" onchange="this.form.submit()">
                                <option value="">All Roles</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string) request('role_id') === (string) $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="col-md-2 col-lg-2">
                            <select name="status" class="form-select form-select-sm bg-light fs-13" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-2 col-lg-3 d-flex align-items-center gap-1">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ti ti-filter fs-13 me-1"></i>Filter
                            </button>
                            @if (request()->hasAny(['search', 'role_id', 'status']))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ti ti-refresh fs-13 me-1"></i>Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-12 text-uppercase text-muted">
                            <tr>
                                <th class="ps-3" style="width: 50px;">#</th>
                                <th>User Account</th>
                                <th>Assigned Role</th>
                                <th>Direct Permissions</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse ($users as $index => $u)
                                @php
                                    $userRole = $u->roles->first();
                                    $roleIcon = match (strtolower(optional($userRole)->name ?? '')) {
                                        'super admin', 'superadmin' => 'ti-shield-lock text-danger',
                                        'admin' => 'ti-user-shield text-primary',
                                        'hr' => 'ti-user-heart text-info',
                                        'employee' => 'ti-user-check text-success',
                                        'client' => 'ti-briefcase text-warning',
                                        default => 'ti-shield text-secondary'
                                    };
                                    $directPermsCount = $u->permissions->count();
                                @endphp
                                <tr>
                                    <td class="ps-3 text-muted font-monospace fs-12">
                                        {{ $users->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($u->name ?: 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.edit', $u) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                    {{ $u->name }}
                                                </a>
                                                <div class="text-muted fs-11 mt-0.5">
                                                    <i class="ti ti-mail me-1"></i>{{ $u->email }}
                                                    @if ($u->phone)
                                                        <span class="mx-1">&bull;</span><i class="ti ti-phone me-0.5"></i>{{ $u->phone }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($userRole)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-12">
                                                <i class="ti {{ $roleIcon }} me-1"></i>{{ ucfirst($userRole->name) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fs-11">
                                                No Role Assigned
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($directPermsCount > 0)
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                @foreach ($u->permissions->take(2) as $p)
                                                    <span class="badge bg-info-subtle text-info border fs-11 px-2 py-0.5">{{ $p->name }}</span>
                                                @endforeach
                                                @if ($directPermsCount > 2)
                                                    <span class="badge bg-light text-muted border fs-11 px-2 py-0.5" title="{{ $u->permissions->skip(2)->pluck('name')->join(', ') }}">
                                                        +{{ $directPermsCount - 2 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted fs-12"><i class="ti ti-shield-check me-1 text-success"></i>Role permissions only</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ((int) $u->status === 1)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-11">
                                                <i class="ti ti-circle-filled fs-8 me-1 text-success"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fs-11">
                                                <i class="ti ti-circle-filled fs-8 me-1 text-secondary"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-12">
                                        {{ optional($u->created_at)->format('d M, Y') ?? '-' }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary shadow-xs" title="Edit User">
                                                <i class="ti ti-edit fs-14"></i>
                                            </a>
                                            @if (auth()->id() !== $u->id)
                                                <button type="button" class="btn btn-sm btn-outline-danger shadow-xs delete-user-btn"
                                                        data-user-id="{{ $u->id }}"
                                                        data-user-name="{{ $u->name }}"
                                                        data-delete-url="{{ route('admin.users.destroy', $u) }}"
                                                        title="Delete User">
                                                    <i class="ti ti-trash fs-14"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ti ti-users-minus fs-36 text-muted mb-2 d-block"></i>
                                        <h6 class="fw-bold text-dark">No Users Found</h6>
                                        <p class="fs-13 text-muted mb-2">No admin users match your current filter criteria.</p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                            <i class="ti ti-plus me-1"></i>Create First User
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($users->hasPages())
                    <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="text-muted fs-13">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </div>
                        <div>
                            {{ $users->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Hidden Delete Form for SweetAlert2 --}}
    <form id="deleteUserForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    @push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-user-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userName = this.getAttribute('data-user-name');
                    const deleteUrl = this.getAttribute('data-delete-url');

                    Swal.fire({
                        title: 'Delete User Account?',
                        html: `Are you sure you want to permanently delete user <strong>${userName}</strong>? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Delete Account',
                        cancelButtonText: 'Cancel',
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const deleteForm = document.getElementById('deleteUserForm');
                            deleteForm.action = deleteUrl;
                            deleteForm.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
@endsection

