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
                        <a href="{{ route('email-templates.create') }}"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-circle-plus me-2"></i> Add Email Template
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive mt-3">
                                <table id="EmailTemplateList" class="display table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Sn</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Preview</th>
                                            <th>Action</th>

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

        <x-footer />
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                        <i class="ti ti-trash-x fs-36"></i>
                    </span>
                    <h4 class="mb-1">Confirm Delete</h4>
                    <p class="mb-3">Are you sure you want to delete this leave type? This action cannot be undone.</p>
                    <input type="hidden" id="deleteLeaveUrl">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#EmailTemplateList').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('settings.email-template.list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, // Sn
                    {
                        data: 'template_key',
                        name: 'template_key'
                    }, // Template Name
                    {
                        data: 'subject',
                        name: 'subject'
                    }, // Subject

                    // Preview Column
                    {
                        data: 'id',
                        render: function(data) {
                            return `
                        <a href="{{ url('settings/email-template/preview') }}/` + data + `" class="btn btn-sm btn-info" target="_blank">
                            Preview
                        </a>
                    `;
                        },
                        orderable: false,
                        searchable: false
                    },

                    // Action Column (Edit / Delete)
                    {
                        data: 'id',
                        render: function(data) {
                            return `
                        <a href="{{ url('settings/email-template/edit') }}/` + data + `" class="btn btn-sm btn-primary me-2">
                            Edit
                        </a>
                        <a href="javascript:void(0);" onclick="deleteTemplate(` + data + `)" class="btn btn-sm btn-danger">
                            Delete
                        </a>
                    `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });
        });

        function deleteTemplate(id) {
            $('#deleteLeaveUrl').val(id);
            $('#delete_modal').modal('show');
        }

        $('#confirmDeleteBtn').click(function() {
            var id = $('#deleteLeaveUrl').val();
            $.ajax({
                url: "{{ url('settings/email-template/delete') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $('#delete_modal').modal('hide');
                    $('#EmailTemplateList').DataTable().ajax.reload();
                },
                error: function(err) {
                    alert(err.responseJSON?.message || 'Something went wrong!');
                }
            });
        });
    </script>
@endpush
