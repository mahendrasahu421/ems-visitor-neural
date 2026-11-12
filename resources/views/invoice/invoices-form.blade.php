@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $isEdit = (bool) $invoice;
        $invoiceNumber = $line->invoiceNumber ?? '';
        $prefillClientId = (int) old('client_id', $invoice->ClientID ?? request('client_id', 0));
        $prefillMilestoneId = (int) old('milestone_id', $invoice->milestone_id ?? request('milestone_id', 0));
        $prefillMilestone1Label = (string) request('milestone_1_label', '');
        $prefillMilestone1Amount = (string) request('milestone_1_amount', '');
        $prefillMilestone1DueDate = (string) request('milestone_1_due_date', $invoice->duedate ?? '');
        $prefillMilestonePct = (string) request('milestone_pct', '');
        $projectTotal = (float) request('project_total', 0);
        $oldLines = collect($lines ?? ($line ? [$line] : []));
        if ($oldLines->isEmpty()) {
            $oldLines = collect([(object) ['description' => $prefillMilestone1Label, 'duration' => '', 'amount' => $prefillMilestone1Amount]]);
        }
        $terms = old('condition', $invoice->terms ?? "E.& O.E.\n1. Payment to be made by \nCheque/NEFT/RTGS\n2. Interest @18% p.a. will be \ncharged if the payment is not \nmade within stipulated time.");
    @endphp

    <style>
        label {
            font-weight: 700;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">
            <div class="container mt-15">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $isEdit ? 'Edit Prefarma Invoice' : 'Create Prefarma Invoice' }}</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ $isEdit ? route('invoice.invoices.update', $invoice->id) : route('invoice.invoices.store') }}" method="post" id="basic-form" novalidate="novalidate" class="row g-3" enctype="multipart/form-data">
                                    @csrf
                                    @if ($isEdit)
                                        @method('PUT')
                                    @endif

                                    <div class="col-md-3">
                                        <label>Invoice Type</label>
                                        <div class="form-check">
                                            <input class="form-check-input invoiceType" type="radio" name="invoice_type" id="newInvoice" value="1" @checked(old('invoice_type', $invoice->invoice_type ?? 1) == 1)>
                                            <label class="form-check-label" for="newInvoice">New Invoice</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input invoiceType" type="radio" name="invoice_type" id="oldInvoice" value="2" @checked(old('invoice_type', $invoice->invoice_type ?? 1) == 2)>
                                            <label class="form-check-label" for="oldInvoice">Old Invoice</label>
                                        </div>
                                    </div>

                                    <div class="col-md-3" id="oldInvoiceTextbox" style="display: none;">
                                        <label>Old Invoice Number</label>
                                        <input type="text" name="oldInvoiceNumber" class="form-control" id="oldInvoiceNumber" value="{{ old('oldInvoiceNumber', $invoiceNumber) }}" />
                                        <span id="invoiceNumberMessage" style="color:red;"></span>
                                    </div>

                                    <div class="col-md-3" id="newInvoiceTextbox">
                                        <label>New Invoice Number</label>
                                        <input type="text" readonly name="newInvoiceNumber" class="form-control" id="newInvoiceNumber" value="{{ old('newInvoiceNumber', $invoiceNumber) }}" />
                                    </div>

                                    <div class="col-md-3">
                                        <label>To Client</label>
                                        <select required class="form-control" name="client_id" id="clientDropdown">
                                            <option value="0">---Select Client---</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->ClientID }}" @selected($prefillClientId === (int) $client->ClientID)>{{ $client->ClientName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <div id="clientDetails"></div>
                                    </div>

                                    @if ($projectTotal > 0)
                                        <div class="col-md-12">
                                            <div class="badge bg-light text-dark border p-2 mb-2">
                                                <i class="fas fa-calculator"></i> Calculations based on Project Total:
                                                <strong>Rs {{ number_format($projectTotal, 2) }}</strong>
                                            </div>
                                        </div>
                                    @endif

                                    <input type="hidden" id="linked_project_total" value="{{ $projectTotal }}">
                                    <input type="hidden" name="milestone_id" value="{{ $prefillMilestoneId }}">

                                    <div class="card-header">
                                        <h6 class="card-title">Self Details</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Invoice Date</label>
                                        <input required type="date" name="invoiceDate" id="invoiceDate" class="form-control currentdate" label="date" value="{{ old('invoiceDate', $invoice->invoiceDate ?? '') }}" />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Concern Person</label>
                                        <input required type="text" name="concern_person" class="form-control" label="concern_person" value="{{ old('concern_person', $invoice->concern ?? 'Shashank Shrivastava') }}" />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Mobile</label>
                                        <input required type="num" name="mobile" class="form-control" label="mobile" value="{{ old('mobile', $invoice->mobile ?? '8318721943') }}" />
                                    </div>

                                    <div class="col-md-4">
                                        <label>Term & Condition</label>
                                        <textarea required type="text" name="condition" class="form-control" label="Contact Number" value="" rows="6">{{ $terms }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivery Date</label>
                                        <input required type="date" class="form-control currentdate" name="deliveryDate" label="Salary Per Month" value="{{ old('deliveryDate', $invoice->deliverydate ?? '') }}" />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Due Date</label>
                                        <input required type="date" class="form-control currentdate" name="deudate" label="Full Address As Aadhar" value="{{ old('deudate', $prefillMilestone1DueDate) }}" />
                                    </div>

                                    <div id="discriptionSection">
                                        @foreach ($oldLines as $index => $oldLine)
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>Description</label>
                                                    <input required type="text" class="form-control" name="discription[]" label="Add Legal Id" value="{{ old('discription.' . $index, $oldLine->description ?? '') }}" />
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Duration</label>
                                                    <input required type="text" class="form-control" name="duration_days[]" placeholder="e.g. 6 Days, 3 Months" value="{{ old('duration_days.' . $index, $oldLine->duration ?? '') }}" />
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Bill %</label>
                                                    <input type="number" step="0.01" class="form-control pct-calc" name="bill_pct[]" placeholder="%" value="{{ $index === 0 ? old('bill_pct.0', $prefillMilestonePct) : old('bill_pct.' . $index, '') }}" />
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Amount</label>
                                                    <input required type="text" class="form-control milestone-amt-field" name="Amount[]" label="Add Legal Id" value="{{ old('Amount.' . $index, $oldLine->amount ?? '') }}" />
                                                </div>
                                                <div class="col-md-3 mt-4">
                                                    <div class="btn btn-success addMore" @if ($index > 0) style="display: none;" @endif>Add More</div>
                                                    <div class="btn btn-danger remove" @if ($index === 0) style="display: none;" @endif>Remove</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="card-header">
                                        <h6 class="card-title">Amount Detail</h6>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-3"><label>Sub Total </label></div>
                                            <div class="col-3"><input required type="text" class="" name="subtotal" id="subtotal" value="{{ old('subtotal', $invoice->subtotal ?? '') }}" /></div>
                                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="subtotal_usd"></span></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3"><label>Discount</label></div>
                                            <div class="col-3"><input type="text" class="" name="discount" id="discount" value="{{ old('discount', $invoice->discount ?? '') }}" /></div>
                                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="discount_usd"></span></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3"><label>Final Amount </label></div>
                                            <div class="col-3"><input required type="text" class="" name="finalAmount" id="finalAmount" value="{{ old('finalAmount', $invoice->finalAmount ?? '') }}" /></div>
                                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="finalAmount_usd"></span></div>
                                        </div>
                                        <div id="gst" style="display: none;">
                                            <div class="row mt-2">
                                                <div class="col-3"><label>SGST</label></div>
                                                <div class="col-3"><input required type="text" name="sgstAmount" class="" id="sgstAmount" value="{{ old('sgstAmount', $invoice->sgstAmount ?? '') }}" /></div>
                                                <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="sgstAmount_usd"></span></div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-3"><label>CGST</label></div>
                                                <div class="col-3"><input required type="text" class="" name="cgstAmount" id="cgstAmount" value="{{ old('cgstAmount', $invoice->cgstAmount ?? '') }}" /></div>
                                                <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="cgstAmount_usd"></span></div>
                                            </div>
                                        </div>
                                        <div class="row mt-2" id="igst" style="display: none;">
                                            <div class="col-3"><label>IGST</label></div>
                                            <div class="col-3"><input type="text" name="igstAmount" class="" id="igstAmount" value="{{ old('igstAmount', $invoice->igstAmount ?? '') }}" /></div>
                                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="igstAmount_usd"></span></div>
                                        </div>
                                        <div class="row mt-2" id="bankChargeRow" style="display: none;">
                                            <div class="col-3"><label>International Bank Charge @3.5%</label></div>
                                            <div class="col-3">
                                                <input type="text" name="bankChargeAmount" class="" id="bankChargeAmount" value="{{ old('bankChargeAmount', $invoice->bankChargeAmount ?? '') }}" />
                                                <input type="hidden" name="bankChargePercent" id="bankChargePercent" value="{{ old('bankChargePercent', $invoice->bankChargePercent ?? 0) }}" />
                                            </div>
                                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="bankChargeAmount_usd"></span></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3"><label>Total Amount </label></div>
                                            <div class="col-3"><input required type="text" name="totalAmount" class="" id="totalAmount" value="{{ old('totalAmount', $invoice->totalAmount ?? '') }}" /></div>
                                            <div class="col-3"><span class="ms-2 text-muted usd-equivalent-field" id="totalAmount_usd"></span></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3"><label>Convert INR to USD</label></div>
                                            <div class="col-3">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" id="convertToUsd" @checked(old('currency', $invoice->currency ?? 'INR') === 'USD')>
                                                    Show USD amount on invoice
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row mt-2" id="currencyConversionRow" style="display: none;">
                                            <div class="col-3"><label>Exchange Rate (1 USD = ? INR)</label></div>
                                            <div class="col-3">
                                                <input type="number" step="" name="exchange_rate" class="form-control" id="exchange_rate" value="{{ old('exchange_rate', $invoice->exchange_rate ?? '83.5000') }}" />
                                            </div>
                                            <div class="col-3"><label>Total Amount (USD)</label></div>
                                            <div class="col-3"><input type="text" name="total_amount_foreign" class="form-control" id="total_amount_foreign" readonly value="{{ old('total_amount_foreign', $invoice->total_amount_foreign ?? '') }}" /></div>
                                        </div>
                                        <input type="hidden" name="currency" id="currency" value="{{ old('currency', $invoice->currency ?? 'INR') }}">
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary" type="submit" name="submit">{{ $isEdit ? 'Update Invoice' : 'Submit' }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        $(document).ready(function () {
            function refreshInvoiceNumber() {
                let newInvoice = $('#newInvoice').val();
                let clientId = $('#clientDropdown').val();
                if (!newInvoice || $('#oldInvoice').is(':checked')) {
                    return;
                }
                $.ajax({
                    type: 'post',
                    url: '{{ route('invoice.invoices.invoice-type') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        newInvoice: newInvoice,
                        client_id: clientId
                    },
                    success: function (response) {
                        $('#newInvoiceNumber').val(response);
                    },
                    error: function (error) {
                        console.error('AJAX request error:', error);
                    }
                });
            }

            function handleRadioChange() {
                if ($('#newInvoice').is(':checked')) {
                    $('#oldInvoiceTextbox').hide();
                    $('#newInvoiceTextbox').show();
                    refreshInvoiceNumber();
                } else {
                    $('#oldInvoiceTextbox').show();
                    $('#newInvoiceTextbox').hide();
                }
            }

            function updateCurrencyConversion() {
                var exchangeRate = parseFloat($('#exchange_rate').val()) || 0;
                var showUsd = $('#convertToUsd').is(':checked');
                $('#currency').val(showUsd ? 'USD' : 'INR');
                $('#currencyConversionRow').toggle(showUsd);
                $('.usd-equivalent-field').text('');

                if (!showUsd || exchangeRate <= 0) {
                    $('#total_amount_foreign').val('');
                    return;
                }

                $('[id$="_usd"]').each(function () {
                    var sourceId = this.id.replace('_usd', '');
                    var amount = parseFloat($('#' + sourceId).val()) || 0;
                    if (amount > 0) {
                        $(this).text('$' + (amount / exchangeRate).toFixed(2));
                    }
                });

                var total = parseFloat($('#totalAmount').val()) || 0;
                $('#total_amount_foreign').val(total > 0 ? (total / exchangeRate).toFixed(2) : '');
            }

            var selectedCountry = '';
            var selectedState = '';

            function updateTaxVisibility() {
                var isInternational = !((selectedCountry === '' || selectedCountry === 'india') && selectedState !== 'internation' && selectedState !== 'international');
                var isUttarPradesh = selectedState === 'uttar pradesh';

                $('#sgstAmount, #cgstAmount, #igstAmount, #bankChargeAmount').val('0.00');
                $('#bankChargePercent').val('0');

                if (isInternational) {
                    $('#gst').hide();
                    $('#igst').hide();
                    $('#convertToUsd').prop('checked', true);
                    $('#bankChargeRow').show();
                } else if (isUttarPradesh) {
                    $('#gst').show();
                    $('#igst').hide();
                    $('#bankChargeRow').hide();
                } else {
                    $('#gst').hide();
                    $('#igst').show();
                    $('#bankChargeRow').hide();
                }
                updateCurrencyConversion();
            }

            function updateFinalAmount() {
                var subtotal = parseFloat($('#subtotal').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;
                var finalAmount = Math.max(0, subtotal - discount);
                $('#finalAmount').val(finalAmount.toFixed(2));

                if ($('#gst').is(':visible')) {
                    var sgst = finalAmount * 0.09;
                    var cgst = finalAmount * 0.09;
                    $('#sgstAmount').val(sgst.toFixed(2));
                    $('#cgstAmount').val(cgst.toFixed(2));
                    $('#igstAmount').val('0.00');
                    $('#bankChargeAmount').val('0.00');
                    $('#bankChargePercent').val('0');
                    $('#totalAmount').val((finalAmount + sgst + cgst).toFixed(2));
                } else if ($('#igst').is(':visible')) {
                    var igst = finalAmount * 0.18;
                    $('#sgstAmount').val('0.00');
                    $('#cgstAmount').val('0.00');
                    $('#igstAmount').val(igst.toFixed(2));
                    $('#bankChargeAmount').val('0.00');
                    $('#bankChargePercent').val('0');
                    $('#totalAmount').val((finalAmount + igst).toFixed(2));
                } else if ($('#bankChargeRow').is(':visible')) {
                    var bankCharge = finalAmount * 0.035;
                    $('#sgstAmount').val('0.00');
                    $('#cgstAmount').val('0.00');
                    $('#igstAmount').val('0.00');
                    $('#bankChargeAmount').val(bankCharge.toFixed(2));
                    $('#bankChargePercent').val('3.5');
                    $('#totalAmount').val((finalAmount + bankCharge).toFixed(2));
                } else {
                    $('#totalAmount').val(finalAmount.toFixed(2));
                }
                updateCurrencyConversion();
            }

            function updateSubtotal() {
                var totalAmount = 0;
                $('#discriptionSection .row').each(function () {
                    var amount = parseFloat($(this).find('input[name="Amount[]"]').val()) || 0;
                    totalAmount += amount;
                });
                $('#subtotal').val(totalAmount.toFixed(2));
                updateFinalAmount();
            }

            function loadClientDetails(clientId) {
                if (!clientId || clientId === '0') {
                    selectedCountry = '';
                    selectedState = '';
                    $('#clientDetails').html('');
                    updateTaxVisibility();
                    updateSubtotal();
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '{{ route('invoice.invoices.client-details') }}',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        client_id: clientId
                    },
                    success: function (response) {
                        if (!response || !response.ok) {
                            selectedCountry = '';
                            selectedState = '';
                            $('#clientDetails').html('');
                            updateTaxVisibility();
                            updateSubtotal();
                            return;
                        }

                        selectedCountry = (response.country_name || '').toLowerCase();
                        selectedState = (response.state_name || '').toLowerCase();
                        $('#clientDetails').html('<small><strong>Country:</strong> ' + (response.country_name || '-') + '<br><strong>State:</strong> ' + (response.state_name || '-') + '</small>');
                        updateTaxVisibility();
                        updateSubtotal();
                    },
                    error: function () {
                        alert('Error fetching client details.');
                    }
                });
            }

            $('#oldInvoiceNumber').change(function () {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('invoice.invoices.check-invoice') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        oldInvoiceNumber: $('#oldInvoiceNumber').val()
                    },
                    success: function (response) {
                        $('#invoiceNumberMessage').html(response);
                    },
                    error: function (error) {
                        console.error('AJAX request error:', error);
                    }
                });
            });

            $('#clientDropdown').on('change', function () {
                refreshInvoiceNumber();
                loadClientDetails($(this).val());
            });

            $('#discriptionSection').on('click', '.addMore', function () {
                var currentRow = $(this).closest('.row');
                var newRow = currentRow.clone(true);
                newRow.find('input').val('');
                newRow.find('.addMore').hide();
                newRow.find('.remove').show();
                currentRow.parent().append(newRow);
                updateSubtotal();
            });

            $('#discriptionSection').on('click', '.remove', function () {
                $(this).closest('.row').remove();
                updateSubtotal();
            });

            $('#discriptionSection').on('input change', '.pct-calc', function () {
                var pct = parseFloat($(this).val()) || 0;
                var projectTotal = parseFloat($('#linked_project_total').val()) || 0;
                if (projectTotal > 0) {
                    $(this).closest('.row').find('.milestone-amt-field').val(((projectTotal * pct) / 100).toFixed(2));
                    updateSubtotal();
                }
            });

            $('#discriptionSection').on('input change', '.milestone-amt-field', function () {
                var amt = parseFloat($(this).val()) || 0;
                var projectTotal = parseFloat($('#linked_project_total').val()) || 0;
                if (projectTotal > 0 && amt > 0) {
                    $(this).closest('.row').find('.pct-calc').val(((amt / projectTotal) * 100).toFixed(2));
                }
                updateSubtotal();
            });

            $('#discount, #exchange_rate').on('input change', updateFinalAmount);
            $('#convertToUsd').on('change', updateCurrencyConversion);
            $('.invoiceType').on('change', handleRadioChange);

            document.querySelectorAll('.currentdate').forEach(function (inputField) {
                if (!inputField.value) {
                    inputField.value = new Date().toISOString().split('T')[0];
                }
            });

            handleRadioChange();
            loadClientDetails($('#clientDropdown').val());
            updateSubtotal();
        });
    </script>
@endpush
