@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 font-weight-bold">{{ $title }}</h2>
                    <span class="badge {{ $increment->status_badge_class }} px-2 py-1 fs-12">
                        {{ $increment->status }}
                    </span>
                    @if($increment->isPromotion())
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-12">
                            <i class="ti ti-arrow-big-up-line me-1"></i> Promotion Included
                        </span>
                    @endif
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.increments.index') }}" class="text-secondary">Annual Increments</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $increment->increment_number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <a href="{{ route('payroll.increments.letter', $increment->id) }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center shadow-sm">
                    <i class="ti ti-file-text me-1"></i> View Letter
                </a>
                <a href="{{ route('payroll.increments.download-letter', $increment->id) }}" class="btn btn-success text-white d-flex align-items-center shadow-sm">
                    <i class="ti ti-download me-1"></i> Download PDF Letter
                </a>
                <a href="{{ route('payroll.increments.index') }}" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Employee & Increment Header Banner -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-xl rounded-circle bg-primary-subtle text-primary font-weight-bold d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; font-size: 20px;">
                                {{ strtoupper(substr($increment->employee->employee_name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="mb-1 font-weight-bold text-dark">{{ $increment->employee->employee_name }}</h4>
                                <div class="text-muted fs-13">
                                    <span>Employee ID: <strong>{{ $increment->employee->employee_id }}</strong></span> &bull;
                                    <span>Agency: <strong>{{ $increment->employee->agency->agency_name ?? 'Direct Payroll' }}</strong></span>
                                </div>
                                <div class="mt-1 fs-13 text-secondary">
                                    <i class="ti ti-mail me-1"></i> {{ $increment->employee->employee_email }} &bull;
                                    <i class="ti ti-phone me-1"></i> {{ $increment->employee->employee_mobile }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 border-start-md ps-md-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-muted fs-11 text-uppercase font-weight-bold">Review Cycle</div>
                                <div class="font-weight-bold text-dark fs-14 mt-1">{{ $increment->review_cycle }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-11 text-uppercase font-weight-bold">Effective Date</div>
                                <div class="font-weight-bold text-dark fs-14 mt-1">
                                    <i class="ti ti-calendar text-primary me-1"></i>{{ $increment->effective_date->format('d M, Y') }}
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="text-muted fs-11 text-uppercase font-weight-bold">Hike Rate &amp; Amount</div>
                                <div class="font-weight-bold text-success fs-14 mt-1">
                                    +{{ number_format($increment->hike_percentage, 1) }}% (+&#8377; {{ number_format($increment->hike_amount, 2) }}/yr)
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="text-muted fs-11 text-uppercase font-weight-bold">Performance Rating</div>
                                <div class="font-weight-bold text-dark fs-14 mt-1">
                                    <span class="badge bg-light text-dark border fs-12">{{ $increment->performance_rating ?: 'Satisfactory' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side-by-Side Comparison Matrix -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 font-weight-bold text-dark">
                    <i class="ti ti-scale text-primary me-2"></i> Pre-Revision vs Post-Revision Breakdown
                </h5>
                <span class="badge bg-light text-secondary border fs-12">Comparative Financial Audit</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Component</th>
                                <th class="text-end">Previous (Pre-Revision)</th>
                                <th class="text-end">Revised (Post-Revision)</th>
                                <th class="text-end pe-4">Monthly Increase (+ Diff)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $prevStruct = $increment->previousSalaryStructure;
                                $newStruct = $increment->newSalaryStructure;

                                $prevGross = (float) $increment->current_gross;
                                $newGross = (float) $increment->new_gross;
                                $grossDiff = max(0, $newGross - $prevGross);

                                $prevCtc = (float) $increment->current_ctc;
                                $newCtc = (float) $increment->new_ctc;
                                $ctcDiff = max(0, $newCtc - $prevCtc);

                                $prevBasic = $prevStruct ? (float)$prevStruct->basic_salary : 0;
                                $newBasic = $newStruct ? (float)$newStruct->basic_salary : 0;

                                $prevHra = $prevStruct ? (float)$prevStruct->hra : 0;
                                $newHra = $newStruct ? (float)$newStruct->hra : 0;

                                $prevSpecial = $prevStruct ? (float)$prevStruct->special_allowance : 0;
                                $newSpecial = $newStruct ? (float)$newStruct->special_allowance : 0;

                                $prevConv = $prevStruct ? (float)$prevStruct->conveyance_allowance : 0;
                                $newConv = $newStruct ? (float)$newStruct->conveyance_allowance : 0;

                                $prevMed = $prevStruct ? (float)$prevStruct->medical_allowance : 0;
                                $newMed = $newStruct ? (float)$newStruct->medical_allowance : 0;

                                $prevPf = $prevStruct ? (float)$prevStruct->pf_employee : 0;
                                $newPf = $newStruct ? (float)$newStruct->pf_employee : 0;

                                $prevEsi = $prevStruct ? (float)$prevStruct->esi_employee : 0;
                                $newEsi = $newStruct ? (float)$newStruct->esi_employee : 0;

                                $prevPt = $prevStruct ? (float)$prevStruct->professional_tax : 0;
                                $newPt = $newStruct ? (float)$newStruct->professional_tax : 0;

                                $prevDed = $prevPf + $prevEsi + $prevPt;
                                $newDed = $newPf + $newEsi + $newPt;

                                $prevNet = max(0, $prevGross - $prevDed);
                                $newNet = max(0, $newGross - $newDed);
                                $netDiff = max(0, $newNet - $prevNet);
                            @endphp

                            <tr>
                                <td class="ps-4 font-weight-bold text-dark">Designation / Role</td>
                                <td class="text-end text-muted">{{ $increment->previousDesignation->name ?? 'Staff' }}</td>
                                <td class="text-end font-weight-bold text-dark">
                                    {{ $increment->newDesignation->name ?? ($increment->previousDesignation->name ?? 'Staff') }}
                                    @if($increment->isPromotion())
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11 ms-1">Promoted</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 text-muted">-</td>
                            </tr>

                            <tr class="table-light">
                                <td class="ps-4 font-weight-bold text-primary" colspan="4">Earnings Breakdown (Monthly)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Basic Salary</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevBasic, 2) }}</td>
                                <td class="text-end font-weight-bold text-dark">&#8377; {{ number_format($newBasic, 2) }}</td>
                                <td class="text-end pe-4 text-success">+&#8377; {{ number_format($newBasic - $prevBasic, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">House Rent Allowance (HRA)</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevHra, 2) }}</td>
                                <td class="text-end font-weight-bold text-dark">&#8377; {{ number_format($newHra, 2) }}</td>
                                <td class="text-end pe-4 text-success">+&#8377; {{ number_format($newHra - $prevHra, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Special Allowance</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevSpecial, 2) }}</td>
                                <td class="text-end font-weight-bold text-dark">&#8377; {{ number_format($newSpecial, 2) }}</td>
                                <td class="text-end pe-4 text-success">+&#8377; {{ number_format($newSpecial - $prevSpecial, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Conveyance Allowance</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevConv, 2) }}</td>
                                <td class="text-end font-weight-bold text-dark">&#8377; {{ number_format($newConv, 2) }}</td>
                                <td class="text-end pe-4 text-muted">+&#8377; {{ number_format($newConv - $prevConv, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Medical Allowance</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevMed, 2) }}</td>
                                <td class="text-end font-weight-bold text-dark">&#8377; {{ number_format($newMed, 2) }}</td>
                                <td class="text-end pe-4 text-muted">+&#8377; {{ number_format($newMed - $prevMed, 2) }}</td>
                            </tr>

                            <tr class="table-primary-subtle font-weight-bold">
                                <td class="ps-4 fs-14">Monthly Gross Salary</td>
                                <td class="text-end fs-14 text-muted text-decoration-line-through">&#8377; {{ number_format($prevGross, 2) }}</td>
                                <td class="text-end fs-15 text-primary">&#8377; {{ number_format($newGross, 2) }}</td>
                                <td class="text-end pe-4 fs-14 text-success font-weight-bold">+&#8377; {{ number_format($grossDiff, 2) }} / mo</td>
                            </tr>

                            <tr class="table-light">
                                <td class="ps-4 font-weight-bold text-danger" colspan="4">Statutory Deductions (Monthly)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Provident Fund (PF)</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevPf, 2) }}</td>
                                <td class="text-end font-weight-bold text-danger">&#8377; {{ number_format($newPf, 2) }}</td>
                                <td class="text-end pe-4 text-muted">&#8377; {{ number_format($newPf - $prevPf, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">ESI Employee Deduction</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevEsi, 2) }}</td>
                                <td class="text-end font-weight-bold text-danger">&#8377; {{ number_format($newEsi, 2) }}</td>
                                <td class="text-end pe-4 text-muted">&#8377; {{ number_format($newEsi - $prevEsi, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Professional Tax (PT)</td>
                                <td class="text-end text-muted">&#8377; {{ number_format($prevPt, 2) }}</td>
                                <td class="text-end font-weight-bold text-danger">&#8377; {{ number_format($newPt, 2) }}</td>
                                <td class="text-end pe-4 text-muted">&#8377; {{ number_format($newPt - $prevPt, 2) }}</td>
                            </tr>

                            <tr class="table-success-subtle font-weight-bold">
                                <td class="ps-4 fs-15 text-success">Net Monthly In-Hand (Take-Home)</td>
                                <td class="text-end fs-14 text-muted text-decoration-line-through">&#8377; {{ number_format($prevNet, 2) }}</td>
                                <td class="text-end fs-16 text-success">&#8377; {{ number_format($newNet, 2) }}</td>
                                <td class="text-end pe-4 fs-15 text-success font-weight-bold">+&#8377; {{ number_format($netDiff, 2) }} / mo</td>
                            </tr>

                            <tr class="table-dark text-white font-weight-bold">
                                <td class="ps-4 fs-15">Annual Cost to Company (CTC)</td>
                                <td class="text-end fs-14 text-light text-decoration-line-through">&#8377; {{ number_format($prevCtc, 2) }}</td>
                                <td class="text-end fs-16 text-warning">&#8377; {{ number_format($newCtc, 2) }}</td>
                                <td class="text-end pe-4 fs-15 text-success font-weight-bold">+&#8377; {{ number_format($ctcDiff, 2) }} / yr</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($increment->remarks || $increment->letter_notes)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="ti ti-notes text-primary me-2"></i> Review Notes &amp; Letter Message
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($increment->remarks)
                        <div class="mb-3">
                            <label class="text-muted fs-11 text-uppercase font-weight-bold">Internal HR Remarks</label>
                            <div class="p-3 bg-light rounded text-dark fs-13 mt-1">{{ $increment->remarks }}</div>
                        </div>
                    @endif
                    @if($increment->letter_notes)
                        <div>
                            <label class="text-muted fs-11 text-uppercase font-weight-bold">Personalized Increment Letter Note</label>
                            <div class="p-3 bg-primary-subtle text-dark fs-13 rounded border border-primary-subtle mt-1 font-italic">
                                "{{ $increment->letter_notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
