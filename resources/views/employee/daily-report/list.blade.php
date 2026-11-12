@extends('employee.layout.layout')
@section('title', $title)

@section('main-section')

    <style>
        .select2-container--default .select2-selection--single .select2-selection__clear {
            display: none !important;
        }

        .select2-container+.select2-container {
            display: none !important;
        }

        #dailyReportList {
            width: 100% !important;
            table-layout: fixed !important;
        }

        #dailyReportList th,
        #dailyReportList td {
            white-space: normal !important;
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: top;
        }

        #dailyReportList th:nth-child(1),
        #dailyReportList td:nth-child(1) {
            width: 6% !important;
            text-align: center;
        }

        #dailyReportList th:nth-child(2),
        #dailyReportList td:nth-child(2),
        #dailyReportList th:nth-child(3),
        #dailyReportList td:nth-child(3) {
            width: 10% !important;
        }

        #dailyReportList th:nth-child(4),
        #dailyReportList td:nth-child(4) {
            width: 15% !important;
        }

        #dailyReportList th:nth-child(5),
        #dailyReportList td:nth-child(5) {
            width: 30% !important;
            max-width: 30% !important;
            word-break: break-word;
        }

        #dailyReportList th:nth-child(6),
        #dailyReportList td:nth-child(6),
        #dailyReportList th:nth-child(7),
        #dailyReportList td:nth-child(7),
        #dailyReportList th:nth-child(8),
        #dailyReportList td:nth-child(8) {
            width: 10% !important;
        }

        .dataTables_scrollBody {
            overflow-x: hidden !important;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">

            <!-- Filter Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filter Your Reports</h5>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" id="filterFromDate" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" id="filterToDate" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <button id="applyFilters" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Your Daily Reports</h4>

                    <div class="d-flex gap-2">
                        <a href="{{ route('employee.daily.reports') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Fill DR
                        </a>

                        <button id="exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dailyReportList" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sn</th>
                                    <th>DR Date</th>
                                    <th>Fill Date</th>
                                    <th>Project</th>
                                    <th>Description</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Total Hours</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="text-end">
                                    <td colspan="7"><strong>Total Hours</strong></td>
                                    <td id="totalHours"><strong>00:00</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {

            let today = new Date();
            let currentMonth = String(today.getMonth() + 1).padStart(2, '0');
            let firstDay = today.getFullYear() + '-' + currentMonth + '-01';
            let lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            lastDay = today.getFullYear() + '-' + currentMonth + '-' + String(lastDay.getDate()).padStart(2, '0');

            $('#filterFromDate').val(firstDay);
            $('#filterToDate').val(lastDay);

            var table = $('#dailyReportList').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('employee.daily.reports.ajax') }}",
                    data: function(d) {
                        d.from_date = $('#filterFromDate').val();
                        d.to_date = $('#filterToDate').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'DrEntryDate'
                    },
                    {
                        data: 'CreationDate'
                    },
                    {
                        data: 'project_name'
                    },
                    {
                        data: 'WorkDescription'
                    },
                    {
                        data: 'StartingTime'
                    },
                    {
                        data: 'EndingTime'
                    },
                    {
                        data: 'TotalHours'
                    }
                ],
                columnDefs: [{
                        width: "5%",
                        targets: 0
                    },
                    {
                        width: "10%",
                        targets: 1
                    },
                    {
                        width: "10%",
                        targets: 2
                    },
                    {
                        width: "15%",
                        targets: 3
                    },
                    {
                        width: "20%",
                        targets: 4
                    },
                    {
                        width: "10%",
                        targets: 5
                    },
                    {
                        width: "10%",
                        targets: 6
                    },
                    {
                        width: "10%",
                        targets: 7
                    }
                ],
                drawCallback: function() {
                    calculateTotalHours();
                }
            });

            // Apply filters only when the Submit button is clicked.
            $('#applyFilters').click(function() {
                table.ajax.reload();
            });

            function calculateTotalHours() {
                let totalMinutes = 0;
                $('#dailyReportList tbody tr').each(function() {
                    let time = $(this).find('td:last').text();
                    if (time) {
                        let t = time.split(':');
                        totalMinutes += (parseInt(t[0] || 0) * 60) + parseInt(t[1] || 0);
                    }
                });
                let h = String(Math.floor(totalMinutes / 60)).padStart(2, '0');
                let m = String(totalMinutes % 60).padStart(2, '0');
                $('#totalHours').html(`<strong>${h}:${m}</strong>`);
            }

            $('#exportExcel').click(function(e) {
                e.preventDefault();
                let params = $.param({
                    from_date: $('#filterFromDate').val(),
                    to_date: $('#filterToDate').val(),
                    project_id: $('#filterProject').length ? $('#filterProject').val() : ''
                });
                window.location.href = "{{ route('employee.daily.reports.export') }}" + "?" + params;
            });

        });
    </script>
@endpush
