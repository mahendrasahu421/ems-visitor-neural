@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $taxPercent = (float) ($project->tax_percent ?? 0);
        $baseAmount = (float) ($project->total_value ?? 0);
        $totalWithTax = (float) (($project->total_value_with_tax ?? 0) ?: (($project->total_with_tax ?? 0) ?: ($baseAmount + (float) ($project->tax_amount ?? 0))));
        $paidPercent = $totalAmount > 0 ? min(100, ($totalPaid / $totalAmount) * 100) : 0;
        $engagement = $project->engagement_type ?? 'project';
        $status = (int) data_get($project, 'status', data_get($project, 'DelStatus', 1));
        $concernPersonsList = $concernPersons ?? collect();
    @endphp

    <style>
        .project-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .project-header h2 { margin: 0 0 10px 0; font-weight: 600; }
        .info-card {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px -4px rgba(0,0,0,0.04);
            border-left: 4px solid #0d6efd;
        }
        .info-label { font-size: 12px; text-transform: uppercase; color: #6c757d; font-weight: 600; letter-spacing: .5px; }
        .info-value { font-size: 18px; font-weight: 600; color: #2c3e50; margin-top: 5px; }
        .payment-summary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .summary-box { text-align: center; padding: 10px; }
        .summary-box .amount { font-size: 28px; font-weight: 700; }
        .summary-box .label { font-size: 12px; opacity: .9; margin-top: 5px; }
        .progress-bar-custom { height: 8px; border-radius: 10px; background: rgba(255,255,255,.3); overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; background: white; border-radius: 10px; }
        .milestone-table { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
        .milestone-table thead { background: #0d6efd; color: white; }
        .milestone-table th, .milestone-table td { padding: 12px 15px; vertical-align: middle; white-space: nowrap; }
        .status-paid, .status-pending, .status-partial { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 500; display: inline-block; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-partial { background: #cce5ff; color: #004085; }
        .btn-sm-custom { padding: 5px 12px; font-size: 12px; border-radius: 20px; }

        .person-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4338ca;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">
            <div class="container mt-15">
                <div class="project-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <h2>{{ $project->project_name }}</h2>
                            <div>{{ $project->ClientName }} | {{ $project->work_type_display ?? $project->work_type ?? '-' }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('invoice.projects') }}" class="btn btn-light btn-sm">Back to List</a>
                            <a href="{{ route('invoice.projects.employees', $project->project_id) }}" class="btn btn-success btn-sm">Map Team</a>
                            <a href="{{ route('invoice.projects.edit', $project->project_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        </div>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" style="width: {{ $paidPercent }}%"></div>
                    </div>
                    <small>{{ number_format($paidPercent, 1) }}% paid</small>
                </div>

                <div class="payment-summary">
                    <div class="row">
                        <div class="col-md-4 summary-box">
                            <div class="amount">Rs {{ number_format($totalAmount, 2) }}</div>
                            <div class="label">Total Amount</div>
                        </div>
                        <div class="col-md-4 summary-box">
                            <div class="amount">Rs {{ number_format($totalPaid, 2) }}</div>
                            <div class="label">Paid</div>
                        </div>
                        <div class="col-md-4 summary-box">
                            <div class="amount">Rs {{ number_format($totalPending, 2) }}</div>
                            <div class="label">Pending</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6>Project Information</h6>
                            <hr>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="info-label">Type</div>
                                    <div class="info-value">{{ $engagement === 'amc_only' ? 'AMC Only' : 'Project' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Status</div>
                                    <div class="info-value">{{ $status === 1 ? 'Active' : 'Inactive' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Base Amount</div>
                                    <div class="info-value">Rs {{ number_format($baseAmount, 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Total With Tax</div>
                                    <div class="info-value">Rs {{ number_format($totalWithTax, 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Tax</div>
                                    <div class="info-value">{{ number_format($taxPercent, 2) }}%</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Payment Mode</div>
                                    <div class="info-value">{{ ucfirst($project->payment_mode ?? 'Percentage') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <h6>Project Duration</h6>
                            <hr>
                            <div class="row">
                                <div class="col-4">
                                    <div class="info-label">Start Date</div>
                                    <div class="info-value">{{ $project->project_start_date ? date('d-m-Y', strtotime($project->project_start_date)) : '-' }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="info-label">End Date</div>
                                    <div class="info-value">{{ $project->project_end_date ? date('d-m-Y', strtotime($project->project_end_date)) : '-' }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="info-label">Duration</div>
                                    <div class="info-value">{{ $project->project_duration_days ? $project->project_duration_days . ' days' : '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="info-card">
                            <h6>AMC Period</h6>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-label">AMC Start Date</div>
                                    <div class="info-value">{{ $project->amc_start ? date('d-m-Y', strtotime($project->amc_start)) : '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">AMC End Date</div>
                                    <div class="info-value">{{ $project->amc_end ? date('d-m-Y', strtotime($project->amc_end)) : '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!empty($project->notes))
                    <div class="info-card">
                        <h6>Notes</h6>
                        <hr>
                        <p class="mb-0">{!! nl2br(e($project->notes)) !!}</p>
                    </div>
                @endif

                <!-- CLIENT CONCERN PERSONS & PORTAL ACCESS CARD -->
                <div class="info-card" style="border-left-color: #6366f1;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="ti ti-users text-primary me-2 fs-18"></i>Client Concern Persons & Portal Logins
                                <span class="badge bg-primary-subtle text-primary ms-2 rounded-pill fs-11">
                                    {{ $concernPersonsList->count() }} Contacts
                                </span>
                            </h6>
                            <small class="text-muted">Client representatives authorized to manage this project and raise tickets.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addConcernPersonModal">
                            <i class="ti ti-user-plus me-1.5"></i> Add Concern Person
                        </button>
                    </div>
                    <hr>

                    @if ($concernPersonsList->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" id="concernPersonsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Concern Person</th>
                                        <th>Contact</th>
                                        <th>Portal Login</th>
                                        <th>Credentials Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($concernPersonsList as $person)
                                        @php
                                            $initials = collect(explode(' ', $person->name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                                        @endphp
                                        <tr id="person-row-{{ $person->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="person-avatar">{{ $initials ?: 'CP' }}</span>
                                                    <div>
                                                        <div class="fw-bold text-dark">
                                                            {{ $person->name }}
                                                            @if ($person->is_primary)
                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-10 ms-1">Primary SPOC</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ $person->designation ?: 'Client Representative' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div><a href="mailto:{{ $person->email }}" class="text-primary text-decoration-none"><i class="ti ti-mail me-1"></i>{{ $person->email }}</a></div>
                                                @if ($person->mobile)
                                                    <div class="text-muted small"><i class="ti ti-phone me-1"></i>{{ $person->mobile }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($person->login_client_id || $person->can_login)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                                        <i class="ti ti-shield-check me-1"></i>Active Portal Login
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-secondary border px-2 py-1 fs-11">
                                                        <i class="ti ti-lock me-1"></i>No Login Account
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($person->credentials_sent_at)
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-11" title="Sent at {{ $person->credentials_sent_at }}">
                                                        <i class="ti ti-mail-check me-1"></i>Sent: {{ date('d-M-Y', strtotime($person->credentials_sent_at)) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border px-2 py-1 fs-11">
                                                        <i class="ti ti-clock me-1"></i>Not Sent
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-info btn-send-credentials" 
                                                            data-id="{{ $person->id }}" 
                                                            data-name="{{ $person->name }}" 
                                                            data-email="{{ $person->email }}" 
                                                            title="Send / Resend Login Credentials">
                                                        <i class="ti ti-send me-1"></i>Send Login
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-person" 
                                                            data-id="{{ $person->id }}" 
                                                            data-name="{{ $person->name }}" 
                                                            title="Remove Concern Person">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0 d-flex align-items-center justify-content-between">
                            <span>No concern persons added yet for this project.</span>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addConcernPersonModal">
                                + Add First Concern Person
                            </button>
                        </div>
                    @endif
                </div>

                <div class="info-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h6 class="mb-0">Project Team</h6>
                        <a href="{{ route('invoice.projects.employees', $project->project_id) }}" class="btn btn-sm btn-outline-success">Map Employees</a>
                    </div>
                    <hr>
                    @if (($teamMembers ?? collect())->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>Designation</th>
                                        <th>Role</th>
                                        <th>Allocation</th>
                                        <th>Period</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teamMembers as $member)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $member->employee_name }}</strong>
                                                <div class="text-muted small">{{ $member->employee_email }} | {{ $member->employee_mobile }}</div>
                                            </td>
                                            <td>{{ $member->designation_name ?: '-' }}</td>
                                            <td>{{ $member->project_role ?: '-' }}</td>
                                            <td>{{ $member->allocation_percent ? $member->allocation_percent . '%' : '-' }}</td>
                                            <td>
                                                {{ $member->start_date ? date('d-m-Y', strtotime($member->start_date)) : '-' }}
                                                -
                                                {{ $member->end_date ? date('d-m-Y', strtotime($member->end_date)) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">No employees are mapped yet.</div>
                    @endif
                </div>

                <div class="info-card">
                    <h6>Payment Milestones</h6>
                    <hr>
                    @if ($milestones->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table milestone-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Milestone</th>
                                        <th>Module</th>
                                        <th>%</th>
                                        <th>Amount (Rs)</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Payment Details</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($milestones as $m)
                                        @php
                                            $milestoneBase = (float) ($m->amount ?? 0);
                                            $milestoneWithTax = (float) (($m->amount_with_tax ?? 0) > 0 ? $m->amount_with_tax : $milestoneBase);
                                            $invoicedBase = (float) ($m->invoiced_amount ?? 0);
                                            $paidAmount = (float) ($m->paid_amount ?? 0);
                                            $pendingPayment = max(0, $milestoneWithTax - $paidAmount);
                                            $remainingBase = max(0, $milestoneBase - $invoicedBase);
                                            $isFullyInvoiced = $invoicedBase >= $milestoneBase - 0.01;
                                            $invoiceQuery = [
                                                'client_id' => $project->client_id,
                                                'milestone_id' => $m->milestone_id,
                                                'newInvoice' => 1,
                                            ];
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $m->milestone_label }}</strong></td>
                                            <td>{{ $m->module_name ?: '-' }}</td>
                                            <td>{{ $m->percentage ? $m->percentage . '%' : '-' }}</td>
                                            <td>
                                                Rs {{ number_format($milestoneBase, 2) }}
                                                <div class="text-muted small">With Tax: Rs {{ number_format($milestoneWithTax, 2) }}</div>
                                            </td>
                                            <td>{{ $m->due_date ? date('d-m-Y', strtotime($m->due_date)) : '-' }}</td>
                                            <td>
                                                @if ($paidAmount >= $milestoneWithTax - 0.01)
                                                    <span class="status-paid">Fully Paid</span>
                                                @elseif ($paidAmount > 0)
                                                    <span class="status-partial">Partially Paid</span>
                                                @else
                                                    <span class="status-pending">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($paidAmount > 0)
                                                    <small class="text-success">
                                                        <strong>Paid:</strong> Rs {{ number_format($paidAmount, 2) }}
                                                        @if ($pendingPayment > 0.01)
                                                            <br><strong>Pending:</strong> Rs {{ number_format($pendingPayment, 2) }}
                                                        @endif
                                                    </small>
                                                @else
                                                    <small class="text-muted">Not paid yet</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @if (!empty($m->linked_inv_id))
                                                        <a href="{{ route('invoice.invoices.show', $m->linked_inv_id) }}" class="btn btn-sm btn-info btn-sm-custom">Preview Invoice</a>
                                                    @elseif (!$isFullyInvoiced)
                                                        <a href="{{ route('invoice.invoices.create', $invoiceQuery) }}" class="btn btn-sm btn-primary btn-sm-custom">Create Invoice</a>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary btn-sm-custom" disabled>Invoiced</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background: #f8f9fa; font-weight: bold;">
                                    <tr>
                                        <td colspan="4" class="text-end">Total:</td>
                                        <td>Rs {{ number_format($totalAmount, 2) }}</td>
                                        <td></td>
                                        <td colspan="3">Paid: Rs {{ number_format($totalPaid, 2) }} | Pending: Rs {{ number_format($totalPending, 2) }} (incl. tax)</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">No payment milestones found for this project.</div>
                    @endif
                </div>

                @if (!empty($project->proposal_file))
                    <div class="info-card">
                        <h6>Proposal Document</h6>
                        <hr>
                        <a href="{{ route('legacy.neuralinvoice', ['path' => $project->proposal_file]) }}"  class="btn btn-primary btn-sm-custom">Download Proposal</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- ADD CONCERN PERSON MODAL -->
        <div class="modal fade" id="addConcernPersonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <form id="addConcernPersonForm">
                        @csrf
                        <div class="modal-header bg-primary text-white rounded-top-4">
                            <h5 class="modal-title text-white fw-bold"><i class="ti ti-user-plus me-1.5"></i>Add Concern Person</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="modal_person_name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="modal_person_email" class="form-control" placeholder="rahul@company.com" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Mobile / Phone</label>
                                    <input type="text" name="mobile" id="modal_person_mobile" class="form-control" placeholder="+91 9876543210">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Designation / Role</label>
                                    <input type="text" name="designation" id="modal_person_designation" class="form-control" placeholder="Project SPOC">
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="is_primary" id="modal_is_primary" value="1">
                                <label class="form-check-label small" for="modal_is_primary">Mark as Primary Contact / SPOC</label>
                            </div>

                            <div class="p-3 bg-light rounded border mb-3">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" name="can_login" id="modal_can_login" value="1" checked>
                                    <label class="form-check-label small fw-bold text-dark" for="modal_can_login">
                                        <i class="ti ti-key text-primary me-1"></i> Create Client Portal Login Account
                                    </label>
                                </div>

                                <div id="modalLoginBox">
                                    <label class="form-label small text-muted">Account Password</label>
                                    <div class="input-group input-group-sm mb-2">
                                        <input type="text" name="password" id="modal_password" class="form-control font-monospace" placeholder="Auto-generated if empty">
                                        <button class="btn btn-outline-secondary" type="button" id="modalGenPwdBtn" title="Generate Password">
                                            <i class="ti ti-refresh"></i>
                                        </button>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="send_email" id="modal_send_email" value="1" checked>
                                        <label class="form-check-label small text-dark" for="modal_send_email">
                                            <i class="ti ti-mail text-info me-1"></i> Send credentials to email
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-semibold" id="submitAddPersonBtn">
                                <i class="ti ti-check me-1"></i> Save Concern Person
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- SEND CREDENTIALS MODAL -->
        <div class="modal fade" id="sendCredentialsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <form id="sendCredentialsForm">
                        @csrf
                        <input type="hidden" id="send_cred_person_id">
                        <div class="modal-body text-center p-4">
                            <span class="avatar avatar-xl rounded-circle bg-primary-subtle text-primary mb-3 d-inline-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="ti ti-mail-fast fs-24"></i>
                            </span>
                            <h5 class="fw-bold text-dark mb-1">Send Login Details</h5>
                            <p class="text-muted fs-13 mb-3">Send Client Portal login credentials to <strong id="send_cred_name" class="text-dark">User</strong> (<span id="send_cred_email" class="text-primary font-monospace"></span>)?</p>

                            <div class="text-start mb-3">
                                <label class="form-label small fw-bold">Set Custom Password <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" name="password" id="send_cred_pwd" class="form-control form-control-sm font-monospace" placeholder="Leave blank for auto-generate">
                            </div>

                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold" id="confirmSendCredBtn">
                                    <i class="ti ti-send me-1"></i> Send Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            function generateRandomPassword(len) {
                len = len || 8;
                var chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
                var pass = '';
                for (var i = 0; i < len; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return pass;
            }

            $('#modalGenPwdBtn').on('click', function () {
                $('#modal_password').val(generateRandomPassword(10));
            });

            $('#modal_can_login').on('change', function () {
                $('#modalLoginBox').toggle($(this).is(':checked'));
            });

            // Add Concern Person Submit
            $('#addConcernPersonForm').on('submit', function (e) {
                e.preventDefault();
                var $btn = $('#submitAddPersonBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                $.ajax({
                    url: "{{ route('invoice.projects.concern-persons.store', $project->project_id) }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Save Concern Person');
                        $('#addConcernPersonModal').modal('hide');
                        $('#addConcernPersonForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Added!',
                            text: res.message || 'Concern person added successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Save Concern Person');
                        var msg = xhr.responseJSON?.message || 'Error adding concern person.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Trigger Send Credentials Modal
            $(document).on('click', '.btn-send-credentials', function () {
                var personId = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');

                $('#send_cred_person_id').val(personId);
                $('#send_cred_name').text(name);
                $('#send_cred_email').text(email);
                $('#send_cred_pwd').val(generateRandomPassword(8));
                $('#sendCredentialsModal').modal('show');
            });

            // Send Credentials Form Submit
            $('#sendCredentialsForm').on('submit', function (e) {
                e.preventDefault();
                var personId = $('#send_cred_person_id').val();
                var $btn = $('#confirmSendCredBtn');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Sending...');

                $.ajax({
                    url: "{{ url('invoice/projects/' . $project->project_id . '/concern-persons') }}/" + personId + "/send-credentials",
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Send Email');
                        $('#sendCredentialsModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Sent!',
                            text: res.message || 'Credentials sent successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Send Email');
                        var msg = xhr.responseJSON?.message || 'Error sending email.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Delete Concern Person
            $(document).on('click', '.btn-delete-person', function () {
                var personId = $(this).data('id');
                var name = $(this).data('name');

                Swal.fire({
                    title: 'Remove Concern Person?',
                    text: 'Are you sure you want to remove ' + name + ' from this project?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, Remove'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('invoice/projects/' . $project->project_id . '/concern-persons') }}/" + personId,
                            method: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            dataType: 'json',
                            success: function (res) {
                                $('#person-row-' + personId).fadeOut(300, function () { $(this).remove(); });
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed!',
                                    text: res.message || 'Concern person removed.',
                                    timer: 1400,
                                    showConfirmButton: false
                                });
                            },
                            error: function (xhr) {
                                var msg = xhr.responseJSON?.message || 'Error removing person.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
