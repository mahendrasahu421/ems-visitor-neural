@extends('employee.layout.layout')
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
                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="ti ti-smart-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.resignation.index') }}">Resignation &amp; Exit</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $resignation->resignation_code }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('employee.resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back to List
                </a>

                @if ($resignation->status === 'pending')
                    <button type="button" class="btn btn-danger d-inline-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="ti ti-arrow-back-up me-1"></i> Withdraw Request
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

        <!-- Status Overview Hero Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
            <div class="card-body p-4 bg-gradient @if($resignation->status === 'approved') bg-success-subtle @elseif($resignation->status === 'rejected') bg-danger-subtle @elseif($resignation->status === 'completed') bg-secondary-subtle @else bg-warning-subtle @endif">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 54px; height: 54px;">
                            @if ($resignation->status === 'approved')
                                <i class="ti ti-check text-success fs-28"></i>
                            @elseif ($resignation->status === 'rejected')
                                <i class="ti ti-x text-danger fs-28"></i>
                            @elseif ($resignation->status === 'completed')
                                <i class="ti ti-certificate text-secondary fs-28"></i>
                            @elseif ($resignation->status === 'withdrawn')
                                <i class="ti ti-arrow-back-up text-muted fs-28"></i>
                            @else
                                <i class="ti ti-hourglass-empty text-warning fs-28"></i>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="mb-0 fw-bold text-dark">{{ $resignation->resignation_code }}</h4>
                                {!! $resignation->status_badge !!}
                                {!! $resignation->initiated_by_badge !!}
                            </div>
                            <p class="mb-0 text-muted fs-13">
                                Applied on <strong>{{ $resignation->resignation_date->format('d M, Y') }}</strong>
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
                                        Today is your Last Working Day
                                    @else
                                        Notice period ended ({{ abs($daysRem) }} days ago)
                                    @endif
                                @else
                                    Active Notice
                                @endif
                            </div>
                            <small class="text-muted">Final LWD: <strong>{{ $resignation->effective_lwd->format('d M, Y') }}</strong></small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Details & Justification -->
            <div class="col-lg-8">
                <!-- Resignation Statement -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-file-text text-primary me-2"></i> Resignation Reason &amp; Statement
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="text-muted fs-12 text-uppercase fw-semibold">Primary Reason</label>
                            <div class="fs-15 fw-bold text-dark">{{ $resignation->reason }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted fs-12 text-uppercase fw-semibold mb-2">Detailed Resignation Letter / Remarks</label>
                            <div class="p-3 bg-light rounded-3 text-dark fs-14" style="white-space: pre-line; line-height: 1.6; border: 1px solid #e9ecef;">
                                {{ $resignation->reason_details }}
                            </div>
                        </div>

                        @if ($resignation->document_path)
                            <div>
                                <label class="text-muted fs-12 text-uppercase fw-semibold mb-2">Attached Document</label>
                                <div>
                                    <a href="{{ asset($resignation->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                                        <i class="ti ti-paperclip me-1 fs-15"></i> View Attached Resignation File
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reviewer Response Section (if action taken) -->
                @if (in_array($resignation->status, ['approved', 'rejected', 'completed', 'withdrawn']))
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="ti ti-message-check text-info me-2"></i> Review &amp; Decision Details
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Reviewed By</label>
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
                                <label class="text-muted fs-12 text-uppercase fw-semibold mb-1">Management Remarks</label>
                                <div class="p-3 bg-light rounded-3 text-dark fs-13" style="line-height: 1.6; border: 1px solid #e9ecef;">
                                    {{ $resignation->action_remarks ?: 'No remarks recorded.' }}
                                </div>
                            </div>

                            @if ($resignation->status === 'completed' && ($resignation->feedback_rating || $resignation->feedback_notes))
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fw-bold text-dark mb-2"><i class="ti ti-star me-1 text-warning"></i> Exit Feedback &amp; Handover</h6>
                                    @if ($resignation->feedback_rating)
                                        <p class="mb-1 fs-13"><strong>Rating:</strong> {{ $resignation->feedback_rating }} / 5</p>
                                    @endif
                                    @if ($resignation->feedback_notes)
                                        <p class="mb-0 fs-13 text-muted"><strong>Notes:</strong> {{ $resignation->feedback_notes }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Timeline & Dates -->
            <div class="col-lg-4">
                <!-- Timeline / Key Dates Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="ti ti-calendar-event text-primary me-2"></i> Dates &amp; Notice Schedule
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                            <div class="rounded-circle bg-light p-2 text-primary">
                                <i class="ti ti-calendar-plus fs-18"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-11 text-uppercase fw-semibold">Resignation Date</div>
                                <div class="fw-bold text-dark fs-14">{{ $resignation->resignation_date->format('d M, Y') }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                            <div class="rounded-circle bg-light p-2 text-info">
                                <i class="ti ti-clock fs-18"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-11 text-uppercase fw-semibold">Notice Period</div>
                                <div class="fw-bold text-dark fs-14">{{ $resignation->notice_period_days }} Days</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                            <div class="rounded-circle bg-light p-2 text-warning">
                                <i class="ti ti-calendar-due fs-18"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-11 text-uppercase fw-semibold">Requested Last Day</div>
                                <div class="fw-bold text-dark fs-14">{{ $resignation->requested_last_working_day->format('d M, Y') }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-light p-2 text-success">
                                <i class="ti ti-calendar-check fs-18"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-11 text-uppercase fw-semibold">Approved Last Day</div>
                                <div class="fw-bold @if($resignation->approved_last_working_day) text-success @else text-muted @endif fs-14">
                                    {{ $resignation->approved_last_working_day ? $resignation->approved_last_working_day->format('d M, Y') : 'Pending Approval' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CC Notification Recipients Card -->
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
                                <p class="text-muted fs-13 mb-0">No additional CC email recipients specified.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <x-footer />
</div>

<!-- Modal: Withdraw Resignation -->
@if ($resignation->status === 'pending')
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <form method="POST" action="{{ route('employee.resignation.withdraw', $resignation->id) }}">
                    @csrf
                    <div class="modal-header border-bottom py-3 px-4">
                        <h5 class="modal-title fw-bold text-danger" id="withdrawModalLabel">
                            <i class="ti ti-alert-triangle me-1"></i> Confirm Withdrawal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted fs-14 mb-3">
                            Are you sure you want to withdraw your resignation request (<strong>{{ $resignation->resignation_code }}</strong>)?
                            Once withdrawn, your active employment status will remain uninterrupted.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold text-dark">Reason for Withdrawal (Optional)</label>
                            <textarea name="withdraw_reason" rows="3" class="form-control fs-13" placeholder="Provide any context or notes regarding your decision to withdraw..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-3 px-4 bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep Active</button>
                        <button type="submit" class="btn btn-danger">Yes, Withdraw Resignation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
