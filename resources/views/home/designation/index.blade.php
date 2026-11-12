@extends('layout.master')
@section('title', $title ?? 'Designation Master')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .designation-master-page {
            background-color: #f8fafc;
        }

        .designation-master-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.03);
            text-decoration: none;
            display: block;
        }

        .designation-master-page .stat-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -6px rgba(0, 0, 0, 0.07);
        }

        .designation-master-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .designation-master-page .kpi-icon-box {
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

        .designation-master-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 18px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .designation-master-page .filter-bar-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 14px 18px;
        }

        .designation-master-page .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.15s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .designation-master-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        /* Pulse dot for status */
        .pulse-dot-green {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulseGreen 2s infinite;
        }

        @keyframes pulseGreen {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Suggestion chips in modal */
        .suggestion-chip {
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .suggestion-chip:hover {
            background: #0d6efd !important;
            color: #ffffff !important;
            border-color: #0d6efd !important;
            transform: translateY(-1px);
        }

        /* Modern Bootstrap 5 Pagination Styling */
        .designation-master-page .pagination {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;
        }

        .designation-master-page .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            transition: all 0.15s ease;
            box-shadow: none;
        }

        .designation-master-page .page-item .page-link:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .designation-master-page .page-item.active .page-link {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28);
        }

        .designation-master-page .page-item.disabled .page-link {
            background: #f8fafc;
            border-color: #f1f5f9;
            color: #94a3b8;
            opacity: 0.65;
            cursor: not-allowed;
        }
    </style>

    <div class="page-wrapper designation-master-page">
        <div class="content py-3">

            {{-- 1. Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-briefcase fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Designation Master</h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 fs-11" id="totalBadgeCount">
                                    {{ number_format($totalDesignations ?? 0) }} Roles Defined
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Masters</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Organisation</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Designations</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-md d-inline-flex align-items-center gap-1.5 fw-medium">
                        <i class="ti ti-users fs-16"></i> Employees Roster
                    </a>
                    <button type="button" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#addDesignationModal">
                        <i class="ti ti-circle-plus fs-16"></i> Add Designation
                    </button>
                </div>
            </div>

            {{-- 2. KPI Statistics Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Designations --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('masters.organisation.designation', ['filter' => 'all']) }}" class="card stat-kpi-card h-100 mb-0" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-bold text-uppercase tracking-wider">Total Roles</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark fs-24">{{ number_format($totalDesignations ?? 0) }}</h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 mt-1">
                                    <i class="ti ti-briefcase me-1"></i>System Defined Roles
                                </span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-briefcase"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Assigned Staff --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('masters.organisation.designation', ['filter' => 'assigned']) }}" class="card stat-kpi-card h-100 mb-0" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-bold text-uppercase tracking-wider">Assigned Staff</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success fs-24">{{ number_format($assignedStaff ?? 0) }}</h3>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 mt-1">
                                    <i class="ti ti-user-check me-1"></i>Active Employees Mapped
                                </span>
                            </div>
                            <span class="kpi-icon-box" style="color: #10b981; background-color: #ecfdf5;"><i class="ti ti-users"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Role Utilization --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('masters.organisation.designation', ['filter' => 'unassigned']) }}" class="card stat-kpi-card h-100 mb-0" style="--card-accent: #8b5cf6; --card-soft-bg: #f5f3ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-bold text-uppercase tracking-wider">Role Utilization</span>
                                <h3 class="mb-0 mt-1 fw-bold text-purple fs-24" style="color: #8b5cf6;">
                                    {{ $usedCount ?? 0 }} <span class="fs-13 fw-normal text-muted">/ {{ $totalDesignations ?? 0 }} Active</span>
                                </h3>
                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle fs-11 mt-1" style="background:#f5f3ff; color:#8b5cf6; border-color:#ddd6fe;">
                                    <i class="ti ti-alert-circle me-1"></i>{{ $unassignedCount ?? 0 }} Vacant / Unassigned
                                </span>
                            </div>
                            <span class="kpi-icon-box" style="color: #8b5cf6; background-color: #f5f3ff;"><i class="ti ti-chart-pie-2"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Top Populated Designation --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="card stat-kpi-card h-100 mb-0" style="--card-accent: #f59e0b; --card-soft-bg: #fffbeb;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12 fw-bold text-uppercase tracking-wider">Leading Role</span>
                                <h6 class="mb-0 mt-1 fw-bold text-dark text-truncate" style="max-width: 160px;" title="{{ $topDesignation->Designation ?? 'N/A' }}">
                                    {{ $topDesignation ? $topDesignation->Designation : 'None' }}
                                </h6>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11 mt-1">
                                    <i class="ti ti-award me-1"></i>{{ $topDesignation ? $topDesignation->employees_count : 0 }} Staff Mapped
                                </span>
                            </div>
                            <span class="kpi-icon-box" style="color: #f59e0b; background-color: #fffbeb;"><i class="ti ti-award"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Search & Filter Bar --}}
            <div class="filter-bar-card mb-4">
                <form method="GET" action="{{ route('masters.organisation.designation') }}" id="designationFilterForm">
                    <div class="row g-2.5 align-items-center">
                        {{-- Search Input --}}
                        <div class="col-xl-4 col-lg-5 col-md-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" value="{{ $currentSearch }}" class="form-control border-start-0 ps-0" placeholder="Search designation title or code...">
                                @if(!empty($currentSearch))
                                    <a href="{{ route('masters.organisation.designation', array_merge(request()->except('search', 'page'))) }}" class="input-group-text bg-white border-start-0 text-muted" title="Clear Search">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Category Quick Filter Buttons --}}
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('masters.organisation.designation', array_merge(request()->except('filter', 'page'), ['filter' => 'all'])) }}" class="btn btn-sm {{ ($currentFilter ?? 'all') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    All ({{ $totalDesignations ?? 0 }})
                                </a>
                                <a href="{{ route('masters.organisation.designation', array_merge(request()->except('filter', 'page'), ['filter' => 'assigned'])) }}" class="btn btn-sm {{ ($currentFilter ?? '') === 'assigned' ? 'btn-success' : 'btn-outline-secondary' }}">
                                    Assigned ({{ $usedCount ?? 0 }})
                                </a>
                                <a href="{{ route('masters.organisation.designation', array_merge(request()->except('filter', 'page'), ['filter' => 'unassigned'])) }}" class="btn btn-sm {{ ($currentFilter ?? '') === 'unassigned' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                    Vacant ({{ $unassignedCount ?? 0 }})
                                </a>
                            </div>
                            @if(!empty($currentFilter) && $currentFilter !== 'all')
                                <input type="hidden" name="filter" value="{{ $currentFilter }}">
                            @endif
                        </div>

                        {{-- Sort Dropdown --}}
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6">
                            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="id_asc" {{ ($currentSort ?? 'id_asc') === 'id_asc' ? 'selected' : '' }}>Sort: ID (Asc)</option>
                                <option value="id_desc" {{ ($currentSort ?? '') === 'id_desc' ? 'selected' : '' }}>Sort: ID (Desc)</option>
                                <option value="name_asc" {{ ($currentSort ?? '') === 'name_asc' ? 'selected' : '' }}>Sort: Title (A-Z)</option>
                                <option value="name_desc" {{ ($currentSort ?? '') === 'name_desc' ? 'selected' : '' }}>Sort: Title (Z-A)</option>
                                <option value="staff_desc" {{ ($currentSort ?? '') === 'staff_desc' ? 'selected' : '' }}>Sort: Most Staff</option>
                            </select>
                        </div>

                        {{-- Per Page Dropdown --}}
                        <div class="col-xl-1.5 col-lg-2 col-md-3 col-sm-6">
                            <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 / page</option>
                                <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15 / page</option>
                                <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 / page</option>
                                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 / page</option>
                                <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 / page</option>
                            </select>
                        </div>

                        {{-- Reset Filter Button --}}
                        <div class="col-xl-1.5 col-lg-2 col-md-12 text-md-end">
                            <a href="{{ route('masters.organisation.designation') }}" class="btn btn-light border btn-sm d-inline-flex align-items-center gap-1 w-100 justify-content-center text-muted">
                                <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 4. Main Designation Table Card --}}
            <div class="card table-container-card mb-4">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-1.5">
                            <i class="ti ti-list text-primary"></i> Active Designation Directory
                        </h5>
                        @if(!empty($currentFilter) && $currentFilter !== 'all')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-11">
                                Filter: {{ ucfirst($currentFilter) }}
                            </span>
                        @endif
                    </div>
                    <div class="text-muted fs-12">
                        Showing page <span class="fw-semibold text-dark">{{ $designations->currentPage() }}</span> of <span class="fw-semibold text-dark">{{ $designations->lastPage() }}</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th style="width: 140px;">Role Code</th>
                                    <th>Designation Title</th>
                                    <th style="width: 160px;">Assigned Employees</th>
                                    <th style="width: 160px;">Creation Date</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 100px;" class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($designations as $index => $desig)
                                    @php
                                        $rowNumber = $designations->firstItem() + $index;
                                        $staffCount = $desig->employees_count ?? 0;
                                        $formattedDate = '—';
                                        if (!empty($desig->CreationDate) && $desig->CreationDate !== '0000-00-00 00:00:00') {
                                            try {
                                                $formattedDate = \Carbon\Carbon::parse($desig->CreationDate)->format('d M, Y');
                                            } catch (\Exception $e) {
                                                $formattedDate = $desig->CreationDate;
                                            }
                                        }
                                        $escapedTitle = htmlspecialchars($desig->Designation, ENT_QUOTES);
                                    @endphp
                                    <tr>
                                        {{-- Row Number --}}
                                        <td class="text-center font-monospace text-muted fs-12">
                                            {{ str_pad($rowNumber, 2, '0', STR_PAD_LEFT) }}
                                        </td>

                                        {{-- Code Badge --}}
                                        <td>
                                            <span class="badge bg-light text-secondary border font-monospace fs-11">
                                                #DESIG-{{ str_pad($desig->DesignationID, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>

                                        {{-- Designation Title --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <span class="avatar avatar-sm rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="ti ti-briefcase fs-16"></i>
                                                </span>
                                                <div>
                                                    <span class="fw-bold text-dark fs-14 d-block">{{ $desig->Designation ?: '--' }}</span>
                                                    <span class="fs-11 text-muted font-monospace">System ID: #{{ $desig->DesignationID }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Assigned Staff --}}
                                        <td>
                                            @if($staffCount > 0)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fs-12 fw-semibold d-inline-flex align-items-center gap-1" title="{{ $staffCount }} employees currently mapped">
                                                    <i class="ti ti-users"></i> {{ $staffCount }} {{ $staffCount === 1 ? 'Employee' : 'Employees' }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill fs-11 d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-user-x"></i> 0 Staff (Vacant)
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Creation Date --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-1.5 text-muted fs-12">
                                                <i class="ti ti-calendar text-muted"></i>
                                                <span>{{ $formattedDate }}</span>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold fs-11 d-inline-flex align-items-center gap-1.5">
                                                <span class="pulse-dot-green"></span> Active
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-end pe-3">
                                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                                <button type="button" class="btn btn-sm btn-outline-success action-btn" onclick="openSalarySetupModal({{ $desig->DesignationID }}, '{{ $escapedTitle }}', {{ $staffCount }})" title="Set / Bulk Apply Salary Structure">
                                                    <i class="ti ti-cash"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary action-btn" onclick="openEditModal({{ $desig->DesignationID }}, '{{ $escapedTitle }}', {{ $staffCount }})" title="Edit Designation">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn" onclick="openDeleteModal({{ $desig->DesignationID }}, '{{ $escapedTitle }}', {{ $staffCount }})" title="Delete Designation">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-4">
                                                <span class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 d-inline-flex align-items-center justify-content-center">
                                                    <i class="ti ti-briefcase-off fs-32"></i>
                                                </span>
                                                <h5 class="fw-bold text-dark mb-1">No Designations Found</h5>
                                                <p class="text-muted fs-13 mb-3">
                                                    @if(!empty($currentSearch))
                                                        No results matching "<strong>{{ $currentSearch }}</strong>". Try checking your spelling or clear filter.
                                                    @else
                                                        No designations are currently registered under this filter criteria.
                                                    @endif
                                                </p>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if(!empty($currentSearch) || !empty($currentFilter))
                                                        <a href="{{ route('masters.organisation.designation') }}" class="btn btn-sm btn-outline-secondary">
                                                            <i class="ti ti-rotate-clockwise me-1"></i> Clear Filters
                                                        </a>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDesignationModal">
                                                        <i class="ti ti-circle-plus me-1"></i> Add Designation
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. Pagination Footer --}}
                @if($designations->hasPages() || $designations->total() > 0)
                    <div class="card-footer bg-transparent py-3 px-3.5 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="text-muted fs-13">
                            Showing <span class="fw-semibold text-dark">{{ $designations->firstItem() ?? 0 }}</span> to <span class="fw-semibold text-dark">{{ $designations->lastItem() ?? 0 }}</span> of <span class="fw-semibold text-dark">{{ $designations->total() }}</span> designations
                        </div>

                        <div>
                            {{ $designations->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
        <x-footer />
    </div>

    {{-- Add Designation Modal --}}
    <div class="modal fade" id="addDesignationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-briefcase fs-16"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Add New Designation</h5>
                            <span class="fs-11 text-muted">Create a company-wide job role</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addDesignationForm">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- Designation Title Input --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-13">
                                Designation Title <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="ti ti-briefcase"></i></span>
                                <input type="text" class="form-control" name="designation_name" id="addDesignationName" placeholder="e.g. Senior Software Engineer" required autocomplete="off">
                            </div>
                            <div class="form-text fs-11 text-muted">Role name will appear across employee profiles and payroll.</div>
                        </div>

                        {{-- Quick Suggestions --}}
                        <div class="mb-3">
                            <label class="form-label text-muted fs-11 fw-semibold text-uppercase tracking-wider mb-1.5 d-block">
                                <i class="ti ti-sparkles me-1 text-primary"></i> Popular Suggestions:
                            </label>
                            <div class="d-flex flex-wrap gap-1.5" id="suggestionChips">
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('Software Engineer')">Software Engineer</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('Frontend Developer')">Frontend Developer</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('Backend Developer')">Backend Developer</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('QA Engineer')">QA Engineer</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('UI/UX Designer')">UI/UX Designer</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('Project Manager')">Project Manager</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('HR Executive')">HR Executive</span>
                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11 suggestion-chip" onclick="applySuggestion('DevOps Specialist')">DevOps Specialist</span>
                            </div>
                        </div>

                        {{-- Live Preview Box --}}
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="fs-11 text-muted text-uppercase fw-semibold d-block mb-1">Preview Badge:</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs rounded-2 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-briefcase fs-12"></i>
                                </span>
                                <span class="fw-bold text-dark fs-13" id="addLivePreviewText">Senior Software Engineer</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-10 ms-auto">Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveDesignationBtn" class="btn btn-primary btn-sm px-3 fw-semibold">
                            <i class="ti ti-check me-1"></i>Save Designation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Designation Modal --}}
    <div class="modal fade" id="editDesignationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-edit fs-16"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Edit Designation</h5>
                            <span class="fs-11 text-muted" id="editDesignationCodeLabel">Modify role details</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDesignationForm">
                    @csrf
                    <input type="hidden" id="editDesignationId">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-13">
                                Designation Title <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="ti ti-briefcase"></i></span>
                                <input type="text" class="form-control" name="designation_name" id="editDesignationName" required autocomplete="off">
                            </div>
                        </div>

                        <div id="editStaffNotice" class="alert alert-info py-2 px-3 border-0 rounded-3 fs-12 mb-0 d-none">
                            <i class="ti ti-info-circle me-1"></i> <strong id="editStaffCountText">0</strong> employee(s) are currently mapped to this designation.
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="updateDesignationBtn" class="btn btn-primary btn-sm px-3 fw-semibold">
                            <i class="ti ti-check me-1"></i>Update Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteDesignationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-body text-center p-4">
                    <span class="avatar avatar-xl rounded-circle bg-danger-subtle text-danger mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-32"></i>
                    </span>
                    <h5 class="fw-bold text-dark mb-1">Delete Designation?</h5>
                    <p class="text-muted fs-13 mb-3" id="deleteConfirmText">Are you sure you want to delete this designation?</p>
                    
                    {{-- Warning alert if staff assigned --}}
                    <div id="deleteWarningAlert" class="alert alert-danger py-2 px-2.5 text-start fs-12 rounded-3 mb-3 d-none">
                        <i class="ti ti-alert-triangle-filled me-1"></i>
                        <span id="deleteWarningAlertMsg">Employees are currently assigned to this designation.</span>
                    </div>

                    <input type="hidden" id="deleteDesignationId">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-3 fw-semibold" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Designation Salary Structure & State Rules Modal --}}
    <div class="modal fade" id="designationSalaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-cash fs-16"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                Designation Salary Setup: <span id="desigSalTitleText" class="text-primary">Loading...</span>
                            </h5>
                            <span class="fs-11 text-muted">Configure role-level compensation &amp; state-wise statutory rules</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 px-2.5 py-1" id="desigSalStaffBadge">
                            <i class="ti ti-users me-1"></i>0 Employees
                        </span>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <form id="designationSalaryForm" onsubmit="saveDesignationSalaryAjax(event)">
                    @csrf
                    <input type="hidden" name="designation_id" id="desig_sal_designation_id">
                    <input type="hidden" name="basic_salary_pct" id="desig_basic_salary_pct" value="50">
                    <input type="hidden" name="hra_pct" id="desig_hra_pct" value="25">

                    <div class="modal-body p-4">
                        {{-- State & Bulk Info Alert --}}
                        <div class="alert alert-primary bg-primary-subtle border-0 rounded-3 p-3 mb-4">
                            <div class="d-flex align-items-start gap-2.5">
                                <span class="avatar avatar-xs rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center flex-shrink-0 mt-0.5">
                                    <i class="ti ti-info-circle fs-14"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-primary fs-13 mb-1">
                                        Bulk Role-Based Sync with State-Wise Statutory Rules:
                                    </div>
                                    <div class="text-dark fs-12 mb-2">
                                        Saving this configuration will update the salary structure for <strong>all active employees</strong> assigned to this role at once. Statutory deductions like <strong>Professional Tax (PT)</strong> and <strong>HRA</strong> will automatically adjust based on each employee's posting state (e.g. Delhi / UP = ₹0 PT, Maharashtra / MP / Karnataka = ₹200 PT, Metro vs Non-Metro HRA).
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap" id="desigStateBreakdownBadges">
                                        <span class="badge bg-light text-muted border fs-11">Loading state distribution...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 1: Monthly Gross & Quick Presets --}}
                        <div class="row g-3 mb-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-bold fs-13 text-secondary">
                                    Monthly Gross Salary (₹) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-dark font-weight-bold">₹</span>
                                    <input type="number" step="0.01" class="form-control form-control-lg font-weight-bold border-primary"
                                        id="desig_monthly_gross" name="monthly_gross" value="35000" required oninput="desigCalcRecalculate()">
                                </div>
                                <small class="text-primary fs-11"><i class="ti ti-bulb me-1"></i>Type Gross to auto-calculate all components.</small>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-bold fs-13 text-secondary">
                                    Salary Effective From <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="desig_effective_from" name="effective_from"
                                    value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                                <small class="text-muted fs-11">Payroll start date for these employees</small>
                            </div>

                            <div class="col-lg-5 col-md-12">
                                <label class="form-label fw-bold fs-13 text-secondary d-block">
                                    Quick Formula Presets
                                </label>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="applyDesigPreset('standard')">
                                        <i class="ti ti-sparkles me-1"></i> Standard Statutory (50% Basic + PF + PT)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="applyDesigPreset('zero')">
                                        <i class="ti ti-shield-check me-1"></i> Zero Deductions (100% In-Hand)
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Earnings Components Breakdown --}}
                        <div class="card border border-light-subtle rounded-3 mb-3 bg-light bg-opacity-25">
                            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark fs-13 d-flex align-items-center gap-1.5">
                                    <i class="ti ti-circle-arrow-up text-success fs-16"></i> Monthly Earnings Distribution
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-11" id="desigEarningsBadge">
                                    Total: ₹ 35,000.00
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1">
                                            Basic Salary (50%)
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control fw-semibold" id="desig_basic_salary" name="basic_salary"
                                                value="17500" readonly>
                                        </div>
                                        <small class="text-muted fs-11" id="desigBasicPctLabel">50% of Gross</small>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1">
                                            HRA (25%)
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control" id="desig_hra" name="hra"
                                                value="8750" readonly>
                                        </div>
                                        <small class="text-muted fs-11" id="desigHraPctLabel">Metro 50% / Non-metro 40%</small>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1">
                                            Conveyance Allowance
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control" id="desig_conveyance" name="conveyance_allowance"
                                                value="2000" oninput="desigCalcRecalculate()">
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1">
                                            Medical Allowance
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control" id="desig_medical" name="medical_allowance"
                                                value="1500" oninput="desigCalcRecalculate()">
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1 d-flex justify-content-between align-items-center">
                                            <span>Bike Allowance</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-10">Fixed ₹1,500</span>
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control" id="desig_bike" name="bike_allowance"
                                                value="1500" oninput="desigCalcRecalculate()">
                                        </div>
                                        <small class="text-muted fs-11">Recommended for Field/Supervisor roles</small>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1">
                                            Special Allowance (Balance)
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control bg-light" id="desig_special" name="special_allowance"
                                                value="3750" readonly>
                                        </div>
                                        <small class="text-muted fs-11">Auto-adjusted to balance Gross</small>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <label class="form-label fw-semibold fs-12 text-secondary mb-1">
                                            Other / Variable Allowance
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" step="0.01" class="form-control" id="desig_other" name="other_allowance"
                                                value="0" oninput="desigCalcRecalculate()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Statutory Contributions & State Intelligence Toggles --}}
                        <div class="card border border-light-subtle rounded-3 mb-3 bg-light bg-opacity-25">
                            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark fs-13 d-flex align-items-center gap-1.5">
                                    <i class="ti ti-shield-check text-primary fs-16"></i> Statutory Contributions &amp; State Rules
                                </span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11" id="desigDeductionsBadge">
                                    Deductions: ₹ 2,000.00
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="desig_toggle_pf" name="is_pf_enabled" value="1" checked onchange="desigCalcRecalculate()">
                                            <label class="form-check-label fw-semibold fs-12 text-dark" for="desig_toggle_pf">
                                                Provident Fund (12%)
                                            </label>
                                        </div>
                                        <small class="text-muted fs-11 d-block">Employee 12% + Employer 12%</small>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="desig_toggle_esi" name="is_esi_enabled" value="1" onchange="desigCalcRecalculate()">
                                            <label class="form-check-label fw-semibold fs-12 text-dark" for="desig_toggle_esi">
                                                ESI (0.75% / 3.25%)
                                            </label>
                                        </div>
                                        <small class="text-muted fs-11 d-block">If Gross &le; ₹21,000</small>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="desig_toggle_gratuity" name="is_gratuity_enabled" value="1" checked onchange="desigCalcRecalculate()">
                                            <label class="form-check-label fw-semibold fs-12 text-dark" for="desig_toggle_gratuity">
                                                Gratuity (4.81%)
                                            </label>
                                        </div>
                                        <small class="text-muted fs-11 d-block">4.81% of Basic (Added to CTC)</small>
                                    </div>

                                    <div class="col-md-3 col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="desig_toggle_pt" name="is_pt_enabled" value="1" checked onchange="desigCalcRecalculate()">
                                            <label class="form-check-label fw-semibold fs-12 text-dark" for="desig_toggle_pt">
                                                Professional Tax (PT)
                                            </label>
                                        </div>
                                        <small class="text-muted fs-11 d-block">State-mandated slab</small>
                                    </div>

                                    <div class="col-12 pt-2 border-top">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="desig_apply_state_pt" name="apply_state_pt_rule" value="1" checked>
                                                    <label class="form-check-label fs-12 fw-semibold text-primary" for="desig_apply_state_pt">
                                                        <i class="ti ti-map-pin me-1"></i>Auto-adjust PT by Employee Posting State (Delhi/UP=₹0, MP/MH/Karnataka=₹200)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="desig_apply_metro_hra" name="apply_metro_hra_rule" value="1" checked>
                                                    <label class="form-check-label fs-12 fw-semibold text-primary" for="desig_apply_metro_hra">
                                                        <i class="ti ti-building me-1"></i>Auto-adjust HRA for Metro States (50% Metro vs 40% Non-Metro)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Live Sticky Financial Preview --}}
                        <div class="card border border-primary-subtle rounded-3 p-3 text-center text-md-start" style="background: linear-gradient(135deg, #f8faff 0%, #edf2fc 100%);">
                            <div class="row align-items-center g-3">
                                <div class="col-md-3 col-6 border-end">
                                    <span class="fs-11 text-muted text-uppercase fw-bold d-block">Gross Earnings</span>
                                    <h5 class="fw-bold text-primary mb-0 fs-18" id="desigPrevGross">₹ 35,000.00</h5>
                                </div>
                                <div class="col-md-3 col-6 border-end">
                                    <span class="fs-11 text-muted text-uppercase fw-bold d-block">Sample Deductions</span>
                                    <h5 class="fw-bold text-danger mb-0 fs-18" id="desigPrevDeductions">₹ 2,000.00</h5>
                                </div>
                                <div class="col-md-3 col-6 border-end">
                                    <span class="fs-11 text-muted text-uppercase fw-bold d-block">Est. In-Hand Pay</span>
                                    <h5 class="fw-bold text-success mb-0 fs-18" id="desigPrevNet">₹ 33,000.00</h5>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="fs-11 text-muted text-uppercase fw-bold d-block">Est. Annual CTC</span>
                                    <h5 class="fw-bold text-dark mb-0 fs-18" id="desigPrevCtc">₹ 4,51,701.00</h5>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="desig_apply_to_employees" name="apply_to_employees" value="1" checked>
                            <label class="form-check-label fs-12 fw-semibold text-dark" for="desig_apply_to_employees">
                                Update and activate this salary structure for all currently active employees mapped to this designation.
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="saveDesigSalBtn" class="btn btn-success btn-sm px-4 fw-semibold">
                            <i class="ti ti-check me-1"></i>Save &amp; Apply to All Employees
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <script>
        // Apply suggestion chip to Add Input
        function applySuggestion(text) {
            document.getElementById('addDesignationName').value = text;
            updateLivePreview(text);
            document.getElementById('addDesignationName').focus();
        }

        // Live preview on typing
        document.getElementById('addDesignationName').addEventListener('input', function() {
            updateLivePreview(this.value);
        });

        function updateLivePreview(text) {
            const preview = document.getElementById('addLivePreviewText');
            preview.textContent = text.trim() ? text.trim() : 'Senior Software Engineer';
        }

        // Open Edit Modal
        function openEditModal(id, name, staffCount) {
            document.getElementById('editDesignationId').value = id;
            document.getElementById('editDesignationName').value = name;
            document.getElementById('editDesignationCodeLabel').textContent = 'Code: #DESIG-' + String(id).padStart(3, '0');

            const notice = document.getElementById('editStaffNotice');
            const countText = document.getElementById('editStaffCountText');
            if (staffCount > 0) {
                countText.textContent = staffCount;
                notice.classList.remove('d-none');
            } else {
                notice.classList.add('d-none');
            }

            const modal = new bootstrap.Modal(document.getElementById('editDesignationModal'));
            modal.show();
        }

        // Open Delete Modal
        function openDeleteModal(id, name, staffCount) {
            document.getElementById('deleteDesignationId').value = id;
            document.getElementById('deleteConfirmText').innerHTML = `Are you sure you want to delete <strong>${name}</strong>?`;

            const warningBox = document.getElementById('deleteWarningAlert');
            const warningMsg = document.getElementById('deleteWarningAlertMsg');
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            if (staffCount > 0) {
                warningMsg.innerHTML = `<strong>Cannot delete!</strong> ${staffCount} employee(s) are actively mapped to this designation. Please reassign them in Employees Roster first.`;
                warningBox.classList.remove('d-none');
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50');
            } else {
                warningBox.classList.add('d-none');
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('opacity-50');
            }

            const modal = new bootstrap.Modal(document.getElementById('deleteDesignationModal'));
            modal.show();
        }

        // Handle Add Designation Submit (AJAX)
        document.getElementById('addDesignationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('addDesignationName').value.trim();
            if (!name) return;

            const btn = document.getElementById('saveDesignationBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ti ti-loader-2 spin me-1"></i>Saving...';

            $.ajax({
                url: "{{ route('masters.organisation.designation.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    designation_name: name
                },
                success: function(res) {
                    $('#addDesignationModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Designation Added!',
                            text: res.message || 'Designation created successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti ti-check me-1"></i>Save Designation';
                    const msg = xhr.responseJSON?.message || 'Failed to save designation.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // Handle Edit Designation Submit (AJAX)
        document.getElementById('editDesignationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editDesignationId').value;
            const name = document.getElementById('editDesignationName').value.trim();
            if (!id || !name) return;

            const btn = document.getElementById('updateDesignationBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="ti ti-loader-2 spin me-1"></i>Updating...';

            $.ajax({
                url: "{{ url('masters/organisation/designation/update') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    designation_name: name
                },
                success: function(res) {
                    $('#editDesignationModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message || 'Designation updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti ti-check me-1"></i>Update Changes';
                    const msg = xhr.responseJSON?.message || 'Failed to update designation.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // Confirm Delete (AJAX)
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const id = document.getElementById('deleteDesignationId').value;
            if (!id) return;

            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Deleting...';

            $.ajax({
                url: "{{ url('masters/organisation/designation/destroy') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                },
                success: function(res) {
                    $('#deleteDesignationModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message || 'Designation deleted successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    btn.disabled = false;
                    btn.textContent = 'Yes, Delete';
                    const msg = xhr.responseJSON?.message || 'Failed to delete designation.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Action Denied', text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // ==========================================
        // Designation Salary & State Statutory Logic
        // ==========================================

        function openSalarySetupModal(id, title, count) {
            $('#desig_sal_designation_id').val(id);
            $('#desigSalTitleText').text(title);
            $('#desigSalStaffBadge').html('<i class="ti ti-users me-1"></i>' + count + ' Staff Mapped');
            
            $('#desigStateBreakdownBadges').html('<span class="badge bg-light text-muted border fs-11"><i class="ti ti-loader-2 spin me-1"></i>Detecting employee posting states...</span>');

            const modal = new bootstrap.Modal(document.getElementById('designationSalaryModal'));
            modal.show();

            // Fetch live profile with state distribution
            $.ajax({
                url: "{{ url('masters/payroll/salary-structure/designation-profile') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.data) {
                        const data = res.data;
                        const states = data.state_breakdown || [];
                        if (states.length > 0) {
                            let html = '';
                            states.forEach(function(st) {
                                const metroBadge = st.is_metro ? '<span class="text-primary fw-bold ms-1">[Metro 50% HRA]</span>' : '<span class="text-secondary ms-1">[Non-Metro 40% HRA]</span>';
                                const ptColor = st.pt_amount > 0 ? 'text-danger' : 'text-success';
                                html += `<span class="badge bg-white text-dark border px-2.5 py-1 fs-11 shadow-xs d-inline-flex align-items-center">
                                    <i class="ti ti-map-pin text-primary me-1"></i>
                                    <strong>${st.state_name}</strong>: ${st.count} staff &bull; PT: <span class="${ptColor} fw-bold ms-1">₹${st.pt_amount}</span> ${metroBadge}
                                </span>`;
                            });
                            $('#desigStateBreakdownBadges').html(html);
                        } else {
                            $('#desigStateBreakdownBadges').html('<span class="badge bg-light text-muted border fs-11"><i class="ti ti-info-circle me-1"></i>No active employees currently mapped to this role. Saving will configure the standard template for future employees.</span>');
                        }

                        // If template exists
                        if (data.template) {
                            const t = data.template;
                            $('#desig_monthly_gross').val(t.monthly_gross || 35000);
                            $('#desig_basic_salary_pct').val(t.basic_salary_pct || 50);
                            $('#desig_hra_pct').val(t.hra_pct || 25);
                            $('#desig_conveyance').val(t.conveyance_allowance !== null ? t.conveyance_allowance : 2000);
                            $('#desig_medical').val(t.medical_allowance !== null ? t.medical_allowance : 1500);
                            $('#desig_bike').val(t.bike_allowance !== null ? t.bike_allowance : 1500);
                            $('#desig_other').val(t.other_allowance || 0);

                            $('#desig_toggle_pf').prop('checked', !!t.is_pf_enabled);
                            $('#desig_toggle_esi').prop('checked', !!t.is_esi_enabled);
                            $('#desig_toggle_pt').prop('checked', !!t.is_pt_enabled);
                            $('#desig_toggle_gratuity').prop('checked', !!t.is_gratuity_enabled);
                            $('#desig_apply_state_pt').prop('checked', !!t.apply_state_pt_rule);
                            $('#desig_apply_metro_hra').prop('checked', !!t.apply_metro_hra_rule);
                        } else {
                            // Smart defaults
                            $('#desig_monthly_gross').val(35000);
                            $('#desig_basic_salary_pct').val(50);
                            $('#desig_hra_pct').val(25);
                            $('#desig_conveyance').val(2000);
                            $('#desig_medical').val(1500);
                            $('#desig_bike').val(1500); // Fixed 1,500 bike allowance as requested
                            $('#desig_other').val(0);

                            $('#desig_toggle_pf').prop('checked', true);
                            $('#desig_toggle_esi').prop('checked', false); // 35000 > 21000
                            $('#desig_toggle_pt').prop('checked', true);
                            $('#desig_toggle_gratuity').prop('checked', true);
                            $('#desig_apply_state_pt').prop('checked', true);
                            $('#desig_apply_metro_hra').prop('checked', true);
                        }

                        desigCalcRecalculate();
                    }
                },
                error: function() {
                    $('#desigStateBreakdownBadges').html('<span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Unable to load live state data. Using default statutory rules.</span>');
                    desigCalcRecalculate();
                }
            });
        }

        function applyDesigPreset(type) {
            if (type === 'standard') {
                $('#desig_basic_salary_pct').val(50);
                $('#desig_hra_pct').val(25);
                $('#desig_conveyance').val(2000);
                $('#desig_medical').val(1500);
                $('#desig_bike').val(1500);
                $('#desig_toggle_pf').prop('checked', true);
                const gross = parseFloat($('#desig_monthly_gross').val()) || 0;
                $('#desig_toggle_esi').prop('checked', gross <= 21000);
                $('#desig_toggle_pt').prop('checked', true);
                $('#desig_toggle_gratuity').prop('checked', true);
                $('#desig_apply_state_pt').prop('checked', true);
                $('#desig_apply_metro_hra').prop('checked', true);
            } else if (type === 'zero') {
                $('#desig_basic_salary_pct').val(50);
                $('#desig_hra_pct').val(25);
                $('#desig_conveyance').val(2000);
                $('#desig_medical').val(1500);
                $('#desig_bike').val(1500);
                $('#desig_toggle_pf').prop('checked', false);
                $('#desig_toggle_esi').prop('checked', false);
                $('#desig_toggle_pt').prop('checked', false);
                $('#desig_toggle_gratuity').prop('checked', false);
                $('#desig_apply_state_pt').prop('checked', false);
                $('#desig_apply_metro_hra').prop('checked', false);
            }
            desigCalcRecalculate();
        }

        function desigCalcRecalculate() {
            const gross = parseFloat($('#desig_monthly_gross').val()) || 0;
            const basicPct = parseFloat($('#desig_basic_salary_pct').val()) || 50;
            const hraPct = parseFloat($('#desig_hra_pct').val()) || 25;

            const basic = Math.round(gross * (basicPct / 100) * 100) / 100;
            const hra = Math.round(gross * (hraPct / 100) * 100) / 100;
            const conveyance = parseFloat($('#desig_conveyance').val()) || 0;
            const medical = parseFloat($('#desig_medical').val()) || 0;
            const bike = parseFloat($('#desig_bike').val()) || 0;
            const other = parseFloat($('#desig_other').val()) || 0;

            const allocated = basic + hra + conveyance + medical + bike + other;
            const special = Math.max(0, Math.round((gross - allocated) * 100) / 100);

            $('#desig_basic_salary').val(basic.toFixed(2));
            $('#desig_hra').val(hra.toFixed(2));
            $('#desig_special').val(special.toFixed(2));

            $('#desigEarningsBadge').text('Total: ₹ ' + gross.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            // Deductions calculation for preview
            const isPf = $('#desig_toggle_pf').is(':checked');
            const isEsi = $('#desig_toggle_esi').is(':checked');
            const isPt = $('#desig_toggle_pt').is(':checked');
            const isGratuity = $('#desig_toggle_gratuity').is(':checked');

            let pfEmp = isPf ? Math.min(basic, 15000) * 0.12 : 0;
            let pfEmpr = isPf ? Math.min(basic, 15000) * 0.12 : 0;
            let esiEmp = (isEsi && gross <= 21000) ? Math.round(gross * 0.0075 * 100) / 100 : 0;
            let esiEmpr = (isEsi && gross <= 21000) ? Math.round(gross * 0.0325 * 100) / 100 : 0;
            let samplePt = isPt ? 200 : 0;
            let gratuityMonthly = isGratuity ? Math.round(basic * 0.0481 * 100) / 100 : 0;

            let sampleDeductions = Math.round((pfEmp + esiEmp + samplePt) * 100) / 100;
            let estNet = Math.max(0, Math.round((gross - sampleDeductions) * 100) / 100);
            let monthlyCtc = gross + pfEmpr + esiEmpr + gratuityMonthly;
            let annualCtc = Math.round(monthlyCtc * 12 * 100) / 100;

            $('#desigDeductionsBadge').text('Deductions: ₹ ' + sampleDeductions.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#desigPrevGross').text('₹ ' + gross.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#desigPrevDeductions').text('₹ ' + sampleDeductions.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#desigPrevNet').text('₹ ' + estNet.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#desigPrevCtc').text('₹ ' + annualCtc.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }

        function saveDesignationSalaryAjax(e) {
            e.preventDefault();
            const btn = $('#saveDesigSalBtn');
            btn.prop('disabled', true).html('<i class="ti ti-loader-2 spin me-1"></i>Applying to All Employees...');

            const formData = $('#designationSalaryForm').serialize();

            $.ajax({
                url: "{{ route('masters.payroll.salary-structure.save-designation-salary') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Save &amp; Apply to All Employees');
                    $('#designationSalaryModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Designation Salary Applied!',
                            text: res.message,
                            confirmButtonText: 'Great!'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Save &amp; Apply to All Employees');
                    const msg = xhr.responseJSON?.message || 'Failed to save designation salary structure.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    </script>
@endpush
