@extends('layout.master')
@section('title', $title ?? 'Consistent Attendees Leaderboard')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .consistent-attendees-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .consistent-attendees-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .consistent-attendees-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .consistent-attendees-page .kpi-icon-box {
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

        .consistent-attendees-page .filter-bar-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            padding: 20px 22px;
        }

        .consistent-attendees-page .filter-pill-btn {
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .consistent-attendees-page .filter-pill-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .consistent-attendees-page .filter-pill-btn.active {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
        }

        .consistent-attendees-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .consistent-attendees-page .emp-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .consistent-attendees-page .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        .consistent-attendees-page .rank-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(245, 158, 11, 0.4);
        }

        .consistent-attendees-page .rank-silver {
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(148, 163, 184, 0.4);
        }

        .consistent-attendees-page .rank-bronze {
            background: linear-gradient(135deg, #b45309 0%, #78350f 100%);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(180, 83, 9, 0.4);
        }

        .consistent-attendees-page .rank-normal {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .consistent-attendees-page .streak-badge {
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #ffedd5;
            font-weight: 600;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .consistent-attendees-page .progress {
            height: 7px;
            border-radius: 10px;
            background-color: #f1f5f9;
        }

        .consistent-attendees-page .podium-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #ffffff;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .consistent-attendees-page .podium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.08);
        }

        .consistent-attendees-page .podium-1 {
            border-top: 4px solid #f59e0b;
            background: linear-gradient(180deg, #fffbeb 0%, #ffffff 50%);
        }

        .consistent-attendees-page .podium-2 {
            border-top: 4px solid #94a3b8;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 50%);
        }

        .consistent-attendees-page .podium-3 {
            border-top: 4px solid #b45309;
            background: linear-gradient(180deg, #fff7ed 0%, #ffffff 50%);
        }
    </style>

    <div class="page-wrapper consistent-attendees-page">
        <div class="content">

            <!-- Breadcrumb / Header Title -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1 text-dark fw-bold">
                        <i class="ti ti-award text-warning me-1"></i> Consistent Attendees & Leaderboard
                    </h2>
                    <p class="text-muted mb-0 fs-13">
                        Track monthly attendance consistency, consecutive streaks & punctuality benchmarks.
                    </p>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm" onclick="exportTableToCSV('consistent_attendees.csv')">
                        <i class="ti ti-download me-1"></i> Export CSV
                    </button>
                    <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="btn btn-outline-primary btn-sm shadow-sm">
                        <i class="ti ti-report me-1"></i> Attendance Logs
                    </a>
                    <a href="{{ url()->current() }}" class="btn btn-primary btn-sm shadow-sm">
                        <i class="ti ti-refresh me-1"></i> Refresh
                    </a>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- 4 Interactive KPI Cards -->
            <div class="row g-3 mb-4">
                {{-- Card 1: Top Consistency (>=90%) --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ request()->fullUrlWithQuery(['tier' => 'top']) }}" class="stat-kpi-card h-100 p-3" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-semibold">Consistent Stars (&ge;90%)</span>
                                <h3 class="mb-1 mt-1 fw-bold text-dark">{{ $topCount }}</h3>
                                <div class="d-flex align-items-center gap-1 text-success fs-12">
                                    <i class="ti ti-trophy fs-13"></i>
                                    <span>High regularity staff</span>
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-medal"></i>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Card 2: Moderately Regular (75-89%) --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ request()->fullUrlWithQuery(['tier' => 'good']) }}" class="stat-kpi-card h-100 p-3" style="--card-accent: #0284c7; --card-soft-bg: #f0f9ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-semibold">Moderately Regular (75-89%)</span>
                                <h3 class="mb-1 mt-1 fw-bold text-dark">{{ $goodCount }}</h3>
                                <div class="d-flex align-items-center gap-1 text-info fs-12">
                                    <i class="ti ti-check-circle fs-13"></i>
                                    <span>Standard attendance</span>
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-calendar-check"></i>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Card 3: Needs Attention (<75%) --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ request()->fullUrlWithQuery(['tier' => 'low']) }}" class="stat-kpi-card h-100 p-3" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-semibold">Needs Attention (&lt;75%)</span>
                                <h3 class="mb-1 mt-1 fw-bold text-danger">{{ $lowCount }}</h3>
                                <div class="d-flex align-items-center gap-1 text-danger fs-12">
                                    <i class="ti ti-alert-triangle fs-13"></i>
                                    <span>Low / irregular</span>
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-clock-pause"></i>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Card 4: Org Consistency Benchmark --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 p-3" style="--card-accent: #8b5cf6; --card-soft-bg: #f5f3ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-semibold">Avg Org Consistency</span>
                                <h3 class="mb-1 mt-1 fw-bold text-dark">{{ $avgOrgConsistency }}%</h3>
                                <div class="d-flex align-items-center gap-1 text-muted fs-12">
                                    <i class="ti ti-flame text-warning fs-13"></i>
                                    <span>Max streak: <strong>{{ $maxOrgStreak }} Days</strong></span>
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-chart-arrows"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 3 Spotlight Podium (Rendered if at least 3 candidates exist and tier is not 'low') -->
            @if(count($attendeesList) >= 3 && in_array($tierFilter, ['all', 'top'], true))
                @php
                    $first = $attendeesList[0] ?? null;
                    $second = $attendeesList[1] ?? null;
                    $third = $attendeesList[2] ?? null;
                @endphp
                <div class="row g-3 mb-4">
                    {{-- 2nd Place --}}
                    @if($second)
                        <div class="col-md-4 order-md-1 order-2">
                            <div class="card podium-card podium-2 p-3 text-center h-100">
                                <div class="position-absolute top-0 start-50 translate-middle mt-3">
                                    <span class="rank-badge rank-silver">2</span>
                                </div>
                                <div class="mt-4 pt-2">
                                    <div class="emp-avatar mx-auto mb-2" style="background: linear-gradient(135deg, #94a3b8, #64748b); width: 48px; height: 48px; font-size: 16px;">
                                        {{ strtoupper(substr($second['employee']->employee_name ?? 'EM', 0, 2)) }}
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $second['employee']->employee_name }}</h6>
                                    <p class="text-muted fs-12 mb-2">{{ $second['employee']->designation?->name ?: 'Staff' }}</p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fs-13 fw-bold">{{ $second['consistency_pct'] }}%</span>
                                        <span class="streak-badge"><i class="ti ti-flame"></i> {{ $second['streak'] }}d streak</span>
                                    </div>
                                    <span class="fs-11 text-muted">{{ $second['present_days'] }} / {{ $second['working_days'] }} Working Days</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 1st Place (Winner) --}}
                    @if($first)
                        <div class="col-md-4 order-md-2 order-1">
                            <div class="card podium-card podium-1 p-3 text-center h-100 shadow-sm">
                                <div class="position-absolute top-0 start-50 translate-middle mt-3">
                                    <span class="rank-badge rank-gold" style="width: 38px; height: 38px; font-size: 15px;">🥇</span>
                                </div>
                                <div class="mt-4 pt-2">
                                    <div class="emp-avatar mx-auto mb-2" style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 56px; height: 56px; font-size: 18px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                                        {{ strtoupper(substr($first['employee']->employee_name ?? 'EM', 0, 2)) }}
                                    </div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $first['employee']->employee_name }}</h5>
                                    <p class="text-muted fs-12 mb-2">{{ $first['employee']->designation?->name ?: 'Staff' }}</p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                        <span class="badge bg-success fs-14 fw-bold px-3 py-1">{{ $first['consistency_pct'] }}% Rate</span>
                                        <span class="streak-badge" style="background: #ffedd5; color: #c2410c;"><i class="ti ti-flame"></i> {{ $first['streak'] }}d streak</span>
                                    </div>
                                    <span class="fs-12 text-muted fw-semibold">{{ $first['present_days'] }} / {{ $first['working_days'] }} Working Days &bull; Avg: {{ $first['avg_hours'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3rd Place --}}
                    @if($third)
                        <div class="col-md-4 order-md-3 order-3">
                            <div class="card podium-card podium-3 p-3 text-center h-100">
                                <div class="position-absolute top-0 start-50 translate-middle mt-3">
                                    <span class="rank-badge rank-bronze">3</span>
                                </div>
                                <div class="mt-4 pt-2">
                                    <div class="emp-avatar mx-auto mb-2" style="background: linear-gradient(135deg, #b45309, #78350f); width: 48px; height: 48px; font-size: 16px;">
                                        {{ strtoupper(substr($third['employee']->employee_name ?? 'EM', 0, 2)) }}
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $third['employee']->employee_name }}</h6>
                                    <p class="text-muted fs-12 mb-2">{{ $third['employee']->designation?->name ?: 'Staff' }}</p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                        <span class="badge bg-success-subtle text-success fs-13 fw-bold">{{ $third['consistency_pct'] }}%</span>
                                        <span class="streak-badge"><i class="ti ti-flame"></i> {{ $third['streak'] }}d streak</span>
                                    </div>
                                    <span class="fs-11 text-muted">{{ $third['present_days'] }} / {{ $third['working_days'] }} Working Days</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Filter Card -->
            <div class="filter-bar-card mb-4">
                <form method="GET" action="{{ route('attendance.daily-attendance.consistent-attendees') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        {{-- Month Selector --}}
                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Month</label>
                            <select name="month" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                @for($m = 1; $m <= 12; $m++)
                                    @php $monthName = \Carbon\Carbon::create(null, $m, 1)->format('F'); @endphp
                                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                        {{ $monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Year Selector --}}
                        <div class="col-lg-2 col-md-4 col-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Year</label>
                            <select name="year" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                @for($y = (int) date('Y'); $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Search Employee --}}
                        <div class="col-lg-3 col-md-4 col-12">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Search Employee</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Name, Email, or ID..." id="searchInput">
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-lg-5 col-md-12 d-flex gap-2 justify-content-lg-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="ti ti-filter me-1"></i> Apply Filter
                            </button>
                            <a href="{{ route('attendance.daily-attendance.consistent-attendees') }}" class="btn btn-light btn-sm border px-3">
                                <i class="ti ti-x me-1"></i> Reset
                            </a>
                        </div>
                    </div>

                    <!-- Quick Filter Badges Row -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-3 border-top">
                        <div class="d-flex flex-wrap align-items-center gap-1.5">
                            <span class="fs-12 text-muted fw-semibold me-1">Consistency Tier:</span>
                            <a href="{{ request()->fullUrlWithQuery(['tier' => 'all']) }}" class="filter-pill-btn {{ $tierFilter === 'all' ? 'active' : '' }}">
                                All Tiers ({{ $totalEmpCount }})
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tier' => 'top']) }}" class="filter-pill-btn {{ $tierFilter === 'top' ? 'active' : '' }}">
                                <i class="ti ti-trophy text-warning"></i> Stars &ge;90% ({{ $topCount }})
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tier' => 'good']) }}" class="filter-pill-btn {{ $tierFilter === 'good' ? 'active' : '' }}">
                                <i class="ti ti-check text-primary"></i> Regular 75-89% ({{ $goodCount }})
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tier' => 'low']) }}" class="filter-pill-btn {{ $tierFilter === 'low' ? 'active' : '' }}">
                                <i class="ti ti-alert-triangle text-danger"></i> Low &lt;75% ({{ $lowCount }})
                            </a>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-1.5">
                            <span class="fs-12 text-muted fw-semibold me-1">Staff Status:</span>
                            <a href="{{ request()->fullUrlWithQuery(['staff_status' => 'active']) }}" class="filter-pill-btn {{ $staffStatus === 'active' ? 'active' : '' }}">
                                Active Staff
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['staff_status' => 'inactive']) }}" class="filter-pill-btn {{ $staffStatus === 'inactive' ? 'active' : '' }}">
                                Inactive
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['staff_status' => 'all']) }}" class="filter-pill-btn {{ $staffStatus === 'all' ? 'active' : '' }}">
                                All
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /Filter Card -->

            <!-- Main Leaderboard Table Card -->
            <div class="table-container-card">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-xs bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-list-numbers fs-14"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-dark">
                            Attendance Rankings &bull; {{ \Carbon\Carbon::create(null, $selectedMonth, 1)->format('F') }} {{ $selectedYear }}
                        </h6>
                        <span class="badge bg-light text-secondary border fs-11">{{ count($attendeesList) }} Staff</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="tableQuickSearch" class="form-control form-control-sm" placeholder="Quick table search..." style="width: 200px;">
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 nowrap" id="leaderboardTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3.5 text-center" style="width: 60px;">Rank</th>
                                    <th>Employee</th>
                                    <th>Designation</th>
                                    <th>Monthly Present</th>
                                    <th>Consecutive Streak</th>
                                    <th>Consistency %</th>
                                    <th>Avg Hours</th>
                                    <th>Tier Status</th>
                                    <th class="pe-3.5 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $avatarGradients = [
                                        'linear-gradient(135deg, #4f46e5 0%, #3730a3 100%)',
                                        'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                                        'linear-gradient(135deg, #059669 0%, #047857 100%)',
                                        'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
                                        'linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)',
                                        'linear-gradient(135deg, #db2777 0%, #be185d 100%)',
                                    ];
                                @endphp
                                @forelse($attendeesList as $index => $item)
                                    @php
                                        $emp = $item['employee'];
                                        $rank = $index + 1;
                                        $words = preg_split('/\s+/', trim($emp->employee_name ?? ''));
                                        $initials = count($words) >= 2 && !empty($words[0]) && !empty($words[1])
                                            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                            : strtoupper(substr($words[0] ?? 'EM', 0, 2));
                                        $gradient = $avatarGradients[$index % count($avatarGradients)];
                                    @endphp
                                    <tr>
                                        {{-- Rank Column --}}
                                        <td class="ps-3.5 text-center">
                                            @if($rank === 1)
                                                <span class="rank-badge rank-gold" title="Rank 1 - Gold Winner">🥇</span>
                                            @elseif($rank === 2)
                                                <span class="rank-badge rank-silver" title="Rank 2 - Silver">🥈</span>
                                            @elseif($rank === 3)
                                                <span class="rank-badge rank-bronze" title="Rank 3 - Bronze">🥉</span>
                                            @else
                                                <span class="rank-badge rank-normal">{{ $rank }}</span>
                                            @endif
                                        </td>

                                        {{-- Employee Name / Details --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="emp-avatar" style="background: {{ $gradient }};">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">{{ $emp->employee_name }}</div>
                                                    <div class="fs-11 text-muted">
                                                        <i class="ti ti-mail me-1"></i>{{ $emp->employee_email }} &bull; <span class="text-secondary font-monospace">#{{ $emp->employee_id }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Designation --}}
                                        <td>
                                            <span class="badge bg-light text-dark border fs-12 fw-medium">
                                                {{ $emp->designation?->name ?: 'Staff' }}
                                            </span>
                                        </td>

                                        {{-- Monthly Present / Working Days Progress --}}
                                        <td style="min-width: 160px;">
                                            <div class="d-flex justify-content-between align-items-center fs-12 mb-1">
                                                <span class="fw-semibold text-dark">{{ $item['present_days'] }} / {{ $item['working_days'] }} Days</span>
                                                <span class="text-muted fs-11">{{ $item['consistency_pct'] }}%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar {{ $item['consistency_pct'] >= 90 ? 'bg-success' : ($item['consistency_pct'] >= 75 ? 'bg-primary' : 'bg-danger') }}"
                                                     role="progressbar"
                                                     style="width: {{ $item['consistency_pct'] }}%;"
                                                     aria-valuenow="{{ $item['consistency_pct'] }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Consecutive Streak --}}
                                        <td>
                                            @if($item['streak'] > 0)
                                                <span class="streak-badge">
                                                    <i class="ti ti-flame text-danger"></i> {{ $item['streak'] }} {{ Str::plural('Day', $item['streak']) }}
                                                </span>
                                            @else
                                                <span class="text-muted fs-12">—</span>
                                            @endif
                                        </td>

                                        {{-- Consistency Rate Badge --}}
                                        <td>
                                            @if($item['consistency_pct'] >= 90)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-12 fw-bold px-2 py-1">
                                                    <i class="ti ti-arrow-up-right me-0.5"></i>{{ $item['consistency_pct'] }}%
                                                </span>
                                            @elseif($item['consistency_pct'] >= 75)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 fw-bold px-2 py-1">
                                                    {{ $item['consistency_pct'] }}%
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-12 fw-bold px-2 py-1">
                                                    <i class="ti ti-arrow-down-right me-0.5"></i>{{ $item['consistency_pct'] }}%
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Average Hours --}}
                                        <td>
                                            <span class="fs-13 fw-semibold text-secondary">
                                                <i class="ti ti-clock me-1 text-muted"></i>{{ $item['avg_hours'] }}
                                            </span>
                                        </td>

                                        {{-- Tier Status Badge --}}
                                        <td>
                                            <span class="badge {{ $item['tier_badge'] }} fs-11 px-2.5 py-1">
                                                {{ $item['tier_label'] }}
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="pe-3.5 text-end">
                                            <a href="{{ route('attendance.attendance-dashboard.org-reports.employee-monthly', $emp->employee_id) }}?month={{ $selectedMonth }}&year={{ $selectedYear }}"
                                               class="btn btn-xs btn-outline-primary shadow-sm"
                                               title="View Full Monthly Attendance Log">
                                                <i class="ti ti-calendar-event me-1"></i> View Log
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="py-4">
                                                <div class="avatar avatar-xl bg-light text-muted rounded-circle mx-auto mb-3 d-inline-flex align-items-center justify-content-center">
                                                    <i class="ti ti-users-minus fs-32"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark">No Consistent Attendees Found</h6>
                                                <p class="text-muted fs-13 mb-3">No attendance records match the selected month, year, or tier filters.</p>
                                                <a href="{{ route('attendance.daily-attendance.consistent-attendees') }}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-refresh me-1"></i> Clear Filters
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-light bg-opacity-50 py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap fs-12 text-muted border-top">
                    <div>
                        Showing <strong>{{ count($attendeesList) }}</strong> employee records for <strong>{{ \Carbon\Carbon::create(null, $selectedMonth, 1)->format('F Y') }}</strong>
                    </div>
                    <div>
                        Formula: <code>(Present Days / {{ $totalWorkingDays }} Working Days) &times; 100</code>
                    </div>
                </div>
            </div>
            <!-- /Main Leaderboard Table Card -->

        </div>

        <x-footer />
    </div>

    {{-- Client-side Search and CSV Export Script --}}
    <script>
        // Table Quick Filter
        document.getElementById('tableQuickSearch')?.addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#leaderboardTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Export Table to CSV
        function exportTableToCSV(filename) {
            const rows = document.querySelectorAll('#leaderboardTable tr');
            let csv = [];
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                const cols = row.querySelectorAll('th, td');
                let rowData = [];
                cols.forEach((col, index) => {
                    // Skip the last column (Actions)
                    if (index === cols.length - 1) return;
                    let text = col.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
                    rowData.push('"' + text + '"');
                });
                if (rowData.length > 0) {
                    csv.push(rowData.join(','));
                }
            });

            const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
            const downloadLink = document.createElement('a');
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
@endsection

