@extends('layout.master')
@section('title', $title)

@section('main-section')
    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ $ticket->ticket_no }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">Administration</li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Client Tickets</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_no }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="mb-2">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-white">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ $ticket->subject }}</h5>
                            <span class="badge bg-info">{{ $ticket->status }}</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Issue Description</h6>
                                <p class="mb-0">{{ $ticket->description }}</p>
                            </div>

                            @include('tickets.partials.response-timeline', ['ticket' => $ticket])

                            <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                @foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Assign To</label>
                                            <select name="assigned_employee_id" class="form-control">
                                                <option value="">Unassigned</option>
                                                @foreach ($assignableEmployees as $employee)
                                                    <option value="{{ $employee->employee_id }}" @selected((int) old('assigned_employee_id', $ticket->assigned_employee_id) === (int) $employee->employee_id)>
                                                        {{ $employee->employee_name }}{{ !empty($employee->project_role) ? ' - ' . $employee->project_role : '' }} ({{ $employee->employee_email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($projectEmployees->isNotEmpty())
                                                <small class="text-muted">Only employees mapped with this project are shown.</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Support Note</label>
                                            <textarea name="admin_note" rows="5" class="form-control">{{ old('admin_note', $ticket->admin_note) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Ticket</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="mb-0">Ticket Summary</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Client:</strong> {{ $ticket->client?->full_name ?: $ticket->client?->company_name }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $ticket->client?->email }}</p>
                            <p class="mb-2"><strong>Project:</strong> {{ $ticket->project?->project_name ?? $ticket->project_name ?? 'General' }}</p>
                            <p class="mb-2"><strong>Assigned To:</strong> {{ $ticket->assignedEmployee?->employee_name ?? 'Unassigned' }}</p>
                            <p class="mb-2"><strong>Employee Status:</strong> {{ $ticket->employee_status ?? 'Assigned' }}</p>
                            <p class="mb-2"><strong>Total Work Time:</strong> {{ $ticket->formattedTotalWorkTime() }}</p>
                            <p class="mb-2"><strong>Resolution Time:</strong> {{ $ticket->formattedResolutionTime() }}</p>
                            <p class="mb-2"><strong>Elapsed Time:</strong> {{ $ticket->formattedOpenTime() }}</p>
                            @if ($ticket->assigned_at)
                                <p class="mb-2"><strong>Assigned At:</strong> {{ $ticket->assigned_at->format('d M Y, h:i A') }}</p>
                            @endif
                            <p class="mb-2"><strong>Priority:</strong> {{ $ticket->priority }}</p>
                            <p class="mb-2"><strong>Created:</strong> {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
                            <p class="mb-2"><strong>Updated:</strong> {{ $ticket->updated_at->format('d M Y, h:i A') }}</p>
                            @if ($ticket->closed_at)
                                <p class="mb-2"><strong>Closed:</strong> {{ $ticket->closed_at->format('d M Y, h:i A') }}</p>
                            @endif
                            @if ($ticket->attachment_path)
                                <p class="mb-0">
                                    <strong>Attachment:</strong>
                                    <a href="{{ asset('storage/' . $ticket->attachment_path) }}" >View file</a>
                                </p>
                            @endif
                            @if ($projectEmployees->isNotEmpty())
                                <hr>
                                <h6>Project Team</h6>
                                @foreach ($projectEmployees as $employee)
                                    <p class="mb-2">
                                        {{ $employee->employee_name }}
                                        @if (!empty($employee->project_role))
                                            <span class="text-muted">({{ $employee->project_role }})</span>
                                        @endif
                                        <br>
                                        <span class="text-muted small">{{ $employee->employee_email }}</span>
                                    </p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
