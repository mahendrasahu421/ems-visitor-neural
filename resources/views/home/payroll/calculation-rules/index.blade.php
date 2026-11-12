@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2 mb-md-0">
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-calculator text-primary"></i> Working Days &amp; Calculation Rules Master
                </h3>
                <nav>
                    <ol class="breadcrumb mb-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item">Masters / Payroll</li>
                        <li class="breadcrumb-item active">Calculation Rules</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('masters.payroll.calculation-rules.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="ti ti-plus"></i> Add Calculation Rule
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Active Default Policy Callout Card -->
        <div class="card border-0 shadow-sm mb-4 bg-primary-subtle border-start border-primary border-4">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success text-white px-2 py-1 fs-11">
                                <i class="ti ti-check me-1"></i> ACTIVE DEFAULT POLICY
                            </span>
                            <h5 class="fw-bold text-dark mb-0">{{ $activeRule->policy_name }}</h5>
                        </div>
                        <p class="text-muted fs-13 mb-0">
                            <strong>Working Days Divisor:</strong> <span class="text-primary fw-semibold">{{ $activeRule->basis_label }}</span> &bull; 
                            <strong>Weekly Offs:</strong> <span class="fw-semibold">{{ $activeRule->weekly_off_label }}</span> &bull; 
                            <strong>Leave / LWP Rule:</strong> Unpaid leaves (LWP) are deducted at <code>Per-Day Salary = Gross ÷ Divisor</code>.
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-primary border shadow-sm fs-12 px-3 py-2">
                            Applied automatically to all Payroll Processing
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rules List Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Configured Working Days &amp; Leave Deduction Policies</h5>
                <span class="text-muted small">Total: {{ count($rules) }} Policy Rule(s)</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40" class="text-center">#</th>
                                <th>Policy Name</th>
                                <th>Calculation Basis (Divisor)</th>
                                <th>Weekly Off Pattern</th>
                                <th>Leave Deduction Formula</th>
                                <th class="text-center">Active Status</th>
                                <th class="text-end" width="160">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rules as $index => $rule)
                                <tr class="{{ $rule->is_default ? 'table-success-subtle' : '' }}">
                                    <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">
                                            {{ $rule->policy_name }}
                                            @if($rule->is_default)
                                                <span class="badge bg-success ms-1 fs-10">Default</span>
                                            @endif
                                        </div>
                                        @if($rule->description)
                                            <div class="small text-muted text-truncate" style="max-width: 300px;">
                                                {{ $rule->description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rule->calculation_basis === 'fixed_26_days')
                                            <span class="badge bg-primary text-white">26 Days Fixed</span>
                                            <div class="small text-muted fs-11 mt-1">Daily = Gross ÷ 26</div>
                                        @elseif($rule->calculation_basis === 'calendar_days')
                                            <span class="badge bg-info text-dark">Calendar Month Days</span>
                                            <div class="small text-muted fs-11 mt-1">Daily = Gross ÷ (28/30/31)</div>
                                        @elseif($rule->calculation_basis === 'fixed_30_days')
                                            <span class="badge bg-secondary text-white">30 Days Fixed</span>
                                            <div class="small text-muted fs-11 mt-1">Daily = Gross ÷ 30</div>
                                        @else
                                            <span class="badge bg-warning text-dark">Actual Working Days</span>
                                            <div class="small text-muted fs-11 mt-1">Gross ÷ (Days - Offs - Hols)</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $rule->weekly_off_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-monospace fs-12 text-danger fw-semibold">
                                            LWP Deduction = LWP Days × (Gross ÷ {{ $rule->calculation_basis === 'fixed_26_days' ? '26' : ($rule->calculation_basis === 'fixed_30_days' ? '30' : 'Divisor') }})
                                        </div>
                                        <div class="small text-muted fs-11">
                                            Paid leaves (CL/SL/EL) are fully paid.
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($rule->is_default)
                                            <span class="badge bg-success px-3 py-1.5 fs-11 shadow-sm">
                                                <i class="ti ti-check me-1"></i> Active Default
                                            </span>
                                        @else
                                            <form action="{{ route('masters.payroll.calculation-rules.set-default', $rule->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success fs-11 py-1 px-2" title="Click to make this the active default rule">
                                                    <i class="ti ti-star me-1"></i> Set as Default
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('masters.payroll.calculation-rules.edit', $rule->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Rule">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @if(!$rule->is_default)
                                                <form action="{{ route('masters.payroll.calculation-rules.destroy', $rule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Delete Rule">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No calculation rules found. Click "Add Calculation Rule" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
