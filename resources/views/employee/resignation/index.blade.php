@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Top Actions -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="mb-1 font-weight-bold text-dark">{{ $title }}</h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="ti ti-smart-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Resignation &amp; Exit</li>
                    </ol>
                </nav>
            </div>
            <div>
                @if (!$activeResignation)
                    <a href="{{ route('employee.resignation.create') }}" class="btn btn-primary d-inline-flex align-items-center shadow-sm">
                        <i class="ti ti-file-upload me-1.5 fs-16"></i> Apply for Resignation
                    </a>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fs-12 fw-semibold">
                        <i class="ti ti-info-circle me-1"></i> Active Exit Request in Progress ({{ $activeResignation->resignation_code }})
                    </span>
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

        <!-- Active Resignation Spotlight Card -->
        @if ($activeResignation)
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" style="background-color: {{ $activeResignation->status === 'approved' ? '#f0fdf4' : '#fffbeb' }}; border-bottom: 1px solid {{ $activeResignation->status === 'approved' ? '#bbf7d0' : '#fef08a' }};">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center {{ $activeResignation->status === 'approved' ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                            <i class="ti {{ $activeResignation->status === 'approved' ? 'ti-calendar-check' : 'ti-clock-pause' }} fs-16"></i>
                        </span>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Active Resignation Request: {{ $activeResignation->resignation_code }}</h5>
                            <small class="text-muted">Submitted on {{ $activeResignation->resignation_date->format('d M, Y') }}</small>
                        </div>
                    </div>
                    <div>
                        {!! $activeResignation->status_badge !!}
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3 border-end-md">
                            <div class="text-muted fs-12 text-uppercase fw-semibold">Notice Period</div>
                            <div class="fs-18 fw-bold text-dark mt-1">{{ $activeResignation->notice_period_days }} Days</div>
                            <div class="text-muted fs-11 mt-1">Company Policy Standard</div>
                        </div>
                        <div class="col-md-3 border-end-md">
                            <div class="text-muted fs-12 text-uppercase fw-semibold">Requested Last Working Day</div>
                            <div class="fs-18 fw-bold text-primary mt-1">{{ $activeResignation->requested_last_working_day->format('d M, Y') }}</div>
                            <div class="text-muted fs-11 mt-1">As proposed in application</div>
                        </div>
                        <div class="col-md-3 border-end-md">
                            <div class="text-muted fs-12 text-uppercase fw-semibold">Final Approved Relieving Date</div>
                            @if ($activeResignation->approved_last_working_day)
                                <div class="fs-18 fw-bold text-success mt-1">{{ $activeResignation->approved_last_working_day->format('d M, Y') }}</div>
                                <div class="text-success fs-11 mt-1 font-weight-bold">
                                    {{ max($activeResignation->daysRemaining(), 0) }} Days Remaining in Notice
                                </div>
                            @else
                                <div class="fs-18 fw-semibold text-muted mt-1">Under Review</div>
                                <div class="text-muted fs-11 mt-1">Awaiting management sign-off</div>
                            @endif
                        </div>
                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-md-end">
                            <a href="{{ route('employee.resignation.show', $activeResignation->id) }}" class="btn btn-outline-primary btn-sm mb-2 w-100 w-md-auto">
                                <i class="ti ti-eye me-1"></i> View Full Details
                            </a>
                            @if ($activeResignation->status === 'pending')
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                    <i class="ti ti-arrow-back-up me-1"></i> Withdraw Resignation
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($activeResignation->action_remarks)
                        <div class="mt-4 p-3 rounded-3 bg-light border">
                            <div class="fw-bold text-dark fs-12 text-uppercase mb-1">
                                <i class="ti ti-message-dots me-1 text-primary"></i> Reviewer Feedback / Instructions:
                            </div>
                            <p class="mb-0 text-muted fs-13">{{ $activeResignation->action_remarks }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Withdraw Modal -->
            <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('employee.resignation.withdraw', $activeResignation->id) }}" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title text-danger"><i class="ti ti-alert-triangle me-1"></i> Withdraw Resignation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-dark mb-3">Are you sure you want to withdraw your resignation request <strong>{{ $activeResignation->resignation_code }}</strong>? This action will cancel the exit request and retain your regular employment status.</p>
                            <div class="mb-3">
                                <label class="form-label fs-13 fw-semibold">Reason for Withdrawal (Optional)</label>
                                <textarea name="withdraw_reason" class="form-control" rows="3" placeholder="Briefly explain why you wish to cancel this resignation request..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Withdraw Resignation</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Resignation History Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-xs rounded bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-history fs-15"></i>
                    </span>
                    <h5 class="card-title mb-0 fw-bold text-dark">Resignation &amp; Exit History</h5>
                </div>
                <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill fs-12">
                    Total Records: {{ $history->total() }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 140px;">Ref Code</th>
                                <th>Submitted On</th>
                                <th>Initiated By</th>
                                <th>Reason</th>
                                <th>Requested LWD</th>
                                <th>Approved LWD</th>
                                <th>Status</th>
                                <th class="text-end pe-4" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($history as $res)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">
                                        <a href="{{ route('employee.resignation.show', $res->id) }}" class="text-decoration-none">
                                            {{ $res->resignation_code }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-semibold fs-13">{{ $res->resignation_date ? $res->resignation_date->format('d M, Y') : '-' }}</div>
                                        <small class="text-muted">{{ $res->created_at?->diffForHumans() ?? '' }}</small>
                                    </td>
                                    <td>
                                        {!! $res->initiated_by_badge !!}
                                    </td>
                                    <td>
                                        <div class="text-dark fw-semibold fs-13">{{ $res->reason }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 220px;" title="{{ $res->reason_details }}">
                                            {{ Str::limit($res->reason_details, 40) }}
                                        </small>
                                    </td>
                                    <td class="text-muted fs-13">
                                        {{ $res->requested_last_working_day ? $res->requested_last_working_day->format('d M, Y') : '-' }}
                                    </td>
                                    <td>
                                        @if ($res->approved_last_working_day)
                                            <span class="text-success fw-bold fs-13">{{ $res->approved_last_working_day->format('d M, Y') }}</span>
                                        @else
                                            <span class="text-muted fs-12">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $res->status_badge !!}
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('employee.resignation.show', $res->id) }}" class="btn btn-outline-secondary btn-sm" title="View Details">
                                            <i class="ti ti-eye"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="ti ti-file-text fs-36 text-muted"></i></div>
                                        <p class="mb-1 fw-semibold fs-14">No resignation records found.</p>
                                        <small class="text-muted">You are currently in active service with no exit requests recorded.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($history->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $history->links() }}
                </div>
            @endif
        </div>

    </div>

    <x-footer />
</div>

@endsection
