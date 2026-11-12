@extends('layout.master')
@section('title', $title ?? 'Holiday Calendar 2026')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .holiday-calendar-page .stat-kpi-card {
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

        .holiday-calendar-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .holiday-calendar-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .holiday-calendar-page .kpi-icon-box {
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

        .holiday-calendar-page .calendar-card {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }

        .holiday-calendar-page .sidebar-holiday-card {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }

        /* FullCalendar Custom Theming */
        .holiday-calendar-page .fc {
            font-family: inherit;
        }

        .holiday-calendar-page .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.25rem;
            padding: 0.5rem 0.25rem;
        }

        .holiday-calendar-page .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
        }

        .holiday-calendar-page .fc .fc-button-primary {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            padding: 6px 14px !important;
            border-radius: 8px !important;
            box-shadow: none !important;
            text-transform: capitalize !important;
            transition: all 0.15s ease !important;
        }

        .holiday-calendar-page .fc .fc-button-primary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        .holiday-calendar-page .fc .fc-button-primary.fc-button-active {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
        }

        .holiday-calendar-page .fc-theme-standard td,
        .holiday-calendar-page .fc-theme-standard th {
            border-color: #f1f5f9;
        }

        .holiday-calendar-page .fc-col-header-cell {
            background: #f8fafc;
            padding: 8px 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .holiday-calendar-page .fc-daygrid-day-number {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            padding: 6px 8px;
        }

        .holiday-calendar-page .fc-day-today {
            background-color: #eff6ff !important;
        }

        .holiday-calendar-page .fc-day-today .fc-daygrid-day-number {
            background-color: #2563eb;
            color: #ffffff;
            border-radius: 6px;
            padding: 2px 7px;
            margin: 4px;
        }

        /* Holiday Event Styling */
        .holiday-event {
            border-radius: 6px !important;
            padding: 3px 6px !important;
            font-size: 11.5px !important;
            border: none !important;
            cursor: pointer !important;
            transition: transform 0.12s ease !important;
            margin: 2px 0 !important;
            display: block !important;
        }

        .holiday-event:hover {
            transform: translateY(-1px) scale(1.02);
            filter: brightness(0.97);
        }

        .common-holiday {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            border-left: 3.5px solid #dc2626 !important;
            font-weight: 600 !important;
        }

        .restricted-holiday {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            border-left: 3.5px solid #3b82f6 !important;
            font-weight: 500 !important;
        }

        .fc-event-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Sidebar Holiday Items */
        .sidebar-holiday-item {
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 10px 12px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .sidebar-holiday-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateX(2px);
        }
    </style>

    <div class="page-wrapper holiday-calendar-page">
        <div class="content">

            {{-- 1. Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Annual Holiday Calendar 2026</h3>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 fs-11">
                                    <i class="ti ti-point-filled me-0.5"></i>{{ number_format($totalActiveHolidays ?? 0) }} Active
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Settings</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('settings.holiday') }}" class="text-muted">Holiday Master</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Calendar View</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Action & View Switcher Buttons --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="btn-group" role="group">
                        <a href="{{ route('settings.holiday') }}" class="btn btn-outline-secondary btn-md d-inline-flex align-items-center gap-1.5 fw-medium">
                            <i class="ti ti-list fs-16"></i> List View
                        </a>
                        <a href="{{ route('settings.holiday.calendar') }}" class="btn btn-primary btn-md active d-inline-flex align-items-center gap-1.5 fw-medium">
                            <i class="ti ti-calendar fs-16"></i> Calendar View
                        </a>
                    </div>
                    <a href="{{ route('settings.holiday.create') }}" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-circle-plus fs-16"></i> Add Holiday
                    </a>
                </div>
            </div>

            {{-- 2. KPI Statistics Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Holidays --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Active Holidays</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($totalActiveHolidays ?? 0) }}</h3>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 mt-1">Full 2026 Schedule</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-calendar-stats"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Common Holidays --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #dc2626; --card-soft-bg: #fef2f2;">
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
                    </div>
                </div>

                {{-- Restricted Holidays --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #6366f1; --card-soft-bg: #eef2ff;">
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
                    </div>
                </div>

                {{-- Next Upcoming Holiday --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Next Upcoming</span>
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
                                    <h6 class="mb-0 mt-1 fw-bold text-muted">Completed</h6>
                                    <span class="badge bg-light text-muted border fs-11 mt-1">Current Year</span>
                                @endif
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-hourglass-empty"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Filter Buttons & Legend Strip --}}
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    {{-- Filter Category Buttons --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fs-13 fw-bold text-dark me-1"><i class="ti ti-filter me-1 text-primary"></i> Filter Events:</span>
                        <div class="btn-group" role="group" id="calendarFilterButtons">
                            <button type="button" class="btn btn-sm btn-primary active" onclick="filterCalendar('all', this)">
                                All Holidays ({{ $totalActiveHolidays }})
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterCalendar('common', this)">
                                <i class="ti ti-star-filled me-1"></i> Common Only ({{ $commonHolidaysCount }})
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterCalendar('restricted', this)">
                                <i class="ti ti-calendar me-1"></i> Restricted Only ({{ $restrictedHolidaysCount }})
                            </button>
                        </div>
                    </div>

                    {{-- Visual Legend --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fs-12 text-muted fw-medium">Color Legend:</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-12 fw-semibold">
                            <i class="ti ti-star-filled me-1"></i> Common (Mandatory: 5)
                        </span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-12 fw-medium">
                            <i class="ti ti-calendar-event me-1"></i> Restricted (Opt Any 5)
                        </span>
                    </div>
                </div>
            </div>

            {{-- 4. Calendar & Sidebar Grid --}}
            <div class="row g-4">
                {{-- Main Calendar Container --}}
                <div class="col-xl-9 col-lg-8">
                    <div class="card calendar-card p-4">
                        <div id="fullHolidayCalendar"></div>
                    </div>
                </div>

                {{-- Sidebar: Quick Holiday List by Month --}}
                <div class="col-xl-3 col-lg-4">
                    <div class="card sidebar-holiday-card">
                        <div class="card-header bg-transparent py-3 px-3.5 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-1.5">
                                <i class="ti ti-calendar-time text-primary"></i> 2026 Festivals
                            </h6>
                            <span class="badge bg-primary-subtle text-primary fs-11">{{ count($holidays) }} Total</span>
                        </div>

                        <div class="card-body p-3" style="max-height: 650px; overflow-y: auto;">
                            <div class="d-flex flex-column gap-2" id="sidebarHolidayList">
                                @foreach($holidays as $h)
                                    @php
                                        $dt = \Carbon\Carbon::parse($h->holiday_date);
                                        $isCommon = str_contains(strtolower($h->holiday_remark ?? ''), 'common');
                                    @endphp
                                    <div class="sidebar-holiday-item {{ $isCommon ? 'sidebar-common' : 'sidebar-restricted' }}" onclick="jumpToDate('{{ $h->holiday_date }}')">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge {{ $isCommon ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-primary-subtle text-primary border-primary-subtle' }} border fs-10 px-2 py-0.5">
                                                {{ $isCommon ? '★ Common' : 'Restricted' }}
                                            </span>
                                            <span class="fs-11 text-muted font-mono">{{ $dt->format('d M') }}</span>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-dark fs-13 text-truncate" title="{{ $h->holiday_name }}">{{ $h->holiday_name }}</h6>
                                        <span class="fs-11 text-muted">{{ $dt->format('l, Y') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <x-footer />
    </div>

    {{-- Event Detail Modal --}}
    <div class="modal fade" id="holiday_event_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalEventTitle">
                        <i class="ti ti-calendar-event text-primary"></i> Festival Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 text-center" id="modalHeaderBox">
                        <span class="fs-12 text-muted text-uppercase fw-semibold d-block mb-1">Date & Day</span>
                        <h4 class="mb-0 text-dark fw-bold" id="modalEventDate">—</h4>
                        <span class="badge px-2.5 py-0.5 fs-12 mt-1.5" id="modalEventDayBadge">—</span>
                    </div>

                    <ul class="list-group list-group-flush fs-13 mb-0">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Festival / Name:</span>
                            <span class="fw-semibold text-dark fs-14" id="modalEventName">—</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Category:</span>
                            <span id="modalEventCategory">—</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status:</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-11">
                                <i class="ti ti-point-filled me-0.5"></i>Active (Mandatory)
                            </span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Description / Remarks:</span>
                            <p class="mb-0 text-dark bg-white border rounded p-2.5 fs-12" id="modalEventRemarks">—</p>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 d-flex justify-content-between">
                    <a href="{{ route('settings.holiday') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-list me-1"></i> View in List
                    </a>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after_scripts')
    {{-- FullCalendar 6 Script --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        var calendarInstance = null;
        var rawEvents = @json($events);

        document.addEventListener("DOMContentLoaded", function() {
            var calendarEl = document.getElementById('fullHolidayCalendar');

            calendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                initialDate: "2026-01-01",
                height: "auto",
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                buttonText: {
                    today: 'Today',
                    dayGridMonth: 'Month',
                    listMonth: 'List'
                },
                events: rawEvents,
                eventClick: function(info) {
                    showEventModal(info.event);
                }
            });

            calendarInstance.render();
        });

        function showEventModal(event) {
            let props = event.extendedProps || {};
            let dt = new Date(event.start);
            let formattedDate = dt.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
            let formattedDay = dt.toLocaleDateString('en-US', { weekday: 'long' });
            let isCommon = props.is_common || false;

            document.getElementById('modalEventTitle').innerHTML = (isCommon ? '<i class="ti ti-star-filled text-danger"></i> ' : '<i class="ti ti-calendar text-primary"></i> ') + (props.name || event.title);
            document.getElementById('modalEventName').textContent = props.name || event.title;
            document.getElementById('modalEventDate').textContent = formattedDate;
            document.getElementById('modalEventDayBadge').textContent = formattedDay;
            document.getElementById('modalEventRemarks').textContent = props.remark || 'No extra remarks.';

            if (isCommon) {
                document.getElementById('modalEventDayBadge').className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-0.5 fs-12 mt-1.5';
                document.getElementById('modalEventCategory').innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-12 fw-semibold"><i class="ti ti-star-filled me-1"></i>Common Holiday (Mandatory)</span>';
            } else {
                document.getElementById('modalEventDayBadge').className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-0.5 fs-12 mt-1.5';
                document.getElementById('modalEventCategory').innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-12 fw-medium"><i class="ti ti-calendar-event me-1"></i>Restricted Holiday (Optional)</span>';
            }

            let modal = new bootstrap.Modal(document.getElementById('holiday_event_modal'));
            modal.show();
        }

        function filterCalendar(type, btn) {
            let btns = document.querySelectorAll('#calendarFilterButtons button');
            btns.forEach(b => {
                b.classList.remove('btn-primary', 'btn-danger');
                b.classList.add('btn-outline-secondary');
            });

            if (type === 'common') {
                btn.className = 'btn btn-sm btn-danger active';
            } else {
                btn.className = 'btn btn-sm btn-primary active';
            }

            calendarInstance.removeAllEvents();

            if (type === 'all') {
                calendarInstance.addEventSource(rawEvents);
            } else if (type === 'common') {
                let filtered = rawEvents.filter(e => (e.extendedProps && e.extendedProps.is_common));
                calendarInstance.addEventSource(filtered);
            } else if (type === 'restricted') {
                let filtered = rawEvents.filter(e => (e.extendedProps && !e.extendedProps.is_common));
                calendarInstance.addEventSource(filtered);
            }
        }

        function jumpToDate(dateStr) {
            if (calendarInstance) {
                calendarInstance.gotoDate(dateStr);
            }
        }
    </script>
@endpush
