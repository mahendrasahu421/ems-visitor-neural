@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('invoice.projects.mapping') }}" class="btn btn-outline-success">
                        <i class="ti ti-users-group me-2"></i>Project Mapping
                    </a>
                    <a href="{{ route('invoice.projects.create') }}" class="btn btn-primary">
                        <i class="ti ti-circle-plus me-2"></i>Create Project
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="invoiceProjectsList" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>With Tax</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection

@include('partials.datatable-pagination', [
    'tableId' => 'invoiceProjectsList',
    'ajaxUrl' => route('invoice.projects.list'),
    'columns' => [
        ['data' => 'project_id', 'name' => 'project_id'],
        ['data' => 'project_name', 'name' => 'project_name'],
        ['data' => 'client', 'name' => 'client'],
        ['data' => 'type', 'name' => 'type'],
        ['data' => 'value', 'name' => 'value'],
        ['data' => 'with_tax', 'name' => 'with_tax'],
        ['data' => 'status', 'name' => 'status'],
        ['data' => 'action', 'name' => 'action'],
    ],
    'order' => [[0, 'desc']],
    'columnDefs' => [['targets' => [7], 'orderable' => false, 'searchable' => false]],
])
