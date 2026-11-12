@extends('layout.master')
@section('title', $title)
@section('main-section')

<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb & Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-4">
            <div class="my-auto mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 font-weight-bold text-dark">Monthly Payroll Batches</h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fs-12 font-weight-bold">
                        <i class="ti ti-stack-2 me-1"></i> Management Hub
                    </span>
                </div>
                <nav class="mt-1">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary"><i class="ti ti-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payroll.batches') }}" class="text-secondary">Payroll</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Batches</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                <a href="{{ route('payroll.process') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
                    <i class="ti ti-bolt me-1.5"></i> Process New Batch
                </a>
                <a href="{{ route('masters.payroll.salary-structure.create') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center">
                    <i class="ti ti-user-plus me-1.5"></i> Add Employee Salary
                </a>
                <a href="{{ route('payroll.payslips') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center">
                    <i class="ti ti-file-text me-1.5"></i> Employee Payslips
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-circle-check fs-22 me-2"></i>
                    <div>
                        <strong class="d-block">Success!</strong>
                        <span class="fs-13">{{ session('success') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle fs-22 me-2"></i>
                    <div>
                        <strong class="d-block">Error:</strong>
                        <span class="fs-13">{{ session('error') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Executive KPI Metrics Overview -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-primary text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-stack-2"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Total Batches Run</span>
                            <h3 class="fw-bold text-dark mb-0 fs-22" id="kpi_total_batches">{{ $totalBatches }}</h3>
                            <span class="fs-11 text-muted">Historical payroll runs</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-success text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-cash"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Total Net Disbursed</span>
                            <h3 class="fw-bold text-success mb-0 fs-20">&#8377; {{ number_format($totalDisbursed, 2) }}</h3>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">
                                {{ $paidBatchesCount }} Fully Disbursed
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fefce8 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-warning text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-clock"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Pending Disbursals</span>
                            <h3 class="fw-bold text-warning mb-0 fs-22">{{ $pendingBatchesCount }}</h3>
                            <span class="fs-11 text-muted">Awaiting final payout</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%);">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-info text-white d-flex align-items-center justify-content-center fs-22" style="width: 48px; height: 48px;">
                            <i class="ti ti-users"></i>
                        </div>
                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase letter-spacing">Staff Cycles Handled</span>
                            <h3 class="fw-bold text-dark mb-0 fs-22">{{ $totalEmployeesProcessed }}</h3>
                            <span class="fs-11 text-muted">Cumulative payslips issued</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card with Live Filters & AJAX Table Container -->
        <div class="card shadow-sm border-0" id="batchesCard">
            <!-- Filter Toolbar -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="row g-2 align-items-center">
                    <!-- Text Search -->
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="ti ti-search fs-14"></i>
                            </span>
                            <input type="text" id="filter_search" class="form-control form-control-sm border-start-0"
                                placeholder="Search by batch number or remarks..." value="{{ $filterSearch ?? '' }}" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary border-start-0" id="btn_clear_search" style="display: {{ empty($filterSearch) ? 'none' : 'block' }};" onclick="clearSearch()">
                                <i class="ti ti-x fs-12"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Year Dropdown -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filter_year" class="form-select form-select-sm" onchange="triggerFilter()">
                            <option value="">All Years</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ ($filterYear == $yr) ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Dropdown -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filter_month" class="form-select form-select-sm" onchange="triggerFilter()">
                            <option value="">All Months</option>
                            @for($m = 1; $m <= 12; $m++)
                                @php $mName = \Carbon\Carbon::createFromDate(null, $m, 1)->format('F'); @endphp
                                <option value="{{ $m }}" {{ ($filterMonth == $m) ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="col-lg-2 col-md-3 col-6">
                        <select id="filter_status" class="form-select form-select-sm" onchange="triggerFilter()">
                            <option value="">All Statuses</option>
                            <option value="Generated" {{ ($filterStatus == 'Generated') ? 'selected' : '' }}>Generated (Pending)</option>
                            <option value="Approved" {{ ($filterStatus == 'Approved') ? 'selected' : '' }}>Approved</option>
                            <option value="Paid" {{ ($filterStatus == 'Paid') ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <!-- Action & Reset -->
                    <div class="col-lg-2 col-md-3 col-6 d-flex align-items-center justify-content-end gap-2">
                        <div id="filterLoading" class="spinner-border spinner-border-sm text-primary" style="display: none;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" onclick="resetFilters()">
                            <i class="ti ti-rotate-2 fs-13"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Container (Reloaded dynamically via AJAX) -->
            <div id="batchesTableContainer" class="position-relative">
                @include('payroll.partials.batches_table')
            </div>
        </div>

    </div>
</div>

<style>
    /* Smooth AJAX transition styling */
    #batchesTableContainer {
        min-height: 250px;
        transition: opacity 0.2s ease-in-out;
    }
    #batchesTableContainer.loading {
        opacity: 0.45;
        pointer-events: none;
    }
    .letter-spacing {
        letter-spacing: 0.05em;
    }
    .btn-icon {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>

<script>
    let searchDebounceTimer = null;

    // Search input debouncer
    document.getElementById('filter_search').addEventListener('input', function() {
        const val = this.value.trim();
        const clearBtn = document.getElementById('btn_clear_search');
        if (clearBtn) {
            clearBtn.style.display = val.length > 0 ? 'block' : 'none';
        }
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            triggerFilter();
        }, 350);
    });

    function clearSearch() {
        const searchInput = document.getElementById('filter_search');
        searchInput.value = '';
        document.getElementById('btn_clear_search').style.display = 'none';
        triggerFilter();
    }

    // Build URL query string from filters
    function getFilterParams() {
        const params = new URLSearchParams();
        const search = document.getElementById('filter_search').value.trim();
        const year = document.getElementById('filter_year').value;
        const month = document.getElementById('filter_month').value;
        const status = document.getElementById('filter_status').value;

        if (search) params.set('search', search);
        if (year) params.set('year', year);
        if (month) params.set('month', month);
        if (status) params.set('status', status);

        return params;
    }

    function triggerFilter() {
        const params = getFilterParams();
        const targetUrl = "{{ route('payroll.batches') }}" + (params.toString() ? ('?' + params.toString()) : '');
        fetchBatches(targetUrl);
    }

    function resetFilters() {
        document.getElementById('filter_search').value = '';
        document.getElementById('filter_year').value = '';
        document.getElementById('filter_month').value = '';
        document.getElementById('filter_status').value = '';
        document.getElementById('btn_clear_search').style.display = 'none';

        fetchBatches("{{ route('payroll.batches') }}");
    }

    // Fetch batches via AJAX and update table container
    function fetchBatches(url) {
        const container = document.getElementById('batchesTableContainer');
        const spinner = document.getElementById('filterLoading');

        container.classList.add('loading');
        if (spinner) spinner.style.display = 'inline-block';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok (HTTP ' + response.status + ')');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.html) {
                container.innerHTML = data.html;
                // Update browser history so page and filters persist
                window.history.pushState({ path: url }, '', url);
            }
        })
        .catch(error => {
            console.error('AJAX pagination error:', error);
            // Fallback to normal navigation if AJAX fails
            window.location.href = url;
        })
        .finally(() => {
            container.classList.remove('loading');
            if (spinner) spinner.style.display = 'none';
        });
    }

    // Intercept all pagination links inside the table container
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#batchesTableContainer .pagination a');
        if (paginationLink) {
            e.preventDefault();
            const targetUrl = paginationLink.getAttribute('href');
            if (targetUrl && targetUrl !== '#' && targetUrl !== 'javascript:void(0);') {
                fetchBatches(targetUrl);
                // Smooth scroll to table card top
                document.getElementById('batchesCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    // Handle browser forward/back buttons
    window.addEventListener('popstate', function(e) {
        if (location.pathname.includes('/payroll/batches')) {
            fetchBatches(window.location.href);
        }
    });
</script>

@endsection
