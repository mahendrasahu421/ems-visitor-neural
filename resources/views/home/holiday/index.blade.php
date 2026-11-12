@extends('layout.master')
@section('title', $title ?? 'Holiday Management')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .holiday-mgmt-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            display: block;
            text-decoration: none;
        }

        .holiday-mgmt-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .holiday-mgmt-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .holiday-mgmt-page .kpi-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--card-soft-bg, #eef5ff);
            color: var(--card-accent, #0d6efd);
            flex-shrink: 0;
        }

        .holiday-mgmt-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .holiday-mgmt-page .filter-bar-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 16px;
            box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.03);
        }

        .holiday-mgmt-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .holiday-mgmt-page .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.15s ease;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
        }

        .holiday-mgmt-page .action-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .holiday-mgmt-page .action-btn.btn-delete:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
        }

        /* Modern Pagination Styling */
        .holiday-mgmt-page .pagination {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;
        }

        .holiday-mgmt-page .page-item .page-link {
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

        .holiday-mgmt-page .page-item .page-link:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .holiday-mgmt-page .page-item.active .page-link {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28);
        }

        .holiday-mgmt-page .page-item.disabled .page-link {
            background: #f8fafc;
            border-color: #f1f5f9;
            color: #94a3b8;
            opacity: 0.65;
            cursor: not-allowed;
        }
    </style>

    <div class="page-wrapper holiday-mgmt-page">
        <div class="content">

            {{-- 1. Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-event fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Holiday Management</h3>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 fs-11">
                                    <i class="ti ti-point-filled me-0.5"></i>{{ number_format($totalActiveHolidays ?? 0) }} Active Holidays
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Settings</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Holiday Master</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="btn-group" role="group">
                        <a href="{{ route('settings.holiday') }}" class="btn btn-primary btn-md active d-inline-flex align-items-center gap-1.5 fw-medium">
                            <i class="ti ti-list fs-16"></i> List View
                        </a>
                        <a href="{{ route('settings.holiday.calendar') }}" class="btn btn-outline-secondary btn-md d-inline-flex align-items-center gap-1.5 fw-medium">
                            <i class="ti ti-calendar fs-16"></i> Calendar View
                        </a>
                    </div>
                    <a href="{{ route('settings.holiday.create') }}" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-circle-plus fs-16"></i> Add Holiday
                    </a>
                </div>
            </div>

            {{-- 2. KPI Metric Cards (Active Holidays Only) --}}
            <div class="row g-3 mb-4">
                {{-- Total Active Holidays --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Active Holidays</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($totalActiveHolidays ?? 0) }}</h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 mt-1">
                                    <i class="ti ti-circle-check me-1"></i>Active & Scheduled
                                </span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-calendar-stats"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Common Holidays (Mandatory) --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('settings.holiday', ['type' => 'common']) }}" class="stat-kpi-card h-100" style="--card-accent: #dc2626; --card-soft-bg: #fef2f2;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Common Holidays</span>
                                <h3 class="mb-0 mt-1 fw-bold text-danger">{{ number_format($commonHolidaysCount ?? 0) }}</h3>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11 mt-1">
                                    <i class="ti ti-star-filled me-1"></i>Mandatory (All Closed)
                                </span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-building-community"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Restricted Holidays (Optional) --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('settings.holiday', ['type' => 'restricted']) }}" class="stat-kpi-card h-100" style="--card-accent: #6366f1; --card-soft-bg: #eef2ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Restricted Holidays</span>
                                <h3 class="mb-0 mt-1 fw-bold text-primary">{{ number_format($restrictedHolidaysCount ?? 0) }}</h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 mt-1">
                                    <i class="ti ti-user-check me-1"></i>Optional (Any 5)
                                </span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-sparkles"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Next Upcoming Holiday --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Next Upcoming Holiday</span>
                                @if(!empty($nextUpcomingHoliday))
                                    @php
                                        $nextDt = \Carbon\Carbon::parse($nextUpcomingHoliday->holiday_date);
                                        $diffDays = \Carbon\Carbon::today()->diffInDays($nextDt, false);
                                    @endphp
                                    <h6 class="mb-0 mt-1 fw-bold text-dark text-truncate" style="max-width: 160px;" title="{{ $nextUpcomingHoliday->holiday_name }}">
                                        {{ $nextUpcomingHoliday->holiday_name }}
                                    </h6>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11 mt-1">
                                        <i class="ti ti-clock me-1"></i>{{ $nextDt->format('d M') }} ({{ $diffDays == 0 ? 'Today' : ($diffDays == 1 ? 'Tomorrow' : 'in '.$diffDays.' days') }})
                                    </span>
                                @else
                                    <h6 class="mb-0 mt-1 fw-bold text-muted">No Upcoming</h6>
                                    <span class="badge bg-light text-muted border fs-11 mt-1">Current Year Completed</span>
                                @endif
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-hourglass-empty"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Search & Filter Bar --}}
            <div class="filter-bar-card mb-4">
                <form method="GET" action="{{ route('settings.holiday') }}" id="holidayFilterForm">
                    <div class="row g-2.5 align-items-center">
                        {{-- Search Input --}}
                        <div class="col-xl-4 col-lg-5 col-md-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" value="{{ $currentSearch }}" class="form-control border-start-0 ps-0" placeholder="Search active holiday by name, remark, or date...">
                                @if(!empty($currentSearch))
                                    <a href="{{ route('settings.holiday', array_merge(request()->except('search', 'page'))) }}" class="input-group-text bg-white border-start-0 text-muted" title="Clear Search">
                                        <i class="ti ti-x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Type Quick Buttons --}}
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('settings.holiday', array_merge(request()->except('type', 'page'))) }}" class="btn btn-sm {{ empty($currentType) ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    All Active ({{ $totalActiveHolidays }})
                                </a>
                                <a href="{{ route('settings.holiday', array_merge(request()->except('type', 'page'), ['type' => 'common'])) }}" class="btn btn-sm {{ $currentType === 'common' ? 'btn-danger' : 'btn-outline-secondary' }}">
                                    ★ Common ({{ $commonHolidaysCount }})
                                </a>
                                <a href="{{ route('settings.holiday', array_merge(request()->except('type', 'page'), ['type' => 'restricted'])) }}" class="btn btn-sm {{ $currentType === 'restricted' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    Restricted ({{ $restrictedHolidaysCount }})
                                </a>
                            </div>
                            @if(!empty($currentType))
                                <input type="hidden" name="type" value="{{ $currentType }}">
                            @endif
                        </div>

                        {{-- Year Dropdown --}}
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6">
                            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all">All Active Years</option>
                                @foreach($availableYears ?? [] as $yr)
                                    <option value="{{ $yr }}" {{ (string)$currentYear === (string)$yr || (empty($currentYear) && $yr == 2026) ? 'selected' : '' }}>
                                        Year {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Per Page Dropdown --}}
                        <div class="col-xl-1.5 col-lg-2 col-md-3 col-sm-6">
                            <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / page</option>
                                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 / page</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / page</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / page</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / page</option>
                            </select>
                        </div>

                        {{-- Reset Filter Button --}}
                        <div class="col-xl-1.5 col-lg-2 col-md-12 text-md-end">
                            <a href="{{ route('settings.holiday') }}" class="btn btn-light border btn-sm d-inline-flex align-items-center gap-1 w-100 justify-content-center text-muted">
                                <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 4. Main Holiday Table Card --}}
            <div class="card table-container-card mb-4">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-1.5">
                            <i class="ti ti-list text-primary"></i> Active Holiday List
                        </h5>
                        @if(!empty($currentType))
                            <span class="badge {{ $currentType === 'common' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} rounded-pill px-2 py-0.5 fs-11">
                                Filtered: {{ ucfirst($currentType) }}
                            </span>
                        @endif
                    </div>
                    <div class="text-muted fs-12">
                        Showing page <span class="fw-semibold text-dark">{{ $holidays->currentPage() }}</span> of <span class="fw-semibold text-dark">{{ $holidays->lastPage() }}</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Holiday / Festival Name</th>
                                    <th>Date & Day</th>
                                    <th>Category</th>
                                    <th>Description / Remarks</th>
                                    <th>Status</th>
                                    <th style="width: 100px;" class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $index => $holiday)
                                    @php
                                        $dt = \Carbon\Carbon::parse($holiday->holiday_date);
                                        $isCommon = str_contains(strtolower($holiday->holiday_remark ?? ''), 'common');
                                        $isToday = $dt->isToday();
                                        $isUpcoming = $dt->isFuture();
                                        $rowNumber = $holidays->firstItem() + $index;
                                    @endphp
                                    <tr>
                                        {{-- Row Number --}}
                                        <td class="text-center font-mono text-muted fs-13">
                                            {{ str_pad($rowNumber, 2, '0', STR_PAD_LEFT) }}
                                        </td>

                                        {{-- Holiday Name + Type Pill --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm rounded-3 {{ $isCommon ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="ti {{ $isCommon ? 'ti-star-filled' : 'ti-calendar' }} fs-15"></i>
                                                </span>
                                                <div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                        <span class="fw-bold text-dark fs-14">{{ $holiday->holiday_name }}</span>
                                                        @if($isToday)
                                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5 fs-10 fw-semibold">TODAY</span>
                                                        @elseif($isUpcoming && \Carbon\Carbon::today()->diffInDays($dt) <= 7)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fs-10">
                                                                In {{ \Carbon\Carbon::today()->diffInDays($dt) }} Days
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="fs-11 text-muted d-block font-mono">ID: #{{ $holiday->id }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Date & Day --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-1.5">
                                                <i class="ti ti-calendar text-muted fs-15"></i>
                                                <div>
                                                    <span class="fw-semibold text-dark fs-13">{{ $dt->format('d M, Y') }}</span>
                                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 fs-11 ms-1">
                                                        {{ $dt->format('l') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Category --}}
                                        <td>
                                            @if($isCommon)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-12 fw-semibold d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-star-filled fs-11"></i> Common (Office Closed)
                                                </span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-12 fw-medium d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-calendar-event fs-11"></i> Restricted (Any 5)
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Remarks --}}
                                        <td>
                                            <span class="text-muted fs-13" title="{{ $holiday->holiday_remark }}">
                                                {{ !empty($holiday->holiday_remark) ? \Illuminate\Support\Str::limit($holiday->holiday_remark, 45) : '—' }}
                                            </span>
                                        </td>

                                        {{-- Status: Always Active as requested --}}
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-point-filled fs-12"></i> Active
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-end pe-3">
                                            <div class="d-inline-flex align-items-center gap-1.5">
                                                <button type="button" class="action-btn" title="Quick View" onclick="showHolidayDetails({{ json_encode($holiday) }})">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <a href="javascript:void(0);" class="action-btn btn-delete" title="Delete" onclick="confirmDeleteHoliday({{ $holiday->id }}, '{{ addslashes($holiday->holiday_name) }}')">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-4">
                                                <span class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 d-inline-flex align-items-center justify-content-center">
                                                    <i class="ti ti-calendar-off fs-32"></i>
                                                </span>
                                                <h5 class="fw-bold text-dark mb-1">No Active Holidays Found</h5>
                                                <p class="text-muted fs-13 mb-3">There are no active holidays matching your search or filter parameters.</p>
                                                <a href="{{ route('settings.holiday') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-rotate-clockwise me-1"></i> Clear Filters
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. Pagination Footer --}}
                @if($holidays->hasPages() || $holidays->total() > 0)
                    <div class="card-footer bg-transparent py-3 px-3.5 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="text-muted fs-13">
                            Showing <span class="fw-semibold text-dark">{{ $holidays->firstItem() ?? 0 }}</span> to <span class="fw-semibold text-dark">{{ $holidays->lastItem() ?? 0 }}</span> of <span class="fw-semibold text-dark">{{ $holidays->total() }}</span> active holidays
                        </div>

                        <div>
                            {{ $holidays->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <x-footer />
    </div>

    {{-- Details Quick Modal --}}
    <div class="modal fade" id="holiday_details_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalHolidayTitle">
                        <i class="ti ti-calendar-event text-primary"></i> Holiday Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 text-center">
                        <span class="fs-12 text-muted text-uppercase fw-semibold d-block mb-1">Date & Day</span>
                        <h4 class="mb-0 text-dark fw-bold" id="modalHolidayDate">—</h4>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-0.5 fs-12 mt-1.5" id="modalHolidayDay">—</span>
                    </div>

                    <ul class="list-group list-group-flush fs-13 mb-0">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Festival / Name:</span>
                            <span class="fw-semibold text-dark" id="modalHolidayName">—</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Holiday Category:</span>
                            <span id="modalHolidayCategory">—</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status:</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-11">
                                <i class="ti ti-point-filled me-0.5"></i>Active
                            </span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Description / Remarks:</span>
                            <p class="mb-0 text-dark bg-white border rounded p-2 fs-12" id="modalHolidayRemark">—</p>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-top py-2.5 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <span class="avatar avatar-xl rounded-circle bg-danger-subtle text-danger mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-trash fs-28"></i>
                    </span>
                    <h5 class="mb-1 fw-bold text-dark">Delete Holiday?</h5>
                    <p class="mb-3 text-muted fs-13" id="deleteModalMessage">Are you sure you want to delete this holiday record?</p>
                    <form id="deleteHolidayForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm px-3">Yes, Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after_scripts')
    <script>
        function showHolidayDetails(holiday) {
            let dt = new Date(holiday.holiday_date);
            let formattedDate = dt.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
            let formattedDay = dt.toLocaleDateString('en-US', { weekday: 'long' });
            let isCommon = (holiday.holiday_remark || '').toLowerCase().includes('common');

            document.getElementById('modalHolidayTitle').innerHTML = '<i class="ti ti-calendar-event text-primary"></i> ' + holiday.holiday_name;
            document.getElementById('modalHolidayName').textContent = holiday.holiday_name;
            document.getElementById('modalHolidayDate').textContent = formattedDate;
            document.getElementById('modalHolidayDay').textContent = formattedDay;
            document.getElementById('modalHolidayRemark').textContent = holiday.holiday_remark || 'No description provided.';

            if (isCommon) {
                document.getElementById('modalHolidayCategory').innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-11 fw-semibold"><i class="ti ti-star-filled me-1"></i>Common (Mandatory)</span>';
            } else {
                document.getElementById('modalHolidayCategory').innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fs-11 fw-semibold"><i class="ti ti-calendar me-1"></i>Restricted (Optional)</span>';
            }

            let modal = new bootstrap.Modal(document.getElementById('holiday_details_modal'));
            modal.show();
        }

        function confirmDeleteHoliday(id, name) {
            document.getElementById('deleteModalMessage').textContent = 'Are you sure you want to delete "' + name + '"?';
            document.getElementById('deleteHolidayForm').action = "{{ url('settings/holiday') }}/" + id;

            let modal = new bootstrap.Modal(document.getElementById('delete_modal'));
            modal.show();
        }
    </script>
@endpush