@extends('layout.master')
@section('title', $title ?? 'Leave Types')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .leave-type-page .stat-kpi-card {
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

        .leave-type-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .leave-type-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .leave-type-page .kpi-icon-box {
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

        .leave-type-page .table-container-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .leave-type-page .filter-bar-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 12px 16px;
        }

        .leave-type-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .leave-type-page .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        /* Modern DataTables Pagination & Info */
        .leave-type-page .dataTables_wrapper .dataTables_info {
            color: #64748b !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .leave-type-page .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .leave-type-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

        .leave-type-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .leave-type-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .leave-type-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.28) !important;
        }

        .leave-type-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .leave-type-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.65 !important;
        }
    </style>

    <div class="page-wrapper leave-type-page">
        <div class="content">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-event fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">Leave Policy & Types</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $totalLeaveTypes ?? 0 }} Policies
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Settings</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Leave Types</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('settings.leave-type.create') }}" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center">
                        <i class="ti ti-plus me-1.5 fs-16"></i>Add Leave Type
                    </a>
                </div>
            </div>

            {{-- 4 Metric KPI Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Types --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-category="" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Leave Types</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($totalLeaveTypes ?? 0) }}</h3>
                                <span class="badge bg-light text-secondary border fs-11 mt-1">All Defined Policies</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-calendar-event"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Active Types --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Active Policies</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ number_format($activeLeaveTypes ?? 0) }}</h3>
                                <span class="badge bg-success-subtle text-success fs-11 mt-1">In Operation</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-circle-check"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Paid Categories --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100 kpi-filter-btn" data-category="Paid" style="--card-accent: #6366f1; --card-soft-bg: #eef2ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Paid Leave Types</span>
                                <h3 class="mb-0 mt-1 fw-bold text-primary">{{ number_format($paidLeaveTypes ?? 0) }}</h3>
                                <span class="badge bg-primary-subtle text-primary fs-11 mt-1">With Compensation</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-cash"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Total Allowed Quota --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-kpi-card h-100" style="--card-accent: #f59e0b; --card-soft-bg: #fef3c7;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Annual Quota</span>
                                <h3 class="mb-0 mt-1 fw-bold text-warning">{{ number_format($totalQuota ?? 0) }} <span class="fs-14 fw-normal text-muted">Days</span></h3>
                                <span class="badge bg-warning-subtle text-warning fs-11 mt-1">Per Employee / Year</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-clock-check"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="filter-bar-card mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-6 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-search"></i></span>
                            <input type="text" id="leaveTypeSearchInput" class="form-control border-start-0 ps-0" placeholder="Search by leave name, code, category, applicability...">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-8 d-flex align-items-center gap-1">
                        <div class="btn-group w-100" role="group" id="categoryFilterGroup">
                            <button type="button" class="btn btn-sm btn-primary active category-pill-btn" data-category="">All Categories</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary category-pill-btn" data-category="Paid">Paid</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary category-pill-btn" data-category="Unpaid">Unpaid</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary category-pill-btn" data-category="Special">Special</button>
                        </div>
                    </div>

                    <div class="col-lg-2 col-sm-4 d-flex justify-content-end">
                        <button type="button" id="refreshTableBtn" class="btn btn-light border btn-sm d-inline-flex align-items-center w-100 justify-content-center">
                            <i class="ti ti-rotate-clockwise me-1.5"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>

            {{-- Leave Type Data Table Card --}}
            <div class="card table-container-card">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">Leave Type Configurations</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="leaveTypeList" class="table table-hover align-middle mb-0 nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Sn</th>
                                    <th>Leave Type & Description</th>
                                    <th>Code</th>
                                    <th>Annual Quota</th>
                                    <th>Category</th>
                                    <th>Applicability</th>
                                    <th>Carry Fwd</th>
                                    <th>Encash</th>
                                    <th>Status</th>
                                    <th style="width: 100px;" class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <span class="avatar avatar-xl rounded-circle bg-danger-subtle text-danger mb-3 d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-trash-x fs-32"></i>
                        </span>
                        <h5 class="fw-bold text-dark mb-1">Delete Leave Type?</h5>
                        <p class="text-muted fs-13 mb-3">Are you sure you want to remove <strong id="deleteLeaveName" class="text-dark">this leave type</strong>?</p>
                        <input type="hidden" id="deleteLeaveUrl">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmDeleteBtn" class="btn btn-danger px-3 fw-semibold">Yes, Delete</button>
                        </div>
                    </div>
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
            let currentCategory = '';

            const table = $('#leaveTypeList').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('settings.leave-type.list') }}",
                    data: function(d) {
                        d.category_filter = currentCategory;
                    }
                },
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: "<'row'<'col-12'tr>>" +
                     "<'row align-items-center justify-content-between p-3 border-top g-2'<'col-md-6 col-12 d-flex align-items-center'i><'col-md-6 col-12 d-flex justify-content-md-end justify-content-center'p>>",
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ leave types",
                    infoEmpty: "Showing 0 to 0 of 0 types",
                    infoFiltered: "(filtered from _MAX_ total)",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                        last: '<i class="ti ti-chevrons-right"></i>'
                    },
                    emptyTable: "No leave types found",
                    zeroRecords: "No matching leave types found"
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        render: data => `<span class="badge bg-light text-secondary border font-monospace fs-11">#${data}</span>`
                    },
                    {
                        data: 'leave_name',
                        name: 'leave_name'
                    },
                    {
                        data: 'leave_code',
                        name: 'leave_code'
                    },
                    {
                        data: 'total_leaves',
                        name: 'total_leaves'
                    },
                    {
                        data: 'leave_category',
                        name: 'leave_category'
                    },
                    {
                        data: 'applicable_for',
                        name: 'applicable_for'
                    },
                    {
                        data: 'carry_forward',
                        name: 'carry_forward'
                    },
                    {
                        data: 'encashable',
                        name: 'encashable'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Live Search
            $('#leaveTypeSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Category Filter Buttons
            $('.category-pill-btn').on('click', function() {
                $('.category-pill-btn').removeClass('btn-primary active').addClass('btn-outline-secondary');
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');

                currentCategory = $(this).data('category');
                table.ajax.reload();
            });

            // KPI Card click
            $('.kpi-filter-btn').on('click', function() {
                const cat = $(this).data('category');
                $(`.category-pill-btn[data-category="${cat}"]`).trigger('click');
            });

            // Reset Filter
            $('#refreshTableBtn').on('click', function() {
                $('#leaveTypeSearchInput').val('');
                currentCategory = '';
                table.search('').columns().search('').draw();
                $(`.category-pill-btn[data-category=""]`).trigger('click');
            });
        });

        function deleteLeave(id, name) {
            $('#deleteLeaveUrl').val(id);
            $('#deleteLeaveName').text(name || 'this leave type');
            $('#delete_modal').modal('show');
        }

        $('#confirmDeleteBtn').click(function() {
            const id = $('#deleteLeaveUrl').val();
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

            $.ajax({
                url: "{{ url('settings/leave-type/delete') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $btn.prop('disabled', false).text('Yes, Delete');
                    $('#delete_modal').modal('hide');

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: res.message || "Leave Type deleted successfully.",
                        timer: 1600,
                        showConfirmButton: false
                    });

                    $('#leaveTypeList').DataTable().ajax.reload(null, false);
                },
                error: function(err) {
                    $btn.prop('disabled', false).text('Yes, Delete');
                    const msg = err.responseJSON?.message || 'Something went wrong!';
                    Swal.fire("Action Blocked", msg, "warning");
                }
            });
        });
    </script>
@endpush
