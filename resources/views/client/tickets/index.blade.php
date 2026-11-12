@extends('client.layout.layout')
@section('title', $title ?? 'Project Issue Tickets')

@section('main-section')
    <style>
        .client-tickets-page .stat-kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .client-tickets-page .stat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .client-tickets-page .stat-kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, #0d6efd);
        }

        .client-tickets-page .kpi-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--card-soft-bg, #eef5ff);
            color: var(--card-accent, #0d6efd);
            flex-shrink: 0;
        }

        .client-tickets-page .tickets-table-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            background: #ffffff;
        }

        .client-tickets-page .filter-bar-card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 14px 16px;
        }

        .client-tickets-page .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .client-tickets-page .status-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .client-tickets-page .badge-pill-soft {
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 11px;
            letter-spacing: 0.2px;
            display: inline-flex;
            align-items: center;
        }
    </style>

    <div class="page-wrapper client-tickets-page">
        <div class="content">

            {{-- Header & Breadcrumb --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-ticket fs-20"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h3 class="mb-0 text-dark fw-bold">{{ $title }}</h3>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 fs-11">
                                    {{ $totalCount }} Total
                                </span>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 fs-12 mt-0.5">
                                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}" class="text-muted"><i class="ti ti-home-2 me-1"></i>Client Portal</a></li>
                                    <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Support Tickets</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('client.tickets.create') }}" class="btn btn-primary btn-md shadow-sm fw-semibold d-inline-flex align-items-center">
                        <i class="ti ti-circle-plus me-1.5 fs-16"></i>Raise New Ticket
                    </a>
                </div>
            </div>

            {{-- 4 Stat / Filter KPI Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('client.tickets.index') }}" class="stat-kpi-card h-100" style="--card-accent: #0d6efd; --card-soft-bg: #eef5ff;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">All Tickets</span>
                                <h3 class="mb-0 mt-1 fw-bold text-dark">{{ number_format($totalCount) }}</h3>
                                <span class="badge bg-light text-secondary border fs-11 mt-1">Total Inquiries</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-ticket"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Open --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('client.tickets.index', ['status' => 'Open']) }}" class="stat-kpi-card h-100" style="--card-accent: #f59e0b; --card-soft-bg: #fffbeb;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Open & Pending</span>
                                <h3 class="mb-0 mt-1 fw-bold text-warning">{{ number_format($openCount) }}</h3>
                                <span class="badge bg-warning-subtle text-warning fs-11 mt-1">In Progress</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-clock-pause"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Resolved --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('client.tickets.index', ['status' => 'Resolved']) }}" class="stat-kpi-card h-100" style="--card-accent: #10b981; --card-soft-bg: #ecfdf5;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">Resolved Tickets</span>
                                <h3 class="mb-0 mt-1 fw-bold text-success">{{ number_format($resolvedCount) }}</h3>
                                <span class="badge bg-success-subtle text-success fs-11 mt-1">Successfully Closed</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-circle-check"></i></span>
                        </div>
                    </a>
                </div>

                {{-- Critical / High Priority --}}
                <div class="col-xl-3 col-sm-6">
                    <a href="{{ route('client.tickets.index', ['priority' => 'High']) }}" class="stat-kpi-card h-100" style="--card-accent: #ef4444; --card-soft-bg: #fef2f2;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-13 fw-medium">High / Critical</span>
                                <h3 class="mb-0 mt-1 fw-bold text-danger">{{ number_format($criticalCount) }}</h3>
                                <span class="badge bg-danger-subtle text-danger fs-11 mt-1">Urgent Matters</span>
                            </div>
                            <span class="kpi-icon-box"><i class="ti ti-flame"></i></span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Filter & Search Form --}}
            <div class="filter-bar-card mb-4">
                <form action="{{ route('client.tickets.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-lg-5 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="ti ti-search"></i></span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search ?? '' }}" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Search by ticket no, subject, project or keyword...">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">-- All Statuses --</option>
                            <option value="Open" @selected(($selectedStatus ?? '') === 'Open')>Open</option>
                            <option value="In Progress" @selected(($selectedStatus ?? '') === 'In Progress')>In Progress</option>
                            <option value="Resolved" @selected(($selectedStatus ?? '') === 'Resolved')>Resolved</option>
                            <option value="Closed" @selected(($selectedStatus ?? '') === 'Closed')>Closed</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <select name="priority" class="form-select" onchange="this.form.submit()">
                            <option value="">-- All Priorities --</option>
                            <option value="Critical" @selected(($selectedPriority ?? '') === 'Critical')>Critical</option>
                            <option value="High" @selected(($selectedPriority ?? '') === 'High')>High</option>
                            <option value="Medium" @selected(($selectedPriority ?? '') === 'Medium')>Medium</option>
                            <option value="Low" @selected(($selectedPriority ?? '') === 'Low')>Low</option>
                        </select>
                    </div>

                    <div class="col-lg-1 col-md-12 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100" title="Apply Filter">
                            <i class="ti ti-filter"></i>
                        </button>
                        @if(!empty($search) || !empty($selectedStatus) || !empty($selectedPriority))
                            <a href="{{ route('client.tickets.index') }}" class="btn btn-light border" title="Clear Filters">
                                <i class="ti ti-rotate-clockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tickets Table --}}
            <div class="card tickets-table-card">
                <div class="card-header bg-transparent py-3 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">Ticket Inquiries</h5>
                        <span class="badge bg-light text-secondary border fs-11">{{ $tickets->total() }} Records</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 140px;">Ticket No</th>
                                    <th>Project / Module</th>
                                    <th>Subject & Summary</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th style="width: 100px;" class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <a href="{{ route('client.tickets.show', $ticket) }}" class="badge bg-light text-primary border font-monospace fs-12 text-decoration-none">
                                                <i class="ti ti-ticket me-1"></i>{{ $ticket->ticket_no }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs bg-primary-subtle text-primary rounded d-inline-flex align-items-center justify-content-center">
                                                    <i class="ti ti-folder fs-12"></i>
                                                </span>
                                                <div>
                                                    <span class="fw-semibold text-dark fs-13">
                                                        {{ $ticket->project?->project_name ?? ($ticket->project_name ?? 'General Inquiry') }}
                                                    </span>
                                                    @if($ticket->project?->project_category)
                                                        <div class="fs-11 text-muted">{{ $ticket->project->project_category }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark fs-13">{{ $ticket->subject }}</div>
                                            @if(!empty($ticket->description))
                                                <div class="fs-11 text-muted text-truncate" style="max-width: 320px;" title="{{ $ticket->description }}">
                                                    {{ Str::limit($ticket->description, 60) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $priority = strtolower($ticket->priority ?? 'medium');
                                                $pClass = match($priority) {
                                                    'critical' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                    'high' => 'bg-warning-subtle text-warning border-warning-subtle',
                                                    'low' => 'bg-success-subtle text-success border-success-subtle',
                                                    default => 'bg-info-subtle text-info border-info-subtle'
                                                };
                                                $pIcon = match($priority) {
                                                    'critical' => 'ti ti-flame',
                                                    'high' => 'ti ti-alert-triangle',
                                                    'low' => 'ti ti-clock',
                                                    default => 'ti ti-info-circle'
                                                };
                                            @endphp
                                            <span class="badge-pill-soft {{ $pClass }} border">
                                                <i class="{{ $pIcon }} me-1"></i>{{ ucfirst($ticket->priority ?: 'Medium') }}
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
                                                $dotColor = match($status) {
                                                    'resolved', 'closed' => '#10b981',
                                                    'in progress' => '#0d6efd',
                                                    default => '#f59e0b'
                                                };
                                            @endphp
                                            <span class="badge-pill-soft {{ $sClass }} border">
                                                <span class="status-pulse-dot" style="background-color: {{ $dotColor }};"></span>
                                                {{ ucfirst($ticket->status ?: 'Open') }}
                                            </span>
                                        </td>
                                        <td class="fs-12 text-muted">
                                            <div>{{ $ticket->created_at ? $ticket->created_at->format('d M Y') : '-' }}</div>
                                            <div class="fs-11 text-muted">{{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '' }}</div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('client.tickets.show', $ticket) }}" class="btn btn-xs btn-outline-primary fw-semibold" title="View Ticket Thread">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="ti ti-ticket-off fs-40 text-muted d-block mb-2"></i>
                                            <h5>No support tickets found</h5>
                                            <p class="fs-13 text-muted mb-3">You haven't raised any tickets matching the selected filters.</p>
                                            <a href="{{ route('client.tickets.create') }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-plus me-1"></i>Raise a Support Ticket
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($tickets->hasPages())
                    <div class="card-footer bg-transparent py-3 d-flex justify-content-end">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
