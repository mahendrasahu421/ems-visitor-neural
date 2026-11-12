<!-- Page Wrapper -->
@extends('layout.master')
@section('title', $title)
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    @if (session('success'))
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

</div>
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
                            {{-- <li class="breadcrumb-item active" aria-current="page">Client Grid</li> --}}
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                            <a href="#"
                                class="btn btn-icon btn-sm me-1"><i class="ti ti-list-tree"></i></a>
                            <a href="#"
                                class="btn btn-icon btn-sm active bg-primary text-white"><i
                                    class="ti ti-layout-grid"></i></a>
                        </div>
                    </div>
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_client"
                            class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                            Client</a>
                    </div>
                    <div class="ms-2 head-icons">
                        <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Collapse" id="collapse-header">
                            <i class="ti ti-chevrons-up"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Clients Info -->
            <div class="row">
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span
                                            class="p-2 br-10 bg-pink-transparent border border-pink d-flex align-items-center justify-content-center">
                                            <i class="ti ti-users-group text-pink fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Total Clients</p>
                                        <h4>{{ $totalClients }}</h4>
                                    </div>
                                </div>
                                <span class="badge bg-transparent-purple d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-arrow-wave-right-down me-1"></i>
                                    {{ '+' . $growthTotalClients . '%' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span
                                            class="p-2 br-10 bg-success-transparent border border-success d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-share fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Active Clients</p>
                                        <h4>{{ $activeClients }}</h4>
                                    </div>
                                </div>
                                <span
                                    class="badge bg-transparent-primary text-primary d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-arrow-wave-right-down me-1"></i>
                                    {{ '+' . $growthActiveClients . '%' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span
                                            class="p-2 br-10 bg-danger-transparent border border-danger d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-pause fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Inactive Clients</p>
                                        <h4>{{ $inactiveClients }}</h4>
                                    </div>
                                </div>
                                <span
                                    class="badge bg-transparent-dark text-dark d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-arrow-wave-right-down me-1"></i>
                                    {{ '+' . $growthInactiveClients . '%' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span
                                            class="p-2 br-10 bg-info-transparent border border-info d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-plus fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">New Clients</p>
                                        <h4>{{ $newClients }}</h4>
                                    </div>
                                </div>
                                <span
                                    class="badge bg-transparent-secondary text-dark d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-arrow-wave-right-down me-1"></i>
                                    {{ '+' . $growthNewClients . '%' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Clients Info -->

            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                        <h5>Client </h5>
                        <div class="d-flex align-items-center flex-wrap row-gap-3">
                            <div class="dropdown me-2">
                                <a href="javascript:void(0);"
                                    class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    Select Status
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Select Status</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Active</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Inactive</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    Sort By : Last 7 Days
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Ascending</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Desending</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Last Month</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Last 7 Days</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients Grid -->
            <div class="row" id="client-list">

                @include('admin.client.partials.client-list', ['clients' => $clients])

                <div class="col-md-12">
                    <div class="text-center mt-3" id="load-message">
                        <span class="text-muted">Scroll down to load more...</span>
                    </div>
                </div>
            </div>
            <!-- /Clients Grid -->

        </div>
        {{-- <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
            <p class="mb-0">2014 - 2025 &copy; EMS.</p>
            <p>Designed &amp; Developed By <a href="javascrSomething went wrong!ipt:void(0);" class="text-primary">Dreams</a></p>
        </div> --}}
    </div>
    <!-- /Page Wrapper -->

    <!-- Add Client -->
    <div class="modal fade" id="add_client">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Client</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="{{ route('admin.clients.create') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="contact-grids-tab">
                        <ul class="nav nav-underline" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                                    data-bs-target="#basic-info" type="button" role="tab"
                                    aria-selected="true">Basic Information</button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
								  <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-selected="false">Permissions</button>
								</li> --}}
                        </ul>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel"
                            aria-labelledby="info-tab" tabindex="0">
                            <div class="modal-body pb-0 ">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div
                                            class="d-flex align-items-center flex-wrap row-gap-3 bg-light w-100 rounded p-3 mb-4">
                                            <div
                                                class="d-flex align-items-center justify-content-center avatar avatar-xxl rounded-circle border border-dashed me-2 flex-shrink-0 text-dark frames">
                                                <i class="ti ti-photo"></i>
                                            </div>
                                            <div class="profile-upload">
                                                <div class="mb-2">
                                                    <h6 class="mb-1">Upload Profile Image</h6>
                                                    <p class="fs-12">Image should be below 4 mb</p>
                                                </div>
                                                <div class="profile-uploader d-flex align-items-center">
                                                    <div class="drag-upload-btn btn btn-sm btn-primary me-2">
                                                        Upload
                                                        <input type="file" class="form-control image-sign"
                                                            name="profile" multiple="">
                                                    </div>
                                                    <a href="javascript:void(0);" class="btn btn-light btn-sm">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">First Name <span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" name="first_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">User Name<span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" name="username">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email<span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" name="email">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 ">
                                            <label class="form-label">Password <span class="text-danger">
                                                    *</span></label>
                                            <div class="pass-group">
                                                <input type="password" class="pass-input form-control" name="password">
                                                <span class="ti toggle-password ti-eye-off"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 ">
                                            <label class="form-label">Confirm Password <span class="text-danger">
                                                    *</span></label>
                                            <div class="pass-group">
                                                <input type="password" class="pass-inputs form-control"
                                                    name="password_confirmation">
                                                <span class="ti toggle-passwords ti-eye-off"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number <span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company</label>
                                            <input type="text" class="form-control" name="company_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light border me-2"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Client -->

    <!-- Edit Client -->
    <div class="modal fade" id="edit_client">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Client</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- /Edit Client -->

    <!-- Add Client Success -->
    <div class="modal fade" id="success_modal" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center p-3">
                        <span class="avatar avatar-lg avatar-rounded bg-success mb-3"><i
                                class="ti ti-check fs-24"></i></span>
                        <h5 class="mb-2">Client Added Successfully</h5>
                        <p class="mb-3">Stephan Peralt has been added with Client ID : <span class="text-primary">#CLT
                                - 0024</span>
                        </p>
                        <div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="#"
                                        class="btn btn-dark w-100">Back to List</a>
                                </div>
                                <div class="col-6">
                                    <a href="#"
                                        class="btn btn-primary w-100">Detail Page</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Client Success -->

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let page = 2; // The first page is already loaded.
    let lastPage = {{ $clients->lastPage() }};
    let loading = false;

    $(window).scroll(function() {
        if (loading) return;

        // Load more records when the user is close to the bottom.
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (page <= lastPage) {
                loadMore(page);
                page++;
            }
        }
    });

    function loadMore(page) {
        loading = true;
        $("#load-message").html('<span class="text-primary">Loading...</span>');

        $.ajax({
            url: "{{ route('admin.clients') }}?page=" + page,
            type: "get",
            success: function(data) {
                if (data.trim().length === 0) {
                    $("#load-message").html('<span class="text-danger">No more records</span>');
                    return;
                }
                $("#client-list").append(data);
                $("#load-message").html('<span class="text-muted">Scroll down to load more...</span>');
                loading = false;
            },
            error: function() {
                $("#load-message").html('<span class="text-danger">Something went wrong!</span>');
                loading = false;
            }
        });
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        toastElList.map(function(toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 4000
            }); // 4 sec
            toast.show();
        })
    });
</script>
