<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Resignation Review - Neural Info Solution' }}</title>
    <link rel="stylesheet" href="{{ asset('frontent/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontent/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #2e384d;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 12px;
        }
        .action-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07);
            max-width: 620px;
            width: 100%;
            overflow: hidden;
            border: 1px solid #e5e9f2;
        }
        .header-band {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 24px 28px;
        }
        .badge-status {
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #f26522;
            box-shadow: 0 0 0 0.2rem rgba(242, 101, 34, 0.2);
        }
        .btn-accept {
            background: #00b074;
            border-color: #00b074;
            color: #ffffff;
            font-weight: 600;
        }
        .btn-accept:hover {
            background: #009361;
            border-color: #009361;
            color: #ffffff;
        }
        .btn-reject {
            background: #ff3e1d;
            border-color: #ff3e1d;
            color: #ffffff;
            font-weight: 600;
        }
        .btn-reject:hover {
            background: #d93114;
            border-color: #d93114;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="action-card">
    <div class="header-band">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fs-12 text-uppercase fw-bold text-warning" style="letter-spacing: 1px;">Neural Info Solutions</span>
            <span class="badge bg-secondary-subtle text-light fs-11">Secure Direct Action</span>
        </div>
        <h4 class="mb-1 fw-bold text-white">{{ $title }}</h4>
        <small class="text-white-50">Review and record your decision on this employee separation request.</small>
    </div>

    <div class="p-4">
        @if (!empty($error))
            <div class="alert alert-danger border-0 d-flex align-items-center mb-4" role="alert">
                <i class="ti ti-alert-triangle fs-24 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Action Unavailable</h6>
                    <p class="mb-0 fs-13">{{ $error }}</p>
                </div>
            </div>
            <div class="text-center">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm">Go to Homepage</a>
            </div>
        @elseif (!empty($done))
            <div class="text-center py-4">
                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 68px; height: 68px;">
                    <i class="ti ti-check fs-36"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Response Recorded!</h4>
                <p class="text-muted fs-14 mb-4" style="max-width: 440px; margin: 0 auto;">
                    {{ $success }}
                </p>
                <div class="p-3 bg-light rounded-3 text-start mb-4 fs-13">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Resignation Code:</span>
                        <strong class="text-dark">{{ $resignation->resignation_code }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Employee:</span>
                        <strong class="text-dark">{{ $resignation->employee?->employee_name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Status:</span>
                        <strong class="text-uppercase @if($resignation->status === 'approved') text-success @else text-danger @endif">{{ $resignation->status }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Effective LWD:</span>
                        <strong class="text-dark">{{ $resignation->effective_lwd ? $resignation->effective_lwd->format('d M, Y') : 'N/A' }}</strong>
                    </div>
                </div>
                <small class="text-muted d-block">You may close this browser window safely.</small>
            </div>
        @else
            @if ($resignation)
                <!-- Resignation Snapshot -->
                <div class="p-3 bg-light rounded-3 mb-4 fs-13">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-11 text-uppercase fw-semibold">Employee</span>
                            <strong class="text-dark">{{ $resignation->employee?->employee_name }}</strong>
                            <small class="text-muted d-block">({{ $resignation->employee?->employee_id }})</small>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block fs-11 text-uppercase fw-semibold">Designation</span>
                            <strong class="text-dark">{{ $resignation->employee?->designation?->name ?? 'Staff' }}</strong>
                            <small class="text-muted d-block">{{ $resignation->employee?->agency?->agency_name ?? 'Direct' }}</small>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted d-block fs-11 text-uppercase fw-semibold">Resignation Date</span>
                            <strong class="text-dark">{{ $resignation->resignation_date->format('d M, Y') }}</strong>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted d-block fs-11 text-uppercase fw-semibold">Requested LWD (Notice)</span>
                            <strong class="text-dark">{{ $resignation->requested_last_working_day->format('d M, Y') }} ({{ $resignation->notice_period_days }} Days)</strong>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted fs-11 text-uppercase fw-semibold mb-1">Reason for Resignation</label>
                    <div class="fw-bold text-dark fs-14 mb-2">{{ $resignation->reason }}</div>
                    <div class="p-3 rounded-2 fs-13 text-secondary" style="background: #ffffff; border: 1px solid #e2e8f0; line-height: 1.5; white-space: pre-line;">
                        {{ $resignation->reason_details }}
                    </div>
                </div>

                @if ($resignation->document_path)
                    <div class="mb-4">
                        <a href="{{ asset($resignation->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-paperclip me-1"></i> View Attached Resignation Document
                        </a>
                    </div>
                @endif

                @if (!in_array($resignation->status, ['pending', 'in_review'], true))
                    <div class="alert alert-info border-0 fs-13 mb-0">
                        <i class="ti ti-info-circle me-1"></i> This resignation has already been responded to with status: <strong>{{ strtoupper($resignation->status) }}</strong>.
                    </div>
                @else
                    <form method="POST" action="{{ route('resignation.email-action', ['token' => $resignation->decision_token]) }}">
                        @csrf

                        <!-- Decision Picker -->
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-dark">Your Decision <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 p-2 bg-light rounded-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="dec_app" value="approved" {{ ($selectedDecision ?? 'approved') === 'approved' ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-bold fs-13" for="dec_app">
                                        <i class="ti ti-check me-1"></i> Accept &amp; Approve
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" id="dec_rej" value="rejected" {{ ($selectedDecision ?? '') === 'rejected' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-bold fs-13" for="dec_rej">
                                        <i class="ti ti-x me-1"></i> Reject Resignation
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Approved LWD -->
                        <div class="mb-3" id="lwdContainer">
                            <label class="form-label fs-13 fw-bold text-dark">
                                Approved Last Working Day <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="approved_last_working_day" class="form-control fs-13" value="{{ $resignation->requested_last_working_day->format('Y-m-d') }}">
                            <small class="text-muted">Confirmed date for employee relieving.</small>
                        </div>

                        <!-- Reviewer Name -->
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-dark">
                                Your Name / Designation <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="reviewer_name" class="form-control fs-13" required placeholder="e.g. Rahul Sharma (Supervisor) or HR Management" value="{{ Auth::check() ? Auth::user()->name : '' }}">
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label class="form-label fs-13 fw-bold text-dark">
                                Remarks / Handover Instructions <span class="text-danger">*</span>
                            </label>
                            <textarea name="action_remarks" rows="3" class="form-control fs-13" required placeholder="Provide acceptance remarks, handover expectations, or reasons for rejection..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fs-14 fw-bold">
                                <i class="ti ti-check me-1.5"></i> Confirm Decision &amp; Notify All Parties
                            </button>
                        </div>
                    </form>
                @endif
            @endif
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const decApp = document.getElementById('dec_app');
    const decRej = document.getElementById('dec_rej');
    const lwdContainer = document.getElementById('lwdContainer');

    function toggleLwd() {
        if (!lwdContainer) return;
        if (decRej && decRej.checked) {
            lwdContainer.style.display = 'none';
        } else {
            lwdContainer.style.display = 'block';
        }
    }

    if (decApp) decApp.addEventListener('change', toggleLwd);
    if (decRej) decRej.addEventListener('change', toggleLwd);
    toggleLwd();
});
</script>

</body>
</html>
