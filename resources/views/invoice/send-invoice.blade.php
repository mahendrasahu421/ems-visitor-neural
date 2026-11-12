@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <div class="page-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Send Invoice to Client</h3>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Invoice:</strong> {{ $invoice->invoiceNumber ?? ('Invoice #' . $invoice->id) }}
                        | <strong>Client:</strong> {{ $invoice->ClientName }}
                    </p>

                    <div class="alert alert-info">
                        PHPMailer sender: <strong>{{ $companyEmail }}</strong>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="post" action="{{ route('invoice.invoices.send.store', $invoice->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">To Email</label>
                            <input type="email" name="email_to" class="form-control" required
                                value="{{ old('email_to', $invoice->EmailAddress ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required
                                value="{{ old('subject', 'Proforma Invoice ' . ($invoice->invoiceNumber ?? ('#' . $invoice->id))) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="body" class="form-control" rows="6">{{ old('body', "Dear Sir/Madam,\n\nPlease find your proforma invoice link below. Kindly process payment as per terms.\n\nRegards,\n" . $companyName) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Send Email
                        </button>
                        <a href="{{ route('invoice.invoices.show', $invoice->id) }}" class="btn btn-secondary" >
                            Preview / Download
                        </a>
                        <a href="{{ route('invoice.invoices') }}" class="btn btn-link">Back to List</a>
                    </form>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection
