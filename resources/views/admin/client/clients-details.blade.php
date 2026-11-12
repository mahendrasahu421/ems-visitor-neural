@extends('layout.master')
@section('title', $title)
@section('main-section')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row justify-content-between align-items-center mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-medium d-inline-flex align-items-center mb-3 mb-sm-0"><a href="{{route('admin.clients')}}">
                                <i class="ti ti-arrow-left me-2"></i>Clients</a>
                        </h6>
                        <div class="ms-2 head-icons">
                            <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-original-title="Collapse" id="collapse-header">
                                <i class="ti ti-chevrons-up"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-xl-4 theiaStickySidebar">
                    <div class="card card-bg-1">
                        <div class="card-body p-0">
                            <span class="avatar avatar-xl avatar-rounded border border-2 border-white m-auto d-flex mb-2">
                                <img src="assets/img/users/user-13.jpg" class="w-auto h-auto" alt="Img">
                            </span>
                            <div class="text-center px-3 pb-3 border-bottom">
                                <div class="mb-3">
                                    <h5 class="d-flex align-items-center justify-content-center mb-1">{{ $client->first_name }} {{ $client->last_name }}<i
                                            class="ti ti-discount-check-filled text-success ms-1"></i></h5>
                                    <p class="text-dark mb-1">{{ $client->company_name }}</p>
                                    <span class="badge badge-soft-secondary fw-medium">Operational Manager</span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="d-inline-flex align-items-center">
                                            <i class="ti ti-id me-2"></i>
                                            Client ID
                                        </span>
                                        <p class="text-dark">{{ $client->id }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="d-inline-flex align-items-center">
                                            <i class="ti ti-calendar-check me-2"></i>
                                            Added on
                                        </span>
                                        <p class="text-dark">{{ $client->created_at }}</p>
                                    </div>
                                    {{-- <div class="row gx-2 mt-3">
                                        <div class="col-6">
                                            <div>
                                                <a href="#" class="btn btn-dark w-100"><i
                                                        class="ti ti-phone-call me-1"></i>Call</a>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div>
                                                <a href="#" class="btn btn-primary w-100"><i
                                                        class="ti ti-message-heart me-1"></i>Message</a>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6>Basic information</h6>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#edit_client"><i class="ti ti-edit"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-phone me-2"></i>
                                        Phone
                                    </span>
                                    <p class="text-dark">{{ $client->phone }}</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-mail-check me-2"></i>
                                        Email
                                    </span>
                                    <a href="javascript:void(0);" class="text-info d-inline-flex align-items-center"><span
                                            class="__cf_email__"
                                            data-cfemail="e595809797848991d4d7a5809d8488958980cb868a88">{{ $client->email }}</span><i
                                            class="ti ti-copy text-dark ms-2"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ti ti-map-pin-check me-2"></i>
                                        Address
                                    </span>
                                    <p class="text-dark text-end">1861 Bayonne Ave, <br> Manchester, NJ, 08759</p>
                                </div>
                            </div>
                            {{-- <div class="p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6>Social Links</h6>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm"><i class="ti ti-edit"></i></a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="me-2"><img src="assets/img/social/social-01.svg"
                                            alt="Img"></a>
                                    <a href="javascript:void(0);" class="me-2"><img src="assets/img/social/social-06.svg"
                                            alt="Img"></a>
                                    <a href="javascript:void(0);" class="me-2"><img src="assets/img/social/social-02.svg"
                                            alt="Img"></a>
                                    <a href="javascript:void(0);" class="me-2"><img src="assets/img/social/social-03.svg"
                                            alt="Img"></a>
                                    <a href="javascript:void(0);" class="me-2"><img src="assets/img/social/social-04.svg"
                                            alt="Img"></a>
                                    <a href="javascript:void(0);" class="me-2"><img src="assets/img/social/social-05.svg"
                                            alt="Img"></a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div>
                        <div class="bg-white rounded">
                            <ul class="nav nav-tabs nav-tabs-bottom nav-justified flex-wrap mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active fw-medium d-flex align-items-center justify-content-center"
                                        href="#bottom-justified-tab1" data-bs-toggle="tab" aria-selected="false"
                                        role="tab">
                                        <i class="ti ti-star me-1"></i>
                                        Overview
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium d-flex align-items-center justify-content-center"
                                        href="#bottom-justified-tab2" data-bs-toggle="tab" aria-selected="false"
                                        role="tab">
                                        <i class="ti ti-box me-1"></i>
                                        Projects
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium d-flex align-items-center justify-content-center"
                                        href="#bottom-justified-tab3" data-bs-toggle="tab" aria-selected="true"
                                        role="tab">
                                        <i class="ti ti-basket-code me-1"></i>
                                        Tasks
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium d-flex align-items-center justify-content-center"
                                        href="#bottom-justified-tab4" data-bs-toggle="tab" aria-selected="true"
                                        role="tab">
                                        <i class="ti ti-file-invoice me-1"></i>
                                        Invoices
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium d-flex align-items-center justify-content-center"
                                        href="#bottom-justified-tab5" data-bs-toggle="tab" aria-selected="true"
                                        role="tab">
                                        <i class="ti ti-file-description me-1"></i>
                                        Notes
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium d-flex align-items-center justify-content-center"
                                        href="#bottom-justified-tab6" data-bs-toggle="tab" aria-selected="true"
                                        role="tab">
                                        <i class="ti ti-folder-open me-1"></i>
                                        Documents
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content custom-accordion-items client-accordion">
                            <div class="tab-pane active show" id="bottom-justified-tab1" role="tabpanel">
                                <div class="accordion accordions-items-seperate" id="accordionExample">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingOne">
                                            <div class="accordion-button bg-white" data-bs-toggle="collapse"
                                                data-bs-target="#primaryBorderOne" aria-expanded="true"
                                                aria-controls="primaryBorderOne" role="button">
                                                <h5>Projects</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderOne" class="accordion-collapse collapse show border-top"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body pb-0">
                                                <div class="row">
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center pb-3 mb-3 border-bottom">
                                                                    <a href="#"
                                                                        class="flex-shrink-0 me-2">
                                                                        <img src="assets/img/social/project-01.svg"
                                                                            alt="Img">
                                                                    </a>
                                                                    <div>
                                                                        <h6 class="mb-1"><a
                                                                                href="#">Hospital
                                                                                Administration</a></h6>
                                                                        <div class="d-flex align-items-center">
                                                                            <span>8 tasks</span>
                                                                            <span class="mx-1"><i
                                                                                    class="ti ti-point-filled text-primary"></i></span>
                                                                            <span>15 Completed</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Deadline</span>
                                                                            <p class="text-dark">31 July 2025</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Value</span>
                                                                            <p class="text-dark">$549987</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Project Lead</span>
                                                                            <h6
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-xs rounded-circle me-1"
                                                                                    src="assets/img/profiles/avatar-01.jpg"
                                                                                    alt="Img">
                                                                                Leona
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-light p-2">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-6">
                                                                            <span
                                                                                class="fw-medium d-flex align-items-center">
                                                                                <i
                                                                                    class="ti ti-clock text-primary me-2"></i>Total
                                                                                565 Hrs
                                                                            </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div>
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-between mb-1">
                                                                                    <small class="text-dark">495
                                                                                        Hrs</small>
                                                                                    <small class="text-dark">70 Hrs</small>
                                                                                </div>
                                                                                <div class="progress  progress-xs">
                                                                                    <div class="progress-bar bg-warning"
                                                                                        role="progressbar"
                                                                                        style="width: 75%"></div>
                                                                                    <div class="progress-bar bg-success"
                                                                                        role="progressbar"
                                                                                        style="width: 25%"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center pb-3 mb-3 border-bottom">
                                                                    <a href="#"
                                                                        class="flex-shrink-0 me-2">
                                                                        <img src="assets/img/social/project-02.svg"
                                                                            alt="Img">
                                                                    </a>
                                                                    <div>
                                                                        <h6 class="mb-1"><a
                                                                                href="#">Video Calling
                                                                                App</a></h6>
                                                                        <div class="d-flex align-items-center">
                                                                            <span>22 tasks</span>
                                                                            <span class="mx-1"><i
                                                                                    class="ti ti-point-filled text-primary"></i></span>
                                                                            <span>15 Completed</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Deadline</span>
                                                                            <p class="text-dark">16 Jan 2025</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Value</span>
                                                                            <p class="text-dark">$279987</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Project Lead</span>
                                                                            <h6
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-xs rounded-circle me-1"
                                                                                    src="assets/img/profiles/avatar-02.jpg"
                                                                                    alt="Img">
                                                                                Mathis
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-light p-2">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-6">
                                                                            <span
                                                                                class="fw-medium d-flex align-items-center">
                                                                                <i
                                                                                    class="ti ti-clock text-primary me-2"></i>Total
                                                                                700 Hrs
                                                                            </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div>
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-between mb-1">
                                                                                    <small class="text-dark">605
                                                                                        Hrs</small>
                                                                                    <small class="text-dark">95 Hrs</small>
                                                                                </div>
                                                                                <div class="progress  progress-xs">
                                                                                    <div class="progress-bar bg-warning"
                                                                                        role="progressbar"
                                                                                        style="width: 75%"></div>
                                                                                    <div class="progress-bar bg-success"
                                                                                        role="progressbar"
                                                                                        style="width: 25%"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingTwo">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                data-bs-target="#primaryBorderTwo" aria-expanded="false"
                                                aria-controls="primaryBorderTwo" role="button">
                                                <h5>Tasks</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderTwo" class="accordion-collapse collapse border-top"
                                            aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 d-flex align-items-center rating-select"><i
                                                                            class="ti ti-star-filled filled"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Patient appointment booking</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge bg-soft-pink d-inline-flex align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Onhold</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-13.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-14.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-15.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 rating-select d-flex align-items-center"><i
                                                                            class="ti ti-star"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Appointment booking with payment
                                                                            gateway</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge bg-transparent-purple d-flex align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Inprogress</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-20.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-21.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-22.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 rating-select d-flex align-items-center"><i
                                                                            class="ti ti-star"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Patient and Doctor video
                                                                            conferencing</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge badge-soft-success align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Completed</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-28.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-29.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-24.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3 todo-strike-content">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            checked="">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 rating-select d-flex align-items-center"><i
                                                                            class="ti ti-star"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Private chat module</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge badge-secondary-transparent d-flex align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Pending</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-23.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-24.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-25.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingThree">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderThree" aria-expanded="false"
                                                aria-controls="primaryBorderThree">
                                                <h5>Invoices</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderThree" class="accordion-collapse collapse border-top"
                                            aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row align-items-center g-3 mb-3">
                                                    <div class="col-sm-8">
                                                        <h6>Total No of Invoice : 45</h6>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="position-relative input-icon">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control"
                                                                placeholder="Search">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="list-group list-group-flush mb-3">
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Phase 2 Completion</h6>
                                                                        <p><a href="#" class="text-info">#INV-123
                                                                            </a> 11 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$6,598</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Paid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Advance for Project</h6>
                                                                        <p><a href="#" class="text-info">#INV-124
                                                                            </a> 14 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$3312</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Hold</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Changes & design
                                                                            Alignments</h6>
                                                                        <p><a href="#" class="text-info">#INV-125
                                                                            </a> 15 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$4154</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Paid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Added New Functionality
                                                                        </h6>
                                                                        <p><a href="#" class="text-info">#INV-126
                                                                            </a> 16 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$658</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Paid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Phase 1 Completion</h6>
                                                                        <p><a href="#" class="text-info">#INV-127
                                                                            </a> 17 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$1259</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-danger d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>unpaid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <a href="#" class="btn btn-primary btn-sm">Load More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingFour">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderFour" aria-expanded="false"
                                                aria-controls="primaryBorderFour">
                                                <h5>Notes</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderFour" class="accordion-collapse collapse border-top"
                                            aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row align-items-center g-3 mb-3">
                                                    <div class="col-sm-8">
                                                        <h6>Total No of Notes : 45</h6>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="position-relative input-icon">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control"
                                                                placeholder="Search">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-6 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-2">
                                                                    <h6 class="text-gray-5 fw-medium">15 May 2025</h6>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);"
                                                                            class="d-inline-flex align-items-center"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-edit me-2"></i>Edit</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-trash me-1"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <h6 class="d-flex align-items-center mb-2"><i
                                                                        class="ti ti-point-filled text-primary me-1"></i>Changes
                                                                    & design</h6>
                                                                <p class="text-truncate line-clamb-3">An office management
                                                                    app project streamlines administrative tasks by
                                                                    integrating
                                                                    tools for scheduling, communication, and
                                                                    task management, enhancing overall productivity and
                                                                    efficiency.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-2">
                                                                    <h6 class="text-gray-5 fw-medium">16 May 2025</h6>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);"
                                                                            class="d-inline-flex align-items-center"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-edit me-2"></i>Edit</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-trash me-1"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <h6 class="d-flex align-items-center mb-2"><i
                                                                        class="ti ti-point-filled text-success me-1"></i>Phase
                                                                    1 Completion</h6>
                                                                <p class="text-truncate line-clamb-3">
                                                                    An office management app project streamlines
                                                                    administrative tasks by integrating tools for
                                                                    scheduling, communication, and task
                                                                    management, enhancing overall productivity and
                                                                    efficiency.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-2">
                                                                    <h6 class="text-gray-5 fw-medium">17 May 2025</h6>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);"
                                                                            class="d-inline-flex align-items-center"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-edit me-2"></i>Edit</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-trash me-1"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <h6 class="d-flex align-items-center mb-2"><i
                                                                        class="ti ti-point-filled text-danger me-1"></i>Phase
                                                                    2 Completion</h6>
                                                                <p class="text-truncate line-clamb-3">
                                                                    An office management app project streamlines
                                                                    administrative tasks by integrating tools for
                                                                    scheduling, communication, and task
                                                                    management, enhancing overall productivity and
                                                                    efficiency.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="text-center">
                                                            <a href="#" class="btn btn-primary btn-sm">Load More</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingFive">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderFive" aria-expanded="false"
                                                aria-controls="primaryBorderFive">
                                                <h5>Documents</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderFive" class="accordion-collapse collapse border-top"
                                            aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row align-items-center g-3 mb-3">
                                                    <div class="col-sm-4">
                                                        <h6>Total No of Documents : 45</h6>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="d-flex align-items-center">
                                                            <div class="dropdown me-2">
                                                                <a href="javascript:void(0);"
                                                                    class="dropdown-toggle btn btn-white"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Sort By : Docs Type
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Docs</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Pdf</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Image</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Folder</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Xml</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="position-relative input-icon flex-fill">
                                                                <span class="input-icon-addon">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Search">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="custom-datatable-filter table-responsive no-datatable-length border">
                                                    <table class="table datatable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Size</th>
                                                                <th>Type</th>
                                                                <th>Modified</th>
                                                                <th>Share</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-01.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Secret</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>7.6 MB</td>
                                                                <td>Doc</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Mar 15, 2025</p>
                                                                    <span>05:00:14 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-27.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-29.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-12.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-02.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Sophie
                                                                                    Headrick</a></p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>7.4 MB</td>
                                                                <td>PDF</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Jan 8, 2025</p>
                                                                    <span>08:20:13 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-15.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-16.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-03.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Gallery</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>6.1 MB</td>
                                                                <td>Image</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Aug 6, 2025</p>
                                                                    <span>04:10:12 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-02.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-03.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-05.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-06.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <a class="avatar bg-primary avatar-rounded text-fixed-white"
                                                                            href="javascript:void(0);">
                                                                            +1
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-04.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Doris
                                                                                    Crowley</a></p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>5.2 MB</td>
                                                                <td>Folder</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Jan 6, 2025</p>
                                                                    <span>03:40:14 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-06.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-10.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-15.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-05.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Cheat_codez</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>8 MB</td>
                                                                <td>Xml</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Oct 12, 2025</p>
                                                                    <span>05:00:14 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-04.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-28.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-14.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-15.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bottom-justified-tab2" role="tabpanel">
                                <div class="accordion accordions-items-seperate">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingOne2">
                                            <div class="accordion-button bg-white" data-bs-toggle="collapse"
                                                data-bs-target="#primaryBorderOne2" aria-expanded="true"
                                                aria-controls="primaryBorderOne2" role="button">
                                                <h5>Projects</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderOne2" class="accordion-collapse collapse show border-top"
                                            aria-labelledby="headingOne2">
                                            <div class="accordion-body pb-0">
                                                <div class="row">
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center pb-3 mb-3 border-bottom">
                                                                    <a href="#"
                                                                        class="flex-shrink-0 me-2">
                                                                        <img src="assets/img/social/project-01.svg"
                                                                            alt="Img">
                                                                    </a>
                                                                    <div>
                                                                        <h6 class="mb-1"><a
                                                                                href="#">Hospital
                                                                                Administration</a></h6>
                                                                        <div class="d-flex align-items-center">
                                                                            <span>8 tasks</span>
                                                                            <span class="mx-1"><i
                                                                                    class="ti ti-point-filled text-primary"></i></span>
                                                                            <span>15 Completed</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Deadline</span>
                                                                            <p class="text-dark">31 July 2025</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Value</span>
                                                                            <p class="text-dark">$549987</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Project Lead</span>
                                                                            <h6
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-xs rounded-circle me-1"
                                                                                    src="assets/img/profiles/avatar-01.jpg"
                                                                                    alt="Img">
                                                                                Leona
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-light p-2">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-6">
                                                                            <span
                                                                                class="fw-medium d-flex align-items-center">
                                                                                <i
                                                                                    class="ti ti-clock text-primary me-2"></i>Total
                                                                                565 Hrs
                                                                            </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div>
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-between mb-1">
                                                                                    <small class="text-dark">495
                                                                                        Hrs</small>
                                                                                    <small class="text-dark">70
                                                                                        Hrs</small>
                                                                                </div>
                                                                                <div class="progress  progress-xs">
                                                                                    <div class="progress-bar bg-warning"
                                                                                        role="progressbar"
                                                                                        style="width: 75%"></div>
                                                                                    <div class="progress-bar bg-success"
                                                                                        role="progressbar"
                                                                                        style="width: 25%"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center pb-3 mb-3 border-bottom">
                                                                    <a href="#"
                                                                        class="flex-shrink-0 me-2">
                                                                        <img src="assets/img/social/project-02.svg"
                                                                            alt="Img">
                                                                    </a>
                                                                    <div>
                                                                        <h6 class="mb-1"><a
                                                                                href="#">Video Calling
                                                                                App</a></h6>
                                                                        <div class="d-flex align-items-center">
                                                                            <span>22 tasks</span>
                                                                            <span class="mx-1"><i
                                                                                    class="ti ti-point-filled text-primary"></i></span>
                                                                            <span>15 Completed</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Deadline</span>
                                                                            <p class="text-dark">16 Jan 2025</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Value</span>
                                                                            <p class="text-dark">$279987</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="mb-3">
                                                                            <span class="mb-1 d-block">Project Lead</span>
                                                                            <h6
                                                                                class="fw-normal d-flex align-items-center">
                                                                                <img class="avatar avatar-xs rounded-circle me-1"
                                                                                    src="assets/img/profiles/avatar-02.jpg"
                                                                                    alt="Img">
                                                                                Mathis
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-light p-2">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-6">
                                                                            <span
                                                                                class="fw-medium d-flex align-items-center">
                                                                                <i
                                                                                    class="ti ti-clock text-primary me-2"></i>Total
                                                                                700 Hrs
                                                                            </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div>
                                                                                <div
                                                                                    class="d-flex align-items-center justify-content-between mb-1">
                                                                                    <small class="text-dark">605
                                                                                        Hrs</small>
                                                                                    <small class="text-dark">95
                                                                                        Hrs</small>
                                                                                </div>
                                                                                <div class="progress  progress-xs">
                                                                                    <div class="progress-bar bg-warning"
                                                                                        role="progressbar"
                                                                                        style="width: 75%"></div>
                                                                                    <div class="progress-bar bg-success"
                                                                                        role="progressbar"
                                                                                        style="width: 25%"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bottom-justified-tab3" role="tabpanel">
                                <div class="accordion accordions-items-seperate">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingTwo2">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderTwo2"
                                                aria-expanded="true" aria-controls="primaryBorderTwo2">
                                                <h5>Tasks</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderTwo2" class="accordion-collapse collapse show border-top"
                                            aria-labelledby="headingTwo2">
                                            <div class="accordion-body">
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 d-flex align-items-center rating-select"><i
                                                                            class="ti ti-star-filled filled"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Patient appointment booking
                                                                        </h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge bg-soft-pink d-inline-flex align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Onhold</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-13.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-14.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-15.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 rating-select d-flex align-items-center"><i
                                                                            class="ti ti-star"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Appointment booking with
                                                                            payment gateway</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge bg-transparent-purple d-flex align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Inprogress</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-20.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-21.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-22.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 rating-select d-flex align-items-center"><i
                                                                            class="ti ti-star"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Patient and Doctor video
                                                                            conferencing</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge badge-soft-success align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Completed</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-28.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-29.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-24.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded p-2">
                                                        <div class="row align-items-center row-gap-3">
                                                            <div class="col-md-7">
                                                                <div
                                                                    class="todo-inbox-check d-flex align-items-center flex-wrap row-gap-3 todo-strike-content">
                                                                    <div class="form-check form-check-md me-2">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            checked="">
                                                                    </div>
                                                                    <span
                                                                        class="me-2 rating-select d-flex align-items-center"><i
                                                                            class="ti ti-star"></i></span>
                                                                    <div class="strike-info">
                                                                        <h4 class="fs-14">Private chat module</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-md-end flex-wrap row-gap-3">
                                                                    <span
                                                                        class="badge badge-secondary-transparent d-flex align-items-center me-3"><i
                                                                            class="fas fa-circle fs-6 me-1"></i>Pending</span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-list-stacked avatar-group-sm">
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-23.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-24.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                            <span class="avatar avatar-rounded">
                                                                                <img class="border border-white"
                                                                                    src="assets/img/profiles/avatar-25.jpg"
                                                                                    alt="img">
                                                                            </span>
                                                                        </div>
                                                                        <div class="dropdown ms-2">
                                                                            <a href="javascript:void(0);"
                                                                                class="d-inline-flex align-items-center"
                                                                                data-bs-toggle="dropdown">
                                                                                <i class="ti ti-dots-vertical"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-end p-3">
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#edit_todo"><i
                                                                                            class="ti ti-edit me-2"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#delete_modal"><i
                                                                                            class="ti ti-trash me-2"></i>Delete</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="dropdown-item rounded-1"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#view_todo"><i
                                                                                            class="ti ti-eye me-2"></i>View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bottom-justified-tab4" role="tabpanel">
                                <div class="accordion accordions-items-seperate">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingThree2">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderThree2"
                                                aria-expanded="true" aria-controls="primaryBorderThree2">
                                                <h5>Invoices</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderThree2"
                                            class="accordion-collapse collapse show border-top"
                                            aria-labelledby="headingThree2">
                                            <div class="accordion-body">
                                                <div class="row align-items-center g-3 mb-3">
                                                    <div class="col-sm-8">
                                                        <h6>Total No of Invoice : 45</h6>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="position-relative input-icon">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control"
                                                                placeholder="Search">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="list-group list-group-flush mb-3">
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Phase 2 Completion</h6>
                                                                        <p><a href="#" class="text-info">#INV-123
                                                                            </a> 11 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$6,598</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Paid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Advance for Project
                                                                        </h6>
                                                                        <p><a href="#" class="text-info">#INV-124
                                                                            </a> 14 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$3312</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Hold</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Changes & design
                                                                            Alignments</h6>
                                                                        <p><a href="#" class="text-info">#INV-125
                                                                            </a> 15 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$4154</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Paid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded mb-2 p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Added New Functionality
                                                                        </h6>
                                                                        <p><a href="#" class="text-info">#INV-126
                                                                            </a> 16 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$658</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-success d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>Paid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item border rounded p-2">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-sm-6">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-lg bg-light flex-shrink-0 me-2"><i
                                                                            class="ti ti-file-invoice text-dark fs-24"></i></span>
                                                                    <div>
                                                                        <h6 class="fw-medium mb-1">Phase 1 Completion</h6>
                                                                        <p><a href="#" class="text-info">#INV-127
                                                                            </a> 17 Sep 2025, 05:35 pm</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div>
                                                                    <span>Amount</span>
                                                                    <p class="text-dark">$1259</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-sm-end">
                                                                    <span
                                                                        class="badge badge-soft-danger d-inline-flex  align-items-center me-4"><i
                                                                            class="ti ti-point-filled me-1"></i>unpaid</span>
                                                                    <a href="#" class="btn btn-icon btn-sm"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#" class="btn btn-icon btn-sm "><i
                                                                            class="ti ti-trash"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <a href="#" class="btn btn-primary btn-sm">Load More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bottom-justified-tab5" role="tabpanel">
                                <div class="accordion accordions-items-seperate">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingFour2">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderFour2"
                                                aria-expanded="true" aria-controls="primaryBorderFour2">
                                                <h5>Notes</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderFour2" class="accordion-collapse collapse show border-top"
                                            aria-labelledby="headingFour2">
                                            <div class="accordion-body">
                                                <div class="row align-items-center g-3 mb-3">
                                                    <div class="col-sm-8">
                                                        <h6>Total No of Notes : 45</h6>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="position-relative input-icon">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control"
                                                                placeholder="Search">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-6 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-2">
                                                                    <h6 class="text-gray-5 fw-medium">15 May 2025</h6>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);"
                                                                            class="d-inline-flex align-items-center"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-edit me-2"></i>Edit</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-trash me-1"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <h6 class="d-flex align-items-center mb-2"><i
                                                                        class="ti ti-point-filled text-primary me-1"></i>Changes
                                                                    & design</h6>
                                                                <p class="text-truncate line-clamb-3">An office management
                                                                    app project streamlines administrative tasks by
                                                                    integrating
                                                                    tools for scheduling, communication, and
                                                                    task management, enhancing overall productivity and
                                                                    efficiency.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-2">
                                                                    <h6 class="text-gray-5 fw-medium">16 May 2025</h6>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);"
                                                                            class="d-inline-flex align-items-center"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-edit me-2"></i>Edit</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-trash me-1"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <h6 class="d-flex align-items-center mb-2"><i
                                                                        class="ti ti-point-filled text-success me-1"></i>Phase
                                                                    1 Completion</h6>
                                                                <p class="text-truncate line-clamb-3">
                                                                    An office management app project streamlines
                                                                    administrative tasks by integrating tools for
                                                                    scheduling, communication, and task
                                                                    management, enhancing overall productivity and
                                                                    efficiency.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6 d-flex">
                                                        <div class="card flex-fill">
                                                            <div class="card-body">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-2">
                                                                    <h6 class="text-gray-5 fw-medium">17 May 2025</h6>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);"
                                                                            class="d-inline-flex align-items-center"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-edit me-2"></i>Edit</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="dropdown-item rounded-1"><i
                                                                                        class="ti ti-trash me-1"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <h6 class="d-flex align-items-center mb-2"><i
                                                                        class="ti ti-point-filled text-danger me-1"></i>Phase
                                                                    2 Completion</h6>
                                                                <p class="text-truncate line-clamb-3">
                                                                    An office management app project streamlines
                                                                    administrative tasks by integrating tools for
                                                                    scheduling, communication, and task
                                                                    management, enhancing overall productivity and
                                                                    efficiency.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="text-center">
                                                            <a href="#" class="btn btn-primary btn-sm">Load
                                                                More</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="bottom-justified-tab6" role="tabpanel">
                                <div class="accordion accordions-items-seperate">
                                    <div class="accordion-item">
                                        <div class="accordion-header" id="headingFive2">
                                            <div class="accordion-button collapsed" data-bs-toggle="collapse"
                                                role="button" data-bs-target="#primaryBorderFive2"
                                                aria-expanded="true" aria-controls="primaryBorderFive2">
                                                <h5>Documents</h5>
                                            </div>
                                        </div>
                                        <div id="primaryBorderFive2" class="accordion-collapse collapse show border-top"
                                            aria-labelledby="headingFive2">
                                            <div class="accordion-body">
                                                <div class="row align-items-center g-3 mb-3">
                                                    <div class="col-sm-4">
                                                        <h6>Total No of Documents : 45</h6>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="d-flex align-items-center">
                                                            <div class="dropdown me-2">
                                                                <a href="javascript:void(0);"
                                                                    class="dropdown-toggle btn btn-white"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Sort By : Docs Type
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Docs</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Pdf</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Image</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Folder</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                            class="dropdown-item rounded-1">Xml</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="position-relative input-icon flex-fill">
                                                                <span class="input-icon-addon">
                                                                    <i class="ti ti-search"></i>
                                                                </span>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Search">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="custom-datatable-filter table-responsive no-datatable-length border">
                                                    <table class="table datatable">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Size</th>
                                                                <th>Type</th>
                                                                <th>Modified</th>
                                                                <th>Share</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-01.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Secret</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>7.6 MB</td>
                                                                <td>Doc</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Mar 15, 2025</p>
                                                                    <span>05:00:14 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-27.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-29.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-12.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-02.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Sophie
                                                                                    Headrick</a></p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>7.4 MB</td>
                                                                <td>PDF</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Jan 8, 2025</p>
                                                                    <span>08:20:13 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-15.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-16.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-03.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Gallery</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>6.1 MB</td>
                                                                <td>Image</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Aug 6, 2025</p>
                                                                    <span>04:10:12 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-02.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-03.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-05.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-06.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <a class="avatar bg-primary avatar-rounded text-fixed-white"
                                                                            href="javascript:void(0);">
                                                                            +1
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-04.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Doris
                                                                                    Crowley</a></p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>5.2 MB</td>
                                                                <td>Folder</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Jan 6, 2025</p>
                                                                    <span>03:40:14 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-06.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-10.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-15.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center file-name-icon">
                                                                        <a href="#"
                                                                            class="avatar avatar-md bg-light"
                                                                            data-bs-toggle="offcanvas"
                                                                            data-bs-target="#preview">
                                                                            <img src="assets/img/icons/file-05.svg"
                                                                                class="img-fluid" alt="img"></a>
                                                                        <div class="ms-2">
                                                                            <p class="text-title fw-medium  mb-0"><a
                                                                                    href="#"
                                                                                    data-bs-toggle="offcanvas"
                                                                                    data-bs-target="#preview">Cheat_codez</a>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>8 MB</td>
                                                                <td>Xml</td>
                                                                <td>
                                                                    <p class="text-title mb-0">Oct 12, 2025</p>
                                                                    <span>05:00:14 PM</span>
                                                                </td>
                                                                <td>
                                                                    <div class="avatar-list-stacked avatar-group-sm">
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-04.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-28.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-14.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                        <span class="avatar avatar-rounded">
                                                                            <img class="border border-white"
                                                                                src="assets/img/profiles/avatar-15.jpg"
                                                                                alt="img">
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rating-select me-2">
                                                                            <a href="javascript:void(0);"><i
                                                                                    class="ti ti-star"></i></a>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <a href="#"
                                                                                class="d-flex align-items-center justify-content-center"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-expanded="false">
                                                                                <i class="ti ti-dots fs-14"></i>
                                                                            </a>
                                                                            <ul
                                                                                class="dropdown-menu dropdown-menu-right p-3">
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-trash me-2"></i>Permanent
                                                                                        Delete
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item rounded-1"
                                                                                        href="#">
                                                                                        <i
                                                                                            class="ti ti-edit-circle me-2"></i>Restore
                                                                                        File
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mb-4">
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="d-inline-flex align-items-center avatar avatar-lg avatar-rounded bg-primary"
                                    data-bs-toggle="dropdown">
                                    <i class="ti ti-plus fs-24 text-white"></i>
                                </a>
                                <ul
                                    class="dropdown-menu dropdown-menu-end bg-gray-900 dropdown-menu-md dropdown-menu-dark p-3">
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="dropdown-item rounded-1 d-flex align-items-center">
                                            <span class="avatar avatar-md bg-gray-800 flex-shrink-0 me-2"><i
                                                    class="ti ti-basket-code"></i></span>
                                            <div>
                                                <h6 class="fw-medium text-white mb-1">Add a Task</h6>
                                                <p class="text-white">Create a new Priority tasks </p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="dropdown-item rounded-1 d-flex align-items-center">
                                            <span class="avatar avatar-md bg-gray-800 flex-shrink-0 me-2"><i
                                                    class="ti ti-file-invoice"></i></span>
                                            <div>
                                                <h6 class="fw-medium text-white mb-1">Add Invoice</h6>
                                                <p class="text-white">Create a new Billing</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="dropdown-item rounded-1 d-flex align-items-center">
                                            <span class="avatar avatar-md bg-gray-800 flex-shrink-0 me-2"><i
                                                    class="ti ti-file-description"></i></span>
                                            <div>
                                                <h6 class="fw-medium text-white mb-1">Notes</h6>
                                                <p class="text-white">Create new note for you & team</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="dropdown-item rounded-1 d-flex align-items-center">
                                            <span class="avatar avatar-md bg-gray-800 flex-shrink-0 me-2"><i
                                                    class="ti ti-folder-open"></i></span>
                                            <div>
                                                <h6 class="fw-medium text-white mb-1">Add Files</h6>
                                                <p class="text-white">Upload New files for this Client</p>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
            <p class="mb-0">2014 - 2025 &copy; EMS.</p>
            <p>Designed &amp; Developed By <a href="#" class="text-primary">Dreams</a></p>
        </div>
    </div>
    <!-- /Page Wrapper -->


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
                <form action="#">
                    <div class="contact-grids-tab">
                        <ul class="nav nav-underline" id="myTab2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab2" data-bs-toggle="tab"
                                    data-bs-target="#basic-info2" type="button" role="tab"
                                    aria-selected="true">Basic Information</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="address-tab2" data-bs-toggle="tab"
                                    data-bs-target="#address2" type="button" role="tab"
                                    aria-selected="false">Permissions</button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" id="basic-info2" role="tabpanel"
                            aria-labelledby="info-tab2" tabindex="0">
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
                                                            multiple="">
                                                    </div>
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-light btn-sm">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">First Name <span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" value="Michael">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="email" class="form-control" value="Walker">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Username <span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" value="Michael Walker">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email<span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" value="michael@example.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 ">
                                            <label class="form-label">Password <span class="text-danger">
                                                    *</span></label>
                                            <div class="pass-group">
                                                <input type="password" class="pass-input form-control" value="1234">
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
                                                    value="1234">
                                                <span class="ti toggle-passwords ti-eye-off"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number <span class="text-danger">
                                                    *</span></label>
                                            <input type="text" class="form-control" value="(163) 2459 315">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company</label>
                                            <input type="text" class="form-control" value="BrightWave Innovations">
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
                        <div class="tab-pane fade" id="address2" role="tabpanel" aria-labelledby="address-tab2"
                            tabindex="0">
                            <div class="modal-body pb-0 ">
                                <div class="card bg-light-500 shadow-none">
                                    <div
                                        class="card-body d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                        <h6>Enable Options</h6>
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="form-check form-check-md form-switch me-2">
                                                <label class="form-check-label mt-0">
                                                    <input class="form-check-input me-2" type="checkbox"
                                                        role="switch">
                                                    Enable all Module
                                                </label>
                                            </div>
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <label class="form-check-label mt-0">
                                                    <input class="form-check-input" type="checkbox" checked="">
                                                    Select All
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive permission-table border rounded">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch" checked>
                                                            Holidays
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox"
                                                                checked="">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox" checked>
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch" checked>
                                                            Leaves
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center"
                                                        checked>
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch">
                                                            Clients
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch">
                                                            Projects
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox" checked>
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch">
                                                            Tasks
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox" checked>
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch">
                                                            Chats
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox" checked>
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch">
                                                            Assets
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                role="switch">
                                                            Timing Sheets
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Read
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Write
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Create
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Delete
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Import
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md d-flex align-items-center">
                                                        <label class="form-check-label mt-0">
                                                            <input class="form-check-input" type="checkbox">
                                                            Export
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    <!-- /Edit Client -->

    <!-- Edit Todo -->
    <div class="modal fade" id="edit_todo">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Todo</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Todo Title</label>
                                    <input type="text" class="form-control" value="Update calendar and schedule">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Tag</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Internal</option>
                                        <option>Projects</option>
                                        <option>Meetings</option>
                                        <option>Reminder</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>High</option>
                                        <option selected>Medium</option>
                                        <option>Low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Descriptions</label>
                                    <div class="summernote"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Add Assignee</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Sophie</option>
                                        <option>Cameron</option>
                                        <option>Doris</option>
                                        <option>Rufana</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <label class="form-label">Status</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Completed</option>
                                        <option>Pending</option>
                                        <option>Onhold</option>
                                        <option>Inprogress</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit Todo -->

    <!-- Todo Details -->
    <div class="modal fade" id="view_todo">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title text-white">Respond to any pending messages</h4>
                    <span class="badge badge-danger d-inline-flex align-items-center"><i
                            class="ti ti-square me-1"></i>Urgent</span>
                    <span><i class="ti ti-star-filled text-warning"></i></span>
                    <a href="#"><i class="ti ti-trash text-white"></i></a>
                    <button type="button"
                        class="btn-close custom-btn-close bg-transparent fs-16 text-white position-static"
                        data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-2">Task Details</h5>
                    <div class="border rounded mb-3 p-2">
                        <div class="row row-gap-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <span class="d-block mb-1">Created On</span>
                                    <p class="text-dark">22 July 2025</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <span class="d-block mb-1">Due Date</span>
                                    <p class="text-dark">22 July 2025</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <span class="d-block mb-1">Status</span>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center">
                                        <i class="fas fa-circle fs-6 me-1"></i>Completed
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h5 class="mb-2">Description</h5>
                        <p>Hiking is a long, vigorous walk, usually on trails or footpaths in
                            the countryside. Walking for pleasure developed in Europe during the eighteenth century.
                            Religious pilgrimages have existed much longer but they involve walking long distances for a
                            spiritual purpose associated with specific
                            religions and also we achieve inner peace while we hike at a local park.
                        </p>
                    </div>
                    <div class="mb-3">
                        <h5 class="mb-2">Tags</h5>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-danger me-2">Internal</span>
                            <span class="badge badge-success me-2">Projects</span>
                            <span class="badge badge-secondary">Reminder</span>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-2">Assignee</h5>
                        <div class="avatar-list-stacked avatar-group-sm">
                            <span class="avatar avatar-rounded">
                                <img class="border border-white" src="assets/img/profiles/avatar-23.jpg"
                                    alt="img">
                            </span>
                            <span class="avatar avatar-rounded">
                                <img class="border border-white" src="assets/img/profiles/avatar-24.jpg"
                                    alt="img">
                            </span>
                            <span class="avatar avatar-rounded">
                                <img class="border border-white" src="assets/img/profiles/avatar-25.jpg"
                                    alt="img">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Todo Details -->

    <!-- Add Note -->
    <div class="modal fade" id="add_notes" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header header-border align-items-center justify-content-between">
                    <h5 class="modal-title">Add New Note</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger"> *</span></label>
                            <input class="form-control" type="text">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note <span class="text-danger"> *</span></label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attachment <span class="text-danger"> *</span></label>
                            <div
                                class="d-flex align-items-center justify-content-center border border-dashed rounded p-3 flex-column">
                                <span class="avatar avatar-lg avatar-rounded bg-primary-transparent mb-2"><i
                                        class="ti ti-folder-open fs-24"></i></span>
                                <p class="fs-14 text-center mb-2">Drag and drop your files</p>
                                <div class="file-upload position-relative btn btn-sm btn-primary px-3 mb-2">
                                    <i class="ti ti-upload me-1"></i>Upload
                                    <input type="file" accept="video/image">
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Uploaded Files</label>
                            <div class="border bg-light-500 rounded mb-3 p-3">
                                <h6 class="fw-medium mb-1">Projectneonals teyys.xls</h6>
                                <p class="mb-2">4.25 MB</p>
                                <div class="progress progress-xs mb-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 45%"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p>45%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between bg-light-500 rounded p-3">
                                <div>
                                    <h6 class="fw-medium mb-1">tes.txt</h6>
                                    <p>1.2 MB</p>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-sm btn-icon text-danger"><i
                                        class="ti ti-trash fs-20"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-end m-0">
                            <button type="button" class="btn btn-outline-light border me-2">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Note -->

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
                        <a href="#" class="btn btn-danger">Yes, Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
