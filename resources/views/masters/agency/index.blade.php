@extends('layout.master')
@section('title', $title ?? 'Agency Master')

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .agency-page .stat-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .agency-page .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .agency-page .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .agency-page .table-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .agency-page .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.15s ease;
        }

        .agency-page .badge-pill-soft {
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
        }

        .agency-page .status-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
    </style>

    <div class="page-wrapper agency-page">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
                <div class="my-auto mb-2 mb-md-0">
                    <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="ti ti-building text-primary"></i> Agency Master
                    </h3>
                    <nav>
                        <ol class="breadcrumb mb-0 fs-12">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item">Organisation</li>
                            <li class="breadcrumb-item active">Agencies</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('masters.organisation.agency.mapping') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-2">
                        <i class="ti ti-users-group fs-16"></i> Employee Mapping
                    </a>
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5 fw-semibold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAgencyModal">
                        <i class="ti ti-circle-plus fs-16"></i> Add Agency
                    </button>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3 cursor-pointer" onclick="$('.status-filter-btn[data-status=\'\']').click();" style="cursor: pointer;" title="Show All Agencies">
                        <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                            <i class="ti ti-building"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Total Agencies</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ $totalAgencies }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3 cursor-pointer" onclick="$('.status-filter-btn[data-status=\'Active\']').click();" style="cursor: pointer;" title="Filter Active Agencies">
                        <div class="stat-icon-wrapper bg-success-subtle text-success">
                            <i class="ti ti-circle-check"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Active Agencies</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ $activeAgencies }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card p-3 d-flex align-items-center gap-3 cursor-pointer" onclick="$('.status-filter-btn[data-status=\'Inactive\']').click();" style="cursor: pointer;" title="Filter Inactive Agencies">
                        <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                            <i class="ti ti-circle-x"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-12 fw-medium text-uppercase">Inactive Agencies</span>
                            <h4 class="fw-bold text-dark mb-0 fs-20">{{ $inactiveAgencies }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <a href="{{ route('masters.organisation.agency.mapping') }}" class="text-decoration-none">
                        <div class="stat-card p-3 d-flex align-items-center gap-3" style="cursor: pointer;" title="Go to Employee Mapping">
                            <div class="stat-icon-wrapper text-purple" style="background-color: #f3e8ff; color: #7e22ce;">
                                <i class="ti ti-users"></i>
                            </div>
                            <div>
                                <span class="text-muted fs-12 fw-medium text-uppercase">Mapped Employees</span>
                                <h4 class="fw-bold text-dark mb-0 fs-20">{{ $mappedEmployees }}</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="table-card mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn {{ empty($selectedStatus) ? 'btn-primary active' : 'btn-outline-secondary' }} status-filter-btn px-3" data-status="">All</button>
                            <button type="button" class="btn {{ ($selectedStatus ?? '') === 'Active' ? 'btn-primary active' : 'btn-outline-secondary' }} status-filter-btn px-3" data-status="Active">Active</button>
                            <button type="button" class="btn {{ ($selectedStatus ?? '') === 'Inactive' ? 'btn-primary active' : 'btn-outline-secondary' }} status-filter-btn px-3" data-status="Inactive">Inactive</button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 260px;">
                            <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                            <input type="text" id="agencySearchInput" class="form-control border-start-0 ps-0" placeholder="Search agency by name, code, contact..." value="{{ $searchKeyword ?? '' }}">
                            @if(!empty($searchKeyword))
                                <button class="btn btn-outline-secondary border-start-0 border-end" type="button" onclick="$('#agencySearchInput').val('').trigger('input');"><i class="ti ti-x"></i></button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="agencyTable">
                            <thead class="bg-light border-bottom text-muted fs-12 text-uppercase">
                                <tr>
                                    <th class="ps-4" style="width: 50px;">#</th>
                                    <th>Agency Details</th>
                                    <th>Contact Person</th>
                                    <th>Email & Phone</th>
                                    <th>Location</th>
                                    <th class="text-center">Employees</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="noFilterResultsRow" style="display: none;">
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="avatar avatar-lg rounded-circle bg-light text-muted mx-auto mb-2 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-search-off fs-24"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">No Matching Agencies</h6>
                                        <p class="text-muted fs-13 mb-0">No agencies match your search or status filter.</p>
                                    </td>
                                </tr>
                                @forelse ($agencies as $idx => $agency)
                                    <tr class="agency-row" data-status="{{ $agency->status }}" data-name="{{ strtolower($agency->agency_name . ' ' . $agency->agency_code . ' ' . $agency->contact_person . ' ' . $agency->email . ' ' . $agency->phone . ' ' . $agency->city . ' ' . $agency->state . ' ' . $agency->address) }}">
                                        <td class="ps-4 text-muted fs-13">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="avatar avatar-sm rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-13">
                                                    {{ strtoupper(substr($agency->agency_name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">{{ $agency->agency_name }}</div>
                                                    @if ($agency->agency_code)
                                                        <span class="badge bg-light text-muted border fs-10 font-monospace">{{ $agency->agency_code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fs-13 text-dark fw-medium">{{ $agency->contact_person ?: '-' }}</span>
                                        </td>
                                        <td>
                                            <div class="fs-12">
                                                @if ($agency->email)
                                                    <a href="mailto:{{ $agency->email }}" class="text-muted text-decoration-none d-block">
                                                        <i class="ti ti-mail me-1"></i>{{ $agency->email }}
                                                    </a>
                                                @endif
                                                @if ($agency->phone)
                                                    <a href="tel:{{ $agency->phone }}" class="text-dark text-decoration-none d-block">
                                                        <i class="ti ti-phone me-1 text-muted"></i>{{ $agency->phone }}
                                                    </a>
                                                @endif
                                                @if (!$agency->email && !$agency->phone)
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fs-12 text-muted">
                                                @if ($agency->city || $agency->state)
                                                    <i class="ti ti-map-pin me-1 text-danger"></i>{{ trim(($agency->city ? $agency->city . ', ' : '') . ($agency->state ?: ''), ', ') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('masters.organisation.agency.mapping', ['filter_agency' => $agency->id]) }}" class="badge rounded-pill px-2.5 py-1 text-decoration-none" style="background-color: #f3e8ff; color: #7e22ce; font-size: 12px;" title="View Mapped Employees">
                                                <i class="ti ti-users me-1"></i>{{ $agency->employees_count }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input agencyStatusSwitch" type="checkbox" {{ $agency->status === 'Active' ? 'checked' : '' }} data-id="{{ $agency->id }}" style="cursor: pointer;">
                                                </div>
                                                <span class="badge-pill-soft {{ $agency->status === 'Active' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border">
                                                    <span class="status-pulse-dot" style="background-color: {{ $agency->status === 'Active' ? '#10b981' : '#ef4444' }};"></span>
                                                    {{ $agency->status }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary action-btn editAgencyBtn" data-id="{{ $agency->id }}" title="Edit Agency">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn deleteAgencyBtn" data-id="{{ $agency->id }}" data-name="{{ $agency->agency_name }}" title="Delete Agency">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="8" class="text-center py-5">
                                            <div class="avatar avatar-xl rounded-circle bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-building-off fs-28"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">No Agencies Found</h6>
                                            <p class="text-muted fs-13 mb-3">Add your first agency to manage third-party vendors and staff mapping.</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAgencyModal">
                                                <i class="ti ti-circle-plus me-1"></i> Add Agency
                                            </button>
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

    <!-- ADD AGENCY MODAL -->
    <div class="modal fade" id="addAgencyModal" tabindex="-1" aria-labelledby="addAgencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light bg-opacity-75 py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-building fs-18"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="addAgencyModalLabel">Add New Agency</h5>
                            <p class="text-muted fs-12 mb-0">Register an external staffing agency or contractor vendor.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('masters.organisation.agency.store') }}" method="POST" id="addAgencyForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Agency Name <span class="text-danger">*</span></label>
                                <input type="text" name="agency_name" class="form-control" placeholder="e.g. Apex Staffing Solutions" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Agency Code</label>
                                <input type="text" name="agency_code" class="form-control" placeholder="e.g. APX-001">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="e.g. Rajesh Kumar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="e.g. +91 9876543210">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="agency@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">City</label>
                                <input type="text" name="city" class="form-control" placeholder="e.g. Mumbai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">State</label>
                                <input type="text" name="state" class="form-control" placeholder="e.g. Maharashtra">
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Office Address</label>
                                <textarea name="address" rows="2" class="form-control" placeholder="Street address, building, suite..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Notes / Terms</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="Additional vendor agreements or notes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2.5 px-4 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4 d-inline-flex align-items-center gap-1">
                            <i class="ti ti-check fs-16"></i> Save Agency
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT AGENCY MODAL -->
    <div class="modal fade" id="editAgencyModal" tabindex="-1" aria-labelledby="editAgencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light bg-opacity-75 py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2.5">
                        <span class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                            <i class="ti ti-edit fs-18"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="editAgencyModalLabel">Edit Agency</h5>
                            <p class="text-muted fs-12 mb-0">Update agency details and contact information.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editAgencyForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editAgencyId">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Agency Name <span class="text-danger">*</span></label>
                                <input type="text" name="agency_name" id="editAgencyName" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Agency Code</label>
                                <input type="text" name="agency_code" id="editAgencyCode" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Contact Person</label>
                                <input type="text" name="contact_person" id="editContactPerson" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Phone Number</label>
                                <input type="text" name="phone" id="editPhone" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Email Address</label>
                                <input type="email" name="email" id="editEmail" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Status <span class="text-danger">*</span></label>
                                <select name="status" id="editStatus" class="form-select" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">City</label>
                                <input type="text" name="city" id="editCity" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">State</label>
                                <input type="text" name="state" id="editState" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Office Address</label>
                                <textarea name="address" id="editAddress" rows="2" class="form-control"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-13 fw-semibold text-dark mb-1">Notes / Terms</label>
                                <textarea name="notes" id="editNotes" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2.5 px-4 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4 d-inline-flex align-items-center gap-1">
                            <i class="ti ti-check fs-16"></i> Update Agency
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="delete_agency_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <span class="avatar avatar-xl rounded-circle bg-danger-subtle text-danger mb-3 d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-trash-x fs-32"></i>
                    </span>
                    <h5 class="fw-bold text-dark mb-1">Delete Agency?</h5>
                    <p class="text-muted fs-13 mb-4">Are you sure you want to delete <strong id="deleteAgencyName">this agency</strong>? Assigned employees will be unmapped.</p>
                    <input type="hidden" id="deleteAgencyId">
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger px-3 fw-semibold" id="confirmDeleteAgencyBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after_scripts')
<script>
    $(document).ready(function() {
        // Search filter: responds instantly to keyup, input, and paste
        $('#agencySearchInput').on('keyup input change', function() {
            filterAgencies();
        });

        // Status button filter
        $('.status-filter-btn').on('click', function() {
            $('.status-filter-btn').removeClass('btn-primary active').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');
            filterAgencies();
        });

        function filterAgencies() {
            let searchVal = ($('#agencySearchInput').val() || '').toLowerCase().trim();
            let statusVal = String($('.status-filter-btn.active').data('status') || '').toLowerCase().trim();

            let visibleCount = 0;
            let totalRows = $('#agencyTable tbody tr.agency-row').length;

            $('#agencyTable tbody tr.agency-row').each(function() {
                let rowStatus = String($(this).data('status') || '').toLowerCase().trim();
                let rowName = String($(this).data('name') || '').toLowerCase();

                let matchStatus = !statusVal || rowStatus === statusVal;
                let matchSearch = !searchVal || rowName.indexOf(searchVal) !== -1;

                if (matchStatus && matchSearch) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (totalRows > 0) {
                if (visibleCount === 0) {
                    $('#noFilterResultsRow').show();
                } else {
                    $('#noFilterResultsRow').hide();
                }
            }
        }

        // Run initial filter in case params exist on page load
        filterAgencies();

        // Edit Agency Click
        $('.editAgencyBtn').on('click', function() {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('masters/organisation/agency') }}/" + id,
                type: 'GET',
                success: function(res) {
                    if (res.success && res.agency) {
                        let a = res.agency;
                        $('#editAgencyId').val(a.id);
                        $('#editAgencyName').val(a.agency_name);
                        $('#editAgencyCode').val(a.agency_code);
                        $('#editContactPerson').val(a.contact_person);
                        $('#editEmail').val(a.email);
                        $('#editPhone').val(a.phone);
                        $('#editStatus').val(a.status);
                        $('#editCity').val(a.city);
                        $('#editState').val(a.state);
                        $('#editAddress').val(a.address);
                        $('#editNotes').val(a.notes);

                        $('#editAgencyForm').attr('action', "{{ url('masters/organisation/agency') }}/" + a.id);
                        $('#editAgencyModal').modal('show');
                    }
                }
            });
        });

        // Status Switch Toggle
        $('.agencyStatusSwitch').on('change', function() {
            let id = $(this).data('id');
            let isChecked = $(this).is(':checked');
            let status = isChecked ? 'Active' : 'Inactive';
            let switchEl = $(this);

            $.ajax({
                url: "{{ url('masters/organisation/agency/status') }}/" + id,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(res) {
                    location.reload();
                },
                error: function() {
                    switchEl.prop('checked', !isChecked);
                    alert('Failed to update status. Please try again.');
                }
            });
        });

        // Delete Agency Click
        $('.deleteAgencyBtn').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            $('#deleteAgencyId').val(id);
            $('#deleteAgencyName').text(`"${name}"`);
            $('#delete_agency_modal').modal('show');
        });

        $('#confirmDeleteAgencyBtn').on('click', function() {
            let id = $('#deleteAgencyId').val();
            $.ajax({
                url: "{{ url('masters/organisation/agency') }}/" + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $('#delete_agency_modal').modal('hide');
                    location.reload();
                },
                error: function() {
                    alert('Error deleting agency. Please try again.');
                }
            });
        });
    });
</script>
@endpush
