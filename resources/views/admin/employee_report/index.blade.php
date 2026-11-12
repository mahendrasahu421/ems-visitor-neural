@extends('layout.master')
@section('title', $title ?? 'Employees Daily Reports')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .employee-report-page .filter-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
        }

        .employee-report-page .report-table-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .employee-report-page .kpi-stat-box {
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            background: #f8fafc;
            padding: 14px 18px;
            transition: all 0.2s ease;
        }

        .employee-report-page .kpi-stat-box:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .employee-report-page .work-text {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            white-space: normal;
            word-break: break-word;
            font-size: 13px;
            line-height: 1.45;
            color: #334155;
        }

        .employee-report-page .read-more-btn {
            color: #0d6efd;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            margin-top: 3px;
        }

        .employee-report-page .read-more-btn:hover {
            text-decoration: underline;
        }

        /* Day Group Headers */
        .employee-report-page #dailyReportList tbody tr.day-group-header td {
            padding: 10px 16px !important;
            font-size: 13px !important;
            border-top: 1.5px solid #e2e8f0 !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
        }

        .employee-report-page tr.day-group-normal td {
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        .employee-report-page tr.day-group-saturday td {
            background: #f0fdf4 !important;
            color: #166534 !important;
        }

        .employee-report-page tr.day-group-sunday td {
            background: #fef2f2 !important;
            color: #991b1b !important;
        }

        .employee-report-page .table-hover tbody tr:hover:not(.day-group-header) {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        /* DataTables Month Pagination */
        .employee-report-page .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 0 !important;
        }

        .employee-report-page .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 34px !important;
            height: 34px !important;
            padding: 0 12px !important;
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

        .employee-report-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .employee-report-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .employee-report-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
        }

        .employee-report-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .employee-report-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
        }
    </style>

    <div class="page-wrapper employee-report-page">
        <div class="content">

            {{-- Page Header --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-file-analytics fs-20"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Daily Work Reports' }}</h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Employees</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Daily Reports</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter Toolbar --}}
            <div class="card filter-card mb-4">
                <div class="card-body p-3.5">
                    <form id="reportFilterForm" class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                <i class="ti ti-user me-1 text-primary"></i>Select Employee <span class="text-danger">*</span>
                            </label>
                            <select class="form-select select2" id="employeeFilter" required>
                                <option value="">-- Choose Employee --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->employee_id }}">
                                        {{ $employee->employee_name }} ({{ $employee->employee_email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                <i class="ti ti-calendar me-1 text-info"></i>From Date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="ti ti-calendar-event"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="startDate"
                                    value="{{ \Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">
                                <i class="ti ti-calendar-due me-1 text-info"></i>To Date
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="ti ti-calendar-event"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="endDate"
                                    value="{{ \Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-12 d-flex gap-2">
                            <button type="button" class="btn btn-primary w-100 fw-semibold d-inline-flex align-items-center justify-content-center" id="searchBtn">
                                <i class="ti ti-search me-1.5"></i>Generate
                            </button>
                            <button type="button" class="btn btn-success fw-semibold d-inline-flex align-items-center justify-content-center" id="exportBtn" title="Export">
                                <i class="ti ti-file-export"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary KPI Boxes (Shown dynamically after filter) --}}
            <div class="row g-3 mb-4 d-none" id="summaryKpiRow">
                <div class="col-md-4">
                    <div class="kpi-stat-box d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase">Total Logged Hours</span>
                            <h3 class="mb-0 fw-bold text-primary mt-1" id="kpiTotalHours">00:00</h3>
                        </div>
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-clock-check fs-20"></i>
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-stat-box d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase">Working Days Logged</span>
                            <h3 class="mb-0 fw-bold text-success mt-1" id="kpiDaysLogged">0</h3>
                        </div>
                        <span class="avatar avatar-md rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-check fs-20"></i>
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-stat-box d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-semibold text-uppercase">Days Not Submitted</span>
                            <h3 class="mb-0 fw-bold text-danger mt-1" id="kpiDaysMissing">0</h3>
                        </div>
                        <span class="avatar avatar-md rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-x fs-20"></i>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Report Table Card --}}
            <div class="card report-table-card">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark" id="tableHeaderTitle">Daily Work Log Sheets</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="dailyReportList" class="table table-hover align-middle mb-0 nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 220px;">Project Name</th>
                                    <th>Work Description</th>
                                    <th style="width: 130px;">Start Time</th>
                                    <th style="width: 130px;">End Time</th>
                                    <th style="width: 130px;" class="text-end pe-4">Duration</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end fw-bold fs-14 pe-3">Month Total Hours:</th>
                                    <th id="totalHours" class="text-end fw-bold text-primary fs-15 pe-4">00:00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <x-footer />
    </div>

    <!-- Daily Report Details Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="ti ti-file-text text-primary me-2 fs-20"></i>Daily Report Details
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted fs-11 text-uppercase fw-semibold mb-1"><i class="ti ti-calendar me-1 text-primary"></i>Report Date</div>
                                <div class="fw-bold text-dark fs-14" id="modalReportDate">-</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted fs-11 text-uppercase fw-semibold mb-1"><i class="ti ti-clock-edit me-1 text-info"></i>Submission Date</div>
                                <div class="fw-bold text-dark fs-14" id="modalFilledDate">-</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="text-muted fs-11 text-uppercase fw-semibold mb-1"><i class="ti ti-folder me-1 text-warning"></i>Project</div>
                                <div class="fw-bold text-dark fs-14" id="modalProject">-</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2.5 bg-light rounded-3 border text-center">
                                <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Start Time</div>
                                <div class="fw-bold text-dark fs-13" id="modalStartTime">-</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2.5 bg-light rounded-3 border text-center">
                                <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">End Time</div>
                                <div class="fw-bold text-dark fs-13" id="modalEndTime">-</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2.5 bg-primary-subtle text-primary rounded-3 border border-primary-subtle text-center">
                                <div class="fs-11 text-uppercase fw-semibold mb-1">Total Duration</div>
                                <div class="fw-bold fs-14" id="modalHours">-</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark fs-14 mb-2"><i class="ti ti-align-left text-primary me-1"></i>Work Description & Activities</h6>
                    <div class="p-3 bg-light rounded-3 border text-dark fs-13 lh-base" id="modalDescription" style="white-space: pre-wrap; max-height: 280px; overflow-y: auto;">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.dataTables.min.css">
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>

    <script>
        $(document).ready(function() {
            flatpickr("#startDate", {
                dateFormat: "d-m-Y"
            });

            flatpickr("#endDate", {
                dateFormat: "d-m-Y"
            });

            let isSearchClicked = false;

            function isFilterEmpty() {
                return !$('#employeeFilter').val();
            }

            let table = $('#dailyReportList').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                paging: true,
                pageLength: 1,
                lengthMenu: [[1], [1]],
                lengthChange: false,
                deferLoading: 0,
                dom: "<'row'<'col-12'tr>>" +
                     "<'row align-items-center justify-content-between p-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center'i><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
                language: {
                    info: "Viewing Month _START_ of _TOTAL_ in selected range",
                    infoEmpty: "",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i> First Month',
                        previous: '<i class="ti ti-chevron-left"></i> Previous Month',
                        next: 'Next Month <i class="ti ti-chevron-right"></i>',
                        last: 'Last Month <i class="ti ti-chevrons-right"></i>'
                    },
                    emptyTable: "No daily reports found for this period."
                },
                ajax: {
                    url: "{{ route('employee-report.list') }}",
                    data: function(d) {
                        d.employee_id = $('#employeeFilter').val();
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                        d.page = table.page.info().page;
                    },
                    dataSrc: function(json) {
                        if (!isSearchClicked) {
                            $('#totalHours').text('00:00');
                            $('#summaryKpiRow').addClass('d-none');
                            return [];
                        }

                        let totalMinutes = 0;
                        let daysLogged = 0;
                        let daysMissing = 0;

                        json.data.forEach(function(row) {
                            if (row.TotalHours) {
                                let parts = row.TotalHours.split(':');
                                if (parts.length === 2) {
                                    totalMinutes += (parseInt(parts[0]) * 60) + parseInt(parts[1]);
                                }
                                daysLogged++;
                            } else if (row.WorkDescription === 'Not Filled') {
                                daysMissing++;
                            }
                        });

                        let h = Math.floor(totalMinutes / 60);
                        let m = totalMinutes % 60;
                        let formattedTotal = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');

                        $('#totalHours').text(formattedTotal + ' hrs');
                        $('#kpiTotalHours').text(formattedTotal + ' hrs');
                        $('#kpiDaysLogged').text(daysLogged);
                        $('#kpiDaysMissing').text(daysMissing);
                        $('#summaryKpiRow').removeClass('d-none');

                        return json.data;
                    }
                },
                rowGroup: {
                    dataSrc: 'group_date',
                    startRender: function(rows, group) {
                        let minutes = 0;

                        rows.every(function() {
                            let total = this.data().TotalHours;
                            if (total) {
                                let t = total.split(':');
                                if (t.length === 2) {
                                    minutes += (parseInt(t[0]) * 60) + parseInt(t[1]);
                                }
                            }
                        });

                        let hh = Math.floor(minutes / 60);
                        let mm = minutes % 60;
                        let totalHours = String(hh).padStart(2, '0') + ":" + String(mm).padStart(2, '0');

                        let dayType = rows.data()[0].day_type;
                        let filledDate = rows.data()[0].CreationDate || '-';

                        if (dayType === "saturday") {
                            return $('<tr class="day-group-header day-group-saturday fw-semibold"/>')
                                .append(
                                    '<td colspan="5">' +
                                    '<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">' +
                                    '<span><i class="ti ti-calendar-event me-1 text-success"></i>' +
                                    '<strong>' + group + ' (Saturday)</strong>' +
                                    '<span class="badge bg-white text-success border ms-2 fs-11">Submitted: ' + filledDate + '</span>' +
                                    '</span>' +
                                    '<span class="badge bg-success text-white px-2.5 py-1 fs-12">Day Total: ' + totalHours + ' hrs</span>' +
                                    '</div>' +
                                    '</td>'
                                );
                        }

                        if (dayType === "sunday") {
                            return $('<tr class="day-group-header day-group-sunday fw-semibold"/>')
                                .append(
                                    '<td colspan="5">' +
                                    '<div class="d-flex align-items-center justify-content-between">' +
                                    '<span><i class="ti ti-sun me-1 text-danger"></i>' +
                                    '<strong>' + group + ' (Sunday)</strong>' +
                                    '</span>' +
                                    '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11">Weekly Off</span>' +
                                    '</div>' +
                                    '</td>'
                                );
                        }

                        return $('<tr class="day-group-header day-group-normal fw-semibold"/>')
                            .append(
                                '<td colspan="5">' +
                                '<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">' +
                                '<span><i class="ti ti-calendar me-1 text-primary"></i>' +
                                '<strong>' + group + '</strong>' +
                                '<span class="badge bg-white text-secondary border ms-2 fs-11">Submitted: ' + filledDate + '</span>' +
                                '</span>' +
                                '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-12">Day Total: ' + totalHours + ' hrs</span>' +
                                '</div>' +
                                '</td>'
                            );
                    }
                },
                rowCallback: function(row, data) {
                    if (data.day_type === "sunday") {
                        $(row).hide();
                        return;
                    }
                },
                columns: [
                    {
                        data: 'project_name',
                        render: function(data, type, row) {
                            if (row.WorkDescription === 'Not Filled') {
                                return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="ti ti-alert-triangle me-1"></i>Not Submitted</span>';
                            }
                            return `
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="avatar avatar-xs bg-light text-primary rounded d-inline-flex align-items-center justify-content-center">
                                        <i class="ti ti-folder fs-12"></i>
                                    </span>
                                    <span class="fw-semibold text-dark fs-13">${data || 'General Work'}</span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'WorkDescription',
                        render: function(data, type, row) {
                            if (!data || data === 'Not Filled') {
                                return '<span class="text-muted fs-12 fst-italic">No daily log submitted for this day.</span>';
                            }

                            return `
                                <div>
                                    <div class="work-text">${data}</div>
                                    <span class="read-more-btn"
                                        data-description="${encodeURIComponent(data)}"
                                        data-project="${row.project_name || 'General'}"
                                        data-report="${row.DrEntryDate}"
                                        data-filled="${row.CreationDate || '-'}"
                                        data-start="${row.StartingTime || '-'}"
                                        data-end="${row.EndingTime || '-'}"
                                        data-hours="${row.TotalHours || '-'}">
                                        <i class="ti ti-eye me-1"></i>View Full Note
                                    </span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'StartingTime',
                        render: data => data ? `<span class="fs-12 text-muted"><i class="ti ti-clock-play me-1 text-info"></i>${data}</span>` : '-'
                    },
                    {
                        data: 'EndingTime',
                        render: data => data ? `<span class="fs-12 text-muted"><i class="ti ti-clock-stop me-1 text-danger"></i>${data}</span>` : '-'
                    },
                    {
                        data: 'TotalHours',
                        className: 'text-end pe-4',
                        render: data => data ? `<span class="badge bg-light text-dark border fs-12 font-monospace">${data} hrs</span>` : '-'
                    }
                ],
                drawCallback: function() {
                    if (!isSearchClicked) {
                        $('#dailyReportList tbody').html(`
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="ti ti-filter-off fs-40 text-muted d-block mb-2"></i>
                                    <h6>Please select an employee and click <strong>Generate</strong></h6>
                                    <p class="fs-12 text-muted mb-0">Daily activity logs will populate here grouped by date.</p>
                                </td>
                            </tr>
                        `);
                        return;
                    }
                }
            });

            $('#searchBtn').click(function() {
                if (isFilterEmpty()) {
                    alert('Please select an employee to generate daily reports.');
                    return;
                }

                isSearchClicked = true;
                const empName = $('#employeeFilter option:selected').text();
                $('#tableHeaderTitle').text('Daily Work Log: ' + empName);
                table.ajax.reload();
            });

            $('#exportBtn').click(function() {
                if (isFilterEmpty()) {
                    alert('Please select an employee to export daily reports.');
                    return;
                }

                const params = $.param({
                    employee_id: $('#employeeFilter').val(),
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val()
                });

                window.location.href = "{{ route('employee-report.export') }}" + "?" + params;
            });

            $(document).on('click', '.read-more-btn', function() {
                $('#modalReportDate').text($(this).data('report'));
                $('#modalFilledDate').text($(this).data('filled'));
                $('#modalProject').text($(this).data('project'));
                $('#modalStartTime').text($(this).data('start'));
                $('#modalEndTime').text($(this).data('end'));
                $('#modalHours').text($(this).data('hours') + ' hrs');
                $('#modalDescription').text(decodeURIComponent($(this).data('description')));

                const modal = new bootstrap.Modal(document.getElementById('reportModal'));
                modal.show();
            });
        });
    </script>
@endpush
