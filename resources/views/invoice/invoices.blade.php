@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <a href="{{ route('invoice.invoices.create') }}" class="btn btn-primary">
                    <i class="ti ti-circle-plus me-2"></i>Create Invoice
                </a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="invoiceInvoicesList" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Client</th>
                                    <th>Company</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
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
    'tableId' => 'invoiceInvoicesList',
    'ajaxUrl' => route('invoice.invoices.list'),
    'columns' => [
        ['data' => 'invoice_no', 'name' => 'invoice_no'],
        ['data' => 'client', 'name' => 'client'],
        ['data' => 'company', 'name' => 'company'],
        ['data' => 'date', 'name' => 'date'],
        ['data' => 'total', 'name' => 'total'],
        ['data' => 'paid', 'name' => 'paid'],
        ['data' => 'status', 'name' => 'status'],
        ['data' => 'action', 'name' => 'action'],
    ],
    'order' => [[3, 'desc']],
    'columnDefs' => [['targets' => [7], 'orderable' => false, 'searchable' => false]],
])
