@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    @php
        $mappedByEmployee = collect($mappedEmployees)->keyBy('employee_id');
        $mappedIds = $mappedByEmployee->keys()->map(fn($id) => (int) $id)->all();
    @endphp

    <style>
        .mapping-page .card { border-radius: 8px; }
        .project-strip { border-left: 4px solid #3bb77e; background: #f8fafc; padding: 14px 16px; border-radius: 8px; }
        .employee-map-table th,
        .employee-map-table td { vertical-align: middle; }
        .employee-map-table .form-control { min-width: 120px; }
        .employee-map-table .role-input { min-width: 150px; }
        .employee-map-table .date-input { min-width: 140px; }
        .employee-row.is-selected { background: #f0fdf4; }
        .team-count { font-size: 13px; color: #64748b; }
    </style>

    <div class="page-wrapper">
        <div class="content">
            <div class="container mt-15 mapping-page">
                <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                    <div>
                        <h2 class="mb-1">{{ $title }}</h2>
                        <div class="team-count">Select the employees who will work on this project.</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('invoice.projects.show', $project->project_id) }}" class="btn btn-outline-primary">
                            <i class="ti ti-eye me-1"></i>View Project
                        </a>
                        <a href="{{ route('invoice.projects') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Project List
                        </a>
                    </div>
                </div>

                <div class="project-strip mb-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="text-muted small">Project</div>
                            <h5 class="mb-0">{{ $project->project_name }}</h5>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Client</div>
                            <strong>{{ $project->ClientName ?? '-' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Work Type</div>
                            <strong>{{ $project->work_type_display ?? $project->work_type ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <form action="{{ route('invoice.projects.employees.store', $project->project_id) }}" method="post" id="projectEmployeeMapForm">
                    @csrf
                    <div class="card">
                        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="mb-0">Project Team</h5>
                                <small class="text-muted"><span id="selectedCount">{{ count($mappedIds) }}</span> employees selected</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <input type="search" id="employeeSearch" class="form-control form-control-sm" placeholder="Search employee">
                                <button type="button" class="btn btn-sm btn-outline-success" id="selectAllVisible">
                                    <i class="ti ti-checks me-1"></i>Select Visible
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">
                                    <i class="ti ti-x me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0 employee-map-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">Map</th>
                                            <th>Employee</th>
                                            <th>Designation</th>
                                            <th>Role</th>
                                            <th>Allocation %</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employees as $employee)
                                            @php
                                                $mapping = $mappedByEmployee->get($employee->employee_id);
                                                $checked = in_array((int) $employee->employee_id, $mappedIds, true);
                                            @endphp
                                            <tr class="employee-row {{ $checked ? 'is-selected' : '' }}" data-search="{{ strtolower($employee->employee_name . ' ' . $employee->employee_email . ' ' . $employee->employee_mobile . ' ' . ($employee->designation_name ?? '')) }}">
                                                <td>
                                                    <input class="form-check-input employee-check" type="checkbox" name="employee_ids[]" value="{{ $employee->employee_id }}" @checked($checked)>
                                                </td>
                                                <td>
                                                    <strong>{{ $employee->employee_name }}</strong>
                                                    <div class="text-muted small">{{ $employee->employee_email }} | {{ $employee->employee_mobile }}</div>
                                                </td>
                                                <td>{{ $employee->designation_name ?: '-' }}</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm role-input map-field" name="project_role[{{ $employee->employee_id }}]" value="{{ old('project_role.' . $employee->employee_id, $mapping->project_role ?? '') }}" placeholder="Developer / QA / PM">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm map-field" name="allocation_percent[{{ $employee->employee_id }}]" min="0" max="100" value="{{ old('allocation_percent.' . $employee->employee_id, $mapping->allocation_percent ?? '') }}" placeholder="100">
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control form-control-sm date-input map-field" name="start_date[{{ $employee->employee_id }}]" value="{{ old('start_date.' . $employee->employee_id, $mapping->start_date ?? '') }}">
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control form-control-sm date-input map-field" name="end_date[{{ $employee->employee_id }}]" value="{{ old('end_date.' . $employee->employee_id, $mapping->end_date ?? '') }}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Active employees nahi mile.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <a href="{{ route('invoice.projects') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i>Save Mapping
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <x-footer />
    </div>
@endsection

@push('after_scripts')
    <script>
        (function () {
            function updateRowState() {
                var selected = 0;
                $('.employee-row').each(function () {
                    var checked = $(this).find('.employee-check').is(':checked');
                    $(this).toggleClass('is-selected', checked);
                    $(this).find('.map-field').prop('disabled', !checked);
                    if (checked) selected++;
                });
                $('#selectedCount').text(selected);
            }

            $('#employeeSearch').on('input', function () {
                var term = ($(this).val() || '').toLowerCase();
                $('.employee-row').each(function () {
                    $(this).toggle(!term || ($(this).data('search') || '').indexOf(term) !== -1);
                });
            });

            $('#selectAllVisible').on('click', function () {
                $('.employee-row:visible .employee-check').prop('checked', true);
                updateRowState();
            });

            $('#clearAll').on('click', function () {
                $('.employee-check').prop('checked', false);
                updateRowState();
            });

            $('.employee-check').on('change', updateRowState);
            updateRowState();
        })();
    </script>
@endpush
