@extends('layout.master')
@section('title', $title)

@php
    $money = fn ($value) => 'Rs. ' . number_format((float) $value, 2);
    $balance = fn ($value) => $money(abs((float) $value)) . ((float) $value >= 0 ? ' Dr' : ' Cr');
    $selectedClient = $clients->firstWhere('ClientID', $clientId);
@endphp

<style>
        .ledger-shell {
            --ledger-ink: #1f2937;
            --ledger-muted: #64748b;
            --ledger-border: #e5e7eb;
        }

        .ledger-filter-card,
        .ledger-statement-card,
        .ledger-table-card,
        .ledger-empty-card {
            border: 1px solid var(--ledger-border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .ledger-filter-card {
            border-top: 4px solid #2563eb;
        }

        .ledger-statement-card {
            border-top: 4px solid #0f766e;
        }

        .ledger-label {
            align-items: center;
            color: var(--ledger-muted);
            display: flex;
            font-size: 13px;
            font-weight: 600;
            gap: 6px;
            margin-bottom: 6px;
        }

        .ledger-label i {
            font-size: 16px;
        }

        .ledger-stat-card {
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--ledger-ink);
            height: 100%;
            min-height: 122px;
            overflow: hidden;
            padding: 18px;
            position: relative;
        }

        .ledger-stat-card::before {
            content: "";
            inset: 0 auto 0 0;
            position: absolute;
            width: 5px;
        }

        .ledger-stat-card .stat-icon {
            align-items: center;
            border-radius: 8px;
            display: inline-flex;
            font-size: 24px;
            height: 44px;
            justify-content: center;
            margin-bottom: 14px;
            width: 44px;
        }

        .ledger-stat-card .stat-title {
            color: var(--ledger-muted);
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .ledger-stat-card .stat-value {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.25;
            margin: 0;
        }

        .ledger-stat-card .stat-note {
            color: var(--ledger-muted);
            display: block;
            font-size: 12px;
            margin-top: 6px;
        }

        .ledger-stat-blue {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .ledger-stat-blue::before,
        .ledger-stat-blue .stat-icon {
            background: #2563eb;
            color: #fff;
        }

        .ledger-stat-teal {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .ledger-stat-teal::before,
        .ledger-stat-teal .stat-icon {
            background: #059669;
            color: #fff;
        }

        .ledger-stat-amber {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .ledger-stat-amber::before,
        .ledger-stat-amber .stat-icon {
            background: #d97706;
            color: #fff;
        }

        .ledger-stat-rose {
            background: #fff1f2;
            border-color: #fecdd3;
        }

        .ledger-stat-rose::before,
        .ledger-stat-rose .stat-icon {
            background: #e11d48;
            color: #fff;
        }

        .ledger-stat-indigo {
            background: #eef2ff;
            border-color: #c7d2fe;
        }

        .ledger-stat-indigo::before,
        .ledger-stat-indigo .stat-icon {
            background: #4f46e5;
            color: #fff;
        }

        .ledger-table-card {
            border-top: 4px solid #7c3aed;
        }

        .ledger-table-card .card-header {
            background: #fafafa;
            border-bottom: 1px solid var(--ledger-border);
        }

        .ledger-table-card thead th {
            background: #f8fafc;
            color: #334155;
            font-size: 12px;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .ledger-empty-icon {
            align-items: center;
            background: #eff6ff;
            border-radius: 8px;
            color: #2563eb;
            display: inline-flex;
            font-size: 42px;
            height: 74px;
            justify-content: center;
            margin-bottom: 16px;
            width: 74px;
        }
</style>

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content ledger-shell">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                </div>
            </div>

            @include('invoice.partials.nav')

            <div class="card ledger-filter-card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('invoice.ledger') }}" class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label ledger-label"><i class="ti ti-user-dollar"></i>Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->ClientID }}" @selected((int) $clientId === (int) $client->ClientID)>{{ $client->ClientName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label ledger-label"><i class="ti ti-calendar-event"></i>From Date</label>
                            <input type="date" name="fromDate" class="form-control" value="{{ $fromDate }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label ledger-label"><i class="ti ti-calendar-check"></i>To Date</label>
                            <input type="date" name="toDate" class="form-control" value="{{ $toDate }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-1"></i>Show Ledger
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if ($clientId > 0)
                <div class="card ledger-statement-card mb-3">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-6">
                                <span class="ledger-label"><i class="ti ti-building-bank"></i>Statement For</span>
                                <h5 class="mb-0">{{ $selectedClient->ClientName ?? 'Selected Client' }}</h5>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <span class="ledger-label"><i class="ti ti-calendar-stats"></i>Statement Period</span>
                                <strong>{{ \Illuminate\Support\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Illuminate\Support\Carbon::parse($toDate)->format('d M Y') }}</strong>
                            </div>
                            <div class="col-lg-3 col-md-6 text-lg-end">
                                <span class="ledger-label justify-content-lg-end"><i class="ti ti-scale"></i>Closing Balance</span>
                                <h5 class="mb-0 {{ $summary['closing'] > 0 ? 'text-danger' : 'text-success' }}">{{ $balance($summary['closing']) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-xl col-md-6">
                        <div class="ledger-stat-card ledger-stat-blue">
                            <span class="stat-icon"><i class="ti ti-door-enter"></i></span>
                            <span class="stat-title">Opening Balance</span>
                            <h5 class="stat-value">{{ $balance($summary['opening']) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl col-md-6">
                        <div class="ledger-stat-card ledger-stat-indigo">
                            <span class="stat-icon"><i class="ti ti-file-invoice"></i></span>
                            <span class="stat-title">Invoices</span>
                            <h5 class="stat-value">{{ $money($summary['invoiced']) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl col-md-6">
                        <div class="ledger-stat-card ledger-stat-teal">
                            <span class="stat-icon"><i class="ti ti-cash-banknote"></i></span>
                            <span class="stat-title">Received</span>
                            <h5 class="stat-value">{{ $money($summary['received']) }}</h5>
                            <small class="stat-note">TDS: {{ $money($summary['tds']) }}</small>
                        </div>
                    </div>
                    <div class="col-xl col-md-6">
                        <div class="ledger-stat-card ledger-stat-rose">
                            <span class="stat-icon"><i class="ti ti-alert-circle"></i></span>
                            <span class="stat-title">Outstanding</span>
                            <h5 class="stat-value {{ $summary['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">{{ $balance($summary['outstanding']) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="card ledger-table-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="ti ti-report-money me-2 text-primary"></i>Account Statement</h5>
                        <span class="text-muted">Debit increases receivable, credit reduces it.</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="invoiceLedgerTable" class="table table-hover table-bordered nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="ti ti-calendar me-1"></i>Date</th>
                                        <th><i class="ti ti-list-details me-1"></i>Particulars</th>
                                        <th><i class="ti ti-hash me-1"></i>Ref No.</th>
                                        <th class="text-end"><i class="ti ti-arrow-up-right me-1"></i>Debit</th>
                                        <th class="text-end"><i class="ti ti-arrow-down-left me-1"></i>Credit</th>
                                        <th><i class="ti ti-percentage me-1"></i>TDS</th>
                                        <th class="text-end"><i class="ti ti-scale me-1"></i>Balance</th>
                                        <th><i class="ti ti-circle-check me-1"></i>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-light">
                                        <td data-order="0000-00-00">Opening</td>
                                        <td>Opening balance brought forward</td>
                                        <td>-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td>-</td>
                                        <td class="text-end fw-semibold">{{ $balance($summary['opening']) }}</td>
                                        <td><span class="badge bg-secondary">Opening</span></td>
                                    </tr>
                                    @foreach ($ledger as $line)
                                        <tr>
                                            <td data-order="{{ !empty($line['date']) ? \Illuminate\Support\Carbon::parse($line['date'])->format('Y-m-d') : '' }}">{{ !empty($line['date']) ? \Illuminate\Support\Carbon::parse($line['date'])->format('d-m-Y') : '-' }}</td>
                                            <td>
                                                <strong>{{ $line['particulars'] }}</strong>
                                                @if (($line['mode'] ?? '-') !== '-')
                                                    <span class="badge bg-light text-dark ms-1">{{ $line['mode'] }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $line['reference'] }}</td>
                                            <td class="text-end">{{ $line['debit'] > 0 ? $money($line['debit']) : '-' }}</td>
                                            <td class="text-end">{{ $line['credit'] > 0 ? $money($line['credit']) : '-' }}</td>
                                            <td>{{ $line['tds'] > 0 ? $money($line['tds']) : '-' }}</td>
                                            <td class="text-end fw-semibold">{{ $balance($line['balance']) }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $line['status'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Current Period Total</th>
                                        <th class="text-end">{{ $money($summary['invoiced']) }}</th>
                                        <th class="text-end">{{ $money($summary['received']) }}</th>
                                        <th>{{ $money($summary['tds']) }}</th>
                                        <th class="text-end">{{ $balance($summary['closing']) }}</th>
                                        <th>Closing</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card ledger-empty-card">
                    <div class="card-body text-center text-muted py-5">
                        <span class="ledger-empty-icon"><i class="ti ti-report-money"></i></span>
                        <h5 class="text-dark mb-1">Client Ledger</h5>
                        <p class="mb-0">Select a client and date range to view the ledger.</p>
                    </div>
                </div>
            @endif
        </div>
        <x-footer />
    </div>
@endsection

@if ($clientId > 0)
    @include('partials.datatable-pagination', [
        'tableId' => 'invoiceLedgerTable',
        'order' => [[0, 'asc']],
        'columnDefs' => [],
    ])
@endif
