@extends('layout.master')
@section('title', $title ?? 'Employee Monthly Attendance Details')

@section('main-section')
    <style>
        .emp-details-page .emp-hero-card {
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .emp-details-page .emp-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d6efd 0%, #6366f1 50%, #10b981 100%);
        }

        .emp-details-page .emp-avatar-lg {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            flex-shrink: 0;
        }

        .emp-details-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            padding: 16px 18px;
            height: 100%;
        }

        .emp-details-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .emp-details-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .emp-details-page .kpi-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: var(--card-soft-bg, #eef5ff);
            color: var(--card-accent, #0d6efd);
            flex-shrink: 0;
        }

        .emp-details-page .filter-bar-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .emp-details-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .emp-details-page .time-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .emp-details-page .time-badge-in {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .emp-details-page .time-badge-out {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .emp-details-page .time-badge-progress {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .emp-details-page .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-animation 1.8s infinite;
        }

        @keyframes pulse-animation {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .emp-details-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }
    </style>

    <div class="page-wrapper emp-details-page">
        <div class="content">

            {{-- Top Breadcrumbs & Back Button --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item text-muted">Attendance</li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="text-muted">Org Reports</a></li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $employee['name'] }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('attendance.attendance-dashboard.org-reports') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="ti ti-arrow-left fs-15"></i> Back to Reports
                    </a>
                </div>
            </div>

            {{-- Employee Hero Header Card --}}
            <div class="emp-hero-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="emp-avatar-lg">{{ $employee['avatar_text'] }}</span>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h4 class="mb-0 text-dark fw-bold">{{ $employee['name'] }}</h4>
                                @if ($employee['active'])
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-11">
                                        <i class="ti ti-circle-filled fs-7 me-1 text-success"></i> Active Staff
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fs-11">
                                        <i class="ti ti-user-x fs-7 me-1"></i> Inactive
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2.5 flex-wrap mt-1.5 text-muted fs-13">
                                <span><i class="ti ti-briefcase me-1 text-primary"></i>{{ $employee['designation'] ?: 'Staff Member' }}</span>
                                <span>•</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">
                                    <i class="ti ti-building me-1"></i>Agency: {{ $employee['agency_name'] ?? 'Direct / In-House' }}
                                </span>
                                <span>•</span>
                                <span class="badge bg-info-subtle text-info border border-info-subtle fs-11">
                                    <i class="ti ti-clock me-1"></i>Shift: {{ $employee['shift']['name'] ?? 'General Shift' }} ({{ $employee['shift']['timing'] ?? '09:30 AM - 06:30 PM' }})
                                </span>
                                @if (!empty($employee['email']))
                                    <span>•</span>
                                    <span><i class="ti ti-mail me-1 text-primary"></i>{{ $employee['email'] }}</span>
                                @endif
                                @if (!empty($employee['mobile']))
                                    <span>•</span>
                                    <span><i class="ti ti-phone me-1 text-primary"></i>{{ $employee['mobile'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Month Display Badge & Switcher --}}
                    @php
                        $currentMonthCarbon = \Carbon\Carbon::createFromFormat('Y-m', $period['month'])->startOfMonth();
                        $prevMonth = $currentMonthCarbon->copy()->subMonth()->format('Y-m');
                        $nextMonth = $currentMonthCarbon->copy()->addMonth()->format('Y-m');
                    @endphp
                    <div class="d-flex align-items-center gap-2 bg-light p-1.5 rounded-pill border">
                        <a href="{{ route('attendance.attendance-dashboard.org-reports.employee', ['employeeId' => $employee['id'], 'month' => $prevMonth]) }}" 
                           class="btn btn-sm btn-white bg-white border-0 rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                           style="width: 32px; height: 32px;" title="Previous Month">
                            <i class="ti ti-chevron-left text-dark fs-14"></i>
                        </a>
                        <span class="px-3 py-1 fs-13 fw-bold text-dark d-flex align-items-center gap-1.5">
                            <i class="ti ti-calendar-event text-primary"></i> {{ $period['month_name'] }}
                        </span>
                        <a href="{{ route('attendance.attendance-dashboard.org-reports.employee', ['employeeId' => $employee['id'], 'month' => $nextMonth]) }}" 
                           class="btn btn-sm btn-white bg-white border-0 rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                           style="width: 32px; height: 32px;" title="Next Month">
                            <i class="ti ti-chevron-right text-dark fs-14"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- 6 Monthly Summary Metric Cards --}}
            <div class="row g-3 mb-4">
                {{-- Present Days --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-kpi-card" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 fw-semibold text-uppercase tracking-wider">Present Days</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ $summary['present_days'] }}</h3>
                                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap fs-10">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1 py-0" title="Late Arrivals">
                                        Late: {{ $summary['late_days'] ?? 0 }}
                                    </span>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-1 py-0" title="Early Departures">
                                        Early: {{ $summary['early_out_days'] ?? 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approved Leaves --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-kpi-card" style="--card-accent: #8b5cf6; --card-soft-bg: #f5f3ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 fw-semibold text-uppercase tracking-wider">Leaves Taken</span>
                                <h3 class="mb-0 mt-1 fw-bold" style="color: #7c3aed;">{{ $summary['leave_days'] }}</h3>
                                <div class="text-muted fs-11 mt-0.5">Approved Leaves</div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-calendar-off"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Holidays / Festivals --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-kpi-card" style="--card-accent: #06b6d4; --card-soft-bg: #ecfeff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 fw-semibold text-uppercase tracking-wider">Holidays</span>
                                <h3 class="mb-0 mt-1 fw-bold text-info">{{ $summary['holiday_days'] }}</h3>
                                <div class="text-muted fs-11 mt-0.5">Festivals / Occasions</div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-gift"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sundays / Weekly Offs --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-kpi-card" style="--card-accent: #f59e0b; --card-soft-bg: #fffbeb;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 fw-semibold text-uppercase tracking-wider">Sundays / Offs</span>
                                <h3 class="mb-0 mt-1 fw-bold text-warning">{{ $summary['weekly_off_days'] }}</h3>
                                <div class="text-muted fs-11 mt-0.5">Weekly Offs</div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-cup"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Absent Days --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-kpi-card" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 fw-semibold text-uppercase tracking-wider">Absent Days</span>
                                <h3 class="mb-0 mt-1 fw-bold text-danger">{{ $summary['absent_days'] }}</h3>
                                <div class="text-muted fs-11 mt-0.5">Past Unmarked</div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Work Hours --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-kpi-card" style="--card-accent: #3b82f6; --card-soft-bg: #eff6ff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 fw-semibold text-uppercase tracking-wider">Total Work Hours</span>
                                <h3 class="mb-0 mt-1 fw-bold text-primary fs-18 font-monospace">{{ $summary['total_work_hours'] }}</h3>
                                <div class="text-muted fs-11 mt-0.5">Monthly Duration</div>
                            </div>
                            <div class="kpi-icon-box">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 1 to 31 Days Register Ribbon --}}
            <div class="card border shadow-sm rounded-3 mb-4 overflow-hidden">
                <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-xs rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-stats fs-14"></i>
                        </span>
                        <span class="fw-bold text-dark fs-13">Monthly Register Ribbon ({{ $period['month_name'] }})</span>
                    </div>
                    <div class="d-flex align-items-center gap-2.5 fs-11 text-muted">
                        <span class="d-flex align-items-center"><span class="badge bg-success text-white px-1.5 py-0.5 me-1">P</span> Present</span>
                        <span class="d-flex align-items-center"><span class="badge bg-danger text-white px-1.5 py-0.5 me-1">A</span> Absent</span>
                        <span class="d-flex align-items-center"><span class="badge text-white px-1.5 py-0.5 me-1" style="background:#8b5cf6;">L</span> Leave</span>
                        <span class="d-flex align-items-center"><span class="badge bg-info text-white px-1.5 py-0.5 me-1">H</span> Holiday</span>
                        <span class="d-flex align-items-center"><span class="badge bg-warning text-dark px-1 py-0.5 me-1">WO</span> Off</span>
                    </div>
                </div>
                <div class="card-body p-2 bg-light-subtle">
                    <div class="d-flex flex-nowrap overflow-auto py-1 px-1 gap-1.5 text-center" style="scrollbar-width: thin;">
                        @foreach ($month_matrix_days ?? $days as $mDay)
                            @php
                                $isP = ($mDay['letter_code'] ?? '') === 'P';
                                $isA = ($mDay['letter_code'] ?? '') === 'A';
                                $isL = ($mDay['letter_code'] ?? '') === 'L';
                                $isH = ($mDay['letter_code'] ?? '') === 'H';
                                $isWO = ($mDay['letter_code'] ?? '') === 'WO';

                                $pillBg = '#f1f5f9';
                                $pillColor = '#64748b';
                                $pillBorder = '#e2e8f0';
                                if ($isP) {
                                    $pillBg = '#dcfce7'; $pillColor = '#15803d'; $pillBorder = '#86efac';
                                } elseif ($isA) {
                                    $pillBg = '#fee2e2'; $pillColor = '#b91c1c'; $pillBorder = '#fca5a5';
                                } elseif ($isL) {
                                    $pillBg = '#f3e8ff'; $pillColor = '#7e22ce'; $pillBorder = '#d8b4fe';
                                } elseif ($isH) {
                                    $pillBg = '#cffafe'; $pillColor = '#0e7490'; $pillBorder = '#a5f3fc';
                                } elseif ($isWO) {
                                    $pillBg = '#fef3c7'; $pillColor = '#92400e'; $pillBorder = '#fde68a';
                                }
                            @endphp
                            <div class="d-inline-flex flex-column align-items-center p-1.5 rounded-2 border flex-shrink-0" 
                                 style="width: 44px; background: {{ $mDay['is_today'] ? '#eff6ff' : '#ffffff' }}; border-color: {{ $mDay['is_today'] ? '#3b82f6' : '#e2e8f0' }} !important; {{ $mDay['is_today'] ? 'box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);' : '' }}"
                                 title="{{ $mDay['date_formatted'] }} ({{ $mDay['day_name'] }}): {{ $mDay['badge_text'] }}">
                                <span class="fs-10 text-muted fw-semibold">{{ substr($mDay['day_name'], 0, 2) }}</span>
                                <span class="fs-12 fw-bold text-dark">{{ sprintf('%02d', $mDay['day']) }}</span>
                                <span class="mt-1 badge fw-bold" style="background-color: {{ $pillBg }}; color: {{ $pillColor }}; border: 1px solid {{ $pillBorder }}; font-size: 11px; padding: 2px 4px; width: 100%;">
                                    {{ $mDay['letter_code'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Date & Status Filter Bar --}}
            <div class="filter-bar-card">
                <form method="GET" class="row g-2.5 align-items-end">
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <label class="form-label fs-12 fw-bold text-muted mb-1">Select Month</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar-month text-muted"></i></span>
                            <input type="month" name="month" class="form-control border-start-0" value="{{ $period['month'] }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <label class="form-label fs-12 fw-bold text-muted mb-1">From Date</label>
                        <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $period['from_date'] }}">
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <label class="form-label fs-12 fw-bold text-muted mb-1">To Date</label>
                        <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $period['to_date'] }}">
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <label class="form-label fs-12 fw-bold text-muted mb-1">Filter by Day Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all" {{ $period['status'] === 'all' ? 'selected' : '' }}>All Days</option>
                            <option value="present" {{ $period['status'] === 'present' ? 'selected' : '' }}>Present Only</option>
                            <option value="leave" {{ $period['status'] === 'leave' ? 'selected' : '' }}>On Leave Only</option>
                            <option value="holiday" {{ $period['status'] === 'holiday' ? 'selected' : '' }}>Festival & Holidays Only</option>
                            <option value="weekly_off" {{ $period['status'] === 'weekly_off' ? 'selected' : '' }}>Sundays / Offs Only</option>
                            <option value="absent" {{ $period['status'] === 'absent' ? 'selected' : '' }}>Absent Only</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6 col-sm-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill d-flex align-items-center justify-content-center gap-1 shadow-sm">
                            <i class="ti ti-search fs-14"></i> Filter
                        </button>
                        <a href="{{ route('attendance.attendance-dashboard.org-reports.employee', $employee['id']) }}" class="btn btn-light border btn-sm d-flex align-items-center justify-content-center gap-1" title="Reset Filters">
                            <i class="ti ti-rotate-clockwise fs-14"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Day-by-Day Complete Month Sheet Table --}}
            <div class="table-container-card">
                <div class="card-header bg-white border-bottom py-3 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar fs-16"></i>
                        </span>
                        <div>
                            <h5 class="card-title mb-0 fs-15 fw-bold text-dark">Daily Attendance & Leave Log</h5>
                            <small class="text-muted">Detailed log showing daily check-in, check-out, approved leaves, festivals and offs</small>
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-11 rounded-pill">
                        {{ count($days) }} Days Displayed
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th width="16%">Date & Day</th>
                                <th width="24%">Status / Occasion</th>
                                <th width="15%">Check In</th>
                                <th width="15%">Check Out</th>
                                <th width="14%">Working Hours</th>
                                <th width="16%">Remarks / Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($days as $day)
                                @php
                                    $rowStyle = '';
                                    if ($day['is_today']) {
                                        $rowStyle = 'background-color: #f0f7ff; border-left: 3px solid #0d6efd;';
                                    } elseif ($day['type'] === 'sunday') {
                                        $rowStyle = 'background-color: #fffdf5;';
                                    } elseif ($day['type'] === 'holiday') {
                                        $rowStyle = 'background-color: #f0fdfa;';
                                    } elseif ($day['type'] === 'leave') {
                                        $rowStyle = 'background-color: #faf5ff;';
                                    }
                                @endphp
                                <tr style="{{ $rowStyle }}">
                                    <td>
                                        <div class="fw-bold text-dark fs-13">
                                            {{ $day['date_formatted'] }}
                                            @if ($day['is_today'])
                                                <span class="badge bg-primary rounded-pill fs-10 ms-1">Today</span>
                                            @endif
                                        </div>
                                        <small class="text-muted fs-11">{{ $day['day_name'] }}</small>
                                    </td>
                                    <td>
                                        @if ($day['type'] === 'present')
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-success text-white fw-bold px-2 py-1 fs-12 shadow-xs" style="min-width: 26px; text-align: center;">P</span>
                                                <span class="badge {{ $day['badge_class'] }} border px-2 py-0.5 fs-11">
                                                    {{ $day['badge_text'] }}
                                                </span>
                                            </div>
                                        @elseif ($day['type'] === 'absent')
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-danger text-white fw-bold px-2 py-1 fs-12 shadow-xs" style="min-width: 26px; text-align: center;">A</span>
                                                <span class="badge {{ $day['badge_class'] }} border px-2 py-0.5 fs-11">
                                                    Absent
                                                </span>
                                            </div>
                                        @elseif ($day['type'] === 'leave')
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge text-white fw-bold px-2 py-1 fs-12 shadow-xs" style="background-color: #8b5cf6 !important; min-width: 26px; text-align: center;">L</span>
                                                <span class="badge {{ $day['badge_class'] }} border px-2 py-0.5 fs-11">
                                                    {{ $day['badge_text'] }}
                                                </span>
                                            </div>
                                        @elseif ($day['type'] === 'holiday')
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-info text-white fw-bold px-2 py-1 fs-12 shadow-xs" style="min-width: 26px; text-align: center;">H</span>
                                                <span class="badge {{ $day['badge_class'] }} border px-2 py-0.5 fs-11">
                                                    {{ $day['badge_text'] }}
                                                </span>
                                            </div>
                                        @elseif ($day['type'] === 'weekly_off')
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-warning text-dark fw-bold px-1.5 py-1 fs-12 shadow-xs" style="min-width: 26px; text-align: center;">WO</span>
                                                <span class="badge {{ $day['badge_class'] }} border px-2 py-0.5 fs-11">
                                                    {{ $day['badge_text'] }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-muted border fw-bold px-2 py-1 fs-12" style="min-width: 26px; text-align: center;">-</span>
                                                <span class="badge {{ $day['badge_class'] }} border px-2 py-0.5 fs-11">
                                                    {{ $day['badge_text'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($day['check_in'] && $day['check_in'] !== '-')
                                            <div>
                                                <span class="time-badge time-badge-in" @if ($day['check_in_ip']) title="IP: {{ $day['check_in_ip'] }}" @endif>
                                                    <i class="ti ti-login fs-12"></i> {{ $day['check_in'] }}
                                                </span>
                                                @if (!empty($day['is_late']))
                                                    <div class="mt-1">{!! $day['late_badge'] !!}</div>
                                                @elseif ($day['type'] === 'present')
                                                    <div class="mt-1"><span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded-pill fs-10"><i class="ti ti-circle-check me-0.5"></i>On Time</span></div>
                                                @endif
                                                @if ($day['check_in_ip'])
                                                    <div class="text-muted fs-10 mt-0.5"><i class="ti ti-world me-0.5"></i>{{ $day['check_in_ip'] }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($day['check_out'] && $day['check_out'] !== '-' && $day['check_out'] !== 'In Progress')
                                            <div>
                                                <span class="time-badge time-badge-out" @if ($day['check_out_ip']) title="IP: {{ $day['check_out_ip'] }}" @endif>
                                                    <i class="ti ti-logout fs-12"></i> {{ $day['check_out'] }}
                                                </span>
                                                @if (!empty($day['is_early_out']))
                                                    <div class="mt-1">{!! $day['early_badge'] !!}</div>
                                                @elseif ($day['type'] === 'present')
                                                    <div class="mt-1"><span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded-pill fs-10"><i class="ti ti-circle-check me-0.5"></i>Shift Done</span></div>
                                                @endif
                                                @if ($day['check_out_ip'])
                                                    <div class="text-muted fs-10 mt-0.5"><i class="ti ti-world me-0.5"></i>{{ $day['check_out_ip'] }}</div>
                                                @endif
                                            </div>
                                        @elseif ($day['check_out'] === 'In Progress')
                                            <span class="time-badge time-badge-progress">
                                                <span class="pulse-dot me-1"></span> In Office
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($day['working_hours'] && $day['working_hours'] !== '-' && $day['working_hours'] !== 'In Progress')
                                            @php
                                                $isGoodHours = ($day['total_minutes'] ?? 0) >= 480;
                                                $badgeClass = $isGoodHours ? 'bg-success-subtle text-success border-success-subtle' : 'bg-light text-dark border';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} border px-2 py-1 fs-12 font-monospace fw-bold">
                                                <i class="ti ti-clock me-1"></i>{{ $day['working_hours'] }}
                                            </span>
                                        @elseif ($day['working_hours'] === 'In Progress')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-10">
                                                <i class="ti ti-clock-play me-1"></i>In Progress
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($day['note'] && $day['note'] !== '-')
                                            <span class="fs-12 text-dark" title="{{ $day['note'] }}">
                                                <i class="ti ti-notes me-1 text-muted"></i>{{ $day['note'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                            <i class="ti ti-calendar-off fs-24"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Attendance Records Found</h6>
                                        <p class="text-muted fs-13 mb-0">No entries match your selected month or filter status.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <x-footer />
    </div>
@endsection
