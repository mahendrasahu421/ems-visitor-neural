@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
                <a href="{{ route('invoice.invoices') }}" class="btn btn-primary">
                    <i class="ti ti-circle-plus me-2"></i>New Invoice
                </a>
            </div>

            <div class="row">
                @foreach ([
                    ['label' => 'Clients', 'value' => $stats['clients'], 'icon' => 'ti-users'],
                    ['label' => 'Companies', 'value' => $stats['companies'], 'icon' => 'ti-building'],
                    ['label' => 'Invoices', 'value' => $stats['invoices'], 'icon' => 'ti-file-invoice'],
                    ['label' => 'Received', 'value' => 'Rs. ' . number_format($stats['received'], 2), 'icon' => 'ti-cash'],
                    ['label' => 'Expenses', 'value' => 'Rs. ' . number_format($stats['expenses'], 2), 'icon' => 'ti-receipt'],
                ] as $item)
                    <div class="col-xl col-md-4 col-sm-6 mb-3">
                        <div class="card mb-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-1 text-muted">{{ $item['label'] }}</p>
                                    <h4 class="mb-0">{{ $item['value'] }}</h4>
                                </div>
                                <span class="avatar avatar-lg bg-primary-transparent text-primary">
                                    <i class="ti {{ $item['icon'] }} fs-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Recent Invoices</h5>
                    <a href="{{ route('invoice.invoices') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoiceNumber ?? '-' }}</td>
                                        <td>{{ $invoice->ClientName ?? '-' }}</td>
                                        <td>{{ $invoice->invoiceDate }}</td>
                                        <td>Rs. {{ number_format($invoice->totalAmount ?? 0, 2) }}</td>
                                        <td>Rs. {{ number_format($invoice->paidAmount ?? 0, 2) }}</td>
                                        <td>
                                            @include('invoice.partials.status', ['status' => $invoice->status])
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No invoice data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection
