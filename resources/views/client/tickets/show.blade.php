@extends('client.layout.layout')
@section('title', $title ?? 'Ticket Details')

@section('main-section')
    <style>
        .client-ticket-detail .card-custom {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .client-ticket-detail .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }

        .client-ticket-detail .info-row:last-child {
            border-bottom: none;
        }

        .client-ticket-detail .info-label {
            color: #64748b;
            font-weight: 500;
        }

        .client-ticket-detail .info-val {
            color: #1e293b;
            font-weight: 600;
            text-align: right;
        }
    </style>

    <div class="page-wrapper client-ticket-detail">
        <div class="content">

            {{-- Header & Breadcrumbs --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-ticket fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">{{ $ticket->ticket_no }}</h3>
                                @php
                                    $status = strtolower($ticket->status ?? 'open');
                                    $sClass = match($status) {
                                        'resolved', 'closed' => 'bg-success-subtle text-success border-success-subtle',
                                        'in progress' => 'bg-primary-subtle text-primary border-primary-subtle',
                                        default => 'bg-warning-subtle text-warning border-warning-subtle'
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $sClass }} border px-2.5 py-0.5 fs-11">
                                    {{ ucfirst($ticket->status ?: 'Open') }}
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Client Portal</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('client.tickets.index') }}" class="text-muted">Tickets</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">{{ $ticket->ticket_no }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('client.tickets.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1"></i>Back to Tickets
                    </a>
                </div>
            </div>

            <div class="row g-4">
                {{-- Main Ticket Details & Timeline --}}
                <div class="col-lg-8">
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="badge bg-light text-secondary border fs-11 mb-1">Subject</span>
                                <h5 class="card-title mb-0 fw-bold text-dark fs-16">{{ $ticket->subject }}</h5>
                            </div>
                            @php
                                $priority = strtolower($ticket->priority ?? 'medium');
                                $pClass = match($priority) {
                                    'critical' => 'bg-danger-subtle text-danger border-danger-subtle',
                                    'high' => 'bg-warning-subtle text-warning border-warning-subtle',
                                    'low' => 'bg-success-subtle text-success border-success-subtle',
                                    default => 'bg-info-subtle text-info border-info-subtle'
                                };
                            @endphp
                            <span class="badge {{ $pClass }} border px-2.5 py-1 fs-12">
                                Priority: {{ ucfirst($ticket->priority ?: 'Medium') }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark fs-14 mb-2"><i class="ti ti-file-text text-primary me-1"></i>Description</h6>
                                <div class="p-3 bg-light rounded-3 border text-dark fs-13 lh-base" style="white-space: pre-line;">
                                    {{ $ticket->description }}
                                </div>
                            </div>

                            @if ($ticket->admin_note)
                                <div class="border rounded-3 p-3.5 bg-info-subtle border-info-subtle mb-4">
                                    <h6 class="fw-bold text-info fs-14 mb-1.5"><i class="ti ti-info-circle me-1"></i>Support Team Note</h6>
                                    <p class="mb-0 text-dark fs-13 lh-base">{{ $ticket->admin_note }}</p>
                                </div>
                            @endif

                            @if ($ticket->attachment_path)
                                <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-sm bg-primary text-white rounded d-inline-flex align-items-center justify-content-center">
                                            <i class="ti ti-paperclip fs-16"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold text-dark fs-13">Attached Document / Screenshot</div>
                                            <span class="fs-11 text-muted">Uploaded with ticket</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $ticket->attachment_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ti ti-download me-1"></i>View Attachment
                                    </a>
                                </div>
                            @endif

                            @if (View::exists('tickets.partials.response-timeline'))
                                <div class="pt-3 border-top">
                                    <h6 class="fw-bold text-dark fs-14 mb-3"><i class="ti ti-messages text-primary me-1"></i>Ticket Thread & Updates</h6>
                                    @include('tickets.partials.response-timeline', ['ticket' => $ticket])
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar Summary Details --}}
                <div class="col-lg-4">
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-white border-bottom py-3 px-3.5">
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="ti ti-info-square-rounded text-primary me-2 fs-18"></i>Ticket Summary
                            </h6>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="info-row">
                                <span class="info-label"><i class="ti ti-folder me-1 text-muted"></i>Project</span>
                                <span class="info-val">{{ $ticket->project?->project_name ?? ($ticket->project_name ?? 'General Inquiry') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="ti ti-alert-triangle me-1 text-muted"></i>Priority</span>
                                <span class="info-val">{{ ucfirst($ticket->priority ?: 'Medium') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="ti ti-activity me-1 text-muted"></i>Status</span>
                                <span class="info-val">{{ ucfirst($ticket->status ?: 'Open') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="ti ti-calendar me-1 text-muted"></i>Created</span>
                                <span class="info-val">{{ $ticket->created_at ? $ticket->created_at->format('d M Y, h:i A') : '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="ti ti-clock-check me-1 text-muted"></i>Last Update</span>
                                <span class="info-val">{{ $ticket->updated_at ? $ticket->updated_at->format('d M Y, h:i A') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Help & Contact Box --}}
                    <div class="card border-0 rounded-3 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="avatar avatar-sm rounded-circle bg-white text-dark d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-headset fs-16"></i>
                                </span>
                                <h6 class="text-white mb-0 fw-bold">Need Further Assistance?</h6>
                            </div>
                            <p class="fs-12 text-white-50 mb-3">Our support team reviews updates in real-time. You will be notified whenever there is progress on this ticket.</p>
                            <a href="{{ route('client.tickets.create') }}" class="btn btn-sm btn-light w-100 fw-semibold text-dark">
                                <i class="ti ti-plus me-1"></i>Create Another Ticket
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
