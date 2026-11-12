@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')
    <div class="page-wrapper" style="min-height: 226px;">
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Performance Indicator</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Performance
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Performance Indicator</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_performance_indicator"
                            class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add
                            Indicator</a>
                    </div>
                    <div class="head-icons ms-2">
                        <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-original-title="Collapse" id="collapse-header">
                            <i class="ti ti-chevrons-up"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Performance Indicator list -->
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <h5>Performance Indicator List</h5>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

                        <div class="dropdown">
                            <a href="javascript:void(0);"
                                class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
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
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_length" id="DataTables_Table_0_length"><label>Row Per Page
                                            <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
                                                class="form-select form-select-sm">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> Entries</label></div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter"><label> <input
                                                type="search" class="form-control form-control-sm" placeholder="Search"
                                                aria-controls="DataTables_Table_0"></label></div>
                                </div>
                            </div>
                            <div class="row dt-row">
                                <div class="col-sm-12 table-responsive">
                                    <table class="table datatable dataTable no-footer" id="DataTables_Table_0"
                                        aria-describedby="DataTables_Table_0_info">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="no-sort sorting sorting_asc" tabindex="0"
                                                    aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                    aria-sort="ascending"
                                                    aria-label="
											
												
											
										: activate to sort column descending"
                                                    style="width: 50.625px;">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" id="select-all">
                                                    </div>
                                                </th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Designation: activate to sort column ascending"
                                                    style="width: 202.525px;">Designation</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Department: activate to sort column ascending"
                                                    style="width: 130.125px;">Department</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Approved By: activate to sort column ascending"
                                                    style="width: 214.8px;">Approved By</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Created Date: activate to sort column ascending"
                                                    style="width: 140.9px;">Created Date</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                    rowspan="1" colspan="1"
                                                    aria-label="Status: activate to sort column ascending"
                                                    style="width: 102.012px;">Status</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0"
                                                    rowspan="1" colspan="1"
                                                    aria-label=": activate to sort column ascending"
                                                    style="width: 108.613px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>






                                            <tr class="odd">
                                                <td class="sorting_1">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Web Designer</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Designing</td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <a href="#" class="avatar avatar-md avatar-rounded">
                                                            <img src="assets/img/users/user-34.jpg" class="img-fluid"
                                                                alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Doglas Martini</a>
                                                            </h6>
                                                            <p class="fs-12">Manager</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    14 Jan 2024
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                        <i class="ti ti-point-filled me-1"></i>Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-icon d-inline-flex">
                                                        <a href="#" class="me-2" data-bs-toggle="modal"
                                                            data-bs-target="#edit_performance-indicator"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="even">
                                                <td class="sorting_1">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Web Developer</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Developer</td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <a href="#" class="avatar avatar-md avatar-rounded">
                                                            <img src="assets/img/users/user-34.jpg" class="img-fluid"
                                                                alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Doglas Martini</a>
                                                            </h6>
                                                            <p class="fs-12">Manager</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    21 Jan 2024
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                        <i class="ti ti-point-filled me-1"></i>Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-icon d-inline-flex">
                                                        <a href="#" class="me-2" data-bs-toggle="modal"
                                                            data-bs-target="#edit_performance-indicator"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="sorting_1">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">IOS Developer</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Developer</td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <a href="#" class="avatar avatar-md avatar-rounded">
                                                            <img src="assets/img/users/user-34.jpg" class="img-fluid"
                                                                alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Doglas Martini</a>
                                                            </h6>
                                                            <p class="fs-12">Manager</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    18 Feb 2024
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                        <i class="ti ti-point-filled me-1"></i>Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-icon d-inline-flex">
                                                        <a href="#" class="me-2" data-bs-toggle="modal"
                                                            data-bs-target="#edit_performance-indicator"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="even">
                                                <td class="sorting_1">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Android Developer</a>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Developer</td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <a href="#" class="avatar avatar-md avatar-rounded">
                                                            <img src="assets/img/users/user-34.jpg" class="img-fluid"
                                                                alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Doglas Martini</a>
                                                            </h6>
                                                            <p class="fs-12">Manager</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    24 Feb 2024
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                        <i class="ti ti-point-filled me-1"></i>Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-icon d-inline-flex">
                                                        <a href="#" class="me-2" data-bs-toggle="modal"
                                                            data-bs-target="#edit_performance-indicator"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="sorting_1">
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">DevOps Engineer</a>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>DevOps</td>
                                                <td>
                                                    <div class="d-flex align-items-center file-name-icon">
                                                        <a href="#" class="avatar avatar-md avatar-rounded">
                                                            <img src="assets/img/users/user-34.jpg" class="img-fluid"
                                                                alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fw-medium"><a href="#">Doglas Martini</a>
                                                            </h6>
                                                            <p class="fs-12">Manager</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    11 Mar 2024
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                        <i class="ti ti-point-filled me-1"></i>Active
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-icon d-inline-flex">
                                                        <a href="#" class="me-2" data-bs-toggle="modal"
                                                            data-bs-target="#edit_performance-indicator"><i
                                                                class="ti ti-edit"></i></a>
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status"
                                        aria-live="polite">Showing 1 - 5 of 5 entries</div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers"
                                        id="DataTables_Table_0_paginate">
                                        <ul class="pagination">
                                            <li class="paginate_button page-item previous disabled"
                                                id="DataTables_Table_0_previous"><a aria-controls="DataTables_Table_0"
                                                    aria-disabled="true" role="link" data-dt-idx="previous"
                                                    tabindex="-1" class="page-link"><i class="ti ti-chevron-left"></i>
                                                </a></li>
                                            <li class="paginate_button page-item active"><a href="#"
                                                    aria-controls="DataTables_Table_0" role="link" aria-current="page"
                                                    data-dt-idx="0" tabindex="0" class="page-link">1</a></li>
                                            <li class="paginate_button page-item next disabled"
                                                id="DataTables_Table_0_next"><a aria-controls="DataTables_Table_0"
                                                    aria-disabled="true" role="link" data-dt-idx="next"
                                                    tabindex="-1" class="page-link"><i
                                                        class="ti ti-chevron-right"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Performance Indicator list -->

        </div>

        <div class="footer d-sm-flex align-items-center justify-content-between border-top bg-white p-3">
            <p class="mb-0">2014 - 2025 © EMS.</p>
            <p>Designed &amp; Developed By <a href="javascript:void(0);" class="text-primary">Dreams</a></p>
        </div>

    </div>
    <div class="modal fade" id="add_performance_indicator">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Indicator</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <select class="select select2-hidden-accessible" data-select2-id="select2-data-1-s1wj"
                                        tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-3-cai2">Select</option>
                                        <option>Web Designer</option>
                                        <option>Web Developer</option>
                                        <option>IOS Developer</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-2-mtq8" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-f94s-container"
                                                aria-controls="select2-f94s-container"><span
                                                    class="select2-selection__rendered" id="select2-f94s-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <h5 class="fw-medium">Technical</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Customer Experience</label>
                                    <select class="select select2-hidden-accessible" data-select2-id="select2-data-4-w9bf"
                                        tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-6-llti">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-5-jdmf" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-7idu-container"
                                                aria-controls="select2-7idu-container"><span
                                                    class="select2-selection__rendered" id="select2-7idu-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Marketing</label>
                                    <select class="select select2-hidden-accessible" data-select2-id="select2-data-7-qm33"
                                        tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-9-28jl">Select</option>
                                        <option>Expert/Leader</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-8-ay1a" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-6q8j-container"
                                                aria-controls="select2-6q8j-container"><span
                                                    class="select2-selection__rendered" id="select2-6q8j-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Management</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-10-355j" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-12-fv06">Select</option>
                                        <option>Intermediate</option>
                                        <option>Medium</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-11-jlxj" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-1lus-container"
                                                aria-controls="select2-1lus-container"><span
                                                    class="select2-selection__rendered" id="select2-1lus-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Administration</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-13-nvsp" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-15-iiuk">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-14-xey8" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-fnwt-container"
                                                aria-controls="select2-fnwt-container"><span
                                                    class="select2-selection__rendered" id="select2-fnwt-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Presentation Skills</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-16-z2as" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-18-xd5h">Select</option>
                                        <option>None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-17-z6d4" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-bgm7-container"
                                                aria-controls="select2-bgm7-container"><span
                                                    class="select2-selection__rendered" id="select2-bgm7-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Quality of Work</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-19-t30t" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-21-xfbp">Select</option>
                                        <option>None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-20-49zt" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-x9p5-container"
                                                aria-controls="select2-x9p5-container"><span
                                                    class="select2-selection__rendered" id="select2-x9p5-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Efficiency</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-22-kj8c" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-24-juou">Select</option>
                                        <option>None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-23-9nfs" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-zvod-container"
                                                aria-controls="select2-zvod-container"><span
                                                    class="select2-selection__rendered" id="select2-zvod-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <h5 class="fw-medium">Organizational</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Integrity</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-25-j923" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-27-26ci">Select</option>
                                        <option>None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-26-cv8e" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-zx3i-container"
                                                aria-controls="select2-zx3i-container"><span
                                                    class="select2-selection__rendered" id="select2-zx3i-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Professionalism</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-28-ezf8" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-30-y8x6">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-29-ui3k" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-2wzg-container"
                                                aria-controls="select2-2wzg-container"><span
                                                    class="select2-selection__rendered" id="select2-2wzg-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Team Work</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-31-aj5u" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-33-xw5t">Select</option>
                                        <option>None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-32-qqry" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-lbeg-container"
                                                aria-controls="select2-lbeg-container"><span
                                                    class="select2-selection__rendered" id="select2-lbeg-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Critical Thinking</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-34-r86t" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-36-nxqw">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-35-v5cv" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-5y6e-container"
                                                aria-controls="select2-5y6e-container"><span
                                                    class="select2-selection__rendered" id="select2-5y6e-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Conflict Management</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-37-4yo1" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-39-4isa">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-38-x9o5" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-7mgh-container"
                                                aria-controls="select2-7mgh-container"><span
                                                    class="select2-selection__rendered" id="select2-7mgh-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Attendance</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-40-puen" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-42-jydl">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-41-8u1p" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-3yqw-container"
                                                aria-controls="select2-3yqw-container"><span
                                                    class="select2-selection__rendered" id="select2-3yqw-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Ability To Meet Deadline</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-43-20vg" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-45-pn3u">Select</option>
                                        <option>Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-44-60mz" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-cku7-container"
                                                aria-controls="select2-cku7-container"><span
                                                    class="select2-selection__rendered" id="select2-cku7-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-46-4jp3" tabindex="-1" aria-hidden="true">
                                        <option data-select2-id="select2-data-48-z829">Select</option>
                                        <option>Active</option>
                                        <option>Inactive</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-47-lmfr" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-3lj7-container"
                                                aria-controls="select2-3lj7-container"><span
                                                    class="select2-selection__rendered" id="select2-3lj7-container"
                                                    role="textbox" aria-readonly="true" title="Select">Select</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Indicator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_performance-indicator">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit New Indicator</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="#">
                    <div class="modal-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Designation</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-49-ax8k" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-51-4mhh">Web Designer
                                        </option>
                                        <option>Web Developer</option>
                                        <option>IOS Developer</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-50-9kz6" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-g077-container"
                                                aria-controls="select2-g077-container"><span
                                                    class="select2-selection__rendered" id="select2-g077-container"
                                                    role="textbox" aria-readonly="true" title="Web Designer">Web
                                                    Designer</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <h5 class="fw-medium">Technical</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Customer Experience</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-52-tvi0" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-54-69vv">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-53-5pru" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-wnhb-container"
                                                aria-controls="select2-wnhb-container"><span
                                                    class="select2-selection__rendered" id="select2-wnhb-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Marketing</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-55-vhvt" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-57-flqx">Expert/Leader
                                        </option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-56-v35m" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-1au7-container"
                                                aria-controls="select2-1au7-container"><span
                                                    class="select2-selection__rendered" id="select2-1au7-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Expert/Leader">Expert/Leader</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Management</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-58-wlu2" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-60-oh6s">Intermediate
                                        </option>
                                        <option>Medium</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-59-t95a" style="width: 100%;"><span
                                            class="selection"><span class="select2-selection select2-selection--single"
                                                role="combobox" aria-haspopup="true" aria-expanded="false"
                                                tabindex="0" aria-disabled="false"
                                                aria-labelledby="select2-nq4e-container"
                                                aria-controls="select2-nq4e-container"><span
                                                    class="select2-selection__rendered" id="select2-nq4e-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Intermediate">Intermediate</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Administration</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-61-xfnc" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-63-foz8">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-62-738m"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-3968-container"
                                                aria-controls="select2-3968-container"><span
                                                    class="select2-selection__rendered" id="select2-3968-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Presentation Skills</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-64-4i09" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-66-dinl">None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-65-6g39"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-pji3-container"
                                                aria-controls="select2-pji3-container"><span
                                                    class="select2-selection__rendered" id="select2-pji3-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="None">None</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Quality of Work</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-67-c9ht" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-69-k00o">None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-68-dw8w"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-v1ng-container"
                                                aria-controls="select2-v1ng-container"><span
                                                    class="select2-selection__rendered" id="select2-v1ng-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="None">None</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Efficiency</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-70-koa6" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-72-gycg">None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-71-326m"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-ocmh-container"
                                                aria-controls="select2-ocmh-container"><span
                                                    class="select2-selection__rendered" id="select2-ocmh-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="None">None</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <h5 class="fw-medium">Organizational</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Integrity</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-73-43xj" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-75-5p3k">None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-74-gzjj"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-tmm6-container"
                                                aria-controls="select2-tmm6-container"><span
                                                    class="select2-selection__rendered" id="select2-tmm6-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="None">None</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Professionalism</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-76-1aha" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-78-llge">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-77-7lqx"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-5gzb-container"
                                                aria-controls="select2-5gzb-container"><span
                                                    class="select2-selection__rendered" id="select2-5gzb-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Team Work</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-79-hvil" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-81-l5ow">None</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-80-tbzc"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-ukod-container"
                                                aria-controls="select2-ukod-container"><span
                                                    class="select2-selection__rendered" id="select2-ukod-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="None">None</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Critical Thinking</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-82-tfz7" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-84-11kx">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-83-mty9"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-oatw-container"
                                                aria-controls="select2-oatw-container"><span
                                                    class="select2-selection__rendered" id="select2-oatw-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Conflict Management</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-85-b5zr" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-87-5pgp">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-86-lce3"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-vg26-container"
                                                aria-controls="select2-vg26-container"><span
                                                    class="select2-selection__rendered" id="select2-vg26-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Attendance</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-88-s871" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-90-15la">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-89-j7za"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-i2gf-container"
                                                aria-controls="select2-i2gf-container"><span
                                                    class="select2-selection__rendered" id="select2-i2gf-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Ability To Meet Deadline</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-91-hd44" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-93-5mkf">Advanced</option>
                                        <option>Intermediate</option>
                                        <option>Average</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-92-0yug"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-m71u-container"
                                                aria-controls="select2-m71u-container"><span
                                                    class="select2-selection__rendered" id="select2-m71u-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Advanced">Advanced</span><span
                                                    class="select2-selection__arrow" role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="select select2-hidden-accessible"
                                        data-select2-id="select2-data-94-bxgz" tabindex="-1" aria-hidden="true">
                                        <option>Select</option>
                                        <option selected="" data-select2-id="select2-data-96-965e">Active</option>
                                        <option>Inactive</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                        dir="ltr" data-select2-id="select2-data-95-vb46"
                                        style="width: 100%;"><span class="selection"><span
                                                class="select2-selection select2-selection--single" role="combobox"
                                                aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                aria-disabled="false" aria-labelledby="select2-8xv6-container"
                                                aria-controls="select2-8xv6-container"><span
                                                    class="select2-selection__rendered" id="select2-8xv6-container"
                                                    role="textbox" aria-readonly="true"
                                                    title="Active">Active</span><span class="select2-selection__arrow"
                                                    role="presentation"><b
                                                        role="presentation"></b></span></span></span><span
                                            class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
