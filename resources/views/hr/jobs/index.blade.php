<!-- Page Wrapper -->
@extends('hr.layout.layout')
@section('title', $title)

@section('main-section')
    <div class="page-wrapper">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">

                            <li class="breadcrumb-item">
                                {{ $title }}
                            </li>

                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">

                    <div class="me-2 mb-2">
                        <div class="dropdown">
                            <a href="javascript:void(0);"
                                class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                                data-bs-toggle="dropdown">
                                <i class="ti ti-file-export me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu  dropdown-menu-end p-3">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                            class="ti ti-file-type-pdf me-1"></i>Export as PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1"><i
                                            class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-2">
                        <a href="{{ route('hr.jobs.create') }}" class="btn btn-primary d-flex align-items-center"><i
                                class="ti ti-circle-plus me-2"></i>{{ $title }}</a>
                    </div>

                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                            <h5>Categories</h5>

                        </div>
                        <div class="card-body p-0">
                            <div class="custom-datatable-filter table-responsive">
                                <div class="table-responsive ">
                                    <table class="display" id="jobs-list">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>title</th>
                                                <th>category_id</th>
                                                <th>skills</th>
                                                <th>positions</th>
                                                <th>job_type</th>
                                                <th>ctc_from</th>
                                                <th>ctc_to</th>
                                                <th>min_experience</th>
                                                <th>max_experience</th>
                                                <th>locations</th>
                                                <th>description</th>
                                                <th>keywords</th>
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

        </div>

    </div>

    @if (session('success'))
        <!-- Success Modal -->
        <div class="modal fade" id="success_modal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <span
                            class="avatar avatar-lg avatar-rounded bg-success mb-3 d-inline-flex align-items-center justify-content-center"
                            style="width:60px; height:60px;">
                            <i class="ti ti-check fs-24 text-white"></i>
                        </span>
                        <h5 class="mb-2">{{ session('success') }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Show Script -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var successModal = new bootstrap.Modal(document.getElementById('success_modal'));
                successModal.show();
            });
        </script>
    @endif




    <!-- Delete Modal -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                        <i class="ti ti-trash-x fs-36"></i>
                    </span>
                    <h4 class="mb-1">Confirm Delete</h4>
                    <p class="mb-3">You want to delete all the marked items, this cant be undone once you delete.</p>
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                        <a href="#" class="btn btn-danger">Yes,
                            Delete</a>
                    </div>
                </div>
            </div>
        </div>
         <x-footer />
    </div>
    <!-- /Delete Modal -->
@endsection

@section('script')
    @parent
    <!-- <script src="{{ asset('datatables/jquery.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            new DataTable('#jobs-list', {
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('hr.jobs.list') }}",
                    data: function(d) {
                        // Add custom filters here if needed.
                        // d.filter = $('#filter-input').val();
                    }
                },

                columns: [{
                        data: 'sn'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'salary'
                    },
                    {
                        data: 'location'
                    },
                    {
                        data: 'post'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>


@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        toastElList.map(function(toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 30000
            }); // 4 sec
            toast.show();
        })
    });
</script>
