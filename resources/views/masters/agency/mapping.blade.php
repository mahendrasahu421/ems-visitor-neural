@extends('layout.master')
@section('title', $title ?? 'Employee Mapping with Agency')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .mapping-page .stat-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .mapping-page .card-main {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .mapping-page .action-bar {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 20px;
        }

        .mapping-page .badge-pill-soft {
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
        }
    </style>

    <div class="page-wrapper mapping-page">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="ti ti-users-group text-primary"></i> Employee Mapping with Agency
                    </h3>
                    <nav>
                        <ol class="breadcrumb mb-0 fs-12">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('masters.organisation.agency') }}">Agencies</a></li>
                            <li class="breadcrumb-item active">Employee Mapping</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('masters.organisation.agency') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-2">
                        <i class="ti ti-arrow-left fs-16"></i> Back to Agencies
                    </a>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-20">
                            <i class="ti ti-users"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Total Employees</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ $totalCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 text-purple d-flex align-items-center justify-content-center fs-20" style="background-color: #f3e8ff; color: #7e22ce;">
                            <i class="ti ti-building"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Mapped to Agencies</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ $mappedCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card p-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md rounded-3 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center fs-20">
                            <i class="ti ti-user-check"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Direct / Unassigned</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ $unmappedCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Mapping Card -->
            <div class="card-main mb-4">
                <form action="{{ route('masters.organisation.agency.mapping.save') }}" method="POST" id="batchMappingForm">
                    @csrf

                    <!-- Filter & Batch Action Toolbar -->
                    <div class="action-bar d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <!-- Filters (Left) -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="input-group input-group-sm" style="width: 240px;">
                                <span class="input-group-text bg-white border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="empSearchInput" class="form-control bg-white border-start-0 ps-0" placeholder="Search employee...">
                            </div>

                            <select id="agencyFilterSelect" class="form-select form-select-sm" style="width: 220px;" onchange="window.location.href = this.value">
                                <option value="{{ route('masters.organisation.agency.mapping') }}">All Employees (Filter by Agency)</option>
                                <option value="{{ route('masters.organisation.agency.mapping', ['filter_agency' => 'unassigned']) }}" {{ request('filter_agency') === 'unassigned' ? 'selected' : '' }}>
                                    -- Direct / Unassigned Only --
                                </option>
                                @foreach ($agencies as $agency)
                                    <option value="{{ route('masters.organisation.agency.mapping', ['filter_agency' => $agency->id]) }}" {{ request('filter_agency') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->agency_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Batch Actions (Right) -->
                        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                            <span class="text-muted fs-12 fw-medium" id="selectedCountBadge">0 selected</span>
                            <select name="target_agency_id" id="targetAgencySelect" class="form-select form-select-sm" style="width: 230px;" required>
                                <option value="">-- Choose Agency to Assign --</option>
                                <option value="unassign">Direct / In-House (Unassign)</option>
                                @foreach ($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" id="applyMappingBtn" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 fw-semibold px-3" disabled>
                                <i class="ti ti-check fs-14"></i> Apply Assignment
                            </button>
                        </div>
                    </div>

                    <!-- Employee Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="mappingTable">
                            <thead class="bg-light border-bottom text-muted fs-12 text-uppercase">
                                <tr>
                                    <th class="ps-4" style="width: 40px;">
                                        <div class="form-check form-check-md mb-0">
                                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                        </div>
                                    </th>
                                    <th>Employee</th>
                                    <th>Assigned Agency</th>
                                    <th>Designation</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    <tr class="emp-row" data-search="{{ strtolower($employee->employee_name . ' ' . $employee->employee_email . ' ' . ($employee->agency->agency_name ?? '')) }}">
                                        <td class="ps-4">
                                            <div class="form-check form-check-md mb-0">
                                                <input class="form-check-input empCheckbox" type="checkbox" name="employee_ids[]" value="{{ $employee->employee_id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                @if ($employee->profile_image && $employee->profile_image !== 'default.png')
                                                    <img src="{{ asset('uploads/employees/' . $employee->profile_image) }}" alt="{{ $employee->employee_name }}" class="rounded-circle border" width="36" height="36" style="object-fit: cover;">
                                                @else
                                                    <div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-12">
                                                        {{ strtoupper(substr($employee->employee_name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">{{ $employee->employee_name }}</div>
                                                    <div class="text-muted fs-11">{{ $employee->employee_email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($employee->agency)
                                                <span class="badge border fs-11" style="background-color: #f3e8ff; color: #7e22ce; border-color: #e9d5ff !important;">
                                                    <i class="ti ti-building me-1"></i>{{ $employee->agency->agency_name }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border fs-11">
                                                    Direct / In-House
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border fs-11">
                                                {{ $employee->designation->name ?? ($employee->designation->designation_name ?? 'Staff') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary font-monospace fs-11">
                                                {{ $employee->employment_type ?: 'Full Time' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fs-12 text-muted">
                                                <i class="ti ti-phone me-1"></i>{{ $employee->employee_mobile ?: '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php $isActive = ($employee->status === 'Active'); @endphp
                                            <span class="badge-pill-soft {{ $isActive ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border">
                                                {{ $isActive ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-users-off fs-28"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">No Employees Found</h6>
                                            <p class="text-muted fs-13 mb-0">Try selecting a different filter or adding employees first.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('after_scripts')
<script>
    $(document).ready(function() {
        // Live search filter
        $('#empSearchInput').on('keyup', function() {
            let val = $(this).val().toLowerCase().trim();
            $('.emp-row').each(function() {
                let searchData = $(this).data('search') || '';
                if (!val || searchData.includes(val)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Select All Checkbox
        $('#selectAllCheckbox').on('change', function() {
            let checked = $(this).is(':checked');
            $('.empCheckbox:visible').prop('checked', checked);
            updateSelectionState();
        });

        // Individual Checkbox change
        $(document).on('change', '.empCheckbox', function() {
            updateSelectionState();
        });

        function updateSelectionState() {
            let count = $('.empCheckbox:checked').length;
            $('#selectedCountBadge').text(count + ' selected');
            $('#applyMappingBtn').prop('disabled', count === 0);

            let allVisible = $('.empCheckbox:visible').length;
            let checkedVisible = $('.empCheckbox:visible:checked').length;
            $('#selectAllCheckbox').prop('checked', allVisible > 0 && allVisible === checkedVisible);
        }

        $('#batchMappingForm').on('submit', function(e) {
            let count = $('.empCheckbox:checked').length;
            if (count === 0) {
                e.preventDefault();
                alert('Please select at least one employee.');
                return false;
            }

            let agencyText = $('#targetAgencySelect option:selected').text();
            if (!$('#targetAgencySelect').val()) {
                e.preventDefault();
                alert('Please choose an agency to assign.');
                return false;
            }

            return confirm(`Are you sure you want to assign ${count} selected employee(s) to "${agencyText}"?`);
        });
    });
</script>
@endpush
