@extends('layout.master')
@section('title', $title)

@section('main-section')
    <x-alert-modal :type="session('success') ? 'success' : (session('error') ? 'error' : '')" :message="session('success') ?? session('error')" />

    <style>
        .invoice-clients-page .stat-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .invoice-clients-page .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px -6px rgba(0, 0, 0, 0.08);
        }

        .invoice-clients-page .stat-card.active-filter {
            border-color: var(--stat-theme, #0d6efd);
            box-shadow: 0 0 0 2px var(--stat-theme, #0d6efd), 0 10px 20px -6px rgba(0, 0, 0, 0.1);
        }

        .invoice-clients-page .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--stat-theme, #0d6efd);
        }

        .invoice-clients-page .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: var(--stat-light-bg, #eef5ff);
            color: var(--stat-theme, #0d6efd);
            flex-shrink: 0;
        }

        .invoice-clients-page .client-avatar-badge {
            width: 38px;
            height: 38px;
            font-size: 13px;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        .invoice-clients-page .client-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        .invoice-clients-page .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .invoice-clients-page .filter-pill-btn {
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 14px;
            transition: all 0.2s ease;
        }

        .invoice-clients-page .table-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .invoice-clients-page table.dataTable tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02) !important;
        }

        .invoice-clients-page .hover-primary:hover {
            color: var(--bs-primary) !important;
            text-decoration: underline !important;
        }

        .invoice-clients-page .hover-success:hover {
            color: var(--bs-success) !important;
            text-decoration: underline !important;
        }

        .detail-item-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 14px;
            border: 1px solid #edf2f7;
            height: 100%;
        }

        .detail-item-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .detail-item-value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            word-break: break-word;
        }
    </style>

    <div class="page-wrapper invoice-clients-page">
        <div class="content">
            {{-- Navigation Module Links --}}
            @include('invoice.partials.nav')

            {{-- Header & Actions --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <h2 class="mb-0 fw-bold text-dark">{{ $title }}</h2>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-12">
                            {{ number_format($clientCounts['total'] ?? 0) }} Total
                        </span>
                    </div>
                    <p class="text-muted mb-0 fs-13 mt-1">Manage billing clients, contact details, GST, and locations</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('invoice.ledger') }}" class="btn btn-outline-secondary btn-md shadow-sm">
                        <i class="ti ti-report-money me-1"></i>Client Ledger
                    </a>
                    <a href="{{ route('invoice.clients.create') }}" class="btn btn-primary btn-md shadow-sm">
                        <i class="ti ti-circle-plus me-1.5 fs-16"></i>Create Client
                    </a>
                </div>
            </div>

            {{-- Stat Cards / KPI Summary --}}
            @php
                $totalCount = (int) ($clientCounts['total'] ?? 0);
                $activeCount = (int) ($clientCounts['active'] ?? 0);
                $inactiveCount = (int) ($clientCounts['inactive'] ?? 0);
                $activePct = $totalCount > 0 ? round(($activeCount / $totalCount) * 100) : 0;
                $inactivePct = $totalCount > 0 ? round(($inactiveCount / $totalCount) * 100) : 0;
            @endphp

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card stat-card active-filter h-100 mb-0" id="statCardAll" data-status="" style="--stat-theme: #0d6efd; --stat-light-bg: #eef5ff;" title="Click to view all clients">
                        <div class="card-body py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-13 fw-medium mb-1">Total Clients</div>
                                <h3 class="mb-1 fw-bold text-dark">{{ number_format($totalCount) }}</h3>
                                <span class="badge bg-light text-secondary border fs-11">All Registered</span>
                            </div>
                            <span class="stat-icon-wrapper"><i class="ti ti-users"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card h-100 mb-0" id="statCardActive" data-status="active" style="--stat-theme: #10b981; --stat-light-bg: #ecfdf5;" title="Click to filter active clients">
                        <div class="card-body py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-13 fw-medium mb-1">Active Clients</div>
                                <h3 class="mb-1 fw-bold text-success">{{ number_format($activeCount) }}</h3>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">
                                    <i class="ti ti-trending-up me-1"></i>{{ $activePct }}% of total
                                </span>
                            </div>
                            <span class="stat-icon-wrapper"><i class="ti ti-user-check"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card h-100 mb-0" id="statCardInactive" data-status="inactive" style="--stat-theme: #ef4444; --stat-light-bg: #fef2f2;" title="Click to filter inactive clients">
                        <div class="card-body py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-13 fw-medium mb-1">Inactive Clients</div>
                                <h3 class="mb-1 fw-bold text-danger">{{ number_format($inactiveCount) }}</h3>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11">
                                    {{ $inactivePct }}% of total
                                </span>
                            </div>
                            <span class="stat-icon-wrapper"><i class="ti ti-user-x"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Search Toolbar --}}
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body py-2.5 px-3">
                    <div class="row align-items-center g-2">
                        <div class="col-md-7 d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted fs-13 fw-medium me-1"><i class="ti ti-filter me-1"></i>Filter by Status:</span>
                            <button type="button" class="btn btn-sm btn-primary filter-pill-btn" data-filter="">
                                All <span class="badge bg-white text-primary ms-1">{{ $totalCount }}</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success filter-pill-btn" data-filter="active">
                                Active <span class="badge bg-success-subtle text-success ms-1">{{ $activeCount }}</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger filter-pill-btn" data-filter="inactive">
                                Inactive <span class="badge bg-danger-subtle text-danger ms-1">{{ $inactiveCount }}</span>
                            </button>
                        </div>
                        <div class="col-md-5 d-flex justify-content-md-end align-items-center gap-2">
                            <div class="input-group input-group-sm" style="max-width: 260px;">
                                <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" id="customClientSearch" class="form-control bg-light border-start-0" placeholder="Search client, email, GST...">
                            </div>
                            <button type="button" id="btnRefreshTable" class="btn btn-sm btn-outline-secondary" title="Refresh Table">
                                <i class="ti ti-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clients Data Table Card --}}
            <div class="card table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="invoiceClientsList" class="table table-hover align-middle mb-0 nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Client Details</th>
                                    <th>Contact Person</th>
                                    <th>Location</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;" class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-footer />
    </div>

    {{-- Client Quick View Details Modal --}}
    <div class="modal fade" id="clientDetailsModal" tabindex="-1" aria-labelledby="clientDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar avatar-md rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold" id="modalClientAvatar">CL</span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalClientName">Client Details</h5>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                                <span class="badge bg-light text-secondary border font-monospace fs-11" id="modalClientId">#0</span>
                                <span id="modalClientStatus"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-user me-1 text-primary"></i>Concern Person</div>
                                <div class="detail-item-value" id="modalConcernPerson">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-phone me-1 text-success"></i>Mobile Number</div>
                                <div class="detail-item-value" id="modalMobile">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-mail me-1 text-info"></i>Email Address</div>
                                <div class="detail-item-value" id="modalEmail">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-world me-1 text-purple"></i>Website</div>
                                <div class="detail-item-value" id="modalWebsite">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-receipt-tax me-1 text-warning"></i>GST Number</div>
                                <div class="detail-item-value font-monospace" id="modalGst">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-id me-1 text-secondary"></i>CIN Number</div>
                                <div class="detail-item-value font-monospace" id="modalCin">-</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-item-card">
                                <div class="detail-item-label"><i class="ti ti-map-pin me-1 text-danger"></i>Billing Address & Location</div>
                                <div class="detail-item-value" id="modalAddress">-</div>
                                <div class="mt-2 fs-12 text-muted" id="modalLocationDetails">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between">
                    <div>
                        <a href="#" id="modalLedgerBtn" class="btn btn-outline-success btn-sm">
                            <i class="ti ti-report-money me-1"></i>View Ledger
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <a href="#" id="modalEditBtn" class="btn btn-primary btn-sm">
                            <i class="ti ti-edit me-1"></i>Edit Client
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('partials.datatable-pagination', [
    'tableId' => 'invoiceClientsList',
    'ajaxUrl' => route('invoice.clients.list'),
    'columns' => [
        ['data' => 'ClientID', 'name' => 'c.ClientID'],
        ['data' => 'client', 'name' => 'c.ClientName'],
        ['data' => 'contact', 'name' => 'c.ConcernOwner'],
        ['data' => 'location', 'name' => 's.StateName'],
        ['data' => 'status', 'name' => 'c.DelStatus'],
        ['data' => 'action', 'name' => 'action'],
    ],
    'order' => [[0, 'desc']],
    'columnDefs' => [['targets' => [5], 'orderable' => false, 'searchable' => false]],
])

@push('after_scripts')
    <script>
        $(document).ready(function() {
            let currentStatus = '';

            function applyStatusFilter(status) {
                currentStatus = status;
                const table = $('#invoiceClientsList').DataTable();
                const url = new URL('{{ route('invoice.clients.list') }}', window.location.origin);

                if (status) {
                    url.searchParams.set('status_filter', status);
                }

                table.ajax.url(url.toString()).load();

                // Update stat cards visual state
                $('.stat-card').removeClass('active-filter');
                if (status === 'active') {
                    $('#statCardActive').addClass('active-filter');
                } else if (status === 'inactive') {
                    $('#statCardInactive').addClass('active-filter');
                } else {
                    $('#statCardAll').addClass('active-filter');
                }

                // Update filter pill buttons
                $('.filter-pill-btn').each(function() {
                    const btnFilter = $(this).data('filter');
                    if (btnFilter === status) {
                        if (btnFilter === 'active') {
                            $(this).removeClass('btn-outline-success').addClass('btn-success text-white');
                        } else if (btnFilter === 'inactive') {
                            $(this).removeClass('btn-outline-danger').addClass('btn-danger text-white');
                        } else {
                            $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');
                        }
                    } else {
                        if (btnFilter === 'active') {
                            $(this).removeClass('btn-success text-white').addClass('btn-outline-success');
                        } else if (btnFilter === 'inactive') {
                            $(this).removeClass('btn-danger text-white').addClass('btn-outline-danger');
                        } else {
                            $(this).removeClass('btn-primary text-white').addClass('btn-outline-primary');
                        }
                    }
                });
            }

            // Stat card click filtering
            $('.stat-card').on('click', function() {
                const status = $(this).data('status');
                applyStatusFilter(status || '');
            });

            // Filter pills click
            $('.filter-pill-btn').on('click', function() {
                const status = $(this).data('filter');
                applyStatusFilter(status || '');
            });

            // Custom search input integration with DataTables
            $('#customClientSearch').on('keyup input', function() {
                const table = $('#invoiceClientsList').DataTable();
                table.search(this.value).draw();
            });

            // Refresh table button
            $('#btnRefreshTable').on('click', function() {
                const $icon = $(this).find('i');
                $icon.addClass('ti-spin');
                const table = $('#invoiceClientsList').DataTable();
                table.ajax.reload(function() {
                    setTimeout(() => $icon.removeClass('ti-spin'), 400);
                });
            });

            // View Details Modal Trigger
            $(document).on('click', '.btn-view-client', function() {
                const rawData = $(this).attr('data-client');
                if (!rawData) return;

                try {
                    const client = JSON.parse(rawData);
                    const name = client.name || 'Client Details';
                    const words = name.trim().split(/\s+/);
                    let initials = 'CL';
                    if (words.length >= 2 && words[0] && words[1]) {
                        initials = (words[0][0] + words[1][0]).toUpperCase();
                    } else if (words.length === 1 && words[0]) {
                        initials = words[0].substring(0, 2).toUpperCase();
                    }

                    $('#modalClientAvatar').text(initials);
                    $('#modalClientName').text(name);
                    $('#modalClientId').text('#' + client.id);

                    if (client.status === 'Active') {
                        $('#modalClientStatus').html('<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-11"><i class="ti ti-circle-filled fs-7 me-1"></i>Active</span>');
                    } else {
                        $('#modalClientStatus').html('<span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-11"><i class="ti ti-circle-filled fs-7 me-1"></i>Inactive</span>');
                    }

                    $('#modalConcernPerson').text(client.person || '-');
                    
                    if (client.mobile) {
                        $('#modalMobile').html('<a href="tel:' + client.mobile + '" class="text-decoration-none text-dark hover-success"><i class="ti ti-phone me-1 text-success"></i>' + client.mobile + '</a>');
                    } else {
                        $('#modalMobile').text('-');
                    }

                    if (client.email) {
                        $('#modalEmail').html('<a href="mailto:' + client.email + '" class="text-decoration-none text-dark hover-primary"><i class="ti ti-mail me-1 text-primary"></i>' + client.email + '</a>');
                    } else {
                        $('#modalEmail').text('-');
                    }

                    if (client.website) {
                        const siteUrl = client.website.startsWith('http') ? client.website : 'https://' + client.website;
                        $('#modalWebsite').html('<a href="' + siteUrl + '" target="_blank" class="text-decoration-none text-dark hover-primary"><i class="ti ti-world me-1 text-info"></i>' + client.website + ' <i class="ti ti-external-link fs-11 ms-0.5"></i></a>');
                    } else {
                        $('#modalWebsite').text('-');
                    }

                    $('#modalGst').text(client.gst || '-');
                    $('#modalCin').text(client.cin || '-');
                    $('#modalAddress').text(client.address || '-');

                    let locDetails = [];
                    if (client.state) locDetails.push('State: <strong>' + client.state + '</strong>');
                    if (client.country) locDetails.push('Country: <strong>' + client.country + '</strong>');
                    $('#modalLocationDetails').html(locDetails.length ? locDetails.join(' &bull; ') : '-');

                    $('#modalLedgerBtn').attr('href', client.ledgerUrl || '#');
                    $('#modalEditBtn').attr('href', client.editUrl || '#');

                    const modal = new bootstrap.Modal(document.getElementById('clientDetailsModal'));
                    modal.show();
                } catch (e) {
                    console.error('Error parsing client modal data:', e);
                }
            });
        });
    </script>
@endpush
