@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ $title }}</h2>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2">
                    <a href="{{ route('masters.organisation.policy.create') }}" 
                        class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-circle-plus me-2"></i>Add New Policy
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="card">
            <div class="card-body p-0">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table datatable">
                        <thead class="thead-light">
                            <tr>
                                <th class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>Policy Title</th>
                                <th>Policy Code</th>
                                <th>Department</th>
                                <th>Effective From</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dummy Row 1 -->
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>Leave Policy</td>
                                <td>POL001</td>
                                <td>Human Resources</td>
                                <td>2025-01-01</td>
                                <td>
                                    <span class="badge badge-success d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>Active
                                    </span>
                                </td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="" class="me-2">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Dummy Row 2 -->
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>Work From Home Policy</td>
                                <td>POL002</td>
                                <td>IT Department</td>
                                <td>2025-02-15</td>
                                <td>
                                    <span class="badge badge-warning d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>Pending
                                    </span>
                                </td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="" class="me-2">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Dummy Row 3 -->
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </td>
                                <td>Dress Code Policy</td>
                                <td>POL003</td>
                                <td>Admin</td>
                                <td>2024-12-01</td>
                                <td>
                                    <span class="badge badge-danger d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>Inactive
                                    </span>
                                </td>
                                <td>
                                    <div class="action-icon d-inline-flex">
                                        <a href="" class="me-2">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <x-footer />
</div>



@endsection
