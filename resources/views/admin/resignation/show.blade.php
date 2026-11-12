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
                        <li class="breadcrumb-item"><a href="{{ route('admin.resignation.index') }}">Resignations &amp; Exits</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $resignation->resignation_code }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back to Report
                </a>

                @if (in_array($resignation->status, ['pending', 'in_review'], true))
                    <button type="button" class="btn btn-success d-inline-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#respondModal">
                        <i class="ti ti-check me-1"></i> Respond (Approve / Reject)
                    </button>
                @endif

                @if ($resignation->status === 'approved')
                    <button type="button" class="btn btn-danger d-inline-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#completeModal">
                        <i class="ti ti-user-x me-1"></i> Complete Exit &amp; Relieve
                    </button>
                @endif
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

        <!-- Status Overview Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
            <div class="card-body p-4 bg-gradient @if($resignation->status === 'approved') bg-success-subtle @elseif($resignation->status === 'rejected') bg-danger-subtle @elseif($resignation->status === 'completed') bg-secondary-subtle @else bg-warning-subtle @endif">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 56px; height: 56px;">
                            @if ($resignation->status === 'approved')
                                <i class="ti ti-check text-success fs-30"></i>
                            @elseif ($resignation->status === 'rejected')
                                <i class="ti ti-x text-danger fs-30"></i>
                            @elseif ($resignation->status === 'completed')
                                <i class="ti ti-certificate text-secondary fs-30"></i>
                            @else
                                <i class="ti ti-hourglass-empty text-warning fs-30"></i>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="mb-0 fw-bold text-dark">{{ $resignation->resignation_code }}</h4>
                                {!! $resignation->status_badge !!}
                                {!! $resignation->initiated_by_badge !!}
                            </div>
                            <p class="mb-0 text-muted fs-13">
                                Initiated on <strong>{{ $resignation->resignation_date->format('d M, Y') }}</strong>
                                &bull; Notice Period: <strong>{{ $resignation->notice_period_days }} Days</strong>
                            </p>
                        </div>
                    </div>

                    @if ($resignation->status === 'approved' && $resignation->effective_lwd)
                        @php $daysRem = $resignation->daysRemaining(); @endphp
                        <div class="text-md-end text-start">
                            <span class="fs-12 text-muted text-uppercase fw-semibold">Notice Status</span>
                            <div class="fs-18 fw-bold @if($daysRem !== null && $daysRem <= 7) text-danger @else text-success @endif">
                                @if ($daysRem !== null)
                                    @if ($daysRem > 0)
                                        {{ $daysRem }} {{ Str::plural('day', $daysRem) }} remaining
                                    @elseif ($daysRem === 0)
                                        Today is the Last Working Day
                                    @else
                                        Notice completed ({{ abs($daysRem) }} days ago)
                                    @endif
                                @else
                                    Active Notice
                                @endif
                            </div>
                            <small class="text-muted">Approved LWD: <strong>{{ $resignation->effective_lwd->format('d M, Y') }}</strong></small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Details & Reviews -->
            <div class="col-lg-8">
                <!-- Resignation Statement -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-file-text text-primary me-2"></i> Separation Statement &amp; Reason
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="text-muted fs-12 text-uppercase fw-semibold">Primary Reason</label>
                            <div class="fs-15 fw-bold text-dark">{{ $resignation->reason }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted fs-12 text-uppercase fw-semibold mb-2">Detailed Reason / Resignation Letter</label>
                            <div class="p-3 bg-light rounded-3 text-dark fs-14" style="white-space: pre-line; line-height: 1.6; border: 1px solid #e9ecef;">
                                {{ $resignation->reason_details }}
                            </div>
                        </div>

                        @if ($resignation->document_path)
                            <div>
                                <label class="text-muted fs-12 text-uppercase fw-semibold mb-2">Attached Document</label>
                                <div>
                                    <a href="{{ asset($resignation->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                                        <i class="ti ti-paperclip me-1 fs-15"></i> View / Download Supporting Document
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Review / Decision Record -->
                @if (in_array($resignation->status, ['approved', 'rejected', 'completed', 'withdrawn']))
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="ti ti-message-check text-info me-2"></i> Review Decision &amp; Audit Trail
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Action By</label>
                                    <div class="fs-14 fw-bold text-dark">
                                        {{ $resignation->action_by_name ?: 'HR / Management' }}
                                        <span class="badge bg-secondary-subtle text-secondary ms-1 fs-11">{{ ucfirst($resignation->action_by_type ?? 'Admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Decision Date</label>
                                    <div class="fs-14 fw-bold text-dark">
                                        {{ $resignation->action_at ? $resignation->action_at->format('d M, Y \a\t h:i A') : '-' }}
                                    </div>
                                </div>
                            </div>

                            @if ($resignation->approved_last_working_day)
                                <div class="mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Approved Last Working Day</label>
                                    <div class="fs-15 fw-bold text-success">
                                        {{ $resignation->approved_last_working_day->format('d M, Y') }}
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="text-muted fs-12 text-uppercase fw-semibold mb-1">Decision Remarks / Notes</label>
                                <div class="p-3 bg-light rounded-3 text-dark fs-13" style="line-height: 1.6; border: 1px solid #e9ecef;">
                                    {{ $resignation->action_remarks ?: 'No remarks recorded.' }}
                                </div>
                            </div>

                            @if ($resignation->status === 'completed' && ($resignation->feedback_rating || $resignation->feedback_notes))
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fw-bold text-dark mb-2"><i class="ti ti-star me-1 text-warning"></i> Exit Clearance &amp; Relieving Details</h6>
                                    @if ($resignation->feedback_rating)
                                        <p class="mb-1 fs-13"><strong>Rating:</strong> {{ $resignation->feedback_rating }} / 5</p>
                                    @endif
                                    @if ($resignation->feedback_notes)
                                        <p class="mb-0 fs-13 text-muted"><strong>Relieving Notes:</strong> {{ $resignation->feedback_notes }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Employee Profile & Timeline -->
            <div class="col-lg-4">
                <!-- Employee Profile Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-user text-primary me-2"></i> Employee Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <div class="avatar avatar-lg rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-20 fw-bold">
                                {{ strtoupper(substr($resignation->employee?->employee_name ?? 'E', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $resignation->employee?->employee_name }}</h6>
                                <span class="badge bg-light text-muted fs-11">{{ $resignation->employee?->employee_id }}</span>
                                <span class="badge @if($resignation->employee?->status === 'Active') bg-success-subtle text-success @else bg-danger-subtle text-danger @endif fs-11">
                                    {{ $resignation->employee?->status }}
                                </span>
                            </div>
                        </div>

                        <div class="fs-13 mb-2">
                            <span class="text-muted">Designation:</span>
                            <strong class="text-dark d-block">{{ $resignation->employee?->designation?->name ?? 'Staff' }}</strong>
                        </div>

                        <div class="fs-13 mb-2">
                            <span class="text-muted">Agency / Deployment:</span>
                            <strong class="text-dark d-block">{{ $resignation->employee?->agency?->agency_name ?? 'Direct Employment' }}</strong>
                        </div>

                        <div class="fs-13 mb-2">
                            <span class="text-muted">Email:</span>
                            <strong class="text-dark d-block">{{ $resignation->employee?->employee_email }}</strong>
                        </div>

                        <div class="fs-13 mb-0">
                            <span class="text-muted">Mobile:</span>
                            <strong class="text-dark d-block">{{ $resignation->employee?->employee_mobile ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Schedule & Dates Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-calendar text-primary me-2"></i> Key Dates &amp; Notice Schedule
                        </h6>
                    </div>
                    <div class="card-body p-4 fs-13">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">Resignation Date:</span>
                            <strong>{{ $resignation->resignation_date->format('d M, Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">Notice Period:</span>
                            <strong>{{ $resignation->notice_period_days }} Days</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">Requested LWD:</span>
                            <strong>{{ $resignation->requested_last_working_day->format('d M, Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Approved LWD:</span>
                            <strong class="{{ $resignation->approved_last_working_day ? 'text-success' : 'text-muted' }}">
                                {{ $resignation->approved_last_working_day ? $resignation->approved_last_working_day->format('d M, Y') : 'Pending Decision' }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- CC Email Recipients -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-mail text-secondary me-2"></i> CC Email Recipients
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="text-muted fs-11 text-uppercase fw-semibold">Supervisor</label>
                            <div class="fs-13 fw-semibold text-dark">
                                {{ $resignation->supervisor?->employee_name ?? 'Assigned Supervisor' }}
                                <span class="d-block text-muted fs-12">{{ $resignation->supervisor?->employee_email ?? 'No email on record' }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-muted fs-11 text-uppercase fw-semibold mb-1">Additional Recipients</label>
                            @if (!empty($resignation->clean_cc_emails))
                                <ul class="list-unstyled mb-0">
                                    @foreach ($resignation->clean_cc_emails as $cc)
                                        <li class="d-flex align-items-center gap-2 mb-1 fs-13 text-dark">
                                            <i class="ti ti-at text-muted fs-12"></i>
                                            <span>{{ $cc }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted fs-13 mb-0">No additional CC recipients specified.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <x-footer />
</div>

<!-- Modal: Respond (Approve / Reject) -->
@if (in_array($resignation->status, ['pending', 'in_review'], true))
    <div class="modal fade" id="respondModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <form method="POST" action="{{ route('admin.resignation.respond', $resignation->id) }}">
                    @csrf
                    <div class="modal-header border-bottom py-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="ti ti-scale text-primary me-1"></i> Review Decision - {{ $resignation->resignation_code }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold text-dark">Decision <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision" id="dec_app" value="approved" checked>
                                    <label class="form-check-label text-success fw-bold" for="dec_app">
                                        <i class="ti ti-check me-1"></i> Accept &amp; Approve
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision" id="dec_rej" value="rejected">
                                    <label class="form-check-label text-danger fw-bold" for="dec_rej">
                                        <i class="ti ti-x me-1"></i> Reject Resignation
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="approvedLwdField">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Approved Last Working Day <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="approved_last_working_day" class="form-control fs-13" value="{{ $resignation->requested_last_working_day->format('Y-m-d') }}">
                            <small class="text-muted">Adjust if required or retain requested date.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold text-dark">
                                Official Remarks / Feedback <span class="text-danger">*</span>
                            </label>
                            <textarea name="action_remarks" rows="3" class="form-control fs-13" required placeholder="State handover instructions, acceptance confirmation, or reasons for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-3 px-4 bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Decision &amp; Dispatch Mail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Modal: Complete Exit -->
@if ($resignation->status === 'approved')
    <div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <form method="POST" action="{{ route('admin.resignation.complete-exit', $resignation->id) }}">
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
                            <strong>Warning:</strong> Relieving employee <strong>{{ $resignation->employee?->employee_name }}</strong> will permanently set their system status to <strong>Inactive</strong>, invalidate all active tokens, and disable login.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold text-dark">Exit Feedback Rating (1 to 5)</label>
                            <select name="feedback_rating" class="form-select form-select-sm">
                                <option value="">Select Rating (Optional)</option>
                                <option value="5">5 - Excellent Exit</option>
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
                        <button type="submit" class="btn btn-danger">Yes, Relieve &amp; Deactivate Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
