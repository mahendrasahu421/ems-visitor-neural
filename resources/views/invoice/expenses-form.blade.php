@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php($isEdit = (bool) $expense)

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <a href="{{ route('invoice.expenses') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list me-2"></i>Expense List
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ $isEdit ? route('invoice.expenses.update', $expense->id) : route('invoice.expenses.store') }}" method="POST" class="row g-3">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif
                        <div class="col-md-3">
                            <label class="form-label">Client</label>
                            <select name="ClientID" class="form-select">
                                <option value="">General Expense</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->ClientID }}" @selected(old('ClientID', $expense->ClientID ?? '') == $client->ClientID)>{{ $client->ClientName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="expense_category_id" class="form-select">
                                <option value="">Select</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('expense_category_id', $expense->expense_category_id ?? '') == $category->id)>{{ $category->categories }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">New Category</label>
                            <input type="text" name="category_name" class="form-control" value="{{ old('category_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expense Name</label>
                            <input type="text" name="expense_name" class="form-control" value="{{ old('expense_name', $expense->expense_name ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cost</label>
                            <input type="number" step="0.01" name="expense_cost" class="form-control" value="{{ old('expense_cost', $expense->expense_cost ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date ?? now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mode</label>
                            <select name="expense_mode" class="form-select" required>
                                <option value="Cash" @selected(old('expense_mode', $expense->expense_mode ?? 'Cash') === 'Cash')>Cash</option>
                                <option value="Online" @selected(old('expense_mode', $expense->expense_mode ?? '') === 'Online')>Online</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="expense_status" class="form-select">
                                <option value="1" @selected(old('expense_status', $expense->expense_status ?? 1) == 1)>Active</option>
                                <option value="0" @selected(old('expense_status', $expense->expense_status ?? 1) == 0)>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Invoice/Reference</label>
                            <input type="text" name="expense_invoice" class="form-control" value="{{ old('expense_invoice', $expense->expense_invoice ?? '') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-2"></i>{{ $isEdit ? 'Update Expense' : 'Save Expense' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection
