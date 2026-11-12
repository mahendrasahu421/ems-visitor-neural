@extends('layout.master')
@section('title', $title ?? 'Admin Dashboard | EMS Portal')

@section('main-section')
   @php
    $authUser = Auth::user();
    $isSupervisor = $authUser && (
        $authUser->role_id == 6 || 
        (method_exists($authUser, 'hasRole') && $authUser->hasRole('Supervisor'))
    );

    // Indian Time Zone
    $hour = \Carbon\Carbon::now('Asia/Kolkata')->hour;

    if ($hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour < 16) {
        $greeting = 'Good Afternoon';
    } else {
        $greeting = 'Good Evening';
    }

    $avatarColors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777'];
@endphp

    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .dashboard-home {
            background-color: #f8fafc;
        }

        /* Hero Welcome Banner */
        .dashboard-home .welcome-hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #172554 100%);
            border-radius: 18px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 12px 30px -6px rgba(15, 23, 42, 0.25);
            padding: 28px 30px;
        }

        .dashboard-home .welcome-hero-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
            pointer-events: none;
        }

        .dashboard-home .welcome-hero-banner::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: 20%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.18) 0%, rgba(147, 51, 234, 0) 70%);
            pointer-events: none;
        }

        /* KPI Metric Cards */
        .dashboard-home .kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 18px -4px rgba(0, 0, 0, 0.03);
        }

        .dashboard-home .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.08);
        }

        .dashboard-home .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--kpi-accent, #0d6efd);
        }

        .dashboard-home .kpi-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            background: var(--kpi-soft-bg, #eef5ff);
            color: var(--kpi-accent, #0d6efd);
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .dashboard-home .kpi-card:hover .kpi-icon-wrapper {
            transform: scale(1.08);
        }

        /* Quick Action Tiles */
        .dashboard-home .action-shortcut-tile {
            display: flex;
            align-items: center;
            padding: 13px 16px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .dashboard-home .action-shortcut-tile:hover {
            border-color: #3b82f6;
            background: #f8fafc;
            transform: translateX(4px);
            color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
        }

        .dashboard-home .action-icon-pill {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        /* Avatar styling */
        .dashboard-home .avatar-initials {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #ffffff;
            font-size: 13px;
            flex-shrink: 0;
        }

        .dashboard-home .dashboard-card-rounded {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .dashboard-home .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .dashboard-home .stat-progress-bar {
            height: 6px;
            border-radius: 10px;
            background-color: #f1f5f9;
            overflow: hidden;
        }

        .dashboard-home .badge-pill-soft {
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
        }

        .dashboard-home .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
    </style>

    <div class="page-wrapper dashboard-home">
        <div class="content py-3">

            {{-- 1. Top Executive Welcome Banner --}}
            <div class="welcome-hero-banner mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-7 col-md-12">
                        <div class="d-flex align-items-center gap-3">
                            <span class="avatar avatar-xl rounded-circle border border-2 border-white shadow flex-shrink-0" style="width: 58px; height: 58px;">
                                <img src="{{ $imageUrl }}" class="rounded-circle w-100 h-100" alt="{{ $roleName ?? 'Admin' }}" style="object-fit: cover;">
                            </span>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h3 class="mb-0 fw-bold text-white fs-22">{{ $greeting }}, {{ $roleName ?? 'Admin' }}! 👋</h3>
                                    <span class="badge bg-primary bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-2.5 py-0.5 fs-11">
                                        HR & Operations Portal
                                    </span>
                                </div>
                                <p class="text-white-50 fs-13 mb-0 mt-1">
                                    <i class="ti ti-calendar me-1"></i>{{ now()->format('l, d F Y') }} &bull; Employees & Operations Overview
                                </p>
                            </div>
                        </div>

                        {{-- Quick Metrics Chips inside Banner --}}
                        <div class="d-flex flex-wrap gap-2 mt-3 pt-2.5 border-top border-white border-opacity-10">
                            <a href="{{ route('employees.index') }}" class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 px-2.5 py-1.5 fs-12 text-decoration-none hover-white">
                                <i class="ti ti-users me-1 text-info"></i><strong>{{ number_format($activeEmployees) }}</strong> Active Staff (Operational)
                            </a>
                            <a href="{{ route('masters.organisation.agency') }}" class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 px-2.5 py-1.5 fs-12 text-decoration-none hover-white">
                                <i class="ti ti-building-skyscraper me-1 text-purple" style="color: #c084fc;"></i><strong>{{ number_format($totalAgencies) }}</strong> Staffing Agencies
                            </a>
                            <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 px-2.5 py-1.5 fs-12 text-decoration-none hover-white">
                                <i class="ti ti-calendar-check me-1 text-success"></i><strong>{{ number_format($todayAttendances) }}</strong> Present Today
                            </a>
                            @if($todayLateCount > 0)
                                <a href="{{ route('attendance.attendance-dashboard.org-reports', ['punctuality' => 'late']) }}" class="badge bg-warning bg-opacity-25 text-white border border-warning border-opacity-40 px-2.5 py-1.5 fs-12 text-decoration-none hover-white animate-pulse">
                                    <i class="ti ti-clock-pause me-1 text-warning"></i><strong>{{ $todayLateCount }}</strong> Late In Today
                                </a>
                            @endif
                            @if($pendingLeaves > 0)
                                <a href="{{ route('attendance.leave.request') }}" class="badge bg-danger bg-opacity-30 text-white border border-danger border-opacity-40 px-2.5 py-1.5 fs-12 text-decoration-none animate-pulse">
                                    <i class="ti ti-clock-pause me-1 text-warning"></i><strong>{{ $pendingLeaves }}</strong> Leaves Awaiting Approval
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Banner Action Buttons --}}
                    <div class="col-lg-5 col-md-12 d-flex justify-content-lg-end">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('employees.create') }}" class="btn btn-light btn-md fw-semibold shadow-sm d-inline-flex align-items-center">
                                <i class="ti ti-user-plus me-1.5 text-primary"></i>Add Employee
                            </a>
                            <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="btn btn-primary btn-md fw-semibold shadow-sm d-inline-flex align-items-center">
                                <i class="ti ti-chart-bar me-1.5"></i>Attendance Log
                            </a>
                            <a href="{{ route('attendance.leave.request') }}" class="bg-white btn btn-outline-light btn-md fw-semibold d-inline-flex align-items-center">
                                <i class="ti ti-calendar-event me-1.5"></i>Leave Queue
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Six Interactive KPI Cards (Employees, Agencies, Attendance, Latecomers, Early Comers, Leaves) --}}
            <div class="row g-3 mb-4">

                {{-- KPI 1: Employees Overview --}}
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    <div class="card kpi-card h-100 mb-0" style="--kpi-accent: #4f46e5; --kpi-soft-bg: #eef2ff;">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-11 fw-bold text-uppercase tracking-wider text-truncate" title="Active Employees">Employees</span>
                                <a href="{{ route('employees.index') }}" class="kpi-icon-wrapper text-decoration-none" title="View Directory">
                                    <i class="ti ti-users"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-bold text-dark fs-26">{{ number_format($activeEmployees) }}</h2>
                                <span class="badge bg-success-subtle text-success fs-10 px-2 py-0.5 rounded-pill fw-semibold">
                                    <i class="ti ti-circle-check me-0.5"></i>Active
                                </span>
                            </div>
                            <div class="stat-progress-bar mt-2.5 mb-2">
                                <div class="bg-success h-100" style="width: 100%;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                <span class="d-flex align-items-center"><span class="pulse-dot bg-success"></span>{{ $activeEmployees }} Active</span>
                                <span class="d-flex align-items-center text-muted" title="{{ $inactiveEmployees }} Inactive excluded"><span class="pulse-dot bg-secondary"></span>{{ $inactiveEmployees }} Off</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 2: Staffing Agencies & Third-Party --}}
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    <div class="card kpi-card h-100 mb-0" style="--kpi-accent: #8b5cf6; --kpi-soft-bg: #f5f3ff;">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-11 fw-bold text-uppercase tracking-wider text-truncate" title="Partner Agencies">Agencies</span>
                                <a href="{{ route('masters.organisation.agency') }}" class="kpi-icon-wrapper text-decoration-none" title="Agencies">
                                    <i class="ti ti-building-skyscraper" style="color: #8b5cf6;"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-bold text-dark fs-26">{{ number_format($totalAgencies) }}</h2>
                                <span class="badge fs-10 px-2 py-0.5 rounded-pill fw-semibold" style="background-color: #ede9fe; color: #7c3aed;">
                                    Vendors
                                </span>
                            </div>
                            <div class="stat-progress-bar mt-2.5 mb-2">
                                @php
                                    $agencyPerc = $totalEmployees > 0 ? round(($agencyEmployees / $totalEmployees) * 100) : 0;
                                @endphp
                                <div class="h-100" style="width: {{ $agencyPerc }}%; background-color: #8b5cf6;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                <span><i class="ti ti-building me-1 text-purple"></i>{{ $agencyEmployees }} Agency</span>
                                <span><i class="ti ti-home me-1 text-secondary"></i>{{ $directEmployees }} In-House</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 3: Today's Attendance --}}
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    <div class="card kpi-card h-100 mb-0" style="--kpi-accent: #0284c7; --kpi-soft-bg: #f0f9ff;">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-11 fw-bold text-uppercase tracking-wider text-truncate" title="Today's Attendance">Attendance</span>
                                <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="kpi-icon-wrapper text-decoration-none" title="Attendance Reports">
                                    <i class="ti ti-calendar-check" style="color: #0284c7;"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                <h2 class="mb-0 fw-bold text-dark fs-26">{{ number_format($todayAttendances) }}</h2>
                                @php
                                    $attendanceRate = $activeEmployees > 0 ? round(($todayAttendances / $activeEmployees) * 100) : 0;
                                @endphp
                                <span class="badge bg-info-subtle text-info fs-10 px-2 py-0.5 rounded-pill fw-semibold">
                                    {{ $attendanceRate }}% Present
                                </span>
                            </div>
                            <div class="stat-progress-bar mt-2.5 mb-2">
                                <div class="bg-info h-100" style="width: {{ min(100, $attendanceRate) }}%;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                <span><i class="ti ti-check me-1 text-success"></i>{{ $todayOnTimeCount }} On Time</span>
                                <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="text-primary text-decoration-none fw-semibold">Register &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 4: Latecomers (Arrived after shift start + grace) --}}
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    <div class="card kpi-card h-100 mb-0" style="--kpi-accent: #ef4444; --kpi-soft-bg: #fef2f2;">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-11 fw-bold text-uppercase tracking-wider text-truncate" title="Latecomers (After Grace Time)">Latecomers</span>
                                <a href="{{ route('attendance.attendance-dashboard.org-reports', ['punctuality' => 'late', 'view' => 'logs']) }}" class="kpi-icon-wrapper text-decoration-none" title="View Late Arrivals">
                                    <i class="ti ti-clock-pause" style="color: #ef4444;"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-bold {{ $todayLateCount > 0 ? 'text-danger' : 'text-dark' }} fs-26">
                                    {{ number_format($todayLateCount) }}
                                </h2>
                                @if($todayLateCount > 0)
                                    <span class="badge bg-danger-subtle text-danger fs-10 px-2 py-0.5 rounded-pill fw-semibold">
                                        <i class="ti ti-alert-triangle me-0.5"></i>Late In
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success fs-10 px-2 py-0.5 rounded-pill fw-semibold">
                                        <i class="ti ti-circle-check me-0.5"></i>All On Time
                                    </span>
                                @endif
                            </div>
                            @php
                                $latePerc = $todayAttendances > 0 ? round(($todayLateCount / $todayAttendances) * 100) : 0;
                            @endphp
                            <div class="stat-progress-bar mt-2.5 mb-2">
                                <div class="bg-danger h-100" style="width: {{ min(100, $latePerc) }}%;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                <span><i class="ti ti-alarm me-1 text-danger"></i>After Grace</span>
                                <a href="{{ route('attendance.attendance-dashboard.org-reports', ['punctuality' => 'late', 'view' => 'logs']) }}" class="text-danger text-decoration-none fw-semibold">View &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 5: Early Comers (Arrived before shift start) --}}
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    <div class="card kpi-card h-100 mb-0" style="--kpi-accent: #06b6d4; --kpi-soft-bg: #ecfeff;">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-11 fw-bold text-uppercase tracking-wider text-truncate" title="Early Comers (Before Shift Time)">Early Comers</span>
                                <a href="{{ route('attendance.attendance-dashboard.org-reports', ['punctuality' => 'early_in', 'view' => 'logs']) }}" class="kpi-icon-wrapper text-decoration-none" title="View Early Arrivals">
                                    <i class="ti ti-clock-play" style="color: #0891b2;"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-bold fs-26" style="color: #0891b2 !important;">
                                    {{ number_format($todayEarlyComerCount) }}
                                </h2>
                                @if($todayEarlyComerCount > 0)
                                    <span class="badge fs-10 px-2 py-0.5 rounded-pill fw-semibold" style="background-color: #cffafe; color: #0e7490;">
                                        <i class="ti ti-sparkles me-0.5"></i>Early In
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary fs-10 px-2 py-0.5 rounded-pill fw-semibold">
                                        Standard
                                    </span>
                                @endif
                            </div>
                            @php
                                $earlyPerc = $todayAttendances > 0 ? round(($todayEarlyComerCount / $todayAttendances) * 100) : 0;
                            @endphp
                            <div class="stat-progress-bar mt-2.5 mb-2">
                                <div class="h-100" style="width: {{ min(100, $earlyPerc) }}%; background-color: #06b6d4;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                <span><i class="ti ti-clock-check me-1" style="color: #0891b2;"></i>Before Shift</span>
                                <a href="{{ route('attendance.attendance-dashboard.org-reports', ['punctuality' => 'early_in', 'view' => 'logs']) }}" class="text-info text-decoration-none fw-semibold">View &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 6: Pending Approvals & Early Departures --}}
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    <div class="card kpi-card h-100 mb-0" style="--kpi-accent: #dc2626; --kpi-soft-bg: #fef2f2;">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-11 fw-bold text-uppercase tracking-wider text-truncate" title="Leave Approvals">Leaves</span>
                                <a href="{{ route('attendance.leave.request') }}" class="kpi-icon-wrapper text-decoration-none" title="Leave Queue">
                                    <i class="ti ti-calendar-event" style="color: #dc2626;"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="mb-0 fw-bold {{ $pendingLeaves > 0 ? 'text-danger' : 'text-dark' }} fs-26">
                                    {{ number_format($pendingLeaves) }}
                                </h2>
                                <span class="badge {{ $pendingLeaves > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} fs-10 px-2 py-0.5 rounded-pill fw-semibold">
                                    {{ $pendingLeaves > 0 ? 'Action Req' : 'All Clear' }}
                                </span>
                            </div>
                            <div class="stat-progress-bar mt-2.5 mb-2">
                                <div class="bg-danger h-100" style="width: {{ $pendingLeaves > 0 ? min(100, $pendingLeaves * 20) : 0 }}%;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                <span><i class="ti ti-clock-stop me-1 text-warning"></i>{{ $todayEarlyCount }} Left Early</span>
                                <a href="{{ route('attendance.leave.request') }}" class="text-primary text-decoration-none fw-semibold">Review &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. Middle Tri-Section: Employees Allocation + Quick Operations Hub + Pending Leaves --}}
            <div class="row g-3 mb-4">

                {{-- Card A: Employees Allocation & Structure --}}
                <div class="col-lg-4 d-flex">
                    <div class="card dashboard-card-rounded flex-fill">
                        <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-chart-pie fs-14"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark fs-14">Employees Composition</h6>
                            </div>
                            <a href="{{ route('employees.index') }}" class="btn btn-xs btn-outline-primary">Directory</a>
                        </div>
                        <div class="card-body p-3.5">
                            {{-- Ratio Visualization --}}
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="fs-12 fw-semibold text-dark">Staff Employment Distribution</span>
                                    <span class="badge bg-primary text-white fs-11 font-monospace">{{ $totalEmployees }} Staff</span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 6px;">
                                    @php
                                        $agencyW = $totalEmployees > 0 ? ($agencyEmployees / $totalEmployees) * 100 : 0;
                                        $directW = $totalEmployees > 0 ? ($directEmployees / $totalEmployees) * 100 : 100;
                                    @endphp
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $directW }}%" title="Direct: {{ $directEmployees }}"></div>
                                    <div class="progress-bar" role="progressbar" style="width: {{ $agencyW }}%; background-color: #8b5cf6;" title="Agency: {{ $agencyEmployees }}"></div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-2 fs-11 text-muted">
                                    <span><i class="ti ti-point-filled text-primary"></i> Direct / In-House: <strong>{{ $directEmployees }}</strong></span>
                                    <span><i class="ti ti-point-filled" style="color: #8b5cf6;"></i> Agency Staff: <strong>{{ $agencyEmployees }}</strong></span>
                                </div>
                            </div>

                            {{-- Departments List --}}
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fs-12 fw-bold text-uppercase text-muted tracking-wider">Top Departments</span>
                                <span class="badge bg-light text-secondary border fs-10">{{ $departments->count() }} Configured</span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @forelse($departments as $dept)
                                    <div class="p-2 rounded-2 border bg-white d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar avatar-xs rounded-circle bg-light text-primary border fw-bold fs-11 d-inline-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($dept->department_name, 0, 2)) }}
                                            </span>
                                            <div>
                                                <h6 class="fs-12 fw-semibold text-dark mb-0">{{ $dept->department_name }}</h6>
                                                <span class="fs-10 text-muted">Head: {{ $dept->department_head ?: 'Unassigned' }}</span>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-primary border font-monospace fs-10">{{ $dept->department_code ?: 'DEPT' }}</span>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3 fs-12">No departments found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card B: Quick Operations Hub --}}
                <div class="col-lg-4 d-flex">
                    <div class="card dashboard-card-rounded flex-fill">
                        <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-bolt fs-14"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark fs-14">Quick Operations</h6>
                            </div>
                            <span class="badge bg-light text-secondary border fs-11">Shortcuts</span>
                        </div>
                        <div class="card-body p-3.5 d-flex flex-column gap-2">
                            <a href="{{ route('employees.index') }}" class="action-shortcut-tile">
                                <span class="action-icon-pill bg-primary-subtle text-primary">
                                    <i class="ti ti-users"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold fs-13">Employee Directory</div>
                                    <div class="fs-11 text-muted">View roster, profiles & status</div>
                                </div>
                                <i class="ti ti-chevron-right text-muted fs-13"></i>
                            </a>

                            <a href="{{ route('employees.create') }}" class="action-shortcut-tile">
                                <span class="action-icon-pill bg-success-subtle text-success">
                                    <i class="ti ti-user-plus"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold fs-13">Register New Employee</div>
                                    <div class="fs-11 text-muted">Onboard staff with agency/roles</div>
                                </div>
                                <i class="ti ti-chevron-right text-muted fs-13"></i>
                            </a>

                            <a href="{{ route('masters.organisation.agency.mapping') }}" class="action-shortcut-tile">
                                <span class="action-icon-pill text-purple" style="background-color: #f3e8ff; color: #7e22ce;">
                                    <i class="ti ti-arrows-exchange"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold fs-13">Staff Agency Mapping</div>
                                    <div class="fs-11 text-muted">Assign employees to staffing vendors</div>
                                </div>
                                <i class="ti ti-chevron-right text-muted fs-13"></i>
                            </a>

                            <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="action-shortcut-tile">
                                <span class="action-icon-pill bg-info-subtle text-info">
                                    <i class="ti ti-calendar-stats"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold fs-13">Attendance Register</div>
                                    <div class="fs-11 text-muted">Daily punch logs & monthly sheets</div>
                                </div>
                                <i class="ti ti-chevron-right text-muted fs-13"></i>
                            </a>

                            <a href="{{ route('attendance.leave.request') }}" class="action-shortcut-tile">
                                <span class="action-icon-pill bg-danger-subtle text-danger">
                                    <i class="ti ti-calendar-event"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold fs-13">Leave Approvals</div>
                                    <div class="fs-11 text-muted">Review pending applications</div>
                                </div>
                                <span class="badge {{ $pendingLeaves > 0 ? 'bg-danger text-white' : 'bg-light text-muted' }} rounded-pill px-2 py-0.5 fs-11">
                                    {{ $pendingLeaves }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Card C: Pending Leave Requests --}}
                <div class="col-lg-4 d-flex">
                    <div class="card dashboard-card-rounded flex-fill">
                        <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-clock-pause fs-14"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark fs-14">Leave Queue</h6>
                            </div>
                            <a href="{{ route('attendance.leave.request') }}" class="btn btn-xs btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="d-flex flex-column gap-2.5">
                                @forelse($recentLeaves as $leave)
                                    <div class="p-2.5 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="fw-semibold fs-13 text-dark">{{ $leave->employee_name ?: 'Employee #' . $leave->employee_id }}</div>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-10 px-2 py-0.5 rounded-pill">
                                                {{ $leave->status ?: 'Pending' }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between fs-11 text-muted">
                                            <span><i class="ti ti-calendar me-1"></i>{{ $leave->from_date }} &rarr; {{ $leave->to_date }}</span>
                                            <span class="badge bg-white text-secondary border fs-10">{{ $leave->leave_name ?: 'Casual Leave' }}</span>
                                        </div>
                                        @if(!empty($leave->reason))
                                            <div class="fs-11 text-muted mt-1 text-truncate" title="{{ $leave->reason }}">
                                                <i class="ti ti-message-2 me-1"></i>{{ $leave->reason }}
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <span class="avatar avatar-lg rounded-circle bg-success-subtle text-success mb-2 d-inline-flex align-items-center justify-content-center">
                                            <i class="ti ti-circle-check fs-28"></i>
                                        </span>
                                        <h6 class="fs-13 fw-semibold text-dark mb-0.5">No Pending Leaves</h6>
                                        <p class="fs-11 text-muted mb-0">All employee leave requests have been reviewed.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 4. Bottom Section: Recent Employees Roster --}}
            <div class="row g-3">
                <div class="col-12">
                    <div class="card dashboard-card-rounded">
                        <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-user-check fs-14"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark fs-14">Active Employees Roster</h6>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 ms-1">Active Only</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-users"></i> Open Full Employee Directory
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Work Shift</th>
                                            <th>Today's Punch</th>
                                            <th>Punctuality</th>
                                            <th>Designation & Agency</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentEmployees as $idx => $emp)
                                            @php
                                                $name = $emp->employee_name ?? 'Employee';
                                                $words = preg_split('/\s+/', trim($name));
                                                $initials = count($words) >= 2 && !empty($words[0]) && !empty($words[1])
                                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                                    : strtoupper(substr($name, 0, 2));
                                                $bgCol = $avatarColors[$idx % count($avatarColors)];
                                                $shift = $emp->shift;
                                                $todayAtt = $todayAttendanceMap[$emp->employee_id] ?? null;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if(!empty($emp->profile_image) && $emp->profile_image !== 'default.png')
                                                             <img src="{{ asset('uploads/employees/' . $emp->profile_image) }}" alt="{{ $name }}" class="rounded-circle me-2 border" width="36" height="36" style="object-fit: cover;">
                                                        @else
                                                            <span class="avatar-initials me-2" style="background-color: {{ $bgCol }};">
                                                                {{ $initials }}
                                                            </span>
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold text-dark fs-13">{{ $name }}</div>
                                                            <div class="fs-11 text-muted">{{ $emp->employee_email ?: ($emp->emp_id_code ?: '-') }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($shift)
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-semibold text-dark fs-12">{{ $shift->shift_name }}</span>
                                                            <span class="fs-11 text-muted">
                                                                <i class="ti ti-clock me-1"></i>{{ date('h:i A', strtotime($shift->start_time)) }} - {{ date('h:i A', strtotime($shift->end_time)) }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted border fs-11">General Shift</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($todayAtt)
                                                        <div>
                                                            <span class="fs-12 fw-medium text-dark"><i class="ti ti-login text-success me-1"></i>{{ $todayAtt['check_in_at'] }}</span>
                                                            @if($todayAtt['check_out_at'])
                                                                <span class="text-muted mx-1">|</span>
                                                                <span class="fs-12 fw-medium text-dark"><i class="ti ti-logout text-danger me-1"></i>{{ $todayAtt['check_out_at'] }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="badge bg-light text-muted border fs-11">
                                                            <i class="ti ti-hourglass-empty me-1"></i>Pending Punch
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($todayAtt)
                                                        <div class="d-flex align-items-center flex-wrap gap-1">
                                                            @if($todayAtt['eval']['is_late'])
                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11">
                                                                    <i class="ti ti-clock-exclamation me-1"></i>Late In ({{ $todayAtt['eval']['late_formatted'] }})
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">
                                                                    <i class="ti ti-clock-check me-1"></i>On Time
                                                                </span>
                                                            @endif
                                                            @if($todayAtt['eval']['is_early_out'])
                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">
                                                                    <i class="ti ti-clock-x me-1"></i>Early Out ({{ $todayAtt['eval']['early_minutes'] }}m)
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted fs-11">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-dark fs-12">{{ $emp->designation?->name ?: ($emp->designation?->designation_name ?: 'Staff') }}</div>
                                                    @if(!empty($emp->agency_id) && !empty($emp->agency))
                                                        <span class="badge border py-0.5 px-1.5 fs-10 fw-normal" style="background-color: #f3e8ff; color: #7e22ce; border-color: #d8b4fe !important;">
                                                            {{ $emp->agency->agency_name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted fs-11">Direct</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12" title="View in Directory">
                                                        <i class="ti ti-eye me-1"></i>View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No active employees found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <x-footer />
    </div>
@endsection
