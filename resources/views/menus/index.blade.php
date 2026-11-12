<!-- Page Wrapper -->
@extends('layout.master')
@section('title', $title)

@section('main-section')
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Add / Edit Menu</h5>
                            <p class="card-text">
                                Use this form to add or edit a menu. All fields marked with * are required.
                            </p>
                        </div>
                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST" action="{{ route('admin.menus.store') }}">
                                @csrf
                                <div class="form-row row">
                                    <!-- Title -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="title">Menu Title *</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Menu Title" required>
                                        <div class="invalid-feedback">
                                            Please enter a menu title.
                                        </div>
                                    </div>

                                    <!-- Icon -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="icon">Icon</label>
                                        <input type="text" class="form-control" id="icon" name="icon"
                                            placeholder="e.g., ti ti-users">
                                        <div class="invalid-feedback">
                                            Please enter a valid icon class.
                                        </div>
                                    </div>

                                    <!-- Route -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="route">Route / URL</label>
                                        <input type="text" class="form-control" id="route" name="route"
                                            placeholder="Route or URL">
                                        <div class="invalid-feedback">
                                            Please provide a valid route.
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row row">
                                    <!-- Parent Menu -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="parent_id">Parent Menu</label>
                                        <select class="form-control" id="parent_id" name="parent_id">
                                            <option value="">-- None (Top-Level) --</option>
                                            @foreach ($menus as $menu)
                                                        <option value="{{ $menu->id }}">{{ $menu->title }}</option>
                                                    @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid parent menu.
                                        </div>
                                    </div>

                                    <!-- Permission -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="permission_id">Permission</label>
                                        <select class="form-control" id="permission_id" name="permission_id">
                                            <option value="">-- None --</option>
                                            @foreach ($permissions as $permission)
                                                        <option value="{{ $permission->id }}">{{ $permission->name }}
                                                        </option>
                                                    @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid permission.
                                        </div>
                                    </div>

                                    <!-- Order -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="order">Order *</label>
                                        <input type="number" class="form-control" id="order" name="order"
                                            placeholder="Display order" value="1" required>
                                        <div class="invalid-feedback">
                                            Please enter display order.
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="form-row row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="status">Status *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select menu status.
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary" type="submit">Save Menu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <x-footer />

    </div>
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
    </div>
    <!-- /Delete Modal -->
@endsection

@section('script')
    @parent


    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();

            // Initialize DataTable
            new DataTable('#example');
        });
    </script>

@endsection
