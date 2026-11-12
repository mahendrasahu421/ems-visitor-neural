@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')
    <x-alert-modal />
    <div class="page-wrapper" style="min-height: 428px;">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item active" aria-current="page">{{ $titleRoute }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">

                    <div class="mb-2">
                        <a href="{{ route('employee.leaves.apply') }}" class="btn btn-primary d-flex align-items-center"><i
                                class="ti ti-circle-plus me-2"></i>Apply for
                            Leave</a>
                    </div>

                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Leaves Info -->
            <div class="row">
                @foreach ($leaveSummary as $leave)
                    <div class="col-xl-3 col-md-6">
                        <div class="card {{ $leave['color'] }}">
                            <div class="card-body">

                                @if (isset($leave['empty']))
                                    <div class="text-center py-4 text-muted">
                                        <i class="fa fa-ban fs-32"></i>
                                        <p class="mb-0">Not Applicable</p>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start">
                                            <p class="mb-1">{{ $leave['name'] }}</p>
                                            <h4>{{ $leave['total_leaves'] }}</h4>
                                        </div>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-2">
                                                <span class="avatar avatar-md d-flex">
                                                    <i class="{{ $leave['icon'] }} fs-32"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <span class="badge bg-secondary-transparent mt-2">
                                        Remaining Leaves : {{ $leave['remaining'] }}
                                    </span>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <!-- /Leaves Info -->


            <!-- Leaves list -->
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <div class="d-flex">
                        <h5 class="me-2">Leave List</h5>
                        <span class="badge bg-primary-transparent me-2">Total Leaves : {{ $totalAllotted }}</span>
                        <span class="badge bg-secondary-transparent">Total Remaining Leaves : {{ $totalRemaining }}</span>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                        <div class="me-3">
                            <!-- koi aur content -->
                        </div>

                        <div class="me-3">
                            <label for="leave_type_filter" class="form-label">Leave Type</label>
                            <select id="leave_type_filter" class="form-control">
                                <option value="">All Leave Types</option>
                                @foreach ($leaveSummary as $leave)
                                    @if (!isset($leave['empty']))
                                        <option value="{{ $leave['id'] }}">{{ $leave['name'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                    </div>


                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="">

                            <div class="card-body">

                                <div class="table-responsive mt-3">
                                    <table id="tableexample" class="display table table-striped table-bordered nowrap">
                                        <thead>
                                            <tr>
                                                <th>Sn</th>
                                                <th>Leave Type</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Total Leaves</th>
                                                <th>reason</th>
                                                <th>status</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Leaves list -->

        </div>
        <x-footer />
    </div>



@endsection
@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            // 🔹 Initialize DataTable
            var table = $('#tableexample').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('employee.leaves.list') }}",
                    data: function(d) {
                        d.leave_type = $('#leave_type_filter').val(); // send selected leave type
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'leave_type'
                    },
                    {
                        data: 'from_date'
                    },
                    {
                        data: 'to_date'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let fromDate = new Date(row.from_date);
                            let toDate = new Date(row.to_date);
                            if (fromDate && toDate && toDate >= fromDate) {
                                let diffTime = toDate - fromDate;
                                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                                return diffDays;
                            }
                            return 0;
                        }
                    },
                    {
                        data: 'reason'
                    },
                    {
                        data: 'status'
                    }
                ],
                dom: "<'row mb-2'<'col-md-6'l><'col-md-6 text-end'B f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>"
            });

            // 🔹 Filter on change
            $('#leave_type_filter').on('change', function() {
                table.ajax.reload();
            });

            window.fetchGenderCounts = function() {
                table.ajax.reload();
            };
        });
    </script>
@endpush
