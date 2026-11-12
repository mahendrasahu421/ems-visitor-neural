@extends('layout.master')

@section('title', $title)

@section('main-section')
    <style>
        .page-wrapper .content {
            background: transparent;
        }

        .card {
            border: none;
            overflow: hidden;
        }
        .btn-submit {
            background: linear-gradient(135deg, #ff6b00, #ff8a2b);
            border: none;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #ff8a2b, #ff6b00);
            transform: translateY(-1px);
            color: #fff !important;
        }

        .page-breadcrumb {
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border-color: #ddd;
            box-shadow: none;
            transition: 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ff6b00;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
        }

        label {
            font-weight: 600;
            color: #333;
        }

        .table {
            overflow: hidden;
        }

        .table thead {
            background: #f0f2f5;
            color: #333;
        }

        .table th,
        .table td {
            vertical-align: middle;
            padding: 14px 12px;
        }

        .action-icon a {
            color: #555;
            transition: 0.2s;
        }

        .action-icon a:hover {
            color: #ff6b00;
        }

        .modal-content {
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            color: #fff;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1);
        }

        #delete_modal .modal-body {
            padding: 2rem 2.5rem;
        }

        .avatar.bg-transparent-danger {
            background: rgba(255, 99, 99, 0.1);
        }

        .badge-success {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .badge-danger {
            background-color: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .btn,
        .form-control,
        .form-select {
            transition: all 0.25s ease-in-out;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto">
                    <h2 class="mb-1">{{ $title }}</h2>
                    <p class="text-muted mb-0">Create and manage your job-specific questions.</p>
                </div>
                <div>
                    <a href="{{ route('recruitment.jobs.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Add Job
                    </a>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Form Card -->
            <div class="card shadow-sm border-top border-3 mb-4">
                <div class="card-body">
                    <form id="questionForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Job <span class="text-danger">*</span></label>
                                <select class="form-select" id="job">
                                    <option selected>Select Job</option>
                                    <option>Web Developer</option>
                                    <option>Designer</option>
                                    <option>Project Manager</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Enter Question <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="question"
                                    placeholder="Type your question...">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Choose Form Element <span class="text-danger">*</span></label>
                                <select class="form-select" id="tool">
                                    <option selected>Select Form Element</option>
                                    <option>Text</option>
                                    <option>Textarea</option>
                                    <option>Radio</option>
                                    <option>Checkbox</option>
                                    <option>Select</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Enter Question Order <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="order" placeholder="Enter order number">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Is Field Mandatory? <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_required" value="Yes"
                                            checked>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_required" value="No">
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end">
                                <button type="button" class="btn btn-submit px-5 py-2 shadow">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Table Section -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table align-middle text-center" id="questionTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </th>
                                    <th>S.No.</th>
                                    <th>Question</th>
                                    <th>Tool</th>
                                    <th>Option</th>
                                    <th>Choose Max.</th>
                                    <th>Order</th>
                                    <th>Mandatory</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample Rows -->
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>1</td>
                                    <td><strong>What is your experience with Laravel?</strong></td>
                                    <td>Textarea</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>1</td>
                                    <td><span class="badge badge-success">Yes</span></td>
                                    <td>
                                        <a href="#" class="me-2 edit-btn" data-job="Web Developer"
                                            data-question="What is your experience with Laravel?" data-tool="Textarea"
                                            data-order="1" data-mandatory="Yes"><i class="ti ti-edit"></i></a>
                                        <a href="#" class="delete-btn" data-bs-toggle="modal"
                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td>1</td>
                                    <td><strong>What is your experience with Laravel?</strong></td>
                                    <td>Textarea</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>1</td>
                                    <td><span class="badge badge-success">Yes</span></td>
                                    <td>
                                        <a href="#" class="me-2 edit-btn" data-job="Web Developer"
                                            data-question="What is your experience with Laravel?" data-tool="Textarea"
                                            data-order="1" data-mandatory="Yes"><i class="ti ti-edit"></i></a>
                                        <a href="#" class="delete-btn" data-bs-toggle="modal"
                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <x-footer />
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Job Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label class="form-label">Select Job</label>
                            <select class="form-select" id="editJob">
                                <option>Web Developer</option>
                                <option>Designer</option>
                                <option>Project Manager</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question</label>
                            <textarea class="form-control" id="editQuestion"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Form Element</label>
                            <select class="form-select" id="editTool">
                                <option>Text</option>
                                <option>Textarea</option>
                                <option>Radio</option>
                                <option>Checkbox</option>
                                <option>Select</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" id="editOrder">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mandatory?</label>
                            <select class="form-select" id="editMandatory">
                                <option>Yes</option>
                                <option>No</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-submit">Save Changes</button>
                </div>
            </div>
        </div>
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
                    <p class="text-muted mb-3">Are you sure you want to delete this question? This action cannot be undone.
                    </p>
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
    <script>
        $(document).ready(function() {
            let deleteRow;

            $('.edit-btn').on('click', function(e) {
                e.preventDefault();
                const job = $(this).data('job');
                const question = $(this).data('question');
                const tool = $(this).data('tool');
                const order = $(this).data('order');
                const mandatory = $(this).data('mandatory');

                $('#editJob').val(job);
                $('#editQuestion').val(question);
                $('#editTool').val(tool);
                $('#editOrder').val(order);
                $('#editMandatory').val(mandatory);
                $('#editModal').modal('show');
            });

            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                deleteRow = $(this).closest('tr');
                $('#delete_modal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (deleteRow) {
                    deleteRow.fadeOut(300, function() {
                        $(this).remove();
                    });
                    $('#delete_modal').modal('hide');
                }
            });
        });
    </script>
@endpush
