@extends('employee.layout.layout')
@section('title', $title)

@section('main-section')
    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $title }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">Employee</li>
                            <li class="breadcrumb-item active">Tickets</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                @foreach ($counts as $status => $count)
                    <div class="col-xl-3 col-md-6 d-flex">
                        <a href="{{ route('employee.tickets.index', ['status' => $status]) }}" class="card flex-fill text-decoration-none">
                            <div class="card-body">
                                <p class="fs-12 fw-medium mb-1 text-gray-5">{{ $status }}</p>
                                <h3 class="mb-0">{{ $count }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Tickets</h5>
                    @if ($selectedStatus)
                        <a href="{{ route('employee.tickets.index') }}" class="btn btn-sm btn-white">Clear Filter</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Project</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Employee Status</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_no }}</td>
                                        <td>{{ $ticket->project?->project_name ?? $ticket->project_name ?? 'General' }}</td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>{{ $ticket->priority }}</td>
                                        <td><span class="badge bg-info">{{ $ticket->status }}</span></td>
                                        <td>{{ $ticket->employee_status ?? 'Assigned' }}</td>
                                        <td>{{ $ticket->assignedEmployee?->employee_name ?? 'Project Team' }}</td>
                                        <td>{{ $ticket->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('employee.tickets.show', $ticket) }}" class="btn btn-sm btn-white">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No tickets assigned or mapped with your projects.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($tickets->hasPages())
                    <div class="card-footer">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
