@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="mb-2">
                        <a href="{{ route('settings.shift.create') }}"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-circle-plus me-2"></i>Add Shift / Working Hours
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="shiftList" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Sn</th>
                                    <th>Shift Name</th>
                                    <th>Shift Code</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Grace Period</th>
                                    <th>Break Duration</th>
                                    <th>Total Working Hours</th>
                                    <th>Week Offs</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <x-footer />
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                        <i class="ti ti-trash-x fs-36"></i>
                    </span>
                    <h4 class="mb-1">Confirm Delete</h4>
                    <p class="mb-3">Are you sure you want to delete this shift? This action cannot be undone.</p>
                    <input type="hidden" id="deleteShiftUrl">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete</button>
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
            var table = $('#shiftList').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('settings.shift.list') }}",
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'shift_name',
                        name: 'shift_name'
                    },
                    {
                        data: 'shift_code',
                        name: 'shift_code'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'grace_period',
                        name: 'grace_period'
                    },
                    {
                        data: 'break_duration',
                        name: 'break_duration'
                    },
                    {
                        data: 'total_working_hours',
                        name: 'total_working_hours'
                    },
                    {
                        data: 'week_off_days',
                        name: 'week_off_days',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                        <div class="action-icon d-inline-flex">
                            <a href="{{ url('masters/organisation/shift/edit') }}/` + data + `" class="me-2"><i class="ti ti-edit"></i></a>
                            <a href="javascript:void(0);" onclick="deleteShift(` + data + `)"><i class="ti ti-trash"></i></a>
                        </div>
                    `;
                        },
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [0, 'asc']
                ],
                dom: "<'row mb-2'<'col-md-6'l><'col-md-6 text-end'f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
            });
        });

        function deleteShift(id) {
            $('#deleteShiftUrl').val(id);
            $('#delete_modal').modal('show');
        }

        $('#confirmDeleteBtn').click(function() {
            var id = $('#deleteShiftUrl').val();
            $.ajax({
                url: "{{ url('masters/organisation/shift/delete') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $('#delete_modal').modal('hide');
                    $('#shiftList').DataTable().ajax.reload();
                },
                error: function(err) {
                    console.log(err.responseJSON || err);
                    alert(err.responseJSON?.message || 'Something went wrong!');
                }
            });
        });
    </script>
@endpush
