<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('invoice.dashboard') }}" class="btn btn-sm {{ request()->routeIs('invoice.dashboard') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-layout-dashboard me-1"></i>Dashboard
    </a>
    <a href="{{ route('invoice.clients') }}" class="btn btn-sm {{ request()->routeIs('invoice.clients') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-users me-1"></i>Clients
    </a>
    <a href="{{ route('invoice.companies') }}" class="btn btn-sm {{ request()->routeIs('invoice.companies') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-building me-1"></i>Companies
    </a>
    <a href="{{ route('invoice.projects') }}" class="btn btn-sm {{ request()->routeIs('invoice.projects') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-briefcase me-1"></i>Projects
    </a>
    <a href="{{ route('invoice.invoices') }}" class="btn btn-sm {{ request()->routeIs('invoice.invoices') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-file-invoice me-1"></i>Invoices
    </a>
    <a href="{{ route('invoice.payments') }}" class="btn btn-sm {{ request()->routeIs('invoice.payments') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-cash me-1"></i>Payments
    </a>
    <a href="{{ route('invoice.ledger') }}" class="btn btn-sm {{ request()->routeIs('invoice.ledger') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-report-money me-1"></i>Ledger
    </a>
    <a href="{{ route('invoice.expenses') }}" class="btn btn-sm {{ request()->routeIs('invoice.expenses') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="ti ti-receipt me-1"></i>Expenses
    </a>
</div>
