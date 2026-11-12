@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

    <style>
        .holiday-event {
            border-radius: 6px !important;
            padding: 3px 6px !important;
            font-size: 12px !important;
            border: none !important;
            font-weight: 500;
        }
        .common-holiday {
            background: #fee2e2 !important;
            color: #b91c1c !important;
            border-left: 3px solid #dc2626 !important;
            font-weight: 600 !important;
        }
        .restricted-holiday {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border-left: 3px solid #3b82f6 !important;
        }

        .fc-event-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-daygrid-event {
            margin: 2px 0 !important;
        }

        .fc-daygrid-event-harness a {
            text-decoration: none !important;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">
            <x-alert-modal />
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Annual Holiday Calendar 2026</h2>
                    <p class="text-muted fs-13 mb-0">Official list of 5 Common Holidays and 22 Restricted Holidays</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 fs-12">
                        <i class="ti ti-star-filled me-1"></i> Common Holidays (Mandatory: 5)
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fs-12">
                        <i class="ti ti-calendar-event me-1"></i> Restricted Holidays (Optional: Opt any 5)
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-12 col-xl-12 theiaStickySidebar">
                    <div class="card border-0">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
            <p class="mb-0">2014 - 2025 &copy; EMS.</p>
            <p>Designed &amp; Developed By <a href="#" class="text-primary">Dreams</a></p>
        </div>
    </div>

@endsection

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                height: "auto",
                displayEventTime: false,
                events: @json($events),
            });
            calendar.render();
        });
    </script>
@endpush
