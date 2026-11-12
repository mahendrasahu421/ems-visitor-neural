@extends('employee.layout.layout')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <h2 class="mb-1 font-weight-bold">{{ $title }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}"><i class="ti ti-smart-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item">Payroll</li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">My Increments &amp; Appraisals</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Current Active Salary Overview Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #ffffff;">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-xl rounded-circle bg-white text-primary font-weight-bold d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 20px;">
                                <i class="ti ti-award fs-28"></i>
                            </div>
                            <div>
                                <h4 class="text-white mb-1 font-weight-bold">{{ $employee->employee_name }}</h4>
                                <div class="text-white-50 fs-13">
                                    <span>Designation: <strong>{{ $employee->designation->name ?? 'Staff' }}</strong></span> &bull;
                                    <span>Emp ID: <strong>{{ $employee->employee_id }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 border-start-md border-white-50 ps-md-4">
                        <div class="row g-2 text-center text-md-start">
                            <div class="col-sm-6">
                                <div class="text-white-50 fs-12 font-weight-bold text-uppercase">Current Monthly Gross</div>
                                <h3 class="text-white font-weight-bold mb-0 mt-1">
                                    &#8377; {{ number_format($currentStructure?->monthly_gross ?? 0, 2) }}
                                </h3>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-white-50 fs-12 font-weight-bold text-uppercase">Current Annual CTC</div>
                                <h3 class="text-warning font-weight-bold mb-0 mt-1">
                                    &#8377; {{ number_format($currentStructure?->ctc_annual ?? 0, 2) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Increments & Appraisal History Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 font-weight-bold text-dark">
                    <i class="ti ti-history text-primary me-2"></i> Annual Increment &amp; Revision History
                </h5>
                <span class="badge bg-light text-secondary border fs-12">Total: {{ $increments->count() }} Revision(s)</span>
            </div>
            <div class="card-body p-0">
                @if($increments->isEmpty())
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-light text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ti ti-clock fs-32"></i>
                        </div>
                        <h5 class="text-dark font-weight-bold">No Annual Increments Processed Yet</h5>
                        <p class="text-muted fs-13 mb-0">Your appraisal revision records and increment letters will appear here once processed by HR.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Increment No</th>
                                    <th>Review Cycle</th>
                                    <th>Effective Date</th>
                                    <th>Pre-Revision Gross</th>
                                    <th>Revised Gross</th>
                                    <th>Hike Rate (%)</th>
                                    <th>Designation / Promotion</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($increments as $inc)
                                    <tr>
                                        <td class="ps-4 font-weight-bold text-dark fs-13">{{ $inc->increment_number }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border fs-12">{{ $inc->review_cycle }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary fs-13"><i class="ti ti-calendar me-1"></i>{{ $inc->effective_date->format('d M, Y') }}</span>
                                        </td>
                                        <td class="text-muted text-decoration-line-through fs-13">
                                            &#8377; {{ number_format($inc->current_gross, 2) }}
                                        </td>
                                        <td class="font-weight-bold text-dark fs-14">
                                            &#8377; {{ number_format($inc->new_gross, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-weight-bold px-2 py-1 fs-12">
                                                <i class="ti ti-arrow-up-right me-1"></i>+{{ number_format($inc->hike_percentage, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            @if($inc->isPromotion())
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11 mb-1 d-inline-block">
                                                    <i class="ti ti-arrow-big-up-line me-1"></i>Promoted
                                                </span>
                                                <div class="font-weight-bold text-dark fs-12">{{ $inc->newDesignation->name }}</div>
                                            @else
                                                <span class="text-dark fs-13">{{ $inc->newDesignation->name ?? ($inc->previousDesignation->name ?? 'Staff') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('employee.increments.download-letter', $inc->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                                <i class="ti ti-download me-1"></i> Increment Letter
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
