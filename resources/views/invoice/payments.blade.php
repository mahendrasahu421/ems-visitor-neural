@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $showBankChargeColumn = $paymentInvoices->contains(fn ($row) => (float) ($row->bankChargeAmount ?? 0) > 0);
        $emptyStateColspan = $showBankChargeColumn ? 17 : 16;
    @endphp

    <style>
        .payment-table label {
            font-weight: 700;
        }

        .amount-pending {
            color: #dc3545;
            font-weight: 700;
        }

        .amount-paid {
            color: #198754;
            font-weight: 700;
        }
    </style>

    <div class="page-wrapper">
        <div class="content payment-table">
            <div class="card-header mb-3">
                <h3 class="text-center mb-0">Received Payment</h3>
            </div>

            <form method="GET" class="mb-4 p-3 card-header" action="{{ route('invoice.payments') }}">
                <div class="row align-items-end">
                    <div class="col-sm-3">
                        <label for="clientId" class="form-label">Client Name:</label>
                        <select class="form-control" id="clientId" name="clientId">
                            <option value="0">All Clients</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->ClientID }}" @selected((int) $clientId === (int) $client->ClientID)>
                                    {{ $client->ClientName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="paymentInvoicesTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Invoice Date</th>
                                    <th>Party Name</th>
                                    <th>Country City</th>
                                    <th>Invoice Number</th>
                                    <th>Duration</th>
                                    <th>Due Date</th>
                                    <th>Gross Total</th>
                                    <th>SGST</th>
                                    <th>CGST</th>
                                    <th>IGST</th>
                                    @if ($showBankChargeColumn)
                                        <th>Bank Charge</th>
                                    @endif
                                    <th>Grand Amount</th>
                                    <th>Received</th>
                                    <th>TDS</th>
                                    <th>Pending</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paymentInvoices as $row)
                                    @php
                                        $paidTotal = (float) ($row->paid_total ?? 0);
                                        $pending = max(0, (float) $row->totalAmount - $paidTotal);
                                        $isPaid = $pending <= 0.01;
                                    @endphp
                                    <tr>
                                        <td>{{ $row->invoiceDate ? \Illuminate\Support\Carbon::parse($row->invoiceDate)->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $row->ClientName ?? '-' }}</td>
                                        <td>{{ trim(($row->CountryName ?? '') . ', ' . ($row->StateName ?? ''), ', ') ?: '-' }}</td>
                                        <td>
                                            {{ $row->invoiceNumber ?? '-' }}<br>
                                            <span class="badge bg-warning">
                                                <a class="text-white" target="_blank" href="{{ route('invoice.invoices.show', $row->id) }}">Preview Invoice</a>
                                            </span>
                                            <span class="badge bg-info">
                                                <a class="text-white" target="_blank" href="{{ route('invoice.invoices.send', $row->id) }}">Email</a>
                                            </span>
                                        </td>
                                        <td>{{ $row->duration_days ?: '-' }}</td>
                                        <td>{{ $row->duedate ? \Illuminate\Support\Carbon::parse($row->duedate)->format('d-m-Y') : '-' }}</td>
                                        <td>{{ number_format((float) $row->finalAmount, 2) }}</td>
                                        <td>{{ number_format((float) $row->sgstAmount, 2) }}</td>
                                        <td>{{ number_format((float) $row->cgstAmount, 2) }}</td>
                                        <td>{{ number_format((float) $row->igstAmount, 2) }}</td>
                                        @if ($showBankChargeColumn)
                                            <td>{{ number_format((float) ($row->bankChargeAmount ?? 0), 2) }}</td>
                                        @endif
                                        <td>{{ number_format((float) $row->totalAmount, 2) }}</td>
                                        <td class="amount-paid">{{ number_format((float) $row->received_amount_total, 2) }}</td>
                                        <td>{{ number_format((float) $row->tds_amount_total, 2) }}</td>
                                        <td class="amount-pending">{{ number_format($pending, 2) }}</td>
                                        <td>
                                            @if ($isPaid)
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Partial / Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$isPaid)
                                                <button type="button"
                                                    onclick="openPaymentModal({{ (int) $row->id }}, {{ (float) $row->totalAmount }}, {{ (int) $row->ClientID }}, {{ $pending }})"
                                                    class="btn btn-primary btn-sm" title="Receive Payment">
                                                    <i class="ti ti-cash"></i>
                                                </button>
                                            @else
                                                <span class="text-success">Completed</span>
                                            @endif

                                            @if ($paidTotal <= 0.01)
                                                <button type="button" class="btn btn-danger btn-sm mt-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cancelInvoiceModal"
                                                    data-invoice-id="{{ (int) $row->id }}"
                                                    title="Cancel Invoice">
                                                    Cancel Invoice
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $emptyStateColspan }}" class="text-center text-muted py-4">No invoices found.</td>
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

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Receive Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('invoice.payments.store') }}" method="post" id="paymentForm">
                        @csrf
                        <input type="hidden" name="workAmount_invoice_id" id="workAmountInvoiceId" value="">
                        <input type="hidden" name="ClientID" id="paymentClientId" value="">

                        <div class="form-group mb-3">
                            <label for="totalAmount">Final Amount In Invoice</label>
                            <input type="text" readonly id="totalAmount" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="pendingAmount">Pending Amount</label>
                            <input type="text" readonly id="pendingAmount" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="receivedAmount">Received Amount</label>
                            <input type="number" step="0.01" min="0" name="recived_amount" id="receivedAmount" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tdsAmount">TDS Amount</label>
                            <input type="number" step="0.01" min="0" name="tds_amount" id="tdsAmount" class="form-control" value="0">
                        </div>
                        <div class="form-group mb-3">
                            <label for="paymentCredit">Payment Credit</label>
                            <input type="text" readonly id="paymentCredit" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="receivedDate">Received Date</label>
                            <input type="date" name="received_date" id="receivedDate" class="form-control" required value="{{ now()->toDateString() }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="paymentNote">Note</label>
                            <input type="text" name="payment_note" id="paymentNote" class="form-control" placeholder="Cheque/NEFT/reference note">
                        </div>
                        <button type="submit" class="btn btn-success mt-2">Save Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelInvoiceModal" tabindex="-1" aria-labelledby="cancelInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelInvoiceModalLabel">Cancel Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoice.payments.cancel-invoice') }}" method="post" id="cancelInvoiceForm">
                    @csrf
                    <div class="modal-body">
                        <p>Do you want to cancel this invoice? Please provide a reason.</p>
                        <input type="hidden" name="invoice_id" id="cancelInvoiceId">
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                            <textarea class="form-control" id="cancelReason" name="cancel_reason" rows="3" required maxlength="255" placeholder="e.g., Incorrect amount, client request"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script>
        var currentPending = 0;

        function openPaymentModal(invoiceId, totalAmount, clientId, pendingAmount) {
            currentPending = parseFloat(pendingAmount) || 0;
            $('#workAmountInvoiceId').val(invoiceId);
            $('#paymentClientId').val(clientId);
            $('#totalAmount').val((parseFloat(totalAmount) || 0).toFixed(2));
            $('#pendingAmount').val(currentPending.toFixed(2));
            $('#receivedAmount').val(currentPending.toFixed(2));
            $('#tdsAmount').val('0.00');
            $('#paymentNote').val('');
            $('#receivedDate').val(new Date().toISOString().slice(0, 10));
            updatePaymentCredit();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal')).show();
        }

        function updatePaymentCredit() {
            var received = parseFloat($('#receivedAmount').val()) || 0;
            var tds = parseFloat($('#tdsAmount').val()) || 0;
            $('#paymentCredit').val((received + tds).toFixed(2));
        }

        $(document).ready(function() {
            $('#paymentInvoicesTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['excelHtml5'],
                order: [[0, 'desc']]
            });

            $('#receivedAmount, #tdsAmount').on('input', updatePaymentCredit);
            $('#paymentForm').on('submit', function(e) {
                var credit = parseFloat($('#paymentCredit').val()) || 0;
                if (credit <= 0) {
                    e.preventDefault();
                    alert('Enter received amount or TDS amount.');
                    return false;
                }
                if (credit > currentPending + 0.01) {
                    e.preventDefault();
                    alert('Payment cannot be more than pending amount.');
                    return false;
                }
            });

            var cancelInvoiceModal = document.getElementById('cancelInvoiceModal');
            if (cancelInvoiceModal) {
                cancelInvoiceModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    $('#cancelInvoiceId').val(button ? button.getAttribute('data-invoice-id') : '');
                    $('#cancelReason').val('');
                });
            }
        });
    </script>
@endpush
