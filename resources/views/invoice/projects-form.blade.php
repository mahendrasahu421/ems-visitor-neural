@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $isEdit = (bool) $project;
        $fyStart = now()->month >= 4 ? now()->year : now()->year - 1;
        $fyOptions = collect(range($fyStart - 3, $fyStart + 2))->map(function ($year) {
            return [
                'start_year' => $year,
                'label' => $year . '-' . substr((string) ($year + 1), -2),
                'start' => $year . '-04-01',
                'end' => ($year + 1) . '-03-31',
            ];
        });
        $projectFinancialYear = $project->financial_year ?? null;
        $selectedFy = (int) old('fy_start_year', $projectFinancialYear ? (int) substr($projectFinancialYear, 0, 4) : $fyStart);
        $selectedFyPeriod = $fyOptions->firstWhere('start_year', $selectedFy) ?? $fyOptions->first();
        
        // Milestones
        $rows = collect(old('milestone_label') ? [] : ($milestones ?? collect()));
        if (old('milestone_label')) {
            foreach (old('milestone_label', []) as $i => $label) {
                $rows->push((object) [
                    'milestone_label' => $label,
                    'module_name' => old('module_name.' . $i),
                    'percentage' => old('percentage.' . $i),
                    'amount' => old('milestone_amount.' . $i),
                    'due_date' => old('due_date.' . $i),
                ]);
            }
        }
        if ($rows->isEmpty()) {
            $rows = collect([(object) ['milestone_label' => '', 'module_name' => '', 'percentage' => '', 'amount' => '', 'due_date' => '']]);
        }

        // Concern Persons
        $personRows = collect(old('concern_name') ? [] : ($concernPersons ?? collect()));
        if (old('concern_name')) {
            foreach (old('concern_name', []) as $i => $name) {
                $personRows->push((object) [
                    'id' => old('concern_person_id.' . $i, 0),
                    'name' => $name,
                    'email' => old('concern_email.' . $i),
                    'mobile' => old('concern_mobile.' . $i),
                    'designation' => old('concern_designation.' . $i),
                    'is_primary' => (int) old('concern_primary_index', 0) === $i,
                    'can_login' => !empty(old('concern_can_login.' . $i)),
                    'credentials_sent_at' => null,
                ]);
            }
        }
        if ($personRows->isEmpty()) {
            $personRows = collect([(object) [
                'id' => 0,
                'name' => '',
                'email' => '',
                'mobile' => '',
                'designation' => 'Project SPOC',
                'is_primary' => true,
                'can_login' => true,
                'credentials_sent_at' => null,
            ]]);
        }

        $hasAmc = old('has_amc', $project->has_amc ?? 'no') === 'yes';
        $engagementType = old('engagement_type', $project->engagement_type ?? 'project');
    @endphp

    <style>
        label { font-weight: 700; }
        .ni-page .card { border-radius: 8px; }
        .ni-section-title { font-weight: 700; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; margin: 18px 0 14px; }
        .tax-input-box { background: #f8fbff; border: 1px solid #dbeafe; border-radius: 8px; padding: 16px; }
        .duration-help { color: #6c757d; font-size: 12px; }
        .value-with-tax-field { background-color: #e8f5e9; font-weight: 700; }
        .amc-optional-section { background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .milestone-row { margin-bottom: 8px; }
        .milestone-exceed { border-color: #dc3545 !important; background-color: #fff3f3 !important; }
        #totalPercentage.ok { color: #198754; }
        #totalPercentage.over { color: #dc3545; }

        /* Concern Person Card */
        .concern-person-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .concern-person-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .concern-person-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 14px;
            border-top-left-radius: 9px;
            border-top-right-radius: 9px;
        }
    </style>

    <div class="page-wrapper">
        <div class="content">
            <div class="container mt-15 ni-page">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                        <div>
                            <h3 class="mb-0">Client Project / AMC</h3>
                            <small class="text-muted">Create project with multiple concern persons, client portal logins, FY-based AMC and payment milestones</small>
                        </div>
                        <div>
                            <a href="{{ route('invoice.projects') }}" class="btn btn-outline-secondary btn-sm">Project List</a>
                            <a href="{{ route('legacy.neuralinvoice', ['path' => 'work-type-master.php']) }}" class="btn btn-outline-primary btn-sm">Work Types</a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($workTypes->isEmpty())
                            <div class="alert alert-warning mb-3">
                                No work types in database.
                                <a href="{{ route('legacy.neuralinvoice', ['path' => 'work-type-master.php']) }}">Add work types</a> first.
                            </div>
                        @endif

                        <form action="{{ $isEdit ? route('invoice.projects.update', $project->project_id) : route('invoice.projects.store') }}" method="post" enctype="multipart/form-data" id="projectForm" novalidate>
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <p class="ni-section-title"><span id="section1Title">1. Project Details</span></p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label>Client <span class="text-danger">*</span></label>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value="">-- Select Client --</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->ClientID }}" @selected(old('client_id', $project->client_id ?? '') == $client->ClientID)>{{ $client->ClientName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Country</label>
                                    <input type="text" id="client_country" class="form-control" value="" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>State</label>
                                    <input type="text" id="client_state" class="form-control" value="" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label id="projectNameLabel">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" name="project_name" id="project_name" class="form-control uppercase-input" required value="{{ old('project_name', $project->project_name ?? '') }}" placeholder="e.g. WEBSITE DEVELOPMENT" oninput="this.value = this.value.toUpperCase();">
                                </div>
                                <div class="col-md-4">
                                    <label>Work Type <span class="text-danger">*</span></label>
                                    <select name="work_type_id" id="work_type_id" class="form-control" required @disabled($workTypes->isEmpty())>
                                        <option value="" data-amc-type="project">-- Select Work Type --</option>
                                        @foreach ($workTypes as $type)
                                            @php($amcType = ($type->amc_type ?? 'project') === 'amc_only' ? 'amc_only' : 'project')
                                            <option value="{{ $type->WorkTypeID }}" data-amc-type="{{ $amcType }}" @selected(old('work_type_id', $project->work_type_id ?? '') == $type->WorkTypeID)>
                                                {{ $type->WorkTypeName }}{{ $amcType === 'amc_only' ? ' (AMC Only)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted" id="workTypeHint">AMC Type is configured in Work Type Master.</small>
                                </div>
                                <div class="col-md-4" id="totalValueCol">
                                    <label id="totalValueLabel">Total Project Value (Rs) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="total_value" id="total_value" class="form-control" required value="{{ old('total_value', $project->total_value ?? '') }}">
                                    <small class="text-muted" id="totalValueHint">Enter base amount without tax</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <div class="tax-input-box">
                                        <h6 class="mb-3">Tax Details (Auto-calculated based on Client Location)</h6>
                                        <div class="row">
                                            <div class="col-md-3" id="cgstBox" style="display: none;">
                                                <label>CGST</label>
                                                <input type="text" id="cgst_display" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-3" id="sgstBox" style="display: none;">
                                                <label>SGST</label>
                                                <input type="text" id="sgst_display" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-3" id="igstBox" style="display: none;">
                                                <label>IGST</label>
                                                <input type="text" id="igst_display" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-3" id="totalTaxBox">
                                                <label>Total Tax</label>
                                                <input type="text" id="total_tax_display" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label>Project Value with Tax (Rs) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="total_value_with_tax" id="total_value_with_tax" readonly class="form-control value-with-tax-field" required value="{{ old('total_value_with_tax', $project->total_value_with_tax ?? '') }}">
                                    <small class="text-muted">Base Amount + Tax (will be saved to database)</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label>Project Duration (Working Days) <span class="text-danger">*</span></label>
                                    <input type="number" name="project_duration_days" id="project_duration_days" class="form-control" min="1" max="365" placeholder="Enter number of working days" required value="{{ old('project_duration_days', $project->project_duration_days ?? '') }}">
                                    <small class="duration-help">Monday to Friday only (Weekends excluded)</small>
                                </div>
                                <div class="col-md-4">
                                    <label>Project Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="project_start_date" id="project_start_date" class="form-control" required value="{{ old('project_start_date', $project->project_start_date ?? '') }}">
                                    <small class="text-muted">Any date can be selected (past or future)</small>
                                </div>
                                <div class="col-md-4">
                                    <label>Project End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="project_end_date" id="project_end_date" class="form-control" required style="background-color: #e9ecef;" value="{{ old('project_end_date', $project->project_end_date ?? '') }}">
                                    <small class="text-muted">Auto-calculated based on working days</small>
                                </div>
                            </div>

                            <!-- SECTION 2: CLIENT CONCERN PERSONS & PORTAL ACCESS -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center ni-section-title">
                                <div>
                                    <i class="ti ti-users text-primary me-1"></i> 2. Client Concern Persons & Portal Access
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="addConcernPersonBtn">
                                    <i class="ti ti-user-plus me-1"></i> + Add Another Concern Person
                                </button>
                            </div>
                            <p class="text-muted small mt-n2 mb-3">
                                Add client contacts who will coordinate this project. You can create a Client Portal login for each person and choose whether to email their credentials.
                            </p>

                            <div id="concernPersonsContainer" class="mb-4">
                                @foreach ($personRows as $pIdx => $person)
                                    <div class="concern-person-card mb-3 p-3" data-person-index="{{ $pIdx }}">
                                        <input type="hidden" name="concern_person_id[]" value="{{ $person->id ?? 0 }}">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary-subtle text-primary font-monospace person-index-badge">
                                                    Person #{{ $pIdx + 1 }}
                                                </span>
                                                <div class="form-check form-check-inline mb-0 ms-2">
                                                    <input class="form-check-input primary-radio" type="radio" name="concern_primary_index" value="{{ $pIdx }}" id="primary_person_{{ $pIdx }}" @checked(!empty($person->is_primary))>
                                                    <label class="form-check-label fw-normal small" for="primary_person_{{ $pIdx }}">Primary Contact / SPOC</label>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-person-btn" @if ($loop->first && count($personRows) === 1) style="display:none;" @endif>
                                                <i class="ti ti-trash me-1"></i> Remove
                                            </button>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="small">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="concern_name[]" class="form-control form-control-sm concern-name-input" placeholder="e.g. John Doe" value="{{ $person->name ?? '' }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" name="concern_email[]" class="form-control form-control-sm concern-email-input" placeholder="john@client.com" value="{{ $person->email ?? '' }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small">Mobile / Phone</label>
                                                <input type="text" name="concern_mobile[]" class="form-control form-control-sm concern-mobile-input" placeholder="+91 9876543210" value="{{ $person->mobile ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small">Designation / Role</label>
                                                <input type="text" name="concern_designation[]" class="form-control form-control-sm" placeholder="e.g. Project Manager, CTO" value="{{ $person->designation ?? '' }}">
                                            </div>

                                            <div class="col-12 bg-light p-2.5 rounded border">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input can-login-toggle" name="concern_can_login[{{ $pIdx }}]" id="can_login_{{ $pIdx }}" value="1" @checked(!empty($person->can_login))>
                                                            <label class="form-check-label fw-bold small text-dark" for="can_login_{{ $pIdx }}">
                                                                <i class="ti ti-key text-primary me-1"></i> Create Client Portal Login
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 login-fields-box" style="{{ !empty($person->can_login) ? '' : 'display:none;' }}">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="concern_password[]" class="form-control password-input font-monospace" placeholder="Password (auto-generated if empty)">
                                                            <button class="btn btn-outline-secondary generate-pwd-btn" type="button" title="Generate Random Password">
                                                                <i class="ti ti-refresh"></i>
                                                            </button>
                                                        </div>
                                                        <small class="text-muted" style="font-size: 11px;">Leave blank for auto-generated 8-char password.</small>
                                                    </div>

                                                    <div class="col-md-4 login-fields-box" style="{{ !empty($person->can_login) ? '' : 'display:none;' }}">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input send-email-toggle" name="concern_send_email[{{ $pIdx }}]" id="send_email_{{ $pIdx }}" value="1" checked>
                                                            <label class="form-check-label small text-dark" for="send_email_{{ $pIdx }}">
                                                                <i class="ti ti-mail text-info me-1"></i> Send credentials to email
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <p class="ni-section-title">3. AMC (Optional) <span class="text-muted">- Annual Maintenance Contract</span></p>
                            <div class="amc-optional-section">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="has_amc" name="has_amc" value="yes" @checked($hasAmc)>
                                            <label class="form-check-label" for="has_amc"><strong>Include AMC for this project</strong></label>
                                            <small class="text-muted d-block">Check this if you want to add Annual Maintenance Contract details</small>
                                        </div>
                                    </div>
                                </div>
                                <div id="amcFields" class="amc-fields" style="{{ $hasAmc ? '' : 'display: none;' }}">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-3">
                                            <label>Financial Year <span class="text-danger">*</span></label>
                                            <select name="fy_start_year" id="fy_start_year" class="form-control">
                                                @foreach ($fyOptions as $fy)
                                                    <option value="{{ $fy['start_year'] }}" data-start="{{ $fy['start'] }}" data-end="{{ $fy['end'] }}" @selected($selectedFy === $fy['start_year'])>FY {{ $fy['label'] }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Allowed: <span id="fy_range_hint">{{ $selectedFyPeriod['start'] }} - {{ $selectedFyPeriod['end'] }}</span></small>
                                        </div>
                                        <div class="col-md-3">
                                            <label>AMC Start Date</label>
                                            <input type="date" name="amc_start" id="amc_start" class="form-control" min="{{ $selectedFyPeriod['start'] }}" max="{{ $selectedFyPeriod['end'] }}" value="{{ old('amc_start', $project->amc_start ?? '') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>AMC End Date</label>
                                            <input type="date" name="amc_end" id="amc_end" class="form-control" min="{{ $selectedFyPeriod['start'] }}" max="{{ $selectedFyPeriod['end'] }}" value="{{ old('amc_end', $project->amc_end ?? '') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Working Days (Mon-Fri)</label>
                                            <input type="number" name="amc_working_days" id="amc_working_days" class="form-control" readonly value="{{ old('amc_working_days', $project->amc_working_days ?? 0) }}">
                                            <small class="text-muted" id="days_hint">5-day working week</small>
                                        </div>
                                    </div>
                                    <div id="amcOnlyPaymentBox" class="row g-3 mb-3" style="{{ $engagementType === 'amc_only' ? '' : 'display:none;' }}">
                                        <div class="col-md-4">
                                            <label>AMC Payment Split</label>
                                            <select name="amc_split" id="amc_split" class="form-control">
                                                <option value="single" @selected(old('amc_split', 'single') === 'single')>Single (100% once)</option>
                                                <option value="half_yearly" @selected(old('amc_split') === 'half_yearly')>Half-yearly (50% + 50%)</option>
                                                <option value="quarterly" @selected(old('amc_split') === 'quarterly')>Quarterly (4 x 25%)</option>
                                                <option value="custom" @selected(old('amc_split') === 'custom')>Custom rows</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>First Payment Due Date</label>
                                            <input type="date" name="amc_fee_due_date" id="amc_fee_due_date" class="form-control" value="{{ old('amc_fee_due_date') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="engagement_type" id="engagement_type" value="{{ $engagementType }}">
                            <input type="hidden" name="total_with_tax" id="total_with_tax_hidden" value="{{ old('total_with_tax', $project->total_with_tax ?? 0) }}">
                            <input type="hidden" name="final_amount" id="final_amount_hidden" value="{{ old('final_amount', $project->final_amount ?? 0) }}">

                            <p class="ni-section-title">4. Payment Milestones</p>
                            <div id="amcOnlyMilestoneNote" class="alert alert-info py-2" style="{{ $engagementType === 'amc_only' ? '' : 'display:none;' }}">
                                AMC Only: payment rows are created automatically for Single, Half-yearly, and Quarterly splits. Choose Custom to add rows manually.
                            </div>
                            <div id="projectMilestonesToolbar" class="row g-3 mb-3 align-items-end" style="{{ $engagementType === 'amc_only' ? 'display:none;' : '' }}">
                                <div class="col-md-4">
                                    <label>Payment Breakup Type</label>
                                    <p class="text-muted small mb-1" id="breakupHint">
                                        <strong>Percentage:</strong> enter % per row - amount fills from total value.
                                        <strong>Module:</strong> enter module name and amount per row.
                                    </p>
                                    <select name="payment_mode" id="payment_mode" class="form-control">
                                        <option value="percentage" @selected(old('payment_mode', $project->payment_mode ?? 'percentage') === 'percentage')>Percentage wise (e.g. 30-40-30)</option>
                                        <option value="module" @selected(old('payment_mode', $project->payment_mode ?? '') === 'module')>Module wise</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row ni-milestone-head d-none d-md-flex mb-2 px-1">
                                <div class="col-md-3">Milestone Label *</div>
                                <div class="col-md-2 module-col">Module</div>
                                <div class="col-md-2 pct-col">Percentage</div>
                                <div class="col-md-2">Amount (Rs)</div>
                                <div class="col-md-2">Amount with Tax (Rs)</div>
                                <div class="col-md-1">Due Date</div>
                            </div>

                            <div id="milestones">
                                @foreach ($rows as $idx => $row)
                                    <div class="row g-2 milestone-row align-items-center">
                                        <div class="col-md-3">
                                            <label class="d-md-none small">Milestone</label>
                                            <input type="text" name="milestone_label[]" class="form-control" placeholder="e.g. Advance" @if ($idx === 0) required @endif value="{{ $row->milestone_label ?? '' }}">
                                        </div>
                                        <div class="col-md-2 module-col">
                                            <label class="d-md-none small">Module</label>
                                            <input type="text" name="module_name[]" class="form-control" placeholder="Module name" value="{{ $row->module_name ?? '' }}">
                                        </div>
                                        <div class="col-md-2 pct-col">
                                            <label class="d-md-none small">%</label>
                                            <input type="number" step="0.01" min="0" max="100" name="percentage[]" class="form-control pct-input" placeholder="%" value="{{ $row->percentage ?? '' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="d-md-none small">Amount</label>
                                            <input type="number" step="0.01" min="0" name="milestone_amount[]" class="form-control amt-input milestone-amount" placeholder="Amount" value="{{ $row->amount ?? '' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="d-md-none small">With Tax</label>
                                            <div class="form-control bg-white" style="font-weight:600; background-color: #f0f0f0 !important;">
                                                <span class="tax-after-tax" data-base="{{ $row->amount ?? '' }}">-</span>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="d-md-none small">Due</label>
                                            <input type="date" name="due_date[]" class="form-control" value="{{ $row->due_date ?? '' }}">
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row mt-md-4" @if ($idx === 0) style="display:none" @endif>x</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="milestoneActions" class="d-flex flex-wrap justify-content-between align-items-center mt-3 mb-4 gap-2">
                                <button type="button" class="btn btn-success btn-sm" id="addMilestone">+ Add Milestone</button>
                                <div class="pct-col" id="pctSummary">
                                    <span class="text-muted">Total percentage:</span>
                                    <strong id="totalPercentage" class="ok">0%</strong>
                                    <span class="text-muted"> / 100%</span>
                                </div>
                            </div>
                            <div id="milestoneValidationMsg" class="alert alert-danger py-2 mt-2" style="display: none;">
                                Total milestone amount cannot exceed Total Project Value!
                            </div>

                            <p class="ni-section-title">5. Other Documents & Notes</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label>Upload Proposal (PDF / DOC)</label>
                                    <input type="file" name="proposal_file" class="form-control" accept=".pdf,.doc,.docx">
                                </div>
                                <div class="col-md-6">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Project scope, deliverables, terms...">{{ old('notes', $project->notes ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <button type="submit" class="btn btn-primary px-4" id="saveBtn">{{ $isEdit ? 'Update Project' : 'Save Project' }}</button>
                                <a href="{{ route('invoice.projects') }}" class="btn btn-link">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        (function () {
            function generateRandomPassword(len) {
                len = len || 8;
                var chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
                var pass = '';
                for (var i = 0; i < len; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return pass;
            }

            function reindexConcernPersons() {
                $('.concern-person-card').each(function (idx) {
                    $(this).attr('data-person-index', idx);
                    $(this).find('.person-index-badge').text('Person #' + (idx + 1));
                    $(this).find('.primary-radio').val(idx).attr('id', 'primary_person_' + idx);
                    $(this).find('label[for^="primary_person_"]').attr('for', 'primary_person_' + idx);
                    $(this).find('.can-login-toggle').attr('name', 'concern_can_login[' + idx + ']').attr('id', 'can_login_' + idx);
                    $(this).find('label[for^="can_login_"]').attr('for', 'can_login_' + idx);
                    $(this).find('.send-email-toggle').attr('name', 'concern_send_email[' + idx + ']').attr('id', 'send_email_' + idx);
                    $(this).find('label[for^="send_email_"]').attr('for', 'send_email_' + idx);
                });

                if ($('.concern-person-card').length === 1) {
                    $('.concern-person-card .remove-person-btn').hide();
                } else {
                    $('.concern-person-card .remove-person-btn').show();
                }

                if ($('.primary-radio:checked').length === 0) {
                    $('.primary-radio:first').prop('checked', true);
                }
            }

            $('#addConcernPersonBtn').on('click', function () {
                var newIdx = $('.concern-person-card').length;
                var $clone = $('.concern-person-card:first').clone();
                $clone.find('input[type="text"], input[type="email"], input[type="password"], input[name="concern_person_id[]"]').val('');
                $clone.find('.primary-radio').prop('checked', false);
                $clone.find('.can-login-toggle').prop('checked', true);
                $clone.find('.send-email-toggle').prop('checked', true);
                $clone.find('.login-fields-box').show();
                $('#concernPersonsContainer').append($clone);
                reindexConcernPersons();
            });

            $(document).on('click', '.remove-person-btn', function () {
                if ($('.concern-person-card').length > 1) {
                    $(this).closest('.concern-person-card').remove();
                    reindexConcernPersons();
                }
            });

            $(document).on('change', '.can-login-toggle', function () {
                var isChecked = $(this).is(':checked');
                $(this).closest('.concern-person-card').find('.login-fields-box').toggle(isChecked);
            });

            $(document).on('click', '.generate-pwd-btn', function () {
                var $pwd = $(this).closest('.input-group').find('.password-input');
                $pwd.val(generateRandomPassword(10));
            });

            function isAmcOnly() {
                return $('#work_type_id option:selected').data('amc-type') === 'amc_only';
            }

            function toggleEngagementType() {
                var amc = isAmcOnly();
                $('#engagement_type').val(amc ? 'amc_only' : 'project');
                if (!$('#work_type_id').val()) {
                    $('#workTypeHint').text('AMC Type is configured in Work Type Master.');
                } else if (amc) {
                    $('#workTypeHint').html('<span class="text-info">AMC Only:</span> maintenance or renewal only - payment split will appear below.');
                } else {
                    $('#workTypeHint').html('<span class="text-secondary">Project Work:</span> milestones and payment breakup below.');
                }
                $('#amcOnlyPaymentBox').toggle(amc);
                $('#amcOnlyMilestoneNote').toggle(amc);
                $('#section1Title').text(amc ? '1. AMC Details' : '1. Project Details');
                $('#projectNameLabel').html(amc ? 'AMC Title <span class="text-danger">*</span>' : 'Project Name <span class="text-danger">*</span>');
                $('#project_name').attr('placeholder', amc ? 'e.g. Website AMC FY 2025-26' : 'e.g. Website Development');
                $('#totalValueLabel').text(amc ? 'AMC Annual Fee (Rs)' : 'Total Project Value (Rs)');
                $('#totalValueHint').text(amc ? 'Yearly AMC amount for this client' : 'Enter base amount without tax');
                var hideMilestones = amc && $('#amc_split').val() !== 'custom';
                $('#projectMilestonesToolbar').toggle(!amc);
                $('#milestones, .ni-milestone-head, #milestoneActions').toggle(!hideMilestones);
                toggleMode();
            }

            function loadClientCountryState() {
                var clientId = parseInt($('#client_id').val() || '0', 10);
                if (!clientId) {
                    $('#client_country, #client_state').val('');
                    recalcTaxDetails();
                    return;
                }
                $.ajax({
                    url: '{{ route('invoice.invoices.client-details') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: { _token: '{{ csrf_token() }}', client_id: clientId }
                }).done(function (r) {
                    $('#client_country').val((r && r.ok) ? (r.country_name || '') : '');
                    $('#client_state').val((r && r.ok) ? (r.state_name || '') : '');
                    
                    // Auto-fill first concern person if name is empty
                    if (r && r.ok && r.concern_person) {
                        var $firstCard = $('.concern-person-card:first');
                        if (!$firstCard.find('.concern-name-input').val()) {
                            $firstCard.find('.concern-name-input').val(r.concern_person);
                            if (r.email) $firstCard.find('.concern-email-input').val(r.email);
                            if (r.mobile) $firstCard.find('.concern-mobile-input').val(r.mobile);
                        }
                    }

                    recalcTaxDetails();
                }).fail(function () {
                    $('#client_country, #client_state').val('');
                    recalcTaxDetails();
                });
            }

            function toggleMode() {
                if (isAmcOnly()) return;
                if ($('#payment_mode').val() === 'module') {
                    $('.pct-col').hide();
                    $('#pctSummary').hide();
                    $('.module-col').show();
                    $('#breakupHint').html('<strong>Module wise:</strong> add module name and fixed amount for each milestone row.');
                } else {
                    $('.pct-col').show();
                    $('#pctSummary').show();
                    $('.module-col').hide();
                    $('#breakupHint').html('<strong>Percentage wise:</strong> enter % per row - amount auto-calculates from total project value. Total should be 100%.');
                    updatePctTotal();
                }
            }

            function addWorkingDays(startDate, days) {
                var currentDate = new Date(startDate);
                var remainingDays = days;
                while (remainingDays > 0) {
                    currentDate.setDate(currentDate.getDate() + 1);
                    var dayOfWeek = currentDate.getDay();
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) remainingDays--;
                }
                return currentDate;
            }

            function countWorkingDays(startDate, endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);
                var count = 0;
                while (start <= end) {
                    var day = start.getDay();
                    if (day !== 0 && day !== 6) count++;
                    start.setDate(start.getDate() + 1);
                }
                return count;
            }

            function applyFyBounds() {
                var opt = $('#fy_start_year option:selected');
                var minD = opt.data('start');
                var maxD = opt.data('end');
                $('#amc_start, #amc_end').attr('min', minD).attr('max', maxD);
                $('#fy_range_hint').text(minD + ' - ' + maxD);
            }

            function recalcWorkingDays() {
                var s = $('#amc_start').val();
                var e = $('#amc_end').val();
                if (!s || !e) return;
                $('#amc_working_days').val(countWorkingDays(s, e));
                $('#days_hint').text('5-day working week');
            }

            function updatePctTotal() {
                var sum = 0;
                $('.pct-input').each(function () { sum += parseFloat($(this).val()) || 0; });
                $('#totalPercentage').text(sum.toFixed(2) + '%').toggleClass('over', sum > 100).toggleClass('ok', sum <= 100);
            }

            function recalcRowAmount($row) {
                var total = parseFloat($('#total_value').val()) || 0;
                var pct = parseFloat($row.find('.pct-input').val()) || 0;
                if ($('#payment_mode').val() === 'percentage' && total > 0 && pct > 0) {
                    $row.find('.amt-input').val((total * pct / 100).toFixed(2));
                }
            }

            function validateMilestoneTotal() {
                var totalValue = parseFloat($('#total_value').val()) || 0;
                var totalMilestone = 0;
                $('.milestone-amount').each(function () { totalMilestone += parseFloat($(this).val()) || 0; });
                var exceeds = totalValue > 0 && totalMilestone > totalValue;
                $('#milestoneValidationMsg').toggle(exceeds);
                $('#saveBtn').prop('disabled', exceeds);
                return !exceeds;
            }

            function recalcTaxDetails() {
                var totalValue = parseFloat($('#total_value').val() || '0');
                var countryName = ($('#client_country').val() || '').toLowerCase();
                var stateName = ($('#client_state').val() || '').toLowerCase();
                var cgstPercent = 0, sgstPercent = 0, igstPercent = 0;
                $('#cgstBox, #sgstBox, #igstBox').hide();
                if (countryName && countryName !== 'india') {
                    // no tax
                } else if (stateName === 'uttar pradesh') {
                    cgstPercent = 9; sgstPercent = 9; $('#cgstBox, #sgstBox').show();
                } else {
                    igstPercent = 18; $('#igstBox').show();
                }
                var cgstAmount = totalValue * cgstPercent / 100;
                var sgstAmount = totalValue * sgstPercent / 100;
                var igstAmount = totalValue * igstPercent / 100;
                var totalTax = cgstAmount + sgstAmount + igstAmount;
                var totalPercent = cgstPercent + sgstPercent + igstPercent;
                $('#cgst_display').val(cgstPercent + '% = Rs ' + cgstAmount.toFixed(2));
                $('#sgst_display').val(sgstPercent + '% = Rs ' + sgstAmount.toFixed(2));
                $('#igst_display').val(igstPercent + '% = Rs ' + igstAmount.toFixed(2));
                $('#total_tax_display').val(totalPercent + '% = Rs ' + totalTax.toFixed(2));
                $('#total_value_with_tax, #total_with_tax_hidden, #final_amount_hidden').val((totalValue + totalTax).toFixed(2));
                $('.milestone-row').each(function () {
                    var amount = parseFloat($(this).find('.amt-input').val()) || 0;
                    var rowTax = amount * totalPercent / 100;
                    $(this).find('.tax-after-tax').html(amount > 0 ? (amount + rowTax).toFixed(2) : '-');
                });
            }

            $('#has_amc').on('change', function () { $('#amcFields').slideToggle($(this).is(':checked')); });
            $('#work_type_id, #amc_split').on('change', toggleEngagementType);
            $('#client_id').on('change', loadClientCountryState);
            $('#payment_mode').on('change', toggleMode);
            $('#fy_start_year').on('change', function () { applyFyBounds(); recalcWorkingDays(); });
            $('#amc_start, #amc_end').on('change', recalcWorkingDays);
            $('#project_duration_days, #project_start_date').on('change', function () {
                var startDate = $('#project_start_date').val();
                var durationDays = parseInt($('#project_duration_days').val());
                if (startDate && durationDays && durationDays > 0) {
                    $('#project_end_date').val(addWorkingDays(startDate, durationDays).toISOString().split('T')[0]);
                }
            });
            $('#total_value').on('input', function () {
                $('.milestone-row').each(function () { recalcRowAmount($(this)); });
                recalcTaxDetails();
                validateMilestoneTotal();
            });
            $('#milestones').on('input', '.pct-input', function () {
                recalcRowAmount($(this).closest('.milestone-row'));
                updatePctTotal();
                recalcTaxDetails();
                validateMilestoneTotal();
            });
            $('#milestones').on('input', '.amt-input', function () { recalcTaxDetails(); validateMilestoneTotal(); });
            $('#addMilestone').on('click', function () {
                var $row = $('.milestone-row:first').clone();
                $row.find('input').val('');
                $row.find('.remove-row').show();
                $('#milestones').append($row);
                toggleMode();
            });
            $('#milestones').on('click', '.remove-row', function () {
                if ($('.milestone-row').length > 1) $(this).closest('.milestone-row').remove();
                updatePctTotal();
                recalcTaxDetails();
                validateMilestoneTotal();
            });
            $('#projectForm').on('submit', function (e) {
                if (!validateMilestoneTotal()) {
                    e.preventDefault();
                    alert('Total milestone amount cannot exceed Total Project Value.');
                }
            });

            toggleEngagementType();
            applyFyBounds();
            loadClientCountryState();
            updatePctTotal();
            recalcTaxDetails();
            validateMilestoneTotal();
            reindexConcernPersons();
        })();
    </script>
@endpush
