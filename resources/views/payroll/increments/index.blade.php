@extends('layout.master')
@section('title', $title)
@section('main-section')

<style>
    .increment-page .stat-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    .increment-page .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }
    .increment-page .table-container-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        background: #ffffff;
    }
    .increment-table th {
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc !important;
        color: #475569;
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    .increment-table td {
        vertical-align: middle;
        font-size: 13px;
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-soft-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-soft-primary {
        background-color: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .badge-soft-warning {
        background-color: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }

    /* Table Hover Effect */
    .increment-table tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Modern DataTables Pagination, Length & Info */
    .increment-page .dataTables_wrapper {
        padding: 0;
    }

    .increment-page .dataTables_wrapper .dataTables_info,
    .increment-page .dt-container .dt-info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .increment-page .dataTables_wrapper .dataTables_length,
    .increment-page .dt-container .dt-length {
        margin: 0 !important;
    }

    .increment-page .dataTables_wrapper .dataTables_length label,
    .increment-page .dt-container .dt-length label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 0;
        font-weight: 500;
    }

    .increment-page .dataTables_wrapper .dataTables_length select,
    .increment-page .dt-container .dt-length select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 4px 28px 4px 10px;
        font-size: 12.5px;
        font-weight: 500;
        color: #334155;
        background-color: #ffffff;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .increment-page .dataTables_wrapper .dataTables_length select:focus,
    .increment-page .dt-container .dt-length select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    /* Modern Pagination Buttons (supports Bootstrap 5, standard DataTables, and DataTables 2) */
    .increment-page .dataTables_wrapper .dataTables_paginate,
    .increment-page .dataTables_wrapper .dataTables_paginate ul.pagination,
    .increment-page .dt-container .dt-paging,
    .increment-page .dt-container .dt-paging nav {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .increment-page .dataTables_wrapper .dataTables_paginate .paginate_button,
    .increment-page .dataTables_wrapper .pagination .page-item .page-link,
    .increment-page .dt-container .dt-paging .dt-paging-button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 34px !important;
        height: 34px !important;
        padding: 0 10px !important;
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
        line-height: 1 !important;
        box-shadow: none !important;
    }

    .increment-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled),
    .increment-page .dataTables_wrapper .pagination .page-item:not(.active):not(.disabled) .page-link:hover,
    .increment-page .dt-container .dt-paging .dt-paging-button:hover:not(.disabled) {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .increment-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .increment-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
    .increment-page .dataTables_wrapper .pagination .page-item.active .page-link,
    .increment-page .dt-container .dt-paging .dt-paging-button.current,
    .increment-page .dt-container .dt-paging .dt-paging-button.current:hover {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
    }

    .increment-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .increment-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
    .increment-page .dataTables_wrapper .pagination .page-item.disabled .page-link,
    .increment-page .dt-container .dt-paging .dt-paging-button.disabled,
    .increment-page .dt-container .dt-paging .dt-paging-button.disabled:hover {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }

    /* Processing Indicator */
    .increment-page .dataTables_processing {
        background: rgba(255, 255, 255, 0.95) !important;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        font-weight: 600;
        color: #0d6efd;
        z-index: 100;
        padding: 14px 24px !important;
    }
</style>

<div class="page-wrapper increment-page">
    <div class="content">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 font-weight-bold">{{ $title }}</h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12">
                        <i class="ti ti-chart-arrows-vertical me-1"></i> Annual Appraisal Flow
                    </span>
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.batches') }}" class="text-secondary">Payroll</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Annual Increments</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <a href="{{ route('payroll.increments.bulk') }}" class="btn btn-outline-primary d-flex align-items-center shadow-sm">
                    <i class="ti ti-users-group me-1 fs-16"></i> Bulk Annual Hike (Batch)
                </a>
                <a href="{{ route('payroll.increments.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm">
                    <i class="ti ti-plus me-1 fs-16"></i> Process Individual Increment
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-circle-check fs-20 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-20 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card mb-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-12 font-weight-bold text-uppercase">Total Increments</span>
                            <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ti ti-chart-line fs-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 font-weight-bold text-dark">{{ number_format($totalProcessed) }}</h3>
                        <div class="fs-12 text-muted">
                            <span class="text-success font-weight-semibold"><i class="ti ti-check me-1"></i>Applied</span> across staff
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card mb-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-12 font-weight-bold text-uppercase">Average Hike %</span>
                            <div class="avatar avatar-sm bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ti ti-percentage fs-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 font-weight-bold text-success">{{ $avgHikePercentage }}%</h3>
                        <div class="fs-12 text-muted">Average annual revision rate</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card mb-0 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-12 font-weight-bold text-uppercase">Annual Budget Added</span>
                            <div class="avatar avatar-sm bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ti ti-wallet fs-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 font-weight-bold text-dark">&#8377; {{ number_format($totalAnnualHike, 2) }}</h3>
                        <div class="fs-12 text-muted">Total annual CTC budget increase</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card mb-0 h-100 border-primary-subtle" style="background: linear-gradient(135deg, #f8faff 0%, #edf2fc 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-primary fs-12 font-weight-bold text-uppercase">Due for Annual Review</span>
                            <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ti ti-calendar-due fs-18"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 font-weight-bold text-primary">{{ number_format($dueForReviewCount) }}</h3>
                        <div class="fs-12 text-muted">
                            <a href="{{ route('payroll.increments.bulk') }}" class="text-primary font-weight-semibold text-decoration-none">
                                Review &amp; Apply Batch &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fs-12 text-muted mb-1">Search Employee</label>
                        <input type="text" id="filter_search" class="form-control form-control-sm" placeholder="Type name, ID or code...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12 text-muted mb-1">Review Cycle</label>
                        <select id="filter_cycle" class="form-select form-select-sm">
                            <option value="all">All Appraisal Cycles</option>
                            @foreach($reviewCycles as $cyc)
                                <option value="{{ $cyc }}">{{ $cyc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted mb-1">Year</label>
                        <select id="filter_year" class="form-select form-select-sm">
                            <option value="all">All Years</option>
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 text-md-end mt-md-4">
                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="resetFilters()">
                            <i class="ti ti-refresh me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="reloadTable()">
                            <i class="ti ti-filter me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Increments Table Card -->
        <div class="card table-container-card mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 font-weight-bold text-dark">
                    <i class="ti ti-list-details text-primary me-2"></i> Increment &amp; Appraisal Records
                </h5>
                <span class="badge bg-light text-secondary border fs-12">Historical &amp; Current Revisions</span>
            </div>
            <div class="card-body p-0">
                <table class="table increment-table mb-0 w-100" id="incrementsTable">
                    <thead>
                        <tr>
                            <th>Inc. No</th>
                            <th>Employee</th>
                            <th>Review Cycle</th>
                            <th>Effective Date</th>
                            <th>Old Gross &rarr; New Gross</th>
                            <th>Old CTC &rarr; New CTC</th>
                            <th>Hike % / Amount</th>
                            <th>Designation / Promotion</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via DataTables AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal for Quick Increment Details -->
<div class="modal fade" id="incrementDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold" id="detailModalTitle">Increment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted fs-13">Loading details...</p>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="#" id="modalLetterBtn" target="_blank" class="btn btn-primary btn-sm">
                    <i class="ti ti-file-text me-1"></i> View Increment Letter
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let incTable;

    $(document).ready(function() {
        incTable = $('#incrementsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('payroll.increments.index') }}",
                data: function(d) {
                    d.cycle = $('#filter_cycle').val();
                    d.year = $('#filter_year').val();
                }
            },
            columns: [
                {
                    data: 'increment_number',
                    render: function(data, type, row) {
                        return `<span class="font-weight-bold text-dark fs-12">${data}</span>`;
                    }
                },
                {
                    data: 'employee_name',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary font-weight-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    ${data.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark fs-13">${data}</div>
                                    <div class="text-muted fs-11">${row.agency} &bull; ID: ${row.employee_id}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'review_cycle',
                    render: function(data) {
                        return `<span class="badge bg-light text-dark border fs-12">${data}</span>`;
                    }
                },
                {
                    data: 'effective_date',
                    render: function(data) {
                        return `<span class="text-secondary font-weight-semibold fs-12"><i class="ti ti-calendar me-1"></i>${data}</span>`;
                    }
                },
                {
                    data: 'new_gross',
                    render: function(data, type, row) {
                        return `
                            <div>
                                <span class="text-muted text-decoration-line-through fs-11">&#8377; ${Number(row.current_gross).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                                <div class="font-weight-bold text-dark fs-13">&#8377; ${Number(data).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                                <span class="text-success fs-11 font-weight-semibold">+&#8377; ${Number(row.monthly_diff).toLocaleString('en-IN', {minimumFractionDigits: 2})}/mo</span>
                            </div>
                        `;
                    }
                },
                {
                    data: 'new_ctc',
                    render: function(data, type, row) {
                        return `
                            <div>
                                <span class="text-muted text-decoration-line-through fs-11">&#8377; ${Number(row.current_ctc).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                                <div class="font-weight-bold text-primary fs-13">&#8377; ${Number(data).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'hike_percentage',
                    render: function(data, type, row) {
                        return `
                            <div>
                                <span class="badge badge-soft-success px-2 py-1 fs-12 font-weight-bold">
                                    <i class="ti ti-arrow-up-right me-1"></i>${Number(data).toFixed(1)}%
                                </span>
                                <div class="text-muted fs-11 mt-1">+&#8377; ${Number(row.hike_amount).toLocaleString('en-IN', {minimumFractionDigits: 0})}/yr</div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'new_designation',
                    render: function(data, type, row) {
                        if (row.is_promotion) {
                            return `
                                <div>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11 mb-1">
                                        <i class="ti ti-arrow-big-up-line me-1"></i>Promoted
                                    </span>
                                    <div class="font-weight-bold text-dark fs-12">${data}</div>
                                </div>
                            `;
                        }
                        return `<span class="text-dark fs-12">${data}</span>`;
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        return `<span class="badge ${row.status_badge} fs-12">${data}</span>`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    className: 'text-end',
                    render: function(data, type, row) {
                        return `
                            <div class="d-inline-flex gap-1">
                                <a href="${row.show_url}" class="btn btn-sm btn-outline-secondary p-1 px-2" title="View Audit Details">
                                    <i class="ti ti-eye fs-14"></i>
                                </a>
                                <a href="${row.letter_url}" class="btn btn-sm btn-outline-primary p-1 px-2" title="View Official Increment Letter" target="_blank">
                                    <i class="ti ti-file-text fs-14"></i>
                                </a>
                                <a href="${row.download_url}" class="btn btn-sm btn-outline-success p-1 px-2" title="Download Letter PDF">
                                    <i class="ti ti-download fs-14"></i>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            order: [[3, 'desc']],
            autoWidth: false,
            pageLength: 15,
            lengthMenu: [
                [10, 15, 25, 50, 100],
                [10, 15, 25, 50, 100]
            ],
            dom: "<'table-responsive'tr>" +
                 "<'row align-items-center justify-content-between px-3 py-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center gap-3'li><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
            language: {
                search: "",
                searchPlaceholder: "Search increment records...",
                lengthMenu: "Show _MENU_ records",
                info: "Showing <span class='fw-bold text-dark'>_START_</span> to <span class='fw-bold text-dark'>_END_</span> of <span class='fw-bold text-dark'>_TOTAL_</span> increments",
                infoEmpty: "Showing 0 to 0 of 0 increments",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="ti ti-search-off fs-24"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No Matching Increments Found</h6>
                        <p class="text-muted fs-13 mb-0">Try adjusting your search criteria or review cycle filter.</p>
                    </div>
                `,
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl rounded-circle bg-light text-muted mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="ti ti-chart-line-off fs-24"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No Annual Increments Recorded</h6>
                        <p class="text-muted fs-13 mb-0">Process an individual increment or run a bulk annual review batch.</p>
                    </div>
                `,
                paginate: {
                    first: '<i class="ti ti-chevrons-left"></i>',
                    previous: '<i class="ti ti-chevron-left"></i>',
                    next: '<i class="ti ti-chevron-right"></i>',
                    last: '<i class="ti ti-chevrons-right"></i>'
                }
            }
        });

        $('#filter_search').on('keyup', function() {
            incTable.search(this.value).draw();
        });

        $('#filter_cycle, #filter_year').on('change', function() {
            reloadTable();
        });
    });

    function reloadTable() {
        incTable.ajax.reload();
    }

    function resetFilters() {
        $('#filter_search').val('');
        $('#filter_cycle').val('all');
        $('#filter_year').val('all');
        incTable.search('').ajax.reload();
    }
</script>
@endpush
