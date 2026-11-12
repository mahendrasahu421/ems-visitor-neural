@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <a href="{{ route('invoice.expenses.create') }}" class="btn btn-primary">
                    <i class="ti ti-circle-plus me-2"></i>Create Expense
                </a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="invoiceExpensesList" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Client</th>
                                    <th>Name</th>
                                    <th>Cost</th>
                                    <th>Date</th>
                                    <th>Mode</th>
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
    'tableId' => 'invoiceExpensesList',
    'ajaxUrl' => route('invoice.expenses.list'),
    'columns' => [
        ['data' => 'id', 'name' => 'id'],
        ['data' => 'category', 'name' => 'category'],
        ['data' => 'client', 'name' => 'client'],
        ['data' => 'name', 'name' => 'name'],
        ['data' => 'cost', 'name' => 'cost'],
        ['data' => 'date', 'name' => 'date'],
        ['data' => 'mode', 'name' => 'mode'],
        ['data' => 'action', 'name' => 'action'],
    ],
    'order' => [[0, 'desc']],
    'columnDefs' => [['targets' => [7], 'orderable' => false, 'searchable' => false]],
])
