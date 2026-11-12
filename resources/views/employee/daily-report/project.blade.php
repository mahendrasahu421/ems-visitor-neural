@extends('employee.layout.layout')
@section('title', $title)

<style>
    /* Select2 fixes */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    .select2-container+.select2-container {
        display: none !important;
    }

    /* Table width fix */
    #projectReportList {
        width: 100% !important;
        table-layout: fixed;
    }

    #projectReportList th,
    #projectReportList td {
        white-space: normal !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        vertical-align: top;
    }

    /* Column width control */
    #projectReportList th:nth-child(1),
    #projectReportList td:nth-child(1) {
        width: 7% !important;
        text-align: center;
    }

    #projectReportList th:nth-child(2),
    #projectReportList td:nth-child(2) {
        width: 12% !important;
    }

    #projectReportList th:nth-child(3),
    #projectReportList td:nth-child(3) {
        width: 15% !important;
    }

    #projectReportList th:nth-child(4),
    #projectReportList td:nth-child(4) {
        width: 30% !important;
        font-weight: 500;
    }

    #projectReportList th:nth-child(5),
    #projectReportList td:nth-child(5) {
        width: 12% !important;
    }

    #projectReportList th:nth-child(6),
    #projectReportList td:nth-child(6) {
        width: 12% !important;
    }

    #projectReportList th:nth-child(7),
    #projectReportList td:nth-child(7) {
        width: 12% !important;
    }

    /* DataTable scroll fix */
    .dataTables_scrollBody {
        overflow-x: hidden !important;
    }
</style>

@section('main-section') 
    <div class="page-wrapper">
        <div class="content">

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filter Your Reports by Project</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Project</label>
                            <select id="filterProject" class="form-select select2">
                                <option value="">All Projects</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->project_id ?? $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">From Date</label>
                            <input type="date" id="filterFromDate" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">To Date</label>
                            <input type="date" id="filterToDate" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col text-end">
                            <button id="resetFilters" class="btn btn-outline-primary">
                                <i class="fas fa-sync-alt me-2"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Project-wise Daily Reports</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="projectReportList" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sn</th>
                                    <th>DR Date</th>
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
                                    <td colspan="6"><strong>Total Hours</strong></td>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            // Select2 init
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });

            // DataTable init
            var table = $('#projectReportList').DataTable({
                processing: true,
                serverSide: true,
                scrollX: false,
                autoWidth: false,
                ajax: {
                    url: "{{ route('employee.daily.reports.project.ajax') }}",
                    data: function(d) {
                        d.project_id = $('#filterProject').val();
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
                order: [
                    [0, 'desc']
                ],
                drawCallback: function() {
                    calculateTotalHours();
                }
            });

            // Filter change
            $('#filterProject, #filterFromDate, #filterToDate').change(function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').click(function() {
                $('#filterProject, #filterFromDate, #filterToDate').val('').trigger('change');
                table.ajax.reload();
            });

            // Calculate total hours
            function calculateTotalHours() {
                let totalMinutes = 0;

                $('#projectReportList tbody tr').each(function() {
                    let time = $(this).find('td:last').text().trim();
                    if (time && time.includes(':')) {
                        let t = time.split(':');
                        totalMinutes += (parseInt(t[0] || 0) * 60) + parseInt(t[1] || 0);
                    }
                });

                let h = String(Math.floor(totalMinutes / 60)).padStart(2, '0');
                let m = String(totalMinutes % 60).padStart(2, '0');

                $('#totalHours').html('<strong>' + h + ':' + m + '</strong>');
            }

        });
    </script>
@endpush
