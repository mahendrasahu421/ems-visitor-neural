@extends('client.layout.layout')
@section('title', $title ?? 'Client Dashboard')

@section('main-section')
    <style>
        .client-portal-dashboard .welcome-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 16px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
        }

        .client-portal-dashboard .welcome-banner::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.22) 0%, rgba(13, 110, 253, 0) 70%);
            pointer-events: none;
        }

        .client-portal-dashboard .stat-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
        }

        .client-portal-dashboard .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .client-portal-dashboard .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-theme, #0d6efd);
        }

        .client-portal-dashboard .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--card-soft-bg, #eef5ff);
            color: var(--card-theme, #0d6efd);
            flex-shrink: 0;
        }

        .client-portal-dashboard .card-custom {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .client-portal-dashboard .project-item-card {
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .client-portal-dashboard .project-item-card:hover {
            border-color: var(--bs-primary);
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .client-portal-dashboard .info-tile {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid #edf2f7;
        }

        .client-portal-dashboard .info-tile-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .client-portal-dashboard .info-tile-value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .client-portal-dashboard .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }
    </style>

    <div class="page-wrapper client-portal-dashboard">
        <div class="content">

            {{-- Welcome Banner --}}
            <div class="welcome-banner p-4 mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8 col-md-12">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $displayName = $client?->full_name ?: ($client?->company_name ?: 'Client');
                                $words = preg_split('/\s+/', trim($displayName));
                                $initials = count($words) >= 2 && !empty($words[0]) && !empty($words[1])
                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                    : strtoupper(substr($words[0] ?? 'CL', 0, 2));
                            @endphp
                            <span class="avatar avatar-xl rounded-circle bg-primary text-white border border-2 border-white shadow-sm flex-shrink-0 d-inline-flex align-items-center justify-content-center fw-bold fs-18">
                                {{ $initials }}
                            </span>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h3 class="mb-0 fw-bold text-white">Welcome, {{ $displayName }}!</h3>
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-0.5 fs-11">
                                        <i class="ti ti-circle-filled fs-7 me-1"></i>Active Account
                                    </span>
                                </div>
                                <p class="text-white-50 fs-13 mb-0 mt-1">
                                    Client Self-Service Portal &bull; Track Projects, Inquiries & Support Tickets
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top border-white border-opacity-10 fs-12">
                            @if($client?->company_name)
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 px-2.5 py-1">
                                    <i class="ti ti-building me-1 text-info"></i>{{ $client->company_name }}
                                </span>
                            @endif
                            @if($client?->email)
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 px-2.5 py-1">
                                    <i class="ti ti-mail me-1 text-primary"></i>{{ $client->email }}
                                </span>
                            @endif
                            @if($client?->gst_number)
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 px-2.5 py-1 font-monospace">
                                    <i class="ti ti-receipt-tax me-1 text-warning"></i>GST: {{ $client->gst_number }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12 d-flex justify-content-lg-end">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('client.tickets.create') }}" class="btn btn-primary btn-md shadow-sm fw-semibold">
                                <i class="ti ti-circle-plus me-1.5 fs-16"></i>Raise Support Ticket
                            </a>
                            <a href="{{ route('client.tickets.index') }}" class="btn btn-outline-light btn-md fw-semibold">
                                <i class="ti ti-ticket me-1.5"></i>My Tickets
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4 Stat Metric Cards --}}
            <div class="row g-3 mb-4">
                {{-- Projects --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="card stat-card h-100 mb-0" style="--card-theme: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Assigned Projects</span>
                                <h3 class="mb-1 mt-1 fw-bold text-dark">{{ number_format($projectCount) }}</h3>
                                <span class="badge bg-primary-subtle text-primary fs-11">Active Deliverables</span>
                            </div>
                            <span class="icon-box"><i class="ti ti-briefcase"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Open Tickets --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="card stat-card h-100 mb-0" style="--card-theme: #f59e0b; --card-soft-bg: #fffbeb;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Open Tickets</span>
                                <h3 class="mb-1 mt-1 fw-bold text-warning">{{ number_format($openTicketCount) }}</h3>
                                <span class="badge bg-warning-subtle text-warning fs-11">Pending Resolution</span>
                            </div>
                            <span class="icon-box"><i class="ti ti-headset"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Resolved Tickets --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="card stat-card h-100 mb-0" style="--card-theme: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Resolved Tickets</span>
                                <h3 class="mb-1 mt-1 fw-bold text-success">{{ number_format($resolvedTicketCount) }}</h3>
                                <span class="badge bg-success-subtle text-success fs-11">Successfully Closed</span>
                            </div>
                            <span class="icon-box"><i class="ti ti-circle-check"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Total Tickets --}}
                <div class="col-xl-3 col-sm-6">
                    <div class="card stat-card h-100 mb-0" style="--card-theme: #6366f1; --card-soft-bg: #eef2ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Total Inquiries</span>
                                <h3 class="mb-1 mt-1 fw-bold text-dark">{{ number_format($totalTicketCount) }}</h3>
                                <span class="badge bg-light text-secondary border fs-11">All Time Tickets</span>
                            </div>
                            <span class="icon-box"><i class="ti ti-messages"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Middle Section: Assigned Projects & Profile Info --}}
            <div class="row g-3 mb-4">
                {{-- Projects List --}}
                <div class="col-lg-7 d-flex">
                    <div class="card card-custom flex-fill">
                        <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-briefcase fs-14"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark">Active Engagements & Projects</h6>
                            </div>
                            <span class="badge bg-light text-secondary border fs-11">{{ $projects->count() }} Total</span>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="d-flex flex-column gap-3">
                                @forelse ($projects as $project)
                                    <div class="project-item-card">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1 fs-14">{{ $project->project_name }}</h6>
                                                <span class="badge bg-primary-subtle text-primary font-monospace fs-11">
                                                    {{ $project->project_category ?: ($project->work_type ?: 'Project') }}
                                                </span>
                                            </div>
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-11">
                                                {{ ucfirst($project->status ?: 'Active') }}
                                            </span>
                                        </div>
                                        <div class="row g-2 mt-1 fs-12 text-muted">
                                            @if($project->project_start_date)
                                                <div class="col-sm-6">
                                                    <i class="ti ti-calendar text-muted me-1"></i>
                                                    <span>Start: <strong>{{ \Carbon\Carbon::parse($project->project_start_date)->format('d M, Y') }}</strong></span>
                                                </div>
                                            @endif
                                            @if($project->project_end_date)
                                                <div class="col-sm-6">
                                                    <i class="ti ti-calendar-due text-muted me-1"></i>
                                                    <span>Target: <strong>{{ \Carbon\Carbon::parse($project->project_end_date)->format('d M, Y') }}</strong></span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-5">
                                        <i class="ti ti-folder-off fs-40 text-muted d-block mb-2"></i>
                                        No projects currently assigned to this portal account.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Client Account Profile Card --}}
                <div class="col-lg-5 d-flex">
                    <div class="card card-custom flex-fill">
                        <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-xs bg-info-subtle text-info rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-user-circle fs-14"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark">Client Profile & Billing Info</h6>
                            </div>
                            <span class="badge bg-light text-secondary border font-monospace fs-11">#{{ $client?->id }}</span>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="d-flex flex-column gap-2.5">
                                <div class="info-tile">
                                    <div class="info-tile-label"><i class="ti ti-building me-1 text-primary"></i>Company / Organization</div>
                                    <div class="info-tile-value">{{ $client?->company_name ?: ($client?->client_name ?: '-') }}</div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="info-tile">
                                            <div class="info-tile-label"><i class="ti ti-user me-1 text-success"></i>Concern Person</div>
                                            <div class="info-tile-value">{{ $client?->concern_person ?: ($client?->full_name ?: '-') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-tile">
                                            <div class="info-tile-label"><i class="ti ti-phone me-1 text-info"></i>Phone / Mobile</div>
                                            <div class="info-tile-value">{{ $client?->mobile ?: ($client?->phone ?: '-') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-tile">
                                    <div class="info-tile-label"><i class="ti ti-mail me-1 text-danger"></i>Email Address</div>
                                    <div class="info-tile-value">{{ $client?->email ?: '-' }}</div>
                                </div>
                                @if($client?->gst_number || $client?->cin)
                                    <div class="row g-2">
                                        @if($client?->gst_number)
                                            <div class="col-sm-6">
                                                <div class="info-tile">
                                                    <div class="info-tile-label"><i class="ti ti-receipt-tax me-1 text-warning"></i>GST Number</div>
                                                    <div class="info-tile-value font-monospace fs-12">{{ $client->gst_number }}</div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($client?->cin)
                                            <div class="col-sm-6">
                                                <div class="info-tile">
                                                    <div class="info-tile-label"><i class="ti ti-id me-1 text-secondary"></i>CIN Number</div>
                                                    <div class="info-tile-value font-monospace fs-12">{{ $client->cin }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if($client?->address)
                                    <div class="info-tile">
                                        <div class="info-tile-label"><i class="ti ti-map-pin me-1 text-danger"></i>Billing Address</div>
                                        <div class="info-tile-value fs-12 text-muted fw-normal">{{ $client->address }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Support Tickets --}}
            <div class="card card-custom">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-xs bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-headset fs-14"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-dark">Recent Support Tickets</h6>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('client.tickets.create') }}" class="btn btn-xs btn-primary">
                            <i class="ti ti-plus me-1"></i>New Ticket
                        </a>
                        <a href="{{ route('client.tickets.index') }}" class="btn btn-xs btn-outline-secondary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 120px;">Ticket No</th>
                                    <th>Project</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th style="width: 90px;" class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentTickets as $ticket)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-primary border font-monospace fs-12">
                                                {{ $ticket->ticket_no }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark fs-13">
                                                {{ $ticket->project?->project_name ?? ($ticket->project_name ?? 'General Inquiry') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-medium text-dark fs-13">{{ $ticket->subject }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $priority = strtolower($ticket->priority ?? 'medium');
                                                $pClass = match($priority) {
                                                    'high', 'urgent' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                    'low' => 'bg-info-subtle text-info border-info-subtle',
                                                    default => 'bg-warning-subtle text-warning border-warning-subtle'
                                                };
                                            @endphp
                                            <span class="badge {{ $pClass }} border fs-11">
                                                {{ ucfirst($ticket->priority ?: 'Medium') }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = strtolower($ticket->status ?? 'open');
                                                $sClass = match($status) {
                                                    'resolved', 'closed' => 'bg-success-subtle text-success border-success-subtle',
                                                    'in progress' => 'bg-primary-subtle text-primary border-primary-subtle',
                                                    default => 'bg-warning-subtle text-warning border-warning-subtle'
                                                };
                                            @endphp
                                            <span class="badge rounded-pill {{ $sClass }} border px-2 py-0.5 fs-11">
                                                {{ ucfirst($ticket->status ?: 'Open') }}
                                            </span>
                                        </td>
                                        <td class="fs-12 text-muted">
                                            {{ $ticket->created_at ? $ticket->created_at->format('d M, Y') : '-' }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('client.tickets.show', $ticket) }}" class="btn btn-xs btn-outline-primary" title="View Ticket">
                                                <i class="ti ti-eye me-0.5"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="ti ti-ticket-off fs-36 text-muted d-block mb-2"></i>
                                            No support tickets raised yet. Need help?
                                            <a href="{{ route('client.tickets.create') }}" class="ms-1 text-primary fw-semibold">Raise a Ticket</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
