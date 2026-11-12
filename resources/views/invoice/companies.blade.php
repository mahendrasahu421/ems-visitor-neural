@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <a href="{{ route('invoice.companies.create') }}" class="btn btn-primary">
                    <i class="ti ti-circle-plus me-2"></i>Create Company
                </a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="invoiceCompaniesList" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company</th>
                                    <th>Contact</th>
                                    <th>GST/PAN</th>
                                    <th>Bank</th>
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
    'tableId' => 'invoiceCompaniesList',
    'ajaxUrl' => route('invoice.companies.list'),
    'columns' => [
        ['data' => 'CompanyID', 'name' => 'CompanyID'],
        ['data' => 'company', 'name' => 'company'],
        ['data' => 'contact', 'name' => 'contact'],
        ['data' => 'gst_pan', 'name' => 'gst_pan'],
        ['data' => 'bank', 'name' => 'bank'],
        ['data' => 'status', 'name' => 'status'],
        ['data' => 'action', 'name' => 'action'],
    ],
    'order' => [[0, 'desc']],
    'columnDefs' => [['targets' => [6], 'orderable' => false, 'searchable' => false]],
])
