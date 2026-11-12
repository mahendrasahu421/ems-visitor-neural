@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php($isEdit = (bool) $payment)

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <h2 class="mb-1">{{ $title }}</h2>
                <a href="{{ route('invoice.payments') }}" class="btn btn-outline-primary">
                    <i class="ti ti-list me-2"></i>Payment List
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ $isEdit ? route('invoice.payments.update', $payment->id) : route('invoice.payments.store') }}" method="POST" class="row g-3">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif
                        <div class="col-md-5">
                            <label class="form-label">Invoice</label>
                            <select name="workAmount_invoice_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach ($invoices as $invoice)
                                    <option value="{{ $invoice->id }}" @selected(old('workAmount_invoice_id', $payment->workAmount_invoice_id ?? '') == $invoice->id)>
                                        {{ $invoice->invoiceNumber ?? ('Invoice #' . $invoice->id) }} - {{ $invoice->ClientName ?? '-' }} - Rs. {{ number_format($invoice->totalAmount ?? 0, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Received Amount</label>
                            <input type="number" step="0.01" name="recived_amount" class="form-control" value="{{ old('recived_amount', $payment->recived_amount ?? '') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">TDS</label>
                            <input type="number" step="0.01" name="tds_amount" class="form-control" value="{{ old('tds_amount', $payment->tds_amount ?? 0) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Received Date</label>
                            <input type="date" name="received_date" class="form-control" value="{{ old('received_date', $payment?->received_date ? \Illuminate\Support\Carbon::parse($payment->received_date)->toDateString() : now()->toDateString()) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" @selected(old('status', $payment->status ?? 1) == 1)>Active</option>
                                <option value="0" @selected(old('status', $payment->status ?? 1) == 0)>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Note</label>
                            <input type="text" name="payment_note" class="form-control" value="{{ old('payment_note', $payment->payment_note ?? '') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-2"></i>{{ $isEdit ? 'Update Payment' : 'Save Payment' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection
