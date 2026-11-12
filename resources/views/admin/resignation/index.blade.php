@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Top Bar -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="mb-1 font-weight-bold text-dark">{{ $title }}</h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="ti ti-smart-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Resignations &amp; Exits</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.resignation.export', request()->query()) }}" class="btn btn-outline-success d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-file-spreadsheet me-1.5 fs-16"></i> Export CSV Report
                </a>
                <a href="{{ route('admin.resignation.create') }}" class="btn btn-primary d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-user-minus me-1.5 fs-16"></i> Initiate Separation / Exit
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-circle-check fs-20 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-20 me-2"></i>
                    <div>{{ session('warning') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle fs-20 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-lg-4 col-sm-6">
                <a href="{{ route('admin.resignation.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 12px; transition: transform .15s ease;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-12 fw-semibold text-uppercase">Total Requests</span>
                                <span class="avatar avatar-sm rounded-circle bg-light text-primary d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-files fs-16"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalCount) }}</h3>
                            <small class="text-muted">All-time separation cases</small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-lg-4 col-sm-6">
                <a href="{{ route('admin.resignation.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 12px; transition: transform .15s ease; border-left: 4px solid #ffab00 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-warning fs-12 fw-semibold text-uppercase">Pending Review</span>
                                <span class="avatar avatar-sm rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-clock-pause fs-16"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($pendingCount) }}</h3>
                            <small class="text-muted">Awaiting decision</small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-sm-6">
                <a href="{{ route('admin.resignation.index', ['status' => 'approved']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 12px; transition: transform .15s ease; border-left: 4px solid #00b074 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-success fs-12 fw-semibold text-uppercase">Serving Notice</span>
                                <span class="avatar avatar-sm rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-calendar-check fs-16"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($approvedCount) }}</h3>
                            <small class="text-muted">Approved &bull; active notice</small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-2 col-lg-4 col-sm-6">
                <a href="{{ route('admin.resignation.index', ['status' => 'rejected']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 12px; transition: transform .15s ease; border-left: 4px solid #ff3e1d !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-danger fs-12 fw-semibold text-uppercase">Rejected</span>
                                <span class="avatar avatar-sm rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-x fs-16"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($rejectedCount) }}</h3>
                            <small class="text-muted">Declined / Revoked</small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-sm-6">
                <a href="{{ route('admin.resignation.index', ['status' => 'completed']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 12px; transition: transform .15s ease; border-left: 4px solid #696cff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-primary fs-12 fw-semibold text-uppercase">Completed Exits</span>
                                <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-user-x fs-16"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($completedCount) }}</h3>
                            <small class="text-muted">Relieved &amp; Deactivated</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.resignation.index') }}" class="row g-2 align-items-end">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">Search Keywords</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm border-start-0" placeholder="Code, Name, Email, Reason...">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Notice)</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Relieved)</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>

                    <!-- Initiated By -->
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">Initiated By</label>
                        <select name="initiated_by" class="form-select form-select-sm">
                            <option value="all" {{ request('initiated_by') === 'all' || !request('initiated_by') ? 'selected' : '' }}>All Initiators</option>
                            <option value="employee" {{ request('initiated_by') === 'employee' ? 'selected' : '' }}>Employee (Self)</option>
                            <option value="supervisor" {{ request('initiated_by') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="admin" {{ request('initiated_by') === 'admin' ? 'selected' : '' }}>Admin / HR</option>
                        </select>
                    </div>

                    <!-- Supervisor Filter -->
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">Supervisor</label>
                        <select name="supervisor_id" class="form-select form-select-sm">
                            <option value="all">All Supervisors</option>
                            @foreach ($supervisors as $sup)
                                <option value="{{ $sup->employee_id }}" {{ request('supervisor_id') == $sup->employee_id ? 'selected' : '' }}>
                                    {{ $sup->employee_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Agency Filter -->
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">Agency</label>
                        <select name="agency_id" class="form-select form-select-sm">
                            <option value="all">All Agencies</option>
                            <option value="unassigned" {{ request('agency_id') === 'unassigned' ? 'selected' : '' }}>Direct (No Agency)</option>
                            @foreach ($agencies as $ag)
                                <option value="{{ $ag->id }}" {{ request('agency_id') == $ag->id ? 'selected' : '' }}>
                                    {{ $ag->agency_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From / To -->
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 text-muted fw-semibold mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm">
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="ti ti-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.resignation.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resignations Data Table Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">Resignation &amp; Exit Records</h5>
                    <small class="text-muted">Showing {{ $resignations->firstItem() ?? 0 }} to {{ $resignations->lastItem() ?? 0 }} of {{ $resignations->total() }} records</small>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted fs-12 text-uppercase">
                            <tr>
                                <th class="ps-4">Code &amp; Initiator</th>
                                <th>Employee</th>
                                <th>Supervisor &amp; Agency</th>
                                <th>Reason</th>
                                <th>Submission &amp; Notice</th>
                                <th>Last Working Day</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse ($resignations as $res)
                                <tr>
                                    <!-- Code & Initiator -->
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary mb-1">
                                            <a href="{{ route('admin.resignation.show', $res->id) }}" class="text-decoration-none">
                                                {{ $res->resignation_code }}
                                            </a>
                                        </div>
                                        <div>{!! $res->initiated_by_badge !!}</div>
                                    </td>

                                    <!-- Employee Info -->
                                    <td>
                                        <div class="fw-bold text-dark">{{ $res->employee?->employee_name ?? 'Unknown Employee' }}</div>
                                        <div class="text-muted fs-12">{{ $res->employee?->employee_id }} &bull; {{ $res->employee?->designation?->name ?? 'Staff' }}</div>
                                        <small class="text-muted">{{ $res->employee?->employee_email }}</small>
                                    </td>

                                    <!-- Supervisor & Agency -->
                                    <td>
                                        <div class="text-dark fw-semibold fs-13">
                                            <i class="ti ti-user-check text-muted me-1"></i>
                                            {{ $res->supervisor?->employee_name ?? 'No Supervisor' }}
                                        </div>
                                        <div class="text-muted fs-12">
                                            <i class="ti ti-building text-muted me-1"></i>
                                            {{ $res->employee?->agency?->agency_name ?? 'Direct Employment' }}
                                        </div>
                                    </td>

                                    <!-- Reason -->
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $res->reason }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $res->reason_details }}">
                                            {{ Str::limit($res->reason_details, 40) }}
                                        </small>
                                    </td>

                                    <!-- Submission Date & Notice -->
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $res->resignation_date->format('d M, Y') }}</div>
                                        <small class="text-muted">{{ $res->notice_period_days }} Days Notice</small>
                                    </td>

                                    <!-- Last Working Day & Days Left -->
                                    <td>
                                        <div>
                                            <span class="text-muted fs-11 text-uppercase fw-semibold">Req:</span>
                                            <span class="fw-semibold text-dark">{{ $res->requested_last_working_day->format('d M, Y') }}</span>
                                        </div>
                                        @if ($res->approved_last_working_day)
                                            <div>
                                                <span class="text-success fs-11 text-uppercase fw-semibold">Appr:</span>
                                                <span class="fw-bold text-success">{{ $res->approved_last_working_day->format('d M, Y') }}</span>
                                            </div>
                                            @php $rem = $res->daysRemaining(); @endphp
                                            @if ($res->status === 'approved' && $rem !== null)
                                                <small class="badge @if($rem <= 5) bg-danger-subtle text-danger @else bg-success-subtle text-success @endif px-1.5 py-0.5 rounded">
                                                    @if($rem > 0) {{ $rem }}d left @elseif($rem === 0) Last Day Today @else Overdue @endif
                                                </small>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Status Badge -->
                                    <td>
                                        {!! $res->status_badge !!}
                                        @if ($res->action_by_name)
                                            <div class="fs-11 text-muted mt-1">by {{ $res->action_by_name }}</div>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <a class="dropdown-item fs-13" href="{{ route('admin.resignation.show', $res->id) }}">
                                                        <i class="ti ti-eye me-2 text-primary"></i> View Record
                                                    </a>
                                                </li>

                                                @if (in_array($res->status, ['pending', 'in_review'], true))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item fs-13 text-success" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#respondModal{{ $res->id }}" data-decision="approved">
                                                            <i class="ti ti-check me-2"></i> Accept / Approve
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item fs-13 text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#respondModal{{ $res->id }}" data-decision="rejected">
                                                            <i class="ti ti-x me-2"></i> Reject Resignation
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($res->status === 'approved')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item fs-13 text-danger fw-semibold" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#completeModal{{ $res->id }}">
                                                            <i class="ti ti-user-x me-2"></i> Complete Exit &amp; Relieve
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <!-- Respond Modal (Accept / Reject) -->
                                        @if (in_array($res->status, ['pending', 'in_review'], true))
                                            <div class="modal fade text-start" id="respondModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                                                        <form method="POST" action="{{ route('admin.resignation.respond', $res->id) }}">
                                                            @csrf
                                                            <div class="modal-header border-bottom py-3 px-4">
                                                                <h5 class="modal-title fw-bold text-dark">
                                                                    <i class="ti ti-scale text-primary me-1"></i> Review Resignation - {{ $res->resignation_code }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="p-3 bg-light rounded-3 mb-3 fs-13">
                                                                    <div class="fw-bold text-dark mb-1">{{ $res->employee?->employee_name }} ({{ $res->employee?->employee_id }})</div>
                                                                    <div class="text-muted">Reason: <strong>{{ $res->reason }}</strong></div>
                                                                    <div class="text-muted">Requested LWD: <strong>{{ $res->requested_last_working_day->format('d M, Y') }}</strong> ({{ $res->notice_period_days }} Days Notice)</div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fs-13 fw-semibold text-dark">Decision <span class="text-danger">*</span></label>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input decision-radio" type="radio" name="decision" id="dec_app_{{ $res->id }}" value="approved" checked>
                                                                            <label class="form-check-label text-success fw-bold" for="dec_app_{{ $res->id }}">
                                                                                <i class="ti ti-check me-1"></i> Accept &amp; Approve
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input decision-radio" type="radio" name="decision" id="dec_rej_{{ $res->id }}" value="rejected">
                                                                            <label class="form-check-label text-danger fw-bold" for="dec_rej_{{ $res->id }}">
                                                                                <i class="ti ti-x me-1"></i> Reject Resignation
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3" id="approvedLwdGroup{{ $res->id }}">
                                                                    <label class="form-label fs-13 fw-semibold text-dark">
                                                                        Approved Last Working Day <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="date" name="approved_last_working_day" class="form-control fs-13" value="{{ $res->requested_last_working_day->format('Y-m-d') }}">
                                                                    <small class="text-muted">You may retain requested LWD or adjust according to handover requirements.</small>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fs-13 fw-semibold text-dark">
                                                                        Official Remarks / Reason for Decision <span class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea name="action_remarks" rows="3" class="form-control fs-13" required placeholder="State remarks, handover instructions, or rationale for this decision..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top py-3 px-4 bg-light">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Submit Decision &amp; Notify</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Complete Exit Modal -->
                                        @if ($res->status === 'approved')
                                            <div class="modal fade text-start" id="completeModal{{ $res->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                                                        <form method="POST" action="{{ route('admin.resignation.complete-exit', $res->id) }}">
                                                            @csrf
                                                            <div class="modal-header border-bottom py-3 px-4 bg-danger text-white">
                                                                <h5 class="modal-title fw-bold text-white">
                                                                    <i class="ti ti-alert-triangle me-1"></i> Complete Exit &amp; Relieve Employee
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="alert alert-warning border-0 mb-3 fs-13">
                                                                    <i class="ti ti-info-circle me-1"></i>
                                                                    <strong>Important:</strong> Relieving employee <strong>{{ $res->employee?->employee_name }}</strong> will set their status to <strong>Inactive</strong> across the system, revoke API tokens, and deactivate their portal login.
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fs-13 fw-semibold text-dark">Exit Feedback Rating (1 to 5)</label>
                                                                    <select name="feedback_rating" class="form-select form-select-sm">
                                                                        <option value="">Select Rating (Optional)</option>
                                                                        <option value="5">5 - Excellent Exit / Strong Asset</option>
                                                                        <option value="4">4 - Good Exit / Satisfactory</option>
                                                                        <option value="3">3 - Average</option>
                                                                        <option value="2">2 - Below Average</option>
                                                                        <option value="1">1 - Unsatisfactory</option>
                                                                    </select>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fs-13 fw-semibold text-dark">Handover &amp; Relieving Notes (Optional)</label>
                                                                    <textarea name="feedback_notes" rows="3" class="form-control fs-13" placeholder="Assets returned, documentation handed over, clearance signed..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top py-3 px-4 bg-light">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Yes, Relieve &amp; Deactivate</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="ti ti-file-text fs-36 text-muted"></i></div>
                                        <p class="mb-1 fw-semibold fs-14">No resignation or exit records found.</p>
                                        <small class="text-muted">Try adjusting your filters or initiate a new exit using the button above.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($resignations->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $resignations->links() }}
                </div>
            @endif
        </div>

    </div>

    <x-footer />
</div>

@endsection
