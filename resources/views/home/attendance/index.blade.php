@extends('layout.master')
@section('title', $title ?? 'Leave Requests')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .leave-requests-page .stat-kpi-card {
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

        .leave-requests-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .leave-requests-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .leave-requests-page .kpi-icon-box {
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

        .leave-requests-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .leave-requests-page .filter-bar-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 12px 16px;
        }

        .leave-requests-page .avatar-initials {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #ffffff;
            font-size: 13px;
            flex-shrink: 0;
        }

        .leave-requests-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        /* Modern DataTables Pagination & Info */
        .leave-requests-page .dataTables_wrapper .dataTables_info {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .leave-requests-page .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .leave-requests-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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
        }

        .leave-requests-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .leave-requests-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .leave-requests-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
        }

        .leave-requests-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .leave-requests-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
        }
    </style>

    <div class="page-wrapper leave-requests-page">
        <div class="content">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-user fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">{{ $title ?? 'Leave Requests' }}</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $totalRequests ?? 0 }} Total
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Attendance</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Leave Requests</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4 Metric KPI Stat Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Requests --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Applications</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($totalRequests ?? 0) }}</h3>
                                <span class="badge bg-light text-secondary border fs-11 mt-1">All Recorded</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-clipboard-list"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Pending Requests --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="Pending" style="--card-accent: #f59e0b; --card-soft-bg: #fef3c7;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Pending Approvals</span>
                                <h3 class="mb-0 mt-1 fw-bold text-warning">{{ number_format($pendingCount ?? 0) }}</h3>
                                <span class="badge bg-warning-subtle text-warning fs-11 mt-1">Requires Action</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-clock-hour-4"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Approved Leaves --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="Approved" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Approved Leaves</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ number_format($approvedCount ?? 0) }}</h3>
                                <span class="badge bg-success-subtle text-success fs-11 mt-1">Granted</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-circle-check"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Rejected Requests --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-status="Rejected" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Rejected Leaves</span>
                                <h3 class="mb-0 mt-1 fw-bold text-danger">{{ number_format($rejectedCount ?? 0) }}</h3>
                                <span class="badge bg-danger-subtle text-danger fs-11 mt-1">Denied</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-circle-x"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Search Toolbar --}}
            <div class="filter-bar-card mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-4 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-search"></i></span>
                            <input type="text" id="leaveSearchInput" class="form-control border-start-0 ps-0" placeholder="Search by staff, email, reason...">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <select id="designationFilter" class="form-select select2">
                            <option value="">All Designations</option>
                            @foreach ($designation as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="btn-group w-100" role="group" id="statusFilterGroup">
                            <button type="button" class="btn btn-sm btn-primary active status-pill-btn" data-status="">All ({{ $totalRequests ?? 0 }})</button>
                            <button type="button" class="btn btn-sm btn-outline-warning status-pill-btn" data-status="Pending">Pending ({{ $pendingCount ?? 0 }})</button>
                            <button type="button" class="btn btn-sm btn-outline-success status-pill-btn" data-status="Approved">Approved</button>
                            <button type="button" class="btn btn-sm btn-outline-danger status-pill-btn" data-status="Rejected">Rejected</button>
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-12 d-flex justify-content-end">
                        <button type="button" id="refreshTableBtn" class="btn btn-light border btn-sm d-inline-flex align-items-center w-100 justify-content-center">
                            <i class="ti ti-rotate-clockwise me-1.5"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Leave Requests Data Table Card --}}
            <div class="card table-container-card">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">Staff Leave Applications</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="leaveRequestsTable" class="table table-hover align-middle mb-0 nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Sn</th>
                                    <th>Employee</th>
                                    <th>Leave Type</th>
                                    <th>Duration</th>
                                    <th>Leave Dates</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th style="width: 130px;" class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Status Confirmation Modal -->
        <div class="modal fade" id="statusModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <form id="statusForm">
                        @csrf
                        <input type="hidden" name="leave_id" id="leave_id">
                        <input type="hidden" name="new_status" id="new_status">
                        
                        <div class="modal-body text-center p-4">
                            <span class="avatar avatar-xl rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" id="statusIconBox">
                                <i class="ti ti-help fs-32" id="statusIcon"></i>
                            </span>
                            <h5 class="fw-bold text-dark mb-1" id="statusModalTitle">Confirm Action</h5>
                            <p class="text-muted fs-13 mb-4">Are you sure you want to mark this leave request as <b id="statusText" class="text-dark"></b>?</p>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary px-3 fw-semibold" id="confirmStatusSubmitBtn">Yes, Confirm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-footer />
    </div>

@endsection

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const avatarColors = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777'];

            let currentStatus = '';

            const table = $('#leaveRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('leaves.list') }}",
                    data: function(d) {
                        d.designation_id = $('#designationFilter').val();
                        d.status_filter = currentStatus;
                    }
                },
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: "<'row'<'col-12'tr>>" +
                     "<'row align-items-center justify-content-between p-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center'i><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ leave requests",
                    infoEmpty: "Showing 0 to 0 of 0 requests",
                    infoFiltered: "(filtered from _MAX_ total)",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                        last: '<i class="ti ti-chevrons-right"></i>'
                    },
                    emptyTable: "No leave requests found",
                    zeroRecords: "No matching leave applications found"
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        render: (data, type, row, meta) => `<span class="badge bg-light text-secondary border font-monospace fs-11">#${meta.row + meta.settings._iDisplayStart + 1}</span>`
                    },
                    {
                        data: 'employee',
                        render: function(data, type, row, meta) {
                            const name = data || 'Employee';
                            const email = row.employee_email || '-';
                            const desig = row.designation ? `<span class="badge bg-light text-secondary border fs-10 ms-1">${row.designation}</span>` : '';
                            const words = name.trim().split(/\s+/);
                            const initials = words.length >= 2 && words[0] && words[1]
                                ? (words[0][0] + words[1][0]).toUpperCase()
                                : name.substring(0, 2).toUpperCase();
                            const bgCol = avatarColors[meta.row % avatarColors.length];

                            return `
                                <div class="d-flex align-items-center">
                                    <span class="avatar-initials me-2" style="background-color: ${bgCol};">${initials}</span>
                                    <div>
                                        <div class="fw-semibold text-dark fs-13">${name} ${desig}</div>
                                        <div class="fs-11 text-muted"><a href="mailto:${email}" class="text-muted text-decoration-none">${email}</a></div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'leave_type',
                        render: data => `<span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 font-monospace">${data || 'General'}</span>`
                    },
                    {
                        data: 'days',
                        render: data => `<span class="badge bg-light text-dark border fs-12 fw-bold font-monospace">${data || 1} ${data == 1 ? 'Day' : 'Days'}</span>`
                    },
                    {
                        data: 'from_date',
                        render: function(data, type, row) {
                            return `
                                <div class="fs-12 text-dark">
                                    <span class="text-muted"><i class="ti ti-calendar me-1"></i>${data}</span>
                                    <i class="ti ti-arrow-narrow-right text-muted mx-1"></i>
                                    <span class="text-muted">${row.to_date}</span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'reason',
                        render: function(data) {
                            const cleanText = data || 'No reason provided';
                            return `<div class="text-truncate text-secondary fs-12" style="max-width: 220px;" title="${cleanText}">${cleanText}</div>`;
                        }
                    },
                    {
                        data: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false
                    }
                ]
            });

            // Live Search
            $('#leaveSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Designation Filter
            $('#designationFilter').on('change', function() {
                table.ajax.reload();
            });

            // Status Filter Buttons
            $('.status-pill-btn').on('click', function() {
                $('.status-pill-btn').removeClass('btn-primary active').addClass('btn-outline-secondary btn-outline-warning btn-outline-success btn-outline-danger');
                $(this).removeClass('btn-outline-secondary btn-outline-warning btn-outline-success btn-outline-danger').addClass('btn-primary active');

                currentStatus = $(this).data('status');
                table.ajax.reload();
            });

            // KPI Card filter click
            $('.kpi-filter-btn').on('click', function() {
                const status = $(this).data('status');
                $(`.status-pill-btn[data-status="${status}"]`).trigger('click');
            });

            // Reset Filter
            $('#refreshTableBtn').on('click', function() {
                $('#leaveSearchInput').val('');
                $('#designationFilter').val('').trigger('change');
                currentStatus = '';
                $(`.status-pill-btn[data-status=""]`).trigger('click');
            });

            // Open Action Modal
            $(document).on("click", ".openModal", function() {
                const leaveId = $(this).data("id");
                const newStatus = $(this).data("status");

                $("#leave_id").val(leaveId);
                $("#new_status").val(newStatus);
                $("#statusText").text(newStatus);

                const isApprove = (newStatus === 'Approved');
                $('#statusIconBox').removeClass('bg-success-subtle text-success bg-danger-subtle text-danger')
                                   .addClass(isApprove ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger');
                $('#statusIcon').removeClass('ti-circle-check ti-circle-x ti-help')
                                .addClass(isApprove ? 'ti-circle-check' : 'ti-circle-x');
                $('#confirmStatusSubmitBtn').removeClass('btn-primary btn-success btn-danger')
                                            .addClass(isApprove ? 'btn-success' : 'btn-danger')
                                            .text(isApprove ? 'Yes, Approve' : 'Yes, Reject');

                $("#statusModal").modal("show");
            });

            // Submit Status Form
            $("#statusForm").on("submit", function(e) {
                e.preventDefault();
                const $btn = $('#confirmStatusSubmitBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');

                $.ajax({
                    url: "{{ route('leave.updateStatus') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $("#statusModal").modal("hide");
                        $btn.prop('disabled', false).text('Yes, Confirm');

                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: response.message || "Leave status has been updated.",
                            timer: 1600,
                            showConfirmButton: false
                        });

                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).text('Yes, Confirm');
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Something went wrong";
                        Swal.fire("Error", msg, "error");
                    }
                });
            });
        });
    </script>
@endpush
